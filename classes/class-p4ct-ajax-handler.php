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
	 * P4CT_AJAX_Handler constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Class hooks.
	 */
	private function hooks() {
		add_action( 'wp_ajax_supportLauncher', [ $this, 'support_launcher_ajax_handler' ] );
		add_action( 'wp_ajax_topicsFollowing', [ $this, 'topics_following_ajax_handler' ] );
		add_action( 'wp_ajax_nopriv_supportLauncher', [ $this, 'support_launcher_ajax_handler' ] );

		// TODO maybe move these ones to P4CT_ElasticSearch class?
		add_action( 'wp_ajax_p4ct_search_site', [ $this, 'search_posts_ajax' ] );
		add_action( 'wp_ajax_nopriv_p4ct_search_site', [ $this, 'search_posts_ajax' ] );
		add_action( 'wp_ajax_projectsFollowing', [ $this, 'projects_following_ajax_handler' ] );
	}

	/**
	 * Send mail upon AJAX request from support_launcher module.
	 */
	public function support_launcher_ajax_handler() {

		$data = $_POST;

		if ( ! wp_verify_nonce( $data['_wpnonce'], self::SUPPORT_LAUNCHER_NONCE_STRING ) ) {
			$this->safe_echo( __( 'Did not save because your form seemed to be invalid. Sorry.', 'planet4-child-theme-backend' ) );
			return;
		}

		// We assume $data['recipient_email'], $data['subject'] to have correct values.
		if ( $data['sender_name'] && $data['sender_email'] && $data['message'] ) {

			$to = $data['recipient_email'];
			$subject = $data['subject'];
			$message = $data['message'];
			$headers = array( 'From: ' . $data['sender_name'] . ' <' . $data['sender_email'] . '>\r\n' );

			wp_mail( $to, $subject, $message, $headers );

			$this->safe_echo( __( 'Message sent.', 'planet4-child-theme-backend' ) );
			return;

		} else {
			$this->safe_echo( __( 'Could not send the message, form fields missing.', 'planet4-child-theme-backend' ) );
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

		if ( ( ! isset( $_COOKIE['gpea_issues'] ) ) && ( ! isset( $_COOKIE['gpea_topics'] ) ) ) {
			return;
		}

		$posts_result = array();

		if ( isset( $_COOKIE['gpea_issues'] ) ) {
			$gpea_issues = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_issues'] ) ) );

			$post_results_issue = get_carousel_posts( $gpea_issues, 'cat', $post_preferences );
		}

		if ( isset( $_COOKIE['gpea_topics'] ) ) {
			$gpea_topics = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_topics'] ) ) );

			$post_results_topic = get_carousel_posts( $gpea_topics, 'tag', $post_preferences );

		}

		$posts_result = array_merge( $post_results_issue, $post_results_topic );

		if ( $posts_result ) {
			$this->safe_echo( json_encode( $posts_result ) );
		}
		return;

	}

	/**
	 * Prepare followeing information and content.
	 */
	public function projects_following_ajax_handler() {

		if ( ! isset( $_COOKIE['gpea_projects'] ) ) {
			return;
		}

		$gpea_extra = new P4CTSite();
		$main_issues = $gpea_extra->gpea_get_main_issue( 866 );

		// if ( ! wp_verify_nonce( $data['_wpnonce'], self::TOPICS_FOLLOWING_NONCE_STRING ) ) {
		// $this->safe_echo( __( 'Did not save because your form seemed to be invalid. Sorry.', 'planet4-child-theme-backend' ) );
		// return;
		// }
		$posts_result = array();

		if ( isset( $_COOKIE['gpea_projects'] ) ) {
			$gpea_projects = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_projects'] ) ) );

			foreach ( $gpea_projects as $gpea_project ) {

				$gpea_project_id = intval( $gpea_project );

				$project_detail = array();
				$project_detail['title'] = get_the_title( $gpea_project_id );
				if ( has_post_thumbnail( $gpea_project_id ) ) {
					$img_id                  = get_post_thumbnail_id( $gpea_project_id );
					$img_data                = wp_get_attachment_image_src( $img_id, 'medium_large' );
					$project_detail['image'] = $img_data[0];
				}
				$project_meta                         = get_post_meta( $gpea_project_id );
				$project_detail['start_date']         = $project_meta['p4-gpea_project_start_date'][0] ?? '';
				$project_detail['localization']       = $project_meta['p4-gpea_project_localization'][0] ?? '';
				$project_detail['project_percentage'] = $project_meta['p4-gpea_project_percentage'][0] ?? 0;
				$project_detail['stroke_dashoffset']  = $project_detail['project_percentage'] ? 697.433 * ( ( 100 - $project_detail['project_percentage'] ) / 100 ) : 0;

				$main_issues = $gpea_extra->gpea_get_main_issue( $gpea_project_id );
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

				while ( $the_query->have_posts() ) :

						$the_query->the_post();
						$single_update = array(
							'title'     => get_the_title(),
							'post_date' => date( 'Y - m - d', strtotime( get_the_date() ) ),
							'link'      => get_the_permalink( $post->ID ),
						);

						// other info
						$main_issues = $gpea_extra->gpea_get_main_issue( $post->ID );
					if ( $main_issues ) {
						$single_update['main_issue_slug'] = $main_issues->slug;
						$single_update['main_issue_name'] = $main_issues->name;
					}

					if ( has_post_thumbnail( $post->ID ) ) {
						$img_id                  = get_post_thumbnail_id( $post->ID );
						$img_data                = wp_get_attachment_image_src( $img_id, 'medium_large' );
						$single_update['image'] = $img_data[0];
					}

						$project_related[] = $single_update;
				endwhile;

				$project_detail['related'] = $project_related;

				wp_reset_query();
				wp_reset_postdata();

				$posts_result[] = $project_detail;

			}

			if ( $posts_result ) {
				$this->safe_echo( json_encode( $posts_result ) );
			}
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

			$args = array(
				'order'          => 'desc',
				'orderby'        => 'date',
				'posts_per_page' => 5,
			);

			if ( 'cat' === $type ) {
				$args['cat'] = $tag_id;
				if ( 'petition' === $preferences ) {
					$args['tag'] = 'petition';
				}
			}

			if ( 'tag' === $type ) {
				$args['tag_id'] = $tag_id;
				if ( 'petition' === $preferences ) {
					$args['tag'] = 'petition';
				}
			}

			$the_query = new \WP_Query( $args );

			while ( $the_query->have_posts() ) :

				$the_query->the_post();
				$single_update = array(
					'title'     => get_the_title(),
					'post_date' => date( 'Y - m - d', strtotime( get_the_date() ) ),
					'link'      => get_the_permalink( $post->ID ),
				);

				$single_update['reading_time'] = get_post_meta( $post->ID, 'p4-gpea_post_reading_time', true );

				// other info
				$main_issues = $gpea_extra->gpea_get_main_issue( $post->ID );
				if ( $main_issues ) {
					$single_update['main_issue_slug'] = $main_issues->slug;
					$single_update['main_issue_name'] = $main_issues->name;
				}

				if ( has_post_thumbnail( $post->ID ) ) {
					$img_id                  = get_post_thumbnail_id( $post->ID );
					$img_data                = wp_get_attachment_image_src( $img_id, 'medium_large' );
					$single_update['image'] = $img_data[0];
				}

				$results[] = $single_update;
			endwhile;

			wp_reset_query();
			wp_reset_postdata();

			return $results;

		}

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
