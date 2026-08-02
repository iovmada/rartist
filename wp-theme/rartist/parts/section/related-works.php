<?php
/**
 * A works row with a display title and an optional "view all" link.
 *
 * Shared by the artwork page's related works and, later, the collection and rartist
 * pages' works grids.
 *
 * @param array<int,array<string,mixed>> $args['items']
 * @param string                         $args['index']
 * @param string                         $args['title']
 * @param string                         $args['view_all_url']
 * @param string                         $args['view_all_label']
 * @param string                         $args['variant'] Passed to the plate card.
 * @param string                         $args['meta']    Passed to the plate card.
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
			<div class="artwork__related-head">
				<div>
					<p class="t-index"><?php echo esc_html( $args['index'] ?? '' ); ?></p>

					<?php if ( ! empty( $args['title'] ) ) : ?>
						<h2 class="artwork__related-title t-display-2"><?php echo esc_html( $args['title'] ); ?></h2>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $args['view_all_url'] ) ) : ?>
					<?php
					get_template_part(
						'parts/ui/link-arrow',
						null,
						array(
							'label'     => $args['view_all_label'] ?? __( 'View all', 'rartist' ),
							'url'       => $args['view_all_url'],
							'direction' => 'right',
						)
					);
					?>
				<?php endif; ?>
			</div>
		</div>

		<div class="plate-row artwork__related-row">
			<?php foreach ( $rartist_items as $rartist_item ) : ?>
				<?php
				get_template_part(
					'parts/ui/plate-card',
					null,
					array(
						'artwork' => $rartist_item,
						'variant' => $args['variant'] ?? 'uniform',
						'meta'    => $args['meta'] ?? 'price',
					)
				);
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
