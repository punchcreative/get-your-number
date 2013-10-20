<?php
/**
 * Functions used by the plugin get-your-number
*/

function gyn_generate_unique_number() {
	// check if the user is not in the list already
	// in dev, so for now give a value 1  indicating that it is oke to add the user 0 inicates the user already exists
	$user_check = 1;
	// generate a number if the user is not in the list already
	$number = mt_rand(1,75);
	return array($number,$user_check);
}

function gyn_mailer() {
	// send mail to the registrated participant
}
?>