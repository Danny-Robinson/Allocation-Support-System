<?php



function genPassHash($inPass){

	$complexity = 10;

	$salt = "";

	$saltSet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";



	//Salt will be 23 characters long

	for ($i = 0; $i <= 22; $i++) {

		$saltSet = str_shuffle($saltSet);

		$salt .= $saltSet[0];

	}

	

	//blowfish encryption

	return crypt($inPass, sprintf('$2y$%02d$', $complexity) . $salt);

}



echo genPassHash("12");





?>