=== Akismet Anti-Spam ===
Contributors: matt, ryan, andy, mdawaffe, tellyworth, josephscott, lessbloat, eoigal, cfinke, automattic, jgs, procifer, stephdau
Tags: akismet, comments, spam, antispam, anti-spam, anti spam, comment moderation, comment spam, contact form spam, spam comments
Requires at least: 4.0
Tested up to: 4.9.1
Stable tag: 4.0.3
License: GPLv2 or later

Akismet checks your comments and contact form submissions against our global database of spam to protect you and your site from malicious content.

== Description ==

Akismet checks your comments and contact form submissions against our global database of spam to prevent your site from publishing malicious content. You can review the comment spam it catches on your blog's "Comments" admin screen.

Major features in Akismet include:

* Automatically checks all comments and filters out the ones that look like spam.
* Each comment has a status history, so you can easily see which comments were caught or cleared by Akismet and which were spammed or unspammed by a moderator.
* URLs are shown in the comment body to reveal hidden or misleading links.
* Moderators can see the number of approved comments for each user.
* A discard feature that outright blocks the worst spam, saving you disk space and speeding up your site.

PS: You'll need an [Akismet.com API key](https://akismet.com/get/) to use it.  Keys are free for personal blogs; paid subscriptions are available for businesses and commercial sites.

== Installation ==

