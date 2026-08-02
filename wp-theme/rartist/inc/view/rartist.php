<?php
/**
 * Rartist view — a term becomes a display-ready array.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param WP_Term|int $term
 * @return array<string,mixed>|null
 */
function rartist_rartist_view( $term ): ?array {
	$term = is_numeric( $term ) ? get_term( (int) $term, 'rartist' ) : $term;

	if ( ! $term instanceof WP_Term ) {
		return null;
	}

	$id         = $term->term_id;
	$discipline = (string) rartist_term_field( 'discipline', $id );
	$location   = (string) rartist_term_field( 'location', $id );

	return array(
		'id'               => $id,
		'name'             => $term->name,
		// The frames break the name after the first word ("Mara / Vell").
		'display_name'     => preg_replace( '/\s+/', "\n", $term->name, 1 ),
		'url'              => (string) get_term_link( $term ),
		'number'           => (string) rartist_term_field( 'number', $id ),
		'index_label'      => rartist_meta_line(
			array( __( 'Curated rartist', 'rartist' ), (string) rartist_term_field( 'number', $id ) )
		),
		'discipline'       => $discipline,
		'location'         => $location,
		'discipline_line'  => rartist_meta_line( array( $discipline, $location ) ),
		'statement'        => (string) rartist_term_field( 'statement', $id ),
		'portrait'         => (int) rartist_term_field( 'portrait', $id, 0 ),
		'portrait_caption' => (string) rartist_term_field( 'portrait_caption', $id ),
		'studio_image'     => (int) rartist_term_field( 'studio_image', $id, 0 ),
		'bio_title'        => (string) rartist_term_field( 'bio_title', $id ),
		'bio_1'            => (string) rartist_term_field( 'bio_body_1', $id ),
		'bio_2'            => (string) rartist_term_field( 'bio_body_2', $id ),
		'facts'            => (string) rartist_term_field( 'facts', $id ),
		'quote'            => (string) rartist_term_field( 'quote', $id ),
		'works_count'      => (int) $term->count,
	);
}

/**
 * Artwork views credited to a rartist, in curator's order.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_rartist_works( int $rartist_id, int $limit = -1 ): array {
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
					'taxonomy' => 'rartist',
					'field'    => 'term_id',
					'terms'    => $rartist_id,
				),
			),
		)
	);

	return array_values( array_filter( array_map( 'rartist_artwork_view', $posts ) ) );
}

/**
 * The collections a rartist appears in, derived from their artworks rather than from
 * the credit meta, so the relation always matches what is actually published.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_rartist_collections( int $rartist_id ): array {
	$collections = array();

	foreach ( rartist_rartist_works( $rartist_id ) as $work ) {
		if ( ! empty( $work['collection'] ) ) {
			$collections[ $work['collection']['id'] ] = $work['collection'];
		}
	}

	return array_values( $collections );
}

/**
 * "03 works for rooms where people gather." — the featured-collection line, composed
 * from the collection's own caption so it needs no extra field.
 *
 * @param array<string,mixed> $collection
 */
function rartist_featured_collection_line( array $collection ): string {
	$caption = trim( (string) $collection['hero_caption'] );

	if ( '' === $caption ) {
		$caption = $collection['name'];
	}

	// Lower-case the opening word so it reads on from the count.
	$caption = function_exists( 'mb_strtolower' )
		? mb_substr( $caption, 0, 1 ) === mb_strtoupper( mb_substr( $caption, 0, 1 ) )
			? mb_strtolower( mb_substr( $caption, 0, 1 ) ) . mb_substr( $caption, 1 )
			: $caption
		: lcfirst( $caption );

	$count = rartist_pad( $collection['works_count'], 2 ) ?: '00';

	return rtrim( sprintf( '%s %s', $count, $caption ), '.' ) . '.';
}

/**
 * Views for every rartist credited on a post.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_artwork_rartists( int $artwork_id ): array {
	$terms = get_the_terms( $artwork_id, 'rartist' );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return array();
	}

	return array_values( array_filter( array_map( 'rartist_rartist_view', $terms ) ) );
}

/**
 * "By Mara Vell" / "By Mara Vell & Jonas Rhee".
 *
 * @param array<int,array<string,mixed>> $artists
 */
function rartist_credit_line( array $artists ): string {
	$names = array_column( $artists, 'name' );

	if ( empty( $names ) ) {
		return '';
	}

	if ( count( $names ) === 1 ) {
		$joined = $names[0];
	} else {
		$last   = array_pop( $names );
		$joined = implode( ', ', $names ) . ' & ' . $last;
	}

	/* translators: %s: artist name or names */
	return sprintf( __( 'By %s', 'rartist' ), $joined );
}
