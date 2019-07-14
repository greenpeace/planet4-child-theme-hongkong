<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */


$context['strings'] = [
	'issue'           => __( 'Issue', 'gpea_theme' ),
	'topic'           => __( 'Topic', 'gpea_theme' ),
	'posts'           => __( 'Posts', 'gpea_theme' ),
	'advanced_search' => __( 'Advanced search', 'gpea_theme' ),
	'filters'         => __( 'Filters', 'gpea_theme' ),
	'none'            => __( 'None', 'gpea_theme' ),
	'start_typing'    => __( 'Start typing', 'gpea_theme' ),
];

/**
 * Planet4 Child Theme - Search functionality.
 */
if ( is_main_query() && is_search() ) {
	if ( 'GET' === filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) ) {
		$selected_sort    = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING );
		// TODO check nonce here?
		$filters = $_GET['f'] ?? '';
		$search = new P4CT_ElasticSearch();
		$search->load( trim( get_search_query() ), $selected_sort, $filters );
		$search->add_load_more();
		$search->view();
	}
}
