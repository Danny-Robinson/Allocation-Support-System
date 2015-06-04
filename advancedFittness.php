<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of advancedFittness
 *
 * @author Dela
 */
class advancedFittness {

    private $slotsTimetable;
    private $sessionTimetable;
    private $weightTimetable;

    function __construct($slotsTimetable, $sessionTimetable, $weightTimetable) {
		
    
        $this->slotsTimetable = $slotsTimetable;
        $this->sessionTimetable = $sessionTimetable;
        $this->weightTimetable = $weightTimetable;
		//echo "before  same session  .......";
        //$this->__printTimeTable();
		
        $this->__SameSession();
		 //echo "after same session  .......";
        //$this->__printTimeTable();
        $this->__kull();
        //echo "before consecative check .......";
        //$this->__printTimeTable();
       foreach ($this->slotsTimetable as $i => $x_value) {
            // if there are 2 or more session for that student
            if ($this->slotsTimetable[$i][1] != "null") {
                $this->__consecutiveGroup($i);
            }
        }
        //$this->__printTimeTable();Cons
        $this->__sortByWeight();
        $this->__StudentCap();
       // echo "............................table";
        //$this->__printTimeTable();
        
        
    }
    function __consecutiveCheck($student) {
        
   
          
              $j = 1;
              $count = 0;
              $biggestWieght = 0;
               // loop through each session with data 
            while ($this->slotsTimetable[$student][$j] != "null") {
                //echo $this->slotsTimetable[$i][$j];
            
                 // if the current session is one after the previous session 
                if ($this->slotsTimetable[$student][$j] - 1 == $this->slotsTimetable[$student][$j - 1]) {
                    $count++;
                    // if the weight if more, store new biggest weight
                    if ($this->weightTimetable[$student][$j] > $biggestWieght) {
                        $biggestWieght = $this->weightTimetable[$student][$j];
                        $bigPos = $j;
                    }
                }
                // if the current session is more than one after the previous session 
                // that means the group has been broken 
                if ($this->slotsTimetable[$student][$j] - 1 != $this->slotsTimetable[$student][$j - 1]) {
                     if ($count > 1){
                         $this->__killBiggest($student, $bigPos);
                         
                     }
                     $bigPos = 0;
                    $biggestWieght = 0;
                    $count = 0;
                    $j = 0;
                }
                $j++;
                }
                // reached end, check if last int is consectative with the previous
            if ($this->slotsTimetable[$i][$j - 1] - 1 == $this->slotsTimetable[$i][$j - 2]) {
                if ($count > 1){
                         $this->__killBiggest($i, $bigPos);   
                         $this->__consecutiveCheck($i);
                     }
           }
            
        
        
        
    }

    function __StudentCap() {
        // for each studnet 
        foreach ($this->slotsTimetable as $i => $x_value) {
            $k = 0;
            // see how many sessions they are taking
            while ($this->slotsTimetable[$i][$k] != "null") {
                $k++;
            }
            while ($k > 5) {
                $this->slotsTimetable[$i][$k] = "null";
                $this->sessionTimetable[$i][$k] = "null";
                $this->weightTimetable[$i][$k] = "null";
                $k--;
            }
        }
    }

        function __SameSession() {
            // for each student 
            foreach ($this->slotsTimetable as $l => $x_value) {
                $size = 0;
                // see how many sessions they are taking
                while ($this->slotsTimetable[$l][$size] != "null") {
                    $size++;
                }

                // for each session 
                for ($i = 0; $i < $size; $i++) {
                    // stores the first index of repeating sessions
                    $smallestIndex = $i;
                    $smallestWeight = $this->weightTimetable[$l][$i];
                    // needed 
                    if ($this->slotsTimetable[$l][$i] == $this->slotsTimetable[$l][$i + 1]) {
                        // while the student was assigned two labs in the same slot
                        while ($this->slotsTimetable[$l][$i] == $this->slotsTimetable[$l][$i + 1]) {
                            if ($this->weightTimetable[$l][$i] < $smallestWeight) {
                                $smallestIndex = $i;
                            }
                            $this->weightTimetable[$l][$i] = $this->weightTimetable[$l][$i] + 80;
                            $i++;
                        }

                        $this->weightTimetable[$l][$i] = $this->weightTimetable[$l][$i] + 80;
                        $this->weightTimetable[$l][$smallestIndex] = $this->weightTimetable[$l][$smallestIndex] - 80;
                    }
                }
            }
        }

