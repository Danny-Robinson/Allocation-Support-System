<?php

	$header = 'Supervisor\'s Menu';

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	include 'check_account_type.php';

	
	if ($acc_type != 4 && $acc_type != 0) { // if not Lecturer nor Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a Supervisor, or contact the Administrator</span><br>";
		exit; // exit further script
	} else if ($acc_type == 0) { // if not Lecturer nor Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a Supervisor</span><br>";
		exit; // exit further script
	}

	
?>

<!--------------------------------------------------
	<br>
	<ul>
		<li><a href=".php" title="">  </a></li><br>
	</ul>
    <br>
-->

<?php
    
	include 'template-bottom.php';

?>
