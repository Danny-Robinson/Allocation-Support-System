<?php
	$header =  'Create a Semester';
	include 'template-top.php';
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	
	
	

	if ($_SERVER["REQUEST_METHOD"] == "POST") { // on submit
		if (empty($_POST["startyear"])) { // validation
			echo "Please enter the start date!<br><br>";
		}
		else if (empty($_POST["semlength"])) { // validation
			echo "Please enter the semester length!<br><br>";
		}
		else if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$_POST["startyear"])) { // validation
			echo "Invalid date!<br><br>";
		}
		else if (intval($_POST["semlength"])>60|intval($_POST["semlength"])==0) { // validation
			echo "Invalid length!<br><br>";
		}
		else {	// validation passed
			try {
				$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
				$conn->exec("DELETE FROM Time_Slots"); // empty Time_Slots table
				$conn->exec("DELETE FROM Week_Date"); // empty Week_Date table
				$conn->exec("DELETE FROM Lab_Requirements"); // empty Lab_Requirements table
				echo 'Previous semester deleted<br><br>';
				$weeks = intval($_POST["semlength"]); // read the length of the semester
				$insweek = 1;
				$ID = 1;
				$weekID = 1;
				// start nested loops to insert records into database
				while ($weeks>0) { // loops through the weeks of the semester
					$days = 1;
					while ($days<6) { // loops through the days in each week
						$times = 1;
							while ($times<10) { // loops through the slots in each day
								$conn->exec("INSERT INTO Time_Slots (Slot_ID, Week, Day, Time) 
											 VALUES (" . $ID . "," . $insweek . "," . $days . "," . $times . ")"); // insert the current slot to Time_Slots table
								$times++;
								$ID++;
							}
						$days++;
					}
					$insweek--;
					$date = strtotime($_POST["startyear"] . " + " . $insweek . " week"); // calculate time-stamp for current week
					$conn->exec("INSERT INTO Week_Date (Week_Com, Slot_ID)
								 VALUES (" . $date . "," . $weekID . ")"); // insert the time-stamp and first Slot_ID for the current week to Week_Date table
					$insweek++;
					$insweek++;
					$weeks--;
					$weekID = $ID;
					} // insert operations completed
				echo 'New semester created<br><br>';
			}
		
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			$conn = null;
		}
	}
	//echo $last_id;
	
 ?>
ATTENTION! This form is for creating a new semester. Submitting this form will replace the existing semester dates and remove all existing timetable data.
<br><br><br>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<span class="popup-trigger"><label>Start date: </label><input type="text" name="startyear" placeholder="(YYYY-MM-DD)"/><span class="popup"><p>Enter the start date of the semester. This must be the Monday of the first week. (YYYY-MM-DD)</p></span></span><br><br>
	<span class="popup-trigger"><label>Semester length:</label><input type="text" name="semlength" placeholder="No. of weeks"/><span class="popup"><p>The number of weeks in the semester.</p></span></span><br><br>
	<input type="submit" value="Submit"/>
</form>

<?php
	include 'template-bottom.php';
?>