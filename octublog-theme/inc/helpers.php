<?php
/**
 * Template helpers: rubrique palette, icons, cards, meta.
 *
 * @package Octublog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Design data for each rubrique, keyed by category slug.
 * Term meta (ob_color, ob_tint, ob_icon, ob_author, ob_spec) overrides any value.
 */
function ob_rubriques() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}
	$defaults = array(
		'enfance-et-famille'         => array( 'key' => 'enf', 'color' => '#EBCFC4', 'tint' => '#F6EAE4', 'icon' => 'baby', 'author' => 'camille-vasseur', 'spec' => 'Enfance & Famille', 'desc' => 'Tout ce dont vous avez besoin pour accompagner vos enfants et profiter de chaque moment en famille : coiffures, chambre d’enfant, activités manuelles, anniversaires et vie de famille au quotidien.' ),
		'inspirations-et-bien-etre'  => array( 'key' => 'bien', 'color' => '#CFD9C6', 'tint' => '#E9EEE4', 'icon' => 'leaf', 'author' => 'lea-simon', 'spec' => 'Bien-être', 'desc' => 'Des citations qui font réfléchir, des phrases pour avancer et des rituels simples pour ralentir, choisis pour ce qu’ils font vraiment au moment où vous les lisez.' ),
		'maison-et-organisation'     => array( 'key' => 'mai', 'color' => '#E3D5BE', 'tint' => '#F2EADD', 'icon' => 'house', 'author' => 'ines-mercier', 'spec' => 'Maison', 'desc' => 'Des idées de rangement cuisine aux astuces pour aménager votre balcon : des solutions pour un chez-vous fonctionnel, beau et bien organisé.' ),
		'vie-digitale-et-tech'       => array( 'key' => 'tech', 'color' => '#D6D0E6', 'tint' => '#EBE8F2', 'icon' => 'laptop', 'author' => 'jozef', 'spec' => 'Tech', 'desc' => 'Fonds d’écran, accessoires iPhone, applications, messageries académiques, outils IA et WordPress : des guides testés pour votre quotidien numérique.' ),
		'loisirs-creatifs-et-dessin' => array( 'key' => 'loi', 'color' => '#F0DFA8', 'tint' => '#F8EFD3', 'icon' => 'palette', 'author' => 'manon-picard', 'spec' => 'Loisirs créatifs', 'desc' => 'Dessin facile, dessin mignon, aquarelle, doodles et projets faits main : des tutoriels classés par niveau, budget et temps, pour tous les débutants.' ),
		'voyage-et-evasion'          => array( 'key' => 'voy', 'color' => '#C6DCE6', 'tint' => '#E6EFF3', 'icon' => 'compass', 'author' => 'chloe-bartoli', 'spec' => 'Voyage', 'desc' => 'Carnets de voyage, photos, illustrations et déco inspirée de vos escapades : de quoi préparer le prochain départ et prolonger le dernier.' ),
	);

	$map = array();
	foreach ( $defaults as $slug => $data ) {
		$term = get_category_by_slug( $slug );
		if ( ! $term ) {
			continue;
		}
		foreach ( array( 'color', 'tint', 'icon', 'author', 'spec' ) as $field ) {
			$meta = get_term_meta( $term->term_id, 'ob_' . $field, true );
			if ( '' !== $meta && false !== $meta ) {
				$data[ $field ] = $meta;
			}
		}
		$data['term'] = $term;
		$data['name'] = $term->name;
		$data['url']  = get_category_link( $term );
		$data['long'] = $term->description ? wp_kses_post( wpautop( $term->description ) ) : '';
		$map[ $slug ] = $data;
	}
	return $map;
}

/**
 * Rubrique data for a category term, with a neutral fallback for other categories.
 */
function ob_rubrique( $term ) {
	$map = ob_rubriques();
	if ( $term && isset( $map[ $term->slug ] ) ) {
		return $map[ $term->slug ];
	}
	return array(
		'key'    => 'mai',
		'color'  => '#E3D5BE',
		'tint'   => '#F2EADD',
		'icon'   => 'house',
		'author' => '',
		'spec'   => '',
		'desc'   => $term ? wp_strip_all_tags( $term->description ) : '',
		'long'   => '',
		'term'   => $term,
		'name'   => $term ? $term->name : '',
		'url'    => $term ? get_category_link( $term ) : home_url( '/' ),
	);
}

/**
 * Main category of a post: Yoast primary category first, then the first known rubrique.
 */
function ob_post_category( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	$primary = (int) get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true );
	if ( $primary ) {
		$term = get_term( $primary, 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			return $term;
		}
	}
	$cats = get_the_category( $post->ID );
	$map  = ob_rubriques();
	foreach ( $cats as $cat ) {
		if ( isset( $map[ $cat->slug ] ) ) {
			return $cat;
		}
	}
	return $cats ? $cats[0] : null;
}

