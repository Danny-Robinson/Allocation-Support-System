<?php
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
session_start();

include 'validate_login.php';


$currAccID = intval($_SESSION['curr_user_id']);

$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");

/*a new Google developer account (cardifflabsupp@gmail.com) was created to get details such as ClientID, 
ClientSecret, Redirect Uri and Dev Key Calender API was activated on Google developer account via Google 
dev console And lab-support.co.uk server IP address was added so API requests are accepted from our website's
server*/
$client->setClientId('784255847612-voqbipgi5u0l668otqlf8dv4b5r1r65j.apps.googleusercontent.com');
$client->setClientSecret('D-2TtWdrG-XEEeKFaU8ZHUkO');
$client->setRedirectUri('http://lab-support.co.uk/google_calendar_function.php');
$client->setDeveloperKey('AIzaSyADyecAMsbhB3aOpclwDegnmuAJbhx3xG8');
$cal = new Google_CalendarService($client);

if (isset($_GET['logout'])) {
	unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
	//getting user's calender id so sessions/labs can be added using it later
	$calList = $cal->calendarList->listCalendarList();
	$itemsArray = $calList['items'];
	$firstArrayInItemsArray = $itemsArray['0'];
	$calendarID = $firstArrayInItemsArray['id']; 
  
  
	$encodedDessionsAsEventsArray = file_get_contents('phd_google_cal_sync_temp/'.$currAccID.'phd_google_syn_temp_store.txt');
	$sessionsAsEventsArray = json_decode($encodedDessionsAsEventsArray, true);
	
    $numOfEvents = count($sessionsAsEventsArray);
	
	if ($numOfEvents < 1){
		header( 'Location: phd_google_sync_page.php?status=NoSess' ) ;
	
	}
	 			
	
	for ($i = 0; $i < $numOfEvents; $i++ ) {
		//Info retrieved from MySQL DB
		$labEventTitle = $sessionsAsEventsArray[$i]["labEventTitle"];
		$labLocation = $sessionsAsEventsArray[$i]["labLocation"];
		$labDate = $sessionsAsEventsArray[$i]["labDate"];
		$labStartTime = $sessionsAsEventsArray[$i]["labStartTime"];
		$labEndTime = $sessionsAsEventsArray[$i]["labEndTime"];
		  
		  
		//Creating Start and End for an event(lab)
		$labStart = new Google_EventDateTime;
		$labStart->setDateTime($labDate.$labStartTime);
		$labEnd = new Google_EventDateTime;
		$labEnd->setDateTime($labDate.$labEndTime);
		  
		 
		  
		//Creating an event(lab) to be inserted to calendar
		$entry = new Google_Event; 
		$entry->setSummary($labEventTitle);
		$entry->setLocation($labLocation);
		$entry->setStart($labStart);
		$entry->setEnd($labEnd);
		  


		//Inserting event to Google Calendar
		$createdEvent = $cal->events->insert($calendarID,$entry);
	}
  
	

	$_SESSION['token'] = $client->getAccessToken();
	


	header( 'Location: phd_google_sync_page.php?status=uploaded' ) ;


} else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Click here to connect to your Google Calendar</a>";
}