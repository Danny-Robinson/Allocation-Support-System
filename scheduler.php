<?php



include ("randomTtAllocation.php");
include ("fittness.php");
class scheduler {


    
        
    //put your code here
    private $randomTimetable;
   private $timetable;
    private $weight;
    private $count;
    private $count1;
    private $newTimetable ;
    private $newWeight;
    
 
    
    function __construct($labs,$students,$prevSessions, $allLabs) {

        $this->count1 = 0;
	
		
        // create random timetable and print it 
         $r = new randomTtAllocation();
         $this->randomTimetable = $r->__randomAllocation($labs, $students); 
		 $prevTt = $r ->__prevAllocation($prevSessions,$labs);
         //echo "<br>" . " ............initial Time Table............." . "<br>" ;
		 
		 echo "<br>";
		  echo "<br>";
		   echo "<br>";
		
		 echo ".....................................................................................";
         $this->__printTt($prevTt, $this->randomTimetable);
		 echo "number of sessions", $r->__getNumOfSessions(), "<br>";
         // work out fittness for the timetable, return fit results and print 
		 
		 echo ".....................................................................................";
         $fit = new fittness( $this->randomTimetable, $labs, $students , $r->__getNumOfSessions(),$prevTt, $allLabs );
    
		
		   echo "<br>";
		 
		 $this->newWeight = $fit->__getnewWeight();
         $this->newTimetable = $fit->__getnewTimetable();;
		 
	
         //echo "<br>" . " ............newTimetable.........newWeight...." . "<br>" ;
         $this->__printTt($this->newTimetable, $this->newWeight );
		 
		 
		 // merge timetables 
		//$this->__mergeWithPreviousweek($prevTt);
		echo "<br>" . " ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////" . "<br>" ;
		
	
		 echo "<br>" . " ..........newTimetable.........newWeight...." . "<br>" ;
		 $this->__printTt($this->newTimetable, $this->newWeight );
         $this->count1++;
         // sort 
         $this->__sortTtByWeight();
         $this->timetable = $this->newTimetable;
         $this->weight =  $this->newWeight;
		 
		 
		 //............................................................................................................
		 for ($i = 0; $i < 80; $i++) {
		 echo "...........................................................new try .............................................";
		 
		 // create random timetable and print it 
       
         $this->randomTimetable = $r->__randomAllocation($labs, $students); 
		 $prevTt = $r ->__prevAllocation($prevSessions,$labs);
         //echo "<br>" . " ............initial Time Table............." . "<br>" ;
		 
		 echo "<br>";
		  echo "<br>";
		   echo "<br>";
		
		 echo ".....................................................................................";
         $this->__printTt($prevTt, $this->randomTimetable);
		 echo "number of sessions", $r->__getNumOfSessions(), "<br>";
         // work out fittness for the timetable, return fit results and print 
		 
		 echo ".....................................................................................";
         $fit = new fittness( $this->randomTimetable, $labs, $students , $r->__getNumOfSessions(),$prevTt, $allLabs );
		   echo "<br>"; 
		 $this->newWeight = $fit->__getnewWeight();
         $this->newTimetable = $fit->__getnewTimetable();;
		$this->__sortTtByWeight();
		
		  
		  
         echo "<br>" . " ......old...........new." . "<br>" ;
         $this->__printTt($this->timetable,$this->newTimetable );
         $this->__mergeTimetables($this->newTimetable, $this->newWeight );     
		
		
	echo "<br>" . " ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////" . "<br>" ;
         echo "<br>" . " ........... mergedTimetable.........newWeight...." . "<br>" ; 
         $this->__printTt($this->timetable,$this->weight );
		 $this->count1++;
		 //...........................................................................check mergered timetables ....................
		 // fittness of new timetable // here
		// $fit = new fittness( $this->timetable, $allLabs, $students , $r->__getNumOfSessions() );
		$fit->__evaluateMergerTt($this->timetable, $allLabs, $students  , $r->__getNumOfSessions(),  $this->weight);
		echo "<br>" . " ........... mergedTimetable///////////////////.........newWeight...." . "<br>" ; 
		 $this->newTimetable = $fit->__getnewTimetable();
	
         
         echo "<br>" . " ......merged.......kulled" . "<br>" ;
         $this->__printTt($this->timetable,$this->newTimetable);
         
      
       
         $this->timetable = $this->newTimetable;
         $this->weight =  $this->newWeight;
		 
		 
		 
		 }

	 echo "<br>" . " ///////////////////////////////////////////////////count/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////" . "<br>" ;
         echo "<br>" . "count" . "<br>" ; 
		   foreach($this->count as $x => $x_value) {
		echo "$x_value";
		echo "<br>";
		   }
	   echo "<br>" . "count" . "<br>" ;
	  
 // print_r($c);
         
       // header('Location: displayScheduledSessions.php'); 
         
                
    }
     // sorts the re allocated time table by weight

	 
	 
	 
     function __sortTtByWeight() {
         		// for each slot
            foreach($this->newTimetable as $l => $i_value) {
			$size = 0;
			// see how many sessions they are taking
			while ($this->newTimetable[$l][$size] != "null") {
				if ($this->newTimetable[$l][$size] == "0") {
					break;
				}
				$size++;
			}
			for ($i = 1; $i < $size; $i++) {
				$key = $this->newWeight[$l][$i];
				$key1 = $this->newTimetable[$l][$i];

				$k = $i - 1;
				while ($k >= 0 && $this->newWeight[$l][$k] < $key) {
					$this->newWeight[$l][$k + 1] = $this->newWeight[$l][$k];
					$this->newTimetable[$l][$k + 1] = $this->newTimetable[$l][$k];
					$k--;
				}
				$this->newTimetable[$l][$k + 1] = $key1;
				$this->newWeight[$l][$k + 1] = $key;
			}
		}    
     }  
       function __countSlotsAllocated() {
           $count = 0;
           foreach($this->timetable as $i => $x_value) {
               $j = 0;
             while (($this->timetable[$i][$j] != "null") && ($this->timetable[$i][$j] != "0")){
                $count++;
                $j++;
             }
           
           }
           echo "count " . $count;
           return $count;
       }
     
