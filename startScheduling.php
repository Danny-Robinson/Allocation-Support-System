<?php
session_start();
 $_SESSION[week] = 1;
$header =  "Week " . $_SESSION[week] . " - " . "Scheduler"; /* use this variable to set the header string */
include 'template-top.php';




?>

 <center><input type=button onClick="parent.location='schedulerMain.php'" value='Run Scheduler'></center>
 <?php



include 'template-bottom.php';
  

 

?>