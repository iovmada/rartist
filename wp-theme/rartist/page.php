<?php
/**
 * Editorial page — About, Shipping, and anything else written in the block editor.
 *
 * The only template whose body comes from the editor rather than from fields, so it
 * gets a reading measure and theme.json's element styles do the rest.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

the_post();
?>

<article class="page">
	<div class="container">
		<h1 class="page__title t-display-2" style="margin-top: 60px"><?php the_title(); ?></h1>

		<div class="page__content t-lead" style="max-width: 700px; margin-top: 40px">
			<?php the_content(); ?>
		</div>
	</div>
</article>

<?php
get_footer();
