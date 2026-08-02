<?php
/**
 * The content model.
 *
 *   Artwork (CPT)  — belongs to exactly ONE collection and ONE subject.
 *   Collection     — taxonomy; credited to one or more rartists.
 *   Rartist        — taxonomy; applied to artworks so artist archives stay one query.
 *   Subject        — taxonomy; drives the catalogue filters and the artwork breadcrumb.
 *
 * The artist relation has a single source of truth: the collection. An artwork saved
 * with an empty artist field inherits its collection's artists, and stays in sync when
 * the collection's credits change. Set artists on the artwork itself (a collaborative
 * collection, a one-off plate) and inheritance steps aside for that artwork.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/** Post meta flag: this artwork's artists were inherited, so keep them in sync. */
const RARTIST_INHERIT_FLAG = '_rartist_artists_inherited';

add_action( 'init', 'rartist_register_content_model' );

function rartist_register_content_model(): void {
	register_post_type(
		'artwork',
		array(
			'labels'        => array(
				'name'               => __( 'Artworks', 'rartist' ),
				'singular_name'      => __( 'Artwork', 'rartist' ),
				'add_new'            => __( 'Add artwork', 'rartist' ),
				'add_new_item'       => __( 'Add artwork', 'rartist' ),
				'edit_item'          => __( 'Edit artwork', 'rartist' ),
				'new_item'           => __( 'New artwork', 'rartist' ),
				'view_item'          => __( 'View artwork', 'rartist' ),
				'search_items'       => __( 'Search artworks', 'rartist' ),
				'not_found'          => __( 'No artworks yet', 'rartist' ),
				'not_found_in_trash' => __( 'No artworks in the trash', 'rartist' ),
				'all_items'          => __( 'All artworks', 'rartist' ),
				'menu_name'          => __( 'Artworks', 'rartist' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'artworks',
				'with_front' => false,
			),
			// No editor: every piece of prose on the page is a named field.
			// page-attributes gives the native Order box, which drives the grid sequence.
			'supports'      => array( 'title', 'thumbnail', 'page-attributes', 'revisions' ),
			'menu_icon'     => 'dashicons-format-image',
			'menu_position' => 20,
			'show_in_rest'  => true,
		)
	);

	rartist_register_taxonomy(
		'collection',
		'collections',
		__( 'Collection', 'rartist' ),
		__( 'Collections', 'rartist' ),
		array(
			'name_field_description'   => __( 'The collection\'s title. It appears in the hero, in breadcrumbs and wherever the collection is credited.', 'rartist' ),
			'slug_field_description'   => __( 'The web address, e.g. <code>/collections/domestic-colour/</code>. Leave empty and it follows the title.', 'rartist' ),
			'parent_field_description' => __( 'Only for a collection that sits inside another one. Leave as None for a normal collection.', 'rartist' ),
		)
	);

	rartist_register_taxonomy(
		'rartist',
		'rartists',
		__( 'Rartist', 'rartist' ),
		__( 'Rartists', 'rartist' ),
		array(
			'name_field_description' => __( 'The artist\'s name, as it appears on their profile and in every credit.', 'rartist' ),
			'slug_field_description' => __( 'The web address, e.g. <code>/rartists/mara-vell/</code>. Leave empty and it follows the name.', 'rartist' ),
		)
	);

	rartist_register_taxonomy(
		'subject',
		'subjects',
		__( 'Subject', 'rartist' ),
		__( 'Subjects', 'rartist' ),
		array(
			'name_field_description'   => __( 'The subject, e.g. Still Life. It appears in the catalogue filter row and in artwork breadcrumbs.', 'rartist' ),
			'slug_field_description'   => __( 'The web address, e.g. <code>/subjects/still-life/</code>. Leave empty and it follows the name.', 'rartist' ),
			'parent_field_description' => __( 'Only for a subject that sits inside another one. Leave as None normally.', 'rartist' ),
		)
	);
}

/**
 * All three taxonomies share the same shape, so they share one registration.
 *
 * The labels are spelled out rather than left to WordPress: the defaults call every
 * screen "Category", offer Jazz and Bebop as an example hierarchy, and describe a
 * Description field this theme never renders. On a catalogue admin that is noise.
 *
 * @param array<string,string> $descriptions Overrides for the term-form help texts.
 */
function rartist_register_taxonomy( string $taxonomy, string $slug, string $singular, string $plural, array $descriptions = array() ): void {
	$labels = array_merge(
		array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			/* translators: %s: taxonomy plural name */
			'all_items'                  => sprintf( __( 'All %s', 'rartist' ), $plural ),
			/* translators: %s: taxonomy singular name */
			'edit_item'                  => sprintf( __( 'Edit %s', 'rartist' ), $singular ),
			/* translators: %s: taxonomy singular name */
			'view_item'                  => sprintf( __( 'View %s', 'rartist' ), $singular ),
			/* translators: %s: taxonomy singular name */
			'update_item'                => sprintf( __( 'Update %s', 'rartist' ), $singular ),
			/* translators: %s: taxonomy singular name */
			'add_new_item'               => sprintf( __( 'Add %s', 'rartist' ), strtolower( $singular ) ),
			/* translators: %s: taxonomy singular name */
			'new_item_name'              => sprintf( __( 'New %s name', 'rartist' ), strtolower( $singular ) ),
			/* translators: %s: taxonomy singular name */
			'parent_item'                => sprintf( __( 'Parent %s', 'rartist' ), strtolower( $singular ) ),
			/* translators: %s: taxonomy singular name */
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'rartist' ), strtolower( $singular ) ),
			/* translators: %s: taxonomy plural name */
			'search_items'               => sprintf( __( 'Search %s', 'rartist' ), strtolower( $plural ) ),
			/* translators: %s: taxonomy plural name */
			'not_found'                  => sprintf( __( 'No %s yet', 'rartist' ), strtolower( $plural ) ),
			/* translators: %s: taxonomy plural name */
			'no_terms'                   => sprintf( __( 'No %s', 'rartist' ), strtolower( $plural ) ),
			/* translators: %s: taxonomy plural name */
			'back_to_items'              => sprintf( __( '&larr; Back to %s', 'rartist' ), strtolower( $plural ) ),
			/* translators: %s: taxonomy plural name */
			'items_list'                 => sprintf( __( '%s list', 'rartist' ), $plural ),
			/* translators: %s: taxonomy plural name */
			'items_list_navigation'      => sprintf( __( '%s list navigation', 'rartist' ), $plural ),
			// The theme reads named fields, never the native description.
			'desc_field_description'     => __( 'Not used by this theme — the page reads the fields below instead.', 'rartist' ),
			'popular_items'              => null,
			'separate_items_with_commas' => null,
			'choose_from_most_used'      => null,
		),
		$descriptions
	);

	register_taxonomy(
		$taxonomy,
		'artwork',
		array(
			'labels'            => $labels,
			'public'            => true,
			// Hierarchical purely for the admin table: it gives a proper term list
			// with a description column instead of the tag cloud UI.
			'hierarchical'      => true,
			'show_admin_column' => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => $slug,
				'with_front' => false,
			),
			// Assignment happens through the ACF fields on the artwork screen, which
			// enforce "exactly one" where the model requires it. Without ACF, fall back
			// to the native metabox so the site is still editable.
			'meta_box_cb'       => rartist_has_acf() ? false : null,
		)
	);
}

