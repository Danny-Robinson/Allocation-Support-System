<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of fittness
 *
 * @author Dela
 */

include ("basicFitness.php");
include ("createStudentTimetable.php");
include ("advancedFittness.php");
include ("convertTimetableOriginalView.php");

class fittness {
   
    
    private $labs;
    private $initialTt;
    private $weight;
    private $newWeight;
    private $newTimetable;
    
 
    
    function __construct($initialTt, $labs, $students , $NumOfSessions, $prevTt, $allLabs) {
        $this->initialTt = $initialTt;
        $this->labs = $labs;
        $this->students = $students;
        echo ".....................................................................................";

         $bF  = new basicFitness($this->initialTt, $this->labs, $this->students);
		 echo ".....................................................................................", "<br>";
		 $this->weight = $bF->__getWeight();
	
		 
		 // merge timetables 
		$numberAdded = $this->__mergeWithPreviousweek($prevTt);
		

		echo "<br>",$NumOfSessions, "number added " , $numberAdded, "<br>";
		 $NumOfSessions = $NumOfSessions + $numberAdded ;
        
         $sTt = new createStudentTimetable($this->initialTt,  $allLabs, $this->weight, count($this->students),  $NumOfSessions);
		 
	

         $aF = new advancedFittness($sTt->__getSlotsTimetable(), $sTt->__getSessionTimetable(), $sTt->__getWeightTimetable());  
         echo ".....................bndbdbdbdbdbd............";
		 
		  
         $CTt = new convertTimetableOriginalView($this->initialTt, $aF->__getSessionTimetable(), $aF->__getWeightTimetable(),$allLabs);
		 
         $this->newWeight = $CTt->__getnewWeight();
         $this->newTimetable = $CTt->__getnewTimetable();
		 
    }
	
	
	function __evaluateMergerTt($initialTt, $labs, $students , $NumOfSessions, $weight) {
		
		echo ".....................bndbdbdbdbdbd............";
		
		$this->initialTt = $initialTt;
        $this->labs = $labs;
        $this->students = $students;
		$this->weight = $weight;
        echo ".....................................................................................";
		

         $sTt = new createStudentTimetable($this->initialTt, $this->labs , $this->weight, count($this->students),  $NumOfSessions);
		 
	

         $aF = new advancedFittness($sTt->__getSlotsTimetable(), $sTt->__getSessionTimetable(), $sTt->__getWeightTimetable());  
         echo ".....................bndbdbdbdbdbd............";
		 
		  
         $CTt = new convertTimetableOriginalView($this->initialTt, $aF->__getSessionTimetable(), $aF->__getWeightTimetable(),$this->labs);
		 
         $this->newWeight = $CTt->__getnewWeight();
         $this->newTimetable = $CTt->__getnewTimetable();
		
		
		
	}
 
 	  function __mergeWithPreviousweek($prevTt) {
		$inTt = $this->initialTt;
		$weight = $this->weight;
		   foreach($prevTt as $i => $i_value) {
			   foreach($i_value as $j => $j_value) {
				  //echo $j_value . "<br>"; 
			  
			   if ($j_value != "null") {
				   
				   $k = 0;
				    while ($inTt[$i][$k] != "null"){
						echo $inTt[$i][$k]  . "<br>";
						$k++;
					}
					//echo "is null" . $inTt[$i][$k]  . "<br>";
				   $inTt[$i][$k] = $j_value; 
					$numAdded++;		   
			   } 
		   }	 
		   }
		    foreach($inTt as $x => $x_value) {
             for ($i = 0; $i < 5; $i++) {
                echo $inTt[$x][$i] . " ";
             }
             echo " . . . . .";
             for ($i = 0; $i < 5; $i++) {
                echo $weight[$x][$i] . " ";
             }

             echo "<br>";
             }
			$this->initialTt = $inTt;
			return $numAdded++;	
	  }
    
    
    
       function __getnewWeight() {
          return $this->newWeight;  
       }
       
      function __getnewTimetable() {
          return $this->newTimetable;
       } 
      function __printTt($timetable, $weight) {
           
           
           foreach($timetable as $x => $x_value) {
             for ($i = 0; $i < 5; $i++) {
                echo $timetable[$x][$i] . " ";
             }
             echo " . . . . .";
             for ($i = 0; $i < 5; $i++) {
                echo $weight[$x][$i] . " ";
             }
             
             
             echo "<br>";
             }
         }	   
}
