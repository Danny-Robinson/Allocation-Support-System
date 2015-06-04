<?php

	session_start();

	$header='Your Technical Skills Preference';

	include 'template-top.php';
	include 'validate_login.php';


	$currID = intval($_SESSION['curr_user_id']);



	//database connection details
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";

	try {

		$conn = mysql_connect($servername, $username, $password);

		mysql_select_db($dbname, $conn);

		$result = mysql_query("SHOW COLUMNS FROM PHD_Students WHERE Field LIKE 'Skill%';", $conn);

		$num_rows = mysql_num_rows($result);


		$skillNameArray = array($num_rows);

		$counter = 0;

		while($row = mysql_fetch_array($result))

		{
			$skillNameArray[$counter] = $row['Field'];
			$counter++;
		}

		
		
		//array to store the selected value for each skill. Format = 0(index)->2(skill value)
		$skillLevelArray = array($num_rows);

		for ($i = 0; $i < $num_rows; $i++) {
			$sql = mysql_query("SELECT $skillNameArray[$i] FROM `PHD_Students` WHERE `Account_ID` = $currID", $conn);
			$row = mysql_fetch_array($sql);
			$skillLevelArray[$i] = $row[$skillNameArray[$i]];
		}



		$skillLevelJSONString = json_encode($skillLevelArray);
		$skillNameJSONString= json_encode($skillNameArray);

    }

	catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
    }

	$conn = null;	
?>



	<!----  Initial transfer of PHP Skill level array to JS array ---->

	<script language="JavaScript">

		var jsSkillValuesArray = <?php echo $skillLevelJSONString; ?>;

		var jsSkillNameArray = <?php echo $skillNameJSONString; ?>;

	</script>



	<!-- This function changes radio button section depending on retrieved data from DB when an item

	if selected from the combo box-->

	<script language="JavaScript">

		function selectChange() {

			selIdx = document.forms[1].skills.selectedIndex; //selected skill name from the combo list

			newSel = document.forms[1].skills.options[selIdx].id; 

			document.skillRadios.selSkill.value=jsSkillValuesArray[newSel];//representing stored skill val on radio button

			//enabling radio buttons and update button

			document.getElementById('UpdateButton').disabled=false; 

			document.getElementById('rad1').disabled=false;

			document.getElementById('rad2').disabled=false;

			document.getElementById('rad3').disabled=false;

		}

	</script>

	<form>
	
		<h4>To change preference for a skill, please select a skill, its preference and click Update.</h4>
	
	</form>

		

	<form>

		<h3>Skill:</h3>

		<select name="skills" onChange="selectChange()">

			<option value="" disabled selected>Select a skill</option>

			<?php

			//loads names of skills stored in the database

			for ($i = 0; $i < $num_rows; $i++) {

				$string = str_replace("Skill_", "", $skillNameArray[$i]);

				$string = str_replace("_", " ", $string);

				echo "<option id=$i>$string";

			}

			?>

		</select>

	</form>

		



		<!--Radio buttons to represent the a skill value -->

		<form name="skillRadios" onChange="UpdateJSSkillLevelArray()">
			<h3>Preference:</h3>

			<label for="rad3">Yes</label>

			<input id="rad3" type="radio" name="selSkill" value="2" disabled>

			<br>

			<label for="rad2">Maybe</label>

			<input id="rad2" type="radio" name="selSkill" value="1" disabled>

			<br>

			<label for="rad1">No</label>

			<input id="rad1" type="radio" name="selSkill" value="0" disabled>

		</form> 



	



<!-- Send Skill Name and its value to update process so data on DB can be updated  -->

<input id="UpdateButton" type="button" value="Update" onClick="UpdateSkillVal()" disabled>

<script>

	function UpdateSkillVal(){

		currSkillIndex = document.forms[1].skills.selectedIndex - 1;

		currSkillName = jsSkillNameArray[currSkillIndex];

		currSelSkillValue = document.skillRadios.selSkill.value;

		window.location.href = "update_phd_skills_procedure.php?skill_name=" + currSkillName + "&skill_val="+currSelSkillValue;

	}

</script>


<?php
	if (isset($_GET["status"])){
	
		if (strcmp($_GET["status"], "success")==0){
			print '<script type="text/javascript">';
			print 'alert("'.$_GET["skill_name"].' preference was successfully updated.")';
			print '</script>';

		}
		elseif (strcmp($_GET["status"], "fail")==0){
			print '<script type="text/javascript">';
			print 'alert("'.$_GET["skill_name"].' preference update failed.")';
			print '</script>';

		}
		
	}

	include 'template-bottom.php';

?>
