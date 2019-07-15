<?php
/**
 * Template Name: Project
 * The template for displaying evergreen pages.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;

$context        = Timber::get_context();
$post           = new P4_Post();
$gpea_extra     = new P4CT_Site();
$page_meta_data = get_post_meta( $post->ID );

// Set Navigation Issues links.
$post->set_issues_links();

// Get Navigation Campaigns links.
$page_tags = wp_get_post_tags( $post->ID );
$tags      = [];

if ( is_array( $page_tags ) && $page_tags ) {
	foreach ( $page_tags as $page_tag ) {
		$tags[] = [
			'name' => $page_tag->name,
			'link' => get_tag_link( $page_tag ),
		];
	}
	$context['campaigns'] = $tags;
}

if ( has_post_thumbnail( $post->ID ) ) {
	$img_id = get_post_thumbnail_id( $post->ID );
	$img_data = wp_get_attachment_image_src( $img_id, 'medium_large' );
	$post->img_url = $img_data[0];
}

// Calculate post percentage
$percent_complete = $post->custom['p4-gpea_project_percentage'] ?? 0;
$percent_complete = preg_match( '/^\d+$/' , $percent_complete ) ? intval( $percent_complete ) : 0;


$main_issues = $gpea_extra->gpea_get_main_issue( $post->ID );
if ( $main_issues ) {
	$main_issue_slug = $main_issues->slug;
	$main_issue_name = $main_issues->name;
}

$context['completion_percentage']       = $percent_complete;
$context['post']                        = $post;
$context['header_title']                = is_front_page() ? '' : ( $page_meta_data['p4_title'][0] ?? $post->title );
$context['header_subtitle']             = $page_meta_data['p4_subtitle'][0] ?? '';
// $context['header_description']		   = wpautop( $page_meta_data['p4_description'][0] ) ?? '';
$context['header_button_title']         = $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']          = $page_meta_data['p4_button_link'][0] ?? '';
// $context['header_button_link_checkbox'] = $page_meta_data['p4_button_link_checkbox'];
$context['background_image']            = wp_get_attachment_url( get_post_meta( get_the_ID(), 'background_image_id', 1 ) );
$context['custom_body_classes']         = $main_issue_slug ?? '';
$context['main_issue_name']             = $main_issue_name ?? '';
$context['project_percentage']          = $page_meta_data['p4-gpea_project_percentage'][0] ?? 0;
$context['stroke_dashoffset']           = $context['project_percentage'] ? 697.433 * ( ( 100 - $context['project_percentage'] ) / 100 ) : 0;
$context['start_date']                  = $page_meta_data['p4-gpea_project_start_date'][0] ?? '';
$context['localization']                = $page_meta_data['p4-gpea_project_localization'][0] ?? 0;

$context['strings'] = [
	'follow' => __( 'Follow', 'gpea_theme' ),
	'following' => __( 'Following', 'gpea_theme' ),
	'start_date' => __( 'Date of start', 'gpea_theme' ),
	'zones_interested' => __( 'Zones interested', 'gpea_theme' ),
];


Timber::render( [ 'project.twig' ], $context );
