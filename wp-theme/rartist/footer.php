<?php
/**
 * Page shell closing.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<?php if ( ! rartist_is_prelaunch() ) : ?>
	<?php get_template_part( 'parts/shell/footer' ); ?>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
