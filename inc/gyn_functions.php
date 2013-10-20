<?php
/**
 * Functions used by the plugin get-your-number
*/

function gyn_generate_unique_number() {
	$number = mt_rand(2,75);
	return $number;
}

function gyn_mailer() {
	// send mail to the registrated participant
}
?>