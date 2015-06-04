<?php
	
	
	
	/* PROCESS PHD STUDENT REALLOCATION TO CLASSES (IF A PHD STUDENT LEAVES) - 3RD PAGE OF 7 */	


	$header = 'REALLOCATE STAFF TO CLASSES'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 

	echo "<p><span class = \"error\"> Please do not refresh page or click the \"BACK\" button in your browser, or any menu buttons! </span></p>";

	$student_ID = $_SESSION['studentID'];
	$new_table_name = $_SESSION['studentCancelledSessionsTable'];
	$student_surname = $_SESSION['studentSurname'];
	$student_forename = $_SESSION['studentForename'];
	$student_initial = $_SESSION['studentInitial'];
	
	$other_students = $available_students = $skill_names = $required_skills_vector = $student_skills_vector = $student_vector = $required_vector = array();
	
	$all_other_students = $all_slots_labs = $student_lab_skill_rank = $lab_skills_vect_norm = $student_skills_vect_norm = $cos_arr = array();
	$all_other_students_slot_labs = $slot_lab_all_other_students_order = $all_other_students_slot_labs_current_iter = $slot_lab_all_suitable_students_order = array();

	echo "Review the options (best skills fit first) and make your choice,  OR<input type=\"button\" name=\"Cancel\" value=\"Cancel reallocation\" title=\"Cancel reallocation and reinstate the student $student_surname $student_forename $student_initial\" onClick=\"location.href='cancel_substitution.php'\"><br>";			
	
	$conn = NULL; // connection to the DB is not open
	
	//Define the output log file for debugging purposes
	//$file = 'log_reallocate_labs.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);


	include 'php_functions.php';

	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$substitute_student = array();
		$substitute_student_txt = "";

		
		foreach($_SESSION["all_available_students"] as $iteration => $sub_arr){
			if ($_POST['choice'] != "Commit$iteration"){
				//$current .= "Button Commit$iteration is not clicked!!!!!";
				continue;
			}
			foreach($sub_arr as $index => $sub_arr1){
				foreach($sub_arr1 as $slot => $sub_arr2){
					foreach($sub_arr2 as $lab => $sub_arr3){
						if(isset($_POST["{$_SESSION['select_name'][$iteration][$slot][$lab]}"])){
							
							$substitute_student_txt = $_POST["{$_SESSION['select_name'][$iteration][$slot][$lab]}"];
							$substitute_student = explode(" ", $substitute_student_txt); // separate student fields
							if($substitute_student[0] != "" && $substitute_student[0] != NULL){
								$_SESSION["substitute_students_IDs"][$iteration][$slot][$lab] = $substitute_student[0];
								//$current .= "\nSelected substitute student (iteration $iteration, [$slot - $lab]):\n";
								//$current .= $_SESSION["substitute_students_IDs"][$iteration][$slot][$lab]."\n";
							} else {
								$_SESSION["substitute_students_IDs"][$iteration][$slot][$lab] = NULL;
								//$current .= "\nLine1:Selected substitute student (iteration $iteration, [$slot - $lab]):\n";
								//$current .= $_SESSION["substitute_students_IDs"][$iteration][$slot][$lab]." NULL\n";
							}
							
						} else {
							$_SESSION["substitute_students_IDs"][$iteration][$slot][$lab] = NULL;
							//$current .= "\nLine2:Selected substitute student (iteration $iteration, [$slot - $lab]):\n";
							//$current .= $_SESSION["substitute_students_IDs"][$iteration][$slot][$lab]." NULL\n";
						}
					}
				}
			}
		}
	}

?>

<br>
<form name="allocations" method="post" onSubmit="" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">


