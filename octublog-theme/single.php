<?php
/**
 * Single article.
 *
 * @package Octublog
 */

get_header();

while ( have_posts() ) :
	the_post();
	$ob_post    = get_post();
	$ob_cat     = ob_post_category( $ob_post );
	$ob_rub     = ob_rubrique( $ob_cat );
	$ob_author  = ob_post_author( $ob_post );
	$ob_toc     = ob_toc( $ob_post );
	$ob_lead    = has_excerpt() ? get_the_excerpt() : get_post_meta( $ob_post->ID, '_yoast_wpseo_metadesc', true );
	$ob_lead    = ( $ob_lead && false === strpos( $ob_lead, '%%' ) ) ? $ob_lead : '';
	$ob_updated = get_the_modified_time( 'U' ) - get_the_time( 'U' ) > DAY_IN_SECONDS;
	$ob_spec    = '';
	foreach ( ob_rubriques() as $ob_r ) {
		if ( $ob_r['author'] === $ob_author['slug'] ) {
			$ob_spec = $ob_r['name'];
			break;
		}
	}
	$ob_spec  = get_user_meta( $ob_author['user_id'], 'ob_role', true ) ?: $ob_spec;
	$ob_thumb = get_post_thumbnail_id();
	$ob_cap   = $ob_thumb ? wp_get_attachment_caption( $ob_thumb ) : '';
	?>
	<div class="ob-progress" aria-hidden="true"><span data-ob-progress></span></div>

	<article <?php post_class( 'ob-article' ); ?> style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
		<header class="ob-article__head ob-wrap ob-wrap--article">
			<nav class="ob-crumbs" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'octublog' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
				<?php if ( $ob_cat ) : ?>
					<span class="ob-crumbs__sep" aria-hidden="true">›</span>
					<a href="<?php echo esc_url( get_category_link( $ob_cat ) ); ?>"><?php echo esc_html( $ob_cat->name ); ?></a>
				<?php endif; ?>
				<span class="ob-crumbs__sep" aria-hidden="true">›</span>
				<span class="ob-crumbs__current" aria-current="page"><?php the_title(); ?></span>
			</nav>
			<?php if ( $ob_cat ) : ?>
				<a class="ob-pill" href="<?php echo esc_url( get_category_link( $ob_cat ) ); ?>"><?php echo esc_html( $ob_cat->name ); ?></a>
			<?php endif; ?>
			<h1 class="ob-article__title"><?php the_title(); ?></h1>
			<?php if ( $ob_lead ) : ?>
				<p class="ob-article__lead"><?php echo esc_html( $ob_lead ); ?></p>
			<?php endif; ?>

			<div class="ob-article__meta">
				<div class="ob-byline">
					<?php echo ob_avatar( $ob_author, 40, 'ob-avatar--rub' ); // phpcs:ignore ?>
					<div class="ob-byline__text">
						<span class="ob-byline__line">
							<span><?php esc_html_e( 'Par', 'octublog' ); ?> <a href="<?php echo esc_url( $ob_author['url'] ); ?>" rel="author"><?php echo esc_html( $ob_author['name'] ); ?></a></span>
							<?php if ( $ob_spec ) : ?>
								<span class="ob-dotsep" aria-hidden="true">·</span><span><?php echo esc_html( $ob_spec ); ?></span>
							<?php endif; ?>
						</span>
						<span class="ob-byline__line">
							<span><?php esc_html_e( 'Publié le', 'octublog' ); ?> <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( ob_date() ); ?></time></span>
							<?php if ( $ob_updated ) : ?>
								<span class="ob-dotsep" aria-hidden="true">·</span><span><?php esc_html_e( 'Mis à jour le', 'octublog' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( ob_date( null, true ) ); ?></time></span>
							<?php endif; ?>
							<span class="ob-dotsep" aria-hidden="true">·</span><span class="ob-readtime"><?php echo ob_icon( 'clock', 14 ); // phpcs:ignore ?><?php echo esc_html( sprintf( /* translators: %d minutes */ __( '%d min de lecture', 'octublog' ), ob_reading_time() ) ); ?></span>
						</span>
					</div>
				</div>
				<div class="ob-share">
					<?php foreach ( ob_share_links() as $ob_share ) : ?>
						<a class="ob-iconbtn" href="<?php echo esc_url( $ob_share['url'] ); ?>" aria-label="<?php echo esc_attr( $ob_share['label'] ); ?>"<?php echo empty( $ob_share['copy'] ) ? ' target="_blank" rel="noopener nofollow"' : ' data-ob-copy'; ?>><?php echo ob_icon( $ob_share['icon'], 17 ); // phpcs:ignore ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</header>

		<?php if ( $ob_thumb ) : ?>
			<figure class="ob-cover ob-wrap ob-wrap--article">
				<div class="ob-cover__frame">
					<div class="ob-cover__img">
						<?php
						echo wp_get_attachment_image(
							$ob_thumb,
							'full',
							false,
							array(
								'sizes'         => '(min-width: 1100px) 900px, 100vw',
								'fetchpriority' => 'high',
								'loading'       => 'eager',
								'alt'           => get_post_meta( $ob_thumb, '_wp_attachment_image_alt', true ) ?: get_the_title(),
							)
						);
						?>
					</div>
					<?php if ( $ob_cap ) : ?>
						<figcaption><?php echo esc_html( $ob_cap ); ?></figcaption>
					<?php endif; ?>
				</div>
			</figure>
		<?php endif; ?>

		<div class="ob-article__layout ob-wrap ob-wrap--article">
			<div class="ob-article__main">
				<?php if ( $ob_toc ) : ?>
					<details class="ob-toc-mobile">
						<summary><span><?php echo ob_icon( 'list', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Sommaire', 'octublog' ); ?></span><?php echo ob_icon( 'chevron', 18, 'ob-toc-mobile__chev' ); // phpcs:ignore ?></summary>
						<ol>
							<?php foreach ( $ob_toc as $ob_item ) : ?>
								<li><a href="#<?php echo esc_attr( $ob_item['id'] ); ?>"><?php echo esc_html( $ob_item['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ol>
					</details>
				<?php endif; ?>

				<div class="ob-prose">
					<?php the_content(); ?>
				</div>

				<?php
				wp_link_pages(
					array(
						'before' => '<nav class="ob-pagination">',
						'after'  => '</nav>',
					)
				);
				?>

				<div class="ob-sharebar">
					<span class="ob-sharebar__label"><?php esc_html_e( 'Partager ce guide', 'octublog' ); ?></span>
					<?php foreach ( ob_share_links() as $ob_share ) : ?>
						<a class="ob-sharebtn" href="<?php echo esc_url( $ob_share['url'] ); ?>"<?php echo empty( $ob_share['copy'] ) ? ' target="_blank" rel="noopener nofollow"' : ' data-ob-copy'; ?>><?php echo ob_icon( $ob_share['icon'], 16 ); // phpcs:ignore ?><span><?php echo esc_html( $ob_share['label'] ); ?></span></a>
					<?php endforeach; ?>
				</div>

				<?php if ( $ob_author['name'] ) : ?>
					<section class="ob-authorbox">
						<?php echo ob_avatar( $ob_author, 96, 'ob-avatar--white' ); // phpcs:ignore ?>
						<div class="ob-authorbox__text">
							<span class="ob-eyebrow ob-eyebrow--ink"><?php esc_html_e( 'Écrit par', 'octublog' ); ?></span>
							<span class="ob-authorbox__name"><?php echo esc_html( $ob_author['name'] ); ?></span>
							<?php if ( $ob_author['bio'] ) : ?>
								<p><?php echo esc_html( wp_trim_words( $ob_author['bio'], 60, '…' ) ); ?></p>
							<?php endif; ?>
							<a class="ob-linkarrow" href="<?php echo esc_url( $ob_author['url'] ); ?>"><?php esc_html_e( 'Voir ses articles', 'octublog' ); ?><?php echo ob_icon( 'arrowRight', 16 ); // phpcs:ignore ?></a>
						</div>
					</section>
				<?php endif; ?>
			</div>

			<?php if ( $ob_toc ) : ?>
				<aside class="ob-toc" aria-label="<?php esc_attr_e( 'Sommaire', 'octublog' ); ?>">
					<span class="ob-toc__title"><?php echo ob_icon( 'list', 16 ); // phpcs:ignore ?><?php esc_html_e( 'Sommaire', 'octublog' ); ?></span>
					<nav class="ob-toc__list">
						<?php foreach ( $ob_toc as $ob_item ) : ?>
							<a href="#<?php echo esc_attr( $ob_item['id'] ); ?>" data-ob-toc-link><?php echo esc_html( $ob_item['label'] ); ?></a>
						<?php endforeach; ?>
					</nav>
				</aside>
			<?php endif; ?>
		</div>
	</article>

	<section class="ob-wrap ob-section ob-section--nl">
		<?php get_template_part( 'template-parts/newsletter', null, array( 'bg' => $ob_rub['color'] ) ); ?>
	</section>

	<?php
	$ob_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( $ob_post->ID ),
			'cat'                 => $ob_cat ? $ob_cat->term_id : 0,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $ob_related->have_posts() ) :
		?>
		<section class="ob-band" style="--band:<?php echo esc_attr( $ob_rub['tint'] ); ?>">
			<div class="ob-wrap ob-section">
				<h2 class="ob-h2 ob-mb-32"><?php esc_html_e( 'À lire aussi', 'octublog' ); ?></h2>
				<div class="ob-grid ob-grid--3">
					<?php
					foreach ( $ob_related->posts as $ob_rel ) {
						ob_card( $ob_rel );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
