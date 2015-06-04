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
		
		if ($accountType != 1) { // if not a student
			echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a Student</span><br>";
			exit; // exit further script
		}
		
		try{
			$swapsQ = "SELECT * FROM Pending_Swaps";		//fill session array with session IDs of swaps for logged in student
			foreach ($conn->query($swapsQ) as $row) {
				$requests[] = $row['Request_ID'];
				if ($userID == $row['Account_ID']){
					$sessions[] = $row['Session_ID'];
					//$requests[] = $row['Request_ID'];
				}
			}
		}catch (PDOException $e){
			echo "Error: ".$e;
		}
?>

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
					$day = $days[floor($slots[$weeklySlot]/ 10)];				// - $slots[$weeklySlot] % 10)  or use floor()
					$time = $times[$slots[$weeklySlot] % 10];
					$labInfo = "Week ".$week." ".$day." ".$time;
					//echo $slotID, ", ", $weeklySlot, ", ", $day, ", ", $days[$slots[$weeklySlot]], ", ", $time, ", ", $times[$slots[$weeklySlot]];
					
	?>
			<option value="<?=$sessions[i];?>"><?=$labInfo;?></option>
	<?php
			
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
			<option value="<?=$sessions[i];?>"><?=$labInfo;?></option>
	<?php
			
				}
			}
		}catch (PDOException $e){
			echo "Error: ".$e;
		}
	?>
	</select>
	<input name="cancelSwap" type="submit" value="Cancel">
	</form>
 </div>

 <?php 
 	
	  	//$namesNum = count($names);											//CHANGE ALLOCATION AND DELETE FROM PENDING SWAPS
	  	//echo $userID, $sessionID;
	  	//$deleteSwaps = "DELETE * FROM Pending_Swaps WHERE Session_ID=?";
	  	//$sql = "UPDATE Allocations SET Account_ID=?, WHERE Session_ID=?";
	    //$swapsQ = "SELECT * FROM Pending_Swaps";
	    //echo $sessionID;
	   // $sessionID = $_POST['swapsSelect'];
	if (isset($_POST['cancelSwap'])) {
		//$session_ID = $_POST['swapsOSelect'];
		$cancelSwap = $conn->prepare("DELETE FROM Pending_Swaps WHERE Request_ID= '$userID' AND Session_ID= '$sessionID';");
		$cancelSwap->execute();
		?>
		<script type="text/javascript">confirm("Swap Cancelled");</script>
		<?php 
	}
 	if (isset($_POST['declineSwap'])) {
 		$deleteSwap = $conn->prepare("DELETE FROM Pending_Swaps WHERE Session_ID= '$sessionID';");
 		$deleteSwap->execute();
 		?>
		<script type="text/javascript">confirm("Swap Declined");</script>
		<?php 
 	}
	if (isset($_POST['acceptSwap'])) {
		try{
	  		//foreach ($conn->query($swapsQ) as $row) {
// 		  	$sessionID = $_POST['swapsSelect'];
// 		  	$userID = $_SESSION['curr_user_id'];
			//echo $sessionID;
		  	$updateAllocation = $conn->prepare("UPDATE Allocations SET Account_ID = '$userID' WHERE Session_ID = '$sessionID';");
		 	$updateAllocation->execute();
		  	$deleteSwap = $conn->prepare("DELETE FROM Pending_Swaps WHERE Session_ID= '$sessionID';");
		  	$deleteSwap->execute();
		  	?>
			<script type="text/javascript">confirm("Swap Accepted");</script>
			<?php 
		  	//mysql_close($connection);
// 		  		$q = $conn->prepare($sql);
// 		  		$q->execute(array($userID,$sessionID));
// 		  		$q2 = $conn->prepare($deleteSwaps);
// 		  		$q2->execute(array($sessionID));
	  		//}
		}catch (PDOException $e){
 			echo "Error: ".$e;
		}	
	}
?>

<?php include 'template-bottom.php'; ?>