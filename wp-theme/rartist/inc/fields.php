<?php
/**
 * ACF wiring.
 *
 * Field groups live in the theme as Local JSON (acf-json/), so they travel with git
 * and appear on every environment without anyone clicking through the admin. Edit a
 * field group in wp-admin and ACF rewrites the JSON here — commit the diff.
 *
 * Nothing outside this file calls get_field(). Templates and the view layer go through
 * rartist_field(), so a deactivated ACF yields blank sections instead of fatals.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

function rartist_has_acf(): bool {
	return function_exists( 'get_field' );
}

/** Write field-group JSON into the theme. */
add_filter( 'acf/settings/save_json', 'rartist_acf_json_path' );

function rartist_acf_json_path(): string {
	return RARTIST_DIR . '/acf-json';
}

/** Read field-group JSON from the theme (in addition to anywhere else ACF looks). */
add_filter( 'acf/settings/load_json', 'rartist_acf_json_paths' );

function rartist_acf_json_paths( array $paths ): array {
	$paths[] = RARTIST_DIR . '/acf-json';

	return $paths;
}

/**
 * Read a field with a guaranteed type-stable fallback.
 *
 * @param string          $selector Field name.
 * @param int|string|null $id       Post ID, or "term_{id}" for a taxonomy term.
 * @param mixed           $default  Returned when ACF is absent or the value is empty.
 * @return mixed
 */
function rartist_field( string $selector, $id = null, $default = '' ) {
	if ( ! rartist_has_acf() ) {
		return $default;
	}

	$value = get_field( $selector, $id );

	if ( null === $value || '' === $value || array() === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Same, for a taxonomy term — saves every caller composing "term_{$id}".
 *
 * Accepts a plain term ID or ACF's own "term_9" form, since the whole point of this
 * wrapper is that callers should not have to know which one ACF wants.
 *
 * @param int|string $term_id
 * @return mixed
 */
function rartist_term_field( string $selector, $term_id, $default = '' ) {
	if ( is_string( $term_id ) && 0 === strpos( $term_id, 'term_' ) ) {
		$term_id = (int) substr( $term_id, 5 );
	}

	return rartist_field( $selector, 'term_' . (int) $term_id, $default );
}

/**
 * Preselect the collection when the author arrived from a collection's "Add artwork"
 * row action, so adding plate after plate to one collection needs no dropdown work.
 *
 * @param mixed $value
 * @return mixed
 */
add_filter( 'acf/load_value/name=collection', 'rartist_prefill_collection', 10, 3 );

function rartist_prefill_collection( $value, $post_id, $field ) {
	if ( ! empty( $value ) || ! is_admin() ) {
		return $value;
	}

	if ( 'post-new.php' !== ( $GLOBALS['pagenow'] ?? '' ) ) {
		return $value;
	}

	$requested = isset( $_GET['collection'] ) ? (int) $_GET['collection'] : 0;

	if ( ! $requested || ! term_exists( $requested, 'collection' ) ) {
		return $value;
	}

	return $requested;
}

/**
 * The theme is usable without ACF, but not authorable. Say so once, in the admin.
 */
add_action( 'admin_notices', 'rartist_acf_missing_notice' );

function rartist_acf_missing_notice(): void {
	if ( rartist_has_acf() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
		esc_html__( 'Rartist Studio:', 'rartist' ),
		esc_html__( 'Advanced Custom Fields (free) is not active, so artwork, collection and rartist fields cannot be edited. Pages will render with those sections omitted.', 'rartist' )
	);
}
