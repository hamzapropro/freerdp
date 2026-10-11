<?php
/**
 * Builds the block markup of the About and Contact pages (run once on the server).
 * Usage: $c = ob_seed_content( 'about' | 'contact' );
 */

function ob_b_p( $text, $cls = '' ) {
	$c = $cls ? ' {"className":"' . $cls . '"}' : '';
	$a = $cls ? ' class="' . $cls . '"' : '';
	return "<!-- wp:paragraph$c --><p$a>$text</p><!-- /wp:paragraph -->\n";
}
function ob_b_h( $level, $text, $cls = '' ) {
	$attrs = '{"level":' . $level . ( $cls ? ',"className":"' . $cls . '"' : '' ) . '}';
	return "<!-- wp:heading $attrs --><h$level class=\"wp-block-heading" . ( $cls ? ' ' . $cls : '' ) . "\">$text</h$level><!-- /wp:heading -->\n";
}
function ob_b_g( $cls, $inner, $tag = 'div', $anchor = '' ) {
	$attrs = array();
	if ( 'div' !== $tag ) {
		$attrs[] = '"tagName":"' . $tag . '"';
	}
	if ( $anchor ) {
		$attrs[] = '"anchor":"' . $anchor . '"';
	}
	$attrs[] = '"className":"' . $cls . '"';
	$id = $anchor ? ' id="' . $anchor . '"' : '';
	return '<!-- wp:group {' . implode( ',', $attrs ) . "} --><$tag$id class=\"wp-block-group $cls\">\n$inner</$tag><!-- /wp:group -->\n";
}
function ob_b_btns( $cls, $buttons ) {
	$html = '';
	foreach ( $buttons as $b ) {
		$html .= '<!-- wp:button {"className":"' . $b[2] . '"} --><div class="wp-block-button ' . $b[2] . '"><a class="wp-block-button__link wp-element-button" href="' . $b[1] . '">' . $b[0] . "</a></div><!-- /wp:button -->\n";
	}
	return '<!-- wp:buttons {"className":"' . $cls . '"} --><div class="wp-block-buttons ' . $cls . "\">\n$html</div><!-- /wp:buttons -->\n";
}
function ob_b_sc( $code ) {
	return "<!-- wp:shortcode -->\n$code\n<!-- /wp:shortcode -->\n";
}

function ob_seed_content( $which ) {
	if ( 'about' === $which ) {
		$hero = ob_b_g(
			'ob-abouthero__text',
			ob_b_p( 'À propos d’Octublog', 'ob-pill ob-pill--gold' )
			. ob_b_h( 1, 'Des idées claires pour simplifier votre quotidien.', 'ob-pagehero__title ob-pagehero__title--wide' )
			. ob_b_p( 'Octublog est un magazine où chaque rubrique est confiée à une seule personne. Organisation de la maison, enfants, bien-être, tech, création, voyage : on publie des guides concrets, testés, avec des budgets réels.', 'ob-contact-hero__lead' )
			. ob_b_btns( 'ob-abouthero__btns', array( array( 'Lire nos guides', '/', 'ob-bb' ), array( 'Rencontrer l’équipe', '#equipe', 'ob-bb ob-bb--outline' ) ) )
		);
		$out  = ob_b_g( 'ob-wrap ob-abouthero', $hero . ob_b_sc( '[ob_mosaic]' ), 'section' );
		$out .= ob_b_sc( '[ob_strip]' );
		$rules = array(
			array( '01', 'Testé avant d’être conseillé', 'Aucun outil ni produit n’est recommandé sans avoir été utilisé par l’auteur de la rubrique.', 'gold' ),
			array( '02', 'Des budgets réels', 'Un prix réaliste, une méthode précise, un temps honnête. Et une alternative moins chère quand elle existe.', 'cream' ),
			array( '03', 'Les ratés aussi', 'Nos tutoriels montrent les étapes qui coincent, pas seulement le résultat final.', 'cream' ),
			array( '04', 'Sans jugement', 'On écrit pour que vous refermiez la page un peu moins seul, jamais plus jugé.', 'cream' ),
		);
		$cards = '';
		foreach ( $rules as $r ) {
			$cards .= ob_b_g( 'ob-rule ob-rule--' . $r[3], ob_b_p( $r[0], 'ob-rule__n' ) . ob_b_h( 3, $r[1] ) . ob_b_p( $r[2] ) );
		}
		$out .= ob_b_g(
			'ob-wrap ob-section',
			ob_b_g( 'ob-introhead', ob_b_p( 'Notre méthode', 'ob-eyebrow' ) . ob_b_h( 2, 'Quatre règles qu’on applique à chaque article', 'ob-h2' ) ) . ob_b_g( 'ob-rules', $cards ),
			'section'
		);
		$out .= ob_b_g(
			'ob-band ob-band--cream',
			ob_b_g(
				'ob-wrap ob-section',
				ob_b_g( 'ob-introhead', ob_b_p( 'L’équipe', 'ob-eyebrow' ) . ob_b_h( 2, 'Une rubrique, une autrice ou un auteur', 'ob-h2' ) ) . ob_b_sc( '[ob_team]' )
			),
			'section',
			'equipe'
		);
		$out .= ob_b_g(
			'ob-wrap ob-section',
			ob_b_g(
				'ob-cta',
				ob_b_g( 'ob-cta__text', ob_b_h( 2, 'Une question, une idée de sujet ?' ) . ob_b_p( 'Écrivez à l’équipe ou directement à l’auteur de la rubrique.' ) )
				. ob_b_btns( 'ob-cta-btns', array( array( 'Contactez-nous', '/contact/', 'ob-bb ob-cta__btn' ) ) )
			),
			'section'
		);
		return $out;
	}

	// Contact.
	$out  = ob_b_g(
		'ob-wrap ob-contact-hero',
		ob_b_p( 'Contact', 'ob-pill ob-pill--gold' )
		. ob_b_h( 1, 'Écrivez-nous. On lit tous les messages.', 'ob-pagehero__title ob-pagehero__title--wide' )
		. ob_b_p( 'Une question sur un article, une erreur repérée, un sujet que vous aimeriez voir traité ou une proposition de partenariat : envoyez-nous un mot.', 'ob-contact-hero__lead' ),
		'section'
	);
	$form = ob_b_g(
		'ob-contact__form',
		ob_b_h( 2, 'Votre message', 'ob-h2' )
		. ob_b_sc( '[wpforms id="485" title="false"]' )
		. ob_b_p( 'Vos données servent uniquement à répondre à votre demande, conformément à la <a href="/politique-de-confidentialite/">politique de confidentialité</a>. Nous répondons sous 24 à 48 heures ouvrées.', 'ob-contact__legal' )
	);
	$side = ob_b_g(
		'ob-contact__side',
		ob_b_sc( '[ob_contact_info emails="contact@octublog.com|admin@octublog.com" address="8 Place Bellecour|69002 Lyon, France" phone=""]' )
		. ob_b_g(
			'ob-authorlist',
			ob_b_h( 2, 'Écrire à un auteur', 'ob-authorlist__title' ) . ob_b_p( 'Pour une question sur une rubrique précise.' ) . ob_b_sc( '[ob_authors_links]' )
		)
	);
	$out .= ob_b_g( 'ob-wrap ob-contact', $form . $side, 'section' );
	return $out;
}
