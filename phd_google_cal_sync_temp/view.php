<?php
session_start();
include ("timeTableView.php");
class view {
	private $servername = "planetmeerkat.co.uk";
    private $username = "planetme_lab1ent";
    private $password = "gr4BFoxcan13";
    private $dbname = "planetme_lab-support";
	
	function __construct($toWrite, $sessionsToFill,$student, $studentIndex) {
	//echo "student ";
	//print_r($studentIndex);
	echo "<br>";
	echo "Week " . $_SESSION[week] ." Scheduled, Please Select Students For The Following Sessions";
	echo "<br>";
	//$this->__print($student);
	$_SESSION[student] = $student;
	//$this->__print($toWrite);

	$toWrite = $this->__getdata($toWrite);// adds sudents name 
	$_SESSION[toWrite] = $toWrite;
	$table = $this->__joinSlotData($toWrite);
	ksort($table);
	
	
	$table = $this->__sortslot($table);
	//echo "table";
	//echo "<br>";
	//$this->__print($table);
	
	//echo "<br>";
	//echo "sessionsToFill";
	//echo "<br>";
	
	$_SESSION['sessionsToFill'] = $sessionsToFill;

	

	$tt =  new timeTableView($table);
	//$this->__nprint($table);
	//echo count($sessionsToFill);
	//echo "<br>";
	//echo "...";
	//echo "<br>";
	$i = 0;
	$st = "student1";
	$availableStudents = $this->__studentsAvailable($sessionsToFill[2][2], $student);






?><html>
<form action="#" method="post">

<?php
	if (count($sessionsToFill) > 0){
		echo "<br>";
		echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	
	
	if (count($sessionsToFill) == 1){
?>
	<select name="student1" ID="student1"  onChange="selectButton()" >
<?php 
	}
	if (count($sessionsToFill) > 1){
?>
	<select name="student1" ID="student1"  onChange="changeStudent1()"><?php

	}
	
	
	$this->__options($sessionsToFill,$student,$i,$studentIndex );

	?></select><?php
	$i++;
	}
	
	
	if (count($sessionsToFill) > 1){
echo "<br>";
echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;

		if (count($sessionsToFill) == 2){
?>
	<select name="student2" ID="student2"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 2){
?>
	<select name="student2" ID="student2" onChange="changeStudent2()" disabled = "true">
<?php 
	}
	$this->__options($sessionsToFill,$student,$i,$studentIndex );
	?></select><?php
	$i++;
	}?>
	
	
	
	
	
	<?php
	if (count($sessionsToFill) > 2){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	if (count($sessionsToFill) == 3){
?>
	<select name="student3" ID="student3"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 3){
?>
	<select name="student3" ID="student3" onChange="changeStudent3()" disabled = "true">
<?php 
	}
	$this->__options($sessionsToFill,$student,$i,$studentIndex );
	?></select><?php
	$i++;
	}
	
	
	
	
	
	
	
	if (count($sessionsToFill) > 3){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	
if (count($sessionsToFill) == 4){
?>
	<select name="student4" ID="student4"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 4){
?>
	<select name="student4" ID="student4" onChange="changeStudent4()" disabled = "true">
<?php 
	}
	$this->__options($sessionsToFill,$student,$i,$studentIndex );
	?></select><?php
	$i++;
	}
	
	
	
	
	
	
	if (count($sessionsToFill) > 4){
	echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
if (count($sessionsToFill) == 5){
?>
	<select name="student5" ID="student5"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 5){
?>
	<select name="student5" ID="student5" onChange="changeStudent5()" disabled = "true">
<?php 
	}
echo "<br>";
echo "<br>";
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ] . "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select>
	
	<?php
	}
	if (count($sessionsToFill) > 5){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	
	
if (count($sessionsToFill) == 6){
?>
	<select name="student6" ID="student6"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 6){
?>
	<select name="student6" ID="student6" onChange="changeStudent6()" disabled = "true">
<?php 
	}

	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ] . "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select>
	
