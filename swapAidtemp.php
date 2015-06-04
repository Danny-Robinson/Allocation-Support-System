<?php
//   	error_reporting(E_ALL);
//   	ini_set('display_errors', '1');	
	$message=$_GET["message"];
	echo "$message";
	$header = 'Shift Swap Tool';
	include 'template-top.php';
	include 'validate_login.php';
?>
  
<?php session_start();?>


	<?php
		$servername = "planetmeerkat.co.uk";		//set up DB connection variable
		$username = "planetme_lab1ent";
		$password = "gr4BFoxcan13";
		$dbname = "planetme_lab-support";
		
		$slots = array(0,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26,27,28,29,31,32,33,34,35,36,37,38,39,41,42,43,44,45,46,47,48,49,51,52,53,54,55,56,57,58,59);
		$names = array();
		$ids = array();
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$userID = $_SESSION['curr_user_id']; //get account id
		$username = $_SESSION['curr_username']; //get username
		$accountType = $_SESSION['curr_account_type']; //get logged in user type 
		
		$sessionID = $_GET['session'];
		$shift = $_GET['shift'];
		$info = $_GET['info'];
		
		if ($accountType != 1) { // if not a student
			echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a Student</span><br>";
			exit; // exit further script
		}

		try {
							//////////////AVAILABILITY CONSTRAINT//////////////
			//echo $info;
			$slotsNum = count($slots);
			$availability = "SELECT * FROM PHD_Availability";
			foreach ($conn->query($availability) as $row) {
				$slotsArray = unserialize($row["Slots_Availability"]);
				$id = $row["Account_ID"];
				if ($id != $userID){								//if not searching current student
					foreach ($slotsArray as $key => $value) {		//iterate through array of available slots
						for($x = 1; $x < $slotsNum; $x++) {			//for 1 to end of coords array	
							if ($slots[$x] == $info){				//if slot in array = session slot
								$slot = "Slt".$x;					//convert Slt format to slot num
								if ($value == 1){					//if slot available
									if ($key == $slot){				//and available slot equal to required slot
										//ADD CONSTRAINTS BEFORE COLLECTING NAMES AND IDS
										$requirements = "SELECT * FROM Allocations a INNER JOIN Lab_Requirements lr INNER JOIN Lab_Timetable lt ON lt.Lab_ID = lr.Lab_ID AND lt.Session_ID = a.Session_ID WHERE a.Session_ID = '$sessionID'";
										$skills = "SELECT * FROM PHD_Students WHERE Account_ID = '$id'";
										$rSkills[] = array();
										$sSkills[] = array();
										foreach ($conn->query($requirements) as $row) {			//read in required skills
											$rSkills[] = $row['Skill_Python'];              	
											$rSkills[] = $row['Skill_Assembly'];
											$rSkills[] = $row['Skill_Problem_Solving'];
											$rSkills[] = $row['Skill_HTML_CSS'];
											$rSkills[] = $row['Skill_PHP_SQL'];
											$rSkills[] = $row['Skill_Discrete_Maths'];
											$rSkills[] = $row['Skill_Professional_Skills'];
											$rSkills[] = $row['Skill_HCI'];
											$rSkills[] = $row['Skill_DBM_Oracle'];
											$rSkills[] = $row['Skill_Systems_Thinking_SSM'];
											$rSkills[] = $row['Skill_Java'];
											$rSkills[] = $row['Skill_Data_Structures'];
											$rSkills[] = $row['Skill_Algorithms'];
											$rSkills[] = $row['Skill_Graphics'];
											$rSkills[] = $row['Skill_C_Cpp'];
											$rSkills[] = $row['Skill_Matlab'];
										}
											
										foreach ($conn->query($skills) as $row) {				//read in requested student's skills
											$sSkills[] = $row['Skill_Python'];
											$sSkills[] = $row['Skill_Assembly'];
											$sSkills[] = $row['Skill_Problem_Solving'];
											$sSkills[] = $row['Skill_HTML_CSS'];
											$sSkills[] = $row['Skill_PHP_SQL'];
											$sSkills[] = $row['Skill_Discrete_Maths'];
											$sSkills[] = $row['Skill_Professional_Skills'];
											$sSkills[] = $row['Skill_HCI'];
											$sSkills[] = $row['Skill_DBM_Oracle'];
											$sSkills[] = $row['Skill_Systems_Thinking_SSM'];
											$sSkills[] = $row['Skill_Java'];
											$sSkills[] = $row['Skill_Data_Structures'];
											$sSkills[] = $row['Skill_Algorithms'];
											$sSkills[] = $row['Skill_Graphics'];
											$sSkills[] = $row['Skill_C_Cpp'];
											$sSkills[] = $row['Skill_Matlab'];
										}
										
										//////////////SKILL MATCH CONSTAINT//////////////
										$skillsNum = count($sSkills);
										$viable = true;
										for ($i = 0; $i < $skillsNum; $i++ ){
											if ($rSkills[$i] > $sSkills[$i]){
												$viable = false;
											}
										}
										
										
										//////////////POPULATE VIABLE STUDENTS ARRAY//////////////
										if ($viable == true){
											$student = "SELECT * FROM PHD_Students WHERE Account_ID = '$id'";
											$studentr = $conn->query($student);
											$studentR = $studentr->fetch();
											$forename = $studentR['Forename'];
											$initial = $studentR['Initial'];
											$surname = $studentR['Surname'];
											$name = $forename." ".$initial." ".$surname;
											$names[] = $name;								//add name and id to names and ids arrays
											$ids[] = $id;
										}	
									}
								}
							}
						}
					}
				}	
			}
		}catch (PDOException $e){
			echo "Error: ".$e;
		 }
		$conn = null;
		// echo '</p></span><a href="RELEVANT.php?shift='.$_GET['shift'] redirect home
	?>
	
 <div id = "Swap Choice">
 <form action="" method="post">
 	<h2>Swap Options</h2>
 	
 	<?php
		$namesNum = count($names);
	?>
	<select name="namesSelect">
	<?php
		for ($i=0;$i<$namesNum;$i++){
	?>
			<option value="<?=$names[$i];?>"><?=$names[$i];?></option>
	<?php
		}
	?>
	</select>	
	<input name="submitSwap" type="submit" value="Request">
	</form>
 </div>
  
  <?php 
  $servername = "planetmeerkat.co.uk";		//set up DB connection variable
  $username = "planetme_lab1ent";
  $password = "gr4BFoxcan13";
  $dbname = "planetme_lab-support";
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $namesNum = count($names);
  $sessionID = $_GET['session'];
  $shift = $_GET['shift'];
  $info = $_GET['info'];
  $userID = $_SESSION['curr_user_id']; //get account id
  
  
  //////////////ON SUBMIT PRESS//////////////
  	if (isset($_POST['submitSwap'])) {
  		$name = $_POST['namesSelect'];
  		for($x = 0; $x < $namesNum; $x++) {
  			if ($names[$x] == $name){
  				$id = $ids[$x];
	  			$viable = true;
	  			$swapsQ = "SELECT * FROM Pending_Swaps"; 
		  		foreach ($conn->query($swapsQ) as $row) { 
		  			//IF NOT SWAP NOT ALREADY REQUESTED
		  			if ($row['Session_ID'] == $sessionID && $row['Account_ID'] == $id){
		  				$viable = false;
		  				?>
		  				<script type="text/javascript">confirm("That student has already been requested a swap for that session");</script>
		  				<?php
		  			}
		  		}
		  		//////////////ADD TO PENDING SWAPS TABLE//////////////
		  		if ($viable == true){
		  			$insertSwap = $conn->prepare("INSERT INTO Pending_Swaps (Session_ID, Account_ID, Request_ID) VALUES ('$sessionID','$id', '$userID');");
		  			$insertSwap->execute();
		  			?>
		  			<script type="text/javascript">confirm("Swap Requested");</script>
		  			<?php 
		  		}
  			//mysql_close($conn);
//   			$sql = "INSERT INTO Pending_Swaps (Session_ID,Account_ID) VALUES (:sID,:userID)";
//   			$q = $conn->prepare($sql);
//   			$q->execute(array(':sID'=>$sessionID,':userID'=>$userID));
  	
  	
  			}
  		}
	}
	{
		//header("Location: http://lab-support.co.uk/swapAid.php");
	}
	?>
 
<?php include 'template-bottom.php'; ?>



                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               