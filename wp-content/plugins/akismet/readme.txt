=== Akismet Anti-spam: Spam Protection ===
Contributors: matt, ryan, andy, mdawaffe, tellyworth, josephscott, lessbloat, eoigal, cfinke, automattic, jgs, procifer, stephdau, kbrownkd, bluefuton, derekspringer, lschuyler, andyperdomo, akismetantispam
Tags: comments, spam, antispam, anti-spam, contact form
Requires at least: 5.8
Tested up to: 6.8.1
Stable tag: 5.5
License: GPLv2 or later

The best anti-spam protection to block spam comments and spam in a contact form. The most trusted antispam solution for WordPress and WooCommerce.

== Description ==

The best anti-spam protection to block spam comments and spam in a contact form. The most trusted antispam solution for WordPress and WooCommerce.

Akismet checks your comments and contact form submissions against our global database of spam to prevent your site from publishing malicious content. You can review the comment spam it catches on your blog's "Comments" admin screen.

Major features in Akismet include:

* Automatically checks all comments and filters out the ones that look like spam.
* Each comment has a status history, so you can easily see which comments were caught or cleared by Akismet and which were spammed or unspammed by a moderator.
* URLs are shown in the comment body to reveal hidden or misleading links.
* Moderators can see the number of approved comments for each user.
* A discard feature that outright blocks the worst spam, saving you disk space and speeding up your site.

PS: You'll be prompted to get an Akismet.com API key to use it, once activated. Keys are free for personal blogs; paid subscriptions are available for businesses and commercial sites.

== Installation ==

Upload the Akismet plugin to your blog, activate it, and then enter your Akismet.com API key.

1, 2, 3: You're done!

== Changelog ==

= 5.5 =
*Release Date - 15 July 2025*

* Enable webhooks so that Akismet can process comments asynchronously to detect more types of spam.
* Only include the Akismet widget CSS when the Akismet widget is present
* Improve contrast/readability for certain UI elements

= 5.4 =
*Release Date - 7 May 2025*

* The stats pages now use the user's locale instead of the site's locale if they're different.
* Adds a 'Compatible plugins' section that will show installed and active plugins that are compatible with Akismet.
* Akismet now requires PHP version 7.2 or above.

= 5.3.7 =
*Release Date - 14 February 2025*

* Simplify the logic used during a comment-check request to compare comments.

= 5.3.6 =
*Release Date - 4 February 2025*

* Improve the utility of submit-spam and submit-ham requests.
* Modernize styles for the Akismet classic widget.

= 5.3.5 =
*Release Date - 18 November 2024*

* Address compatibility issues with < PHP 7.3 in v5.3.4 release.

= 5.3.4 =
*Release Date - 18 November 2024*

* Improve activation notice on Comments for users who haven't set up their API key yet.
* Improve notice about commercial site status.

= 5.3.3 =
*Release Date - 10 July 2024*

* Make setup step clearer for new users.
* Remove the stats section from the configuration page if the site has been revoked from the key.
* Skip the Akismet comment check when the comment matches something in the disallowed list.
* Prompt users on legacy plans to contact Akismet support for upgrades.

= 5.3.2 =
*Release Date - 21 March 2024*

* Improve the empty state shown to new users when no spam has been caught yet.
* Update the message shown to users without a current subscription.
* Add foundations for future webhook support.

= 5.3.1 =
*Release Date - 17 January 2024*

* Make the plugin more resilient when asset files are missing (as seen in WordPress Playground).
* Add a link to the 'Account overview' page on akismet.com.
* Fix a minor error that occurs when another plugin removes all comment actions from the dashboard.
* Add the akismet_request_args filter to allow request args in Akismet API requests to be filtered.
* Fix a bug that causes some contact forms to include unnecessary data in the comment_content parameter.

= 5.3 =
*Release Date - 14 September 2023*

* Improve display of user notices.
* Add stylesheets for RTL languages.
* Remove initial disabled state from 'Save changes' button.
* Improve accessibility of API key entry form.
* Add new filter hooks for Fluent Forms.
* Fix issue with PHP 8.1 compatibility.

= 5.2 =
*Release Date - 21 June 2023*

* Visual refresh of Akismet stats.
* Improve PHP 8.1 compatibility.
* Improve appearance of plugin to match updated stats.
* Change minimum supported PHP version to 5.6 to match WordPress.
* Drop IE11 support and update minimum WordPress version to 5.8 (where IE11 support was removed from WP Core).

= 5.1 =
*Release Date - 20 March 2023*

* Removed unnecessary limit notices from admin page.
* Improved spam detection by including post taxonomies in the comment-check call.
* Removed API keys from stats iframes to avoid possible inadvertent exposure.

= 5.0.2 =
*Release Date - 1 December 2022*

* Improved compatibility with themes that hide or show UI elements based on mouse movements.
* Increased security of API keys by sending them in request bodies instead of subdomains.

= 5.0.1 =
*Release Date - 28 September 2022*

* Added an empty state for the Statistics section on the admin page.
* Fixed a bug that broke some admin page links when Jetpack plugins are active.
* Marked some event listeners as passive to improve performance in newer browsers.
* Disabled interaction observation on forms that post to other domains.

= 5.0 =
*Release Date - 26 July 2022*

* Added a new feature to catch spammers by observing how they interact with the page.

For older changelog entries, please see the [additional changelog.txt file](https://plugins.svn.wordpress.org/akismet/trunk/changelog.txt) delivered with the plugin.
