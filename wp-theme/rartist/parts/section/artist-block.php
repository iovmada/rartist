<?php
/**
 * The artist section — used by the artwork page and, later, the collection page.
 *
 * One credited rartist gets the design's portrait-beside-copy split. Two or more get
 * compact cards side by side, which is the variant the frames don't cover: the design
 * assumes a single artist per collection.
 *
 * @param array<int,array<string,mixed>> $args['artists']
 * @param string                         $args['index']      The section's index label.
 * @param string                         $args['link_label'] Defaults to "Read <name> profile".
 * @param bool                           $args['wide']       Collection-page proportions
 *                                                           (450 portrait) rather than the
 *                                                           artwork page's 390.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_artists = $args['artists'] ?? array();

if ( empty( $rartist_artists ) ) {
	return;
}

$rartist_single = 1 === count( $rartist_artists );
$rartist_split  = empty( $args['wide'] ) ? 'split split--artist' : 'split--artist-wide';
?>
<section class="section">
	<div class="container">
		<div class="section__head">
			<p class="t-index"><?php echo esc_html( $args['index'] ?? __( 'The artist', 'rartist' ) ); ?></p>
		</div>

		<?php if ( $rartist_single ) : ?>
			<div class="<?php echo esc_attr( $rartist_split ); ?>" style="margin-top: 26px">
				<?php
				$rartist_artist = $rartist_artists[0];

				if ( $rartist_artist['portrait'] ) :
					?>
					<figure class="artist-card__portrait">
						<?php
						echo wp_get_attachment_image(
							$rartist_artist['portrait'],
							'rartist-portrait',
							false,
							array(
								'alt'     => $rartist_artist['name'],
								'loading' => 'lazy',
							)
						);
						?>
					</figure>
				<?php else : ?>
					<div></div>
				<?php endif; ?>

				<div>
					<h2 class="artist-card__name t-display-3"><?php echo esc_html( $rartist_artist['name'] ); ?></h2>

					<?php if ( '' !== $rartist_artist['discipline_line'] ) : ?>
						<p class="artist-card__discipline t-micro"><?php echo esc_html( $rartist_artist['discipline_line'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $rartist_artist['bio_1'] ) : ?>
						<p class="artist-card__bio t-body"><?php echo esc_html( $rartist_artist['bio_1'] ); ?></p>
					<?php endif; ?>

					<div class="artist-card__link">
						<?php
						get_template_part(
							'parts/ui/link-arrow',
							null,
							array(
								'label' => $args['link_label'] ?? sprintf(
									/* translators: %s: artist name */
									__( 'Read %s profile', 'rartist' ),
									$rartist_artist['name']
								),
								'url'   => $rartist_artist['url'],
							)
						);
						?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="plate-row" style="margin-top: 26px; grid-template-columns: repeat(2, 1fr); column-gap: 9.82%">
				<?php foreach ( $rartist_artists as $rartist_artist ) : ?>
					<?php
					get_template_part(
						'parts/ui/artist-card',
						null,
						array(
							'artist'  => $rartist_artist,
							'variant' => 'compact',
						)
					);
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
