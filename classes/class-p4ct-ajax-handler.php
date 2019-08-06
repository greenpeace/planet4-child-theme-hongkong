<?php
/**
 * P4 AJAX Handler Class
 *
 * @package P4CT
 */

use P4CT_Site as P4CTSite;


/**
 * Class P4CT_AJAX_Handler
 */
class P4CT_AJAX_Handler {

	/**
	 * The nonce string.
	 *
	 * @const string SUPPORT_LAUNCHER_NONCE_STRING
	 */
	const SUPPORT_LAUNCHER_NONCE_STRING = 'support_launcher';

	/**
	 * The nonce string.
	 *
	 * @const string TOPICS_FOLLOWING_NONCE_STRING
	 */
	const TOPICS_FOLLOWING_NONCE_STRING = 'topics_following';

	/**
	 * Holds P4CTSite
	 *
	 * @var P4CTSite
	 */
	private $gpea_extra;

	/**
	 * P4CT_AJAX_Handler constructor.
	 */
	public function __construct() {
		$this->hooks();
		$this->gpea_extra = new P4CTSite();
	}

	/**
	 * Class hooks.
	 */
	private function hooks() {
		add_action( 'wp_ajax_supportLauncher', [ $this, 'support_launcher_ajax_handler' ] );
		add_action( 'wp_ajax_nopriv_supportLauncher', [ $this, 'support_launcher_ajax_handler' ] );
		add_action( 'wp_ajax_topicsFollowing', [ $this, 'topics_following_ajax_handler' ] );
		add_action( 'wp_ajax_nopriv_topicsFollowing', [ $this, 'topics_following_ajax_handler' ] );
		add_action( 'wp_ajax_projectsFollowing', [ $this, 'projects_following_ajax_handler' ] );
		add_action( 'wp_ajax_nopriv_projectsFollowing', [ $this, 'projects_following_ajax_handler' ] );

		// TODO maybe move these ones to P4CT_ElasticSearch class?
		add_action( 'wp_ajax_p4ct_search_site', [ $this, 'search_posts_ajax' ] );
		add_action( 'wp_ajax_nopriv_p4ct_search_site', [ $this, 'search_posts_ajax' ] );
	}

	/**
	 * Send mail upon AJAX request from support_launcher module.
	 */
	public function support_launcher_ajax_handler() {

		$args = [
			'action' => FILTER_SANITIZE_STRING,
			'recipient_email' => FILTER_SANITIZE_EMAIL,
			'subject' => FILTER_SANITIZE_STRING,
			'send_to' => FILTER_SANITIZE_URL,
			'name' => FILTER_SANITIZE_STRING,
			'email' => FILTER_SANITIZE_EMAIL,
			'message' => FILTER_SANITIZE_STRING,
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'_wp_http_referer' => FILTER_SANITIZE_STRING,
		];
		$data = filter_input_array( INPUT_POST , $args , false );

		if ( ! ( $data && wp_verify_nonce( $data['_wpnonce'], self::SUPPORT_LAUNCHER_NONCE_STRING ) ) ) {
			$this->safe_echo( __( 'Did not save because your form seemed to be invalid. Sorry.', 'gpea_theme' ) );
			return;
		}

		// We assume $data['recipient_email'], $data['subject'] to have correct values.
		if ( $data['name'] && $data['email'] && $data['message'] ) {

			$to = $data['recipient_email'];
			$subject = $data['subject'];
			$message = $data['message'];
			$headers = [ "From: $data[name] <$data[email]>\n" ];

			wp_mail( $to, $subject, $message, $headers );

			$this->safe_echo( __( 'Message sent.', 'gpea_theme' ) );
			return;

		} else {
			$this->safe_echo( __( 'Could not send the message, form fields missing.', 'gpea_theme' ) );
			return;
		}

	}

