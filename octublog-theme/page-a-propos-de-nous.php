<?php
/**
 * About page: the layout is made of blocks in the page editor.
 *
 * @package Octublog
 */

get_header();
while ( have_posts() ) {
	the_post();
	the_content();
}
get_footer();
