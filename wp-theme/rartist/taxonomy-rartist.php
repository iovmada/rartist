<?php
/**
 * Rartist profile — frame RmliI.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

$artist = rartist_rartist_view( get_queried_object() );

if ( ! $artist ) {
	get_footer();
	return;
}

$works       = rartist_rartist_works( $artist['id'] );
$collections = rartist_rartist_collections( $artist['id'] );
?>

<div class="rartist">

	<?php /* ---------- Hero ---------- */ ?>
	<div class="container">
		<p class="rartist__index t-index"><?php echo esc_html( $artist['index_label'] ); ?></p>

		<div class="rartist__hero">
			<div>
				<h1 class="rartist__name"><?php echo esc_html( $artist['display_name'] ); ?></h1>

				<?php if ( '' !== $artist['discipline_line'] ) : ?>
					<p class="rartist__discipline t-index"><?php echo esc_html( $artist['discipline_line'] ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $artist['statement'] ) : ?>
					<p class="rartist__statement">
						<?php
						printf(
							/* translators: %s: the artist's statement, in typographic quotes */
							esc_html_x( '“%s”', 'artist statement', 'rartist' ),
							esc_html( $artist['statement'] )
						);
						?>
					</p>
				<?php endif; ?>
			</div>

			<div>
				<?php if ( $artist['portrait'] ) : ?>
					<figure class="rartist__portrait">
						<?php
						echo wp_get_attachment_image(
							$artist['portrait'],
							'rartist-portrait',
							false,
							array(
								'alt'           => $artist['name'],
								'fetchpriority' => 'high',
							)
						);
						?>
					</figure>
				<?php endif; ?>

				<?php if ( '' !== $artist['portrait_caption'] ) : ?>
					<p class="rartist__portrait-caption t-micro t-soft"><?php echo esc_html( $artist['portrait_caption'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php /* ---------- Biography ---------- */ ?>
	<?php if ( '' !== $artist['bio_1'] || $artist['studio_image'] ) : ?>
		<section class="section">
			<div class="container">
				<div class="section__head">
					<p class="t-index"><?php echo esc_html( rartist_meta_line( array( __( 'Practice', 'rartist' ), __( 'Biography', 'rartist' ) ) ) ); ?></p>
				</div>

				<div class="rartist__bio">
					<?php if ( $artist['studio_image'] ) : ?>
						<figure class="rartist__studio">
							<?php
							echo wp_get_attachment_image(
								$artist['studio_image'],
								'rartist-hero',
								false,
								array(
									'alt'     => sprintf(
										/* translators: %s: artist name */
										__( '%s in the studio', 'rartist' ),
										$artist['name']
									),
									'loading' => 'lazy',
								)
							);
							?>
						</figure>
					<?php else : ?>
						<div></div>
					<?php endif; ?>

					<div>
						<?php if ( '' !== $artist['bio_title'] ) : ?>
							<h2 class="rartist__bio-title"><?php echo esc_html( $artist['bio_title'] ); ?></h2>
						<?php endif; ?>

						<?php foreach ( array( $artist['bio_1'], $artist['bio_2'] ) as $paragraph ) : ?>
							<?php if ( '' !== $paragraph ) : ?>
								<p class="rartist__bio-body t-body"><?php echo esc_html( $paragraph ); ?></p>
							<?php endif; ?>
						<?php endforeach; ?>

						<?php if ( '' !== $artist['facts'] ) : ?>
							<p class="rartist__facts t-micro"><?php echo esc_html( $artist['facts'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* ---------- Selected works ---------- */ ?>
	<?php
	get_template_part(
		'parts/section/works-row',
		null,
		array(
			'items'       => $works,
			'index'       => rartist_meta_line( array( __( 'Selected works', 'rartist' ), '2024—2027' ) ),
			'title'       => __( 'Selected works', 'rartist' ),
			'title_class' => 'rartist__works-title',
			'meta'        => 'year-medium',
			'show_number' => false,
		)
	);
	?>

	<?php /* ---------- Pull quote ---------- */ ?>
	<?php if ( '' !== $artist['quote'] ) : ?>
		<div class="section">
			<?php
			get_template_part(
				'parts/section/quote-band',
				null,
				array(
					'quote'   => $artist['quote'],
					'centred' => true,
				)
			);
			?>
		</div>
	<?php endif; ?>

	<?php /* ---------- Featured collection ---------- */ ?>
	<?php if ( ! empty( $collections ) ) : ?>
		<?php $featured = $collections[0]; ?>
		<div class="container">
			<div class="rartist__featured">
				<div>
					<p class="t-index"><?php echo esc_html( rartist_meta_line( array( __( 'Featured collection', 'rartist' ), $featured['name'] ) ) ); ?></p>
					<h2 class="rartist__featured-title"><?php echo esc_html( rartist_featured_collection_line( $featured ) ); ?></h2>
				</div>

				<?php
				get_template_part(
					'parts/ui/button-solid',
					null,
					array(
						'label'   => __( 'View collection', 'rartist' ),
						'url'     => $featured['url'],
						'variant' => 'compact',
					)
				);
				?>
			</div>
		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
