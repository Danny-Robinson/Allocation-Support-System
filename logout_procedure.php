<?php

	session_start();
	
	if (isset($_SESSION) && isset($_SESSION['curr_user_id']) && isset($_SESSION['curr_username']) && isset($_SESSION['curr_account_type'])) {
        // remove all session variables
		session_unset();

		// destroy the session
		session_destroy(); 
		
		header( 'Location: index.php' );
	}

?>