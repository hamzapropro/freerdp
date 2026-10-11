<?php
/**
 * Octublog theme bootstrap.
 *
 * @package Octublog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OB_VERSION', '1.0.0' );
define( 'OB_DIR', get_template_directory() );
define( 'OB_URI', get_template_directory_uri() );

require OB_DIR . '/inc/setup.php';
require OB_DIR . '/inc/helpers.php';
require OB_DIR . '/inc/content.php';
require OB_DIR . '/inc/fields.php';
require OB_DIR . '/inc/shortcodes.php';
