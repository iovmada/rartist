<?php
/**
 * Catalogue — frame iaMv8.
 *
 * Also serves the subject archives (taxonomy-subject falls back here through
 * archive.php), so filtering by ABSTRACT re-renders the same page with fewer plates.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

$works = array();

while ( have_posts() ) {
	the_post();
	$view = rartist_artwork_view( get_post() );

	if ( $view ) {
		$works[] = $view;
	}
}

$is_subject = is_tax( 'subject' );
$subject    = $is_subject ? get_queried_object() : null;
?>

<div class="catalogue">

	<div class="container">
		<p class="catalogue__eyebrow t-index">
			<?php
			echo esc_html(
				rartist_meta_line(
					array(
						$is_subject ? $subject->name : __( 'Available works', 'rartist' ),
						rartist_studio_value( 'season' ),
					)
				)
			);
			?>
		</p>

		<div class="catalogue__head">
			<h1 class="catalogue__headline">
				<?php echo esc_html( $is_subject ? $subject->name : __( 'Artworks', 'rartist' ) ); ?>
			</h1>

			<p class="catalogue__intro t-lead">
				<?php esc_html_e( 'Printed works selected for their atmosphere, colour and staying power. Released slowly, in numbered editions.', 'rartist' ); ?>
			</p>
		</div>

		<div class="catalogue__filters">
			<?php get_template_part( 'parts/section/filter-bar' ); ?>
		</div>

		<?php if ( empty( $works ) ) : ?>
			<p class="catalogue__count t-micro"><?php esc_html_e( 'No works published yet.', 'rartist' ); ?></p>
		<?php else : ?>
			<div class="collage">
				<?php foreach ( $works as $work ) : ?>
					<?php
					get_template_part(
						'parts/ui/plate-card',
						null,
						array(
							'artwork'     => $work,
							'meta'        => 'edition',
							'show_number' => true,
						)
					);
					?>
				<?php endforeach; ?>
			</div>

			<p class="catalogue__count t-micro"><?php echo esc_html( rartist_shown_count_label( count( $works ) ) ); ?></p>
		<?php endif; ?>
	</div>

	<?php
	// The collections index closes the catalogue, but not a filtered subject view.
	if ( ! $is_subject ) {
		get_template_part(
			'parts/section/collections-index',
			null,
			array( 'collections' => rartist_featured_collections() )
		);
	}
	?>

</div>

<?php
get_footer();
