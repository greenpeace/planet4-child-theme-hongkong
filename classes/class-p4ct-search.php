<?php
/**
 * P4CT Search Class
 *
 * @package P4CT
 */

use Timber\Timber;
use Timber\Post as TimberPost;

if ( ! class_exists( 'P4CT_Search' ) ) {

	/**
	 * Abstract Class P4CT_Search
	 */
	abstract class P4CT_Search {

		const POSTS_PER_PAGE             = 12;
		const POSTS_LIVE_SEARCH_PER_LOAD = 6;
		const DEFAULT_SORT               = '_score';
		const DEFAULT_ALL_POST_SORT      = 'post_date';
		const DEFAULT_PAGE_WEIGHT        = 100;
		const DEFAULT_ACTION_WEIGHT      = 2000;
		const DEFAULT_CACHE_TTL          = 600;
		const DUMMY_THUMBNAIL            = '/images/dummy-thumbnail.png';
		const POST_TYPES                 = [
			'post',
		];
		const DOCUMENT_TYPES             = [
			'application/pdf',
		];

		/**
		 * Search Query
		 *
		 * @var string $search_query
		 */
		protected $search_query;

		/**
		 * Total Posts
		 *
		 * @var int $total_posts
		 */
		protected $total_posts;

		/**
		 * Paged Posts
		 *
		 * @var array|bool|null $paged_posts
		 */
		protected $paged_posts;

		/**
		 * Selected sort criteria
		 *
		 * @var array $selected_sort
		 */
		protected $selected_sort;

		/**
		 * FIlters
		 *
		 * @var array $filters
		 */
		protected $filters;

		/**
		 * Localizations
		 *
		 * @var array $localizations
		 */
		protected $localizations;

		/**
		 * Templates
		 *
		 * @var array $templates
		 */
		public $templates;

		/**
		 * Context
		 *
		 * @var array $context
		 */
		public $context;

		/**
		 * Current Page
		 *
		 * @var int $current_page
		 */
		public $current_page;

		/**
		 * Main issues category id
		 *
		 * @var int $main_issues_category_id;
		 */
		public $main_issues_category_id;

		/**
		 * Main issues
		 *
		 * @var array $main_issues;
		 */
		public $main_issues = [];

		/**
		 * P4CT_Search constructor.
		 */
		public function __construct() {}

		/**
		 * Initialize the class. Hook necessary actions and filters.
		 */
		protected function initialize() {
			$this->localizations = [
				// The ajaxurl variable is a global js variable defined by WP itself but only for the WP admin
				// For the frontend we need to define it ourselves and pass it to js.
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
			];
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
			add_filter( 'posts_where', [ $this, 'edit_search_mime_types' ] );
			$this->set_main_issues();
		}

		/**
		 * Conducts the actual search.
		 *
		 * @param string     $search_query The searched term.
		 * @param string     $selected_sort The selected order_by.
		 * @param array      $filters The selected filters.
		 * @param array      $templates An indexed array with template file names. The first to be found will be used.
		 * @param array|null $context An associative array with all the context needed to render the template found first.
		 */
		public function load( $search_query, $selected_sort = NULL, $filters = [], $templates = [ 'search.twig', 'archive.twig', 'index.twig' ], $context = null ) {

			// TODO Shouldn't we check for a nonce, both here and on AJAX?
			$this->initialize();
			$this->search_query = $search_query;
			$this->templates    = $templates;

			if ( $context ) {
				$this->context = $context;
			} else {
				$this->context = Timber::get_context();

				$paged = ( 0 === get_query_var( 'paged' ) ) ? 1 : get_query_var( 'paged' );

				$has_search_query = @strlen($this->search_query) > 0;
				if (!$has_search_query && is_null($selected_sort)) {
					$selected_sort = $selected_sort ? self::DEFAULT_SORT : self::DEFAULT_ALL_POST_SORT;
				}

				// Validate user input (sort, filters, etc).
				if ( $this->validate( $selected_sort, $filters, $this->context ) ) {
					$this->selected_sort = $selected_sort;
					$this->filters       = $filters;
				}

				// Set the decoded url query string as key.
				$query_string = urldecode( filter_input( INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING ) );
				$group        = 'search';
				$subgroup     = $has_search_query ? $this->search_query : ':all';
				// Check Object cache for stored key.
				$this->check_cache( $query_string, "$group:$subgroup", $paged );

				$this->current_page = $paged;
				$this->set_context( $this->context );
			}
		}

		/**
		 * Conducts the actual AJAX search.
		 *
		 * @param string     $search_query The searched term.
		 * @param string     $selected_sort The selected order_by.
		 * @param array      $filters The selected filters.
		 * @param array|null $context An associative array with all the context needed to render the template found first.
		 */
		public function gpea_load_ajax( $search_query, $selected_sort = NULL, $filters = [], $context = null ) {

			$this->search_query = $search_query;
			$this->context = Timber::get_context();
			$this->set_main_issues();

			$paged = 1;

			$has_search_query = @strlen($this->search_query) > 0;
			if (!$has_search_query && is_null($selected_sort)) {
				$selected_sort = $selected_sort ? self::DEFAULT_SORT : self::DEFAULT_ALL_POST_SORT;
			}

			// Validate user input (sort, filters, etc).
			if ( $this->validate( $selected_sort, $filters, $this->context ) ) {
				$this->selected_sort = $selected_sort;
				$this->filters       = $filters;
			}

			// Set the decoded url query string as key.
			$query_string = urldecode( filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING ) );
			$group        = 'search';
			$subgroup     = $has_search_query ? $this->search_query : ':all';

			// Check Object cache for stored key.
			$this->check_cache( $query_string, "$group:$subgroup", $paged );

			$this->current_page = $paged;
			$this->set_context( $this->context );

		}

		/**
		 * Callback for Lazy-loading the next results.
		 * Gets the paged posts that belong to the next page/load and are to be used with the twig template.
		 */
		public function get_paged_posts() {
			// If this is an ajax call.
			if ( wp_doing_ajax() ) {
				$search_action = filter_input( INPUT_GET, 'search-action', FILTER_SANITIZE_STRING );
				$paged         = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_STRING );

				// Check if call action is correct.
				if ( 'get_paged_posts' === $search_action ) {
					$search_async = new static();
					$search_async->set_context( $search_async->context );
					$search_async->search_query = urldecode( filter_input( INPUT_GET, 'search_query', FILTER_SANITIZE_STRING ) );
					
					$has_search_query = @strlen($search_async->search_query) > 0;

					// Get the decoded url query string and then use it as key for redis.
					$query_string_full = urldecode( filter_input( INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING ) );
					$query_string      = str_replace( '&query-string=', '', strstr( $query_string_full, '&query-string=' ) );

					$group                      = 'search';
					$subgroup                   = $has_search_query ? $search_async->search_query : ':all';
					$search_async->current_page = $paged;

					parse_str( $query_string, $filters_array );
					$selected_sort    = $filters_array['orderby'] ?? ($has_search_query ? self::DEFAULT_SORT : self::DEFAULT_ALL_POST_SORT);
					$selected_filters = $filters_array['f'] ?? [];
					$filters          = [];

					// Handle submitted filter options.
					if ( $selected_filters && is_array( $selected_filters ) ) {
						foreach ( $selected_filters as $type => $id ) {
							$filters[ $type ] = $id;
						}
					}

					// Validate user input (sort, filters, etc).
					if ( $search_async->validate( $selected_sort, $filters, $search_async->context ) ) {
						$search_async->selected_sort = $selected_sort;
						$search_async->filters       = $filters;
					}

					// Check Object cache for stored key.
					$search_async->check_cache( $query_string, "$group:$subgroup", $paged );

					// Check if there are results already in the cache else fallback to the primary database.
					if ( !$search_async->paged_posts ) {
						$search_async->paged_posts = $search_async->get_timber_posts( $search_async->current_page );
					}

					// If there are paged results then set their context and send them back to client.
					$search_async->set_results_context( $search_async->context );
					$search_async->view_paged_posts();
				}
				wp_die();
			}
		}

		/**
		 * Check if search is cached. If it is not then get posts from primary database and cache it.
		 *
		 * @param string $cache_key The key that will be used for storing the results in the object cache.
		 * @param string $cache_group The group that will be used for storing the results in the object cache.
		 */
		protected function check_cache( $cache_key, $cache_group, $paged = 1 ) {
			// Get search results from cache and then set the context for those results.
			$this->paged_posts = wp_cache_get( $cache_key . ':' . $paged, $cache_group );
			if( !$this->total_posts ) {
				$this->total_posts = wp_cache_get( $cache_key . ':total_posts', $cache_group );
			}
			// If cache key expired then retrieve results once again and re-cache them.
			if ( !$this->paged_posts ) {
				$this->paged_posts = $this->get_timber_posts($paged);
				if ( $this->paged_posts ) {
					wp_cache_add( $cache_key . ':' . $paged, $this->paged_posts, $cache_group, self::DEFAULT_CACHE_TTL );
				}
				if ( $this->total_posts ) {
					wp_cache_add( $cache_key . ':total_posts', $this->total_posts, $cache_group, self::DEFAULT_CACHE_TTL );
				}
			}
		}

		/**
		 * Gets the respective Timber Posts, to be used with the twig template.
		 * If there are not then uses Timber's get_posts to retrieve all of them (up to the limit set).
		 *
		 * @param int $paged The number of the page of the results to be shown when using pagination/load_more.
		 *
		 * @return array The respective Timber Posts.
		 */
		protected function get_timber_posts( $paged = 1 ) : array {
			$timber_posts = [];

			$posts = $this->get_posts( $paged );
			// Use Timber's Post instead of WP_Post so that we can make use of Timber within the template.
			if ( $posts ) {
				foreach ( $posts as $post ) {
					// TODO we don't really need the TimberPost overhead here. Could just use $post.
					$timber_post = new TimberPost( $post->ID );

					$timber_post->link = get_permalink( $post->ID );

					$timber_post->post_date = date( 'Y-m-d', strtotime( $timber_post->post_date ) );

					$timber_post->reading_time = get_post_meta( $post->ID, 'p4-gpea_post_reading_time', true );

					$img_url = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
					if ( $img_url ) {
						$timber_post->img_url = $img_url;
					}

					if ( 'post' === $post->post_type ) {
						$news_type = wp_get_post_terms( $post->ID, 'p4-page-type' );
						if ( $news_type ) {
							$timber_post->news_type = $news_type[0]->name;
						}
					} else if ( 'page' === $post->post_type ) {
						$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
						if ( 'page-templates/petition.php' === $page_template ) {
							$timber_post->news_type = __( 'Petition', 'gpea_theme' );
						} else if ( 'page-templates/project.php' === $page_template ) {
							$timber_post->news_type = __( 'Project', 'gpea_theme' );
						} else if ( 'page-templates/main-issue.php' === $page_template ) {
							$timber_post->news_type = __( 'Main Issue', 'gpea_theme' );
						}
					}

					$post_categories = wp_get_post_categories( $post->ID );
					$main_issue = is_array( $this->main_issues ) ? array_filter(
						$post_categories, function( $cat ) {
							return array_key_exists( $cat, $this->main_issues );
						}
					) : NULL;

					if ( $main_issue && isset( $main_issue[0] ) ) {
						$main_issue = $main_issue[0];
						$timber_post->main_issue = $main_issue ? $this->main_issues[ $main_issue ]->name : 'none';
						$timber_post->main_issue_slug = $main_issue ? $this->main_issues[ $main_issue ]->slug : 'none';
					}

					$timber_posts[] = $timber_post;
				}
			}
			return (array) $timber_posts;
		}

		/**
		 * Applies user selected filters to the search if there are any and gets the filtered posts.
		 *
		 * @param int $paged The number of the page of the results to be shown when using pagination/load_more.
		 *
		 * @return array The posts of the search.
		 */
		public function get_posts( $paged = NULL ) : array {
			$args = [];

			$this->set_general_args( $args, $paged );
			try {
				$this->set_filters_args( $args );
			} catch ( UnexpectedValueException $e ) {
				$this->context['exception'] = $e->getMessage();
				return [];
			}
			$this->set_engines_args( $args );

			$the_query = new WP_Query( $args );
			$posts = $the_query->posts;
			$this->total_posts = $the_query->found_posts;

			return (array) $posts;
		}

		/**
		 * Gets the query terms.
		 *
		 * @return array The terms of the search.
		 */
		public function get_terms() : array {
			$args = [];

			$this->set_general_terms_args( $args );
			$terms = ( new WP_Term_Query( $args ) )->terms;
			return (array) $terms;
		}

		/**
		 * Sets arguments for the WP_Query that are related to the application.
		 *
		 * @param array $args The search query args.
		 * @param int   $paged The number of the page of the results to be shown when using pagination/load_more.
		 */
		protected function set_general_args( &$args, $paged ) {
			$args = [
				'posts_per_page' => self::POSTS_PER_PAGE,
				'no_found_rows'  => false,
				'post_type'      => self::POST_TYPES,
				'post_status'    => [ 'publish', 'inherit' ],
				'paged'          => $paged,
			];

			if ( $this->search_query ) {
				$args['s'] = $this->search_query;
			} else {
				// If we search for everything then order by 'post_date'.
				$args2 = [
					'orderby'    => 'date',
					'order'      => 'DESC',
				];
				$args  = array_merge( $args, $args2 );
			}

			$args['s'] = $this->search_query;
			// Add sort by date.
			$selected_sort = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING );
			$selected_sort = sanitize_sql_orderby( $selected_sort );

			if ( $selected_sort && self::DEFAULT_SORT !== $selected_sort ) {
				$args['orderby'] = 'date';
				$args['order']   = 'desc';
			}
		}

		/**
		 * Sets arguments for the WP_Query that are related to the application.
		 *
		 * @param array $args The search query args.
		 */
		protected function set_general_terms_args( &$args ) {

			$args = [
				'taxonomy' => array( 'category', 'post_tag' ),
			];

			$args['name__like'] = $this->search_query;

		}

		/**
		 * Adds arguments for the WP_Query that are related only to the search engine.
		 *
		 * @param array $args The search query args.
		 */
		abstract public function set_engines_args( &$args );

		/**
		 * Applies user selected filters to the search if there are any and gets the filtered posts.
		 *
		 * @param array $args The search query args.
		 *
		 * @throws UnexpectedValueException When filter type is not recognized.
		 */
		public function set_filters_args( &$args ) {
			if ( $this->filters ) {
				foreach ( $this->filters as $type => $id ) {
					if ( $id ) {
						switch ( $type ) {
							case 'cat':
								$args['tax_query'][] = [
									'taxonomy' => 'category',
									'field'    => 'term_id',
									'terms'    => $id,
								];
								break;
							case 'tag':
								$args['tax_query'][] = [
									'taxonomy' => 'post_tag',
									'field'    => 'term_id',
									'terms'    => $id,
								];
								break;
							default:
								throw new UnexpectedValueException( 'Unexpected filter!' );
						}
					}
				}
			}
		}

		/**
		 * Sets the P4CT Search page context.
		 *
		 * @param array $context Associative array with the data to be passed to the view.
		 */
		protected function set_context( &$context ) {
			$this->set_general_context( $context );
			try {
				$this->set_filters_context( $context );
			} catch ( UnexpectedValueException $e ) {
				$this->context['exception'] = $e->getMessage();
			}
			$this->set_results_context( $context );
		}

		/**
		 * Sets the general context for the Search page.
		 *
		 * @param array $context Associative array with the data to be passed to the view.
		 */
		protected function set_general_context( &$context ) {

			// Search context.
			$context['paged_posts']      = $this->paged_posts;
			$context['current_page']     = $this->current_page;
			$context['search_query']     = $this->search_query;
			$context['selected_sort']    = $this->selected_sort;
			$context['default_sort']     = self::DEFAULT_SORT;
			$context['filters']          = $this->filters;
			$context['found_posts']      = (int) $this->total_posts;
			$context['page_category']    = __( 'Search Page', 'gpea_theme' );
			$context['sort_options']     = [
				'_score'    => [
					'name'  => __( 'Most relevant', 'gpea_theme' ),
					'order' => 'DESC',
				],
				'post_date' => [
					'name'  => __( 'Most recent', 'gpea_theme' ),
					'order' => 'DESC',
				],
			];
			$context['is_search_page'] = '1';

			if ( $this->search_query ) {
				$context['page_title'] = sprintf(
					// translators: %1$d = Number of results.
					_n( '%1$d result for \'%2$s\'', '%1$d results for \'%2$s\'', $context['found_posts'], 'gpea_theme' ),
					$context['found_posts'],
					$this->search_query
				);
			} else {
				// translators: %d = Number of results.
				$context['page_title'] = sprintf( _n( '%d result', '%d results', $context['found_posts'], 'gpea_theme' ), $context['found_posts'] );
			}
		}

		/**
		 * Set filters context
		 *
		 * @param mixed $context Context.
		 *
		 * @throws UnexpectedValueException When filter type is not recognized.
		 */
		protected function set_filters_context( &$context ) {

			// Category <-> Issue.
			// Consider Issues that have multiple Categories.
			$categories = get_categories();
			if ( $categories ) {
				foreach ( $categories as $category ) {
					if ( $category->parent === $this->main_issues_category_id ) {
						$context['categories'][ $category->term_id ] = [
							'term_id' => $category->term_id,
							'name'    => $category->name,
							'results' => 0,
						];
					}
				}
			}

			// Tag <-> Campaign.
			$tags = get_terms(
				[
					'taxonomy'   => 'post_tag',
					'hide_empty' => false,
				]
			);
			if ( $tags ) {
				foreach ( (array) $tags as $tag ) {
					// Tag filters.
					$context['tags'][ $tag->term_id ] = [
						'term_id' => $tag->term_id,
						'name'    => $tag->name,
						'results' => 0,
					];
				}
			}

			$context['content_types']['0'] = [
				'name'    => __( 'Page', 'gpea_theme' ),
				'results' => 0,
			];
			$context['content_types']['1'] = [
				'name'    => __( 'Post', 'gpea_theme' ),
				'results' => 0,
			];

			// Keep track of which filters are already checked.
			if ( $this->filters ) {
				foreach ( $this->filters as $type => $id ) {
					if ( $id ) {
						switch ( $type ) {
							case 'cat':
								$context['categories'][ $id ]['selected'] = true;
								break;
							case 'tag':
								$context['tags'][ $id ]['selected'] = true;
								break;
							default:
								throw new UnexpectedValueException( 'Unexpected filter!' );
						}
					}
				}
			}

			// Sort associative array with filters alphabetically .
			if ( isset( $context['categories'] ) && $context['categories'] ) {
				uasort(
					$context['categories'],
					function ( $a, $b ) {
						return strcmp( $a['name'], $b['name'] );
					}
				);
			}
			if ( isset( $context['tags'] ) && $context['tags'] ) {
				uasort(
					$context['tags'],
					function ( $a, $b ) {
						return strcmp( $a['name'], $b['name'] );
					}
				);
			}
		}

		/**
		 * Sets the context for the results of the Search.
		 *
		 * Categories are Issues.
		 * Tags       are Campaigns.
		 * Page types are Categories.
		 * Post_types are Content Types.
		 *
		 * @param array $context Associative array with the data to be passed to the view.
		 */
		protected function set_results_context( &$context ) {

			$posts = $this->paged_posts;

			// Retrieve P4CT settings in order to check that we add only categories that are children of the Issues category.
			$options = get_option( 'planet4_options' );

			// Pass planet4 settings.
			$context['settings'] = $options;

			// Set default thumbnail.
			$context['posts_data']['dummy_thumbnail'] = get_template_directory_uri() . self::DUMMY_THUMBNAIL;

			foreach ( (array) $posts as $post ) {
				// Post Type (+Action) <-> Content Type.
				switch ( $post->post_type ) {
					case 'page':
						$content_type_text = __( 'PAGE', 'gpea_theme' );
						$content_type      = 'page';
						$context['content_types']['0']['results']++;
						break;
					case 'post':
						$content_type_text = __( 'POST', 'gpea_theme' );
						$content_type      = 'post';
						$context['content_types']['1']['results']++;
						break;
					default:
						continue 2;     // Ignore other post_types and continue with next $post.
				}
				$context['posts_data'][ $post->ID ]['content_type_text'] = $content_type_text;
				$context['posts_data'][ $post->ID ]['content_type']      = $content_type;

				// Tag <-> Campaign.
				if ( 'attachment' !== $post->post_type ) {
					// Category <-> Issue.
					// Consider Issues that have multiple Categories.
					$categories = get_the_category( $post->ID );
					if ( $categories ) {
						foreach ( $categories as $category ) {
							if ( $category->parent === $this->main_issues_category_id ) {
								$context['categories'][ $category->term_id ]['term_id'] = $category->term_id;
								$context['categories'][ $category->term_id ]['name']    = $category->name;
								$context['categories'][ $category->term_id ]['results']++;
							}
						}
					}

					$tags = get_the_terms( $post->ID, 'post_tag' );
					if ( $tags ) {
						foreach ( (array) $tags as $tag ) {
							// Set tags info for each result item.
							$context['posts_data'][ $post->ID ]['tags'][] = [
								'name' => $tag->name,
								'link' => get_tag_link( $tag ),
							];

							// Tag filters.
							$context['tags'][ $tag->term_id ]['term_id'] = $tag->term_id;
							$context['tags'][ $tag->term_id ]['name']    = $tag->name;
							$context['tags'][ $tag->term_id ]['results'] ++;
						}
					}
				}
			}
		}

		/**
		 * Customize which mime types we want to search for regarding attachments.
		 *
		 * @param string $where The WHERE clause of the query.
		 *
		 * @return string The edited WHERE clause.
		 */
		public function edit_search_mime_types( $where ) : string {
			global $wpdb;

			$search_action = filter_input( INPUT_GET, 'search-action', FILTER_SANITIZE_STRING );

			if ( ( ! is_admin() && is_search() ) || ( wp_doing_ajax() && ( 'get_paged_posts' === $search_action ) ) ) {
				$mime_types = implode( ',', self::DOCUMENT_TYPES );
				$where     .= ' AND ' . $wpdb->posts . '.post_mime_type IN("' . $mime_types . '","") ';
			}
			return $where;
		}

		/**
		 * Validates the input.
		 *
		 * @param string $selected_sort The selected orderby to be validated.
		 * @param array  $filters The selected filters to be validated.
		 * @param array  $context Associative array with the data to be passed to the view.
		 *
		 * @return bool True if validation is ok, false if validation fails.
		 */
		public function validate( &$selected_sort, &$filters, $context ) : bool {
			$selected_sort = filter_var( $selected_sort, FILTER_SANITIZE_STRING );
			if ( ! isset( $context['sort_options'] ) || ! in_array( $selected_sort, array_keys( $context['sort_options'] ), true ) ) {
				$selected_sort = self::DEFAULT_SORT;
			}

			if ( $filters ) {
				foreach ( $filters as &$filter ) {
					$filter = filter_var( $filter, FILTER_VALIDATE_INT );
					if ( false === $filter || null === $filter || $filter < 0 ) {
						return false;
					}
				}
			}
			return true;
		}

		/**
		 * Adds a section with a Load more button.
		 *
		 * @param array|null $args The array with the data for the pagination.
		 */
		public function add_load_more( $args = null ) {
			$this->context['load_more'] = $args ?? [
				'posts_per_load' => self::POSTS_PER_PAGE,
				'button_text'    => __( 'SHOW MORE RESULTS', 'gpea_theme' ),
			];
		}

		/**
		 * View the Search page template.
		 */
		public function view() {
			Timber::render(
				$this->templates,
				$this->context,
				self::DEFAULT_CACHE_TTL,
				\Timber\Loader::CACHE_OBJECT
			);
		}

		/**
		 * Return search results as JSON.
		 */
		public function gpea_view_json() {
			return wp_json_encode(
				array(
					'posts' => array_slice( $this->context['paged_posts'], 0, self::POSTS_LIVE_SEARCH_PER_LOAD ),
				)
			);
		}

		/**
		 * View the paged posts of the next page/load.
		 */
		public function view_paged_posts() {
			// TODO - The $paged_context related code should be transferred to set_results_context method for better separation of concerns.
			if ( $this->paged_posts ) {
				$paged_context             = [
					'posts_data' => $this->context['posts_data'],
				];
				$paged_context['settings'] = get_option( 'planet4_options' );

				foreach ( $this->paged_posts as $index => $post ) {
					$paged_context['post'] = $post;
					Timber::render( [ 'tease-search.twig' ], $paged_context, self::DEFAULT_CACHE_TTL, \Timber\Loader::CACHE_OBJECT );
				}
			}
		}

		/**
		 * Set main issues ID.
		 */
		public function set_main_issues() {
			$planet4_options = get_option( 'planet4_options' );
			$main_issues_category_id = isset( $planet4_options['issues_parent_category'] ) ? (int) $planet4_options['issues_parent_category'] : false;
			if ( $main_issues_category_id ) {
				$this->main_issues_category_id = $main_issues_category_id;
				$main_issues = get_categories(
					[
						'parent' => $main_issues_category_id,
					]
				);
				$main_issues_ids = array_map(
					function( $issue ) {
						return $issue->term_id;
					}, $main_issues
				);
				$this->main_issues = array_combine( $main_issues_ids, $main_issues );
			}
		}

		/**
		 * Load assets only on the search page.
		 * Update 8/9/2019: on ALL pages -> moved to class-p4ct-site.php
		 */
		public function enqueue_public_assets() {
			// if ( is_search() ) {
				// $js_creation = filectime( get_stylesheet_directory() . '/static/js/search.js' );

				// wp_register_script( 'search-script', get_stylesheet_directory_uri() . '/static/js/search.js', [], $js_creation, true );
				// wp_localize_script( 'search-script', 'localizations', $this->localizations );
				// wp_enqueue_script( 'search-script' );
			// }
		}
	}
}
