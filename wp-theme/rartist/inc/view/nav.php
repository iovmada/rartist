<?php
/**
 * Navigation view.
 *
 * The four destinations are the content model, so they are derived rather than
 * configured — including the counts the design shows beside them ("24 available
 * works", "02 curated groups"), which come straight from the database.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int,array{key:string,label:string,short:string,meta:string,url:string,current:bool}>
 */
function rartist_nav_items(): array {
	$artworks    = rartist_artwork_count();
	$collections = rartist_term_count( 'collection' );

	$items = array(
		array(
			'key'   => 'artworks',
			'label' => 'Artworks',
			'short' => 'Artworks',
			'meta'  => sprintf(
				/* translators: %d: number of published artworks */
				_n( '%d available work', '%d available works', $artworks, 'rartist' ),
				$artworks
			),
			'url'   => (string) get_post_type_archive_link( 'artwork' ),
		),
		array(
			'key'   => 'collections',
			'label' => 'Collections',
			'short' => 'Collections',
			'meta'  => sprintf(
				/* translators: %s: zero-padded number of collections */
				__( '%s curated groups', 'rartist' ),
				rartist_pad( $collections, 2 ) ?: '00'
			),
			'url'   => rartist_page_url( 'collections', trailingslashit( (string) get_post_type_archive_link( 'artwork' ) ) . '#collections' ),
		),
		array(
			'key'   => 'rartists',
			'label' => 'Curated rartists',
			'short' => 'Curated rartists',
			'meta'  => __( 'Meet the artists', 'rartist' ),
			'url'   => rartist_page_url( 'rartists' ),
		),
		array(
			'key'   => 'about',
			'label' => 'About the studio',
			'short' => 'About',
			'meta'  => __( 'Our approach', 'rartist' ),
			'url'   => rartist_page_url( 'about' ),
		),
	);

	foreach ( $items as &$item ) {
		$item['current'] = rartist_nav_item_is_current( $item['key'] );
	}

	return $items;
}

/**
 * Which destination the current request belongs to.
 */
function rartist_nav_item_is_current( string $key ): bool {
	switch ( $key ) {
		case 'artworks':
			return is_post_type_archive( 'artwork' ) || is_singular( 'artwork' ) || is_tax( 'subject' );

		case 'collections':
			return is_tax( 'collection' ) || is_page( 'collections' );

		case 'rartists':
			return is_tax( 'rartist' ) || is_page( 'rartists' );

		case 'about':
			return is_page( 'about' );
	}

	return false;
}
