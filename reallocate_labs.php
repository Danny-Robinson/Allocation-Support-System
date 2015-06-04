<?php
	
	
	
	/* REALLOCATE REMAINING STAFF TO CLASSES (IF A PHD STUDENT LEAVES) - 1ST PAGE OF 7 */
	

	$header = 'REALLOCATE STAFF TO CLASSES'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 

	echo "Please choose a PhD Student/Support Staff <strong>who is leaving/absent</strong><br><br>";			

	$student_txt = $student_title = $student_surname = $student_forename = $student_initial = "";
	$supervisor_title = $supervisor_surname = $supervisor_name = $supervisor_initial = $start_date = $end_date = "";
	$studentErr = $start_dateErr = $end_dateErr = "";
	$student_ID = $supervisor_ID = NULL;
	$student = array(); // to store student details
	$first_week_timestamp = $start_week_timestamp = $end_week_timestamp = NULL;
	$start_slot_ID = $end_slot_ID = NULL;


	$conn = NULL; // connection to the DB is not open
	$location = 'process_student_reallocation.php';
	
	//Define the output log file for debugging purposes
	$file = 'log_reallocate_labs.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$current .= "\n\nReallocate staff to classes when a student leaves\n\n";


	include 'php_functions.php';



	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$student_txt = test_input($_POST["student"]);
  		$student = explode(" ", $student_txt); // separate student fields
		$student_ID = $student[0];
		$student_title = $student[1];
		$student_surname = $student[2];
		$student_forename = $student[3];
		$student_initial = $student[4];
		$supervisor_title = $student[6];
		$supervisor_surname = $student[7];
		$supervisor_name = $student[8];
		$supervisor_initial = $student[9];
		
		
		$start_date = test_input($_POST["start_date"]);
		$end_date = test_input($_POST["end_date"]);
		
		
		$current .= "Student: ".$student_txt."\n";
	}

	
?>

	<form name="cancel_student" method="post" onSubmit="return verifyCancelStudent(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<span class="error">* </span> Required fields
		<br><br>
		<label> PhD Students/Support Staff: </label>


