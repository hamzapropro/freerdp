<?php
/**
 * Legal pages (mentions légales, confidentialité, RGPD, DMCA, disclaimer, conditions).
 *
 * @package Octublog
 */

get_header();

while ( have_posts() ) :
	the_post();
	$ob_legal  = ob_legal_prepare( apply_filters( 'the_content', get_the_content() ) );
	$ob_others = array();
	foreach ( ob_legal_slugs() as $ob_slug ) {
		$ob_page = get_page_by_path( $ob_slug );
		if ( $ob_page && $ob_page->ID !== get_the_ID() ) {
			$ob_others[] = $ob_page;
		}
	}
	?>
	<section class="ob-band" style="--band:var(--ob-cream)">
		<div class="ob-wrap ob-wrap--legal ob-legalhero">
			<nav class="ob-crumbs ob-crumbs--ink" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'octublog' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
				<span class="ob-crumbs__sep" aria-hidden="true">›</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<h1 class="ob-pagehero__title ob-legalhero__title"><?php the_title(); ?></h1>
			<span class="ob-updated"><?php echo esc_html( sprintf( /* translators: %s date */ __( 'Dernière mise à jour : %s', 'octublog' ), get_the_modified_date( 'j F Y' ) ) ); ?></span>
		</div>
	</section>

	<div class="ob-wrap ob-wrap--legal ob-legal">
		<aside class="ob-legal__aside" aria-label="<?php esc_attr_e( 'Sur cette page', 'octublog' ); ?>">
			<?php if ( $ob_legal['sections'] ) : ?>
				<span class="ob-eyebrow ob-eyebrow--ink"><?php esc_html_e( 'Sur cette page', 'octublog' ); ?></span>
				<?php foreach ( $ob_legal['sections'] as $ob_s ) : ?>
					<a class="ob-legal__toc" href="#<?php echo esc_attr( $ob_s['id'] ); ?>"><?php echo esc_html( (int) $ob_s['n'] . '. ' . $ob_s['h'] ); ?></a>
				<?php endforeach; ?>
				<span class="ob-legal__sep" aria-hidden="true"></span>
			<?php endif; ?>
			<span class="ob-eyebrow ob-eyebrow--ink"><?php esc_html_e( 'Autres pages', 'octublog' ); ?></span>
			<?php foreach ( $ob_others as $ob_o ) : ?>
				<a class="ob-legal__other" href="<?php echo esc_url( get_permalink( $ob_o ) ); ?>"><?php echo esc_html( get_the_title( $ob_o ) ); ?></a>
			<?php endforeach; ?>
		</aside>
		<div class="ob-legal__main ob-prose">
			<?php echo $ob_legal['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- post content filtered by the_content. ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
