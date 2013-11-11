<?php
/********************************************************
 * Functions admin settings for plugin get-your-number
*/

// load css styles & scripts for the admin area
function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = plugins_url() . '/get-your-number/css/gyn_styles.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}

add_action('admin_head', 'admin_register_head');

function gyn_load_admin_scripts() {
	global $current_screen;
	global $gyn_options_page;
	
	if ( $current_screen->id == $gyn_options_page ) {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}
}

add_action( 'admin_enqueue_scripts', 'gyn_load_admin_scripts' );



/****************************************
 * functions used for admin settings page
*/

function gyn_admin_init() {
	add_action( 'admin_post_save_gyn_options', 'process_gyn_options' );
}

add_action( 'admin_init', 'gyn_admin_init' );

function gyn_settings_menu() {
	global $gyn_options_page;
	
	// add gyn settings as submenu item in the admin area
	$gyn_options_page = add_options_page( 'GYN Configuration', 'Get Your Number', 'manage_options', 'gyn-configuration', 'gyn_config_page' );
	if ( $gyn_options_page ) {
		add_action( 'load-' . $gyn_options_page, 'gyn_help_tabs' );
	}
}

add_action( 'admin_menu', 'gyn_settings_menu' );

// save settings in options table
function process_gyn_options() {
	// Check user security level
	if ( !current_user_can( 'manage_options' ) ) wp_die( _e('No permission for you to change options', 'get-your-number') );
	
	// Check nonce field created in configuration form
	check_admin_referer( 'gyn-settings' );
	
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
	$options['gyn_available_numbers'] = range( $options['gyn_min_nr'], $options['gyn_max_nr'] );
	
	// save the new values in the options table;
	update_option( 'gyn_options', $options );
	
	// Store updated options array to databaseupdate_option( 'gyn_options', $options );
	// Redirect the page to the configuration form that was processed
	wp_redirect( add_query_arg( array( 'page' => 'gyn-configuration', 'message' => '1' ), admin_url( 'options-general.php' ) ) );
	exit;
}

// markup for the admin page shown when submenu gyn configurations is clicked
function gyn_config_page() { 
	 global $gyn_options;
	 global $gyn_options_page;
	 
	 $gyn_options = get_option( 'gyn_options' );
	 ?>
    <div id="gyn-general" class="wrap">
    
        <div class="admin-settings-box">
        
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('GYN settings | GYN version', 'get-your-number'); ?> <?php echo $gyn_options['gyn_version']; ?></h2>
            
            <?php if (isset( $_GET['message'] ) && $_GET['message'] == '1') { ?>
                <div id='message' class='updated fade'>
                	<p><strong>Settings Saved</strong></p>
                </div>
            <?php } ?>
            
            <form action="admin-post.php" method="post">
                <input type="hidden" name="action" value="save_gyn_options" />
                <!-- Adding security through hidden referrer field -->
                <?php wp_nonce_field( 'gyn-settings' ); ?>
                <!-- Security fields for meta box save processing -->
				  <!-- <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?> -->
                <!-- <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?> -->
                
                <div id="poststuff" class="metabox-holder">
                    <div id="post-body">
                        <div id="post-body-content">
								<?php do_meta_boxes( $gyn_options_page, 'normal', $gyn_options) ; ?>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>
            </form>
            <script type="text/javascript">
                //<![CDATA[
                jQuery( document ).ready( function( $ ) {
                    // close postboxes that should be closed
                    $( '.if-js-closed' ) .removeClass( 'if-js-closed' ). addClass( 'closed' );
                    // postboxes setup
                    postboxes.add_postbox_toggles ( '<?php echo $gyn_options_page; ?>' );
                });
                //]]>
            </script>

        </div>
   </div>
	<?php
}