	/**
	 * Prepare followeing information and content.
	 */
	public function topics_following_ajax_handler() {

		$data = $_POST;

		if ( isset( $data['star'] ) ) {
			$post_preferences = sanitize_text_field( $data['star'] );
		} else {
			$post_preferences = false;
		}

		$posts_result = array();

		if ( ( ! isset( $_COOKIE['gpea_issues'] ) ) && ( ! isset( $_COOKIE['gpea_topics'] ) ) ) {
			$this->safe_echo( json_encode( $posts_result ), false );
			return;
		}

		$post_results_issue = array();
		if ( isset( $_COOKIE['gpea_issues'] ) ) {
			$gpea_issues = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_issues'] ) ) );

			$post_results_issue = $this->get_carousel_posts( $gpea_issues, 'cat', $post_preferences );

		}

		$post_results_topic = array();
		if ( isset( $_COOKIE['gpea_topics'] ) ) {
			$gpea_topics = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_topics'] ) ) );

			$post_results_topic = $this->get_carousel_posts( $gpea_topics, 'tag', $post_preferences );

		}


		$posts_result = array_merge( $post_results_issue, $post_results_topic );

		// if ( $posts_result ) {
			$this->safe_echo( json_encode( $posts_result ), false );
		// }
		return;

	}

	/**
	 * Prepare followeing information and content.
	 */
	public function projects_following_ajax_handler() {

		if ( ! isset( $_COOKIE['gpea_projects'] ) ) {
			return;
		}

		// if ( ! wp_verify_nonce( $data['_wpnonce'], self::TOPICS_FOLLOWING_NONCE_STRING ) ) {
		// $this->safe_echo( __( 'Did not save because your form seemed to be invalid. Sorry.', 'gpea_theme_backend' ) );
		// return;
		// }
		$posts_result = array();

		if ( isset( $_COOKIE['gpea_projects'] ) ) {
			$gpea_projects = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_projects'] ) ) );

			foreach ( $gpea_projects as $gpea_project ) {

				$gpea_project_id = intval( $gpea_project );

				// we cache single tag and category request, then combine cached results
				$cache_key = 'following_' . $gpea_project_id . '_project';
				$cache_group = 'following';

				$cached_result = wp_cache_get( $cache_key, $cache_group );

				if ( false !== $cached_result ) {
					$posts_result[] = $cached_result;
					// if in cache we set the results corresponding value and continue to next project id
					continue;
				}

				$project_detail = array();
				$project_detail['title'] = get_the_title( $gpea_project_id );
				if ( has_post_thumbnail( $gpea_project_id ) ) {
					$img_id                  = get_post_thumbnail_id( $gpea_project_id );
					$img_data                = wp_get_attachment_image_src( $img_id, 'medium_large' );
					$project_detail['image'] = $img_data[0];
				}
				$project_meta                         = get_post_meta( $gpea_project_id );
				$project_detail['link']               = get_the_permalink( $gpea_project_id );
				$project_detail['start_date']         = $project_meta['p4-gpea_project_start_date'][0] ?? '';
				$project_detail['localization']       = $project_meta['p4-gpea_project_localization'][0] ?? '';
				$project_detail['project_percentage'] = $project_meta['p4-gpea_project_percentage'][0] ?? 0;
				$project_detail['stroke_dashoffset']  = $project_detail['project_percentage'] ? 697.433 * ( ( 100 - $project_detail['project_percentage'] ) / 100 ) : 0;

				$main_issues = $this->gpea_extra->gpea_get_main_issue( $gpea_project_id );
				if ( $main_issues ) {
					$project_detail['main_issue_slug'] = $main_issues->slug;
					$project_detail['main_issue_name'] = $main_issues->name;
				}

				// get related posts
				$the_query = new \WP_Query(
					array(
						'meta_key'       => 'p4_select_project_related',
						'meta_value'     => $gpea_project_id,
						'order'          => 'desc',
						'orderby'        => 'date',
						'posts_per_page' => 3,
					)
				);

				$project_related = array();

				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) :

						$the_query->the_post();
						$single_update = array(
							'title'     => get_the_title(),
							//'post_date' => date( 'Y - m - d', strtotime( get_the_date() ) ),
							'post_date'       => get_the_date(),
							'link'      => get_the_permalink( $post->ID ),
						);

						// other info
						$main_issues = $this->gpea_extra->gpea_get_main_issue( $post->ID );
					if ( $main_issues ) {
						$single_update['main_issue_slug'] = $main_issues->slug;
						$single_update['main_issue_name'] = $main_issues->name;
					}

					if ( has_post_thumbnail( $post->ID ) ) {
						$img_id                  = get_post_thumbnail_id( $post->ID );
						$img_data                = wp_get_attachment_image_src( $img_id, 'medium_large' );
						$single_update['image'] = $img_data[0];
					}

					// news type
					$news_type = wp_get_post_terms( $post->ID, 'p4-page-type' );
					if ( $news_type ) {
						$single_update['news_type'] = $news_type[0]->name;
					}

					$single_update['reading_time'] = get_post_meta( $post->ID, 'p4-gpea_post_reading_time', true );

					$project_related[] = $single_update;
				endwhile;
				}

				$project_detail['related'] = $project_related;

				wp_reset_query();
				wp_reset_postdata();

				// store in cache 
				wp_cache_add( $cache_key, $project_detail, $cache_group );

				$posts_result[] = $project_detail;

			}

