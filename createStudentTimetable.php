<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of studentTimetable
 *
 * @author Dela
 */

include ("insertionSort.php");
include ("Kull.php");
class createStudentTimetable {
    private $labs;
    private $initialTt;
    private $weight;
    private  $slotsTimetable;
    private  $sessionTimetable;
    private  $weightTimetable;
  
    function __construct($initialTt,  $labs, $weight, $studentCount,  $sessionsCount) {
		// remove initial time table 1
	//	print_r($initialTt1);
      
         $this->initialTt = $initialTt;
		
         $this->labs = $labs;
         $this->weight = $weight;
         // for each student
         for ($i = 0; $i < $studentCount +1; $i++) {
             for ($j = 0; $j <  $sessionsCount+ 1; $j++) {
                 $this->slotsTimetable[$i][$j] = "null";
                 $this->sessionTimetable[$i][$j] = "null";
                 $this->weightTimetable[$i][$j] = "null";
             }     
         }
        // echo "<br>", "............vv.............." ,$sessionsCount ,  "<br>";
		// print_r($this->initialTt);
		  //print_r( $initialTt);
		
       //$this->__printTimeTable();
	 
        $this->__createTimeTable();
		echo "<br>", "............Time table created............." ,  "<br>";
		$this->__printTimeTable();
        $this->__sortBySlot();
		echo "...............................................................AAAAAAAAAAAAA..................";
        $this->__printTimeTable();
		
		//echo"here";
        $this->__kull();
		
		

        //$this->__printTimeTable();
      
  
        echo "...............................................................n..................";
        $this->__printTimeTable();
    }
    function __kull() {
		//echo"herdde";
        $k = new Kull($this->slotsTimetable, $this->sessionTimetable, $this->weightTimetable);
		//echo"herdssssde";
        $k->__Kull();
		
        $this->slotsTimetable = $k->__getSlotsTimetable();
        $this->sessionTimetable = $k->__getSessionTimetable();
        $this->weightTimetable = $k->__getWeightTimetable();    
    }
    
    
    
    
    // calls the insertionsort and handels returning values 
    function __sortBySlot() {
        $sort = new insertionSort();
        $sort->__sortBySlot($this->slotsTimetable, $this->sessionTimetable, $this->weightTimetable);
        $this->slotsTimetable = $sort->__getSlotsTimetable();
        $this->sessionTimetable = $sort->__getSessionTimetable();
        $this->weightTimetable = $sort->__getWeightTimetable();
    }
    
    function __createTimeTable() {
		echo "<br>";
		echo "<br>";
        foreach(  $this->initialTt as $i => $i_value) {
			
			//print_r($i_value);
			//echo "<br>";
			
            for ($j = 0; $j < 5; $j++) {
               
                if ( ( $this->initialTt[$i][$j] != "null") && ( $this->initialTt[$i][$j] != "0")){
                    // move to the next free slot
                         $k = 0;  
						 
			while ($this->slotsTimetable[$this->initialTt[$i][$j]][$k] != "null") {
				 //echo " = " , $this->slotsTimetable[$this->initialTt[$i][$j]][$k];
				$k++;
			}
			

			// copy accross the data 
                        $this->slotsTimetable[$this->initialTt[$i][$j]][$k] =  $this->labs[$i][1];
                        $this->sessionTimetable[$this->initialTt[$i][$j]][$k] = $i;
                        $this->weightTimetable[$this->initialTt[$i][$j]][$k] =  $this->weight[$i][$j];
						$this->weightTimetable[1][0] = 0;
					  // echo $this->initialTt[$i][$j], " ";
						//echo "," , $k, " = ";
						//echo  $this->weightTimetable[$this->initialTt1[$i][$j]][$k], "<br>";
					   
					   
			//echo $this->slotsTimetable[$this->initialTt[$i][$j]][$k], " ";
			//echo $this->sessionTimetable[$this->initialTt[$i][$j]][$k], " ";
			//echo  $this->weightTimetable[$this->initialTt1[$i][$j]][$k], "<br>";
			//echo  "i ",$this->initialTt[$i][$j], "j ", $k, " ", $this->weightTimetable[$this->initialTt1[$i][$j]][$k], "<br>";
			//echo  " 40" , $this->weightTimetable[4][0], "<br>";
			     
            }
        }
        
        }
		/*
		echo "weights", "<br>";
		echo  $this->slotsTimetable[1][0], " ";
		echo  $this->weightTimetable[1][0], "<br>";
		
		echo  $this->slotsTimetable[2][0], " ";
		echo  $this->weightTimetable[2][0], "<br>";
		
		echo  $this->slotsTimetable[3][0], " ";
		echo  $this->weightTimetable[3][0], "<br>";
		
		echo  $this->slotsTimetable[4][0], " ";
		echo  $this->weightTimetable[4][0], "<br>";
		
		echo  $this->slotsTimetable[5][0], " ";
		echo  $this->weightTimetable[5][0], "<br>";
		
		echo  $this->slotsTimetable[6][0], " ";
		echo  $this->weightTimetable[6][0], "<br>";
		*/
 
    }
    
    function __printTimeTable() {
       // echo "aaaaa";
       echo "<br>";
	 $sizeOf = 0;
		for ($i = 0; $i < count($this->slotsTimetable); $i++) {
			echo "<br>";
			echo "Student ";
			echo $i;
			echo "<br>";
			for ($j = 0; $j < 11; $j++) {
                            echo '<tr>';
				echo '<td>' . $this->slotsTimetable[$i][$j] . '</td>';
				echo "................";
				if ($this->slotsTimetable[$i][$j] != "null"){
					$sizeOf++;
				}
				
			}
			echo "<br>";
			for ($j = 0; $j < 11; $j++) {
				echo '<td>' . $this->sessionTimetable[$i][$j]. '</td>';
				echo "...............";
				
			}
			echo "<br>";
			for ($j = 0; $j < 11; $j++) {
				echo($this->weightTimetable[$i][$j]);
				echo ".............";
			}
			echo "<br>";
                           echo '</tr>';

		}
		echo "<br>";
		echo "filled sessions size", $sizeOf , "<br>";
        
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
            
    
    
    //put your code here
}
