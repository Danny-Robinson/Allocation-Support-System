<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kull
 *
 * @author Dela
 */
class Kull {
    //put your code here
    private  $slotsTimetable;
    private  $sessionTimetable;
    private  $weightTimetable;
    
    
    function __construct($slotsTimetable, $sessionTimetable, $weightTimetable){
            $this->slotsTimetable = $slotsTimetable;
            $this->sessionTimetable = $sessionTimetable;
            $this->weightTimetable = $weightTimetable;
    }
            
            
    
    // kill off all sessions greater than 80 
    function __kull() {
        // number of colombs 
        $sessionsCount =  sizeof($this->slotsTimetable, 1) / sizeof($this->slotsTimetable);
        // create the session2 tables 
        foreach( $this->slotsTimetable as $i => $i_value) {
             for ($j = 0; $j <  $sessionsCount+ 1; $j++) {
                 $slotsTimetable1[$i][$j] = "null";
                 $sessionTimetable1[$i][$j] = "null";
                 $weightTimetable1[$i][$j] = "null";
             }     
         }

		// for each possible student
               foreach($this->slotsTimetable as $j => $x_value) {
			$k = 0;
			$l = 0;
			// while the session does not = null
			while ($this->slotsTimetable[$j][$k] != "null") {
				if ($this->weightTimetable[$j][$k] < 80) {
					$slotsTimetable1[$j][$l] = $this->slotsTimetable[$j][$k];
					$sessionTimetable1[$j][$l] = $this->sessionTimetable[$j][$k];
					$weightTimetable1[$j][$l] = $this->weightTimetable[$j][$k];
					$l++;
				}
				$k++;
			}
		}
		// new tables
		$this->slotsTimetable = $slotsTimetable1;
		$this->sessionTimetable = $sessionTimetable1;
		$this->weightTimetable = $weightTimetable1;
	}
        
        
       function __getSlotsTimetable() {
          return $this->slotsTimetable;  
      }
      
       function __getSessionTimetable() {
          return $this->sessionTimetable;  
       }
       
      function __getWeightTimetable() {
          return $this->weightTimetable;
       }
}
