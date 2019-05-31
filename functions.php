<?php
/**
 * Additional code for the child theme goes in here.
 *
 * @package P4CT
 */

require_once( __DIR__ . '/classes/class-p4ct-site.php' );

$services = [
	'P4CT_Metabox_Register',
	'P4CT_Custom_Post_Type_Register',
];

new P4CT_Site( $services );

