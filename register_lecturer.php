<?php
	


	/* REGISTER A NEW MODULE LEADER */



	$header = 'Register Staff'; /* use this variable to set the header string */

	include 'template-top.php';
	
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 

	
	
	echo "You are registering a <strong>Module Leader</strong><br>";			
	
	$title = $first_name = $initial = $surname = $email = $usname = $uspsw = $module = $new_module_code = $new_module_name = "";
	$module_code = array(); // to parse module code and module name from the combined string
	$titleErr = $nameErr = $surnameErr = $emailErr = $module_codeErr = $new_module_codeErr = $new_module_nameErr = "";

	$conn = NULL; // connection to the DB is not open
	$location = 'register_lecturer.php';
	
	$acc_type = 2;

	//Define the output log file for debugging purposes
	$file = 'log_reg_lecturer.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$current .= "\n\nRegister Module Leader\n\n";


	include 'php_functions.php';


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$module = test_input($_POST["module"]);
  		$module_code = explode(" ", $module); // separate Module Code from the name of the Module (Module Code is in $module_code[0])
		$title = test_input($_POST["title"]);
  		$first_name = test_input($_POST["first_name"]);
  		$initial = test_input($_POST["initial"]);
  		$surname = test_input($_POST["surname"]);
  		$email = test_input($_POST["email"]);
		
		$new_module_name = test_input($_POST["new_module_name"]);
		$new_module_code = test_input($_POST["new_module_code"]);
		$new_module_code = strtoupper($new_module_code);
		
		$current .= "Chosen module: ".$module."\n";
		$current .= "New module: ".$new_module_code." ".$new_module_name."\n";
	}

	
	$message=$_GET["message"]; //the message is either "" (if this page is hit for the first time) or "Form is succefully submitted"
	$message = test_input($message);
	$insert_error=$_GET["insert_error"]; //the insert error is either "" (if this page is hit for the first time or no DB insertion errors occurred) or "The Module Leader <Leader's full name> already exists!" 
	$insert_error = test_input($insert_error);
	$insert_error = $insert_error;

	
	if (isset($_POST['btn_reg_submit'])) {
	
  		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
  		$counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message
		$new_module_required = false; // checks if the new module is required
  		
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
			
			if ($new_module_code == "" && $new_module_name == ""){ // if the new Module is not required
				if ($module_code[0] == "") {
					$err .= "Please choose Module. "; //for logging purposes
					$module_codeErr = "Please choose Module";
				}
			// if a New Module is required
			} else if ($new_module_code != "" && $new_module_name == ""){
				$new_module_required = true;
				$err .= "Please enter New Module's name. ";
				$new_module_nameErr = "Please enter New Module's name";
			} else if ($new_module_code == "" && $new_module_name != ""){
				$new_module_required = true;
				$err .= "Please enter New Module's code. ";
				$new_module_codeErr = "Please enter New Module's code";
			} else { // if all the other fields are correctly filled then insert the New Module and the Module Leader into DB
				$new_module_required = true;
				$regex = "/[a-zA-Z][a-zA-Z]([a-zA-Z]|\d)\d\d\d+/";
				if(!preg_match($regex, $new_module_code)){
					$err .= "Please enter CORRECT New Module code. "; //for logging purposes
					$new_module_codeErr = "Please enter CORRECT New Module code";
				}
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
			
				//connect to DB
				require_once 'dbconfig.php';

				$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				// Check if the person is already in the Module_Leaders table in DB (do not allow duplicates)
				$person = "Module Leader";
				$db_table = "Module_Leaders";
								 
				require_once 'check_duplicate_staff.php';

				//if there is no duplicate Module Leader in DB, check if the selected Module already has a Module Leader
				if (!$new_module_required){ // when the Module Leader chooses an existing module, NOT creates a new one
					$chosen_module = $module_code[0];
					$current .= "chosen module = $chosen_module\n";
					$module_leader_found_title = "";
					
					$query_two_module_leaders = $conn->prepare("SELECT ML.Account_ID, ML.Title, ML.Forename, ML.Initial, ML.Surname
															    FROM Module_Leaders ML
																WHERE ML.Account_ID = 
															   		(SELECT LM.Account_ID 
																	 FROM Leader_Module LM
										 							 WHERE LM.Module_Code='$chosen_module' LIMIT 1);");
					
					$query_two_module_leaders->execute();
					
					while ($another_module_leader_found = $query_two_module_leaders -> fetch(PDO::FETCH_ASSOC)) {
						
						$module_leader_found_title = $another_module_leader_found['Title'];
						$module_leader_found_forename = $another_module_leader_found['Forename'];
						$module_leader_found_initial = $another_module_leader_found['Initial'];
						$module_leader_found_surname = $another_module_leader_found['Surname'];
						
					} // end of while loop
					
					if ($module_leader_found_title != ""){
						$current .= "Module Leader '$module_leader_found_title $module_leader_found_forename $module_leader_found_initial $module_leader_found_surname' is already the Module Leader for '$chosen_module'\n";
						$current .= "\n************** TWO MODULE LEADERS FOUND FOR SAME MODULE - ERROR ***************\n";
						// Write the contents to the log file
						file_put_contents($file, $current);
				
						$conn = NULL; //close DB connection
						//Now redirect to itself with the error message (that clears the form as well) and exit further script
						header("Location: $location?insert_error=Module Leader '$module_leader_found_title $module_leader_found_forename $module_leader_found_initial $module_leader_found_surname' is already the Module Leader for '$chosen_module'. Please delete the existing Module Leader first.");
						exit;
					}
				}
			
				
				try {
					
					require_once 'create_user_credentials.php'; 
					
				} catch (PDOException $e){
					  $err_output = $e->getMessage();				
					  echo "Error1: " . $err_output;
					  $current .= $err_output;
					  $current .= "\n********************* PDO ERROR1 ************************\n";		
					  file_put_contents($file, $current);
					  $conn = NULL; //close DB connection
				}

				// if a New Module is required, insert it into the DB
				if ($new_module_required){
					try {
						require_once 'add_module.php';
					} catch (PDOException $e){
						$err_output = $e->getMessage();				
	   					echo "Error2: " . $err_output;
						$current .= $err_output;
						$current .= "\n********************* PDO ERROR2 ************************\n";		
  						file_put_contents($file, $current);
						$conn = NULL; //close DB connection
					}
						$module_code[0] = $new_module_code; // use the New Module's Code further in the script
				}
				
		
				//insert the new staff member to the general staff table (Account_Data)
				include 'insert_account_data.php';
				
				
				//insert the new staff member to the Module_Leaders table
				$query = $conn->prepare("INSERT INTO $db_table (Title, Account_ID, Forename, Initial, Surname) 
												VALUES (:Title, :Account_ID, :Forename, :Initial, :Surname);");
				$query->bindParam(':Title', $title);
				$query->bindParam(':Account_ID', $acc_id);
				$query->bindParam(':Forename', $first_name);
				$query->bindParam(':Initial', $initial);
				$query->bindParam(':Surname', $surname);
				$query->execute();
				$current .= "Record for $title $first_name $initial $surname inserted into $db_table. ";
				
				//insert the new module leader to the Leader_Module table
				$query = $conn->prepare("INSERT INTO Leader_Module (Module_Code, Account_ID) 
												VALUES (:Module_Code, :Account_ID);");
				$query->bindParam(':Module_Code', $module_code[0]);
				$query->bindParam(':Account_ID', $acc_id);
				$query->execute();
				$current .= "Record inserted into Leader_Module. ";
		

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
				echo "Error3: " . $err_output;
				$current .= $err_output;
				$current .= "\n********************* PDO ERROR3 ************************\n";		
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
	<form name="register_staff" method="post" onSubmit="return verifyRegistrationLecturer(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
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
        <label> Module </label>
         
		<?php
		
			if (isset($_POST["ch"])){
				echo "&nbsp; &nbsp; &nbsp; &nbsp;";
            } else {
				echo "<span class=\"error\">&nbsp; &nbsp;* $module_codeErr </span>";
			}
			
			include	'module_dropdown.php';
			
		?>

        
		<!-- Expanding subform for entering a new Module --> 
        
        <br><br>  
        <div class="container">
			<input class="expand_input" id="ch" name="ch" type="checkbox" <?php echo ($new_module_required == true ? "checked" : ""); ?>>
			<label class="expand_label" for="ch"></label>
			<div class="expand">
				<br>
				<label> New Module Code </label><input type="text" name="new_module_code" maxlength="8" value="<?php echo $new_module_code; ?>">
    			<span class="error">* <?php echo $new_module_codeErr; ?></span>
				<br><br>
				<label> New Module Name </label><input type="text" name="new_module_name" value="<?php echo $new_module_name; ?>">
    			<span class="error">* <?php echo $new_module_nameErr; ?></span>
				<br><br>        
            </div>
		</div>
		<br><br><br>
		<!-- -->
        
		<input type="submit" name="btn_reg_submit" title="Submit registration" value="Submit"> 
    	<input type="reset" name="btn_reg_reset" title="Reset" value="Reset">
	</form>


<?php
	
	echo "<p><span class = \"info\"> $message </span></p>";
	echo "<p><span class = \"error\"> $insert_error </span></p>";

	echo "<input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>
