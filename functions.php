<?php
/**
 * Additional code for the child theme goes in here.
 *
 * @package P4CT
 */

require_once( __DIR__ . '/classes/class-p4ct-site.php' );

$services = [
	'P4CT_Lang',
	'P4CT_Metabox_Register',
	'P4CT_Custom_Post_Type_Register',
	'P4CT_AJAX_Handler',
	'P4CT_ElasticSearch',
	'P4CT_Shortcode',
];

/*
/* move jquery core script to footer */
add_action('wp_enqueue_scripts', 'wp_enqueue_child', 100);
function wp_enqueue_child() {
	wp_dequeue_script('jquery-core');
	wp_deregister_script('cssvarsponyfill');
	wp_register_script( 'cssvarsponyfill', 'https://cdnjs.cloudflare.com/ajax/libs/css-vars-ponyfill/2.3.1/css-vars-ponyfill.min.js', [], '2', true );
}

new P4CT_Site( $services );

/*
/* custom html tages for SEO */
add_filter( 'img_caption_shortcode', 'my_img_caption_shortcode', 10, 3 );
function my_img_caption_shortcode( $empty, $attr, $content ) {

	$atts = shortcode_atts( array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => ''
	), $attr );

	if ( 1 > (int) $atts['width'] || empty( $atts['caption'] ) ) {
		return '';
	}

	$image_id = trim( str_replace( 'attachment_', '', $atts['id'] ) );
	$meta = get_post_meta( $image_id );
	$image_credit = ( isset( $meta['_credit_text'] ) && ! empty( $meta['_credit_text'] ) ) ? ' ' . $meta['_credit_text'][0] : '';
	$class = trim( 'wp-caption ' . $atts['align'] );

	if ( $atts['id'] ) {
		$atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';
	}


	$output = '<figure ' . $attr['id'] . ' class="' . esc_attr( $class ) . '" style="max-width: ' . ( (int) $attr['width'] ) . 'px;">'
	          . do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $attr['caption'] . $image_credit . '</figcaption></figure>';

	return $output;
}

/*
/* custom RSS feed for Asia */
remove_action( 'do_feed_rss2', 'do_feed_rss2', 10 );
add_action( 'do_feed_rss2', 'asia_do_feed_rss2' );
function asia_do_feed_rss2() {
	load_template( __DIR__ . '/rss.php' ); 
}

/*
/* redirect author page */
add_action( 'template_redirect', 'my_redirect' );
function my_redirect() {
	if ( is_author() ) {
		wp_redirect( get_option('home'), 301 );
	}
}

/*
/* collapsible notes for articles */

// display on frontend
add_shortcode( 'note', 'article_note_shortcode' );
function article_note_shortcode( $attr = [], $content = NULL ) {
	$attr = shortcode_atts( [ 'title' => NULL ], $attr, 'note' );
	return '<span class="article-note"><span class="article-note__title">' . $attr[ 'title' ] . '</span><span class="article-note__content">' . $content . '</span></span>';
}

// add buttons to visual editors
add_action( 'after_setup_theme', 'article_note_theme_setup' );
function article_note_theme_setup(){
	add_action( 'admin_init', 'article_note_add_editor_styles' );
	add_action( 'init', 'article_note_add_editor_buttons' );
}
function article_note_add_editor_styles() {
	add_editor_style( 'tinymce_article_note_buttons.css' );
}
function article_note_add_editor_buttons() {
	if ( ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) || get_user_option( 'rich_editing' ) !== 'true' ) {
		return;
	}
	add_filter( 'mce_external_plugins', 'article_note_add_editor_script' );
	add_filter( 'mce_buttons', 'article_note_register_editor_button' );
}
function article_note_add_editor_script( $plugin_array ) {
	$plugin_array['article_notes'] = get_stylesheet_directory_uri() . '/tinymce_article_note_buttons.js';
	return $plugin_array;
}
function article_note_register_editor_button( $buttons ) {
	array_push( $buttons, 'article_notes' );
	return $buttons;
}

// add buttons to basic editors
add_action( 'admin_enqueue_scripts', 'article_note_add_editor_quicktag' );
function article_note_add_editor_quicktag(){
	wp_enqueue_script( 'quicktag-article-note-buttons', get_stylesheet_directory_uri() . '/quicktag_article_note_buttons.js', array( 'jquery', 'quicktags' ), '1.0.0', TRUE );
}