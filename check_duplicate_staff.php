<?php

	
	
	/* Check if the person is already in the corresponding table in the DB (do not allow duplicates) */
	


	include 'validate_login.php';

	

	if ($conn == NULL){ // there is no connection to the DB open
		//connect to DB
		require_once 'dbconfig.php';
  
		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	$person_title = $title;
	$person_forename = strtolower($first_name);
	$person_surname = strtolower($surname);
	$person_initial = $initial;
	if ($person_initial != NULL){
		$person_initial = parse_initial($person_initial);
	}

	$person_query = "SELECT Title, Forename, Initial, Surname 
					 FROM $db_table;";
					 
	$search_person_query = $conn->prepare($person_query);
	$search_person_query->execute();
	
	while ($person_found = $search_person_query -> fetch(PDO::FETCH_ASSOC)) {
		
		$person_found_title = $person_found['Title'];
		$person_found_forename = strtolower($person_found['Forename']);
		$person_found_surname = strtolower($person_found['Surname']);
		$person_found_initial = $person_found['Initial'];
		if ($person_found_initial != NULL){
			$person_found_initial = parse_initial($person_found_initial);
		}
		
		if ($person_found_title == $person_title && $person_found_forename == $person_forename 
			&& $person_found_initial == $person_initial && $person_found_surname == $person_surname){
			$db_initial = $person_found['Initial'];
			$current .= "$person '{$person_found['Title']} {$person_found['Forename']} {$person_found['Initial']} {$person_found['Surname']}' is already in the $db_table table!\n";
			$current .= "\n*********************** DB VALIDATION ERRORS *********************\n";
			// Write the contents to the log file
			file_put_contents($file, $current);
		
			$conn = NULL; //close DB connection
			$initial = substr($initial, 0, 1); // get the first character of the entered initial
			
			//Now redirect to itself with the error message (that clears the form as well) and exit further script
			header("Location: $location?insert_error=$person '$title $first_name $db_initial $surname' already exists! If you believe you are a different person from the registered one, please enter your initial as e.g. '$initial<Number>' where <Number> could be any number 0 to 9");
			exit;
		}
		
	} // end of while loop
				
?>