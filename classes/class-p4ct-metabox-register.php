<?php
/**
 * P4 Metabox Register Class
 *
 * @package P4CT
 */

/**
 * Class P4CT_Metabox_Register
 */
class P4CT_Metabox_Register {

	/**
	 * Meta box prefix.
	 *
	 * @var string $prefix
	 */
	private $prefix = 'p4_';

	/**
	 * P4CT_Metabox_Register constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Class hooks.
	 */
	private function hooks() {
		add_action( 'cmb2_admin_init', [ $this, 'register_p4_meta_box' ] );
		add_filter( 'cmb2_show_on', [ $this, 'be_taxonomy_show_on_filter' ], 10, 2 );
		add_filter( 'cmb2_render_supportus_page_dropdown', [ $this, 'gpea_render_supportus_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_mainissue_page_dropdown', [ $this, 'gpea_render_mainissue_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_ugc_page_dropdown', [ $this, 'gpea_render_ugc_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_latest_page_dropdown', [ $this, 'gpea_render_latest_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_make_change_page_dropdown', [ $this, 'gpea_render_make_change_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_press_media_page_dropdown', [ $this, 'gpea_render_press_media_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_preferences_page_dropdown', [ $this, 'gpea_render_preferences_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_commitment_projects_page_dropdown', [ $this, 'gpea_render_commitment_projects_page_dropdown' ], 10, 2 );
		add_filter( 'cmb2_render_commitment_issues_page_dropdown', [ $this, 'gpea_render_commitment_issues_page_dropdown' ], 10, 2 );
	}

	/**
	 * Register P4 meta box(es).
	 */
	public function register_p4_meta_box() {
		$this->register_sidebar_metabox();
		$this->register_project_metabox();
		$this->register_petition_metabox();
		$this->register_tip_metabox();
		$this->register_user_story_metabox();
		$this->register_team_metabox();
		$this->register_category_metabox();
		$this->register_post_metabox();
		$this->register_page_metabox();
		$this->register_main_options_metabox();
		$this->register_donation_button_options_metabox();
		$this->register_donation_block_options_metabox();
	}

	/**
	 * Registers sidebar meta box(es).
	 */
	public function register_sidebar_metabox() {

		$cmb_sidebar = new_cmb2_box(
			[
				'id'           => $this->prefix . 'metabox_sidebar',
				'title'        => __( 'Post extra attributes', 'gpea_theme_backend' ),
				'object_types' => [ 'post' ], // Post type.
				// 'show_on' => array(
				// 'key' => 'taxonomy',
				// 'value' => array(
				// 'p4-page-type' => array( 'update' )
				// ),
				// ),
				'context'       => 'side',
				'priority'      => 'low',
			]
		);

		$cmb_sidebar->add_field(
			array(
				'name'             => esc_html__( 'Project related', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Select a project connected to this post (optional)', 'gpea_theme_backend' ),
				'id'               => $this->prefix . 'select_project_related',
				'type'             => 'select',
				'show_option_none' => true,
				'options'          => $this->generate_post_select( 'page', null, 'page-templates/project.php' ),
			)
		);

	}

	/**
	 * Registers project meta box(es).
	 */
	public function register_project_metabox() {

		$cmb_project = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-project-box',
				'title'        => 'Information about current project',
				'object_types' => array( 'page' ), // post type
				'show_on'      => array(
					'key'          => 'page-template',
					'value'        => 'page-templates/project.php',
				),
				'context'      => 'normal', // 'normal', 'advanced', or 'side'
				'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
				'show_names'   => true, // Show field names on the left
			)
		);

		$cmb_project->add_field(
			array(
				'name'             => esc_html__( 'Start date', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'The date the project started (textual)', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_project_start_date',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_project->add_field(
			array(
				'name'             => esc_html__( 'Zone interested', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Country, city or place involved by the project', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_project_localization',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_project->add_field(
			array(
				'name'             => esc_html__( 'Project percentage', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Percentage of completition of the project', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_project_percentage',
				'type'             => 'text',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
				),
			 // 'sanitization_cb' => 'intval',
			 // 'escape_cb'       => 'intval',
			)
		);

	}

	/**
	 * Registers Petition meta box(es).
	 */
	public function register_petition_metabox() {

		$cmb_petition = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-donation-box',
				'title'        => 'Information about this petition',
				'object_types' => array( 'page' ), // post type
			  'show_on'      => array(
				  'key'          => 'page-template',
				  'value'        => 'page-templates/petition.php',
			  ),
			  'context'      => 'normal', // 'normal', 'advanced', or 'side'
			  'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			  'show_names'   => true, // Show field names on the left
			)
		);

		$cmb_petition->add_field(
			array(
				'name'    => esc_html__( '"Id" code of Engaging connected petition', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'This will be used to retrieve current signature number', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_petition_engaging_pageid',
				'type'             => 'text',
			)
		);

		$cmb_petition->add_field(
			array(
				'name'    => esc_html__( 'Number of signatures (manually specified!)', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'In case you specify this value the Engaging connection will be ignored and this value will be shown', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_petition_current_signatures',
				'type'             => 'text',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
				),
			)
		);

		$cmb_petition->add_field(
			array(
				'name'             => esc_html__( 'Petition goal number', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Number of signatures to be reached by this petition', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_petition_engaging_target',
				'type'             => 'text',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
				),
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_petition->add_field(
			array(
				'name'    => esc_html__( 'External link to redirect petition (optional)', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'If set, card on the website will link to this external link instead of internal page', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_petition_external_link',
				'type'             => 'text',
			)
		);

		$cmb_petition->add_field(
			array(
				'name'    => esc_html__( 'CTA for external link(optional)', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'default will take the translation of "sign now', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_cta_external_link',
				'type'             => 'text',
			)
		);

		$cmb_petition->add_field(
			array(
				'name'    => esc_html__( 'Link: Extra link for cta in thanks box (optional)', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'if added and no thanks page is set, this will be shown in the thanks box after signature', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_cta_thanks_link',
				'type'             => 'text',
			)
		);

		$cmb_petition->add_field(
			array(
				'name'    => esc_html__( 'Label: Extra link for cta in thanks box (optional)', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'This is the label!', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_cta_thanks_label',
				'type'             => 'text',
			)
		);

	}

	/**
	 * Registers tip meta box(es).
	 */
	public function register_tip_metabox() {

		$cmb_tip = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-tip-box',
				'title'        => 'Tip card',
				'object_types' => array( 'tips' ), // post type
				'context'      => 'normal', // 'normal', 'advanced', or 'side'
				'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
				'show_names'   => true, // Show field names on the left
				// 'show_on' => array(
				// 'key' => 'taxonomy',
				// 'value' => array(
				// 'p4_post_attribute' => array( 'tip' ),
				// ),
				// ),
			)
		);

		$cmb_tip->add_field(
			array(
				'name'             => esc_html__( 'Ask users to engage?', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'If checked, an action for users will be encouraged', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_tip_engage',
				'type'             => 'checkbox',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_tip->add_field(
			array(
				'name'             => esc_html__( 'Frequency pledge', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Will be displayed in the tip card, in the top', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_tip_frequency',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_tip->add_field(
			array(
				'name'             => esc_html__( 'Tip icon', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Icon/image shown in the card', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_tip_icon',
				'type'             => 'file',
				// Optional.
				'options'          => [
					'url' => false,
				],
				'text'             => [
					'add_upload_file_text' => __( 'Add Tip Image', 'gpea_theme_backend' ),
				],
				'query_args'       => [
					'type' => 'image',
				],
				'preview_size' => 'small',
			)
		);

		$cmb_tip->add_field(
			array(
				'name'             => esc_html__( 'Number of commitments', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Number of users that clicked on this tip (readonly)', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_tip_commitments',
				'type'             => 'text',
				'save_field'  => false, // Otherwise CMB2 will end up removing the value.
				'attributes'  => array(
					'readonly' => 'readonly',
				),
			)
		);

	}

	/**
	 * Registers user story meta box.
	 */
	public function register_user_story_metabox() {

		$cmb_user_story = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-user-story-box',
				'title'        => 'User story extra information',
				'object_types' => array( 'user_story' ), // post type
				'context'      => 'normal', // 'normal', 'advanced', or 'side'
				'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
				'show_names'   => true, // Show field names on the left
				// 'show_on' => array(
				// 'key' => 'taxonomy',
				// 'value' => array(
				// 'p4_post_attribute' => array( 'tip' ),
				// ),
				// ),
			)
		);

		// add p4_author_override already used for standard posts
		$cmb_user_story->add_field(
			array(
				'name'             => esc_html__( 'Author of the story', 'gpea_theme_backend' ),
				'id'               => 'p4_author_override',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_user_story->add_field(
			array(
				'name'             => esc_html__( 'Email address of the author', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'For internal scope, will not be published on the site', 'gpea_theme_backend' ),
				'id'               => 'p4_author_email_address',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

	}

	/**
	 * Registers team meta box(es).
	 */
	public function register_team_metabox() {

		$cmb_team = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-team-box',
				'title'        => 'Team extra info',
				'object_types' => array( 'team' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$cmb_team->add_field(
			array(
				'name'             => esc_html__( 'Short bio', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_short_bio',
				'type'             => 'textarea',
			)
		);

		$cmb_team->add_field(
			array(
				'name'             => esc_html__( 'Role', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Role in the staff', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_team_role',
				'type'             => 'text',
			)
		);

		$cmb_team->add_field(
			array(
				'name'             => esc_html__( 'Code', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'used for example for street fundraisers', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_team_code',
				'type'             => 'text',
			)
		);

	}

	/**
	 * Registers category meta box(es).
	 */
	public function register_category_metabox() {

		$cmb_category = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-category-box',
				'title'        => 'Main issue extra info',
				'object_types' => array( 'term' ),
				'taxonomies'       => array( 'category' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$cmb_category->add_field(
			array(
				'name'             => esc_html__( 'Main issue page to redirect to:', 'gpea_theme_backend' ),
				'id'               => 'gpea_mainissue_page',
				'type'             => 'mainissue_page_dropdown',
			)
		);

	}


	/**
	 * Registers post meta box(es).
	 */
	public function register_post_metabox() {

		/**
		 * Information about current post
		 */

		$cmb_post = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-post-box',
				'title'        => 'Information about current post',
				'object_types' => array( 'post' ),
				'context'      => 'normal', // 'normal', 'advanced', or 'side'
				'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
				'show_names'   => true, // Show field names on the left
			)
		);

		$cmb_post->add_field(
			array(
				'name'             => esc_html__( 'Subtitle', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'will be shown below the title', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_post_subtitle',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_post->add_field(
			array(
				'name'             => esc_html__( 'Reading time', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Specify the time extimated to read the article (i.e. 4 min)', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_post_reading_time',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_post->add_field(
			array(
				'name'             => esc_html__( 'Meta Keywords', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Google ignores meta keywords in ranking but you can still keep the relevant keywords here as a reference.', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_post_meta_desc',
				'type'             => 'text',
			)
		);

		$cmb_post->add_field(
			array(
				'name'             => esc_html__( 'Show the "Article donation laundher"', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Show the launcher to support page, below the main content?', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_show_article_donation_launcher',
				'type'             => 'checkbox',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		/**
		 * Donation Buttons
		 */

		$cmb_post_donation_button = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-post-donation-button-box',
				'title'        => 'Donation Buttons',
				'object_types' => array( 'post' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Donation Button on Top', 'gpea_theme_backend' ),
				'desc'             => '
					<ol>
						<li>Location: above the main content, under the quoteblock (quote with theme color background).</li>
						<li>Leave the link or label field empty to use the default value.</li>
						<li>Go to the setting &quot;' . __( 'Settings' ) . ' &gt; Post Donation Buttons&quot; to setup default values.</li>
					<ol>', 'gpea_theme_backend',
				'id'               => 'p4-gpea_article_top_donation_button',
				'type'             => 'title',
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Show the Button', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_show_article_top_donation_button',
				'type'             => 'select',
				'options_cb'       => [ $this, 'populate_donation_button_options' ],
				'default_cb'       => [ $this, 'set_donation_button_default' ],
			)
		);


		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Button Link', 'gpea_theme_backend' ),
				'id'               => 'p4-article_top_donation_button_link',
				'type'             => 'text',
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Button Label', 'gpea_theme_backend' ),
				'id'               => 'p4-article_top_donation_button_text',
				'type'             => 'text_medium',
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Donation Button at Bottom', 'gpea_theme_backend' ),
				'desc'             => '
					<ol>
						<li>Location: below the main content, before the further reading section.</li>
						<li>Leave the link or label field empty to use the default value.</li>
						<li>Go to the setting &quot;' . __( 'Settings' ) . ' &gt; Post Donation Buttons&quot; to setup default values.</li>
					<ol>', 'gpea_theme_backend',
				'id'               => 'p4-gpea_article_bottom_donation_button',
				'type'             => 'title',
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Show the Button', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_show_article_bottom_donation_button',
				'type'             => 'select',
				'options_cb'       => [ $this, 'populate_donation_button_options' ],
				'default_cb'       => [ $this, 'set_donation_button_default' ],
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Button Link', 'gpea_theme_backend' ),
				'id'               => 'p4-article_bottom_donation_button_link',
				'type'             => 'text',
			)
		);

		$cmb_post_donation_button->add_field(
			array(
				'name'             => esc_html__( 'Button Label', 'gpea_theme_backend' ),
				'id'               => 'p4-article_bottom_donation_button_text',
				'type'             => 'text_medium',
			)
		);

	}

	/**
	 * Registers post meta box(es).
	 */
	public function register_page_metabox() {

		$cmb_page = new_cmb2_box(
			array(
				'id'           => 'p4-gpea-page-box',
				'title'        => 'Extra field for the current page',
				'object_types' => array( 'page' ),
				'context'      => 'normal', // 'normal', 'advanced', or 'side'
			  'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			  'show_names'   => true, // Show field names on the left
			)
		);

		$cmb_page->add_field(
			array(
				'name'             => esc_html__( 'Extra content section', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'You can use this extra field to add rich text content below the main section', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_page_extra_content',
				'type'             => 'wysiwyg',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

		$cmb_page->add_field(
			array(
				'name'             => esc_html__( 'Special html class for the page', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'WARNING: this class is used to apply special style and behaviour, edit with caution...', 'gpea_theme_backend' ),
				'id'               => 'p4-gpea_page_special_class',
				'type'             => 'text',
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
			)
		);

	}

	/**
	 * Registers main option meta box(es).
	 */
	public function register_main_options_metabox() {

		$cmb_options = new_cmb2_box(
			array(
				'id'           => 'gpea_main_options_page',
				'title'        => esc_html__( 'GPEA Options', 'gpea_theme_backend' ),
				'object_types' => array( 'options-page' ),

				/*
				 * The following parameters are specific to the options-page box
				 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
				 */

				'option_key'      => 'gpea_options', // The option key and admin menu page slug.
				// 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
				// 'menu_title'      => esc_html__( 'Options', 'cmb2' ), // Falls back to 'title' (above).
				'parent_slug'     => 'options-general.php', // Make options page a submenu item of the themes menu.
				// 'capability'      => 'manage_options', // Cap required to view options-page.
				// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
				// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
				// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
				// 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
				// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
				// 'message_cb'      => 'yourprefix_options_page_message_callback',
			)
		);

		/**
		 * Options fields ids only need to be unique within these boxes.
		 * Prefix is not needed.
		 */

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Base of site domain', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Do not touch unless you know what you are doing', 'gpea_theme_backend' ),
				'id'      => 'gpea_site_base',
				'type'    => 'text',
				'attributes'  => array(
					'readonly' => 'readonly',
				),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Background image for "Values" section', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Specify the image to be used as background', 'gpea_theme_backend' ),
				'id'      => 'gpea_values_section_bg_image',
				'type'    => 'file',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Country title, to show next to the logo', 'gpea_theme_backend' ),
				'id'      => 'gpea_current_country',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'css file with fonts specifications (optional)', 'gpea_theme_backend' ),
				'id'      => 'gpea_css_fonts',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Description text for generic footer', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Description text for generic footer', 'gpea_theme_backend' ),
				'id'      => 'gpea_description_generic_footer_text',
				'type'    => 'textarea',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Footer additional link', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Will be placed in the right corner, same row of other green links in footer', 'gpea_theme_backend' ),
				'id'      => 'gpea_footer_extra_link',
				'type'    => 'text',
			)
		);
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Footer additional link label', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Label of above link, if present', 'gpea_theme_backend' ),
				'id'      => 'gpea_footer_extra_link_label',
				'type'    => 'text',
			)
		);

		/* support us */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the "support" landing page', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Support page with all information and links', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_supportus_link',
				'type'    => 'supportus_page_dropdown',
			)
		);

		/* support us external */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Specify an external "support" page', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'If specified, the button in the top right of the screen will bring out of the site!', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_supportus_link_external',
				'type'    => 'text',
			)
		);

		/* ugc */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the "user generated content" landing page', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Page with form to submit new stories from users', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_ugc_link',
				'type'    => 'ugc_page_dropdown',
			)
		);

		/* latest from the Earth */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the "latest from the Earth" page', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Page with latest news', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_latest_link',
				'type'    => 'latest_page_dropdown',
			)
		);

		/* Make a change */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the "Make a change" page', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_make_change',
				'type'    => 'make_change_page_dropdown',
			)
		);

		/* Press & Media link */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the "Press&Media" page', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_press_media',
				'type'    => 'press_media_page_dropdown',
			)
		);

		/* My preferences */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the "User preferences" page', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_preferences',
				'type'    => 'preferences_page_dropdown',
			)
		);

		/* Our commitment: projects */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the page with full list of projects', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_commitment_projects',
				'type'    => 'commitment_projects_page_dropdown',
			)
		);

		/* Our commitment: issues */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Select the page with full list of issues', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_commitment_issues',
				'type'    => 'commitment_issues_page_dropdown',
			)
		);

		/* Engaging default newsletter recipient */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( '"Id" code of your Engaging default subscription page', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'When user will select one or more topics to follow, he will be able to subscribe to that page', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_en_subscription_page',
				'type'    => 'text',
			)
		);

		/* default Engaging planet4-form associated to the tag cloud */

		$cmb_options->add_field(
			array(
				'name'             => esc_html__( 'Select the form for tag cloud subsciption', 'gpea_theme_backend' ),
				'desc'             => esc_html__( 'Form will be shown below the tag cloud to subscribe to Engaging Newsletter', 'gpea_theme_backend' ),
				'id'               => 'gpea_tag_cloud_newsletter_form',
				'type'             => 'select',
				'show_option_none' => true,
				'options'          => $this->generate_post_select( 'p4en_form', null, null ),
			)
		);

		/* Other Engaging newsletter default module */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Thank you main text, for "topic/issue" newsletter subscribe', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Show to user after he sign up also to newsletter', 'gpea_theme_backend' ),
				'id'      => 'gpea_subscription_page_thankyou_title',
				'type'    => 'text',
			)
		);
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Thank you message: second line', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Show to user after he sign up also to newsletter', 'gpea_theme_backend' ),
				'id'      => 'gpea_subscription_page_thankyou_subtitle',
				'type'    => 'text',
			)
		);

		/* donation default link */
		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Default external link for donation ', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Parameters and fields of donation box will be sent to this link', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_donation_link',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Engaging question for recurring donation', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'question name', 'gpea_theme_backend' ),
				'id'      => 'gpea_donation_recurring_question',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Donation: minimum amount for one-off', 'gpea_theme_backend' ),
				'id'      => 'gpea_donation_minimum-oneoff',
				'type' => 'text',
				'attributes' => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Donation: minimum amount for regular', 'gpea_theme_backend' ),
				'id'      => 'gpea_donation_minimum-regular',
				'type' => 'text',
				'attributes' => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Donation: suggested amount for one-off', 'gpea_theme_backend' ),
				'id'      => 'gpea_donation_suggested-oneoff',
				'type' => 'text',
				'attributes' => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Donation: suggested amount for regular', 'gpea_theme_backend' ),
				'id'      => 'gpea_donation_suggested-regular',
				'type' => 'text',
				'attributes' => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Donation: error message in case of lower to minimum', 'gpea_theme_backend' ),
				'id'      => 'gpea_donation_minimum-error-message',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Donation: default currency', 'gpea_theme_backend' ),
				'id'      => 'gpea_default_donation_currency',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Sharing options', 'gpea_theme_backend' ),
				'id'      => 'gpea_sharing_options',
				'type'    => 'multicheck',
				'options' => array(
					'email'     => 'Email',
					'facebook'  => 'Facebook',
					'twitter'   => 'Twitter',
					'whatsapp'  => 'Whatsapp',
					'line'      => 'Line',
					'kakaotalk' => 'KakaoTalk',
					'wechat'    => 'WeChat',
					'web_api'   => 'Web API Navigator',
				),
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'KakaoTalk app id', 'gpea_theme_backend' ),
				'id'      => 'gpea_kakao_app_id',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Facebook app id', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Used in subscribe form', 'gpea_theme_backend' ),
				'id'      => 'gpea_facebook_app_id',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Support email recipient: general', 'gpea_theme_backend' ),
				'id'      => 'gpea_support_recipient_email_general',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Support email recipient: special donations', 'gpea_theme_backend' ),
				'id'      => 'gpea_support_recipient_email_special',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Email recipient for ugc submission', 'gpea_theme_backend' ),
				'id'      => 'gpea_ugc_recipient_email',
				'type'    => 'text',
			)
		);

		// post donation luncher

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Title for donation launcher below post', 'gpea_theme_backend' ),
				'id'      => 'gpea_post_donation_launcher_title',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Text for donation launcher below post', 'gpea_theme_backend' ),
				'id'      => 'gpea_post_donation_launcher_text',
				'type'    => 'textarea',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Link for donation launcher below post', 'gpea_theme_backend' ),
				'id'      => 'gpea_post_donation_launcher_link',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Button label for donation launcher', 'gpea_theme_backend' ),
				'id'      => 'gpea_post_donation_launcher_label',
				'type'    => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Image for donation launcher below post', 'gpea_theme_backend' ),
				'id'      => 'gpea_post_donation_launcher_image',
				'type'    => 'file',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Align the image to vertical center for donation launcher', 'gpea_theme_backend' ),
				'desc'    => esc_html__( 'Align the image center vertically.', 'gpea_theme_backend' ),
				'id'      => 'gpea_post_donation_launcher_image_align_center',
				'type'    => 'checkbox',
			)
		);

	}

	/**
	 * Registers donation buttons option meta box(es).
	 */
	public function register_donation_button_options_metabox() {

		$cmb_options = new_cmb2_box(
			array(
				'title'           => esc_html__( 'Post Donation Buttons', 'gpea_theme_backend' ),
				'menu_title'      => esc_html__( 'Donation Buttons', 'gpea_theme_backend' ),
				'id'              => 'gpea_donation_button_options_page',
				'object_types'    => array( 'options-page' ),
				'option_key'      => 'gpea_donation_button_options',
				'parent_slug'     => 'options-general.php',
			)
		);

		$this->add_donation_option_fields( $cmb_options, 'gpea_donation_button_' );

	}

	/**
	 * Registers donation blocks option meta box(es).
	 */
	public function register_donation_block_options_metabox() {

		$cmb_options = new_cmb2_box(
			array(
				'title'           => esc_html__( 'Post/Page Donation Blocks', 'gpea_theme_backend' ),
				'menu_title'      => esc_html__( 'Donation Blocks', 'gpea_theme_backend' ),
				'id'              => 'gpea_donation_block_options_page',
				'object_types'    => array( 'options-page' ),
				'option_key'      => 'gpea_donation_block_options',
				'parent_slug'     => 'options-general.php',
			)
		);

		$this->add_donation_option_fields( $cmb_options, 'gpea_donation_block_', true );

	}

	/**
	 * Populate an associative array with donation buttons' display mode.
	 *
	 * @return array
	 */
	public function add_donation_option_fields( $cmb_options = null, $id_prefix = '', $is_block = false ) {

		$main_issues_category_id = isset( $planet4_options['issues_parent_category'] ) ? $planet4_options['issues_parent_category'] : false;
		if ( ! $main_issues_category_id ) {
			$main_issues_category = get_term_by( 'slug', 'issues', 'category' );
			if ( $main_issues_category ) {
				$main_issues_category_id = $main_issues_category->term_id;
			}
		}

		$main_issues = array();
		if( $main_issues_category_id ) {
			$main_issues = get_terms(
				array(
					'taxonomy' => 'category',
					'parent' => $main_issues_category_id,
				)
			);
		}

		$main_issues = array_column( $main_issues, 'name', 'slug' );

		if( $is_block ) {
			$cmb_options->add_field(
				array(
					'name'             => '',
					'desc'             => '
						<ol>
							<li>Please set the default values for the donation blocks in the post/page.</li>
							<li>Location: it\'s better to set around the middle area of the post.</li>
							<li>You need to set the &quot;All-site Default&quot; at least. Every post not under ' . count( $main_issues ) . ' themes (' . implode( ', ', $main_issues ) . '), or you didn\'t set the default value for 6 themes and leave the fields empty will go to the &quot;All-site Default&quot; once writers insert the donation block.</li>
						<ol>', 'gpea_theme_backend',
					'id'               => $id_prefix . '_hint',
					'type'             => 'title',
				)
			);
		}
		else {
			$cmb_options->add_field(
				array(
					'name'             => '',
					'desc'             => '
						<ol>
							<li>Please set the default values for the donation buttons in the post.</li>
							<li>Buttons\' locations: one is above the main content and under the blockquote (quote with theme color background); the second one is below the main content and before the further reading section.</li>
							<li>You need to set the &quot;All-site Default&quot; at least. Every post not under ' . count( $main_issues ) . ' themes (' . implode( ', ', $main_issues ) . '), or you didn\'t set the default value for 6 themes and leave the link/label empty will go to the &quot;All-site Default&quot; once writers &quot;show the button.&quot;</li>
						<ol>', 'gpea_theme_backend',
					'id'               => $id_prefix . '_hint',
					'type'             => 'title',
				)
			);
		}

		$main_issues = array(
			'default'  => 'All-site Default',
		) + $main_issues;

		foreach( $main_issues as $issue_key => $issue_title ) {

			$cmb_options->add_field(
				array(
					'name'             => esc_html__( $issue_title, 'gpea_theme_backend' ),
					'desc'             => $issue_key == 'default' ? '' : esc_html__( 'Leave empty to use the same setting in &quot;All-site Default&quot;', 'gpea_theme_backend' ),
					'id'               => $id_prefix . $issue_key,
					'type'             => 'title',
				)
			);

			if( $is_block ) {

				$cmb_options->add_field(
					array(
						'name'             => esc_html__( 'Title', 'gpea_theme_backend' ),
						'id'               => $id_prefix . $issue_key . '_title',
						'type'             => 'text',
					)
				);

				$cmb_options->add_field(
					array(
						'name'             => esc_html__( 'Description', 'gpea_theme_backend' ),
						'desc'             => esc_html__( 'For a better user experience, it\'s better to leave empty or not over 20 characters.', 'gpea_theme_backend' ),
						'id'               => $id_prefix . $issue_key . '_desc',
						'type'             => 'textarea',
					)
				);

			}

			$cmb_options->add_field(
				array(
					'name'             => esc_html__( 'Button Link', 'gpea_theme_backend' ),
					'id'               => $id_prefix . $issue_key . '_button_link',
					'type'             => 'text',
				)
			);

			$cmb_options->add_field(
				array(
					'name'             => esc_html__( 'Button Label', 'gpea_theme_backend' ),
					'id'               => $id_prefix . $issue_key . '_button_text',
					'type'             => 'text_medium',
				)
			);

			if( $is_block ) {

				$cmb_options->add_field(
					array(
						'name'    => esc_html__( 'Background Image', 'gpea_theme_backend' ),
						'id'      => $id_prefix . $issue_key . '_bg_img',
						'type'    => 'file',
					)
				);

			}

		}
		
	}

	/**
	 * Taxonomy show_on filter
	 *
	 * @author Bill Erickson
	 * @link https://github.com/CMB2/CMB2/wiki/Adding-your-own-show_on-filters
	 *
	 * @param bool  $display
	 * @param array $metabox
	 * @return bool display metabox
	 */
	public function be_taxonomy_show_on_filter( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) {
			return $display;
		}

		if ( 'taxonomy' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		$post_id = 0;

		// If we're showing it based on ID, get the current ID.
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = $_POST['post_ID'];
		}

		if ( ! $post_id ) {
			return $display;
		}

		foreach ( (array) $meta_box['show_on']['value'] as $taxonomy => $slugs ) {
			if ( ! is_array( $slugs ) ) {
				$slugs = array( $slugs );
			}

			$display = false;
			$terms = wp_get_object_terms( $post_id, $taxonomy );
			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $slugs, true ) ) {
					$display = true;
					break;
				}
			}

			if ( $display ) {
				break;
			}
		}

		return $display;
	}

	/**
	 * Fetches posts to use with cmb2
	 * TODO optimize
	 *
	 * @param string $post_type
	 * @param string $post_attribute
	 * @param string $page_template
	 * @return bool  display metabox
	 */
	private function generate_post_select( $post_type, $post_attribute, $page_template ) {

		$options = array(
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'posts_per_page'   => -1,
		);

		if ( $post_attribute ) {
			$options['tax_query'] = array(
				array(
					'taxonomy' => 'p4_post_attribute',
					'field'    => 'slug',
					'terms'    => $post_attribute,
				),
			);
		}

		if ( $page_template ) {
			$options['meta_key'] = '_wp_page_template';
			$options['meta_value']  = $page_template;
		}

		$posts = get_posts( $options );

		$output = array();
		foreach ( $posts as $post ) {
			$postid = $post->ID;
			$output[ $postid ] = $post->post_title;
		}
		return $output;
	}

	/**
	 * Populate an associative array with donation buttons' display mode.
	 *
	 * @return array
	 */
	public function populate_donation_button_options( $field ) {
		$options = array(
			'1' => 'Yes',
			'0' => 'No',
		);
		return $options;
	}

	/**
	 * Set donation buttons' default display mode.
	 *
	 * @return string
	 */
	public function set_donation_button_default( $field_args, $field ) {
		return get_current_screen()->action == 'add' ? '1' : '0';
	}

	/**
	 * Render support us page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_supportus_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_supportus_link',
			]
		);
	}

	/**
	 * Render ugc page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_ugc_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_ugc_link',
			]
		);
	}

	/**
	 * Render make a change page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_make_change_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_make_change',
			]
		);
	}

	/**
	 * Render Press media page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_press_media_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_press_media',
			]
		);
	}

	/**
	 * Render Preferences page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_preferences_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_preferences',
			]
		);
	}

	/**
	 * Render commitment projects page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_commitment_projects_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_commitment_projects',
			]
		);
	}

	/**
	 * Render commitment issues page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_commitment_issues_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_commitment_issues',
			]
		);
	}


	/**
	 * Render latest from earth page dropdown.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_latest_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_default_latest_link',
			]
		);
	}

	/**
	 * Render pages for main issue page dropdown in category.
	 *
	 * @param array  $field_args Field arguments.
	 * @param string $value Value.
	 */
	public function gpea_render_mainissue_page_dropdown( $field_args, $value ) {
		wp_dropdown_pages(
			[
				'show_option_none' => __( 'Select Page', 'gpea_theme_backend' ),
				'hide_empty'       => 0,
				'hierarchical'     => true,
				'selected'         => $value,
				'name'             => 'gpea_mainissue_page',
			]
		);
	}

}
