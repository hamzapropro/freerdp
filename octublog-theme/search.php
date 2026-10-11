<?php
/**
 * Search results.
 *
 * @package Octublog
 */

get_header();

$ob_total = (int) $GLOBALS['wp_query']->found_posts;
$ob_q     = get_search_query();
?>

<section class="ob-band" style="--band:var(--ob-cream)">
	<div class="ob-wrap ob-wrap--search ob-searchhero">
		<span class="ob-eyebrow"><?php esc_html_e( 'Recherche', 'octublog' ); ?></span>
		<form class="ob-searchfield ob-searchfield--big" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo ob_icon( 'search', 24 ); // phpcs:ignore ?>
			<label class="screen-reader-text" for="ob-s"><?php esc_html_e( 'Rechercher', 'octublog' ); ?></label>
			<input id="ob-s" type="search" name="s" value="<?php echo esc_attr( $ob_q ); ?>" placeholder="<?php esc_attr_e( 'Rechercher un guide, une idée…', 'octublog' ); ?>">
			<button class="ob-btn ob-btn--gold" type="submit"><?php esc_html_e( 'Rechercher', 'octublog' ); ?></button>
		</form>
		<h1 class="ob-searchhero__count">
			<?php
			if ( $ob_total ) {
				/* translators: 1: number of results, 2: query */
				echo esc_html( sprintf( _n( '%1$s résultat pour « %2$s »', '%1$s résultats pour « %2$s »', $ob_total, 'octublog' ), number_format_i18n( $ob_total ), $ob_q ) );
			} else {
				/* translators: %s query */
				echo esc_html( sprintf( __( 'Aucun résultat pour « %s »', 'octublog' ), $ob_q ) );
			}
			?>
		</h1>
	</div>
</section>

<?php if ( have_posts() ) : ?>
	<section class="ob-wrap ob-wrap--search ob-section ob-section--s">
		<div class="ob-results">
			<?php
			while ( have_posts() ) :
				the_post();
				$ob_cat    = ob_post_category();
				$ob_rub    = ob_rubrique( $ob_cat );
				$ob_author = ob_post_author();
				?>
				<a class="ob-result" href="<?php the_permalink(); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
					<span class="ob-media ob-result__thumb"><?php echo ob_thumb( get_post(), 'medium_large', '240px' ); // phpcs:ignore ?></span>
					<span class="ob-result__body">
						<?php if ( $ob_cat ) : ?>
							<span class="ob-pill"><?php echo esc_html( $ob_cat->name ); ?></span>
						<?php endif; ?>
						<span class="ob-result__title"><?php the_title(); ?></span>
						<span class="ob-result__ex"><?php echo esc_html( ob_summary( null, 26 ) ); ?></span>
						<span class="ob-small"><?php echo esc_html( $ob_author['name'] . ' · ' . ob_date() ); ?></span>
					</span>
				</a>
			<?php endwhile; ?>
		</div>
		<?php ob_pagination(); ?>
	</section>
<?php else : ?>
	<section class="ob-wrap ob-section ob-section--s ob-empty">
		<div class="ob-empty__intro">
			<h2 class="ob-h2"><?php esc_html_e( 'On n’a rien trouvé, mais on a de quoi vous occuper.', 'octublog' ); ?></h2>
			<p><?php esc_html_e( 'Vérifiez l’orthographe, essayez un mot plus court, ou parcourez une rubrique.', 'octublog' ); ?></p>
		</div>
		<div class="ob-pills">
			<?php foreach ( ob_rubriques() as $ob_rub ) : ?>
				<a class="ob-pill ob-pill--icon ob-pill--xl" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>"><?php echo ob_icon( $ob_rub['icon'], 18 ); // phpcs:ignore ?><?php echo esc_html( $ob_rub['name'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<h3 class="ob-h3"><?php esc_html_e( 'Derniers articles', 'octublog' ); ?></h3>
		<div class="ob-grid ob-grid--3">
			<?php
			foreach ( get_posts( array( 'posts_per_page' => 3 ) ) as $ob_p ) {
				ob_card( $ob_p );
			}
			?>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
