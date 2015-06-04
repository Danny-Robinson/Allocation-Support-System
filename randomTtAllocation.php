<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of randomTtAllocation
 *
 * @author Dela
 */
class randomTtAllocation {
    
    private $countsessions;
	
	function __prevAllocation($prevSessions,$labs) {
		echo "previous allocations";
		for ($y = 0; $y < count($labs); $y++) {
         for ($i = 0; $i < 5; $i++) {
                $prevTt[$y][$i]  = "null";
         }
        }
		
		
		
		foreach($prevSessions as $x => $x_value) {
			if ($x_value[6] != null){
				$k = 0;
				while ($prevTt[$x_value[6]][$k] != "null") {
				$k++;
				}
				$prevTt[$x_value[6]][$k] = $x_value[3];
				//$prevTt[$x_value[6]][0] = 9;
			}
		
		
	}
	return $prevTt;
	
	}
	

    function __randomAllocation($labs, $student) {
		
		echo"randomTtAllocation";
        
        $array = array();
        foreach($student as $x => $x_value) {
            if ($student[$x][0] != "null" ){

            $array[] = $x;
            }
        }
        for ($y = 0; $y < count($labs); $y++) {
         for ($i = 0; $i < 5; $i++) {
                $initialTimeTable[$y][$i]  = "null";
         }
        }
        print_r($array);
        for ($i = 0; $i < count($labs); $i++) {
            $studentNumbers = $array;
            
            for ($j = 0; $j < $labs[$i][3]; $j++) {
             $a = rand(0, count($studentNumbers) - 1); 
             $initialTimeTable[$i][$j] = $studentNumbers[$a];
             unset($studentNumbers[$a]);
             $studentNumbers = array_values($studentNumbers);
             $this->countsessions++;
            }
        }
      //creates array filled with student numbers 

         return  $initialTimeTable;
    }
    
  function __getNumOfSessions() {
      return $this->countsessions;
      
  }
    

        
    
        
    
}
