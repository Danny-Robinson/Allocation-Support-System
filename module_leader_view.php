<?php

	$header = 'Module Leader\'s Menu';

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	include 'check_account_type.php';

	
	if ($acc_type != 2) { // if not Lecturer
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a Module Leader</span><br>";
		exit; // exit further script
	}

	
?>
	<br>
	<ul>
		<li><a href="select_module_labs.php?message=" title="Request Class Support"> Request Class Support </a></li><br>
	</ul>
    <br>


<?php
    
	include 'template-bottom.php';

?>
