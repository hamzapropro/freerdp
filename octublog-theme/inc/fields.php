<?php
/**
 * Editable texts for the designed pages.
 *
 * About and Contact: a "Textes de la page" panel in the page editor (post meta).
 * Homepage: Appearance > Customize > "Octublog – Accueil" (theme mods).
 * Every field has a default, so an empty field shows the designed text.
 *
 * @package Octublog
 */

/**
 * Field registry: scope => key => [ label, default, type ].
 *
 * @return array
 */
function ob_fields() {
	static $fields = null;
	if ( null !== $fields ) {
		return $fields;
	}
	$fields = array(
		'about'   => array(
			'badge'      => array( 'Étiquette du haut', 'À propos d’Octublog', 'text' ),
			'title'      => array( 'Titre principal', 'Des idées claires pour simplifier votre quotidien.', 'text' ),
			'lead'       => array( 'Texte d’introduction', 'Octublog est un magazine où chaque rubrique est confiée à une seule personne. Organisation de la maison, enfants, bien-être, tech, création, voyage : on publie des guides concrets, testés, avec des budgets réels.', 'textarea' ),
			'btn1'       => array( 'Bouton 1 (vers l’accueil)', 'Lire nos guides', 'text' ),
			'btn2'       => array( 'Bouton 2 (vers l’équipe)', 'Rencontrer l’équipe', 'text' ),
			'm_eyebrow'  => array( 'Méthode : petit titre', 'Notre méthode', 'text' ),
			'm_title'    => array( 'Méthode : titre', 'Quatre règles qu’on applique à chaque article', 'text' ),
			'r1_t'       => array( 'Règle 1 : titre', 'Testé avant d’être conseillé', 'text' ),
			'r1_d'       => array( 'Règle 1 : texte', 'Aucun outil ni produit n’est recommandé sans avoir été utilisé par l’auteur de la rubrique.', 'textarea' ),
			'r2_t'       => array( 'Règle 2 : titre', 'Des budgets réels', 'text' ),
			'r2_d'       => array( 'Règle 2 : texte', 'Un prix réaliste, une méthode précise, un temps honnête. Et une alternative moins chère quand elle existe.', 'textarea' ),
			'r3_t'       => array( 'Règle 3 : titre', 'Les ratés aussi', 'text' ),
			'r3_d'       => array( 'Règle 3 : texte', 'Nos tutoriels montrent les étapes qui coincent, pas seulement le résultat final.', 'textarea' ),
			'r4_t'       => array( 'Règle 4 : titre', 'Sans jugement', 'text' ),
			'r4_d'       => array( 'Règle 4 : texte', 'On écrit pour que vous refermiez la page un peu moins seul, jamais plus jugé.', 'textarea' ),
			't_eyebrow'  => array( 'Équipe : petit titre', 'L’équipe', 'text' ),
			't_title'    => array( 'Équipe : titre', 'Une rubrique, une autrice ou un auteur', 'text' ),
			'cta_title'  => array( 'Bandeau final : titre', 'Une question, une idée de sujet ?', 'text' ),
			'cta_text'   => array( 'Bandeau final : texte', 'Écrivez à l’équipe ou directement à l’auteur de la rubrique.', 'text' ),
			'cta_btn'    => array( 'Bandeau final : bouton', 'Contactez-nous', 'text' ),
		),
		'contact' => array(
			'badge'      => array( 'Étiquette du haut', 'Contact', 'text' ),
			'title'      => array( 'Titre principal', 'Écrivez-nous. On lit tous les messages.', 'text' ),
			'lead'       => array( 'Texte d’introduction', 'Une question sur un article, une erreur repérée, un sujet que vous aimeriez voir traité ou une proposition de partenariat : envoyez-nous un mot.', 'textarea' ),
			'form_title' => array( 'Titre du formulaire', 'Votre message', 'text' ),
			'note'       => array( 'Note sous le formulaire', 'Nous répondons sous 24 à 48 heures ouvrées.', 'text' ),
			'emails'     => array( 'E-mails (un par ligne)', "contact@octublog.com\nadmin@octublog.com", 'textarea' ),
			'address'    => array( 'Adresse (une ligne par ligne)', "8 Place Bellecour\n69002 Lyon, France", 'textarea' ),
			'phone'      => array( 'Téléphone (laisser vide pour masquer)', '', 'textarea' ),
			'a_title'    => array( 'Auteurs : titre', 'Écrire à un auteur', 'text' ),
			'a_text'     => array( 'Auteurs : texte', 'Pour une question sur une rubrique précise.', 'text' ),
		),
		'home'    => array(
			'hero_title'   => array( 'À la une : titre', 'À la une', 'text' ),
			'recent_title' => array( 'Colonne « récents » : titre', 'Récemment publiés', 'text' ),
			'latest_title' => array( 'Derniers articles : titre', 'Derniers articles', 'text' ),
			'more_btn'     => array( 'Bouton « plus d’articles »', 'Voir plus d’articles', 'text' ),
			'see_all'      => array( 'Lien « Voir tout » des rubriques', 'Voir tout', 'text' ),
			'quote'        => array( 'Citation (bandeau Enfance & Famille)', 'Il n’existe pas de méthode parentale universelle.', 'textarea' ),
			'team_title'   => array( 'Experts : titre', 'Nos expertes et experts', 'text' ),
			'team_text'    => array( 'Experts : texte', 'Chaque rubrique est tenue par une personne qui la pratique au quotidien.', 'text' ),
		),
	);
	return $fields;
}

