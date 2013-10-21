<?php
/**
 * Functions used by the plugin get-your-number
*/

function gyn_generate_unique_number() {
	// generate a number if the user is not in the list already
	$number = mt_rand(1,100);
	return $number;
}

function gyn_check_email($email_to_check) {
	// in future release check if the email is not already used
	// for now return 1
	return 1;		
	
}

function gyn_mailer() {
	// send mail to the registrated participant
}
?>