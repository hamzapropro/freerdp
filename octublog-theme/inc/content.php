<?php
/**
 * Post content: heading anchors for the table of contents.
 *
 * @package Octublog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stable anchor ids for a list of heading texts.
 */
function ob_heading_ids( array $texts ) {
	$ids  = array();
	$seen = array();
	foreach ( $texts as $text ) {
		$base = sanitize_title( $text );
		$base = $base ? $base : 'section';
		$id   = $base;
		$n    = 2;
		while ( isset( $seen[ $id ] ) ) {
			$id = $base . '-' . $n++;
		}
		$seen[ $id ] = true;
		$ids[]       = $id;
	}
	return $ids;
}

/**
 * Level 2 headings of a post, as [ [ 'id' => ..., 'label' => ... ], ... ].
 */
function ob_toc( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || ! preg_match_all( '#<h2\b[^>]*>(.*?)</h2>#is', $post->post_content, $m ) ) {
		return array();
	}
	$labels = array_map( fn( $html ) => trim( html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) ), $m[1] );
	$ids    = ob_heading_ids( $labels );
	$toc    = array();
	foreach ( $labels as $i => $label ) {
		if ( '' !== $label ) {
			$toc[] = array( 'id' => $ids[ $i ], 'label' => $label );
		}
	}
	return $toc;
}

/**
 * Give every h2 of a single post the id used by the table of contents.
 */
add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( ! preg_match_all( '#<h2\b([^>]*)>(.*?)</h2>#is', $content, $m, PREG_SET_ORDER ) ) {
		return $content;
	}
	$labels = array_map( fn( $match ) => trim( html_entity_decode( wp_strip_all_tags( $match[2] ), ENT_QUOTES, 'UTF-8' ) ), $m );
	$ids    = ob_heading_ids( $labels );
	$i      = 0;
	return preg_replace_callback(
		'#<h2\b([^>]*)>(.*?)</h2>#is',
		function ( $match ) use ( $ids, &$i ) {
			$id    = $ids[ $i++ ] ?? '';
			$attrs = $match[1];
			if ( $id && ! preg_match( '/\sid\s*=/', $attrs ) ) {
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}
			return '<h2' . $attrs . '>' . $match[2] . '</h2>';
		},
		$content
	);
}, 20 );
