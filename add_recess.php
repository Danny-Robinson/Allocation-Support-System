<?php
	$header='Add Recess';
	include 'template-top.php';

	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	$findWeeks = "SELECT * FROM Week_Date";


	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$updateNo=0;
		$updateValue=0;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			
			foreach ($conn->query($findWeeks) as $row) {
				$updateNo++;
				if ($_POST['week'.$updateNo]==1){
					$updateValue=1;
				}
				else {
					$updateValue=0;
				}
				$updateSQL='UPDATE Week_Date SET Is_Recess = '.intval($updateValue).' WHERE Week_Com = '.intval($row['Week_Com']);
				$conn->query($updateSQL);
			}
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		$conn=null;
		echo 'RECESS SELECTIONS HAVE BEEN SAVED<br><br>';
	}
	
?>

You can use this page to specify which weeks of the semester fall within a recess.<br><br>

<form class="weekSelect" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	Please select all weeks that fall within a recess. If a recess currently exists it will already be selected. To remove an existing recess un-select it. When you are done press submit and the new selections will be saved.<br><br>
	<?php
		try {
			//Query database to find time existing weeks
			$weekNo=0;
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);				
			foreach ($conn->query($findWeeks) as $row) {
				$weekNo++;
				echo '<input type="checkbox" name="week'.$weekNo.'" id="box'.$weekNo.'" value="1" ';
				if ($row['Is_Recess']==1){
					echo 'checked="checked"';
				}
				echo "/>";
				echo '<label for="box'.$weekNo.'">Week commencing: '.date('l, d F Y',$row['Week_Com']).'</label><br><br>'."\n";
			}
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	?>
	<br>
	<input type="submit" value="Submit">
	<input type="checkbox" name="chknewsletter" id="chknewsletter"
      checked="checked"/>
</form>

<?php

	include 'template-bottom.php';
	$conn=null;
?>