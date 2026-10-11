<?php
/**
 * Homepage sections, placed in the "Accueil" page as shortcode blocks.
 * Texts are shortcode attributes (title, button, quote ...): edit them in the block.
 *
 * @package Octublog
 */

/** Posts already shown on the page, so no article appears twice. */
function ob_home_take( $args ) {
	if ( ! isset( $GLOBALS['ob_home'] ) ) {
		$GLOBALS['ob_home'] = array( 'shown' => array(), 'num' => 3 );
	}
	$query = new WP_Query(
		array_merge(
			array(
				'post_type'           => 'post',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'post__not_in'        => $GLOBALS['ob_home']['shown'],
			),
			$args
		)
	);
	foreach ( $query->posts as $p ) {
		$GLOBALS['ob_home']['shown'][] = $p->ID;
	}
	return $query->posts;
}

/** [ob_home_hero title="À la une" recent="Récemment publiés"] */
add_shortcode( 'ob_home_hero', function ( $a ) {
	$GLOBALS['ob_home'] = array( 'shown' => array(), 'num' => 3 );
	$a = shortcode_atts( array( 'title' => 'À la une', 'recent' => 'Récemment publiés' ), $a );
	$ob_sticky = get_option( 'sticky_posts' );
	$ob_hero   = $ob_sticky ? ob_home_take( array( 'post__in' => $ob_sticky, 'posts_per_page' => 1 ) ) : array();
	if ( ! $ob_hero ) {
		$ob_hero = ob_home_take( array( 'posts_per_page' => 1 ) );
	}
	$ob_hero   = $ob_hero ? $ob_hero[0] : null;
	$ob_recent = ob_home_take( array( 'posts_per_page' => 3 ) );
	ob_start();
	?>
<section class="ob-wrap ob-hero">
	<div class="ob-shead ob-shead--rule">
		<span class="ob-shead__num" aria-hidden="true">01</span>
		<h2 class="ob-h2 ob-nowrap"><?php echo esc_html( $a['title'] ); ?></h2>
		<span class="ob-shead__rule" aria-hidden="true"></span>
	</div>

	<div class="ob-hero__grid">
		<?php
		if ( $ob_hero ) :
			$ob_cat    = ob_post_category( $ob_hero );
			$ob_rub    = ob_rubrique( $ob_cat );
			$ob_author = ob_post_author( $ob_hero );
			?>
			<article class="ob-hero__main" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
				<a class="ob-media ob-media--zoom" href="<?php echo esc_url( get_permalink( $ob_hero ) ); ?>" tabindex="-1" aria-hidden="true">
					<?php echo ob_thumb( $ob_hero, 'full', '(min-width: 1100px) 760px, 100vw', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore ?>
				</a>
				<?php if ( $ob_cat ) : ?>
					<a class="ob-pill" href="<?php echo esc_url( get_category_link( $ob_cat ) ); ?>"><?php echo esc_html( $ob_cat->name ); ?></a>
				<?php endif; ?>
				<h1 class="ob-hero__title"><a href="<?php echo esc_url( get_permalink( $ob_hero ) ); ?>"><?php echo esc_html( get_the_title( $ob_hero ) ); ?></a></h1>
				<p class="ob-hero__excerpt"><?php echo esc_html( ob_summary( $ob_hero, 40 ) ); ?></p>
				<div class="ob-meta">
					<?php echo ob_avatar( $ob_author, 40 ); // phpcs:ignore ?>
					<span><?php esc_html_e( 'Par', 'octublog' ); ?> <strong><?php echo esc_html( $ob_author['name'] ); ?></strong></span>
					<span aria-hidden="true">·</span>
					<time datetime="<?php echo esc_attr( get_the_date( 'c', $ob_hero ) ); ?>"><?php echo esc_html( ob_date( $ob_hero ) ); ?></time>
				</div>
			</article>
		<?php endif; ?>

		<div class="ob-recent">
			<div class="ob-recent__title"><?php echo esc_html( $a['recent'] ); ?></div>
			<?php
			foreach ( $ob_recent as $ob_p ) :
				$ob_cat    = ob_post_category( $ob_p );
				$ob_rub    = ob_rubrique( $ob_cat );
				$ob_author = ob_post_author( $ob_p );
				?>
				<article class="ob-row" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
					<a class="ob-media ob-row__thumb" href="<?php echo esc_url( get_permalink( $ob_p ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo ob_thumb( $ob_p, 'medium_large', '200px' ); // phpcs:ignore ?></a>
					<div class="ob-row__body">
						<?php if ( $ob_cat ) : ?>
							<span class="ob-catdot"><span class="ob-catdot__dot"></span><?php echo esc_html( $ob_cat->name ); ?></span>
						<?php endif; ?>
						<h3 class="ob-row__title"><a href="<?php echo esc_url( get_permalink( $ob_p ) ); ?>"><?php echo esc_html( get_the_title( $ob_p ) ); ?></a></h3>
						<span class="ob-small"><?php echo esc_html( $ob_author['name'] . ' · ' . ob_date( $ob_p ) ); ?></span>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
	<?php
	return ob_get_clean();
} );

/** [ob_home_cats] Rubrique cards. */
add_shortcode( 'ob_home_cats', function () {
	$ob_rubs = ob_rubriques();
	ob_start();
	?>
<section class="ob-band" style="--band:var(--ob-cream)">
	<div class="ob-wrap ob-section ob-section--s">
		<div class="ob-catgrid">
			<?php
			foreach ( $ob_rubs as $ob_rub ) :
				$ob_cover = get_posts(
					array(
						'cat'            => $ob_rub['term']->term_id,
						'posts_per_page' => 1,
						'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery
						'no_found_rows'  => true,
					)
				);
				?>
				<a class="ob-catcard" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
					<span class="ob-media ob-catcard__media"><?php echo $ob_cover ? ob_thumb( $ob_cover[0], 'medium_large', '(min-width: 1100px) 200px, 50vw', array( 'alt' => '' ) ) : ''; // phpcs:ignore ?></span>
					<span class="ob-catcard__icon"><?php echo ob_icon( $ob_rub['icon'], 20 ); // phpcs:ignore ?></span>
					<span class="ob-catcard__text">
						<span class="ob-catcard__name"><?php echo esc_html( $ob_rub['name'] ); ?></span>
						<span class="ob-catcard__count"><?php echo esc_html( sprintf( /* translators: %s number */ _n( '%s article', '%s articles', $ob_rub['term']->count, 'octublog' ), number_format_i18n( $ob_rub['term']->count ) ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
	<?php
	return ob_get_clean();
} );

/** [ob_home_latest title="Derniers articles" button="Voir plus d’articles" count="3"] */
add_shortcode( 'ob_home_latest', function ( $a ) {
	$a = shortcode_atts( array( 'title' => 'Derniers articles', 'button' => 'Voir plus d’articles', 'count' => 3 ), $a );
	$ob_latest = ob_home_take( array( 'posts_per_page' => max( 1, min( 12, (int) $a['count'] ) ) ) );
	ob_start();
	?>
<section class="ob-wrap ob-section">
	<?php ob_section_head( '02', $a['title'] ); ?>
	<div class="ob-grid ob-grid--3 ob-grid--tall">
		<?php
		foreach ( $ob_latest as $ob_p ) {
			ob_card( $ob_p );
		}
		?>
	</div>
	<div class="ob-center ob-mt-56">
		<a class="ob-btn ob-btn--outline" href="<?php echo esc_url( get_pagenum_link( 2 ) ); ?>"><?php echo esc_html( $a['button'] ); ?></a>
	</div>
</section>
	<?php
	return ob_get_clean();
} );

/**
 * [ob_home_rubrique slug="vie-digitale-et-tech" layout="grid|feature|quotes" seeall="Voir tout" quote=""]
 * A non-empty quote adds the quote band above the section (author of the rubrique).
 */
add_shortcode( 'ob_home_rubrique', function ( $a ) {
	$a = shortcode_atts( array( 'slug' => '', 'layout' => 'grid', 'seeall' => 'Voir tout', 'quote' => '', 'count' => 3 ), $a );
	$ob_rubs = ob_rubriques();
	if ( empty( $ob_rubs[ $a['slug'] ] ) ) {
		return '';
	}
	$ob_rub    = $ob_rubs[ $a['slug'] ];
	$ob_owner  = ob_author_by_slug( $ob_rub['author'] );
	$ob_items  = ob_home_take( array( 'cat' => $ob_rub['term']->term_id, 'posts_per_page' => max( 2, min( 4, (int) $a['count'] ) ) ) );
	if ( ! $ob_items ) {
		return '';
	}
	$ob_feature = 'feature' === $a['layout'];

	ob_start();
	?>
	<?php
	if ( '' !== $a['quote'] && $ob_owner ) :
		$ob_quote = $a['quote'];
		?>
		<section class="ob-quote">
			<div class="ob-quote__inner">
				<?php echo ob_avatar( $ob_owner, 160, 'ob-avatar--white ob-quote__av' ); // phpcs:ignore ?>
				<figure>
					<blockquote>« <?php echo esc_html( $ob_quote ); ?> »</blockquote>
					<figcaption><strong><?php echo esc_html( $ob_owner['name'] ); ?></strong> · <?php echo esc_html( $ob_rub['name'] ); ?></figcaption>
				</figure>
			</div>
		</section>
	<?php endif; ?>

	<section class="ob-band" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>--band:<?php echo esc_attr( $ob_feature ? 'var(--ob-bg)' : $ob_rub['tint'] ); ?>">
		<div class="ob-wrap ob-section">
			<div class="ob-shead-row">
				<?php
				ob_section_head(
					sprintf( '%02d', $GLOBALS['ob_home']['num']++ ),
					$ob_rub['name'],
					$ob_owner ? sprintf( /* translators: %s author */ __( 'Par %s', 'octublog' ), $ob_owner['name'] ) : '',
					$ob_feature ? $ob_rub['color'] : ''
				);
				?>
				<a class="ob-seeall" href="<?php echo esc_url( $ob_rub['url'] ); ?>"><?php echo esc_html( $a['seeall'] ); ?> →</a>
			</div>

			<?php if ( $ob_feature ) : ?>
				<div class="ob-feature">
					<?php ob_card( $ob_items[0], array( 'heading' => 'h3' ) ); ?>
					<div class="ob-feature__list">
						<?php
						foreach ( array_slice( $ob_items, 1 ) as $ob_p ) :
							$ob_author = ob_post_author( $ob_p );
							?>
							<article class="ob-row ob-row--center">
								<a class="ob-media ob-row__thumb ob-row__thumb--l" href="<?php echo esc_url( get_permalink( $ob_p ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo ob_thumb( $ob_p, 'medium_large', '200px' ); // phpcs:ignore ?></a>
								<div class="ob-row__body">
									<h3 class="ob-row__title"><a href="<?php echo esc_url( get_permalink( $ob_p ) ); ?>"><?php echo esc_html( get_the_title( $ob_p ) ); ?></a></h3>
									<span class="ob-small"><?php echo esc_html( $ob_author['name'] . ' · ' . ob_date( $ob_p ) ); ?></span>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php else : ?>
				<div class="ob-grid ob-grid--<?php echo (int) min( 4, max( 3, count( $ob_items ) ) ); ?> ob-grid--scroll">
					<?php
					foreach ( $ob_items as $ob_p ) {
						echo '<div class="ob-grid__cell">';
						ob_card( $ob_p, array( 'quote' => 'quotes' === $a['layout'] ) );
						echo '</div>';
					}
					?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );

/** [ob_home_newsletter] */
add_shortcode( 'ob_home_newsletter', function () {
	ob_start();
	?>
	<section class="ob-wrap ob-section ob-section--nl-home">
		<?php get_template_part( 'template-parts/newsletter' ); ?>
	</section>
	<?php
	return ob_get_clean();
} );

/** [ob_home_team title="Nos expertes et experts" text="..."] */
add_shortcode( 'ob_home_team', function ( $a ) {
	$a = shortcode_atts(
		array(
			'title' => 'Nos expertes et experts',
			'text'  => 'Chaque rubrique est tenue par une personne qui la pratique au quotidien.',
		),
		$a
	);
	$ob_rubs = ob_rubriques();
	ob_start();
	?>
<section class="ob-wrap ob-section">
	<div class="ob-shead ob-shead--center">
		<span class="ob-shead__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $GLOBALS['ob_home']['num'] ) ); ?></span>
		<h2 class="ob-h2"><?php echo esc_html( $a['title'] ); ?></h2>
		<p><?php echo esc_html( $a['text'] ); ?></p>
	</div>
	<div class="ob-team">
		<?php
		$ob_order = array( 'enfance-et-famille', 'inspirations-et-bien-etre', 'maison-et-organisation', 'loisirs-creatifs-et-dessin', 'voyage-et-evasion', 'vie-digitale-et-tech' );
		foreach ( $ob_order as $ob_slug ) :
			if ( empty( $ob_rubs[ $ob_slug ] ) ) {
				continue;
			}
			$ob_person = ob_author_by_slug( $ob_rubs[ $ob_slug ]['author'] );
			if ( ! $ob_person ) {
				continue;
			}
			?>
			<a class="ob-team__item" href="<?php echo esc_url( $ob_person['url'] ); ?>">
				<?php echo ob_avatar( $ob_person, 128, 'ob-avatar--gold' ); // phpcs:ignore ?>
				<span class="ob-team__name"><?php echo esc_html( $ob_person['name'] ); ?></span>
				<span class="ob-team__spec"><?php echo esc_html( $ob_rubs[ $ob_slug ]['spec'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
	<?php
	return ob_get_clean();
} );

/** Default blocks of the "Accueil" page (also used when the page has no content). */
function ob_home_default_blocks() {
	$sc = function ( $code ) {
		return "<!-- wp:shortcode -->\n$code\n<!-- /wp:shortcode -->\n\n";
	};
	return $sc( '[ob_home_hero title="À la une" recent="Récemment publiés"]' )
		. $sc( '[ob_strip]' )
		. $sc( '[ob_home_latest title="Derniers articles" button="Voir plus d’articles" count="3"]' )
		. $sc( '[ob_home_rubrique slug="vie-digitale-et-tech" layout="grid" seeall="Voir tout" count="3"]' )
		. $sc( '[ob_home_rubrique slug="maison-et-organisation" layout="grid" seeall="Voir tout" count="3"]' )
		. $sc( '[ob_home_rubrique slug="enfance-et-famille" layout="feature" seeall="Voir tout" count="3" quote="Il n’existe pas de méthode parentale universelle."]' )
		. $sc( '[ob_home_rubrique slug="inspirations-et-bien-etre" layout="quotes" seeall="Voir tout" count="3"]' )
		. $sc( '[ob_home_newsletter]' )
		. $sc( '[ob_home_team title="Nos expertes et experts" text="Chaque rubrique est tenue par une personne qui la pratique au quotidien."]' );
}

/* The homepage URL is not the Accueil page's own URL: purge the whole cache when it is saved. */
add_action( 'save_post_page', function ( $post_id ) {
	if ( 'accueil' === get_post_field( 'post_name', $post_id ) ) {
		do_action( 'litespeed_purge_all' );
	}
}, 20 );