     function __mergeTimetables($tempTimeTable,$tempWeight) {
         
		// for each session
         foreach($this->newTimetable as $i => $i_value) {
			$j = 0;
			$k = 0;
			// while there are still students
                        
			while (($tempTimeTable[$i][$j] != "null") && ($tempTimeTable[$i][$j] != "0")) {
                            //echo $tempTimeTable[$i][$j];
				// System.out.println(timeTable[i][k]);
				// see if there is a free gap
				while ($this->timetable[$i][$k] != "null") {
					// if student is already taking that session
					if ($tempTimeTable[$i][$j] == $this->timetable[$i][$k]) {
						break;
					}
					if ($this->timetable[$i][$k] == "0") {
						$this->timetable[$i][$k] = $tempTimeTable[$i][$j];
						$this->weight[$i][$k] = $tempWeight[$i][$j];
						break;
					}
					$k++;
				}
				if ($this->timetable[$i][$k] == "null") {
					if ($tempWeight[$i][$j] < $this->weight[$i][0]) {
						$this->timetable[$i][0] = $tempTimeTable[$i][$j];
						$this->weight[$i][0] = $tempWeight[$i][$j];

					}
				}

				$j++;

			}

		}
         
         
         
         
     }
	  function __returnTimetable() {
		  return $this->timetable;
	  }

     
      function __printTt($timetable, $weight) {
           $size = 0;
           
           foreach($timetable as $x => $x_value) {
             for ($i = 0; $i < 5; $i++) {
				 if ($timetable[$x][$i] != null AND $timetable[$x][$i] != 0){
					 $size++;
				 }
				 
                echo $timetable[$x][$i] . " ";
             }
             echo " . . . . .";
             for ($i = 0; $i < 5; $i++) {
                echo $weight[$x][$i] . " ";
             }
             
             
             echo "<br>";
             }
			 
			 echo ".............................Size..................." . $size;
			$this->count[$this->count1] = $size;
         }
}
