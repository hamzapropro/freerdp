<?php
/**
 * One-off clean-up of the Octublog draft articles and content plans.
 * Run through execute-php: define OB_APPLY (true/false) and OB_DESC_JSON, then include this file.
 *  - Bathroom-storage drafts (HTML) -> Gutenberg blocks, no H1, category Maison & Organisation, author Inès Mercier.
 *  - Meta descriptions (+ focus keyphrases for the converted ones) for the draft articles.
 *  - Content plans -> the category and the author of their rubrique.
 */

$CAT  = array( 'enfance' => 639, 'inspi' => 640, 'loisirs' => 641, 'maison' => 642, 'tech' => 643, 'voyage' => 644, 'cuisine' => 638 );
$AUTH = array(
	'camille' => array( 'user' => 7, 'term' => 650 ),
	'lea'     => array( 'user' => 6, 'term' => 649 ),
	'ines'    => array( 'user' => 4, 'term' => 647 ),
	'lucas'   => array( 'user' => 2, 'term' => 645 ),
	'manon'   => array( 'user' => 5, 'term' => 648 ),
	'chloe'   => array( 'user' => 3, 'term' => 646 ),
	'thomas'  => array( 'user' => 8, 'term' => 651 ),
);
$PLAN = array(
	'enfance' => array( 'camille', array( 3869, 3871, 3875, 3877, 3879 ) ),
	'maison'  => array( 'ines', array( 3910, 3912, 3914, 3916, 3918 ) ),
	'inspi'   => array( 'lea', array( 3902 ) ),
	'loisirs' => array( 'manon', array( 3906 ) ),
	'voyage'  => array( 'chloe', array( 3928 ) ),
	'tech'    => array( 'lucas', array( 3930 ) ),
	'cuisine' => array( 'thomas', array( 4588, 4590, 4593, 4595, 4598, 4600, 4602 ) ),
);
$JOZEF  = range( 8754, 8774 );
$THOMAS = array( 4683, 4668, 4663, 4665, 4641, 4670, 4672, 4674, 4676, 4679, 4681, 4685, 4688, 4690, 4692, 4694, 4659, 4656, 4653, 4651, 4649, 4646, 4644, 4639, 4696 );

/** Find the end of the element that opens at $pos (same-name nesting aware). */
function ob_dr_element( $html, $pos, $tag ) {
	$depth = 0;
	$len   = strlen( $html );
	$i     = $pos;
	while ( $i < $len ) {
		if ( ! preg_match( '#<(/?)' . $tag . '\b[^>]*>#i', $html, $m, PREG_OFFSET_CAPTURE, $i ) ) {
			return false;
		}
		$depth += ( '/' === $m[1][0] ) ? -1 : 1;
		$i      = $m[0][1] + strlen( $m[0][0] );
		if ( 0 === $depth ) {
			return $i;
		}
	}
	return false;
}

function ob_dr_inner( $el, $tag ) {
	return trim( preg_replace( '#^<' . $tag . '\b[^>]*>|</' . $tag . '>$#i', '', trim( $el ) ) );
}

