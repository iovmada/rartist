<?php
/**
 * Navigation panel — frames tu30N (desktop) and tlvLz (compact).
 *
 * One markup for both: the sheet header and footer strip are hidden or shown by CSS.
 * Counts come from the content model, not from typed strings.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_id      = $args['id'] ?? 'rartist-nav-panel';
$rartist_items   = rartist_nav_items();
$rartist_feature = rartist_nav_feature();
$rartist_studio  = rartist_studio();
?>
<div class="nav-panel" id="<?php echo esc_attr( $rartist_id ); ?>" data-nav-panel hidden>
	<div class="nav-panel__sheet-head">
		<span class="t-micro"><?php esc_html_e( 'Explore', 'rartist' ); ?></span>
		<button class="nav-panel__close" type="button" data-nav-close aria-label="<?php esc_attr_e( 'Close menu', 'rartist' ); ?>">
			&times;
		</button>
	</div>

	<div class="container">
		<div class="nav-panel__body">
			<div class="nav-panel__feature">
				<p class="nav-panel__eyebrow t-micro"><?php echo esc_html( $rartist_feature['eyebrow'] ); ?></p>
				<p class="nav-panel__statement"><?php echo esc_html( $rartist_feature['statement'] ); ?></p>
				<p class="nav-panel__note"><?php echo esc_html( $rartist_feature['note'] ); ?></p>
			</div>

			<div class="nav-panel__options">
				<?php foreach ( $rartist_items as $rartist_item ) : ?>
					<a class="nav-option" href="<?php echo esc_url( $rartist_item['url'] ); ?>">
						<span class="nav-option__labels">
							<span class="nav-option__label t-meta"><?php echo esc_html( $rartist_item['label'] ); ?></span>
							<span class="nav-option__meta t-nano"><?php echo esc_html( $rartist_item['meta'] ); ?></span>
						</span>
						<span class="nav-option__arrow" aria-hidden="true">&rarr;</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="nav-panel__footer t-nano">
			<span>
				<?php
				echo esc_html(
					rartist_meta_line(
						array( __( 'New collection', 'rartist' ), $rartist_studio['season'] )
					)
				);
				?>
			</span>
			<span class="nav-panel__footer-links">
				<?php foreach ( rartist_footer_links() as $rartist_link ) : ?>
					<a href="<?php echo esc_url( $rartist_link['url'] ); ?>"><?php echo esc_html( $rartist_link['label'] ); ?></a>
				<?php endforeach; ?>
			</span>
		</div>
	</div>
</div>
