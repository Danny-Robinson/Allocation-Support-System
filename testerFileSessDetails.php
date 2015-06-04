<?php

include 'session_details_functions.php';

include 'db_connect.php';

echo print_r(getSessionDetails(263, $conn));

echo print_r(getSessionDetails(284, $conn));





?>