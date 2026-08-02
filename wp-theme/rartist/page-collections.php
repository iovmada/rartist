<?php
/**
 * Collections index.
 *
 * Template Name: Collections index
 *
 * The design has no standalone collections page — the index lives in the lower half of
 * the catalogue frame (iaMv8). This reuses that exact section, so the two stay
 * identical, and puts the page's own content above it.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

the_post();

/*
 * Everything, featured or not. The catalogue promotes a chosen few; this page is the
 * complete index, so a collection created five minutes ago is reachable immediately.
 */
$collections = rartist_all_collections();
?>

<div class="catalogue">
	<div class="container">
		<p class="catalogue__eyebrow t-index">
			<?php
			echo esc_html(
				rartist_meta_line(
					array( __( 'Curated groups', 'rartist' ), rartist_studio_value( 'season' ) )
				)
			);
			?>
		</p>

		<div class="catalogue__head">
			<h1 class="catalogue__headline"><?php the_title(); ?></h1>

			<div class="catalogue__intro t-lead"><?php the_content(); ?></div>
		</div>
	</div>

	<?php
	get_template_part(
		'parts/section/collections-index',
		null,
		array(
			'collections' => $collections,
			'show_head'   => false,
		)
	);
	?>
</div>

<?php
get_footer();
