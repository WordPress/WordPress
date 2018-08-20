=== Preferred Languages ===
Contributors: swissspidy
Tags: internationalization, i18n, localization, l10n, language, locale, translation
Requires at least: 4.9
Tested up to: 4.9
Requires PHP: 5.2
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Choose languages for displaying WordPress in, in order of preference.

== Description ==

Thanks to language packs it's easier than ever before to change the main language of your site. However, in some cases a single locale is not enough. When WordPress can't find a translation for the active locale, it falls back to the original English strings. That’s a poor user experience for many non-English speakers.

This feature project aims to change that by letting users choose multiple languages for displaying WordPress in. That way you can set some sort of "fallback chain" where WordPress tries to load translations in your preferred order.

Please help us test this plugin and let us know if something is not working as you think it should.

= Get Involved =

Active development is taking place on [GitHub](https://github.com/swissspidy/preferred-languages).

If you want to get involved, check out [open issues](https://github.com/swissspidy/preferred-languages/issues) and join the [#core-i18n](https://wordpress.slack.com/messages/core-i18n) channel on [Slack](https://wordpress.slack.com/). If you don't have a Slack account yet, you can sign up at [make.wordpress.org/chat/](https://make.wordpress.org/chat/).

== Screenshots ==

1. The new language section in 'Settings' -> 'General'
2. The new language section in your user profile.

== Changelog ==

= 1.4.0 =

* New: Keyboard navigation improvements.
* New: Tooltips now show the available keyboard shortcuts.
* New: Missing translations are now downloaded even when no changes were made.
* New: A warning is shown when some of the preferred languages aren't installed.
* New: Settings form is now hidden when JavaScript is disabled.
* Fixed: Improved setting the current locale.
* Fixed: CSS is no longer enqueued on the front end.

= 1.3.0 =

* New: Users can now choose English (United States) again as a preferred locale.
* New: Users with the right capabilities can now install languages in their user profile as well.

= 1.2.0 =

* Fixed: Other English locales can now be added again.
* Fixed: Prevented some errors when adding all available locales.

= 1.1.0 =

* New: Support for just-in-time loading of translations.
* New: Keyboard shortcut for making inactive locales active.
* Fixed: Responsive design improvements.
* Fixed: Worked around a few edge cases with the various controls.
* Fixed: Added missing text domains.

= 1.0.1 =

* Fixed: Fixed a bug that prevented saving changes.

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.4.0 =

This release contains improvements regarding translation downloads upon saving.

= 1.3.0 =

Users can now install new languages from their user profile. Also, English (US) can be chosen as a preferred user language.

= 1.2.0 =

This release fixes various bugs when adding and removing multiple languages.

= 1.1.0 =

This release includes some accessibility and usability improvements, as well as support for just-in-time loading of translations.

= 1.0.1 =

This release fixes a bug that prevented saving changes in some cases.

= 1.0.0 =

Initial release.
