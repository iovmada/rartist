<?php
/**
 * Admin authoring experience.
 *
 * The point of this file is that adding artworks to a collection is a loop you can
 * sit in: a Works count on every collection, an "Add artwork" row action that arrives
 * with the collection preselected, filters on the artwork list, and a checklist that
 * tells you what a plate is still missing before it goes live.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Artwork list table
 * ---------------------------------------------------------------------- */

add_filter( 'manage_artwork_posts_columns', 'rartist_artwork_columns' );

function rartist_artwork_columns( array $columns ): array {
	$ordered = array(
		'cb'              => $columns['cb'] ?? '',
		'rartist_plate'   => __( 'Plate', 'rartist' ),
		'rartist_thumb'   => __( 'Image', 'rartist' ),
		'title'           => $columns['title'] ?? __( 'Title', 'rartist' ),
		'rartist_subject' => __( 'Subject', 'rartist' ),
		'collection'      => __( 'Collection', 'rartist' ),
		'rartists'        => __( 'Rartists', 'rartist' ),
		'rartist_price'   => __( 'From', 'rartist' ),
		'rartist_status'  => __( 'Availability', 'rartist' ),
		'menu_order'      => __( 'Position', 'rartist' ),
	);

	return $ordered;
}

add_action( 'manage_artwork_posts_custom_column', 'rartist_artwork_column_content', 10, 2 );

function rartist_artwork_column_content( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'rartist_plate':
			$plate = rartist_pad( rartist_field( 'plate_number', $post_id, 0 ), 3 );
			echo '' === $plate
				? '<span style="color:#b32d2e">' . esc_html__( '— missing', 'rartist' ) . '</span>'
				: '<strong>' . esc_html( $plate ) . '</strong>';
			break;

		case 'rartist_thumb':
			echo has_post_thumbnail( $post_id )
				? get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'height:auto;width:44px' ) )
				: '<span style="color:#b32d2e">' . esc_html__( '— none', 'rartist' ) . '</span>';
			break;

		case 'rartist_subject':
		case 'collection':
		case 'rartists':
			$taxonomy = 'rartist_subject' === $column ? 'subject' : ( 'collection' === $column ? 'collection' : 'rartist' );
			rartist_render_term_links( $post_id, $taxonomy );
			break;

		case 'rartist_price':
			$price = rartist_field( 'size_1_price', $post_id, '' );
			echo '' === $price ? '—' : esc_html( rartist_price( $price ) );
			break;

		case 'rartist_status':
			$status = rartist_field( 'availability', $post_id, '' );
			$labels = rartist_availability_choices();
			echo esc_html( $labels[ $status ] ?? '—' );
			break;

		case 'menu_order':
			echo (int) get_post_field( 'menu_order', $post_id );
			break;
	}
}

/**
 * Term names in a list column, each linking to that term's filtered artwork list.
 */
function rartist_render_term_links( int $post_id, string $taxonomy ): void {
	$terms = get_the_terms( $post_id, $taxonomy );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		echo '<span style="color:#b32d2e">—</span>';
		return;
	}

	$links = array();

	foreach ( $terms as $term ) {
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url(
				add_query_arg(
					array(
						'post_type' => 'artwork',
						$taxonomy   => $term->slug,
					),
					admin_url( 'edit.php' )
				)
			),
			esc_html( $term->name )
		);
	}

	$inherited = 'rartist' === $taxonomy && get_post_meta( $post_id, RARTIST_INHERIT_FLAG, true );

	echo wp_kses_post( implode( ', ', $links ) );

	if ( $inherited ) {
		echo ' <span title="' . esc_attr__( 'Inherited from the collection', 'rartist' ) . '" style="color:#787c82">↩</span>';
	}
}

/** Sort by plate number and by position. */
add_filter( 'manage_edit-artwork_sortable_columns', 'rartist_artwork_sortable_columns' );

function rartist_artwork_sortable_columns( array $columns ): array {
	$columns['rartist_plate'] = 'rartist_plate';
	$columns['menu_order']    = 'menu_order';

	return $columns;
}

