<?php
/**
 * Plugin Name: Get your number
 * Plugin URI: https://github.com/punchcreative/get-your-number
 * Description: A random number generator for subscribing to an event with a limited number of participants. It provides the possibility of attending to a limited event for subscribers, even if they are not the fisrt with subscribing. See the plugin site (github) for a more detailed description.
 * Version: 1.01 BETA
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
	/**
	 * include all php files in subfolder /inc
	*/
	
	foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
	include_once $file;
			
	/**
	 * functions that runs at activation of the plugin
	*/
	
	//function gyn_activation() {
		/**
		 * register scripts used by the plugin
		*/
		
	//}
	// on activation of the plugin call this function
	//register_activation_hook( __FILE__ , 'gyn_activation' );
		
	add_action( 'wp_enqueue_scripts', 'gyn_scripts' );
	
	function gyn_scripts() {
		// load the wp included jquery
		wp_enqueue_script('jquery');
		
		// load gyn_js scripts
		wp_register_script( 'gyn_js', plugins_url('js/gyn_js.js', __FILE__) , array('jquery') , false, true);
		wp_enqueue_script( 'gyn_js' );
		
		// load bootstrap_js library from CDN in the footer area
		//wp_register_script( 'bootstrap_js', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js' , array('jquery') , false, true);
		//wp_enqueue_script( 'bootstrap_js' );
		
	}
	
	/**
	 * register styles for the plugin
	*/
	
	add_action('wp_enqueue_scripts', 'gyn_styles');
	
	function gyn_styles() {
		// load bootstrap_css 2.3.2 file from CDN
		wp_register_style( 'bootstrap_css', '//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css' );
		wp_enqueue_style( 'bootstrap_css' );
		
		wp_register_style( 'fontawesome_css' , '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css' );
		wp_enqueue_style( 'fontawesome_css' );
		
		// load bootstrap_css 3.0.0 file from CDN
		//wp_register_style( 'bootstrap_css', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' );
		//wp_enqueue_style( 'bootstrap_css' );
					
		wp_register_style( 'gyn_styles', plugins_url('css/gyn_styles.css', __FILE__) );
		wp_enqueue_style( 'gyn_styles' );
	}
		
	
	/**
	 * functions that runs at de-activation of the plugin
	*/
	
	function gyn_deactivation() {
		// run uninstall script
		include( $dir . 'inc/uninstall.php');
	}
	// on de-activation of the plugin call this function
	register_deactivation_hook( __FILE__ , 'gyn_deactivation' );

	/**
	 * add admin admin menu
	*/
		
	/**
	 * Page content submitted to posts or pages by the shortcode [gyn/]
	*/
	
	function display_gyn() {
		// use this variable to set a state to the form when it is send
		$form_to_show = -1;
		
		if ( isset($_POST['nonce_field']) && wp_verify_nonce( $_POST['nonce_field'], 'form_check' ) && isset( $_POST['gyn_value'] ) ) {
			// save POST variables in an array
			if ( !isset( $gyn_variables) ) {
				$gyn_variables = $_POST['gyn_value'];
				unset( $_POST['gyn_value'] );
			}
			// check if the email is valid
			if ( is_email( $gyn_variables[1] ) && !isset( $gyn_user_check[0] ) ) {
				// generate a unique number and at the same time check if the email is not already in the database
				$gyn_user_check = gyn_generate_unique_number();
				
				$form_to_show = $gyn_user_check[1];
			} elseif ( isset( $gyn_user_check[0] ) ) {
				$form_to_show = 1;
			} else {
				$form_to_show = 2;
			}
		}
		if ( $form_to_show == 1 ) {
			$html = '<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">This is your number</h3>
				</div>
				<div class="span12">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">Name</label></th>
							<td>' . $gyn_variables[0] . '</td>
						</tr>
						<tr>
							<th><label for="email">Email</i></label></th>
							<td>' . $gyn_variables[1] . '</td>
						</tr>
						<tr>
							<th><label for="number">Your number</label></th>
							<td>' . $gyn_user_check[0] . '</td>
						</tr>
					</tbody>
				</table>
				</div>
			</div>';
		} elseif ( $form_to_show == 2 ) {
			echo '<script>
				$( document ).ready(function() {
			    $( "#email" ).focus();
			});
			</script>';
			
			$html = '<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">Get your number</h3>
				</div>
				<div class="span12">
				<form action="" name="send_number" method="post" >
				<div class="header">
					<h3 class="text-error">There seems to be something wrong with your email</h3>
				</div>
				<div class="span12">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">Name</label></th>
							<td>' . $gyn_variables[0] . '
							<input id="name" type="hidden" name="gyn_value[]" value="' . $gyn_variables[0] . '" />
							</td>
						</tr>
						<tr class="error">
							<th><label for="email">Email <i class="icon-asterisk"></i></label></th>
							<td><input id="email" type="text" name="gyn_value[]" class="span12 error" value="' . $gyn_variables[1] . '" /></td>
						</tr>
							<tr>
								<th><label for="number">Retrieve your number</label></th>
								<td><button type="submit" class="btn btn-inverse btn-block">Try again <i class="icon-gift icon-white"></i> </button>
								<input type="hidden" name="nonce_field" value="' . wp_create_nonce( 'form_check' ) . '" />
							</tr>
							<tr>
								<th></th>
								<td><i class="icon-asterisk"></i> In order to get your number you must share a valid email.</td>
							</tr>
						</tbody>
					</table>
					</form>
				</div>
			</div>';
		} else {
			$html = '<div class="row-fluid">
					<div class="header">
						<h3 class="text-success">Get your number</h3>
					</div>
					<div class="span12">
					<form action="" name="send_number" method="post" onsubmit="return validateForm()" >
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th><label for="name">Name <i class="icon-asterisk"></label></th>
								<td><input id="name" type="text" name="gyn_value[]" class="span12" /></i></td>
							</tr>
							<tr>
								<th><label for="email">Email <i class="icon-asterisk"></i></label></th>
								<td><input id="email" type="text" name="gyn_value[]" class="span12" /></td>
							</tr>
							<tr>
								<th><label for="number">Retrieve your number</label></th>
								<td><button type="submit" class="btn btn-inverse btn-block">Send me my number <i class="icon-gift icon-white"></i> </button>
								<input type="hidden" name="nonce_field" value="' . wp_create_nonce( 'form_check' ) . '" /></td>
							</tr>
							<tr>
								<th></th>
								<td><i class="icon-asterisk"></i> In order to get your number you must share your name and email.</td>
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
	
	/**
	 * Admin setting for this plugin
	*/
	
	add_action('admin_menu', 'gyn_plugin_settings');

	function gyn_plugin_settings() {
	
		add_menu_page('GYN Settings', 'GYN Settings', 'administrator', 'gyn_settings', 'gyn_display_settings');
	
	}
	
	/**
	 * form processing using nonce for protection
	*/
	
	function process_form() {
		if ( isset($_POST['nonce_field']) && wp_verify_nonce( $_POST['nonce_field'], 'form_check' ) ) {
			
		}
	}
	add_action( 'init', 'process_form' );

?>