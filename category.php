<?php
/**
 * Category archive page.
 *
 * Shows the category name, its description and its posts as cards.
 * The cards and the grid come from the search page.
 * Numbered page links let readers and search engines reach every page.
 *
 * @package P4CT
 */

use Timber\Timber;

global $wp, $wp_query;

$context  = Timber::context();
$category = get_queried_object();

// The search page turns posts into cards. Use the same code so the cards match.
$search = new P4CT_ElasticSearch();
$search->set_main_issues();

$context['category']             = $category;
$context['category_description'] = wpautop( $category->description );
$context['taxonomy']             = $category;
$context['wp_title']             = $category->name;
$context['og_type']              = 'website';
$context['og_description']       = $category->description;
$context['page_category']        = 'Listing Page';
$context['paged_posts']          = $search->make_card_posts( $wp_query->posts );
$context['page_links']           = gpea_category_page_links();

// The search page styles apply only to pages with the "search" body class.
// This page reuses that card grid, so it takes the class too.
$context['custom_body_classes'] = 'search';

// Yoast SEO prints its own canonical link when it is active.
if ( ! defined( 'WPSEO_VERSION' ) ) {
	$context['canonical_link'] = home_url( $wp->request );
}

do_action( 'enqueue_google_tag_manager_script', $context );
Timber::render( [ 'category.twig' ], $context );
