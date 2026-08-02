/**
 * Compare rendered pages against the Pencil frames' measurements.
 *
 *   node scripts/measure-design.js [template] [base-url]
 *   node scripts/measure-design.js artwork http://localhost:8790
 *
 * Needs node 18+ and a playwright-core install. On this machine:
 *   CHROMIUM=$(find ~/Library/Caches/ms-playwright -name Chromium -type f | head -1) \
 *   /usr/local/opt/node@20/bin/node scripts/measure-design.js artwork
 *
 * Each check is [label, selector, property, design value, tolerance]. Properties:
 *   width | height | inner-width | font-size | track-gap | track-widths
 *
 * Display headings carry a deliberate negative margin (Playfair's optical left
 * bearing, which is why the frames place display text at x=43 and meta at x=48), so
 * gaps are measured from resolved grid tracks rather than from child boxes.
 */

const path = require('path');

const PLAYWRIGHT = process.env.PLAYWRIGHT_CORE || '/Users/mada/.dev-browser/node_modules/playwright-core';
const { chromium } = require(PLAYWRIGHT);

const PAGES = {
	artwork: {
		path: '/artworks/quiet-table/',
		frame: 'kTNBO',
		checks: [
			['container inner width', '.artwork > .container', 'inner-width', 1344, 1],
			['hero tracks', '.split--hero', 'track-widths', [650, 470], 1],
			['hero gap', '.split--hero', 'track-gap', 122, 1],
			['artwork title size', '.artwork__title', 'font-size', 78, 0.6],
			['plate meta size', '.artwork__plate-meta', 'font-size', 10, 0.2],
			['description measure', '.artwork__description', 'width', 470, 2],
			['reserve bar', '.reserve-bar', 'width', 470, 2],
			['reserve bar height', '.reserve-bar', 'height', 58, 1],
			['size button height', '.size-option__face', 'height', 36, 1],
			['accordion row height', '.accordion__summary', 'height', 48, 1],
			['story tracks', '.split--story', 'track-widths', [510, 550], 1],
			['story gap', '.split--story', 'track-gap', 132, 1],
			['story title size', '.artwork__story-title', 'font-size', 56, 0.6],
			['story body measure', '.artwork__story-body', 'width', 480, 2],
			['spec row height', '.rows--specs .rows__item', 'height', 38, 1],
			['specs width', '.artwork__specs .rows', 'width', 550, 2],
			['in-situ tracks', '.split--situ', 'track-widths', [860, 310], 1],
			['in-situ gap', '.split--situ', 'track-gap', 102, 1],
			['in-situ title size', '.artwork__situ-title', 'font-size', 42, 0.6],
			['artist tracks', '.split--artist', 'track-widths', [390, 560], 1],
			['artist gap', '.split--artist', 'track-gap', 132, 1],
			['artist name size', '.artist-card__name', 'font-size', 57, 0.6],
			['plate card width', '.plate-row > *:first-child', 'width', 250, 3],
			['related title size', '.artwork__related-title', 'font-size', 76, 0.6],
			['footer band height', '.site-footer__inner', 'height', 75, 1],
			// The frames show a "RARTIST STUDIO" text wordmark; the real identity is the
			// lockup, so the bar carries the mark at 30px instead.
			['logo height', '.topbar__wordmark svg', 'height', 30, 0.5],
			['nav item size', '.topbar__link', 'font-size', 11, 0.2],
		],
	},

	catalogue: {
		path: '/artworks/',
		frame: 'iaMv8',
		checks: [
			['container inner width', '.catalogue > .container', 'inner-width', 1344, 1],
			['headline size', '.catalogue__headline', 'font-size', 202, 1],
			['headline box', '.catalogue__headline', 'width', 510, 2],
			['intro measure', '.catalogue__intro', 'width', 390, 2],
			['filter bar height', '.filter-bar', 'height', 56, 1],
			['collage columns', '.collage', 'track-widths', [448, 448, 448], 1],
			['slot 1 width', '.collage > *:nth-child(1)', 'width', 360, 2],
			['slot 2 width', '.collage > *:nth-child(2)', 'width', 330, 2],
			['slot 3 width', '.collage > *:nth-child(3)', 'width', 377, 2],
			['slot 4 width', '.collage > *:nth-child(4)', 'width', 430, 2],
			['slot 5 width', '.collage > *:nth-child(5)', 'width', 300, 2],
			['slot 6 width', '.collage > *:nth-child(6)', 'width', 362, 2],
			['collections title', '.catalogue__collections-title', 'font-size', 176, 1],
			['feature image', '.collection-feature__figure', 'width', 625, 2],
			['feature title', '.collection-feature__title', 'font-size', 68, 0.6],
		],
	},

	collection: {
		path: '/collections/domestic-colour/',
		frame: 'kWI5J',
		checks: [
			['hero title size', '.collection__title', 'font-size', 166, 1],
			['hero image ratio', '.collection__figure', 'width', 1344, 2],
			['intro tracks', '.collection__intro', 'track-widths', [460, 480], 1],
			['intro gap', '.collection__intro', 'track-gap', 257, 1],
			['intro title size', '.collection__intro-title', 'font-size', 53, 0.6],
			['works title size', '.collection__works-title', 'font-size', 122, 1],
			['works gap', '.works-row', 'track-gap', 62, 1],
			['quote band height', '.quote-band', 'height', 220, 1],
			['quote size', '.quote-band__quote', 'font-size', 34, 0.6],
			['artist tracks', '.split--artist-wide', 'track-widths', [450, 560], 1],
			['artist name size', '.artist-card__name', 'font-size', 65, 0.6],
		],
	},

	rartist: {
		path: '/rartists/mara-vell/',
		frame: 'RmliI',
		checks: [
			['name size', '.rartist__name', 'font-size', 182, 1],
			['hero tracks', '.rartist__hero', 'track-widths', [842, 502], 1],
			['statement size', '.rartist__statement', 'font-size', 37, 0.6],
			['statement measure', '.rartist__statement', 'width', 520, 2],
			['portrait width', '.rartist__portrait', 'width', 502, 2],
			['bio tracks', '.rartist__bio', 'track-widths', [560, 560], 1],
			['bio gap', '.rartist__bio', 'track-gap', 142, 1],
			['bio title size', '.rartist__bio-title', 'font-size', 56, 0.6],
			['bio measure', '.rartist__bio-body', 'width', 500, 2],
			['works title size', '.rartist__works-title', 'font-size', 110, 1],
			['quote size', '.quote-band--centred .quote-band__quote', 'font-size', 37, 0.6],
			['featured title size', '.rartist__featured-title', 'font-size', 51, 0.6],
		],
	},
};

