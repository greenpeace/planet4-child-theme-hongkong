<?php
/**
 * Template Name: Thankyou page for petition
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
 * @package	 WordPress
 * @subpackage	Timber
 * @since	 Timber 0.1
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
$tags	   = [];

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

/* get info from source page */

$context['supporter_name']  = ( get_query_var( 'fn' ) ) ? sanitize_text_field( get_query_var( 'fn' ) ) : false;

$pet = ( get_query_var( 'pet' ) ) ? intval( get_query_var( 'pet' ) ) : false;

if ( $pet ) {
	$context['source_title'] = get_the_title( $pet );
	$context['source_description'] = get_post_meta( $pet, 'p4_og_description', true );
	$context['source_link']  = get_the_permalink( $pet );

	if ( has_post_thumbnail( $pet ) ) {
		$img_id = get_post_thumbnail_id( $pet );
		$img_data = wp_get_attachment_image_src( $img_id, 'medium_large' );
		$context['source_thumb'] = $img_data[0];
	}
}

$main_issues = $gpea_extra->gpea_get_main_issue( $post->ID );
if ( $main_issues ) {
	$categories = $main_issues->slug;
} else {
	$categories = '';
}

$context['post']                        = $post;
$context['header_title']                = is_front_page() ? '' : ( $page_meta_data['p4_title'][0] ?? $post->title );
$context['header_subtitle']             = $page_meta_data['p4_subtitle'][0] ?? '';
// $context['header_description']		   = wpautop( $page_meta_data['p4_description'][0] ) ?? '';
$context['header_button_title']         = $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']          = $page_meta_data['p4_button_link'][0] ?? '';
// $context['header_button_link_checkbox'] = $page_meta_data['p4_button_link_checkbox'];
$context['background_image']            = wp_get_attachment_url( get_post_meta( get_the_ID(), 'background_image_id', 1 ) );
if ( $context['background_image'] ) {
	$context['body_extra_info'] = 'style="background-image: url(' . $context['background_image'] . ')"';
}
$context['custom_body_classes']         = $categories;


Timber::render( [ 'petition-thankyou.twig' ], $context );