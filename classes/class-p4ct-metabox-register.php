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
	 * ID of the Metabox
	 */
	public const METABOX_ID = 'gpea_theme_backend';

	/**
	 * Option page slug
	 *
	 * @var string
	 */
	private $slug = 'gpea_main_options_page';

	/**
	 * Options Page title
	 *
	 * @var string
	 */
	protected $title = 'GPEA';

	/**
	 * Meta box prefix.
	 *
	 * @var string $prefix
	 */
	private $prefix = 'p4_';

	/**
	 * Subpages
	 *
	 * @var array
	 * Includes arrays with the title and fields of each subpage
	 */
	protected $subpages = [];

	/**
	 * Allowed subscription buttons or not
	 *
	 * @var bool
	 */
	protected $allowed_subscription_buttons = FALSE;

	/**
	 * P4CT_Metabox_Register constructor.
	 */
	public function __construct() {
		$this->hooks();
		$this->allowed_subscription_buttons = in_array( get_locale(), [
			'zh_TW',
		] );
	}

	/**
	 * Class hooks.
	 */
	private function hooks() {

		add_action( 'cmb2_admin_init', [ $this, 'register_p4_meta_box' ] );
		add_action( 'admin_menu', [ $this, 'add_options_pages' ] );
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

		add_filter( 'cmb2_override_meta_save', [ $this, 'gpea_override_meta_value' ], 10, 4 );

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
		$this->register_notification_options_metabox();

		/**
		 * Donation/Subscription Buttons/Blocks
		 */
		$this->register_donation_button_options_metabox();
		$this->register_donation_block_options_metabox();
		if( $this->allowed_subscription_buttons ) {
			$this->register_subscription_button_options_metabox();
		}
		$this->register_subscription_block_options_metabox();

		$this->register_testimony_block_options_metabox();
		$this->register_header_nav_options_metabox();
		$this->register_module_map_page();

	}

	/**
	 * Add menu options page.
	 */
	public function add_options_pages() {
		add_menu_page( $this->title, $this->title, 'manage_options', $this->slug, function () {}, 'dashicons-admin-site-alt' );
		foreach ( $this->subpages as $path => $subpage ) {
			add_submenu_page(
				$this->slug,
				$subpage['title'],
				isset($subpage['menu_title']) ? $subpage['menu_title'] : $subpage['title'],
				'manage_options',
				$path,
				function () use ( $path, $subpage ) {
					$this->admin_page_display( $subpage );
				}
			);
		}
	}

	/**
	 * Registers sidebar meta box(es).
	 */
	public function register_sidebar_metabox() {

		$cmb_sidebar = new_cmb2_box([
			'id'           => $this->prefix . 'metabox_sidebar',
			'title'        => __( 'Post extra attributes', self::METABOX_ID ),
			'object_types' => [ 'post' ], // Post type.
			'context'      => 'side',
			'priority'     => 'low',
		]);

		$cmb_sidebar->add_field([
			'name'             => esc_html__( 'Project related', self::METABOX_ID ),
			'desc'             => esc_html__( 'Select a project connected to this post (optional)', self::METABOX_ID ),
			'id'               => $this->prefix . 'select_project_related',
			'type'             => 'select',
			'show_option_none' => TRUE,
			'options'          => $this->generate_post_select( 'page', NULL, 'page-templates/project.php' ),
		]);

	}

	/**
	 * Registers project meta box(es).
	 */
	public function register_project_metabox() {

		$cmb_project = new_cmb2_box([
			'id'           => 'p4-gpea-project-box',
			'title'        => 'Information about current project',
			'object_types' => [ 'page' ], // post type
			'show_on'      => [
				'key' => 'page-template',
				'value' => 'page-templates/project.php',
			],
			'context'      => 'normal', // 'normal', 'advanced', or 'side'
			'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			'show_names'   => TRUE, // Show field names on the left
		]);

		$cmb_project->add_field([
			'name'             => esc_html__( 'Start date', self::METABOX_ID ),
			'desc'             => esc_html__( 'The date the project started (textual)', self::METABOX_ID ),
			'id'               => 'p4-gpea_project_start_date',
			'type'             => 'text',
		]);

		$cmb_project->add_field([
			'name'             => esc_html__( 'Zone interested', self::METABOX_ID ),
			'desc'             => esc_html__( 'Country, city or place involved by the project', self::METABOX_ID ),
			'id'               => 'p4-gpea_project_localization',
			'type'             => 'text',
		]);

		$cmb_project->add_field([
			'name'             => esc_html__( 'Project percentage', self::METABOX_ID ),
			'desc'             => esc_html__( 'Percentage of completition of the project', self::METABOX_ID ),
			'id'               => 'p4-gpea_project_percentage',
			'type'             => 'text',
			'attributes' => [
				'type' => 'number',
				'pattern' => '\d*',
			],
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
		]);

	}

	/**
	 * Registers Petition meta box(es).
	 */
	public function register_petition_metabox() {

		$cmb_petition = new_cmb2_box([
			'id'           => 'p4-gpea-donation-box',
			'title'        => 'Information about this petition',
			'object_types' => [ 'page' ], // post type
			'show_on'      => [
				'key' => 'page-template',
				'value' => 'page-templates/petition.php',
			],
			'context'      => 'normal', // 'normal', 'advanced', or 'side'
			'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			'show_names'   => TRUE, // Show field names on the left
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( '"Id" code of Engaging connected petition', self::METABOX_ID ),
			'desc'             => esc_html__( 'This will be used to retrieve current signature number', self::METABOX_ID ),
			'id'               => 'p4-gpea_petition_engaging_pageid',
			'type'             => 'text',
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( 'Number of signatures (manually specified!)', self::METABOX_ID ),
			'desc'             => esc_html__( 'In case you specify this value the Engaging connection will be ignored and this value will be shown', self::METABOX_ID ),
			'id'               => 'p4-gpea_petition_current_signatures',
			'type'             => 'text',
			'attributes' => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( 'Petition goal number', self::METABOX_ID ),
			'desc'             => esc_html__( 'Number of signatures to be reached by this petition', self::METABOX_ID ),
			'id'               => 'p4-gpea_petition_engaging_target',
			'type'             => 'text',
			'attributes' => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
			// 'sanitization_cb' => 'intval',
			// 'escape_cb'       => 'intval',
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( 'External link to redirect petition (optional)', self::METABOX_ID ),
			'desc'             => esc_html__( 'If set, card on the website will link to this external link instead of internal page', self::METABOX_ID ),
			'id'               => 'p4-gpea_petition_external_link',
			'type'             => 'text',
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( 'CTA for external link(optional)', self::METABOX_ID ),
			'desc'             => esc_html__( 'default will take the translation of "sign now', self::METABOX_ID ),
			'id'               => 'p4-gpea_cta_external_link',
			'type'             => 'text',
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( 'Link: Extra link for cta in thanks box (optional)', self::METABOX_ID ),
			'desc'             => esc_html__( 'if added and no thanks page is set, this will be shown in the thanks box after signature', self::METABOX_ID ),
			'id'               => 'p4-gpea_cta_thanks_link',
			'type'             => 'text',
		]);

		$cmb_petition->add_field([
			'name'             => esc_html__( 'Label: Extra link for cta in thanks box (optional)', self::METABOX_ID ),
			'desc'             => esc_html__( 'This is the label!', self::METABOX_ID ),
			'id'               => 'p4-gpea_cta_thanks_label',
			'type'             => 'text',
		]);

	}

	/**
	 * Registers tip meta box(es).
	 */
	public function register_tip_metabox() {

		$cmb_tip = new_cmb2_box([
			'id'           => 'p4-gpea-tip-box',
			'title'        => 'Tip card',
			'object_types' => [ 'tips' ], // post type
			'context'      => 'normal', // 'normal', 'advanced', or 'side'
			'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			'show_names'   => TRUE, // Show field names on the left
			// 'show_on' => [
			// 	'key' => 'taxonomy',
			// 	'value' => [
			// 		'p4_post_attribute' => [ 'tip' ],
			// 	],
			// ],
		]);

		$cmb_tip->add_field([
			'name'             => esc_html__( 'Ask users to engage?', self::METABOX_ID ),
			'desc'             => esc_html__( 'If checked, an action for users will be encouraged', self::METABOX_ID ),
			'id'               => 'p4-gpea_tip_engage',
			'type'             => 'checkbox',
		]);

		$cmb_tip->add_field([
			'name'             => esc_html__( 'Frequency pledge', self::METABOX_ID ),
			'desc'             => esc_html__( 'Will be displayed in the tip card, in the top', self::METABOX_ID ),
			'id'               => 'p4-gpea_tip_frequency',
			'type'             => 'text',
		]);

		$cmb_tip->add_field([
			'name'             => esc_html__( 'Tip icon', self::METABOX_ID ),
			'desc'             => esc_html__( 'Icon/image shown in the card', self::METABOX_ID ),
			'id'               => 'p4-gpea_tip_icon',
			'type'             => 'file',
			// Optional.
			'options'          => [
				'url' => FALSE,
			],
			'text'             => [
				'add_upload_file_text' => __( 'Add Tip Image', self::METABOX_ID ),
			],
			'query_args'       => [
				'type' => 'image',
			],
			'preview_size'     => 'small',
		]);

		$cmb_tip->add_field([
			'name'             => esc_html__( 'Number of commitments', self::METABOX_ID ),
			'desc'             => esc_html__( 'Number of users that clicked on this tip (readonly)', self::METABOX_ID ),
			'id'               => 'p4-gpea_tip_commitments',
			'type'             => 'text',
			'save_field'       => FALSE, // Otherwise CMB2 will end up removing the value.
			'attributes'       => [
				'readonly' => 'readonly',
			],
		]);

	}

	/**
	 * Registers user story meta box.
	 */
	public function register_user_story_metabox() {

		$cmb_user_story = new_cmb2_box([
			'id'           => 'p4-gpea-user-story-box',
			'title'        => 'User story extra information',
			'object_types' => [ 'user_story' ], // post type
			'context'      => 'normal', // 'normal', 'advanced', or 'side'
			'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			'show_names'   => TRUE, // Show field names on the left
			// 'show_on'      => [
			// 	'key' => 'taxonomy',
			// 	'value' => [
			// 		'p4_post_attribute' => [ 'tip' ],
			// 	],
			// ],
		]);

		// add p4_author_override already used for standard posts
		$cmb_user_story->add_field([
			'name'             => esc_html__( 'Author of the story', self::METABOX_ID ),
			'id'               => 'p4_author_override',
			'type'             => 'text',
		]);

		$cmb_user_story->add_field([
			'name'             => esc_html__( 'Email address of the author', self::METABOX_ID ),
			'desc'             => esc_html__( 'For internal scope, will not be published on the site', self::METABOX_ID ),
			'id'               => 'p4_author_email_address',
			'type'             => 'text',
		]);

	}

	/**
	 * Registers team meta box(es).
	 */
	public function register_team_metabox() {

		$cmb_team = new_cmb2_box([
			'id'           => 'p4-gpea-team-box',
			'title'        => 'Team extra info',
			'object_types' => [ 'team' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => TRUE,
		]);

		$cmb_team->add_field([
			'name'             => esc_html__( 'Short bio', self::METABOX_ID ),
			'id'               => 'p4-gpea_short_bio',
			'type'             => 'textarea',
		]);

		$cmb_team->add_field([
			'name'             => esc_html__( 'Role', self::METABOX_ID ),
			'desc'             => esc_html__( 'Role in the staff', self::METABOX_ID ),
			'id'               => 'p4-gpea_team_role',
			'type'             => 'text',
		]);

		$cmb_team->add_field([
			'name'             => esc_html__( 'Code', self::METABOX_ID ),
			'desc'             => esc_html__( 'used for example for street fundraisers', self::METABOX_ID ),
			'id'               => 'p4-gpea_team_code',
			'type'             => 'text',
		]);

	}

	/**
	 * Registers category meta box(es).
	 */
	public function register_category_metabox() {

		$cmb_category = new_cmb2_box([
			'id'           => 'p4-gpea-category-box',
			'title'        => 'Main issue extra info',
			'object_types' => [ 'term' ],
			'taxonomies'   => [ 'category' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => TRUE,
		]);

		$cmb_category->add_field([
			'name'             => esc_html__( 'Main issue page to redirect to:', self::METABOX_ID ),
			'id'               => 'gpea_mainissue_page',
			'type'             => 'mainissue_page_dropdown',
		]);

	}


	/**
	 * Registers post meta box(es).
	 */
	public function register_post_metabox() {

		/**
		 * Information about current post
		 */

		$cmb_post = new_cmb2_box([
			'id'           => 'p4-gpea-post-box',
			'title'        => 'Information about current post',
			'object_types' => [ 'post' ],
			'context'      => 'normal', // 'normal', 'advanced', or 'side'
			'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			'show_names'   => TRUE, // Show field names on the left
		]);

		$cmb_post->add_field([
			'name'             => esc_html__( 'Subtitle', self::METABOX_ID ),
			'desc'             => esc_html__( 'will be shown below the title', self::METABOX_ID ),
			'id'               => 'p4-gpea_post_subtitle',
			'type'             => 'text',
		]);

		$cmb_post->add_field([
			'name'             => esc_html__( 'Reading time', self::METABOX_ID ),
			'desc'             => esc_html__( 'Specify the time extimated to read the article (i.e. 4 min)', self::METABOX_ID ),
			'id'               => 'p4-gpea_post_reading_time',
			'type'             => 'text',
		]);

		$cmb_post->add_field([
			'name'             => esc_html__( 'Meta Keywords', self::METABOX_ID ),
			'desc'             => esc_html__( 'Google ignores meta keywords in ranking but you can still keep the relevant keywords here as a reference.', self::METABOX_ID ),
			'id'               => 'p4-gpea_post_meta_desc',
			'type'             => 'text',
		]);

		$cmb_post->add_field([
			'name'             => esc_html__( 'Show the "Article donation laundher"', self::METABOX_ID ),
			'desc'             => esc_html__( 'Show the launcher to support page, below the main content?', self::METABOX_ID ),
			'id'               => 'p4-gpea_show_article_donation_launcher',
			'type'             => 'checkbox',
		]);

		/**
		 * Donation/Subscription Buttons
		 */
		$cmb_post_donation_button = $this->register_donation_button_metabox();
		if( $this->allowed_subscription_buttons ) {
			$cmb_post_subscription_button = $this->register_donation_button_metabox(TRUE);
		}

	}

	/**
	 * Registers post donation/subscription button meta box(es).
	 */
	public function register_donation_button_metabox($is_subscription = FALSE) {

		$type_string = $is_subscription ? 'Subscription' : 'Donation';
		$type_key = $is_subscription ? 'subscription' : 'donation';

		$cmb_post_donation_button = new_cmb2_box([
			'id'           => 'p4-gpea-post-' . $type_key . '-button-box',
			'title'        => $type_string . ' Buttons',
			'object_types' => [ 'post' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => TRUE,
		]);

		$cmb_post_donation_button->add_field([
			'name'             => esc_html__( $type_string . ' Button on Top', self::METABOX_ID ),
			'desc'             => __('
				<ol>
					<li>Location: above the main content, under the quoteblock (quote with theme color background).</li>
					<li>Leave the link or label field empty to use the default value.</li>
					<li>Go to the setting &quot;' . __( 'Settings' ) . ' &gt; Post ' . $type_string . ' Buttons&quot; to setup default values.</li>
				</ol>', self::METABOX_ID),
			'id'               => 'p4-gpea_article_top_' . $type_key . '_button',
			'type'             => 'title',
		]);

		$cmb_post_donation_button->add_field([
			'name'             => esc_html__( 'Show the Button', self::METABOX_ID ),
			'id'               => 'p4-gpea_show_article_top_' . $type_key . '_button',
			'type'             => 'select',
			'options_cb'       => [ $this, 'populate_yes_or_no_options' ],
			'default_cb'       => [ $this, 'set_' . $type_key . '_button_default' ],
		]);

		if(!$is_subscription) {

			$cmb_post_donation_button->add_field([
				'name'             => esc_html__( 'Button Link', self::METABOX_ID ),
				'id'               => 'p4-article_top_' . $type_key . '_button_link',
				'type'             => 'text',
			]);

		}

		$cmb_post_donation_button->add_field([
			'name'             => esc_html__( 'Button Label', self::METABOX_ID ),
			'id'               => 'p4-article_top_' . $type_key . '_button_text',
			'type'             => 'text_medium',
		]);

		$cmb_post_donation_button->add_field([
			'name'             => esc_html__( $type_string . ' Button at Bottom', self::METABOX_ID ),
			'desc'             => __('
				<ol>
					<li>Location: below the main content, before the further reading section.</li>
					<li>Leave the link or label field empty to use the default value.</li>
					<li>Go to the setting &quot;' . __( 'Settings' ) . ' &gt; Post ' . $type_string . ' Buttons&quot; to setup default values.</li>
				<ol>', self::METABOX_ID),
			'id'               => 'p4-gpea_article_bottom_' . $type_key . '_button',
			'type'             => 'title',
		]);

		$cmb_post_donation_button->add_field([
			'name'             => esc_html__( 'Show the Button', self::METABOX_ID ),
			'id'               => 'p4-gpea_show_article_bottom_' . $type_key . '_button',
			'type'             => 'select',
			'options_cb'       => [ $this, 'populate_yes_or_no_options' ],
			'default_cb'       => [ $this, 'set_' . $type_key . '_button_default' ],
		]);

		if(!$is_subscription) {

			$cmb_post_donation_button->add_field([
				'name'             => esc_html__( 'Button Link', self::METABOX_ID ),
				'id'               => 'p4-article_bottom_' . $type_key . '_button_link',
				'type'             => 'text',
			]);

		}

		$cmb_post_donation_button->add_field([
			'name'             => esc_html__( 'Button Label', self::METABOX_ID ),
			'id'               => 'p4-article_bottom_' . $type_key . '_button_text',
			'type'             => 'text_medium',
		]);

		return $cmb_post_donation_button;

	}

	/**
	 * Registers post meta box(es).
	 */
	public function register_page_metabox() {

		$cmb_page = new_cmb2_box([
			'id'           => 'p4-gpea-page-box',
			'title'        => 'Extra field for the current page',
			'object_types' => [ 'page' ],
			'context'      => 'normal', // 'normal', 'advanced', or 'side'
			'priority'     => 'high',  // 'high', 'core', 'default' or 'low'
			'show_names'   => TRUE, // Show field names on the left
		]);

		$cmb_page->add_field([
			'name'             => esc_html__( 'Extra content section', self::METABOX_ID ),
			'desc'             => esc_html__( 'You can use this extra field to add rich text content below the main section', self::METABOX_ID ),
			'id'               => 'p4-gpea_page_extra_content',
			'type'             => 'wysiwyg',
		]);

		$cmb_page->add_field([
			'name'             => esc_html__( 'Special html class for the page', self::METABOX_ID ),
			'desc'             => esc_html__( 'WARNING: this class is used to apply special style and behaviour, edit with caution...', self::METABOX_ID ),
			'id'               => 'p4-gpea_page_special_class',
			'type'             => 'text',
		]);

	}

	/**
	 * Registers main option meta box(es).
	 */
	public function register_main_options_metabox() {

		$this->subpages[$this->slug] = [
			'title'        => esc_html__( 'GPEA Options', self::METABOX_ID ),
			'option_key'   => 'gpea_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages[$this->slug]['fields'];

		/**
		 * Options fields ids only need to be unique within these boxes.
		 * Prefix is not needed.
		 */

		$cmb_options[] = [
			'name'             => esc_html__( 'Base of site domain', self::METABOX_ID ),
			'desc'             => esc_html__( 'Do not touch unless you know what you are doing', self::METABOX_ID ),
			'id'               => 'gpea_site_base',
			'type'             => 'text',
			'attributes'       => [
				'readonly' => 'readonly',
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Background image for "Values" section', self::METABOX_ID ),
			'desc'             => esc_html__( 'Specify the image to be used as background', self::METABOX_ID ),
			'id'               => 'gpea_values_section_bg_image',
			'type'             => 'file',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Country title, to show next to the logo', self::METABOX_ID ),
			'id'               => 'gpea_current_country',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'css file with fonts specifications (optional)', self::METABOX_ID ),
			'id'               => 'gpea_css_fonts',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Description text for generic footer', self::METABOX_ID ),
			'desc'             => esc_html__( 'Description text for generic footer', self::METABOX_ID ),
			'id'               => 'gpea_description_generic_footer_text',
			'type'             => 'wysiwyg',
			'options'          => [
				'textarea_rows' => 3,
				'media_buttons' => FALSE,
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Footer additional link', self::METABOX_ID ),
			'desc'             => esc_html__( 'Will be placed in the right corner, same row of other green links in footer', self::METABOX_ID ),
			'id'               => 'gpea_footer_extra_link',
			'type'             => 'text',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Footer additional link label', self::METABOX_ID ),
			'desc'             => esc_html__( 'Label of above link, if present', self::METABOX_ID ),
			'id'               => 'gpea_footer_extra_link_label',
			'type'             => 'text',
		];

		/* support us */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the "support" landing page', self::METABOX_ID ),
			'desc'             => esc_html__( 'Support page with all information and links', self::METABOX_ID ),
			'id'               => 'gpea_default_supportus_link',
			'type'             => 'supportus_page_dropdown',
		];

		/* support us external */
		$cmb_options[] = [
			'name'             => esc_html__( 'Specify an external "support" page', self::METABOX_ID ),
			'desc'             => esc_html__( 'If specified, the button in the top right of the screen will bring out of the site!', self::METABOX_ID ),
			'id'               => 'gpea_default_supportus_link_external',
			'type'             => 'text',
		];

		/* ugc */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the "user generated content" landing page', self::METABOX_ID ),
			'desc'             => esc_html__( 'Page with form to submit new stories from users', self::METABOX_ID ),
			'id'               => 'gpea_default_ugc_link',
			'type'             => 'ugc_page_dropdown',
		];

		/* latest from the Earth */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the "latest from the Earth" page', self::METABOX_ID ),
			'desc'             => esc_html__( 'Page with latest news', self::METABOX_ID ),
			'id'               => 'gpea_default_latest_link',
			'type'             => 'latest_page_dropdown',
		];

		/* Make a change */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the "Make a change" page', self::METABOX_ID ),
			'id'               => 'gpea_default_make_change',
			'type'             => 'make_change_page_dropdown',
		];

		/* Press & Media link */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the "Press&Media" page', self::METABOX_ID ),
			'id'               => 'gpea_default_press_media',
			'type'             => 'press_media_page_dropdown',
		];

		/* My preferences */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the "User preferences" page', self::METABOX_ID ),
			'id'               => 'gpea_default_preferences',
			'type'             => 'preferences_page_dropdown',
		];

		/* Our commitment: projects */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the page with full list of projects', self::METABOX_ID ),
			'id'               => 'gpea_default_commitment_projects',
			'type'             => 'commitment_projects_page_dropdown',
		];

		/* Our commitment: issues */
		$cmb_options[] = [
			'name'             => esc_html__( 'Select the page with full list of issues', self::METABOX_ID ),
			'id'               => 'gpea_default_commitment_issues',
			'type'             => 'commitment_issues_page_dropdown',
		];

		/* Engaging default newsletter recipient */
		$cmb_options[] = [
			'name'             => esc_html__( '"Id" code of your Engaging default subscription page', self::METABOX_ID ),
			'desc'             => esc_html__( 'When user will select one or more topics to follow, he will be able to subscribe to that page', self::METABOX_ID ),
			'id'               => 'gpea_default_en_subscription_page',
			'type'             => 'text',
		];

		/* default Engaging planet4-form associated to the tag cloud */

		$cmb_options[] = [
			'name'             => esc_html__( 'Select the form for tag cloud subsciption', self::METABOX_ID ),
			'desc'             => esc_html__( 'Form will be shown below the tag cloud to subscribe to Engaging Newsletter', self::METABOX_ID ),
			'id'               => 'gpea_tag_cloud_newsletter_form',
			'type'             => 'select',
			'show_option_none' => TRUE,
			'options'          => $this->generate_post_select( 'p4en_form', NULL, NULL ),
		];

		/* Other Engaging newsletter default module */
		$cmb_options[] = [
			'name'             => esc_html__( 'Thank you main text, for "topic/issue" newsletter subscribe', self::METABOX_ID ),
			'desc'             => esc_html__( 'Show to user after he sign up also to newsletter', self::METABOX_ID ),
			'id'               => 'gpea_subscription_page_thankyou_title',
			'type'             => 'text',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Thank you message: second line', self::METABOX_ID ),
			'desc'             => esc_html__( 'Show to user after he sign up also to newsletter', self::METABOX_ID ),
			'id'               => 'gpea_subscription_page_thankyou_subtitle',
			'type'             => 'text',
		];

		/* donation default link */
		$cmb_options[] = [
			'name'             => esc_html__( 'Default external link for donation ', self::METABOX_ID ),
			'desc'             => esc_html__( 'Parameters and fields of donation box will be sent to this link', self::METABOX_ID ),
			'id'               => 'gpea_default_donation_link',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Engaging question for recurring donation', self::METABOX_ID ),
			'desc'             => esc_html__( 'question name', self::METABOX_ID ),
			'id'               => 'gpea_donation_recurring_question',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Donation: minimum amount for one-off', self::METABOX_ID ),
			'id'               => 'gpea_donation_minimum-oneoff',
			'type'             => 'text',
			'attributes'       => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Donation: minimum amount for regular', self::METABOX_ID ),
			'id'               => 'gpea_donation_minimum-regular',
			'type'             => 'text',
			'attributes'       => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Donation: suggested amount for one-off', self::METABOX_ID ),
			'id'               => 'gpea_donation_suggested-oneoff',
			'type'             => 'text',
			'attributes'       => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Donation: suggested amount for regular', self::METABOX_ID ),
			'id'               => 'gpea_donation_suggested-regular',
			'type'             => 'text',
			'attributes'       => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Donation: error message in case of lower to minimum', self::METABOX_ID ),
			'id'               => 'gpea_donation_minimum-error-message',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Donation: default currency', self::METABOX_ID ),
			'id'               => 'gpea_default_donation_currency',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Sharing options', self::METABOX_ID ),
			'id'               => 'gpea_sharing_options',
			'type'             => 'multicheck',
			'options'          => [
				'email'     => 'Email',
				'facebook'  => 'Facebook',
				'twitter'   => 'Twitter',
				'whatsapp'  => 'Whatsapp',
				'line'      => 'Line',
				'kakaotalk' => 'KakaoTalk',
				'wechat'    => 'WeChat',
				'web_api'   => 'Web API Navigator',
			],
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'KakaoTalk app id', self::METABOX_ID ),
			'id'               => 'gpea_kakao_app_id',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Facebook app id', self::METABOX_ID ),
			'desc'             => esc_html__( 'Used in subscribe form', self::METABOX_ID ),
			'id'               => 'gpea_facebook_app_id',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Support email recipient: general', self::METABOX_ID ),
			'id'               => 'gpea_support_recipient_email_general',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Support email recipient: special donations', self::METABOX_ID ),
			'id'               => 'gpea_support_recipient_email_special',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Email recipient for ugc submission', self::METABOX_ID ),
			'id'               => 'gpea_ugc_recipient_email',
			'type'             => 'text',
		];

		// post donation luncher

		$cmb_options[] = [
			'name'             => esc_html__( 'Title for donation launcher below post', self::METABOX_ID ),
			'id'               => 'gpea_post_donation_launcher_title',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Text for donation launcher below post', self::METABOX_ID ),
			'id'               => 'gpea_post_donation_launcher_text',
			'type'             => 'textarea',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Link for donation launcher below post', self::METABOX_ID ),
			'id'               => 'gpea_post_donation_launcher_link',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Button label for donation launcher', self::METABOX_ID ),
			'id'               => 'gpea_post_donation_launcher_label',
			'type'             => 'text',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Image for donation launcher below post', self::METABOX_ID ),
			'id'               => 'gpea_post_donation_launcher_image',
			'type'             => 'file',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Align the image to vertical center for donation launcher', self::METABOX_ID ),
			'desc'             => esc_html__( 'Align the image center vertically.', self::METABOX_ID ),
			'id'               => 'gpea_post_donation_launcher_image_align_center',
			'type'             => 'checkbox',
		];

	}

	/**
	 * Registers donation buttons option meta box(es).
	 */
	public function register_donation_button_options_metabox() {
		$this->subpages['gpea_donation_button_options_page'] = [
			'title'        => esc_html__( 'Post Donation Buttons', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Donation Buttons', self::METABOX_ID ),
			'option_key'   => 'gpea_donation_button_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_donation_button_options_page']['fields'];

		$cmb_options = $this->add_donation_option_fields( $cmb_options );

	}

	/**
	 * Registers donation blocks option meta box(es).
	 */
	public function register_donation_block_options_metabox() {

		$this->subpages['gpea_donation_block_options_page'] = [
			'title'        => esc_html__( 'Post/Page Donation Blocks', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Donation Blocks', self::METABOX_ID ),
			'option_key'   => 'gpea_donation_block_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_donation_block_options_page']['fields'];

		$cmb_options = $this->add_donation_option_fields( $cmb_options, TRUE );

	}

	/**
	 * Registers subscription buttons option meta box(es).
	 */
	public function register_subscription_button_options_metabox() {
		$this->subpages['gpea_subscription_button_options_page'] = [
			'title'        => esc_html__( 'Post Subscription Buttons', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Subscription Buttons', self::METABOX_ID ),
			'option_key'   => 'gpea_subscription_button_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_subscription_button_options_page']['fields'];

		$cmb_options = $this->add_donation_option_fields( $cmb_options, FALSE, TRUE );

	}

	/**
	 * Registers subscription blocks option meta box(es).
	 */
	public function register_subscription_block_options_metabox() {

		$this->subpages['gpea_subscription_block_options_page'] = [
			'title'        => esc_html__( 'Post/Page Subscription Blocks', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Subscription Blocks', self::METABOX_ID ),
			'option_key'   => 'gpea_subscription_block_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_subscription_block_options_page']['fields'];

		$cmb_options = $this->add_donation_option_fields( $cmb_options, TRUE, TRUE );

	}

	/**
	 * Populate an associative array with donation/subscription buttons' display mode.
	 *
	 * @return array
	 */
	public function add_donation_option_fields( $cmb_options = [], $is_block = FALSE, $is_subscription = FALSE ) {

		$gpea_extra = new \P4CT_Site();
		$main_issues = $gpea_extra->gpea_get_all_main_issues();
		$count_issues = count( $main_issues );
		$issue_string = implode( ', ', $main_issues );

		$type_string = $is_subscription ? 'subscription' : 'donation';
		$type_key = $is_subscription ? 'subscription' : 'donation';
		$layout_key = $is_block ? 'block' : 'button';
		$id_prefix = 'gpea_' . $type_key . '_' . $layout_key . '_';

		if( $is_block ) {
			$cmb_options[] = [
				'name'             => '',
				'desc'             => __('
					<ol>
						<li>Please set the default values for the ' . $type_string . ' blocks in the post/page.</li>
						<li>Location: it\'s better to set around the middle area of the post.</li>
						<li>You need to set the &quot;All-site Default&quot; at least. Every post not under ' . $count_issues . ' themes (' . $issue_string . '), or you didn\'t set the default value for 6 themes and leave the fields empty will go to the &quot;All-site Default&quot; once writers insert the ' . $type_string . ' block.</li>
					</ol>', self::METABOX_ID),
				'id'               => $id_prefix . '_hint',
				'type'             => 'title',
			];
		}
		else {
			$cmb_options[] = [
				'name'             => '',
				'desc'             => __('
					<ol>
						<li>Please set the default values for the ' . $type_string . ' buttons in the post.</li>
						<li>Buttons\' locations: one is above the main content and under the blockquote (quote with theme color background); the second one is below the main content and before the further reading section.</li>
						<li>You need to set the &quot;All-site Default&quot; at least. Every post not under ' . $count_issues . ' themes (' . $issue_string . '), or you didn\'t set the default value for 6 themes and leave the link/label empty will go to the &quot;All-site Default&quot; once writers &quot;show the button.&quot;</li>
					</ol>', self::METABOX_ID),
				'id'               => $id_prefix . '_hint',
				'type'             => 'title',
			];
		}

		$main_issues = [
			'default'  => 'All-site Default',
		] + $main_issues;

		foreach( $main_issues as $issue_key => $issue_title ) {

			$cmb_options[] = [
				'name'             => esc_html__( $issue_title, self::METABOX_ID ),
				'desc'             => $issue_key == 'default' ? '' : esc_html__( 'Leave empty to use the same setting in &quot;All-site Default&quot;', self::METABOX_ID ),
				'id'               => $id_prefix . $issue_key,
				'type'             => 'title',
			];

			if( $is_block ) {

				$cmb_options[] = [
					'name'             => esc_html__( 'Title', self::METABOX_ID ),
					'id'               => $id_prefix . $issue_key . '_title',
					'type'             => 'text',
				];

				$cmb_options[] = [
					'name'             => esc_html__( 'Description', self::METABOX_ID ),
					'desc'             => esc_html__( 'For a better user experience, it\'s better to leave empty or not over 20 characters.', self::METABOX_ID ),
					'id'               => $id_prefix . $issue_key . '_desc',
					'type'             => 'textarea',
				];

			}

			if(!$is_subscription) {

				$cmb_options[] = [
					'name'             => esc_html__( 'Button Link', self::METABOX_ID ),
					'id'               => $id_prefix . $issue_key . '_button_link',
					'type'             => 'text',
				];

			}

			$cmb_options[] = [
				'name'             => esc_html__( 'Button Label', self::METABOX_ID ),
				'id'               => $id_prefix . $issue_key . '_button_text',
				'type'             => 'text_medium',
			];

			if( $is_block ) {

				$cmb_options[] = [
					'name'    => esc_html__( 'Background Image', self::METABOX_ID ),
					'id'      => $id_prefix . $issue_key . '_bg_img',
					'type'    => 'file',
				];

			}

		}

		return $cmb_options;
		
	}

	/**
	 * Registers notification option meta box(es).
	 */
	public function register_notification_options_metabox() {

		$this->subpages['gpea_notification_options_page'] = [
			'title'        => esc_html__( 'Homepage Notifications', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Notifications', self::METABOX_ID ),
			'option_key'   => 'gpea_notification_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_notification_options_page']['fields'];

		$cmb_options['gpea_notification_group'] = [
			'id'      => 'gpea_notification_group',
			'type'    => 'group',
			'options' => [
				'group_title'   => __( 'Notification {#}', self::METABOX_ID ),
				'add_button'    => __( 'Add Another Notification', self::METABOX_ID ),
		        'remove_button' => __( 'Remove Notification', self::METABOX_ID ),
		        'sortable'      => TRUE,
			],
			'fields'  => [],
		];

		$cmb_child_options = &$cmb_options['gpea_notification_group']['fields'];

		$cmb_child_options[] = [
			'name'             => esc_html__( 'Type (Color)', self::METABOX_ID ),
			'id'               => 'layout',
			'type'             => 'radio',
			'options'          => [
				'new'         => 'Hot updates (Dark green)',
				'maintaining' => 'Maintaining (Green)',
				'maintain'    => 'Maintain announcement (Light green)',
			],
			'default'          => 'new',
		];

		$cmb_child_options[] = [
			'name'             => esc_html__( 'Text', self::METABOX_ID ),
			'id'               => 'content',
			'type'             => 'textarea_small',
		];

		$cmb_child_options[] = [
			'name'             => esc_html__( 'Link', self::METABOX_ID ),
			'id'               => 'link',
			'type'             => 'text',
		];

		$cmb_child_options[] = [
			'name'             => esc_html__( 'Show the Notification', self::METABOX_ID ),
			'id'               => 'enabled',
			'type'             => 'select',
			'options_cb'       => [ $this, 'populate_yes_or_no_options' ],
			'default'          => 1,
		];

		$cmb_child_options[] = [
			'name'             => esc_html__( 'Start Date/Time', self::METABOX_ID ),
			'desc'             => esc_html__( 'Leave empty for unlimited.', self::METABOX_ID ),
			'id'               => 'start_timestamp',
			'type'             => 'text_datetime_timestamp',
		];

		$cmb_child_options[] = [
			'name'             => esc_html__( 'End Date/Time', self::METABOX_ID ),
			'desc'             => esc_html__( 'Leave empty for unlimited.', self::METABOX_ID ),
			'id'               => 'end_timestamp',
			'type'             => 'text_datetime_timestamp',
		];

	}

	public function gpea_override_meta_value( $override, $args, $field_args, $field ) {

		switch($args['id']) {
			case 'gpea_notification_options':
				update_option( $args['id'] . '_last_modified', str_replace('.', '_', microtime(TRUE)) );
				break;
		}
		
		return NULL;
		
	}
	
	/**
	 * Registers testimony block option meta box(es).
	 */
	public function register_testimony_block_options_metabox() {

		$const_name = 'P4EABKS\Controllers\Blocks\Testimony_Controller::LAYOUT_OPTIONS';

		$this->subpages['gpea_testimony_block_options_page'] = [
			'title'        => esc_html__( 'Page Testimony Blocks', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Testimony Blocks', self::METABOX_ID ),
			'option_key'   => 'gpea_testimony_block_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_testimony_block_options_page']['fields'];

		$card_layouts = defined($const_name) ? constant($const_name) : [];

		foreach($card_layouts as $layout_data) {

			$cmb_options[] = [
				'name'             => esc_html__( 'Layout ' . $layout_data['key'] . ': ' . $layout_data['title'], self::METABOX_ID ),
				'desc'             => $layout_data['image'] ? NULL : esc_html__( 'This layout has no background image.', self::METABOX_ID ) . '<br><br>',
				'id'               => 'gpea_testimony_block_' . $layout_data['key'],
				'type'             => 'title',
			];

			if($layout_data['image']) {
				$cmb_options[] = [
					'name'    => esc_html__( 'Background Image', self::METABOX_ID ),
					'id'      => 'gpea_testimony_block_' . $layout_data['key'] . '_bg_img',
					'type'    => 'file',
				];
			}

		}

	}

	/**
	 * Registers header nav option meta box(es).
	 */
	public function register_header_nav_options_metabox() {

		$this->subpages['gpea_header_nav_options_page'] = [
			'title'        => esc_html__( 'Header Navigation', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Navigation', self::METABOX_ID ),
			'option_key'   => 'gpea_header_nav_options',
			'fields'       => [],
		];

		$cmb_options = &$this->subpages['gpea_header_nav_options_page']['fields'];

		$cmb_options[] = [
			'name'             => esc_html__( 'Sticky CTA Button (on the bottom)', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_sticky',
			'type'             => 'title',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Show the Sticky Button', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_sticky_enabled',
			'type'             => 'checkbox',
			'default'          => '0',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Sticky Button Link', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_sticky_link',
			'type'             => 'text',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Sticky Button Label (for Tablet)', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_sticky_label',
			'type'             => 'text_medium',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Sticky Button Label (for Mobile)', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_sticky_label_mobile',
			'type'             => 'text_medium',
		];

		$cmb_options[] = [
			'name'             => esc_html__( 'Header CTA Button', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_button',
			'type'             => 'title',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Header CTA Button Link', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_button_link',
			'type'             => 'text',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Header CTA Button Label (for PC/Tablet)', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_button_label',
			'type'             => 'text_medium',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Header CTA Button Label (for Mobile)', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_button_label_mobile',
			'type'             => 'text_medium',
		];

		$cmb_options[] = [
			'name'             => esc_html( sprintf( __('"%s" Button'), __( 'login', 'gpea_theme' ) ), self::METABOX_ID ),
			'id'               => 'gpea_header_nav_login',
			'type'             => 'title',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Show the Button', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_login_enabled',
			'type'             => 'checkbox',
			'default'          => '0',
		];
		$cmb_options[] = [
			'name'             => esc_html__( 'Button Link', self::METABOX_ID ),
			'id'               => 'gpea_header_nav_login_link',
			'type'             => 'text',
		];

		$gpea_extra = new \P4CT_Site();
		$main_issues = $gpea_extra->gpea_get_all_main_issues();

		foreach($gpea_extra::NAV_MENUS as $menu_key => $menu_conf) {

			$id_prefix = 'gpea_header_nav_menu_' . $menu_key;

			$cmb_options[] = [
				'name'             => esc_html( sprintf( __( 'Header Menu: %s' ), __( $menu_conf[ 'msgid' ], 'gpea_theme' ) ), self::METABOX_ID ),
				'id'               => $id_prefix,
				'type'             => 'title',
			];

			if( $menu_conf[ 'link' ] ) {
				$cmb_options[] = [
					'name'             => esc_html__( 'Main Item Link', self::METABOX_ID ),
					'id'               => $id_prefix . '_link',
					'type'             => 'text',
				];
			}

			if( $menu_conf[ 'issues' ] ) {
				foreach( $main_issues as $issue_key => $issue_title ) {
					$cmb_options[] = [
						'name'             => esc_html( sprintf( __( 'Sub-Item Title: %s' ), $issue_title ), self::METABOX_ID ),
						'id'               => $id_prefix . '_label--' . $issue_key,
						'type'             => 'text_medium',
					];
					$cmb_options[] = [
						'name'             => esc_html( sprintf( __( 'Sub-Item Link: %s' ), $issue_title ), self::METABOX_ID ),
						'id'               => $id_prefix . '_link--' . $issue_key,
						'type'             => 'text',
					];
					$cmb_options[] = [
						'name'             => esc_html( sprintf( __( 'Sub-Item Sort: %s' ), $issue_title ), self::METABOX_ID ),
						'id'               => $id_prefix . '_sort--' . $issue_key,
						'type'             => 'text_small',
					];
				}
			}

			$cmb_options[] = [
				'name'             => esc_html__( 'Show Sub-menu CTA Button', self::METABOX_ID ),
				'id'               => $id_prefix . '_cta_enabled',
				'type'             => 'checkbox',
				'default'          => '0',
			];
			$cmb_options[] = [
				'name'             => esc_html__( 'Sub-menu CTA Button Link', self::METABOX_ID ),
				'id'               => $id_prefix . '_cta_link',
				'type'             => 'text',
			];
			$cmb_options[] = [
				'name'             => esc_html__( 'Sub-menu CTA Button Label (for PC/Tablet)', self::METABOX_ID ),
				'id'               => $id_prefix . '_cta_label',
				'type'             => 'text_medium',
			];
			$cmb_options[] = [
				'name'             => esc_html__( 'Sub-menu CTA Button Label (for Mobile)', self::METABOX_ID ),
				'id'               => $id_prefix . '_cta_label_mobile',
				'type'             => 'text_medium',
			];
			$cmb_options[] = [
				'name'             => esc_html__( 'Sub-menu CTA Button Icon', self::METABOX_ID ),
				'id'               => $id_prefix . '_cta_img',
				'type'             => 'file',
			];


		}



	}

	/**
	 * Registers module map page.
	 */
	public function register_module_map_page() {

		$this->subpages['gpea_module_map_page'] = [
			'title'        => esc_html__( 'Module In Posts/Pages', self::METABOX_ID ),
			'menu_title'   => esc_html__( 'Module In Pages', self::METABOX_ID ),
			'display_callback'   => [ $this, 'display_module_map_page' ],
		];

	}

	/**
	 * Registers module map page.
	 */
	public function display_module_map_page(array $subpage) {

		$type_map = [ 
			'page' => 'Pages',
			'post' => 'Posts',
		];

		$perpage = 50;
		$current_type = isset( $_GET['type'] ) && array_key_exists( $_GET['type'], $type_map ) ? $_GET['type'] : key( $type_map );
		$current_module = isset( $_GET['module'] ) ? $_GET['module'] : NULL;
		$current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
		?>
		
		<?php if( !is_callable( 'Shortcode_UI', 'get_shortcodes' ) ): ?>

		<p>Plugin &quot;Shortcake (Shortcode UI)&quot; is not exists.</p>

		<?php else: ?>

		<nav class="nav-tab-wrapper">
			<?php foreach( $type_map as $value => $label ): ?>
			<a href="<?php echo esc_attr( add_query_arg( [
				'type' => $value,
				'module' => NULL,
				'paged' => NULL,
			] ) ); ?>" class="nav-tab <?php if( $current_type == $value ): ?>nav-tab-active<?php endif; ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
    	</nav>

		<h3>Search <?php echo esc_html( $type_map[ $current_type ] ); ?> by Selected a Module:</h3>

		<div style="column-gap: 2em; column-count: auto; column-width: 20em; line-height: 2;">
		<?php
		
		$module_map = \Shortcode_UI::get_instance()->get_shortcodes();
		$current_module_shortcode = NULL;
		$first_module = TRUE;
		foreach( $module_map as $key => $settings) {
			if( !in_array( $current_type, $settings[ 'post_type' ] ) ) {
				continue;
			}
			if( $first_module && !isset( $current_module ) ) {
				$current_module = $key;
				$first_module = FALSE;
			}
			if( $key == $current_module ) {
				$current_module_shortcode = $settings[ 'shortcode_tag' ];
			}
			?>
			<label style="display: block;"><input onclick="location=this.value" name="module" type="radio" value="<?php echo esc_attr( add_query_arg( [
				'module' => $key,
				'paged' => NULL,
			] ) ); ?>" <?php if( $current_module == $key ): ?>checked<?php endif; ?> /><?php echo esc_html( $settings[ 'label' ] ); ?></label>
			<?php
		}

		?>

		</div>

		<?php if( !isset( $current_module_shortcode ) ): ?>

		<h3>Search Result:</h3>

		<p>No module is selected.</p>

		<?php else: ?>

		<?php
			
		$the_query = new WP_Query( [
			'posts_per_page' => $perpage,
			'paged' => $current_page,
			'post_type' => $current_type,
			'gpea_short_code' => $current_module,
		] );

		?>

		<h3>Search Result: Found <?php echo esc_html( $the_query->found_posts ); ?> <?php echo esc_html( $type_map[ $current_type ] ); ?></h3>

		<?php

		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			echo '<p><b>' . esc_html( get_the_title() ) . ' — ' . esc_html( get_post_statuses()[ get_post_status() ] ) . '</b><br><a href="' . esc_attr( get_the_permalink() ) . '" target="_blank">' . esc_html( get_the_permalink() ) . '</a></p>';
		}

		echo paginate_links( [
			'base' => add_query_arg( [
				'paged' => '%#%',
			] ),
			'format' => '?paged=%#%',
			'current' => $current_page,
			'total' => $the_query->max_num_pages,
		] );

		?>

		<?php endif; ?>

		<?php endif; ?>

		<?php

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
				$slugs = [ $slugs ];
			}

			$display = FALSE;
			$terms = wp_get_object_terms( $post_id, $taxonomy );
			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $slugs, TRUE ) ) {
					$display = TRUE;
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

		$options = [
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'suppress_filters' => FALSE,
			'posts_per_page'   => -1,
		];

		if ( $post_attribute ) {
			$options['tax_query'] = [
				[
					'taxonomy' => 'p4_post_attribute',
					'field'    => 'slug',
					'terms'    => $post_attribute,
				],
			];
		}

		if ( $page_template ) {
			$options['meta_key'] = '_wp_page_template';
			$options['meta_value']  = $page_template;
		}

		$posts = get_posts( $options );

		$output = [];
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
	public function populate_yes_or_no_options( $field ) {
		$options = [
			'1' => 'Yes',
			'0' => 'No',
		];
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
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
				'show_option_none' => __( 'Select Page', self::METABOX_ID ),
				'hide_empty'       => 0,
				'hierarchical'     => TRUE,
				'selected'         => $value,
				'name'             => 'gpea_mainissue_page',
			]
		);
	}


	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @param string $plugin_page The key for the current page.
	 */
	public function admin_page_display( array $subpage ) {

		$fields = $subpage['fields'] ?? [];
		$option_key = $subpage['option_key'] ?? NULL;

		$add_scripts = $subpage['add_scripts'] ?? NULL;
		if ( $add_scripts ) {
			$add_scripts();
		}
		?>
		<div class="wrap <?php echo esc_attr( $option_key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php
			if( isset( $subpage[ 'display_callback' ] ) && is_callable( $subpage[ 'display_callback' ] ) ){
				call_user_func($subpage['display_callback'], $subpage);
			}
			elseif(isset($option_key)) {
				cmb2_metabox_form( $this->option_metabox( $fields, $option_key ), $option_key );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Defines the theme option metabox and field configuration.
	 *
	 * @param array $fields expects the fields (if they exist) of this subpage.
	 *
	 * @return array
	 */
	public function option_metabox( $fields, $option_key ) {
		return [
			'id'         => self::METABOX_ID,
			'show_on'    => [
				'key'   => 'options-page',
				'value' => [
					$option_key,
				],
			],
			'show_names' => TRUE,
			'fields'     => $fields,
		];
	}

}
