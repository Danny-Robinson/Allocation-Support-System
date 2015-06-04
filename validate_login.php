<?php
session_start(); // Starting Session
	
	//If user is not logged in, redirect user to login page
	if (!isset($_SESSION) || !isset($_SESSION['curr_user_id']) || !isset($_SESSION['curr_username']) || !isset($_SESSION['curr_account_type'])) {
        header( 'Location: login_page.php' );
    }
?>