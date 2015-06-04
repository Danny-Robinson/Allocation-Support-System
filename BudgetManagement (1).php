<?php
$message=$_GET["message"];
echo "$message";
$header = 'Budget Management';
include 'template-top.php';
include 'validate_login.php';

/////////////////////////
$acc_type = $_SESSION['curr_account_type'];
if ($acc_type == 1) { // if logged in as student
	echo "<br><span class=\"error\">To view this page, please <a href=\"logout_procedure.php\" title=\"Login Page\"><u>login</u></a> as an Administrator</span><br>";
	exit; // exit further script
}
////////////////////////
	
//ABOVE ADMIN VALIDATION CODE TAKEN FROM TEAM MEMBER'S 'delete_staff.php' AND MODIFIED
?>
<?php session_start();?>


<?php
$servername = "planetmeerkat.co.uk";		//set up DB connection variable
$username = "planetme_lab1ent";
$password = "gr4BFoxcan13";
$dbname = "planetme_lab-support";
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);		//connect to DB
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt1 = $conn->prepare("SELECT lr.Type, lt.Session_ID 
    						FROM Allocations a 
    						INNER JOIN Lab_Timetable lt 
    						INNER JOIN Lab_Requirements lr 
    						ON a.Session_ID=lt.Session_ID 
    						AND lt.Lab_ID = lr.Lab_ID;");
    
    
    $stmt1->setFetchMode(PDO::FETCH_ASSOC);
    $stmt1->execute();
    
    
    global $demoJan; global $demoFeb; global $demoMar; global $demoApr; global $demoMay; global $demoJun; global $demoJul; global $demoAug; global $demoAug; global $demoSep; global $demoOct; global $demoNov; global $demoDec; 
    global $TutorJan; global $TutorFeb; global $TutorMar; global $TutorApr; global $TutorMay; global $TutorJun; global $TutorJul; global $TutorAug; global $TutorAug; global $TutorSep; global $TutorOct; global $TutorNov; global $TutorDec;
    
    
    $demoJan = 0; $demoFeb = 0; $demoMar = 0; $demoApr = 0; $demoMay = 0; $demoJun = 0; $demoJul = 0; $demoAug = 0; $demoAug = 0; $demoSep = 0; $demoOct = 0; $demoNov = 0; $demoDec = 0;
    $TutorJan = 0; $TutorFeb = 0; $TutorMar = 0; $TutorApr = 0; $TutorMay = 0; $TutorJun = 0; $TutorJul = 0; $TutorAug = 0; $TutorAug = 0; $TutorSep = 0; $TutorOct = 0; $TutorNov = 0; $TutorDec = 0;

    //arrays of weekstart timeslots
    $wsArray = array('1','46','91','136','181','226','271','316','361','406','451','496','541','586','631','676','721','766','811');
    //arrays of weekstart timestamps
    $tsArray = array('1' => '1422248400','46' => '1422248400','91' =>'1423458000','136' => '1424062800','181' => '1424667600','226' => '1425272400','271' => '1425873600','316' => '1426478400','361' => '1427083200' ,'406' => '1427688000','451' => '1428292800','496' => '1428897600','541' => '1429502400','586' => '1430107200','631' => '1430712000','676' => '1431316800','721' => '1431921600','766' => '1432526400','811' => '1433131200');

    function closest($wsArray, $tS) {    //find closest week start to timeslot in question
    	sort($wsArray);
    	foreach ($wsArray as $a) {
    		if ($a >= $tS) 
    			return $a;
    	}
    	return end($wsArray);
    }
   
    
    while($row = $stmt1->fetch()) {
    	global $demoCount;
    	global $tutorCount;
    	$type = $row['Type'];
    	$sessionID = $row['Session_ID'];
    	//var_dump($type);
    	//var_dump($sessionID);
    	if (strpos($type, 'Demo') !== FALSE) {
    		$stmtTimeslot = "SELECT lt.Slot_ID FROM Lab_Timetable lt INNER JOIN Allocations a ON lt.Session_ID = a.Session_ID WHERE a.Session_ID = '$sessionID'";
    		$resultSID = $conn->query($stmtTimeslot);
   		 	$ResultSID = $resultSID->fetch();
    		$slotID= $ResultSID['Slot_ID'];
    		//var_dump($slotID);
    		$weekStart = closest($wsArray, $slotID);
    		//var_dump($weekStart);
    		if ($slotID > $weekStart)
    			{
    			$accurateStamp = ((($slotID - $weekStart)/9)*86400) + $tsArray[$weekStart];
    			}
    		else if ($slotID < $weekStart)
    			{
    			$accurateStamp = $tsArray[$weekStart] - ((($weekStart - $slotID)/9)*86400);
    			//var_dump($accurateStamp);
    			}
    			else{
    			$accurateStamp = $tsArray[$weekStart];
    		}
    		$month = gmdate('m',$accurateStamp);
    		//var_dump($month);
    		switch ($month) {
    			case "01":
    				$demoJan++;
    				break;
    			case "02":
    				$demoFeb++;
    				break;
    			case "03":
    				$demoMar++;
    				break;
    			case "04":
    				$demoApr++;
    				break;
    			case "05":
    				$demoMay++;
    				break;
    			case "06":
   					$demoJun++;
   					break;
   				case "07":
   					$demoJul++;
   					break;
   				case "08":
    				$demoAug++;
    				break;
    			case "09":
    				$demoSep++;
    				break;
    			case "10":
    				$demoOct++;
    				break;
    			case "11":
    				$demoNov++;
    				break;
    			case "12":
    				$demoDec++;
    				break;
    		}
    	}
    	if (strpos($type, 'Tutor') !== FALSE) {
    	$stmtTimeslot = "SELECT lt.Slot_ID FROM Lab_Timetable lt INNER JOIN Allocations a ON lt.Session_ID = a.Session_ID WHERE a.Session_ID = '$sessionID'";
    		$resultSID = $conn->query($stmtTimeslot);
   		 	$ResultSID = $resultSID->fetch();
    		$slotID= $ResultSID['Slot_ID'];
    		//var_dump($slotID);
    		$weekStart = closest($wsArray, $slotID);
    		//var_dump($weekStart);
    		if ($slotID > $weekStart)
    			{
    			$accurateStamp = ((($slotID - $weekStart)/9)*86400) + $tsArray[$weekStart];
    			}
    		else if ($slotID < $weekStart)
    			{
    			$accurateStamp = $tsArray[$weekStart] - ((($weekStart - $slotID)/9)*86400);
    			//var_dump($accurateStamp);
    			}
    			else{
    			$accurateStamp = $tsArray[$weekStart];
    		}
    		$month = gmdate('m',$accurateStamp);
    		switch ($month) {
    			case "01":
    				$TutorJan++;
    				break;
    			case "02":
    				$TutorFeb++;
    				break;
    			case "03":
    				$TutorMar++;
    				break;
    			case "04":
    				$TutorApr++;
    				break;
    			case "05":
    				$TutorMay++;
    				break;
    			case "06":
   					$TutorJun++;
   					break;
   				case "07":
   					$TutorJul++;
   					break;
   				case "08":
    				$TutorAug++;
    				break;
    			case "09":
    				$TutorSep++;
    				break;
    			case "10":
    				$TutorOct++;
    				break;
    			case "11":
    				$TutorNov++;
    				break;
    			case "12":
    				$TutorDec++;
    				break;
    		}
    	}
    	$demoCount =  $demoJan + $demoFeb + $demoMar + $demoApr + $demoMay + $demoJun + $demoJul + $demoAug + $demoAug + $demoSep + $demoOct + $demoNov + $demoDec;
    	$tutorCount = $TutorJan + $TutorFeb + $TutorMar + $TutorApr + $TutorMay + $TutorJun + $TutorJul + $TutorAug + $TutorAug + $TutorSep + $TutorOct + $TutorNov + $TutorDec;
    }
    
    $stmtD = "SELECT Rate FROM Pay_Data WHERE Type = 'Demo'";
    $resultD = $conn->query($stmtD);
    $stmtM = "SELECT Rate FROM Pay_Data WHERE Type = 'Marking'";
    $resultM = $conn->query($stmtM);
    $stmtT = "SELECT Rate FROM Pay_Data WHERE Type = 'Tutor'";
    $resultT = $conn->query($stmtT);
    $ResultDR = $resultD->fetch();
    $ResultMR = $resultM->fetch();
    $ResultTR = $resultT->fetch();
    $ResultDV= (float)$ResultDR['Rate'];
    $ResultMV= (float)$ResultMR['Rate'];
    $ResultTV= (float)$ResultTR['Rate'];
    
    $jan = "SELECT Jan_Budget FROM Budgeting";
    $janr = $conn->query($jan);
    $janR = $janr->fetch();
    $Jan = $janR['Jan_Budget'];
    
    $feb = "SELECT Feb_Budget FROM Budgeting";
    $febr = $conn->query($feb);
    $febR = $febr->fetch();
    $Feb = $febR['Feb_Budget'];
    
    $mar = "SELECT Mar_Budget FROM Budgeting";
    $marr = $conn->query($mar);
    $marR = $marr->fetch();
    $Mar = $marR['Mar_Budget'];
    
    $apr = "SELECT Apr_Budget FROM Budgeting";
    $aprr = $conn->query($apr);
    $aprR = $aprr->fetch();
    $Apr = $aprR['Apr_Budget'];
    
    $may = "SELECT May_Budget FROM Budgeting";
    $mayr = $conn->query($may);
    $mayR = $mayr->fetch();
    $May = $mayR['May_Budget'];
    
    $jun = "SELECT Jun_Budget FROM Budgeting";
    $junr = $conn->query($jun);
    $junR = $junr->fetch();
    $Jun = $junR['Jun_Budget'];
    
    $jul = "SELECT Jul_Budget FROM Budgeting";
    $julr = $conn->query($jul);
    $julR = $julr->fetch();
    $Jul = $julR['Jul_Budget'];
    
    $aug = "SELECT Aug_Budget FROM Budgeting";
    $augr = $conn->query($aug);
    $augR = $augr->fetch();
    $Aug = $augR['Aug_Budget'];
    
    $sep = "SELECT Sep_Budget FROM Budgeting";
    $sepr = $conn->query($sep);
    $sepR = $sepr->fetch();
    $Sep = $sepR['Sep_Budget'];
    
    $oct = "SELECT Oct_Budget FROM Budgeting";
    $octr = $conn->query($oct);
    $octR = $octr->fetch();
    $Oct = $octR['Oct_Budget'];
    
    $nov = "SELECT Nov_Budget FROM Budgeting";
    $novr = $conn->query($nov);
    $novR = $novr->fetch();
    $Nov = $novR['Nov_Budget'];
    
    $dec = "SELECT Dec_Budget FROM Budgeting";
    $decr = $conn->query($dec);
    $decR = $decr->fetch();
    $Dec = $decR['Dec_Budget'];
    
    //$DemoCount =  $demoJan + $demoFeb + $demoMar + $demoApr + $demoMay + $demoJun + $demoJul + $demoAug + $demoAug + $demoSep + $demoOct + $demoNov + $demoDec;
    //$TutorCount = $TutorFeb + $TutorMar + $TutorApr + $TutorMay + $TutorJun + $TutorJul + $TutorAug + $TutorAug + $TutorSep + $TutorOct + $TutorNov + $TutorDec;
    
    echo "<br>";
    echo " Demo count: {$demoCount}, Tutorial count: {$tutorCount}";//, Marking count: {$markingCount}";
    $cost = ($ResultDV*$demoCount)+($ResultTV*$tutorCount);//+($ResultMV*$markingCount);
    $remBudget = $Jan + $Feb + $Mar + + $Apr + $May + $Jun + $Jul + $Aug + $Sep + $Oct + $Nov + $Dec - $cost;
    echo "<br>"; echo "<br>";
    echo "Demo - &pound;{$ResultDR ["Rate"]}, Tutorial - &pound;{$ResultTR ["Rate"]}";//, Marking - &pound;{$ResultMR ["Rate"]}";
    echo "<br>"; echo "<br>";
    echo "Total cost: &pound{$cost}";
    echo "<br>"; echo "<br>";
    echo "Remaining budget: &pound{$remBudget}";
    echo "<br>"; echo "<br>";
    $dsn = null;
    global $JanBudget; global $FebBudget; global $MarBudget; global $AprBudget;global $MayBudget; global $JunBudget; global $JulBudget; global $AugBudget; global $SepBudget; global $OctBudget; global $NovBudget; global $DecBudget; 
   
   	$JanBudget = $Jan - ($demoJan*$ResultDV + $TutorJan*$ResultTV);
   	$FebBudget = $Feb - ($demoFeb*$ResultDV + $TutorFeb*$ResultTV);
   	$MarBudget = $Mar - ($demoMar*$ResultDV + $TutorMar*$ResultTV);
   	$AprBudget = $Apr - ($demoApr*$ResultDV + $TutorApr*$ResultTV);
   	$MayBudget = $May - ($demoMay*$ResultDV + $TutorMay*$ResultTV);
   	$JunBudget = $Jun - ($demoJun*$ResultDV + $TutorJun*$ResultTV);
   	$JulBudget = $Jul - ($demoJul*$ResultDV + $TutorJul*$ResultTV);
   	$AugBudget = $Aug - ($demoAug*$ResultDV + $TutorAug*$ResultTV);
   	$SepBudget = $Sep - ($demoSep*$ResultDV + $TutorSep*$ResultTV);
   	$OctBudget = $Oct - ($demoOct*$ResultDV + $TutorOct*$ResultTV);
   	$NovBudget = $Nov - ($demoNov*$ResultDV + $TutorNov*$ResultTV);
   	$DecBudget = $Dec - ($demoDec*$ResultDV + $TutorDec*$ResultTV);
   	
    }
