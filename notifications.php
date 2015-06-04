<?php
	$message=$_GET["message"];
	echo "$message";
	$header = 'Notifications';
	include 'template-top.php';
	include 'validate_login.php';
	session_start();
	
	$servername = "planetmeerkat.co.uk";		//set up DB connection variable
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	$acc_type = $_SESSION['curr_account_type'];

	$slots = array(0,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26,27,28,29,31,32,33,34,35,36,37,38,39,41,42,43,44,45,46,47,48,49,51,52,53,54,55,56,57,58,59);
	$days = array("", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$times = array("", "0900-0950", "1000-1050", "1110-1200", "1210-1300", "1310-1400", "1410-1500", "1510-1600", "1610-1700", "1710-1800");
	
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);		//connect to DB
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$userID = $_SESSION['curr_user_id']; //get account id
	if (isset($_POST['deleteNotifications'])) {
		$userID = $_SESSION['curr_user_id']; //get account id
		$deleteNotificationsQ = $conn->prepare("DELETE FROM Notifications WHERE Account_ID = '$userID';");
		$deleteNotificationsQ->execute();
	?>
		<script type="text/javascript">
			confirm("All temporary notifications have been deleted");
		</script>
	<?php 
			 		
			 	}
	try{
	?>
		<div id = "Notifications Table">
			<form action="" method="post">
				<style type="text/css">
				.tg  {border-collapse:collapse;border-spacing:0;border-color:#aaa;}
				.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#aaa;color:#333;background-color:#fff;}
				.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#aaa;color:#fff;background-color:#f38630;}
				.tg .tg-z2zr{background-color:#FCFBE3}
				</style>
				<table class="tg">
	<?php 
		//INSERT NOTIFICATIONS FROM DATABASE
			$notificationsQ = "SELECT * FROM Notifications WHERE Account_ID = '$userID';";
			foreach ($conn->query($notificationsQ) as $row) {
			?>
				<tr> 
				<td class="tg-031e"><?php echo $row['Message']?></td>
				</tr> 
			<?php 
	}
	
		//FOR UNDERSUBSCRIBED SESSION
			if ($acc_type != 1){ //if not student
				$noAllocations = "SELECT * FROM Lab_Timetable WHERE Session_ID NOT IN (SELECT Session_ID FROM Allocations)";
				//FOR 0 ALLOCATED STUDENTS
				foreach ($conn->query($noAllocations) as $row2) {
					$sessionID = $row2['Session_ID'];
					$notificationsQ = "SELECT * FROM Lab_Timetable WHERE Session_ID = '$sessionID';";
					foreach ($conn->query($notificationsQ) as $row) {
						$sessionCount = $row['num'];
						$slotID = $row['Slot_ID'];
						$weeklySlot = $slotID%45;							//get slot from session ID and link to week/time via labtimetable
						$week = floor($slotID/45) + 1;
						$day = $days[floor($slots[$weeklySlot]/ 10)];
						$time = $times[$slots[$weeklySlot] % 10];
						$labInfo = "Week ".$week." ".$day." ".$time;
						$message = "The session at {$labInfo}  , ID: {$sessionID} currently has no allocated students";
				?>
					<tr> 
						<td class="tg-031e"><?php echo $message ?></td>
					</tr> 
				<?php
					$unallocated = false; 	
					}
				}
				//COLLECT ALL ALLOCATED SESSIONS WITH LAB INFO AND COUNT NUMBER OF TIMES ALLOCATED
				$timetableQ = "SELECT *, count(1) as num, No_Staff - count(1) as needs FROM Lab_Timetable lt INNER JOIN Allocations a INNER JOIN Lab_Requirements lr ON a.Session_ID = lt.Session_ID AND lr.Lab_ID = lt.Lab_ID GROUP BY a.Session_ID ORDER BY `lt`.`Session_ID`;";
				$unallocated = true;
				foreach ($conn->query($timetableQ) as $row2) {
					$sessionID = $row2['Session_ID'];
					$needs = $row2['needs'];
					//IF SESSION HAS LESS ALLOCATIONS THAN NEEDED BUT GREATER THAN 1
					if ($needs > 0){
						$notificationsQ = "SELECT * FROM Lab_Timetable WHERE Session_ID = '$sessionID';";
						foreach ($conn->query($notificationsQ) as $row) {
							$slotID = $row['Slot_ID'];
							$weeklySlot = $slotID%45;							//get slot from session ID and link to week/time via labtimetable
							$week = floor($slotID/45) + 1;
							$day = $days[floor($slots[$weeklySlot]/ 10)];
							$time = $times[$slots[$weeklySlot] % 10];
							$labInfo = "Week ".$week." ".$day." ".$time;
							$message = "The session at {$labInfo}, ID: {$sessionID} does not have enough allocated students, allocate {$needs} more";
						}
			
					?>
						<tr> 
							<td class="tg-031e"><?php echo $message; ?></td>
						</tr> 
					<?php 
						$unallocated = false;
					}
				}
					if ($unallocated == true){
					?>
						<tr> 
						<td class="tg-031e"><?php echo "All sessions have the required allocated PhD Students" ?></td>
						</tr> 
					<?php 
					}
			}	
?>
</table>
<br><br>
<input name="deleteNotifications" type="submit" value="Delete All">
</form>
</div>


<?php 
	}catch (PDOException $e){
			echo "Error: ".$e;
		 }
		 
include 'template-bottom.php';
?>






<!-- switch ($row['Notification_ID']){ -->
<!-- 				case 1: -->
<!-- 					?> -->
<!-- 					<tr> -->
					<td class="tg-031e">Your swap for <?php $row['Lab_Info']?> was accepted please check your timetable to confirm</td>
<!-- 					</tr> -->
					<?php 
// 					break;
// 				case 2:
// 					?>
<!-- 					<tr> -->
					<td class="tg-031e">Your swap for <?php $row['Lab_Info']?>  was declined please check your timetable to confirm</td>
<!-- 					</tr> -->
					<?php 
// 					break;
// 				case 3:
// 					?>
<!-- 					<tr> -->
					<td class="tg-031e">Lab with Session_ID: <?php $row['Session_ID']?> does not have the required number of allocated students"</td>
<!-- 					</tr> -->
					<?php 
// 					break;
// 			}