<?php
/**
 * P4 AJAX Handler Class
 *
 * @package P4CT
 */

/**
 * Class P4CT_AJAX_Handler
 */
class P4CT_AJAX_Handler {

	/**
	 * The nonce string.
	 *
	 * @const string NONCE_STRING
	 */
	const SUPPORT_LAUNCHER_NONCE_STRING = 'support_launcher';
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
	 * Send mail upon AJAX request from support_launcher module.
	 */
	public function topics_following_ajax_handler() {		

		if ( ( ! isset( $_COOKIE['gpea_issues'] ) ) && ( ! isset( $_COOKIE['gpea_topics'] ) ) ) {
			return;
		}

		// if ( ! wp_verify_nonce( $data['_wpnonce'], self::TOPICS_FOLLOWING_NONCE_STRING ) ) {
		// 	$this->safe_echo( __( 'Did not save because your form seemed to be invalid. Sorry.', 'planet4-child-theme-backend' ) );
		// 	return;
		// }

		$posts_result = array();

		if ( isset( $_COOKIE['gpea_issues'] ) ) {
			$gpea_issues = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_issues'] ) ) );

			foreach ( $gpea_issues as $gpea_issue ) {
				$query = new \WP_Query(
					array(
						'cat'            => $gpea_issue,
						'order'          => 'desc',
						'orderby'        => 'date',
						'posts_per_page' => 5,
					)
				);

				$posts = $query->posts;

				if ( $posts ) {
					foreach ( $posts as $post ) {
						$posts_result[] = array(
							'title' => $post->post_title,
							'post_date' => date( 'Y - m - d', strtotime( $post->post_date ) ),
						);
					}
				}
				// $this->safe_echo( json_encode( $posts_result ) );
				// return;
			}
		}

		if ( isset( $_COOKIE['gpea_topics'] ) ) {
			$gpea_topics = json_decode( sanitize_text_field( wp_unslash( $_COOKIE['gpea_topics'] ) ) );

			foreach ( $gpea_topics as $gpea_topic ) {
				$query = new \WP_Query(
					array(
						'tag_id'            => $gpea_topic,
						'order'          => 'desc',
						'orderby'        => 'date',
						'posts_per_page' => 5,
					)
				);

				$posts = $query->posts;

				if ( $posts ) {
					foreach ( $posts as $post ) {
						$posts_result[] = array(
							'title' => $post->post_title,
							'post_date' => date( 'Y - m - d', strtotime( $post->post_date ) ),
						);
					}
				}
			}

			if ( $posts_result ) $this->safe_echo( json_encode( $posts_result ) );
			return;

		}

	}

	/**
	 * Echo escaped response and stop processing the request.
	 *
	 * @param string $string The message to be sent.
	 */
	private function safe_echo( $string ) {
		echo esc_html( $string );
		wp_die();
	}


}
