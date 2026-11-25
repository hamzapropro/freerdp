<?php
/**
 * Automatic parts of the About and Contact pages, placed in the editor as shortcode blocks.
 *
 * @package Octublog
 */

/** [ob_mosaic] Four cover images, one per main rubrique. */
add_shortcode(
	'ob_mosaic',
	function () {
		$rubs  = ob_rubriques();
		$cover = array();
		foreach ( array( 'maison-et-organisation', 'enfance-et-famille', 'inspirations-et-bien-etre', 'voyage-et-evasion' ) as $slug ) {
			if ( empty( $rubs[ $slug ] ) ) {
				continue;
			}
			$found = get_posts(
				array(
					'cat'            => $rubs[ $slug ]['term']->term_id,
					'posts_per_page' => 1,
					'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery
					'no_found_rows'  => true,
				)
			);
			if ( $found ) {
				$cover[] = $found[0];
			}
		}
		if ( ! $cover ) {
			return '';
		}
		$html = '<div class="ob-mosaic" aria-hidden="true">';
		foreach ( $cover as $i => $p ) {
			$html .= '<span class="ob-media ob-mosaic__item ob-mosaic__item--' . (int) $i . '">' . ob_thumb( $p, 'medium_large', '(min-width: 1100px) 300px, 50vw', array( 'alt' => '' ) ) . '</span>';
		}
		return $html . '</div>';
	}
);

/** [ob_strip] Link strip with the six rubriques. */
add_shortcode(
	'ob_strip',
	function () {
		$html = '<div class="ob-strip"><div class="ob-strip__inner">';
		foreach ( ob_rubriques() as $rub ) {
			$html .= '<a href="' . esc_url( $rub['url'] ) . '">' . esc_html( $rub['name'] ) . '<span class="ob-strip__dot" aria-hidden="true"></span></a>';
		}
		return $html . '</div></div>';
	}
);

/** [ob_team] One card per rubrique author. */
add_shortcode(
	'ob_team',
	function () {
		$html = '<div class="ob-teamgrid">';
		foreach ( ob_rubriques() as $rub ) {
			$person = ob_author_by_slug( $rub['author'] );
			if ( ! $person ) {
				continue;
			}
			$html .= '<a class="ob-teamcard" href="' . esc_url( $person['url'] ) . '" style="' . ob_rub_style( $rub ) . '">'
				. '<span class="ob-teamcard__top">' . ob_avatar( $person, 72, 'ob-avatar--gold' )
				. '<span><strong class="ob-teamcard__name">' . esc_html( $person['name'] ) . '</strong><span class="ob-pill">' . esc_html( $rub['name'] ) . '</span></span></span>';
			if ( $person['bio'] ) {
				$html .= '<span class="ob-teamcard__bio">' . esc_html( wp_trim_words( $person['bio'], 26, '…' ) ) . '</span>';
			}
			$html .= '</a>';
		}
		return $html . '</div>';
	}
);

/**
 * [ob_contact_info emails="a@x.com|b@x.com" address="Ligne 1|Ligne 2" phone="" ]
 * Lines are separated by "|". An empty phone hides the row.
 */
add_shortcode(
	'ob_contact_info',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'emails'  => 'contact@octublog.com|admin@octublog.com',
				'address' => '8 Place Bellecour|69002 Lyon, France',
				'phone'   => '',
			),
			$atts
		);
		$split = function ( $v ) {
			return array_values( array_filter( array_map( 'trim', explode( '|', (string) $v ) ) ) );
		};
		$rows = array(
			array( 'icon' => 'mail', 'label' => __( 'E-mail', 'octublog' ), 'lines' => $split( $atts['emails'] ), 'mail' => true ),
			array( 'icon' => 'pin', 'label' => __( 'Adresse', 'octublog' ), 'lines' => $split( $atts['address'] ) ),
			array( 'icon' => 'phone', 'label' => __( 'Téléphone', 'octublog' ), 'lines' => $split( $atts['phone'] ) ),
		);
		$html = '<div class="ob-infocard">';
		foreach ( $rows as $row ) {
			if ( ! $row['lines'] ) {
				continue;
			}
			$html .= '<div class="ob-infocard__row"><span class="ob-infocard__icon">' . ob_icon( $row['icon'], 18 ) . '</span><div class="ob-infocard__text"><span class="ob-eyebrow ob-eyebrow--gold">' . esc_html( $row['label'] ) . '</span>';
			foreach ( $row['lines'] as $line ) {
				if ( ! empty( $row['mail'] ) ) {
					$html .= '<a href="mailto:' . esc_attr( antispambot( $line ) ) . '">' . esc_html( antispambot( $line ) ) . '</a>';
				} else {
					$html .= '<span>' . esc_html( $line ) . '</span>';
				}
			}
			$html .= '</div></div>';
		}
		$html .= '<div class="ob-socials ob-infocard__socials">';
		foreach ( ob_socials() as $name => $social ) {
			$html .= '<a class="ob-socials__link" href="' . esc_url( $social['url'] ) . '" target="_blank" rel="noopener me" aria-label="' . esc_attr( $social['label'] ) . '">' . ob_icon( $name, 17 ) . '</a>';
		}
		return $html . '</div></div>';
	}
);

