<?php
/**
 * Plugin Name: Get your number
 * Plugin URI: https://github.com/punchcreative/get-your-number
 * Description: A random number generator as an assist for subscribing to an event with a limited number of participants. See the plugin site for a more detailed description.
 * Version: 1.1 beta
 * Author: Erik Kroon | Punch Creative
 * Author URI: http://www.punchcreative.nl
 * License: GPL2
 Copyright 2013  Erik Kroon  (email : mail@punchcreative.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	global $gyn_options;
	global $gyn_form_checked;
	global $gyn_form_error;
	global $gyn_the_nr;
	global $gyn_user_name;
	global $gyn_user_email;
	
	// version number to be used in the plugin files
	define( 'VERSION', '1.1' );
	// developpers setting for quick an dirty removing options on deactivate
	// leave this as it is when using the plugin on your site
	define( 'GYNBUG', true);
	
	/*****************************************
	 * include functions php file in subfolder /inc
	*/
	
	require plugin_dir_path( __FILE__ ). 'inc/gyn_functions.php';
	
	// only load admin functions is the user has admin permissions
	if ( is_admin() ) {
		require plugin_dir_path( __FILE__ ) . 'inc/gyn_admin_functions.php';
	}
	
	/*
	 * Punch Creative development tools
	 * use tj( $var ) or tj_log( $var )
	*/
	if ( GYNBUG ) {
		require plugin_dir_path( __FILE__ ) . 'inc/punch_dev_functions.php';
	}
	
	/*************************************************
	 * functions that runs at activation of the plugin
	*/
	function gyn_activation() {
		if ( get_option( 'gyn_options' ) === false ) {
			
			$new_options['gyn_version'] = VERSION;
			$new_options['gyn_admin_email'] = get_option( 'admin_email' );
			$new_options['gyn_min_nr'] = '1';
			$new_options['gyn_max_nr'] = '90';
			$new_options['gyn_event_name'] = __( 'Event name' , 'get-your-number');;
			$new_options['gyn_given_numbers'] = array();
			$new_options['gyn_available_numbers'] = range( $new_options['gyn_min_nr'], $new_options['gyn_max_nr'] );
			
			add_option( 'gyn_options', $new_options );
			
		} else {
			
			$existing_options = get_option( 'gyn_options' );
			
			if ( $existing_options['gyn_version'] < VERSION ) {
				
				$existing_options['gyn_version'] = VERSION;
			}
				
			update_option( 'gyn_options', $existing_options );
		}
	}
	
	register_activation_hook( __FILE__ , 'gyn_activation' );	
	
	
	/*************************************
	 * register scripts used by the plugin
	*/			
	function gyn_scripts() {
		// load the wp included jquery
		wp_enqueue_script('jquery');
		
		// load gyn_js scripts
		// wp_register_script( 'gyn_js', plugins_url('js/gyn_js.js', __FILE__) , array('jquery') , false, true);
		// wp_enqueue_script( 'gyn_js' );
		
		// load jqBootstrapValidation_js scripts
		wp_register_script( 'bootstrap_validation_js', plugins_url('js/jqBootstrapValidation.js', __FILE__) , array('jquery') , false, true);
		wp_enqueue_script( 'bootstrap_validation_js' );
		
		// load bootstrap_js 3.0.0 library from CDN in the footer area
		//wp_register_script( 'bootstrap_js', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js' , array('jquery') , false, true);
		//wp_enqueue_script( 'bootstrap_js' );
		
	}
		
	add_action( 'wp_enqueue_scripts', 'gyn_scripts' );
	
	/************************************
	 * register styles used by the plugin
	*/	
	function gyn_styles() {
		// fontawesome is used in the shortcode tables for displaying asterisk
		// you can disable this if you prefer		
		wp_register_style( 'fontawesome_css' , '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css' );
		wp_enqueue_style( 'fontawesome_css' );

					
		wp_register_style( 'gyn_styles', plugins_url('css/gyn_styles.css', __FILE__) );
		wp_enqueue_style( 'gyn_styles' );
	}
	
	add_action('wp_enqueue_scripts', 'gyn_styles');
	
	/****************************************************
	 * functions that runs at de-activation of the plugin
	*/
	function gyn_deactivation() {
		// for debugging purposes by developer: runs uninstall script when the plugin is deactivated by an admin
		if ( GYNBUG ) {
			if ( get_option( 'gyn_options' ) != false ) {
				delete_option( 'gyn_options' );
			}
		}
		
	}
	
	register_deactivation_hook( __FILE__ , 'gyn_deactivation' );

	/***********
	 * init vars
	*/
	function gyn_init_variables() {
		// get the settings foor the plugin
		$gyn_options = get_option( 'gyn_options' );
	}
	
	add_action( 'init', 'gyn_init_variables' );
	
	/***********************
	 * check for $_POST vars after form submission
	*/
	function gyn_post_variables() {
		global $gyn_form_checked;
		global $gyn_form_error;
		global $gyn_the_nr;
		global $gyn_options;
		global $gyn_user_name;
		global $gyn_user_email;
		
		if ( isset($_POST['gyn_form_nonce']) && wp_verify_nonce( $_POST['gyn_form_nonce'], 'gyn_number_request_form' ) ) {
			// load settings
			$gyn_options = get_option( 'gyn_options' );
			// check if name and email aren't empty
			if ( !empty( $_POST['gyn_name'] ) && !empty( $_POST['gyn_email'] ) && $_POST['gyn_nospam'] == $gyn_options['gyn_event_name'] ) {
				$gyn_user_name = $_POST['gyn_name'];
				$gyn_user_email = $_POST['gyn_email'];
				// check if the email adres is already saved in the options for gyn
				// used the extended in_array function to search in multidimensional arrays
				if ( !in_array_r( $gyn_user_email, $gyn_options['gyn_given_numbers'] ) ) {
					// generate a unique number sending the name and email to store it in an array
					$nr = gyn_generate_unique_number( $gyn_user_name, $gyn_user_email );
					// check the returned number
					if ( $nr != 0 ) {
						$gyn_the_nr = $nr;
						// send an email to the subscriber and set the variable $gyn_form_checked with a message about email sent
						$gyn_form_checked = handle_form_submit( $nr, $gyn_user_name, $gyn_user_email, $gyn_options['gyn_event_name'] );
					} else {
						// seems like the returned nr is 0, so the maximum amount of users is reached
						$gyn_the_nr = '--';
						$gyn_form_checked = __( 'All numbers are taken, sorry!' , 'get-your-number');
					}
				} else {
					// an entry with the subscribers email already exists, let's find it
					$key = recursive_array_search( $gyn_user_email, $gyn_options['gyn_given_numbers'] );
					// and display it
					$gyn_the_nr = $gyn_options['gyn_given_numbers'][$key][1];
					$gyn_form_checked = __( 'It seems you already got a number.' , 'get-your-number' );
					$gyn_form_error = $gyn_options['gyn_given_numbers'][$key][0] . ' ' .__('has already received number', 'get-your-number') . '.';
				}
			} else {
				unset( $gyn_form_checked );
				if ( empty( $_POST['gyn_email'] ) || empty( $_POST['gyn_email'] ) ) {
					// email or name is empty
					$gyn_form_error = __('Required fields are empty!', 'get-your-number');
				}
				if ( $_POST['gyn_nospam'] != $gyn_options['gyn_event_name'] ) {
					// email or name is empty
					$gyn_form_error = __('Wrong access code!', 'get-your-number');
				}
				
			}
		} 
	}
	
	add_action( 'init', 'gyn_post_variables' );
	
	/*******************************
	 * Language setup for the plugin
	 * use POEDIT and first load default.po from the root of the directory
	 * make changes in there and save as in the languages folder
	 * be sure to put get-your-number in front of the language definition
	**/
	function gyn_language_setup() {
		// set location of the language files for the plugin
    	load_plugin_textdomain('get-your-number', false, dirname(plugin_basename(__FILE__)) . '/language/');
	}
	
	add_action('plugins_loaded', 'gyn_language_setup');
	
	/******************************************************************
	 * Page content submitted to posts or pages by the shortcode [gyn/]
	*/
	function display_gyn() {		
		global $gyn_form_checked;
		global $gyn_form_error;
		global $gyn_the_nr;
		global $gyn_options;
		
		$gyn_options = get_option( 'gyn_options' );
		$html = '';
		
		// check if there are still numbers available else display the form for registration
		if ( count( $gyn_options['gyn_available_numbers'] ) == 0 && !isset( $gyn_form_checked ) ) {
			$html .= '<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">' . __( 'All numbers are taken, sorry!' , 'get-your-number') . '</h3>
				</div>
				<div class="span12">
				<p>' . __( 'Subscription is closed', 'get-your-number') . '</p>
				</div>
			</div>';
		} else {
			// form is received and checked
			if ( isset( $gyn_form_checked ) ) {
				
				if ( isset( $gyn_form_error ) ) {
					$gyn_form_header = $gyn_form_error;
					unset( $gyn_form_error );
				} else {
					$gyn_form_header = __( 'This is your number' , 'get-your-number' );
				}
				
				$key = recursive_array_search( $_POST['gyn_email'], $gyn_options['gyn_given_numbers'] );
				$html .= '<div class="header">
						<h3 class="text-success">' . $gyn_form_header . '</h3>
					</div>
					<div class="span12">
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th><label for="name">' . __('Name', 'get-your-number') . '</label></th>
								<td>' . $gyn_options['gyn_given_numbers'][$key][0] .'</td>
							</tr>
							<tr>
								<th><label for="email">' . __('Email', 'get-your-number') . '</i></label></th>
								<td>' . $gyn_options['gyn_given_numbers'][$key][2] .'</td>
							</tr>
							<tr>
								<th><label for="number">' . __('Your number', 'get-your-number') . '</label></th>
								<td>' . $gyn_options['gyn_given_numbers'][$key][1] . '</td>
							</tr>
							<tr>
								<th></th>
								<td class="text-success">' . $gyn_form_checked . '</td>
							</tr>
						</tbody>
					</table>
					</div>';
			} else {
				// form is received and checked
				if ( !isset( $_POST['gyn_name'] ) ) {
					$gyn_user_name = '';
				} else {
					$gyn_user_name = $_POST['gyn_name'];
				}
				if ( !isset( $_POST['gyn_email'] ) ) {
					$gyn_user_email = '';
				} else {
					$gyn_user_email = $_POST['gyn_email'];
				}
				if ( isset( $gyn_form_error ) ) {
					$gyn_form_header = $gyn_form_error;
					unset( $gyn_form_error );
				} else {
					$gyn_form_header = __('Get your number', 'get-your-number');
				}
				
				$html .= '<script>
					  jQuery(function () { $("input").not("[type=submit]").jqBootstrapValidation(); } );
					</script>
					<div class="header">
						<h3 class="text-success">' . $gyn_form_header . '</h3>
					</div>
					<div class="span12">
					<form action=""  id="gyn_form" name="send_number" method="post" onsubmit="return validateForm()" >
					<!-- Nonce fields to verify visitor provenance -->
					' .  wp_nonce_field( "gyn_number_request_form", "gyn_form_nonce" ) . '
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th><label for="gyn_name">' . __('Name', 'get-your-number') . ' <i class="icon-asterisk"></label></th>
								<td><input id="gyn_name" type="text" name="gyn_name" class="formfield" value="' . $gyn_user_name . '" required /></i></td>
							</tr>
							<tr>
								<th><label for="gyn_email">' . __('Email', 'get-your-number') . ' <i class="icon-asterisk"></i></label></th>
								<td><input id="gyn_email" type="email" name="gyn_email" class="formfield" value="' . $gyn_user_email . '" required /></td>
							</tr> 
							<tr>
								<th><label for="gyn_nospam">' . __('Access code', 'get-your-number') . ' <i class="icon-asterisk"></i><i class="icon-asterisk"></i></label></th>
								<td><input id="gyn_nospam" type="text" name="gyn_nospam" class="formfield" placeholder="' . __( 'Enter this value in here','get-your-number') . ': ' . $gyn_options['gyn_event_name'] . '" required /></td>
							</tr> 
							<tr>
								<th><i class="icon-asterisk"></i></th>
								<td>' . __('In order to get your number you must share your name and email', 'get-your-number') . '.</td>
							</tr> 
							<tr>
								<th><i class="icon-asterisk"></i></label><i class="icon-asterisk"></th>
								<td colspan="2">' . __('Fill in the same as you see as a placeholder.', 'get-your-number') . ' </td>
							</tr> 
							<tr>
								<th>&nbsp;</th>
								<td>
									<button type="submit" class="btn">' . __('Send me my number', 'get-your-number') . ' <i class="icon-gift icon-white"></i> </button>
									<input type="hidden" name="gyn_event" value="' . $gyn_options['gyn_event_name'] . '" />
								</td>
	
							</tr>
						</tbody>
					</table>
					</form>
					</div>';
			}
		}
		return $html;
	}
	
	add_shortcode("gyn", "display_gyn");
?>