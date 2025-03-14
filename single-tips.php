<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;

// Initializing variables.
$context = Timber::get_context();
/**
 * P4 Post Object
 *
 * @var P4_Post $post
 */
$post            = Timber::query_post( false, 'P4_Post' );
$context['post'] = $post;

// Set Navigation Issues links.
$post->set_issues_links();

// Get the cmb2 custom fields data
// Articles block parameters to populate the articles block
// p4_take_action_page parameter to populate the take action boxout block
// Author override parameter. If this is set then the author profile section will not be displayed.
$page_meta_data                 = get_post_meta( $post->ID );
$page_terms_data                = get_the_terms( $post, 'p4-page-type' );
$context['background_image']    = $page_meta_data['p4_background_image_override'][0] ?? '';
$take_action_page               = $page_meta_data['p4_take_action_page'][0] ?? '';
$context['page_type']           = $page_terms_data[0]->name ?? '';
$context['page_term_id']        = $page_terms_data[0]->term_id ?? '';
$context['page_category']       = $category->name ?? __( 'Post page', 'gpea_theme' );
$context['page_type_slug']      = $page_terms_data[0]->slug ?? '';
$context['social_accounts']     = $post->get_social_accounts( $context['footer_social_menu'] );
$context['og_title']            = $post->get_og_title();
$context['og_description']      = strip_tags( $post->get_og_description() );
$context['og_image_data']       = $post->get_og_image();
$context['custom_body_classes'] = 'white-bg';
$context['show_article_donation_launcher']    = $page_meta_data['p4-gpea_show_article_donation_launcher'][0] ?? '';

// reading time and categories info!
$context['reading_time_for_display']        = $page_meta_data['p4-gpea_post_reading_time'][0] ?? '';
$post->subtitle                 = $page_meta_data['p4-gpea_post_subtitle'][0] ?? '';
$post_categories     = get_the_terms( $post, 'category' );

$planet4_options = get_option( 'planet4_options' );

if ( ! $context['og_image_data'] ) {
	$tips_image = $page_meta_data['p4-gpea_tip_icon'][0] ?? '';
	if ( null !== $tips_image ) {		
		$context['og_image_data'][0] = $tips_image;
	}
}

/*
 for main issue relation we use categories */
// $context['categories'] = implode( ', ', $post->categories() );
do_action('enqueue_google_tag_manager_script', $context);
if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( [ 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ], $context );
}
