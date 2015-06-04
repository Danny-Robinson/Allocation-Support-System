<?php

	$header = 'Cardiff University CS Teaching Support Allocation';
	include 'template-top.php';

	session_start(); // Starting Session
	
	//If user is already logged in, redirect to Index page
	if (isset($_SESSION) && isset($_SESSION['curr_user_id']) && isset($_SESSION['curr_username']) || isset($_SESSION['curr_account_type'])) {
        header( 'Location: index.php' );
	}
	
	
	include('login_procedure.php'); // Includes Login Script

?>


			

	<div id="login">

		<h3 style="text-align: center;">Please enter your login details below</h3>

		<form method="post">

			<label>Username :</label>

			<input id="name" name="username" placeholder="Enter Username" type="text">

			<label>Password :</label>

			<input id="password" name="password" placeholder="Enter Password" type="password">

			<input name="submit" type="submit" value=" Login ">

			<span><?php echo $error; ?></span>

		</form>

	</div>
<?php
	//checks its returning from login_procedure
	if (isset($_GET["status"])){
			
		///echo "Username or Password is invalid!";
		print '<script type="text/javascript">';
		print 'alert("Username or Password is invalid! Please try again.")';
		print '</script>';
	}
	
	include 'template-bottom.php';
?>