<?php
/**
 * Subject filters and sort control — frame boTQU.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

$rartist_filters = rartist_subject_filters();
?>
<div class="filter-bar">
	<nav class="filter-bar__list" aria-label="<?php esc_attr_e( 'Filter by subject', 'rartist' ); ?>">
		<?php foreach ( $rartist_filters as $rartist_filter ) : ?>
			<a
				class="filter-bar__link t-meta"
				href="<?php echo esc_url( $rartist_filter['url'] ); ?>"
				<?php echo $rartist_filter['current'] ? 'aria-current="true"' : ''; ?>
			>
				<?php echo esc_html( $rartist_filter['label'] ); ?>
				<span class="filter-bar__count"><?php echo esc_html( $rartist_filter['count'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>

	<p class="filter-bar__sort t-meta">
		<?php echo esc_html( rartist_meta_line( array( __( 'Sort', 'rartist' ), __( "Curator's order", 'rartist' ) ) ) ); ?>
		&nbsp;&darr;
	</p>
</div>
