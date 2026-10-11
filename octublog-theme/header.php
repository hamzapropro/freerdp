<?php
/**
 * Site header: logo, rubriques menu, search panel and mobile drawer.
 *
 * @package Octublog
 */

$ob_rubs    = ob_rubriques();
$ob_about   = get_page_by_path( 'a-propos-de-nous' );
$ob_contact = get_page_by_path( 'contact' );
$ob_current = is_category() ? get_queried_object_id() : 0;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ob-skip" href="#contenu"><?php esc_html_e( 'Aller au contenu', 'octublog' ); ?></a>

<header class="ob-header" data-ob-header>
	<div class="ob-header__bar ob-wrap">
		<a class="ob-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<img src="<?php echo esc_url( ob_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="160" height="40">
		</a>

		<nav class="ob-nav" aria-label="<?php esc_attr_e( 'Navigation principale', 'octublog' ); ?>">
			<a class="ob-nav__link<?php echo is_front_page() ? ' is-current' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
			<div class="ob-dd" data-ob-dropdown>
				<button class="ob-nav__link ob-dd__toggle<?php echo $ob_current ? ' is-current' : ''; ?>" type="button" aria-expanded="false" aria-controls="ob-dd-menu">
					<?php esc_html_e( 'Rubriques', 'octublog' ); ?><span class="ob-caret" aria-hidden="true"></span>
				</button>
				<div class="ob-dd__panel" id="ob-dd-menu" hidden>
					<div class="ob-dd__list">
						<?php foreach ( $ob_rubs as $ob_rub ) : ?>
							<a class="ob-dd__item" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
								<span class="ob-dot-icon"><?php echo ob_icon( $ob_rub['icon'], 16 ); // phpcs:ignore ?></span><?php echo esc_html( $ob_rub['name'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php if ( $ob_about ) : ?>
				<a class="ob-nav__link<?php echo is_page( $ob_about->ID ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_permalink( $ob_about ) ); ?>"><?php esc_html_e( 'À propos', 'octublog' ); ?></a>
			<?php endif; ?>
		</nav>

		<div class="ob-header__actions">
			<button class="ob-iconbtn" type="button" data-ob-search-toggle aria-expanded="false" aria-controls="ob-search" aria-label="<?php esc_attr_e( 'Rechercher', 'octublog' ); ?>"><?php echo ob_icon( 'search', 18 ); // phpcs:ignore ?></button>
			<?php if ( $ob_contact ) : ?>
				<a class="ob-btn ob-btn--gold ob-header__cta<?php echo is_page( $ob_contact->ID ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_permalink( $ob_contact ) ); ?>"><?php esc_html_e( 'Contactez-nous', 'octublog' ); ?></a>
			<?php endif; ?>
			<button class="ob-iconbtn ob-iconbtn--dark ob-header__burger" type="button" data-ob-menu-toggle aria-expanded="false" aria-controls="ob-drawer" aria-label="<?php esc_attr_e( 'Menu', 'octublog' ); ?>">
				<span class="ob-burger-open"><?php echo ob_icon( 'menu', 18 ); // phpcs:ignore ?></span>
				<span class="ob-burger-close"><?php echo ob_icon( 'x', 18 ); // phpcs:ignore ?></span>
			</button>
		</div>
	</div>

	<div class="ob-chips" aria-label="<?php esc_attr_e( 'Rubriques', 'octublog' ); ?>">
		<a class="ob-chip<?php echo is_front_page() ? ' is-current' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
		<?php foreach ( $ob_rubs as $ob_rub ) : ?>
			<a class="ob-chip<?php echo ( $ob_current === $ob_rub['term']->term_id ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>"><?php echo esc_html( $ob_rub['name'] ); ?></a>
		<?php endforeach; ?>
	</div>

	<div class="ob-search-panel" id="ob-search" hidden>
		<div class="ob-search-panel__inner">
			<form class="ob-searchfield" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo ob_icon( 'search', 22 ); // phpcs:ignore ?>
				<label class="screen-reader-text" for="ob-search-input"><?php esc_html_e( 'Rechercher', 'octublog' ); ?></label>
				<input id="ob-search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Rechercher un guide, une idée…', 'octublog' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button class="ob-iconbtn ob-iconbtn--cream" type="button" data-ob-search-toggle aria-label="<?php esc_attr_e( 'Fermer', 'octublog' ); ?>"><?php echo ob_icon( 'x', 18 ); // phpcs:ignore ?></button>
			</form>
			<div class="ob-search-panel__group">
				<span class="ob-eyebrow"><?php esc_html_e( 'Rubriques', 'octublog' ); ?></span>
				<div class="ob-pills">
					<?php foreach ( $ob_rubs as $ob_rub ) : ?>
						<a class="ob-pill ob-pill--icon" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>"><?php echo ob_icon( $ob_rub['icon'], 16 ); // phpcs:ignore ?><?php echo esc_html( $ob_rub['name'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="ob-drawer" id="ob-drawer" hidden>
		<a class="ob-drawer__big" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Accueil', 'octublog' ); ?></a>
		<div class="ob-drawer__group">
			<span class="ob-eyebrow"><?php esc_html_e( 'Rubriques', 'octublog' ); ?></span>
			<?php foreach ( $ob_rubs as $ob_rub ) : ?>
				<a class="ob-drawer__item" href="<?php echo esc_url( $ob_rub['url'] ); ?>" style="<?php echo ob_rub_style( $ob_rub ); // phpcs:ignore ?>">
					<span class="ob-dot-icon ob-dot-icon--lg"><?php echo ob_icon( $ob_rub['icon'], 18 ); // phpcs:ignore ?></span><?php echo esc_html( $ob_rub['name'] ); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<div class="ob-drawer__group">
			<?php if ( $ob_about ) : ?>
				<a class="ob-drawer__mid" href="<?php echo esc_url( get_permalink( $ob_about ) ); ?>"><?php esc_html_e( 'À propos', 'octublog' ); ?></a>
			<?php endif; ?>
			<?php if ( $ob_contact ) : ?>
				<a class="ob-drawer__mid" href="<?php echo esc_url( get_permalink( $ob_contact ) ); ?>"><?php esc_html_e( 'Contact', 'octublog' ); ?></a>
			<?php endif; ?>
		</div>
		<?php if ( $ob_contact ) : ?>
			<a class="ob-btn ob-btn--gold ob-btn--block" href="<?php echo esc_url( get_permalink( $ob_contact ) ); ?>"><?php esc_html_e( 'Contactez-nous', 'octublog' ); ?></a>
		<?php endif; ?>
	</div>
</header>

<main id="contenu" class="ob-main">
