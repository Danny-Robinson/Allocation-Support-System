<?php
// 	error_reporting(E_ALL);
// 	ini_set('display_errors', '1');
	$message=$_GET["message"];
	echo "$message";
	$header = 'Pending Swaps';
	include 'template-top.php';
	include 'validate_login.php';
?>
  
<?php session_start();?>

<?php
		$servername = "planetmeerkat.co.uk";		//set up DB connection variable
		$username = "planetme_lab1ent";
		$password = "gr4BFoxcan13";
		$dbname = "planetme_lab-support";
		
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
 		$slots = array(0,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26,27,28,29,31,32,33,34,35,36,37,38,39,41,42,43,44,45,46,47,48,49,51,52,53,54,55,56,57,58,59);
 		$days = array("", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
 		$times = array("", "0900-0950", "1000-1050", "1110-1200", "1210-1300", "1310-1400", "1410-1500", "1510-1600", "1610-1700", "1710-1800");
		$userID = $_SESSION['curr_user_id']; //get account id
		$username = $_SESSION['curr_username']; //get username
		$accountType = $_SESSION['curr_account_type']; //get logged in user type  
		$sessions[] = array();
		$requests[] = array();
		$labInfoArray[] = array();
		
		if ($accountType != 1) { // if not a student
			echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a Student</span><br>";
			exit; // exit further script
		}
		
		try{
			$swapsQ = "SELECT * FROM Pending_Swaps";		//fill session array with session IDs of swaps for logged in student
			foreach ($conn->query($swapsQ) as $row) {
				
				if ($userID == $row['Account_ID']){
					$sessions[] = $row['Session_ID'];
					//$requests[] = $row['Request_ID'];
				}
				if ($userID == $row['Request_ID']){
					$requests[] = $row['Session_ID'];
				}
			}
		}catch (PDOException $e){
			echo "Error: ".$e;
		}
?>
								<?php ////////Incoming Swaps///////// ?>
<div id = "Swap Requested">
 
 	<h2>Incoming Swap Requests</h2>
 	<form action="" method="post">
 	<?php
 		$sessionsNum = count($sessions);
 	?>
	<select name="swapsSelect">		
	<?php			
		try{					//DROPDOWN FOR DISPLAY
			for ($i=0;$i<$sessionsNum;$i++){
				$sessionID = $sessions[$i];
				$timeQ = "SELECT * FROM Lab_Timetable WHERE Session_ID = '$sessionID'";
				if ($sessionID != null){
					foreach ($conn->query($timeQ) as $row) {
						$slotID = $row['Slot_ID'];
					}
					$weeklySlot = $slotID%45;							//get slot from session ID and link to week/time via labtimetable
					$week = floor($slotID/45) + 1;
					$day = $days[floor($slots[$weeklySlot]/ 10)];				//alternative:  - $slots[$weeklySlot] % 10)
					$time = $times[$slots[$weeklySlot] % 10];
					$labInfo = "Week ".$week." ".$day." ".$time;
					//echo $slotID, ", ", $weeklySlot, ", ", $day, ", ", $days[$slots[$weeklySlot]], ", ", $time, ", ", $times[$slots[$weeklySlot]];
					
	?>
			<option value="<?=$sessions[$i];?>"><?=$labInfo;?></option>
	<?php
			$labInfoArray[$sessions[$i]] = $labInfo;
				}
			}
		}catch (PDOException $e){
			echo "Error: ".$e();
		}
	?>
	</select>
	<input name="acceptSwap" type="submit" value="Accept">
	<input name="declineSwap" type="submit" value="Decline">
	</form>
 </div>
 
 								<?php ////////Outgoing Swaps///////// ?>
 
 <div id = "Swap O Requested">
 
 	<h2>Outgoing Swap Requests</h2>
 	<form action="" method="post">
 	<?php
 		$requestsNum = count($requests);
 	?>
	<select name="swapsOSelect">		
	<?php			
		try{					//DROPDOWN FOR DISPLAY
			//for ($i=0;$i<$requestsNum;$i++){
				//$requestID = $requests[$i];
				$requestQ = "SELECT * FROM Pending_Swaps WHERE Request_ID = '$userID'";
				foreach ($conn->query($requestQ)as $row){
					$sessionID = $row['Session_ID'];
					$timeQ = "SELECT * FROM Lab_Timetable WHERE Session_ID = '$sessionID'";
					if ($sessionID != null){
						foreach ($conn->query($timeQ) as $row) {
							$slotID = $row['Slot_ID'];
						}
						$weeklySlot = $slotID%45;							//get slot from session ID and link to week/time via labtimetable
						$week = floor($slotID/45) + 1;
						$day = $days[floor($slots[$weeklySlot]/ 10)];				// - $slots[$weeklySlot] % 10)  or use floor()
						$time = $times[$slots[$weeklySlot] % 10];
						$labInfo = "Week ".$week." ".$day." ".$time;
					//echo $slotID, ", ", $weeklySlot, ", ", $day, ", ", $days[$slots[$weeklySlot]], ", ", $time, ", ", $times[$slots[$weeklySlot]];
					
	?>
			<option value="<?=$requests[$i];?>"><?=$labInfo;?></option>
	<?php
		$labInfoArray[$requests[$i]] = $labInfo;
				}
			}
		}catch (PDOException $e){
			echo "Error: ".$e;
		}
	?>
	</select>
	<input name="cancelSwap" type="submit" value="Cancel">
	</form>
	<p>Please confirm any swap results by checking your timetable</p>
 </div>
 
 <?php 
 //CANCEL SWAP BUTTON
 
	if (isset($_POST['cancelSwap'])) {
		$sessionID = $_POST['swapsOSelect'];
		$cancelSwap = $conn->prepare("DELETE FROM Pending_Swaps WHERE Request_ID= '$userID' AND Session_ID= '$sessionID';");
		$cancelSwap->execute();
		?>
		<script type="text/javascript">confirm("Swap Cancelled");</script>
		<?php 
	}
	
//DECLINE SWAP BUTTON
 	if (isset($_POST['declineSwap'])) {
	 	$sessionID = $_POST['swapsSelect'];
	 	$accountID = 0;
	 	$noteType = 0;
	 	$noteInfo = null;
	 	$accountQ = "SELECT * FROM Pending_Swaps WHERE Session_ID = '$sessionID';";
	 	foreach ($conn->query($accountQ)as $row){
	 		$accountID = $row['Request_ID'];
	 		$swapeeID = $row['Account_ID'];
//	 		$noteType = 2;
	 		}
	 		if ($labInfoArray[$sessionID]  != null){
	 			$noteInfo = $labInfoArray[$sessionID];
	 		}
	 		$student = "SELECT * FROM PHD_Students WHERE Account_ID = '$swapeeID'";
	 		$studentr = $conn->query($student);
	 		$studentR = $studentr->fetch();
	 		$forename = $studentR['Forename'];
	 		//$initial = $studentR['Initial'];
	 		$surname = $studentR['Surname'];
	 		$name = $forename." ".$surname;
	 		$message = "Your swap request to {$name} for the {$noteInfo} session was declined; please check timetable to confirm";
	 		$insertNotification = $conn->prepare("INSERT INTO Notifications (Account_ID, Message) VALUES ('$accountID', '$message');");
		 	$insertNotification->execute();
	 		$deleteSwap = $conn->prepare("DELETE FROM Pending_Swaps WHERE Session_ID= '$sessionID';");
	 		$deleteSwap->execute();	
	 	?>
			<script type="text/javascript">
				confirm("Swap Declined");
			</script>
		<?php 
 	}
 //ACCEPT SWAP BUTTON 
	if (isset($_POST['acceptSwap'])) {
		try{
			$sessionID = $_POST['swapsSelect'];
		  	$updateAllocation = $conn->prepare("UPDATE Allocations SET Account_ID = '$userID' WHERE Session_ID = '$sessionID';");
		 	$updateAllocation->execute();
		 	$accountQ = "SELECT * FROM Pending_Swaps WHERE Session_ID = '$sessionID';";
		 	foreach ($conn->query($accountQ)as $row){
		 		$accountID = $row['Request_ID'];
		 		$swapeeID = $row['Account_ID'];
		 	}
		 	if ($labInfoArray[$sessionID]  != null){
		 		$noteInfo = $labInfoArray[$sessionID];
		 	}else {
		 		$noteInfo = null;
		 	}
		  	$deleteSwap = $conn->prepare("DELETE FROM Pending_Swaps WHERE Session_ID= '$sessionID';");
		  	$deleteSwap->execute();
		  	$student = "SELECT * FROM PHD_Students WHERE Account_ID = '$swapeeID'";
		  	$studentr = $conn->query($student);
		  	$studentR = $studentr->fetch();
		  	$forename = $studentR['Forename'];
		  	//$initial = $studentR['Initial'];
		  	$surname = $studentR['Surname'];
		  	$name = $forename." ".$surname;
		  	$message = "Your swap request to {$name} for the {$noteInfo} session was accepted; please check your timetable to confirm";
		  	$insertNotification = $conn->prepare("INSERT INTO Notifications (Account_ID, Message) VALUES ('$accountID', '$message');");
		  	$insertNotification->execute();
		  	$moduleLeaderQ = "SELECT * FROM Lab_Requirements lr INNER JOIN Lab_Timetable lt ON lt.Lab_ID = lr.Lab_ID WHERE Session_ID = '$sessionID';";
		  	foreach ($conn->query($moduleLeaderQ)as $row){
		  		$accountID = $row['Account_ID'];
		  	}
		  	$message = "{$name} will now be supporting your class at {$noteInfo}";
		  	$insertNotification = $conn->prepare("INSERT INTO Notifications (Account_ID, Message) VALUES ('$accountID', '$message');");
		  	$insertNotification->execute();
		  	?>
			<script type="text/javascript">
			confirm("Swap Accepted");
			</script>
			<?php 
			
		}catch (PDOException $e){
 			echo "Error: ".$e;
		}	
	}
?>

<?php include 'template-bottom.php'; ?>


<?php //$insertNotificationOld = $conn->prepare("INSERT INTO Notifications (Account_ID, Notification_ID, Session_ID, Lab_Info) VALUES ('$accountID', '$noteType', '$sessionID', '$noteInfo');");?>