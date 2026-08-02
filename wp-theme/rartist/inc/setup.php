<?php
/**
 * Theme supports, image sizes and menu locations.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'rartist_setup' );

function rartist_setup(): void {
	load_theme_textdomain( 'rartist', RARTIST_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );

	/*
	 * Plates are deliberately varied in aspect ratio — the catalogue grid is built
	 * around that. Never hard-crop: every size below constrains width only.
	 */
	add_image_size( 'rartist-plate', 900, 9999, false );
	add_image_size( 'rartist-hero', 2000, 9999, false );
	add_image_size( 'rartist-portrait', 1000, 9999, false );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation (dropdown)', 'rartist' ),
			'footer'  => __( 'Footer links', 'rartist' ),
		)
	);
}

/**
 * The CPT and taxonomies introduce new permalink structures, so the rules have to
 * be rebuilt the first time the theme is activated.
 */
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

/**
 * Is this the prelaunch page?
 *
 * It brings its own top bar and footer, so the theme's shell stands down: a prelaunch
 * page carrying a catalogue nav would advertise a shop that has not opened.
 */
function rartist_is_prelaunch(): bool {
	return is_front_page() && ! is_paged();
}

/**
 * Fall back to the lockup as the favicon until a Site Icon is set in Settings → General.
 * assets/img/logo.svg keeps the original #212020 fill, unlike the inlined currentColor one.
 */
add_action( 'wp_head', 'rartist_favicon' );

function rartist_favicon(): void {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
		esc_url( RARTIST_URI . '/assets/img/logo.svg' )
	);
}

/**
 * The prelaunch page opens on the dark hero, so the mobile browser chrome should meet
 * it rather than flash paper white.
 */
add_action( 'wp_head', 'rartist_theme_colour' );

function rartist_theme_colour(): void {
	if ( rartist_is_prelaunch() ) {
		echo '<meta name="theme-color" content="#0a0a0a">' . "\n";
	}
}
