<?php
	$header='Allocation Timetable';
	include 'template-top.php';
	include 'convert_week_number.php';
	include 'validate_login.php';
	
	if (($_SESSION['curr_account_type'] != 0)&&($_SESSION['curr_account_type'] != 2)&&($_SESSION['curr_account_type'] != 4)) {
		header("Location: no_permission_for_page.php"); // redirect if incorrect account type.
		exit; // exit further script
	}	
	
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	
	$weekNo = 0;
	$time = strtotime('now'); // current timestamp
	$matchTime = 1;
	$finWeek = 0;
	

		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//Query database to find time slots for the current week.
			$matchTimeSQL = "SELECT * FROM Week_Date";
			foreach ($conn->query($matchTimeSQL) as $row) {
				if (($row['Week_Com'])<=($time)) {
					$matchTime = $row['Slot_ID']; // sets the first slot_ID of the current week
					$weekNo++;
				}
				$finWeek++;
			}
			$matchTime = $matchTime + (45*$_GET['shift']); // adjusts the starting slot_ID if the user selects a different week
			$weekNo = $weekNo + $_GET['shift']; // adjusts the week number if the user selects a different week
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		
	if ($_GET['module']!=null) { // Timetable not displayed until a module is selected
		include 'timetableMODviewDISPLAY.php'; // finds allocations and displays the timetable

	}
	
	else {
	
		include 'timetableMODviewUNSET.php'; // prompts user to select a module if nine selected
		
	}

	include 'timetableMODviewSET.php'; // module selection

	include 'template-bottom.php';
?>