<?php 



	/* CONFIRMATION TO PROCEED TO CREATE A NEW SEMESTER'S TIMETABLE */



	$header = 'Add a Timetable file and create a New Semester\'s Timetable'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	


	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 



?>	
	<br><form name="change_timetable" method="post" action="<?php echo htmlspecialchars("populate_lab_requirements.php"); ?>">
    
<?php
	echo "<span class = \"error\"><label>Are you sure you would like to proceed?<br>";
	echo "<br>THIS PROCEDURE WILL COMPLETELY WIPE THE PREVIOUS SEMESTER'S TIMETABLE!</label></span>";
	echo "<br><br><input type=\"submit\" name=\"btn_yes\" value=\"Yes\" title=\"Yes\">";
	echo "<input type=\"reset\" name=\"btn_no\" value=\"No\" title=\"No\" onClick=\"location.href='manage_staff.php'\">";
	echo "</form><br>";


	echo "<br><input type=\"button\" name=\"back\" value=\"Back to manage all accounts\" title=\"Back to manage all accounts\" onClick=\"location.href='manage_staff.php'\">";

	include 'template-bottom.php';

?>