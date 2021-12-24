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
$context['reading_time']        = $page_meta_data['p4-gpea_post_reading_time'][0] ?? '';
$post->subtitle                 = $page_meta_data['p4-gpea_post_subtitle'][0] ?? '';
$post_categories     = get_the_terms( $post, 'category' );

$gpea_extra = new \P4CT_Site();
$main_issues_category_id = $gpea_extra->gpea_get_main_issue_parent_id();

$context['post_categories'] = '';
if ( $post_categories ) {
	foreach ( $post_categories as $post_category ) {
		$context['post_categories'] .= $post_category->slug . ' ';
		if ( ( $main_issues_category_id ) && ( intval( $post_category->parent ) === intval( $main_issues_category_id ) ) ) {
			$context['main_issue'] = $post_category->name;
			$context['main_issue_id'] = $post_category->term_id;
			$context['main_issue_slug'] = $post_category->slug;
			$context['main_issue_url'] = get_category_link( $post_category->term_id );
		}
	}
}

$gpea_extra = new P4CT_Site();

/*
/* Donation buttons */

// Get button options

$donation_button_options = get_option( 'gpea_donation_button_options' );

$donation_button_default_link = '';
if( isset( $context['main_issue_slug'] ) && isset( $donation_button_options['gpea_donation_button_' . $context['main_issue_slug'] . '_button_link'] ) && @strlen( $donation_button_options['gpea_donation_button_' . $context['main_issue_slug'] . '_button_link'] ) ) {
	$donation_button_default_link = $donation_button_options['gpea_donation_button_' . $context['main_issue_slug'] . '_button_link'];
}
elseif( isset( $donation_button_options['gpea_donation_button_default_button_link'] ) ) {
	$donation_button_default_link = $donation_button_options['gpea_donation_button_default_button_link'];
}

$donation_button_default_text = '';
if( isset( $context['main_issue_slug'] ) && isset( $donation_button_options['gpea_donation_button_' . $context['main_issue_slug'] . '_button_text'] ) && @strlen( $donation_button_options['gpea_donation_button_' . $context['main_issue_slug'] . '_button_text'] ) ) {
	$donation_button_default_text = $donation_button_options['gpea_donation_button_' . $context['main_issue_slug'] . '_button_text'];
}
elseif( isset( $donation_button_options['gpea_donation_button_default_button_text'] ) ) {
	$donation_button_default_text = $donation_button_options['gpea_donation_button_default_button_text'];
}

$top_donation_button_html = '';
if ( isset( $page_meta_data['p4-gpea_show_article_top_donation_button'][0] ) && $page_meta_data['p4-gpea_show_article_top_donation_button'][0] ) {

	$donation_button_link = '';
	if( isset( $page_meta_data['p4-article_top_donation_button_link'][0] ) && @strlen( $page_meta_data['p4-article_top_donation_button_link'][0] ) ) {
		$donation_button_link = $page_meta_data['p4-article_top_donation_button_link'][0];
	}
	else {
		$donation_button_link = $donation_button_default_link;
	}

	$donation_button_text = '';
	if( isset( $page_meta_data['p4-article_top_donation_button_text'][0] ) && @strlen( $page_meta_data['p4-article_top_donation_button_text'][0] ) ) {
		$donation_button_text = $page_meta_data['p4-article_top_donation_button_text'][0];
	}
	else {
		$donation_button_text = $donation_button_default_text;
	}

	$donation_button_link = $gpea_extra->add_post_ref_query_to_link_url( $donation_button_link, $post );

	$top_donation_button_html = Timber::compile( 'blocks/donation-button.twig', [
		'donation_button_link' => $donation_button_link,
		'donation_button_text' => $donation_button_text,
	] );

}

$bottom_donation_button_html = '';
if ( isset( $page_meta_data['p4-gpea_show_article_bottom_donation_button'][0] ) && $page_meta_data['p4-gpea_show_article_bottom_donation_button'][0] ) {

	$donation_button_link = '';
	if( isset( $page_meta_data['p4-article_bottom_donation_button_link'][0] ) && @strlen( $page_meta_data['p4-article_bottom_donation_button_link'][0] ) ) {
		$donation_button_link = $page_meta_data['p4-article_bottom_donation_button_link'][0];
	}
	else {
		$donation_button_link = $donation_button_default_link;
	}

	$donation_button_text = '';
	if( isset( $page_meta_data['p4-article_bottom_donation_button_text'][0] ) && @strlen( $page_meta_data['p4-article_bottom_donation_button_text'][0] ) ) {
		$donation_button_text = $page_meta_data['p4-article_bottom_donation_button_text'][0];
	}
	else {
		$donation_button_text = $donation_button_default_text;
	}

	$donation_button_link = $gpea_extra->add_post_ref_query_to_link_url( $donation_button_link, $post );

	$bottom_donation_button_html = Timber::compile( 'blocks/donation-button.twig', [
		'donation_button_link' => $donation_button_link,
		'donation_button_text' => $donation_button_text,
	] );

}

// Find the position to insert buttons

