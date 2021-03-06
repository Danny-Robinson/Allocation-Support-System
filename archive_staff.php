<?php
	
	
	/* ARCHIVE PHD STUDENTS/SUPPORT STAFF */
	


	$header = 'Archive Staff'; /* use this variable to set the header string */

	include 'template-top.php';
	
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	

	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 


	echo "You are about to Archive <strong>PhD Student</strong> or <strong>Support Staff</strong>'s data<br>";

	$staff_ID = htmlspecialchars($_GET['student_ID']);
	$forename = htmlspecialchars($_GET['forename']);
	$surname = htmlspecialchars($_GET['surname']);
	
	if ($staff_ID != "") {

?>	

		<br><form name="archive_phd" method="post" action="<?php echo htmlspecialchars("staff_archived.php?staff_ID=$staff_ID&forename=$forename&surname=$surname"); ?>">
    
<?php
		echo "<span class = \"error\"><label>Are you sure you would like to archive $forename $surname?<br>";
		echo "<br>The person WILL NOT BE AVAILABLE for scheduling.</label></span>";
		echo "<br><br><input type=\"submit\" name=\"btn_yes\" value=\"Yes\" title=\"Yes\">";
		echo "<input type=\"reset\" name=\"btn_no\" value=\"No\" title=\"No\" onClick=\"location.href='manage_staff.php'\">";
		echo "</form><br>";

	} else {
		
		echo "<br><span class = \"error\"> ERROR: No staff can be been archived (staff ID has not been specified)</span><br>";
	}


	
	echo "<br><input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>