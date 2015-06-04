<?php

	$header = 'PhD Student / Support Staff\'s Menu';

	include 'template-top.php';
	
	include 'validate_login.php';

	
	$acc_type = $_SESSION['curr_account_type']; // account type
	
	include 'check_account_type.php';

	
	if ($acc_type != 1 && $acc_type != 0) { // if not Lecturer nor Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a PhD Student / Support Staff, or contact the Administrator</span><br>";
		exit; // exit further script
	} else if ($acc_type == 0) { // if not Lecturer nor Admin
		echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as a PhD Student / Support Staff</span><br>";
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
