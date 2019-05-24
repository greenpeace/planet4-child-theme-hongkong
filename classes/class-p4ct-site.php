<?php
/**
 * P4 Site Class
 *
 * @package P4CT
 */

/**
 * Class P4CT_Site.
 * The main class that handles Planet4 Child Theme.
 */
class P4CT_Site {

	/**
	 * Services
	 *
	 * @var array $services
	 */
	protected $services;

	/**
	 * P4CT_Site constructor.
	 *
	 * @param array $services The dependencies to inject.
	 */
	public function __construct( $services = [] ) {

		$this->load();
		$this->settings();
		$this->hooks();
		$this->services( $services );

	}

	/**
	 * Load required files.
	 */
	protected function load() {
		/**
		 * Class names need to be prefixed with P4CT and should use capitalized words separated by underscores.
		 * Any acronyms should be all upper case.
		 */
		spl_autoload_register(
			function ( $class_name ) {
				if ( strpos( $class_name, 'P4CT_' ) !== false ) {
					$file_name = 'class-' . str_ireplace( [ 'P4CT\\', '_' ], [ '', '-' ], strtolower( $class_name ) );
					require_once __DIR__ . '/' . $file_name . '.php';
				}
			}
		);
	}

	/**
	 * Define settings for the Planet4 Child Theme.
	 */
	protected function settings() {
	}

	/**
	 * Hooks the theme.
	 */
	protected function hooks() {
		add_filter( 'timber_context', [ $this, 'add_to_context' ] );
		add_filter( 'get_twig', [ $this, 'add_to_twig' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ], 2 );
		// add_action( 'pre_get_posts', [ $this, 'add_search_options' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
		add_action( 'after_setup_theme', [ $this, 'add_oembed_filter' ] ); // TODO OURS
		add_action( 'cmb2_admin_init', [ $this, 'register_sidebar_metabox_child' ] ); // TODO OURS
		add_filter( 'cmb2_show_on', [ $this, 'be_taxonomy_show_on_filter' ], 10, 2 ); // TODO OURS
		// add_action( 'save_post', [ $this, 'p4_auto_generate_excerpt' ], 10, 2 );
		register_nav_menus(
			[
				'navigation-bar-menu' => __( 'Navigation Bar Menu', 'planet4-child-theme-backend' ),
			]
		);

	}

	/**
	 * Filters the oEmbed process to run the responsive_embed() function
	 */
	public function add_oembed_filter(	) {
		add_filter('embed_oembed_html', [ $this, 'responsive_embed' ], 10, 3);
	}

	/**
	 * Adds a responsive embed wrapper around oEmbed content
	 * @param  string $html The oEmbed markup
	 * @param  string $url	The URL being embedded
	 * @param  array  $attr An array of attributes
	 * @return string		Updated embed markup
	 */
	public function responsive_embed($html, $url, $attr) {
		return $html!=='' ? '<div class="embed-container">'.$html.'</div>' : '';
	}

	/**
	 * Inject dependencies.
	 *
	 * @param array $services The dependencies to inject.
	 */
	private function services( $services = [] ) {
		if ( $services ) {
			foreach ( $services as $service ) {
				$this->services[ $service ] = new $service();
			}
		}
	}

	/**
	 * Gets the loaded services.
	 *
	 * @return array The loaded services.
	 */
	public function get_services() : array {
		return $this->services;
	}

	/**
	 * Adds more data to the context variable that will be passed to the main template.
	 *
	 * @param array $context The associative array with data to be passed to the main template.
	 *
	 * @return mixed
	 */
	public function add_to_context( $context ) {
		global $wp;
		// $context['antani'] = 'scappellamento';
		// $options = get_option( 'planet4_tarapia' );
		// $context['sbiriguda'] = $options['brematurata'] ?? '';
		return $context;
	}

	/**
	 * Add your own functions to Twig.
	 *
	 * @param Twig_ExtensionInterface $twig The Twig object that implements the Twig_ExtensionInterface.
	 *
	 * @return mixed
	 */
	public function add_to_twig( $twig ) {
		// $twig->addExtension( new Twig_Scappella_Destra() );
		// $twig->addFilter( new Twig_Filtra_Scappella( 'svgicon', [ $this, 'svgicon' ] ) );
		return $twig;
	}

