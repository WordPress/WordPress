=== Anti-Malware Security and Brute-Force Firewall ===
Plugin URI: http://gotmls.net/
Author: Eli Scheetz
Author URI: http://wordpress.ieonly.com/category/my-plugins/anti-malware/
Contributors: scheeeli, gotmls
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QZHD8QHZ2E7PE
Tags: security, firewall, anti-malware, scanner, automatic, repair, remove, malware, virus, threat, hacked, malicious, infection, timthumb, exploit, block, brute-force, wp-login, patch, antimalware, revslider, Revolution Slider
Version: 4.17.58
Stable tag: 4.17.58
Requires at least: 3.3
Tested up to: 4.9.4

This Anti-Malware scanner searches for Malware, Viruses, and other security threats and vulnerabilities on your server and it helps you fix them.

== Description ==

**Features:**

* Run a Complete Scan to automatically remove known security threats and backdoor scripts.
* Firewall block SoakSoak and other malware from exploiting Revolution Slider and other plugins from known vulnerabilites.
* Upgrade vulnerable versions of timthumb scripts.
* Download Definition Updates to protect against new threats.

**Premium Features:**

* Patch your wp-login and XMLRPC to block Brute-Force and DDoS attacks.
* Check the integrity of your WordPress Core files.
* Automatically download new Definition Updates when running a Complete Scan.

Updated February 19th

