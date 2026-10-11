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

/**
 * Legal pages use the dedicated template (hero, side contents, numbered sections).
 */
function ob_legal_slugs() {
	return array( 'mentions-legales', 'politique-de-confidentialite', 'rgpd', 'dmca', 'disclaimer', 'termes-et-conditions' );
}

add_filter( 'template_include', function ( $template ) {
	if ( is_page() && in_array( get_post_field( 'post_name', get_queried_object_id() ), ob_legal_slugs(), true ) ) {
		$legal = OB_DIR . '/template-legal.php';
		if ( is_readable( $legal ) ) {
			return $legal;
		}
	}
	return $template;
} );

/**
 * Prepare legal page HTML: numbered headings ("1. Title") become a gold number
 * plus a title with an anchor id. Returns the HTML and the list of sections.
 *
 * @param string $html Rendered content.
 * @return array{html:string,sections:array<int,array{n:string,h:string,id:string}>}
 */
function ob_legal_prepare( $html ) {
	$sections = array();
	$seen     = array();
	$count    = 0;

	$has_numbered = (bool) preg_match( '#<h[23][^>]*>\s*\d+\.\s#u', $html );

	$html = preg_replace_callback(
		'#<h([23])([^>]*)>(.*?)</h\1>#is',
		function ( $m ) use ( &$sections, &$seen, &$count, $has_numbered ) {
			$level = $m[1];
			$text  = trim( html_entity_decode( wp_strip_all_tags( $m[3] ), ENT_QUOTES, 'UTF-8' ) );
			$num   = '';
			$title = $text;
			if ( preg_match( '/^(\d+)\.\s*(.+)$/u', $text, $parts ) ) {
				$num   = str_pad( $parts[1], 2, '0', STR_PAD_LEFT );
				$title = $parts[2];
			} elseif ( $has_numbered ) {
				// Unnumbered heading in a numbered document: a document title, left as is.
				return '<h' . $level . $m[2] . '>' . $m[3] . '</h' . $level . '>';
			}
			$base = sanitize_title( $title ) ?: 'section';
			$id   = $base;
			$n    = 2;
			while ( isset( $seen[ $id ] ) ) {
				$id = $base . '-' . $n++;
			}
			$seen[ $id ] = true;
			++$count;
			$sections[] = array( 'n' => $num ?: str_pad( (string) $count, 2, '0', STR_PAD_LEFT ), 'h' => $title, 'id' => $id );
			$badge      = '<span class="ob-hnum">' . esc_html( $num ?: str_pad( (string) $count, 2, '0', STR_PAD_LEFT ) ) . '</span>';
			return '<h2 class="ob-legal-h" id="' . esc_attr( $id ) . '">' . $badge . esc_html( $title ) . '</h2>';
		},
		$html
	);

	return array( 'html' => $html, 'sections' => $sections );
}

/**
 * Images in articles without alt text take their caption (or the post title) as alt.
 */
add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || false === strpos( $content, '<img' ) ) {
		return $content;
	}
	$title = get_the_title();
	return preg_replace_callback(
		'#<figure\b[^>]*>.*?</figure>|<img\b[^>]*>#is',
		function ( $m ) use ( $title ) {
			$html = $m[0];
			$caption = '';
			if ( 0 === strpos( $html, '<figure' ) && preg_match( '#<figcaption[^>]*>(.*?)</figcaption>#is', $html, $c ) ) {
				$caption = trim( wp_strip_all_tags( $c[1] ) );
			}
			return preg_replace_callback(
				'#<img\b[^>]*>#i',
				function ( $im ) use ( $caption, $title ) {
					$tag = $im[0];
					$has_alt = preg_match( '#\salt=(["\'])(.*?)\1#is', $tag, $a );
					if ( $has_alt && '' !== trim( $a[2] ) ) {
						return $tag;
					}
					$alt = esc_attr( $caption ?: $title );
					if ( $has_alt ) {
						return preg_replace( '#\salt=(["\']).*?\1#is', ' alt="' . $alt . '"', $tag, 1 );
					}
					return preg_replace( '#<img\b#i', '<img alt="' . $alt . '"', $tag, 1 );
				},
				$html
			);
		},
		$content
	);
}, 30 );
