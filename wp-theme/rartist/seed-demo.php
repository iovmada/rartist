<?php
/**
 * Demo content — the design's own content, verbatim.
 *
 *   wp eval-file wp-content/themes/rartist/seed-demo.php
 *
 * Every string below is the copy from the Pencil document
 * (~/Documents/rartist/rartist.pen), and every image is the exact file that node's
 * fill points at, so the site reads as the frames do.
 *
 * Re-runnable: matched by slug and updated in place. Seeded objects carry a
 * _rartist_seed marker; a previously seeded artwork that is no longer listed here is
 * deleted, so the demo set cannot drift.
 *
 * Where the design and the model disagree, the design wins and the note says why:
 *
 *  - The frames show "12 WORKS" and "08 WORKS" per collection but only draw six
 *    plates in total. Our counts are real, so a collection page will say 3, not 12.
 *  - Quiet Table appears in both collections in the frames (placeholder reuse). An
 *    artwork belongs to exactly one collection, so each plate is assigned to the
 *    collection whose artist made it.
 *  - Only Quiet Table has three sizes in the design, and only one price (€180) for
 *    all three. That is reproduced exactly: sizes 2 and 3 carry no price, so the
 *    displayed price does not change when they are selected.
 *  - Only Quiet Table has story and in-situ sections drawn, so the other five plates
 *    leave those fields empty — which is also the cleanest demonstration that
 *    optional sections disappear rather than break.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

const RARTIST_SEED_FLAG   = '_rartist_seed';
const RARTIST_SEED_SOURCE = '_rartist_seed_source';
const RARTIST_SEED_IMAGES = '/Users/mada/Documents/rartist/images';

/* -------------------------------------------------------------------------
 * Images — keyed exactly as the .pen fills reference them
 * ---------------------------------------------------------------------- */

$images = array(
	'after-rain'         => 'generated-1785660675409.png',
	'two-figures'        => 'generated-1785660676251.png',
	'the-garden'         => 'generated-1785660677760.png',
	'quiet-table'        => 'generated-1785660678780.png',
	'blue-room'          => 'generated-1785660679847.png',
	'wild-flowers'       => 'generated-1785660681155.png',
	'domestic-hero'      => 'generated-1785660706033.png',
	'after-dark-hero'    => 'generated-1785660709896.png',
	'mara-portrait'      => 'generated-1785660954521.png',
	'jonas-portrait'     => 'generated-1785660978898.png',
	'mara-studio'        => 'generated-1785661182722.png',
	'jonas-studio'       => 'generated-1785661242027.png',
	'quiet-table-insitu' => 'generated-1785661348280.png',
);

/**
 * Import one of the design's images into the media library, once.
 */
function rartist_seed_image( string $file, string $title ): int {
	$path = RARTIST_SEED_IMAGES . '/' . $file;

	if ( ! file_exists( $path ) ) {
		printf( "  ! missing image %s\n", $file );
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => RARTIST_SEED_SOURCE,
			'meta_value'     => $file,
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$uploads  = wp_upload_dir();
	$filename = wp_unique_filename( $uploads['path'], sanitize_title( $title ) . '.' . pathinfo( $path, PATHINFO_EXTENSION ) );
	$target   = $uploads['path'] . '/' . $filename;

	if ( ! copy( $path, $target ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $target )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$target
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $target ) );
	update_post_meta( $attachment_id, RARTIST_SEED_SOURCE, $file );
	update_post_meta( $attachment_id, RARTIST_SEED_FLAG, 1 );

	return (int) $attachment_id;
}

/**
 * The writable field names in a group, read from the group itself so this list can
 * never drift from acf-json/. Tabs and messages hold no value; taxonomy fields are
 * excluded because clearing one would unassign real terms.
 *
 * @return array<int,string>
 */
function rartist_seed_managed_fields( string $group_key ): array {
	$names = array();

	foreach ( (array) acf_get_fields( $group_key ) as $field ) {
		if ( empty( $field['name'] ) || in_array( $field['type'], array( 'tab', 'message', 'taxonomy' ), true ) ) {
			continue;
		}

		$names[] = $field['name'];
	}

	return $names;
}

