<?php
/**
 * Fallback archive (later pages of the post list, tags, dates).
 *
 * @package Octublog
 */

get_header();

if ( is_home() ) {
	$ob_title = __( 'Derniers articles', 'octublog' );
} elseif ( is_archive() ) {
	$ob_title = wp_strip_all_tags( get_the_archive_title() );
} else {
	$ob_title = get_bloginfo( 'name' );
}
?>

<section class="ob-wrap ob-section">
	<h1 class="ob-h2 ob-mb-28">
		<?php
		echo esc_html( $ob_title );
		if ( is_paged() ) {
			/* translators: %d page number */
			echo '<span class="ob-muted"> · ' . esc_html( sprintf( __( 'page %d', 'octublog' ), get_query_var( 'paged' ) ) ) . '</span>';
		}
		?>
	</h1>
	<?php if ( have_posts() ) : ?>
		<div class="ob-grid ob-grid--3 ob-grid--tall">
			<?php
			while ( have_posts() ) {
				the_post();
				ob_card( get_post() );
			}
			?>
		</div>
		<?php ob_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Aucun article pour le moment.', 'octublog' ); ?></p>
	<?php endif; ?>
</section>

<?php
get_footer();