add_action( 'pre_get_posts', 'rartist_artwork_admin_order' );

function rartist_artwork_admin_order( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || 'artwork' !== $query->get( 'post_type' ) ) {
		return;
	}

	if ( 'rartist_plate' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', 'plate_number' );
		$query->set( 'orderby', 'meta_value_num' );
		return;
	}

	// Default to the curator's order, which is what the front end shows.
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}

/** Collection / rartist / subject dropdowns above the artwork list. */
add_action( 'restrict_manage_posts', 'rartist_artwork_filters' );

function rartist_artwork_filters( string $post_type ): void {
	if ( 'artwork' !== $post_type ) {
		return;
	}

	foreach ( array( 'collection', 'rartist', 'subject' ) as $taxonomy ) {
		$object = get_taxonomy( $taxonomy );

		wp_dropdown_categories(
			array(
				'taxonomy'        => $taxonomy,
				'name'            => $taxonomy,
				'value_field'     => 'slug',
				'show_option_all' => sprintf(
					/* translators: %s: taxonomy plural name */
					__( 'All %s', 'rartist' ),
					strtolower( $object->labels->name )
				),
				'selected'        => sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ?? '' ) ),
				'hide_empty'      => false,
				'hierarchical'    => true,
				'orderby'         => 'name',
			)
		);
	}
}

/* -------------------------------------------------------------------------
 * Collection & rartist term tables
 * ---------------------------------------------------------------------- */

foreach ( array( 'collection', 'rartist', 'subject' ) as $rartist_taxonomy ) {
	add_filter( "manage_edit-{$rartist_taxonomy}_columns", 'rartist_term_columns' );
	add_filter( "manage_{$rartist_taxonomy}_custom_column", 'rartist_term_column_content', 10, 3 );
	add_filter( "{$rartist_taxonomy}_row_actions", 'rartist_term_row_actions', 10, 2 );
}
unset( $rartist_taxonomy );

/**
 * Replace the generic Count column with a Works column that reads like the site, and
 * drop Description — the theme reads named fields and never renders it.
 */
function rartist_term_columns( array $columns ): array {
	unset( $columns['posts'], $columns['description'] );

	$columns['rartist_works'] = __( 'Works', 'rartist' );

	return $columns;
}

/**
 * Tidy the term screens: hide the Description field the theme never reads, and the
 * Parent field where a hierarchy makes no sense (one rartist is not inside another).
 *
 * The taxonomies stay hierarchical because that is what gives collections their
 * grouping and gives every term screen the proper table.
 */
add_action( 'admin_head', 'rartist_term_screen_tidy' );

function rartist_term_screen_tidy(): void {
	$screen = get_current_screen();

	if ( ! $screen || ! in_array( $screen->base, array( 'edit-tags', 'term' ), true ) ) {
		return;
	}

	if ( ! in_array( $screen->taxonomy, array( 'collection', 'rartist', 'subject' ), true ) ) {
		return;
	}

	echo '<style>.term-description-wrap{display:none}';

	if ( 'rartist' === $screen->taxonomy ) {
		echo '.term-parent-wrap{display:none}';
	}

	echo '</style>' . "\n";
}

function rartist_term_column_content( string $content, string $column, int $term_id ): string {
	if ( 'rartist_works' !== $column ) {
		return $content;
	}

	$term = get_term( $term_id );

	if ( ! $term || is_wp_error( $term ) ) {
		return '—';
	}

	$url = add_query_arg(
		array(
			'post_type'     => 'artwork',
			$term->taxonomy => $term->slug,
		),
		admin_url( 'edit.php' )
	);

	// A parent collection holds no plates directly, so show the roll-up.
	$count = 'collection' === $term->taxonomy
		? rartist_collection_works_count( $term->term_id )
		: (int) $term->count;

	return sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( rartist_pad( $count, 2 ) ?: '00' ) );
}

/**
 * "Add artwork" on every collection row — opens a new artwork with that collection set.
 */
