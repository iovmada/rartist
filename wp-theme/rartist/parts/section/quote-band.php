<?php
/**
 * Quote band — frames c2Du7Q (collector note) and wxBpW (rartist quote).
 *
 * @param string $args['quote']   Without quotation marks; they are added here.
 * @param string $args['index']   Optional left-hand label.
 * @param string $args['note']    Optional right-hand note.
 * @param bool   $args['centred'] True for the rartist profile's single centred line.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_quote = trim( (string) ( $args['quote'] ?? '' ) );

if ( '' === $rartist_quote ) {
	return;
}

$rartist_centred = ! empty( $args['centred'] );
$rartist_index   = (string) ( $args['index'] ?? '' );
$rartist_note    = (string) ( $args['note'] ?? '' );
?>
<div class="container">
	<blockquote class="quote-band<?php echo $rartist_centred ? ' quote-band--centred' : ''; ?>">
		<?php if ( ! $rartist_centred && '' !== $rartist_index ) : ?>
			<p class="quote-band__index t-micro"><?php echo esc_html( $rartist_index ); ?></p>
		<?php endif; ?>

		<p class="quote-band__quote">
			<?php
			printf(
				/* translators: %s: quotation text, wrapped in typographic quotes */
				esc_html_x( '“%s”', 'pull quote', 'rartist' ),
				esc_html( $rartist_quote )
			);
			?>
		</p>

		<?php if ( ! $rartist_centred && '' !== $rartist_note ) : ?>
			<p class="quote-band__note t-micro t-lines"><?php echo esc_html( $rartist_note ); ?></p>
		<?php endif; ?>
	</blockquote>
</div>
