<?php
/**
 * Expanding rows — frame xEfls.
 *
 * Native details/summary: no JavaScript, keyboard and screen-reader behaviour for free.
 *
 * @param array<int,array{label:string,body:string}> $args['rows']
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_rows = $args['rows'] ?? array();

if ( empty( $rartist_rows ) ) {
	return;
}
?>
<div class="rows rows--accordion">
	<?php foreach ( $rartist_rows as $rartist_row ) : ?>
		<details class="rows__item">
			<summary class="accordion__summary">
				<span class="t-micro"><?php echo esc_html( $rartist_row['label'] ); ?></span>
				<span class="accordion__sign" aria-hidden="true"></span>
			</summary>
			<div class="accordion__body"><?php echo esc_html( $rartist_row['body'] ); ?></div>
		</details>
	<?php endforeach; ?>
</div>
