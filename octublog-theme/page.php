<?php
/**
 * Static page (À propos, Contact, legal pages).
 *
 * @package Octublog
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'ob-page' ); ?>>
		<header class="ob-wrap ob-wrap--article ob-page__head">
			<nav class="ob-crumbs" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'octublog' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
				<span class="ob-crumbs__sep" aria-hidden="true">›</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<h1 class="ob-article__title"><?php the_title(); ?></h1>
		</header>
		<div class="ob-wrap ob-wrap--article">
			<div class="ob-prose ob-page__body">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
