<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of insertionSort
 *
 * @author Dela
 */
class insertionSort {
    private  $slotsTimetable;
    private  $sessionTimetable;
    private  $weightTimetable;
    private  $timetable;
    private  $weight;

	
	function __sortslot($toWrite) {
        

        	// for each student
        foreach($toWrite as $l => $x_value) {
            $element=$toWrite[$l][3];
			//echo $element;
			$record = $toWrite[$l];
            $j=$l;
            while($j>0 && $toWrite[$j-1][3]>$element) {
                //move value to right and key to previous smaller index
                $toWrite[$j]=$toWrite[$j-1];
                $j=$j-1;
                }
            //put the element at index $j
            $toWrite[$j]=$record ;
            }
        return $toWrite;   
    }
    
    function __sortBySlot($slotsTimetable, $sessionTimetable, $weightTimetable) {
        echo"herse";
            $this->slotsTimetable = $slotsTimetable;
            $this->sessionTimetable = $sessionTimetable;
            $this->weightTimetable = $weightTimetable;
			echo"heresdd";

        	// for each student
            foreach($this->slotsTimetable as $l => $x_value) {
		//for ($l = 1; $l < 11; $l++) {
			$k = 0;
			// see how many sessions they are taking
			while ($this->slotsTimetable[$l][$k] != "null") {
				$k++;
			}
			$size = $k;

			for ($i = 1; $i < $size; $i++) {

				$key = $this->slotsTimetable[$l][$i];
				$key1 =  $this->sessionTimetable[$l][$i];
				$key2 = $this->weightTimetable[$l][$i];

				$k = $i - 1;
				while ($k >= 0 && $this->slotsTimetable[$l][$k] > $key) {
					$this->slotsTimetable[$l][$k + 1] = $this->slotsTimetable[$l][$k];
					$this->sessionTimetable[$l][$k + 1] = $this->sessionTimetable[$l][$k];
					$this->weightTimetable[$l][$k + 1] = $this->weightTimetable[$l][$k];
					$k--;
				}
				$this->slotsTimetable[$l][$k + 1] = $key;
				$this->sessionTimetable[$l][$k + 1] = $key1;
				$this->weightTimetable[$l][$k + 1] = $key2;
			}
		}     
    }

    
       function __sortByWeight($slotsTimetable, $sessionTimetable, $weightTimetable) {
        
            $this->slotsTimetable = $slotsTimetable;
            $this->sessionTimetable = $sessionTimetable;
            $this->weightTimetable = $weightTimetable;

        	// for each student
            foreach($this->slotsTimetable as $l => $x_value) {
		//for ($l = 1; $l < 11; $l++) {
			$k = 0;
			// see how many sessions they are taking
			while ($this->slotsTimetable[$l][$k] != "null") {
				$k++;
			}
			$size = $k;

			for ($i = 1; $i < $size; $i++) {
                                $key = $this->weightTimetable[$l][$i];
				$key1 = $this->slotsTimetable[$l][$i];
				$key2 =  $this->sessionTimetable[$l][$i];
				

				$k = $i - 1;
				while ($k >= 0 && $this->weightTimetable[$l][$k] > $key) {
					$this->slotsTimetable[$l][$k + 1] = $this->slotsTimetable[$l][$k];
					$this->sessionTimetable[$l][$k + 1] = $this->sessionTimetable[$l][$k];
					$this->weightTimetable[$l][$k + 1] = $this->weightTimetable[$l][$k];
					$k--;
				}
                                $this->weightTimetable[$l][$k + 1] = $key;
				$this->slotsTimetable[$l][$k + 1] = $key1;
				$this->sessionTimetable[$l][$k + 1] = $key2;
				
			}
		}     
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
