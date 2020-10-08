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

//move jquery core script to footer
add_action('wp_enqueue_scripts', 'wp_enqueue_child', 100);
function wp_enqueue_child(){
	wp_dequeue_script('jquery-core');
	wp_enqueue_script('jquery-core', '/wp-includes/js/jquery/jquery.js',  array(), false, true);
	wp_deregister_script('cssvarsponyfill');
	wp_register_script( 'cssvarsponyfill', 'https://cdnjs.cloudflare.com/ajax/libs/css-vars-ponyfill/2.3.1/css-vars-ponyfill.min.js', [], '2', true );
}


new P4CT_Site( $services );

//custom html tages for SEO
add_filter( 'img_caption_shortcode', 'my_img_caption_shortcode', 10, 3 );
function my_img_caption_shortcode( $empty, $attr, $content ){
	$attr = shortcode_atts( array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => ''
	), $attr );

	if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
		return '';
	}

	if ( $attr['id'] ) {
		$attr['id'] = 'id="' . esc_attr( $attr['id'] ) . '" ';
	}

	return '<figure ' . $attr['id']
	. 'class="wp-caption ' . esc_attr( $attr['align'] ) . '" '
	. 'style="max-width: ' . ( 10 + (int) $attr['width'] ) . 'px;">'
	. do_shortcode( $content )
	. '<figcaption class="wp-caption-text">' . $attr['caption'] . '</figcaption>'
	. '</figure>';
}

// custom RSS feed for ASIA
remove_action( 'do_feed_rss2', 'do_feed_rss2', 10 );
function asia_do_feed_rss2() {
	load_template( __DIR__ . '/rss.php' ); 
}
add_action( 'do_feed_rss2', 'asia_do_feed_rss2' );