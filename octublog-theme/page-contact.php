<?php
/**
 * Contact page: designed hero, WPForms form, contact details and author list.
 *
 * @package Octublog
 */

get_header();

while ( have_posts() ) :
	the_post();

	// Form id comes from the page's own WPForms shortcode, so editors can swap the form.
	$ob_form_id = 0;
	if ( preg_match( '/\[wpforms[^\]]*\bid=["\']?(\d+)/', get_post()->post_content, $ob_m ) ) {
		$ob_form_id = (int) $ob_m[1];
	}
	$ob_privacy = get_privacy_policy_url() ?: home_url( '/politique-de-confidentialite/' );
	$ob_info    = array(
		'emails'  => ob_t_lines( 'contact', 'emails' ),
		'address' => ob_t_lines( 'contact', 'address' ),
		'phone'   => ob_t_lines( 'contact', 'phone' ),
	);
	$ob_rows = array(
		array( 'icon' => 'mail', 'label' => __( 'E-mail', 'octublog' ), 'lines' => $ob_info['emails'], 'mail' => true ),
		array( 'icon' => 'pin', 'label' => __( 'Adresse', 'octublog' ), 'lines' => $ob_info['address'] ),
		array( 'icon' => 'phone', 'label' => __( 'Téléphone', 'octublog' ), 'lines' => $ob_info['phone'] ),
	);
	?>
	<section class="ob-wrap ob-contact-hero">
		<span class="ob-pill ob-pill--gold"><?php echo esc_html( ob_t( 'contact', 'badge' ) ); ?></span>
		<h1 class="ob-pagehero__title ob-pagehero__title--wide"><?php echo esc_html( ob_t( 'contact', 'title' ) ); ?></h1>
		<p class="ob-contact-hero__lead"><?php echo esc_html( ob_t( 'contact', 'lead' ) ); ?></p>
	</section>

	<section class="ob-wrap ob-contact">
		<div class="ob-contact__form">
			<h2 class="ob-h2"><?php echo esc_html( ob_t( 'contact', 'form_title' ) ); ?></h2>
			<?php
			if ( $ob_form_id && shortcode_exists( 'wpforms' ) ) {
				echo do_shortcode( '[wpforms id="' . $ob_form_id . '" title="false"]' );
			} else {
				the_content();
			}
			?>
			<p class="ob-contact__legal">
				<?php esc_html_e( 'Vos données servent uniquement à répondre à votre demande, conformément à la', 'octublog' ); ?>
				<a href="<?php echo esc_url( $ob_privacy ); ?>"><?php esc_html_e( 'politique de confidentialité', 'octublog' ); ?></a>.
				<?php echo esc_html( ob_t( 'contact', 'note' ) ); ?>
			</p>
		</div>

		<div class="ob-contact__side">
			<div class="ob-infocard">
				<?php foreach ( $ob_rows as $ob_row ) : ?>
					<?php if ( empty( array_filter( (array) $ob_row['lines'] ) ) ) { continue; } ?>
					<div class="ob-infocard__row">
						<span class="ob-infocard__icon"><?php echo ob_icon( $ob_row['icon'], 18 ); // phpcs:ignore ?></span>
						<div class="ob-infocard__text">
							<span class="ob-eyebrow ob-eyebrow--gold"><?php echo esc_html( $ob_row['label'] ); ?></span>
							<?php foreach ( (array) $ob_row['lines'] as $ob_line ) : ?>
								<?php if ( ! empty( $ob_row['mail'] ) ) : ?>
									<a href="mailto:<?php echo esc_attr( antispambot( $ob_line ) ); ?>"><?php echo esc_html( antispambot( $ob_line ) ); ?></a>
								<?php else : ?>
									<span><?php echo esc_html( $ob_line ); ?></span>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="ob-socials ob-infocard__socials">
					<?php foreach ( ob_socials() as $ob_icon_name => $ob_social ) : ?>
						<a class="ob-socials__link" href="<?php echo esc_url( $ob_social['url'] ); ?>" target="_blank" rel="noopener me" aria-label="<?php echo esc_attr( $ob_social['label'] ); ?>"><?php echo ob_icon( $ob_icon_name, 17 ); // phpcs:ignore ?></a>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="ob-authorlist">
				<h2 class="ob-authorlist__title"><?php echo esc_html( ob_t( 'contact', 'a_title' ) ); ?></h2>
				<p><?php echo esc_html( ob_t( 'contact', 'a_text' ) ); ?></p>
				<?php
				foreach ( ob_rubriques() as $ob_rub ) :
					$ob_person = ob_author_by_slug( $ob_rub['author'] );
					$ob_user   = $ob_person && $ob_person['user_id'] ? get_userdata( $ob_person['user_id'] ) : null;
					if ( ! $ob_person || ! $ob_user || false === strpos( $ob_user->user_email, '@octublog.com' ) ) {
						continue;
					}
					?>
					<a class="ob-authorlist__item" href="mailto:<?php echo esc_attr( antispambot( $ob_user->user_email ) ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
						<?php echo ob_avatar( $ob_person, 44, 'ob-avatar--rub' ); // phpcs:ignore ?>
						<span>
							<strong><?php echo esc_html( $ob_person['name'] ); ?> <em>· <?php echo esc_html( $ob_rub['spec'] ); ?></em></strong>
							<small><?php echo esc_html( antispambot( $ob_user->user_email ) ); ?></small>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
