<?php
/**
 * Catalogue view — the archive's filter row and its counts.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * The subject filters, with "All" first. Counts come from the taxonomy, so the row in
 * the design ("ABSTRACT 08 · FIGURATIVE 07 …") is always true rather than typed.
 *
 * @return array<int,array{label:string,count:string,url:string,current:bool}>
 */
function rartist_subject_filters(): array {
	$archive = (string) get_post_type_archive_link( 'artwork' );

	$filters = array(
		array(
			'label'   => __( 'All', 'rartist' ),
			'count'   => rartist_pad( rartist_artwork_count(), 2 ) ?: '00',
			'url'     => $archive,
			'current' => ! is_tax( 'subject' ),
		),
	);

	$terms = get_terms(
		array(
			'taxonomy'   => 'subject',
			'hide_empty' => false,
			'orderby'    => 'term_id',
		)
	);

	if ( is_wp_error( $terms ) ) {
		return $filters;
	}

	foreach ( $terms as $term ) {
		$filters[] = array(
			'label'   => $term->name,
			'count'   => rartist_pad( $term->count, 2 ) ?: '00',
			'url'     => (string) get_term_link( $term ),
			'current' => is_tax( 'subject', $term->term_id ),
		);
	}

	return $filters;
}

/**
 * "06 / 24" — how many plates this view shows out of everything published.
 */
function rartist_shown_count_label( int $shown ): string {
	return sprintf(
		'%s / %s',
		rartist_pad( $shown, 2 ) ?: '00',
		rartist_pad( rartist_artwork_count(), 2 ) ?: '00'
	);
}
