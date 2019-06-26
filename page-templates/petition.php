<?php
/**
 * Template Name: Petition
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

// engaging assets
$engaging_settings = get_option( 'p4en_main_settings' );
$engaging_token = $engaging_settings['p4en_frontend_public_api'];

// check if external link is set, and redirect in this case
$external_link = $page_meta_data['p4-gpea_petition_external_link'][0] ?? '';
if ( $external_link ) {
	wp_redirect( $external_link );
	exit;

}


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
$context['custom_body_classes']         = $categories;
$context['engaging_page_id']            = $page_meta_data['p4-gpea_petition_engaging_pageid'][0] ?? '';
$context['petition_target']             = $page_meta_data['p4-gpea_petition_engaging_target'][0] ?? '';

if ( $context['engaging_page_id'] ) {
	global $wp_version;
	$url = 'http://www.e-activist.com/ea-dataservice/data.service?service=EaDataCapture&token=' . $engaging_token . '&campaignId=' . $context['engaging_page_id'] . '&contentType=json&resultType=summary';
	echo $url;
	$args = array(
		'timeout'     => 5,
		'redirection' => 5,
		'httpversion' => '1.0',
		'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
		'blocking'    => true,
		'headers'     => array(),
		'cookies'     => array(),
		'body'        => null,
		'compress'    => false,
		'decompress'  => true,
		'sslverify'   => true,
		'stream'      => false,
		'filename'    => null,
	);
	$result = wp_remote_get( $url, $args );
	$obj = json_decode( $result['body'], true );
	$context['signatures'] = $obj['rows'][0]['columns'][4]['value'];

}

if ( $context['petition_target'] && $context['signatures'] ) {
	$context['percentage'] = intval( intval( $context['signatures'] ) * 100 / intval( $context['petition_target'] ) );
} else {
	$context['percentage'] = 100;
}

Timber::render( [ 'petition.twig' ], $context );
