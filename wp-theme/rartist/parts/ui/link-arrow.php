<?php
/**
 * Underlined link with a trailing arrow — frames MReZm / u0wJW.
 *
 * @param string $args['label']
 * @param string $args['url']
 * @param string $args['direction'] 'up-right' (default) or 'right'
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_label = $args['label'] ?? '';
$rartist_url   = $args['url'] ?? '';

if ( '' === $rartist_label || '' === $rartist_url ) {
	return;
}

$rartist_right = 'right' === ( $args['direction'] ?? 'up-right' );
?>
<a class="link-arrow<?php echo $rartist_right ? ' link-arrow--right' : ''; ?>" href="<?php echo esc_url( $rartist_url ); ?>">
	<span class="t-link"><?php echo esc_html( $rartist_label ); ?></span>
	<span class="link-arrow__arrow" aria-hidden="true"><?php echo $rartist_right ? '&rarr;' : '&nearr;'; ?></span>
</a>
