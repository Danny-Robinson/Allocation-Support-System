<?php



	session_start();

	include 'validate_login.php';

	$encodedArray = $_GET['slots'];

	$decodedArray = json_decode($encodedArray, true);

	$SerialisedSlotsArray = serialize($decodedArray);



						

	//current logged in user ID 

	$currAccID = intval($_SESSION['curr_user_id']);

	

	$servername = "planetmeerkat.co.uk";

	$username = "planetme_lab1ent";

	$password = "gr4BFoxcan13";

	$dbname = "planetme_lab-support";

	
	
	

	try {

		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "UPDATE `PHD_Availability` SET `Slots_Availability`='$SerialisedSlotsArray' WHERE `Account_ID`=$currAccID";

		// Prepare statement
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();

		echo $stmt->rowCount() . " records UPDATED successfully";
		
		$status = "success";

		}

	catch(PDOException $e)

		{

		echo $sql . "<br>" . $e->getMessage();
		$status = "fail";
		}



	$conn = null;

	

	header('Location:phd_availability_page.php?status='.$status);

	

?>