/**
 * Blank every managed field the caller did not supply, so a re-run leaves no value
 * from an earlier version of this file behind. Without this the seed is additive, not
 * authoritative — a field removed here would keep its old value forever.
 *
 * @param array<string,mixed> $supplied
 * @param int|string          $target   Post ID or "term_{id}".
 */
function rartist_seed_clear_unset( string $group_key, array $supplied, $target ): void {
	foreach ( rartist_seed_managed_fields( $group_key ) as $name ) {
		if ( ! array_key_exists( $name, $supplied ) ) {
			update_field( $name, '', $target );
		}
	}
}

/**
 * Create or update a term by slug and write its fields.
 *
 * @param array<string,mixed> $fields
 */
function rartist_seed_term( string $taxonomy, string $name, string $slug, array $fields = array() ): int {
	$term = get_term_by( 'slug', $slug, $taxonomy );

	if ( $term ) {
		wp_update_term( $term->term_id, $taxonomy, array( 'name' => $name ) );
		$term_id = (int) $term->term_id;
	} else {
		$created = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );

		if ( is_wp_error( $created ) ) {
			return 0;
		}

		$term_id = (int) $created['term_id'];
	}

	$group = 'collection' === $taxonomy ? 'group_rartist_collection' : ( 'rartist' === $taxonomy ? 'group_rartist_rartist' : '' );

	if ( '' !== $group ) {
		rartist_seed_clear_unset( $group, $fields, 'term_' . $term_id );
	}

	foreach ( $fields as $key => $value ) {
		update_field( $key, $value, 'term_' . $term_id );
	}

	update_term_meta( $term_id, RARTIST_SEED_FLAG, 1 );

	return $term_id;
}

/**
 * Create or update an artwork by slug.
 *
 * @param array<string,mixed> $data
 */
function rartist_seed_artwork( array $data ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'artwork',
			'post_status'    => 'any',
			'name'           => $data['slug'],
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	$postarr = array(
		'post_type'   => 'artwork',
		'post_status' => 'publish',
		'post_title'  => $data['title'],
		'post_name'   => $data['slug'],
		'menu_order'  => $data['position'],
	);

	if ( ! empty( $existing ) ) {
		$postarr['ID'] = (int) $existing[0];
		$artwork_id    = wp_update_post( $postarr );
	} else {
		$artwork_id = wp_insert_post( $postarr );
	}

	if ( is_wp_error( $artwork_id ) || ! $artwork_id ) {
		return 0;
	}

	$artwork_id = (int) $artwork_id;

	wp_set_object_terms( $artwork_id, array( $data['collection'] ), 'collection', false );
	wp_set_object_terms( $artwork_id, array( $data['subject'] ), 'subject', false );

	// Left empty on purpose: credits are inherited from the collection on save.
	wp_set_object_terms( $artwork_id, array(), 'rartist', false );

	rartist_seed_clear_unset( 'group_rartist_artwork', $data['fields'], $artwork_id );

	foreach ( $data['fields'] as $key => $value ) {
		update_field( $key, $value, $artwork_id );
	}

	if ( ! empty( $data['image'] ) ) {
		set_post_thumbnail( $artwork_id, $data['image'] );
	}

	update_post_meta( $artwork_id, RARTIST_SEED_FLAG, 1 );

	// Fire the save hooks so inheritance runs exactly as it does in wp-admin.
	wp_update_post( array( 'ID' => $artwork_id ) );

	return $artwork_id;
}

/**
 * Create a page if it is missing. Content is only rewritten for pages this script
 * created, so anything edited by hand in wp-admin survives a re-run.
 */
function rartist_seed_page( string $title, string $slug, string $content ): int {
	$existing = get_page_by_path( $slug );

	if ( $existing ) {
		if ( get_post_meta( $existing->ID, RARTIST_SEED_FLAG, true ) ) {
			wp_update_post(
				array(
					'ID'           => $existing->ID,
					'post_title'   => $title,
					'post_content' => $content,
				)
			);
		}

		return (int) $existing->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		)
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	update_post_meta( (int) $page_id, RARTIST_SEED_FLAG, 1 );

	return (int) $page_id;
}

/* -------------------------------------------------------------------------
 * Pages — the four destinations that are editorial rather than generated.
 *
 * The nav resolves these by slug (see rartist_page_url), so creating them is what
 * turns COLLECTIONS, CURATED RARTISTS and ABOUT into working links. The two index
 * pages get their listing templates in step 3; until then they render their content.
 * ---------------------------------------------------------------------- */

