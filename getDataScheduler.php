
<?php
class getDataScheduler {
	 private $servername = "planetmeerkat.co.uk";
    private $username = "planetme_lab1ent";
    private $password = "gr4BFoxcan13";
    private $dbname = "planetme_lab-support";
    private $newStudents;
    private $studentIndex;
	private $newLabs;
	private $allLabs;
	private $labsIndex;
	private $prevSessions;
	

    function __construct() {
		
		$week = $_SESSION[week];
		 echo "previous sessions";
		 	
		/*$toWrite= array
		 (
  array(33 , "CM1202", 32 ,470 ,"E","EEE" ),
  array(33, "CM1202" ,32 ,474 ,"I" ,"III"),
  array( 43 ,"CM1202", 40 ,2 ),
  array(46 ,"CM1202", 42, 479, "N",  "NNN")
  );*/
  
  
 $toWrite = $_SESSION['toWrite'];
		$this->__print($toWrite);
		echo "<br>";
		echo "next ";
      
	
        $labs = $this->__getLabs();
        $students = $this->__getPhDStudents();
        $students = $this->__getPhDAvailablity($students);
		
        $this->__createNewStudentsTimetable($students);
		$this->__createNewLabs($labs);
		echo "<br>";
		echo "....................students......................", "<br>";
        $this->__print($this->newStudents);

		echo "<br>";
		echo "<Labs ....................." . "<br>";
		$this->__print($this->newLabs);
		echo "<br>";
		// remove labs already allocated 
		$this->allLabs = $this->newLabs;
		$this->prevSessions = $this->__removeAllocatedLabs($toWrite);
		echo "< New     Labs .....................";
		$this->__print($this->newLabs);
		echo "previous sessions";
		$this->__print($this->prevSessions);
    }
	
	function __removeAllocatedLabs($allocatedLabs) {
		foreach ($allocatedLabs as $x => $x_value) {
			// change student id 
			$i = 0;
			//print_r($this->studentIndex);
			echo "searching for " , $allocatedLabs[$x][3] , "against " , $this->studentIndex[$i];
			while ($allocatedLabs[$x][3] != $this->studentIndex[$i]){
				$i++;
			}
			if ($allocatedLabs[$x][3] == $this->studentIndex[$i]){
				echo "found";
				$allocatedLabs[$x][3] = $i;
			}
			
			
			
			
			
			foreach ($this->newLabs as $y => $y_value) {
			if ($x_value[1] ==  $y_value[20]){
				if ($x_value[2] ==  $y_value[1]){
				$this->newLabs[$y][3] = $this->newLabs[$y][3] - 1;
				$allocatedLabs[$x][6] = $y;
				}
			}
		
			}
		}
		
		return $allocatedLabs;
		
		
		
	}

    function __createNewStudentsTimetable($students) {
        $j = 1;
        foreach ($students as $x => $x_value) {
            $this->newStudents[$j] = $x_value;
            $this->studentIndex[$j] = $x;
            $j++;
        }
    }
	
    function __createNewLabs($labs) {
        $j = 0;
        foreach ($labs as $x => $x_value) {
			if ($x_value[1] <= (45 * $_SESSION[week])  and  ($x_value[1] >(45 * ($_SESSION[week] - 1) ) )){
            $this->newLabs[$j] = $x_value;
			$this->newLabs[$j][1] =  $x_value[1] - (45 * ($_SESSION[week] - 1));
            $this->labsIndex[$j][0] = $x;
			$this->labsIndex[$j][1] = $x_value[20];
			$this->labsIndex[$j][2] = $x_value[1]- (45 * ($_SESSION[week] - 1));
            $j++;
			}
        }
    }


