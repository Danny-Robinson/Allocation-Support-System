<?php



session_start();



include 'validate_login.php';





//Determining which week slot id is closest to the searchSlot

function getClosest($search, $arr) {

   $tempSearch = $search;

   $closest = null;

   foreach($arr as $item) {

	  if($closest == null || abs($tempSearch - $closest) > abs($item - $tempSearch) && ($item <= $tempSearch)) {

		 $closest = $item;

	  }

   }

   return $closest;

}





function getSessionDetails($searchSession, $conn) {



		

	try {

		//establishing connection with DB

		

		

		//Get Slot ID(used to derive time/date) and Lab ID(used to derive module/loc) for searched session ID

		$result = mysql_query("SELECT * FROM `Lab_Timetable` WHERE `Session_ID`=$searchSession", $conn);

		$row = mysql_fetch_array($result);

		$slotID = $row['Slot_ID']; //used to derive date/time

		$labID = $row['Lab_ID']; //used to derive module/loc

		

		

		/**    DERIVING DATE/TIME     **/

		//Get all Slot ids from Week_date

		$result = mysql_query("SELECT `Slot_ID` FROM `Week_Date`", $conn);

		$num_rows = mysql_num_rows($result);

		

		//Transferring all Week Slots ids to an Array

		$StartWeekSlots = array($num_rows);

		$counter = 0;

		while($row = mysql_fetch_array($result))

		{

			$StartWeekSlots[$counter] = $row['Slot_ID'];

			$counter++;

		}

		

		//Finding the week the Slot ID belongs to

		$closestWeekSlot = getClosest($slotID, $StartWeekSlots);

		

		//Getting the time-stamp for the closest Week slot id from DB 

		$result = mysql_query("SELECT `Week_Com` FROM `Week_Date` WHERE `Slot_ID`=$closestWeekSlot", $conn);

		$row = mysql_fetch_array($result);

		$weekTimeStamp = $row['Week_Com'];



		//getting "Day" of the week for Slot ID

		$result = mysql_query("SELECT * FROM `Time_Slots` WHERE `Slot_ID`=$slotID", $conn);

		$row = mysql_fetch_array($result);

		$SlotDayOfWeek = $row['Day'];

		$SlotDayOfWeek = $SlotDayOfWeek - 1;

		

		//adding day of the week to the week time-stamp

		$dayTimeStamp = strtotime('+'.$SlotDayOfWeek.' day', $weekTimeStamp);

		

		//retrieving data from the time-stamp

		$tsToDate = getDate($dayTimeStamp);

		$yearOfSlot = $tsToDate["year"];

		$monthOfSlot = $tsToDate["mon"];

		$dayOfSlot = $tsToDate["mday"];

		

		//getting the time when the session is to be run

		$slotTimeOfDay = $row['Time']; 

		

		//array to map time of the day to actual clock time

		$timeOfSlotsArray = array(

					1 => 9,

					2 => 10,

					3 => 11,

					4 => 12,

					5 => 13,

					6 => 14,

					7 => 15,

					8 => 16,

					9 => 17,				

		);

		//getting accurate start and end times for the slot

		$slotTime = $timeOfSlotsArray[$slotTimeOfDay];

		$slotEndTime = $slotTime + 1;

		

		

		//Preparing output variables for Date/StartTime/EndTime

		$formattedSlotDate = $yearOfSlot."-".$monthOfSlot."-".$dayOfSlot;

		$formattedSlotStartTime = "T".$slotTime.":00:00.000+00:00";

		$formattedSlotEndTime = "T".$slotEndTime.":00:00.000+00:00";

		

		

		

		/**    GETTING MODULE/LOC     **/

		$result = mysql_query("SELECT * FROM `Lab_Requirements` WHERE `Lab_ID`=$labID", $conn);

		$row = mysql_fetch_array($result);

		$labModule = $row['Module'];

		$labLoc = $row['Room'];

		

		/*

		//to get module name(not working)

		$result = mysql_query("SELECT * FROM `Modules` WHERE `Module_Code`=$labModule", $conn);

		$row = mysql_fetch_array($result);

		$labModuleName = $row['Module_Name'];

		$labName = $labModuleName;

		*/

		

		

	}

		

		

	catch(PDOException $e) {



		echo "Error: " . $e->getMessage();



    }



	$conn = null;	



	//$outputArray = array($formattedSlotDate,$formattedSlotStartTime,$formattedSlotEndTime);

	$outputArray = array("labEventTitle"=>$labModule, "labLocation"=>$labLoc, "labDate"=>$formattedSlotDate, 

						"labStartTime"=>$formattedSlotStartTime, "labEndTime"=>$formattedSlotEndTime);

	

	

	return $outputArray;











}









?>