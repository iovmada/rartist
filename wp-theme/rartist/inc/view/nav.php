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
 * Every collection as a dropdown row, parents followed by their children.
 *
 * Counts roll up, so a parent category reports everything beneath it. Built from the
 * same option rows as the main panel, so the two dropdowns are one component.
 *
 * @return array<int,array{label:string,meta:string,url:string,depth:int,current:bool}>
 */
function rartist_collection_nav_items(): array {
	$queried = is_tax( 'collection' ) ? (int) get_queried_object_id() : 0;
	$rows    = array();

	foreach ( rartist_all_collections() as $collection ) {
		// Children are listed under their parent, not twice.
		if ( ! empty( $collection['parent'] ) ) {
			continue;
		}

		$rows[] = rartist_collection_nav_row( $collection, 0, $queried );

		foreach ( $collection['children'] as $child ) {
			$rows[] = rartist_collection_nav_row( $child, 1, $queried );
		}
	}

	return $rows;
}

/**
 * Every rartist as a dropdown row, in their numbered order.
 *
 * The meta line carries the discipline and location the profile shows, falling back to a
 * work count for an artist whose fields are not filled in yet.
 *
 * @return array<int,array{label:string,meta:string,url:string,depth:int,current:bool}>
 */
function rartist_rartist_nav_items(): array {
	$queried = is_tax( 'rartist' ) ? (int) get_queried_object_id() : 0;
	$rows    = array();

	foreach ( rartist_curated_rartists() as $artist ) {
		$meta = $artist['discipline_line'];

		if ( '' === $meta ) {
			$meta = sprintf(
				/* translators: %s: zero-padded number of works */
				__( '%s works', 'rartist' ),
				rartist_pad( $artist['works_count'], 2 ) ?: '00'
			);
		}

		$rows[] = array(
			'label'   => $artist['name'],
			'meta'    => $meta,
			'url'     => $artist['url'],
			'depth'   => 0,
			'current' => $queried === $artist['id'],
			// The avatar field, or the portrait cropped, beside the name.
			'avatar'  => $artist['avatar'],
			// Stands in for a portrait that has not been uploaded yet, so the rows
			// keep their alignment instead of jumping left.
			'initial' => mb_substr( $artist['name'], 0, 1 ),
		);
	}

	return $rows;
}

/**
 * @param array<string,mixed> $collection
 * @return array{label:string,meta:string,url:string,depth:int,current:bool}
 */
function rartist_collection_nav_row( array $collection, int $depth, int $queried ): array {
	return array(
		'label'   => $collection['name'],
		'meta'    => sprintf(
			/* translators: %s: zero-padded number of works */
			__( '%s works', 'rartist' ),
			rartist_pad( $collection['works_count'], 2 ) ?: '00'
		),
		'url'     => $collection['url'],
		'depth'   => $depth,
		'current' => $queried === (int) $collection['id'],
	);
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
