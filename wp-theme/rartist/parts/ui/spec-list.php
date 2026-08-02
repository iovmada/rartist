<?php
/**
 * Divided label/value rows — frame bA7vh.
 *
 * @param array<int,array{label:string,value:string}> $args['rows']
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_rows = $args['rows'] ?? array();

if ( empty( $rartist_rows ) ) {
	return;
}
?>
<dl class="rows rows--specs">
	<?php foreach ( $rartist_rows as $rartist_row ) : ?>
		<div class="rows__item">
			<dt class="rows__label t-micro"><?php echo esc_html( $rartist_row['label'] ); ?></dt>
			<dd class="rows__value t-micro-tight"><?php echo esc_html( $rartist_row['value'] ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>
