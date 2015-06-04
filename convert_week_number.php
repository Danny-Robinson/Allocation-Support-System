<?php
	function convertWeekNumber($rawWeekNumber){
		$servername = "planetmeerkat.co.uk";
		$username = "planetme_lab1ent";
		$password = "gr4BFoxcan13";
		$dbname = "planetme_lab-support";
		$findWeeks = "SELECT * FROM Week_Date";
		$weekNo = 0;
		$recessNo = 0;
		$recessSubtraction = 0;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			
			foreach ($conn->query($findWeeks) as $row) {
				$weekNo++;
				if ($row['Is_Recess']==0){
					$recessNo=0;
				}
				else {
					$recessNo++;
					$recessSubtraction++;
				}
				if ($weekNo==$rawWeekNumber) {
					if ($recessNo!=0) {
						echo 'Recess(Wk'.$recessNo.')';
					}
					else {
						echo $weekNo-$recessSubtraction;
					}
					$conn=null;
					return null;
				}
			}
		}
		catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		echo 'ERROR: input argument is invalid!';
		$conn=null;
	}
?>