<?php
/**
 * Plate card — one artwork in a grid.
 *
 * The design shows four different meta treatments for the same card, so the variant is
 * a parameter rather than four components:
 *
 *   edition       €180  /  EDITION OF 50   (catalogue)
 *   availability  €180  /  AVAILABLE       (collection page)
 *   year-medium   2026  /  PIGMENT PRINT   (rartist profile)
 *   price         €180                     (related works)
 *
 * @param array<string,mixed> $args['artwork']     An artwork view.
 * @param string              $args['variant']     '' or 'uniform' (fixed 250×285 crop).
 * @param string              $args['meta']        One of the above. 'none' to omit.
 * @param bool                $args['show_number'] Show the PLATE 014 line above the title.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_artwork = $args['artwork'] ?? null;

if ( ! $rartist_artwork ) {
	return;
}

$rartist_variant = $args['variant'] ?? '';
$rartist_meta    = $args['meta'] ?? 'price';
$rartist_number  = ! empty( $args['show_number'] );
$rartist_sold    = 'sold_out' === $rartist_artwork['availability']['status'];

$rartist_classes = array( 'plate-card' );

if ( 'uniform' === $rartist_variant ) {
	$rartist_classes[] = 'plate-card--uniform';
}

if ( $rartist_sold ) {
	$rartist_classes[] = 'plate-card--sold-out';
}

switch ( $rartist_meta ) {
	case 'edition':
		$rartist_meta_text = rartist_meta_line(
			array( $rartist_artwork['price_from'], rartist_edition_label( rartist_field( 'edition_total', $rartist_artwork['id'], 0 ) ) )
		);
		break;

	case 'availability':
		$rartist_meta_text = rartist_meta_line(
			array( $rartist_artwork['price_from'], $rartist_artwork['availability']['label'] )
		);
		break;

	case 'year-medium':
		$rartist_meta_text = rartist_meta_line(
			array( $rartist_artwork['year'], $rartist_artwork['medium'] )
		);
		break;

	case 'none':
		$rartist_meta_text = '';
		break;

	default:
		$rartist_meta_text = $rartist_artwork['price_from'];
}
?>
<a class="<?php echo esc_attr( implode( ' ', $rartist_classes ) ); ?>" href="<?php echo esc_url( $rartist_artwork['url'] ); ?>">
	<?php if ( $rartist_artwork['image'] ) : ?>
		<figure class="plate-card__figure">
			<?php
			echo wp_get_attachment_image(
				$rartist_artwork['image'],
				'rartist-plate',
				false,
				array(
					'alt'     => $rartist_artwork['title'],
					'loading' => 'lazy',
				)
			);
			?>
		</figure>
	<?php endif; ?>

	<?php if ( $rartist_number && '' !== $rartist_artwork['plate_code'] ) : ?>
		<p class="plate-card__number t-micro"><?php echo esc_html( $rartist_artwork['plate_code'] ); ?></p>
	<?php endif; ?>

	<h3 class="plate-card__title t-plate-title"><?php echo esc_html( $rartist_artwork['title'] ); ?></h3>

	<?php if ( '' !== $rartist_meta_text ) : ?>
		<p class="plate-card__meta t-micro-tight"><?php echo esc_html( $rartist_meta_text ); ?></p>
	<?php endif; ?>
</a>
