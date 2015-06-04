<?php

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	switch ($acc_type) {
		
		case 0:
			$login_type="Administrator";
			break;
		case 1:
			$login_type="PhD Student / Support Staff";
			break;
		case 2:
			$login_type="Module Leader";
			break;
		case 3:
			$login_type="Technical Support Staff";
			break;
		case 4:
			$login_type="Supervisor";
			break;
		default:
			$login_type="***Unknown Account Type***";

	}
	
	echo "You are logged in as $login_type.<br>";
	


?>