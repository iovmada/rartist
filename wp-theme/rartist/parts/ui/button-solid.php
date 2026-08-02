<?php
/**
 * Solid ink button with a trailing arrow — frames WQQHl / x7Ai0.
 *
 * @param string $args['label']
 * @param string $args['url']
 * @param string $args['variant'] '' or 'compact'
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_label = $args['label'] ?? '';
$rartist_url   = $args['url'] ?? '';

if ( '' === $rartist_label || '' === $rartist_url ) {
	return;
}
?>
<a
	class="button-solid<?php echo 'compact' === ( $args['variant'] ?? '' ) ? ' button-solid--compact' : ''; ?>"
	href="<?php echo esc_url( $rartist_url ); ?>"
>
	<span class="button-solid__label t-meta"><?php echo esc_html( $rartist_label ); ?></span>
	<span class="button-solid__arrow" aria-hidden="true">&rarr;</span>
</a>
