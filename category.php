<?php
/**
 * Displays a Main issue (category) page.
 *
 * Category <-> Issue
 * Tag <-> Campaign
 * Post <-> Action
 *
 * @package P4MT
 */

use Timber\Timber;
// use P4BKS\Controllers\Blocks\Covers_Controller as Covers;
// use P4BKS\Controllers\Blocks\Articles_Controller as Articles;
// use P4BKS\Controllers\Blocks\ContentFourColumn_Controller as ContentFourColumn;
// use P4BKS\Controllers\Blocks\CampaignThumbnail_Controller as CampaignThumbnail;
// use P4BKS\Controllers\Blocks\HappyPoint_Controller as HappyPoint;

$context = Timber::get_context();
$gpea_extra = new P4CT_Site();

if ( is_category() ) {
	$context['category']  = get_queried_object();
	$redirect_id = get_term_meta( $context['category']->term_id, 'gpea_mainissue_page', true );

	if ( $redirect_id ) {

		global $wp_query;
		$redirect_page               = get_post( $redirect_id );
		$wp_query->queried_object    = $redirect_page;
		$wp_query->queried_object_id = $redirect_page->ID;
		include 'page-templates/main-issue.php';

	} else {

		$context['strings'] = [
			'our_initiatives' => __( 'Our Initiatives', 'gpea_theme' ),
			'latest_related_news' => __( 'Latest news about this topic', 'gpea_theme' ),
			'read_all' => __( 'Read all', 'gpea_theme' ),
		];

		$context['custom_body_classes'] = 'white-bg page-issue-page';

		// $context['background_image']      = get_term_meta( $context['tag']->term_id, 'tag_attachment', true );

		$context['tag_name']            = $context['category']->name;
		$context['tag_description']     = wpautop( $context['category']->description );		

		$context['og_description'] = $context['tag_description'];
		$context['projects'] = "[shortcake_projects_carousel layout='light' title='".$context['strings']['our_initiatives']."' topic='".$context['category']->term_id."' /]";

		$context['related_posts'] = $gpea_extra->gpea_get_related( false, 6, 1, $context['category']->term_id, false );

		//[shortcake_take_action_boxout take_action_page='$take_action_page' /]";

		// $campaign = new P4_Taxonomy_Campaign( $templates, $context );

		// $campaign->add_block(
		// 	Covers::BLOCK_NAME,
		// 	[
		// 		'title'       => __( 'Things you can do', 'planet4-master-theme' ),
		// 		'description' => __( 'We want you to take action because together we\'re strong.', 'planet4-master-theme' ),
		// 		'select_tag'  => $context['tag']->term_id,
		// 		'covers_view' => '0',   // Show 6 covers in Campaign page.
		// 	]
		// );

		// $campaign->add_block(
		// 	Articles::BLOCK_NAME,
		// 	[
		// 		'tags' => $context['tag']->term_id,
		// 	]
		// );

		Timber::render( [ 'tag.twig' ], $context );

	}
}
