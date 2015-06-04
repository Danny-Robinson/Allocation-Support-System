<?php
	$header = 'Lab/Tutorial Timetabling';
	include 'template-top.php';
	include 'convert_week_number.php';
?>

This page allows labs and tutorials to be added to the timetabling system.

<form>
	<input class="weekSelect" type="radio" name="labType" value="lab" id="labBox1" checked="checked"/><label for="labBox1">Lab</label>
	<input class="weekSelect" type="radio" name="labType" value="tutorial" id="labBox2"/><label for="labBox2">Tutorial</label>
</form>

<?php
	include 'template-bottom.php';