	/**
	 * Dequeue parent theme assets for subsequent override.
	 * Uncomment as needed.
	 */
	public function dequeue_parent_assets() {
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

	/**
	 * Load styling and behaviour on admin pages.
	 *
	 * @param string $hook Hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		// TODO check that this one still works if not hooked on 'init'
		add_editor_style( get_stylesheet_directory_uri().'/admin/css/admin_editor_style.css' );

		$css_creation = filectime(get_stylesheet_directory() . '/admin/css/admin_style.css');
		wp_enqueue_style( 'admin-child-style', get_stylesheet_directory_uri() . '/admin/css/admin_style.css', [], $css_creation );
	}

	/**
	 * Load styling and behaviour on website pages.
	 *
	 * @param string $hook Hook.
	 */
	public function enqueue_public_assets( $hook ) {
		$css_creation = filectime(get_stylesheet_directory() . '/static/css/style.css');
		$js_creation = filectime(get_stylesheet_directory() . '/static/js/script.js');

		wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/static/css/style.css', [], $css_creation );
		wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/static/js/script.js', array(), $js_creation, true );
	}

	/**
	 * Registers taxonomies.
	 */
	public function register_taxonomies() {

		$labels = array(
			'name'				=> _x( 'Special attributes', 'taxonomy general name', 'planet4-child-theme-backend'),
			'singular_name'		=> _x( 'Special attribute', 'taxonomy singular name', 'planet4-child-theme-backend' ),
			'search_items'		=> __( 'Search attributes', 'planet4-child-theme-backend' ),
			'all_items'			=> __( 'All attributes', 'planet4-child-theme-backend' ),
			'edit_item'			=> __( 'Edit attribute', 'planet4-child-theme-backend' ),
			'update_item'		=> __( 'Update attribute', 'planet4-child-theme-backend' ),
			'add_new_item'		=> __( 'Add New attribute', 'planet4-child-theme-backend' ),
			'new_item_name'		=> __( 'New attribute Name', 'planet4-child-theme-backend' ),
			'menu_name'			=> __( 'Attribute', 'planet4-child-theme-backend' ),
		);
		$args = array(
			'hierarchical'		=> true,
			'labels'			=> $labels,
			'show_ui'			=> true,
			'show_admin_column' => true,
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'attribute' ),
		);
		register_taxonomy( 'p4_post_attribute', array( 'post', 'page' ), $args );

	}

