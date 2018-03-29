=== Exploit Scanner ===
Contributors: donncha, duck_, ryan, azaozz, tott, pento, philipjohn
Tags: security, scanner, hacking, spam, hack, crack, exploit, vulnerability
Tested up to: 4.7.5
Stable tag: 1.5.2
Requires at least: 3.3

Search the files and database of your WordPress install for signs that may indicate that it has fallen victim to malicious hackers.

== Description ==
This plugin searches the files on your website, and the posts and comments tables of your database for anything suspicious. It also examines your list of active plugins for unusual filenames.

It does not remove anything. That is left to the user to do.

Latest MD5 hash values for Exploit Scanner:

* 17e2ccfc834d691bc68cc5c64f9bed89  exploit-scanner.php (1.5.2)
* 1d5f9d6220fe159cd44cb70a998a1cd7  hashes-4.6.php
* fbdf61c17f65094c8e331e1e364acf68  hashes-4.6.1.php
* 477d128d84802e3470cec408424a8de3  hashes-4.7.php
* d53210f999847fbd6f5a2ecac0ad42f2  hashes-4.7.5.php

Latest SHA1 hash values for Exploit Scanner:

* 1decc1e47a53d1cab9e8f1ef15b31682198367ee  exploit-scanner.php (1.5.2)
* 5cec64380a2acdc876fd22fbbbbf8c335df1ed3f  hashes-4.6.php
* 99d9e7be23a350f3d1962d0f41e7b4e28c00841e  hashes-4.6.1.php
* 1eeab377a1afc6d776827a063678d2461b29e71d  hashes-4.7.php
* 8c890a6af26bb74e9d17e5d2b21d6be27764da45  hashes-4.7.5.php

