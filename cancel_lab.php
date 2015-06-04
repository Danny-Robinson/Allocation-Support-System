<?php
	$header = 'Cancel Session';
	include 'template-top.php';
	
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	$slots = array(0,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26,27,28,29,31,32,33,34,35,36,37,38,39,41,42,43,44,45,46,47,48,49,51,52,53,54,55,56,57,58,59);
	$days = array("", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$times = array("", "0900-0950", "1000-1050", "1110-1200", "1210-1300", "1310-1400", "1410-1500", "1510-1600", "1610-1700", "1710-1800");
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$notificationsQ = "SELECT * FROM Lab_Timetable WHERE Session_ID = ".intval($_GET['session']);
			foreach ($conn->query($notificationsQ) as $row) {
				$slotID = $row['Slot_ID'];
				$weeklySlot = $slotID%45;							//get slot from session ID and link to week/time via labtimetable
				$week = floor($slotID/45) + 1;
				$day = $days[floor($slots[$weeklySlot]/ 10)];				
				$time = $times[$slots[$weeklySlot] % 10];
				$labInfo = "Week ".$week." ".$day." ".$time;
				$message = "Your session at {$labInfo}  was cancelled by staff, please check your timetable to confirm";
				$notificationsQ = "SELECT * FROM Allocations WHERE Session_ID = ".intval($_GET['session']);
				foreach ($conn->query($notificationsQ) as $row) {
					$accountID = $row['Account_ID'];
					$insertNotification = $conn->prepare("INSERT INTO Notifications (Account_ID, Message) VALUES ('$accountID', '$message');");
					$insertNotification->execute();
				}	
			}
			$cancelLab = "DELETE FROM Lab_Timetable WHERE Session_ID = ".intval($_GET['session']);
			$conn->exec($cancelLab);
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		$conn=null;
		echo '<form>
			  The session has been cancelled.<br><br>
			  <a href="timetableMODview.php"><input type="button" value="back to timetable"/></a>';
	}
	else {
		echo '<form method="POST" action="cancel_lab.php?module='.htmlspecialchars($_GET['module']).'&shift='.htmlspecialchars($_GET['shift']).'&ID='.htmlspecialchars($_GET['ID']).'&info='.htmlspecialchars($_GET['info']).'&session='.htmlspecialchars($_GET['session']).'">';
		echo 'WARNING: you are about to cancel a timetabled session!<br>
			  By pressing submit the session will be cancelled.<br>
			  <a href="timetableMODview.php?module='.htmlspecialchars($_GET['module']).'&shift='.htmlspecialchars($_GET['shift']).'&ID='.htmlspecialchars($_GET['ID']).'&info='.htmlspecialchars($_GET['info']).'&session='.htmlspecialchars($_GET['session']).'">If you did not intend to cancel this session click here to return to the previous page.</a><br>
			  <br>
			  <input type="submit" value="submit"/><br><br><br>
			  </form>';
	}
	
	include 'template-bottom.php'; //NOTE: This will not automatically send notifications to persons associated with the session.<br>
?>
