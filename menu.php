<?php
	session_start(); // Starting Session
?>

	<?php
		 try{
		 	//collect info for pending swaps notification
		 	$userID = $_SESSION['curr_user_id']; //get account id
		 	$connection = mysql_connect("planetmeerkat.co.uk", "planetme_lab1ent", "gr4BFoxcan13");
		 	$servername = "planetmeerkat.co.uk";		//set up DB connection variable
		 	$username = "planetme_lab1ent";
		 	$password = "gr4BFoxcan13";
		 	$dbname = "planetme_lab-support";
		 	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);		//connect to DB
		 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		 	$notificationQ = false;
		 	$notificationN = false;
			$swapsQ = "SELECT * FROM Pending_Swaps";	
			foreach ($conn->query($swapsQ) as $row) {
				if ($userID == $row['Account_ID']){ 
					$notificationQ = true;
				}
			}
			$notificationsQ = "SELECT * FROM Notifications";
			foreach ($conn->query($notificationsQ) as $row) {
				if ($userID == $row['Account_ID']){
					$notificationN = true;
				}
			}
		}catch (PDOException $e){
			echo "Error: ".$e;
		}?>
	
	<ul>

		<?php
		//only appears if logged in user is of type 0 i.e. Admin
		if (isset($_SESSION) && isset($_SESSION['curr_user_id']) && isset($_SESSION['curr_username']) && isset($_SESSION['curr_account_type']) && $_SESSION['curr_account_type'] == "0") {
			echo "<li><a href='budgetManagement.php'>Budget</a></li>";
			echo "<li><a href='timetableMODview.php'>MODtable</a></li>";
			echo "<li><a href='manage_staff.php'>Admin</a></li>";
			echo "<li><a href='add_recess.php'>Recess</a></li>";
			echo "<li><a href='select_module_labs.php'>View Labs</a></li>";
		}
		?>





		<?php
		//only appears if logged in user is of type 1 i.e. Student
		if (isset($_SESSION) && isset($_SESSION['curr_user_id']) && isset($_SESSION['curr_username']) && isset($_SESSION['curr_account_type']) && $_SESSION['curr_account_type'] == "1") {
			echo "<li><a href='phd_availability_page.php'>Availability</a></li>";
			echo "<li><a href='timetablePHDview.php'>PHDtable</a></li>";
			echo "<li><a href='phd_skills_pref_page.php'>Skills</a></li>";
			if ($notificationQ == true){
				echo "<li><a href='pendingSwaps.php' style=\"color:red\" >Swaps</a></li>";
			}
			else{
				echo "<li><a href='pendingSwaps.php'>Swaps</a></li>";
			}
			echo "<li><a href='phd_google_sync_page.php'><img src='google-calendar.gif' alt='Google Calendar'/></a></li>";
			echo "<li><a href='student_edit_phd.php'>Account</a></li>";
			

		}
		?>


		<?php
		//only appears if user is logged in
		if (isset($_SESSION) && isset($_SESSION['curr_user_id']) && isset($_SESSION['curr_username']) && isset($_SESSION['curr_account_type'])) {
			echo "<li><span style=\"font-size:7pt; margin-top:0px; padding-top:0px;\"><a href='change_password_page.php'>Change Password</a><span></li>";
			if ($notificationN == true){
				echo "<li><span style=\"font-size:7pt; margin-top:0px; padding-top:0px;\"><a href='notifications.php' style=\"color:red\" >Notifications</a></li>";
			}
			else{
				echo "<li><span style=\"font-size:7pt; margin-top:0px; padding-top:0px;\"><a href='notifications.php'>Notifications</a></li>";
			}
			echo "<li><a href='logout_procedure.php'>Log out</a></li>";
		}
		?>



	</ul>

