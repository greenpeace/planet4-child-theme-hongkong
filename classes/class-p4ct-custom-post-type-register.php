<?php
/**
 * P4 Custom Post Type Register Class
 *
 * @package P4CT
 */

/**
 * Class P4CT_Custom_Post_Type_Register
 */
class P4CT_Custom_Post_Type_Register {

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
		add_action( 'init', [ $this, 'register_p4_custom_post_type' ] );
	}

	/**
	 * Register P4 meta box(es).
	 */
	public function register_p4_custom_post_type() {
		$this->register_post_type_tips();
		$this->register_post_type_team();
		$this->register_post_type_stories();
	}

	/**
	 * Register a tips post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type_tips() {

		$labels = array(
			'name'               => _x( 'Tips', 'post type general name', 'gpea_theme' ),
			'singular_name'      => _x( 'Tip', 'post type singular name', 'gpea_theme' ),
			'menu_name'          => _x( 'Tips', 'admin menu', 'gpea_theme' ),
			'name_admin_bar'     => _x( 'Tip', 'add new on admin bar', 'gpea_theme' ),
			'add_new'            => _x( 'Add New', 'tip', 'gpea_theme' ),
			'add_new_item'       => __( 'Add New Tip', 'gpea_theme' ),
			'new_item'           => __( 'New Tip', 'gpea_theme' ),
			'edit_item'          => __( 'Edit Tip', 'gpea_theme' ),
			'view_item'          => __( 'View Tip', 'gpea_theme' ),
			'all_items'          => __( 'All Tips', 'gpea_theme' ),
			'search_items'       => __( 'Search Tips', 'gpea_theme' ),
			'parent_item_colon'  => __( 'Parent Tips:', 'gpea_theme' ),
			'not_found'          => __( 'No tips found.', 'gpea_theme' ),
			'not_found_in_trash' => __( 'No tips found in Trash.', 'gpea_theme' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Description.', 'gpea_theme' ),
			'public'              => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'rewrite'             => array(
				'slug' => 'tip',
			),
			'capability_type'     => 'post',
			'taxonomies'          => array( 'category', 'post_tag' ),
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-tablet',
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
		);

		register_post_type( 'tips', $args );

	}

	/**
	 * Register a team post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type_team() {

		$labels = array(
			'name'               => _x( 'Team Members', 'post type general name', 'gpea_theme' ),
			'singular_name'      => _x( 'Team Member', 'post type singular name', 'gpea_theme' ),
			'menu_name'          => _x( 'Team Members', 'admin menu', 'gpea_theme' ),
			'name_admin_bar'     => _x( 'Team Member', 'add new on admin bar', 'gpea_theme' ),
			'add_new'            => _x( 'Add New', 'team member', 'gpea_theme' ),
			'add_new_item'       => __( 'Add New Team Member', 'gpea_theme' ),
			'new_item'           => __( 'New Team Member', 'gpea_theme' ),
			'edit_item'          => __( 'Edit Team Member', 'gpea_theme' ),
			'view_item'          => __( 'View Team Member', 'gpea_theme' ),
			'all_items'          => __( 'All Team Members', 'gpea_theme' ),
			'search_items'       => __( 'Search Team Members', 'gpea_theme' ),
			'parent_item_colon'  => __( 'Parent Team Members:', 'gpea_theme' ),
			'not_found'          => __( 'No Team Members found.', 'gpea_theme' ),
			'not_found_in_trash' => __( 'No Team Members found in Trash.', 'gpea_theme' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Description.', 'gpea_theme' ),
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'rewrite'             => array(
				'slug' => 'people',
			),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-admin-users',
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
		);

		register_post_type( 'team', $args );

	}

	/**
	 * Register a user_story post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type_stories() {

		$labels = array(
			'name'               => _x( 'User Stories', 'post type general name', 'gpea_theme' ),
			'singular_name'      => _x( 'User Story', 'post type singular name', 'gpea_theme' ),
			'menu_name'          => _x( 'User Stories', 'admin menu', 'gpea_theme' ),
			'name_admin_bar'     => _x( 'User Story', 'add new on admin bar', 'gpea_theme' ),
			'add_new'            => _x( 'Add New', 'tip', 'gpea_theme' ),
			'add_new_item'       => __( 'Add New User Story', 'gpea_theme' ),
			'new_item'           => __( 'New User Story', 'gpea_theme' ),
			'edit_item'          => __( 'Edit User Story', 'gpea_theme' ),
			'view_item'          => __( 'View User Story', 'gpea_theme' ),
			'all_items'          => __( 'All User Stories', 'gpea_theme' ),
			'search_items'       => __( 'Search User Stories', 'gpea_theme' ),
			'parent_item_colon'  => __( 'Parent User Stories:', 'gpea_theme' ),
			'not_found'          => __( 'No User Stories found.', 'gpea_theme' ),
			'not_found_in_trash' => __( 'No User Stories found in Trash.', 'gpea_theme' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Description.', 'gpea_theme' ),
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'rewrite'             => array(
				'slug' => 'user_story',
			),
			'capability_type'     => 'post',
			'taxonomies'          => array( 'category', 'post_tag' ),
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-admin-comments',
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
		);

		register_post_type( 'user_story', $args );

	}
}
