<?php
/**
 * Functions used by the admin for plugin get-your-number
*/
	/*******************************
	 * Admin setting for this plugin
	*/
	//add_action('admin_menu', 'gyn_plugin_settings');
	
	function gyn_settings_menu() {
		// add a settings menu item in the admin area
		add_options_page( 'GYN Configuration', 'Get Your Number', 'manage_options', 'gyn-configuration', 'gyn_config_page' );
	}

	add_action( 'admin_menu', 'gyn_settings_menu' );
		
	/*****************************************
	 * admin init function for saving settings
	*/
	function gyn_admin_init() {
		global $gyn_options;
		
		// Register a setting group with a validation function so that post data handling is done automatically
		register_setting( 'gyn_settings', 'gyn_options', 'gyn_validate_options' );
		
		// Add a new settings section within the group
		add_settings_section( 'gyn_main_section', __('Main settings', 'get-your-number'), 'gyn_main_setting_section_callback', 'gyn_settings_section' );
		
		// Add each field with its name and function to use for our new settings, put them in our new section            
		add_settings_field( 'gyn_admin_email', __('Admin email', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_admin_email' ) ); //, 'value' => $gyn_options['gyn_admin_email'] 
		add_settings_field( 'gyn_min_nr', __('Start number', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_min_nr' ) ); //, 'value' => $gyn_options['gyn_min_nr'] 
		add_settings_field( 'gyn_max_nr', __('End number', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_max_nr' ) ); //, 'value' => $gyn_options['gyn_max_nr'] 
		add_settings_field( 'gyn_event_name', __('Event name', 'get-your-number'), 'gyn_display_text_field', 'gyn_settings_section', 'gyn_main_section', array( 'name' => 'gyn_event_name' ) ); //, 'value' => $gyn_options['gyn_event_name'] 
	
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
       
           <h2><?php _e('GYN settings | GYN version', 'get-your-number'); ?> <?php echo $gyn_options['gyn_version']; ?></h2>
           
           <form name="gyn_options_form_settings_api" method="post" action="options.php">
           
				<?php settings_fields( 'gyn_settings' ); ?>
                
				<?php do_settings_sections( 'gyn_settings_section' ); ?>
                
				<input type="submit" value="<?php _e('Save settings', 'get-your-number'); ?>" class="button-primary" />
                
           </form>
           <table class="table gyn-reg-users-table" id="reg_users" width="80%">
			   <?php
                echo '<tr>'; 
                echo '<td width="10%"><strong>' . __('Name','get_your_number') . '</strong></td>';
                echo '<td width="30%"><strong>' . __('Number','get_your_number') . '</strong></td>';
                echo '<td width="30%"><strong>' . __('Email','get_your_number') . '</strong></td>';
                echo '<tr>';
                
                if ( count( $gyn_options['gyn_given_numbers'] ) > 0 ) {
                   foreach ( $gyn_options['gyn_given_numbers'] as $gyn_reg_users ){
                        echo '<tr>'; 
                        echo '<td>' . $gyn_reg_users[0] . '</td>';
                        echo '<td>' . $gyn_reg_users[1] . '</td>';
                        echo '<td>' . $gyn_reg_users[2] . '</td>';
                        echo '<tr>';
                    }
                }
               ?>
			</table>
       </div>
		<?php
	}
	
 	function gyn_validate_options( $input ) {
		
		$input['version'] = VERSION;
		
		return $input;
	}
	
	function gyn_main_setting_section_callback() {
		
		_e('Admin configuration section', 'get-your-number');
		
    }
	
	function gyn_display_text_field( $data = array() ) {
		
		extract( $data );
		$gyn_options = get_option( 'gyn_options' );
		
		?>
		<input type="text" name="gyn_options[<?php echo $name; ?>]" value="<?php echo esc_html( $gyn_options[$name] ); ?>"/><br />
		<?php
    }
	
	function gyn_display_check_box( $data = array() ) {
		extract ( $data );
		$gyn_options = get_option( 'gyn_options' );
		?>
		<input type="checkbox" name="gyn_options[<?php echo $name; ?>]" <?php if ( $gyn_options[$name] ) echo ' checked="checked"'; ?>/>
		<?php
    }
?>