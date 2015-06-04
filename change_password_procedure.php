<?php

	session_start(); // Starting Session
	
	include 'password_functions.php';
	
	
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
			
			
	
	
	if (isset($_POST['submit'])) {

		//check if any of the fields are empty
		if (empty($_POST['currentPassword']) || empty($_POST['newPassword1']) || empty($_POST['newPassword2'])) {

			
			print '<script type="text/javascript">';
			print 'alert("Please make sure all fields are filled!")';
			print '</script>'; 


		}
	
		//check if new password in both fields match
		elseif(strcmp($_POST['newPassword1'], $_POST['newPassword2'])!=0){
			print '<script type="text/javascript">';
			print 'alert("Please make sure that new password in both fields match!")';
			print '</script>'; 
		
		}
		
		else
		{
		
			// Establishing Connection with Server by passing server_name, user_id and password as a parameter
			$connection = mysql_connect($servername, $username, $password);

			// Selecting Database
			$db = mysql_select_db($dbname, $connection);
			
			
			//current logged in user ID 
			$currAccID = intval($_SESSION['curr_user_id']);
			
			
			//getting the details of current user
			$query = mysql_query("SELECT * FROM `Account_Data` WHERE `Account_ID`='$currAccID'", $connection);
			$rows = mysql_num_rows($query);
			$record = mysql_fetch_assoc($query);

			//get current password hash from DB
			$currPassDB = $record["Password"];
			
			//current password entered
			$currPassEnt = $_POST['currentPassword'];
			
			//checking if passwords match
			$validPass = validatePass($currPassEnt, $currPassDB);
			
			// Closing Connection
			mysql_close($connection); 
				
				if($validPass == true) {
				
					try {
						//new password is hashed 
						$newPassword=$_POST['newPassword1'];
						$hashedNewPassword = genPassHash($newPassword);
					
						$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
						
						// set the PDO error mode to exception
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						
						//new hashed password is sent to the database
						$sql = "UPDATE `Account_Data` SET `Password`='$hashedNewPassword' WHERE `Account_ID`=$currAccID";

						// Prepare statement
						$stmt = $conn->prepare($sql);

						// execute the query
						$stmt->execute();

						// echo a message to say the UPDATE succeeded
						echo $stmt->rowCount() . "Password changed";
						}
					catch(PDOException $e)
					{
						echo $sql . "<br>" . $e->getMessage();
					}
				


					header("location: change_password_page.php?status=success"); // Redirecting To Main page
				}
				
				else {
					header("location: change_password_page.php?status=fail"); // Redirecting To Login page
				
				}
				
				
		} 
			

	}
	
?>

