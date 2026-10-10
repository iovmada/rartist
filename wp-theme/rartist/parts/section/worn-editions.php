<?php
/**
 * Worn Editions gallery — frame HO2ev, with native scroll and JS controls.
 * @param array<int,array<string,string>> $args['items'] Display-ready editions.
 * @param string $args['email'] Collaboration address.
 * @package Rartist
 */
defined( 'ABSPATH' ) || exit;
$rartist_items = $args['items'] ?? array();
if ( empty( $rartist_items ) ) { return; }
$rartist_id = wp_unique_id( 'worn-gallery-' );
$rartist_count = count( $rartist_items );
?>
<section class="worn-gallery" id="worn-editions" data-worn-gallery role="region" aria-roledescription="carousel" aria-labelledby="<?php echo esc_attr( $rartist_id . '-title' ); ?>">
	<div class="worn-gallery__heading">
		<div>
			<p class="worn-gallery__eyebrow"><?php esc_html_e( 'RARTIST / WORN EDITIONS', 'rartist' ); ?></p>
			<h2 class="worn-gallery__title" id="<?php echo esc_attr( $rartist_id . '-title' ); ?>"><?php esc_html_e( 'Art, worn out.', 'rartist' ); ?></h2>
		</div>
		<p class="worn-gallery__intro"><?php esc_html_e( 'Custom-printed shirts.', 'rartist' ); ?><br><?php esc_html_e( 'Small batches. No reprints.', 'rartist' ); ?></p>
	</div>
	<div class="worn-gallery__viewport" id="<?php echo esc_attr( $rartist_id . '-viewport' ); ?>" tabindex="0" aria-label="<?php esc_attr_e( 'Shirt previews. Scroll or use the arrow keys to browse.', 'rartist' ); ?>">
		<?php foreach ( $rartist_items as $rartist_index => $rartist_item ) : ?>
			<figure class="worn-gallery__slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d', 'rartist' ), $rartist_index + 1, $rartist_count ) ); ?>" data-title="<?php echo esc_attr( $rartist_item['title'] ); ?>" data-details="<?php echo esc_attr( $rartist_item['details'] ); ?>">
				<img class="worn-gallery__image" src="<?php echo esc_url( $rartist_item['image'] ); ?>" width="<?php echo esc_attr( $rartist_item['width'] ?? 1376 ); ?>" height="<?php echo esc_attr( $rartist_item['height'] ?? 768 ); ?>" alt="<?php echo esc_attr( $rartist_item['alt'] ); ?>" loading="lazy" decoding="async" draggable="false">
				<figcaption class="worn-gallery__slide-caption"><?php echo esc_html( sprintf( '%02d — %s / %s', $rartist_index + 1, $rartist_item['title'], $rartist_item['details'] ) ); ?></figcaption>
			</figure>
		<?php endforeach; ?>
	</div>
	<div class="worn-gallery__footer" hidden>
		<div aria-live="polite" aria-atomic="true">
			<p class="worn-gallery__edition" data-worn-title><?php echo esc_html( '01 — ' . $rartist_items[0]['title'] ); ?></p>
			<p class="worn-gallery__details" data-worn-details><?php echo esc_html( $rartist_items[0]['details'] ); ?></p>
		</div>
		<div class="worn-gallery__controls">
			<p class="worn-gallery__counter" data-worn-counter><?php echo esc_html( sprintf( '01 / %02d', $rartist_count ) ); ?></p>
			<button class="worn-gallery__button" type="button" data-worn-prev aria-label="<?php esc_attr_e( 'Previous shirt', 'rartist' ); ?>" aria-controls="<?php echo esc_attr( $rartist_id . '-viewport' ); ?>"><svg aria-hidden="true" focusable="false" viewBox="0 0 13.99993896484375 14" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" > <path d="M6.90088 2.35156q-0.0957 0.01367-0.16748 0.05127-0.06836 0.03418-2.17725 2.13623-2.10547 2.09863-2.14648 2.16699-0.11279 0.18115-0.07178 0.40674 0.02734 0.0957 0.06836 0.17432 0.04443 0.0752 2.15674 2.17725l1.68164 1.67822q0.37598 0.36572 0.51611 0.46484 0.08545 0.05469 0.19824 0.05469l0.04102 0q0.23926 0 0.42041-0.18115 0.11279-0.11279 0.14697-0.28028 0.03418-0.16748-0.02051-0.32128-0.02734-0.08545-0.25976-0.3213-0.23242-0.23926-1.36377-1.37402-1.58252-1.58252-1.58252-1.59619 0-0.01367 3.42822-0.01367l3.01123 0q0.44775 0 0.52979-0.03418 0.08545-0.0376 0.16748-0.11963 0.08545-0.08545 0.1333-0.18799 0.05127-0.10596 0.05127-0.23242 0-0.12646-0.04102-0.23926-0.09912-0.19482-0.29394-0.29394l-0.08545-0.04102-3.44531 0q-3.45557 0-3.45557-0.01367 0-0.01367 1.58252-1.59619 1.13135-1.13477 1.36377-1.37061 0.23242-0.23926 0.25976-0.3247 0.06836-0.2085-0.00341-0.40332-0.06836-0.19824-0.24268-0.30079-0.17432-0.10596-0.3999-0.06494z" fill="currentColor" ></path> </svg></button>
			<button class="worn-gallery__button" type="button" data-worn-next aria-label="<?php esc_attr_e( 'Next shirt', 'rartist' ); ?>" aria-controls="<?php echo esc_attr( $rartist_id . '-viewport' ); ?>"><svg aria-hidden="true" focusable="false" viewBox="0 0 13.99993896484375 14" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" > <path d="M6.90088 2.35156q-0.18115 0.02734-0.32129 0.16748-0.11279 0.11279-0.14697 0.28028-0.03418 0.16748 0.02051 0.32129 0.02734 0.08545 0.25976 0.3247 0.23242 0.23584 1.36377 1.37061 1.58252 1.58252 1.58252 1.59619 0 0.01367-3.45557 0.01367l-3.44531 0-0.08545 0.04102q-0.22217 0.11279-0.30078 0.33838-0.0752 0.22217 0.00684 0.43408 0.05811 0.0957 0.14013 0.18115 0.08545 0.08203 0.16748 0.11963 0.08545 0.03418 0.53321 0.03418l3.01123 0q3.42822 0 3.42822 0.01367 0 0.01367-1.58252 1.59619-1.13135 1.13477-1.36377 1.37402-0.23242 0.23584-0.25976 0.3213-0.05469 0.15381-0.02051 0.32128 0.03418 0.16748 0.14697 0.28028 0.18115 0.18115 0.42041 0.18115l0.04102 0q0.11279 0 0.19824-0.05469 0.14014-0.09912 0.51611-0.46484l1.68164-1.67822q2.1123-2.10205 2.15332-2.18409 0.07178-0.12646 0.07178-0.28027 0-0.15381-0.07178-0.28027-0.04102-0.08203-2.1499-2.18067-2.10547-2.10205-2.17725-2.13623-0.06836-0.0376-0.23584-0.06494-0.04102 0-0.12646 0.01367z" fill="currentColor" ></path> </svg></button>
		</div>
	</div>
	<div class="worn-gallery__progress" role="group" aria-label="<?php esc_attr_e( 'Choose a shirt preview', 'rartist' ); ?>" hidden>
		<?php foreach ( $rartist_items as $rartist_index => $rartist_item ) : ?>
			<button class="worn-gallery__progress-button" type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Show %s', 'rartist' ), $rartist_item['title'] ) ); ?>" aria-controls="<?php echo esc_attr( $rartist_id . '-viewport' ); ?>"<?php echo 0 === $rartist_index ? ' aria-current="true"' : ''; ?>></button>
		<?php endforeach; ?>
	</div>
	<div class="worn-gallery__collaboration">
		<p><?php esc_html_e( 'Made for your event. Kept long after it.', 'rartist' ); ?></p>
		<a class="worn-gallery__link" href="<?php echo esc_url( 'mailto:' . ( $args['email'] ?? '' ) . '?subject=Worn%20Editions' ); ?>"><?php esc_html_e( 'PROPOSE A COLLABORATION', 'rartist' ); ?> <svg aria-hidden="true" focusable="false" viewBox="0 0 13.99993896484375 14" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" > <path d="M3.94775 3.51367q-0.14014 0.04102-0.25976 0.15381-0.11963 0.11279-0.16065 0.25293-0.05469 0.19482 0.02735 0.39307 0.08545 0.19482 0.25293 0.29394 0.08545 0.02734 0.19482 0.04102 0.1709 0.01367 0.65967 0.02734l3.83496 0-2.45068 2.45068q-1.65088 1.65088-2.05762 2.07129-0.40332 0.42041-0.43408 0.4751-0.10938 0.23926-0.01367 0.46485 0.09912 0.22217 0.32128 0.32128 0.22559 0.0957 0.46485-0.01367 0.05469-0.03076 0.4751-0.43408 0.42041-0.40674 2.07129-2.05762l2.45068-2.45068 0 2.21143q0 2.2251 0.02734 2.31054 0.02734 0.18115 0.15381 0.29395 0.19824 0.19482 0.46143 0.17431 0.2666-0.02051 0.42041-0.24609l0.02734-0.02734q0.04443-0.05469 0.05811-0.18116 0.01367-0.15381 0.02734-0.68701l0-5.37646-0.04102-0.09571q-0.02734-0.11279-0.12646-0.21191-0.09912-0.09912-0.21192-0.12646l-0.0957-0.04102-3.01123 0q-2.99414 0-3.06592 0.01367z" fill="currentColor" ></path> </svg></a>
	</div>
</section>