/**
 * Inline style declaring the rubrique colors as custom properties.
 */
function ob_rub_style( $rub ) {
	return sprintf( '--rub:%s;--rub-tint:%s;', esc_attr( $rub['color'] ), esc_attr( $rub['tint'] ) );
}

/**
 * Stroke icon set used across the design (Lucide geometry).
 */
function ob_icon( $name, $size = 20, $class = '' ) {
	$c     = static fn( $x, $y, $r ) => sprintf( 'M%s %sa%s %s 0 1 0 %s 0a%s %s 0 1 0 %s 0', $x - $r, $y, $r, $r, 2 * $r, $r, $r, -2 * $r );
	$icons = array(
		'search'     => array( $c( 11, 11, 8 ), 'm21 21-4.3-4.3' ),
		'menu'       => array( 'M4 6h16', 'M4 12h16', 'M4 18h16' ),
		'house'      => array( 'M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8', 'M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z' ),
		'laptop'     => array( 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16' ),
		'compass'    => array( $c( 12, 12, 10 ), 'M16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88z' ),
		'palette'    => array( 'M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z', $c( 13.5, 6.5, 0.6 ), $c( 17.5, 10.5, 0.6 ), $c( 8.5, 7.5, 0.6 ), $c( 6.5, 12.5, 0.6 ) ),
		'baby'       => array( 'M9 12h.01', 'M15 12h.01', 'M10 16c.5.3 1.2.5 2 .5s1.5-.2 2-.5', 'M19 6.3a9 9 0 0 1 1.8 3.9 2 2 0 0 1 0 3.6 9 9 0 0 1-17.6 0 2 2 0 0 1 0-3.6A9 9 0 0 1 12 3c2 0 3.5 1.1 3.5 2.5s-.9 2.5-2 2.5c-.8 0-1.5-.4-1.5-1' ),
		'leaf'       => array( 'M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z', 'M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12' ),
		'mail'       => array( 'M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z', 'm22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7' ),
		'pin'        => array( 'M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0', $c( 12, 10, 3 ) ),
		'phone'      => array( 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z' ),
		'chevron'    => array( 'm6 9 6 6 6-6' ),
		'x'          => array( 'M18 6 6 18', 'm6 6 12 12' ),
		'arrowUp'    => array( 'm5 12 7-7 7 7', 'M12 19V5' ),
		'arrowRight' => array( 'M5 12h14', 'm12 5 7 7-7 7' ),
		'arrowLeft'  => array( 'M19 12H5', 'm12 19-7-7 7-7' ),
		'clock'      => array( $c( 12, 12, 10 ), 'M12 6v6l4 2' ),
		'link'       => array( 'M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71', 'M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71' ),
		'list'       => array( 'M8 6h13', 'M8 12h13', 'M8 18h13', 'M3 6h.01', 'M3 12h.01', 'M3 18h.01' ),
		'bulb'       => array( 'M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5', 'M9 18h6', 'M10 22h4' ),
		'check'      => array( 'M20 6 9 17l-5-5' ),
		'plus'       => array( 'M12 5v14', 'M5 12h14' ),
		'minus'      => array( 'M5 12h14' ),
		'facebook'   => array( 'M7 10v4h3v7h4v-7h3l1-4h-4V8a1 1 0 0 1 1-1h3V3h-3a5 5 0 0 0-5 5v2H7' ),
		'instagram'  => array( 'M4 8a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z', $c( 12, 12, 3 ), 'M16.5 7.5v.01' ),
		'youtube'    => array( 'M2 8a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4z', 'M10 9l5 3-5 3z' ),
		'pinterest'  => array( 'M8 20l4-9', 'M10.7 14c.437 1.263 1.43 2 2.55 2 2.071 0 3.75-1.554 3.75-4a5 5 0 1 0-9.7 1.7', $c( 12, 12, 9 ) ),
		'tiktok'     => array( 'M21 7.917v4.034a9.948 9.948 0 0 1-5-1.951v4.5a6.5 6.5 0 1 1-8-6.326v4.326a2.5 2.5 0 1 0 4 2V3h4.083A6.005 6.005 0 0 0 21 7.917z' ),
		'threads'    => array( 'M19 7.5C17.667 4.5 15.333 3 12 3c-5 0-8 2.5-8 9s3.5 9 8 9 7-3 7-5-1-5-7-5c-2.5 0-3 1.25-3 2.5 0 1.5 1 2.5 2.5 2.5 2.5 0 3.5-1.5 3.5-5s-2-4-3-4-1.833.333-2.5 1' ),
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	$paths = '';
	foreach ( $icons[ $name ] as $d ) {
		$paths .= '<path d="' . esc_attr( $d ) . '"/>';
	}
	return sprintf(
		'<svg class="ob-icon %3$s" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		(int) $size,
		$paths,
		esc_attr( $class )
	);
}

/**
 * Author displayed for a post: PublishPress Authors first, WordPress author otherwise.
 * Returns name, url, avatar, bio and slug.
 */
function ob_post_author( $post = null ) {
	$post = get_post( $post );
	if ( function_exists( 'get_post_authors' ) ) {
		$authors = get_post_authors( $post->ID );
		if ( ! empty( $authors ) && is_object( $authors[0] ) ) {
			return ob_author_data( $authors[0] );
		}
	}
	return ob_author_data( get_userdata( $post->post_author ) );
}

/**
 * Normalise a PublishPress author or a WP_User into a plain array.
 */
function ob_author_data( $author ) {
	if ( ! $author ) {
		return array( 'name' => '', 'url' => '', 'avatar' => '', 'bio' => '', 'slug' => '', 'user_id' => 0 );
	}
	if ( $author instanceof WP_User ) {
		return array(
			'name'    => $author->display_name,
			'url'     => get_author_posts_url( $author->ID ),
			'avatar'  => get_avatar_url( $author->ID, array( 'size' => 192 ) ),
			'bio'     => $author->description,
			'slug'    => $author->user_nicename,
			'user_id' => $author->ID,
		);
	}
	$avatar = method_exists( $author, 'get_avatar_url' ) ? $author->get_avatar_url( 192 ) : '';
	if ( is_array( $avatar ) ) {
		$avatar = $avatar['url'] ?? '';
	}
	return array(
		'name'    => $author->display_name,
		'url'     => $author->link,
		'avatar'  => $avatar,
		'bio'     => $author->description,
		'slug'    => $author->slug,
		'user_id' => (int) $author->user_id,
	);
}

/**
 * Author data from an author slug (PublishPress term slug or user nicename).
 */
function ob_author_by_slug( $slug ) {
	if ( ! $slug ) {
		return null;
	}
	if ( class_exists( '\MultipleAuthors\Classes\Objects\Author' ) ) {
		$author = \MultipleAuthors\Classes\Objects\Author::get_by_term_slug( $slug );
		if ( $author ) {
			return ob_author_data( $author );
		}
	}
	$user = get_user_by( 'slug', $slug );
	return $user ? ob_author_data( $user ) : null;
}

/**
 * Avatar image, with an initials disc when no picture is set.
 */
function ob_avatar( $author, $size = 32, $class = '' ) {
	$name = $author['name'] ?? '';
	$url  = $author['avatar'] ?? '';
	if ( ! $url || false !== strpos( $url, 'gravatar.com' ) ) {
		$initials = '';
		foreach ( preg_split( '/\s+/', trim( $name ) ) as $part ) {
			$initials .= mb_substr( $part, 0, 1 );
		}
		return sprintf(
			'<span class="ob-avatar ob-avatar--initials %3$s" style="width:%1$dpx;height:%1$dpx;font-size:%4$dpx" aria-hidden="true">%2$s</span>',
			(int) $size,
			esc_html( mb_strtoupper( mb_substr( $initials, 0, 2 ) ) ),
			esc_attr( $class ),
			max( 11, (int) round( $size * 0.38 ) )
		);
	}
	return sprintf(
		'<img class="ob-avatar %4$s" src="%1$s" alt="%2$s" width="%3$d" height="%3$d" loading="lazy" decoding="async">',
		esc_url( $url ),
		esc_attr( $name ),
		(int) $size,
		esc_attr( $class )
	);
}

/**
 * French long date, e.g. "15 août 2026".
 */
function ob_date( $post = null, $modified = false ) {
	return $modified ? get_the_modified_date( 'j F Y', $post ) : get_the_date( 'j F Y', $post );
}

/**
 * Reading time in minutes (230 words per minute).
 */
function ob_reading_time( $post = null ) {
	$post  = get_post( $post );
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
	return max( 1, (int) round( $words / 230 ) );
}

/**
 * Short text describing a post: manual excerpt, Yoast description, then the opening words.
 */
function ob_summary( $post = null, $words = 32 ) {
	$post = get_post( $post );
	if ( has_excerpt( $post ) ) {
		return wp_trim_words( $post->post_excerpt, $words, '…' );
	}
	$desc = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
	if ( $desc && false === strpos( $desc, '%%' ) ) {
		return wp_trim_words( $desc, $words, '…' );
	}
	return wp_trim_words( excerpt_remove_blocks( $post->post_content ), $words, '…' );
}

/**
 * Responsive featured image markup.
 */
function ob_thumb( $post, $size = 'medium_large', $sizes = '(min-width: 1100px) 400px, (min-width: 700px) 50vw, 100vw', $attrs = array() ) {
	$id = get_post_thumbnail_id( $post );
	if ( ! $id ) {
		return '';
	}
	$attrs = array_merge(
		array(
			'sizes'    => $sizes,
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => get_the_title( $post ),
		),
		$attrs
	);
	return wp_get_attachment_image( $id, $size, false, $attrs );
}

/**
 * Article card.
 *
 * @param WP_Post|int $post Post.
 * @param array       $args quote (bool) renders the citation card variant.
 */
function ob_card( $post, $args = array() ) {
	get_template_part( 'template-parts/card', null, array_merge( array( 'post' => get_post( $post ) ), $args ) );
}

/**
 * Numbered section heading: big gold number plus title.
 */
function ob_section_head( $num, $title, $kicker = '', $blob = '', $tag = 'h2' ) {
	?>
	<div class="ob-shead">
		<span class="ob-shead__num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
		<div class="ob-shead__text">
			<?php if ( $kicker ) : ?>
				<span class="ob-kicker"><?php echo esc_html( $kicker ); ?></span>
			<?php endif; ?>
			<div class="ob-shead__title<?php echo $blob ? ' has-blob' : ''; ?>"<?php echo $blob ? ' style="--blob:' . esc_attr( $blob ) . '"' : ''; ?>>
				<<?php echo tag_escape( $tag ); ?> class="ob-h2"><?php echo esc_html( $title ); ?></<?php echo tag_escape( $tag ); ?>>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Pagination in the design style.
 */
function ob_pagination( $query = null ) {
	$query = $query ?: $GLOBALS['wp_query'];
	if ( $query->max_num_pages < 2 ) {
		return;
	}
	$links = paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'mid_size'  => 1,
			'end_size'  => 1,
			'type'      => 'array',
			'prev_text' => ob_icon( 'arrowLeft', 16 ) . '<span>' . esc_html__( 'Précédent', 'octublog' ) . '</span>',
			'next_text' => '<span>' . esc_html__( 'Suivant', 'octublog' ) . '</span>' . ob_icon( 'arrowRight', 16 ),
		)
	);
	if ( ! $links ) {
		return;
	}
	echo '<nav class="ob-pagination" aria-label="' . esc_attr__( 'Pagination', 'octublog' ) . '">';
	foreach ( $links as $link ) {
		echo $link; // phpcs:ignore WordPress.Security.EscapeOutput -- core markup.
	}
	echo '</nav>';
}

/**
 * Social profiles from Yoast SEO, keyed by icon name.
 */
function ob_socials() {
	$social = get_option( 'wpseo_social', array() );
	$urls   = array_filter( array_merge( array( $social['facebook_site'] ?? '' ), (array) ( $social['other_social_urls'] ?? array() ) ) );
	$known  = array(
		'facebook'  => array( 'facebook.com', 'Facebook' ),
		'instagram' => array( 'instagram.com', 'Instagram' ),
		'youtube'   => array( 'youtube.com', 'YouTube' ),
		'pinterest' => array( 'pinterest.', 'Pinterest' ),
		'tiktok'    => array( 'tiktok.com', 'TikTok' ),
		'threads'   => array( 'threads.', 'Threads' ),
	);
	$out = array();
	foreach ( $known as $icon => $match ) {
		foreach ( $urls as $url ) {
			if ( false !== stripos( $url, $match[0] ) ) {
				$out[ $icon ] = array( 'url' => $url, 'label' => $match[1] );
				break;
			}
		}
	}
	return $out;
}

/**
 * Share links for the current post.
 */
function ob_share_links( $post = null ) {
	$post  = get_post( $post );
	$url   = rawurlencode( get_permalink( $post ) );
	$title = rawurlencode( html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ) );
	$img   = rawurlencode( (string) get_the_post_thumbnail_url( $post, 'full' ) );
	return array(
		array( 'icon' => 'pinterest', 'label' => 'Pinterest', 'url' => "https://pinterest.com/pin/create/button/?url={$url}&media={$img}&description={$title}" ),
		array( 'icon' => 'facebook', 'label' => 'Facebook', 'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}" ),
		array( 'icon' => 'mail', 'label' => 'E-mail', 'url' => "mailto:?subject={$title}&body={$url}" ),
		array( 'icon' => 'link', 'label' => __( 'Copier le lien', 'octublog' ), 'url' => get_permalink( $post ), 'copy' => true ),
	);
}

/**
 * URL of the white footer logo, falling back to the custom logo.
 */
function ob_logo_url( $variant = 'dark' ) {
	if ( 'light' === $variant ) {
		$id = (int) get_theme_mod( 'ob_logo_light', 8597 );
		$src = $id ? wp_get_attachment_image_url( $id, 'full' ) : '';
		if ( $src ) {
			return $src;
		}
	}
	$id = (int) get_theme_mod( 'custom_logo', 0 );
	if ( ! $id ) {
		$id = (int) get_option( 'ob_logo_id', 8488 );
	}
	return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '';
}
