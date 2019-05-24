<?php
/**
 * Additional code for the child theme goes in here.
 *
 * @package P4CT
 */

require_once( __DIR__ . '/classes/class-p4ct-site.php' );

$services = [
	// 'P4CT_Metabox_Register',
	// 'P4CT_Custom_Taxonomy',
	// 'P4CT_Campaigns',
	// 'P4CT_Post_Campaign',
	// 'P4CT_Settings',
	// 'P4CT_Control_Panel',
	// 'P4CT_Post_Report_Controller',
	// 'P4CT_Cookies',
	// 'P4CT_Dev_Report',
];

new P4CT_Site( $services );

// TODO move this one to own class
require_once( __DIR__ . '/includes/gpea-settings.php' );

// add_action( 'wp_footer', function () {
//     if ( ! current_user_can( 'manage_options' ) ) {
//         return;
// 	}
// 	wp_cache_add( 'sticaz', 'sti gran ca', 'test:sticaztest', 600 );
//     $GLOBALS['wp_object_cache']->stats();
// } );
