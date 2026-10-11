<?php
/**
 * Page not found.
 *
 * @package Octublog
 */

get_header();
?>

<section class="ob-wrap ob-404">
	<div class="ob-404__num" aria-hidden="true"><span>404</span></div>
	<h1 class="ob-404__title"><?php esc_html_e( 'Cette page s’est égarée.', 'octublog' ); ?></h1>
	<p class="ob-404__lead"><?php esc_html_e( 'Elle a peut-être changé d’adresse, ou n’a jamais existé. Cherchez ce qu’il vous faut, ou repartez d’une rubrique.', 'octublog' ); ?></p>
	<form class="ob-searchfield ob-404__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php echo ob_icon( 'search', 22 ); // phpcs:ignore ?>
		<label class="screen-reader-text" for="ob-404-s"><?php esc_html_e( 'Rechercher', 'octublog' ); ?></label>
		<input id="ob-404-s" type="search" name="s" placeholder="<?php esc_attr_e( 'Rechercher un guide, une idée…', 'octublog' ); ?>">
		<button class="ob-iconbtn ob-iconbtn--gold" type="submit" aria-label="<?php esc_attr_e( 'Lancer la recherche', 'octublog' ); ?>"><?php echo ob_icon( 'arrowRight', 18 ); // phpcs:ignore ?></button>
	</form>
	<div class="ob-404__cats">
		<?php foreach ( ob_rubriques() as $ob_rub ) : ?>
			<a class="ob-404__cat" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
				<span class="ob-bigicon ob-bigicon--s"><?php echo ob_icon( $ob_rub['icon'], 22 ); // phpcs:ignore ?></span>
				<span><?php echo esc_html( $ob_rub['name'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<a class="ob-btn ob-btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Retour à l’accueil', 'octublog' ); ?></a>
</section>

<?php
get_footer();