libxml_use_internal_errors(true);
$dom = new DomDocument();
$dom->loadHTML('<?xml encoding="UTF-8">' . $post->content);
$xpath = new DOMXPath($dom);

$top_donation_button_temp_tag = $dom->createTextNode('{{TOP_DONATION_BUTTON}}');
$bottom_donation_button_temp_tag = $dom->createTextNode('{{BOTTOM_DONATION_BUTTON}}');

$find_abstract_element = $xpath->query('//html/body/blockquote[1]|//html/body/div[@class="leader"]');
$content_abstract = NULL;
if( $find_abstract_element->length ) {
	$content_abstract = $find_abstract_element[0];
	if ( $content_abstract->nextSibling === NULL ) {
		$content_abstract->parentNode->appendChild($top_donation_button_temp_tag);
	}
	else {
		$content_abstract->parentNode->insertBefore($top_donation_button_temp_tag, $content_abstract->nextSibling);
	}
}

$find_last_element = $xpath->query('//html/body/*[last()]');
$content_footnote = NULL;
if( $find_last_element->length && $find_last_element[0]->tagName == 'ul' ) {
	$find_previous_element = $find_last_element[0];
	while( $find_previous_element->previousSibling ) {
		if( $find_previous_element->previousSibling instanceof DOMElement ) {
			if( $find_previous_element->previousSibling->tagName == 'h3' ) {
				$content_footnote = $find_previous_element->previousSibling;
				$content_footnote->parentNode->insertBefore($bottom_donation_button_temp_tag, $content_footnote);
			}
			break;
		}
		$find_previous_element = $find_previous_element->previousSibling;
	}
}

$post->content = str_replace( [
	'<?xml encoding="UTF-8">',
	'{{TOP_DONATION_BUTTON}}',
	'{{BOTTOM_DONATION_BUTTON}}',
], [
	'',
	$top_donation_button_html,
	$bottom_donation_button_html,
], $dom->saveHTML( $dom->documentElement ) );

if( $content_abstract === NULL ) {
	$post->content = $top_donation_button_html . $post->content;
}

if( $content_footnote === NULL ) {
	$post->content .= $bottom_donation_button_html;
}

/*
/* get useful theme options */
$options = get_option( 'gpea_options' );
$context['latest_link'] = isset( $options['gpea_default_latest_link'] ) ? get_page_link( $options['gpea_default_latest_link'] ) : site_url();

// sharing options
$context['share_options'] = isset( $options['gpea_sharing_options'] ) ? $options['gpea_sharing_options'] : '';


$context['filter_url'] = add_query_arg(
	[
		's'                                       => ' ',
		'orderby'                                 => 'relevant',
		'f[ptype][' . $context['page_type'] . ']' => $context['page_term_id'],
	],
	get_home_url()
);


// Build the shortcode for articles block.
if ( 'no' !== $post->include_articles ) {
	// $post->articles = "[shortcake_articles exclude_post_id='" . $post->ID . "' /]"; !
	$context['related_posts'] = $gpea_extra->gpea_get_related( $post->ID, 4, false, false, false, 'big' );
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
	// 'article_donation_launcher' => __( 'Article donation launcher', 'gpea_theme' ),
	// 'intro_donation_launcher' => __( 'Short intro to donation launcher', 'gpea_theme' ),
	'article_donation_launcher' => isset( $options['gpea_post_donation_launcher_title'] ) ? $options['gpea_post_donation_launcher_title'] : '',
	'intro_donation_launcher' => isset( $options['gpea_post_donation_launcher_text'] ) ? $options['gpea_post_donation_launcher_text'] : '',
	'donation_launcher_link' => isset( $options['gpea_post_donation_launcher_link'] ) ? $options['gpea_post_donation_launcher_link'] : '',
	'i_support' => isset( $options['gpea_post_donation_launcher_label'] ) ? $options['gpea_post_donation_launcher_label'] : '',
	'donation_launcher_img' => isset( $options['gpea_post_donation_launcher_image'] ) ? $options['gpea_post_donation_launcher_image'] : '',
	'donation_launcher_img_align_center' => isset( $options['gpea_post_donation_launcher_image_align_center'] ) ? $options['gpea_post_donation_launcher_image_align_center'] : '',
	'related_news' => __( 'Related news', 'gpea_theme' ),
	'share' => __( 'Share', 'gpea_theme' ),
];

$context['post_tags'] = implode( ', ', $post->tags() );

//full description
$context["full_description"] = $post->post_excerpt;
if(isset($page_meta_data['p4-gpea_post_meta_desc'])){
	$context["full_description"] = $page_meta_data['p4-gpea_post_meta_desc'][0] ?? '';
}elseif(preg_match('~<blockquote>([\s\S]+?)</blockquote>~', $post->content, $matches)){
	$context["full_description"] = trim(strip_tags($matches[1]));
}else{
	$context["full_description"] = mb_substr(strip_tags($post->content),0,300,"utf-8").'...';
}

/*
 for main issue relation we use categories */
// $context['categories'] = implode( ', ', $post->categories() );
if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( [ 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ], $context );
}
