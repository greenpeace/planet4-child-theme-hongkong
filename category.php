<?php
/**
 * Category archive page.
 *
 * Shows the category name, its description and its posts as cards.
 * The cards and the grid come from the search page.
 * A Load More button adds the next page of cards, like the search page.
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
$context['load_more_text']       = __( 'SHOW MORE RESULTS', 'gpea_theme' );

// The Load More button links to the next page of this category.
$current_page = max( 1, (int) get_query_var( 'paged' ) );
if ( $current_page < (int) $wp_query->max_num_pages ) {
	$context['next_page_link'] = get_pagenum_link( $current_page + 1 );
}

// The search page styles apply only to pages with the "search" body class.
// This page reuses that card grid, so it takes the class too.
$context['custom_body_classes'] = 'search';

// Yoast SEO prints its own canonical link when it is active.
if ( ! defined( 'WPSEO_VERSION' ) ) {
	$context['canonical_link'] = home_url( $wp->request );
}

do_action( 'enqueue_google_tag_manager_script', $context );
Timber::render( [ 'category.twig' ], $context );
