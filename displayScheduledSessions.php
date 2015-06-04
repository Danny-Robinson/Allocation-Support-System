<?php
session_start();

$week = $_SESSION[week];
$_SESSION[week] = $week;
$header = "Week " . $_SESSION[week] . " - " . "Admin Selection"; /* use this variable to set the header string */
	include 'template-top.php';



include ("view.php");


$toWrite = null;
$toWrite = $_SESSION['toWrite'];
$sessionsToFill = $_SESSION['sessionsToFill'];
$student= $_SESSION['student'];
$studentIndex = $_SESSION['studentIndex'];
$v = new view($toWrite,$sessionsToFill,$student, $studentIndex);





include 'template-bottom.php';
  

 

?>
