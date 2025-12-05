# Introductions

Is for showing introductions to a user, on Yoast admin pages.
Based on plugin version, page, user capabilities and whether the user has seen it already.

- `Introduction_Interface` defines what data is needed
    - `id` as unique identifier
    - `plugin` and `version` to determine if the introduction is new (version > plugin version)
    - `pages` to be able to only show on certain Yoast admin pages
    - `capabilities` to be able to only show for certain users
- `Introductions_Collector` uses that data to determine whether an introduction should be "shown" to a user
    - uses the `wpseo_introductions` filter to be extendable from our other plugins
    - uses `Introductions_Seen_Repository` to get the data to determine if the user saw an introduction already
- `Introductions_Seen_Repository` is the doorway whether a user has seen an introduction or not
    - uses the `_yoast_introductions` user metadata
- `Introduction_Bucket` and `Introduction_Item` are used by the collector to get an array
- `Introductions_Integration` runs on the Yoast Admin pages and loads the assets
    - only loads on our Yoast admin pages, but never on our installation success pages as to not disturb onboarding
    - only loads assets if there is an introduction to show
        - `js/src/introductions` holds the JS
        - `wpseoIntroductions` is the localized script to transfer data from PHP to JS
        - `css/src/ai-generator.css` holds the CSS

Inside JS, register the modal content via `window.YoastSEO._registerIntroductionComponent`, which takes a
`id` and a `Component`. The id needs to be the same as the id in the `Introduction_Interface`.
The action `yoast.introductions.ready` can be used to know whether the registration function is available and ready for
use.
