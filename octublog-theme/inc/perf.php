<?php
/**
 * Mobile speed: analytics and stats scripts wait for the first interaction.
 *
 * The page HTML is rewritten so Google tag, Microsoft Clarity and Jetpack Stats are not run while
 * the page is first drawn. assets/js/main.js starts them on the first tap, scroll or key press,
 * or 8 seconds after the page has loaded.
 *
 * @package Octublog
 */

add_action(
	'template_redirect',
	function () {
		if ( is_admin() || is_feed() || is_robots() || is_trackback() || wp_doing_ajax() || is_customize_preview() || is_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		ob_start( 'ob_delay_third_party' );
	},
	0
);

/**
 * @param string $html Final page HTML.
 * @return string
 */
function ob_delay_third_party( $html ) {
	if ( false === stripos( $html, '</body>' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'#<script\b([^>]*)>(.*?)</script>#is',
		function ( $m ) {
			$attrs = $m[1];
			$body  = $m[2];
			// External: Google tag, Jetpack Stats, Clarity.
			if ( preg_match( '#\bsrc=([\'"])(https?:)?//(www\.googletagmanager\.com|stats\.wp\.com|www\.clarity\.ms)/([^\'"]*)\1#i', $attrs, $s ) ) {
				$url   = ( $s[2] ? $s[2] : 'https:' ) . '//' . $s[3] . '/' . $s[4];
				$attrs = preg_replace( '#\s*\bsrc=([\'"])[^\'"]*\1#i', '', $attrs );
				$attrs = preg_replace( '#\s*\b(type|async|defer)(=([\'"])[^\'"]*\3)?(?=\s|$)#i', '', $attrs );
				return '<script type="ob/delay" data-ob-src="' . esc_url( $url ) . '"' . $attrs . '></script>';
			}
			// Inline: the Clarity loader.
			if ( false !== strpos( $body, 'clarity.ms/tag/' ) && false === stripos( $attrs, 'src=' ) && ( false === stripos( $attrs, 'type=' ) || preg_match( '#type=([\'"])(text|application)/javascript\1#i', $attrs ) ) ) {
				$attrs = preg_replace( '#\s*\btype=([\'"])[^\'"]*\1#i', '', $attrs );
				return '<script type="ob/delay"' . $attrs . '>' . $body . '</script>';
			}
			return $m[0];
		},
		$html
	);
}
