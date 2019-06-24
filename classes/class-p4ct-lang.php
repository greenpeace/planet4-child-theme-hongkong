<?php
/**
 * P4 Languages Class
 *
 * @package P4CT
 */

use P4CT_Site as P4CTSite;


/**
 * Class P4CT_Lang
 */
class P4CT_Lang {

	/**
	 * P4CT_Lang constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Class hooks.
	 */
	private function hooks() {
		add_action( 'after_setup_theme', [ $this, 'load_textdomain' ] );
		add_action( 'after_setup_theme', [ $this, 'child_theme_slug_setup' ] );
	}

	/**
	 * Load child theme text domain
	 */
	public function load_textdomain() {
		load_theme_textdomain( 'gpea_theme', get_stylesheet_directory() . '/languages' );
	}

	/**
	 * Override parent theme text domain
	 */
	public function child_theme_slug_setup() {
		load_child_theme_textdomain( 'planet4-master-theme', get_stylesheet_directory() . '/languages' );
	}

}
