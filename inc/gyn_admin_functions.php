<?php
/********************************************************
 * Functions admin settings for plugin get-your-number
*/

function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/gyn_styles.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}

add_action('admin_head', 'admin_register_head');

function gyn_settings_menu() {
	// add a settings menu item in the admin area
	add_options_page( 'GYN Configuration', 'Get Your Number', 'manage_options', 'gyn-configuration', 'gyn_config_page' );
}

add_action( 'admin_menu', 'gyn_settings_menu' );
	
/*****************************************
 * admin init function for saving settings
*/
function gyn_admin_init() {
	// Register a setting group with a validation function so that post data handling is done automatically
	register_setting( 'gyn_settings', 'gyn_options', 'gyn_validate_options' );
	
	// Add a new settings section within the group
	add_settings_section( 'gyn_main_section', __('Main settings', 'get-your-number'), 'gyn_main_setting_section_callback', 'gyn_settings_section' );
	
	// Add each field with its name and function to use for our new settings, put them in our new section            
	add_settings_field( 'gyn_admin_email', __('Admin email', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_admin_email' ) ); 
	add_settings_field( 'gyn_min_nr', __('Start number', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_min_nr' ) );
	add_settings_field( 'gyn_max_nr', __('End number', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_max_nr' ) );
	add_settings_field( 'gyn_event_name', __('Event name', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_event_name' ) );

}

add_action( 'admin_init', 'gyn_admin_init' );

/****************************************
 * functions used for admin settings page
*/
function gyn_config_page() { 
	 global $gyn_options;
	 $gyn_options = get_option( 'gyn_options' );
	 ?>
    <div id="gyn-general" class="wrap">
        <div class="admin-settings-box">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('GYN settings | GYN version', 'get-your-number'); ?> <?php echo $gyn_options['gyn_version']; ?></h2>
            
            <form name="gyn_options_form_settings_api" method="post" action="options.php">
            
                <?php settings_fields( 'gyn_settings' ); ?>
                
                <?php do_settings_sections( 'gyn_settings_section' ); ?>
                
                <input type="submit" value="<?php _e('Save settings', 'get-your-number'); ?>" class="button-primary" />
                
            </form>
        </div>
        <table class="widefat gyn-admin-warning-table" id="admin-warnings">
           <tr> 
           <td width="100%" class="warning"><strong><?php _e('WARNING! Changing the settings will also clear the saved registrations!','get_your_number'); ?></strong></td>
           <tr>
        </table>
        <table class="widefat gyn-reg-users-table" id="reg_users" >
            <thead>
                <tr>
                  <th><strong><?php _e('Name','get_your_number') ?></strong></td>
                  <th><strong><?php _e('Number','get_your_number') ?></strong></td>
                  <th><strong><?php _e('Email','get_your_number') ?></strong></td>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th><strong><?php _e('Name','get_your_number') ?></strong></td>
                  <th><strong><?php _e('Number','get_your_number') ?></strong></td>
                  <th><strong><?php _e('Email','get_your_number') ?></strong></td>
                </tr>
            </tfoot>
            <?php
            if ( count( $gyn_options['gyn_given_numbers'] ) > 0 ) {
               foreach ( $gyn_options['gyn_given_numbers'] as $gyn_reg_users ){
                     echo '<tbody>'; 
                    echo '<tr>'; 
                    echo '<td>' . $gyn_reg_users[0] . '</td>';
                    echo '<td>' . $gyn_reg_users[1] . '</td>';
                    echo '<td>' . $gyn_reg_users[2] . '</td>';
                    echo '</tr>';
                    echo '</tbody>';
                }
            }
            ?>
       </table>
   </div>
	<?php
}

// validation option, variable set in get-your-number.php define
function gyn_validate_options( $input ) {
	
	$input['version'] = VERSION;
	
	return $input;
}

// text showed on admin page above settings
function gyn_main_setting_section_callback() {
	
	_e('Set an administrator email, the range for the numbers to give and an event name here.', 'get-your-number');
	
}

// display input text field function
function gyn_display_text_field( $data = array() ) {
	
	extract( $data );
	$gyn_options = get_option( 'gyn_options' );
	
	?>
	<input type="text" name="gyn_options[<?php echo $name; ?>]" value="<?php echo esc_html( $gyn_options[$name] ); ?>"/><br />
	<?php
}

// display checkbox function
function gyn_display_check_box( $data = array() ) {
	extract ( $data );
	$gyn_options = get_option( 'gyn_options' );
	?>
	<input type="checkbox" name="gyn_options[<?php echo $name; ?>]" <?php if ( $gyn_options[$name] ) echo ' checked="checked"'; ?>/>
	<?php
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