<?php
	
	
	/* MANAGE PHD STUDENTS/SUPPORT STAFF DETAILS */
	


	$header = 'Edit/Archive Staff'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 


	

	echo "You are about to edit/archive <strong>PhD Student</strong> or <strong>Support Staff</strong>'s data<br>";

	$search = $searchErr = $sql = "";
	
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$search = test_input($_POST["find_who"]);
	}


	// Protect from SQL injection attacks
	function test_input($data) {
  		$data = trim($data);
  		$data = stripslashes($data);
  		$data = htmlspecialchars($data);
  		return $data;

	}

	
	if (isset($_POST['btn_find'])) {
		$sql = "SELECT * FROM PHD_Students WHERE (Account_ID = '$search' OR Surname LIKE '$search%');";
	} else if (isset($_POST['btn_showAll'])) {
		$sql = "SELECT * FROM PHD_Students;";
	}

	if (isset($_POST['btn_find']) || isset($_POST['btn_showAll'])) {
		$count_records = 0; // how many records found
		$first_time = true; // to add a "archive staff" column only once
		
  		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
  		$counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message
  
  		do {
			if (isset($_POST['btn_find'])) {
	  			if ($search == "") {
					$searchErr = "Please enter your search term";
        			$err .= $searchErr; //for logging purposes
				}
		
				if ($err != "") {
					break; //exit the do..while loop
				}
			}
		
			try {
				$fields = array(); // array to store DB table field names
				$types = array(); // array to store DB table field types
				$describe = array(); // temporary array to store DB table field names and types
				$num_precision = array(); // array to store DB table field lengths
			
				//connect to DB
				require_once 'dbconfig.php';

				$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	           	$query_students = $conn -> prepare($sql); 
    	       	$query_students -> execute();
			
				$query_describe = $conn -> prepare("DESCRIBE PHD_Students;");
				$query_describe -> execute();
				
				$query_who_has_supervisor = $conn->prepare("SELECT DISTINCT S.Account_ID, S.Forename, S.Surname 
														   FROM PHD_Students P, Supervisors S 
														   WHERE P.Supervisor = S.Account_ID;");
				
				echo "<br>";				
				echo "<table>";
				echo "<tr>";
				
				while ($describe = $query_describe -> fetch(PDO::FETCH_ASSOC)) {
					$field_name = ""; // to store formatted table field name
					if ($first_time == true){ // only print "Action" column once
						echo "<th>";
						echo "Action";
						echo "</th>";
					}
					$first_time = false;
					if ($describe['Type'] == 'date'){
						$num_precision[$describe['Field']] = 10;
					} else {
						$num_precision[$describe['Field']] = filter_var($describe['Type'], FILTER_SANITIZE_NUMBER_INT);
					}
					$fields[] = $describe['Field'];
					$field_name = str_replace("_", " ", $describe['Field']);
					$types[$describe['Field']] = $describe['Type'];					
					echo "<th>";
					if (substr($field_name, 0, 5) == "Skill"){
						$field_name = substr($field_name, 5); // display formatted field name from the DB without 'Skill' in the beginning
					} 
					print_r($field_name); // display formatted field name from the DB
					echo "</th>";
				}
				echo "</tr>";
				
				
				while ($person = $query_students -> fetch(PDO::FETCH_ASSOC)) {
					$width = ""; // to store the <style=coulmn width> expression for date fields
					echo "<tr>";
					echo "<td>";
					echo "<a href=\"edit_phd.php?student_ID=$person[Account_ID]\"> <span class=\"error\"> Edit </span></a>";
					echo "<p style=\"line-height: 60%;\"><a href=\"archive_staff.php?student_ID=$person[Account_ID]&surname=$person[Surname]&forename=$person[Forename]\"> <span class=\"error\"> Archive </span></a></p>";
					echo "</td>";
					foreach ($fields as $f){
						if ($types[$f] == "date"){ // styling: to stop the date breaking over 2 lines
							$width = "style=\"white-space: nowrap; width: 10ch;\"";
						} else {
							$width = "";
						}
						echo "<td $width>";
						if ($f == "Supervisor") {
							$query_who_has_supervisor->execute();
							while($supervisors = $query_who_has_supervisor -> fetch(PDO::FETCH_ASSOC)) {
								if ($supervisors[Account_ID] == $person[$f]){
									echo "$supervisors[Surname] $supervisors[Forename]";
								}
							}
						} else {
							echo "$person[$f]";
						}
			
						echo "</td>";
					}
					echo "</tr>";
					$count_records++;
				}
				echo "</table>";
				
				if ($count_records == 0){
					echo "<br>Sorry, no records have been found";
				} else {
					echo "<br>Found: $count_records record(s)";
				}
				echo "<br><br>";
       		}
			catch(PDOException $e){
   				echo "Error: " . $e->getMessage();
				$conn = NULL; //close DB connection
			}
			
  			$conn = NULL;
			$counter = 0;
			$search = $searchErr = $sql = ""; //reset the vars
  		} while ($counter != 0); //end of do...while loop
		unset($_POST['btn_find']);
		unset($_POST['btn_showAll']);
	}
	
?>
	<br>
	<form name="find_staff" method="post" onSubmit="return verifySearch(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"> 
    	<label> Type Surname or ID </label><input type="text" name="find_who" maxlength="30" onKeyUp="checkLen(this.value)" >
    	<input name="btn_find" type="submit" title="Find a PhD student/Support staff" value="Find">
        <span class="error"> <?php echo "&nbsp; &nbsp; $searchErr";?> </span>
    </form>
        <br>
        <br>

    <form name="find_all_staff" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input name="btn_showAll" type="submit" title = "Show all accounts" value="Show all">
    </form>
        <br>
        <br>


<?php

	echo "<input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>
