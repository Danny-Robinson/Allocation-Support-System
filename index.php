<?php

	

	$header = 'Cardiff University CS Teaching Support Allocation';

	include 'template-top.php';

	

	session_start();

	
	

	//If user is not logged in, redirect user to login page

	if (!isset($_SESSION) || !isset($_SESSION['curr_user_id']) || !isset($_SESSION['curr_username']) || !isset($_SESSION['curr_account_type'])) {

        header( 'Location: login_page.php' );

    }

   

	

   

   //Otherwise list logged in user details

	else{

	

		//checks its returning from login_procedure

		if (isset($_GET["status"])){

			

			echo "Successfully logged in!";

			echo "<br>";

			echo "<br>";

		}

		

		echo "Session ID: ". session_id();

		echo "<br>";

		echo "Current User ID: ". $_SESSION['curr_user_id'];

		echo "<br>";

		echo "Current Username: ". $_SESSION['curr_username'];

		echo "<br>";

		echo "Current Account Type: ". $_SESSION['curr_account_type'];



		/********************** REDIRECTIONS BASED ON ACCOUN TYPE **********************/
		
		if ($_SESSION['curr_account_type'] == 0) { // if Admin staff is logged in
			header("Location: manage_staff.php"); // redirect to Admin menu
			exit; // exit further script
		}

		if ($_SESSION['curr_account_type'] == 1) { // if PHD Student is logged in
			header("Location: phd_student_view.php"); // redirect to PhD Student menu
// redirect to PHD Student menu
			exit; // exit further script
		}

		if ($_SESSION['curr_account_type'] == 2) { // if Module Leader is logged in
			header("Location: module_leader_view.php"); // redirect to Module Leader menu
			exit; // exit further script
		}
		
		if ($_SESSION['curr_account_type'] == 3) { // if Technical Support Staff is logged in
			header("Location: tech_view.php"); // redirect to Technical Support Staff menu
			exit; // exit further script
		}
		
		if ($_SESSION['curr_account_type'] == 4) { // if Supervisor is logged in
			header("Location: supervisor_view.php"); // redirect to Supervisor menu
			exit; // exit further script
		}
		

		

		

	}

	

	include 'template-bottom.php';

?>

	