/** HTML -> Gutenberg blocks. Returns array( blocks, stats ). */
function ob_dr_blocks( $html ) {
	$html  = preg_replace( '#<!--.*?-->#s', '', $html );
	$html  = preg_replace( '#<h1\b[^>]*>.*?</h1>#is', '', $html );
	$html  = trim( $html );
	$out   = '';
	$stats = array( 'p' => 0, 'h2' => 0, 'h3' => 0, 'list' => 0, 'table' => 0, 'html' => 0 );
	$pos   = 0;
	$len   = strlen( $html );
	while ( $pos < $len ) {
		if ( ! preg_match( '#<([a-z0-9]+)\b[^>]*>#i', $html, $m, PREG_OFFSET_CAPTURE, $pos ) ) {
			break;
		}
		$tag   = strtolower( $m[1][0] );
		$start = $m[0][1];
		$end   = ob_dr_element( $html, $start, $tag );
		if ( false === $end ) {
			$end = $len;
		}
		$el    = substr( $html, $start, $end - $start );
		$pos   = $end;
		$inner = ob_dr_inner( $el, $tag );
		if ( 'p' === $tag ) {
			$out .= "<!-- wp:paragraph -->\n<p>" . $inner . "</p>\n<!-- /wp:paragraph -->\n\n";
			$stats['p']++;
		} elseif ( 'h2' === $tag ) {
			$out .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . $inner . "</h2>\n<!-- /wp:heading -->\n\n";
			$stats['h2']++;
		} elseif ( 'h3' === $tag ) {
			$out .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">" . $inner . "</h3>\n<!-- /wp:heading -->\n\n";
			$stats['h3']++;
		} elseif ( ( 'ul' === $tag || 'ol' === $tag ) && ! preg_match( '#<li\b[^>]*>(?:(?!</li>).)*<(ul|ol)\b#is', $el ) ) {
			preg_match_all( '#<li\b[^>]*>(.*?)</li>#is', $el, $li );
			$items = '';
			foreach ( $li[1] as $item ) {
				$items .= "<!-- wp:list-item -->\n<li>" . trim( $item ) . "</li>\n<!-- /wp:list-item -->\n";
			}
			$attr  = 'ol' === $tag ? ' {"ordered":true}' : '';
			$out  .= '<!-- wp:list' . $attr . " -->\n<" . $tag . " class=\"wp-block-list\">\n" . $items . '</' . $tag . ">\n<!-- /wp:list -->\n\n";
			$stats['list']++;
		} elseif ( 'table' === $tag ) {
			$out .= "<!-- wp:table -->\n<figure class=\"wp-block-table\">" . preg_replace( '#^<table\b[^>]*>#i', '<table>', trim( $el ) ) . "</figure>\n<!-- /wp:table -->\n\n";
			$stats['table']++;
		} else {
			$out .= "<!-- wp:html -->\n" . trim( $el ) . "\n<!-- /wp:html -->\n\n";
			$stats['html']++;
		}
	}
	return array( trim( $out ) . "\n", $stats );
}

function ob_dr_text( $html ) {
	return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) ) );
}

function ob_dr_set_author( $post_id, $a ) {
	wp_update_post( array( 'ID' => $post_id, 'post_author' => $a['user'] ) );
	wp_set_object_terms( $post_id, array( (int) $a['term'] ), 'author' );
}

$desc   = json_decode( OB_DESC_JSON, true );
$report = array( 'apply' => OB_APPLY, 'jozef' => array(), 'desc' => array(), 'plans' => array(), 'problems' => array() );
$backup = array();

global $wpdb;

/* 1. Bathroom-storage drafts */
foreach ( $JOZEF as $id ) {
	$p = get_post( $id );
	if ( ! $p || 'draft' !== $p->post_status ) {
		$report['problems'][] = "skip $id";
		continue;
	}
	$backup[ $id ] = array( 'content' => $p->post_content, 'author' => $p->post_author, 'cats' => wp_get_post_categories( $id ), 'terms' => wp_get_object_terms( $id, 'author', array( 'fields' => 'ids' ) ) );
	list( $blocks, $st ) = ob_dr_blocks( $p->post_content );
	$before = ob_dr_text( preg_replace( '#<h1\b[^>]*>.*?</h1>#is', '', preg_replace( '#<!--.*?-->#s', '', $p->post_content ) ) );
	$after  = ob_dr_text( $blocks );
	$parsed = parse_blocks( $blocks );
	$names  = array_filter( wp_list_pluck( $parsed, 'blockName' ) );
	$report['jozef'][ $id ] = array( 'same_text' => $before === $after, 'blocks' => count( $names ), 'freeform' => count( parse_blocks( $blocks ) ) - count( $names ), 'h1_left' => substr_count( strtolower( $blocks ), '<h1' ), 'stats' => $st );
	if ( $before !== $after ) {
		$report['problems'][] = "text differs $id";
		continue;
	}
	if ( OB_APPLY ) {
		wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $blocks ) ) );
		wp_set_post_categories( $id, array( $CAT['maison'] ) );
		ob_dr_set_author( $id, $AUTH['ines'] );
	}
}

