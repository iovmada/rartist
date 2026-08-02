<?php
/**
 * Collection view — a term becomes a display-ready array.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param WP_Term|int $term
 * @return array<string,mixed>|null
 */
function rartist_collection_view( $term ): ?array {
	$term = is_numeric( $term ) ? get_term( (int) $term, 'collection' ) : $term;

	if ( ! $term instanceof WP_Term ) {
		return null;
	}

	$id     = $term->term_id;
	$number = (string) rartist_term_field( 'number', $id );

	return array(
		'id'              => $id,
		'name'            => $term->name,
		// The frames break a two-word collection title after the first word.
		'display_name'    => preg_replace( '/\s+/', "\n", $term->name, 1 ),
		'url'             => (string) get_term_link( $term ),
		'number'          => $number,
		/* translators: %s: two-digit collection number */
		'number_label'    => '' === $number ? '' : sprintf( __( 'Collection %s', 'rartist' ), $number ),
		'index_label'     => rartist_meta_line(
			array(
				'' === $number ? __( 'Collection', 'rartist' ) : sprintf( __( 'Collection %s', 'rartist' ), $number ),
				sprintf(
					/* translators: %d: number of works */
					_n( '%d work', '%d works', (int) $term->count, 'rartist' ),
					(int) $term->count
				),
			)
		),
		'hero_image'      => (int) rartist_term_field( 'hero_image', $id, 0 ),
		'hero_caption'    => (string) rartist_term_field( 'hero_caption', $id ),
		'intro_title'     => (string) rartist_term_field( 'intro_title', $id ),
		'intro_body'      => (string) rartist_term_field( 'intro_body', $id ),
		'details'         => (string) rartist_term_field( 'details', $id ),
		'collector_quote' => (string) rartist_term_field( 'collector_quote', $id ),
		'shipping_note'   => (string) rartist_term_field( 'shipping_note', $id ),
		'works_title'     => (string) rartist_term_field( 'works_title', $id, __( 'The edition', 'rartist' ) ),
		'featured'        => (bool) rartist_term_field( 'featured', $id, false ),
		'feature_order'   => (int) rartist_term_field( 'feature_order', $id, 0 ),
		// Counting everything beneath it, so a grouping parent reports its real total.
		'works_count'     => rartist_collection_works_count( $id ),
		'own_count'       => (int) $term->count,
		'parent'          => rartist_collection_ancestor( $term ),
		'children'        => rartist_collection_children( $id ),
		'artists'         => array_values(
			array_filter( array_map( 'rartist_rartist_view', rartist_collection_artists( $id ) ) )
		),
		// "COLLECTION 01 / 03 WORKS / SPRING 2027" — the block beside the hero title.
		// The count rolls up child collections, so a grouping parent is not "00 works".
		'stats'           => implode(
			"\n",
			array_filter(
				array(
					'' === $number ? '' : sprintf( __( 'Collection %s', 'rartist' ), $number ),
					sprintf(
						/* translators: %s: zero-padded number of works */
						__( '%s works', 'rartist' ),
						rartist_pad( rartist_collection_works_count( $id ), 2 ) ?: '00'
					),
					rartist_studio_value( 'season' ),
				)
			)
		),
	);
}

/**
 * The parent collection, as a flat array.
 *
 * Deliberately not a full view: a full parent view would build its children, one of
 * which is this collection, and the recursion would never end.
 *
 * @return array{id:int,name:string,url:string}|null
 */
function rartist_collection_ancestor( WP_Term $term ): ?array {
	if ( ! $term->parent ) {
		return null;
	}

	$parent = get_term( $term->parent, 'collection' );

	if ( ! $parent || is_wp_error( $parent ) ) {
		return null;
	}

	return array(
		'id'   => (int) $parent->term_id,
		'name' => $parent->name,
		'url'  => (string) get_term_link( $parent ),
	);
}

/**
 * Child collections, as flat arrays with the fields the group listing needs.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_collection_children( int $collection_id ): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'collection',
			'parent'     => $collection_id,
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$children = array();

	foreach ( $terms as $term ) {
		$number = (string) rartist_term_field( 'number', $term->term_id );

		$children[] = array(
			'id'           => (int) $term->term_id,
			'name'         => $term->name,
			'display_name' => preg_replace( '/\s+/', "\n", $term->name, 1 ),
			'url'          => (string) get_term_link( $term ),
			'number'       => $number,
			/* translators: %s: two-digit collection number */
			'number_label' => '' === $number ? '' : sprintf( __( 'Collection %s', 'rartist' ), $number ),
			'hero_image'   => (int) rartist_term_field( 'hero_image', $term->term_id, 0 ),
			'intro_body'   => (string) rartist_term_field( 'intro_body', $term->term_id ),
			'works_count'  => rartist_collection_works_count( (int) $term->term_id ),
			'feature_order' => (int) rartist_term_field( 'feature_order', $term->term_id, 0 ),
			'featured'     => (bool) rartist_term_field( 'featured', $term->term_id, false ),
		);
	}

	usort( $children, static fn( $a, $b ) => array( $a['feature_order'], $a['number'] ) <=> array( $b['feature_order'], $b['number'] ) );

	return $children;
}

/**
 * Artwork views for a collection, in curator's order.
 *
 * Includes artworks in child collections, so a grouping parent shows everything beneath
 * it — that is WordPress's default for a hierarchical tax_query, and it is what makes a
 * parent category useful rather than an empty shell.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_collection_works( int $collection_id, int $limit = -1 ): array {
	$posts = get_posts(
		array(
			'post_type'      => 'artwork',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'collection',
					'field'    => 'term_id',
					'terms'    => $collection_id,
				),
			),
		)
	);

	return array_values( array_filter( array_map( 'rartist_artwork_view', $posts ) ) );
}

/**
 * Every collection, for the Collections page.
 *
 * Featured ones lead, in their chosen order, then the rest by number. Nothing is left
 * out: the "Feature on the catalogue" flag decides what the catalogue promotes, never
 * whether a collection is reachable.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_all_collections(): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'collection',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$views = array_values( array_filter( array_map( 'rartist_collection_view', $terms ) ) );

	usort(
		$views,
		static function ( $a, $b ) {
			$rank_a = array( $a['featured'] ? 0 : 1, $a['feature_order'] ?: PHP_INT_MAX, $a['number'], $a['name'] );
			$rank_b = array( $b['featured'] ? 0 : 1, $b['feature_order'] ?: PHP_INT_MAX, $b['number'], $b['name'] );

			return $rank_a <=> $rank_b;
		}
	);

	return $views;
}

/**
 * Featured collections for the catalogue's index section, in the order set on each term.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_featured_collections(): array {
	$terms = get_terms(
		array(
			'taxonomy'   => 'collection',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	$views = array_values( array_filter( array_map( 'rartist_collection_view', $terms ) ) );
	$views = array_values( array_filter( $views, static fn( $view ) => $view['featured'] ) );

	usort( $views, static fn( $a, $b ) => $a['feature_order'] <=> $b['feature_order'] );

	return $views;
}

/**
 * The collection an artwork belongs to, as a view.
 *
 * @return array<string,mixed>|null
 */
function rartist_artwork_collection( int $artwork_id ): ?array {
	$collection_id = rartist_artwork_collection_id( $artwork_id );

	return $collection_id ? rartist_collection_view( $collection_id ) : null;
}
