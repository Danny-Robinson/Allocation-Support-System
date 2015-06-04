<?php

	


	/* CREATE LAB/TUTORIAL REQUIREMENTS */
	/* PRESUMING ALL THE MODULES ARE ALREADY REGISTERED AND HAVE REGISTERED MODULE LEADERS FOR EACH MODULE! */
	
	
	
	$header = 'Add a Timetable file and create a New Semester\'s Timetable'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 

	

	include 'php_functions.php';

	$file_name = $fileErr = "";
	
	$conn = NULL; // connection to the DB is not open
	$db_table = "Lab_Requirements";
	$location = "populate_lab_requirements.php";
	
	//Define the output log file for debugging purposes
	$file = 'log_populate_lab_requirements.txt';
	// Open the file to get existing content
	$current = file_get_contents($file);
	$sessions = array(); // 2D array to parse the University timetable from a csv file
	$labs = array(); // 2D array to extract only lab, tutorial, or project supervision sessions from the University timetable
	
	
	
	$labs_module = array(); // 2D array to sort the lab, tutorial, or project supervision sessions by module
	
	$map_session = array("laboratory" => "Demo",
						 "tutorial" => "Tutor",
						 "project supervision" => "Tutor");
	
	
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
  		$file_name = test_input($_POST["file_name"]);
	}


	$message=$_GET["message"]; //the message is either "" (if this page is hit for the first time) or "File is succefully uploaded"
	$message = test_input($message);



	if (isset($_POST['btn_upload'])) {
		// perform server-side validation of the form as well (in case JavaScript is disabled in the browser)
		$storagename = ""; // filename as stored on server
  		$while_loop_counter = 1; // to make sure the do...while loop is executed (when =1, and finished when =0)
  		$err = ""; // construct the input validation error message

		do {
			
			// http://stackoverflow.com/questions/5593473/how-to-upload-and-parse-a-csv-file-in-php
			if (isset($_FILES["file_name"])) {
	
				//if there was an error uploading the file
				if ($file_name == ""){
					$err .= "Please choose file to upload. "; //for logging purposes
					$fileErr = "Please choose file to upload";
				}
				
				if ($_FILES["file_name"]["error"] > 0) {
					echo "ERROR Code: " . $_FILES["file_name"]["error"] . ". Please see 'http://php.net/manual/en/features.file-upload.errors.php' for error codes<br>";
					$err .= "ERROR Code: " . $_FILES["file_name"]["error"] . ". Please see 'http://php.net/manual/en/features.file-upload.errors.php' for error codes\n"; //for logging purposes
		
				} else {
					$err = "";
					$fileErr = "";
		
					//if file already exists
					if (file_exists("upload/" . $_FILES["file_name"]["name"])) {
						echo $_FILES["file_name"]["name"] . " already exists. ";
						$err .= "File already exists. Please try again. "; //for logging purposes
						$fileErr = "File already exists. Please try again";
					} else {
						//Store file in directory "upload" with a random name
						$storagename = "uplofile_" . rand(1345, 9037) . ".txt";
						move_uploaded_file($_FILES["file_name"]["tmp_name"],  $storagename);
						//echo "Stored in: " . "upload/" . $_FILES["file_name"]["name"] . "<br>";
					}
				}
			} 

		
			if ($err != "") {
				$message = "";
				//$current .= "Input errors: $err\n";
				$err = "";				
				break; //exit the do..while loop
			}
		


			$csv_file = fopen($storagename , r);
			while(! feof($csv_file)){
				$sessions[] = fgetcsv($csv_file, 0, ";"); // read the timetable from the csv file into an array
			}
			//$current .= print_r($sessions, true); // TRUE returns a string		
			fclose($csv_file);
			
			
			$regex = "/^week.*$/i";
			//$date_regex= "/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/";
			$length = count($sessions);
			//$demo_count = 0; // lab session counter
			//$tutor_count = 0; // tutorial and project supervision session counter
			//$current .= "\nLength of sessions array = $length\n";



/************************************************************************************************************************************/
			try {
				//connect to DB
				require_once 'dbconfig.php';
			
				$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					
				$week_No = NULL; // define first week number in the semester (used in Time_Slots table)
				$first_week_com = NULL; // the first week of the semester commences
				$starting_slot_ID = NULL; // the first time slot number
				
				for ($i = 0; $i < $length; $i++){
					
					$sublength = count($sessions[$i]);
					if ($sublength == 0){
						continue; // skip empty session to the next session i
					}
					if ($sublength == 1){
						if ($sessions[$i][0] == ""){
							continue; // skip an empty line to the next session i
						} else if (preg_match($regex, $sessions[$i][0]) && $first_week_com == NULL) { // if element is a week date then store the timestamp of when the first week of the semester commences
							//http://stackoverflow.com/questions/19564063/regex-to-get-date-yyyy-mm-dd-from-any-string
							$dateArray = preg_match("/(\d{4}-\d{2}-\d{2})/", $sessions[$i][0], $match);
							$date = date('Y-m-d',strtotime($match[0])); // current session's date in UTC format (GMT + 5 hrs)
							$first_week_com = (int)strtotime($date);
							
							// find the same week timestamp in the Week_Date db table 
							$query_first_week_com = $conn -> prepare("SELECT Week_Com, Slot_ID 
															   		  FROM Week_Date
															   		  WHERE Week_Com = $first_week_com;"); 
							$query_first_week_com -> execute();
							
							
							while ($found_first_week_com = $query_first_week_com -> fetch(PDO::FETCH_ASSOC)) {
								$starting_slot_ID = $found_first_week_com['Slot_ID'];
								break; // do not check records after 1 is found 
							}
							
							if($starting_slot_ID != NULL){
								
								$week_No = ($starting_slot_ID - 1)/45 + 1; // find a corresponding week number
								//$current .= print_r("The first week_No in this semester = ".$week_No." - ".$date." - ".$first_week_com."\n", true);
								
							} else {
								echo "ERROR: Unable to find starting Slot_ID for the first week starting $date!";
								exit; // exit further script
							}
							continue; // skip to the next session i
						}
					} else if (strtolower($sessions[$i][0]) == "activity"){ // and if sublength > 1
						continue; // skip the whole array of headers to the next session i
					} else { // session fields are available 
						// check the type of session field [i][9]
						if (strtolower($sessions[$i][9]) == "laboratory" || strtolower($sessions[$i][9]) == "tutorial" || strtolower($sessions[$i][9]) == "project supervision"){ // record only current lab or tutorial (project supervision) session info
							foreach ($map_session as $key => $value) {
								if (strtolower($sessions[$i][9]) == $key){
									$labs[$i]["Type"] = $value;
								} 
							}
							
							if ($labs[$i]["Type"] == ""){
								echo "ERROR: Cannot map current session $i ($sessions[$i][9]) to the known session type!";
								exit; // exit further script
							}
							$labs[$i]["Module"] = $sessions[$i][1];
							$labs[$i]["Description"] = $sessions[$i][9].". Day: ".$sessions[$i][2].". Time: ".$sessions[$i][4].". Duration: ".$sessions[$i][8].". Staff: ".$sessions[$i][10].". ".$sessions[$i][14];
							$labs[$i]["Start_day"] = $sessions[$i][2];
							$labs[$i]["Start_date"] = $sessions[$i][3];
							$labs[$i]["Start_time"] = $sessions[$i][4];
							$labs[$i]["Duration"] = $sessions[$i][8];
							$labs[$i]["Room"] = $sessions[$i][11];
							$labs[$i]["Size"] = $sessions[$i][13];
							
							
							
							
							// Calculate time-slot number(s) for the current lab/tutorial session (next 4 steps):
							
							// 1. Find corresponding week number
							
							$lab_start_date = (string)$labs[$i]["Start_date"]; // string date
							//$current .= print_r($lab_start_date." - ", true); // string date 
							$lab_start_date = strtotime($lab_start_date); // timestamp date
							
							$labs[$i]["Start_week_timestamp"] = $lab_start_date;
							//$current .= print_r($labs[$i]["Start_week_timestamp"]."\n", true);
							$labs[$i]["Week_No"] = ($labs[$i]["Start_week_timestamp"] - $first_week_com)/3600/24/7 + 1;
							$temp_no = ceil($labs[$i]["Week_No"]) - $labs[$i]["Week_No"];
							
							if (abs($temp_no) < 0.01) {
								$labs[$i]["Week_No"] = ceil($labs[$i]["Week_No"]);
							} else {
								$labs[$i]["Week_No"] = floor($labs[$i]["Week_No"]);
							}
							
							//$current .= print_r("Week No. ".$labs[$i]["Week_No"], true);
							
							
							
							// 2. Map days of the week
							
							switch ($labs[$i]["Start_day"]) {
								
    							case "Mon":
        							$labs[$i]["Day"] = 1;
        							break;
									
    							case "Tue":
        							$labs[$i]["Day"] = 2;
        							break;

    							case "Wed":
        							$labs[$i]["Day"] = 3;
        							break;

    							case "Thu":
        							$labs[$i]["Day"] = 4;
        							break;

    							case "Fri":
        							$labs[$i]["Day"] = 5;
        							break;
							}
							
							//$current .= print_r("   Day No ".$labs[$i]["Day"].";   ", true);




							// 3. Map time-slots
							
							
							$duration_arr = explode (":", $labs[$i]["Duration"]); // check how long is the duration of the lab session
							
							$time_arr = explode (":", $labs[$i]["Start_time"]);
							switch ((int)$time_arr[0]) {
								
    							case 9:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 1) < 10) {
											$labs[$i]["Time"][$s] = $s + 1;
										}
									}
        							break;
									
    							case 10:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 2) < 10) {
											$labs[$i]["Time"][$s] = $s + 2;
										}
									}
        							break;
									
    							case 11:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 3) < 10) {
											$labs[$i]["Time"][$s] = $s + 3;
										}
									}
        							break;
									
    							case 12:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 4) < 10) {
											$labs[$i]["Time"][$s] = $s + 4;
										}
									}
        							break;
									
    							case 13:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 5) < 10) {
											$labs[$i]["Time"][$s] = $s + 5;
										}
									}
        							break;
									
    							case 14:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 6) < 10) {
											$labs[$i]["Time"][$s] = $s + 6;
										}
									}
        							break;
									
    							case 15:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 7) < 10) {
											$labs[$i]["Time"][$s] = $s + 7;
										}
									}
        							break;
									
    							case 16:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 8) < 10) {
											$labs[$i]["Time"][$s] = $s + 8;
										}
									}
        							break;
									
    							case 17:
									for ($s = 0; $s < (int)$duration_arr[0]; $s++){
										if (($s + 9) < 10) {
											$labs[$i]["Time"][$s] = $s + 9;
										}
									}
        							break;
									
							}
							


							// 4. Find Slot ID from Time_Slots table
							
							for ($s = 0; $s < (int)$duration_arr[0]; $s++){
								
								$week_slot = (int)$labs[$i]["Week_No"];
								$day_slot = (int)$labs[$i]["Day"];
								$time_slot = (int)$labs[$i]["Time"][$s];
								
								
								$query_find_slot_ID = $conn->prepare("SELECT Slot_ID 
															  	  	  FROM Time_Slots 
															  	  	  WHERE Week = $week_slot 
																	  AND Day = $day_slot
																	  AND Time = $time_slot;");
								$query_find_slot_ID->execute();
					
								while ($found_slot_ID = $query_find_slot_ID -> fetch(PDO::FETCH_ASSOC)) {
									$labs[$i]["Slot_ID"][$s] = $found_slot_ID['Slot_ID'];
								}
								//$current .= print_r("Time slot ".$labs[$i]["Time"][$s]."   "."Slot ID = ".$labs[$i]["Slot_ID"][$s]."\n", true);
							}
							
							//$current .= "\n";
							
							continue; // skip to the next session i
						}
					}
			
				} //end of for loop
				
				$conn = NULL; // close connection to the DB
				
			} catch(PDOException $e){
				$err_output = $e->getMessage();				
				echo "Error: " . $err_output;
				$current .= $err_output;
				$current .= "\n********************* PDO ERROR 1 ************************\n";		
				file_put_contents($file, $current);
				$conn = NULL; //close DB connection
			}