/* 2. Meta descriptions and focus keyphrases */
foreach ( $desc as $id => $row ) {
	$id = (int) $id;
	$p  = get_post( $id );
	if ( ! $p || 'draft' !== $p->post_status ) {
		$report['problems'][] = "no desc target $id";
		continue;
	}
	$backup[ $id ]['metadesc'] = get_post_meta( $id, '_yoast_wpseo_metadesc', true );
	$backup[ $id ]['focuskw']  = get_post_meta( $id, '_yoast_wpseo_focuskw', true );
	$d  = $row['desc'];
	$kw = $row['kw'];
	$report['desc'][ $id ] = array( 'len' => mb_strlen( $d ), 'colon' => false !== strpos( $d, ':' ) );
	if ( OB_APPLY ) {
		update_post_meta( $id, '_yoast_wpseo_metadesc', $d );
		$upd = array( 'description' => $d );
		if ( $kw ) {
			update_post_meta( $id, '_yoast_wpseo_focuskw', $kw );
			$upd['primary_focus_keyword'] = $kw;
		}
		$wpdb->update( $wpdb->prefix . 'yoast_indexable', $upd, array( 'object_id' => $id, 'object_type' => 'post' ) );
	}
}

/* 3. Authors for the Thomas drafts (kept as they are, only the PublishPress author is made explicit) */
foreach ( $THOMAS as $id ) {
	$p = get_post( $id );
	if ( ! $p ) {
		continue;
	}
	$backup[ $id ]['terms'] = wp_get_object_terms( $id, 'author', array( 'fields' => 'ids' ) );
	$backup[ $id ]['cats']  = wp_get_post_categories( $id );
	$backup[ $id ]['author'] = $p->post_author;
	if ( OB_APPLY ) {
		wp_set_post_categories( $id, array( $CAT['cuisine'] ) );
		ob_dr_set_author( $id, $AUTH['thomas'] );
	}
}

/* 4. Content plans */
foreach ( $PLAN as $cat => $row ) {
	foreach ( $row[1] as $id ) {
		$p = get_post( $id );
		if ( ! $p ) {
			$report['problems'][] = "no plan $id";
			continue;
		}
		$backup[ $id ]['plan'] = array( 'author' => $p->post_author, 'cats' => wp_get_post_categories( $id ), 'terms' => wp_get_object_terms( $id, 'author', array( 'fields' => 'ids' ) ) );
		$report['plans'][ $id ] = array( 'from' => implode( ',', wp_get_post_categories( $id ) ) . '/' . get_the_author_meta( 'display_name', $p->post_author ), 'to' => $cat . '/' . $row[0] );
		if ( OB_APPLY ) {
			wp_set_post_categories( $id, array( $CAT[ $cat ] ) );
			ob_dr_set_author( $id, $AUTH[ $row[0] ] );
		}
	}
}

if ( OB_APPLY ) {
	update_option( 'ob_backup_drafts_20261126', $backup, false );
	do_action( 'litespeed_purge_all' );
	// Verify.
	$v = array();
	foreach ( array_merge( $JOZEF, $THOMAS, array_keys( $report['plans'] ) ) as $id ) {
		$authors = function_exists( 'get_multiple_authors' ) ? wp_list_pluck( get_multiple_authors( $id ), 'display_name' ) : array();
		$v[ $id ] = array( implode( ',', wp_list_pluck( get_the_category( $id ), 'name' ) ), implode( ',', $authors ), get_post_status( $id ) );
	}
	$report['verify'] = $v;
}
return $report;
