# DESIGN.md

How to design with Ipsum: what each preset is for, how the theme uses it, and what the style variations, templates, parts and block styles give you. `AGENTS.md` covers working in the repository.

## Ipsum

Ipsum is the default theme for WordPress 7.2: a block theme built around blogging, which its readme calls a blank canvas. Each palette is a background and a text ink in a few strengths, plus an accent the theme uses once.

## Color

Six palette colors. Every color variation redefines all six, and the duotone too. The values are in `theme.json` and the files in `styles/colors/`.

| Slug | Name | Role | Where the theme uses it |
| --- | --- | --- | --- |
| `base` | Base | Background | Page background, button text, form field fill, text and links on Strong Contrast grounds, the end of both gradients |
| `contrast` | Contrast | Text | Body text, headings, links, button fill and border, form field text and border |
| `accent-3` | Strong Contrast | Strongest ink | Hover for links, buttons, form fields and tag pills; the ground of inverted sections, cards and the navigation overlay |
| `accent-1` | Accent | Accent | One place only: the arrival bar, a 3px line that sweeps the top of the screen when a single post loads (only when the visitor hasn't asked for reduced motion) |
| `accent-5` | Surface | Faint fill | Quotes, pullquotes, comment bubbles, tag pills, table headers and stripes, the calendar header, the audio player, cards in patterns, the start of both gradients, and a few rules (Header Sticky, Text Alternating) |
| `accent-6` | Border | Faint line | Separators, list markers, quote marks, table and calendar lines, the code block border, dotted rules between rows |

`accent-2` and `accent-4` are not defined. The slugs match Twenty Twenty-Five's, so patterns written for it find matching colors, but a block that asks for `accent-2` or `accent-4` gets no color.

How the palettes are built:

-   **Light palettes:** Contrast, Border and Surface are the dark ink at 75%, 20% and 3% opacity (hex alpha `bf`, `33`, `08`). Strong Contrast is the ink itself. The default builds Border and Surface on `#0a0a0a` instead of its `#000000` ink.
-   **Dark palettes:** Contrast is opaque. Border and Surface are Contrast at 25% and 10% (`40`, `1a`). Strong Contrast is lighter than Contrast.
-   Border and Surface are translucent, so they tint whatever is under them.

### Usage rules

-   **Use the slugs, never literal colors.** `var(--wp--preset--color--contrast)` in CSS, `var:preset|color|contrast` in block attributes, or `textColor` and `backgroundColor`. A literal hex freezes one variation's look into all the others.
-   **Text is Contrast.** Body, headings and links all use it. Hover goes to Strong Contrast. Contrast is translucent in the light palettes, so it only reads as intended on Base or Surface; on any other ground, use Strong Contrast.
-   **Links** have a dotted 1px underline. On hover they turn Strong Contrast and lose the underline. Navigation, post title, post navigation and pagination links do the reverse: no underline until hover.
-   **Buttons** fill with Contrast, with Base text, and fill with Strong Contrast on hover. Focus and press use Contrast at 85% (`color-mix()`). The Outline style keeps a Contrast border and fills with Surface on hover.
-   **Form fields** in Search and the comment form are Base with Contrast text and border. The border turns Strong Contrast on hover. Jetpack form fields get the same border.
-   **Accent is not a link or button color.** The theme uses it only in the arrival bar.
-   **Inverted sections and cards** use a Strong Contrast ground with Base text and links: the About Row band, the Navigation Overlay, and the cards in Text Statement, Footer Centered, Footer Subscribe, Header Collapsed, Page Bio, Page Blog Home and the gallery text tiles. They are dark in light variations and light in dark ones.
-   **Tinted cards** use a Surface ground, Contrast text and 10px corners: Kind Words, CTA Membership, Hero Colophon, Format Audio, Format Link and the author chip in single posts. Surface works on any ground, so keep Contrast text on it.
-   **Lines** use Border, at 1px, apart from the Surface rules in Header Sticky (1px) and Text Alternating (5px). Dotted rules use Border or the current text color.
-   **Light or dark:** read `settings.custom.controlScheme`. It is `light` in `theme.json`, and each color variation sets its own. The variation titles don't tell you.
-   **Colors that don't follow the palette:** the Evening code block style keeps the Evening palette's literals on purpose, so it reads as a dark slab under every variation. The Soft shadow is black at 8%. Each variation's duotone colors are hex copies of its palette colors.

### Gradients and duotone

-   **Gradients:** two presets, both Surface to Base, top to bottom. `gradient-1`, "Vertical soft wash to base", fades over the whole height. `gradient-2`, "Vertical hard wash to base", is Surface for the top 90%, then Base. No template or pattern uses them.
-   **Duotone:** one preset, `duotone-1`. The default is "Black and white". Each color variation redefines it with two of its own palette colors as hex, dark first, so editing a palette doesn't update its duotone. Header Collapsed, Header Sticky and Hero Colophon apply it to the site logo. Blue Hour and Golden Hour also apply it to every Featured Image.
-   Core's default palette, gradients and duotones are off, so only Ipsum's presets show.

## Typography

| Slug | Family | Role |
| --- | --- | --- |
| `inter` | Inter | The text font: body and, by default, headings |
| `manrope` | Manrope | The meta voice (below), buttons, navigation, site title and tagline; the text font in Golden Hour |
| `jetbrains-mono` | JetBrains Mono | Code and preformatted text; the text font in High Noon |
| `stix-two-text` | STIX Two Text | The text font in Blue Hour, plus its site title and tagline |
| `besley` | Besley | The text font in Sunrise |
| `archivo` | Archivo | The text font in Morning and After Hours |
| `archivo-condensed` | Archivo Condensed | Headings in After Hours |

All seven are variable fonts. Manrope has no italic face.

### The meta voice

Labels, dates, terms, bylines, comment meta, pagination, read more links, cites, form labels and table headers share one treatment: **Manrope, Small, weight 600**. A few use 700 (site title, accordion and details summaries, table headers) or 400 (comment date, comment edit and reply links, comments title). The tagline is 500. The Navigation block defaults to Manrope at Medium, 500; every header but Header Vertical sets it to Small.

It isn't a preset you can pick. New meta text needs Manrope, Small and weight 600 set on the block, and changing the voice means changing every block that carries it. Typography variations leave it alone, since those blocks set Manrope directly. Blue Hour is the one variation that moves part of it: the site title and tagline go to STIX Two Text.

### Size scale

| Slug | Name | Used for |
| --- | --- | --- |
| `small` | Small | The meta voice, h5, h6 |
| `medium` | Medium | Body text, h4, navigation, verse |
| `large` | Large | h3 |
| `x-large` | X-Large | h2 |
| `2-x-large` | 2X-Large | h1, single post titles, quote marks |

Small to Large are fixed; X-Large and 2X-Large are fluid, smaller on narrow screens. The sizes are in `theme.json`. Above 2X-Large, the theme uses the Display block style (`is-style-text-display`) or fit text (Hero Epigraph, About Row, the 404 heading, the Footer Centered wordmark). Core's default sizes are off.

### Conventions

-   **Line height:** body 1.5, paragraphs 1.6. Headings use `calc(1em + 0.5rem)`, so they get tighter as they grow. Display is 1.2; quote marks are 1.
-   **Weight:** text 400, headings 500.
-   **Letter spacing:** h1 and h2 are tightened by -0.02rem, h3 by -0.01rem. The site title and tagline open to 0.01rem. The meta voice sets 0.
-   **Uppercase** is rare: the site title in Header Sticky, Header Vertical and Footer Social, and the Navigation Overlay menu.
-   **Writing mode** is on. Header Vertical runs its site title vertically.

## Spacing and layout

| Slug | Name | Typical use |
| --- | --- | --- |
| `20` | 2X-Small | Gaps inside rows (date · categories, title and tagline), tag pill padding, heading bottom margin |
| `30` | X-Small | Gaps between small items and buttons; padding in most cards and in compact boxes (code, comment bubbles, button top and bottom) |
| `40` | Small | The root block gap and side padding, button side padding |
| `50` | Regular | Section padding (header top, footer), heading top margin |
| `60` | Large | Between sections (separators, comments, tags, author chip), the gap between posts in query loops, header bottom padding |
| `70` | X-Large | Large section padding, the gap beside the sidebar |
| `80` | 2X-Large | Hero and statement patterns: Hero Epigraph, CTA Search, Contact Say Hi, About Row |

`20` and `30` are fixed; from `40` up, sizes scale with the viewport. The sizes are in `theme.json`. Core's default spacing sizes are off.

### Layout

-   **Widths:** content 640px, wide 1280px.
-   **Root padding:** spacing `40` on each side, with root-padding-aware alignments, so full-width blocks reach the screen edges.
-   **Templates** wrap their content in a `main` group with a constrained layout; `archive` and `search` use the default layout. In the one-column templates, post content is full-aligned with a constrained layout, so its blocks sit at 640px and can go wide or full.
-   **Sidebar templates** use a wide Columns block: 66.66% content, 33.33% sidebar, spacing `70` between.

### Shadow

One preset, `soft` ("Soft"), a small black shadow at 8%. Only the Header Sticky bar uses it.

### Corners and lines

| Radius | Where |
| --- | --- |
| 10px | Buttons, featured images, avatars, comment bubbles, tag pills, the audio player, most cards in patterns |
| 5px | Images, quotes and pullquotes, code, form fields, the site logo |
| 4px | Site title, search button |
| 2px | Post author name |
| 0 and 9999px | The Sharp and Rounded site logo styles |

Lines are 1px, except Text Alternating's 5px heading rules. Dotted lines mark link underlines, button focus outlines, the Search Outline button, the top of accordions and details, the rules between rows in Archive Simple, Archive Minimal, Events List and All Archives Sidebar, and the rule beside the link in Format Link. Other buttons, separators, tables and code use solid lines.

## Style variations

### Color variations

Six of the seven also change fonts, and two apply duotone to featured images. Every one redefines the six colors and `duotone-1`, so the duotone on the header site logos follows the variation.

| # | Title | Scheme | Also changes |
| --- | --- | --- | --- |
| | Default (`theme.json`) | light | Nothing |
| 01 | Blue Hour | light | Text font, site title and tagline in STIX Two Text. Duotone "Ink on paper" on featured images. |
| 02 | Sunrise | light | Text font in Besley. |
| 03 | Morning | light | Text font in Archivo. |
| 04 | High Noon | light | Text font in JetBrains Mono. |
| 05 | Golden Hour | dark | Text font and headings in Manrope. Duotone "Peach on maroon" on featured images. |
| 06 | Evening | dark | Colors only. |
| 07 | After Hours | dark | Text font in Archivo, headings in Archivo Condensed. |

Scheme is `settings.custom.controlScheme`. Each variation's colors are in its file in `styles/colors/`.

### Typography variations

Each sets only the root font family, which headings inherit. Manrope blocks and code keep their own families.

| Title | Slug | Text font |
| --- | --- | --- |
| Archivo | `archivo` | Archivo |
| Archivo Condensed | `archivo-condensed` | Archivo Condensed |
| Besley | `besley` | Besley |
| JetBrains Mono | `jetbrains-mono` | JetBrains Mono |
| STIX Two Text | `stix-two-text` | STIX Two Text |

### Block style variations

The class is `is-style-` plus the slug.

| Style | Class | Blocks | What it does |
| --- | --- | --- | --- |
| Crop Contents | `is-style-crop-contents` | Group | `overflow: clip` |
| Display | `is-style-text-display` | Heading, Paragraph, Post Title, Site Title, Query Title | A size above 2X-Large that scales with the viewport, line height 1.2 |
| Quote Mark | `is-style-text-quote-mark` | Heading, Paragraph | A ❝ above the text, in Border, at 2X-Large |
| Quote Mark on Color | `is-style-text-quote-mark-on-color` | Heading, Paragraph | The same mark in the current text color; Text Statement uses it on a Strong Contrast card |
| Evening | `is-style-evening` | Code | A black ground with near-white text and border, the same under every variation |
| Sharp | `is-style-sharp` | Site Logo | Square corners |
| Pinned to Bottom | `is-style-sticky-bottom` | Template Part | Pins the part to the bottom of the screen; see [Utility block styles](#utility-block-styles) |
| Row Hover | `is-style-hover-together` | Post Template | Post rows hover as one |
| Dot Separator | `is-style-dot-before` | Comment Edit Link, Comment Reply Link | A decorative `·` before the link |
| Outline | `is-style-outline` | Search | A dotted Contrast outline on a Base button until hover, focus or press; the Search Bar part uses it |

Ipsum also restyles core's own block styles: Button Outline (a separate style from Search's Outline, though they share the name), Quote Plain (no ground, padding or corners), Separator Wide (capped at the wide width), Site Logo Rounded (9999px) and Social Icons Logos Only (icons in the current text color).

## Templates and parts

### Templates

| Template | Pattern | Shows |
| --- | --- | --- |
| `index` | `ipsum/hidden-index` | The post feed, each post in full |
| `archive` | `ipsum/hidden-archive` | The archive title over the Archive Compact list |
| `search` | `ipsum/hidden-search` | The search title and field, then results with excerpts |
| `single` | `ipsum/hidden-single` | The post, tags, author chip, comments and Keep Reading |
| `page` | `ipsum/hidden-page` | Featured image, title and content |
| `404` | `ipsum/hidden-404` | A fit-text heading, a line of text and search |
| `page-no-title` | `ipsum/hidden-page-no-title` | Content only |
| `page-sidebar` | `ipsum/hidden-page-sidebar` | The page beside the Sidebar part |
| `single-sidebar` | `ipsum/hidden-single-sidebar` | The post beside the Sidebar part |
| `index-sidebar` | `ipsum/hidden-index-sidebar` | The feed beside the Sidebar part |
| `archive-sidebar` | `ipsum/hidden-archive-sidebar` | The archive beside the Sidebar part |

-   Every template starts with the Header part.
-   One-column templates use the Footer part; sidebar templates use Footer Wide.
-   All but `404` and `search` add the Search Bar part with the Pinned to Bottom block style, which pins it to the bottom of the screen.
-   Both single templates insert two hidden helper patterns: `ipsum/hidden-comments` and `ipsum/hidden-keep-reading` (the three latest posts).

### Custom templates

-   **In `theme.json` `customTemplates`:** Single Posts Sidebar (`single-sidebar`, posts), Pages Sidebar (`page-sidebar`, pages) and Pages No Title (`page-no-title`, pages). Editors pick them per post or page.
-   **Template types:** Index Sidebar (`index-sidebar`) and All Archives Sidebar (`archive-sidebar`) have a title and description in the Site Editor. Their slugs aren't in the template hierarchy, so WordPress doesn't choose them on its own.

### Template parts

| Part | Area | Pattern | Used in |
| --- | --- | --- | --- |
| `header` | `header` | `ipsum/header-default` | Every template |
| `header-collapsed` | `header` | `ipsum/header-collapsed` | None; an alternative with the menu always behind a button |
| `header-sticky` | `header` | `ipsum/header-sticky` | None; an alternative sticky bar with the Soft shadow |
| `footer` | `footer` | `ipsum/footer` | One-column templates |
| `footer-wide` | `footer` | `ipsum/footer-wide` | Sidebar templates |
| `navigation-overlay` | `navigation-overlay` | `ipsum/navigation-overlay` | The menu button in Header Default, Header Collapsed, Header Sticky, Hero Colophon and Hero Epigraph |
| `search-bar` | `uncategorized` | `ipsum/hidden-search-bar` | All templates but `404` and `search` |
| `sidebar` | `uncategorized` | `ipsum/hidden-sidebar` | The four sidebar templates: categories and the five latest posts |

The header, footer and overlay parts delegate to insertable patterns. Other patterns carry `Block Types: core/template-part/<area>`, so the editor offers them as replacements:

-   **Header:** Header Default, Header Collapsed, Header Sticky, Header Vertical, Hero Colophon, Hero Epigraph.
-   **Footer:** Footer, Footer Wide, Footer Centered, Footer Social, Footer Subscribe.
-   **Navigation overlay:** Navigation Overlay, Overlay Contents, Overlay Profile.

## Utility block styles

Three block styles carry behaviour, not just looks, and need a particular setup to work. Like every block style, they're listed in the block's Styles panel and in REST at `/wp/v2/block-types/<block>` under `styles`. A block holds one style at a time, and choosing a style stores `is-style-<slug>` in its `className`.

| Style | Block | What it does | Used in |
| --- | --- | --- | --- |
| Pinned to Bottom (`sticky-bottom`) | Template Part | Pins the part to the bottom of the viewport, shrunk to its content at the inline end (bottom right in LTR, bottom left in RTL), above the content (`z-index: 10`). | The Search Bar part at the end of every template but Search and 404. |
| Row Hover (`hover-together`) | Post Template | Post rows hover as one: hovering any visible part of a row turns all its text and links Strong Contrast and gives its featured image a small tilt and settle (skipped with reduced motion). The empty space beside the content doesn't trigger it. | Archive Simple, Archive Standard, Archive Compact, Archive Lead, Search Results, All Archives Sidebar. |
| Dot Separator (`dot-before`) | Comment Edit Link, Comment Reply Link | Puts a decorative `·` before the link, with 0.5em of space on each side and weight 600. Screen readers skip it, and the dot comes and goes with its link. | The date, Edit and Reply row of each comment in `ipsum/hidden-comments`. |

What each one needs to work:

-   **Pinned to Bottom:** put the template part at the root of the template, after the footer. A sticky element only travels within its parent; at the root that's the site wrapper, which the theme stretches to at least the viewport height. Inside a Group it only sticks while that Group is on screen.
-   **Row Hover:** wrap each post's blocks in a single Group directly inside the Post Template, as every Ipsum pattern that uses it does. Without that Group the style does nothing.
-   **Dot Separator:** put the link in a flex row with a block gap of 0. The dot's padding does the spacing, so a gap would double it.

### Helper classes

Two classes in `style.css` hide a group when a block inside it renders nothing, so the hand-written text around it doesn't stand alone. Keep the class on the group when you copy it into a new pattern.

-   **`comments-meta`** goes on the group around the comments link row in post lists. Core renders nothing for the Comments Link block when comments are closed, so the row, with its `·` and call to action, hides with it.
-   **`author-card`** goes on the author chip in single posts. Core renders nothing for the Author Name block when the post type doesn't support authors, so the card, with its "Written by" byline and avatar, hides with it.

### The `·` meta separator

The separator between meta items is built three ways, on purpose:

-   **Where both neighbours always render** (post date · categories), it's a Paragraph named Meta Separator: small, weight 600, with the dot hidden from screen readers. The row's block gap (`spacing|20`) spaces it. To add one, copy it from an archive pattern.
-   **Where the whole row can go** (comments link · call to action), it's the same Paragraph, and the `comments-meta` class hides the row when comments are closed.
-   **Where a neighbour may not render**, it's the Dot Separator style. The Edit link only renders for people who can edit the comment, and the Reply link only when threading allows it, so a Paragraph would be left behind as a stray dot. Use the Paragraph wherever it's safe: unlike the style's dot, it can be translated.
