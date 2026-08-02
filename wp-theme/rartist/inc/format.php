<?php
/**
 * Formatters — the only place raw field values become display strings.
 *
 * Templates never format. The view layer (inc/view/) and the admin columns both
 * call these, so a plate number reads identically everywhere it appears.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * Zero-pad a number the way the design does: 1 → "01", 14 → "014" at length 3.
 */
function rartist_pad( $number, int $length = 2 ): string {
	$number = (int) $number;

	if ( $number <= 0 ) {
		return '';
	}

	return str_pad( (string) $number, $length, '0', STR_PAD_LEFT );
}

/**
 * "PLATE 014" — the plate's public identifier.
 */
function rartist_plate_code( $plate_number ): string {
	$padded = rartist_pad( $plate_number, 3 );

	return '' === $padded ? '' : sprintf( 'PLATE %s', $padded );
}

/**
 * "€180". Whole euros only — the design never shows decimals.
 */
function rartist_price( $amount ): string {
	if ( '' === $amount || null === $amount ) {
		return '';
	}

	return RARTIST_CURRENCY . number_format_i18n( (float) $amount, 0 );
}

/**
 * "EDITION OF 50" from the edition total.
 */
function rartist_edition_label( $total ): string {
	$total = (int) $total;

	return $total > 0 ? sprintf( 'EDITION OF %d', $total ) : '';
}

/**
 * "50 + 5 ARTIST PROOFS" for the specification list.
 */
function rartist_edition_spec( $total, $proofs ): string {
	$total  = (int) $total;
	$proofs = (int) $proofs;

	if ( $total <= 0 ) {
		return '';
	}

	// Natural case: the spec list uppercases with CSS, like every other value.
	return $proofs > 0
		? sprintf(
			/* translators: 1: edition size, 2: number of artist proofs */
			_n( '%1$d + %2$d artist proof', '%1$d + %2$d artist proofs', $proofs, 'rartist' ),
			$total,
			$proofs
		)
		: sprintf( '%d', $total );
}

/**
 * Availability choices — admin label and the front-end line the design shows.
 *
 * Kept in one place so the ACF JSON, the admin column and the artwork page can't drift.
 *
 * @return array<string,string>
 */
function rartist_availability_choices(): array {
	return array(
		'available' => __( 'Available', 'rartist' ),
		'reserved'  => __( 'Reserved', 'rartist' ),
		'sold_out'  => __( 'Sold out', 'rartist' ),
	);
}

/**
 * "IN STOCK" / "RESERVED" / "SOLD OUT" as shown beside the availability dot.
 */
function rartist_availability_label( string $status ): string {
	$labels = array(
		'available' => __( 'IN STOCK', 'rartist' ),
		'reserved'  => __( 'RESERVED', 'rartist' ),
		'sold_out'  => __( 'SOLD OUT', 'rartist' ),
	);

	return $labels[ $status ] ?? '';
}

/**
 * Join the parts of a meta line with the design's double-spaced slash: "A  /  B".
 * Empty parts drop out, so an incomplete record never renders a stray separator.
 *
 * @param array<int,string> $parts
 */
function rartist_meta_line( array $parts ): string {
	$parts = array_filter( array_map( 'trim', $parts ), static fn( $part ) => '' !== $part );

	return implode( '  /  ', $parts );
}
