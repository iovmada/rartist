<?php
/**
 * Reserve bar and availability note — frames Xo0YQ / hHCUv.
 *
 * @param array<string,mixed> $args['artwork'] An artwork view.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_artwork = $args['artwork'] ?? null;

if ( ! $rartist_artwork ) {
	return;
}

$rartist_status = $rartist_artwork['availability']['status'];
$rartist_sold   = 'sold_out' === $rartist_status;
$rartist_label  = RARTIST_COMMERCE ? __( 'Add to bag', 'rartist' ) : __( 'Reserve this edition', 'rartist' );
?>
<?php if ( $rartist_sold ) : ?>
	<div class="reserve-bar reserve-bar--sold-out">
		<span class="reserve-bar__label t-meta"><?php esc_html_e( 'Edition sold out', 'rartist' ); ?></span>
		<span class="reserve-bar__price t-meta"><?php esc_html_e( 'Join the list', 'rartist' ); ?></span>
	</div>
<?php else : ?>
	<a class="reserve-bar" href="<?php echo esc_url( $rartist_artwork['reserve_url'] ); ?>">
		<span class="reserve-bar__label t-meta"><?php echo esc_html( $rartist_label ); ?></span>
		<span class="reserve-bar__price t-meta" data-reserve-price>
			<?php echo esc_html( $rartist_artwork['sizes'][0]['price'] ?? $rartist_artwork['price_from'] ); ?>
		</span>
	</a>
<?php endif; ?>

<?php if ( '' !== $rartist_artwork['availability']['line'] ) : ?>
	<p class="availability availability--<?php echo esc_attr( str_replace( '_', '-', $rartist_status ) ); ?> t-micro">
		<span class="availability__dot" aria-hidden="true"></span>
		<?php echo esc_html( $rartist_artwork['availability']['line'] ); ?>
	</p>
<?php endif; ?>
