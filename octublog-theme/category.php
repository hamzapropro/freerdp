<?php
/**
 * Rubrique (category) archive.
 *
 * @package Octublog
 */

get_header();

$ob_term  = get_queried_object();
$ob_rub   = ob_rubrique( $ob_term );
$ob_owner = ob_author_by_slug( $ob_rub['author'] );
$ob_paged = max( 1, get_query_var( 'paged' ) );
$ob_posts = $GLOBALS['wp_query']->posts;
$ob_feat  = ( 1 === $ob_paged && $ob_posts ) ? array_shift( $ob_posts ) : null;
?>

<section class="ob-band" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>--band:<?php echo esc_attr( $ob_rub['tint'] ); ?>">
	<div class="ob-wrap ob-pagehero">
		<div class="ob-pagehero__text">
			<nav class="ob-crumbs ob-crumbs--ink" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'octublog' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
				<span class="ob-crumbs__sep" aria-hidden="true">›</span>
				<span aria-current="page"><?php echo esc_html( $ob_term->name ); ?></span>
			</nav>
			<div class="ob-pagehero__titlerow">
				<span class="ob-bigicon"><?php echo ob_icon( $ob_rub['icon'], 28 ); // phpcs:ignore ?></span>
				<h1 class="ob-pagehero__title"><?php echo esc_html( $ob_term->name ); ?><?php echo $ob_paged > 1 ? '<span class="screen-reader-text"> – ' . esc_html( sprintf( __( 'page %d', 'octublog' ), $ob_paged ) ) . '</span>' : ''; ?></h1>
			</div>
			<?php if ( $ob_rub['desc'] ) : ?>
				<p class="ob-pagehero__lead"><?php echo esc_html( $ob_rub['desc'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="ob-statbox">
			<div class="ob-statbox__num"><span><?php echo esc_html( number_format_i18n( $ob_term->count ) ); ?></span> <?php echo esc_html( _n( 'article', 'articles', $ob_term->count, 'octublog' ) ); ?></div>
			<?php if ( $ob_owner ) : ?>
				<a class="ob-statbox__author" href="<?php echo esc_url( $ob_owner['url'] ); ?>">
					<?php echo ob_avatar( $ob_owner, 44, 'ob-avatar--rub' ); // phpcs:ignore ?>
					<span><small><?php esc_html_e( 'Rubrique tenue par', 'octublog' ); ?></small><strong><?php echo esc_html( $ob_owner['name'] ); ?></strong></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
if ( $ob_feat ) :
	$ob_author = ob_post_author( $ob_feat );
	?>
	<section class="ob-wrap ob-section ob-section--top" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
		<article class="ob-catfeat">
			<a class="ob-media ob-media--zoom ob-catfeat__media" href="<?php echo esc_url( get_permalink( $ob_feat ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo ob_thumb( $ob_feat, 'large', '(min-width: 1100px) 660px, 100vw', array( 'loading' => 'eager' ) ); // phpcs:ignore ?></a>
			<div class="ob-catfeat__body">
				<span class="ob-pill ob-pill--caps"><?php esc_html_e( 'À la une de la rubrique', 'octublog' ); ?></span>
				<h2 class="ob-catfeat__title"><a href="<?php echo esc_url( get_permalink( $ob_feat ) ); ?>"><?php echo esc_html( get_the_title( $ob_feat ) ); ?></a></h2>
				<p><?php echo esc_html( ob_summary( $ob_feat, 30 ) ); ?></p>
				<div class="ob-meta">
					<?php echo ob_avatar( $ob_author, 32 ); // phpcs:ignore ?>
					<strong><?php echo esc_html( $ob_author['name'] ); ?></strong>
					<span aria-hidden="true">·</span>
					<time datetime="<?php echo esc_attr( get_the_date( 'c', $ob_feat ) ); ?>"><?php echo esc_html( ob_date( $ob_feat ) ); ?></time>
				</div>
			</div>
		</article>
	</section>
<?php endif; ?>

<section class="ob-wrap ob-section">
	<h2 class="ob-h2 ob-h2--s ob-mb-28"><?php esc_html_e( 'Tous les articles', 'octublog' ); ?></h2>
	<?php if ( $ob_posts ) : ?>
		<div class="ob-grid ob-grid--3 ob-grid--tall">
			<?php
			foreach ( $ob_posts as $ob_p ) {
				ob_card( $ob_p );
			}
			?>
		</div>
	<?php endif; ?>
	<?php ob_pagination(); ?>
	<?php if ( 1 === $ob_paged && ! empty( $ob_rub['long'] ) ) : ?>
		<div class="ob-seo-text ob-prose" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
			<h2><?php echo esc_html( sprintf( /* translators: %s rubrique */ __( 'À propos de la rubrique %s', 'octublog' ), $ob_term->name ) ); ?></h2>
			<?php echo $ob_rub['long']; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitised in ob_rubriques(). ?>
		</div>
	<?php endif; ?>
	<div class="ob-mt-section">
		<?php get_template_part( 'template-parts/newsletter', null, array( 'bg' => $ob_rub['color'] ) ); ?>
	</div>
</section>

<?php
get_footer();
