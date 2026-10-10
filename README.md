# Rartist Studio — Prelaunch Page

Static prelaunch landing page for **Rartist Studio**, a curated print-editions house.
Plain HTML + CSS + vanilla JS. No build step, no dependencies.

## Structure

```
rartist/
├── index.html          # the page
├── css/styles.css      # tokens, sections, responsive rules
├── js/main.js          # scroll reveal + invite form
└── assets/
    ├── logo.svg        # brand lockup — favicon + source of the inline hero mark
    ├── SVG/            # original supplied export
    └── *.jpg|png       # hero and plate images
```

## Logo

The lockup is **inlined** into `index.html` (hero top bar) rather than loaded via
`<img>`, so it can take `fill: currentColor` — white on the dark hero, ink anywhere
else. `assets/logo.svg` keeps the original `#212020` fill and is used as the favicon.
It is a two-line stacked mark, so it is sized by **height** (`42px`, `32px` on mobile)
and lets width follow the `40.68 : 21.95` viewBox.

## Run

Open `index.html` directly, or serve it:

```bash
python3 -m http.server 8080
# → http://localhost:8080
```

## Design source

Built from the Pencil design `Prelaunch Page`
(`~/.pencil/documents/abbf77f7-4512-4feb-aa6f-4fb1b33dcd40/pencil-new.pen`,
shared read-only at `app.pen.dev/s/H91aHlOLnTCzw7FssBYoXDiqgSPOH4BX5pKcGejlb3Q`).

### Tokens

| Token         | Value     | Use                          |
| ------------- | --------- | ---------------------------- |
| `--paper`     | `#FCFAF7` | page background, footer      |
| `--ink`       | `#000000` | primary text                 |
| `--muted`     | `#666666` | secondary text, meta labels  |
| `--night`     | `#0A0A0A` | signup band                  |

Type: **Playfair Display** for the oversized display lines, **Geist** for body,
**Geist Mono** for labels, eyebrows and captions — all from Google Fonts.

### Display type

The four display lines (`RARTIST`, `COLLECT SLOWLY`, `THE FIRST THIRTY`, `BE FIRST`)
are sized in `vw`, not `px`, so they keep the design's deliberate bleed past both
edges at every viewport width. The `px` values from the design map as
`size / 1440 * 100vw`; letter-spacing is expressed in `em` for the same reason.

## Not yet wired

The invite form validates and confirms client-side only — no endpoint. Point
`js/main.js` at a real list provider when there is one.

## Worn Editions gallery

The “Art, worn out.” module follows the [shirt gallery design](https://app.pen.dev/s/PptKjI8-3HERwtTpGRSf-s69O2mizROCwCaAKx3SVJA), frame `HO2ev`. It sits between the Worn Editions introduction and upcoming batches. Its scoped styles and dependency-free script live in `css/worn-editions.css` and `js/worn-editions.js`. Typography reuses the site’s font tokens: Playfair Display for the heading, Geist for body and edition text, and Geist Mono for labels and captions.

The gallery contains five concept slides: the two original design images and three Adobe Firefly scenes (gallery wall, printmaker’s table and studio display), generated using the original shirt as a reference. Arrows loop; progress buttons, keyboard arrows, Home/End and native touch scrolling update the caption and counter. Reduced motion is respected. Without JavaScript, images remain horizontally scrollable with individual captions.

To add a static slide, add a `.worn-gallery__slide` figure with `data-title` and `data-details`, plus its matching progress button. The WordPress copy uses the same CSS and JS; keep both copies in sync when changing the module.
