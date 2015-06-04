<?php



	/* REGISTER A NEW MODULE */



	$header = 'Register Module'; /* use this variable to set the header string */

	include 'template-top.php';

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	

	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 


	echo "You are registering a <strong> new Module</strong><br>";			
	
	$new_module_code = $new_module_name = "";
	$new_module_codeErr = $new_module_nameErr = "";
	
	$conn = NULL; // connection to the DB is not open
	$location = 'register_module.php';

	//Define the output log file for debugging purposes
	$file = 'log_reg_module.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$current .= "\n\nRegister Module\n\n";


	include 'php_functions.php';


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$new_module_code = test_input($_POST["module_code"]);
		$new_module_code = strtoupper($new_module_code);		
		$new_module_name = test_input($_POST["module_name"]);
		
	}

	
	$message=$_GET["message"]; //the message is either "" (if this page is hit for the first time) or "Form is succefully submitted"
	$message = test_input($message);
	$insert_error=$_GET["insert_error"]; //the insert error is either "" (if this page is hit for the first time or no DB insertion errors occurred) or "The Module <MODULE_CODE> <MODULE_NAME> already exists!" 
	$insert_error = test_input($insert_error);
	


	if (isset($_POST['btn_reg_submit'])) {
	
  		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
  		$counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message
  
  		do {	
			$regex = "/[a-zA-Z][a-zA-Z]([a-zA-Z]|\d)\d\d\d+/";
			if ($new_module_code == "") {
        		$err .= "Please enter Module code. "; //for logging purposes
				$new_module_codeErr = "Please enter Module code";
			} else if(!preg_match($regex, $new_module_code)){
				$err .= "Please enter CORRECT Module code. "; //for logging purposes
				$new_module_codeErr = "Please enter CORRECT Module code";
    		}

			if ($new_module_name == "") {
        		$err .= "Please enter full Module name. "; //for logging purposes
				$new_module_nameErr = "Please enter full Module name";
    		}
		

			if ($err != "") {
				$message = "";
				$current .= "Input errors: $err\n";
				$err = "";				
				break; //exit the do..while loop
			}
			
 	
			try {
				
				require_once 'add_module.php';
				
				// Write the contents to the log file
				$current .= "\n********************* SUCCESS ************************\n";												
  				file_put_contents($file, $current);
				
				$conn = NULL; //close DB connection

				//Now redirect to itself with the success message (that clears the form as well) and exit further script
				header("Location: $location?message=The form is successfully submitted");
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
		$current .= "\n*********************** SERVER VALIDATION ERRORS *********************\n";
  		file_put_contents($file, $current);
	}

?> 


	<br>
	<form name="register_module" method="post" onSubmit="return verifyRegistrationModule(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<span class="error">* </span> Required fields
		<br><br>
		<label> Module Code </label><input type="text" name="module_code" maxlength="8" value="<?php echo $new_module_code;?>">
    	<span class="error">* <?php echo $new_module_codeErr;?></span>
		<br><br>
		<label> Module Name </label><input type="text" name="module_name" value="<?php echo $new_module_name;?>">
    	<span class="error">* <?php echo $new_module_nameErr;?></span>
		<br><br>        
                
		<br><br>
		<input type="submit" name="btn_reg_submit" title="Submit registration" value="Submit"> 
    	<input type="reset" name="btn_reg_reset" title="Reset" value="Reset">
	</form>
	<br>


<?php	

	echo "<p><span class = \"info\"> $message </span></p>";
	echo "<p><span class = \"error\"> $insert_error </span></p>";

	echo "<input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>