/**
 * Value of an editable text (falls back to the designed default).
 *
 * @param string   $scope about|contact|home.
 * @param string   $key   Field key.
 * @param int|null $post  Page id (about/contact); defaults to the current post.
 * @return string
 */
function ob_t( $scope, $key, $post = null ) {
	$fields = ob_fields();
	if ( ! isset( $fields[ $scope ][ $key ] ) ) {
		return '';
	}
	$default = $fields[ $scope ][ $key ][1];
	if ( 'home' === $scope ) {
		$legacy = 'quote' === $key ? get_theme_mod( 'ob_home_quote', null ) : null;
		$value  = get_theme_mod( 'ob_t_home_' . $key, null );
		if ( null === $value || '' === $value ) {
			$value = $legacy;
		}
		return ( null === $value || '' === $value ) ? $default : $value;
	}
	$post  = $post ? $post : get_the_ID();
	$value = $post ? get_post_meta( $post, 'ob_t_' . $key, true ) : '';
	// Phone may be intentionally empty: only fall back when the meta was never saved.
	if ( 'phone' === $key && $post && metadata_exists( 'post', $post, 'ob_t_phone' ) ) {
		return (string) $value;
	}
	return ( '' === $value || false === $value ) ? $default : $value;
}

/**
 * Lines of a multi-line field, trimmed, empty ones dropped.
 *
 * @param string $scope Scope.
 * @param string $key   Key.
 * @return string[]
 */
function ob_t_lines( $scope, $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) ob_t( $scope, $key ) ) ) ) );
}

/* Editor panel for the About and Contact pages. */
function ob_fields_scope_for_post( $post ) {
	$post = get_post( $post );
	if ( ! $post || 'page' !== $post->post_type ) {
		return '';
	}
	$map = array(
		'a-propos-de-nous' => 'about',
		'contact'          => 'contact',
	);
	return isset( $map[ $post->post_name ] ) ? $map[ $post->post_name ] : '';
}

add_action(
	'add_meta_boxes_page',
	function ( $post ) {
		if ( ! ob_fields_scope_for_post( $post ) ) {
			return;
		}
		add_meta_box( 'ob-texts', __( 'Textes de la page', 'octublog' ), 'ob_fields_box', 'page', 'normal', 'high' );
	}
);

function ob_fields_box( $post ) {
	$scope  = ob_fields_scope_for_post( $post );
	$fields = ob_fields();
	wp_nonce_field( 'ob_texts_' . $post->ID, 'ob_texts_nonce' );
	echo '<p>' . esc_html__( 'Modifiez ici les textes de la page. Un champ vidé reprend le texte du design. Les articles, les photos et l’équipe se mettent à jour tout seuls.', 'octublog' ) . '</p>';
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields[ $scope ] as $key => $f ) {
		$value = ob_t( $scope, $key, $post->ID );
		echo '<tr><th scope="row"><label for="ob_t_' . esc_attr( $key ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
		if ( 'textarea' === $f[2] ) {
			echo '<textarea class="large-text" rows="3" id="ob_t_' . esc_attr( $key ) . '" name="ob_t[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
		} else {
			echo '<input class="large-text" type="text" id="ob_t_' . esc_attr( $key ) . '" name="ob_t[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	if ( 'contact' === $scope ) {
		echo '<p class="description">' . esc_html__( 'Le formulaire se modifie dans le bloc WPForms du contenu de la page.', 'octublog' ) . '</p>';
	}
}

add_action(
	'save_post_page',
	function ( $post_id ) {
		if ( ! isset( $_POST['ob_texts_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ob_texts_nonce'] ) ), 'ob_texts_' . $post_id ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
		$scope = ob_fields_scope_for_post( $post_id );
		if ( ! $scope || empty( $_POST['ob_t'] ) || ! is_array( $_POST['ob_t'] ) ) {
			return;
		}
		$fields = ob_fields();
		$posted = wp_unslash( $_POST['ob_t'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		foreach ( $fields[ $scope ] as $key => $f ) {
			if ( ! isset( $posted[ $key ] ) ) {
				continue;
			}
			$raw   = (string) $posted[ $key ];
			$clean = 'textarea' === $f[2] ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
			// Same as the default: store nothing, so future default changes apply.
			if ( $clean === $f[1] && 'phone' !== $key ) {
				delete_post_meta( $post_id, 'ob_t_' . $key );
			} else {
				update_post_meta( $post_id, 'ob_t_' . $key, $clean );
			}
		}
	}
);

/* Customizer section for the homepage texts. */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'ob_home',
			array(
				'title'    => __( 'Octublog – Accueil', 'octublog' ),
				'priority' => 30,
			)
		);
		foreach ( ob_fields()['home'] as $key => $f ) {
			$wp_customize->add_setting(
				'ob_t_home_' . $key,
				array(
					'default'           => '',
					'sanitize_callback' => 'textarea' === $f[2] ? 'sanitize_textarea_field' : 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'ob_t_home_' . $key,
				array(
					'section'     => 'ob_home',
					'label'       => $f[0],
					'description' => __( 'Vide = texte du design : ', 'octublog' ) . $f[1],
					'type'        => $f[2],
				)
			);
		}
	}
);

/* Make edits visible at once, whatever the page cache holds. */
add_action(
	'customize_save_after',
	function () {
		do_action( 'litespeed_purge_all' );
	}
);
add_action(
	'save_post_page',
	function ( $post_id ) {
		if ( ob_fields_scope_for_post( $post_id ) ) {
			do_action( 'litespeed_purge_post', $post_id );
			do_action( 'litespeed_purge_all' );
		}
	},
	20
);
