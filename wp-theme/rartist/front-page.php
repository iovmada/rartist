<?php
/**
 * Prelaunch page — frame uOIiu, and the live rartist.ro homepage.
 *
 * Ported from the static site's index.html. It brings its own top bar and footer, so
 * the theme's shell steps aside (see rartist_is_prelaunch()) — a prelaunch page with a
 * catalogue nav on it would be advertising a shop that has not opened.
 *
 * The four display lines are sized in vw so their bleed past both edges holds at every
 * width. Do not "fix" that overflow; it is the design.
 *
 * When the catalogue opens: delete this file and set Settings → Reading to the
 * Collections page, or point the front page wherever the launch calls for.
 *
 * @package Rartist
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rartist_img   = RARTIST_URI . '/assets/img';
$rartist_email = rartist_studio_value( 'enquiry_email' );
?>

<div class="page">

	<?php
	/*
	 * The hero's own bar now lives in header.php, laid over the hero as the prelaunch
	 * variant of the site top bar — the logo and launch status the design had, plus the
	 * main nav. .hero keeps a matching top padding where that bar sits.
	 */
	?>
	<header class="hero" id="hero">
		<div class="hero__statement-row">
			<p class="hero__statement" data-reveal>
				<?php esc_html_e( 'A quiet house for printed art. Every edition chosen by hand, printed in small numbers, and sent to people who intend to live with it for a long time.', 'rartist' ); ?>
			</p>
		</div>

		<div class="hero__headline-row">
			<h1 class="display display--hero" data-reveal><?php esc_html_e( 'Rartist', 'rartist' ); ?></h1>
		</div>
	</header>

	<?php /* ---------- Statement ---------- */ ?>
	<section class="statement" aria-labelledby="statement-headline">
		<div class="statement__headline-row">
			<h2 class="display display--statement" id="statement-headline" data-reveal>
				<?php esc_html_e( 'Collect slowly', 'rartist' ); ?>
			</h2>
		</div>
	</section>

	<?php /* ---------- Editorial ---------- */ ?>
	<section class="editorial">
		<div class="editorial__row">
			<div class="editorial__label">
				<p class="mono mono--muted"><?php esc_html_e( 'No. 01', 'rartist' ); ?></p>
				<p class="mono"><?php esc_html_e( 'What Rartist is', 'rartist' ); ?></p>
			</div>

			<div class="editorial__body" data-reveal>
				<p class="body">
					<?php esc_html_e( 'We are building a gallery without the room. Each season we release a single, tightly edited collection of prints — thirty works, no more — sourced from painters, photographers and printmakers we have followed for years.', 'rartist' ); ?>
				</p>
				<p class="body body--muted">
					<?php esc_html_e( 'Every edition is printed on cotton rag, numbered, and retired for good once it sells out. No infinite catalogue, no algorithmic recommendations, no art chosen because it matches a sofa.', 'rartist' ); ?>
				</p>
			</div>

			<div class="editorial__link">
				<a class="mono link" href="<?php echo esc_url( rartist_page_url( 'about', '#signup' ) ); ?>">
					<?php esc_html_e( 'Read the manifesto', 'rartist' ); ?>
				</a>
				<p class="mono mono--muted"><?php esc_html_e( '3 min', 'rartist' ); ?></p>
			</div>
		</div>

		<figure class="editorial__photo-row" data-reveal>
			<img
				class="editorial__photo"
				src="<?php echo esc_url( $rartist_img . '/plate-014.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Untitled (Harbour) — archival pigment print hung in a stairwell', 'rartist' ); ?>"
				loading="lazy"
				decoding="async"
			>
			<figcaption class="editorial__caption">
				<p class="mono"><?php esc_html_e( 'Plate 014', 'rartist' ); ?></p>
				<p class="caption"><?php esc_html_e( 'Untitled (Harbour), archival pigment on cotton rag, edition of 40.', 'rartist' ); ?></p>
			</figcaption>
		</figure>
	</section>

	<?php /* ---------- Collection ---------- */ ?>
	<section class="collection" aria-labelledby="collection-headline">
		<div class="collection__headline-row">
			<h2 class="display display--collection" id="collection-headline" data-reveal>
				<?php esc_html_e( 'The first thirty', 'rartist' ); ?>
			</h2>
		</div>

		<ul class="plates">
			<?php
			$rartist_plates = array(
				array(
					'class'   => 'plate--002',
					'image'   => 'plate-002.jpg',
					'alt'     => __( 'Öland, 2024 — black and white photograph of a coastal cliff', 'rartist' ),
					'number'  => __( 'Plate 002', 'rartist' ),
					'title'   => __( 'Öland, 2024', 'rartist' ),
					'edition' => __( 'Edition of 30', 'rartist' ),
				),
				array(
					'class'   => 'plate--009',
					'image'   => 'plate-009.png',
					'alt'     => __( 'Interval No. 4 — abstract print in ochre and bone', 'rartist' ),
					'number'  => __( 'Plate 009', 'rartist' ),
					'title'   => __( 'Interval No. 4', 'rartist' ),
					'edition' => __( 'Edition of 25', 'rartist' ),
				),
				array(
					'class'   => 'plate--017',
					'image'   => 'plate-017.jpg',
					'alt'     => __( 'Long Grass — botanical study of a pink hibiscus', 'rartist' ),
					'number'  => __( 'Plate 017', 'rartist' ),
					'title'   => __( 'Long Grass', 'rartist' ),
					'edition' => __( 'Edition of 30', 'rartist' ),
				),
			);

			foreach ( $rartist_plates as $rartist_plate ) :
				?>
				<li class="plate <?php echo esc_attr( $rartist_plate['class'] ); ?>" data-reveal>
					<img
						class="plate__image"
						src="<?php echo esc_url( $rartist_img . '/' . $rartist_plate['image'] ); ?>"
						alt="<?php echo esc_attr( $rartist_plate['alt'] ); ?>"
						loading="lazy"
						decoding="async"
					>
					<div class="plate__caption">
						<p class="mono"><?php echo esc_html( $rartist_plate['number'] ); ?></p>
						<p class="plate__title"><?php echo esc_html( $rartist_plate['title'] ); ?></p>
						<p class="mono mono--muted"><?php echo esc_html( $rartist_plate['edition'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<?php /* ---------- Signup ---------- */ ?>
	<section class="signup" id="signup" aria-labelledby="signup-headline">
		<p class="signup__eyebrow mono"><?php esc_html_e( 'The first collection opens spring 2027', 'rartist' ); ?></p>

		<form class="signup__form" id="signup-form" novalidate>
			<div class="signup__field">
				<label class="visually-hidden" for="email"><?php esc_html_e( 'Email address', 'rartist' ); ?></label>
				<input
					class="signup__input"
					type="email"
					id="email"
					name="email"
					placeholder="your@email.com"
					autocomplete="email"
					required
				>
			</div>
			<button class="signup__submit mono" type="submit"><?php esc_html_e( 'Request an invite', 'rartist' ); ?></button>
		</form>

		<p class="signup__fine-print" id="signup-message" role="status" aria-live="polite">
			<span id="collector-count">1,240</span>
			<?php esc_html_e( 'collectors already on the list. First access to every release, and the founding plate sent free with your first order.', 'rartist' ); ?>
		</p>

		<div class="signup__headline-row">
			<h2 class="display display--signup" id="signup-headline" data-reveal><?php esc_html_e( 'Be first', 'rartist' ); ?></h2>
		</div>
	</section>

	<?php /* ---------- Footer ---------- */ ?>
	<footer class="footer">
		<p class="mono"><?php echo esc_html( rartist_studio_value( 'footer_mark' ) ); ?></p>
		<nav class="footer__links" aria-label="<?php esc_attr_e( 'Elsewhere', 'rartist' ); ?>">
			<a
				class="mono mono--muted link"
				href="https://www.instagram.com/raretist.studio"
				target="_blank"
				rel="noopener noreferrer me"
			><?php esc_html_e( 'Instagram', 'rartist' ); ?></a>
			<a class="mono mono--muted link" href="<?php echo esc_url( 'mailto:' . $rartist_email ); ?>">
				<?php echo esc_html( $rartist_email ); ?>
			</a>
			<a class="mono mono--muted link" href="<?php echo esc_url( rartist_page_url( 'shipping' ) ); ?>">
				<?php esc_html_e( 'Press', 'rartist' ); ?>
			</a>
		</nav>
		<p class="mono mono--muted">&copy;&nbsp;<?php echo esc_html( gmdate( 'Y' ) ); ?></p>
	</footer>

</div>

<?php
get_footer();
