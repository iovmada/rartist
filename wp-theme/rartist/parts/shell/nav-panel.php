<?php
/**
 * Navigation panel — frames tu30N (desktop) and tlvLz (compact).
 *
 * One markup for every dropdown in the bar: the "Explore" panel with its feature copy
 * beside four destinations, and the Collections panel listing every collection. The
 * sheet header and footer strip are hidden or shown by CSS, so the compact skin comes
 * for free.
 *
 * @param string                              $args['id']       Panel element id.
 * @param array<int,array<string,mixed>>       $args['items']    Option rows: label, meta, url,
 *                                                              optional depth and current.
 * @param array<string,string>|null            $args['feature']  Feature column, or null for a
 *                                                              full-width list.
 * @param string                               $args['title']    Compact sheet heading.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_id      = $args['id'] ?? 'rartist-nav-panel';
$rartist_items   = $args['items'] ?? array();
$rartist_feature = $args['feature'] ?? null;
$rartist_title   = $args['title'] ?? __( 'Explore', 'rartist' );
$rartist_studio  = rartist_studio();

if ( empty( $rartist_items ) ) {
	return;
}
?>
<div class="nav-panel" id="<?php echo esc_attr( $rartist_id ); ?>" data-nav-panel hidden>
	<div class="nav-panel__sheet-head">
		<span class="t-micro"><?php echo esc_html( $rartist_title ); ?></span>
		<button class="nav-panel__close" type="button" data-nav-close aria-label="<?php esc_attr_e( 'Close menu', 'rartist' ); ?>">
			&times;
		</button>
	</div>

	<div class="container">
		<div class="nav-panel__body<?php echo $rartist_feature ? '' : ' nav-panel__body--wide'; ?>">
			<?php if ( $rartist_feature ) : ?>
				<div class="nav-panel__feature">
					<p class="nav-panel__eyebrow t-micro"><?php echo esc_html( $rartist_feature['eyebrow'] ); ?></p>
					<p class="nav-panel__statement"><?php echo esc_html( $rartist_feature['statement'] ); ?></p>
					<p class="nav-panel__note"><?php echo esc_html( $rartist_feature['note'] ); ?></p>
				</div>
			<?php endif; ?>

			<div class="nav-panel__options">
				<?php foreach ( $rartist_items as $rartist_item ) : ?>
					<?php $rartist_has_avatar = array_key_exists( 'avatar', $rartist_item ); ?>
					<a
						class="nav-option<?php echo empty( $rartist_item['depth'] ) ? '' : ' nav-option--child'; ?><?php echo $rartist_has_avatar ? ' nav-option--with-avatar' : ''; ?>"
						href="<?php echo esc_url( $rartist_item['url'] ); ?>"
						<?php echo empty( $rartist_item['current'] ) ? '' : 'aria-current="page"'; ?>
					>
						<span class="nav-option__lead">
							<?php if ( $rartist_has_avatar ) : ?>
								<span class="nav-option__avatar" aria-hidden="true">
									<?php
									if ( $rartist_item['avatar'] ) {
										echo wp_get_attachment_image(
											$rartist_item['avatar'],
											'thumbnail',
											false,
											array(
												'alt'     => '',
												'loading' => 'lazy',
											)
										);
									} else {
										echo '<span class="nav-option__initial">' . esc_html( $rartist_item['initial'] ?? '' ) . '</span>';
									}
									?>
								</span>
							<?php endif; ?>

							<span class="nav-option__labels">
								<span class="nav-option__label t-meta"><?php echo esc_html( $rartist_item['label'] ); ?></span>
								<span class="nav-option__meta t-nano"><?php echo esc_html( $rartist_item['meta'] ); ?></span>
							</span>
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
