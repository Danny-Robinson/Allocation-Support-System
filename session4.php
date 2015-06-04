<?php
session_start();
$header = 'Commit Timetable To Database'; /* use this variable to set the header string */
include 'template-top.php';
	$timetable = $_SESSION['toWrite'];

 $servername = "planetmeerkat.co.uk";
 $username = "planetme_lab1ent";
 $password = "gr4BFoxcan13";
 $dbname = "planetme_lab-support";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	foreach ($timetable as $x => $element) {
	
   $sql = "INSERT INTO `planetme_lab-support`.`Allocations` (`Allocation_ID`, `Session_ID`, `Account_ID`, `Status`) 
	VALUES (NULL, '$element[0]', '$element[3]', 'confirmed')";
    //use exec() because no results are returned
    $conn->exec($sql);
    //echo "New record created successfully";
	}
    }
catch(PDOException $e)
    {
    //echo $sql . "<br>" . $e->getMessage();
    }
	
	echo "Timetable Successfully Committed To Database";
	header('Location: schedulerMain.php');

$conn = null;



  include 'template-bottom.php';

 

?>