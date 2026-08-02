<?php
/**
 * Rartists index.
 *
 * Template Name: Rartists index
 *
 * Not in the design — the frames cover a rartist's own profile but never a list of
 * them. Built from the same parts as everything else (the compact artist card used
 * when a collection has several credits), so it reads as part of the system rather
 * than as an invention. Worth a design pass before launch.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

the_post();

// The curated ones only — the same list the navigation dropdown shows.
$artists = rartist_curated_rartists();
?>

<div class="catalogue">
	<div class="container">
		<p class="catalogue__eyebrow t-index">
			<?php
			echo esc_html(
				rartist_meta_line(
					array(
						__( 'Curated rartists', 'rartist' ),
						sprintf(
							/* translators: %s: zero-padded number of artists */
							__( '%s artists', 'rartist' ),
							rartist_pad( count( $artists ), 2 ) ?: '00'
						),
					)
				)
			);
			?>
		</p>

		<div class="catalogue__head">
			<h1 class="catalogue__headline"><?php the_title(); ?></h1>

			<div class="catalogue__intro t-lead"><?php the_content(); ?></div>
		</div>

		<?php if ( ! empty( $artists ) ) : ?>
			<div class="works-row" style="grid-template-columns: repeat(2, 1fr); column-gap: calc(100% * 122 / 1344); row-gap: 72px; margin-top: 74px">
				<?php foreach ( $artists as $artist ) : ?>
					<?php
					get_template_part(
						'parts/ui/artist-card',
						null,
						array(
							'artist'  => $artist,
							'variant' => 'compact',
						)
					);
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
