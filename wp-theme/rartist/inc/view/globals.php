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

/**
 * Worn Editions concept gallery — two design images and three Adobe Firefly scenes.
 * The filter can supply further editions without changing the section or its script.
 *
 * @return array<int,array<string,string>>
 */
function rartist_worn_editions(): array {
	return apply_filters(
		'rartist_worn_editions',
		array(
			array(
				'title'   => __( 'Studio edition', 'rartist' ),
				'details' => __( 'SCREENPRINT ON COTTON / CONCEPT PREVIEW', 'rartist' ),
				'image'   => RARTIST_URI . '/assets/img/worn-studio.jpg',
				'alt'	 => __( 'Back of a cotton shirt with a blue abstract print, worn in an artist’s studio', 'rartist' ),
			),
			array(
				'title'   => __( 'Print detail', 'rartist' ),
				'details' => __( 'INK AND TEXTURE / CONCEPT PREVIEW', 'rartist' ),
				'image'   => RARTIST_URI . '/assets/img/worn-detail.jpg',
				'alt'	 => __( 'Close-up of blue screenprinted artwork and the texture of a cotton shirt', 'rartist' ),
			),
			array(
				'title'   => __( 'Gallery wall edition', 'rartist' ),
				'details' => __( 'SCREENPRINT ON COTTON / CONCEPT PREVIEW', 'rartist' ),
				'image'   => RARTIST_URI . '/assets/img/worn-gallery-wall.png',
				'alt'     => __( 'Off-white cotton shirt with blue and black abstract artwork hanging against a warm plaster studio wall', 'rartist' ),
				'width'   => 2048,
				'height'  => 1152,
			),
			array(
				'title'   => __( 'Printmaker’s table', 'rartist' ),
				'details' => __( 'SCREENPRINT ON COTTON / CONCEPT PREVIEW', 'rartist' ),
				'image'   => RARTIST_URI . '/assets/img/worn-printmakers-table.png',
				'alt'     => __( 'Off-white cotton shirt with blue and black abstract artwork laid on an oak printmaker’s table', 'rartist' ),
				'width'   => 2048,
				'height'  => 1152,
			),
			array(
				'title'   => __( 'Studio display', 'rartist' ),
				'details' => __( 'SCREENPRINT ON COTTON / CONCEPT PREVIEW', 'rartist' ),
				'image'   => RARTIST_URI . '/assets/img/worn-studio-display.png',
				'alt'     => __( 'Off-white cotton shirt with blue and black abstract artwork displayed on a wooden studio bench', 'rartist' ),
				'width'   => 2048,
				'height'  => 1152,
			),
		)
	);
}