/* -------------------------------------------------------------------------
 * Artist inheritance
 * ---------------------------------------------------------------------- */

/**
 * Rartist term IDs credited on a collection.
 *
 * @return array<int,int>
 */
function rartist_collection_artists( int $collection_id ): array {
	$artists = get_term_meta( $collection_id, 'artists', true );

	if ( ! is_array( $artists ) ) {
		$artists = '' === $artists || null === $artists ? array() : array( $artists );
	}

	return array_values( array_filter( array_map( 'intval', $artists ) ) );
}

/**
 * How many artworks a collection holds, counting everything in its child collections.
 *
 * A parent collection groups others rather than holding plates directly, so its own
 * term count is 0 — the number that matters is the roll-up.
 */
function rartist_collection_works_count( int $collection_id ): int {
	$term = get_term( $collection_id, 'collection' );

	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	$children = get_term_children( $collection_id, 'collection' );

	if ( empty( $children ) || is_wp_error( $children ) ) {
		return (int) $term->count;
	}

	$total = (int) $term->count;

	foreach ( $children as $child_id ) {
		$child = get_term( (int) $child_id, 'collection' );

		if ( $child && ! is_wp_error( $child ) ) {
			$total += (int) $child->count;
		}
	}

	return $total;
}

/**
 * The single collection an artwork belongs to, or 0.
 */