catch(PDOException $e)
    {
    echo "Error: " . $e->getMessage();
    }
$conn = null;
	?>
									<?php //////////////BUDGET TABLE DISPLAY///////////////////?>	
<div id = "Budget Table">
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;border-color:#aaa;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#aaa;color:#333;background-color:#fff;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#aaa;color:#fff;background-color:#f38630;}
.tg .tg-z2zr{background-color:#FCFBE3}
</style>
<table class="tg">
<tr>
<th class="tg-031e"></th>
<th class="tg-031e">January</th>
<th class="tg-031e">Febuary</th>
<th class="tg-031e">March</th>
<th class="tg-031e">April</th>
<th class="tg-031e">May</th>
<th class="tg-031e">June</th>
<th class="tg-031e">July</th>
<th class="tg-031e">August</th>
<th class="tg-031e">September</th>
<th class="tg-031e">October</th>
<th class="tg-031e">November</th>
<th class="tg-031e">December</th>
</tr>
<tr>
<td class="tg-031e">Remaining Budget</td>
<td class="tg-z2zr"><?php echo "&pound", $JanBudget?></td>
<td class="tg-031e"><?php echo "&pound", $FebBudget?></td>
<td class="tg-z2zr"><?php echo "&pound", $MarBudget?></td>
<td class="tg-031e"><?php echo "&pound", $AprBudget?></td>
<td class="tg-z2zr"><?php echo "&pound", $MayBudget?></td>
<td class="tg-031e"><?php echo "&pound", $JunBudget?></td>
<td class="tg-z2zr"><?php echo "&pound", $JulBudget?></td>
<td class="tg-031e"><?php echo "&pound", $AugBudget?></td>
<td class="tg-z2zr"><?php echo "&pound", $SepBudget?></td>
<td class="tg-031e"><?php echo "&pound", $OctBudget?></td>
<td class="tg-z2zr"><?php echo "&pound", $NovBudget?></td>
<td class="tg-031e"><?php echo "&pound", $DecBudget?></td>
</tr>
<tr>
<td class="tg-031e">Demo Count</td>
<td class="tg-z2zr"><?php echo $demoJan?></td>
<td class="tg-031e"><?php echo $demoFeb?></td>
<td class="tg-z2zr"><?php echo $demoMar?></td>
<td class="tg-031e"><?php echo $demoApr?></td>
<td class="tg-z2zr"><?php echo $demoMay?></td>
<td class="tg-031e"><?php echo $demoJun?></td>
<td class="tg-z2zr"><?php echo $demoJul?></td>
<td class="tg-031e"><?php echo $demoAug?></td>
<td class="tg-z2zr"><?php echo $demoSep?></td>
<td class="tg-031e"><?php echo $demoOct?></td>
<td class="tg-z2zr"><?php echo $demoNov?></td>
<td class="tg-031e"><?php echo $demoDec?></td>
</tr>
<tr>
<td class="tg-031e">Tutorial Count</td>
<td class="tg-z2zr"><?php echo $TutorJan?></td>
<td class="tg-031e"><?php echo $TutorFeb?></td>
<td class="tg-z2zr"><?php echo $TutorMar?></td>
<td class="tg-031e"><?php echo $TutorApr?></td>
<td class="tg-z2zr"><?php echo $TutorMay?></td>
<td class="tg-031e"><?php echo $TutorJun?></td>
<td class="tg-z2zr"><?php echo $TutorJul?></td>
<td class="tg-031e"><?php echo $TutorAug?></td>
<td class="tg-z2zr"><?php echo $TutorSep?></td>
<td class="tg-031e"><?php echo $TutorOct?></td>
<td class="tg-z2zr"><?php echo $TutorNov?></td>
<td class="tg-031e"><?php echo $TutorDec?></td>
</tr>
</table>
</div>
	
							<?php //////////////CONTROL PANEL FRONT END ///////////////////?>
	
	<?php include 'updateRates.php';?>
	<div id="Update Rates">

				<h2>Updates</h2>

				<form action="" method="post">
				
				<h3>Update Rates</h3>

					<label>Update Demo Rate:</label>

					<input id="DemoID" name="UpdateD" placeholder="<?= $ResultDV?>" type="text">

					<label>Update Tutorial Rate:</label>

					<input id="TutorialID" name="UpdateT" placeholder="<?= $ResultTV?>" type="text">
					
					<!-- <label>Update Marking Rate:</label> -->

					<!-- <input id="MarkingID" name="UpdateM" placeholder="<?= $ResultMV?>" type="text">  -->

					<input name="submitRate" type="submit" value=" Update ">
					
					<br><br><br><br>
					<h3>Update Monthly Budget</h3>
					<select name= "month" id = "MonthSelectID">
  					<option value="01">January</option>
 					<option value="02">Febuary</option>
 					<option value="03">March</option>
 					<option value="04">April</option>
 					<option value="05">May</option>
 					<option value="06">Jun</option>
 					<option value="07">July</option>
 					<option value="08">August</option>
 					<option value="09">September</option>
 					<option value="10">October</option>
 					<option value="11">November</option>
 					<option value="12">December</option>
					</select>
					
					<input id="BudgetID" name="UpdateB" type="text">
					<input name="submitBudget" type="submit" value=" Update ">

					<span><?php echo $error; ?></span>
				</form>
			</div>
			
<?php include 'template-bottom.php';?>

