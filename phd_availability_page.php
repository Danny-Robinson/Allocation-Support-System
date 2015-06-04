<?php

	session_start();
	
	$header='Your Available Slots';

	include 'template-top.php';
	
	include 'validate_login.php';
	
	//to store un-serialised slots array
	$SlotsArray = array();
	
	//serialised array
	$SerialisedSlotsArray; 
	
	//database connection details
	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";

	//current logged in user ID 
	$currAccID = intval($_SESSION['curr_user_id']);

	

	try {

		$conn = mysql_connect($servername, $username, $password);
		mysql_select_db($dbname, $conn);
		
		$result = mysql_query("SELECT * FROM `PHD_Availability` WHERE `Account_ID` = $currAccID", $conn);
		$num_rows = mysql_num_rows($result);

	
		
		//checking if a record for current user exists
		//If exists, get availability data
		if ($num_rows>0){
			$row = mysql_fetch_assoc($result);
			$SlotsArray = unserialize($row["Slots_Availability"]);
			$SlotsArrayJSONString = json_encode($SlotsArray);

		}
		//If doesn't exist, create an availability record for current user
		else {
			
			$SlotsArray = array("Slt1"=>1,"Slt2"=>1,"Slt3"=>1,"Slt4"=>1,"Slt5"=>1,"Slt6"=>1,"Slt7"=>1,"Slt8"=>1,"Slt9"=>1,
						"Slt10"=>1,"Slt11"=>1, "Slt12"=>1,"Slt13"=>1,"Slt14"=>1,"Slt15"=>1,"Slt16"=>1,"Slt17"=>1,"Slt18"=>1,
						"Slt19"=>1,"Slt20"=>1,"Slt21"=>1,"Slt22"=>1,"Slt23"=>1,"Slt24"=>1,"Slt25"=>1,"Slt26"=>1,"Slt27"=>1,
						"Slt28"=>1,"Slt29"=>1,"Slt30"=>1,"Slt31"=>1,"Slt32"=>1,"Slt33"=>1,"Slt34"=>1,"Slt35"=>1,"Slt36"=>1,
						"Slt37"=>1,"Slt38"=>1,"Slt39"=>1,"Slt40"=>1,"Slt41"=>1,"Slt42"=>1,"Slt43"=>1,"Slt44"=>1,"Slt45"=>1,
						);

			$SlotsArrayJSONString = json_encode($SlotsArray);
			
			$SerialisedSlotsArray = serialize($SlotsArray);
			
			$sql = "INSERT INTO PHD_Availability 
				    VALUES ('$currAccID', '$SerialisedSlotsArray')";
			
			mysql_query($sql, $conn);
	
		
		}

    }

	catch(PDOException $e) {

		echo "Error:" . $e->getMessage();

    }

	$conn = null;	


	//This function is called by each slot in the table to determine its initial colour
	function thisColour ($cID, $SlotsArray) {
		if ($SlotsArray[$cID] == 1) {
			echo 'class = "greenSlot"';
		}
		else{
			echo 'class = "redSlot"';
		}
	}

?>

<form>
<h4>Please click cells/slots in the timetable below to make changes.</h4>
</form>

