# AGENTS.md

How coding agents should work on the Ipsum theme. This file covers what is specific to the theme and easy to get wrong. [DESIGN.md](DESIGN.md) says how to design with it.

## Key directories

Templates, patterns, `theme.json` and CSS are served as they are in the tree. The only generated file is `style.min.css`.

-   `templates/*.html` - Block templates. Each is a one-line delegate to a pattern.
-   `parts/*.html` - Template parts. Each is a one-line delegate to a pattern.
-   `patterns/*.php` - The markup of every template and part, plus the insertable patterns.
-   `styles/colors/*.json` - Color style variations, number-prefixed to control their order.
-   `styles/typography/*.json` - Typography style variations.
-   `styles/blocks/*.json` - Block style variations. The `slug` is used in markup as `is-style-<slug>`.
-   `assets/fonts/` - Self-hosted fonts, one directory per family, credited under `== Fonts ==` in `readme.txt`.
-   `assets/css/editor-style.css` - Editor-only overrides, loaded after `style.css` in the editors.
-   `functions.php` - Theme PHP: stylesheets, the comments CTA block binding, sidebar template types, block styles. It loads `inc/abilities.php`.
-   `inc/abilities.php` - The `ipsum/get-design-guide` ability, which gives agents on a live site the palette roles, presets, style variations, block styles, patterns and templates through the Abilities API. A new palette color needs its role in `ipsum_get_color_roles()`.
-   `theme.json` - Global settings and styles, fonts, `customTemplates`, per-block styles.
-   `style.css` - The theme header plus the CSS that `theme.json` cannot express. It loads in the editors through `add_editor_style()`, and is inlined on the front end when `SCRIPT_DEBUG` is on.
-   `style.min.css` - `style.css` minified by `npm run build` in the theme directory, inlined on the front end otherwise. Generated: rebuild it, never edit it.
-   `readme.txt` - The WordPress.org readme: description, changelog, copyright, font credits.

## Progressive discovery

Read only what your task needs, when it needs it:

-   **Styling, or building pages, patterns or templates**: read [DESIGN.md](DESIGN.md) first. It covers the color roles, the type and spacing scales, the style variations, and which pattern fills each template and part.
-   **Adding or changing a pattern**: read [Writing patterns](DESIGN.md#writing-patterns) in DESIGN.md first. It covers categories, hiding from the inserter, which i18n function to use, images, and removing `id`, `queryId`, `theme`, `area`, `patternName` and `metadata.categories` from copied markup.

## Markdown files

Ipsum's Markdown files are soft-wrapped: a paragraph, list item or table row is one line, however long. Do not hard-wrap prose, and do not re-flow lines a change does not otherwise touch.

## Architectural decisions

-   **The pattern is the source of truth.** `templates/index.html` is `<!-- wp:pattern {"slug":"ipsum/hidden-index"} /-->` and nothing else. Editing a delegate template or part changes nothing at render time; edit the pattern it names. A pattern that only fills a template or part is hidden from the inserter with `Inserter: no`, and its file and slug take the `hidden-` prefix (`patterns/hidden-index.php` is `ipsum/hidden-index`); template patterns also carry `Template Types:`. Insertable patterns use `Categories`, `Block Types`, `Viewport width` and `Description`.
-   **Style with the design tokens, not literals.** Colors come from `var(--wp--preset--color--<slug>)`, spacing from `var:preset|spacing|N`. The palette uses the Twenty Twenty-Five slugs, so default theme patterns pick up Ipsum's colors: `base` (Base) is the background, `contrast` (Contrast) the text, headings, links and buttons, `accent-1` (Accent) the accent, which only the arrival bar uses, `accent-3` (Strong Contrast) the hover color and the ground of inverted sections and cards, `accent-5` (Surface) the faint wash behind quotes, tags and cards, and `accent-6` (Border) the faint lines. `accent-2` and `accent-4` are not defined. Every color variation redefines all six, so a literal hex freezes one variation's look into all the others. `settings.custom.controlScheme` says whether the active palette is `light` or `dark`.
-   **`theme.json` comes first.** Prefer `theme.json` and Global Styles over custom CSS, and add CSS only when the block editor or theme APIs cannot express the design yet. Don't add PHP or JavaScript workarounds for what the block editor might reasonably provide.
-   **CSS explains why, in prose.** Rules in `style.css` and `assets/css/editor-style.css` carry a comment saying why they exist, often with the Gutenberg or Trac issue behind it. `css` strings in `theme.json` cannot hold comments.
-   **Text domain is `ipsum`.** Every user-visible string goes through an i18n function, including single characters such as the `·` separators.
-   **PHP follows the WordPress coding standards**, in tabs. Functions in `functions.php` and `inc/` are wrapped in `if ( ! function_exists( '…' ) ) : … endif;`, every docblock carries `@since Ipsum 1.0`, and `@package Ipsum` heads the file.

## Do not reformat block markup

The delimiters in templates, parts and patterns are Gutenberg's serialization, not hand-written HTML: flush left, one delimiter per line, a blank line between sibling blocks, and a block's wrapper element on the same line as the opening delimiter of its first child:

```html
<!-- wp:group {"metadata":{"name":"Body"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:post-title /-->
```

Reindenting or rewrapping it produces a large diff that the next editor round-trip undoes.
