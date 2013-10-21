<?php
/**
 * Plugin Name: Get your number
 * Plugin URI: https://github.com/punchcreative/get-your-number
 * Description: A random number generator for subscribing to an event with a limited number of participants. It provides the possibility of attending to a limited event for subscribers, even if they are not the fisrt with subscribing. See the plugin site (github) for a more detailed description.
 * Version: 1.02 beta
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
		
		// load jqBootstrapValidation_js scripts
		wp_register_script( 'bootstrap_validation_js', plugins_url('js/jqBootstrapValidation.js', __FILE__) , array('jquery') , false, true);
		wp_enqueue_script( 'bootstrap_validation_js' );
		
		// load bootstrap_js 3.0.0 library from CDN in the footer area
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
		if ( isset($_POST['nonce_field']) && wp_verify_nonce( $_POST['nonce_field'], 'form_check' ) ) {
			$html = '<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">This is your number</h3>
				</div>
				<div class="span12">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">Name</label></th>
							<td>' . $_POST['gyn_form_value'][0] . '</td>
						</tr>
						<tr>
							<th><label for="email">Email</i></label></th>
							<td>' . $_POST['gyn_form_value'][1] . '</td>
						</tr>
						<tr>
							<th><label for="number">Your number</label></th>
							<td>' . $_POST['gyn_form_value'][2] . '</td>
						</tr>
						<tr>
							<th></th>
							<td>An email has been sent to you.</td>
						</tr>
					</tbody>
				</table>
				</div>
			</div>';
			// send an emai to subscriber and administrator
			// this shoud not happen here, but for the moment it's oké
			$gyn_admin_email = get_option( 'admin_email' );
			gyn_mailer($_POST['gyn_form_value'][0],$_POST['gyn_form_value'][1],$_POST['gyn_form_value'][2],'Get your number admin',$gyn_admin_email,'Test');
		} else {
			$html = '<script>
					  jQuery(function () { $("input").not("[type=submit]").jqBootstrapValidation(); } );
					</script>
					<div class="row-fluid">
					<div class="header">
						<h3 class="text-success">Get your number</h3>
					</div>
					<div class="span12">
					<form action=""  id="gyn_form" name="send_number" method="post" onsubmit="return validateForm()" >
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th><label for="name">Name <i class="icon-asterisk"></label></th>
								<td><input id="name" type="text" name="gyn_form_value[]" class="span12" required /></i></td>
							</tr>
							<tr>
								<th><label for="email">Email <i class="icon-asterisk"></i></label></th>
								<td><input id="email" type="email" name="gyn_form_value[]" class="span12" required /></td>
							</tr> 
							<tr>
								<th><label for="number">Retrieve your number</label></th>
								<td><button type="submit" class="btn btn-inverse btn-block">Send me my number <i class="icon-gift icon-white"></i> </button>
								<input type="hidden" name="nonce_field" value="' . wp_create_nonce( 'form_check' ) . '" /></td>
								<input type="hidden" name="gyn_form_value[]" value="' . gyn_generate_unique_number() . '" /></td>

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
	 * init vars and check $_POST vars
	*/
	
	function gyn_set_variables() {
	}
	add_action( 'init', 'gyn_set_variables' );
	
?>