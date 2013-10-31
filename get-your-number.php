<?php
/**
 * Plugin Name: Get your number
 * Plugin URI: https://github.com/punchcreative/get-your-number
 * Description: A random number generator for subscribing to an event with a limited number of participants. It provides the possibility of attending to a limited event for subscribers, even if they are not the fisrt with subscribing. See the plugin site (@github) for a more detailed description.
 * Version: 1.03 dev
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
	 * register scripts used by the plugin
	*/			
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
		
	add_action( 'wp_enqueue_scripts', 'gyn_scripts' );
	
	/**
	 * register styles used by the plugin
	*/	
	function gyn_styles() {
		$style = 'bootstrap';
		if( ( ! wp_style_is( $style, 'queue' ) ) && ( ! wp_style_is( $style, 'done' ) ) ) {
			//queue up bootstrap_css 2.3.2 file from CDN
			wp_register_style( 'bootstrap_css', '//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css' );
			wp_enqueue_style( 'bootstrap_css' );
			
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
	
	/**
	 * functions that runs at activation of the plugin
	*/
	function gyn_activation() {
		if ( get_option( 'gyn_options' ) === false ) {
			
			$new_options['gyn_version'] = '1.0';
			$new_options['gyn_admin_email'] = get_option( 'admin_email' );
			$new_options['gyn_min_nr'] = '1';
			$new_options['gyn_max_nr'] = '80';
			$new_options['gyn_event_name'] = 'Try-out GYN';
			
			add_option( 'gyn_options', $new_options );
			
		} else {
			
			$existing_options = get_option( 'gyn_options' );
			
			if ( $existing_options['gyn_version'] < 1.0 ) {
				
				$existing_options['gyn_version'] = "1.0";
				
				update_option( 'gyn_options', $existing_options );
				
			}
		}
	}
	// on activation of the plugin call this function
	register_activation_hook( __FILE__ , 'gyn_activation' );	
	
	/**
	 * Admin setting page html
	*/
	function gyn_config_page() {
		// Retrieve plugin configuration options from database
		$options = get_option( 'gyn_options' );
		?>
		<div id="gyn-general" class="wrap">
            <h2><?php _e('GYN settings | GYN version', 'gyn') . " " . $options['gyn_version']; ?></h2>
            <?php
            if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) {
				?>
				<div id='message' class='updated fade'><p><strong><?php _e('Settings are saved', 'gyn'); ?></strong></p></div>
			 <?php 
			 }
			 ?>
            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="save_gyn_options" />
                <table border="1" celpadding="0" celspacing="0" class="table table-bordered span12">
                    <tr>
                        <th scope="row"><?php _e('Admin email', 'gyn'); ?></th>
                        <td><input type="text" name="gyn_admin_email" value="<?php echo $options['gyn_admin_email']; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Start number', 'gyn'); ?></th>
                        <td><input type="text" name="gyn_min_nr" value="<?php echo $options['gyn_min_nr']; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('End number', 'gyn'); ?></th>
                        <td><input type="text" name="gyn_max_nr" value="<?php echo $options['gyn_max_nr']; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Event name', 'gyn'); ?></th>
                        <td><input type="text" name="gyn_event_name" value="<?php echo $options['gyn_event_name']; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row">&nbsp;</th>
                        <td><input type="submit" value="<?php _e('Submit', 'gyn'); ?>" class="button-primary"/></td>
                    </tr>
                </table>
                <!-- Adding security through hidden referrer field -->
                <?php wp_nonce_field( 'gyn_settings' ); ?>
            </form>
		</div>
	<?php
	}
	
	/**
	 * Admin setting for this plugin
	*/
	add_action('admin_menu', 'gyn_plugin_settings');

	function gyn_plugin_settings() {
		// add a settings menu item in the admin area
		add_options_page( 'GYN Configuration', 'Get Your Number', 'manage_options', 'gyn-configuration', 'gyn_config_page' );
	}
	
	/**
	 * admin init function for saving settings
	*/
	function gyn_admin_init() {
		add_action( 'admin_post_save_gyn_options', 'process_gyn_options' );
	}
	
	add_action( 'admin_init', 'gyn_admin_init' );
	
	/**
	 * functions that runs at de-activation of the plugin
	*/
	
	function gyn_deactivation() {
		// run uninstall script when the plugin is deleted by an admin
		
	}
	// on de-activation of the plugin call this function
	register_deactivation_hook( __FILE__ , 'gyn_deactivation' );

	/**
	 * init vars
	*/
	
	function gyn_init_variables() {
		// not used for the moment
	}
	
	add_action( 'init', 'gyn_init_variables' );
	
	/**
	 * Language setup for the plugin
	**/

	function gyn_language_setup() {
    	load_plugin_textdomain('gyn', false, dirname(plugin_basename(__FILE__)) . '/language/');
	}
	
	add_action('init', 'gyn_language_setup');
	
	/**
	 * Page content submitted to posts or pages by the shortcode [gyn/]
	*/
	function display_gyn() {
		
		$gyn_form_checked = handle_form_submit();
				
		if ( isset($gyn_form_checked) ) {
			$html = '<div class="row-fluid">
				<div class="header">
					<h3 class="text-success">' . __('This is your number', 'gyn') . '</h3>
				</div>
				<div class="span12">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">' . __('Name', 'gyn') . '</label></th>
							<td>' . $_POST['gyn_form_value'][0] . '</td>
						</tr>
						<tr>
							<th><label for="email">' . __('Email', 'gyn') . '</i></label></th>
							<td>' . $_POST['gyn_form_value'][1] . '</td>
						</tr>
						<tr>
							<th><label for="number">' . __('Your number', 'gyn') . '</label></th>
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
					<h3 class="text-success">' . __('Get your number', 'gyn') . '</h3>
				</div>
				<div class="span12">
				<form action=""  id="gyn_form" name="send_number" method="post" onsubmit="return validateForm()" >
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><label for="name">' . __('Name', 'gyn') . ' <i class="icon-asterisk"></label></th>
							<td><input id="name" type="text" name="gyn_form_value[]" class="span12" required /></i></td>
						</tr>
						<tr>
							<th><label for="email">' . __('Email', 'gyn') . ' <i class="icon-asterisk"></i></label></th>
							<td><input id="email" type="email" name="gyn_form_value[]" class="span12" required /></td>
						</tr> 
						<tr>
							<th><label for="number">' . __('Retreive your number', 'gyn') . '</label></th>
							<td><button type="submit" class="btn btn-inverse btn-block">' . __('Send me my number', 'gyn') . ' <i class="icon-gift icon-white"></i> </button>
							<input type="hidden" name="nonce_field" value="' . wp_create_nonce( 'form_check' ) . '" /></td>
							<input type="hidden" name="gyn_form_value[]" value="' . gyn_generate_unique_number() . '" /></td>

						</tr>
						<tr>
							<th></th>
							<td><i class="icon-asterisk"></i> ' . __('In order to get your number you must share your name and email', 'gyn') . '.</td>
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