<?php

	include 'validate_login.php';
	
	if (($_SESSION['curr_account_type'] != 0)&&($_SESSION['curr_account_type'] != 2)&&($_SESSION['curr_account_type'] != 4)) {
		header("Location: no_permission_for_page.php"); // redirect if incorrect account type.
		exit; // exit further script
	}	

?>


<div id='column_container'>
	<div id='left_column'>
		Modules:<br>

		<ul>
			<?php	
				
				try {  // query database to find list of modules.
					$findModules = "SELECT Module_Code FROM Modules ORDER BY Modules.Module_Code ASC";
					foreach ($conn->query($findModules) as $row) {
						echo '<li><a href="timetableMODview.php?module='.$row['Module_Code'].'">'.$row['Module_Code'].'</a></li>';
					}  	// append next module to list as a link.
				}
				catch(PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			
			?>
		</ul>
	</div>
	<div id='center_column'>
		PHD Students:<br>
		<ul>
			<?php
				try {  // query database to find list of students.
					$findStudents = "SELECT Forename, Surname, Account_ID FROM PHD_Students ORDER BY PHD_Students.Forename ASC";
					foreach ($conn->query($findStudents) as $row) {
						echo '<li><a href="timetableMODview.php?module=Student&ID='.$row['Account_ID'].'">'.$row['Forename'].' '.$row['Surname'].'</a></li>';
					}
				}
				catch(PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			?>
		</ul>	
	</div>
	<div id='right_column'>
		<?php
			if ($_GET['info']!=null){
				echo '<form class="NarrowForm" method="POST" action="timetableMODview.php?module='.htmlspecialchars($_GET['module']).'&shift='.htmlspecialchars($_GET['shift']).'&ID='.htmlspecialchars($_GET['ID']).'&info='.htmlspecialchars($_GET['info']).'">';
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					try {
						$headers = "From: do-not-reply@lab-support.co.uk";
						$allocatedStudents = "SELECT PHD_Students.Forename, PHD_Students.Surname, Account_Data.Email FROM PHD_Students
											  INNER JOIN Allocations ON Allocations.Account_ID = PHD_Students.Account_ID
											  INNER JOIN Account_Data ON Account_Data.Account_ID = PHD_Students.Account_ID
											  WHERE Allocations.Session_ID = " . intval($addresses[$_GET['info']][3]);
						foreach ($conn->query($allocatedStudents) as $row) {
							$subject = "Message about ".$addresses[$_GET['info']][1]." work.";
							mail($row['Email'], $subject, $_POST['message'], $headers);
						}
						echo 'Email(s) sent.<br><br>';
					}
					catch(PDOException $e) {
						echo "Error: " . $e->getMessage();
					}					
				}	
				echo $addresses[$_GET['info']][0] . '<br><br>';
				echo '<a href="cancel_lab.php?module='.htmlspecialchars($_GET['module']).'&shift='.htmlspecialchars($_GET['shift']).'&ID='.htmlspecialchars($_GET['ID']).'&info='.htmlspecialchars($_GET['info']).'&session='.htmlspecialchars($_GET['session']).'"><input type="button" value="cancel this session"/></a><br><br><br>';
				if ($allocated[$_GET['info']]==0) { // previously set to zero to indicate no allocations
					echo 'There are no PHD students allocated to this slot.';
				}
				else {
					echo 'There are/is ' . $allocated[$_GET['info']] . ' PHD student(s) allocated to this slot.<br><br>';
					echo 'Type in the box and press submit to send an email to the allocated PHD students:<br>';
					echo '<textarea name="message" rows=15 cols=47></textarea><br><br>';
					echo '<input type="submit" value="submit"/>';
				}
				echo '</form>';
			}
			else if ($_GET['module']!=null){
				echo '<form class="NarrowForm">Click on a scheduled event to see more options.</form>';
			}
			$conn = null;
		?>
	</div>
</div>