			// if ( $posts_result ) {
				$this->safe_echo( json_encode( $posts_result ), false );
			// }
			return;

		}
	}

	/**
	 * Get post results for AJAX autocomplete.
	 */
	public function search_posts_ajax() {
		$query = $_POST['search'];
		$search = new P4CT_ElasticSearch(); // TODO Can we create a new search only once?
		$search->gpea_load_ajax( $query, $selected_sort, $filters );
		$this->safe_echo( $search->gpea_view_json(), false );
	}

	/**
	 * Retrieve posts from array of elements
	 *
	 * @tags array $array elements to be queried
	 * @type can be cat or tag
	 * @preferences can be petition
	 */
	private function get_carousel_posts( $tags, $type, $preferences ) {
		$results = array();

		foreach ( $tags as $tag_id ) {

			// we cache single tag and category request, then combine cached results
			$cache_key = 'following_' . $tag_id . '_' . $type . '_' . ( $preferences ? $preferences : 'none' );
			$cache_group = 'following';

			$cached_result = wp_cache_get( $cache_key, $cache_group );

			if ( false !== $cached_result ) {
				$results[ $tag_id ] = $cached_result;
				// if in cache we set the results corresponding value and continue to next tag_id
				continue;
			}
			$args = array(
				'post_type'      => array( 'page', 'post' ),
				'order'          => 'desc',
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'posts_per_page' => 5,
			);

			if ( 'cat' === $type ) {
				$args['cat'] = $tag_id;
				$term_info   = get_term( $tag_id, 'category' );

				if ( 'petition' === $preferences ) {
					$args['tag'] = 'petition';
				}
			}

			if ( 'tag' === $type ) {
				$term_info   = get_term( $tag_id, 'post_tag' );
				if ( 'petition' === $preferences ) {
					$args['tag_slug__and'] = array( 'petition', $term_info->slug );
				} else {
					$args['tag_id'] = $tag_id;
				}
			}

			$the_query = new \WP_Query( $args );

			if ( $the_query->have_posts() ) {
				$most_recent = 0;

				if ( $term_info ) {
					$results[ $tag_id ]['name']     = $term_info->name;
					$results[ $tag_id ]['slug']     = $term_info->slug;
					if ( 'category' === $term_info->taxonomy ) {
						$results[ $tag_id ]['cat_id']  = $term_info->term_id;
						$results[ $tag_id ]['tag_id']  = 0;
					} else if ( 'post_tag' === $term_info->taxonomy ) {
						$results[ $tag_id ]['tag_id']  = $term_info->term_id;
						$results[ $tag_id ]['cat_id']  = 0;
					} 
					$results[ $tag_id ]['term_id']  = $term_info->term_id;
				}

				while ( $the_query->have_posts() ) :

					$the_query->the_post();
					$single_update = array(
						'ID'         => get_the_ID(),
						'post_title' => get_the_title(),
						//'date'       => date( 'Y - m - d', strtotime( get_the_date() ) ),
						'date'       => get_the_date(),
						'link'       => get_the_permalink( $post->ID ),
					);

					// update the most recent date
					$most_recent = ( strtotime( get_the_date() ) > $most_recent ) ? strtotime( get_the_date() ) : $most_recent;

					$single_update['reading_time'] = get_post_meta( $post->ID, 'p4-gpea_post_reading_time', true );

					// other info
					$main_issues = $this->gpea_extra->gpea_get_main_issue( $post->ID );
					if ( $main_issues ) {
						$single_update['main_issue_slug'] = $main_issues->slug;
						$single_update['main_issue'] = $main_issues->name;
					}

					if ( has_post_thumbnail( $post->ID ) ) {
						$img_id                  = get_post_thumbnail_id( $post->ID );
						$img_data                = wp_get_attachment_image_src( $img_id, 'medium_large' );
						$single_update['img_url'] = $img_data[0];
					}

					// news type
					$news_type = wp_get_post_terms( $post->ID, 'p4-page-type' );
					if ( $news_type ) {
						$single_update['news_type'] = $news_type[0]->name;
					}

					// check if petition and, if so, retrieve extra information
					if ( has_term( 'petition', 'post_tag', $post->ID ) ) {
						if ( 'page-templates/petition.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {
							$single_update['engaging_pageid'] = get_post_meta( $post->ID, 'p4-gpea_petition_engaging_pageid', true );
							$single_update['engaging_target'] = get_post_meta( $post->ID, 'p4-gpea_petition_engaging_target', true );

							if ( $single_update['engaging_pageid'] ) {

								// get Engaging options:
								$engaging_settings = get_option( 'p4en_main_settings' );
								$engaging_token = $engaging_settings['p4en_frontend_public_api'];

								global $wp_version;
								$url = 'http://www.e-activist.com/ea-dataservice/data.service?service=EaDataCapture&token=' . $engaging_token . '&campaignId=' . $single_update['engaging_pageid'] . '&contentType=json&resultType=summary';
								$args = array(
									'timeout'     => 5,
									'redirection' => 5,
									'httpversion' => '1.0',
									'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
									'blocking'    => true,
									'headers'     => array(),
									'cookies'     => array(),
									'body'        => null,
									'compress'    => false,
									'decompress'  => true,
									'sslverify'   => true,
									'stream'      => false,
									'filename'    => null,
								);
								$result = wp_remote_get( $url, $args );
								$obj = json_decode( $result['body'], true );
								$single_update['signatures'] = $obj['rows'][0]['columns'][4]['value'];
							}

							if ( $single_update['engaging_target'] && $single_update['signatures'] ) {
								$post->percentage = intval( intval( $single_update['signatures'] ) * 100 / intval( $single_update['engaging_target'] ) );
							} else {
								$post->percentage = 100;
							}

							/* if external link is set, we use that instead of standard one */
							$external_link = get_post_meta( $post->ID, 'p4-gpea_petition_external_link', true );
							if ( $external_link ) $single_update['link'] = $external_link;
						}
					}

					$results[ $tag_id ]['most_recent'] = $most_recent;

					$results[ $tag_id ]['posts'][] = $single_update;

				endwhile;
			}

			wp_reset_query();
			wp_reset_postdata();

			// store in cache 
			wp_cache_add( $cache_key, $results[ $tag_id ], $cache_group );

		}

		return $results;

	}


	/**
	 * Echo escaped response and stop processing the request.
	 *
	 * @param string $string The message to be sent.
	 * @param bool   $escape Whether to esc_html $string.
	 */
	private function safe_echo( $string, $escape = true ) {
		echo $escape ? esc_html( $string ) : $string;
		wp_die();
	}

}
