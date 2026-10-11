<?php
/**
 * Theme setup, assets and global filters.
 *
 * @package Octublog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'octublog', OB_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array( 'height' => 100, 'width' => 400, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	register_nav_menus(
		array(
			'footer_about'  => __( 'Pied de page : Qui sommes-nous', 'octublog' ),
			'footer_famous' => __( 'Pied de page : Articles célèbres', 'octublog' ),
			'footer_legal'  => __( 'Pied de page : Liens légaux', 'octublog' ),
		)
	);
} );

add_action( 'wp_enqueue_scripts', function () {
	$css = OB_DIR . '/assets/css/main.css';
	$js  = OB_DIR . '/assets/js/main.js';

	wp_enqueue_style( 'octublog', OB_URI . '/assets/css/main.css', array(), OB_VERSION . '.' . filemtime( $css ) );
	wp_enqueue_script( 'octublog', OB_URI . '/assets/js/main.js', array(), OB_VERSION . '.' . filemtime( $js ), array( 'strategy' => 'defer', 'in_footer' => true ) );

	// Block styles are only needed where post content is rendered.
	if ( ! is_singular() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}, 20 );

// Preload the two faces used above the fold.
add_action( 'wp_head', function () {
	foreach ( array( 'fraunces-latin-opsz-normal.woff2', 'figtree-latin.woff2' ) as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( OB_URI . '/assets/fonts/' . $font )
		);
	}
}, 1 );

// The theme renders its own author box, so the PublishPress Authors one is not appended.
add_filter( 'pp_multiple_authors_filter_should_display_author_box', '__return_false' );

add_filter( 'excerpt_length', fn() => 32 );
add_filter( 'excerpt_more', fn() => '…' );

/*
 * Archive page sizes that keep the 3 column grids complete. A rubrique shows one
 * featured article plus 9 cards on its first page, then 9 cards per page.
 */
const OB_CAT_FIRST = 10;
const OB_CAT_PER   = 9;

add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_category() ) {
		$paged = max( 1, (int) $query->get( 'paged' ) );
		if ( 1 === $paged ) {
			$query->set( 'posts_per_page', OB_CAT_FIRST );
		} else {
			$query->set( 'posts_per_page', OB_CAT_PER );
			$query->set( 'offset', OB_CAT_FIRST + ( $paged - 2 ) * OB_CAT_PER );
		}
	}
	if ( $query->is_author() || $query->is_home() ) {
		$query->set( 'posts_per_page', 12 );
	}
	if ( $query->is_search() ) {
		$query->set( 'posts_per_page', 10 );
		$query->set( 'post_type', 'post' );
	}
} );

// With the uneven first page, report a total that gives the right number of pages.
add_filter( 'found_posts', function ( $found, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
		return $found;
	}
	$pages = $found <= OB_CAT_FIRST ? 1 : 1 + (int) ceil( ( $found - OB_CAT_FIRST ) / OB_CAT_PER );
	return $pages * (int) $query->get( 'posts_per_page' );
}, 10, 2 );
