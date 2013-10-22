<?php

	// If uninstall not called from WordPress exit

	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit();
	}
	
	// Delete settings page options from options table
	
	if ( get_option( 'gyn_options' ) != false ) {
		delete_option( 'gyn_options' );
	}
?>