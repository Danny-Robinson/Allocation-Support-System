<?php
	  
	  
	 /* A REUSABLE PROCEDURE TO Select student suitability for substituting labs (called from 'substitute_staff.php') */
	/*  7TH PAGE OF 7 */
	  


	include 'validate_login.php';



	switch ($suitability) {
		case ($suitability > 0 && $suitability < 10):
			$str_suitability = "very low";
			break;						
		case ($suitability >= 10 && $suitability < 20):
			$str_suitability = "low";
			break;
		case ($suitability >= 20 && $suitability < 35):
			$str_suitability = "medium";
			break;
		case ($suitability >= 35 && $suitability < 60):
			$str_suitability = "high";
			break;
		case ($suitability >= 60 && $suitability <= 100):
			$str_suitability = "very high";
			break;
	}



?>