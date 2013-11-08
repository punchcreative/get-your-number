<?php
/**
 * Functions used by the plugin get-your-number
*/

/**
 * function for generating the numbers
*/
function gyn_generate_unique_number( $name, $email) {
	$gyn_username = $name;
	$gyn_usermail = $email;
		
	$options = get_option( 'gyn_options' );
	// fetch the array with available numbers
	$arr = $options['gyn_given_numbers'];
	// set a var to the max numbers that can be given
	$gyn_max_numbers_to_give = $options['gyn_max_nr'] - ( $options['gyn_min_nr'] - 1 );
	
	// check if there are still numbers available
	if ( isset( $arr ) && count( $arr ) < $gyn_max_numbers_to_give ) {
		
		// generate a number inbetween the numbers set on activation of the plugin
		$nr = mt_rand( $options['gyn_min_nr'], $options['gyn_max_nr'] );	
		// look if the number is available
		if ( !in_array_r( $nr, $arr ) ) {
			array_push( $arr, array( $gyn_username, $nr , $gyn_usermail ) );
			// save the new subcriber in the options table
			$options[ 'gyn_given_numbers' ] = $arr;
			update_option( 'gyn_options', $options );
			
		} else {	
			$nr = NULL;
			gyn_generate_unique_number( $gyn_username, $gyn_usermail );			
		}
		
	} else {		
		// no numbers available anymore
		$nr = '0';
	}	
	return $nr;
}

/**
 * functions extending standard in_array function and array_search to work in multidimensional arrays
*/

function in_array_r($needle, $haystack, $strict = true) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function recursive_array_search($needle,$haystack, $strict = true) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value, $strict) !== false)) {
            return $current_key;
        }
    }
    return false;
}

/**
 * functions called after users entry is checked and ready to go
*/

// use the nonce to check that the function is called from within wordpress and the gyn form
function handle_form_submit( $nr, $name, $email, $event ) {
	global $gyn_mail_check;
	
	$gyn_username = $name;
	$gyn_usermail = $email;
	$gyn_event = $event;
	
	if ( !isset( $gyn_mail_check ) && isset($_POST['gyn_form_nonce']) && wp_verify_nonce( $_POST['gyn_form_nonce'], 'gyn_number_request_form' ) ) {
		// send an emai to subscriber and administrator
		$gyn_mail_check = gyn_mailer( $gyn_username, $gyn_usermail, $nr, __('Get your number admin', 'get-your-number'), $gyn_event );

		return $gyn_mail_check;
		
	} else {
		// somebody is messing around in Wordpress, which we don't want
		return null;		
	}
}

// function that sends an email to the user after the nonce is checked in handle_form_submit( $nr )
function gyn_mailer( $name, $email, $number, $from_name, $eventname ) {
	
	$gyn_options = get_option( 'gyn_options' );
	
	if ( isset($_POST['gyn_form_nonce']) && wp_verify_nonce( $_POST['gyn_form_nonce'], 'gyn_number_request_form' ) ) {
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
}
?>