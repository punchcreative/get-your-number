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

function handle_form_submit() {
	if ( isset($_POST['nonce_field']) && wp_verify_nonce( $_POST['nonce_field'], 'form_check' ) && !isset( $gyn_mail_check )) {
		
		// send an emai to subscriber and administrator
		$gyn_mail_check = gyn_mailer($_POST['gyn_form_value'][0],$_POST['gyn_form_value'][1],$_POST['gyn_form_value'][2],'Get your number admin','Test event');

		return $gyn_mail_check;
		
	} else {
		
		return null;
		
	}
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
	$subject = _e('Your subscription number is ', 'gyn') . $number;
	
	$message = _e('Dear', 'gyn') . " " . $name . ",\n\n" . $number . " " . _e('is your subscription number for the event', 'gyn') . " " . $eventname . ".\n\n" . _e('You will soon be informed if you have a lucky number', 'gyn'). "\n" . _e('Registration', 'gyn') . ":\n" . _e('Name', 'gyn') . ": " . $name . "\n" . _e('Email', 'gyn') . ": " . $email . "\n" . _e('Number', 'gyn') . ": " . $number . "\n\n" . _e('Thank you for subscribing', 'gyn') . ".";
	
	// send the mail and set $gyn_mail with a boolean to test sending went well
	$gyn_mail = wp_mail( $to, $subject, $message, $headers );
	
	if ( $gyn_mail ) {
		
		$gyn_mail_message = _e('An email has been sent to you to confirm your subscription', 'gyn');
		
	} else {
		
		$gyn_mail_message = _e('Sending an email failed, please remember your number', 'gyn');
		
	}
	
	return $gyn_mail_message;
}

/**
 * function called after admin changes settings and want to save them
*/
function process_gyn_options() {
	
	// Check user security level
	if ( !current_user_can( 'manage_options' ) ) wp_die( _e('No permission for you to change options', 'gyn') );
	
	// Check nonce field created in configuration form
	check_admin_referer( 'gyn_settings' );
	
	// Retrieve original plugin options array
	$options = get_option( 'gyn_options' );
	
	// Cycle through all text form fields and store their values in the options array
	foreach ( $options as $key => $value ) {
		
		if ( isset( $_POST[$key] ) ) {
			
			$options[$key] = $_POST[$key]; //sanitize_text_field( $_POST[$key] );
			
		}
		
	}

	//$options['gyn_event_name'] = 'test123';
	update_option( 'gyn_options', $options );
	
	// Store updated options array to databaseupdate_option( 'gyn_options', $options );
	// Redirect the page to the configuration form that was processed
	
	wp_redirect( add_query_arg( array( 'page' => 'gyn-configuration', 'message' => '1' ), admin_url( 'options-general.php' ) ) );
	exit;
}
?>