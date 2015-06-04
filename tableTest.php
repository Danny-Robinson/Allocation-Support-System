<?php
	$header = 'Your Header'; /* use this variable to set the header string */
	include 'template-top.php';
	
?> 
<?php
        $myarray = array("key1"=>array(1,2,3,4),
                 "key2"=>array(2,3,5),
                 "key3"=>array(3,4,5,6),
                 "key4"=>array(4,5,6,7)); //Having a key or not doesn't break it
$out  = "";
$out .= "<table>";
foreach($myarray as $key => $element){
    $out .= "<tr>";
    foreach($element as $subkey => $subelement){
        $out .= "<td>$subelement</td>";
    }
    $out .= "</tr>";
}
$out .= "</table>";

echo $out;

?> 

<?php
	include 'template-bottom.php';
?>
	