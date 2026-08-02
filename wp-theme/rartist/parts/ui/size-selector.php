<?php
/**
 * Size selector — frame zchnW.
 *
 * Real radio inputs: the selection works, and reads correctly, with no JavaScript.
 * assets/js/artwork.js only mirrors the chosen price into the reserve bar.
 *
 * @param array<int,array{label:string,price:string,amount:float}> $args['sizes']
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_sizes = $args['sizes'] ?? array();

if ( empty( $rartist_sizes ) ) {
	return;
}
?>
<div class="size-selector" data-size-selector>
	<p class="size-selector__label t-micro"><?php esc_html_e( 'Select size', 'rartist' ); ?></p>

	<div class="size-selector__options">
		<?php foreach ( $rartist_sizes as $rartist_i => $rartist_size ) : ?>
			<?php $rartist_input_id = 'rartist-size-' . $rartist_i; ?>
			<span class="size-option">
				<input
					class="size-option__input"
					type="radio"
					name="rartist-size"
					id="<?php echo esc_attr( $rartist_input_id ); ?>"
					value="<?php echo esc_attr( $rartist_size['label'] ); ?>"
					data-price="<?php echo esc_attr( $rartist_size['price'] ); ?>"
					<?php checked( 0, $rartist_i ); ?>
				>
				<label class="size-option__face" for="<?php echo esc_attr( $rartist_input_id ); ?>">
					<?php echo esc_html( $rartist_size['label'] ); ?>
				</label>
			</span>
		<?php endforeach; ?>
	</div>
</div>
