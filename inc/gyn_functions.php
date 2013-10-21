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

function gyn_mailer($name,$email,$number,$from_name,$eventname) {
	// send mail to the subscriber
	$gyn_admin_email = get_option( 'admin_email' );
	$headers[] = 'From: ' .$from_name . ' <' . $gyn_admin_email . '>';
	$headers[] = 'Cc: ' .$from_name . ' <' . $gyn_admin_email . '>';
	$to = $name . '<' . $email .'>';
	$subject = 'Your subscription number is ' . $number;
	$message = "Dear " . $name . ",\n\n" . $number . " is your subscription number for the event " . $eventname . ".\n\nYou will soon be informed if you have a lucky number.\n\nThank you for subscribing.";
	
	// send the mail and set $gyn_mail with a boolean to test sending went well
	$gyn_mail = wp_mail( $to, $subject, $message, $headers );
	
	if ( $gyn_mail ) {
		$gyn_mail_message = 'An email has been sent to you to confirm your subscription';
	} else {
		$gyn_mail_message = 'Sending an email failed, please remember your number';
	}
	return $gyn_mail_message;
}
?>