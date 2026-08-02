<?php
/**
 * Artwork detail — frame kTNBO.
 *
 * The template reads one view and composes parts. No field access, no formatting,
 * no conditionals beyond "does this section exist".
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

the_post();

$artwork = rartist_artwork_view( get_post() );

if ( ! $artwork ) {
	get_footer();
	return;
}

$related = rartist_related_artworks( $artwork['id'] );
?>

<article class="artwork">

	<?php /* ---------- Hero ---------- */ ?>
	<div class="container">
		<nav class="artwork__breadcrumb t-micro t-soft" aria-label="<?php esc_attr_e( 'Breadcrumb', 'rartist' ); ?>">
			<?php
			$crumbs = array();

			foreach ( $artwork['breadcrumb'] as $crumb ) {
				$crumbs[] = '' === $crumb['url']
					? esc_html( $crumb['label'] )
					: sprintf( '<a href="%s">%s</a>', esc_url( $crumb['url'] ), esc_html( $crumb['label'] ) );
			}

			echo wp_kses_post( implode( '  /  ', $crumbs ) );
			?>
		</nav>

		<div class="split split--hero artwork__hero">
			<div>
				<?php if ( $artwork['image'] ) : ?>
					<figure class="artwork__figure">
						<?php
						echo wp_get_attachment_image(
							$artwork['image'],
							'rartist-hero',
							false,
							array(
								'alt'           => $artwork['title'],
								'fetchpriority' => 'high',
							)
						);
						?>
					</figure>
				<?php endif; ?>

				<?php if ( '' !== $artwork['image_note'] ) : ?>
					<p class="artwork__image-note t-micro t-soft"><?php echo esc_html( $artwork['image_note'] ); ?></p>
				<?php endif; ?>
			</div>

			<div class="artwork__panel">
				<?php if ( '' !== $artwork['plate_meta'] ) : ?>
					<p class="artwork__plate-meta t-meta t-soft"><?php echo esc_html( $artwork['plate_meta'] ); ?></p>
				<?php endif; ?>

				<h1 class="artwork__title t-display-1"><?php echo esc_html( $artwork['title'] ); ?></h1>

				<?php if ( ! empty( $artwork['artists'] ) ) : ?>
					<a class="artwork__artist-link t-meta" href="<?php echo esc_url( $artwork['artists'][0]['url'] ); ?>">
						<?php echo esc_html( $artwork['credit_line'] ); ?>&nbsp;&nbsp;&nearr;
					</a>
				<?php endif; ?>

				<?php if ( '' !== $artwork['intro'] ) : ?>
					<p class="artwork__description t-lead"><?php echo esc_html( $artwork['intro'] ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $artwork['price_from'] ) : ?>
					<p class="artwork__price t-price"><?php echo esc_html( $artwork['price_from'] ); ?></p>
				<?php endif; ?>

				<div class="artwork__sizes">
					<?php get_template_part( 'parts/ui/size-selector', null, array( 'sizes' => $artwork['sizes'] ) ); ?>
				</div>

				<div class="artwork__reserve">
					<?php get_template_part( 'parts/ui/reserve-bar', null, array( 'artwork' => $artwork ) ); ?>
				</div>

				<div class="artwork__purchase-info">
					<?php get_template_part( 'parts/ui/accordion', null, array( 'rows' => rartist_purchase_information() ) ); ?>
				</div>
			</div>
		</div>
	</div>

	<?php /* ---------- Story ---------- */ ?>
	<?php if ( $artwork['story'] ) : ?>
		<section class="section artwork__story">
			<div class="container">
				<div class="section__head">
					<p class="t-index"><?php echo esc_html( rartist_meta_line( array( __( 'The work', 'rartist' ), __( 'Details', 'rartist' ) ) ) ); ?></p>
				</div>

				<div class="split split--story" style="margin-top: var(--index-to-title)">
					<h2 class="artwork__story-title t-display-4"><?php echo esc_html( $artwork['story']['title'] ); ?></h2>

					<div>
						<?php if ( '' !== $artwork['story']['body'] ) : ?>
							<p class="artwork__story-body t-body"><?php echo esc_html( $artwork['story']['body'] ); ?></p>
						<?php endif; ?>

						<div class="artwork__specs">
							<?php get_template_part( 'parts/ui/spec-list', null, array( 'rows' => $artwork['specs'] ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* ---------- In situ ---------- */ ?>
	<?php if ( $artwork['in_situ'] ) : ?>
		<section class="section artwork__situ">
			<div class="container">
				<div class="section__head">
					<p class="t-index"><?php echo esc_html( rartist_meta_line( array( __( 'In a room', 'rartist' ), __( 'Scale', 'rartist' ) ) ) ); ?></p>
				</div>

				<div class="split split--situ" style="margin-top: 47px">
					<figure class="artwork__situ-figure">
						<?php
						echo wp_get_attachment_image(
							$artwork['in_situ']['image'],
							'rartist-hero',
							false,
							array(
								'alt'     => sprintf(
									/* translators: %s: artwork title */
									__( '%s shown in a room', 'rartist' ),
									$artwork['title']
								),
								'loading' => 'lazy',
							)
						);
						?>
					</figure>

					<div class="artwork__situ-copy">
						<?php if ( '' !== $artwork['in_situ']['size_label'] ) : ?>
							<p class="artwork__situ-label t-micro t-soft">
								<?php echo esc_html( rartist_meta_line( array( __( 'Shown', 'rartist' ), $artwork['in_situ']['size_label'] ) ) ); ?>
							</p>
						<?php endif; ?>

						<?php if ( '' !== $artwork['in_situ']['title'] ) : ?>
							<h2 class="artwork__situ-title t-display-5"><?php echo esc_html( $artwork['in_situ']['title'] ); ?></h2>
						<?php endif; ?>

						<?php if ( '' !== $artwork['in_situ']['body'] ) : ?>
							<p class="artwork__situ-body t-body-s"><?php echo esc_html( $artwork['in_situ']['body'] ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $artwork['in_situ']['note'] ) : ?>
							<p class="artwork__situ-note t-micro t-soft t-lines"><?php echo esc_html( $artwork['in_situ']['note'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* ---------- The artist ---------- */ ?>
	<?php
	get_template_part(
		'parts/section/artist-block',
		null,
		array(
			'artists' => $artwork['artists'],
			'index'   => rartist_meta_line(
				array( __( 'The artist', 'rartist' ), $artwork['collection']['name'] ?? '' )
			),
		)
	);
	?>

	<?php /* ---------- Related works ---------- */ ?>
	<?php
	get_template_part(
		'parts/section/related-works',
		null,
		array(
			'items'          => $related,
			'index'          => rartist_meta_line( array( __( 'Related', 'rartist' ), __( 'Works', 'rartist' ) ) ),
			'title'          => __( 'More from this collection.', 'rartist' ),
			'view_all_url'   => $artwork['collection']['url'] ?? '',
			'view_all_label' => __( 'View all', 'rartist' ),
			'variant'        => 'uniform',
			'meta'           => 'price',
		)
	);
	?>

</article>

<?php
get_footer();
