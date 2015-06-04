<?php
session_start();


$week = $_SESSION[week];
$_SESSION[week] = $week;

$header =  "Week " . $_SESSION[week] . " - " . "Scheduler";
include 'template-top.php';
include ("timeTableView.php");
include ("writeDataToDB.php");
include ("finalConversion.php");

$ids = $_SESSION['ids'];
$sessionsToFill = $_SESSION['sessionsToFill'];
$toWrite = $_SESSION['toWrite'];
$student = $_SESSION['student'];

echo "If You Are Happy With This Timetable Click Commit";
echo "<br>";
echo "<br>";
$fc = new finalConversion($ids, $sessionsToFill, $toWrite, $student);
$toWrite = $fc->__getScheduledTimetable();
$table = $fc->__getTable();
$_SESSION[week]++;

//$toDB = new writeDataToDB($toWrite);

?>
  <center><input type=button onClick="parent.location='schedulerMain.php'" value='Schedule Next Week'> <center>
 <?php
 echo "<br>";
 echo "<br>";
$tt =  new timeTableView($table);
  include 'template-bottom.php';
?>