	/**
	 * Taxonomy show_on filter
	 * @author Bill Erickson
	 * @link https://github.com/CMB2/CMB2/wiki/Adding-your-own-show_on-filters
	 *
	 * @param bool $display
	 * @param array $metabox
	 * @return bool display metabox
	 */
	public function be_taxonomy_show_on_filter( $display, $meta_box ) {
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

	public function register_sidebar_metabox_child() {

		$prefix = 'p4_';

		$p4_sidebar = new_cmb2_box(
			[
				'id'		   => $prefix . 'metabox_sidebar',
				'title'		   => __( 'Post extra attributes', 'planet4-child-theme-backend' ),
				'object_types' => [ 'post' ], // Post type.
				// 'show_on' => array(
				//	'key' => 'taxonomy',
				//	'value' => array(
				//		'p4-page-type' => array( 'update' )
				//	)
				// ),
				'context'		=> 'side',
				'priority'		=> 'low'
			]
		);

		// funzione per recuperare posts e usare in backend con cmb2
		// da ottimizzare in secondo momento
		function generate_post_select($post_type,$post_attribute) {
			$post_type_object = get_post_type_object($post_type);
			$label = $post_type_object->label;
			$posts = get_posts(
				array(
					'post_type'		   => $post_type,
					'post_status'	   => 'publish',
					'suppress_filters' => false,
					'posts_per_page'   => -1,
					'tax_query'		   => array(
						array(
							'taxonomy' => 'p4_post_attribute',
							'field'	   => 'slug',
							'terms'	   => $post_attribute
						)
					)
				)
			);
			$output = array();
			foreach ($posts as $post) {
				$postid = $post->ID;
				$output[$postid] = $post->post_title;
			}
			return $output;
		}

			$p4_sidebar->add_field( array(
				'name'			   => esc_html__( 'Project related', 'cmb2' ),
				'desc'			   => esc_html__( 'Select a project connected to this post (optional)', 'cmb2' ),
				'id'			   => $prefix . 'select_project_related',
				'type'			   => 'select',
				'show_option_none' => true,
				'options'		   => generate_post_select('post','project')
			) );

		// add project related meta fields (for example percentage of project)

		$cmb_project = new_cmb2_box( array(
			'id'		   => 'p4-gpea-project-box',
			'title'		   => 'Information about current project',
			'object_types' => array( 'page' ), // post type
			'show_on'	   => array( 'key' => 'page-template', 'value' => 'page-templates/project.php' ),
			'context'	   => 'normal', //	'normal', 'advanced', or 'side'
			'priority'	   => 'high',  //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );

		$cmb_project->add_field( array(
			'name'			   => esc_html__( 'Start date', 'cmb2' ),
			'desc'			   => esc_html__( 'The date the project started (textual)', 'cmb2' ),
			'id'			   => 'p4-gpea_project_start_date',
			'type'			   => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'		 => 'intval',
		) );

		$cmb_project->add_field( array(
			'name'			   => esc_html__( 'Zone interested', 'cmb2' ),
			'desc'			   => esc_html__( 'Country, city or place involved by the project', 'cmb2' ),
			'id'			   => 'p4-gpea_project_localization',
			'type'			   => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'		 => 'intval',
		) );

		$cmb_project->add_field( array(
			'name'			   => esc_html__( 'Project percentage', 'cmb2' ),
			'desc'			   => esc_html__( 'Percentage of completition of the project', 'cmb2' ),
			'id'			   => 'p4-gpea_project_percentage',
			'type'			   => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'		 => 'intval',
		) );

		// add Tip extra field

		$cmb_tip = new_cmb2_box( array(
			'id'		   => 'p4-gpea-tip-box',
			'title'		   => 'Tip card',
			'object_types' => array( 'post' ), // post type
			'context'	   => 'normal', //	'normal', 'advanced', or 'side'
			'priority'	   => 'high',  //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
			'show_on' => array(
				'key' => 'taxonomy',
				'value' => array(
					'p4_post_attribute' => array( 'tip' )
				)
			),
		) );

		$cmb_tip->add_field( array(
			'name'			   => esc_html__( 'Frequency pledge', 'cmb2' ),
			'desc'			   => esc_html__( 'Will be displayed in the tip card, in the top', 'cmb2' ),
			'id'			   => 'p4-gpea_tip_frequency',
			'type'			   => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'		 => 'intval',
		) );

		$cmb_tip->add_field( array(
			'name'			   => esc_html__( 'Tip icon', 'cmb2' ),
			'desc'			   => esc_html__( 'Icon/image shown in the card', 'cmb2' ),
			'id'			   => 'p4-gpea_tip_icon',
			'type'			   => 'file',
			// Optional.
			'options'		   => [
				'url' => false,
			],
			'text'			   => [
				'add_upload_file_text' => __( 'Add Tip Image', 'planet4-child-theme-backend' ),
			],
			'query_args'	   => [
				'type' => 'image',
			],
			'preview_size' => 'small',
		) );

		// add Testimonial extra field

	$cmb_team = new_cmb2_box( array(
		'id'		   => 'p4-gpea-team-box',
		'title'		   => 'Team extra info',
		'object_types' => array( 'post' ), // post type
		'context'	   => 'normal', //	'normal', 'advanced', or 'side'
		'priority'	   => 'high',  //  'high', 'core', 'default' or 'low'
		'show_names'   => true, // Show field names on the left
		'show_on' => array(
			'key' => 'taxonomy',
			'value' => array(
				'p4-page-type' => array( 'team' )
			)
		),
	) );

	$cmb_team->add_field( array(
		'name'			   => esc_html__( 'Role', 'cmb2' ),
		'desc'			   => esc_html__( 'Role in the staff', 'cmb2' ),
		'id'			   => 'p4-gpea_team_role',
		'type'			   => 'text',
		// 'sanitization_cb' => 'intval',
		// 'escape_cb'		 => 'intval',
	) );

	$cmb_team->add_field( array(
		'name'			   => esc_html__( 'Citation', 'cmb2' ),
		'desc'			   => esc_html__( 'Will be displayed in testimonials carousel', 'cmb2' ),
		'id'			   => 'p4-gpea_team_citation',
		'type'			   => 'textarea',
		// 'sanitization_cb' => 'intval',
		// 'escape_cb'		 => 'intval',
	) );

		// add post related meta fields

		$cmb_post = new_cmb2_box( array(
			'id'		   => 'p4-gpea-post-box',
			'title'		   => 'Information about current post',
			'object_types' => array( 'post' ), // post type
			'context'	   => 'normal', //	'normal', 'advanced', or 'side'
			'priority'	   => 'high',  //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );

		$cmb_post->add_field( array(
			'name'			   => esc_html__( 'Reading time', 'cmb2' ),
			'desc'			   => esc_html__( 'Specify the time extimated to read the article (i.e. 4 min)', 'cmb2' ),
			'id'			   => 'p4-gpea_post_reading_time',
			'type'			   => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'		 => 'intval',
		) );

	}

	/**
	 * Add custom options to the main WP_Query.
	 *
	 * @param WP_Query $wp The WP Query to customize.
	 */
	public function add_search_options( WP_Query $wp ) {
	}

	/**
	 * Auto generate excerpt for post.
	 *
	 * @param int	  $post_id Id of the saved post.
	 * @param WP_Post $post Post object.
	 */
	public function p4_auto_generate_excerpt( $post_id, $post ) {
	}

}
