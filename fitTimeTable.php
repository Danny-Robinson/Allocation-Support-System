<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of fitTimeTable
 *
 * @author Dela
 */
class fitTimeTable {
    
    
    private $sessionTimetable;
    private $weightTimetable;
    private $newTimeTable;
    private $weight;
  
    function __construct($initialTt, $sessionTimetable2, $weightTimetable2) {
        $this->sessionTimetable = sessionTimetable2;
        $this->weightTimetable = weightTimetable2;
        $this->__newTimeTable();
  
        
    }
    
    function __newTimeTable() {
       
        foreach($student as $i => $i_value) {
            // see how many sessions they are taking
                while($this->sessionTimetable[$i][$k] != "null") {
                    $k++;    
                }
                for ($j = 0; $j <= $k; $j++) {
                    $l = 0;
                    while($this->$newTimeTable[$this->sessionTimetable[$i][$j]][$l] != null) {
                        $l++;  
                    }
                $this->$newTimeTable[$this->sessionTimetable[$i][$j]][$l] = $i;
                $this->$weight[$this->sessionTimetable[$i][$j]][$l] = $weightTimetable[$i][$j];
                }
        }  
    }
    function __getTimeTable() {
        return  $this->$newTimeTable;
    }
    
       function __getWeight() {
        return  $this->$weight;
    }
    
    
    
    //put your code here
}
