<?php
/**
 * Site-wide strings.
 *
 * The design carries a handful of texts that belong to the studio rather than to any
 * artwork: the wordmark, the season label, the nav panel's feature copy and the three
 * purchase-information panels. ACF free has no options page, so they live here behind
 * filters — a Customizer section can supply them later without touching a template.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,string>
 */
function rartist_studio(): array {
	return apply_filters(
		'rartist_studio',
		array(
			'wordmark'      => 'Rartist Studio',
			'footer_mark'   => 'Rartist Studio — Curated Print Editions',
			'season'        => 'Spring 2027',
			'enquiry_email' => 'hello@rartist.ro',
			'shipping_line' => 'Ships within 5–7 days',
		)
	);
}

function rartist_studio_value( string $key ): string {
	$studio = rartist_studio();

	return (string) ( $studio[ $key ] ?? '' );
}

/**
 * The feature column inside the navigation panel (frame tu30N).
 *
 * @return array<string,string>
 */
function rartist_nav_feature(): array {
	return apply_filters(
		'rartist_nav_feature',
		array(
			'eyebrow'   => 'Rartist  /  Curated print editions',
			'statement' => "Collect slowly.\nLive with art.",
			'note'      => 'Limited works selected by hand, printed in small numbers and made to stay with you.',
		)
	);
}

/**
 * The three panels beside an artwork's price (frame xEfls).
 *
 * @return array<int,array{label:string,body:string}>
 */
function rartist_purchase_information(): array {
	return apply_filters(
		'rartist_purchase_information',
		array(
			array(
				'label' => 'Print & materials',
				'body'  => "Archival pigment print on 310 gsm cotton rag, printed in-house and inspected by hand.\nEach print is signed and numbered on the reverse.",
			),
			array(
				'label' => 'Shipping & returns',
				'body'  => "Rolled in an archival tube and shipped worldwide, tracked. Dispatch within 5–7 days.\nReturns accepted within 14 days of delivery, unframed and undamaged.",
			),
			array(
				'label' => 'Framing guide',
				'body'  => 'A short guide to mounts, glazing and hanging heights is included with every order, with the sizes we recommend for each edition.',
			),
		)
	);
}

/**
 * Footer links. Uses the "footer" menu location when one is assigned.
 *
 * @return array<int,array{label:string,url:string}>
 */
function rartist_footer_links(): array {
	$links = array();

	if ( has_nav_menu( 'footer' ) ) {
		$items = wp_get_nav_menu_items( get_nav_menu_locations()['footer'] ?? 0 );

		foreach ( (array) $items as $item ) {
			$links[] = array(
				'label' => $item->title,
				'url'   => $item->url,
			);
		}
	}

	if ( empty( $links ) ) {
		$links = array(
			array(
				'label' => 'Instagram',
				'url'   => 'https://instagram.com/rartist.studio',
			),
			array(
				'label' => 'Shipping',
				'url'   => rartist_page_url( 'shipping' ),
			),
			array(
				'label' => 'Contact',
				'url'   => 'mailto:' . rartist_studio_value( 'enquiry_email' ),
			),
		);
	}

	return apply_filters( 'rartist_footer_links', $links );
}

/**
 * Permalink of a page by slug, or a fallback when the page does not exist yet.
 */
function rartist_page_url( string $slug, string $fallback = '#' ): string {
	$page = get_page_by_path( $slug );

	return $page ? (string) get_permalink( $page ) : $fallback;
}