$pages = array(
	array(
		'title'   => 'About the studio',
		'slug'    => 'about',
		'content' => '<!-- wp:paragraph --><p>Rartist Studio publishes curated print editions. Limited works selected by hand, printed in small numbers and made to stay with you.</p><!-- /wp:paragraph -->'
			. '<!-- wp:paragraph --><p>Printed works are selected for their atmosphere, colour and staying power, and released slowly, in numbered editions.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Collections',
		'slug'    => 'collections',
		'content' => '<!-- wp:paragraph --><p>Curated groups, released a collection at a time.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Curated rartists',
		'slug'    => 'rartists',
		'content' => '<!-- wp:paragraph --><p>The artists behind the editions.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Shipping & returns',
		'slug'    => 'shipping',
		'content' => '<!-- wp:paragraph --><p>Rolled in an archival tube and shipped worldwide, tracked. Dispatch within 5–7 days. Returns accepted within 14 days of delivery, unframed and undamaged.</p><!-- /wp:paragraph -->',
	),
);

foreach ( $pages as $page ) {
	$page_id = rartist_seed_page( $page['title'], $page['slug'], $page['content'] );
	printf( "  page     %-22s %s\n", $page['slug'], $page_id ? get_permalink( $page_id ) : 'FAILED' );
}

/* WordPress's own placeholder is noise in a catalogue admin. */
$sample = get_page_by_path( 'sample-page' );

if ( $sample && 'trash' !== $sample->post_status ) {
	wp_trash_post( $sample->ID );
	printf( "  trashed  %s\n", 'sample-page' );
}

/* -------------------------------------------------------------------------
 * Subjects — the catalogue's filter row
 * ---------------------------------------------------------------------- */

$subjects = array();

foreach ( array(
	'abstract'   => 'Abstract',
	'figurative' => 'Figurative',
	'botanical'  => 'Botanical',
	'still-life' => 'Still Life',
) as $slug => $name ) {
	$subjects[ $slug ] = rartist_seed_term( 'subject', $name, $slug );
}

/* -------------------------------------------------------------------------
 * Rartists — frames RmliI and bQoBn
 * ---------------------------------------------------------------------- */

$mara = rartist_seed_term(
	'rartist',
	'Mara Vell',
	'mara-vell',
	array(
		'number'           => '01',
		'discipline'       => 'Painter / Printmaker',
		'location'         => 'Barcelona',
		'statement'        => 'I want colour to feel familiar before it feels impressive.',
		'portrait'         => rartist_seed_image( $images['mara-portrait'], 'Mara Vell portrait' ),
		'portrait_caption' => 'Mara Vell in her Poblenou studio / 2026',
		'studio_image'     => rartist_seed_image( $images['mara-studio'], 'Mara Vell studio' ),
		'bio_title'        => 'Every picture begins on the table.',
		'bio_body_1'       => 'Mara Vell builds images from the objects and colours of daily life. She paints sheets of paper by hand, then cuts, turns and rearranges them until tables, flowers and figures become a loose visual language.',
		'bio_body_2'       => 'Born in Valencia and based in Barcelona, Vell studied illustration before moving toward painting and printmaking. Her work has been exhibited across Spain, France and the Netherlands and is held in private collections worldwide.',
		'facts'            => 'B. 1984, Valencia / Lives & works in Barcelona',
		'quote'            => 'The image is finished when every shape feels as if it has always lived beside the others.',
	)
);

$jonas = rartist_seed_term(
	'rartist',
	'Jonas Rhee',
	'jonas-rhee',
	array(
		'number'           => '02',
		'discipline'       => 'Painter',
		'location'         => 'Berlin',
		'statement'        => 'Night removes the unnecessary parts of a room.',
		'portrait'         => rartist_seed_image( $images['jonas-portrait'], 'Jonas Rhee portrait' ),
		'portrait_caption' => 'Jonas Rhee in his Kreuzberg studio / 2026',
		'studio_image'     => rartist_seed_image( $images['jonas-studio'], 'Jonas Rhee studio' ),
		'bio_title'        => 'Painting begins when daylight ends.',
		'bio_body_1'       => 'Jonas Rhee paints after sunset, when colour loses certainty and artificial light begins to shape the room. Thin layers of oil and dry pigment accumulate into interiors interrupted by cobalt, ember and sudden gold.',
		'bio_body_2'       => 'Born in Seoul and raised in Hamburg, Rhee studied painting in Berlin, where he continues to live and work. His ongoing Night Rooms series has been shown in Germany, Denmark and South Korea.',
		'facts'            => 'B. 1988, Seoul / Lives & works in Berlin',
		'quote'            => 'A dark painting is not an absence of colour. It is colour learning to speak quietly.',
	)
);

