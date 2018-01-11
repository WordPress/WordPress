=== Polylang ===
Contributors: Chouby
Donate link: https://polylang.pro
Tags: multilingual, bilingual, translate, translation, language, multilanguage, international, localization
Requires at least: 4.4
Tested up to: 4.9
Stable tag: 2.2.8
License: GPLv2 or later

Making WordPress multilingual

== Description ==

= Features  =

Polylang allows you to create a bilingual or multilingual WordPress site. You write posts, pages and create categories and post tags as usual, and then define the language for each of them. The translation of a post, whether it is in the default language or not, is optional.

* You can use as many languages as you want. RTL language scripts are supported. WordPress languages packs are automatically downloaded and updated.
* You can translate posts, pages, media, categories, post tags, menus, widgets...
* Custom post types, custom taxonomies, sticky posts and post formats, RSS feeds and all default WordPress widgets are supported.
* The language is either set by the content or by the language code in url, or you can use one different subdomain or domain per language
* Categories, post tags as well as some other metas are automatically copied when adding a new post or page translation
* A customizable language switcher is provided as a widget or in the nav menu

> The author does not provide support on the wordpress.org forum. Support and extra features are available to [Polylang Pro](https://polylang.pro) users.

If you wish to migrate from WPML, you can use the plugin [WPML to Polylang](https://wordpress.org/plugins/wpml-to-polylang/)

If you wish to use a professional or automatic translation service, you can install [Lingotek Translation](https://wordpress.org/plugins/lingotek-translation/), as an addon of Polylang. Lingotek offers a complete translation management system which provides services such as translation memory or semi-automated translation processes (e.g. machine translation > human translation > legal review).

= Credits =

Thanks a lot to all translators who [help translating Polylang](https://translate.wordpress.org/projects/wp-plugins/polylang).
Thanks a lot to [Alex Lopez](http://www.alexlopez.rocks/) for the design of the logo.
Most of the flags included with Polylang are coming from [famfamfam](http://famfamfam.com/) and are public domain.
Wherever third party code has been used, credit has been given in the code’s comments.

= Do you like Polylang? =

Don't hesitate to [give your feedback](http://wordpress.org/support/view/plugin-reviews/polylang#postform).

== Installation ==

1. Make sure you are using WordPress 4.0 or later and that your server is running PHP 5.2.4 or later (same requirement as WordPress itself)
1. If you tried other multilingual plugins, deactivate them before activating Polylang, otherwise, you may get unexpected results!
1. Install and activate the plugin as usual from the 'Plugins' menu in WordPress.
1. Go to the languages settings page and create the languages you need
1. Add the 'language switcher' widget to let your visitors switch the language.
1. Take care that your theme must come with the corresponding .mo files (Polylang automatically downloads them when they are available for themes and plugins in this repository). If your theme is not internationalized yet, please refer to the [Theme Handbook](https://developer.wordpress.org/themes/functionality/internationalization/) or ask the theme author to internationalize it.

== Frequently Asked Questions ==

= Where to find help ? =

* First time users should read [Polylang - Getting started](https://polylang.pro/doc-category/getting-started/), which explains the basics with a lot of screenshots.
* Read the [documentation](https://polylang.pro/doc/). It includes a [FAQ](https://polylang.pro/doc-category/faq/) and the [documentation for developers](https://polylang.pro/doc-category/developers/).
* Search the [community support forum](https://wordpress.org/search/). You will probably find your answer here.
* Read the sticky posts in the [community support forum](http://wordpress.org/support/plugin/polylang).
* If you still have a problem, open a new thread in the [community support forum](http://wordpress.org/support/plugin/polylang).
* [Polylang Pro](https://polylang.pro) users have access to our helpdesk.

= Is Polylang compatible with WooCommerce? =

* You need a separate addon to make Polylang and WooCommerce work together. [A Premium addon](https://polylang.pro/downloads/polylang-for-woocommerce/) is available.

= Do you need translation services? =

* If you want to use professional or automatic translation services, install and activate the [Lingotek Translation](https://wordpress.org/plugins/lingotek-translation/) plugin.

== Screenshots ==

1. The Polylang languages admin panel
2. The Strings translations admin panel
3. Multilingual media library
4. The Edit Post screen with the Languages metabox

== Changelog ==

= 2.2.8 (2018-01-09) =

* Pro: Fix: Impossible to link past events by translation in The Events Calendar
* Disallow to delete translations of the default term for all taxonomies
* Fix: Auto add pages adds WooCommerce pages in default language to menus in all languages
* Fix most used tag cloud in Tags metabox in WP4.9+. Props Pär Thernström. #208

= 2.2.7 (2017-11-30) =

* Fix queries by taxonomy broken since WP 4.9
* Fix PHP notice in icl_object_id()

= 2.2.6 (2017-11-22) =

* Pro: Fix query by post name and alternative language always returning the post in current language (when sharing slugs)
* Pro: Fix query by taxonomy and alternative language returning empty results
* Rework how translation files are loaded in ajax on front when the user is logged (in WP 4.7+)
* Add filter 'get_objects_with_no_lang_limit'
* Force loading the admin side when using WP CLI (Props chrisschrijver)
* Fix check for terms with no language not scaling
* Fix pll_count_posts not working with multiple post types
* Fix inconsistent spacing between flag and language name in language switcher parent menu item (Props Amit Tal)
* Fix spacing between flag and language name when displaying an RTL language
* Fix get_terms not accepting comma separated values for 'lang' parameter (Props Pavlo Zhukov)
* Fix possible wrong language detected in url when using subdomains (Props Pavlo Zhukov)
* Fix double escaped query

= 2.2.5 (2017-11-09) =

* Update plugin updater class to 1.6.15
* Add $link in cache key of links filters
* Add support for 'nav_menu' post type in wpml_object_id
* Fix conflict with Timber (introduced in 2.2.4)

= 2.2.4 (2017-10-26) =

* Pro: Fix unknown language not redirected to default when using multiple domains
* Pro: Fix empty 'lang' query var not deactivating the language query filter
* Pro: Fix conflict with The Events Calendar and Visual Composer when used together
* Add new filter `pll_hide_archive_translation_url` #174
* Add support for undocumented and deprecated WPML functions `wpml_object_id_filter` and `icl_get_current_language`
* Fix 'orderby' and 'order' in `wpml_active_languages`. Needs WP 4.7+
* Fix `icl_get_languages` not returning all languages when skip_missing = 0. Props Loïc Blascos
* Fix `pll_translate_string` not working on admin #178
* Fix PHP Warning in widget video in WP 4.9
* Fix query using 'any' post type not filtered per language (introduced in 2.2)
* Fix untranslatable string in About metabox. Props Farhad Sakhaei
* Fix error with PHP 7.1 and Duplicate Post. Props Enea Scerba
* Fix query auto translation not active in ajax requests on frontend
* Fix query auto translation for 'postname' and 'pagename'
* Fix terms query auto translation not working for 'include' when no taxonomy is provided (WP 4.5+)

= 2.2.3 (2017-09-24) =

* Fix editor removed on pages (introduced in 2.2.2)

= 2.2.2 (2017-09-22) =

* Pro: Fix Duplicate post button not working when the user meta has been corrupted
* Fix PHP notice with the plugin Members #175
* Fix page template select displayed when editing a translated page for posts
* Fix incompatibility with WP 4.8.2 (placeholder %1$s in prepare)

= 2.2.1 (2017-08-30) =

* Pro: partially refactor REST API classes
* Pro: Fix duplicate content user meta not removed from DB when uninstalling the plugin
* Fix strings translations not removed from DB when uninstalling the plugin
* Fix incorrect translation files loaded in ajax on front when the user is logged in (WP 4.7+)
* Fix widget language dropdown removed when saving a widget (introduced in 2.2)
* Fix queries with negative values for the 'cat' parameter (introduced in 2.2 for queries made on frontend)
* Fix performance issue in combination with some plugins when the language is set from the content (introduced in 2.2)

= 2.2 (2017-08-16) =

* Pro: Add support for the REST API
* Pro: Add integration with The Events Calendar
* Pro: Refactor ACF Pro integration for post metas and integrate term metas
* Pro: Ask confirmation if synchronizing a post overwrites an existing translation
* Pro: Separate sync post logic from interface
* Pro: Fix 'Detect browser language' option automatically deactivated
* Pro: Fix redirect to 404 when the 'page' slug translation includes non alphanumeric characters.
* Pro: Fix untranslated post type archive slug
* Pro: Fix ACF taxonomy fields not copied when the taxonomy is not translated #156
* Pro: Fix fatal error with ACF4
* Support a different content text direction in admin #45
* Add support for wildcards and 'copy-once' attribute in wpml-config.xml
* Add minimal support for the filters 'wpml_display_language_names' and 'icl_ls_languages'
* Improve compatibility with the plugin WordPress MU Domain Mapping #116
* Improve speed of the sticky posts filter #41
* Remove redirect_lang option for multiple domains and subdomains
* Use secure cookie when using SSL
* Allow to copy/sync term metas with the filter 'pll_copy_term_metas'
* Filter ajax requests in term.php according to the term language
* Add error message in customizer when setting an untranslated static front page #47
* Load static page class only if we are using a static front page
* Refactor parse_query filters to use the same code on frontend and admin
* Don't use add_language_to_link in filters
* Move ajaxPrefilter footer script on top
* Use wp_doing_ajax() instead of DOING_AJAX constant
* Fix queries custom tax not excluded from language filter on admin
* Fix WP translation not loaded when the language is set from the content on multisite.
* Fix the list of core post types in PLL_OLT_Manager for WP 4.7+
* Fix post name and tag slug incorrectly sanitized for German and Danish
* Fix lang attribute in dropdowns
* Fix wpml_permalink filter #139
* Fix WPML constants undefined on backend #151
* Fix a conflict with the plugin Custom Permalinks #143
* Fix menu location unexpectedly unset

See [changelog.txt](https://plugins.svn.wordpress.org/polylang/trunk/changelog.txt) for older changelog
