<?php
	


	/* ADMIN VIEW OF LABS FOR SELECTED MODULE */




	$header = "VIEW MODULE'S LABS"; /* use this variable to set the header string */

	include 'template-top.php';

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type



	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	}

		
	$location = 'admin_view_module_labs.php';
	$conn = NULL; // connection to the DB is not open
	$db_table = "Lab_Requirements";
	$err = ""; //error message

	$fields = array(); // array to store DB table field names
	$types = array(); // array to store DB table field types
	$field_value = array(); // array to store DB table field values to be sent to the DB
	$record = -1; // session record counter 
	$records_no = 0; // total found sessions for the Module Leader



	$module_name = $lecturer_ID = $lecturer_name = $lecturer_title = $lecturer_name = $lecturer_initial = $lecturer_surname = "";
	$sql = "";
	$lab_ID = array();

	include 'php_functions.php';

	$message=$_GET["message"]; //the message is either "" (if this page is hit for the first time) or "Form is succefully submitted"
	$message = test_input($message);
	$module_code = $_GET["module_code"];
	$module_code = test_input($module_code);



	// if the Admin is logged in, show the list of labs

	try {
		$describe = array(); // temporary array to store DB table field names and types
		$num_precision = array(); // array to store DB table field lengths
				
		//connect to DB
		require_once 'dbconfig.php';

		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		

		
		// Get module name
		$query_find_module_name = $conn -> prepare("SELECT Module_Name 
											     	FROM Modules 
											     	WHERE Module_Code = '$module_code' LIMIT 1;"); 
		$query_find_module_name -> execute();
		
		while ($found_module_name = $query_find_module_name -> fetch(PDO::FETCH_ASSOC)) {
			$module_name = $found_module_name['Module_Name'];
		} 

		echo "List of lab/tutorial sessions for $module_code '$module_name':<br>";

?>

		<br>
		<div class = "update_multi">
        	<form name="request_support" method="post" onSubmit="" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF?message=&module_code=$module_code"]); ?>">


<?php
		// Format the heading for the list of sessions

		$query_describe = $conn -> prepare("DESCRIBE $db_table;"); // find the field names and field types 
		$query_describe -> execute();
				
		echo "<br>";
		echo "<table>";
		echo "<tr>";
		echo "<th>";
		echo "Session No.";
		echo "</th>";
		
		while ($describe = $query_describe -> fetch(PDO::FETCH_ASSOC)) {
			$field_name = ""; // to store formatted table field name
			
			if ($describe['Type'] == 'date'){ // set the field length for date type
				$num_precision[$describe['Field']] = 10;
			} else {
				$num_precision[$describe['Field']] = filter_var($describe['Type'], FILTER_SANITIZE_NUMBER_INT); // find the field length for other field types from the DB (not suitable for example, for decimals with precision (5,2) types)
			}
			$fields[] = $describe['Field'];
			$field_name = str_replace("_", " ", $describe['Field']);
			$types[$describe['Field']] = $describe['Type'];
			if ($describe['Field'] != "Account_ID" && $describe['Field'] != "Module"){ // skip these fields
				echo "<th>";
				if (substr($field_name, 0, 5) == "Skill"){
					$field_name = substr($field_name, 5); // display formatted field name from the DB without 'Skill' in the beginning
				} 
				print_r($field_name); // display formatted field name from the DB
				echo "</th>";
				
			} else {
				continue;
			}
		}
		echo "</tr>";
		
		
		$query_find_sessions = $conn -> prepare("SELECT * 
												 FROM $db_table 
												 WHERE Module = '$module_code';"); 
				
		$query_find_sessions -> execute();
		
		$found_session = array();
	
		
		while ($found_session[] = $query_find_sessions -> fetch(PDO::FETCH_ASSOC)) {
			
			echo "<tr>";
			echo "<td>";
			$record ++; // = 0 - starting index
			$line_no = $record+1;
			
			echo "$line_no"; 
			echo "</td>";
			
			//$field_value[] = array();

			foreach ($fields as $f){
				
				$field_value[$record][$f] = $found_session[$record][$f];
				
				
				if ($f == "Module"){ // skip these fields
					continue; // skip to the next column
				} else if ($f == "Account_ID"){
					$lecturer_ID = $field_value[$record][$f];
					continue; // skip to the next column
				} else {
					echo "<td>";
					
					if ($f == "Lab_ID"){
						$lab_ID[$record] = $field_value[$record][$f];
						echo "$lab_ID[$record]"; 
						
					} else if ($f == "Type") { // to select Pay Rate type (Tutor = tutorial/project supervision rate
																		//  Demo = lab rate)
						if (strtolower($field_value[$record][$f]) == "demo"){
							$display_type = "Laboratory";
							
						} else if (strtolower($field_value[$record][$f]) == "tutor"){
							$display_type = "Tutorial or Project Supervision";
							
						} else {
							$display_type = $field_value[$record][$f];
						}
						
						echo "<select name=\"$f$record\" title=\"$f$record\">";
						echo "<option value=\"{$field_value[$record][$f]}\" selected>$display_type</option>";
						
						
						$query_pay_type_definitions = $conn->prepare("SELECT Type 
																	  FROM Pay_Data;");
						$query_pay_type_definitions->execute();
						
						while ($pay_type = $query_pay_type_definitions -> fetch(PDO::FETCH_ASSOC)) {
							if (strtolower($pay_type[Type]) == "demo"){
								$display_type = "Laboratory";
							} else if (strtolower($pay_type[Type]) == "tutor"){
								$display_type = "Tutorial or Project Supervision";
							} else {
								$display_type = $pay_type[Type];
							}
							echo "<option value=\"$pay_type[Type]\">$display_type</option>";
						}
						echo "</select>";
						
					} else if ($f == "Room"){ // to type other training in a textarea field
						echo "<textarea name=\"$f$record\" cols=\"30\" rows=\"5\" title=\"$f$record\" maxlength=\"$num_precision[$f]\" onKeyUp=\"checkLen(this.value)\">{$field_value[$record][$f]}</textarea>";
	
					} else if ($f == "Description"){ // to type other training in a textarea field
						echo "<textarea name=\"$f$record\" cols=\"40\" rows=\"5\" title=\"$f$record\" maxlength=\"$num_precision[$f]\" onKeyUp=\"checkLen(this.value)\">{$field_value[$record][$f]}</textarea>";
	
					} else if ($f == "No_Staff") { // to select Skills level for each Skill. Will allow to add more Skill fields to the DB without changing this code
						echo "<select name=\"$f$record\" title=\"$f$record\">
								<option value=\"{$field_value[$record][$f]}\" selected>{$field_value[$record][$f]}</option>
								<option value=\"0\">0 - Staff not required</option>
								<option value=\"1\">1</option>
								<option value=\"2\">2</option>
								<option value=\"3\">3</option>
							</select>";
	
					} else if (substr($f, 0, 5) == "Skill") { // to select Skills level for each Skill. Will allow to add more Skill fields to the DB without changing this code
						echo "<select name=\"$f$record\" title=\"$f$record\">
								<option value=\"{$field_value[$record][$f]}\" selected>{$field_value[$record][$f]}</option>
								<option value=\"0\">0 - Not required</option>
								<option value=\"1\">1 - Required</option>
							</select>";
							
					} else { // for all other fields
						echo "<input type=\"text\" name=\"$f$record\" title=\"$f$record\" value=\"{$field_value[$record][$f]}\" readonly>"; 
					}
				}
				echo "</td>";
			}
			echo "</tr>";
		} // end while loop
		
		echo "</table>";
		echo "<br>";
		
		if($lecturer_ID != ""){
			$query_find_module_leader = $conn -> prepare("SELECT Title, Forename, Initial, Surname
														  FROM Module_Leaders 
														  WHERE Account_ID = $lecturer_ID LIMIT 1;"); 
					
			$query_find_module_leader -> execute();
			
			while ($found_module_leader = $query_find_module_leader -> fetch(PDO::FETCH_ASSOC)) {
				$lecturer_title = $found_module_leader['Title'];
				$lecturer_name = $found_module_leader['Forename'];
				$lecturer_initial = $found_module_leader['Initial'];
				$lecturer_surname = $found_module_leader['Surname'];			
			}
			echo "<br>Module Leader: $lecturer_title $lecturer_surname $lecturer_name $lecturer_initial<br>";
		}
		
		
		echo "<input name=\"btn_update\" type=\"submit\" title=\"Update Lab Details\" value=\"Update\">";
		echo "</form></div><br>";
		
	} 
	catch(PDOException $e){
		echo "Error: " . $e->getMessage();
		$conn = NULL; //close DB connection
	} // end while loop
	
	$conn = NULL; //close DB connection
	
	$records_no = $record + 1;
	$record = -1; // reset record counter
	if ($records_no == 0){
		echo "<br>Sorry, no sessions have been found";
	} else {
		echo "<br>Found: $records_no session(s)";
	}




	if ($_SERVER["REQUEST_METHOD"] == "POST") { // test user input and store the "cleaned" input in variables when form is sent

		for ($record = 0; $record < $records_no; $record++){
			
			foreach ($fields as $f){
				if ($f != "Lab_ID" && $f != "Account_ID" && $f != "Module") {
					$field_value[$record][$f] = test_input($_POST["$f$record"]);
				} else {
					if ($f == "Lab_ID"){
						$field_value[$record][$f] = $lab_ID[$record];
					} else if ($f == "Account_ID") {
						$field_value[$record][$f] = $lecturer_ID;
					} else if ($f == "Module"){
						$field_value[$record][$f] = $module_code;
					}
				}
			}
		}
	}



	
	
	// Update all sessions' requirements
	
	if (isset($_POST['btn_update'])) { 
		
		//update Lab_Requirements table by constructing an SQL query dynamically
		$sql = "UPDATE $db_table SET ";
		$length = count($fields); // total number of fields in the record
		$i = 1; // current field counter

		foreach ($fields as $f){
			if ($f != "Lab_ID" && $f != "Account_ID" && $f != "Module" && $i < $length) {
				$sql .= "$f=:$f, ";
			} else if ($f != "Lab_ID"  && $f != "Account_ID" && $f != "Module" && $i = $length) {
				$sql .= "$f=:$f ";
			}
			$i++;
		}
		
		$sql .= "WHERE Lab_ID=:Lab_ID AND Account_ID=:Account_ID AND Module=:Module;"; // finish constructing query
		//echo "<br>$sql<br><br>";
		
  		
		for ($record = 0; $record < $records_no; $record++){ // perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
		

			foreach ($fields as $f){
				if ($f == "Room" && $field_value[$record][$f] == "") {
					$field_value[$record][$f] = "TBA";
    			}

				if ($f == "Description" && $field_value[$record][$f] == "") {
					$field_value[$record][$f] = "No description given.";
				}				

			}
			
	
	
			try {
				//connect to DB
				require_once 'dbconfig.php';

				$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				
				
				$query_update_lab_requirements = $conn->prepare($sql);
				
				foreach ($fields as $f){// bind all the parameters in query to their values 
					$query_update_lab_requirements->bindParam(":$f", $field_value[$record][$f]); 
				}

				
				$query_update_lab_requirements->execute();

        	}
			catch(PDOException $e){
	   			echo "Error: " . $e->getMessage();
				$conn = NULL; //close DB connection
			}
						
  		} //end of 'for' loop
  		
		//Now redirect to itself with the success message and exit further script
				
		$conn = NULL; //close DB connection
		unset($_POST['btn_update']);
		
		header("Location: $location?message=Data for your lab sessions have been successfully updated&module_code=$module_code");
		exit; // exit further script

	}


	
	echo "<p><span class = \"info\"> $message </span></p>";
	
	echo "<input type=\"button\" name=\"back\" value=\"Back to modules\" title=\"Back to modules\" onClick=\"location.href='select_module_labs.php'\">";

	
	include 'template-bottom.php';

?>