/* -------------------------------------------------------------------------
 * Collections — frames kWI5J and JDCJ8
 * ---------------------------------------------------------------------- */

$domestic = rartist_seed_term(
	'collection',
	'Domestic Colour',
	'domestic-colour',
	array(
		'number'          => '01',
		'featured'        => 1,
		'feature_order'   => 1,
		'artists'         => array( $mara ),
		'hero_image'      => rartist_seed_image( $images['domestic-hero'], 'Domestic Colour interior' ),
		'hero_caption'    => 'Works for rooms where people gather',
		'intro_title'     => 'Colour that makes a room feel lived in.',
		'intro_body'      => 'Twelve generous works selected for kitchens, dining rooms and shared spaces. Ochre, tomato red, garden green and clear blue move through the collection—each one vivid enough to hold a wall, and easy enough to live beside every day.',
		'details'         => 'Archival pigment prints / Numbered editions / Unframed',
		'works_title'     => 'The edition',
		'collector_quote' => 'Begin with the work you cannot stop looking at. The room will arrange itself around it.',
		'shipping_note'   => "Worldwide shipping\nFraming guide included",
	)
);

$after_dark = rartist_seed_term(
	'collection',
	'After Dark',
	'after-dark',
	array(
		'number'          => '02',
		'featured'        => 1,
		'feature_order'   => 2,
		'artists'         => array( $jonas ),
		'hero_image'      => rartist_seed_image( $images['after-dark-hero'], 'After Dark interior' ),
		'hero_caption'    => 'Works that change as the light leaves the room.',
		'intro_title'     => 'For the hours after sunset.',
		'intro_body'      => 'Eight works built from deep blue, smoke black, ember red and sudden gold. Strong in daylight, but selected for what they become beside lamps, candles and low evening light.',
		'details'         => 'Archival pigment prints / Numbered editions / Unframed',
		'works_title'     => 'Night editions',
		'collector_quote' => 'Dark work does not disappear at night. It begins to hold the light.',
		'shipping_note'   => "Worldwide shipping\nFraming guide included",
	)
);

/* -------------------------------------------------------------------------
 * Plates — prices, editions and years exactly as the frames state them
 * ---------------------------------------------------------------------- */

$common = array(
	'availability' => 'available',
	'medium'       => 'Archival pigment print',
	'paper'        => '310 gsm cotton rag',
	'signature'    => 'Signed & numbered',
);

