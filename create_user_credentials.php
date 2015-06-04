<?php



	/* A REUSABLE PROCEDURE TO CREATE A NEW USER CREDENTIALS*/


	include 'validate_login.php';
	include 'password_functions.php';

	

	$attempts = 10; // give up after 10 attempts of creating a unique username
	
	while ($attempts > 0){ // create and compare username with the existing ones in DB
	
		//create username (<= 15 characters) and password 
		$duplicate_username = "";
		$usname = $surname.$first_name.$initial;
		if (strlen($usname) > 12){
			$usname = substr($usname, 0, 12).rand(101, 998);
		} else {
			$usname = $usname.rand(101, 998);
		}
		$usname = stripString ($usname); // clean the string from spaces and special characters
		
		$uspswPlanText = genPass(); //create a random password 8 characters long
		sendPassEmail($usname,$uspswPlanText, $email);//email created user their new password(plain text format)
		$uspsw = genPassHash($uspswPlanText);//hash password is generated from the plain text password
	  
		if ($conn == NULL){ // there is no connection to the DB open
			//connect to DB
			require_once 'dbconfig.php';
	  
			$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
		// check if the same username already exists in DB
		$search_username_query = $conn->prepare("SELECT Username 
												 FROM Account_Data 
												 WHERE Username='$usname'");
		$search_username_query->execute();
		
		while ($duplicate_username_found = $search_username_query -> fetch(PDO::FETCH_ASSOC)) {// Same username already exixts in the DB
			
			$duplicate_username = $duplicate_username_found['Username'];

			$current .= "Username $duplicate_username already exists in the DB!\n";
			$current .= "\n*********************** DUPLICATE USERNAME WARNING*********************\n";
		}
		
		$attempts--;
		
		if ($duplicate_username == ""){
			break;
		}
		
	} // end of while loop
	
	if ($attempts <= 0){ //all attempts have been used up
		
		file_put_contents($file, $current);
		$conn = NULL; //close DB connection
		
		//Now redirect to itself with the error message (that clears the form as well) and exit further script
		header("Location: $location?insert_error=Sorry, could not create a unique username for you. Please contact the administrator.");
		exit;
		
	} else {
		
		// Append new data to the log file
		$current .= "User Full Name: $title $first_name $initial $surname\n";
		$current .= "User Email: $email\n";
		$current .= "Username: $usname\n";
		$current .= "User Password: $uspsw\n";
	}
	
?>