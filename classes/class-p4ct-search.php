<?php
/**
 * P4CT Search Class
 *
 * @package P4CT
 */

use Timber\Timber;
use Timber\Post as TimberPost;
use Timber\Term as TimberTerm;

if ( ! class_exists( 'P4CT_Search' ) ) {

	/**
	 * Abstract Class P4CT_Search
	 */
	abstract class P4CT_Search {

		const POSTS_LIMIT           = 300;
		const POSTS_PER_PAGE        = 10;
		const POSTS_PER_LOAD        = 5;
		const SHOW_SCROLL_TIMES     = 2;
		const DEFAULT_SORT          = '_score';
		const DEFAULT_MIN_WEIGHT    = 1;
		const DEFAULT_PAGE_WEIGHT   = 100;
		const DEFAULT_ACTION_WEIGHT = 2000;
		const DEFAULT_MAX_WEIGHT    = 3000;
		const DEFAULT_CACHE_TTL     = 600;
		const DUMMY_THUMBNAIL       = '/images/dummy-thumbnail.png';
		const POST_TYPES            = [
			'page',
			'post',
			'attachment',
		];
		const DOCUMENT_TYPES        = [
			'application/pdf',
		];

		/**
		 * Engaging campaign ID meta key.
		 *
		 * @const string ENGAGING_CAMPAIGN_ID_META_KEY
		 */
		const ENGAGING_CAMPAIGN_ID_META_KEY = 'engaging_campaign_ID';

		/**
		 * Search Query
		 *
		 * @var string $search_query
		 */
		protected $search_query;

		/**
		 * Posts
		 *
		 * @var array|bool|null $posts
		 */
		protected $posts;

		/**
		 * Terms
		 *
		 * @var array|bool|null $terms
		 */
		protected $terms;

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
		 * AJAX request flag
		 *
		 * @var bool $is_ajax_request
		 */
		protected $is_ajax_request = false;

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
				'show_scroll_times' => self::SHOW_SCROLL_TIMES,
			];
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
			add_filter( 'posts_where', [ $this, 'edit_search_mime_types' ] );
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
		public function load( $search_query, $selected_sort = self::DEFAULT_SORT, $filters = [], $templates = [ 'search.twig', 'archive.twig', 'index.twig' ], $context = null ) {

			$this->is_ajax_request = false;
			$this->initialize();
			$this->search_query = $search_query;
			$this->templates    = $templates;

			if ( $context ) {
				$this->context = $context;
			} else {
				$this->context = Timber::get_context();

				// Validate user input (sort, filters, etc).
				if ( $this->validate( $selected_sort, $filters, $this->context ) ) {
					$this->selected_sort = $selected_sort;
					$this->filters       = $filters;
				}

				// Set the decoded url query string as key.
				$query_string = urldecode( filter_input( INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING ) );
				$group        = 'search';
				$subgroup     = $this->search_query ? $this->search_query : 'all';
				// Check Object cache for stored key.
				$this->check_cache( $query_string, "$group:$subgroup" );

				// If posts were found either in object cache or primary database then get the first POSTS_PER_LOAD results.
				if ( $this->posts ) {
					$this->paged_posts = array_slice( $this->posts, 0, self::POSTS_PER_LOAD );
				}

				$this->current_page = ( 0 === get_query_var( 'paged' ) ) ? 1 : get_query_var( 'paged' );
				$this->set_context( $this->context );
			}
		}

		/**
		 * Conducts the actual AJAX search.
		 * TODO rename without gpea_.
		 *
		 * @param string     $search_query The searched term.
		 * @param string     $selected_sort The selected order_by.
		 * @param array      $filters The selected filters.
		 * @param array|null $context An associative array with all the context needed to render the template found first.
		 */
		public function gpea_load_ajax( $search_query, $selected_sort = self::DEFAULT_SORT, $filters = [], $context = null ) {

			$this->search_query = $search_query;
			$this->context = Timber::get_context();
			$this->is_ajax_request = true;

			// Validate user input (sort, filters, etc).
			if ( $this->validate( $selected_sort, $filters, $this->context ) ) {
				$this->selected_sort = $selected_sort;
				$this->filters       = $filters;
			}

			// Set the decoded url query string as key.
			$query_string = urldecode( filter_input( INPUT_SERVER, 'search', FILTER_SANITIZE_STRING ) );
			$group        = 'search';
			$subgroup     = $this->search_query ? $this->search_query : 'all';

			// Check Object cache for stored key.
			$this->check_cache( $query_string, "$group:$subgroup" );

			// If posts were found either in object cache or primary database then get the first POSTS_PER_LOAD results.
			if ( $this->posts ) {
				$this->paged_posts = array_slice( $this->posts, 0, self::POSTS_PER_LOAD );
			}

			$this->current_page = 1;
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

					// Get the decoded url query string and then use it as key for redis.
					$query_string_full = urldecode( filter_input( INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING ) );
					$query_string      = str_replace( '&query-string=', '', strstr( $query_string_full, '&query-string=' ) );

					$group                      = 'search';
					$subgroup                   = $search_async->search_query ? $search_async->search_query : 'all';
					$search_async->current_page = $paged;

					parse_str( $query_string, $filters_array );
					$selected_sort    = $filters_array['orderby'] ?? self::DEFAULT_SORT;
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
					$search_async->check_cache( $query_string, "$group:$subgroup" );

					// Check if there are results already in the cache else fallback to the primary database.
					if ( $search_async->posts ) {
						$search_async->paged_posts = array_slice( $search_async->posts, ( $search_async->current_page - 1 ) * self::POSTS_PER_LOAD, self::POSTS_PER_LOAD );
					} else {
						$search_async->paged_posts = $search_async->get_timber_posts( $search_async->current_page );
					}

					// If there are paged results then set their context and send them back to client.
					if ( $search_async->paged_posts ) {
						$search_async->set_results_context( $search_async->context );
						$search_async->view_paged_posts();
					}
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
		protected function check_cache( $cache_key, $cache_group ) {
			$cache_group_terms = $cache_group . '_terms';
			// TODO IMPORTANT!!! reactivate cache on production.
			// Get search results from cache and then set the context for those results.
			// $this->posts = wp_cache_get( $cache_key, $cache_group );
			// $this->terms = wp_cache_get( $cache_key, $cache_group_terms );
			$this->posts = false;
			$this->terms = false;
			// If cache key expired then retrieve results once again and re-cache them.
			if ( false === $this->posts ) {
				$this->posts = $this->get_timber_posts();
				// if ( $this->posts ) {
				// wp_cache_add( $cache_key, $this->posts, $cache_group, self::DEFAULT_CACHE_TTL );
				// }
			}
			if ( false === $this->terms ) {
				$this->terms = $this->get_timber_terms();
				// if ( $this->terms ) {
				// wp_cache_add( $cache_key, $this->posts, $cache_group_terms, self::DEFAULT_CACHE_TTL );
				// }
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
					$timber_posts[] = new TimberPost( $post->ID );
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
		public function get_posts( $paged = 1 ) : array {
			$args = [];

			$this->set_general_args( $args, $paged );
			try {
				$this->set_filters_args( $args );
			} catch ( UnexpectedValueException $e ) {
				$this->context['exception'] = $e->getMessage();
				return [];
			}
			$this->set_engines_args( $args );

			$posts = ( new WP_Query( $args ) )->posts;
			return (array) $posts;
		}

		/**
		 * Gets the respective Timber Terms, to be used with the twig template.
		 * If there are not then uses Timber's get_posts to retrieve all of them (up to the limit set).
		 *
		 * @param int $paged The number of the page of the results to be shown when using pagination/load_more.
		 *
		 * @return array The respective Timber Posts.
		 */
		protected function get_timber_terms( $paged = 1 ) : array {
			$timber_terms = [];

			$terms = $this->get_terms( $paged );
			// Use Timber's Post instead of WP_Post so that we can make use of Timber within the template.
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$timber_terms[] = new TimberTerm( $term->term_id );
				}
			}
			return (array) $timber_terms;
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
				'posts_per_page' => self::POSTS_LIMIT,          // Set a high maximum because -1 will get ALL posts and this can be very intensive in production.
				'no_found_rows'  => true,                       // This means that the result counters of each filter might not be 100% precise.
				'post_type'      => self::POST_TYPES,
				'post_status'    => [ 'publish', 'inherit' ],
			];

			if ( $paged > 1 ) {
				$args['posts_per_page'] = self::POSTS_PER_LOAD;
				$args['paged']          = $paged;
			}

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
			$context['posts']            = $this->posts;
			$context['terms']            = $this->terms;
			$context['paged_posts']      = $this->paged_posts;
			$context['current_page']     = $this->current_page;
			$context['search_query']     = $this->search_query;
			$context['selected_sort']    = $this->selected_sort;
			$context['default_sort']     = self::DEFAULT_SORT;
			$context['filters']          = $this->filters;
			$context['found_posts']      = count( (array) $this->posts );
			$context['page_category']    = 'Search Page';
			$context['sort_options']     = [
				'_score'    => [
					'name'  => __( 'Most relevant', 'planet4-master-theme' ),
					'order' => 'DESC',
				],
				'post_date' => [
					'name'  => __( 'Most recent', 'planet4-master-theme' ),
					'order' => 'DESC',
				],
			];

			if ( $this->search_query ) {
				$context['page_title'] = sprintf(
					// translators: %1$d = Number of results.
					_n( '%1$d result for \'%2$s\'', '%1$d results for \'%2$s\'', $context['found_posts'], 'planet4-master-theme' ),
					$context['found_posts'],
					$this->search_query
				);
			} else {
				// translators: %d = Number of results.
				$context['page_title'] = sprintf( _n( '%d result', '%d results', $context['found_posts'], 'planet4-master-theme' ), $context['found_posts'] );
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
			// Retrieve P4CT settings in order to check that we add only categories that are children of the Issues category.
			$options = get_option( 'planet4_options' );
			$issues_parent_category = (int) $options['issues_parent_category'];

			// Category <-> Issue.
			// Consider Issues that have multiple Categories.
			$categories = get_categories();
			if ( $categories ) {
				foreach ( $categories as $category ) {
					if ( $category->parent === $issues_parent_category ) {
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
					// Get only tags that have an associated Engaging campaign.
					if ( get_term_meta( $tag->term_id, self::ENGAGING_CAMPAIGN_ID_META_KEY, true ) ) {
						// Tag filters.
						$context['tags'][ $tag->term_id ] = [
							'term_id' => $tag->term_id,
							'name'    => $tag->name,
							'results' => 0,
						];
					}
				}
			}

			$context['content_types']['0'] = [
				'name'    => __( 'Page', 'planet4-master-theme' ),
				'results' => 0,
			];
			$context['content_types']['1'] = [
				'name'    => __( 'Post', 'planet4-master-theme' ),
				'results' => 0,
			];

			// Keep track of which filters are already checked.
			if ( $this->filters ) {
				foreach ( $this->filters as $type => $filter ) {
					switch ( $type ) {
						case 'cat':
							$context['categories'][ $filter ]['selected'] = true;
							break;
						case 'tag':
							$context['tags'][ $filter ]['selected'] = true;
							break;
						default:
							throw new UnexpectedValueException( 'Unexpected filter!' );
					}
				}
			}

			// Sort associative array with filters alphabetically .
			if ( $context['categories'] ) {
				uasort(
					$context['categories'],
					function ( $a, $b ) {
						return strcmp( $a['name'], $b['name'] );
					}
				);
			}
			if ( $context['tags'] ) {
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

			$posts = $this->posts;

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
							$content_type_text = __( 'PAGE', 'planet4-master-theme' );
							$content_type      = 'page';
							$context['content_types']['0']['results']++;
						break;
					case 'post':
						$content_type_text = __( 'POST', 'planet4-master-theme' );
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
							if ( $category->parent === (int) $options['issues_parent_category'] ) {
								$context['categories'][ $category->term_id ]['term_id'] = $category->term_id;
								$context['categories'][ $category->term_id ]['name']    = $category->name;
								$context['categories'][ $category->term_id ]['results']++;
							}
						}
					}

					$tags = get_the_terms( $post->ID, 'post_tag' );
					if ( $tags ) {
						foreach ( (array) $tags as $tag ) {
							// Get only tags that have an associated Engaging campaign
							if ( get_term_meta( $tag->term_id, self::ENGAGING_CAMPAIGN_ID_META_KEY, true ) ) {
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

			if ( ! is_admin() && is_search() ||
			wp_doing_ajax() && ( 'get_paged_posts' === $search_action ) ) {
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
				'posts_per_load' => self::POSTS_PER_LOAD,
				// Translators: %s = number of results per page.
				'button_text'    => sprintf( __( 'SHOW %s MORE RESULTS', 'planet4-master-theme' ), self::POSTS_PER_LOAD ),
				'async'          => true,
			];
		}

		/**
		 * View the Search page template.
		 */
		public function view() {
			Timber::render(
				$this->templates,
				$this->context
				// TODO Uncomment these to enable search page cache.
				// ,self::DEFAULT_CACHE_TTL,
				// \Timber\Loader::CACHE_OBJECT
			);
		}

		/**
		 * Return search results as JSON.
		 * TODO rename without gpea_.
		 */
		public function gpea_view_json() {
			return wp_json_encode(
				array(
					'posts' => array_slice( $this->context['posts'], 0, self::POSTS_PER_LOAD ),
					'terms' => array_slice( $this->context['terms'], 0, self::POSTS_PER_LOAD ),
				)
			);
			// return wp_json_encode( array_slice( $this->context['posts'], 0, self::POSTS_PER_LOAD ) );
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
					if ( 0 === $index % self::POSTS_PER_LOAD ) {
						$paged_context['first_of_the_page'] = true;
					} else {
						$paged_context['first_of_the_page'] = false;
					}
					Timber::render( [ 'tease-search.twig' ], $paged_context, self::DEFAULT_CACHE_TTL, \Timber\Loader::CACHE_OBJECT );
				}
			}
		}

		/**
		 * Load assets only on the search page.
		 */
		public function enqueue_public_assets() {
			if ( is_search() ) {
				$js_creation = filectime( get_stylesheet_directory() . '/static/js/search.js' );

				wp_register_script( 'search-script', get_stylesheet_directory_uri() . '/static/js/search.js', [], $js_creation, true );
				wp_localize_script( 'search-script', 'localizations', $this->localizations );
				wp_enqueue_script( 'search-script' );
			}
		}
	}
}
