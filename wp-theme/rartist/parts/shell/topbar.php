<?php
/**
 * Top bar — frame AF73d.
 *
 * The third nav item opens the navigation panel; every other item is a plain link.
 * Below 900px the whole nav collapses into the panel and only a trigger remains.
 *
 * Two variants:
 *   default     wordmark left, nav centre, enquire/bag right — on paper.
 *   prelaunch   the inlined logo left, nav centre, launch status right — white, laid
 *               over the dark hero, taking the place of the design's own hero bar.
 *
 * @param string $args['variant'] '' or 'prelaunch'
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_nav       = rartist_nav_items();
$rartist_panel     = 'rartist-nav-panel';
$rartist_prelaunch = 'prelaunch' === ( $args['variant'] ?? '' );
?>
<div class="container">
	<div class="topbar<?php echo $rartist_prelaunch ? ' topbar--over-hero' : ''; ?>">
		<?php
		/*
		 * The lockup is the brand identity everywhere — the frames' "RARTIST STUDIO"
		 * wordmark was a stand-in for it. Inlined rather than an <img> so it can take
		 * fill: currentColor: ink here, white over the prelaunch hero.
		 */
		?>
		<a
			class="topbar__wordmark<?php echo $rartist_prelaunch ? ' hero__logo' : ''; ?>"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php
				printf(
					/* translators: %s: the studio name */
					esc_attr__( '%s — home', 'rartist' ),
					esc_attr( rartist_studio_value( 'wordmark' ) )
				);
			?>"
		>
			<?php require RARTIST_DIR . '/parts/ui/logo.php'; ?>
		</a>

		<nav class="topbar__nav" aria-label="<?php esc_attr_e( 'Primary', 'rartist' ); ?>">
			<?php foreach ( $rartist_nav as $rartist_index => $rartist_item ) : ?>
				<?php if ( 2 === $rartist_index ) : ?>
					<button
						class="topbar__toggle t-nav"
						type="button"
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $rartist_panel ); ?>"
						data-nav-toggle
					>
						<?php echo esc_html( $rartist_item['short'] ); ?>
						<span class="topbar__toggle-arrow" aria-hidden="true">&uarr;</span>
					</button>
				<?php else : ?>
					<a
						class="topbar__link t-nav"
						href="<?php echo esc_url( $rartist_item['url'] ); ?>"
						<?php echo $rartist_item['current'] ? 'aria-current="page"' : ''; ?>
					>
						<?php echo esc_html( $rartist_item['short'] ); ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<?php if ( $rartist_prelaunch ) : ?>
			<p class="topbar__status hero__status"><?php esc_html_e( 'Launching spring 2027', 'rartist' ); ?></p>
		<?php elseif ( RARTIST_COMMERCE ) : ?>
			<a class="topbar__bag t-nav" href="<?php echo esc_url( rartist_page_url( 'bag' ) ); ?>">
				<?php esc_html_e( 'Bag', 'rartist' ); ?>&nbsp;&nbsp;00
			</a>
		<?php else : ?>
			<a class="topbar__bag t-nav" href="<?php echo esc_url( 'mailto:' . rartist_studio_value( 'enquiry_email' ) ); ?>">
				<?php esc_html_e( 'Enquire', 'rartist' ); ?>
			</a>
		<?php endif; ?>

		<button
			class="topbar__burger t-nav"
			type="button"
			aria-expanded="false"
			aria-controls="<?php echo esc_attr( $rartist_panel ); ?>"
			data-nav-toggle
		>
			<?php esc_html_e( 'Explore', 'rartist' ); ?>
			<span class="topbar__toggle-arrow" aria-hidden="true">&uarr;</span>
		</button>
	</div>

	<?php if ( ! $rartist_prelaunch ) : ?>
		<hr class="topbar__rule">
	<?php endif; ?>
</div>

<?php get_template_part( 'parts/shell/nav-panel', null, array( 'id' => $rartist_panel ) ); ?>
