<?php
/**
 * Newsletter block, wired to Jetpack subscriptions when available.
 * Args: bg (CSS color).
 *
 * @package Octublog
 */

$ob_bg      = $args['bg'] ?? '#E3D5BE';
$ob_privacy = get_privacy_policy_url();
if ( ! $ob_privacy ) {
	$ob_page    = get_page_by_path( 'politique-de-confidentialite' );
	$ob_privacy = $ob_page ? get_permalink( $ob_page ) : '';
}
?>
<div class="ob-newsletter" style="--nl-bg:<?php echo esc_attr( $ob_bg ); ?>">
	<div class="ob-newsletter__text">
		<span class="ob-eyebrow ob-eyebrow--ink"><?php esc_html_e( 'Newsletter', 'octublog' ); ?></span>
		<h2 class="ob-newsletter__title"><?php esc_html_e( 'Nos nouveaux guides, chaque dimanche', 'octublog' ); ?></h2>
		<p><?php esc_html_e( 'Une sélection de guides et d’idées de la semaine, dans votre boîte mail. Désinscription en un clic.', 'octublog' ); ?></p>
	</div>
	<div class="ob-newsletter__form">
		<?php
		if ( shortcode_exists( 'jetpack_subscription_form' ) ) {
			echo do_shortcode( '[jetpack_subscription_form title="" subscribe_text="" subscribe_placeholder="Votre adresse e-mail" submit_button_text="S’abonner" show_subscribers_total="false"]' );
		}
		?>
		<p class="ob-newsletter__legal">
			<?php esc_html_e( 'Vos données ne sont jamais partagées.', 'octublog' ); ?>
			<?php if ( $ob_privacy ) : ?>
				<a href="<?php echo esc_url( $ob_privacy ); ?>"><?php esc_html_e( 'Politique de confidentialité', 'octublog' ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</div>
