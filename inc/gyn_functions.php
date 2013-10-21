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

function gyn_mailer($name,$email,$number,$from_name,$from_email,$eventname) {
	// send mail to the subscriber
	$headers[] = 'From: ' .$from_name . ' <' . $from_email . '>';
	$headers[] = 'Cc: ' .$from_name . ' <' . $from_email . '>';
	$to = $name . '<' . $email .'>';
	$subject = 'Your ' . $eventname .  'subscription number';
	$message = "Dear " . $name . "\n\nThis your subsctiption number for the event " . $eventname . " is " . $number . "\n\nThanks for subscribing.\nYou will soon be informed is you had a lucky number.\n\nBest regards,\n" . $from_name;
	wp_mail( $to, $subject, $message, $headers );
}
?>