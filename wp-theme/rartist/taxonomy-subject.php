<?php
/**
 * Subject archive — the catalogue, filtered.
 *
 * The design has no separate subject page: clicking ABSTRACT in the filter row shows
 * the same catalogue with fewer plates. So this is literally the catalogue template,
 * which branches on is_tax( 'subject' ) for its eyebrow, headline and the collections
 * index it omits.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

require get_template_directory() . '/archive-artwork.php';
