<?php
/**
 * Collection detail — frame kWI5J.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

$collection = rartist_collection_view( get_queried_object() );

if ( ! $collection ) {
	get_footer();
	return;
}

$works = rartist_collection_works( $collection['id'] );
?>

<div class="collection">

	<?php /* ---------- Hero ---------- */ ?>
	<div class="container">
		<nav class="collection__breadcrumb t-micro t-soft" aria-label="<?php esc_attr_e( 'Breadcrumb', 'rartist' ); ?>">
			<a href="<?php echo esc_url( rartist_page_url( 'collections' ) ); ?>"><?php esc_html_e( 'Collections', 'rartist' ); ?></a>

			<?php if ( $collection['parent'] ) : ?>
				&nbsp;&nbsp;/&nbsp;&nbsp;<a href="<?php echo esc_url( $collection['parent']['url'] ); ?>"><?php echo esc_html( $collection['parent']['name'] ); ?></a>
			<?php endif; ?>

			&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo esc_html( $collection['name'] ); ?>
		</nav>

		<div class="collection__hero">
			<h1 class="collection__title"><?php echo esc_html( $collection['display_name'] ); ?></h1>
			<p class="collection__stats t-micro t-lines"><?php echo esc_html( $collection['stats'] ); ?></p>
		</div>

		<?php if ( $collection['hero_image'] ) : ?>
			<figure class="collection__figure">
				<?php
				echo wp_get_attachment_image(
					$collection['hero_image'],
					'rartist-hero',
					false,
					array(
						'alt'           => $collection['name'],
						'fetchpriority' => 'high',
					)
				);
				?>
			</figure>
		<?php endif; ?>

		<?php if ( '' !== $collection['hero_caption'] ) : ?>
			<p class="collection__caption t-micro t-soft"><?php echo esc_html( $collection['hero_caption'] ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== $collection['intro_title'] || '' !== $collection['intro_body'] ) : ?>
			<div class="collection__intro">
				<h2 class="collection__intro-title t-display-4"><?php echo esc_html( $collection['intro_title'] ); ?></h2>

				<div>
					<?php if ( '' !== $collection['intro_body'] ) : ?>
						<p class="collection__intro-body"><?php echo esc_html( $collection['intro_body'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $collection['details'] ) : ?>
						<p class="collection__details t-micro"><?php echo esc_html( $collection['details'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php /* ---------- Collections in this group ---------- */ ?>
	<?php if ( ! empty( $collection['children'] ) ) : ?>
		<?php
		get_template_part(
			'parts/section/collections-index',
			null,
			array(
				'collections' => $collection['children'],
				'show_head'   => true,
				'index'       => rartist_meta_line(
					array(
						__( 'In this group', 'rartist' ),
						sprintf(
							/* translators: %s: zero-padded number of collections */
							__( '%s collections', 'rartist' ),
							rartist_pad( count( $collection['children'] ), 2 ) ?: '00'
						),
					)
				),
				'title'       => __( 'Collections', 'rartist' ),
			)
		);
		?>
	<?php endif; ?>

	<?php /* ---------- Works ---------- */ ?>
	<?php
	get_template_part(
		'parts/section/works-row',
		null,
		array(
			'items'       => $works,
			'index'       => rartist_meta_line(
				array(
					__( 'Selected works', 'rartist' ),
					sprintf(
						/* translators: 1: works shown, 2: works in the collection */
						__( '%1$s of %2$s', 'rartist' ),
						rartist_pad( count( $works ), 2 ) ?: '00',
						rartist_pad( $collection['works_count'], 2 ) ?: '00'
					),
				)
			),
			'title'       => $collection['works_title'],
			'title_class' => 'collection__works-title',
			'meta'        => 'availability',
			'show_number' => true,
			'cta_label'   => count( $works ) < $collection['works_count']
				? sprintf(
					/* translators: %s: total number of works */
					__( 'View all %s works', 'rartist' ),
					rartist_pad( $collection['works_count'], 2 )
				)
				: '',
			'cta_url'     => count( $works ) < $collection['works_count'] ? (string) get_post_type_archive_link( 'artwork' ) : '',
		)
	);
	?>

	<?php /* ---------- Collector note ---------- */ ?>
	<?php if ( '' !== $collection['collector_quote'] ) : ?>
		<div class="section">
			<?php
			get_template_part(
				'parts/section/quote-band',
				null,
				array(
					'quote' => $collection['collector_quote'],
					'index' => rartist_meta_line( array( __( 'Collector note', 'rartist' ), $collection['number'] ) ),
					'note'  => $collection['shipping_note'],
				)
			);
			?>
		</div>
	<?php endif; ?>

	<?php /* ---------- The artist(s) ---------- */ ?>
	<div class="collection__artist">
		<?php
		get_template_part(
			'parts/section/artist-block',
			null,
			array(
				'artists'    => $collection['artists'],
				'index'      => rartist_meta_line( array( __( 'The artist', 'rartist' ), $collection['name'] ) ),
				'link_label' => __( "Visit the artist's studio", 'rartist' ),
				'wide'       => true,
			)
		);
		?>
	</div>

</div>

<?php
get_footer();
