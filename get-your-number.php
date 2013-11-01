<?php
/**
 * Plugin Name: Get your number
 * Plugin URI: https://github.com/punchcreative/get-your-number
 * Description: A random number generator for subscribing to an event with a limited number of participants. It provides the possibility of attending to a limited event for subscribers, even if they are not the fisrt with subscribing. See the plugin site (@github) for a more detailed description.
 * Version: 1.0
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
	
	// version number to be used in the plugin files
	define( "VERSION", "1.0" );
	
	/*****************************************
	 * include functions php file in subfolder /inc
	*/
	
	require plugin_dir_path( __FILE__ ). 'inc/gyn_functions.php';
	
	// only load admin functions is the user has admin permissions
	if ( is_admin() ) {
		require plugin_dir_path( __FILE__ ) . 'inc/gyn_admin_functions.php';
	}
	
	/*************************************************
	 * functions that runs at activation of the plugin
	*/
	function gyn_activation() {
		if ( get_option( 'gyn_options' ) === false ) {
			
			$new_options['gyn_version'] = '1.0';
			$new_options['gyn_admin_email'] = get_option( 'admin_email' );
			$new_options['gyn_min_nr'] = '1';
			$new_options['gyn_max_nr'] = '100';
			$new_options['gyn_event_name'] = 'First event';
			$new_options['gyn_given_numbers'] = array();
			
			add_option( 'gyn_options', $new_options );
			
		} else {
			
			$existing_options = get_option( 'gyn_options' );
			
			if ( $existing_options['gyn_version'] < 1.0 ) {
				
				$existing_options['gyn_version'] = "1.0";
			}
			
			if ( !isset( $existing_options['gyn_given_numbers'] ) || $existing_options['gyn_given_numbers'] != '' ) {
				
				$existing_options['gyn_given_numbers'] = array();
			}
				
			update_option( 'gyn_options', $existing_options );
		}
	}
	// on activation of the plugin call this function
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
		// if you want to use bootstrap for this plugin 
		$style = 'bootstrap';
		if( ( ! wp_style_is( $style, 'queue' ) ) && ( ! wp_style_is( $style, 'done' ) ) ) {
			//queue up bootstrap_css 2.3.2 file from CDN
			//wp_register_style( 'bootstrap_css', '//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css' );
			//wp_enqueue_style( 'bootstrap_css' );
			
			// load bootstrap_css 3.0.0 file from CDN
			//wp_register_style( 'bootstrap_css', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' );
			//wp_enqueue_style( 'bootstrap_css' );
		}
				
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
		// run uninstall script when the plugin is deleted by an admin
		
	}
	// on de-activation of the plugin call this function
	register_deactivation_hook( __FILE__ , 'gyn_deactivation' );

	/***********
	 * init vars
	*/
	
	function gyn_init_variables() {
		// get the settings foor the plugin
		$gyn_options = get_option( 'gyn_options' );
	}
	
	add_action( 'init', 'gyn_init_variables' );
	
	/*******************************
	 * Language setup for the plugin
	**/

	function gyn_language_setup() {
    	load_plugin_textdomain('get-your-number', false, dirname(plugin_basename(__FILE__)) . '/language/');
	}
	
	add_action('plugins_loaded', 'gyn_language_setup');
	
	/******************************************************************
	 * Page content submitted to posts or pages by the shortcode [gyn/]
	*/
	function display_gyn() {		
		global $gyn_form_checked;
		
		if ( isset($_POST['gyn_form_nonce']) && wp_verify_nonce( $_POST['gyn_form_nonce'], 'gyn_number_request_form' ) && !empty( $_POST['gyn_form_value'][0] ) && !empty( $_POST['gyn_form_value'][1] ) && !isset( $gyn_form_checked ) ) {
			if ( $_POST['gyn_form_value'][2] != '0' ) {
				$gyn_form_checked = handle_form_submit();
			} else {
				$gyn_form_checked = __('All numbers are taken, sorry!', 'get_your_number');
			}
		} 
				
		if ( isset( $gyn_form_checked ) ) {
			$html = '<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">' . __('This is your number', 'get-your-number') . '</h3>
				</div>
				<div class="span12">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">' . __('Name', 'get-your-number') . '</label></th>
							<td>' . $_POST['gyn_form_value'][0] . '</td>
						</tr>
						<tr>
							<th><label for="email">' . __('Email', 'get-your-number') . '</i></label></th>
							<td>' . $_POST['gyn_form_value'][1] . '</td>
						</tr>
						<tr>
							<th><label for="number">' . __('Your number', 'get-your-number') . '</label></th>
							<td>' . $_POST['gyn_form_value'][2] . '</td>
						</tr>
						<tr>
							<th></th>
							<td class="text-success">' . $gyn_form_checked . '</td>
						</tr>
					</tbody>
				</table>
				</div>
			</div>';
		} else {
			$html = '<script>
				  jQuery(function () { $("input").not("[type=submit]").jqBootstrapValidation(); } );
				</script>
				<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">' . __('Get your number', 'get-your-number') . '</h3>
				</div>
				<div class="span12">
				<form action=""  id="gyn_form" name="send_number" method="post" onsubmit="return validateForm()" >
				<!-- Nonce fields to verify visitor provenance -->
				' .  wp_nonce_field( "gyn_number_request_form", "gyn_form_nonce" ) . '
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">' . __('Name', 'get-your-number') . ' <i class="icon-asterisk"></label></th>
							<td><input id="name" type="text" name="gyn_form_value[]" class="formfield" required /></i></td>
						</tr>
						<tr>
							<th><label for="email">' . __('Email', 'get-your-number') . ' <i class="icon-asterisk"></i></label></th>
							<td><input id="email" type="email" name="gyn_form_value[]" class="formfield" required /></td>
						</tr> 
						<tr>
							<th><!-- <label for="number">' . __('Retreive your number', 'get-your-number') . '</label> --></th>
							<td><button type="submit" class="btn">' . __('Send me my number', 'get-your-number') . ' <i class="icon-gift icon-white"></i> </button>
							<input type="hidden" name="nonce_field" value="' . wp_create_nonce( 'form_check' ) . '" /></td>
							<input type="hidden" name="gyn_form_value[]" value="' . gyn_generate_unique_number() . '" /></td>

						</tr>
						<tr>
							<th></th>
							<td><i class="icon-asterisk"></i> ' . __('In order to get your number you must share your name and email', 'get-your-number') . '.</td>
						</tr>
					</tbody>
				</table>
				</form>
				</div>
			</div>';
		}
		echo $html;
	}
	// define the shortcode for the plugin
	add_shortcode("gyn", "display_gyn");
	
?>