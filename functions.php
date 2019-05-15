<?php

/**
 * Additional code for the child theme goes in here.
 */

/**
 * This will remove parent assets, uncomment as needed.
 * Register code can be found under parent theme at:
 * class-p4-master-site.php -> enqueue_public_assets
 */
function dequeue_parent_assets() {
    wp_dequeue_style( 'parent-style' );
    wp_deregister_style( 'parent-style' );
    wp_dequeue_style( 'bootstrap' );
    wp_dequeue_style( 'slick' );
    // wp_deregister_script( 'jquery' );
    wp_dequeue_script( 'popperjs' );
    wp_dequeue_script( 'bootstrapjs' );
    wp_dequeue_script( 'main' );
    wp_dequeue_script( 'slick' );
    wp_dequeue_script( 'hammer' );
}
add_action( 'wp_enqueue_scripts', 'dequeue_parent_assets', 20 );

function enqueue_child_frontend_styles() {
	$css_creation = filectime(get_stylesheet_directory() . '/static/css/style.css');
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/static/css/style.css', [], $css_creation );
}
add_action( 'wp_enqueue_scripts', 'enqueue_child_frontend_styles', 99);
add_action( 'admin_enqueue_scripts', 'enqueue_child_frontend_styles', 99);

function enqueue_child_backend_styles() {
	$css_creation = filectime(get_stylesheet_directory() . '/static/css/admin_style.css');
	wp_enqueue_style( 'admin-child-style', get_stylesheet_directory_uri() . '/static/css/admin_style.css', [], $css_creation );
}
add_action( 'admin_enqueue_scripts', 'enqueue_child_backend_styles', 99);


function enqueue_child_scripts() {
	$js_creation = filectime(get_stylesheet_directory() . '/static/js/script.js');
	wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/static/js/script.js', array(), $js_creation, true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_child_scripts', 99);

/* special styles for admin */
function enqueue_child_editor_styles() {
	add_editor_style( get_stylesheet_directory_uri().'/static/css/admin_editor_style.css' );
}
add_action( 'init', 'enqueue_child_editor_styles' );


/**
 * Taxonomy show_on filter
 * @author Bill Erickson
 * @link https://github.com/CMB2/CMB2/wiki/Adding-your-own-show_on-filters
 *
 * @param bool $display
 * @param array $metabox
 * @return bool display metabox
 */
function be_taxonomy_show_on_filter( $display, $meta_box ) {
	if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) {
		return $display;
	}

	if ( 'taxonomy' !== $meta_box['show_on']['key'] ) {
		return $display;
	}

	$post_id = 0;

	// If we're showing it based on ID, get the current ID
	if ( isset( $_GET['post'] ) ) {
		$post_id = $_GET['post'];
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = $_POST['post_ID'];
	}

	if ( ! $post_id ) {
		return $display;
	}

	foreach( (array) $meta_box['show_on']['value'] as $taxonomy => $slugs ) {
		if ( ! is_array( $slugs ) ) {
			$slugs = array( $slugs );
		}

		$display = false;
		$terms = wp_get_object_terms( $post_id, $taxonomy );
		foreach( $terms as $term ) {
			if ( in_array( $term->slug, $slugs ) ) {
				$display = true;
				break;
			}
		}

		if ( $display ) {
			break;
		}
	}

	return $display;
}
add_filter( 'cmb2_show_on', 'be_taxonomy_show_on_filter', 10, 2 );

