<?php
/**
 * Document head and page shell opening.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'rartist' ); ?></a>

<?php if ( rartist_is_prelaunch() ) : ?>
	<?php
	/*
	 * On the prelaunch page the bar is laid over the dark hero, in place of the design's
	 * own hero bar. It sits outside <main> because .hero clips its overflow, which would
	 * swallow the dropdown panel.
	 */
	?>
	<header class="site-header site-header--over-hero">
		<?php get_template_part( 'parts/shell/topbar', null, array( 'variant' => 'prelaunch' ) ); ?>
	</header>
<?php else : ?>
	<header class="site-header">
		<?php get_template_part( 'parts/shell/topbar' ); ?>
	</header>
<?php endif; ?>

<main id="main">
