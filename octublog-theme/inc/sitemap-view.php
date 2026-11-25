<?php
/**
 * Readable XML sitemaps for people.
 *
 * Yoast SEO 28.9 removed the XSL stylesheet that made its sitemaps readable in a browser, because
 * browsers are dropping XSLT. A visitor whose browser asks for a web page now gets the same sitemap as a
 * table; search engines and tools, and anyone adding ?raw=1, still get the plain XML.
 *
 * @package Octublog
 */

/**
 * Sitemap path of this request, or ''.
 */
function ob_sitemap_request_path() {
	$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security
	return ( $path && preg_match( '#^/(sitemap_index|[a-z0-9_-]+-sitemap[0-9]*)\.xml$#i', $path ) ) ? $path : '';
}

/**
 * True only for a real browser asking for a page (never for crawlers or tools).
 */
function ob_sitemap_wants_html() {
	if ( isset( $_GET['raw'] ) || isset( $_SERVER['HTTP_X_OB_RAW'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return false;
	}
	$ua     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
	$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( (string) $_SERVER['HTTP_ACCEPT'] ) : '';
	if ( 0 !== strpos( $accept, 'text/html' ) ) {
		return false;
	}
	if ( preg_match( '/bot|crawl|spider|slurp|google|bing|yandex|duckduck|baidu|facebook|twitter|linkedin|pinterest|curl|wget|python|java|go-http|node|axios|lighthouse|ahrefs|semrush|screaming|indexnow|validator|feed/i', $ua ) ) {
		return false;
	}
	return (bool) preg_match( '/mozilla\/.*(chrome|safari|firefox|edg)/i', $ua );
}

add_action(
	'init',
	function () {
		$path = ob_sitemap_request_path();
		if ( ! $path || ! ob_sitemap_wants_html() ) {
			return;
		}
		$res = wp_remote_get(
			home_url( $path ),
			array(
				'timeout' => 25,
				'headers' => array( 'Accept' => '*/*', 'X-OB-Raw' => '1', 'User-Agent' => 'Octublog-Sitemap-View' ),
			)
		);
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return;
		}
		$xml = simplexml_load_string( wp_remote_retrieve_body( $res ) );
		if ( ! $xml ) {
			return;
		}
		$xml->registerXPathNamespace( 's', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
		$xml->registerXPathNamespace( 'i', 'http://www.google.com/schemas/sitemap-image/1.1' );
		$fmt   = function ( $d ) {
			$t = strtotime( (string) $d );
			return $t ? gmdate( 'Y-m-d H:i', $t ) . ' UTC' : '';
		};
		$rows  = '';
		$index = 'sitemapindex' === $xml->getName();
		if ( $index ) {
			$head = '<th>Sitemap</th><th>Dernière modification</th>';
			foreach ( $xml->xpath( '//s:sitemap' ) as $n ) {
				$loc   = (string) $n->children()->loc;
				$rows .= '<tr><td><a href="' . esc_url( $loc ) . '">' . esc_html( $loc ) . '</a></td><td>' . esc_html( $fmt( $n->children()->lastmod ) ) . '</td></tr>';
			}
			$title = 'Plan du site XML';
			$count = count( $xml->xpath( '//s:sitemap' ) );
			$intro = 'Ce plan du site XML aide les moteurs de recherche à explorer le site. Il contient ' . $count . ' plans du site.';
		} else {
			$head = '<th>URL</th><th>Images</th><th>Dernière modification</th>';
			$urls = $xml->xpath( '//s:url' );
			foreach ( $urls as $n ) {
				$loc   = (string) $n->children()->loc;
				$imgs  = count( $n->xpath( 'i:image' ) );
				$rows .= '<tr><td><a href="' . esc_url( $loc ) . '">' . esc_html( $loc ) . '</a></td><td>' . (int) $imgs . '</td><td>' . esc_html( $fmt( $n->children()->lastmod ) ) . '</td></tr>';
			}
			$title = 'Plan du site XML';
			$intro = 'Ce plan du site contient ' . count( $urls ) . ' URL. <a href="' . esc_url( home_url( '/sitemap_index.xml' ) ) . '">← Retour à l’index</a>';
		}
		nocache_headers();
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		header( 'Content-Type: text/html; charset=UTF-8' );
		header( 'X-Robots-Tag: noindex, follow' );
		header( 'Vary: Accept' );
		echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,follow"><title>' . esc_html( $title ) . ' - ' . esc_html( get_bloginfo( 'name' ) ) . '</title><style>'
			. 'body{margin:0;background:#f7f7f7;color:#222;font:15px/1.5 system-ui,-apple-system,Segoe UI,Roboto,sans-serif}.w{max-width:1100px;margin:0 auto;padding:32px 20px}h1{font-size:28px;margin:0 0 8px}p{margin:0 0 20px;color:#555}a{color:#a8977b;text-decoration:none}a:hover{text-decoration:underline}'
			. 'table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #e3e3e3}th,td{text-align:left;padding:10px 14px;border-bottom:1px solid #eee;overflow-wrap:anywhere}th{background:#111518;color:#fff;font-weight:600;font-size:13px;text-transform:uppercase;letter-spacing:.04em}tr:nth-child(even) td{background:#fafafa}td a{color:#6b5d44}'
			. '</style></head><body><div class="w"><h1>' . esc_html( $title ) . '</h1><p>' . wp_kses_post( $intro ) . '</p><table><thead><tr>' . $head . '</tr></thead><tbody>' . $rows . '</tbody></table></div></body></html>'; // phpcs:ignore WordPress.Security.EscapeOutput
		exit;
	},
	1
);
