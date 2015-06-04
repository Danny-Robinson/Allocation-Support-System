<?php
	$header='Your Timetable';
	include 'template-top.php';
	include 'convert_week_number.php';
	include 'validate_login.php';
	
	if ($_SESSION['curr_account_type'] != 1) { // if PHD Student is not logged in
		header("Location: no_permission_for_page.php"); // redirect if incorrect account type.
		exit; // exit further script
	}
	
	$addresses = array();
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	
	$weekNo = 0;
	$time = strtotime('now'); // current time-stamp
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
		//Query database to extract the times and details of all allocations for the logged in PHD student for the selected week
		$findAddresses =   "SELECT Time_Slots.Week, Time_Slots.Day, Time_Slots.Time, Lab_Requirements.Description, Lab_Requirements.Module, Lab_Requirements.Room, Lab_Timetable.Session_ID FROM Time_Slots
							INNER JOIN Lab_Timetable ON Time_Slots.Slot_ID = Lab_Timetable.Slot_ID 
							INNER JOIN Lab_Requirements ON Lab_Timetable.Lab_ID = Lab_Requirements.Lab_ID
							INNER JOIN Allocations ON Allocations.Session_ID = Lab_Timetable.Session_ID 
							WHERE Allocations.Account_ID = ". $_SESSION['curr_user_id'] ." AND Time_Slots.Slot_ID >= " . intval($matchTime) . " AND Time_Slots.Slot_ID <= " . intval($matchTime+44);
		foreach ($conn->query($findAddresses) as $row) {
			// Calculate a timetable address based on the day and time of each allocation.
			// Calculated address is stored as a key in a two dimensional array.
			// Details of each allocation are stored in the array under the corresponding address.
			$addresses[($row['Day']*10+$row['Time'])]=array($row['Description'], $row['Module'], $row['Room'], $row['Session_ID']);

        }
    }
	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
    }
	$conn = null;	
	// thisAddress function is called once for each space on the timetable.
	// Requires the address of the space and the addresses 2D array.
	function thisAddress($testAddress, $addresses) {
		if (array_key_exists($testAddress, $addresses)){ // Check if there is an allocation in this space.
			echo '<span class="popup-trigger"><span class="popup"><p>';
			echo $addresses[$testAddress][0];			 // If allocation exists display details.
			echo '</p></span><a href="swapAid.php?shift='.$_GET['shift'].'&info='.$testAddress.'&session='.$addresses[$testAddress][3].'">'. $addresses[$testAddress][1];
			echo '<br>Rm: ' . $addresses[$testAddress][2].'</a>';
			echo '</span>';
		}
		else{
			echo "-"; // Display a dash if there is no allocation.
		}
	}
?>

<h1>Week <?php convertWeekNumber($weekNo);?> </h1>

 
<table class='TimeView'><!-- following 5 lines display the navigation buttons calculating the correct URLs -->
	<th><a href='timetablePHDview.php?shift=<?php echo 1-($weekNo-$_GET['shift']);?>'>first</a></th>
	<th><a href='timetablePHDview.php?shift=<?php if ($weekNo != 1) { echo $_GET['shift']-1; } else { echo $_GET['shift']; }?>'>previous</a></th>
	<th><a href='timetablePHDview.php?shift=0'>current</a></th>
	<th><a href='timetablePHDview.php?shift=<?php if ($weekNo != $finWeek) { echo $_GET['shift']+1; } else { echo $_GET['shift']; }?>'>next</a></th>
	<th><a href='timetablePHDview.php?shift=<?php echo $finWeek-($weekNo-$_GET['shift']);?>'>last</a></th>
</table><br>

	
<table class='TimeView'>

<tr><th></th><th>0900-0950</th><th>1000-1050</th><th>1110-1200</th><th>1210-1300</th><th>1310-1400</th><th>1410-1500</th><th>1510-1600</th><th>1610-1700</th><th>1710-1800</th></tr>
<tr>
	<th>Mon</th>
	<td><?php thisAddress(11, $addresses); ?></td> <!-- Call thisAddress function with the address of each space -->
	<td><?php thisAddress(12, $addresses); ?></td>
	<td><?php thisAddress(13, $addresses); ?></td>
	<td><?php thisAddress(14, $addresses); ?></td>
	<td><?php thisAddress(15, $addresses); ?></td>
	<td><?php thisAddress(16, $addresses); ?></td>
	<td><?php thisAddress(17, $addresses); ?></td>
	<td><?php thisAddress(18, $addresses); ?></td>
	<td><?php thisAddress(19, $addresses); ?></td>
</tr>
<tr>
	<th>Tue</th>
	<td><?php thisAddress(21, $addresses); ?></td>
	<td><?php thisAddress(22, $addresses); ?></td>
	<td><?php thisAddress(23, $addresses); ?></td>
	<td><?php thisAddress(24, $addresses); ?></td>
	<td><?php thisAddress(25, $addresses); ?></td>
	<td><?php thisAddress(26, $addresses); ?></td>
	<td><?php thisAddress(27, $addresses); ?></td>
	<td><?php thisAddress(28, $addresses); ?></td>
	<td><?php thisAddress(29, $addresses); ?></td>
</tr>
<tr>
	<th>Wed</th>
	<td><?php thisAddress(31, $addresses); ?></td>
	<td><?php thisAddress(32, $addresses); ?></td>
	<td><?php thisAddress(33, $addresses); ?></td>
	<td><?php thisAddress(34, $addresses); ?></td>
	<td><?php thisAddress(35, $addresses); ?></td>
	<td><?php thisAddress(36, $addresses); ?></td>
	<td><?php thisAddress(37, $addresses); ?></td>
	<td><?php thisAddress(38, $addresses); ?></td>
	<td><?php thisAddress(39, $addresses); ?></td>
</tr>
<tr>
	<th>Thu</th>
	<td><?php thisAddress(41, $addresses); ?></td>
	<td><?php thisAddress(42, $addresses); ?></td>
	<td><?php thisAddress(43, $addresses); ?></td>
	<td><?php thisAddress(44, $addresses); ?></td>
	<td><?php thisAddress(45, $addresses); ?></td>
	<td><?php thisAddress(46, $addresses); ?></td>
	<td><?php thisAddress(47, $addresses); ?></td>
	<td><?php thisAddress(48, $addresses); ?></td>
	<td><?php thisAddress(49, $addresses); ?></td>
</tr>
<tr>
	<th>Fri</th>
	<td><?php thisAddress(51, $addresses); ?></td>
	<td><?php thisAddress(52, $addresses); ?></td>
	<td><?php thisAddress(53, $addresses); ?></td>
	<td><?php thisAddress(54, $addresses); ?></td>
	<td><?php thisAddress(55, $addresses); ?></td>
	<td><?php thisAddress(56, $addresses); ?></td>
	<td><?php thisAddress(57, $addresses); ?></td>
	<td><?php thisAddress(58, $addresses); ?></td>
	<td><?php thisAddress(59, $addresses); ?></td>
</tr>
</table>		
<br>Click on a session to request a shift swap.
<?php
	include 'template-bottom.php';
?>