/** [ob_authors_links] E-mail links to the rubrique authors. */
add_shortcode(
	'ob_authors_links',
	function () {
		$html = '';
		foreach ( ob_rubriques() as $rub ) {
			$person = ob_author_by_slug( $rub['author'] );
			$user   = $person && $person['user_id'] ? get_userdata( $person['user_id'] ) : null;
			if ( ! $person || ! $user || false === strpos( $user->user_email, '@octublog.com' ) ) {
				continue;
			}
			$html .= '<a class="ob-authorlist__item" href="mailto:' . esc_attr( antispambot( $user->user_email ) ) . '" style="' . ob_rub_style( $rub ) . '">'
				. ob_avatar( $person, 44, 'ob-avatar--rub' )
				. '<span><strong>' . esc_html( $person['name'] ) . ' <em>· ' . esc_html( $rub['spec'] ) . '</em></strong><small>' . esc_html( antispambot( $user->user_email ) ) . '</small></span></a>';
		}
		return $html;
	}
);

/** [ob_sitemap] Human-readable site map: rubriques with their articles, then the pages. */
add_shortcode(
	'ob_sitemap',
	function () {
		$by = array();
		foreach ( get_posts( array( 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true ) ) as $p ) {
			$cat = ob_post_category( $p );
			$by[ $cat ? $cat->slug : 'autres' ][] = $p;
		}
		$html = '<div class="ob-sitemap">';
		foreach ( ob_rubriques() as $slug => $rub ) {
			if ( empty( $by[ $slug ] ) ) {
				continue;
			}
			$html .= '<section class="ob-sitemap__block" style="' . ob_rub_style( $rub ) . '">'
				. '<h2 class="ob-sitemap__title"><a href="' . esc_url( $rub['url'] ) . '">' . esc_html( $rub['name'] ) . '</a> <span>' . count( $by[ $slug ] ) . '</span></h2><ul>';
			foreach ( $by[ $slug ] as $p ) {
				$html .= '<li><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ) ) . '</a></li>';
			}
			$html .= '</ul></section>';
		}
		$pages = get_pages( array( 'sort_column' => 'menu_order,post_title', 'exclude' => array_filter( array( (int) get_option( 'page_on_front' ), (int) ( get_page_by_path( 'accueil' )->ID ?? 0 ), (int) ( get_page_by_path( 'plan-du-site' )->ID ?? 0 ) ) ) ) );
		if ( $pages ) {
			$html .= '<section class="ob-sitemap__block"><h2 class="ob-sitemap__title">' . esc_html__( 'Pages', 'octublog' ) . '</h2><ul>';
			foreach ( $pages as $page ) {
				$html .= '<li><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( get_the_title( $page ) ) . '</a></li>';
			}
			$html .= '</ul></section>';
		}
		return $html . '</div>';
	}
);

/* Keep the site map page fresh when an article is published or changed. */
add_action(
	'transition_post_status',
	function ( $new, $old, $post ) {
		if ( 'post' === $post->post_type && ( 'publish' === $new || 'publish' === $old ) ) {
			$page = get_page_by_path( 'plan-du-site' );
			if ( $page ) {
				do_action( 'litespeed_purge_post', $page->ID );
			}
		}
	},
	10,
	3
);
