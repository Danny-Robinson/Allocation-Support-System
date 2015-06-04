<?php



	/* FORM TO SELECT WHICH MODULE'S LABS TO VIEW FOR BOTH ADMIN AND MODULE LEADER*/



	$header = "VIEW MODULE'S LABS"; /* use this variable to set the header string */

	include 'template-top.php';

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	

	if ($acc_type != 0 && $acc_type != 2) { // if not Admin or Module Leader
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator or Module Leader</span><br>";
		exit; // exit further script
	} 


	include 'php_functions.php';


	echo "Please select module to view its labs<br>";			
	
	$module = $moduleErr = "";
	$module_code = array(); // to parse module code and module name from the combined string

	$message=$_GET["message"]; //the message is either "" or an error message for the Module Leader who selected wrong Module
	$message = test_input($message);

	$conn = NULL; // connection to the DB is not open
	$location = $file = NULL;
	
	//Define the output log file for debugging purposes
	if($acc_type == 0){	//admin
		$location = 'admin_view_module_labs.php';
		$file = 'log_admin_view_labs.txt';
	}
	if($acc_type == 2){ //module leader
		$location = 'lecturer_request_class_support.php';
		$file = 'log_lecturer_request_class_support.txt';
	}
	
	// Open the file to get existing content
	//$current = file_get_contents($file);
	//$current .= "\n\nList of Module's Labs\n\n";



	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$module = $_POST["module"];
		$module_code = explode(" ", $module); // separate Module Code from the name of the Module (Module Code is in $module_code[0])


	}

	


	if (isset($_POST['btn_module_submit'])) {
	
  		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
  		$counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message
  
  		do {	
			if ($module == "") {
        		$err .= "Please select Module. "; //for logging purposes
				$moduleErr = "Please select Module";
    		}


			if ($err != "") {
				$message = "";
				//$current .= "Input errors: $err\n";
				$err = "";				
				break; //exit the do..while loop
			}
			
 	
			try {
				
				
				// Write the contents to the log file
  				//file_put_contents($file, $current);
				
				$conn = NULL; //close DB connection

				//Now send the module code to the script to select all Module's labs, and exit further script
				header("Location: $location?message=&module_code=$module_code[0]");
				exit;
        	}
			catch(PDOException $e){
				$err_output = $e->getMessage();				
				echo "Error: " . $err_output;
				//$current .= $err_output;
				//$current .= "\n********************* PDO ERROR ************************\n";		
				//file_put_contents($file, $current);
				$conn = NULL; //close DB connection
			}
			$counter = 0;
  		} while ($counter != 0); //end of do...while loop
		
  		unset($_POST['btn_module_submit']);

   		// Write the contents to the log file
		//$current .= "\n*********************** SERVER VALIDATION ERRORS *********************\n";
  		//file_put_contents($file, $current);
	}

?> 


	<br>
	<form name="select_module" method="post" onSubmit="return verifyModuleSelected(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<span class="error">* </span> Required fields
		<br><br>
        <label> Module<span class="error">* <?php echo $moduleErr;?></span> </label>

		<?php
		
			include	'module_dropdown.php';
			
		?>
		
        <br><br><br>
		<input type="submit" name="btn_module_submit" title="Submit" value="Submit"> 
    	<input type="reset" name="btn_module_reset" title="Reset" value="Reset">
	</form>
	<br>


<?php	

	echo "<p><span class = \"error\"> $message </span></p>";

	if ($acc_type == 2) { // button for the module leader
		echo "<input type=\"button\" name=\"back\" value=\"Back to main menu\" title=\"Back to main menu\" onClick=\"location.href='module_leader_view.php'\">";
	}

	include 'template-bottom.php';

?>
