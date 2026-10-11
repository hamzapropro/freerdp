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

$ob_rubs  = ob_rubriques();
$ob_shown = array();

$ob_take = function ( $args ) use ( &$ob_shown ) {
	$query = new WP_Query(
		array_merge(
			array(
				'post_type'           => 'post',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'post__not_in'        => $ob_shown,
			),
			$args
		)
	);
	foreach ( $query->posts as $p ) {
		$ob_shown[] = $p->ID;
	}
	return $query->posts;
};

$ob_sticky = get_option( 'sticky_posts' );
$ob_hero   = $ob_sticky ? $ob_take( array( 'post__in' => $ob_sticky, 'posts_per_page' => 1 ) ) : array();
if ( ! $ob_hero ) {
	$ob_hero = $ob_take( array( 'posts_per_page' => 1 ) );
}
$ob_hero   = $ob_hero ? $ob_hero[0] : null;
$ob_recent = $ob_take( array( 'posts_per_page' => 3 ) );
$ob_latest = $ob_take( array( 'posts_per_page' => 6 ) );

// Rubrique sections, in the order of the design.
$ob_sections = array(
	array( 'slug' => 'vie-digitale-et-tech', 'layout' => 'grid' ),
	array( 'slug' => 'maison-et-organisation', 'layout' => 'grid' ),
	array( 'slug' => 'enfance-et-famille', 'layout' => 'feature', 'quote' => true ),
	array( 'slug' => 'inspirations-et-bien-etre', 'layout' => 'quotes' ),
);
?>

<section class="ob-wrap ob-hero">
	<div class="ob-shead ob-shead--rule">
		<span class="ob-shead__num" aria-hidden="true">01</span>
		<h2 class="ob-h2 ob-nowrap"><?php echo esc_html( ob_t( 'home', 'hero_title' ) ); ?></h2>
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
			<div class="ob-recent__title"><?php echo esc_html( ob_t( 'home', 'recent_title' ) ); ?></div>
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

<div class="ob-strip">
	<div class="ob-strip__inner">
		<?php foreach ( $ob_rubs as $ob_rub ) : ?>
			<a href="<?php echo esc_url( $ob_rub['url'] ); ?>"><?php echo esc_html( $ob_rub['name'] ); ?><span class="ob-strip__dot" aria-hidden="true"></span></a>
		<?php endforeach; ?>
	</div>
</div>

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

<section class="ob-wrap ob-section">
	<?php ob_section_head( '02', ob_t( 'home', 'latest_title' ) ); ?>
	<div class="ob-grid ob-grid--3 ob-grid--tall">
		<?php
		foreach ( $ob_latest as $ob_p ) {
			ob_card( $ob_p );
		}
		?>
	</div>
	<div class="ob-center ob-mt-56">
		<a class="ob-btn ob-btn--outline" href="<?php echo esc_url( get_pagenum_link( 2 ) ); ?>"><?php echo esc_html( ob_t( 'home', 'more_btn' ) ); ?></a>
	</div>
</section>

<?php
$ob_num = 3;
foreach ( $ob_sections as $ob_section ) :
	if ( empty( $ob_rubs[ $ob_section['slug'] ] ) ) {
		continue;
	}
	$ob_rub    = $ob_rubs[ $ob_section['slug'] ];
	$ob_owner  = ob_author_by_slug( $ob_rub['author'] );
	$ob_items  = $ob_take( array( 'cat' => $ob_rub['term']->term_id, 'posts_per_page' => 4 ) );
	if ( ! $ob_items ) {
		continue;
	}
	$ob_feature = 'feature' === $ob_section['layout'];

	if ( ! empty( $ob_section['quote'] ) && $ob_owner ) :
		$ob_quote = ob_t( 'home', 'quote' );
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
					sprintf( '%02d', $ob_num++ ),
					$ob_rub['name'],
					$ob_owner ? sprintf( /* translators: %s author */ __( 'Par %s', 'octublog' ), $ob_owner['name'] ) : '',
					$ob_feature ? $ob_rub['color'] : ''
				);
				?>
				<a class="ob-seeall" href="<?php echo esc_url( $ob_rub['url'] ); ?>"><?php echo esc_html( ob_t( 'home', 'see_all' ) ); ?> →</a>
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
				<div class="ob-grid ob-grid--4 ob-grid--scroll">
					<?php
					foreach ( $ob_items as $ob_p ) {
						echo '<div class="ob-grid__cell">';
						ob_card( $ob_p, array( 'quote' => 'quotes' === $ob_section['layout'] ) );
						echo '</div>';
					}
					?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endforeach; ?>

<section class="ob-wrap ob-section ob-section--nl-home">
	<?php get_template_part( 'template-parts/newsletter' ); ?>
</section>

<section class="ob-wrap ob-section">
	<div class="ob-shead ob-shead--center">
		<span class="ob-shead__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $ob_num ) ); ?></span>
		<h2 class="ob-h2"><?php echo esc_html( ob_t( 'home', 'team_title' ) ); ?></h2>
		<p><?php echo esc_html( ob_t( 'home', 'team_text' ) ); ?></p>
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
get_footer();