const template = process.argv[2] || 'artwork';
const base = (process.argv[3] || 'http://localhost:8790').replace(/\/$/, '');
const page_spec = PAGES[template];

if (!page_spec) {
	console.error(`Unknown template "${template}". Known: ${Object.keys(PAGES).join(', ')}`);
	process.exit(1);
}

(async () => {
	const browser = await chromium.launch({ executablePath: process.env.CHROMIUM });
	const page = await browser.newPage({ viewport: { width: 1440, height: 1200 } });

	await page.goto(base + page_spec.path, { waitUntil: 'load' });

	const results = await page.evaluate((checks) => {
		const tracks = (el) =>
			getComputedStyle(el)
				.gridTemplateColumns.split(' ')
				.map((v) => parseFloat(v));

		return checks.map(([label, selector, prop, expected, tol]) => {
			const el = document.querySelector(selector);

			if (!el) {
				return { label, missing: true, expected, tol };
			}

			const cs = getComputedStyle(el);
			const box = el.getBoundingClientRect();
			let actual;

			switch (prop) {
				case 'inner-width':
					actual = box.width - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
					break;
				case 'width':
					actual = box.width;
					break;
				case 'height':
					actual = box.height;
					break;
				case 'font-size':
					actual = parseFloat(cs.fontSize);
					break;
				case 'track-widths':
					actual = tracks(el);
					break;
				case 'track-gap': {
					const t = tracks(el);
					const used = t.reduce((sum, v) => sum + v, 0);
					const gaps = t.length - 1;
					// Percentage gaps resolve against the content box, so derive the real
					// distance from the difference between declared tracks and the box.
					const declared = parseFloat(cs.columnGap);
					actual = cs.columnGap.endsWith('%')
						? (parseFloat(cs.columnGap) / 100) * (box.width - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight))
						: declared;
					void used;
					void gaps;
					break;
				}
				default:
					actual = null;
			}

			return { label, actual, expected, tol };
		});
	}, page_spec.checks);

	let failures = 0;

	for (const r of results) {
		if (r.missing) {
			console.log(`  MISSING  ${r.label}`);
			failures++;
			continue;
		}

		const actuals = Array.isArray(r.actual) ? r.actual : [r.actual];
		const expects = Array.isArray(r.expected) ? r.expected : [r.expected];
		const diffs = expects.map((e, i) => Math.abs((actuals[i] ?? NaN) - e));
		const ok = diffs.every((d) => d <= r.tol);

		if (!ok) {
			failures++;
		}

		console.log(
			`  ${ok ? 'PASS' : 'FAIL'}  ${r.label.padEnd(22)} design ${expects.join(' / ').padStart(11)}   rendered ${actuals.map((a) => (a ?? NaN).toFixed(1)).join(' / ').padStart(13)}`
		);
	}

	const overflow = await page.evaluate(() => ({
		scrollWidth: document.documentElement.scrollWidth,
		viewport: window.innerWidth,
	}));

	console.log(
		`\n  horizontal overflow: ${overflow.scrollWidth > overflow.viewport ? `YES (${overflow.scrollWidth} > ${overflow.viewport})` : 'none'}`
	);
	console.log(`  frame ${page_spec.frame}: ${failures === 0 ? 'all checks passed' : failures + ' failed'}`);

	await browser.close();
	process.exit(failures === 0 ? 0 : 1);
})();
