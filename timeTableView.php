	<?php
class timeTableView {	
function __construct($toWrite){
	
	
	
	
	
		echo '<table cellpadding="0" cellspacing="0" class="db-table">';
 echo "<tr><th></th><th>0900-0950</th><th>1000-1050</th><th>1110-1200</th><th>1210-1300</th><th>1310-1400</th><th>1410-1500</th><th>1510-1600</th><th>1610-1700</th><th>1710-1800</th></tr>";
    
	
        //foreach ($toWrite as $x => $element) {
		for ($x = 1; $x <= 45; $x++) {
			if ($x  == 1 ){
				echo '<tr>';
				echo'<th>Mon</th>';
			}
			if ($x  == 10 ){
				echo '<tr>';
				echo'<th>Tue</th>';
			}
			if ( $x  == 19 ){
				echo '<tr>';
				echo'<th>Wed</th>';
			}
			if ( $x  == 28){
				echo '<tr>';
				echo'<th>Thur</th>';
			}
			if ($x  == 37){
				echo '<tr>';
				echo'<th>Fri</th>';
			}
			echo '<td>';
			echo "Slot " .$x;
            foreach ($toWrite[$x] as $subkey => $subelement) {
				
				echo '<br>';
				//foreach ($subelement as $nkey => $nelement) {
					
                echo $subelement[1] . " - " . $subelement[4] . " " . $subelement[5];
				//}
				echo '</br>';
            }
            echo '</td>';
        }
		}
		}   