/************************************
 * metabox for admin form to edit gyn settings
*/
function gyn_plugin_meta_box( $gyn_options ) {
	?>
	<table class="widefat gyn-reg-users-table" id="reg_users" >
		<tbody>
			<tr>
			  <td class="form-field-label" ><strong><?php _e('Admin email', 'get-your-number'); ?></strong></td>
			  <td><input type="text" name="gyn_admin_email" value="<?php echo $gyn_options['gyn_admin_email']; ?>"/></td>              
			</tr>
			<tr>
			  <td><strong><?php _e('Start number', 'get-your-number'); ?></strong></td>
			  <td><input type="text" name="gyn_min_nr" value="<?php echo $gyn_options['gyn_min_nr']; ?>"/></td>              
			</tr>
			<tr>
			  <td><strong><?php _e('End number', 'get-your-number'); ?></strong></td>
			  <td><input type="text" name="gyn_max_nr" value="<?php echo $gyn_options['gyn_max_nr']; ?>"/></td>              
			</tr>
			<tr>
			  <td><strong><?php _e('Available numbers', 'get-your-number'); ?> (<?php echo count( $gyn_options['gyn_available_numbers'] ); ?>)</strong></td>
			  <td><?php echo implode(", ", $gyn_options['gyn_available_numbers']); ?></td>              
			</tr>
			<tr>
			  <td><strong><?php _e('Event name', 'get-your-number') ?></strong></td>
			  <td><input type="text" name="gyn_event_name" value="<?php echo $gyn_options['gyn_event_name']; ?>"/>
            	<input type="hidden" name="gyn_version" value="<?php echo VERSION; ?>" />
            	<!-- <input type="hidden" name="gyn_given_numbers" value="" /> --></td>              
			</tr>
		</tbody>
	</table>
	<table class="widefat gyn-admin-warning-table" id="admin-warnings">
       <tr> 
       		<td class="alert"><strong><?php _e('WARNING! Changing the settings will also clear the saved registrations!', 'get-your-number'); ?></strong></td>
       <tr>
    </table>
	<input type="submit" value="<?php _e( 'Save changes', 'get-your-number'); ?>" class="button-primary"/>
	<?php
}

/************************************
 * metabox to show subscribers
 */
function gyn_subscribers_meta_box( $gyn_options ) { ?>
	<table class="widefat gyn-reg-users-table" id="reg_users" >
        <thead>
            <tr>
              <th><strong><?php _e('Name','get_your_number') ?></strong></th>
              <th><strong><?php _e('Number','get_your_number') ?></strong></th>
              <th><strong><?php _e('Email','get_your_number') ?></strong></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
              <th><strong><?php _e('Name','get_your_number') ?></strong></th>
              <th><strong><?php _e('Number','get_your_number') ?></strong></th>
              <th><strong><?php _e('Email','get_your_number') ?></strong></th>
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
<?php }


/************************************
 * functions used for admin help tabs
*/
function gyn_help_tabs() {
	global $gyn_options_page;
	
	$screen = get_current_screen();
	$screen -> add_help_tab( array( 'id' => 'gyn-plugin-help-instructions', 'title' => 'Instructions', 'callback' => 'gyn_plugin_help_instructions' 	) );
	//$screen -> add_help_tab( array( 'id' => 'gyn-plugin-help-faq', 	'title' => 'FAQ', 'callback' => 'gyn_plugin_help_faq', ) );
	$screen -> set_help_sidebar( '<p><a href="https://github.com/punchcreative/get-your-number" target="_blank" title="Github dev page">Github punchcreative</a></p><img src="' . plugins_url() . '/get-your-number/img/punchreative.png" width="125" height="33" alt="Punch Creative"/>' );
	
	// use meta boxes for the admin area
	add_meta_box('gyn_general_meta_box', __('Main Settings', 'get-your-number'), 'gyn_plugin_meta_box', $gyn_options_page, 'normal', 'core');
	add_meta_box('gyn_second_meta_box', __('Overview of subscribers', 'get-your-number'), 'gyn_subscribers_meta_box', $gyn_options_page, 'normal', 'core');
}

function gyn_plugin_help_instructions() {
	echo "<p>" . __('The plugin works with a shortcode. In a post or page you can use [gyn/] to display a form where subscribers can leave their name and email to get their number. Name and email are not to be left empty by the subscriber, this is checked during entry. If the email adres is not already registred, the user gets a random number by email. After registration the name, number and email address are stored in the Wordpress option table. Be sure to setup the plugin before activating the page or post with the shortcode in it. Good luck, let\'s play bingo.', 'get-your-number') . "</p>";
}

function gyn_plugin_help_faq() {
	echo "<p>" . __('At this moment there are no most frequently asked questions about this plugin.', 'get-your-number') . "</p>";
}

/**
 * function called after admin changes settings and want to save them
*/

?>