function rartist_term_row_actions( array $actions, WP_Term $term ): array {
	if ( 'collection' !== $term->taxonomy ) {
		return $actions;
	}

	$actions['rartist_add_artwork'] = sprintf(
		'<a href="%s">%s</a>',
		esc_url(
			add_query_arg(
				array(
					'post_type'  => 'artwork',
					'collection' => $term->term_id,
				),
				admin_url( 'post-new.php' )
			)
		),
		esc_html__( 'Add artwork', 'rartist' )
	);

	// Turning a collection into a parent category: add another one inside it.
	$actions['rartist_add_child'] = sprintf(
		'<a href="%s">%s</a>',
		esc_url(
			add_query_arg(
				array(
					'taxonomy'         => 'collection',
					'post_type'        => 'artwork',
					'rartist_parent'   => $term->term_id,
				),
				admin_url( 'edit-tags.php' )
			)
		),
		esc_html__( 'Add collection inside', 'rartist' )
	);

	return $actions;
}

/**
 * Preselect the parent when the author arrived from a collection's "Add collection
 * inside" action, so building a group is one click rather than a dropdown hunt.
 *
 * @param array<string,mixed> $args
 * @return array<string,mixed>
 */
add_filter( 'taxonomy_parent_dropdown_args', 'rartist_prefill_term_parent', 10, 3 );

function rartist_prefill_term_parent( array $args, string $taxonomy, string $context ): array {
	if ( 'new' !== $context || 'collection' !== $taxonomy ) {
		return $args;
	}

	$requested = isset( $_GET['rartist_parent'] ) ? (int) $_GET['rartist_parent'] : 0;

	if ( $requested && term_exists( $requested, 'collection' ) ) {
		$args['selected'] = $requested;
	}

	return $args;
}

/* -------------------------------------------------------------------------
 * Publish checklist
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes_artwork', 'rartist_add_checklist_box' );

function rartist_add_checklist_box(): void {
	add_meta_box(
		'rartist-checklist',
		__( 'Ready to publish', 'rartist' ),
		'rartist_render_checklist',
		'artwork',
		'side',
		'high'
	);
}

function rartist_render_checklist( WP_Post $post ): void {
	$checks = array(
		__( 'Title', 'rartist' )          => '' !== trim( (string) $post->post_title ),
		__( 'Plate image', 'rartist' )    => has_post_thumbnail( $post ),
		__( 'Plate number', 'rartist' )   => (bool) rartist_field( 'plate_number', $post->ID, 0 ),
		__( 'Collection', 'rartist' )     => (bool) rartist_artwork_collection_id( $post->ID ),
		__( 'Subject', 'rartist' )        => ! empty( get_the_terms( $post->ID, 'subject' ) ),
		__( 'Rartist credit', 'rartist' ) => ! empty( get_the_terms( $post->ID, 'rartist' ) ),
		__( 'Description', 'rartist' )    => '' !== rartist_field( 'intro', $post->ID, '' ),
		__( 'At least one size', 'rartist' ) => '' !== rartist_field( 'size_1_price', $post->ID, '' ),
		__( 'Edition size', 'rartist' )   => (bool) rartist_field( 'edition_total', $post->ID, 0 ),
	);

	echo '<ul style="margin:0">';

	foreach ( $checks as $label => $done ) {
		printf(
			'<li style="margin:0 0 4px"><span style="color:%s">%s</span> %s</li>',
			$done ? '#008a20' : '#b32d2e',
			$done ? '&#10003;' : '&#10007;',
			esc_html( $label )
		);
	}

	echo '</ul>';
	echo '<p class="description">' . esc_html__( 'Optional sections (story, in-situ) simply do not render when empty.', 'rartist' ) . '</p>';
}

/* -------------------------------------------------------------------------
 * Small touches
 * ---------------------------------------------------------------------- */

add_filter( 'enter_title_here', 'rartist_title_placeholder', 10, 2 );

function rartist_title_placeholder( string $text, WP_Post $post ): string {
	return 'artwork' === $post->post_type
		? __( 'Artwork title — e.g. Quiet Table', 'rartist' )
		: $text;
}
