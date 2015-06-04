<?php


$hostname="planetmeerkat.co.uk";
$username="planetme_lab1ent";
$password="gr4BFoxcan13";
$dbname="planetme_lab-support";

$conn = mysql_connect($hostname, $username, $password) OR DIE('Unable to connect to database! Please try again later.');
mysql_select_db($dbname, $conn);


?>