<?php

include 'password_functions.php';

echo genPass();
echo "<br>";
echo genPass();
echo "<br>";
echo genPass();
echo "<br>";
echo genPass();
echo "<br>";


echo genPassHash(lHlSnepBN);
echo "<br>";
echo genPassHash(TOOIaUGL9);
echo "<br>";
echo genPassHash(KXWadXnDu);
echo "<br>";
echo genPassHash(uEEB7qPLi);
echo "<br>";


sendPassEmail("omKhan21", "lHlSnepBN", "omkhan@hotmail.co.uk" );




?>