<?php	


	
	if (isset($_POST['btn_cancel_student_submit'])) {
	
  		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
  		$counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message
  		
		do {	
			if ($student_ID == "" || $student_ID == NULL) {
				$err .= "Please select a student/support staff. "; //for logging purposes
				$studentErr = "Please select a student/support staff";
			}
			
			if ($start_date == "" || $start_date == NULL) {
				$err .= "Please enter absense start date. "; //for logging purposes
				$start_dateErr = "Please enter absense start date";
			}
			
			if ($end_date == "" || $end_date == NULL) {
				$err .= "Please enter absense end date. "; //for logging purposes
				$end_dateErr = "Please enter absense end date";
			}
			
			if ($start_date != "" && validateDate($start_date) != true) {
				$err .= "Please enter CORRECT absense start date<br>";
				$start_dateErr = "Please enter CORRECT absense start date";
			}

			if ($end_date != "" && validateDate($end_date) != true) {
				$err .= "Please enter CORRECT absense end date<br>";
				$end_dateErr = "Please enter CORRECT absense end date";
			}

			if ($start_date != "" && $end_date != "" && validateDate($start_date) == true && validateDate($end_date) == true && $start_date >= $end_date) {
				$err .= "The end date must be later than the start date<br>";
				$end_dateErr = "The end date must be later than the start date";
				$start_dateErr = "";
			}



			if ($err != "") {
				$current .= "Input errors: $err\n";
				$err = "";
				break; //exit the do..while loop
			}
	
			try {
				//connect to DB
				require_once 'dbconfig.php';

				$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


				// Find corresponding start slot number
				
				$start_slot_timestamp = strtotime((string)$start_date); // timestamp of the start date
				$end_slot_timestamp = strtotime((string)$end_date); // timestamp of the end date
				
				$sql_find_first_week = "SELECT Week_Com
										FROM Week_Date
										WHERE Slot_ID = 1;";
											   
				$query_find_first_week = $conn->prepare($sql_find_first_week);
				$query_find_first_week->execute();
				
	
				while ($found_first_week = $query_find_first_week -> fetch(PDO::FETCH_ASSOC)) {
					$first_week_timestamp = $found_first_week['Week_Com'];
					$current .= "First week timestamp in the semester... ".$first_week_timestamp."\n";
				}
				
				
				$sql_find_last_week = "SELECT Week_Com
									   FROM Week_Date 
									   ORDER BY Week_Com DESC LIMIT 1;";
				$query_find_last_week = $conn->prepare($sql_find_last_week); 
				$query_find_last_week->execute();

				while ($found_last_week = $query_find_last_week -> fetch(PDO::FETCH_ASSOC)) {
					$end_of_semester_timestamp = $found_last_week['Week_Com'];
				}


				$current .= "Last week timestamp in the semester... ".$end_of_semester_timestamp."\n";
				
				
				if ($start_slot_timestamp < $first_week_timestamp || $start_slot_timestamp > ($end_of_semester_timestamp + 5 * 24 * 3600)){
					$start_dateErr = "The start date is outside this semester. Please try again";
					$current .= "\nThe start date is outside this semester. Please try again\n";
					break;
				}
				
				$current .= "Start of leave: ".$start_slot_timestamp." End of leave: ".$end_slot_timestamp."\n";
				
				

				// find start and end slot IDs (convert start_date and end_date into slot IDs):
				
				// 1. find nearest start week timestamp
				
				$sql_find_nearest_week = "SELECT Week_Com
										  FROM Week_Date ORDER BY Week_Com;";
											   
				$query_find_nearest_week = $conn->prepare($sql_find_nearest_week);
				$query_find_nearest_week->execute();
				
				
				$x = 2 * 24 * 3600; // weekend (2 days) in seconds
				$previous_week_timestamp = $first_week_timestamp;
				
				while ($found_nearest_week = $query_find_nearest_week -> fetch(PDO::FETCH_ASSOC)) {
					$threshold = $found_nearest_week['Week_Com'] - $x; // current week - previous weekend
					if ($start_slot_timestamp == $found_nearest_week['Week_Com']){ // it is current week's Monday
						$start_week_timestamp = $found_nearest_week['Week_Com'];
						break;
					} else if($start_slot_timestamp < $found_nearest_week['Week_Com']){ // it is either current or previous week
						if ($start_slot_timestamp < $threshold){ // it is Mon to Fri of previous week
							$start_week_timestamp = $previous_week_timestamp;
							break;
						} else { // it is Sat or Sun of previous week
							$start_week_timestamp = $found_nearest_week['Week_Com'];
							$start_slot_timestamp = $start_week_timestamp;
							break;
						}
					}
					
					$start_week_timestamp = $found_nearest_week['Week_Com'];					
					$previous_week_timestamp = $found_nearest_week['Week_Com'];
				}
				
				$current .= "\nNearest start week timestamp: ".$start_week_timestamp."\n";
				$current .= "Start slot timestamp: ".$start_slot_timestamp."\n";
				
				$extra_slots = round((abs($start_slot_timestamp - $start_week_timestamp)/3600/24)*9); // how many slots between start date and start of nearest week
				$current .= "Extra slots ".$extra_slots."\n";
				
				$sql_find_slot_ID = "SELECT Slot_ID
									 FROM Week_Date 
									 WHERE Week_Com = '$start_week_timestamp';";
									 
				$query_find_slot_ID = $conn->prepare($sql_find_slot_ID); 
				$query_find_slot_ID->execute();

				while ($found_slot_ID = $query_find_slot_ID -> fetch(PDO::FETCH_ASSOC)) {
					$start_slot_ID = $found_slot_ID['Slot_ID'];
					break;
				}
				
				$start_slot_ID += $extra_slots;
				
				$current .= "Start slot No. ".$start_slot_ID."\n";
				$current .= "----------------------------------------------------------------\n\n";
				
				
				// 2. find nearest end week timestamp
				
				$sql_find_nearest_week = "SELECT Week_Com
										  FROM Week_Date ORDER BY Week_Com;";
											   
				$query_find_nearest_week = $conn->prepare($sql_find_nearest_week);
				$query_find_nearest_week->execute();
				
				
				$previous_week_timestamp = $first_week_timestamp;
				
				while ($found_nearest_week = $query_find_nearest_week -> fetch(PDO::FETCH_ASSOC)) {
					if ($end_slot_timestamp == $found_nearest_week['Week_Com']){ // it is current week's Monday
						$end_week_timestamp = $found_nearest_week['Week_Com'];
						break;
					} else if($end_slot_timestamp < $found_nearest_week['Week_Com']){ // it is previous week
						$end_week_timestamp = $previous_week_timestamp;
						break;
					}
					$end_week_timestamp = $found_nearest_week['Week_Com'];					
					$previous_week_timestamp = $found_nearest_week['Week_Com'];
				}
				
				$current .= "Nearest end week timestamp: ".$end_week_timestamp."\n";
				$current .= "End slot timestamp: ".$end_slot_timestamp."\n";
				
				$extra_slots = round((abs($end_slot_timestamp - $end_week_timestamp)/3600/24)*9); // how many slots between start date and start of nearest week
				$current .= "Extra slots ".$extra_slots."\n";
				
				$sql_find_slot_ID = "SELECT Slot_ID
									 FROM Week_Date 
									 WHERE Week_Com = '$end_week_timestamp';";
									 
				$query_find_slot_ID = $conn->prepare($sql_find_slot_ID); 
				$query_find_slot_ID->execute();

				while ($found_slot_ID = $query_find_slot_ID -> fetch(PDO::FETCH_ASSOC)) {
					$end_slot_ID = $found_slot_ID['Slot_ID'];
					break;
				}
				if($extra_slots >= 45){ // if end date is beyond the semester dates/or at the weekend
					$extra_slots = 44;
				}
				
				$end_slot_ID += $extra_slots;
				
				$current .= "End slot No. ".$end_slot_ID."\n";

		
			}
			catch(PDOException $e){
				$err_output = $e->getMessage();				
				echo "Error2: " . $err_output;
				$current .= $err_output;
				$current .= "\n********************* PDO ERROR2 ************************\n";		
				file_put_contents($file, $current);
				$conn = NULL; //close DB connection
			}


			$conn = NULL; //close DB connection
			file_put_contents($file, $current);
			
			$_SESSION['studentID'] = $student_ID;
			$_SESSION['studentSurname'] = $student_surname;
			$_SESSION['studentForename'] = $student_forename;
			$_SESSION['studentInitial'] = $student_initial;

			//Now redirect to the processing student reallocation file and exit further script
			header("Location: $location?student_ID=$student_ID&start_date=$start_date&end_date=$end_date&message=On the next page you will be able to cancel the Student (support staff) $student_title $student_surname $student_forename $student_initial (Supervisor: $supervisor_title $supervisor_surname $supervisor_name $supervisor_initial) and to reassign their classes to other students.&start_slot_ID=$start_slot_ID&end_slot_ID=$end_slot_ID");
			exit;

			$counter = 0;
			
		} while ($counter != 0); //end of do...while loop
  
		$conn = NULL; //close DB connection
		unset($_POST['btn_cancel_student_submit']);
 
		// Write the contents to the log file
		$current .= "\n*********************** SERVER VALIDATION ERRORS *********************\n";
		file_put_contents($file, $current);
	}


	
	// Provide a list of all allocated PhD students to select from (student who is leaving)
	try {
		
		//connect to DB
		require_once 'dbconfig.php';

		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$query_allocated_students = $conn->prepare("SELECT DISTINCT S.Account_ID, Title, Forename, Initial, Surname, Supervisor
													FROM PHD_Students S, Allocations A
													WHERE S.Account_ID = A.Account_ID AND (S.Status = 'Confirmed' OR S.Status = 'confirmed');");
		$query_allocated_students->execute();
		
		echo "<select name=\"student\">";
		echo "<option value=\"$student_txt\" selected>$student_txt</option>";
		
		while ($selected_student = $query_allocated_students -> fetch(PDO::FETCH_ASSOC)) {
			$student_ID = $selected_student['Account_ID'];
			$student_title = $selected_student['Title'];
			$student_surname = $selected_student['Surname'];
			$student_name = $selected_student['Forename']; 
			$student_initial = $selected_student['Initial'];
			$supervisor_ID = $selected_student['Supervisor'];
						

// get student's supervisor details
			
			$query_get_supervisor = $conn->prepare("SELECT Account_ID, Title, Forename, Initial, Surname
													FROM Supervisors
													WHERE Account_ID = '$supervisor_ID' LIMIT 1;");
			$query_get_supervisor->execute();
			
			while ($selected_supervisor = $query_get_supervisor -> fetch(PDO::FETCH_ASSOC)) {
				$supervisor_title =  $selected_supervisor['Title'];
				$supervisor_name =  $selected_supervisor['Forename'];
				$supervisor_initial =  $selected_supervisor['Initial'];
				$supervisor_surname =  $selected_supervisor['Surname'];
			}

			echo "<option value=\"$student_ID $student_title $student_surname $student_name $student_initial Supervisor: $supervisor_title $supervisor_surname $supervisor_name $supervisor_initial\">$student_ID $student_title $student_surname $student_name $student_initial Supervisor: $supervisor_title $supervisor_surname $supervisor_name $supervisor_initial</option>";
			
			// reset supervisor details ready for the next line
			$supervisor_title = $supervisor_surname = $supervisor_name = $supervisor_initial = "";
		}

		echo "</select><span class=\"error\">&nbsp;&nbsp;&nbsp;* $studentErr</span><br>";
		
		echo "<br><br><label> Absent from: </label>";
		echo "<input type=\"text\" name=\"start_date\" title=\"Start Date\" placeholder=\"(YYYY-MM-DD)\"  maxlength=\"10\" onKeyUp=\"checkLen(this.value)\" value=\"$start_date\"><span class=\"error\">* $start_dateErr</span><br>";
		echo "<br><label> back on: </label>";
		echo "<input type=\"text\" name=\"end_date\" title=\"End Date (if forever - any date after the end of current semester)\" placeholder=\"(YYYY-MM-DD)\"  maxlength=\"10\" onKeyUp=\"checkLen(this.value)\" value=\"$end_date\"><span class=\"error\">* $end_dateErr</span><br>";
		

?>

		<br><br>
		<input type="submit" name="btn_cancel_student_submit" title="Submit" value="Submit"> 
		<input type="reset" name="btn_cancel_student_reset" title="Reset" value="Reset">
		</form>
		

<?php

		$conn = NULL; //close DB connection
	
	}
	catch(PDOException $e){
		$err_output = $e->getMessage();				
		echo "Error1: " . $err_output;
		$current .= $err_output;
		$current .= "\n********************* PDO ERROR1 ************************\n";		
		file_put_contents($file, $current);
		$conn = NULL; //close DB connection
	}


	
	echo "<br><input type=\"button\" name=\"back\" value=\"Back to main menu\" title=\"Back to main menu\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';


?>