<?php



	/* MAKE PHD STUDENT/SUPPORT STAFF UNAVAILABLE (IF A PHD STUDENT/STAFF LEAVES) - 2ND PAGE OF 7 */
	


	$header = 'REALLOCATE STAFF TO CLASSES'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 

	//Define the output log file for debugging purposes
	$file = 'log_reallocate_labs.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);


	$student_ID = htmlspecialchars($_GET['student_ID']);
	$student_surname = $_SESSION['studentSurname'];
	$student_forename = $_SESSION['studentForename'];
	$student_initial = $_SESSION['studentInitial'];
	$start_date = htmlspecialchars($_GET['start_date']);
	$end_date = htmlspecialchars($_GET['end_date']);
	$message = htmlspecialchars($_GET['message']); //the message is either "" (if this page is hit for the first time) or "You are about to cancel the Student"
	$start_slot_ID = htmlspecialchars($_GET['start_slot_ID']);
	$end_slot_ID = htmlspecialchars($_GET['end_slot_ID']);

	$current .= "Start date = ".$start_date."\n";
	$current .= "End date = ".$end_date."\n";
	
	if ($student_ID != "" || $student_ID != NULL) {
				
		$new_table_name = "Cancelled_Allocations_$student_ID";
		$_SESSION['studentCancelledSessionsTable'] = $new_table_name;		
		
		
		// create a new back up table and transfer all the cancelled student's data into it

		try {
		
			//connect to DB
			require_once 'dbconfig.php';
	
			$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// find all unique affected slot IDs
			$sql_find_affected_sessions = "SELECT LT.Slot_ID
										   FROM Lab_Timetable LT, Allocations A 
										   WHERE A.Account_ID = $student_ID AND LT.Session_ID = A.Session_ID;";
										   
			$query_find_affected_sessions = $conn->prepare($sql_find_affected_sessions);
			$query_find_affected_sessions->execute();
			
			$slot_IDs = array();
			while ($found_affected_sessions = $query_find_affected_sessions -> fetch(PDO::FETCH_ASSOC)) {
				$temp = $found_affected_sessions['Slot_ID'];
				if ($temp >= $start_slot_ID && $temp <= $end_slot_ID){
					$slot_IDs[] =  $temp;
				}
			}
			
			$slot_IDs = array_unique($slot_IDs); //removes duplicate slot_IDs (should not happen if PHD allocation had been only run once!)
			$slot_IDs = array_values($slot_IDs); //renumbers the array in order
			$no_slots = count($slot_IDs);
			$current .= "$no_slots slots:\n";
			$current .= print_r($slot_IDs, true);
			
			
			// find all unique corresponding affected session IDs
			$session_IDs = array();
			$i = -1; //sessions counter
			foreach($slot_IDs as $key => $val){
				$i++;
				$sql_find_session_IDs = "SELECT A.Session_ID, LT.Lab_ID
										 FROM Allocations A, Lab_Timetable LT
										 WHERE A.Account_ID = $student_ID AND A.Session_ID = LT.Session_ID AND LT.Slot_ID = $val;";
	
				$query_find_session_IDs = $conn->prepare($sql_find_session_IDs);
				$query_find_session_IDs->execute();
				
				while ($found_session_IDs = $query_find_session_IDs -> fetch(PDO::FETCH_ASSOC)) {
					//$session_IDs[$key] =  $found_session_IDs['Session_ID'];
					$temp = $found_session_IDs['Session_ID'];
					$session_IDs[$i][$temp] = $found_session_IDs['Lab_ID'];
				}

			}
			
			$no_sessions = count($session_IDs);
			$current .= "$no_sessions sessions (+ corresponding labs):\n";
			$current .= print_r($session_IDs, true);

			
			// Create a new table/write into existing one the cancelled sessions for the chosen PHD Student for the period they are on leave
			
			$sql_new_allocations_table = "CREATE TABLE IF NOT EXISTS $new_table_name
										 (
											Account_ID int(5),
											Allocation_ID int(5) NOT NULL,
											Session_ID int(11) NOT NULL,
											Lab_ID int(11) NOT NULL,
											Slot_ID int(11) PRIMARY KEY,
											Start_Date date NOT NULL,
											End_Date date NOT NULL,											
											Start_Slot_ID int(11) NOT NULL,
											End_Slot_ID int(11) NOT NULL,
											Confirmed boolean NOT NULL DEFAULT 0,
											Substitution_Acc int(5) DEFAULT NULL
											
										 ) ENGINE = INNODB;" ;

			$query_create_new_allocations_table = $conn->prepare($sql_new_allocations_table);
			$query_create_new_allocations_table->execute();

			foreach($session_IDs as $key => $arrVal){
				foreach($arrVal as $k => $v){
					$session_ID = $k;
					$lab_ID = $v;
					
					if ($slot_IDs[$key] == NULL){ // in case something went wrong and no_slots < no_sessions (should not happen if PHD allocation had been only run once!)
						$current .= "There were more sessions than time slots:\n";
					} else {
						$slot_ID = $slot_IDs[$key];
						$sql_backup_student_allocations = "REPLACE INTO $new_table_name 
														   SELECT $student_ID, Allocation_ID, $session_ID, $lab_ID, $slot_ID, '$start_date', '$end_date', 
														   $start_slot_ID, $end_slot_ID, 0, NULL
													   	   FROM Allocations
													       WHERE Account_ID = $student_ID AND Session_ID = $session_ID;";
		
						$query_backup_student_allocations = $conn->prepare($sql_backup_student_allocations);
						$query_backup_student_allocations->execute();
					}
					
				}
			}
			
			
			echo "<p><span class = \"info\"> $message </span></p>";
			echo "<p><span class = \"error\">Please do not click the \"back\" button in your browser, or any menu buttons! </span></p>";
			echo "<p><a href=\"substitute_staff.php\" title=\"Choose a substitute staff member\"> <u>Click here to proceed to choose substitute staff.</u></a></p>";
			
			file_put_contents($file, $current);
			$conn = NULL; //close DB connection


		}
		catch(PDOException $e){
			$err_output = $e->getMessage();				
			echo "Error: " . $err_output;
			$current .= $err_output;
			$current .= "\n********************* PDO ERROR3 ************************\n";		
			file_put_contents($file, $current);
			$conn = NULL; //close DB connection
		}
	
	
	}
	
	// Write the contents to the log file
	file_put_contents($file, $current);

	include 'template-bottom.php';
	
?>