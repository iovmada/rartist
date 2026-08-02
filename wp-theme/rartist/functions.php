<?php
/**
 * Rartist Studio — theme bootstrap.
 *
 * This file does nothing but define constants and load inc/. Keep it that way:
 * every behaviour belongs in a named file under inc/ so it can be found by name.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

define( 'RARTIST_VERSION', '0.1.0' );
define( 'RARTIST_DIR', get_template_directory() );
define( 'RARTIST_URI', get_template_directory_uri() );

/**
 * Prices are displayed, nothing is sold yet. The single switch below hides every
 * commerce affordance (bag counters, add-to-bag). Flip it to true when a cart exists.
 */
define( 'RARTIST_COMMERCE', false );

/** Currency symbol prefixed to every price. */
define( 'RARTIST_CURRENCY', '€' );

require_once RARTIST_DIR . '/inc/format.php';
require_once RARTIST_DIR . '/inc/setup.php';
require_once RARTIST_DIR . '/inc/assets.php';
require_once RARTIST_DIR . '/inc/fields.php';
require_once RARTIST_DIR . '/inc/content-model.php';

// View layer: post/term → display-ready array. Templates read nothing else.
require_once RARTIST_DIR . '/inc/view/globals.php';
require_once RARTIST_DIR . '/inc/view/nav.php';
require_once RARTIST_DIR . '/inc/view/rartist.php';
require_once RARTIST_DIR . '/inc/view/collection.php';
require_once RARTIST_DIR . '/inc/view/artwork.php';
require_once RARTIST_DIR . '/inc/view/catalogue.php';

if ( is_admin() ) {
	require_once RARTIST_DIR . '/inc/admin.php';
}