function rartist_artwork_collection_id( int $artwork_id ): int {
	$terms = wp_get_object_terms( $artwork_id, 'collection', array( 'fields' => 'ids' ) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return 0;
	}

	return (int) $terms[0];
}

/**
 * Fill an artwork's artists from its collection when the artwork doesn't name its own.
 *
 * Runs late on save_post so it sees the terms ACF just wrote (ACF saves at priority 10).
 */
add_action( 'save_post_artwork', 'rartist_sync_artwork_artists', 30 );

function rartist_sync_artwork_artists( $artwork_id ): void {
	$artwork_id = (int) $artwork_id;

	if ( wp_is_post_revision( $artwork_id ) || wp_is_post_autosave( $artwork_id ) ) {
		return;
	}

	$own = wp_get_object_terms( $artwork_id, 'rartist', array( 'fields' => 'ids' ) );
	$own = is_wp_error( $own ) ? array() : array_map( 'intval', $own );

	$inherited = (bool) get_post_meta( $artwork_id, RARTIST_INHERIT_FLAG, true );

	// Artists named on the artwork itself win, and switch inheritance off — unless
	// they are exactly what we last inherited, which means the author didn't touch them.
	if ( ! empty( $own ) && ! $inherited ) {
		update_post_meta( $artwork_id, RARTIST_INHERIT_FLAG, 0 );
		return;
	}

	$collection_id   = rartist_artwork_collection_id( $artwork_id );
	$from_collection = $collection_id ? rartist_collection_artists( $collection_id ) : array();

	// Compare as sets: term order is not meaningful on either side.
	$same = array() === array_merge(
		array_diff( $own, $from_collection ),
		array_diff( $from_collection, $own )
	);

	if ( ! empty( $own ) && ! $same && $inherited ) {
		// The author edited an inherited set — from now on it is theirs.
		update_post_meta( $artwork_id, RARTIST_INHERIT_FLAG, 0 );
		return;
	}

	wp_set_object_terms( $artwork_id, $from_collection, 'rartist', false );
	update_post_meta( $artwork_id, RARTIST_INHERIT_FLAG, 1 );
}

/**
 * When a collection's credits change, push them onto every artwork still inheriting.
 *
 * Term field values are written by ACF, so this waits for acf/save_post rather than
 * edited_term — at edited_term the new artists aren't in the database yet.
 */
add_action( 'acf/save_post', 'rartist_sync_collection_credits', 20 );

function rartist_sync_collection_credits( $acf_id ): void {
	if ( ! is_string( $acf_id ) || 0 !== strpos( $acf_id, 'term_' ) ) {
		return;
	}

	$term = get_term( (int) substr( $acf_id, 5 ) );

	if ( ! $term || is_wp_error( $term ) || 'collection' !== $term->taxonomy ) {
		return;
	}

	$artists = rartist_collection_artists( $term->term_id );

	$artworks = get_posts(
		array(
			'post_type'      => 'artwork',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'collection',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
			'meta_query'     => array(
				array(
					'key'   => RARTIST_INHERIT_FLAG,
					'value' => '1',
				),
			),
		)
	);

	foreach ( $artworks as $artwork_id ) {
		wp_set_object_terms( (int) $artwork_id, $artists, 'rartist', false );
	}
}

/* -------------------------------------------------------------------------
 * Front-end queries
 * ---------------------------------------------------------------------- */

/**
 * Curator's order everywhere artworks are listed.
 *
 * The Position field (menu_order) decides which plate lands in which slot of the
 * catalogue's rhythm, so date order would scramble a composed page. The design shows
 * no pagination either, hence the high per-page count.
 */
add_action( 'pre_get_posts', 'rartist_front_end_query' );

function rartist_front_end_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$lists_artworks = $query->is_post_type_archive( 'artwork' )
		|| $query->is_tax( array( 'subject', 'collection', 'rartist' ) );

	if ( ! $lists_artworks ) {
		return;
	}

	$query->set(
		'orderby',
		array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		)
	);

	$query->set( 'posts_per_page', 48 );
}

/* -------------------------------------------------------------------------
 * Counts — the nav dropdown shows live numbers ("24 AVAILABLE WORKS").
 * ---------------------------------------------------------------------- */

function rartist_artwork_count(): int {
	$counts = wp_count_posts( 'artwork' );

	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

function rartist_term_count( string $taxonomy ): int {
	$count = wp_count_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);

	return is_wp_error( $count ) ? 0 : (int) $count;
}
