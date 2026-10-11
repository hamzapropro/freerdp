<?php
/**
 * Homepage (magazine layout). Later pages of the post list fall back to the archive grid.
 *
 * @package Octublog
 */

if ( is_paged() ) {
	get_template_part( 'index' );
	return;
}

get_header();

// The sections come from the "Accueil" page (blocks); the default set is used until it has content.
$ob_home = get_page_by_path( 'accueil' );
$ob_body = $ob_home && trim( $ob_home->post_content ) ? $ob_home->post_content : ob_home_default_blocks();
remove_filter( 'the_content', 'convert_smilies', 20 ); // Titles must not turn into emoticons.
echo apply_filters( 'the_content', $ob_body ); // phpcs:ignore WordPress.Security.EscapeOutput

get_footer();
