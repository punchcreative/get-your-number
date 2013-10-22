<?php
/**
 * Functions used by the plugin get-your-number
*/

/**
 * function for generating the numbers
*/
function gyn_generate_unique_number() {
	$gyn_options = get_option( 'gyn_options' );
	// generate a number inbetween the numbers set on activation of the plugin
	$number = mt_rand($gyn_options['gyn_min_nr'],$gyn_options['gyn_max_nr']);
	return $number;
}

/**
 * function called after users entry is submitted looking in dbase if the user doesn't exist already
*/
function gyn_check_email($email_to_check) {
	// in future release check if the email is not already used
	// for now return 1
	return 1;		
	
}

/**
 * function called after users entry is checked and ready to go
*/
function gyn_mailer($name,$email,$number,$from_name,$eventname) {
	$gyn_options = get_option( 'gyn_options' );
	// send mail to the subscriber
	$headers[] = 'From: ' .$from_name . ' <' . $gyn_options['gyn_admin_email'] . '>';
	$headers[] = 'Cc: ' .$from_name . ' <' . $gyn_options['gyn_admin_email'] . '>';
	$to = $name . '<' . $email .'>';
	$subject = 'Your subscription number is ' . $number;
	$message = "Dear " . $name . ",\n\n" . $number . " is your subscription number for the event " . $eventname . ".\n\nYou will soon be informed if you have a lucky number.\nRegistration details:\nName: " . $name . "\n\nThank you for subscribing.";
	
	// send the mail and set $gyn_mail with a boolean to test sending went well
	$gyn_mail = wp_mail( $to, $subject, $message, $headers );
	
	if ( $gyn_mail ) {
		$gyn_mail_message = 'An email has been sent to you to confirm your subscription';
	} else {
		$gyn_mail_message = 'Sending an email failed, please remember your number';
	}
	return $gyn_mail_message;
}

/**
 * function called after admin changes settings and want to save them
*/
function process_gyn_options() {
	// Check user security level
	if ( !current_user_can( 'manage_options' ) ) wp_die( 'No permission for you to change options' );
	
	// Check nonce field created in configuration form
	check_admin_referer( 'gyn_settings' );
	// Retrieve original plugin options array
	$options = get_option( 'gyn_options' );
	// Cycle through all text form fields and store their values in the options array
	foreach ( $options as $key ) {
		if ( isset( $_POST[$key] ) ) {
			$options[$key] = $_POST[$key]; //sanitize_text_field( $_POST[$key] );
		}
	}

	$options['gyn_event_name'] = 'test123';
	update_option( 'gyn_options', $options );
	
	// Store updated options array to databaseupdate_option( 'gyn_options', $options );
	// Redirect the page to the configuration form that was processed
	
	wp_redirect( add_query_arg( array( 'page' => 'gyn-configuration', 'message' => '1' ), admin_url( 'options-general.php' ) ) );
	exit;
}
?>