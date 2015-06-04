<?php
	
	
	
	/* REGISTER A NEW PHD STUDENT/OTHER SUPPORTING STAFF */
	


	$header = 'Register Staff'; /* use this variable to set the header string */

	include 'template-top.php';

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 



	echo "You are registering a <strong>PhD Student</strong> or <strong>Support Staff</strong><br>";			

	$title = $first_name = $initial = $surname = $email = $usname = $uspsw  = $year = "";
	$titleErr = $nameErr = $surnameErr = $emailErr = "";

	$conn = NULL; // connection to the DB is not open
	$location = 'register_phd.php';
	
	$acc_type = 1;
	
	//Define the output log file for debugging purposes
	$file = 'log_reg_phd.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$current .= "\n\nRegister PhD Student\n\n";


	include 'php_functions.php';


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$title = test_input($_POST["title"]);
  		$first_name = test_input($_POST["first_name"]);
  		$initial = test_input($_POST["initial"]);
  		$surname = test_input($_POST["surname"]);
  		$email = test_input($_POST["email"]);
  		$year = $_POST["year"];
	}


	$message=$_GET["message"]; //the message is either "" (if this page is hit for the first time) or "Form is succefully submitted"
	$message = test_input($message);
	$insert_error=$_GET["insert_error"]; //the insert error is either "" (if this page is hit for the first time or no DB insertion errors occurred) or "The PhD Student <PhD Student's full name> already exists!" 
	$insert_error = test_input($insert_error);

	
	if (isset($_POST['btn_reg_submit'])) {
	
  		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
  		$counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message
  
  		do {	
  			if ($title == "") {
        		$err .= "Please choose title. "; //for logging purposes
				$titleErr = "Please choose title";
			}
			if ($first_name == "") {
        		$err .= "Please enter first name. "; //for logging purposes
				$nameErr = "Please enter first name";
    		}
			if ($surname == "") {
        		$err .= "Please enter surname. "; //for logging purposes
				$surnameErr = "Please enter surname";
    		}
			
			if ($email == "") {
        		$err .= "Please enter email address. "; //for logging purposes
				$emailErr = "Please enter email address"; 
    		} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$err .= "Please enter VALID email address. "; //for logging purposes
				$emailErr = "Please enter VALID email address";
			}
			
		
			if ($err != "") {
				$message = "";
				$current .= "Input errors: $err\n";
				$err = "";				
				break; //exit the do..while loop
			}


			try {
            
            	if ($initial == ""){
					$initial = NULL;
				}								

				if ($year == ""){
					$year = NULL;
				}
				
				$status = "Pending"; //default status
				
				// Check if the person is already in the Admin table in DB (do not allow duplicates)
				$person = "PhD Student/Support Staff";
				$db_table = "PHD_Students";
				
				require_once 'check_duplicate_staff.php';
				
				require_once 'create_user_credentials.php';
				$current .= "User Status: $status\n";
				$current .= "Year of study: $year\n";
			
				//insert the new staff member to the general staff table (Account_Data)
				include 'insert_account_data.php';
				
			
				//insert the new staff member to the PHD_Students table
            	$query = $conn->prepare("INSERT INTO $db_table (Title, Account_ID, Forename, Initial, Surname, Status, Year) 
												VALUES (:Title, :Account_ID, :Forename, :Initial, :Surname, :Status, :Year);");
				$query->bindParam(':Title', $title);
				$query->bindParam(':Account_ID', $acc_id);
				$query->bindParam(':Forename', $first_name);
				$query->bindParam(':Initial', $initial);
				$query->bindParam(':Surname', $surname);
				$query->bindParam(':Status', $status);
				$query->bindParam(':Year', $year);				
				$query->execute();
				$current .= "Record for $title $first_name $initial $surname inserted into $db_table. ";
		

				// Write the contents to the log file
				$current .= "\n********************* SUCCESS ************************\n";						
  				file_put_contents($file, $current);

				$conn = NULL; //close DB connection

				//Now redirect to itself with the success message (that clears the form as well) and exit further script
				header("Location: $location?message=The form is successfully submitted. Username = $usname ,Password =  $uspswPlanText");
				exit;
			
        	}
			catch(PDOException $e){
	   			$err_output = $e->getMessage();
				echo "Error: " . $err_output;
				$current .= $err_output;
				$current .= "\n********************* PDO ERROR ************************\n";		
				file_put_contents($file, $current);
				$conn = NULL; //close DB connection
			}			
			$counter = 0;
  		} while ($counter != 0); //end of do...while loop
  
  		unset($_POST['btn_reg_submit']);
 
  		// Write the contents to the log file
		$current .= "\n*************************** SERVER VALIDATION ERRORS *********************\n";		
  		file_put_contents($file, $current);
	}
?> 


	<br>
	<form name="register_staff" method="post" onSubmit="return verifyRegistrationStaff(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<span class="error">* </span> Required fields
		<br><br>
    	<label> Title </label> <span class="error">&nbsp; &nbsp;* <?php echo $titleErr;?></span>
    		<select name="title">
        		<option value="<?php echo $title;?>" selected><?php echo $title;?></option>
				<option value="Mr">Mr</option>
				<option value="Miss">Miss</option>
        		<option value="Ms">Ms</option>
				<option value="Mrs">Mrs</option>
				<option value="Dr">Dr</option>
        		<option value="Prof">Prof</option>
			</select>
            
		<br><br>
		<label> First Name </label><input type="text" name="first_name" value="<?php echo $first_name;?>" >
    	<span class="error">* <?php echo $nameErr;?></span>
		<br><br>
		<label> Initial </label><input type="text" name="initial" maxlength="2" onKeyUp="checkLen(this.value)" value="<?php echo $initial;?>" >
		<br><br>
		<label> Surname </label> <input type="text" name="surname" value="<?php echo $surname;?>">
    	<span class="error">* <?php echo $surnameErr;?></span>
		<br><br>
		<label> E-mail </label><input type="text" name="email" value="<?php echo $email;?>">
    	<span class="error">* <?php echo $emailErr;?></span>
		<br><br>
    	<label> Year of study (if applicable) </label>
    		<select name="year">
        		<option value="<?php echo $year;?>" selected><?php echo $year;?></option>
				<option value="1">1</option>
				<option value="2">2</option>
        		<option value="3">3</option>
				<option value="4">4</option>
			</select>
            
		<br><br>
		<br><br>
		<input type="submit" name="btn_reg_submit" title="Submit registration" value="Submit"> 
    	<input type="reset" name="btn_reg_reset" title="Reset" value="Reset">
	</form>


<?php

	echo "<p><span class = \"info\"> $message </span></p>";
	echo "<p><span class = \"error\"> $insert_error </span></p>";

	echo "<input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>