/************************************************************************************************************************************/

			
			$lab_count = count($labs); // the number of all calendar lab/tutorial (project supervision) sessions in a semester
			
			// sort labs/tutorials by Module
			$module = "";			
			$labs_module_count = 0; // count sessions again after sorting by module to see everything went OK
		
			foreach($labs as $key => $value){
			
				foreach($value as $k => $v){ // find Module code
					if (strtolower($k) == "module") {
						$module = strtoupper($v);
						break; // module has been found
					} 
				}
				
				if ($module != ""){
					
					foreach($value as $k1 => $v1){
						$labs_module[$module][] = $value;
						$labs_module_count ++;
						break; // only record each lab once
					}
				}
			}
			
		
		
		
		
			// Populate the Lab_Requirements table in the DB with distinct lab/tutorial sessions
			try {
				//connect to DB
				require_once 'dbconfig.php';
			
				$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				//$field_value = array(); // an array to store the content of the table's field
				$fields = array(); // an array to store the names of the table's field
				
				$query_describe = $conn -> prepare("DESCRIBE $db_table;"); // find the field names and field types for Lab_Requirements table
				$query_describe -> execute();
				
				while ($describe = $query_describe -> fetch(PDO::FETCH_ASSOC)) {
					$fields[] = $describe['Field'];
				}
				
				$f_length = count($fields); // total number of fields in the record
		
		
				// for each module, only choose distinct sessions 
				$total_distinct_sessions = 0;
				$final_distinct_labs = array(); // array for the final distinct labs
				$final_distinct_labs_with_timeslots = array(); // array for the final distinct labs with all the timeslots for all labs
				$total_records_no = 0; // the total number of records inserted into Lab_Requirements table
				$total_slots_no = 0; // all the lab slots as counted after they are assigned to [Slot_ID] fields of every distinct lab
									 //(at the end, compare total_records_no and total_slots_no: should be the same)
				

				/************************************* UNCOMMENT BELOW to REALLY create a new timetable!!!!!!***********************************/


				$query_clear_table1 = $conn->prepare ("DELETE FROM $db_table;");
				$query_clear_table1->execute();
				
				$query_clear_table2 = $conn->prepare ("DELETE FROM Lab_Timetable;");
				$query_clear_table2->execute();
				
				 
				// find all Cancelled_Allocations_... tables and delete them
				// http://stackoverflow.com/questions/1589278/sql-deleting-tables-with-prefix
				$query_clear_tables = $conn->prepare ("SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) 
													   AS statement FROM information_schema.tables 
													   WHERE table_schema = 'planetme_lab-support' AND table_name LIKE 'Cancelled_Allocations_%';");
				$query_clear_tables->execute();
				$new_statement = "";
				while ($statement = $query_clear_tables -> fetch(PDO::FETCH_ASSOC)) {
					$new_statement = $statement['statement'];
					$current = print_r("Drop Statement:\n".$new_statement, true); 
				}
				if($new_statement != ""){
					$new_drop_query = $conn->prepare($new_statement);
					$new_drop_query->execute();
				}
				
				/******************************************************************************************************************************/

				// create an SQL statement for inserting a record into the Lab_Requirements table
				$sql = "INSERT INTO $db_table (";
				$j = 1; // current field counter
				foreach ($fields as $f){
					if ($f != "Lab_ID" && $j < $f_length) {
						$sql .= "$f, ";
					} else if ($f != "Lab_ID" && $j = $f_length) {
						$sql .= "$f) ";
					}
					$j++;
				}
				$sql .= "VALUES (";
								 
				$j = 1; // current field counter
				foreach ($fields as $f){
					if ($f != "Lab_ID" && $j < $f_length) {
						$sql .= ":$f, ";
					} else if ($f != "Lab_ID" && $j = $f_length) {
						$sql .= ":$f);";
					}
					$j++;
				}
				
				//$current .= $sql."\n";
					
					
				// create an SQL statement to insert records into Lab_Timetable
				$sql_populate_lab_timetable = "INSERT INTO Lab_Timetable (Lab_ID, Slot_ID) 
											   VALUES (:Lab_ID, :Slot_ID);";
				
				

				$slot_count = array(); // DEBUG INFO
				
				foreach($labs_module as $mod => $lab_list){
					
					$slot_count[$mod] = count($lab_list);  // DEBUG INFO
					
					$iter = -1; //iterations over the distinct labs array for the current module
					$next = 0; // index of the selected for comparison lab in the current lab list
					$refine_condition = true; // continue refining distinct lab_list until cannot find more distinct labs  (false = empty new distinct lab list)
					$records_no = array(); // array for the total number of records inserte for current module
					$records_no[$mod] = 0; // number of distinct labs in the current  Module
					$current_lab_slots_no = array();
					$current_lab_slots_no[$mod] = 0;
			
					do { // iterate through the lab list within current Module until the labs are all distinct
						$first_time = true; // just started to iterate through the lab_list in the current Module		
						$distinct_labs = array(); // array to store all the distincts labs at current iteration
						$selected_lab = array(); // lab selected for comparison	
						$iter++;  // 0 - start iteration
						//$current .= "\nIteration = $iter:\n";
			
						foreach ($lab_list as $counter => $lab) { 
							if ($first_time == true){  // true
								if ((count($lab_list)-1) > ($counter + $iter)){ 
									$next = $counter + $iter; // index of the selected for comparison lab in the current lab list
								} else { // end of lab list
									$refine_condition = false;
									break; // break from foreach lab_list to the next iteration
								}
								// Transfer all distinct (so far) sessions to a new array
								for ($i = 0; $i < $next; $i++){ 
									$distinct_labs[] = $lab_list[$i];
								}
							}
							
							if (count($selected_lab) == 0){ // selected_lab does not exist yet
								$selected_lab = $lab_list[$next]; // select only the first lab (an array) to compare to
								$distinct_labs[] = $selected_lab; // add selected lab to the distinct ones
								$first_time = false; // false
							}
							
							foreach ($lab as $key => $value) {
								
								if ($counter <= $next){
									break; // do not compare all previous distinct labs
								}
								
								if($key != "Start_date" && $key != "Start_week_timestamp" && $key != "Week_No" && $key != "Day" 
								   && $key != "Time" && $key != "Slot_ID" && $value != $selected_lab[$key]) {
									
									$distinct_labs[] = $lab; // add current lab to distinct ones
									break; // stop comparing further fields
								}
								
							} // end of foreach lab
			
						} // end of foreach lab_list
						
						$lab_list = $distinct_labs; // repeat the procedure on distinct_labs array (all different from the current selected lab)
						$final_distinct_labs[$iter] = $distinct_labs;
			
					} while ($refine_condition);
					


					$distinct_labs_count[$mod] = count($final_distinct_labs[$iter-1]);
					$total_distinct_sessions += $distinct_labs_count[$mod];
					$current .= "\nDistinct labs for Module $mod: {$distinct_labs_count[$mod]}\n";
					
					//$current .= print_r($final_distinct_labs[$iter-1], true);
					
		
					// find Module Leader by Module code and insert the lab record into the Lab_Requirements table
					$query_leader_by_module = $conn->prepare("SELECT Account_ID 
															  FROM Leader_Module 
															  WHERE Module_Code='$mod' LIMIT 1;");
					$query_leader_by_module->execute();
					
					while ($module_leader_found = $query_leader_by_module -> fetch(PDO::FETCH_ASSOC)) {
						$acc_id = $module_leader_found['Account_ID'];
					}
					//echo "<br>Account ID = $acc_id <br>";
					$mod_checked = test_input($mod);
					//echo "Module = $mod_checked <br>";
				
					foreach ($final_distinct_labs[$iter-1] as $counter1 => $lab1){
						
						$type = $room = $description = "";
						
						foreach ($lab1 as $key1 => $val1) {
							switch (strtolower($key1)) {
								case "type":
									$type = test_input($val1);
									break;
								case "description":
									$description = test_input($val1);
									break;
								case "room":
									$room = test_input($val1);
									break;
							}
		
						} // end for each final distinct labs
						
						$staff_no = 0; // default number of required staff members
						//echo "Type = $type ; room = $room ; description = $description ; randint = $rand_int<br>";
						
											
						$query_insert_lab_session = $conn->prepare($sql);
						
						$skill_required = 0; // default skill level
						foreach ($fields as $f){
							if ($f == 'Lab_ID') {
								continue;
							} else if ($f == 'Account_ID') {
								$query_insert_lab_session->bindParam(":$f", $acc_id);
							} else if ($f == 'Type') {
								$query_insert_lab_session->bindParam(":$f", $type);
							} else if ($f == 'Room') {
								$query_insert_lab_session->bindParam(":$f", $room);
							} else if ($f == 'Description') {
								$query_insert_lab_session->bindParam(":$f", $description);
							} else if ($f == 'Module') {
								$query_insert_lab_session->bindParam(":$f", $mod_checked);
							} else if ($f == 'No_Staff') {
								$query_insert_lab_session->bindParam(":$f", $staff_no);
							} else if (substr($f, 0, 5) == "Skill") { // to input Skills level for each Skill. Will allow to add more Skill fields to the DB without changing this code
								$query_insert_lab_session->bindParam(":$f", $skill_required);
							} else {
								echo "ERROR: Unrecognised field name '$f'!";
								exit; // exit further script
							}
						}
						
						/************************************* UNCOMMENT BELOW to REALLY create a new timetable!!!!!!***********************************/
						
						$query_insert_lab_session->execute(); 
						
						/******************************************************************************************************************************/
						
		
						$records_no[$mod] ++;
						
					} // end foreach final list of distinct labs
					
					//echo "<br>$records_no[$mod] records inserted into $db_table for Module $mod.<br>";
					$current .= "\n{$records_no[$mod]} distinct records inserted into $db_table for Module $mod.\n";	
					$total_records_no += $records_no[$mod];
					
					
					
					

/********************************** ADD TIME_SLOTS OF EACH RUNNING LAB TO ITS DISTINCT PARENT LAB (PUSH ONTO ITS [Slot_ID] FIELD) **********************/

					$current_lab_slots_no[$mod] = 0;
					$different = false;
					
					// computational complexity = about 112500 steps, but skips a lot of inner loops
					foreach ($final_distinct_labs[$iter-1] as $counter2 => $lab2){ // iterate over distinct labs (normally < 10 per Module)
						
						foreach ($labs_module[$mod] as $counter3 => $lab3){ // iterate over all module labs (about 50 per Module)
						
							foreach ($lab2 as $field2 => $value2){ // iterate over all distinct lab's fields (15)
								
								foreach ($lab3 as $field3 => $value3){ // iterate over all current lab's fields (15)
						
									//compare all the fields of every distinct lab2 session to every field of the current lab3 
									if ($field3 == "Start_date" || $field2 == "Start_date" || $field3 == "Start_week_timestamp" 
										|| $field2 == "Start_week_timestamp" || $field3 == "Week_No" || $field2 == "Week_No" || $field3 == "Day" 
										|| $field2 == "Day" || $field3 == "Time" || $field2 == "Time" || $field3 == "Slot_ID" || $field2 == "Slot_ID"){
								
										continue; // to the next field3 (ignore those fields as they are not indicative of a different lab)
								
									} else if ($field2 == $field3){
										if ($value2 == $value3) {// the two fields are the same
											$different = false;
											break; // to the next field2
										} else {
											$different = true;
											break; // to the next field2
										}
									}
								} // end foreach field3
								
								if ($different == true) {
									break; // to the next counter3
								}
							} // end foreach field2
							if ($different == false) { // the two labs are the same or the current lab is a child of the distinct lab 
								if ($lab2["Start_date"] != $lab3["Start_date"]) { // the current lab is the distinct lab itself 
									// add current lab's Slot_ID to the array of Slot_IDs of the distinct lab
									array_push($lab2["Slot_ID"], $lab3["Slot_ID"]);
									
								}
							}
							
						} // end foreach lab3
						
						$current_lab_slots_no[$mod] += count($lab2["Slot_ID"]);
						$total_slots_no += count($lab2["Slot_ID"]);
						$final_distinct_labs_with_timeslots[$mod][] = $lab2;
						
					} // end foreach lab2
					

					// Flatten "Slot_ID" subarray and populate Lab_Timetable table
					foreach($final_distinct_labs_with_timeslots[$mod] as $counter4 => $lab4){
						
						$flat_Slot_ID_array = flatten($lab4["Slot_ID"]);
						$final_distinct_labs_with_timeslots[$mod][$counter4]["Slot_ID"] = $flat_Slot_ID_array;
						
						// find the Lab_ID from Lab_Requirements table
						$type1 = test_input ($lab4["Type"]);
						$description1 = test_input ($lab4["Description"]);
						$room1 = test_input ($lab4["Room"]);
						
						$query_find_lab_ID = $conn->prepare("SELECT Lab_ID 
															FROM $db_table
															WHERE Type='$type1' AND Room='$room1' AND Description='$description1' AND Module='$mod';");
						$query_find_lab_ID->execute();
						
						$lab_id = NULL;
						
						while ($lab_ID_found = $query_find_lab_ID -> fetch(PDO::FETCH_ASSOC)) {
							$lab_id = $lab_ID_found['Lab_ID'];
						}
						
						
						
/********************************** POPULATE Lab_Timetable DB TABLE WITH ALL THE LAB IDs AND SLOT IDs **********************/				
						
						$query_populate_lab_timetable = $conn->prepare($sql_populate_lab_timetable);
						
						$length_slot_IDs = count($flat_Slot_ID_array);
						for ($i = 0; $i < $length_slot_IDs; $i++){
							
							if ($lab_id != NULL){
								$query_populate_lab_timetable->bindParam(":Lab_ID", $lab_id);
							} else {
								echo "ERROR: Undefined field name 'Lab_ID'!";
								exit; // exit further script
							}
							
							$slot_id = $flat_Slot_ID_array[$i];
							
							$query_populate_lab_timetable->bindParam(":Slot_ID", $slot_id);
							
							$current .= "Lab ID = $lab_id\n";
							$current .= "Slot_ID = $slot_id\n";
							
							
							/********************************** UNCOMMENT BELOW to REALLY create a new timetable!!!!!!********************************/
							
							$query_populate_lab_timetable->execute();
							
							/*************************************************************************************************************************/
							
						}
						$current .= "\nquery_populate_lab_timetable has been executed!\n";
					}
					
/******************************************************************************************************************************/	

					//$current .= print_r($final_distinct_labs[$iter-1], true);

					$current .= print_r($final_distinct_labs_with_timeslots[$mod], true);
					$current .= "\n Number of labs for Module $mod: {$slot_count[$mod]}\n"; // DEBUG INFO
					$current .= "\n Number of slots for Module $mod: {$current_lab_slots_no[$mod]}\n\n\n"; // count maybe slightly different from number of labs above since some of the 2 hr sessions are represented as 2 array elements, and the others aren't
					
				} // next Module
				
				$conn = NULL; // close connection to the DB
				
				//echo "<br>Total $total_records_no records inserted into $db_table<br>";
				$current .= "\nTotal $total_records_no records inserted into $db_table\n\n";
				$current .= "\nTotal distinct sessions: $total_distinct_sessions\n";
				//$current .= "\nWeeks commencing:\n";
				//$current .= print_r($week_com, true);
				//$current .= "\nSession types map:\n";
				//$current .= print_r($map_session, true);
				$current .= "\nLab/tutorial sessions only ($lab_count); total slots number ($total_slots_no)\n";
				//$current .= print_r($labs, true);
				//$current .= "\nLab/tutorial sessions sorted by Module ($labs_module_count)\n";
				//$current .= print_r($labs_module, true);
		
				//$current .= "\n";
				// Write the contents to the log file
				$current .= "\n********************* SUCCESS ************************\n";						
				file_put_contents($file, $current);
				
				//Now redirect to itself with the success message (that clears the form as well) and exit further script
				header("Location: $location?message=The file is successfully uploaded and the timetable is created from it.");
				exit;
		
			} catch(PDOException $e){
				$err_output = $e->getMessage();				
				echo "Error: " . $err_output;
				$current .= $err_output;
				$current .= "\n********************* PDO ERROR 2 ************************\n";		
				file_put_contents($file, $current);
				$conn = NULL; //close DB connection
			}
			
			$while_loop_counter = 0;
  		} while ($while_loop_counter != 0); //end of the outer do...while loop

		unset($_POST['btn_upload']);
		unset($_FILES["file_name"]);
		
		// Write the contents to the log file
		$current .= "\n*************************** SERVER VALIDATION ERRORS *********************\n";		
  		file_put_contents($file, $current);
	}
	
	?>
    
    
    <br>
	<!-- http://php.net/manual/en/features.file-upload.post-method.php -->
    <!-- The data encoding type, enctype, MUST be specified as below -->
    <form enctype="multipart/form-data" method="POST" onSubmit="return verifyUpload(this)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <span class="error">* </span> Required fields
		<br><br>
        <!-- MAX_FILE_SIZE must precede the file input field in Bytes-->
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <!-- Name of input element determines name in $_FILES array -->
        <label>Upload this '.csv' file: </label><input name="file_name" type="file" value="<?php echo $file_name;?>"/>
        <span class="error">* <?php echo $fileErr;?></span>
        <br><br>
        <input type="submit" name="btn_upload" title="Upload an (.csv) timetable file" value="Upload" />
    </form>

	
<?php
	
	echo "<p><span class = \"info\"> $message </span></p>";

	echo "<br><input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>