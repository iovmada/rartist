# Rartist Studio — WordPress theme

Catalogue theme for [rartist.ro](https://rartist.ro). Built from the Pencil design
`~/Documents/rartist/rartist.pen`
([shared](https://app.pen.dev/s/fvDckuTq_HpyIRDrD_84iwkcOdv47O2Jk8G6RxmLJmo)).

No commerce: prices are displayed, nothing is sold. `RARTIST_COMMERCE` in `functions.php`
is the single switch that reveals bag affordances when that changes.

## Content model

```
Rartist  ──credited on──►  Collection  ──contains──►  Artwork  ──has one──►  Subject
   └──────────────── also applied to the artwork ─────────┘
```

| Thing | Type | Rule |
| --- | --- | --- |
| **Artwork** | CPT `artwork`, `/artworks/<slug>/` | exactly **one** collection, exactly **one** subject |
| **Collection** | taxonomy `collection`, `/collections/<slug>/` | credited to **one or more** rartists |
| **Rartist** | taxonomy `rartist`, `/rartists/<slug>/` | applied to artworks too, so artist archives are one query |
| **Subject** | taxonomy `subject`, `/subjects/<slug>/` | drives the catalogue filters and the artwork breadcrumb |

### Artist credits have one source of truth

The **collection** carries the credits. Save an artwork with the Rartists field empty and it
inherits the collection's rartists; change the collection's credits and every inheriting
artwork follows. Name rartists on the artwork itself — a collaborative collection, a one-off
plate — and that artwork stops inheriting. The `_rartist_artists_inherited` post meta flag
records which mode an artwork is in; the list table marks inherited credits with `↩`.

See `inc/content-model.php`.

## Authoring flow

1. **Rartists → Add new** — the profile page exists immediately, works section empty.
2. **Collections → Add new** — pick its rartists; the artist block fills itself.
3. **Artworks → Add new** — pick the collection; credits, breadcrumbs, related works follow.

Shortcuts built for step 3: every collection row has an **Add artwork** action that opens a
new artwork with that collection preselected, and a **Works** count linking to its filtered
list. **Position** (the native Order box) sets where a plate lands in the grid rhythm.

### Defining new collections, rartists and subjects

Three ways, all of them safe:

1. **Artworks → Collections / Rartists / Subjects** — the full form, every field at once.
2. **Inline from the artwork screen** — the Collection, Rartists and Subject fields each
   offer *+ Add*, so you never have to leave a half-entered artwork. The term is created
   with a name only; fill in its hero, intro and credits afterwards.
3. **Parent categories** — see below.

A collection with nothing but a name renders a valid page: the title, the works grid and
the derived count appear; the hero, intro, collector note and artist block simply are not
there. Without a hero image its row in the index takes the full width rather than leaving
a hole. Nothing to clean up if you create one and fill it in later.

**Where a new collection shows up.** `/collections/` lists **every** collection, featured
first and then by number — a collection created a minute ago is reachable immediately. The
catalogue's index section shows only the ones with **Feature on the catalogue** switched on,
which is how the design promotes two. That flag decides what the catalogue *promotes*,
never whether a collection is *reachable*.

### Parent categories — grouping collections

The **Parent collection** dropdown can only offer collections that already exist, so a
parent category *is* a collection you create first:

1. **Artworks → Collections**, add one — `Paintings`, say — leaving **Parent collection**
   as *None*. Give it a number and an intro; it needs no hero and no plates of its own.
2. Edit the collections that belong in it and set that as their parent. Or use the
   **Add collection inside** row action on the parent, which opens the form with the
   parent already selected.

A parent's page then earns its keep rather than sitting empty:

- an **In this group** section listing its children, in the catalogue's alternating
  feature layout
- a works grid holding **every** artwork beneath it, because a `tax_query` on a
  hierarchical taxonomy includes children by default
- a roll-up count in its stats block and in the admin **Works** column, so a parent that
  holds no plates directly still reports `06 works` rather than `00`
- children get a three-level breadcrumb: `COLLECTIONS / PAINTINGS / DOMESTIC COLOUR`

Term screens are relabelled throughout — WordPress's defaults call every one of these a
"Category", offer Jazz and Bebop as the example hierarchy, and describe a Description
field this theme never renders. The Description row and column are hidden for all three
taxonomies, and the Parent row is hidden for rartists, where a hierarchy means nothing.

### Typed once, derived everywhere

`PLATE 014` · `EDITION OF 50` · `50 + 5 ARTIST PROOFS` · `QUIET TABLE, 2026 / SHOWN AT
140 × 170 CM` · `SHOWN / 140 × 170 CM` · `COLLECTION 01 / 12 WORKS` · every nav count.
All composed in `inc/format.php` and the view layer from the plate number, title, year,
edition size and the largest filled size. Never fields.

### Optional by design

Story, in-situ, collector note, pull quote, studio image: leave them empty and the section
and its rule line disappear. A half-filled record still renders as a composed page.

## How the code is organised

```
functions.php     constants + requires, nothing else
inc/
  format.php      raw values → display strings. The only place formatting happens
  setup.php       supports, image sizes, menu locations
  fields.php      ACF wiring; rartist_field() is the only field reader
  content-model.php  CPT, taxonomies, artist inheritance, counts
  admin.php       list columns, filters, row actions, publish checklist
  view/           post/term → display-ready array (step 2)
acf-json/         field groups as Local JSON — commit the diffs
parts/            ui/ · section/ · shell/ markup components (step 2+)
assets/css/       components/<name>.css pairs 1:1 with parts/ui/<name>.php
```

Three rules:

1. **Templates never touch fields.** They call a view function and hand the result to parts.
   No `get_field`, no formatting, no `if ( $field )` branching in a template.
2. **CSS is organised by component, not by page.** `templates/` holds only genuinely
   page-specific layout (the catalogue rhythm, the artwork two-column).
3. **One reader.** `rartist_field()` / `rartist_term_field()` wrap ACF so a deactivated
   plugin means blank sections, never a fatal.

### The logo

The stacked `rar.tist` lockup is the brand identity on **every** page — the frames' 
"RARTIST STUDIO" text wordmark was a stand-in for it. It lives in `parts/ui/logo.php`,
inlined rather than loaded as an `<img>` so it can take `fill: currentColor`: ink on the
paper top bar, white over the prelaunch hero. Never give it its own colour.

Sized by height, width following the `40.68 : 21.95` viewBox — 30px in the top bar
(`components/logo.css`), 42px over the prelaunch hero, 26px below 900px.
`assets/img/logo.svg` keeps the original `#212020` fill and is the favicon fallback until
a Site Icon is set.

### Images

Plates are deliberately varied in aspect ratio — the grid is built around it. All theme
image sizes constrain width only and never crop. Upload plates around 2000px wide.

## Requirements

- WordPress 6.5+, PHP 8.1+
- **Advanced Custom Fields (free)** — field groups load from `acf-json/`, nothing to
  configure. Without it the site renders, but nothing can be edited.

## Local development

LocalWP site **rartist** (PHP 8.2.29), theme symlinked into `wp-content/themes/rartist`:

```bash
export PHPRC="$HOME/Library/Application Support/Local/run/<site-id>/conf/php"
cd ~/Local\ Sites/rartist/app/public
wp plugin install advanced-custom-fields --activate
wp rewrite structure '/%postname%/' && wp rewrite flush
```

## Demo content

`seed-demo.php` reproduces the Pencil document's content verbatim — every string is the
frames' copy and every image is the exact file that node's fill points at. Re-runnable
and **authoritative**: it blanks any managed field it does not set (the field list is
read from `acf-json/`, so it cannot drift), which means removing a value here removes it
from the database too.

Three places the design and the model necessarily differ, all noted in the file's header:
the frames claim 12 and 8 works per collection while drawing six plates in total; Quiet
Table appears in both collections as placeholder reuse, so each plate is assigned to the
collection whose artist made it; and only Quiet Table has story, in-situ and three sizes
drawn, so the other five leave those empty.

## Verifying against the design

`scripts/measure-design.js` renders a page at 1440 and compares the resolved geometry
against the numbers measured off the Pencil frames — column tracks, gaps, type sizes,
component heights — and fails on any drift. Add a `PAGES` entry per template.

```bash
CHROMIUM=$(find ~/Library/Caches/ms-playwright -name Chromium -type f | head -1) \
  /usr/local/opt/node@20/bin/node scripts/measure-design.js artwork
```

Gaps are read from resolved grid tracks, not child boxes: display headings carry a
deliberate `-0.06em` margin for Playfair's optical left bearing, which is why the
frames place display text at x=43 and meta text at x=48.

`scripts/fetch-fonts.py` re-downloads the three fonts as woff2 and regenerates
`assets/css/05-fonts.css`. Run it only when a weight or subset changes.

## Templates

| URL | Template | Frame |
| --- | --- | --- |
| `/` | `front-page.php` — the prelaunch page | `uOIiu` |
| `/artworks/` | `archive-artwork.php` | `iaMv8` |
| `/artworks/<slug>/` | `single-artwork.php` | `kTNBO` |
| `/collections/<slug>/` | `taxonomy-collection.php` | `kWI5J` |
| `/rartists/<slug>/` | `taxonomy-rartist.php` | `RmliI` |
| `/subjects/<slug>/` | `taxonomy-subject.php` → the catalogue, filtered | `iaMv8` |
| `/collections/` | `page-collections.php` → reuses the catalogue's index section | `iaMv8` |
| `/rartists/` | `page-rartists.php` | — *not designed* |
| `/about/`, `/shipping/` | `page.php` | — |

Two frames per content type exist and they differ (`JDCJ8` vs `kWI5J`, `bQoBn` vs
`RmliI`). One template cannot be both, so the second of each pair is canonical and every
collection and rartist gets that treatment.

### Deliberate deviations from the frames

1. **The catalogue headline.** The frame sets `ARTWORKS` at 202px in a 510-wide box,
   which stacks it `ART / WOR / KS`, and then covers the third line with the filter bar
   and the first plates. The stacking is reproduced; the covering is not, so all eight
   letters are visible. Reproducing the overlap is a negative margin on `.catalogue__filters`.
2. **Counts are real.** The frames say `12 WORKS`, `08 WORKS`, `06 / 24`; the pages show
   what is actually published.
3. **Ordering** is `menu_order` (the Position field) everywhere, which is what makes the
   catalogue's rhythm a curatorial decision rather than a side effect of publish dates.

## Status

- [x] Step 1 — content model, ACF field groups, admin authoring experience
- [x] Step 2 — tokens, `theme.json`, self-hosted fonts, view layer, shell + nav panel
- [x] Step 3 — all four templates, plus the index and editorial pages
- [x] Step 4 — `seed-demo.php` and the 1440px measurement harness

Every template passes its geometry checks against its frame (`artwork` 27, `catalogue`
15, `collection` 11, `rartist` 12) with no horizontal overflow from 1440 down to 390.
The site works without JavaScript except for the nav panel: the size selector is native
radios, the purchase panels native `details`.

### The prelaunch homepage

`front-page.php` is the live rartist.ro landing page, ported from the static site.
`assets/css/templates/prelaunch.css` is that site's stylesheet verbatim — its own tokens
and `body` rules included, because it is enqueued on exactly one URL where the prelaunch
is the entire document. The only edits are the hero image path and a closing block that
applies the capitals the static markup carried as literal uppercase.

The main nav is **on** the prelaunch page, as the `prelaunch` variant of the top bar: the
design's hero bar (inlined logo left, `LAUNCHING SPRING 2027` right) with the nav in the
middle, in white over the photograph. It is rendered from `header.php` and positioned
over the hero from *outside* it, because `.hero` clips its overflow and would swallow the
dropdown panel. The panel keeps its paper background and ink text, so it reads as a sheet
pulled down over the photograph.

`rartist_is_prelaunch()` still stands the site *footer* down — the prelaunch has its own.
`prelaunch.js` (scroll reveal, invite form) loads alongside `main.js`. The invite form is
client-side only, exactly as on the live site.

The display lines are sized in `vw`, so `RARTIST` is 406px at 1440 and keeps its bleed
past both edges at every width. **That overflow is the design — do not "fix" it.**

When the catalogue opens, delete `front-page.php` and point Settings → Reading wherever
the launch calls for.

### Still open

- **`/rartists/`** has no design.
- The prelaunch footer's email now comes from `rartist_studio()` (`hello@rartist.ro`), so
  the whole site agrees on one address. The live static page says `hello@rartist.studio` —
  worth confirming which is correct.
- The collection page's artist block and the rartist page's featured collection assume a
  single relation; multi-artist and multi-collection variants exist in code but are
  undesigned.

### Known design gaps

The collection page's artist block and the rartist page's featured-collection block both
assume a single relation. Multi-artist and multi-collection variants need a design decision.

### Worn Editions homepage gallery

`parts/section/worn-editions.php` renders the [shirt gallery design](https://app.pen.dev/s/PptKjI8-3HERwtTpGRSf-s69O2mizROCwCaAKx3SVJA), frame `HO2ev`, below the prelaunch hero. The view function `rartist_worn_editions()` in `inc/view/globals.php` supplies five concept images: two from the design and three Adobe Firefly studio scenes. Its `rartist_worn_editions` filter accepts a list of `{title, details, image, alt}` arrays (with optional `width` and `height`) for further editions; the caption, counter and progress indicators follow the actual list size. An empty list omits the section; one slide hides navigation.

The module's CSS and vanilla JS load only on the prelaunch homepage. Typography uses the existing site font tokens and self-hosted Playfair Display, Geist and Geist Mono fonts; the module adds no font assets or font stylesheet. It supports arrows, looping, keyboard navigation, native touch scrolling and reduced motion, with a scrollable image-and-caption fallback without JavaScript. The collaboration link uses the studio enquiry email. Styles and JS also exist in the static site; keep both copies in sync.
