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

