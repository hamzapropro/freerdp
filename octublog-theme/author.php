<?php
/**
 * Author archive.
 *
 * @package Octublog
 */

get_header();

$ob_obj    = get_queried_object();
$ob_author = null;
if ( function_exists( 'get_archive_author' ) ) {
	$ob_pp = get_archive_author();
	if ( $ob_pp ) {
		$ob_author = ob_author_data( $ob_pp );
	}
}
if ( ! $ob_author ) {
	$ob_author = ob_author_data( $ob_obj instanceof WP_User ? $ob_obj : get_userdata( (int) get_query_var( 'author' ) ) );
}

// The rubrique this person runs, if any.
$ob_rub = ob_rubrique( null );
foreach ( ob_rubriques() as $ob_r ) {
	if ( $ob_r['author'] === $ob_author['slug'] ) {
		$ob_rub = $ob_r;
		break;
	}
}
$ob_first = strtok( $ob_author['name'], ' ' );
$ob_total = (int) $GLOBALS['wp_query']->found_posts;
$ob_user  = $ob_author['user_id'] ? get_userdata( $ob_author['user_id'] ) : null;
$ob_mail  = $ob_user ? $ob_user->user_email : '';
$ob_about = get_page_by_path( 'a-propos-de-nous' );
?>

<section class="ob-band" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>--band:<?php echo esc_attr( $ob_rub['tint'] ); ?>">
	<div class="ob-wrap ob-wrap--article ob-authorhero">
		<?php echo ob_avatar( $ob_author, 240, 'ob-avatar--halo ob-authorhero__av' ); // phpcs:ignore ?>
		<div class="ob-authorhero__text">
			<nav class="ob-crumbs ob-crumbs--ink" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'octublog' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
				<?php if ( $ob_about ) : ?>
					<span class="ob-crumbs__sep" aria-hidden="true">›</span>
					<a href="<?php echo esc_url( get_permalink( $ob_about ) ); ?>"><?php esc_html_e( 'L’équipe', 'octublog' ); ?></a>
				<?php endif; ?>
				<span class="ob-crumbs__sep" aria-hidden="true">›</span>
				<span aria-current="page"><?php echo esc_html( $ob_author['name'] ); ?></span>
			</nav>
			<h1 class="ob-pagehero__title"><?php echo esc_html( $ob_author['name'] ); ?></h1>
			<div class="ob-pills">
				<?php if ( ! empty( $ob_rub['term'] ) ) : ?>
					<a class="ob-pill ob-pill--icon ob-pill--l" href="<?php echo esc_url( $ob_rub['url'] ); ?>"><?php echo ob_icon( $ob_rub['icon'], 16 ); // phpcs:ignore ?><?php echo esc_html( $ob_rub['name'] ); ?></a>
				<?php endif; ?>
				<span class="ob-pill ob-pill--white ob-pill--l"><?php echo esc_html( sprintf( /* translators: %s number */ _n( '%s article', '%s articles', $ob_total, 'octublog' ), number_format_i18n( $ob_total ) ) ); ?></span>
			</div>
			<?php if ( $ob_author['bio'] ) : ?>
				<div class="ob-authorhero__bio"><?php echo wp_kses_post( wpautop( $ob_author['bio'] ) ); ?></div>
			<?php endif; ?>
			<?php if ( $ob_mail && false !== strpos( $ob_mail, '@octublog.com' ) ) : ?>
				<a class="ob-btn ob-btn--dark ob-btn--s" href="mailto:<?php echo esc_attr( antispambot( $ob_mail ) ); ?>"><?php echo ob_icon( 'mail', 16 ); // phpcs:ignore ?><?php echo esc_html( antispambot( $ob_mail ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="ob-wrap ob-section">
	<h2 class="ob-h2 ob-mb-28">
		<?php
		/* translators: %s first name */
		echo esc_html( sprintf( __( 'Les articles de %s', 'octublog' ), $ob_first ) );
		?>
	</h2>
	<?php if ( have_posts() ) : ?>
		<div class="ob-grid ob-grid--3 ob-grid--tall">
			<?php
			while ( have_posts() ) {
				the_post();
				ob_card( get_post() );
			}
			?>
		</div>
	<?php endif; ?>
	<?php ob_pagination(); ?>
</section>

<?php
get_footer();
