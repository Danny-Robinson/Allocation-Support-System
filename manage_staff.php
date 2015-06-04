<?php
	
	
	/* MANAGE STAFF AND MODULES */
	


	$header = 'Manage Staff Accounts, Modules and Timetable'; /* use this variable to set the header string */

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	include 'check_account_type.php';
	
	if ($acc_type != 0) { // if not Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
		exit; // exit further script
	} 



	// Assuming the person who gets access to this page is logged in as Admin (account type $acc_type = 0)
	// From here, the Admin can create, edit and delete accounts in the Account_Data table and the other tables corresponding to the type of account:
	// $acc_type = 0 - Admin Staff	
	// $acc_type = 1 - PhD Students/Other supporting staff
	// $acc_type = 2 - Module Leaders (Lecturers)
	// $acc_type = 3 - Tech Staff
	// $acc_type = 4 - Supervisor

?>
	<br>
    <div id="left_column">
		<ul>
			<li><a href="register_phd.php" title="Register a PhD Student/Support staff"> Register a PhD Student/Support staff </a></li><br />
			<li><a href="register_admin.php" title="Register Admin staff"> Register Admin staff </a></li><br />
			<li><a href="register_lecturer.php" title="Register Module Leader"> Register Module Leader </a></li><br />
			<li><a href="register_supervisor.php" title="Register Supervisor"> Register Supervisor </a></li><br />
			<li><a href="register_tech.php" title="Register Technical staff"> Register Technical staff </a></li><br />
		</ul>
	</div>
    
    <div id="column_container">
   	 	<div id="center_column" style="border-right:dotted; border-color:orange;">
			<ul>
				<li><a href="manage_phd.php" title="Edit/Archive a PhD Student/Support staff"> Edit/Archive a PhD Student/Support staff </a></li><br />
                <li><a href="startScheduling.php" title="Allocate Students First Time"> Allocate Students First Time </a></li><br />            
                <li><a href="reallocate_labs.php" title="Reallocate Staff to Classes (if a PhD Student leaves)"> Reallocate Staff to Classes (if a PhD Student leaves) </a></li><br />
				<!-- <li><a href="manage_admin.php" title="Edit/Delete Admin staff"> Edit/Delete Admin staff </a></li><br />
				<li><a href="manage_lecturer.php" title="Edit/Delete Module Leader"> Edit/Delete Module Leader </a></li><br />
				<li><a href="manage_supervisor.php" title="Edit/Delete Supervisor"> Edit/Delete Supervisor </a></li><br />                
				<li><a href="manage_tech.php" title="Edit/Delete Technical staff"> Edit/Delete Technical staff </a></li><br /> -->
			</ul>
		</div>
        
   	 	<div id="right_column">
			<ul>
				<li><a href="register_module.php" title="Register Module"> Register Module </a></li><br />
				<!-- <li><a href="manage_module.php" title="Edit/Delete Module"> Edit/Delete Module </a></li><br />    --> 
                <li><a href="change_timetable.php" title="Upload a Timetable file and create a New Semester's Timetable"> Upload a Timetable file and create a New Semester's Timetable </a></li><br />
                <li><a href="slot-generation.php" title="Reset Semester"> Reset Semester </a></li><br />
			</ul>
 		</div>
	</div>

<?php

	include 'template-bottom.php';

?>
