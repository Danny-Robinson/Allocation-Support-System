<?php



	/* A REUSABLE PROCEDURE TO INSERT A NEW MODULE INTO THE DB*/




	include 'validate_login.php';

	

	if ($conn == NULL){ // there is no connection to the DB open
		//connect to DB
		require_once 'dbconfig.php';

		$conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	$duplicate_module_found = array();

	// Check if Module with the same code already exixts in the DB
	$search_module_query = $conn->prepare("SELECT Module_Code, Module_Name 
										   FROM Modules 
										   WHERE Module_Code='$new_module_code' LIMIT 1");
	$search_module_query->execute();
	
	while ($duplicate_module_found = $search_module_query -> fetch(PDO::FETCH_ASSOC)) {// Module with the same code already exixts in the DB
		$new_module_name = $duplicate_module_found['Module_Name'];
		$new_module_code = $duplicate_module_found['Module_Code'];
		
		$current .= "Module $new_module_code '$new_module_name' is already in the DB!\n";
		$current .= "\n*********************** DB VALIDATION ERRORS *********************\n";
		// Write the contents to the log file
		file_put_contents($file, $current);
		
		$conn = NULL; //close DB connection

		//Now redirect to itself with the error message (that clears the form as well) and exit further script
		header("Location: $location?insert_error=The Module $new_module_code '$new_module_name' already exists! Please re-enter the form.");
		exit;

	} // end of while loop
	

	//if there is no duplicate module code in DB
	//insert the new module into the Modules table
	$query = $conn->prepare("INSERT INTO Modules 
							(Module_Code, Module_Name) 
							VALUES (:Module_Code, :Module_Name);");
	$query->bindParam(':Module_Name', $new_module_name);
	$query->bindParam(':Module_Code', $new_module_code);
	$query->execute();
	$current .= "\nRecord for $new_module_code '$new_module_name' inserted into Modules\n";

?> 
