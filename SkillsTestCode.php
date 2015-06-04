<?php


	$servername = "planetmeerkat.co.uk";
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";


try {

$conn = mysql_connect($servername, $username, $password);

		mysql_select_db($dbname, $conn);

		$result = mysql_query("SHOW COLUMNS FROM PHD_Students WHERE Field LIKE 'Skill%';", $conn);

		$num_rows = mysql_num_rows($result);

		//array to store names of skills retrieved from the database
		$skillNameArray = array($num_rows);
		
	
		
		$counter = 0;
		//getting names of all fields and placing them in an array
		while($row = mysql_fetch_array($result)){
			$skillNameArray[$counter] = $row['Field'];
			$counter++;
		}
		
		for ($i = 0; $i < $num_rows; $i++) {
			
				$string = str_replace("Skill_", "", $skillNameArray[$i]);

				$string = str_replace("_", " ", $string);
				
				$skillNameArray[$i] = $string;
			}
print_r($skillNameArray);
}


catch(PDOException $e)
	{

		echo "asdas";
		$status = "fail";
	}


$conn = null;









?>