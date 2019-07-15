<?php
/**
 * P4 Shortcode Class
 *
 * @package P4CT
 */

/**
 * Class P4CT_Shortcode
 */
class P4CT_Shortcode {

	/**
	 * P4CT_Custom_Post_Type_Register constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Class hooks.
	 */
	private function hooks() {
		// add_action( 'init', [ $this, 'register_p4_custom_post_type' ] );
	}

}
