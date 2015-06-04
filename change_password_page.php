
<?php
	session_start(); // Starting Session
	
	$header = 'Change Password';
	include 'template-top.php';
	include 'validate_login.php';
	
	
	
	
	include('change_password_procedure.php'); // Includes Change Password Script

?>

	<div id="login">

		Please enter your current password and new password below:
		<br>
		<br>
		<form action="" method="post">

			<label>Current Password* :</label>
			
			<input id="password" name="currentPassword" placeholder="" type="password">
			<br><br>
			
			
			<label>New Password* :</label>

			<input id="password" name="newPassword1" placeholder="" type="password">
			<br><br>
			
			<label>Confirm Password* :</label>

			<input id="password" name="newPassword2" placeholder="" type="password">
			<br><br>
			
			<input name="submit" type="submit" value=" Submit ">

			<span><?php echo $error; ?></span>

		</form>

	</div>




<?php

	
	if (isset($_GET["status"])){
		if (strcmp($_GET["status"], "success")==0){
			print '<script type="text/javascript">';
			print 'alert("Password successfully changed.")';
			print '</script>';

		}
		elseif (strcmp($_GET["status"], "fail")==0){
			print '<script type="text/javascript">';
			print 'alert("Password change failure! Please make sure that you have entered valid current password.")';
			print '</script>';

		}
		
	}


	include 'template-bottom.php';


?>