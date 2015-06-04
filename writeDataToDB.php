<?php

class writeDataToDB {
	function __construct($toWrite) {
	
 $servername = "planetmeerkat.co.uk";
 $username = "planetme_lab1ent";
 $password = "gr4BFoxcan13";
 $dbname = "planetme_lab-support";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	foreach ($toWrite as $x => $element) {
	
   $sql = "INSERT INTO `planetme_lab-support`.`Allocations` (`Allocation_ID`, `Session_ID`, `Account_ID`, `Status`) 
	VALUES (NULL, '$element[0]', '$element[3]', 'Confirmed')";
    //use exec() because no results are returned
    $conn->exec($sql);
    echo "New record created successfully";
	}
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

$conn = null;
	
	
	}
	}