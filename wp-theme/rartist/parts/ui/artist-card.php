<?php
/**
 * Artist card — portrait, name, discipline line, short biography, profile link.
 *
 * @param array<string,mixed> $args['artist']  A rartist view.
 * @param string              $args['variant'] '' or 'compact' (two or more artists on a page).
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_artist = $args['artist'] ?? null;

if ( ! $rartist_artist ) {
	return;
}

$rartist_compact = 'compact' === ( $args['variant'] ?? '' );
?>
<div class="artist-card<?php echo $rartist_compact ? ' artist-card--compact' : ''; ?>">
	<?php if ( $rartist_artist['portrait'] ) : ?>
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
	<?php endif; ?>

	<div class="artist-card__copy">
		<h3 class="artist-card__name <?php echo $rartist_compact ? 't-display-5' : 't-display-3'; ?>">
			<?php echo esc_html( $rartist_artist['name'] ); ?>
		</h3>

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
					/* translators: %s: artist name */
					'label' => sprintf( __( 'Read %s profile', 'rartist' ), $rartist_artist['name'] ),
					'url'   => $rartist_artist['url'],
				)
			);
			?>
		</div>
	</div>
</div>
