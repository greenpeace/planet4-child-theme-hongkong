<?php
/**
 * Template Name: Main Issue
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

$issues = get_category_by_slug( 'issues' );
$issues = $issues->term_id;
$categories = get_the_category( $post->ID );
$categories = array_filter( $categories , function( $cat ) use ( $issues ) {
    return $cat->category_parent === $issues;
});
$categories = array_reduce( $categories, function( $acc, $cat ) {
    return $acc . ( $cat->slug ) . ' ';
}, '');

if ( has_post_thumbnail( $post->ID ) ) {
    $img_id = get_post_thumbnail_id( $post->ID );
    $img_data = wp_get_attachment_image_src( $img_id , 'medium_large' );
    $post->img_url = $img_data[0];
}

$context['post']                        = $post;
$context['header_title']                = is_front_page() ? '' : ( $page_meta_data['p4_title'][0] ?? $post->title );
$context['header_subtitle']             = $page_meta_data['p4_subtitle'][0] ?? '';
// $context['header_description']          = wpautop( $page_meta_data['p4_description'][0] ) ?? '';
$context['header_button_title']         = $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']          = $page_meta_data['p4_button_link'][0] ?? '';
// $context['header_button_link_checkbox'] = $page_meta_data['p4_button_link_checkbox'];
$context['background_image']            = wp_get_attachment_url( get_post_meta( get_the_ID(), 'background_image_id', 1 ) );
$context['custom_body_classes']         = $categories;

Timber::render( [ 'main-issue.twig' ], $context );