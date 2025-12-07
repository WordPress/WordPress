=== Performance Lab ===

Contributors: wordpressdotorg
Tested up to: 6.8
Stable tag:   4.0.0
License:      GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Tags:         performance, site health, measurement, optimization, diagnostics

Performance plugin from the WordPress Performance Team, which is a collection of standalone performance features.

== Description ==

The Performance Lab plugin is a collection of features focused on enhancing performance of your site, most of which should eventually be merged into WordPress core. The plugin facilitates the discovery and activation of the individual performance feature plugins which the performance team is developing. In this way you can test the features to get their benefits before they become available in WordPress core. You can also play an important role by providing feedback to further improve the solutions. 

The feature plugins which are currently featured by this plugin are:

* [Embed Optimizer](https://wordpress.org/plugins/embed-optimizer/)
* [Enhanced Responsive Images](https://wordpress.org/plugins/auto-sizes/)
* [Image Placeholders](https://wordpress.org/plugins/dominant-color-images/)
* [Image Prioritizer](https://wordpress.org/plugins/image-prioritizer/)
* [Modern Image Formats](https://wordpress.org/plugins/webp-uploads/)
* [No-cache BFCache](https://wordpress.org/plugins/nocache-bfcache/)
* [Optimization Detective](https://wordpress.org/plugins/optimization-detective/) (dependency for Embed Optimizer and Image Prioritizer)
* [Performant Translations](https://wordpress.org/plugins/performant-translations/)
* [Speculative Loading](https://wordpress.org/plugins/speculation-rules/)
* [View Transitions](https://wordpress.org/plugins/view-transitions/) _(experimental)_
* [Web Worker Offloading](https://wordpress.org/plugins/web-worker-offloading/) _(experimental)_

These plugins can also be installed separately from installing Performance Lab, but having the Performance Lab plugin also active will ensure you find out about new performance features as they are developed.

== Installation ==

= Installation from within WordPress =

1. Visit **Plugins > Add New**.
2. Search for **Performance Lab**.
3. Install and activate the Performance Lab plugin.

= Manual installation =

1. Upload the entire `performance-lab` folder to the `/wp-content/plugins/` directory.
2. Visit **Plugins**.
3. Activate the Performance Lab plugin.

= After activation =

1. Visit the new **Settings > Performance** menu.
2. Enable the individual features you would like to use.

== Frequently Asked Questions ==

= What is the purpose of this plugin? =

The primary purpose of the Performance Lab plugin is to allow testing of various performance features for which the goal is to eventually land in WordPress core. It is essentially a collection of "feature plugins", which makes it different from other performance plugins that offer performance features which are not targeted at WordPress core and potentially rely on functionality that would not be feasible to use in WordPress core. The list of available features will regularly change: Existing features may be removed after they have been released in WordPress core, while new features may be added in any release.

= Can I use this plugin on my production site? =

Per the primary purpose of the plugin (see above), it can mostly be considered a beta testing plugin for the various performance features it includes. However, it's essential to understand that utilizing it comes with inherent risks. Users are encouraged to proceed with caution and understand that they are doing so at their own risk.

= Where can I submit my plugin feedback? =

Feedback is encouraged and much appreciated, especially since this plugin is a collection of future WordPress core features. If you have suggestions or requests for new features, you can [submit them as an issue in the Performance Lab GitHub repository](https://github.com/WordPress/performance/issues/new/choose). If you need help with troubleshooting or have a question about the plugin, please [create a new topic on our support forum](https://wordpress.org/support/plugin/performance-lab/#new-topic-0).

= Where can I report security bugs? =

The Performance team and WordPress community take security bugs seriously. We appreciate your efforts to responsibly disclose your findings, and will make every effort to acknowledge your contributions.

To report a security issue, please visit the [WordPress HackerOne](https://hackerone.com/wordpress) program.

= How can I contribute to the plugin? =

Contributions are always welcome! Learn more about how to get involved in the [Core Performance Team Handbook](https://make.wordpress.org/performance/handbook/get-involved/).

== Changelog ==

= 4.0.0 =

**Enhancements**

* Add No-cache BFCache to list of performance feature plugins. ([2119](https://github.com/WordPress/performance/pull/2119))
* Add admin pointers to promote new Performance Lab features. ([2122](https://github.com/WordPress/performance/pull/2122))
* Implement opt-in `PERFLAB_PLACE_OBJECT_CACHE_DROPIN` for  Server-Timing `object-cache.php` placement. ([1996](https://github.com/WordPress/performance/pull/1996))
* Use `wp_is_rest_endpoint()` to detect if we are handling a REST API request. ([2094](https://github.com/WordPress/performance/pull/2094))

**Bug Fixes**

* Fix TypeError in `perflab_aao_query_autoloaded_options()` by serializing non-scalar option values. ([1934](https://github.com/WordPress/performance/pull/1934))
* Omit admin pointer for new plugin if plugin is already active. ([2143](https://github.com/WordPress/performance/pull/2143))
* Prevent effective asset cache headers audit from running on local/development environments. ([2035](https://github.com/WordPress/performance/pull/2035))
* Use HTML Tag Processor to audit blocking scripts & styles in Site Health’s enqueued-assets test. ([2059](https://github.com/WordPress/performance/pull/2059))

= 3.9.0 =

**Enhancements**

* Remove experimental flags from Embed Optimizer and Image Prioritizer. ([1846](https://github.com/WordPress/performance/pull/1846))

= 3.8.0 =

**Enhancements**

* Add Site Health test for `Cache-Control: no-store` page response header which disables bfcache. ([1807](https://github.com/WordPress/performance/pull/1807))
* Add Site Health test to verify that static assets are served with far-future expires. ([1727](https://github.com/WordPress/performance/pull/1727))
* Enqueue scripts using `plugins_url()` instead of `plugin_dir_url()`. ([1761](https://github.com/WordPress/performance/pull/1761))

= 3.7.0 =

**Enhancements**

* Add guidance for managing Performance feature plugins. ([1734](https://github.com/WordPress/performance/pull/1734))
* Automatically discover plugin dependencies when obtaining Performance feature plugins from WordPress.org. ([1680](https://github.com/WordPress/performance/pull/1680))
* Disregard transient cache in `perflab_query_plugin_info()` when a plugin is absent. ([1694](https://github.com/WordPress/performance/pull/1694))
* Minify script used for ajax activation of features; warn if absent and serve original file when SCRIPT_DEBUG is enabled. ([1658](https://github.com/WordPress/performance/pull/1658))

**Bug Fixes**

* Fix latest plugin version not being downloaded consistently. ([1693](https://github.com/WordPress/performance/pull/1693))

= 3.6.1 =

**Bug Fixes**

* Fix race condition bug where activating multiple features sequentially could fail to activate some features. ([#1675](https://github.com/WordPress/performance/pull/1675))

= 3.6.0 =

**Enhancements**

* Use AJAX for activating features / plugins in Performance Lab. ([1646](https://github.com/WordPress/performance/pull/1646))
* Introduce AVIF header health check. ([1612](https://github.com/WordPress/performance/pull/1612))
* Install and activate Optimization Detective when the Embed Optimizer feature is activated from the Performance screen. ([1644](https://github.com/WordPress/performance/pull/1644))

**Bug Fixes**

* Fix uses of 'Plugin not found' string. ([1651](https://github.com/WordPress/performance/pull/1651))

= 3.5.1 =

**Bug Fixes**

* Account for plugin dependencies when storing relevant plugin info. ([1613](https://github.com/WordPress/performance/pull/1613))

= 3.5.0 =

**Enhancements**

* Add Web Worker Offloading to list of Performance features. ([1577](https://github.com/WordPress/performance/pull/1577))
* Only store info for relevant standalone plugins in the transient cache. ([1573](https://github.com/WordPress/performance/pull/1573))
* Use a single WordPress.org API request to get information for all plugins. ([1562](https://github.com/WordPress/performance/pull/1562))

= 3.4.1 =

**Bug Fixes**

* Fix Incorrect use of _n(). ([1491](https://github.com/WordPress/performance/pull/1491))

= 3.4.0 =

**Enhancements**

* Remove Server-Timing metric for the autoloaded options query time. ([1456](https://github.com/WordPress/performance/pull/1456))

**Bug Fixes**

* Avoid sending Server-Timing header when buffer is being cleaned. ([1443](https://github.com/WordPress/performance/pull/1443))
* Fix disabled options from reappearing in Site Health after external update. ([1374](https://github.com/WordPress/performance/pull/1374))
* Improve Performance screen when external requests to WordPress.org fail. ([1474](https://github.com/WordPress/performance/pull/1474))

= 3.3.1 =

**Enhancements**

* Add PHPStan strict rules (except for empty.notAllowed). ([1241](https://github.com/WordPress/performance/pull/1241))

**Bug Fixes**

* Allow null to be passed into perflab_admin_pointer(). ([1393](https://github.com/WordPress/performance/pull/1393))

= 3.3.0 =

**Enhancements**

* Bump minimum WP versions and WP version tested up to. ([1333](https://github.com/WordPress/performance/pull/1333))
* Improve message for WebP site health test. ([1249](https://github.com/WordPress/performance/pull/1249))
* Remove 'Requires at least' and 'Requires PHP' from plugin readmes. ([1334](https://github.com/WordPress/performance/pull/1334))
* Upgrade PHPStan to 1.11.6. ([1325](https://github.com/WordPress/performance/pull/1325))

**Bug Fixes**

* Extend core's Autoloaded Options Site Health test if present (in WP 6.6). ([1298](https://github.com/WordPress/performance/pull/1298))
* Fix unit tests for multisite. ([1327](https://github.com/WordPress/performance/pull/1327))

= 3.2.0 =

**Enhancements**

* Add install note after each PerfLab feature plugin in the plugin list table. ([1265](https://github.com/WordPress/performance/pull/1265))
* Update plugins with new banners and icons. ([1272](https://github.com/WordPress/performance/pull/1272))

**Bug Fixes**

* Fix Server-Timing compatibility with other plugins that do output buffering. ([1260](https://github.com/WordPress/performance/pull/1260))
* Harden autoloaded-options site health test for incorrectly implemented external object cache plugins. ([1238](https://github.com/WordPress/performance/pull/1238))

= 3.1.0 =

**Enhancements**

* Add progress indicator when activating a feature. ([1190](https://github.com/WordPress/performance/pull/1190))
* Display plugin settings links in the features screen and fix responsive layout for mobile. ([1208](https://github.com/WordPress/performance/pull/1208))
* Add plugin dependency support for activating performance features. ([1184](https://github.com/WordPress/performance/pull/1184))
* Add support for AVIF image format in site health. ([1177](https://github.com/WordPress/performance/pull/1177))
* Add server timing to REST API response. ([1206](https://github.com/WordPress/performance/pull/1206))
* Bump minimum PHP requirement to 7.2. ([1130](https://github.com/WordPress/performance/pull/1130))
* Refine logic in perflab_install_activate_plugin_callback() to rely only on validated slug. ([1170](https://github.com/WordPress/performance/pull/1170))
* Improve overall code quality with stricter static analysis checks. ([775](https://github.com/WordPress/performance/issues/775))

**Bug Fixes**

* Avoid passing incomplete data to perflab_render_plugin_card() and show error when plugin directory API query fails. ([1175](https://github.com/WordPress/performance/pull/1175))
* Do not show admin pointer on the Performance screen and dismiss the pointer when visited. ([1147](https://github.com/WordPress/performance/pull/1147))
* Fix `WordPress.DB.DirectDatabaseQuery.DirectQuery` warning for Autoloaded Options Health Check. ([1179](https://github.com/WordPress/performance/pull/1179))

= 3.0.0 =

**Enhancements**

* Add caching to the wordpress.org query to get plugin information. ([1022](https://github.com/WordPress/performance/pull/1022))
* Add support for autoloading enhancements in WordPress 6.6 trunk to autoloaded options Site Health check. ([1112](https://github.com/WordPress/performance/pull/1112))
* Bump minimum required WordPress version to 6.4. ([1062](https://github.com/WordPress/performance/pull/1062))
* Enhance `object-cache.php` drop-in placement logic to support updating to a newer version of the file. ([1047](https://github.com/WordPress/performance/pull/1047))
* Implement support for annotating certain plugins as experimental. ([1111](https://github.com/WordPress/performance/pull/1111))
* Migrate Site Health checks from being modules to becoming part of the plugin core. ([1042](https://github.com/WordPress/performance/pull/1042))
* Remove modules infrastructure and UI from the plugin. ([1060](https://github.com/WordPress/performance/pull/1060))
* Support changing autoload value for largest autoloaded options in Site Health check. ([1048](https://github.com/WordPress/performance/pull/1048))
* Use plugin slug for generator tag. ([1103](https://github.com/WordPress/performance/pull/1103))

**Documentation**

* Update tested WordPress version to 6.5. ([1027](https://github.com/WordPress/performance/pull/1027))

= 2.9.0 =

**Features**

* Infrastructure: Add standalone plugin version constants for auto-sizes and speculation-rules. ([958](https://github.com/WordPress/performance/pull/958))

**Enhancements**

* Infrastructure: Include standalone plugin slugs in generator tag. ([949](https://github.com/WordPress/performance/pull/949))

**Bug Fixes**

* Infrastructure: Sanitize metric name for `Server-Timing` header. ([957](https://github.com/WordPress/performance/pull/957))

= 2.8.0 =

**Features**

* Infrastructure: Introduce UI for managing Performance Lab standalone plugins. ([864](https://github.com/WordPress/performance/pull/864))

**Enhancements**

* Infrastructure: Add support for plugin live preview in the plugin directory. ([890](https://github.com/WordPress/performance/pull/890))
* Infrastructure: Allow module `can-load.php` callbacks to return a `WP_Error` with more information. ([891](https://github.com/WordPress/performance/pull/891))
* Infrastructure: Implement admin pointer to indicate to the user they need to migrate modules to their standalone plugins. ([910](https://github.com/WordPress/performance/pull/910))
* Infrastructure: Implement migration logic and UI from Performance Lab modules to their standalone plugins. ([899](https://github.com/WordPress/performance/pull/899))
* Infrastructure: Reset admin pointer dismissal for module migration when the user activates a module. ([915](https://github.com/WordPress/performance/pull/915))

**Bug Fixes**

* Infrastructure: Fix construction of translation strings in admin/plugins.php. ([925](https://github.com/WordPress/performance/pull/925))

= 2.7.0 =

**Enhancements**

* Images: Remove Fetchpriority module as the functionality is now available in WordPress core. ([854](https://github.com/WordPress/performance/pull/854))
* Infrastructure: Bump minimum required PHP version to 7.0 and minimum required WP version to 6.3. ([851](https://github.com/WordPress/performance/pull/851))

**Documentation**

* Infrastructure: Publish Image Placeholders standalone plugin. ([842](https://github.com/WordPress/performance/pull/842))

= 2.6.1 =

**Bug Fixes**

* Infrastructure: Remove PHPStan config file from plugin directory. ([816](https://github.com/WordPress/performance/pull/816))

**Documentation**

* Infrastructure: Add standalone plugin assets. ([815](https://github.com/WordPress/performance/pull/815))

= 2.6.0 =

**Features**

* Infrastructure: Add output buffering checkbox to Server-Timing screen. ([801](https://github.com/WordPress/performance/pull/801))
* Infrastructure: Implement logic to measure specific hook execution time with Server-Timing controlled by a WP Admin screen. ([784](https://github.com/WordPress/performance/pull/784))

**Enhancements**

* Images: Fix incorrect function prefixes in Image Placeholders. ([789](https://github.com/WordPress/performance/pull/789))
* Infrastructure: Add early exit clauses to files with procedural code. ([790](https://github.com/WordPress/performance/pull/790))
* Infrastructure: Allow disabling Server-Timing entirely using `PERFLAB_DISABLE_SERVER_TIMING` constant. ([795](https://github.com/WordPress/performance/pull/795))

**Bug Fixes**

* Images: Fix WebP handling when editing images based on WordPress 6.3 change. ([796](https://github.com/WordPress/performance/pull/796))
* Infrastructure: Fix errors detected by Plugin Checker. ([788](https://github.com/WordPress/performance/pull/788))

= 2.5.0 =

**Enhancements**

* Images: Check for fetchpriority feature being available in WordPress core before loading the module. ([769](https://github.com/WordPress/performance/pull/769))
* Database Optimization: Remove SQLite module. ([764](https://github.com/WordPress/performance/pull/764))
* Infrastructure: Bump tested up to version to 6.3. ([772](https://github.com/WordPress/performance/pull/772))

= 2.4.0 =

**Enhancements**

* Database: Implement migration prompt to migrate from SQLite module to standalone plugin due to removal in the following release. ([739](https://github.com/WordPress/performance/pull/739))
* Infrastructure: Enhance code quality by adding PHPStan and fixing level 0 issues. ([730](https://github.com/WordPress/performance/pull/730))
* Infrastructure: Use static closures for minor performance improvement whenever instance access is not needed. ([729](https://github.com/WordPress/performance/pull/729))

**Bug Fixes**

* Database: Fix SQLite module deactivation routine to make standalone plugin migration work correctly. ([743](https://github.com/WordPress/performance/pull/743))
* Infrastructure: Make `Server-Timing` header output more robust. ([736](https://github.com/WordPress/performance/pull/736))

= 2.3.0 =

**Enhancements**

* Images: Configure `Dominant Color` and `Fetchpriority` modules for their standalone plugins. ([704](https://github.com/WordPress/performance/pull/704))
* Infrastructure: Temporarily remove Image Placeholders from standalone `plugins.json` definition. ([719](https://github.com/WordPress/performance/pull/719))
* Infrastructure: Use dynamic version from `plugins.json` for manual workflow. ([710](https://github.com/WordPress/performance/pull/710))

**Bug Fixes**

* Images: Add dominant color styling before any existing inline style attributes. ([716](https://github.com/WordPress/performance/pull/716))
* Infrastructure: Resolve low-severity security advisory [GHSA-66qq-69rw-6x63](https://github.com/WordPress/performance/security/advisories/GHSA-66qq-69rw-6x63).

= 2.2.0 =

**Enhancements**

* Images: Remove "experimental" flag from Fetchpriority module. ([702](https://github.com/WordPress/performance/pull/702))
* Infrastructure: Implement infrastructure for launching standalone plugins from modules, including Modern Image Formats. ([699](https://github.com/WordPress/performance/pull/699))
* Infrastructure: Include `WordPress-Extra` rules in PHPCS configuration and fix resulting problems. ([695](https://github.com/WordPress/performance/pull/695))

**Bug Fixes**

* Images: Sanitize target param before using it. ([690](https://github.com/WordPress/performance/pull/690))

**Documentation**

* Images: Change module slug/directory from `dominant-color` to `dominant-color-images`. ([708](https://github.com/WordPress/performance/pull/708))
* Images: Rename `Dominant Color` module to `Dominant Color images`. ([705](https://github.com/WordPress/performance/pull/705))

= 2.1.0 =

**Enhancements**

* Infrastructure: Add `wp-total` metric to default Server-Timing metrics. ([669](https://github.com/WordPress/performance/pull/669))
* Infrastructure: Ensure module `load.php` files really only load other code to prevent conflicts in standalone plugins. ([674](https://github.com/WordPress/performance/pull/674))

**Bug Fixes**

* Infrastructure: Fix problems with placing `object-cache.php` drop-in. ([672](https://github.com/WordPress/performance/pull/672))

= 2.0.0 =

**Enhancements**

* Object Cache: Update WordPress version to 6.1 and remove Cache modules. ([641](https://github.com/WordPress/performance/pull/641))
* Measurement: Add `perflab_disable_object_cache_dropin` filter. ([629](https://github.com/WordPress/performance/pull/629))
* Database: Add an indicator in the adminbar to show when using SQLite. ([604](https://github.com/WordPress/performance/pull/604))

**Bug Fixes**

* Images: Check for existing `$metadata['sizes']` to fix PHP warning. ([648](https://github.com/WordPress/performance/pull/648))
* Images: Use correct number of arguments in filter callback. ([634](https://github.com/WordPress/performance/pull/634))
* Database: Fix invalid docs and return types as highlighted by static analysis. ([645](https://github.com/WordPress/performance/pull/645))
* Infrastructure: Fix incorrect usage of `plugin_action_links_*` filter. ([647](https://github.com/WordPress/performance/pull/647))

**Documentation**

* Infrastructure: Add file header to object-cache drop-in to clarify purpose. ([649](https://github.com/WordPress/performance/pull/649))

= 1.9.0 =

**Enhancements**

* Database: Remove warning about multi-server environment from the SQLite module description. ([619](https://github.com/WordPress/performance/pull/619))

**Bug Fixes**

* Infrastructure: Enhance object-cache.php drop-in interoperability with other plugins. ([616](https://github.com/WordPress/performance/pull/616))

= 1.8.0 =

**Features**

* Measurement: Implement Server-Timing API foundation as well as basic load time metrics. ([553](https://github.com/WordPress/performance/pull/553))
* Database: Implement new experimental SQLite integration module. ([547](https://github.com/WordPress/performance/pull/547))
* Images: Implement new experimental `fetchpriority` module. ([528](https://github.com/WordPress/performance/pull/528))

**Bug Fixes**

* Database: Fix SQLite notices related to undefined properties. ([600](https://github.com/WordPress/performance/pull/600))
* Database: Fix incorrect handling of `admin_email` and actual admin user's email when original `admin_email` user was deleted. ([603](https://github.com/WordPress/performance/pull/603))
* Database: Make WP filesystem setup more robust to prevent potential errors. ([595](https://github.com/WordPress/performance/pull/595))

= 1.7.0 =

**Enhancements**

* Images: Change WP Image editor quality for mime types. ([571](https://github.com/WordPress/performance/pull/571))
* Infrastructure: Introduce database focus area, rename JavaScript focus area to JS & CSS, and phase out Site Health focus area. ([566](https://github.com/WordPress/performance/pull/566))

**Bug Fixes**

* Images: Avoid potentially adding invalid attributes or duplicates for dominant color images. ([578](https://github.com/WordPress/performance/pull/578))
* Images: Fix fatal error in REST API response when an image has no attachment metadata. ([568](https://github.com/WordPress/performance/pull/568))
* Images: Fix image focal point bug when dominant color is enabled by not overriding `style` attribute. ([582](https://github.com/WordPress/performance/pull/582))
* Images: Fix opt-in checkbox for generating WebP and JPEG to also show on Multisite. ([565](https://github.com/WordPress/performance/pull/565))

= 1.6.0 =

**Enhancements**

* Site Health: Only load Site Health checks for persistent cache and full page cache when not available in core. ([543](https://github.com/WordPress/performance/pull/543))
* Images: Add checkbox to Settings > Media to control whether to generate JPEG in addition to WebP. ([537](https://github.com/WordPress/performance/pull/537))
* Images: Generate only WebP images by default for JPEG and WebP uploads. ([527](https://github.com/WordPress/performance/pull/527))
* Infrastructure: Bump minimum WordPress requirement to 6.0. ([549](https://github.com/WordPress/performance/pull/549))

= 1.5.0 =

**Enhancements**

* Site Health: Improve autoloaded options check by highlighting largest autoloaded options. ([353](https://github.com/WordPress/performance/pull/353))

= 1.4.0 =

**Enhancements**

* Images: Enhance JS replacement mechanism for WebP to JPEG to more reliably replace full file name. ([443](https://github.com/WordPress/performance/pull/443))
* Images: Introduce `webp_uploads_get_content_image_mimes()` to get content image MIME replacement rules. ([420](https://github.com/WordPress/performance/pull/420))
* Infrastructure: Add `PERFLAB_PLUGIN_DIR_PATH` constant for `plugin_dir_path()`. ([429](https://github.com/WordPress/performance/pull/429))
* Infrastructure: Rename Site Health check modules for language and consistency. ([423](https://github.com/WordPress/performance/pull/423))

**Bug Fixes**

* Site Health: Fix incorrect usage of badge colors in all Site Health checks. ([472](https://github.com/WordPress/performance/pull/472))
* Images: Add the original image's extension to the WebP file name to ensure it is unique. ([444](https://github.com/WordPress/performance/pull/444))
* Images: Fix REST API support for plain permalinks. ([457](https://github.com/WordPress/performance/pull/457))
* Infrastructure: Remove plugin option network-wide for Multisite during uninstall. ([458](https://github.com/WordPress/performance/pull/458))

= 1.3.0 =

**Enhancements**

* Images: Add replacing of images only in frontend context. ([424](https://github.com/WordPress/performance/pull/424))
* Images: Allow control for which image sizes to generate additional MIME type versions. ([415](https://github.com/WordPress/performance/pull/415))
* Images: Discard WebP image if it is larger than corresponding JPEG image. ([418](https://github.com/WordPress/performance/pull/418))
* Images: Optimize computing dominant color and transparency for images by combining the two functions. ([381](https://github.com/WordPress/performance/pull/381))
* Images: Provide fallback JPEG images in frontend when WebP is not supported by the browser. ([360](https://github.com/WordPress/performance/pull/360))
* Images: Rely on `wp_get_image_editor()` methods argument to check whether it supports dominant color methods. ([404](https://github.com/WordPress/performance/pull/404))
* Images: Remove experimental label from Dominant Color module and turn on by default for new installs. ([425](https://github.com/WordPress/performance/pull/425))
* Site Health: Remove `perflab_aea_get_resource_file_size()` in favor of `wp_filesize()`. ([380](https://github.com/WordPress/performance/pull/380))
* Site Health: Update documentation link for autoloaded options. ([408](https://github.com/WordPress/performance/pull/408))
* Infrastructure: Implement mechanism to not load module if core version is available. ([390](https://github.com/WordPress/performance/pull/390))

**Bug Fixes**

* Images: Ensure incorrect usage of `webp_uploads_upload_image_mime_transforms` filter is treated correctly. ([393](https://github.com/WordPress/performance/pull/393))
* Images: Fix PHP notice and bug in logic for when `webp_uploads_prefer_smaller_image_file` filter is set to `true`. ([397](https://github.com/WordPress/performance/pull/397))
* Images: Fix an infinite loop in the WebP fallback mechanism. ([433](https://github.com/WordPress/performance/pull/433))
* Images: Fix dominant color upload process to not override potential third-party editors. ([401](https://github.com/WordPress/performance/pull/401))
* Images: Remove additional image backup sources & sizes files when attachment deleted. ([411](https://github.com/WordPress/performance/pull/411))
* Infrastructure: Avoid including .husky directory in plugin ZIP. ([421](https://github.com/WordPress/performance/pull/421))
* Infrastructure: Do not show admin pointer in multisite Network Admin. ([394](https://github.com/WordPress/performance/pull/394))

= 1.2.0 =

**Features**

* Images: Add Dominant Color module to provide color background for loading images. ([282](https://github.com/WordPress/performance/pull/282))
* Site Health: Add Site Health check for Full Page Cache usage. ([263](https://github.com/WordPress/performance/pull/263))

**Enhancements**

* Images: Update `webp_uploads_pre_generate_additional_image_source` filter to allow returning file size. ([334](https://github.com/WordPress/performance/pull/334))
* Infrastructure: Introduce plugin uninstaller routine. ([345](https://github.com/WordPress/performance/pull/345))
* Infrastructure: Use `wp_filesize` instead of `filesize` if available. ([376](https://github.com/WordPress/performance/pull/376))

**Bug Fixes**

* Images: Avoid overwriting existing WebP files when creating WebP images. ([359](https://github.com/WordPress/performance/pull/359))
* Images: Back up edited `full` image sources when restoring the original image. ([314](https://github.com/WordPress/performance/pull/314))

= 1.1.0 =

**Features**

* Infrastructure: Add Performance Lab generator meta tag to `wp_head` output. ([322](https://github.com/WordPress/performance/pull/322))

**Enhancements**

* Images: Introduce filter `webp_uploads_pre_generate_additional_image_source` to short-circuit generating additional image sources on upload. ([318](https://github.com/WordPress/performance/pull/318))
* Images: Introduce filter `webp_uploads_pre_replace_additional_image_source` to short-circuit replacing additional image sources in frontend content. ([319](https://github.com/WordPress/performance/pull/319))
* Images: Refine logic to select smaller image file in the frontend based on `webp_uploads_prefer_smaller_image_file` filter. ([302](https://github.com/WordPress/performance/pull/302))
* Images: Replace the featured image with WebP version when available. ([316](https://github.com/WordPress/performance/pull/316))
* Site Health: Update Site Health Autoloaded options documentation link. ([313](https://github.com/WordPress/performance/pull/313))
* Infrastructure: Avoid unnecessarily early escape of Site Health check labels. ([332](https://github.com/WordPress/performance/pull/332))

**Bug Fixes**

* Object Cache: Correct label for persistent object cache Site Health check. ([329](https://github.com/WordPress/performance/pull/329))
* Images: Only update the specified target images when an image is edited. ([301](https://github.com/WordPress/performance/pull/301))

= 1.0.0 =

**Features**

* Images: Generate secondary image MIME types when editing original image. ([235](https://github.com/WordPress/performance/pull/235))

**Enhancements**

* Images: Introduce `webp_uploads_prefer_smaller_image_file` filter allowing to opt in to preferring the smaller image file. ([287](https://github.com/WordPress/performance/pull/287))
* Images: Select MIME type to use in frontend content based on file size. ([243](https://github.com/WordPress/performance/pull/243))
* Site Health: Update Site Health reports copy for more clarity and consistency. ([272](https://github.com/WordPress/performance/pull/272))

**Documentation**

* Infrastructure: Define the plugin's version support and backward compatibility policy. ([240](https://github.com/WordPress/performance/pull/240))

= 1.0.0-rc.1 =

**Enhancements**

* Images: Change expected order of items in the `webp_uploads_content_image_mimes` filter. ([250](https://github.com/WordPress/performance/pull/250))
* Images: Replace images in frontend content without using an additional regular expression. ([262](https://github.com/WordPress/performance/pull/262))
* Images: Restore and backup image sizes alongside the sources properties. ([242](https://github.com/WordPress/performance/pull/242))

**Bug Fixes**

* Images: Select image editor based on WebP support instead of always using the default one. ([259](https://github.com/WordPress/performance/pull/259))

= 1.0.0-beta.3 =

**Bug Fixes**

* Infrastructure: Ensure default modules are loaded regardless of setting registration. ([248](https://github.com/WordPress/performance/pull/248))

= 1.0.0-beta.2 =

**Features**

* Images: Create additional MIME types for the full size image. ([194](https://github.com/WordPress/performance/pull/194))
* Site Health: Add module to warn about excessive amount of autoloaded options. ([124](https://github.com/WordPress/performance/pull/124))

**Enhancements**

* Images: Adds sources information to the attachment media details of the REST response. ([224](https://github.com/WordPress/performance/pull/224))
* Images: Allow developers to select which image format to use for images in the content. ([230](https://github.com/WordPress/performance/pull/230))
* Images: Allow developers to tweak which image formats to generate on upload. ([227](https://github.com/WordPress/performance/pull/227))
* Images: Replace the full size image in `the_content` with additional MIME type if available. ([195](https://github.com/WordPress/performance/pull/195))
* Object Cache: Include `memcached` extension in checks for object cache support. ([206](https://github.com/WordPress/performance/pull/206))
* Infrastructure: Add plugin banner and icon assets. ([231](https://github.com/WordPress/performance/pull/231))
* Infrastructure: Use `.gitattributes` instead of `.distignore` to better support ZIP creation. ([223](https://github.com/WordPress/performance/pull/223))

**Bug Fixes**

* Images: Use `original` image to generate all additional image format sub-sizes. ([207](https://github.com/WordPress/performance/pull/207))
* Infrastructure: Replace unreliable activation hook with default value for enabled modules. ([222](https://github.com/WordPress/performance/pull/222))

**Documentation**

* Infrastructure: Update release instructions to include proper branching strategy and protect release branches. ([221](https://github.com/WordPress/performance/pull/221))

= 1.0.0-beta.1 =

**Features**

* Images: Add WebP for uploads module. ([32](https://github.com/WordPress/performance/pull/32))
* Images: Support retry mechanism for generating sub-sizes in additional MIME types on constrained environments. ([188](https://github.com/WordPress/performance/pull/188))
* Images: Update `the_content` with the appropriate image format. ([152](https://github.com/WordPress/performance/pull/152))
* Site Health: Add WebP support in site health. ([141](https://github.com/WordPress/performance/pull/141))
* Site Health: Add module to alert about excessive JS and CSS assets. ([54](https://github.com/WordPress/performance/pull/54))
* Object Cache: Add Site Health check module for persistent object cache. ([111](https://github.com/WordPress/performance/pull/111))
* Infrastructure: Add settings screen to toggle modules. ([30](https://github.com/WordPress/performance/pull/30))
* Infrastructure: Added admin pointer. ([199](https://github.com/WordPress/performance/pull/199))

**Enhancements**

* Object Cache: Always recommend object cache on multisite. ([200](https://github.com/WordPress/performance/pull/200))
* Images: Create image sub-sizes in additional MIME types using `sources` for storage. ([147](https://github.com/WordPress/performance/pull/147))
* Images: Update module directories to be within their focus directory. ([58](https://github.com/WordPress/performance/pull/58))
* Site Health: Enhance detection of enqueued frontend assets. ([136](https://github.com/WordPress/performance/pull/136))
* Infrastructure: Add link to Settings screen to the plugin's entry in plugins list table. ([197](https://github.com/WordPress/performance/pull/197))
* Infrastructure: Enable all non-experimental modules on plugin activation. ([191](https://github.com/WordPress/performance/pull/191))
* Infrastructure: Include generated module-i18n.php file in repository. ([196](https://github.com/WordPress/performance/pull/196))
* Infrastructure: Introduce `perflab_active_modules` filter to control which modules are active. ([87](https://github.com/WordPress/performance/pull/87))
* Infrastructure: Remove unnecessary question marks from checkbox labels. ([110](https://github.com/WordPress/performance/pull/110))
* Infrastructure: Rename `object-caching` to `object-cache`. ([108](https://github.com/WordPress/performance/pull/108))

**Bug Fixes**

* Images: Ensure the `-scaled` image remains in the original uploaded format. ([143](https://github.com/WordPress/performance/pull/143))
* Images: Fix typo to access to the correct image properties. ([203](https://github.com/WordPress/performance/pull/203))
* Infrastructure: Ensure that module header fields can be translated. ([60](https://github.com/WordPress/performance/pull/60))

**Documentation**

* Site Health: Mark Site Health Audit Enqueued Assets module as experimental for now. ([205](https://github.com/WordPress/performance/pull/205))
* Infrastructure: Add `readme.txt` and related update script. ([72](https://github.com/WordPress/performance/pull/72))
* Infrastructure: Add changelog generator script. ([51](https://github.com/WordPress/performance/pull/51))
* Infrastructure: Add contribution documentation. ([47](https://github.com/WordPress/performance/pull/47))
* Infrastructure: Add release documentation. ([138](https://github.com/WordPress/performance/pull/138))
* Infrastructure: Define module specification in documentation. ([26](https://github.com/WordPress/performance/pull/26))

== Upgrade Notice ==

= n.e.x.t =

This release introduces two new features: View Transitions which adds smooth transitions between navigations on your site, and No-cache BFCache which enables back/forward cache (bfcache) for instant history navigations.

= 3.2.0 =

This release introduces a new feature plugin called Image Prioritizer which optimizes the loading of images to improve LCP.

= 3.0.0 =

Starting with this release, modules such as Image Placeholders and Modern Image Formats are only available as standalone plugins as opposed to bundled modules. After updating, you will be able to easily migrate to this new structure.

= 2.5.0 =

The SQLite module is no longer present starting with this release. If you still use it, please migrate to the standalone plugin before updating.
