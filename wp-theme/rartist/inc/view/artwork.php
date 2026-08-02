<?php
/**
 * Artwork view — a post becomes a display-ready array.
 *
 * Everything the artwork template and every plate card needs is assembled here, in
 * final display form: labels composed, prices formatted, empty sections returned as
 * null so a template can ask `if ( $view['story'] )` and nothing else.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param WP_Post|int|null $post
 * @return array<string,mixed>|null
 */
function rartist_artwork_view( $post = null ): ?array {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post || 'artwork' !== $post->post_type ) {
		return null;
	}

	$id            = $post->ID;
	$plate_number  = (int) rartist_field( 'plate_number', $id, 0 );
	$year          = (string) rartist_field( 'year', $id );
	$edition_total = (int) rartist_field( 'edition_total', $id, 0 );
	$sizes         = rartist_artwork_sizes( $id );
	$largest       = empty( $sizes ) ? null : $sizes[ count( $sizes ) - 1 ];
	$artists       = rartist_artwork_rartists( $id );
	$subject       = rartist_artwork_subject( $id );
	$collection    = rartist_artwork_collection( $id );
	$status        = (string) rartist_field( 'availability', $id, 'available' );

	$view = array(
		'id'           => $id,
		'title'        => get_the_title( $post ),
		'url'          => (string) get_permalink( $post ),
		'image'        => (int) get_post_thumbnail_id( $id ),
		'plate_number' => $plate_number,
		'plate_code'   => rartist_plate_code( $plate_number ),
		'plate_meta'   => rartist_meta_line(
			array( rartist_plate_code( $plate_number ), rartist_edition_label( $edition_total ) )
		),
		'intro'        => (string) rartist_field( 'intro', $id ),
		'year'         => $year,
		'medium'       => (string) rartist_field( 'medium', $id ),

		'artists'      => $artists,
		'credit_line'  => rartist_credit_line( $artists ),
		'subject'      => $subject,
		'collection'   => $collection,

		'sizes'        => $sizes,
		'price_from'   => rartist_artwork_price_from( $sizes ),

		'availability' => array(
			'status' => $status,
			'label'  => rartist_availability_label( $status ),
			'line'   => 'available' === $status
				? rartist_meta_line( array( rartist_availability_label( $status ), rartist_studio_value( 'shipping_line' ) ) )
				: rartist_availability_label( $status ),
		),

		'breadcrumb'   => rartist_artwork_breadcrumb( $subject, $plate_number ),
		// "Quiet Table, 2026  /  Shown at 140 × 170 cm"
		'image_note'   => rartist_meta_line(
			array(
				'' === $year ? get_the_title( $post ) : get_the_title( $post ) . ', ' . $year,
				$largest
					/* translators: %s: the largest available size, e.g. 140 × 170 CM */
					? sprintf( __( 'Shown at %s', 'rartist' ), $largest['label'] )
					: '',
			)
		),
		'specs'        => rartist_artwork_specs( $id, $year, $edition_total ),
		'story'        => null,
		'in_situ'      => null,
		'reserve_url'  => rartist_artwork_reserve_url( $post, $largest ),
	);

	$story_title = (string) rartist_field( 'story_title', $id );
	$story_body  = (string) rartist_field( 'story_body', $id );

	if ( '' !== $story_title || '' !== $story_body ) {
		$view['story'] = array(
			'title' => $story_title,
			'body'  => $story_body,
		);
	}

	$situ_image = (int) rartist_field( 'in_situ_image', $id, 0 );

	if ( $situ_image ) {
		$view['in_situ'] = array(
			'image'      => $situ_image,
			'size_label' => $largest ? $largest['label'] : '',
			'title'      => (string) rartist_field( 'in_situ_title', $id ),
			'body'       => (string) rartist_field( 'in_situ_body', $id ),
			'note'       => (string) rartist_field( 'styling_note', $id ),
		);
	}

	return $view;
}

/**
 * The filled size slots, smallest first.
 *
 * @return array<int,array{label:string,price:string,amount:float}>
 */
