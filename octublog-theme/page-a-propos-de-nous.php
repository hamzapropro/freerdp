<?php
/**
 * About page: designed hero, method cards, the page's own text, team and contact call.
 *
 * @package Octublog
 */

get_header();

$ob_rubs  = ob_rubriques();
$ob_cover = array();
foreach ( array( 'maison-et-organisation', 'enfance-et-famille', 'inspirations-et-bien-etre', 'voyage-et-evasion' ) as $ob_slug ) {
	if ( empty( $ob_rubs[ $ob_slug ] ) ) {
		continue;
	}
	$ob_found = get_posts(
		array(
			'cat'            => $ob_rubs[ $ob_slug ]['term']->term_id,
			'posts_per_page' => 1,
			'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery
			'no_found_rows'  => true,
		)
	);
	if ( $ob_found ) {
		$ob_cover[] = $ob_found[0];
	}
}
$ob_rules = array(
	array( 'n' => '01', 't' => __( 'Testé avant d’être conseillé', 'octublog' ), 'd' => __( 'Aucun outil ni produit n’est recommandé sans avoir été utilisé par l’auteur de la rubrique.', 'octublog' ), 'bg' => 'var(--ob-gold)' ),
	array( 'n' => '02', 't' => __( 'Des budgets réels', 'octublog' ), 'd' => __( 'Un prix réaliste, une méthode précise, un temps honnête. Et une alternative moins chère quand elle existe.', 'octublog' ), 'bg' => 'var(--ob-cream)' ),
	array( 'n' => '03', 't' => __( 'Les ratés aussi', 'octublog' ), 'd' => __( 'Nos tutoriels montrent les étapes qui coincent, pas seulement le résultat final.', 'octublog' ), 'bg' => 'var(--ob-cream)' ),
	array( 'n' => '04', 't' => __( 'Sans jugement', 'octublog' ), 'd' => __( 'On écrit pour que vous refermiez la page un peu moins seul, jamais plus jugé.', 'octublog' ), 'bg' => 'var(--ob-cream)' ),
);

while ( have_posts() ) :
	the_post();
	?>
	<section class="ob-wrap ob-abouthero">
		<div class="ob-abouthero__text">
			<span class="ob-pill ob-pill--gold"><?php esc_html_e( 'À propos d’Octublog', 'octublog' ); ?></span>
			<h1 class="ob-pagehero__title ob-pagehero__title--wide"><?php esc_html_e( 'Des idées claires pour simplifier votre quotidien.', 'octublog' ); ?></h1>
			<p class="ob-contact-hero__lead"><?php esc_html_e( 'Octublog est un magazine où chaque rubrique est confiée à une seule personne. Organisation de la maison, enfants, bien-être, tech, création, voyage : on publie des guides concrets, testés, avec des budgets réels.', 'octublog' ); ?></p>
			<div class="ob-abouthero__btns">
				<a class="ob-btn ob-btn--gold" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Lire nos guides', 'octublog' ); ?></a>
				<a class="ob-btn ob-btn--outline" href="#equipe"><?php esc_html_e( 'Rencontrer l’équipe', 'octublog' ); ?></a>
			</div>
		</div>
		<?php if ( $ob_cover ) : ?>
			<div class="ob-mosaic" aria-hidden="true">
				<?php foreach ( $ob_cover as $ob_i => $ob_p ) : ?>
					<span class="ob-media ob-mosaic__item ob-mosaic__item--<?php echo (int) $ob_i; ?>"><?php echo ob_thumb( $ob_p, 'medium_large', '(min-width: 1100px) 300px, 50vw', array( 'alt' => '' ) ); // phpcs:ignore ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<div class="ob-strip">
		<div class="ob-strip__inner">
			<?php foreach ( $ob_rubs as $ob_rub ) : ?>
				<a href="<?php echo esc_url( $ob_rub['url'] ); ?>"><?php echo esc_html( $ob_rub['name'] ); ?><span class="ob-strip__dot" aria-hidden="true"></span></a>
			<?php endforeach; ?>
		</div>
	</div>

	<section class="ob-wrap ob-section">
		<div class="ob-introhead">
			<span class="ob-eyebrow"><?php esc_html_e( 'Notre méthode', 'octublog' ); ?></span>
			<h2 class="ob-h2"><?php esc_html_e( 'Quatre règles qu’on applique à chaque article', 'octublog' ); ?></h2>
		</div>
		<div class="ob-rules">
			<?php foreach ( $ob_rules as $ob_rule ) : ?>
				<div class="ob-rule" style="background:<?php echo esc_attr( $ob_rule['bg'] ); ?>">
					<span class="ob-rule__n"><?php echo esc_html( $ob_rule['n'] ); ?></span>
					<h3><?php echo esc_html( $ob_rule['t'] ); ?></h3>
					<p><?php echo esc_html( $ob_rule['d'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( trim( wp_strip_all_tags( get_the_content() ) ) ) : ?>
		<section class="ob-wrap ob-wrap--article ob-section ob-section--content">
			<div class="ob-prose ob-page__body"><?php the_content(); ?></div>
		</section>
	<?php endif; ?>

	<section class="ob-band" id="equipe" style="--band:var(--ob-cream)">
		<div class="ob-wrap ob-section">
			<div class="ob-introhead">
				<span class="ob-eyebrow"><?php esc_html_e( 'L’équipe', 'octublog' ); ?></span>
				<h2 class="ob-h2"><?php esc_html_e( 'Une rubrique, une autrice ou un auteur', 'octublog' ); ?></h2>
			</div>
			<div class="ob-teamgrid">
				<?php
				foreach ( $ob_rubs as $ob_rub ) :
					$ob_person = ob_author_by_slug( $ob_rub['author'] );
					if ( ! $ob_person ) {
						continue;
					}
					?>
					<a class="ob-teamcard" href="<?php echo esc_url( $ob_person['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
						<span class="ob-teamcard__top">
							<?php echo ob_avatar( $ob_person, 72, 'ob-avatar--gold' ); // phpcs:ignore ?>
							<span>
								<strong class="ob-teamcard__name"><?php echo esc_html( $ob_person['name'] ); ?></strong>
								<span class="ob-pill"><?php echo esc_html( $ob_rub['name'] ); ?></span>
							</span>
						</span>
						<?php if ( $ob_person['bio'] ) : ?>
							<span class="ob-teamcard__bio"><?php echo esc_html( wp_trim_words( $ob_person['bio'], 26, '…' ) ); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="ob-wrap ob-section">
		<div class="ob-cta">
			<div class="ob-cta__text">
				<h2><?php esc_html_e( 'Une question, une idée de sujet ?', 'octublog' ); ?></h2>
				<p><?php esc_html_e( 'Écrivez à l’équipe ou directement à l’auteur de la rubrique.', 'octublog' ); ?></p>
			</div>
			<?php $ob_contact = get_page_by_path( 'contact' ); ?>
			<a class="ob-btn ob-btn--gold ob-cta__btn" href="<?php echo esc_url( $ob_contact ? get_permalink( $ob_contact ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Contactez-nous', 'octublog' ); ?></a>
		</div>
	</section>
	<?php
endwhile;

get_footer();
