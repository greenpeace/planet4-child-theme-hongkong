<?php
/**
 * P4CT ElasticSearch Class
 *
 * @package P4CT
 */

if ( ! class_exists( 'P4CT_ElasticSearch' ) ) {

	/**
	 * Class P4CT_ElasticSearch
	 */
	class P4CT_ElasticSearch extends P4CT_Search {

		/**
		 * Applies user selected filters to the search if there are any and gets the filtered posts.
		 *
		 * @param array $args The array with the arguments that will be passed to WP_Query.
		 */
		public function set_engines_args( &$args ) {

			$args['ep_integrate'] = true;

			// Get only DOCUMENT_TYPES from the attachments.
			if ( ! $this->search_query && ! $this->filters ) {
				add_filter(
					'ep_formatted_args',
					function ( $formatted_args ) use ( $args ) {
						// TODO - Fix parsing exception in EP API call to Elasticsearch.
						$formatted_args['post_mime_type'] = self::DOCUMENT_TYPES;
						return $formatted_args;
					},
					10,
					1
				);
			}

			add_filter( 'ep_formatted_args', [ $this, 'set_full_text_search' ], 19, 1 );
			add_filter( 'ep_formatted_args', [ $this, 'set_results_weight' ], 20, 1 );

			// Remove from results any Documents that should not be there.
			// TODO - This is a temp fix until we manage to query ES for only the desired documents.
			add_filter(
				'ep_search_results_array',
				function ( $results, $response, $args, $scope ) {
					foreach ( $results['posts'] as $key => $post ) {
						if ( $post['post_mime_type'] && ! in_array( $post['post_mime_type'], self::DOCUMENT_TYPES, true ) ) {
							unset( $results['posts'][ $key ] );
						}
					}
					return $results;
				},
				10,
				4
			);
		}

		/**
		 * Apply full-text search.
		 *
		 * @param mixed $formatted_args Assoc array with the args that ES expects.
		 *
		 * @return mixed
		 */
		public function set_full_text_search( $formatted_args ) {
			if ( isset( $formatted_args['query']['function_score']['query']['bool'] ) ) {
				// Create/change the bool query from should to must.
				$formatted_args['query']['function_score']['query']['bool']['must'] = $formatted_args['query']['function_score']['query']['bool']['should'];
				// Add the operator AND to the new bool query.
				$formatted_args['query']['function_score']['query']['bool']['must'][0]['multi_match']['operator'] = 'AND';
				// Erase the old should query.
				unset( $formatted_args['query']['function_score']['query']['bool']['should'] );
				// Erase the phrase matching (to make sure we get results that include both 'courageous' AND 'act' instead of only those with 'courageous act'.
				unset( $formatted_args['query']['function_score']['query']['bool']['must'][0]['multi_match']['type'] );
			}
			return $formatted_args;
		}

		/**
		 * Apply custom weight to search results.
		 *
		 * @param mixed $formatted_args Assoc array with the args that ES expects.
		 *
		 * @return mixed
		 */
		public function set_results_weight( $formatted_args ) {

			// Move the existing query.
			$existing_query = $formatted_args['query'];
			unset( $formatted_args['query'] );
			$formatted_args['query']['function_score']['query'] = $existing_query;

			$options = get_option( 'planet4_options' );

			/**
			 * Use any combination of filters here, any matched filter will adjust
			 * the weighted results according to the scoring settings set below.
			 */
			$formatted_args['query']['function_score']['functions'] = [
				[
					'filter' => [
						'match' => [
							'post_type' => 'page',
						],
					],
					'weight' => self::DEFAULT_PAGE_WEIGHT,
				],
				[
					'filter' => [
						'term' => [
							'post_parent' => esc_sql( $options['act_page'] ),
						],
					],
					'weight' => self::DEFAULT_ACTION_WEIGHT,
				],
			];

			// Specify how the computed scores are combined.
			$formatted_args['query']['function_score']['score_mode'] = 'sum';

			return $formatted_args;
		}
	}
}
