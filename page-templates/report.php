<?php
/**
 * Template Name: Report
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
$page_meta_data = get_post_meta( $post->ID );

// Set Navigation Issues links.
// $post->set_issues_links();
// Get Navigation Campaigns links.
// $page_tags = wp_get_post_tags( $post->ID );
// $tags      = [];
// if ( is_array( $page_tags ) && $page_tags ) {
// foreach ( $page_tags as $page_tag ) {
// $tags[] = [
// 'name' => $page_tag->name,
// 'link' => get_tag_link( $page_tag ),
// ];
// }
// $context['campaigns'] = $tags;
// }
$context['post']                        = $post;
$context['header_title']                = is_front_page() ? '' : ( $page_meta_data['p4_title'][0] ?? $post->title );
$context['header_subtitle']             = $page_meta_data['p4_subtitle'][0] ?? '';
// $context['header_description']		    = wpautop( $page_meta_data['p4_description'][0] ) ?? '';
$context['header_button_title']         = $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']          = $page_meta_data['p4_button_link'][0] ?? '';
// $context['header_button_link_checkbox'] = $page_meta_data['p4_button_link_checkbox'];
$context['background_image']            = wp_get_attachment_url( get_post_meta( get_the_ID(), 'background_image_id', 1 ) );
$context['custom_body_classes']         = 'single-report-page';
$context['page_category']               = 'About Page';

$context['og_title']                = $post->get_og_title();
$context['og_description']          = $post->get_og_description();
$context['og_image_data']           = $post->get_og_image();

$extra_content                          = $page_meta_data['p4-gpea_page_extra_content'][0] ?? '';
$context['extra_content']               = $extra_content ? wpautop( $extra_content ) : '';

// P4 Campaign/dataLayer fields.
$context['cf_campaign_name'] = $page_meta_data['p4_campaign_name'][0] ?? '';
$context['cf_basket_name']   = $page_meta_data['p4_basket_name'][0] ?? '';
$context['cf_scope']         = $page_meta_data['p4_scope'][0] ?? '';
$context['cf_department']    = $page_meta_data['p4_department'][0] ?? '';

$context['strings'] = [
	'follow' => __( 'Follow', 'gpea_theme' ),
];

/* build about menu */

do_action('enqueue_google_tag_manager_script', $context);
Timber::render( [ 'about.twig' ], $context );
