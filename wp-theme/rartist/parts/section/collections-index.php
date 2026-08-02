<?php
/**
 * The collections index — frame iaMv8's lower half.
 *
 * Features alternate: the first puts a 625 × 440 image left with the copy right, the
 * second mirrors it with a smaller 410 × 315 image. The CSS handles the alternation,
 * so any number of collections keeps the rhythm.
 *
 * @param array<int,array<string,mixed>> $args['collections']
 * @param bool                           $args['show_head']
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_collections = $args['collections'] ?? array();

if ( empty( $rartist_collections ) ) {
	return;
}

$rartist_show_head = $args['show_head'] ?? true;
?>
<section class="section" id="collections">
	<div class="container">
		<?php if ( $rartist_show_head ) : ?>
			<div class="section__head">
				<p class="t-index">
					<?php
					echo esc_html(
						$args['index'] ?? rartist_meta_line(
							array(
								__( 'Curated groups', 'rartist' ),
								sprintf(
									/* translators: %s: zero-padded number of collections */
									__( '%s collections', 'rartist' ),
									rartist_pad( count( $rartist_collections ), 2 ) ?: '00'
								),
							)
						)
					);
					?>
				</p>

				<h2 class="catalogue__collections-title">
					<?php echo esc_html( $args['title'] ?? __( 'Collections', 'rartist' ) ); ?>
				</h2>
			</div>
		<?php endif; ?>

		<?php foreach ( $rartist_collections as $rartist_collection ) : ?>
			<?php /* No hero yet — the copy takes the full width rather than leaving a hole. */ ?>
			<article class="collection-feature<?php echo $rartist_collection['hero_image'] ? '' : ' collection-feature--no-image'; ?>">
				<?php if ( $rartist_collection['hero_image'] ) : ?>
					<figure class="collection-feature__figure">
						<?php
						echo wp_get_attachment_image(
							$rartist_collection['hero_image'],
							'rartist-hero',
							false,
							array(
								'alt'     => $rartist_collection['name'],
								'loading' => 'lazy',
							)
						);
						?>
					</figure>
				<?php endif; ?>

				<div class="collection-feature__copy">
					<p class="collection-feature__number t-meta">
						<?php
						echo esc_html(
							rartist_meta_line(
								array(
									$rartist_collection['number_label'],
									sprintf(
										/* translators: %s: zero-padded number of works */
										__( '%s works', 'rartist' ),
										rartist_pad( $rartist_collection['works_count'], 2 ) ?: '00'
									),
								)
							)
						);
						?>
					</p>

					<h3 class="collection-feature__title"><?php echo esc_html( $rartist_collection['display_name'] ); ?></h3>

					<?php if ( '' !== $rartist_collection['intro_body'] ) : ?>
						<p class="collection-feature__description t-body"><?php echo esc_html( $rartist_collection['intro_body'] ); ?></p>
					<?php endif; ?>

					<div class="collection-feature__link">
						<?php
						get_template_part(
							'parts/ui/link-arrow',
							null,
							array(
								'label' => __( 'View collection', 'rartist' ),
								'url'   => $rartist_collection['url'],
							)
						);
						?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
