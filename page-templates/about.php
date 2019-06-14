<?php
/**
 * Template Name: About
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
$page_meta_data = get_post_meta( $post->ID );

// Set Navigation Issues links.
// $post->set_issues_links();

// Get Navigation Campaigns links.
// $page_tags = wp_get_post_tags( $post->ID );
// $tags      = [];

// if ( is_array( $page_tags ) && $page_tags ) {
// 	foreach ( $page_tags as $page_tag ) {
// 		$tags[] = [
// 			'name' => $page_tag->name,
// 			'link' => get_tag_link( $page_tag ),
// 		];
// 	}
// 	$context['campaigns'] = $tags;
// }


$context['post']						= $post;
$context['header_title']				= is_front_page() ? '' : ( $page_meta_data['p4_title'][0] ?? $post->title );
$context['header_subtitle']				= $page_meta_data['p4_subtitle'][0] ?? '';
// $context['header_description']		    = wpautop( $page_meta_data['p4_description'][0] ) ?? '';
$context['header_button_title']			= $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']			= $page_meta_data['p4_button_link'][0] ?? '';
// $context['header_button_link_checkbox'] = $page_meta_data['p4_button_link_checkbox'];
$context['background_image']            = wp_get_attachment_url( get_post_meta( get_the_ID(), 'background_image_id', 1 ) );
$context['custom_body_classes']         = 'white-bg';
$context['page_category']               = 'About Page';

$extra_content                          = $page_meta_data['p4-gpea_page_extra_content'][0] ?? '';
$context['extra_content']				= $extra_content ? wpautop ( $extra_content  ) :  '';

/* build about menu */
$parent_id = wp_get_post_parent_id( $post->ID );
if ( $parent_id ) {
    $about_menu_args = array(
        'post_parent' => $parent_id,
        'post_type'   => 'page', 
        'numberposts' => 20,
        'post_status' => 'publish' 
    );
    $about_menu = get_children( $about_menu_args );
    if ( ! empty( $about_menu ) ) {
        foreach ( $about_menu as $about_menu_item ) {
            $about_menu_item->link = get_permalink( $about_menu_item->ID );
        }
        $context['about_menu'] = $about_menu;
    }
}

Timber::render( [ 'about.twig' ], $context );
