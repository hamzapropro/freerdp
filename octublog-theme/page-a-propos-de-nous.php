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
$ob_rules = array();
foreach ( array( 1 => 'var(--ob-gold)', 2 => 'var(--ob-cream)', 3 => 'var(--ob-cream)', 4 => 'var(--ob-cream)' ) as $ob_n => $ob_bg ) {
	$ob_rules[] = array(
		'n'  => sprintf( '%02d', $ob_n ),
		't'  => ob_t( 'about', 'r' . $ob_n . '_t' ),
		'd'  => ob_t( 'about', 'r' . $ob_n . '_d' ),
		'bg' => $ob_bg,
	);
}

while ( have_posts() ) :
	the_post();
	?>
	<section class="ob-wrap ob-abouthero">
		<div class="ob-abouthero__text">
			<span class="ob-pill ob-pill--gold"><?php echo esc_html( ob_t( 'about', 'badge' ) ); ?></span>
			<h1 class="ob-pagehero__title ob-pagehero__title--wide"><?php echo esc_html( ob_t( 'about', 'title' ) ); ?></h1>
			<p class="ob-contact-hero__lead"><?php echo esc_html( ob_t( 'about', 'lead' ) ); ?></p>
			<div class="ob-abouthero__btns">
				<a class="ob-btn ob-btn--gold" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( ob_t( 'about', 'btn1' ) ); ?></a>
				<a class="ob-btn ob-btn--outline" href="#equipe"><?php echo esc_html( ob_t( 'about', 'btn2' ) ); ?></a>
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
			<span class="ob-eyebrow"><?php echo esc_html( ob_t( 'about', 'm_eyebrow' ) ); ?></span>
			<h2 class="ob-h2"><?php echo esc_html( ob_t( 'about', 'm_title' ) ); ?></h2>
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
				<span class="ob-eyebrow"><?php echo esc_html( ob_t( 'about', 't_eyebrow' ) ); ?></span>
				<h2 class="ob-h2"><?php echo esc_html( ob_t( 'about', 't_title' ) ); ?></h2>
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
				<h2><?php echo esc_html( ob_t( 'about', 'cta_title' ) ); ?></h2>
				<p><?php echo esc_html( ob_t( 'about', 'cta_text' ) ); ?></p>
			</div>
			<?php $ob_contact = get_page_by_path( 'contact' ); ?>
			<a class="ob-btn ob-btn--gold ob-cta__btn" href="<?php echo esc_url( $ob_contact ? get_permalink( $ob_contact ) : home_url( '/' ) ); ?>"><?php echo esc_html( ob_t( 'about', 'cta_btn' ) ); ?></a>
		</div>
	</section>
	<?php
endwhile;

get_footer();
