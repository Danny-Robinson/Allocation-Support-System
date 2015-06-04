<?php
	session_start(); // Starting Session
	$error=''; // Variable To Store Error Message
	$connection = mysql_connect("planetmeerkat.co.uk", "planetme_lab1ent", "gr4BFoxcan13");
	$servername = "planetmeerkat.co.uk";		//set up DB connection variable
	$username = "planetme_lab1ent";
	$password = "gr4BFoxcan13";
	$dbname = "planetme_lab-support";
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);		//connect to DB
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	///////UPDATE BUDGET///////
	
	if (isset($_POST['submitBudget'])) {
		if (empty($_POST['UpdateB']) || !preg_match("/^[0-9]+(?:\.[0-9]{2})?$/",$_POST["UpdateB"])) {  //cannot be empty and must match regex
			?>
			<script type="text/javascript">confirm("Please enter a budget in the correct format");</script>
			<?php 
						
	}
	else
	{
		{
			header("Location: http://lab-support.co.uk/budgetManagement.php");
		}
		$budgetVal= $_POST["UpdateB"];
		$budgetVal = stripslashes($budgetVal);
		$budgetVal = mysql_real_escape_string($budgetVal);
		
		$updateBudget = $_POST['month'];
		switch ($updateBudget) {
    			case "01":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Jan_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "02":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Feb_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "03":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Mar_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "04":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Apr_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "05":
    				$updateB = $conn->prepare("UPDATE Budgeting SET May_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "06":
   					$updateB = $conn->prepare("UPDATE Budgeting SET Jun_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
   					break;
   				case "07":
   					$updateB = $conn->prepare("UPDATE Budgeting SET Jul_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
   					break;
   				case "08":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Aug_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "09":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Sep_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "10":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Oct_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "11":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Nov_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
    			case "12":
    				$updateB = $conn->prepare("UPDATE Budgeting SET Dec_Budget= '$budgetVal';");
    				$updateB->execute();
    				mysql_close($connection);
    				break;
			}
		}
	}
	///////UPDATE RATES///////
	
	if (isset($_POST['submitRate'])) {
		if (empty($_POST['UpdateD']) || empty($_POST['UpdateT'])  || !preg_match("/^[0-9]+(?:\.[0-9]{2})?$/",$_POST["UpdateD"]) || !preg_match("/^[0-9]+(?:\.[0-9]{2})?$/",$_POST["UpdateD"])) {  //cannot be empty and must match regex
			//echo "<font color='red'><b>Please enter a rate for both session types in the correct format</font></b><br>";
			?>
			<script type="text/javascript">confirm("Please enter a rate for both session types in the correct format");</script>
			<?php 
			//$test = $_POST['UpdateD'];
			//echo $test;
		}	
		else
		{
			{
				header("Location: http://lab-support.co.uk/budgetManagement.php");
			}
			
				$UpdateD = $_POST["UpdateD"];	 
				$UpdateT = $_POST["UpdateT"];    
				
				$sql = "UPDATE Pay_Data SET Rate = '$UpdateD' WHERE Type = 'Demo';";
				$UpdateDemo = $conn->prepare($sql);
				$UpdateDemo->execute();
				
				$sql = "UPDATE Pay_Data SET Rate = '$UpdateT' WHERE Type = 'Tutor';";
				$UpdateDemo = $conn->prepare($sql);
				$UpdateDemo->execute();
				
			mysql_close($connection); // Closing Connection
		}
	}
?>