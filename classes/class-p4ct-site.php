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

	const LOCALES_ENABLE_SUBSCRIPTION_BUTTONS = [
		'zh_TW',
	];

	const LOCALES_ENABLE_ALT_HEADER = [
		'ko_KR',
	];

	const NAV_MENUS             = [
		'about' => [
			'msgid'  => 'WHO WE ARE',
			'depth'  => 1,
			'issues' => FALSE,
			'counter' => FALSE,
			'link' => TRUE,
		],
		'issues' => [
			'msgid'  => 'OUR WORK',
			'depth'  => 1,
			'issues' => TRUE,
			'counter' => FALSE,
			'link' => TRUE,
		],
		'involved' => [
			'msgid'  => 'GET INVOLVED',
			'depth'  => 2,
			'issues' => FALSE,
			'counter' => TRUE,
			'link' => TRUE,
		],
		'news' => [
			'msgid'  => 'NEWS & STORIES',
			'depth'  => 1,
			'issues' => FALSE,
			'counter' => FALSE,
			'link' => TRUE,
		],
	];

	/**
	 * Allowed subscription buttons or not
	 *
	 * @var bool
	 */
	public $allowed_subscription_buttons = FALSE;

	/**
	 * Use alternative header
	 *
	 * @var bool
	 */
	public $use_alt_header = FALSE;

	/**
	 * P4CT_Site constructor.
	 *
	 * @param array $services The dependencies to inject.
	 */
	public function __construct( $services = [] ) {

		$this->allowed_subscription_buttons = in_array( get_locale(), $this::LOCALES_ENABLE_SUBSCRIPTION_BUTTONS );
		$this->use_alt_header = in_array( get_locale(), $this::LOCALES_ENABLE_ALT_HEADER );

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
		add_action( 'init', [ $this, 'remove_planet4_actions' ] );
		add_action( 'wp_print_styles', [ $this, 'dequeue_parent_assets' ], 100 );
		// add_action( 'pre_get_posts', [ $this, 'add_search_options' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		// add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
		add_action( 'after_setup_theme', [ $this, 'add_oembed_filter' ] );
		// add_action( 'save_post', [ $this, 'p4_auto_generate_excerpt' ], 10, 2 );
		add_action( 'save_post', [ $this, 'gpea_auto_set_tag' ], 10, 2 );
		add_filter( 'query_vars', [ $this, 'add_query_vars_filter' ], 10, 2 );
		// avoid apostrofi
		add_filter( 'run_wptexturize', '__return_false' );

		// get main issues & create header nav
		$main_issues = $this->gpea_get_all_main_issues();
		$header_nav = [];
		foreach( self::NAV_MENUS as $menu_key => $menu_conf ) {
			if( ! $this->use_alt_header && $menu_conf[ 'issues' ] ) {
				foreach( $main_issues as $issue_key => $issue_title ) {
					$header_nav[ 'gpea-header-' . $menu_key . '-menu--' . $issue_key ] = sprintf(__( 'Header: %s: %s', 'gpea_theme_backend' ), __( $menu_conf[ 'msgid' ], 'gpea_theme' ), $issue_title);
				}
				continue;
			}
			$header_nav[ 'gpea-header-' . $menu_key . '-menu' ] = sprintf(__( 'Header: %s', 'gpea_theme_backend' ), __( $menu_conf[ 'msgid' ], 'gpea_theme' ));
		}
		register_nav_menus($header_nav);
		add_filter( 'nav_menu_meta_box_object', [ $this, 'gpea_register_nav_menu_metabox' ] );
		add_action( 'after_setup_theme', [ $this, 'gpea_child_theme_setup' ] );

		// Override parent AJAX search functionality.
		$search = new P4CT_ElasticSearch();
		remove_action( 'wp_ajax_get_paged_posts', [ 'P4MT\P4_ElasticSearch', 'get_paged_posts' ] );
		remove_action( 'wp_ajax_nopriv_get_paged_posts', [ 'P4MT\P4_ElasticSearch', 'get_paged_posts' ] );
		add_action( 'wp_ajax_get_paged_posts', [ &$search, 'get_paged_posts' ] );
		add_action( 'wp_ajax_nopriv_get_paged_posts', [ &$search, 'get_paged_posts' ] );

	}

	/**
	 * Registers taxonomies.
	 */
	public function remove_planet4_actions() {
		// remove the anti-democratic function to create tag pages..

		// P4_Loader::get_instance()->get_services();

		// remove_action( 'create_post_tag', [ 'P4MT\P4_Campaigns', 'save_taxonomy_meta' ] );
		// remove_action( 'edit_post_tag', [ 'P4MT\P4_Campaigns', 'save_taxonomy_meta' ], 10 );
		// remove_action( 'edit_post_tag', 'save_taxonomy_meta' );
		global $pagenow;
		if ( is_admin() && isset( P4_Loader::get_instance()->get_services()['P4_Campaigns'] ) ) {
			remove_action( 'edit_post_tag', [ P4_Loader::get_instance()->get_services()['P4_Campaigns'], 'save_taxonomy_meta' ] );
			add_action( 'edit_post_tag', [ $this, 'save_redirect_page_tag' ] );
			remove_action( 'create_post_tag', [ P4_Loader::get_instance()->get_services()['P4_Campaigns'], 'save_taxonomy_meta' ] );
			add_action( 'create_post_tag', [ $this, 'save_redirect_page_tag' ] );
		}

	}

	/**
	 * save info for redirection
	 */
	public function save_redirect_page_tag( $term_id ) {
		$redirect_page = $_POST['redirect_page'] ?? 0;
		update_term_meta( $term_id, 'redirect_page', intval( $redirect_page ) );
	}


	/**
	 * Filters the oEmbed process to run the responsive_embed() function
	 */
	public function add_oembed_filter() {
		add_filter( 'embed_oembed_html', [ $this, 'responsive_embed' ], 10, 3 );
	}

	/**
	 * Adds a responsive embed wrapper around oEmbed content.
	 *
	 * @param  string $html The oEmbed markup.
	 * @param  string $url  The URL being embedded.
	 * @param  array  $attr An array of attributes.
	 * @return string       Updated embed markup.
	 */
	public function responsive_embed( $html, $url, $attr ) {
		return '' !== $html ? '<div class="embed-container">' . $html . '</div>' : '';
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
		$options = get_option( 'gpea_options' );
		$context['press_media_link'] = isset( $options['gpea_default_press_media'] ) ? get_permalink( $options['gpea_default_press_media'] ) : site_url();
		$context['make_change_link'] = isset( $options['gpea_default_make_change'] ) ? get_permalink( $options['gpea_default_make_change'] ) : site_url();
		$context['preferences_link'] = isset( $options['gpea_default_preferences'] ) ? get_permalink( $options['gpea_default_preferences'] ) : site_url();

		$context['commitment_projects_link'] = isset( $options['gpea_default_commitment_projects'] ) ? get_permalink( $options['gpea_default_commitment_projects'] ) : site_url();
		$context['commitment_issues_link'] = isset( $options['gpea_default_commitment_issues'] ) ? get_permalink( $options['gpea_default_commitment_issues'] ) : site_url();
		$context['commitment_projects_link_label'] = isset( $options['gpea_default_commitment_projects'] ) ? get_the_title( $options['gpea_default_commitment_projects'] ) : '';
		$context['commitment_issues_link_label'] = isset( $options['gpea_default_commitment_issues'] ) ? get_the_title( $options['gpea_default_commitment_issues'] ) : '';

		if ( isset( $options['gpea_default_supportus_link_external'] ) && ( '' !== $options['gpea_default_supportus_link_external'] ) ) {
			$context['support_link'] = $options['gpea_default_supportus_link_external'];
		} else {
			$context['support_link'] = isset( $options['gpea_default_supportus_link'] ) ? get_permalink( $options['gpea_default_supportus_link'] ) : site_url();
		}

		// special third link
		$context['footer_extra_link'] = $options['gpea_footer_extra_link'] ?? '';
		$context['footer_extra_link_label'] = $options['gpea_footer_extra_link_label'] ?? '';

		$context['generic_footer_text'] = isset( $options['gpea_description_generic_footer_text'] ) ? $options['gpea_description_generic_footer_text'] : '';
		$context['current_country'] = isset( $options['gpea_current_country'] ) ? $options['gpea_current_country'] : 'HK';
		$context['home_url'] = site_url();
		$context['kakao_app_id'] = isset( $options['gpea_kakao_app_id'] ) ? $options['gpea_kakao_app_id'] : '';

		$context['gpea_site_base'] = isset( $options['gpea_site_base'] ) ? $options['gpea_site_base'] : 'gpea';

		// facebook app id info
		$context['gpea_facebook_app_id'] = isset( $options['gpea_facebook_app_id'] ) ? $options['gpea_facebook_app_id'] : '';

		// footer extra link

		$context['strings_navbar'] = [
			'sign' => __( 'Sign', 'gpea_theme' ),
			'share' => __( 'Share', 'gpea_theme' ),
			'donate' => __( 'Donate', 'gpea_theme' ),
			'support' => __( 'Support us', 'gpea_theme' ),
		];

		$context['strings_footer'] = [
			'follow' => __( 'Follow us', 'gpea_theme' ),
			'our_commitment' => __( 'Our commitment', 'gpea_theme' ),
			'projects' => __( 'Projects', 'gpea_theme' ),
			'main_issues' => __( 'Main issues', 'gpea_theme' ),
			'about_greenpeace' => __( 'About Greenpeace', 'gpea_theme' ),
			'quick_links' => __( 'Quick links', 'gpea_theme' ),
			'press_media' => __( 'Press release and media', 'gpea_theme' ),
			'my_preferences' => __( 'My Preferences', 'gpea_theme' ),
		];

		$context['search_strings'] = [
			'issue'           => __( 'issue', 'gpea_theme' ),
			'topic'           => __( 'topic', 'gpea_theme' ),
			'posts'           => __( 'posts', 'gpea_theme' ),
			'advanced_search' => __( 'advanced_search', 'gpea_theme' ),
			'filters'         => __( 'filters', 'gpea_theme' ),
			'none'            => __( 'None', 'gpea_theme' ),
			'start_typing'    => __( 'Start typing', 'gpea_theme' ),
			'search_label'  => __( 'Search', 'gpea_theme' ),
			'reset_filters' => __( 'Reset Filters', 'gpea_theme' ),
			'sort_by'       => __( 'Sort by', 'gpea_theme' ),
			'nothing_found' => __( 'Nothing found, sorry.', 'gpea_theme' ),
			'any'           => __( 'Any', 'gpea_theme' ),
			'any_issue'     => __( 'Any issue', 'gpea_theme' ),
			'any_topic'     => __( 'Any topic', 'gpea_theme' ),
		];

		// new menu
		$header_nav_options = get_option( 'gpea_header_nav_options' );
		$main_issues = $this->gpea_get_all_main_issues();
		$header_nav = [];
		foreach( self::NAV_MENUS as $menu_key => $menu_conf ) {

			$classes = [ 'menu__container' ];

			$classes[] = ( ! $this->use_alt_header && $menu_conf[ 'issues' ] ) || $menu_conf[ 'depth' ] > 1 ? 'has-children' : 'no-children';
			$classes[] = ! $this->use_alt_header && $menu_conf[ 'issues' ] ? 'is-issues' : 'not-issues';
			$classes[] = $menu_conf[ 'counter' ] ? 'is-counter' : 'not-counter';

			$menu = [];
			$menu[ 'label' ] = __( $menu_conf[ 'msgid' ], 'gpea_theme' );

			if( $menu_conf[ 'link' ] && isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_link' ] ) ) {
				$menu[ 'link' ] = $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_link' ];
			}
			else {
				$menu[ 'link' ] = '#';
			}

			$cta_content = '';
			if( isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_enabled' ] ) && $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_enabled' ] ) {
				$cta_link = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_link' ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_link' ] : '';
				$cta_label = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_label' ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_label' ] : '';
				$cta_label_mobile = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_label_mobile' ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_label_mobile' ] : '';
				$cta_img = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_img' ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_cta_img' ] : '';
				$cta_content .= '
				<a class="header__cta header__cta--real" href="' . esc_attr( $cta_link ) . '">
					' . ( @strlen( $cta_img ) ? '<span class="icon" style="background-image: url(' . esc_attr( $cta_img ) . ')"></span>' : '' ) . '
					<span class="title hide-lt-sm">' . esc_html( $cta_label ) . '</span>
					<span class="title hide-gt-sm">' . esc_html( $cta_label_mobile ) . '</span>
					<span class="arrow"><span></span></span>
				</a>';
			}

			if( $menu_conf[ 'issues' ] ) {
				$children_data_attr = ! $this->use_alt_header ? ' data-label="' . esc_attr(__( 'Issue we work on', 'gpea_theme' )) . '" data-label-fake="' . esc_attr(__( 'On-Going Projects', 'gpea_theme' )) . '" ' : '';
				$children = '
				<div class="' . implode( ' ', $classes ) . '">
					<div class="menu__inner">
						<div class="menu__inner2">
							<ul class="menu__inner3 menu__inner3--real" ' . $children_data_attr . '>';
				$all_issue_children = [];
				foreach( $main_issues as $issue_key => $issue_title ) {
					if( $this->use_alt_header && $issue_key == 'general' ) {
						continue;
					}
					$setting_title = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_label--' . $issue_key ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_label--' . $issue_key ] : '';
					$setting_link = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_link--' . $issue_key ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_link--' . $issue_key ] : '#';
					$setting_sort = isset( $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_sort--' . $issue_key ] ) ? $header_nav_options[ 'gpea_header_nav_menu_' . $menu_key . '_sort--' . $issue_key ] : '0';
					$issue_children_menu_html = ! $this->use_alt_header && has_nav_menu( 'gpea-header-' . $menu_key . '-menu--' . $issue_key ) ? wp_nav_menu( [
						'container' => NULL,
						'menu_class' => 'sub-menu',
						'theme_location' => 'gpea-header-' . $menu_key . '-menu--' . $issue_key,
						'echo' => FALSE,
						'depth' => $menu_conf[ 'depth' ],
					]) : '';
					$issue_children = '
								<li class="menu-item' . (@strlen($issue_children_menu_html) ? ' menu-item-has-children' : '') . '">
									<a href="' . esc_attr($setting_link) . '"><span class="issue ' . esc_attr($issue_key) . '">' . esc_html($issue_title) . '</span>' . $setting_title . '</a>';
					$issue_children .= $issue_children_menu_html;
					$issue_children .= '
								</li>';
					$all_issue_children[] = [
						'sort' => $setting_sort,
						'html' => $issue_children,
					];
				}
				usort( $all_issue_children, function( $a, $b ) {
					if( $a[ 'sort' ] == $b[ 'sort' ] ) {
						return 0;
					}
					return ( $a[ 'sort' ] < $b[ 'sort' ] ) ? -1 : 1;
				} );
				$children .= implode( '', array_column( $all_issue_children, 'html' ) );
				$children .= '
							</ul>
						</div>' . $cta_content . '
					</div>
				</div>';
				$menu[ 'children' ] = $children;
			}
			else {
				$menu[ 'children' ] = has_nav_menu( 'gpea-header-' . $menu_key . '-menu' ) ? wp_nav_menu( [
					'container_class' => implode( ' ', $classes ),
					'menu_class' => 'menu__inner3 menu__inner3--real',
					'theme_location' => 'gpea-header-' . $menu_key . '-menu',
					'echo' => FALSE,
					'depth' => $menu_conf[ 'depth' ],
					'items_wrap' => '<div class="menu__inner"><div class="menu__inner2"><ul id="%1$s" class="%2$s">%3$s</ul></div>' . $cta_content . '</div>',
				]) : '';
			}

			$header_nav[] = $menu;

		}

		$header_nav_sticky_link = '';
		$header_nav_button_link = '';

		if( isset( $header_nav_options[ 'gpea_header_nav_sticky_link' ] ) ) {
			$header_nav_sticky_link = $header_nav_options[ 'gpea_header_nav_sticky_link' ];
			if( @strlen( $header_nav_sticky_link ) ) {
				$header_nav_sticky_link = add_query_arg( 'ref', 'cta-sticky', $header_nav_sticky_link );
			}
		}
		if( isset( $header_nav_options[ 'gpea_header_nav_button_link' ] ) ) {
			$header_nav_button_link = $header_nav_options[ 'gpea_header_nav_button_link' ];
			if( @strlen( $header_nav_button_link ) ) {
				$header_nav_button_link = add_query_arg( 'ref', 'cta-header', $header_nav_button_link );
			}
		}

		$context[ 'header_nav' ] = [
			'sticky' => [
				'enabled' => isset( $header_nav_options[ 'gpea_header_nav_sticky_enabled' ] ) ? $header_nav_options[ 'gpea_header_nav_sticky_enabled' ] : '0',
				'link' => $header_nav_sticky_link,
				'label' => isset( $header_nav_options[ 'gpea_header_nav_sticky_label' ] ) ? $header_nav_options[ 'gpea_header_nav_sticky_label' ] : '',
				'label_mobile' => isset( $header_nav_options[ 'gpea_header_nav_sticky_label_mobile' ] ) ? $header_nav_options[ 'gpea_header_nav_sticky_label_mobile' ] : '',
			],
			'button' => [
				'link' => $header_nav_button_link,
				'label' => isset( $header_nav_options[ 'gpea_header_nav_button_label' ] ) ? $header_nav_options[ 'gpea_header_nav_button_label' ] : '',
				'label_mobile' => isset( $header_nav_options[ 'gpea_header_nav_button_label_mobile' ] ) ? $header_nav_options[ 'gpea_header_nav_button_label_mobile' ] : '',
			],
			'login' => [
				'enabled' => isset( $header_nav_options[ 'gpea_header_nav_login_enabled' ] ) ? $header_nav_options[ 'gpea_header_nav_login_enabled' ] : '0',
				'link' => isset( $header_nav_options[ 'gpea_header_nav_login_link' ] ) ? $header_nav_options[ 'gpea_header_nav_login_link' ] : '',
				'label' => __( 'login', 'gpea_theme' ),
			],
			'menus' => $header_nav,
		];

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
	 * Add custom meta boxes for nav menu manage
	 */
	public function gpea_register_nav_menu_metabox( $object ) {
		add_meta_box( 'gpea-nav-menu-item-projects', __( 'Projects', 'gpea_theme_backend' ), [ $this, 'gpea_render_nav_menu_projects_metabox' ] , 'nav-menus', 'side', 'default' );
		return $object;
	}
	public function gpea_render_nav_menu_projects_metabox() {

		global $nav_menu_selected_id;
		$walker = new Walker_Nav_Menu_Checklist();

		$options = array(
			'order'       => 'desc',
			'orderby'     => 'date',
			'post_type'   => 'page',
			'posts_per_page' => 20,
			'meta_key'    => '_wp_page_template',
			'meta_value'  => 'page-templates/project.php',
		);
		$query = new \WP_Query( $options );
		$posts = $query->posts;

		?>
		<div id="gpea-projects" class="categorydiv">
			<div class="tabs-panel tabs-panel-active">
				<ul class="categorychecklist form-no-clear">
				<?php
					echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $posts), 0, (object) array( 'walker' => $walker) );
				?>
				</ul>
			</div>
			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-gpea-projects-menu-item" id="submit-gpea-projects" />
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Load translations for master theme
	 */
	public function gpea_child_theme_setup() {

		// unregister master theme's nav menu
		unregister_nav_menu( 'navigation-bar-menu' );

		$domains = [
			'gpea_theme',
			'gpea_theme_backend',
		];
		$locale  = is_admin() ? get_user_locale() : get_locale();

		foreach ( $domains as $domain ) {
			$mofile = get_stylesheet_directory() . '/languages/' . $domain . '-' . $locale . '.mo';
			load_textdomain( $domain, $mofile );
		}

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
		// wp_dequeue_style( 'plugin-en' );
		wp_dequeue_style( 'gravity-forms-style' );
		wp_dequeue_style( 'global-styles' );
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
		$css_creation = filectime( get_stylesheet_directory() . '/admin/css/admin_style.css' );

		wp_enqueue_style( 'admin-child-style', get_stylesheet_directory_uri() . '/admin/css/admin_style.css', [], $css_creation );
		add_editor_style( get_stylesheet_directory_uri() . '/admin/css/admin_editor_style.css' );
	}

	/**
	 * Load styling and behaviour on website pages.
	 *
	 * @param string $hook Hook.
	 */
	public function enqueue_public_assets( $hook ) {

		$css_creation = filectime( get_stylesheet_directory() . '/static/css/style.css' );
		$js_creation = filectime( get_stylesheet_directory() . '/static/js/script.js' );
		$search_js_creation = filectime( get_stylesheet_directory() . '/static/js/search.js' );

		$css_fonts = gpea_get_option( 'gpea_css_fonts' ) ? gpea_get_option( 'gpea_css_fonts' ) : 'hk-fonts.css';

		switch($css_fonts) {
			case 'kr-fonts.css':
				wp_enqueue_style( 'child-style-noto-sans-kr', 'https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500&display=swap' );
				break;
			default:
				wp_enqueue_style( 'child-style-noto-sans-tc', 'https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500&display=swap' );
		}
		
		wp_enqueue_style( 'child-style-montserrat', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500&display=swap' );
		wp_enqueue_style( 'child-style-fonts', get_stylesheet_directory_uri() . '/static/css/' . $css_fonts, [], $css_creation );

		wp_enqueue_style( 'child-style-swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/css/swiper.min.css' );
    	wp_enqueue_style( 'child-style-flexboxgrid', 'https://cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css' );

		wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/static/css/style.css', [], $css_creation );

		wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/static/js/script.js', [ 'jquery' ], $js_creation, true );
		wp_localize_script( 'child-script', 'localizations', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		]);

		wp_register_script( 'search-script', get_stylesheet_directory_uri() . '/static/js/search.js', [ 'jquery' ], $search_js_creation, true );
		wp_localize_script( 'search-script', 'localizations', [
			'ajaxurl'           => admin_url( 'admin-ajax.php' ),
		] );
		wp_enqueue_script( 'search-script' );
	}

	/**
	 * Registers taxonomies.
	 */
	public function register_taxonomies() {

		$labels = array(
			'name'              => _x( 'Special attributes', 'taxonomy general name', 'gpea_theme_backend' ),
			'singular_name'     => _x( 'Special attribute', 'taxonomy singular name', 'gpea_theme_backend' ),
			'search_items'      => __( 'Search attributes', 'gpea_theme_backend' ),
			'all_items'         => __( 'All attributes', 'gpea_theme_backend' ),
			'edit_item'         => __( 'Edit attribute', 'gpea_theme_backend' ),
			'update_item'       => __( 'Update attribute', 'gpea_theme_backend' ),
			'add_new_item'      => __( 'Add New attribute', 'gpea_theme_backend' ),
			'new_item_name'     => __( 'New attribute Name', 'gpea_theme_backend' ),
			'menu_name'         => __( 'Attribute', 'gpea_theme_backend' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'attribute',
			),
		);
		register_taxonomy( 'p4_post_attribute', array( 'post', 'page' ), $args );

		$labels_members = array(
			'name'              => _x( 'Team category', 'taxonomy general name', 'gpea_theme_backend' ),
			'singular_name'     => _x( 'Team category', 'taxonomy singular name', 'gpea_theme_backend' ),
			'search_items'      => __( 'Search team category', 'gpea_theme_backend' ),
			'all_items'         => __( 'All Team categories', 'gpea_theme_backend' ),
			'edit_item'         => __( 'Edit team category', 'gpea_theme_backend' ),
			'update_item'       => __( 'Update team category', 'gpea_theme_backend' ),
			'add_new_item'      => __( 'Add New team category', 'gpea_theme_backend' ),
			'new_item_name'     => __( 'New Team category Name', 'gpea_theme_backend' ),
			'menu_name'         => __( 'Team category', 'gpea_theme_backend' ),
		);
		$args_members = array(
			'hierarchical'      => true,
			'labels'            => $labels_members,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'team_category',
			),
		);
		register_taxonomy( 'p4_gpea_team_category', array( 'team' ), $args_members );

	}

	/**
	 * Gpea_get_realated gets related posts.
	 *
	 * @param int  $exclude_post_id Id to be excluded, usually the current one.
	 * @param text $limit limit of related posts to be retrieved, default 3.
	 * @param int $avoid_post_search if
	 * @param int $cat_id cat id to use as source.
	 * @param int $tag_id tag id to use as source
	 * @param text layout to differentiate
	 */
	public function gpea_get_related( $exclude_post_id, $limit, $avoid_post_search = 0, $cat_id = 0, $tag_id = 0, $layout = false ) {

		$exclude_post_id = (int) ( $exclude_post_id ?? '' );
		$limit           = (int) ( $limit ?? '3' );

		$post_args = [
			'orderby'          => 'date',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'posts_per_page'   => $limit,
		];

		/* build other options, category and tags info + exclude post */

		if ( $exclude_post_id ) {
			$post_args['post__not_in'] = [ $exclude_post_id ];
		}

		// Get page categories.
		if ( ! $avoid_post_search ) {
			$post_categories   = get_the_category();
			if ( $post_categories ) {
				$category_id_array = [];
				foreach ( $post_categories as $category ) {
					$category_id_array[] = $category->term_id;
				}
				$post_args['category__in'] = $category_id_array;
			}
		} else {
			$post_args['cat'] = $cat_id ?? false;
		}

		if ( ! $avoid_post_search ) {
			// Get page/post tags.
			$post_tags = get_the_tags();
			if ( $post_tags ) {
				$tag_id_array = [];
				foreach ( $post_tags as $tag ) {
					$tag_id_array[] = $tag->term_id;
				}
				$post_args['tag__in'] = $tag_id_array;
			}
		} else {
			$post_args['tag_id'] = $tag_id ?? false;
		}

		if ( 'big' === $layout ) {
			$templates          = [ 'tease-related-post-big.twig' ];
		} else {
			$templates          = [ 'tease-related-post.twig' ];
		}

		$pagetype_posts     = new \Timber\PostQuery( $post_args, 'P4_Post' );

		return $pagetype_posts;

	}


	/**
	 * Gpea_get_main_issue
	 *
	 * @param int $post_id current post Id.
	 *
	 * @return bool|WP_Term
	 */
	public function gpea_get_main_issue( $post_id ) {

		$post_id = (int) ( $post_id ?? '' );

		// get related main issues!
		$main_issues_category_id = $this->gpea_get_main_issue_parent_id();

		if ( $main_issues_category_id ) {
			$categories = get_the_category( $post_id );
			if ( ! empty( $categories ) ) {
				$categories = array_filter(
					$categories, function( $cat ) use ( $main_issues_category_id ) {
						return $cat->category_parent === intval( $main_issues_category_id );
					}
				);
				if ( ! empty( $categories ) ) {
					$first_category = array_values( $categories )[0];
					return $first_category;
				}
			}
		}

		return false;

	}

	/**
	 * Gpea_get_all_main_issues
	 *
	 * @param bool $all_field Return an array with WP_Term objects. Only slug (as key) and name (as value) will be returned if set to FALSE.
	 *
	 * @return array
	 */
	function gpea_get_all_main_issues( $all_field = FALSE ) {

		$main_issues_category_id = $this->gpea_get_main_issue_parent_id();

		$main_issues = [];
		if( $main_issues_category_id ) {
			$main_issues = get_terms([
				'taxonomy' => 'category',
				'parent' => $main_issues_category_id,
			]);
		}

		if( !$all_field ) {
			$main_issues = array_column( $main_issues, 'name', 'slug' );
		}

		return $main_issues;

	}

	/**
	 * Gpea_get_main_issue_parent_id
	 *
	 * @return int
	 */
	function gpea_get_main_issue_parent_id() {

		$planet4_options = get_option( 'planet4_options' );
		$main_issues_category_id = isset( $planet4_options['issues_parent_category'] ) ? $planet4_options['issues_parent_category'] : false;
		if ( ! $main_issues_category_id ) {
			$main_issues_category = get_term_by( 'slug', 'issues', 'category' );
			if ( $main_issues_category ) {
				$main_issues_category_id = $main_issues_category->term_id;
			}
		}

		return $main_issues_category_id;

	}

	/**
	 * Gpea_set_more_data_by_post
	 */
	function gpea_set_more_data_by_post(&$post, &$context = []) {

		$context['post_categories'] = NULL;
		$context['main_issue'] = NULL;
		$context['main_issue_id'] = NULL;
		$context['main_issue_slug'] = NULL;
		$context['main_issue_url'] = NULL;

		$post_categories = get_the_terms( $post, 'category' );
		$main_issues_category_id = $this->gpea_get_main_issue_parent_id();
		$context_post_categories = [];

		if ( $post_categories ) {
			foreach ( $post_categories as $post_category ) {
				$context_post_categories[] = $post_category->slug;
				if ( ( $main_issues_category_id ) && ( intval( $post_category->parent ) === intval( $main_issues_category_id ) ) ) {
					$context['main_issue'] = $post_category->name;
					$context['main_issue_id'] = $post_category->term_id;
					$context['main_issue_slug'] = $post_category->slug;
					$context['main_issue_url'] = get_category_link( $post_category->term_id );
				}
			}
			$context['post_categories'] = implode(' ', $context_post_categories);
		}

		$page_meta_data = get_post_meta( $post->ID );

		$reading_time_for_display = $page_meta_data['p4-gpea_post_reading_time'][0] ?? '';
		preg_match('/^\s*(\d+)(\s*mins?|\s*분)?\s*$/i', $reading_time_for_display, $matches);

		$context['reading_time_for_display'] = $reading_time_for_display;
		$context['reading_time']             = isset($matches[1]) ? $matches[1] * 60 * 1000 : '';
		$post->subtitle                      = $page_meta_data['p4-gpea_post_subtitle'][0] ?? '';

	}

	/**
	 * Auto generate excerpt for post.
	 *
	 * @param int     $post_id Id of the saved post.
	 * @param WP_Post $post Post object.
	 */
	public function gpea_auto_set_tag( $post_id, $post ) {
		if ( 'user_story' === $post->post_type ) {

			// Unhook save_post function so it doesn't loop infinitely.
			remove_action( 'save_post', [ $this, 'gpea_auto_set_tag' ], 10 );

			wp_set_post_tags( $post_id, 'stories', true );

			// re-hook save_post function.
			add_action( 'save_post', [ $this, 'gpea_auto_set_tag' ], 10, 2 );
		}
	}


	/**
	 * Add "ref" param into query string for links in post.
	 *
	 * @param string  $link Link URL.
	 * @param WP_Post $post Post object.
	 */
	function add_post_ref_query_to_link_url( $link_url = '', $post ) {

		$home_url_info = parse_url( home_url() );
		$domain = $home_url_info['host'];
		$domain = implode( '.', array_slice( explode( '.', $domain ), -2, 2 ) );

		$link_info = parse_url( $link_url );
		if( isset( $link_info['query'] ) ) {
			parse_str( $link_info['query'], $link_query_array );
		}
		else {
			$link_query_array = [];
		}

		if( !is_single() || @strlen( $link_url ) == 0 || strpos( $link_url, $domain ) === FALSE || isset( $link_query_array['ref'] ) ) {
			return $link_url;
		}
		return add_query_arg( 'ref', 'update-' . $post->ID . '-' . urldecode( $post->slug ), $link_url );

	}

	/**
	 * Allow parameter to be passed to url.
	 *
	 * @param text $vars text variable allowed.
	 * @return array|text
	 */
	public function add_query_vars_filter( $vars ) {
		$vars[] = 'fn';
		$vars[] = 'pet';
		return $vars;
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
	 * @param int     $post_id Id of the saved post.
	 * @param WP_Post $post Post object.
	 */
	public function p4_auto_generate_excerpt( $post_id, $post ) {
	}

}
/**
 * Wrapper function around cmb2_get_option.
 *
 * @param  string $key Options array key.
 * @return mixed Option value.
 */
function gpea_get_option( $key = '' ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		return cmb2_get_option( 'gpea_options', $key );
	} else {
		$options = get_option( 'gpea_options' );
		return isset( $options[ $key ] ) ? $options[ $key ] : false;
	}
}
