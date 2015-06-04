<?php
class finalConversion {
	private $servername = "planetmeerkat.co.uk";
    private $username = "planetme_lab1ent";
    private $password = "gr4BFoxcan13";
    private $dbname = "planetme_lab-support";
	private $toWrite;
	private $table;
	function __construct($ids, $sessionsToFill, $toWrite, $student){
		$this->toWrite = $toWrite;
		 //echo "<br>" . "towrite" . "<br>";
		//$this-> __print($this->toWrite);
		 //echo "<br>" . "sessionsToFill" . "<br>";
		 //$this-> __print($sessionsToFill);
		 //echo "<br>" . "student" . "<br>";
		 //$this-> __print($student);
		//echo "<br>" . "ids" . "<br>";
		// print_r($ids);
		 $sessionsToFill = $this->__getID($ids,$student,$sessionsToFill );
		 //$this-> __print($sessionsToFill);
		  $sessionsToFill = $this->__getData($sessionsToFill );
		   //$this-> __print($sessionsToFill);
		$this->toWrite = $this->__mergeLists($sessionsToFill,$this->toWrite);
		
		foreach ($this->toWrite as $x => $element) {
			ksort($this->toWrite[$x]);
			
		}
		
		
		//echo "<br>";
		//echo "<br>";
		
		
		//echo "<br>";
		//echo "<br>";
		//print_r($this->toWrite);
		//echo "<br>";
		//echo "<br>" . "count" . count($this->toWrite);
		$this->table = $this->__joinSlotData($this->toWrite);
		$_SESSION['toWrite'] = $this->toWrite;
		//echo "<br>";
		//echo"tablexxxxxxxxxxxxxxxxxxxxxxxx";
		//echo "<br>";
		//print_r($this->table[11]);
		 
	}
	
	function __mergeLists($sessionsToFill,$toWrite) {
	$count = count($toWrite);
		foreach ($sessionsToFill as $x => $element) {
			$toWrite[$count] = $element;	
			$count++;
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
	function __getID($ids,$student,$sessionsToFill ) {
		foreach ($sessionsToFill as $x => $element) {
			$sessionsToFill[$x][3]  = $ids[$x];
	
		}
		return $sessionsToFill;
	}
		function __getdata($toWrite) {
		
		foreach($toWrite as $x => $x_value) {
		//echo $x_value[3];
		$a = $x_value[3];
		try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Query database to find time slots for the current week.
          $matchTimeSQL = "SELECT `Forename` FROM `PHD_Students` WHERE `Account_ID` = '$a'";
		   $matchTimeSQLb = "SELECT `Surname` FROM `PHD_Students` WHERE `Account_ID` = '$a'";
			foreach ($conn->query($matchTimeSQL) as $row) { 
				$toWrite[$x][4]	= $row[0];
			//echo $row[0];
			//echo "<br>";
			//echo $row;
			}
			foreach ($conn->query($matchTimeSQLb) as $row) { 
				$toWrite[$x][5]	= $row[0];
			//echo $row[0];
			//echo "<br>";
			//echo $row;
			}
			
            //$this->__print($students);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
		ksort($toWrite[$x]);
		}
		//$this->__print($toWrite);
       //print_r($toWrite);
	   return $toWrite;
    }
	function __joinSlotData($toWrite) {
		for ($i = 1; $i < 46; $i++) {
			$j = 0;
			$table[$i]  = null;
		foreach($toWrite as $x => $x_value) {
			
			if ($x_value[2] == $i){
				
				$table[$x_value[2]][$j] = $x_value;
				$j++;
			}
		}
		
			
		}
		//$this->__nprint($table);
		//echo"why";
		//print_r($table);
		return $table;
	}
	function __getScheduledTimetable() {
		return $this->toWrite;
	}
	function __getTable() {
		return $this->table;
	}
	
	
}