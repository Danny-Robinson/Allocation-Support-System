<?php

	include 'validate_login.php';
	
	if (($_SESSION['curr_account_type'] != 0)&&($_SESSION['curr_account_type'] != 2)&&($_SESSION['curr_account_type'] != 4)) {
		header("Location: no_permission_for_page.php"); // redirect if incorrect account type.
		exit; // exit further script
	}	

?>
	<h1>Week <?php convertWeekNumber($weekNo); echo ' '.htmlspecialchars($_GET['module']);?> </h1>

	 
	<table class='TimeView'><!-- following 5 lines display the navigation buttons calculating the correct URLs -->
		<th><a href='timetableMODview.php?module=<?php echo htmlspecialchars($_GET['module']); ?>&ID=<?php echo htmlspecialchars($_GET['ID']); ?>&shift=<?php echo 1-($weekNo-$_GET['shift']);?>'>first</a></th>
		<th><a href='timetableMODview.php?module=<?php echo htmlspecialchars($_GET['module']); ?>&ID=<?php echo htmlspecialchars($_GET['ID']); ?>&shift=<?php if ($weekNo != 1) { echo $_GET['shift']-1; } else { echo $_GET['shift']; }?>'>previous</a></th>
		<th><a href='timetableMODview.php?module=<?php echo htmlspecialchars($_GET['module']); ?>&ID=<?php echo htmlspecialchars($_GET['ID']); ?>&shift=0'>current</a></th>
		<th><a href='timetableMODview.php?module=<?php echo htmlspecialchars($_GET['module']); ?>&ID=<?php echo htmlspecialchars($_GET['ID']); ?>&shift=<?php if ($weekNo != $finWeek) { echo $_GET['shift']+1; } else { echo $_GET['shift']; }?>'>next</a></th>
		<th><a href='timetableMODview.php?module=<?php echo htmlspecialchars($_GET['module']); ?>&ID=<?php echo htmlspecialchars($_GET['ID']); ?>&shift=<?php echo $finWeek-($weekNo-$_GET['shift']);?>'>last</a></th>
	</table><br>