<table id="data" class='TimeView'>

	<tr><th></th><th>0900-0950</th><th>1000-1050</th><th>1110-1200</th><th>1210-1300</th><th>1310-1400</th><th>1410-1500</th><th>1510-1600</th><th>1610-1700</th><th>1710-1800</th></tr>
	<tr>
		<th>Mon</th>
		<td <?php thisColour('Slt1', $SlotsArray); ?> id = Slt1></td> 	
		<td <?php thisColour('Slt2', $SlotsArray); ?> id = Slt2></td> 
		<td <?php thisColour('Slt3', $SlotsArray); ?> id = Slt3></td> 
		<td <?php thisColour('Slt4', $SlotsArray); ?> id = Slt4></td> 
		<td <?php thisColour('Slt5', $SlotsArray); ?> id = Slt5></td> 	
		<td <?php thisColour('Slt6', $SlotsArray); ?> id = Slt6></td>  
		<td <?php thisColour('Slt7', $SlotsArray); ?> id = Slt7></td> 
		<td <?php thisColour('Slt8', $SlotsArray); ?> id = Slt8></td> 
		<td <?php thisColour('Slt9', $SlotsArray); ?> id = Slt9></td> 
	</tr>
	<tr>
		<th>Tue</th>
		<td <?php thisColour('Slt10', $SlotsArray); ?> id = Slt10></td> 	
		<td <?php thisColour('Slt11', $SlotsArray); ?> id = Slt11></td> 
		<td <?php thisColour('Slt12', $SlotsArray); ?> id = Slt12></td> 
		<td <?php thisColour('Slt13', $SlotsArray); ?> id = Slt13></td> 
		<td <?php thisColour('Slt14', $SlotsArray); ?> id = Slt14></td> 	
		<td <?php thisColour('Slt15', $SlotsArray); ?> id = Slt15></td>  
		<td <?php thisColour('Slt16', $SlotsArray); ?> id = Slt16></td> 
		<td <?php thisColour('Slt17', $SlotsArray); ?> id = Slt17></td> 
		<td <?php thisColour('Slt18', $SlotsArray); ?> id = Slt18></td> 
	</tr>
	<tr>
		<th>Wed</th>
		<td <?php thisColour('Slt19', $SlotsArray); ?> id = Slt19></td> 	
		<td <?php thisColour('Slt20', $SlotsArray); ?> id = Slt20></td> 
		<td <?php thisColour('Slt21', $SlotsArray); ?> id = Slt21></td> 
		<td <?php thisColour('Slt22', $SlotsArray); ?> id = Slt22></td> 
		<td <?php thisColour('Slt23', $SlotsArray); ?> id = Slt23></td> 	
		<td <?php thisColour('Slt24', $SlotsArray); ?> id = Slt24></td>  
		<td <?php thisColour('Slt25', $SlotsArray); ?> id = Slt25></td> 
		<td <?php thisColour('Slt26', $SlotsArray); ?> id = Slt26></td> 
		<td <?php thisColour('Slt27', $SlotsArray); ?> id = Slt27></td> 
	</tr>
	<tr>
		<th>Thu</th>
		<td <?php thisColour('Slt28', $SlotsArray); ?> id = Slt28></td> 	
		<td <?php thisColour('Slt29', $SlotsArray); ?> id = Slt29></td> 
		<td <?php thisColour('Slt30', $SlotsArray); ?> id = Slt30></td> 
		<td <?php thisColour('Slt31', $SlotsArray); ?> id = Slt31></td> 
		<td <?php thisColour('Slt32', $SlotsArray); ?> id = Slt32></td> 	
		<td <?php thisColour('Slt33', $SlotsArray); ?> id = Slt33></td>  
		<td <?php thisColour('Slt34', $SlotsArray); ?> id = Slt34></td> 
		<td <?php thisColour('Slt35', $SlotsArray); ?> id = Slt35></td> 
		<td <?php thisColour('Slt36', $SlotsArray); ?> id = Slt36></td> 
	</tr>
	<tr>
		<th>Fri</th>
		<td <?php thisColour('Slt37', $SlotsArray); ?> id = Slt37></td> 	
		<td <?php thisColour('Slt38', $SlotsArray); ?> id = Slt38></td> 
		<td <?php thisColour('Slt39', $SlotsArray); ?> id = Slt39></td> 
		<td <?php thisColour('Slt40', $SlotsArray); ?> id = Slt40></td> 
		<td <?php thisColour('Slt41', $SlotsArray); ?> id = Slt41></td> 	
		<td <?php thisColour('Slt42', $SlotsArray); ?> id = Slt42></td>  
		<td <?php thisColour('Slt43', $SlotsArray); ?> id = Slt43></td> 
		<td <?php thisColour('Slt44', $SlotsArray); ?> id = Slt44></td> 
		<td <?php thisColour('Slt45', $SlotsArray); ?> id = Slt45></td> 
	</tr>
</table>		



<script type='text/javascript' src='http://code.jquery.com/jquery-2.0.2.js'></script>
<!-- Called when a cell/slot is clicked by user. Changes slot colour and updates slot value in JS array-->
<script>
$('#data tr td').click(function(e) {
	if($(this).hasClass("redSlot")){
		$(this).removeClass('redSlot');
		$(this).addClass('greenSlot');
		jsSlotsArray[this.id] = 1;
		document.getElementById('updateButton').disabled=false; 
	}else{
		$(this).addClass('redSlot');
		jsSlotsArray[this.id] = 0;
		document.getElementById('updateButton').disabled=false; 
	}
});
</script>


<br>

<!-- When clicked, this button will send slots array to db using update php file******TBC(((((-->
<input id="updateButton" type="button" value="Update" disabled="true" onClick="updateFunc()">


<script>
	function updateFunc() {
		
		//Changing JS array to string so it can be parsed to Update php file
		var stringifiedSlotsArray = JSON.stringify(jsSlotsArray);

		window.location.href = "http://lab-support.co.uk/update_phd_availability_procedure.php?slots=" + stringifiedSlotsArray;
		
	}
</script>


<!----  Initial transfer of PHP slots array to JS array ---->
<script>
	var jsSlotsArray = <?php echo $SlotsArrayJSONString; ?>;
</script>





<?php
	if (isset($_GET["status"])){
	
		if (strcmp($_GET["status"], "success")==0){
			print '<script type="text/javascript">';
			print 'alert("Availability was successfully updated.")';
			print '</script>';

		}
		elseif (strcmp($_GET["status"], "fail")==0){
			print '<script type="text/javascript">';
			print 'alert("Updating availability failed.")';
			print '</script>';

		}
		
	}


	include 'template-bottom.php';
	
	
		$conn = mysql_connect($servername, $username, $password);
        mysql_select_db($dbname, $conn);
        
		$result = mysql_query("SELECT * FROM `PHD_Availability` WHERE
		`Account_ID` = $currAccID", $conn);
        $num_rows = mysql_num_rows($result);
        if ($num_rows>0){
            $row = mysql_fetch_assoc($result);

			//getting serialized array and transforming it into assoc array
            $avaibilityArray = unserialize($row["Slots_Availability"]);
			
        }
		
		//getting availability value for slot 1 in this array
		$slot1Availability = $avaibilityArray["Slt1"];

?>