See the [Exploit Scanner homepage](http://ocaoimh.ie/exploit-scanner/) for further information.

== Upgrade Notice ==

* Added hashes for WordPress 4.7.5

== Changelog ==

= 1.5.2 =

* Added hashes for WordPress 4.7.5

= 1.5.1 =

* WordPress 4.6 hashes
* WordPress 4.6.1 hashes
* WordPress 4.7 hashes

= 1.5 =

* WordPress 4.5.3 hashes
* Move to follow WP versioning system

= 1.4.12 =

* WordPress 4.5.2 hashes

= 1.4.11 =

* WordPress 4.5 hashes
* WordPress 4.5.1 hashes

= 1.4.10 =

* WordPress 4.4.1 hashes

= 1.4.9 =

* WordPress 4.4 hashes

= 1.4.8 =
* WordPress 4.3.1 security release hashes
* Other missing hashes

= 1.4.7 =
* WordPress 4.3 hashes

= 1.4.6 =
* WordPress 4.2.3 hashes
* WordPress 4.2.4 hashes

= 1.4.5 =
* WordPress 4.2.2 hashes

= 1.4.4 =
* WordPress 3.7.3 hashes
* WordPress 3.7.4 hashes
* WordPress 3.7.5 hashes
* WordPress 3.7.6 hashes
* WordPress 3.7.7 hashes
* WordPress 3.8.4 hashes
* WordPress 3.8.5 hashes
* WordPress 3.8.6 hashes
* WordPress 3.8.7 hashes
* WordPress 3.9.4 hashes
* WordPress 3.9.5 hashes
* WordPress 4.0.2 hashes
* WordPress 4.0.3 hashes
* WordPress 4.0.4 hashes
* WordPress 4.1.4 hashes
* WordPress 4.2.1 hashes

= 1.4.3 =
* WordPress 4.1.3 hashes

= 1.4.2 =
* WordPress 4.2 hashes

= 1.4.1 =
* WordPress 3.9.3, 4.1, 4.1.1 and 4.1.2 hashes

= 1.4 =
* Remove an example link to a hacked site
* Fixed the eval() check incorrectly matching function names that end in "eval"
* Fixed some PHP warnings
* WordPress 3.5.2 hashes
* WordPress 3.6 and 3.6.1 hashes
* Wordpress 3.7, 3.7.1 and 3.7.2 hashes
* Wordpress 3.8, 3.8.1, 3.8.2 and 3.7.3 hashes
* Wordpress 3.9, 3.9.1 and 3.9.2 hashes
* Wordpress 4.0 and 4.0.1 hashes

= 1.3.3 =
* WordPress 3.5 and 3.5.1 hashes

= 1.3.2 =
* WordPress 3.4.2 hashes

= 1.3.1 =
* WordPress 3.4.1 hashes

= 1.3 =
* Detect unknown files in the wp-admin and wp-includes directories
* WordPress 3.4 hashes

= 1.2.1 =
* WordPress 3.3.2 hashes

= 1.2 =
* WordPress 3.3.1 hashes
* Use help tabs introduced in WordPress 3.3
* Help prevent one cause of hanging scans (MySQL error 1153)

= 1.1 =
* Scan for and fix old, vulnerable TimThumb scripts
* Detect old export files even if they're larger than the size limit
* WordPress 3.3 hashes

= 1.0.5 =
* WordPress 3.2 and 3.2.1 hashes

= 1.0.4 =
* WordPress 3.1.4 hashes
* Suspicious pattern updates and tweaks

= 1.0.3 =
* Detection of export files left by incomplete imports.
* WordPress 3.1.3 hashes

= 1.0.2 =
* WordPress 3.0.6 and 3.1.2 hashes

= 1.0.1 =
* WordPress 3.1.1 hashes

= 1.0 =
* Core file diffs
* WordPress 3.1 hashes
* Updated suspicious patterns

= 0.97.6 =
* WordPress 3.0.5 hashes

= 0.97.5 =
* WordPress 3.0.4 hashes
* Dropped wp-content from hashes

= 0.97.4 =
* WordPress 3.0.3 compatibility

= 0.97.3 =
* 3.0.2 compatibility

= 0.97.2 =
* 3.0.1 compatibility

= 0.97.1 =
* PHP 4 compatibility

= 0.97 =
* AJAX paging
* simplified results system (now only 3 levels)
* contextual help
* moved to Tools menu section
* a number of backend changes

= 0.96 =
* Compatibility for WordPress 3.0

= 0.95 =
* Added "exploits" scan level for obvious hacker exploit code.
* Stored results for later review.
* Rearranged layout of results.
* Paged scanning so plugin scans 50 files at a time to avoid timeout errors.
* Only show "General Info" to non MU sites (it's too expensive for large MU sites)

== Installation ==
1. Download and unzip the plugin.
2. Copy the exploit-scanner directory into your plugins folder.
3. Visit your Plugins page and activate the plugin.
4. A new menu item called "Exploit Scanner" will be available under the Tools menu.

== Frequently Asked Questions ==

= How do I fix the out of memory error? =
Scanning your website can take quite a bit of memory. The plugin tries to allocate 128MB but sometimes that's not enough. You can modify the amount of memory PHP has access to from within the plugin admin page. You can also limit the max size of scanned files. Reduce this number to skip more files but be aware that it may miss hacked files. Any skipped files are listed after scanning. Memory is also used if you have deep directories because of the way the scanner works. It will help if you clean out any cache directories (wp-content/cache/ for example) before scanning.

== Interpreting the Results ==
It is likely that this scanner will find false positives (i.e. files which do not contain malicious code). However, it is best to err
on the side of caution; if you are unsure then ask in the [Support Forums](http://wordpress.org/support/),
download a fresh copy of a plugin, search the Internet for similar situations, et cetera. You should be most concerned if the scanner is:
making matches around unknown external links; finding base64 encoded text in modified core files or the `wp-config.php` file;
listing extra admin accounts; or finding content in posts which you did not put there.

Understanding the three different result levels:

* **Severe:** results that are often strong indicators of a hack (though they are not definitive proof)
* **Warning:** these results are more commonly found in innocent circumstances than Severe matches, but they should still be treated with caution
* **Note:** lowest priority, showing results that are very commonly used in legitimate code or notifications about events such as skipped files

== Help! I think I have been hacked! ==
Follow the guides from the Codex:

* [Codex: FAQ - My site was hacked](http://codex.wordpress.org/FAQ_My_site_was_hacked)
* [Codex: Hardening WordPress](http://codex.wordpress.org/Hardening_WordPress)

Ensure that you change **all** of your WordPress related passwords (site, FTP, MySQL, etc.). A regular backup routine
(either manual or plugin powered) is extremely useful; if you ever find that your site has been hacked you can easily restore your site from
a clean backup and fresh set of files and, of course, use a new set of passwords.

== Updates ==
Updates to the plugin will be posted here, to [Holy Shmoly!](http://ocaoimh.ie/) and the [WordPress Exploit Scanner](http://ocaoimh.ie/exploit-scanner/) page will always link to the newest version.

== Other Languages ==
Unfortunately for people using WordPress versions for other locales some of the file hashes may be incorrect as some strings have to be hardcoded in their translated form. Here are some file hashes for WordPress in other languagues provided separately by other members of the community:

* [Japanese](http://wpbiz.jp/files/exploit-scanner-hashes/ja/) - thanks to Naoko
* [German](http://talkpress.de/artikel/exploit-scanner-hash-deutsch-wordpress) - thanks to Robert Wetzlmayr

The hash files should only be declaring an array called $filehashes and the majority of the hashes should still be the same.