<?php

	if(isset($_POST['choice'])) {
		$err = ""; // construct the input validation error message
		// unset ANY input errors from before
		if(isset($_SESSION["studentErr.1"])){ 
			unset($_SESSION["studentErr.1"]);
		}
		if(isset($_SESSION["studentErr.2"])){ 
			unset($_SESSION["studentErr.2"]);
		}

		switch ($_POST['choice']) {
			
			case "Commit1" :	
				$_SESSION["iter"] = 1;
				//$current .= "\nManually chosen students 1\n";

				foreach($_SESSION["no_substitution_alert.1"] as $unalloc_slot => $sub_arr_labs){
					foreach($sub_arr_labs as $unalloc_lab => $digit){
						foreach($_SESSION["substitute_students_IDs"][1][$unalloc_slot] as $lab => $unalloc_acc){
							
							// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
							if ($unalloc_acc == NULL || $unalloc_acc == ""){
								$err .= "\nPlease select a substituting student/support staff from the list for the slot-$unalloc_slot, lab-$lab\n"; //for logging purposes
							}
							
							//$current .= "Student allocated to slot-$unalloc_slot, lab-$lab: $unalloc_acc\n";
						}
					}
				}
				if ($err != ""){
					echo "<span class=\"error\"><br>Please select one student in each list!</span><br>";
					$_SESSION["studentErr.1"] = "Select!";
					unset($_POST['choice']); 
					// Write the contents to the log file
					$current .= $err;
					$current .= "\n*********************** SERVER VALIDATION ERRORS *********************\n";
					file_put_contents($file, $current);
					$conn = NULL;
					break;
				}
				$conn = NULL; //close DB connection
				file_put_contents($file, $current);
				
				//Now redirect to the confirmation page and exit further script
				header("Location: confirm_substitution.php");
				exit;
	
				break;
				
	
				
				case "Commit2" :
				//$current .= "\nManually chosen students 2\n";
				$_SESSION["iter"] = 2;
				
				foreach($_SESSION["no_substitution_alert.2"] as $unalloc_slot => $sub_arr_labs){
					foreach($sub_arr_labs as $unalloc_lab => $digit){
						foreach($_SESSION["substitute_students_IDs"][2][$unalloc_slot] as $lab => $unalloc_acc){
							
							// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
							if ($unalloc_acc == NULL || $unalloc_acc == ""){
								$err .= "\nPlease select a substituting student/support staff from the list for the slot-$unalloc_slot, lab-$lab\n"; //for logging purposes
							}
							
							//$current .= "Student allocated to slot-$unalloc_slot, lab-$lab: $unalloc_acc\n";
						}
					}
				}
				if ($err != ""){
					echo "<span class=\"error\"><br>Please select one student in each list!</span><br>";
					$_SESSION["studentErr.2"] = "Select!";
					unset($_POST['choice']); 
					// Write the contents to the log file
					$current .= $err;
					$current .= "\n*********************** SERVER VALIDATION ERRORS *********************\n";
					file_put_contents($file, $current);
					$conn = NULL;
					break;
				}
				$conn = NULL; //close DB connection
				file_put_contents($file, $current);
				
				//Now redirect to the confirmation page and exit further script
				header("Location: confirm_substitution.php");
				exit;
	
				break;

		}
	}

		
	$_SESSION["allocated_lab_details"] = array();
	$_SESSION["unallocated_lab_details"] = array();
	$_SESSION["all_available_students"] = array();
	$_SESSION["substitute_students_IDs"] = array();
	$_SESSION['select_name'] = array();


	// reusable function to map slot-lab to the real date/time and lab description
	function map_slot_lab($sub_arr, $file, $current, $conn, $suitability_is_printed, $iter, $n, $small_details){
	
		//convert slots to date-time
		$biggest_slot = max(array_keys($sub_arr));  // get the biggest slot ID
		$floor_biggest_slot = floor((int)$biggest_slot/45);
		$slot_count = 0;

		foreach($sub_arr as $slot => $sub_arr1){
			//find nearest commencing slot and week for the current slot
			//$current .= "Loop entered $slot_count\n";
			$slot_count++;
			$slot_timestamp = NULL;
			$slot_date_start = NULL;
			$slot_date_finish = NULL;
			$nearest_week_com_slot = NULL;
			$floor_slot = floor($slot/45); // whole part of the division
			//$current .= "Floor slot: $floor_slot\n";
			$mod_slot = $slot%45; // remainder (0 to 44)
			//$current .= "Mod slot: $mod_slot\n";
				
			for($i = 0; $i <= $floor_biggest_slot; $i++){
			
				if(($floor_slot == $i && $mod_slot > 0) || ($floor_slot == ($i+1) && $mod_slot == 0)){
					$nearest_week_com_slot = (int)($i * 45) + 1;
					//$current .= "Nearest slot for $acc : $nearest_week_com_slot\n";
					break;
				}
			}
			if($nearest_week_com_slot == NULL){
				//$current .= "Nearest week AGAIN: $nearest_week_com_slot\n";
				//$current .= "\n*** COULD NOT MAP LAB SLOTS TO DATE-TIMES (COMMENCING SLOT NOT FOUND) ***\n";
				file_put_contents($file, $current);
				echo "Could not map lab slots to date-times! (commencing slot not found)";
				$conn = NULL; //close DB connection
				header("Location: cancel_substitution.php");
			}
			
    	    // map current slot to the commencing timestamp of the week
			$query_week_com = $conn -> prepare("SELECT Week_Com 
												FROM Week_Date
												WHERE Slot_ID = $nearest_week_com_slot LIMIT 1;");
			$query_week_com->execute();
			
			$week_com = NULL;
			while ($found_week_com = $query_week_com -> fetch(PDO::FETCH_ASSOC)) {
				$week_com = $found_week_com['Week_Com'];
			}
			
			if($week_com == NULL){
				$current .= "\n*** COULD NOT MAP LAB SLOTS TO DATE-TIMES (COMMENCING WEEK NOT FOUND) ***\n";
				file_put_contents($file, $current);
				echo "Could not map lab slots to date-times (commencing week not found)!";
				$conn = NULL; //close DB connection
				header("Location: cancel_substitution.php");
			}
			// calculate time of the beginning of the lab/tutorial
			$week_com = (int)$week_com; // find number of sets of 9 consecutive slots
			$floor_9 = floor($mod_slot/9); //find the remaining number of slots (< 9)
			$mod_9 = $mod_slot%9;
			$const = 8; //hrs to add to arrive to the right slot
			
			if($mod_9 == 0){
				$const = -7; //hrs to add to arrive to the right slot
			}
			$slot_timestamp = $week_com + ($const + $floor_9 * 9 + $floor_9 * 15 + $mod_9) * 3600;
			$slot_date_start =  date("D, Y-m-d H:i", $slot_timestamp);
			$slot_date_finish =  date("H:i", $slot_timestamp + 3600);
  
			$suitability = 0; // student's suitability to the slot-lab in %
			$lab_details = array();

			foreach($sub_arr1 as $lab => $cos){
				$suitability_message = "";
				
				if ($suitability_is_printed){ // for the allocated labs
					$str_suitability = "";
					$suitability = $cos * 100;
					//$suitability = number_format($suitability, 0);

					include 'get_student_suitability.php';
					
					$suitability_message = "Student's suitability: $str_suitability.<br><br>";
					
				} else { // for the unallocated labs and manual allocation
				
					// insert dropdown list with potential choice of substituting students
					echo "<tr>";
					echo "<td>";
					
		
					$current_students_arr = array(); //temporary array to get students in a "slot-lab-students" format
					foreach($_SESSION["all_available_students"][$iter][$n] as $s_l){
						$current_students_arr[$slot][$lab] = $_SESSION["all_available_students"][$iter][$n][$slot][$lab];
					}
					
					$select_size = count($current_students_arr[$slot][$lab]) + 1; // count how many students on the list
					$_SESSION['select_name'][$iter][$slot][$lab] = "potential_students_".$iter."_".$slot."_".$lab; // different names for the select lists
					
					$studentErr = "";
					if(isset($_SESSION["studentErr.$iter"])){
						$studentErr = $_SESSION["studentErr.$iter"];
					}
					echo "<br><label> Choose one: <span class=\"error\">* $studentErr</span></label>";
					echo "<br><select name=\"{$_SESSION['select_name'][$iter][$slot][$lab]}\" title=\"Average worked hrs are given without taking on this lab\" size=\"$select_size\" multiple>";
					echo "<option selected disabled hidden value=\"\" selected></option>";
					
					foreach($current_students_arr[$slot][$lab] as $number => $avail_student){
						// find the potential students' data
						$sql_find_student = "SELECT Title, Forename, Initial, Surname
											 FROM PHD_Students
											 WHERE Account_ID = $avail_student;";
							
						$query_find_student = $conn->prepare($sql_find_student);
						$query_find_student->execute();
						
						$full_name = "";
						while ($found_student = $query_find_student -> fetch(PDO::FETCH_ASSOC)) {
							$title = $found_student['Title'];
							$name = $found_student['Forename'];
							$initial = $found_student['Initial'];
							$surname = $found_student['Surname'];
							$full_name = $surname." ".$name." ".$initial;
						}
						
						$suitability_message = "";
						$worked_hrs_message = "";
						$str_suitability = "";
						$arr_suitability = array();
						
						if($small_details != NULL){
							
							// find the level of student's suitability
							$str_suitability = $number;
							$arr_suitability = explode("_", $str_suitability);
							$suitability = $arr_suitability[1];
							$suitability = $suitability * 100;
							
							include 'get_student_suitability.php';
					
							$suitability_message = "$str_suitability ";
							
							$whole_hrs = floor($small_details[$avail_student]);
							$minutes = round(($small_details[$avail_student] - $whole_hrs) * 60);
							if ($small_details[$avail_student] > 6){
								$worked_hrs_message = "hrs>6 ($whole_hrs:$minutes)";
							} else {
								$worked_hrs_message = "$whole_hrs:$minutes hrs";;
							}
							
						}
						echo "<option value=\"$avail_student\">$full_name $suitability_message $worked_hrs_message</option>";
						$suitability_message = "";
					}
					
					echo "</select>";
					echo "<br><br>";
					echo "</td>";
					echo "<td>";
					
				}
				
				echo "$slot_count. Start: $slot_date_start Finish: $slot_date_finish<br>";

			
				// find lab/tutorial info
				$query_find_lab_description = $conn -> prepare("SELECT Account_ID, Description, Module  
																FROM Lab_Requirements
																WHERE Lab_ID = $lab LIMIT 1;");
				$query_find_lab_description->execute();
				
				$lab_description = "";
				$module = "";
				$module_leader = 0;	// module leader's account ID			
				while ($found_lab_description = $query_find_lab_description -> fetch(PDO::FETCH_ASSOC)) {
					$lab_description = $found_lab_description['Description'];
					$module = $found_lab_description['Module'];
					$module_leader = $found_lab_description['Account_ID'];
				}
				
				$lab_details[$lab]["description"] = $lab_description;
				$lab_details[$lab]["module"] = $module;
				
				// find module leader
				$query_find_module_leader = $conn -> prepare("SELECT Title, Forename, Initial, Surname  
															  FROM Module_Leaders
															  WHERE Account_ID = $module_leader LIMIT 1;");
				$query_find_module_leader->execute();
				
				$module_leader_details = "";
				while ($found_module_leader = $query_find_module_leader -> fetch(PDO::FETCH_ASSOC)) {
					$module_leader_details = $found_module_leader['Title'].". ".$found_module_leader['Surname']." ".$found_module_leader['Forename']." ".$found_module_leader['Initial'];
				}
				
				$lab_details[$lab]["module_leader"] = $module_leader_details;
	
				if ($suitability_is_printed){ // for the allocated labs
					$_SESSION["allocated_lab_details"][$iter][$slot] = $lab_details;
				} else { // for the unallocated labs
					$_SESSION["unallocated_lab_details"][$iter][$slot] = $lab_details;
				}
				
				echo "Class details: ";
				echo "$module. $lab_description<br>";
				echo "Module Leader: $module_leader_details<br><br>";
				echo "$suitability_message";
				
				if (!$suitability_is_printed){ // for the unallocated labs
					echo "</td>";
					echo "<td>";
					echo "</td>";
					echo "</tr>";
				}
			}
		}
	} // end function map_slot_lab






	try {
	
		//connect to DB
		require_once 'dbconfig.php';

		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		
		// 1. Find all other confirmed PHD Students and their basic availability
		
		$sql_find_all_other_students = "SELECT PS.Account_ID, PA.Slots_Availability
										FROM PHD_Students PS, PHD_Availability PA  
										WHERE PS.Account_ID <> $student_ID AND PS.Status = 'Confirmed' AND PA.Account_ID = PS.Account_ID;";
									   
		$query_find_all_other_students = $conn->prepare($sql_find_all_other_students);
		$query_find_all_other_students->execute();
		
		$other_students = array();
		$all_other_students = array();
		while ($found_all_other_students = $query_find_all_other_students -> fetch(PDO::FETCH_ASSOC)) {
			$other_students[$found_all_other_students['Account_ID']] = unserialize($found_all_other_students['Slots_Availability']);
			$all_other_students[] = $found_all_other_students['Account_ID']; // array of all other students FOR THE MANUAL ALLOCATION
		}
		
		//$current .= "\nAll other confirmed PhD Students and their availability:\n";
		//$current .= print_r($other_students, true);
		
		//$current .= "\nAll other confirmed PhD Students:\n"; // FOR THE MANUAL ALLOCATION
		//$current .= print_r($all_other_students, true);
		
/**************************************************************************************************************/


		// 2. Find if the students are available for the would-be unallocated slots
		
		$sql_check_student_availability = "SELECT Session_ID, Slot_ID, Lab_ID
										   FROM $new_table_name
										   WHERE Account_ID = $student_ID;";
									   
		$query_check_student_availability = $conn->prepare($sql_check_student_availability);
		$query_check_student_availability->execute();
		
		while ($found_student_availability = $query_check_student_availability -> fetch(PDO::FETCH_ASSOC)) {
			$slot_ID = $found_student_availability['Slot_ID'];
			$session_ID = $found_student_availability['Session_ID'];
			$all_slots_labs[$slot_ID] = $found_student_availability['Lab_ID'];
			
			foreach($other_students as $acc_id => $arr){
				$i = 0; // slots counter
				foreach($arr as $slot_no => $avail){
					$i++; // slots from 1 to 45 (or multiples of 45)
					if($i == 45){
						$i=0;
					}
					if(((int)$slot_ID % 45) == $i) {
						if($avail == 1){ // student marked as free for that slot in the PHD_Availability table
							$available_students[$acc_id][] = $slot_ID;
						}
					}
				}
			}
		}
		
		//$current .= "\nPossibly available PhD Students:\n";
		//$current .= print_r($available_students, true);
		
		//$current .= "\nAll slots-labs to substitute:\n";
		//$current .= print_r($all_slots_labs, true);



/**************************************** FOR THE MANUAL ALLOCATION *******************************/		
//
//
		// Make all other students available for all the slots being substituted
		foreach($all_other_students as $num => $acc_ID){
			foreach($all_slots_labs as $slot => $lab){
				$all_other_students_slot_labs[$acc_ID][$slot] = $lab;
			}
		}
		//$current .= "\nAll other confirmed PhD Students with slots-labs:\n";
		//$current .= print_r($all_other_students_slot_labs, true);

		foreach ($all_other_students_slot_labs as $acc_ID => $arr){
			foreach($arr as $slot_ID => $lab_ID){
				$sql_check_student_schedule = "SELECT LT.Slot_ID, A.Session_ID
				 							   FROM Lab_Timetable LT, Allocations A
											   WHERE LT.Slot_ID = $slot_ID
											   AND LT.Session_ID = A.Session_ID
											   AND A.Account_ID = $acc_ID;";
									   
				$query_check_student_schedule = $conn->prepare($sql_check_student_schedule);
				$query_check_student_schedule->execute();
		
				while ($found_student_schedule = $query_check_student_schedule -> fetch(PDO::FETCH_ASSOC)) {
					$unavail_slot = $found_student_schedule['Slot_ID'];
					//$current .= "$unavail_slot\n";
					unset($all_other_students_slot_labs[$acc_ID][$unavail_slot]); // remove the unavailable slot from the student's availability array
				}
			}
		} // end for each available students
		
		//$current .= "\nAll presumed available PhD Students (regardless the Availability table and skills), no slot clashes:\n";
		//$current .= print_r($all_other_students_slot_labs, true);
		

//
//
/**************************************************************************************************************/


		// 3. Check if the other available students' labs don't clash with the leaving student's slots
		
		foreach ($available_students as $acc_id => $arr){
			// $current .= "\nUnavailable slots for PhD Student $acc_id:\n";			
			foreach($arr as $item_no => $slot_ID){
				$sql_check_student_schedule = "SELECT LT.Slot_ID, A.Session_ID
				 							   FROM Lab_Timetable LT, Allocations A
											   WHERE LT.Slot_ID = $slot_ID
											   AND LT.Session_ID = A.Session_ID
											   AND A.Account_ID = $acc_id;";
									   
				$query_check_student_schedule = $conn->prepare($sql_check_student_schedule);
				$query_check_student_schedule->execute();
		
				while ($found_student_schedule = $query_check_student_schedule -> fetch(PDO::FETCH_ASSOC)) {
					//$unavail_slot = $found_student_schedule['Slot_ID'];
					//$current .= "$unavail_slot\n";
					unset($available_students[$acc_id][$item_no]); // remove the unavailable slot from the student's availability array
				}
			}
			$available_students[$acc_id] = array_values($available_students[$acc_id]); // available students with their ordered available slots
			
		} // end for each available students
		
		//$current .= "\nAvailable PhD Students:\n";
		//$current .= print_r($available_students, true);
		
/**************************************************************************************************************/


		// 4. Check availability based on students' skills
		
		// find the skill field names 
		$query_describe = $conn -> prepare("DESCRIBE Lab_Requirements;"); 
		$query_describe -> execute();

		while ($describe = $query_describe -> fetch(PDO::FETCH_ASSOC)) {
			$skill_name = $describe['Field'];
			if (substr($skill_name, 0, 5) == "Skill"){
				$skill_names[] = $skill_name; // add skill field name to the list
			}
		}

		//$current .= "\nList of required Skills:\n";
		//$current .= print_r($skill_names, true);
			

		foreach($available_students as $acc_ID => $slotArr){
			
			// find each student's skill vector
			foreach($skill_names as $num => $skill_name){
				if(!isset($student_vector[$acc_ID][$skill_name])) {
					$sql_find_student_skills = "SELECT $skill_name
												FROM PHD_Students
												WHERE Account_ID = $acc_ID LIMIT 1;";
									   
					$query_find_student_skills = $conn->prepare($sql_find_student_skills);
					$query_find_student_skills->execute();
			
					while ($found_student_skills = $query_find_student_skills -> fetch(PDO::FETCH_ASSOC)) {
						$student_vector[$acc_ID][$skill_name] = $found_student_skills[$skill_name];
					}
				}
			} // end for each skill names
			

			// find each lab's skill requirements vector
			foreach($slotArr as $number => $slot_ID){
				$lab_ID = 0;
				$sql_find_lab_ID = "SELECT Lab_ID
									FROM $new_table_name
									WHERE Slot_ID = $slot_ID LIMIT 1;"; // stop when lab_ID is found
									   
				$query_find_lab_ID = $conn->prepare($sql_find_lab_ID);
				$query_find_lab_ID->execute();
		
				while ($found_lab_ID = $query_find_lab_ID -> fetch(PDO::FETCH_ASSOC)) {
					$lab_ID = $found_lab_ID['Lab_ID'];
				}
				// add all lab IDs to the PhD Student who is available to take them on
				$student_vector[$acc_ID]["slot-lab"][$slot_ID] = $lab_ID;

				
				foreach($skill_names as $num => $skill_name){
					if(!isset($required_skills_vector[$lab_ID][$skill_name])) {
						$sql_find_required_skills = "SELECT $skill_name
													 FROM Lab_Requirements
													 WHERE Lab_ID = $lab_ID LIMIT 1;";
										   
						$query_find_required_skills = $conn->prepare($sql_find_required_skills);
						$query_find_required_skills->execute();
				
						while ($found_required_skills = $query_find_required_skills -> fetch(PDO::FETCH_ASSOC)) {
							if ($found_required_skills[$skill_name] == 1) {
								$found_required_skills[$skill_name] = 2; // changed to unify with students' skills (2 = definitely have the skill): useful for later comparisons of the 2 skill vectors
							}
							$required_skills_vector[$lab_ID][$skill_name] = $found_required_skills[$skill_name];
						}
					} // end if
					
				} // end for each skill names
				
			} // end for each slot array
			
		} // end for each available students
		

		foreach($required_skills_vector as $lab_ID => $skillsArr){
			foreach($skillsArr as $required_skill => $required_level){
					$required_vector[$lab_ID][] = $required_level;
			}
		}			
		$required_skills_vector = $required_vector;

		//$current .= "\nRequired skills Vector for each lab:\n";
		//$current .= print_r($required_skills_vector, true);
		
		//$current .= "\nSkills of each PhD Student:\n";
		//$current .= print_r($student_vector, true);
		


		// compare each student skillset to the skills required by each lab that they are available to support
		foreach($student_vector as $acc_ID => $arr){
			// find all unique lab IDs the student can take on
			$slot_lab = array();
			$slot_lab = $arr["slot-lab"];
			$slot_lab = array_unique($slot_lab);
			$slot_lab = array_values($slot_lab);
			//$current .= "\nStudent's $acc_ID unique labs they can take on:\n";
			//$current .= print_r($slot_lab, true);
			
			foreach($arr as $skill => $level){
				if (substr($skill, 0, 5) == "Skill"){
					$student_skills_vector[$acc_ID][] = $level;
				}
			}
			//$current .= "\nSkills Vector for PhD Student $acc_ID:\n";
			//$current .= print_r($student_skills_vector[$acc_ID], true);



			// calculate the norm of each possible lab (that the current student can take on) skills vector 
			foreach($slot_lab as $index => $lab_ID){
				// calculate the norm of the current lab skills vector
				if(!isset($lab_skills_vect_norm[$lab_ID])){
					$temp_norm = 0;
					foreach($required_skills_vector[$lab_ID] as $ind => $level){
						$temp_norm += $level * $level;
					}
					$lab_skills_vect_norm[$lab_ID] = sqrt(abs($temp_norm));
					
					//$current .=  "\nNorm of lab $lab_ID skills Vector:\n";
					//$current .=  "$lab_skills_vect_norm[$lab_ID]\n";
				}
				
			}
			
			
			// calculate the norm of the current student skills vector
			$temp_norm = 0;
			foreach($student_skills_vector[$acc_ID] as $index => $level){
				$temp_norm += $level * $level;
			}
			$student_skills_vect_norm[$acc_ID] = sqrt(abs($temp_norm));
			
			//$current .= "\nNorm of Student $acc_ID skills Vector:\n";
			//$current .= "$student_skills_vect_norm[$acc_ID]\n";


			// calculate the product of the student's skills vector and each corresponding lab skills vector
			$student_lab_skill_product = array();
			foreach($slot_lab as $index => $lab_ID){
				$temp_product = 0;
				foreach($student_skills_vector[$acc_ID] as $ind => $level){
					$temp_product += $required_skills_vector[$lab_ID][$ind]*$level;
				}
				$student_lab_skill_product[$acc_ID][$lab_ID] = $temp_product;
			}
			//$current .=  "\nProduct of Student $acc_ID Vector and labs Vectors:\n";
			//$current .= print_r($student_lab_skill_product, true);
			//$current .=  "\n";


			// calculate the cos(angle) between the Student skills vector and every corresponding required Lab skills vector
			foreach($student_lab_skill_product[$acc_ID] as $lab_ID => $product){
				if ($lab_skills_vect_norm[$lab_ID] != 0 && $student_skills_vect_norm[$acc_ID] !=0) { // either the lab did not have any requirements set or the Student did not have any skills set (need to prevent division by zero)
					$cos_arr[$acc_ID][$lab_ID] = $product/($student_skills_vect_norm[$acc_ID]*$lab_skills_vect_norm[$lab_ID]);
				}
			}
			//$current .= "\nCosines of Vectors for Student $acc_ID:\n";
			//$current .= print_r($cos_arr[$acc_ID], true);
			//$current .=  "\n";
			
		} // end of Student Vector (next Student's Vector)
		


	
		// order students by best fit skill-wise for taking on a lab
		foreach($student_vector as $acc_ID => $arr){
			foreach ($all_slots_labs as $slot_ID => $lab_ID){
				$student_lab_skill_rank[$acc_ID][$slot_ID][$lab_ID] = -1; // set the default rank to -1 = "Student cannot take the slot-lab on either because they do not have necessary skills, or the lab requirements/student skills are not set at all"
				foreach($cos_arr[$acc_ID] as $lab => $cos){
					if ($lab_ID == $lab){
						$student_lab_skill_rank[$acc_ID][$slot_ID][$lab_ID] = $cos; // create each student's rank for each slot-lab they could take on
					}
				}
			}
		}
		//$current .= "\nAll Student Ranks:\n";
		//$current .= print_r($student_lab_skill_rank, true);

				
		// sort students' availability and skills by slot-lab
		$student_lab_skill_rank_slots_order = array(); // stores sorted students' ranks
		foreach($student_lab_skill_rank as $acc_no => $sub_arr){
			foreach($sub_arr as $slot => $sub_arr1){
				foreach($sub_arr1 as $lab => $cos){
					$student_lab_skill_rank_slots_order[$slot][$lab][$acc_no] = $cos;
				}
			}
		}
		//$current .= "\nAll Student Ranks sorted by slots:\n";
		//$current .= print_r($student_lab_skill_rank_slots_order, true);
		
		// sort decsending students' suitability for each slot-lab based on cosine values
		foreach($student_lab_skill_rank_slots_order as $slot => $sub_arr){
			foreach($sub_arr as $lab => $sub_arr1){
				if (arsort($sub_arr1)){
					$student_lab_skill_rank_slots_order[$slot][$lab] = $sub_arr1;
				} else {
					$current .= "\n*** SORTING THE AVAILABLE STUDENTS BASED ON SKILLS FAILED ***\n";
					file_put_contents($file, $current);
					echo "Could not sort the student skills!";
					$conn = NULL; //close DB connection
					header("Location: cancel_substitution.php");
				}
			}
		}
		
		//$current .= "\nAll Student Ranks sorted by suitability (cosines):\n";
		//$current .= print_r($student_lab_skill_rank_slots_order, true);




/******************************** ITERATIONS TO SELECT SETS OF SUITABLE STUDENTS *******************************/

		$top_student_name = "top_student"; // list of allocatable students
		$no_substitution_name = "no_substitution_alert"; // list of unallocatable labs
		$condition = true; //(break from the do...while loop condition)
		$iter = 1; // count iterations
		
		do{
			${$top_student_name.$iter} = array(); // stores the most suitable student for each slot-lab in the current iteration
			${$no_substitution_name.$iter} = array(); // stores slot-labs which did not have any suitable substitutions

			$best_fit_name = "$top_student_name.$iter";
			//$current .= "\nBEST FIT STUDENTS NAME: $best_fit_name\n";
			$_SESSION["$best_fit_name"] = array();
			
			$unalloc_arr_name = "$no_substitution_name.$iter";
			//$current .= "\nUNALLOC NAME: $unalloc_arr_name\n";
			if($iter == 1){
				$_SESSION["$unalloc_arr_name"] = array();
			}
			
			if($iter == 2){
				$_SESSION["$unalloc_arr_name"] = array();
				${$no_substitution_name.$iter} = $_SESSION["no_substitution_alert.1"]; // assign same array as in iteration 1
				$_SESSION["$unalloc_arr_name"] = $_SESSION["no_substitution_alert.1"];
				//$current .= "\nLAST SESSION VAR 813 [$iter]\":\n";
				//$current .= print_r($_SESSION["no_substitution_alert.1"], true);
			}

			
			$all_other_students_slot_labs_current_iter[$iter] = $all_other_students_slot_labs;
			
			if ($iter == 1){
				foreach($student_lab_skill_rank_slots_order as $slot => $sub_arr){
					foreach($sub_arr as $lab => $sub_arr1){
						foreach($sub_arr1 as $acc => $rank){
							// choose only the top rank student for each slot-lab
							if ($rank > 0){
								${$top_student_name.$iter}[$acc][$slot][$lab] = $rank;
								break;
							} else { // rank <= 0
								// add lab to the list of unallocated labs if there is no one able to substitute
								${$no_substitution_name.$iter}[$slot][$lab] = true;
								break;
							}
						}
						break;
					}
				}
				$_SESSION["$no_substitution_name.$iter"] = ${$no_substitution_name.$iter};
				//$current .= "\nNo substitution alert array for each slot-lab at iteration $iter:\n";
				//$current .= print_r(${$no_substitution_name.$iter}, true);
			}
			
			
			
			//$current .= "\nTop suitable Students for each slot-lab at iteration $iter:\n";
			if ($iter > 1){
				foreach($student_lab_skill_rank_slots_order as $slot => $sub_arr){
					foreach($sub_arr as $lab => $sub_arr1){
						foreach($sub_arr1 as $acc => $rank){
							// choose all students with rank > 0
							if ($rank > 0){
								${$top_student_name.$iter}[$acc][$slot][$lab] = $rank;
								$_SESSION["$unalloc_arr_name"][$slot][$lab] = true;
							}
						}
					}
				}
				//$current .= print_r(${$top_student_name.$iter}, true);
				//$current .= "\nSESSION VAR 870 No substitution alert array for each slot-lab at iteration $iter:\n";
				//$current .= print_r($_SESSION["$unalloc_arr_name"], true);
			}




			// for all potential substituting students, find if they will be working over 6 hrs/week

			// count average hours that each student will work at the current iteration
			$worked_hrs = array(); // worked hours counter for each student
			$avg_hrs_week = array(); //average number of hours worked by each student
			
			 // weeks in 6 months (26)
			$weeks = 26; 
			
			$title = "";
			if($iter == 1){
				$title = "Best choice skill-wise, average worked hours take into account the allocated slot";
			} else {
				$title = "Manual allocation with suggestions of skill level and worked hours where possible.<br>Skill rates are Very High, High, Medium, Low and Very Low";
			}
			echo "<br><span class=\"info\"><strong> Choice $iter: $title</strong></span><br><br>";
			echo "<table>";
			echo "<tr>";
			echo "<th>";
			echo "Substituting PhD Students";
			echo "</th>";
			echo "<th>";
			echo "Allocated classes";
			echo "</th>";
			echo "<th>";
			echo "Notes and Constraints";
			echo "</th>";
			echo "</tr>";
			
			// iteration 1
			if ($iter == 1){
				
				if (count(${$top_student_name.$iter}) > 0){
					
					foreach(${$top_student_name.$iter} as $acc => $sub_arr){
						
						$xtra_hrs = count($sub_arr);
						
						$sql_find_student = "SELECT Forename, Initial, Surname
											 FROM PHD_Students
											 WHERE Account_ID = $acc;";
												
						$query_find_student = $conn->prepare($sql_find_student);
						$query_find_student->execute();
						
						while ($found_student = $query_find_student -> fetch(PDO::FETCH_ASSOC)) {
							$name = $found_student['Forename'];
							$initial = $found_student['Initial'];
							$surname = $found_student['Surname'];
						}
		
						echo "<tr>";
						echo "<td>";
						echo "$surname $name $initial";
						echo "</td>";
						
						
						$sql_find_worked_hrs = "SELECT COUNT(DISTINCT Allocation_ID) AS WorkedHrs
												FROM Allocations
												WHERE Account_ID = $acc;";
												
						$query_find_worked_hrs = $conn->prepare($sql_find_worked_hrs);
						$query_find_worked_hrs->execute();
						
						while ($found_worked_hrs = $query_find_worked_hrs -> fetch(PDO::FETCH_ASSOC)) {
							$worked_hrs[$acc] = $found_worked_hrs['WorkedHrs'];
						}
						$worked_hrs[$acc] = (int)$worked_hrs[$acc];
						$worked_hrs[$acc] += $xtra_hrs;
						
						//$current .= "Student $acc total worked hours = $worked_hrs[$acc]\n";
						
						$avg_hrs_week[$acc] = $worked_hrs[$acc]/$weeks;
						$whole_hrs = floor($avg_hrs_week[$acc]);
						$minutes = round(($avg_hrs_week[$acc] - $whole_hrs) * 60);
						//$current .= "Average worked hours/week in the current semester = $whole_hrs hrs. $minutes min.\n";
						
						echo "<td>";
						
						// map slots to labs and output all allocated sessions
						map_slot_lab($sub_arr, $file, $current, $conn, true, $iter, 0, NULL);
		
						echo "</td>";
						echo "<td>";
						if ($avg_hrs_week[$acc] <= 6){
							echo "Avg. hrs/week = $whole_hrs hrs. $minutes mins.";
						} else{
							echo "Avg. hrs/week = $whole_hrs hrs. $minutes mins. > 6";
						}
						echo "</td>";
						echo "</tr>";
					}
				}
			
			
			
				// output still unallocated sessions for iteration 1 only (if there are any unallocated sessions and if there are any available students)
				if(count(${$no_substitution_name.$iter}) > 0 && count($all_other_students_slot_labs_current_iter) > 0){
					
					// allow choice from all students (regardless basic availability or skills) whose labs do not clash with the unallocated slots
					foreach(${$top_student_name.$iter} as $acc => $sub_arr){ // this step will be skipped if the top student array is empty
						foreach($sub_arr as $slot => $sub_arr1){
							
							foreach($all_other_students_slot_labs_current_iter[$iter] as $a => $s){
								// remove the slot from every available student's slot-lab list, as it's being allocated to the top-rated students
								if(isset($all_other_students_slot_labs_current_iter[$iter][$a][$slot])){
									unset($all_other_students_slot_labs_current_iter[$iter][$a][$slot]); 
								}
							}
						}
					}
					
					// sort all available students by slot-lab
					foreach($all_other_students_slot_labs_current_iter[$iter] as $a => $s_arr){
						foreach($s_arr as $s => $l){
							$slot_lab_all_other_students_order[$iter][$s][$l][]= $a; // stores all available students sorted by slots
						}
					}
					
					//$current .= "\nAll available students UNSORTED [$iter]\":\n";
					//$current .= print_r($all_other_students_slot_labs_current_iter[$iter], true);
										
					$_SESSION["all_available_students"][$iter][] = $slot_lab_all_other_students_order[$iter]; // added only once each iteration
					
					//$current .= "\nSESSION VAR 976 \"all_available_students [$iter]\":\n";
					//$current .= print_r($_SESSION["all_available_students"][$iter], true);
					
					echo "<tr>";
					echo "<th>";
					echo "</th>";
					echo "<th>";
					echo "Unallocated sessions:";
					echo "</th>";
					echo "<th>";
					echo "</th>";
					echo "</tr>";

					//$current .= "\n989 \"no_substitution_name.iter: [$iter]\":\n";
					//$current .= print_r(${$no_substitution_name.$iter}, true);

					// map slots to labs and output all unallocated sessions (if any)
					map_slot_lab(${$no_substitution_name.$iter}, $file, $current, $conn, false, $iter, 0, NULL);
	
				}
			} else { // iter = 2
				
				if (count(${$top_student_name.$iter}) > 0){
					// sort all suitable students by slot-lab
					foreach(${$top_student_name.$iter} as $a => $s_arr){
						foreach($s_arr as $s => $s_arr1){
							foreach($s_arr1 as $l => $r){
								$slot_lab_all_suitable_students_order[$iter][$s][$l][$a] = $r ; // stores all suitable students sorted by slots
							}
						}
					}
					
					// sort suitable students in descending order skill-wise
					foreach($slot_lab_all_suitable_students_order[$iter] as $s => $l_arr){
						foreach($l_arr as $l => $a_arr){
							if (arsort($a_arr)){
								$slot_lab_all_suitable_students_order[$iter][$s][$l] = $a_arr;
							} else {
								$current .= "\n*** SORTING ALL THE SUITABLE STUDENTS BASED ON SKILLS FAILED ***\n";
								file_put_contents($file, $current);
								echo "Could not sort the student skills!";
								$conn = NULL; //close DB connection
								header("Location: cancel_substitution.php");
							}
							
						}
					}
					
					// convert suitable students array into the uniform format (sorted by slot-lab) to send to the map-slot-lab function
					foreach($slot_lab_all_suitable_students_order[$iter] as $s => $l_arr){
						foreach($l_arr as $l => $a_arr){
							$temp_arr = array(); // to store the intermediate subarray while swapping keys-values
							$i = 0;
							foreach($a_arr as $a => $r){
								$ind = $i."_". $r;
								$temp_arr[$ind] = $a;
								$i++;
							}
							$slot_lab_all_suitable_students_order[$iter][$s][$l] = $temp_arr;
						}
					}
					//$current .= "\nAll students SORTED by slot-lab [$iter]\":\n";
					//$current .= print_r($slot_lab_all_suitable_students_order[$iter], true);
				}
				
				// store the potential suitable students and all available students into a single array
				$temp_arr = array(); // to store 2 arrays together
				$temp_arr[] = $slot_lab_all_suitable_students_order[$iter]; // suitable students first (could be empty)
				$temp_arr[] = $_SESSION["all_available_students"][1][0]; // all available students last (also could be empty, but less likely)
				$_SESSION["all_available_students"][$iter] = $temp_arr; 
				
				//$current .= "\nSESSION VAR 1045 \"all_available_students [$iter]\":\n";
				//$current .= print_r($_SESSION["all_available_students"][$iter], true);


				if (count(${$top_student_name.$iter}) > 0){
					// for all potential substituting students, find if they will be working over 6 hrs/week
					// count average hours that each student will work at the current iteration
					$worked_hrs = array(); // worked hours counter for each student
					$avg_hrs_week = array(); //average number of hours worked by each student
					
					// calculate their worked hours so far
					foreach(${$top_student_name.$iter} as $a => $s_arr){
						if(!isset($worked_hrs[$a])){
							
							$sql_find_worked_hrs = "SELECT COUNT(DISTINCT Allocation_ID) AS WorkedHrs
													FROM Allocations
													WHERE Account_ID = $a;";
													
							$query_find_worked_hrs = $conn->prepare($sql_find_worked_hrs);
							$query_find_worked_hrs->execute();
							
							while ($found_worked_hrs = $query_find_worked_hrs -> fetch(PDO::FETCH_ASSOC)) {
								$worked_hrs[$a] = $found_worked_hrs['WorkedHrs'];
							}
							$worked_hrs[$a] = (int)$worked_hrs[$a];
							
							//$current .= "Student $a total worked hours = $worked_hrs[$a]\n";
							
							$avg_hrs_week[$a] = $worked_hrs[$a]/$weeks;
							$whole_hrs = floor($avg_hrs_week[$a]);
							$minutes = round(($avg_hrs_week[$a] - $whole_hrs) * 60);
							//$current .= "Average worked hours/week in the current semester = $whole_hrs hrs. $minutes min.\n";
						}
						
					}
					
					echo "<tr>";
					echo "<th>";
					echo "</th>";
					echo "<th>";
					echo "Allocatable sessions:";
					echo "</th>";
					echo "<th>";
					echo "</th>";
					echo "</tr>";
					
					//$current .= "\n1110 \"slot_lab_all_suitable_students_order: [$iter]\":\n";
					//$current .= print_r($slot_lab_all_suitable_students_order[$iter], true);
					
					// map slots to labs and output all allocated sessions
					map_slot_lab($slot_lab_all_suitable_students_order[$iter], $file, $current, $conn, false, $iter, 0, $avg_hrs_week);
				}
				
				// if there are any unallocatable labs
				if(count(${$no_substitution_name.$iter}) > 0){
					echo "<tr>";
					echo "<th>";
					echo "</th>";
					echo "<th>";
					echo "Unallocated sessions:";
					echo "</th>";
					echo "<th>";
					echo "</th>";
					echo "</tr>";
					
					//$current .= "\n1106 \"no_substitution_name.iter: [$iter]\":\n";
					//$current .= print_r(${$no_substitution_name.$iter}, true);
					
					// map slots to labs and output all unallocated sessions
					map_slot_lab(${$no_substitution_name.$iter}, $file, $current, $conn, false, $iter, 1, NULL);
				}
			
			}
			
			echo "</table><br>";
			

			$_SESSION["$best_fit_name"] = ${$top_student_name.$iter}; // stores user choice of lab allocations
			
			//$current .= "\nSESSION VAR 1123 \"$best_fit_name\":\n";
			//$current .= print_r($_SESSION["$best_fit_name"], true);
			
			//$current .= "\nSESSION VAR 1126 \"$unalloc_arr_name\":\n";
			//$current .= print_r($_SESSION["$unalloc_arr_name"], true);
			
			echo "<br><input type=\"submit\" name=\"choice\" value=\"Commit$iter\" title=\"Confirm Choice $iter\"><br><br><br><br><br>";
			
			$iter++;
			if ($iter > 2){ // 2 iterations
				$condition = false; // stop after this iteration
			}
			
		} while ($condition);
?>
		
</form>

<?php		

		file_put_contents($file, $current);
		$conn = NULL; //close DB connection

	} catch(PDOException $e){
		$err_output = $e->getMessage();				
		echo "Error: " . $err_output;
		$current .= $err_output;
		$current .= "\n********************* PDO ERROR4 ************************\n";		
		file_put_contents($file, $current);
		$conn = NULL; //close DB connection
	}
	
	include 'template-bottom.php';
	
?>
	
