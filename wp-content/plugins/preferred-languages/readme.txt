=== Preferred Languages ===
Contributors: swissspidy
Tags: internationalization, i18n, localization, language, translation
Tested up to: 6.9
Stable tag: 2.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Choose languages for displaying WordPress in, in order of preference.

== Description ==

Thanks to language packs it's easier than ever before to change the main language of your site.
However, in some cases a single locale is not enough. When WordPress can't find a translation for the active locale, it falls back to the original English strings.
That’s a poor user experience for many non-English speakers.

This feature project aims to change that by letting users choose multiple languages for displaying WordPress in.
That way you can set some sort of "fallback chain" where WordPress tries to load translations in your preferred order.

Please help us test this plugin and let us know if something is not working as you think it should.

**Keyboard Shortcuts**

* <code>Arrow Up</code>: Move selected locale one position up.
* <code>Arrow Down</code>: Move selected locale one position down.
* <code>Home</code>: Select first locale in the list.
* <code>End</code>: Select last locale in the list.
* <code>Backspace</code>/<code>Delete</code>: remove the selected locale from the list.
* <code>Alt+A</code>: Add the current locale from the dropdown to the list.

**Note**: the Preferred Languages UI needs to be focused in order for the keyboard shortcuts to work.

**Merging Translations**

Previously, only the first available translation for a given locale and domain will be loaded.
However, when translations are incomplete, some strings might still be displayed in English.
That's a poor user experience as well.

To prevent this, Preferred Languages now automatically merges all incomplete translations in the list.

 the `preferred_languages_merge_translations` filter can be used to opt out of this behavior.
It provides three parameters:

1. `$merge` - Whether translations should be merged. Defaults to `true`.
2. `$domain` - The text domain
3. `$current_locale` - The current locale.

= Get Involved =

Active development is taking place on [GitHub](https://github.com/swissspidy/preferred-languages).

If you want to get involved, check out [open issues](https://github.com/swissspidy/preferred-languages/issues) and join the [#core-i18n](https://wordpress.slack.com/messages/core-i18n) channel on [Slack](https://wordpress.slack.com/). If you don't have a Slack account yet, you can sign up at [make.wordpress.org/chat/](https://make.wordpress.org/chat/).

== Screenshots ==

1. The new language section in 'Settings' -> 'General'
2. The new language section in your user profile.

== Changelog ==

For the plugin's changelog, please head over to [the GitHub repository](https://github.com/swissspidy/preferred-languages).

== Upgrade Notice ==

= 2.4.1 =

This release fixes a regression with the fallback functionality in WordPress 6.8+.