        function __printTimeTable() {
            echo "<br>";
            for ($i = 0; $i < 23; $i++) {
                echo "<br>";
                echo "Student ";
                echo $i;
                echo "<br>";
                for ($j = 0; $j < 11; $j++) {
                    echo '<tr>';
                    echo '<td>' . $this->slotsTimetable[$i][$j] . '</td>';
                    echo "................";
                }
                echo "<br>";
                for ($j = 0; $j < 11; $j++) {
                    echo '<td>' . $this->sessionTimetable[$i][$j] . '</td>';
                    echo "...............";
                }
                echo "<br>";
                for ($j = 0; $j <11; $j++) {
                    echo($this->weightTimetable[$i][$j]);
                    echo ".............";
                }
                echo "<br>";
                echo '</tr>';
            }
        }
        function __sortByWeight() {
             $sort = new insertionSort;
            $sort->__sortByWeight($this->slotsTimetable, $this->sessionTimetable, $this->weightTimetable);
            $this->slotsTimetable = $sort->__getSlotsTimetable();
            $this->sessionTimetable = $sort->__getSessionTimetable();
            $this->weightTimetable = $sort->__getWeightTimetable();
        }
        

        function __kull() {
            $k = new Kull($this->slotsTimetable, $this->sessionTimetable, $this->weightTimetable);
            $k->__kull();
            $this->slotsTimetable = $k->__getSlotsTimetable();
            $this->sessionTimetable = $k->__getSessionTimetable();
            $this->weightTimetable = $k->__getWeightTimetable();
        }

        function __consecutiveGroup($student) {
            //Initialise 
            $j = 1;
            $start = 0;
            $end = 0;
            $biggestWieght = 0;
            $bigPos = 0;
            $biggestWieght = $this->weightTimetable[$student][$j - 1];
            $start = $j - 1;
            // loop through each session with data 
            while ($this->slotsTimetable[$student][$j] != "null") {

                // if the current session is one after the previous session 
                if ($this->slotsTimetable[$student][$j] - 1 == $this->slotsTimetable[$student][$j - 1]) {
                    // if the weight if more, store new biggest weight
                    if ($this->weightTimetable[$student][$j] > $biggestWieght) {
                        $biggestWieght = $this->weightTimetable[$student][$j];
                        $bigPos = $j;
                    }
                }
                // if the current session is more than one after the previous session 
                // that means the group has been broken 
                if ($this->slotsTimetable[$student][$j] - 1 != $this->slotsTimetable[$student][$j - 1]) {
                    // if more than 2 sessions are consecrative 
                    // then a session will need to be killed off 
                    if (($end - $start) > 1) {
                        // kill off weakest 
                        echo "killing off student" . $student . "position" .  $bigPos;
                        $this->__killBiggest($student, $bigPos);
                        // start from the beginning 
                        $j = 1;
                    }

                    // set biggest to what will be first in the group 
                    $biggestWieght = $this->weightTimetable[$student][$j];
                    $bigPos = $j;
                    $start = $j;
                }
                $end = $j;
                $j++;
            }

            // reached end, check if last int is consectative with the previous
            if ($this->slotsTimetable[$student][$j - 1] - 1 == $this->slotsTimetable[$student][$j - 2]) {
                $end = $j - 1;
                // if more than 2 sessions are consecrative 
                // then a session will need to be killed off 
                if ($end - $start > 1) {
                    // kill one off 
                    echo "killing off student" . $student . "position" .  $bigPos;
                    $this->__killBiggest($student, $bigPos);
                    // call meathod again 
                    $this->__consecutiveGroup($student);
                }
            }
        }

        function __killBiggest($student, $biggest) {

            $i = $student;
            $j = $biggest;
            // if the last session is the biggest, just kill it off 
            if ($this->slotsTimetable[$i][$j + 1] == "null") {
                $this->slotsTimetable[$i][$j] = "null";
                $this->sessionTimetable[$i][$j] = "null";
                $this->weightTimetable[$i][$j] = "null";
            } else {
                // else the session to kill off is not last, copy data from next sessions
                while ($this->slotsTimetable[$i][$j + 1] != "null") {
                    $this->slotsTimetable[$i][$j] = $this->slotsTimetable[$i][$j + 1];
                    $this->sessionTimetable[$i][$j] = $this->sessionTimetable[$i][$j + 1];
                    $this->weightTimetable[$i][$j] = $this->weightTimetable[$i][$j + 1];
                    $j++;
                }
                // set last session to null 
                $this->slotsTimetable[$i][$j] = "null";
                $this->sessionTimetable[$i][$j] = "null";
                $this->weightTimetable[$i][$j] = "null";
            }
        }
       function __getSessionTimetable() {
          return $this->sessionTimetable;  
       }
       
      function __getWeightTimetable() {
          return $this->weightTimetable;
       }  
        //put your code here
    }
    