    function __getPhDStudents() {
        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Query database to find time slots for the current week.
            $matchTimeSQL = "SELECT `Account_ID`, `Forename`, `Surname`,  `Skill_Python`, `Skill_Assembly`, `Skill_Problem_Solving`, `Skill_HTML_CSS`, `Skill_PHP_SQL`, `Skill_Discrete_Maths`, `Skill_Professional_Skills`, `Skill_HCI`, `Skill_DBM_Oracle`, `Skill_Systems_Thinking_SSM`, `Skill_Java`, `Skill_Data_Structures`, `Skill_Algorithms`, `Skill_Graphics`, `Skill_C_Cpp`, `Skill_Matlab`,`Lab_Training`, `Tutorial_Training`, `Marking_Training`, `Other_Training` FROM `PHD_Students` WHERE `Status` = 'Confirmed'";
            foreach ($conn->query($matchTimeSQL) as $row) {
                $arrlength = count($row);

                for ($x = 0; $x < 22; $x++) {
                    $students[$row[0]][$x] = $row [$x + 1];
                    if ($x > 17) {
                        if ($row[$x + 1] != null) {
                            $students[$row[0]][$x] = "True";
                        } else {
                            $students[$row[0]][$x] = "False";
                        }
                    } else {
                        $students[$row[0]][$x] = $row [$x + 1];
                    }
                }
            }

            //$this->__print($students);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $students;
    }

    function __getPhDAvailablity($students) {
           try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Query database to find time slots for the current week.
            $matchTimeSQL = "SELECT `Account_ID`, `Slots_Availability` FROM `PHD_Availability`";
            foreach ($conn->query($matchTimeSQL) as $row) {

                $SlotsArray = unserialize($row["Slots_Availability"]);
				echo $SlotsArray;
                $arrlength = count($SlotsArray);
                $j = 0;
                foreach ($SlotsArray as $i => $i_value) {
                    if ($i_value == 1) {
                        $students[$row[0]][$j + 22] = "Fre";
                    } else {
                        $students[$row[0]][$j + 22] = "Bsy";
                    }
                    $j ++;
                }
               
            }
           //$this->__print($students);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $students;
    }

    function __getLabs() {
        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Query database to find time slots for the current week.
            $matchTimeSQL = "SELECT Lab_Timetable.Session_ID, Lab_Timetable.Lab_ID, Lab_Timetable.Slot_ID, Lab_Requirements.Type, Lab_Requirements.No_Staff, Lab_Requirements.Skill_Python, Lab_Requirements.Skill_Assembly, Lab_Requirements.Skill_Problem_Solving, Lab_Requirements.Skill_HTML_CSS, Lab_Requirements.Skill_PHP_SQL, Lab_Requirements.Skill_Discrete_Maths, Lab_Requirements.Skill_Professional_Skills, Lab_Requirements.Skill_HCI, Lab_Requirements.Skill_DBM_Oracle, Lab_Requirements.Skill_Systems_Thinking_SSM, Lab_Requirements.Skill_Java, Lab_Requirements.Skill_Data_Structures, Lab_Requirements.Skill_Algorithms, Lab_Requirements.Skill_Graphics, Lab_Requirements.Skill_C_Cpp, Lab_Requirements.Skill_Matlab, Lab_Requirements.Module  FROM Lab_Requirements 
			INNER JOIN Lab_Timetable ON Lab_Requirements.Lab_ID = Lab_Timetable.Lab_ID";
            foreach ($conn->query($matchTimeSQL) as $row) {
                $arrlength = count($row);
                for ($x = 0; $x < $arrlength; $x++) {
                    $labs[$row[0]][$x] = $row [$x + 1];
                }
            }
            //$this->__print($labs);
            //print_r($labs);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
		return $labs;
    }

    function __print($table) {
        echo "<br>";
        foreach ($table as $x => $element) {
            echo "Key=" . $x . "      ";
            foreach ($element as $subkey => $subelement) {
                echo $subelement . " ";
            }
            echo "<br>";
        }
    }

	 function __getNewLabs() {
         return $this->newLabs;    
     }
	 
	 function __getPrevSessions() {
         return $this->prevSessions;    
     }
	 
	function __getNewStudents() {
         return $this->newStudents;    
     }
	 function __getStudentIndex() {
         return $this->studentIndex;    
     }
	  function __getLabsIndex() {
         return $this->labsIndex;    
     }
	   function __getAllLabs() {
         return $this->allLabs;    
     }


}
?>  