$plates = array(
	array(
		'title'      => 'After Rain',
		'slug'       => 'after-rain',
		'position'   => 1,
		'collection' => $after_dark,
		'subject'    => $subjects['abstract'],
		'image'      => rartist_seed_image( $images['after-rain'], 'After Rain' ),
		'fields'     => $common + array(
			'plate_number'  => 1,
			'year'          => 2026,
			'edition_total' => 50,
			'size_1_label'  => '70 × 85 CM',
			'size_1_price'  => 180,
		),
	),
	array(
		'title'      => 'Two Figures',
		'slug'       => 'two-figures',
		'position'   => 2,
		'collection' => $after_dark,
		'subject'    => $subjects['figurative'],
		'image'      => rartist_seed_image( $images['two-figures'], 'Two Figures' ),
		'fields'     => $common + array(
			'plate_number'  => 6,
			'year'          => 2024,
			'edition_total' => 35,
			'size_1_label'  => '70 × 85 CM',
			'size_1_price'  => 220,
		),
	),
	array(
		'title'      => 'The Garden',
		'slug'       => 'the-garden',
		'position'   => 3,
		'collection' => $domestic,
		'subject'    => $subjects['botanical'],
		'image'      => rartist_seed_image( $images['the-garden'], 'The Garden' ),
		'fields'     => $common + array(
			'plate_number'  => 11,
			'year'          => 2025,
			'edition_total' => 50,
			'size_1_label'  => '70 × 85 CM',
			'size_1_price'  => 190,
		),
	),
	array(
		// The one plate the design draws in full — frame kTNBO.
		'title'      => 'Quiet Table',
		'slug'       => 'quiet-table',
		'position'   => 4,
		'collection' => $domestic,
		'subject'    => $subjects['still-life'],
		'image'      => rartist_seed_image( $images['quiet-table'], 'Quiet Table' ),
		'fields'     => $common + array(
			'plate_number'  => 14,
			'year'          => 2026,
			'edition_total' => 50,
			'artist_proofs' => 5,
			'intro'         => 'A black vase, pale fruit and one red flower held against a field of warm ochre. A still life about the small arrangements that make a room feel inhabited.',
			'size_1_label'  => '70 × 85 CM',
			'size_1_price'  => 180,
			'size_2_label'  => '100 × 121 CM',
			'size_3_label'  => '140 × 170 CM',
			'story_title'   => 'A still life with nothing still about it.',
			'story_body'    => 'Quiet Table began as a stack of painted paper on Mara Vell’s studio floor. The vase arrived first, then the fruit, and finally the single red flower that pulls the composition off balance. Translated as a pigment print, the brushed surfaces and cut edges retain their handmade character.',
			'in_situ_image' => rartist_seed_image( $images['quiet-table-insitu'], 'Quiet Table in a room' ),
			'in_situ_title' => 'Built to hold a wall, not fill it.',
			'in_situ_body'  => 'The largest size leaves enough air around the composition to work above a dining table, sofa or low cabinet.',
			'styling_note'  => "Styling note\nPair with dark timber, natural linen and objects that carry their own history.",
		),
	),
	array(
		'title'      => 'Blue Room',
		'slug'       => 'blue-room',
		'position'   => 5,
		'collection' => $after_dark,
		'subject'    => $subjects['abstract'],
		'image'      => rartist_seed_image( $images['blue-room'], 'Blue Room' ),
		'fields'     => $common + array(
			'plate_number'  => 19,
			'year'          => 2027,
			'edition_total' => 30,
			'size_1_label'  => '70 × 85 CM',
			'size_1_price'  => 240,
		),
	),
	array(
		'title'      => 'Wild Flowers',
		'slug'       => 'wild-flowers',
		'position'   => 6,
		'collection' => $domestic,
		'subject'    => $subjects['botanical'],
		'image'      => rartist_seed_image( $images['wild-flowers'], 'Wild Flowers' ),
		'fields'     => $common + array(
			'plate_number'  => 23,
			'year'          => 2027,
			'edition_total' => 50,
			'size_1_label'  => '70 × 85 CM',
			'size_1_price'  => 190,
		),
	),
);

$seeded_slugs = array();

foreach ( $plates as $plate ) {
	$id             = rartist_seed_artwork( $plate );
	$seeded_slugs[] = $plate['slug'];

	printf(
		"  %-9s %-13s %-16s %-11s €%-4d ed.%-3d %s\n",
		rartist_plate_code( $plate['fields']['plate_number'] ),
		$plate['slug'],
		get_term( $plate['collection'] )->name,
		get_term( $plate['subject'] )->name,
		$plate['fields']['size_1_price'],
		$plate['fields']['edition_total'],
		implode( ', ', wp_get_object_terms( $id, 'rartist', array( 'fields' => 'names' ) ) )
	);
}

/* Drop artworks from earlier runs that are no longer part of the demo set. */
$stale = get_posts(
	array(
		'post_type'      => 'artwork',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_key'       => RARTIST_SEED_FLAG,
		'meta_value'     => 1,
	)
);

foreach ( $stale as $stale_id ) {
	$slug = get_post_field( 'post_name', $stale_id );

	if ( ! in_array( $slug, $seeded_slugs, true ) ) {
		wp_delete_post( (int) $stale_id, true );
		printf( "  removed  %s\n", $slug );
	}
}

printf(
	"\n  %d artworks / %d collections / %d rartists / %d subjects\n",
	rartist_artwork_count(),
	rartist_term_count( 'collection' ),
	rartist_term_count( 'rartist' ),
	rartist_term_count( 'subject' )
);
