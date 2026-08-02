<?php
/**
 * Stylesheet and script loading.
 *
 * One ordered manifest, two modes. While developing, every part is enqueued on its
 * own so a change needs nothing but a reload. For production, bin/build-css.sh
 * concatenates the same list into assets/css/main.css and that single file is used.
 * There is no build dependency either way.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * The stylesheet order. Foundations, then components, then page-specific layout.
 *
 * @return array<int,string> Paths relative to the theme root.
 */
function rartist_css_manifest(): array {
	return array(
		'assets/css/00-tokens.css',
		'assets/css/05-fonts.css',
		'assets/css/10-base.css',
		'assets/css/20-type.css',
		'assets/css/30-layout.css',
		'assets/css/components/logo.css',
		'assets/css/components/topbar.css',
		'assets/css/components/nav-panel.css',
		'assets/css/components/site-footer.css',
		'assets/css/components/link-arrow.css',
		'assets/css/components/size-selector.css',
		'assets/css/components/reserve-bar.css',
		'assets/css/components/detail-rows.css',
		'assets/css/components/plate-card.css',
		'assets/css/components/artist-card.css',
		'assets/css/components/works-row.css',
		'assets/css/components/quote-band.css',
		'assets/css/components/button-solid.css',
		'assets/css/components/filter-bar.css',
		'assets/css/templates/artwork.css',
		'assets/css/templates/collection.css',
		'assets/css/templates/rartist.css',
		'assets/css/templates/catalogue.css',
	);
}

/**
 * Stylesheets loaded only where they apply, outside the bundle.
 *
 * The prelaunch page's CSS is the whole static design in one 900-line file and is used
 * on exactly one URL, so it never belongs in a site-wide bundle.
 *
 * @return array<string,string> handle => path
 */
function rartist_css_conditional(): array {
	$sheets = array();

	if ( rartist_is_prelaunch() ) {
		$sheets['rartist-prelaunch'] = 'assets/css/templates/prelaunch.css';
	}

	return $sheets;
}

/**
 * Use the concatenated bundle when one has been built and we are not debugging.
 */
function rartist_use_css_bundle(): bool {
	return ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) && file_exists( RARTIST_DIR . '/assets/css/main.css' );
}

add_action( 'wp_enqueue_scripts', 'rartist_enqueue_assets' );

function rartist_enqueue_assets(): void {
	if ( rartist_use_css_bundle() ) {
		wp_enqueue_style( 'rartist', RARTIST_URI . '/assets/css/main.css', array(), rartist_asset_version( 'assets/css/main.css' ) );
	} else {
		$previous = array();

		foreach ( rartist_css_manifest() as $path ) {
			$handle = 'rartist-' . sanitize_title( str_replace( array( 'assets/css/', '.css', '/' ), array( '', '', '-' ), $path ) );
			wp_enqueue_style( $handle, RARTIST_URI . '/' . $path, $previous, rartist_asset_version( $path ) );
			$previous = array( $handle );
		}
	}

	foreach ( rartist_css_conditional() as $handle => $path ) {
		wp_enqueue_style( $handle, RARTIST_URI . '/' . $path, array(), rartist_asset_version( $path ) );
	}

	// The shell script runs everywhere — the prelaunch page carries the nav too.
	wp_enqueue_script( 'rartist', RARTIST_URI . '/assets/js/main.js', array(), rartist_asset_version( 'assets/js/main.js' ), true );

	if ( rartist_is_prelaunch() ) {
		wp_enqueue_script( 'rartist-prelaunch', RARTIST_URI . '/assets/js/prelaunch.js', array(), rartist_asset_version( 'assets/js/prelaunch.js' ), true );
	}

	if ( is_singular( 'artwork' ) ) {
		wp_enqueue_script( 'rartist-artwork', RARTIST_URI . '/assets/js/artwork.js', array(), rartist_asset_version( 'assets/js/artwork.js' ), true );
	}
}

/**
 * File mtime as the cache buster, so nothing is ever stale in development.
 */
function rartist_asset_version( string $path ): string {
	$file = RARTIST_DIR . '/' . $path;

	return file_exists( $file ) ? (string) filemtime( $file ) : RARTIST_VERSION;
}

/**
 * Preload the two faces that appear above the fold on every page.
 */
add_action( 'wp_head', 'rartist_preload_fonts', 1 );

function rartist_preload_fonts(): void {
	foreach ( array( 'playfair-display-400-latin.woff2', 'geist-mono-400-latin.woff2' ) as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( RARTIST_URI . '/assets/fonts/' . $font )
		);
	}
}