function rartist_artwork_sizes( int $artwork_id ): array {
	$sizes = array();

	for ( $slot = 1; $slot <= 3; $slot++ ) {
		$label = trim( (string) rartist_field( "size_{$slot}_label", $artwork_id ) );
		$price = rartist_field( "size_{$slot}_price", $artwork_id, '' );

		if ( '' === $label ) {
			continue;
		}

		$sizes[] = array(
			'label'  => $label,
			'price'  => '' === $price ? '' : rartist_price( $price ),
			'amount' => (float) $price,
		);
	}

	return $sizes;
}

/**
 * The lowest price across the filled sizes — the "from" figure the catalogue shows.
 *
 * @param array<int,array{label:string,price:string,amount:float}> $sizes
 */
function rartist_artwork_price_from( array $sizes ): string {
	$amounts = array_filter( array_column( $sizes, 'amount' ) );

	return empty( $amounts ) ? '' : rartist_price( min( $amounts ) );
}

/**
 * @return array<string,mixed>|null
 */
function rartist_artwork_subject( int $artwork_id ): ?array {
	$terms = get_the_terms( $artwork_id, 'subject' );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return null;
	}

	return array(
		'name' => $terms[0]->name,
		'url'  => (string) get_term_link( $terms[0] ),
	);
}

/**
 * Artworks / Still life / Plate 014.
 *
 * @param array<string,mixed>|null $subject
 * @return array<int,array{label:string,url:string}>
 */
function rartist_artwork_breadcrumb( ?array $subject, int $plate_number ): array {
	$crumbs = array(
		array(
			'label' => __( 'Artworks', 'rartist' ),
			'url'   => (string) get_post_type_archive_link( 'artwork' ),
		),
	);

	if ( $subject ) {
		$crumbs[] = array(
			'label' => $subject['name'],
			'url'   => $subject['url'],
		);
	}

	if ( $plate_number > 0 ) {
		$crumbs[] = array(
			'label' => rartist_plate_code( $plate_number ),
			'url'   => '',
		);
	}

	return $crumbs;
}

/**
 * The specification rows, empties dropped.
 *
 * @return array<int,array{label:string,value:string}>
 */
function rartist_artwork_specs( int $artwork_id, string $year, int $edition_total ): array {
	$rows = array(
		array(
			'label' => __( 'Year', 'rartist' ),
			'value' => $year,
		),
		array(
			'label' => __( 'Medium', 'rartist' ),
			'value' => (string) rartist_field( 'medium', $artwork_id ),
		),
		array(
			'label' => __( 'Paper', 'rartist' ),
			'value' => (string) rartist_field( 'paper', $artwork_id ),
		),
		array(
			'label' => __( 'Edition', 'rartist' ),
			'value' => rartist_edition_spec( $edition_total, rartist_field( 'artist_proofs', $artwork_id, 0 ) ),
		),
		array(
			'label' => __( 'Signature', 'rartist' ),
			'value' => (string) rartist_field( 'signature', $artwork_id ),
		),
	);

	return array_values( array_filter( $rows, static fn( $row ) => '' !== trim( $row['value'] ) ) );
}

/**
 * Reserving is an email while there is no cart. The subject carries the plate and size
 * so an enquiry arrives already identified.
 *
 * @param array{label:string,price:string,amount:float}|null $largest
 */
function rartist_artwork_reserve_url( WP_Post $post, ?array $largest ): string {
	$subject = sprintf(
		/* translators: 1: plate code, 2: artwork title */
		__( 'Reserve %1$s — %2$s', 'rartist' ),
		rartist_plate_code( rartist_field( 'plate_number', $post->ID, 0 ) ),
		get_the_title( $post )
	);

	return sprintf(
		'mailto:%s?subject=%s',
		rawurlencode( rartist_studio_value( 'enquiry_email' ) ),
		rawurlencode( $subject )
	);
}

/**
 * Plate-card views for other works in the same collection.
 *
 * @return array<int,array<string,mixed>>
 */
function rartist_related_artworks( int $artwork_id, int $limit = 3 ): array {
	$collection_id = rartist_artwork_collection_id( $artwork_id );

	if ( ! $collection_id ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'      => 'artwork',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'post__not_in'   => array( $artwork_id ),
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
