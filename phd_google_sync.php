<?php

session_start();

$header='Google Calendar Upload';

include 'template-top.php';

include 'validate_login.php';


$currAccID = intval($_SESSION['curr_user_id']);

include 'session_details_functions.php';



	
	try {
		
		include 'db_connect.php';
		
		//Getting all sessions for the current user
		$result = mysql_query("SELECT * FROM `Allocations` WHERE `Account_ID`=$currAccID", $conn);
		$num_rows = mysql_num_rows($result);
		
		$sessionsIDsArray = array($num_rows); //array to store all retrieved session IDs
		
		//putting sessions IDs in to an array
		$counter = 0; 
		while($row = mysql_fetch_array($result))
		{
			$sessionsIDsArray[$counter] = $row['Session_ID'];
			$counter++;
			
		}
		
		
		//array to store results on the get
		$sessionsAsEventsArray = array($num_rows); 
		
		//calling getModuleLocDateTimeFunc function to get labs details for each session 
		//and storing resulting arrays in to sessionsAsEventsArray
		for ($i = 0; $i < $num_rows; $i++) {
		
			$funcResult = getSessionDetails($sessionsIDsArray[$i],$conn);
			$sessionsAsEventsArray[$i] = $funcResult;
		
		}
		
		
		
		///print_r ($sessionsAsEventsArray);
	
		$encodedDessionsAsEventsArray = json_encode($sessionsAsEventsArray);
		
		///echo $encodedDessionsAsEventsArray;
		///echo "<br>";
	
		//creating a text file to store encodedDessionsAsEventsArray, file is uniquely named by current Account ID
		$file = 'phd_google_cal_sync_temp/'.$currAccID.'phd_google_syn_temp_store.txt';
		//encodedDessionsAsEventsArray is written to the text file 
		file_put_contents($file, $encodedDessionsAsEventsArray);
		
		

	
	}
	
	catch(PDOException $e) {

		echo "Error: " . $e->getMessage();

    }

	$conn = null;


?>

	<form>
			<h4>Please click Upload to add your lab timetable to your Google Calender.</h4>
			
	</form>

<?php

	if (isset($_GET["status"])){
			
			///echo "Timetable successfully uploaded to your Google Calendar!";
			print '<script type="text/javascript">';
			print 'alert("Timetable successfully uploaded to your Google Calendar!")';
			print '</script>'; 
		}

?>
	
	
	<input type="button" value="Upload" onClick="uploadFunc()">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" value="Calendar" onClick="openGoogleCalendar()">

	<script type='text/javascript' src='http://code.jquery.com/jquery-2.0.2.js'></script>
	<script>
		function uploadFunc() {
			var numOfSessions = "<?php echo $num_rows; ?>";
			if (numOfSessions < 1){
				alert("Cannot not upload. There are no lab/tutorial sessions in your timetable!")
			}
			else{
				window.location.href = "http://lab-support.co.uk/google_calendar_function.php";
			}
		}
		
		function openGoogleCalendar(){
			window.open("https://www.google.com/calendar/");
		
		}
		
		
	</script>

	
<?php


	include 'template-bottom.php';
?>
