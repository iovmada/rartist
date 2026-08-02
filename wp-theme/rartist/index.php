<?php
/**
 * Fallback template.
 *
 * Placeholder until step 2 (shell + tokens) lands. It exists now so the theme is
 * activatable and the content model can be exercised in wp-admin.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		the_title( '<h1>', '</h1>' );
	}
}

get_footer();
