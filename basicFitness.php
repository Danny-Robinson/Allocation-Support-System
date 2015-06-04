<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of fitness
 *
 * @author Dela
 */
class basicFitness {
    
    private $students;
    private $Labs;
    private $initialTt;
    private $weight;
    
    function __construct($initialTt, $Labs, $Students) {
       
        $this->students = $Students;
	$this->Labs = $Labs;
	$this->initialTt = $initialTt;
        $this->__howFit();
        
    }
     function __howFit() {
         
         // set up weight table 
         for ($y = 0; $y < count($this->initialTt); $y++) {
            for ($i = 0; $i < 5; $i++) {
               $this->weight[$y][$i]  = 0;
            }
        }
       
         
         foreach($this->Labs as $x => $x_value) {
             for ($j = 0; $j < 5; $j++) {
                $student = $this->initialTt[$x][$j];
		$slot = $this->Labs[$x][1];
                if ($student != "null") {
			$this->weight[$x][$j] = $this->weight[$x][$j] + $this->__available($student, $slot);
			$this->weight[$x][$j] =  $this->weight[$x][$j] + $this->__skillSet($student, $x);
			$this->weight[$x][$j] = $this->weight[$x][$j] + $this->__training($student, $x);	
		}
                 
         
             }
         }
       
        //$this->__printWeight();
           
         
     }
     function __training($student, $i2) {
		  echo $this->Labs[$i2][2];
         if ($this->Labs[$i2][2] == "Demo") {
			 echo $this->students[$student][18];
		// see if the student has that training
		if ($this->students[$student][18] == "True") {
			return 1;
		}
	}
	if ($this->Labs[$i2][2] == "Tutor") {
		echo "true";
		// see if the student has that training
		if ($this->students[$student][19] == "True") {
				return 1;
		}
	}
	if (($this->Labs[$i2][2] =="Marking")) {
            // see if the student has that training
		if ($this->students[$student][20] == "True") {
				return 1;
		}
	}
	echo " .......................................80 from here..........................";
		return 80;
   
     }

     	// is this student free in this slot ??
	function __available($student, $slot) {
		if ($this->students[$student][$slot+ 19] == "Fre") {
			return 1;
		}
		echo " .......................................80 availiable..........................";
		return 80;
	}
        
        
        
        	// has the student the correct skill set 
	function __skillSet($student, $i2) {
		// for each skill
		for ($i = 4; $i < 19; $i++) {
			// if the skill is 1
			if ($this->Labs[$i2][$i] == "1") {
				// see if the student has that skill
				if ($this->students[$student][$i - 1] == "2") {
					return 1;

				}
				if ($this->students[$student][$i - 1] == "1") {
					return 10;
				}
				if ($this->students[$student][$i - 1] == "0") {
					return 80;
					echo " .......................................80 skillfrom here..........................";
				}
			}
		}
		
		return 0;
	}
        
        function __printWeight() {
       
       echo "<br>";
       echo "................weightings.................." . "<br>";
        foreach( $this->weight as $x => $x_value) {
             for ($i = 0; $i < 5; $i++) {
                echo  $this->weight[$x][$i] . " ";
             }
             echo "<br>";
             }   
   }
        
        
        
        
      function __getweight() {
	        return $this->weight;
        }

    
    //put your code here
}
