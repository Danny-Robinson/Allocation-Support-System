<?php



	session_start();

	include 'validate_login.php';



	$SkillName = $_GET['skill_name'];

	$SkillVal = $_GET['skill_val'];

	

	

	//current logged in user ID 

	$currAccID = intval($_SESSION['curr_user_id']);

	
	$status = updateSkillPref($currAccID, $SkillName, $SkillVal);
	
	
	$normalSkillName = str_replace("Skill_", "", $SkillName);
	$normalSkillName = str_replace("_", " ", $normalSkillName);

	header('Location:phd_skills_pref_page.php?status='.$status.'&skill_name='.$normalSkillName);

	
	
	

function updateSkillPref($currAccID, $SkillName, $SkillVal){
	try {
	
		$servername = "planetmeerkat.co.uk";

		$username = "planetme_lab1ent";

		$password = "gr4BFoxcan13";

		$dbname = "planetme_lab-support";

		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

		// set the PDO error mode to exception

		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



		$sql = "UPDATE `PHD_Students` SET `$SkillName`='$SkillVal' WHERE `Account_ID`=$currAccID";



		// Prepare statement

		$stmt = $conn->prepare($sql);



		// execute the query

		$stmt->execute();


		
		$conn = null;
		return "success";
	}
	catch(PDOException $e)
	{

		echo $sql . "<br>" . $e->getMessage();
		$conn = null;
		return "fail";
	}

}




?>