	<?php
	}
	if (count($sessionsToFill) > 6){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
if (count($sessionsToFill) == 7){
?>
	<select name="student7" ID="student7"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 7){
?>
	<select name="student7" ID="student7" onChange="changeStudent7()" disabled = "true">
<?php 
	}

	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ] . "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select>
	
	
	
		<?php
		}
	if (count($sessionsToFill) > 7){
		echo "<br>";
		echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
if (count($sessionsToFill) == 8){
?>
	<select name="student8" ID="student8"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 8){
?>
	<select name="student8" ID="student8" onChange="changeStudent8()" disabled = "true">
<?php 
	}
echo "<br>";
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ] . "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select>
	
		<?php
		}
	if (count($sessionsToFill) > 8){
		echo "<br>";
		echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
if (count($sessionsToFill) == 9){
?>
	<select name="student9" ID="student9"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 9){
?>
	<select name="student9" ID="student9" onChange="changeStudent9()" disabled = "true">
<?php 
	}
echo "<br>";
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ] . "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	
	?></select>
	<?php }?>
	
	
	
		<?php
	if (count($sessionsToFill) > 9){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 10){
?>
	<select name="student10" ID="student10"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 10){
?>
	<select name="student10" ID="student10" onChange="changeStudent10()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	

	
		
	<?php
	if (count($sessionsToFill) > 10){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 11){
?>
	<select name="student11" ID="student11"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 11){
?>
	<select name="student11" ID="student11" onChange="changeStudent11()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
			
	<?php
	if (count($sessionsToFill) > 11){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 12){
?>
	<select name="student12" ID="student12"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 12){
?>
	<select name="student12" ID="student12" onChange="changeStudent12()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
	
			
	<?php
	if (count($sessionsToFill) > 12){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 13){
?>
	<select name="student13" ID="student13"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 13){
?>
	<select name="student13" ID="student13" onChange="changeStudent13()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
			
	<?php
	if (count($sessionsToFill) > 13){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 14){
?>
	<select name="student14" ID="student14"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 14){
?>
	<select name="student14" ID="student14" onChange="changeStudent14()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
			
	<?php
	if (count($sessionsToFill) > 14){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 15){
?>
	<select name="student15" ID="student15"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 15){
?>
	<select name="student15" ID="student15" onChange="changeStudent15()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
	
			
	<?php
	if (count($sessionsToFill) > 15){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 16){
?>
	<select name="student16" ID="student16"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 16){
?>
	<select name="student16" ID="student16" onChange="changeStudent16()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
	
	
			
	<?php
	if (count($sessionsToFill) > 16){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 17){
?>
	<select name="student17" ID="student17"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 17){
?>
	<select name="student17" ID="student17" onChange="changeStudent17()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
			
	<?php
	if (count($sessionsToFill) > 17){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 18){
?>
	<select name="student18" ID="student18"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 18){
?>
	<select name="student18" ID="student18" onChange="changeStudent18()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
			
	<?php
	if (count($sessionsToFill) > 18){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 19){
?>
	<select name="student19" ID="student19"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 19){
?>
	<select name="student19" ID="student19" onChange="changeStudent19()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
	
	
			
	<?php
	if (count($sessionsToFill) > 18){
		echo "<br>";
	echo "<br>";
	echo "select Student For  Lab " . $sessionsToFill[$i][1]. " in slot ".$sessionsToFill[$i][2] ;
	$i++;
	if (count($sessionsToFill) == 19){
?>
	<select name="student19" ID="student19"  onChange="buttonChange()" disabled = "true">
<?php 
	}
	if (count($sessionsToFill) > 19){
?>
	<select name="student19" ID="student19" onChange="changeStudent19()" disabled = "true">
<?php 
	}
	for ($j = 0; $j < count($student); $j++) {
	?><option><?php 
	echo $studentIndex[$j ]. "  " . $student[$j][0] . "  " . $student[$j][1] ; 
	?></option><?php
	}
	echo "<br>";
	?></select><?php
	}?>
	
	
	
	
	
	<?php
	if (count($sessionsToFill) == 0){
	?>
	<br>
	<input type="submit" id = "submitButton" name="submit" value="Submit Students"/>
	<?php }else{
		?>
	<br>
	<input type="submit" id = "submitButton" name="submit" value="Submit Students" disabled = "true"/>
	<?php
	}?>
	
	</form>
			<script language="JavaScript">

			

			function changeStudent1() {
			   document.getElementById('student2').disabled=false; 
				}
				function changeStudent2() {
			   document.getElementById('student3').disabled=false; 
				}
				function changeStudent3() {
				
			   document.getElementById('student4').disabled=false; 
				}
				function changeStudent4() {
				
			   document.getElementById('student5').disabled=false; 
				}
				function changeStudent5() {
				
			   document.getElementById('student6').disabled=false; 
				}
				function changeStudent6() {
				
			   document.getElementById('student7').disabled=false; 
				}
				function changeStudent7() {
				
			   document.getElementById('student8').disabled=false; 
				}
				function changeStudent8() {
				 
			   document.getElementById('student9').disabled=false; 
				}
				function changeStudent9() {
			   document.getElementById('student10').disabled=false; 
				}
				function changeStudent10() {
			   document.getElementById('student11').disabled=false; 
				}
				function changeStudent11() {
			   document.getElementById('student12').disabled=false; 
				}
				function changeStudent12() {
			   document.getElementById('student13').disabled=false; 
				}
				function changeStudent13() {
			   document.getElementById('student14').disabled=false; 
				}
				function changeStudent14() {
			   document.getElementById('student15').disabled=false; 
				}
				function changeStudent15() {
			   document.getElementById('student16').disabled=false; 
				}
				function changeStudent16() {
			   document.getElementById('student17').disabled=false; 
				}
				function changeStudent17() {
			   document.getElementById('student18').disabled=false; 
				}
				function changeStudent18() {
			   document.getElementById('student19').disabled=false; 
				}
				function changeStudent19() {
			   document.getElementById('student20').disabled=false; 
				}
				function buttonChange() {
				document.getElementById('submitButton').disabled=false; 
				}
			

			   

			

			

		</script>

	


<?php
	
if(isset($_POST['submit'])){
	if (count($sessionsToFill) > 0){
$selected_val = $_POST['student1'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[0] = $id[0]. $word[$i];
	$i++;
}



echo "You have selected :" . $id[0] . '</br>';  // Displaying Selected Value
	}
	if (count($sessionsToFill) > 1){
$selected_val = $_POST['student2'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[1] = $id[1]. $word[$i];
	$i++;
}


echo "You have selected :" .$id[1]. '</br>';  // Displaying Selected Value
	}
	if (count($sessionsToFill) > 2){
$selected_val = $_POST['student3'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[2] = $id[2]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[2] . '</br>';  // Displaying Selected Value
	}
	if (count($sessionsToFill) > 3){
$selected_val = $_POST['student4'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[3] = $id[3]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[3] . '</br>'; // Displaying Selected Value
	}
	if (count($sessionsToFill) > 4){
$selected_val = $_POST['student5'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[4] = $id[4]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[4] . '</br>';  // Displaying Selected Value
	}
	if (count($sessionsToFill) > 5){
$selected_val = $_POST['student6'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[5] = $id[5]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[5] . '</br>';  // Displaying Selected Value
	}
	if (count($sessionsToFill) > 6){
$selected_val = $_POST['student7'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[6] = $id[6]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[6] . '</br>'; // Displaying Selected Value
	}
	if (count($sessionsToFill) > 7){
$selected_val = $_POST['student8'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[7] = $id[7]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[7] . '</br>';  // Displaying Selected Value
	}
	
	
	if (count($sessionsToFill) > 8){
$selected_val = $_POST['student9'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[8] = $id[8]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[8]. '</br>';  // Displaying Selected Value
	}
	
	if (count($sessionsToFill) > 9){
$selected_val = $_POST['student10'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[9] = $id[9]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[9] . '</br>';  // Displaying Selected Value

	}



	if (count($sessionsToFill) > 10){
$selected_val = $_POST['student10'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[10] = $id[10]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[10]. '</br>';  // Displaying Selected Value
	}

	if (count($sessionsToFill) > 11){
$selected_val = $_POST['student11'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[11] = $id[11]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[11]. '</br>';  // Displaying Selected Value
	}

	if (count($sessionsToFill) > 12){
$selected_val = $_POST['student12'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[12] = $id[12]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[12]. '</br>';  // Displaying Selected Value
	}

	if (count($sessionsToFill) > 13){
$selected_val = $_POST['student13'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[13] = $id[13]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[13]. '</br>';  // Displaying Selected Value
	}
	
		if (count($sessionsToFill) > 14){
$selected_val = $_POST['student14'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[14] = $id[14]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[14]. '</br>';  // Displaying Selected Value
	}

	
		if (count($sessionsToFill) > 16){
$selected_val = $_POST['student15'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[16] = $id[16]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[16]. '</br>';  // Displaying Selected Value
	}
	
			if (count($sessionsToFill) > 17){
$selected_val = $_POST['student15'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[17] = $id[17]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[17]. '</br>';  // Displaying Selected Value
	}
	
			if (count($sessionsToFill) > 15){
$selected_val = $_POST['student15'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[15] = $id[15]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[15]. '</br>';  // Displaying Selected Value
	}



		if (count($sessionsToFill) > 18){
$selected_val = $_POST['student15'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[18] = $id[18]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[18]. '</br>';  // Displaying Selected Value
	}
	
			if (count($sessionsToFill) > 19){
$selected_val = $_POST['student15'];  // Storing Selected Value In Variable
$word = str_split($selected_val);
$i = 0;
while ($word[$i] != " "){
	$id[19] = $id[19]. $word[$i];
	$i++;
}
echo "You have selected :" .$id[19]. '</br>';  // Displaying Selected Value
	}







	
	$_SESSION['ids'] = $id;
header('Location: session3.php');
	}
	
?></html><?php



	
	

	}
	function __joinSlotData($toWrite) {
		for ($i = 1; $i < 46; $i++) {
			$j = 0;
			$table[$i]  = null;
		foreach($toWrite as $x => $x_value) {
			
			if ($x_value[2] != $i){
				echo $x_value[2] . " ";
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
		}
		//$this->__print($toWrite);
       //print_r($toWrite);
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
		return $availableStudents;   
    }
	
	function __options($sessionsToFill,$student,$i,$studentIndex ) {
		
	$availableStudents = $this->__studentsAvailable($sessionsToFill[$i][2], $student);
	for ($j = 0; $j < count($availableStudents); $j++) {
	?><option><?php 
	echo $studentIndex[$availableStudents[$j]]. "  " . $student[$availableStudents[$j]][0] . "  " . $student[$availableStudents[$j]][1] ; 
	?></option><?php
	}
	echo "<br>";

    }


	
	
		
	function __print($toWrite) {
        echo "<br>";
		echo "<br>";
        foreach ($toWrite as $x => $element) {
            echo "Key=" . $x . "      ";
            foreach ($element as $subkey => $subelement) {
                echo $subelement . " ";
            }
            echo "<br>";
        }
    }
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

	
	

}