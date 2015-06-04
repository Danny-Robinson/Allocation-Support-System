
<?php
include ("view.php");
class nextSessionPrep {
	function __construct($timetable, $labsIndex,  $studentIndex,$student) {
		$toWrite = $this-> __convertToDbView($timetable);
		$sessionsToFill = $this->__labsTobBeAssigned($timetable);
		$sessionsToFill = $this->__getSessionToFillData($sessionsToFill, $labsIndex);
		$this-> __print($sessionsToFill);
		$this-> __print($toWrite);
		$toWrite = $this-> __convert($toWrite, $labsIndex,  $studentIndex);
		$_SESSION['toWrite'] = null;
		 echo "<br>" . "towrite" . "<br>";
		 $this-> __print($toWrite);
		 echo "<br>" . "sessionsToFill" . "<br>";
		 $this-> __print($sessionsToFill);
		 echo "<br>" . "student" . "<br>";
		 $this-> __print($student);
		 $_SESSION['studentIndex'] = $studentIndex;
		$_SESSION['student'] = $student;
		$_SESSION['sessionsToFill'] = $sessionsToFill;
		$_SESSION['toWrite'] = $toWrite;
		 echo "<br>" . "towrite" . "<br>";
		 $this-> __print($_SESSION['toWrite']);
		//$v = new view($toWrite,$sessionsToFill,$student);
		//$this-> __writeData($toWrite);
	}
	function __getSessionToFillData($sessionsToFill, $labsIndex) {
		echo "to fill x";
		foreach ($sessionsToFill as $x => $element) {
			echo $labsIndex[$element[0]][1];
			$sessionsToFill[$x][0] = $labsIndex[$element[0]][0];
			$sessionsToFill[$x][1] = $labsIndex[$element[0]][1];
			$sessionsToFill[$x][2] = $labsIndex[$element[0]][2];

		}
		print_r($sessionsToFill);
		return $sessionsToFill;
	}

	

	function __convertToDbView($timetable) {
		$j = 0;
		 foreach ($timetable as $x => $element) {
            foreach ($element as $subkey => $subelement) {
				
				if ($subelement != 0){
					$toWrite[$j][0] = $x;
					$toWrite[$j][1] = $subelement;
					
					$j++;
				}
		
			}
		 }
		 return $toWrite;
	}
	function __labsTobBeAssigned($timetable) {
		$i = 0;
		echo "<br>";
		echo "<br>";
		foreach ($timetable as $x => $element) {
			foreach ($element as $subkey => $subelement) {
				echo $subelement;
				if ($subelement == "0"){
					$slotsToFill[$i][0] = $x;
					$i ++;
				}
				
			}
			
		}
		echo"slots to fill";
		print_r($slotsToFill);
		return $slotsToFill;
		
		
	}
	
	function __convert($toWrite, $labsIndex,  $studentIndex) {
		print_r($labsIndex);
		foreach ($toWrite as $x => $element) {
			// lab 
			$toWrite[$x][0] = $labsIndex[$element[0]][0];
			//$toWrite[$x][0] = $element[0];
			$toWrite[$x][1] = $labsIndex[$element[0]][1];
			$toWrite[$x][3] = $studentIndex[$element[1]];
			echo "bb" .$labsIndex[$element[0]][1]. " ";
			//$toWrite[$x][2] = $labsIndex[$element[0]][1];
			echo "aa" .$labsIndex[$element[0]][2]. " ";
			$toWrite[$x][2] = $labsIndex[$element[0]][2];

		}
		return $toWrite;
	}


    function __print($table) {
        echo "<br>";
		echo "<br>";
        foreach ($table as $x => $element) {
            echo "Key=" . $x . "      ";
            foreach ($element as $subkey => $subelement) {
                echo $subelement . " ";
            }
            echo "<br>";
        }
    }

}