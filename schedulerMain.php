<?php

 session_start();
 $_SESSION[toWrite] = null;
//$_SESSION[week] = 5;
$week = $_SESSION[week];
$_SESSION[week] = $week;
	$header =  "Week " . $_SESSION[week] . " - " . "Automatically Scheduled";
	include 'template-top.php';
	

include ("scheduler.php");
include ("getDataScheduler.php");
include ("nextSessionPrep.php");
$d = new getDataScheduler;


        
 $allLabs = $d->__getallLabs();
 $Labs = $d->__getNewLabs();
 $student = $d->__getNewStudents();
 $prevSessions = $d->__getPrevSessions();
 //$labsIndex = $d->__getLabsIndex();
 //$studentIndex = $d->__getStudentIndex();

 
 
 //print_r($student);
  //print_r($Labs);    
echo ".....................................................................................";  


$s = new scheduler($Labs,$student,$prevSessions,$allLabs );
echo "<br>" . " ........... mergeaaaaaaae///////////////////.........newWeight...." . "<br>" ; 
$timetable = $s->__returnTimetable();
echo "<br>" . " ........... mergedTimetable///////////////////.........newWeight...." . "<br>" ; 
  foreach($timetable as $x => $x_value) {
             for ($i = 0; $i < 5; $i++) {
                echo $timetable[$x][$i] . " ";
             }
             echo "<br>";
             }
echo "<br>";

$studentIndex = $d->__getStudentIndex();
$labsIndex = $d->__getLabsIndex();

echo "<br>";
echo " student indexx";
//print_r($studentIndex );
echo "<br>";
echo "<br>";
echo " lab index ";
//print_r($labsIndex );

$prep = new nextSessionPrep($timetable, $labsIndex,  $studentIndex, $student );
/*
?>
  <center><input type=button onClick="parent.location='displayScheduledSessions.php'" value='Schedule Next Week'> <center>
 <?php
 */

header('Location: displayScheduledSessions.php');





	include 'template-bottom.php';
?>
	