<?php
/**
 * Site footer — frame Vtmzo. Full-bleed ink band.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="site-footer">
	<div class="container">
		<div class="site-footer__inner">
			<span class="t-index" style="color: var(--inverse)">
				<?php echo esc_html( rartist_studio_value( 'footer_mark' ) ); ?>
			</span>

			<nav class="site-footer__links t-meta" aria-label="<?php esc_attr_e( 'Footer', 'rartist' ); ?>">
				<?php foreach ( rartist_footer_links() as $rartist_link ) : ?>
					<a href="<?php echo esc_url( $rartist_link['url'] ); ?>"><?php echo esc_html( $rartist_link['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>

			<span class="site-footer__copyright t-meta">
				&copy;&nbsp;<?php echo esc_html( gmdate( 'Y' ) ); ?>
			</span>
		</div>
	</div>
</footer>
