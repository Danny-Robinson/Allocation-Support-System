<?php
class manuallyPickStudentsFunctionality {
	
	function __sortslot($toWrite) {
        	// for each student
        foreach($toWrite as $l => $x_value) {
            $element=$toWrite[$l][3];
			//echo $element;
			$record = $toWrite[$l];
            $j=$i;
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
	
	
	function __studentsAvailable($slot, $students) {
		$availableStudents = null;
				$i = 0;
				$x =1;
			while($students[$x][1] != null) {	
			if ($students[$x][$slot + 19] == "Fre") {
				
				$availableStudents[$i] = $x;
				
				$i++;
				
			}
			$x++;
		}
	
		if (count($availableStudents) == 0){
			
			$i = 0;
			while($students[$i + 1][1] != null) {	
				$availableStudents[$i] = ($i + 1);
				$i++;
			}	
		}
		$availableStudents[0] = 1;
		$availableStudents[1] = 2;
		return $availableStudents;   
    }
	
}