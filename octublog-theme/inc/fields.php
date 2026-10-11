<?php
/**
 * Editable texts for the designed pages.
 *
 * Homepage: Appearance > Customize > "Octublog – Accueil" (theme mods).
 * About and Contact are built with blocks in the page editor.
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
 * @param string   $scope home.
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
	return '';
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
