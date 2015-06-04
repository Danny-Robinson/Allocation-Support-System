<?php
	
	
	
	/* CONFIRM REALLOCATING STAFF TO CLASSES (IF A PHD STUDENT LEAVES) - 5TH PAGE OF 7 */
	


	$header = 'REALLOCATION OF STAFF TO CLASSES CONFIRMED'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 
	
	echo "<p><span class = \"error\"> Please do not refresh page or click the \"BACK\" button in your browser, or any menu buttons! </span></p><br>";

	include 'php_functions.php';

	$student_ID = $_SESSION['studentID'];
	$new_table_name = $_SESSION['studentCancelledSessionsTable'];
	$student_surname = $_SESSION['studentSurname'];
	$student_forename = $_SESSION['studentForename'];
	$student_initial = $_SESSION['studentInitial'];
	
	$iter = $_SESSION["iter"]; // get the user choice number
	$top_student = $_SESSION["top_student.$iter"]; // get the user chosen substituting students
	$unallocated_labs = $_SESSION["no_substitution_alert.$iter"]; // get the list of unallocate labs (if any)
	$available_students = $_SESSION["all_available_students"][$iter]; // get the list of all confirmed available students whose slots do not clash with the unallocated slots (regardless of skills or the PHD_Availability table)
	$allocated_lab_details = $_SESSION["allocated_lab_details"][$iter];
	$unallocated_lab_details = $_SESSION["unallocated_lab_details"][$iter];
	$substitute_students_IDs = $_SESSION["substitute_students_IDs"][$iter];
	
	$conn = NULL; // connection to the DB is not open

	//Define the output log file for debugging purposes
	$file = 'log_reallocate_labs.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$current .= "\n************ Substitution confirmation **************\n\n";

	$current .= "\nIteration = $iter\n";
	$current .= "no_substitution_alert.$iter";
	
	$current .= "\nTop suitable Students for each slot-lab:\n";
	$current .= print_r($top_student, true);

	$current .= "\nAllocated lab details:\n";
	$current .= print_r($allocated_lab_details, true);

	$current .= "\nUnallocated labs:\n";
	$current .= print_r($unallocated_labs, true);

	$current .= "\nUnallocated lab details:\n";
	$current .= print_r($unallocated_lab_details, true);

	$current .= "\nAll available students:\n";
	$current .= print_r($available_students, true);

	$current .= "\nManually substituted students:\n";
	$current .= print_r($substitute_students_IDs, true);


	try {
		//connect to DB
		require_once 'dbconfig.php';

		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$query_cancel_student = $conn->prepare("UPDATE PHD_Students SET Status = 'Cancelled'
												WHERE Account_ID = $student_ID;"); 
		$query_cancel_student->execute();
		
		if($iter == 1){
			// for top substituting students (1 for each lab)
			foreach($top_student as $acc => $sub_arr){
				foreach($sub_arr as $slot => $sub_arr1){
					foreach($sub_arr1 as $lab => $cos){
				
						include 'perform_substitution.php';
					
					}
				}
			}
		}

		$sorted_substitute_students_IDs = array();
		//Sort substitute_students_IDs by account -> slot -> lab
		foreach ($substitute_students_IDs as $slot => $sub_arr){
			foreach($sub_arr as $lab => $acc){
				if($acc == NULL){
					$current .= "\n*** CANNOT WRITE TO DB: STUDENTS ARE NOT DEFINED ***\n";
					file_put_contents($file, $current);
					echo "*********** COULD NOT WRITE TO DB: STUDENT ACCOUNTS ARE NULL! *************";
					$conn = NULL; //close DB connection
					header("Location: cancel_substitution.php");
				}
				$sorted_substitute_students_IDs[$acc][$slot] = $sub_arr;
			}
		}
		$current .= "\nManually substituted SORTED students:\n";
		$current .= print_r($sorted_substitute_students_IDs, true);

	
		foreach ($sorted_substitute_students_IDs as $acc => $sub_arr){
			foreach($sub_arr as $slot => $sub_arr1){
				foreach($sub_arr1 as $lab => $id){
			
				include 'perform_substitution.php';
				
				}
				
			}
		}
		
		echo "<p><span class = \"info\"> The student $student_surname $student_forename $student_initial has been \"substituted\", and their sessions have been allocated to the chosen students. </span></p>";

		$current .= "\nSubstitution confirmed.\n";
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
		$current .= "\n********************* PDO ERROR6 ************************\n";		
		file_put_contents($file, $current);
		$conn = NULL; //close DB connection
	}
	
	
	echo "<br><input type=\"button\" name=\"back\" value=\"Back to main menu\" title=\"Back to main menu\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';
	
?>