<?php
	if ($_GET['module']!='Student') {
		$findAddresses =   "SELECT Time_Slots.Week, Time_Slots.Day, Time_Slots.Time, Lab_Requirements.Description, Lab_Requirements.Module, Lab_Requirements.Room, Lab_Timetable.Session_ID FROM Time_Slots
							INNER JOIN Lab_Timetable ON Time_Slots.Slot_ID = Lab_Timetable.Slot_ID 
							INNER JOIN Lab_Requirements ON Lab_Timetable.Lab_ID = Lab_Requirements.Lab_ID
							WHERE Lab_Requirements.Module = '" .htmlspecialchars($_GET['module']). "' AND Time_Slots.Slot_ID >= " . intval($matchTime) . " AND Time_Slots.Slot_ID <= " . intval($matchTime+44);
	}
	else {
		$findAddresses =   "SELECT Time_Slots.Slot_ID, Lab_Timetable.Lab_ID, Time_Slots.Week, Time_Slots.Day, Time_Slots.Time, Lab_Requirements.Description, Lab_Requirements.Module, Lab_Requirements.Room, Lab_Timetable.Session_ID FROM Time_Slots
							INNER JOIN Lab_Timetable ON Time_Slots.Slot_ID = Lab_Timetable.Slot_ID 
							INNER JOIN Lab_Requirements ON Lab_Timetable.Lab_ID = Lab_Requirements.Lab_ID
							INNER JOIN Allocations ON Allocations.Session_ID = Lab_Timetable.Session_ID 
							WHERE Allocations.Account_ID = '" .intval($_GET['ID']). "' AND Time_Slots.Slot_ID >= " . intval($matchTime) . " AND Time_Slots.Slot_ID <= " . intval($matchTime+44);
	}
	try {  // query database to find session times for selected module.
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
	
	// thisAddress function is called once for each space on the timetable.
	// Requires the address of the space and the addresses 2D array.
	function thisAddress($testAddress, $addresses, $conn) {
		if (array_key_exists($testAddress, $addresses)){ // Check if there is an allocation in this space.
			$allocatedNum=0;
			echo '<span class="popup-trigger"><span class="popup"><p>';
			echo $addresses[$testAddress][0];			 // If allocation exists display details.
			echo '<br> Allocated PHD Students:<br>';
								// query database to find PHD students allocated to this session.
			$allocatedStudents = "SELECT PHD_Students.Forename, PHD_Students.Surname, Account_Data.Email FROM PHD_Students
								  INNER JOIN Allocations ON Allocations.Account_ID = PHD_Students.Account_ID
								  INNER JOIN Account_Data ON Account_Data.Account_ID = PHD_Students.Account_ID
								  WHERE Allocations.Session_ID = " . intval($addresses[$testAddress][3]);
			try {
				foreach ($conn->query($allocatedStudents) as $row) {
					echo $row['Forename'].' '.$row['Surname'].'<br>'; // append student names to pop-up.
					$allocatedNum++;
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
	
			echo '</p></span><a href="timetableMODview.php?module='.htmlspecialchars($_GET['module']).'&shift='.htmlspecialchars($_GET['shift']).'&ID='.htmlspecialchars($_GET['ID']).'&info='.$testAddress.'&session='.$addresses[$testAddress][3].'">'.$addresses[$testAddress][1];
			echo '<br>Rm: ' . $addresses[$testAddress][2] . '</a>';
			echo '</span>';
		}
		else{
			echo "-"; // Display a dash if there is no allocation.
		}
		return $allocatedNum;
	}
?>

<table class='TimeView'>

<tr><th></th><th>0900-0950</th><th>1000-1050</th><th>1110-1200</th><th>1210-1300</th><th>1310-1400</th><th>1410-1500</th><th>1510-1600</th><th>1610-1700</th><th>1710-1800</th></tr>
<tr>
	<th>Mon</th>
	<td><?php $allocated[11] = thisAddress(11, $addresses, $conn); ?></td> <!-- Call thisAddress function with the address of each space -->
	<td><?php $allocated[12] = thisAddress(12, $addresses, $conn); ?></td>
	<td><?php $allocated[13] = thisAddress(13, $addresses, $conn); ?></td>
	<td><?php $allocated[14] = thisAddress(14, $addresses, $conn); ?></td>
	<td><?php $allocated[15] = thisAddress(15, $addresses, $conn); ?></td>
	<td><?php $allocated[16] = thisAddress(16, $addresses, $conn); ?></td>
	<td><?php $allocated[17] = thisAddress(17, $addresses, $conn); ?></td>
	<td><?php $allocated[18] = thisAddress(18, $addresses, $conn); ?></td>
	<td><?php $allocated[19] = thisAddress(19, $addresses, $conn); ?></td>
</tr>
<tr>
	<th>Tue</th>
	<td><?php $allocated[21] = thisAddress(21, $addresses, $conn); ?></td>
	<td><?php $allocated[22] = thisAddress(22, $addresses, $conn); ?></td>
	<td><?php $allocated[23] = thisAddress(23, $addresses, $conn); ?></td>
	<td><?php $allocated[24] = thisAddress(24, $addresses, $conn); ?></td>
	<td><?php $allocated[25] = thisAddress(25, $addresses, $conn); ?></td>
	<td><?php $allocated[26] = thisAddress(26, $addresses, $conn); ?></td>
	<td><?php $allocated[27] = thisAddress(27, $addresses, $conn); ?></td>
	<td><?php $allocated[28] = thisAddress(28, $addresses, $conn); ?></td>
	<td><?php $allocated[29] = thisAddress(29, $addresses, $conn); ?></td>
</tr>
<tr>
	<th>Wed</th>
	<td><?php $allocated[31] = thisAddress(31, $addresses, $conn); ?></td>
	<td><?php $allocated[32] = thisAddress(32, $addresses, $conn); ?></td>
	<td><?php $allocated[33] = thisAddress(33, $addresses, $conn); ?></td>
	<td><?php $allocated[34] = thisAddress(34, $addresses, $conn); ?></td>
	<td><?php $allocated[35] = thisAddress(35, $addresses, $conn); ?></td>
	<td><?php $allocated[36] = thisAddress(36, $addresses, $conn); ?></td>
	<td><?php $allocated[37] = thisAddress(37, $addresses, $conn); ?></td>
	<td><?php $allocated[38] = thisAddress(38, $addresses, $conn); ?></td>
	<td><?php $allocated[39] = thisAddress(39, $addresses, $conn); ?></td>
</tr>
<tr>
	<th>Thu</th>
	<td><?php $allocated[41] = thisAddress(41, $addresses, $conn); ?></td>
	<td><?php $allocated[42] = thisAddress(42, $addresses, $conn); ?></td>
	<td><?php $allocated[43] = thisAddress(43, $addresses, $conn); ?></td>
	<td><?php $allocated[44] = thisAddress(44, $addresses, $conn); ?></td>
	<td><?php $allocated[45] = thisAddress(45, $addresses, $conn); ?></td>
	<td><?php $allocated[46] = thisAddress(46, $addresses, $conn); ?></td>
	<td><?php $allocated[47] = thisAddress(47, $addresses, $conn); ?></td>
	<td><?php $allocated[48] = thisAddress(48, $addresses, $conn); ?></td>
	<td><?php $allocated[49] = thisAddress(49, $addresses, $conn); ?></td>
</tr>
<tr>
	<th>Fri</th>
	<td><?php $allocated[51] = thisAddress(51, $addresses, $conn); ?></td>
	<td><?php $allocated[52] = thisAddress(52, $addresses, $conn); ?></td>
	<td><?php $allocated[53] = thisAddress(53, $addresses, $conn); ?></td>
	<td><?php $allocated[54] = thisAddress(54, $addresses, $conn); ?></td>
	<td><?php $allocated[55] = thisAddress(55, $addresses, $conn); ?></td>
	<td><?php $allocated[56] = thisAddress(56, $addresses, $conn); ?></td>
	<td><?php $allocated[57] = thisAddress(57, $addresses, $conn); ?></td>
	<td><?php $allocated[58] = thisAddress(58, $addresses, $conn); ?></td>
	<td><?php $allocated[59] = thisAddress(59, $addresses, $conn); ?></td>
</tr>
</table>		
