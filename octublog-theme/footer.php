<?php
/**
 * Site footer.
 *
 * @package Octublog
 */

$ob_socials = ob_socials();
?>
</main>

<footer class="ob-footer">
	<div class="ob-wrap">
		<div class="ob-footer__top">
			<a class="ob-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( ob_logo_url( 'light' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="224" height="56" loading="lazy">
			</a>
			<?php if ( $ob_socials ) : ?>
				<div class="ob-socials">
					<?php foreach ( $ob_socials as $ob_icon_name => $ob_social ) : ?>
						<a class="ob-socials__link" href="<?php echo esc_url( $ob_social['url'] ); ?>" target="_blank" rel="noopener me" aria-label="<?php echo esc_attr( $ob_social['label'] ); ?>"><?php echo ob_icon( $ob_icon_name, 17 ); // phpcs:ignore ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="ob-footer__cols">
			<div class="ob-footer__col">
				<h2 class="ob-footer__title"><?php esc_html_e( 'Qui sommes-nous', 'octublog' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_about',
						'container'      => false,
						'menu_class'     => 'ob-footer__links',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div class="ob-footer__col">
				<h2 class="ob-footer__title"><?php esc_html_e( 'Points forts', 'octublog' ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'ob_footer_about', 'Octublog aide les esprits curieux et créatifs à simplifier leur quotidien et s’évader grâce à des guides pratiques, des astuces tech testées, des idées d’organisation concrètes et des inspirations pour toute la famille.' ) ); ?></p>
			</div>
			<div class="ob-footer__col">
				<h2 class="ob-footer__title"><?php esc_html_e( 'Articles célèbres', 'octublog' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_famous',
						'container'      => false,
						'menu_class'     => 'ob-footer__links',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div class="ob-footer__col">
				<h2 class="ob-footer__title"><?php esc_html_e( 'Contact', 'octublog' ); ?></h2>
				<ul class="ob-footer__links">
					<?php foreach ( (array) get_theme_mod( 'ob_footer_contact', array( 'contact@octublog.com', 'admin@octublog.com', '8 Place Bellecour, 69002 Lyon, France' ) ) as $ob_line ) : ?>
						<li>
							<?php if ( is_email( $ob_line ) ) : ?>
								<a href="mailto:<?php echo esc_attr( antispambot( $ob_line ) ); ?>"><?php echo esc_html( antispambot( $ob_line ) ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $ob_line ); ?>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="ob-footer__bottom">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer_legal',
					'container'      => false,
					'menu_class'     => 'ob-footer__legal',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
			<div class="ob-footer__copy">
				<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> OctuBlog.com – <?php esc_html_e( 'Tous droits réservés.', 'octublog' ); ?></span>
				<a class="ob-totop" href="#contenu" data-ob-totop><?php echo ob_icon( 'arrowUp', 16 ); // phpcs:ignore ?><?php esc_html_e( 'Haut de page', 'octublog' ); ?></a>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
