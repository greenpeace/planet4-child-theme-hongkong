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

/**
 * Planet4 Child Theme - Search functionality.
 */
if ( is_main_query() && is_search() ) {
	if ( 'GET' === filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) ) {
		$selected_sort    = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING );
		$selected_filters = $_GET['f'] ?? '';
		$filters          = [];
		// Handle submitted filter options .
		if ( $selected_filters && is_array( $selected_filters ) ) {
			foreach ( $selected_filters as $type => $filter_type ) {
				foreach ( $filter_type as $name => $id ) {
					$filters[ $type ][] = [
						'id'   => $id,
						'name' => $name,
					];
				}
			}
		}
		$search = new P4CT_ElasticSearch();
		$search->load( trim( get_search_query() ), $selected_sort, $filters );
		$search->add_load_more();
		$search->view();
	}
}
