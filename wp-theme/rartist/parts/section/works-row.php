<?php
/**
 * Four-up works grid with its index label, display title and optional button.
 *
 * Shared by the collection page (frame kWI5J) and the rartist profile (RmliI).
 *
 * @param array<int,array<string,mixed>> $args['items']
 * @param string $args['index']
 * @param string $args['title']
 * @param string $args['title_class'] Template class carrying the display size.
 * @param string $args['meta']        Plate-card meta variant.
 * @param bool   $args['show_number'] Show the PLATE 014 line.
 * @param string $args['cta_label']
 * @param string $args['cta_url']
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_items = $args['items'] ?? array();

if ( empty( $rartist_items ) ) {
	return;
}
?>
<section class="section">
	<div class="container">
		<div class="section__head">
			<p class="t-index"><?php echo esc_html( $args['index'] ?? '' ); ?></p>

			<?php if ( ! empty( $args['title'] ) ) : ?>
				<h2 class="<?php echo esc_attr( $args['title_class'] ?? 't-display-2' ); ?>">
					<?php echo esc_html( $args['title'] ); ?>
				</h2>
			<?php endif; ?>
		</div>

		<div class="works-row <?php echo esc_attr( $args['row_class'] ?? '' ); ?>">
			<?php foreach ( $rartist_items as $rartist_item ) : ?>
				<?php
				get_template_part(
					'parts/ui/plate-card',
					null,
					array(
						'artwork'     => $rartist_item,
						'meta'        => $args['meta'] ?? 'availability',
						'show_number' => $args['show_number'] ?? true,
					)
				);
				?>
			<?php endforeach; ?>
		</div>

		<?php if ( ! empty( $args['cta_url'] ) && ! empty( $args['cta_label'] ) ) : ?>
			<div class="collection__works-cta">
				<?php
				get_template_part(
					'parts/ui/button-solid',
					null,
					array(
						'label' => $args['cta_label'],
						'url'   => $args['cta_url'],
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
