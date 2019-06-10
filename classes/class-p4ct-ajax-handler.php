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
		add_action( 'wp_ajax_nopriv_supportLauncher', [ $this, 'support_launcher_ajax_handler' ] );

		// TODO maybe move these ones to P4CT_ElasticSearch class?
		add_action( 'wp_ajax_p4ct_search_site', [ $this, 'search_posts_ajax' ] );
		add_action( 'wp_ajax_nopriv_p4ct_search_site', [ $this, 'search_posts_ajax' ] );
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
	 * Get post results for AJAX autocomplete.
	 */
	public function search_posts_ajax() {
		$query = $_POST['search'];
		$search = new P4CT_ElasticSearch(); // TODO Can we create a new search only once?
		$search->load( $query, $selected_sort, $filters );
		$this->safe_echo( $search->view_json(), false );
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
