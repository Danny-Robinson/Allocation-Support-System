<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of convertTimetableOriginalView
 *
 * @author Dela
 */
class convertTimetableOriginalView {
     private  $sessionTimetable;
     private  $weightTimetable;
     private  $newTimetable;
     private  $newWeight;
     private  $labs;
     
     
     // turns the advanceed veiw per student into the timetable formatt 
     function __construct($initialTt, $sessionTimetable, $weightTimetable, $labs) {
        $this->sessionTimetable =  $sessionTimetable;
        $this->weightTimetable =  $weightTimetable;
        $this->labs =  $labs;
        $this->__addSlotsToBeFilled($initialTt);
        $this->__addNewStudentsToTt($this->newTimetable);   
     }
     
     
     function __addSlotsToBeFilled($initialTt) {
         
           // create the session2 tables 
        foreach( $initialTt as $i => $i_value) {
             for ($j = 0; $j <  6; $j++) {
                 $this->newTimetable[$i][$j] = "null";
                 $this->newWeight[$i][$j] = "null";
             }     
         }

		foreach( $initialTt as $i => $i_value) {
			echo "i", $i, "<br>"; 
			for ($j = 0; $j < $this->labs[$i][3]; $j++) {
				$this->newTimetable[$i][$j] = "0";
			}
		}
		print_r($this->newTimetable);
		
  
     }
     
     
     // adds the new students to the timetable 
      function __addNewStudentsToTt($initialTt) {
           // $sessionsCount =  sizeof($this->slotsTimetable, 1) / sizeof($this->slotsTimetable);
      
         
         // for each studnet
          
	foreach($this->sessionTimetable as $i => $i_value) {
			$size = 0;
			// see how many students are taking that session 
			while ($this->sessionTimetable[$i][$size] != "null") {
				$size++;
			}
			
			// for each session
			for ($j = 0; $j != $size; $j++) {
				// move to next free space
				$l = 0;
				echo $this->sessionTimetable[$i][$j], "   ",  $l, "<br>";
				echo $this->newTimetable[$this->sessionTimetable[$i][$j]][$l], "<br>";
				echo "<br>";
				
				while ($this->newTimetable[$this->sessionTimetable[$i][$j]][$l] !=  "0") {
					$l++;
				}
				
				// has a free slot 
				$this->newTimetable[$this->sessionTimetable[$i][$j]][$l] = $i;
				$this->newWeight[$this->sessionTimetable[$i][$j]][$l] = $this->weightTimetable[$i][$j];

			}

		}
         
         
       }
     
     
      function __getnewWeight() {
          return $this->newWeight;  
       }
       
      function __getnewTimetable() {
          return $this->newTimetable;
       }  
     
   
}
