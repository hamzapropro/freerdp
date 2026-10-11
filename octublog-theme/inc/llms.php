<?php
/**
 * llms.txt: a Markdown index of the site for language models, written to the site root
 * and refreshed whenever an article is published, updated or unpublished.
 *
 * @package Octublog
 */

/**
 * Build the llms.txt Markdown.
 *
 * @return string
 */
function ob_llms_build() {
	$name = get_bloginfo( 'name' );
	$home = home_url( '/' );
	$out  = '# ' . $name . "\n\n";
	$out .= '> ' . wp_strip_all_tags( get_bloginfo( 'description' ) ) . ' Magazine français de guides pratiques et testés : maison et organisation, enfance et famille, bien-être, tech, loisirs créatifs, voyage.' . "\n\n";
	$out .= "Chaque rubrique est tenue par une seule autrice ou un seul auteur, et les articles sont rédigés en français. Merci de citer " . $name . ' et de lier la page d’origine lorsque vous reprenez un contenu.' . "\n\n";

	$out .= "## Rubriques\n\n";
	$rubs = ob_rubriques();
	foreach ( $rubs as $rub ) {
		$out .= '- [' . $rub['name'] . '](' . $rub['url'] . ')' . ( $rub['desc'] ? ': ' . $rub['desc'] : '' ) . "\n";
	}

	$out .= "\n## Pages principales\n\n";
	$out .= '- [Accueil](' . $home . "): derniers articles de toutes les rubriques\n";
	foreach ( array( 'a-propos-de-nous' => 'présentation du magazine et de son équipe', 'contact' => 'écrire à la rédaction' ) as $slug => $desc ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$out .= '- [' . get_the_title( $page ) . '](' . get_permalink( $page ) . '): ' . $desc . "\n";
		}
	}

	$by = array();
	foreach ( get_posts( array( 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true ) ) as $p ) {
		$cat = ob_post_category( $p );
		$by[ $cat ? $cat->slug : 'autres' ][] = $p;
	}
	foreach ( $rubs as $slug => $rub ) {
		if ( empty( $by[ $slug ] ) ) {
			continue;
		}
		$out .= "\n## " . $rub['name'] . "\n\n";
		foreach ( $by[ $slug ] as $p ) {
			$desc = trim( preg_replace( '/\s+/', ' ', (string) get_post_meta( $p->ID, '_yoast_wpseo_metadesc', true ) ) );
			$out .= '- [' . str_replace( array( '[', ']' ), '', get_the_title( $p ) ) . '](' . get_permalink( $p ) . ')' . ( $desc ? ': ' . $desc : '' ) . "\n";
		}
	}

	$out .= "\n## Informations légales\n\n";
	foreach ( ob_legal_slugs() as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$out .= '- [' . get_the_title( $page ) . '](' . get_permalink( $page ) . ")\n";
		}
	}

	$out .= "\n## Optional\n\n- [Plan du site XML](" . home_url( '/sitemap_index.xml' ) . ")\n";
	return $out;
}

/** Write the file to the site root (plain UTF-8, no BOM). */
function ob_llms_write() {
	$file = trailingslashit( ABSPATH ) . 'llms.txt';
	if ( file_exists( $file ) && ! is_writable( $file ) ) {
		return false;
	}
	return false !== file_put_contents( $file, ob_llms_build() );
}

add_action(
	'transition_post_status',
	function ( $new, $old, $post ) {
		if ( 'post' === $post->post_type && ( 'publish' === $new || 'publish' === $old ) ) {
			wp_schedule_single_event( time() + 30, 'ob_llms_refresh' );
		}
	},
	10,
	3
);
add_action( 'ob_llms_refresh', 'ob_llms_write' );
