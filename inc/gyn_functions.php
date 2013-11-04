<?php
/**
 * Functions used by the plugin get-your-number
*/

/**
 * function for generating the numbers
*/

function gyn_generate_unique_number( $reg_name, $reg_email) {
	$options = get_option( 'gyn_options' );
	// fetch the array with available numbers
	$arr = $options['gyn_given_numbers'];
	// check if there are still numbers available
	$gyn_max_numbers_to_give = $options['gyn_max_nr'] - ( $options['gyn_min_nr'] - 1 );
	
	if ( isset( $arr ) && count( $arr ) < $gyn_max_numbers_to_give ) {
		
		// generate a number inbetween the numbers set on activation of the plugin
		$nr = mt_rand( $options['gyn_min_nr'], $options['gyn_max_nr'] );	
		
		if ( !in_array( $nr, $arr ) ) {
			array_push( $arr, array( $reg_name, $nr , $reg_email ) );
			$options[ 'gyn_given_numbers' ] = $arr;
			// save the new values in the options table;
			update_option( 'gyn_options', $options );
			
		} else {
			
			gyn_generate_unique_number();
			
		}
		
	} else {
		
		// no numbers available anymore
		$nr = '0';
		
	}
	
	return $nr;
}


/**
 * function called after users entry is submitted looking in dbase if the user doesn't exist already
*/

function gyn_check_email($email_to_check) {
	// in future release check if the email is not already used
	// for now return 1
	return 1;		
	
}
// function that sends an email to the user
function handle_form_submit( $nr) {
	if ( isset($_POST['nonce_field']) && wp_verify_nonce( $_POST['nonce_field'], 'form_check' ) && !isset( $gyn_mail_check )) {
		
		// send an emai to subscriber and administrator
		$gyn_mail_check = gyn_mailer( $_POST['gyn_form_value'][0], $_POST['gyn_form_value'][1], $nr, __('Get your number admin', 'get-your-number'), $_POST['gyn_event'] );

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
	$subject = __('Your subscription number is ', 'get-your-number') . " " . $number;
	
	$message = __('Dear', 'get-your-number') . " " . $name . ",\n\n" . $number . " " . __('is your subscription number for the event', 'get-your-number') . " " . $eventname . ".\n\n" . __('You will soon be informed if you have a lucky number', 'get-your-number'). "\n\n" . __('Name', 'get-your-number') . ": " . $name . "\n" . __('Email', 'get-your-number') . ": " . $email . "\n" . __('Number', 'get-your-number') . ": " . $number . "\n\n" . __('Thank you for subscribing', 'get-your-number') . ".";
	
	// send the mail and set $gyn_mail with a boolean to test sending went well
	$gyn_mail = wp_mail( $to, $subject, $message, $headers );
	
	if ( $gyn_mail ) {
		
		$gyn_mail_message = __('An email has been sent to you to confirm your subscription', 'get-your-number');
		
	} else {
		
		$gyn_mail_message = __('Sending an email failed, please remember your number', 'get-your-number');
		
	}
	
	return $gyn_mail_message;
}

/**
 * function called after admin changes settings and want to save them
*/
function process_gyn_options() {
	// Check user security level
	if ( !current_user_can( 'manage_options' ) ) wp_die( _e('No permission for you to change options', 'get-your-number') );
	
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

	// set the array for given numbers to new min and max values
	$options['gyn_given_numbers'] = array();
	
	// save the new values in the options table;
	update_option( 'gyn_options', $options );
	
	// Store updated options array to databaseupdate_option( 'gyn_options', $options );
	// Redirect the page to the configuration form that was processed
	
	wp_redirect( add_query_arg( array( 'page' => 'gyn-configuration', 'message' => '1' ), admin_url( 'options-general.php' ) ) );
	exit;
}

?>