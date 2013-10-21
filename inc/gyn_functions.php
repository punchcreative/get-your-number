<?php
/**
 * Functions used by the plugin get-your-number
*/

function gyn_generate_unique_number() {
	// generate a number if the user is not in the list already
	$number = mt_rand(1,75);
	return $number;
}

function gyn_check_email($email_to_check) {
	// check if the email is valid
	if ( is_email( $email_to_check ) ) {
		return 1;		
	} else {
		return 0;
	}
}

function gyn_check_if_registred() {
	// check if the user is not in the list already
	// in dev, so for now give a value 1 indicating that it is oke to add the user 0 inicates the user already exists
	$user_check = 1;
}

function gyn_mailer() {
	// send mail to the registrated participant
}
?>