Register this plugin at [GOTMLS.NET](http://gotmls.net/) and get access to new definitions of "Known Threats" and added features like Automatic Removal, plus patches for specific security vulnerabilities like old versions of timthumb. Updated definition files can be downloaded automatically within the admin once your Key is registered. Otherwise, this plugin just scans for "Potential Threats" and leaves it up to you to identify and remove the malicious ones.

NOTICE: This plugin make call to GOTMLS.NET to check for updates not unlike what WordPress does when checking your plugins and themes for new versions. Staying up-to-date is an essential part of any security plugin and this plugin can let you know when there are new plugin and definition update available. If you're allergic to "phone home" scripts then don't use this plugin (or WordPress at all for that matter).

**Special thanks to:**

* Clarus Dignus for design suggestions and graphic design work on the banner image.
* Jelena Kovacevic and Andrew Kurtis of webhostinghub.com for providing the Spanish translation.
* Marcelo Guernieri for the Brazilian Portuguese translation.
* Umut Can Alparslan for the Turkish translation.

== Installation ==

1. Download and unzip the plugin into your WordPress plugins directory (usually `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' menu in your WordPress Admin.
1. Register on gotmls.net and download the newest definition updates to scan for Known Threats.

== Frequently Asked Questions ==

= Why should I register? =

If you register on [GOTMLS.NET](http://gotmls.net/) you will have access to download definitions of New Threats and added features like automatic removal of "Known Threats" and patches for specific security issues like old versions of timthumb and brute-force attacks on wp-login.php. Otherwise, this plugin only scans for "Potential Threats" on your site, it would then be up to you to identify the good from the bad and remove them accordingly. 

= How do I patch the Revolution Slider vulnerability? =

Easy, if you have installed and activated my this Anti-Malware plugin on your site then it will automatically block attempts to exploit the Revolution Slider vulnerability.

= How do I patch the wp-login vulnerability? =

The WordPress Login page is susceptible to a brute-force attack (just like any other login page). These types of attacks are becoming more prevalent these days and can sometimes cause your server to become slow or unresponsive, even if the attacks do not succeed in gaining access to your site. This plugin can apply a patch that will block access to the WordPress Login page whenever this type of attack is detected. Just click the Install Patch button under Brute-force Protection on the Anti-Malware Setting page. For more information on this subject [read my blog](http://gotmls.net/tag/wp-login-php/).

= Why can't I automatically remove the "Potential Threats" in yellow? =

Many of these files may use eval and other powerful PHP function for perfectly legitimate reasons and removing that code from the files would likely cripple or even break your site so I have only enabled the Auto remove feature for "Know Threats".

= How do I know if any of the "Potential Threats" are dangerous? =

Click on the linked filename to examine it, then click each numbered link above the file content box to highlight the suspicious code. If you cannot tell whether or not the code is malicious just leave it alone or ask someone else to look at it for you. If you find that it is malicious please send me a copy of the file so that I can add it to my definition update as a "Know Threat", then it can be automatically removed.

= What if the scan gets stuck part way through? =

First just leave it for a while. If there are a lot of files on your server it could take quite a while and could sometimes appear to not be moving along at all even if it really is working. If it still seems stuck after a while then try running the scan again, be sure you try both the Complete Scan and the Quick scan.

= How did I get hacked in the first place? =

First, don't take the attack personally. Lots of hackers routinely run automated script that crawl the internet looking for easy targets. Your site probably got hacked because you are unknowingly an easy target. This might be because you are running an older version of WordPress or have installed a Plugin or Theme with a backdoor or known security vulnerability. However, the most common type of infection I see is cross-conamination. This can happen when your site is on a shared server with other exploitable sites that got infected. In most shared hosting environments it's possible for hackers to use an one infected site to infect other sites on the same server, sometimes even if the sites are on different accounts.

= What can I do to prevent it from happening again? =

There is no sure way to protect your site from every kind of hack attempt. That said, don't be an easy target. Some basic steps should include: hardening your password, keeping all your sites up-to-date, and run regular scans with Anti-Malware software like [GOTMLS.NET](http://gotmls.net/)

= Why does sucuri.net or the Google Safe Browsing Diagnostic page still say my site is infected after I have removed the malicious code? =

sucuri.net caches their scan results and will not refresh the scan until you click the small link near the bottom of the page that says "Force a Re-scan" to clear the cache. Google also caches your infected pages and usually takes some time before crawling your site again, but you can speed up that process by Requesting a Review in the Malware or Security section of [Google Webmaster Tools](https://www.google.com/webmasters/tools/). It is a good idea to have a Webmaster Tools account for your site anyway as it can provide lots of other helpful information about your site.

== Screenshots ==

1. The menu showing Anti-Malware options.
2. The Scan Setting page in the admin.
3. An example scan that found some threats.
4. The results window when "Automatic Repair" fixes threats.
5. The Quarantine showing threats that have been fix already.

== Changelog ==

= 4.17.58 =
* Updated code for compatibility with WP 4.9.4 (latest release).
* Fixed dashicons sizing in css.
* Add ability to update registration email from within the plugin settings.
* Cleaned up expired nonce tokens left behind from an older version.

= 4.17.57 =
* Updated code for compatibility with WP 4.9.3 (latest release).
* Fixed registration form and alternate domain for definition updates to work on HTTPS.
* Fixed the wording on the Title check error message.

= 4.17.44 =
* Added Title check to make sure it does say you were hacked.
* Updated code for compatibility with WP 4.8.3 (latest release).
* Fixed Undefined variable error in Quarantine.
* Fixed XSS vulnerability in nonce error output.

= 4.17.29 =
* Changed the definition update URL to only use SSL when required.
* Updated PayPal form for better domestic IPN compatibility.

= 4.17.28 =
* Added the Turkish translation thanks to Umut Can Alparslan.
* Improved the auto update so that old definitions could be phased out and new threat types would be selected by default.
* Fixed the admin username change feature on multisite installs.

= 4.16.53 =
* Fixed the details window so that it scrolls to the highlighted code.
* Set defaults to disable the Potential Threat scan if other threats definitions are enabled.
* Encoded definitions array for DB storage.

= 4.16.49 =
* Fixed syntax error in the XMLRPC patch for newer versions of Apache.

= 4.16.48 =
* Added fall-back to manual updates if the Automatic update feature fails.
* Fixed PHP Notices about undefined variable added in last Version release.
* Improved Apache version detection.

= 4.16.47 =
* Changed Automatic update feature to automatically download all definitions and firewall updates.
* Added PHP and Apache version detections and changed the XMLRPC patch to work with Apache 2.4 directives.
* Removed the onbeforeunload function because Norton detected it as a False Positive.
* Removed code that was deprecated in PHP Version 7.

= 4.16.39 =
* Fixed PHP Notice about an array to string conversion with some rare global variable conditions.

= 4.16.38 =
* Added more firewall options.
* Moved Scan Log from the Quarantine page to the main Setings page.
* Fixed PHP Warning about an invalid argument in foreach and some other bugs too.

= 4.16.26 =
* Fixed "What to look for" Options so that changes are saved.
* Changed get_currentuserinfo to wp_get_current_user because the get_currentuserinfo function was deprecated in WP 4.5

= 4.16.17 =
* Removed Menu Item Placement Options because the add_object_page function was deprecated in WP 4.5.
* Added firewall options for better compatibility with WP Firewall 2.
* Fixed an XSS vulnerability in the debug output of the nonce token.

= 4.15.49 =
* Moved the Firewall Options to it's own page linked to from the admin menu.
* Moved the Quick Scan from the admin menu to the top of the Scan Settings page.

= 4.15.46 =
* Fixed PHP Warning about in_array function expecting parameter 2 to be an array, found by Georgey B.
* Made a few minor cosmetic changes and fixed a few other small bugs in the interface.

= 4.15.45 =
* Fixed the Nonce Token error caused by W3 Total Cache breaking the set_transient function in WordPress.
* Added the Brazilian Portuguese language files, thanks to Marcelo Guernieri for the translation.

= 4.15.44 =
* Fixed the admin menu and also some links that did not work on Windows server.

= 4.15.43 =
* Added Core Files to the Quick Scan list on the admin menu.
* Added a nonce token to prevent Cross-Site Request Forgery by admins who are logged-in from another site.
* Hardened against XSS vulnerability triggered by the file names being scanned (thanks to Mahadev Subedi).
* Improved brute-force patch compatibility with alternate wp-config.php location.

= 4.15.42 =
* Had to remove the encoding of the Default Definitions to meet the WordPress Plugin Guidelines.

= 4.15.41 =
* Improved the JavaScript in the new Brute-Force login patch so that it works with caching enabled on the login page.

= 4.15.40 =
* Improved the Brute-Force login patch with custom fields and JavaScript. 
* Added a Save button to that Scan Settings page.
* Fixed a bug in the XMLRPC Patch "Unblock" feature.

= 4.15.30 =
* Added a link to purge the deleted Quarantine items from the database.
* Added firewall option to Block all XMLRPC calls.
* Fixed a few cosmetic bugs in the quarantine and firewall options.

= 4.15.29 =
* Fixed a bugs in the Quarantine that was memory_limit errors if there number of files in the was too high.
* Added the highlight malicious code feature back to the Quarantine file viewer.
* Added the ability to change the admin username if the current username is "admin".
* Improved the code in the Brute-Force Protection patch.

= 4.15.28 =
* Fixed a few bugs in the Core Files Check that was preventing it from fixing some unusual file modifications.

= 4.15.27 =
* Fixed a major bug that made multisite scan extremely slow and sometimes error out.
* Moved all ajax call out of the init function and into their own functions for better handling time.

= 4.15.26 =
* Moved the quarantine files into the database and deleted the old directory in uploads.
* Fixed some minor formatting issues in the HTML output on the settings page.
* Added a warning message if base64_decode has been disabled.

= 4.15.24 =
* Hardened against injected HTML content by encoding the tags with variables.
* Fixed debug option to exclude individual definitions.

= 4.15.23 =
* Hardened admin_init with current_user_can and realpath on the quarantine file deletion (thanks to J.D. Grimes).
* Fixed another XSS vulnerabilities in the admin (thanks to James H.)

= 4.15.20 =
* Hardened against XSS vulnerabilities in the admin (thanks to Tim Coen).
* Added feature to restore default settings for Exclude Extensions.
* Changed the encoding on the index.php file in the Quarantine to make it more human-readable.
* Fixed a few small bugs that were throwing PHP Notices in some configurations and added more info to some error messages.

= 4.15.17 =
* Extended execution_time during the Fix process to increase the number of files that could be fixed at a time.
* Added a Quarantine log to the database.
* Fixed a couple of minor bugs that would throw PHP notices.

= 4.15.16 =
* Created an automatic update feature that downloads any new definition updates before starting the scan.
* Added WordPress Core files to the new definitions update process and included a scan option to check the integrity of the Core files.
* Automatically whitelisted the unmodified WordPress Core files.
* Made more improvements to the Brute-Force protection patch and other minor cosmetic changes to the interface.
* Protected the HTML in my plugin from filter injections and fixed a few other minor bugs.

= 4.14.65 =
* Fixed a problem with deleting files from the Quarantine folder.
* Added a descriptive reason to the error displayed if the fix was unsuccessful.
* Added link to restore the default location of the Examine Results window.

= 4.14.64 =
* Improved the encoding of definition updates so that they would not be blocked by poorly written firewall rules.
* Suppressed the "Please make a donation" nag if the fix was unsuccessful, to avoid confusion over premium services.

= 4.14.63 =
* Removed debug alert from initial session check.

= 4.14.62 =
* Improved rewrite compatibility of session check for the Brute-Force Protection Installation.

= 4.14.59 =
* Improved session check for the option to Install Brute-Force Protection and added an error message on failure.
* Improved support for Multisite by only allowing Network Admins access to the Anti-Malware menu.

= 4.14.55 =
* Added link to view a simple scan history on the Quarantine page.
* Updated firewall to better protect agains new variations of the RevSlider Exploit.
* Improved check for session support before giving the option to Install Brute-Force patch.

= 4.14.54 =
* Added option to skip scanning the Quarantined files.
* Updated Brute-Force patch to fix the problem of being included more that once.
* Fixed a few minor bugs (better window positioning and css, cleaner results page, updated new help tab, etc.).
* Made sure that the plugin does not check my servers for updates unless you have registered (this opt-in requirement is part of the WordPress Repository Guidelines).

= 4.14.52 =
* Added exception for the social.png files to the skip files by extension list.
* Fixed removal of Known Threats from files in the Quarantine directory.

= 4.14.51 =
* Block SoakSoak and other malware from exploiting the Slider Revolution Vulnerability (THIS IS A WIDESPREAD THREAT RIGHT NOW).

= 4.14.50 =
* Enabled the Brute-Force protection option directly from the Settings page.
* Fixed window position to auto-adjust on small screens.

= 4.14.47 =
* Major upgrade to the protection for wp-login.php Brute-Force attempts.
* Fixes a bug in setting the permissions for read-only files so that they could still be cleaned.
* Fixes a minor bug with pass-by-reference which raises a fatal error in PHP v5.4.
* Enhanced the Examine File window with better styles and more info.
* Changed form submission of encrypted file lists to array values instead of keys.
* Fixes other minor bugs.
* Made the Examine File window sizable.
* Fixed a few small bugs and removed some old code.
* Added a link to my new twitter account.
* Re-purposed Quick Scan to just scan the most affected areas.
* Set the registration form to display by defaulted in the definition update section.
* Fixed a few small bugs in advanced features and directory depth determination.
* Fixed a session bug to display the last directory scanned.
* Fixed a few small cosmetic bugs for WP 3.8.
* Added Spanish translation, thanks to Jelena Kovacevic and Andrew Kurtis at webhostinghub.com.
* Updated string in the code and added a .pot file to be ready for translation into other languages.
* Added "Select All" checkbox to Quarantine and a new button to delete items from the Quarantine.
* Added a trace.php file for advanced session tracking.
* Fixed undefined index bug with menu_group item in settings array.
* Added support for multisite network admin menu and the ability to restrict admin access.
* Fixed a session bug in the progress bar related to the last release.
* Fixed a session bug that conflicted with jigoshop. (Thanks dragonflyfla)
* Fixed a few bug in the Whitelist definition feature.

= 3.07.06 =
* Added SSL support for definition updates and registration form.
* Upgraded the Whitelist feature so the it could not contain duplicates.
* Downgraded the WP-Login threat and changed it to an opt-in fix.
* Fixed a bug in the Add to Whitelist feature so the you do not need to update the definitions after whitelisting a file.
* Added ability to whitelist files.
* Fixed a major bug in yesterdays release broke the login page on some sites.
* Added a patch for the wp-login.php brute force attack that has been going around.
* Created a process to restore files from the Quarantine.
* Fixed a few other small bugs including path issues on Winblows server.

= 1.3.02.15 =
* Improved security on the Quarantine directory to fix the 500 error on some servers.
* Fixed count of Quarantined items.
* Added htaccess security to the Uploads directory.
* Linked the Quarantined items to the File Examiner.
* Added a scan category for Backdoor Scripts.
* Consolidated the Definition Types and added a Whitelist category.
* Completely redesigned the Definition Updates to handle incremental updates.
* Added "View Quarantine" to the menu.
* Enhanced Output Buffer to work with compression enabled (like ob_gzhandler).
* Moved the quarantine to the uploads directory to protect against blanket inclusion.
* Fixed Output Buffer issue for when ob_start has already been called.
* Enhanced the Automatic Fix process to handle bad directory permissions.
* Added more detailed error messages for different types of file errors.
* Improved overall error handling.
* Minor UI enhancements and a few bug fixes.
* Completely revamped the scan engine to handle large file systems with better error handling.
* Enhanced the results for the Automatic Fix process.
* Fixed a few other small bugs.
* Enhanced the iFrame for the File Viewer and Automatic Fix process.
* Improved error handling during the scan.
* Moved the File Viewer and Automatic Fix process into an iFrame to decrease scan time and memory usage.
* Enhanced the Automatic Fix process for better success with read-only files.
* Improved code cleanup process and general efficiency of the scan.
* Encoded definition update for better compatibility with some servers that have post limitation.
* Fixed XSS vulnerability.
* Changed registration to allow for multiple sites/keys to be registered under one user/email.
* Changed auto-update path to update threat level array for all new definition updates.
* Updated timthumb replacement patch to version 2.8.10 per WordPress.org plugins requirement.
* Fixed option to exclude directories so that the scan would not get stuck if omitted.
* Added support for winblows servers using BACKSLASH directory structures.
* Changed definition updates to write to the DB instead of a file.

= 1.2.03.23 =
* First versions available for WordPress (code removed, no longer compatible).

== Upgrade Notice ==

= 4.17.58 =
Updated code for compatibility with WP 4.9.4, fixed dashicons sizing in css, add ability to update registration email from within the plugin settings, and cleaned up expired nonce tokens left behind from an older version.

= 4.17.57 =
Updated code for compatibility with WP 4.9.3, fixed registration form and alternate domain for definition updates to work on HTTPS, and fixed the wording on the Title Check error message.

= 4.17.44 =
Added Title check to make sure it does say you were hacked, updated code for compatibility with WP 4.8.3 and fixed Undefined variable error in Quarantine and an XSS vulnerability in nonce error output.

= 4.17.29 =
Changed the definition update URL to only use SSL when required, and updated PayPal form for better domestic IPN compatibility.

= 4.17.28 =
Added the Turkish translation thanks to Umut Can Alparslan, improved the auto update feature, and fixed the admin username change feature on multisite installs.

= 4.16.53 =
Fixed the details window to scrolls to the highlighted code, set default Potential Threat scan to disabled, and encoded definitions array for DB storage.

= 4.16.49 =
Fixed syntax error in the XMLRPC patch for newer versions of Apache.

= 4.16.48 =
Added fall-back to manual updates if the Automatic update feature fails, fixed PHP Notices  and improved Apache version detection.

= 4.16.47 =
Changed Automatic update feature, added PHP and Apache version detections, and removed the onbeforeunload function other code that was deprecated.

= 4.16.39 =
Fixed PHP Notice about an array to string conversion with some rare global variable conditions.

= 4.16.38 =
Added more firewall options, moved Scan Log from to the main Setings page, and fixed PHP Warning about an invalid argument and some other bugs too.

= 4.16.26 =
Fixed "What to look for" Options so that changes are saved, and changed get_currentuserinfo to wp_get_current_user.

= 4.16.17 =
Removed Menu Item Placement Options that were deprecated in WP 4.5, Added firewall options for better compatibility with WP Firewall 2, and fixed an XSS vulnerability in the debug output of the nonce token.

= 4.15.49 =
Moved the Firewall Options to it's own page and moved the Quick Scan to the top of the Scan Settings page.

= 4.15.46 =
Made a few minor cosmetic changes and fixed a few small bugs including a PHP Warning about in_array function expecting parameter 2 to be an array.

= 4.15.45 =
Fixed the Nonce Token error caused by W3 Total Cache, and added the Brazilian Portuguese translation by Marcelo Guernieri.

= 4.15.44 =
Fixed the admin menu and also some links that did not work on Windows server.

= 4.15.43 =
Improved brute-force patch compatibility, added Core Files to the Quick Scan list, added a nonce token to prevent Cross-Site Request Forgery by admins who are logged-in, and hardened against XSS vulnerability triggered by bad file names.

= 4.15.42 =
Had to remove the encoding of the Default Definitions to meet the WordPress Plugin Guidelines.

= 4.15.41 =
Improved the JavaScript in the new Brute-Force login patch so that it works with caching enabled on the login page.

= 4.15.40 =
Improved the Brute-Force login patch with custom fields and JavaScript, added a Save button to that Scan Settings page, and fixed a bug in the XMLRPC Patch.

= 4.15.30 =
Added a new firewall option to Block all XMLRPC calls and a link to purge the deleted Quarantine items from the database, and fixed a few cosmetic bugs in the quarantine and firewall options.

= 4.15.29 =
Fixed a bugs in the Quarantine, added the highlight malicious code feature back to the Quarantine file viewer, added the ability to change the admin username, and improved the Brute-Force Protection.

= 4.15.28 =
Fixed a few bugs in the Core Files Check that was preventing it from fixing some unusual file modifications.

= 4.15.27 =
Fixed a major bug that made multisite scan extremely slow and moved all ajax call out of the init function and into their own functions.

= 4.15.26 =
Moved the quarantine files into the database and deleted the old directory in uploads, fixed some minor HTML formatting issues, and added a warning if base64_decode is disabled.

= 4.15.24 =
Hardened against injected HTML content and fixed debug option to exclude individual definitions.

= 4.15.23 =
Fixed another XSS vulnerabilities in the admin (thanks to James H.), and hardened admin_init with current_user_can and realpath on the quarantine file deletion (thanks to J.D. Grimes).

= 4.15.20 =
Hardened against XSS in the admin, changed encoding of the index.php file in the Quarantine, added more info to some error messages and a feature to restore a default setting, and fixed a few small bugs.

= 4.15.17 =
Extended execution_time during the Fix process, added a Quarantine log to the database, and fixed a couple of minor bugs.

= 4.15.16 =
Created automatic definition updates that include WordPress Core files for integrity checking and whitelisting, made more improvements to the Brute-Force protection patch, and a few other cosmetic changes and minor bug fixes.

= 4.14.65 =
Fixed a problem with deleting files from the Quarantine folder, added more descriptive errors and a link to restore the default location of the Examine Results window.

= 4.14.64 =
Improved the encoding of definition updates and suppressed the "Please make a donation" nag if the fix was unsuccessful.

= 4.14.63 =
Removed debug alert from initial session check.

= 4.14.62 =
Improved rewrite compatibility of session check for the Brute-Force Protection Installation.

= 4.14.59 =
Improved session check for the Brute-Force Protection and support for Multisite menu.

= 4.14.55 =
Added link to scan history, improved check for session support before giving installing Brute-Force patch, and updated firewall to better protect agains the RevSlider Exploit.

= 4.14.54 =
Added option to skip scanning the Quarantine, updated Brute-Force patch, and fixed a few minor bugs.

= 4.14.52 =
Added exception for the social.png files to the skip files by extension list, and fixed removal of Known Threats from files in the Quarantine directory.

= 4.14.51 =
Block SoakSoak and other malware from exploiting the Slider Revolution Vulnerability (THIS IS A WIDESPREAD THREAT RIGHT NOW).

= 4.14.50 =
Enabled the Brute-Force protection from the Settings page and fixed window position on small screens.

= 4.14.47 =
Major upgrade to the protection for Brute-Force attempts, and a bug fix for resetting the permissions of read-only files (Plus many other improvement from v3.X: see Changelog for details).

= 3.07.06 =
Added SSL support for definition updates and upgraded the Whitelist feature (Plus many other improvement from v1.3: see Changelog for details).

= 1.3.02.15 =
Improved security on the Quarantine directory to fix the 500 error on some servers (Plus many other improvement from v1.2: see Changelog for details).

= 1.2.03.23 =
First versions available for WordPress (code removed, no longer compatible).
