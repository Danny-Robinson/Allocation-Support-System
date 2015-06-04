<?php
	  
	  
	  
	/* Insert the new staff member to the general staff table (Account_Data) */
	  


	include 'validate_login.php';

	

	$query = $conn->prepare("INSERT INTO Account_Data (Account_Type, Username, Password, Email) 
	    							VALUES (:Account_Type, :Username, :Password, :Email);");
	$query->bindParam(':Account_Type', $acc_type);
	$query->bindParam(':Username', $usname);
	$query->bindParam(':Password', $uspsw);
	$query->bindParam(':Email', $email);
	$query->execute();
	$current .= "Record inserted into Accounts_Data. ";

	$query = $conn -> prepare("SELECT Account_ID 
							   FROM Account_Data 
							   WHERE (Username = '$usname' AND Password = '$uspsw');"); 
	$query -> execute();
	while ($row = $query->fetch()) {
		$acc_id = $row['Account_ID'];
		$current .= "Account id = $acc_id\n\n";
	}


?>