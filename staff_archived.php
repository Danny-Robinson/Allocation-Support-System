<?php
	
	
	/* STAFF ARCHIVED CONFIRMATION  */
	


	$header = 'Staff Archived'; /* use this variable to set the header string */

	include 'template-top.php';

	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 



	$staff_ID = $_GET['staff_ID'];
	$forename = $_GET['forename'];
	$surname = $_GET['surname'];

	if ($staff_ID != "") {
		
		try {
			//connect to DB
			require_once 'dbconfig.php';
	
			$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			// sql to update the record
			$query = $conn -> prepare("UPDATE PHD_Students 
									   SET Status='Cancelled' 
									   WHERE Account_ID='$staff_ID';"); 
			$query -> execute();
	
		}
		catch(PDOException $e){
			echo "Error: " . $e->getMessage();
			$conn = NULL; //close DB connection		
		}
		
		$conn = NULL;
		echo "<br><span class = \"info\"> $forename $surname has been successfully archived </span><br>";

	} else {
		
		echo "<br><span class = \"error\"> ERROR: No staff has been archived (staff ID has not been specified)</span><br>";
	}


	echo "<br><input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\"><br>";

	include 'template-bottom.php';

?>
