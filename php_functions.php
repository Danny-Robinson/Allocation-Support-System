<?php



	/* PHP COMMON FUNCTIONS */



	include 'validate_login.php';


	// Protect from SQL injection attacks
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
  		return $data;
	}



	// Remove special characters, spaces and other symbols from a string
	function stripString ($string) { 
		$string = preg_replace("/[^a-zA-Z0-9\-]/i", ' ', $string); // replaces special chars with spaces.
		$string = str_ireplace(" ", "", $string); //strips all the spaces from username
		$string = str_ireplace("-", "", $string); //remove all "-" from username
   		return $string;
	}



	// Check if the second character of the initial is not alphanumerical, and if it is not, then only return the first character of the initial
	// to deal with "A." and "A" variations of the same initial
	function parse_initial($string){
		$string = strtolower($string);
		$pattern = "/\W/";
		if (preg_match($pattern, substr($string, -1)) == 1){
			return substr($string, 0, 1);
		} else {
			return $string;
		}
	}


	// Check if the date format is valid (http://php.net/manual/en/function.checkdate.php)
	function validateDate($date, $format = 'Y-m-d'){ 
    	$d = DateTime::createFromFormat($format, $date);
    	return $d && $d->format($format) == $date;
	}

	
	
	// Flatten a multidimentional array (http://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array)
	function flatten(array $array) {
    	$flat_array = array();
   		array_walk_recursive($array, function($a) use (&$flat_array) { $flat_array[] = $a; });
    	return $flat_array;
	}

?>