function register_sidebar_metabox_child() {

	$prefix = 'p4_';

	$p4_sidebar = new_cmb2_box(
		[
			'id'           => $prefix . 'metabox_sidebar',
			'title'        => __( 'Post extra attributes', 'planet4-master-theme-backend' ),
			'object_types' => [ 'post' ], // Post type.
			// 'show_on' => array( 
			// 	'key' => 'taxonomy', 
			// 	'value' => array( 
			// 		'p4-page-type' => array( 'update' ) 
			// 	) 
			// ),
			'context'		=> 'side',
			'priority'		=> 'low'
		]
	);
	
	// funzione per recuperare posts e usare in backend con cmb2
	// da ottimizzare in secondo momento
	function generate_post_select($post_type) {
		$post_type_object = get_post_type_object($post_type);
		$label = $post_type_object->label;
		$posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1,'tax_query'=>array(array('taxonomy' => 'p4_post_attribute','field' => 'slug', 'terms' => 'project'))));
		$output = array();        
		foreach ($posts as $post) {
			$postid = $post->ID;
			$output[$postid] = $post->post_title;
		}
		return $output;
	}

	$p4_sidebar->add_field( array(
		'name'             => esc_html__( 'Project related', 'cmb2' ),
		'desc'             => esc_html__( 'Select a project connected to this post (optional)', 'cmb2' ),
		'id'               => $prefix . 'select_project_related',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => generate_post_select('post')
	) );

	// add project related meta fields (for example percentage of project)

	$cmb_project = new_cmb2_box( array(
		'id'           => 'p4-gpea-project-box',
		'title'        => 'Information about current project',
		'object_types' => array( 'page' ), // post type
		// 'show_on'      => array( 'key' => 'page-template', 'value' => 'project.php' ),
		'context'      => 'normal', //  'normal', 'advanced', or 'side'
		'priority'     => 'high',  //  'high', 'core', 'default' or 'low'
		'show_names'   => true, // Show field names on the left
	) );

	$cmb_project->add_field( array(
		'name'             => esc_html__( 'Project percentage', 'cmb2' ),
		'desc'             => esc_html__( 'Percentage of completition of the project', 'cmb2' ),
		'id'               => 'p4-gpea_project_percentage',
		'type'             => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		// 'sanitization_cb' => 'intval',
		// 'escape_cb'       => 'intval',
	) );

}


add_action( 'cmb2_admin_init', 'register_sidebar_metabox_child' );


// add_action( 'wp_footer', function () {
//     if ( ! current_user_can( 'manage_options' ) ) {
//         return;
// 	}
// 	wp_cache_add( 'sticaz', 'sti gran ca', 'test:sticaztest', 600 );
//     $GLOBALS['wp_object_cache']->stats();
// } );



///////  custom taxonomy, andrà tolta da qui, in attesa di decidere come strutturare functions child
// poi da vedere se riutilizziamo la loro funzione

add_action('init', 'p4_child_register_taxonomy');
function p4_child_register_taxonomy() {

	$labels = array(
		'name'              => _x( 'Special attributes', 'taxonomy general name', 'p4' ),
		'singular_name'     => _x( 'Special attribute', 'taxonomy singular name', 'p4' ),
		'search_items'      => __( 'Search attributes', 'p4' ),
		'all_items'         => __( 'All attributes', 'p4' ),
		'edit_item'         => __( 'Edit attribute', 'p4' ),
		'update_item'       => __( 'Update attribute', 'p4' ),
		'add_new_item'      => __( 'Add New attribute', 'p4' ),
		'new_item_name'     => __( 'New attribute Name', 'p4' ),
		'menu_name'         => __( 'Attribute', 'p4' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'attribute' ),
	);

	register_taxonomy( 'p4_post_attribute', array( 'post', 'page' ), $args );
}

// add settings gpea  

require_once( __DIR__ . '/includes/gpea-settings.php' );

// add embedding responsive
function setup_theme(  ) {
    // Filters the oEmbed process to run the responsive_embed() function
    add_filter('embed_oembed_html', 'responsive_embed', 10, 3);
}
add_action('after_setup_theme', 'setup_theme');

/**
 * Adds a responsive embed wrapper around oEmbed content
 * @param  string $html The oEmbed markup
 * @param  string $url  The URL being embedded
 * @param  array  $attr An array of attributes
 * @return string       Updated embed markup
 */
function responsive_embed($html, $url, $attr) {
    return $html!=='' ? '<div class="embed-container">'.$html.'</div>' : '';
}