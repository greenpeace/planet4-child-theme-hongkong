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
$context['og_description']      = $post->get_og_description();
$context['og_image_data']       = $post->get_og_image();
$context['custom_body_classes'] = 'white-bg';
$context['show_article_donation_launcher']    = $page_meta_data['p4-gpea_show_article_donation_launcher'][0] ?? '';

// reading time and categories info!
$context['reading_time']        = $page_meta_data['p4-gpea_post_reading_time'][0] ?? '';
$post_categories     = get_the_terms( $post, 'category' );

$planet4_options = get_option( 'planet4_options' );

$main_issues_category_id = isset( $planet4_options['issues_parent_category'] ) ? $planet4_options['issues_parent_category'] : false;
if ( ! $main_issues_category_id ) {
	$main_issues_category = get_term_by( 'slug', 'issues', 'category' );
	if ( $main_issues_category ) {
		$main_issues_category_id = $main_issues_category->term_id;
	}
}

$context['post_categories'] = '';
if ( $post_categories ) {
	foreach ( $post_categories as $post_category ) {
		$context['post_categories'] .= $post_category->slug . ' ';
		if ( ( $main_issues_category_id ) && ( intval( $post_category->parent ) === intval( $main_issues_category_id ) ) ) {
			$context['main_issue'] = $post_category->name;
			$context['main_issue_slug'] = $post_category->slug;
		}
	}
}

/*
/* get useful theme options */
$options = get_option( 'gpea_options' );
$context['latest_link'] = isset( $options['gpea_default_latest_link'] ) ? get_page_link( $options['gpea_default_latest_link'] ) : site_url();


$context['filter_url'] = add_query_arg(
	[
		's'                                       => ' ',
		'orderby'                                 => 'relevant',
		'f[ptype][' . $context['page_type'] . ']' => $context['page_term_id'],
	],
	get_home_url()
);


// Build the shortcode for articles block.
if ( 'yes' === $post->include_articles ) {
	// $post->articles = "[shortcake_articles exclude_post_id='" . $post->ID . "' /]"; !
	$gpea_extra = new P4CT_Site();
	$context['related_posts'] = $gpea_extra->gpea_get_related( $post->ID, 3 );
}

// Build the shortcode for take action boxout block
// Break the content to retrieve first 2 paragraphs and split the content if the take action page has been defined.
if ( ! empty( $take_action_page ) ) {
	$post->take_action_page   = $take_action_page;
	$post->take_action_boxout = "[shortcake_take_action_boxout take_action_page='$take_action_page' /]";
}

// Build an arguments array to customize WordPress comment form.
$comments_args = [
	'comment_notes_before' => '',
	'comment_notes_after'  => '',
	'comment_field'        => Timber::compile( 'comment_form/comment_field.twig' ),
	'submit_button'        => Timber::compile( 'comment_form/submit_button.twig' ),
	'title_reply'          => __( 'Leave Your Reply', 'gpea_theme' ),
	'fields'               => apply_filters(
		'comment_form_default_fields',
		[
			'author' => Timber::compile( 'comment_form/author_field.twig' ),
			'email'  => Timber::compile( 'comment_form/email_field.twig' ),
		]
	),
];

$context['comments_args']       = $comments_args;
$context['show_comments']       = comments_open( $post->ID );
$context['post_comments_count'] = get_comments(
	[
		'post_id' => $post->ID,
		'status'  => 'approve',
		'type'    => 'comment',
		'count'   => true,
	]
);

$context['strings'] = [
	'previous_page' => __( 'Previous page', 'gpea_theme' ),
	'time_to_read' => __( 'Time to read', 'gpea_theme' ),
	'author' => __( 'Author:', 'gpea_theme' ),
	'article_donation_launcher' => __( 'Article donation launcher', 'gpea_theme' ),
	'intro_donation_launcher' => __( 'Short intro to donation launcher', 'gpea_theme' ),
	'i_support' => __( 'I want to support', 'gpea_theme' ),
	'related_news' => __( 'Related news', 'gpea_theme' ),
	'share' => __( 'Share', 'gpea_theme' ),
];

$context['post_tags'] = implode( ', ', $post->tags() );
/*
 for main issue relation we use categories */
// $context['categories'] = implode( ', ', $post->categories() );
if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( [ 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ], $context );
}
