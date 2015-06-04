<?php
	
	
	
	/* CANCEL REALLOCATING STAFF TO CLASSES (IF A PHD STUDENT LEAVES) - 4TH PAGE OF 7 */
	


	$header = 'REALLOCATION OF STAFF TO CLASSES CANCELLED'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 

	echo "<p><span class = \"error\"> Please do not refresh page or click the \"BACK\" button in your browser, or any menu buttons! </span></p><br>";

	$student_ID = $_SESSION['studentID'];
	$new_table_name = $_SESSION['studentCancelledSessionsTable'];
	$student_surname = $_SESSION['studentSurname'];
	$student_forename = $_SESSION['studentForename'];
	$student_initial = $_SESSION['studentInitial'];

	$conn = NULL; // connection to the DB is not open

	//Define the output log file for debugging purposes
	$file = 'log_reallocate_labs.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$current .= "\n************* Substitution cancellation ***************\n\n";


	include 'php_functions.php';


	try {
		//connect to DB
		require_once 'dbconfig.php';

		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$null_val = NULL;
		$sql_cancel_substitution = "DELETE FROM $new_table_name 
									WHERE Confirmed = 0;";
	
		$query_cancel_substitution = $conn->prepare($sql_cancel_substitution);
		$query_cancel_substitution->execute();

		$sql_rollback_status = "UPDATE PHD_Students
								SET Status = 'Confirmed'
								WHERE Account_ID = $student_ID;";
	
		$query_rollback_status = $conn->prepare($sql_rollback_status);
		$query_rollback_status->execute();

		
		echo "<p><span class = \"info\"> Either you cancelled the substitution or something went wrong!<br>The student $student_surname $student_forename $student_initial has been reinstated, no substitutions made. </span></p>";

		$current .= "\nSubstitution cancelled. The student $student_surname $student_forename $student_initial has been reinstated, no substitutions made.\n";
		$current .= "\n********************* SUCCESS ************************\n";						
		file_put_contents($file, $current);
		$conn = NULL; //close DB connection
		
		unset($_SESSION["top_student$iter"]);
		unset($_SESSION["no_substitution_alert.$iter"]);
		unset($_SESSION["all_available_students"]);
		unset($_SESSION["allocated_lab_details"]);
		unset($_SESSION["unallocated_lab_details"]);
		
		unset($_SESSION['studentID']);
		unset($_SESSION['studentCancelledSessionsTable']);
		unset($_SESSION['studentSurname']);
		unset($_SESSION['studentForename']);
		unset($_SESSION['studentInitial']);
		unset($_SESSION["substitute_students_IDs"]);
		unset($_SESSION["iter"]);
		unset($_SESSION['select_name']);
		unset($_SESSION["studentErr.1"]);
		unset($_SESSION["studentErr.2"]);

	
	} catch(PDOException $e){
		$err_output = $e->getMessage();				
		echo "Error: " . $err_output;
		$current .= $err_output;
		$current .= "\n********************* PDO ERROR5 ************************\n";		
		file_put_contents($file, $current);
		$conn = NULL; //close DB connection
	}
	
	
	echo "<br><input type=\"button\" name=\"back\" value=\"Back to main menu\" title=\"Back to main menu\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>