<?php
//function to create a random password of length 8
function genPass(){
	$passLength = 8;
	//list of characters from which password is created from
	$passSet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$createdPass = "";
	

	for ($i = 0; $i <= $passLength; $i++) {
		//shuffling the character set
		$passSet = str_shuffle($passSet);
		//choosing the first character of the shuffled character set
		$createdPass .= $passSet[0];
		
	}
	return $createdPass;
}


//creating a hashed password using a plain text password
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





//to validate a plain text password by comparing it to a hashed password
function validatePass($inPass, $inHashedPass){
	
	if(crypt($inPass, $inHashedPass) == $inHashedPass){
		
		return true;
	}
	else {
		
		return false;
	}

}

function sendPassEmail($inUsername, $inPass, $inEmail) {
	$message = "Hi, Your account username is ".$inUsername." and your account password is ".$inPass;
	mail($inEmail, "Your Cardiff Uni Lab Support Password", $message);
	echo "Email sent";
}


?>