Upload the Akismet plugin to your blog, Activate it, then enter your [Akismet.com API key](https://akismet.com/get/).

1, 2, 3: You're done!

== Changelog ==

= 4.0.3 =
*Release Date - 19 February 2018*

* Added a scheduled task to remove entries in wp_commentmeta that no longer have corresponding comments in wp_comments.
* Added a new `akismet_batch_delete_count` action to the batch delete methods for people who'd like to keep track of the numbers of records being processed by those methods.

= 4.0.2 =
*Release Date - 18 December 2017*

* Fixed a bug that could cause Akismet to recheck a comment that has already been manually approved or marked as spam.
* Fixed a bug that could cause Akismet to claim that some comments are still waiting to be checked when no comments are waiting to be checked.

= 4.0.1 =
*Release Date - 6 November 2017*

* Fixed a bug that could prevent some users from connecting Akismet via their Jetpack connection.
* Ensured that any pending Akismet-related events are unscheduled if the plugin is deactivated.
* Allow some JavaScript to be run asynchronously to avoid affecting page render speeds.

= 4.0 =
*Release Date - 19 September 2017*

* Added REST API endpoints for configuring Akismet and retrieving stats.
* Increased the minimum supported WordPress version to 4.0.
* Added compatibility with comments submitted via the REST API.
* Improved the progress indicator on the "Check for Spam" button.

= 3.3.4 =
*Release Date - 3 August 2017*

* Disabled Akismet's debug log output by default unless AKISMET_DEBUG is defined.
* URL previews now begin preloading when the mouse moves near them in the comments section of wp-admin.
* When a comment is caught by the Comment Blacklist, Akismet will always allow it to stay in the trash even if it is spam as well.
* Fixed a bug that was preventing an error from being shown when a site can't reach Akismet's servers.

= 3.3.3 =
*Release Date - 13 July 2017*

* Reduced amount of bandwidth used by the URL Preview feature.
* Improved the admin UI when the API key is manually pre-defined for the site.
* Removed a workaround for WordPress installations older than 3.3 that will improve Akismet's compatibility with other plugins.
* The number of spam blocked that is displayed on the WordPress dashboard will now be more accurate and updated more frequently.
* Fixed a bug in the Akismet widget that could cause PHP warnings.

= 3.3.2 =
*Release Date - 10 May 2017*

* Fixed a bug causing JavaScript errors in some browsers.
 
= 3.3.1 =
*Release Date - 2 May 2017*

* Improve performance by only requesting the akismet_comment_nonce option when absolutely necessary.
* Fixed two bugs that could cause PHP warnings.
* Fixed a bug that was preventing the "Remove author URL" feature from working after a comment was edited using "Quick Edit."
* Fixed a bug that was preventing the URL preview feature from working after a comment was edited using "Quick Edit."

= 3.3 =
*Release Date - 23 February 2017*

* Updated the Akismet admin pages with a new clean design.
* Fixed bugs preventing the `akismet_add_comment_nonce` and `akismet_update_alert` wrapper functions from working properly.
* Fixed bug preventing the loading indicator from appearing when re-checking all comments for spam.
* Added a progress indicator to the "Check for Spam" button.
* Added a success message after manually rechecking the Pending queue for spam.

= 3.2 =
*Release Date - 6 September 2016*

* Added a WP-CLI module. You can now check comments and recheck the moderation queue from the command line.
* Stopped using the deprecated jQuery function `.live()`.
* Fixed a bug in `remove_comment_author_url()` and `add_comment_author_url()` that could generate PHP notices.
* Fixed a bug that could cause an infinite loop for sites with very very very large comment IDs.
* Fixed a bug that could cause the Akismet widget title to be blank.

= 3.1.11 =
*Release Date - 12 May 2016*

* Fixed a bug that could cause the "Check for Spam" button to skip some comments.
* Fixed a bug that could prevent some spam submissions from being sent to Akismet.
* Updated all links to use https:// when possible.
* Disabled Akismet debug logging unless WP_DEBUG and WP_DEBUG_LOG are both enabled.

= 3.1.10 =
*Release Date - 1 April 2016*

* Fixed a bug that could cause comments caught as spam to be placed in the Pending queue.
* Fixed a bug that could have resulted in comments that were caught by the core WordPress comment blacklist not to have a corresponding History entry.
* Fixed a bug that could have caused avoidable PHP warnings in the error log.

= 3.1.9 =
*Release Date - 28 March 2016*

* Add compatibility with Jetpack so that Jetpack can automatically configure Akismet settings when appropriate.
* Fixed a bug preventing some comment data from being sent to Akismet.

= 3.1.8 =
*Release Date - 4 March 2016*

* Fixed a bug preventing Akismet from being used with some plugins that rewrite admin URLs.
* Reduced the amount of bandwidth used on Akismet API calls
* Reduced the amount of space Akismet uses in the database
* Fixed a bug that could cause comments caught as spam to be placed in the Pending queue.

= 3.1.7 =
*Release Date - 4 January 2016*

* Added documentation for the 'akismet_comment_nonce' filter.
* The post-install activation button is now accessible to screen readers and keyboard-only users.
* Fixed a bug that was preventing the "Remove author URL" feature from working in WordPress 4.4

= 3.1.6 =
*Release Date - 14 December 2015*

* Improve the notices shown after activating Akismet.
* Update some strings to allow for the proper plural forms in all languages.

= 3.1.5 =
*Release Date - 13 October 2015*

* Closes a potential XSS vulnerability.

= 3.1.4 =
*Release Date - 24 September 2015*

* Fixed a bug that was preventing some users from automatically connecting using Jetpack if they didn't have a current Akismet subscription.
* Fixed a bug that could cause comments caught as spam to be placed in the Pending queue.
* Error messages and instructions have been simplified to be more understandable.
* Link previews are enabled for all links inside comments, not just the author's website link.

= 3.1.3 =
*Release Date - 6 July 2015*

* Notify users when their account status changes after previously being successfully set up. This should help any users who are seeing blank Akismet settings screens.

= 3.1.2 =
*Release Date - 7 June 2015*

* Reduced the amount of space Akismet uses in the commentmeta table.
* Fixed a bug where some comments with quotes in the author name weren't getting history entries
* Pre-emptive security improvements to ensure that the Akismet plugin can't be used by attackers to compromise a WordPress installation.
* Better UI for the key entry field: allow whitespace to be included at the beginning or end of the key and strip it out automatically when the form is submitted.
* When deactivating the plugin, notify the Akismet API so the site can be marked as inactive.
* Clearer error messages.

= 3.1.1 =
*Release Date - 17th March, 2015*

* Improvements to the "Remove comment author URL" JavaScript
* Include the pingback pre-check from the 2.6 branch.

= 3.1 =
*Release Date - 11th March, 2015*

* Use HTTPS by default for all requests to Akismet.
* Fix for a situation where Akismet might strip HTML from a comment.

= 3.0.4 =
*Release Date - 11th December, 2014*

* Fix to make .htaccess compatible with Apache 2.4.
* Fix to allow removal of https author URLs.
* Fix to avoid stripping part of the author URL when removing and re-adding.
* Removed the "Check for Spam" button from the "Trash" and "Approved" queues, where it would have no effect.
* Allow automatic API key configuration when Jetpack is installed and connected to a WordPress.com account

= 3.0.3 =
*Release Date - 3rd November, 2014*

* Fix for sending the wrong data to delete_comment action that could have prevented old spam comments from being deleted.
* Added a filter to disable logging of Akismet debugging information.
* Added a filter for the maximum comment age when deleting old spam comments.
* Added a filter for the number per batch when deleting old spam comments.
* Removed the "Check for Spam" button from the Spam folder.

= 3.0.2 =
*Release Date - 18th August, 2014*

* Performance improvements.
* Fixed a bug that could truncate the comment data being sent to Akismet for checking.

= 3.0.1 =
*Release Date - 9th July, 2014*

* Removed dependency on PHP's fsockopen function
* Fix spam/ham reports to work when reported outside of the WP dashboard, e.g., from Notifications or the WP app
* Remove jQuery dependency for comment form JavaScript
* Remove unnecessary data from some Akismet comment meta
* Suspended keys will now result in all comments being put in moderation, not spam.

= 3.0.0 =
*Release Date - 15th April, 2014*

* Move Akismet to Settings menu
* Drop Akismet Stats menu
* Add stats snapshot to Akismet settings
* Add Akismet subscription details and status to Akismet settings
* Add contextual help for each page
* Improve Akismet setup to use Jetpack to automate plugin setup
* Fix "Check for Spam" to use AJAX to avoid page timing out
* Fix Akismet settings page to be responsive
* Drop legacy code
* Tidy up CSS and Javascript
* Replace the old discard setting with a new "discard pervasive spam" feature.

= 2.6.0 =
*Release Date - 18th March, 2014*

* Add ajax paging to the check for spam button to handle large volumes of comments
* Optimize javascript and add localization support 
* Fix bug in link to spam comments from right now dashboard widget
* Fix bug with deleting old comments to avoid timeouts dealing with large volumes of comments
* Include X-Pingback-Forwarded-For header in outbound WordPress pingback verifications
* Add pre-check for pingbacks, to stop spam before an outbound verification request is made

= 2.5.9 =
*Release Date - 1st August, 2013*

* Update 'Already have a key' link to redirect page rather than depend on javascript
* Fix some non-translatable strings to be translatable
* Update Activation banner in plugins page to redirect user to Akismet config page

= 2.5.8 =
*Release Date - 20th January, 2013*

* Simplify the activation process for new users
* Remove the reporter_ip parameter
* Minor preventative security improvements

= 2.5.7 =
*Release Date - 13th December, 2012*

* FireFox Stats iframe preview bug
* Fix mshots preview when using https
* Add .htaccess to block direct access to files
* Prevent some PHP notices
* Fix Check For Spam return location when referrer is empty
* Fix Settings links for network admins
* Fix prepare() warnings in WP 3.5

= 2.5.6 =
*Release Date - 26th April, 2012*

* Prevent retry scheduling problems on sites where wp_cron is misbehaving
* Preload mshot previews
* Modernize the widget code
* Fix a bug where comments were not held for moderation during an error condition
* Improve the UX and display when comments are temporarily held due to an error
* Make the Check For Spam button force a retry when comments are held due to an error
* Handle errors caused by an invalid key
* Don't retry comments that are too old
* Improve error messages when verifying an API key

= 2.5.5 =
*Release Date - 11th January, 2012*

* Add nonce check for comment author URL remove action
* Fix the settings link

= 2.5.4 =
*Release Date - 5th January, 2012*

* Limit Akismet CSS and Javascript loading in wp-admin to just the pages that need it
* Added author URL quick removal functionality
* Added mShot preview on Author URL hover
* Added empty index.php to prevent directory listing
* Move wp-admin menu items under Jetpack, if it is installed
* Purge old Akismet comment meta data, default of 15 days

= 2.5.3 = 
*Release Date - 8th Febuary, 2011*

* Specify the license is GPL v2 or later
* Fix a bug that could result in orphaned commentmeta entries
* Include hotfix for WordPress 3.0.5 filter issue

= 2.5.2 =
*Release Date - 14th January, 2011*

* Properly format the comment count for author counts
* Look for super admins on multisite installs when looking up user roles
* Increase the HTTP request timeout
* Removed padding for author approved count
* Fix typo in function name
* Set Akismet stats iframe height to fixed 2500px.  Better to have one tall scroll bar than two side by side.

= 2.5.1 =
*Release Date - 17th December, 2010*

* Fix a bug that caused the "Auto delete" option to fail to discard comments correctly
* Remove the comment nonce form field from the 'Akismet Configuration' page in favor of using a filter, akismet_comment_nonce
* Fixed padding bug in "author" column of posts screen
* Added margin-top to "cleared by ..." badges on dashboard
* Fix possible error when calling akismet_cron_recheck()
* Fix more PHP warnings
* Clean up XHTML warnings for comment nonce
* Fix for possible condition where scheduled comment re-checks could get stuck
* Clean up the comment meta details after deleting a comment
* Only show the status badge if the comment status has been changed by someone/something other than Akismet
* Show a 'History' link in the row-actions
* Translation fixes
* Reduced font-size on author name
* Moved "flagged by..." notification to top right corner of comment container and removed heavy styling
* Hid "flagged by..." notification while on dashboard

= 2.5.0 =
*Release Date - 7th December, 2010*

* Track comment actions under 'Akismet Status' on the edit comment screen
* Fix a few remaining deprecated function calls ( props Mike Glendinning ) 
* Use HTTPS for the stats IFRAME when wp-admin is using HTTPS
* Use the WordPress HTTP class if available
* Move the admin UI code to a separate file, only loaded when needed
* Add cron retry feature, to replace the old connectivity check
* Display Akismet status badge beside each comment
* Record history for each comment, and display it on the edit page
* Record the complete comment as originally submitted in comment_meta, to use when reporting spam and ham
* Highlight links in comment content
* New option, "Show the number of comments you've approved beside each comment author."
* New option, "Use a nonce on the comment form."

= 2.4.0 =
*Release Date - 23rd August, 2010*

* Spell out that the license is GPLv2
* Fix PHP warnings
* Fix WordPress deprecated function calls
* Fire the delete_comment action when deleting comments
* Move code specific for older WP versions to legacy.php
* General code clean up

= 2.3.0 =
*Release Date - 5th June, 2010*

* Fix "Are you sure" nonce message on config screen in WPMU
* Fix XHTML compliance issue in sidebar widget
* Change author link; remove some old references to WordPress.com accounts
* Localize the widget title (core ticket #13879)

= 2.2.9 =
*Release Date - 2nd June, 2010*

* Eliminate a potential conflict with some plugins that may cause spurious reports

= 2.2.8 =
*Release Date - 27th May, 2010*

* Fix bug in initial comment check for ipv6 addresses
* Report comments as ham when they are moved from spam to moderation
* Report comments as ham when clicking undo after spam
* Use transition_comment_status action when available instead of older actions for spam/ham submissions
* Better diagnostic messages when PHP network functions are unavailable
* Better handling of comments by logged-in users

= 2.2.7 =
*Release Date - 17th December, 2009*

* Add a new AKISMET_VERSION constant
* Reduce the possibility of over-counting spam when another spam filter plugin is in use
* Disable the connectivity check when the API key is hard-coded for WPMU

= 2.2.6 =
*Release Date - 20th July, 2009*

* Fix a global warning introduced in 2.2.5
* Add changelog and additional readme.txt tags
* Fix an array conversion warning in some versions of PHP
* Support a new WPCOM_API_KEY constant for easier use with WordPress MU

= 2.2.5 =
*Release Date - 13th July, 2009*

* Include a new Server Connectivity diagnostic check, to detect problems caused by firewalls

= 2.2.4 =
*Release Date - 3rd June, 2009*

* Fixed a key problem affecting the stats feature in WordPress MU
* Provide additional blog information in Akismet API calls
