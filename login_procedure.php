<?php

	session_start(); // Starting Session
	
	include 'password_functions.php';

	if (isset($_POST['submit'])) {

		if (empty($_POST['username']) || empty($_POST['password'])) {

			///echo "Please enter both username and password"; 
			print '<script type="text/javascript">';
			print 'alert("Please enter both username and password!")';
			print '</script>'; 


		}

		else

		{

			// Define $username and $password

			$username=$_POST['username'];

			$password=$_POST['password'];

			$correctLogin = loginFunc($username, $password);

			if ($correctLogin == true){
				header("location: index.php?status=success"); // Redirecting To Main page(index)
			}
			else {
				header("location: login_page.php?status=fail"); // Redirecting To Login page with failure status
			}

		}

	}
	
	function loginFunc($username, $password){
	
			
			
			
			// Establishing Connection with Server by passing server_name, user_id and password as a parameter

			$connection = mysql_connect("planetmeerkat.co.uk", "planetme_lab1ent", "gr4BFoxcan13");

			

			// To protect MySQL injection for Security purpose (Security)

			$username = stripslashes($username);

			$password = stripslashes($password);

			$username = mysql_real_escape_string($username);

			$password = mysql_real_escape_string($password);

			

			

			

			// Selecting Database

			$db = mysql_select_db("planetme_lab-support", $connection);

			

			

			// To find record which contain entered username 

			$query = mysql_query("SELECT * FROM `Account_Data` WHERE `Username`='$username'", $connection);
			
			$rows = mysql_num_rows($query);
			
			//runs if the username exists in the database
			if ($rows == 1) {
				$record = mysql_fetch_assoc($query);
				
				//gets hashed password stored in the DB
				$hashedPass = $record["Password"];
				
				//validates password by running function in password_functions file and keeps record of the result
				$validPass = validatePass($password, $hashedPass);

				

				

				//if password was valid, gets user details from database and Defines session variables
				if($validPass == true) {

					$curr_id = $record["Account_ID"];
					$curr_username = $record["Username"];
					$curr_account_type = $record["Account_Type"];

					$_SESSION['curr_user_id']=$curr_id; // Define Account ID 
					$_SESSION['curr_username']=$curr_username; // Define Username 
					$_SESSION['curr_account_type']=$curr_account_type; // Define Account Type 

					
					$correctLogin = true;
				}
				else {
					
					$correctLogin = false;
				}
				

			} 
			else {

				
				$correctLogin = false;
			}



			mysql_close($connection); // Closing Connection
			
			return $correctLogin;
	
	
	}

?>

