  WordPress › ReadMe 

[![WordPress](wp-admin/images/wordpress-logo.png)](https://wordpress.org/)
==========================================================================

Semantic Personal Publishing Platform

First Things First
------------------

Welcome. WordPress is a very special project to me. Every developer and contributor adds something unique to the mix, and together we create something beautiful that I am proud to be a part of. Thousands of hours have gone into WordPress, and we are dedicated to making it better every day. Thank you for making it part of your world.

— Matt Mullenweg

Installation: Famous 5-minute install
-------------------------------------

1.  Unzip the package in an empty directory and upload everything.
2.  Open [wp-admin/install.php](wp-admin/install.php) in your browser. It will take you through the process to set up a `wp-config.php` file with your database connection details.
    1.  If for some reason this does not work, do not worry. It may not work on all web hosts. Open up `wp-config-sample.php` with a text editor like WordPad or similar and fill in your database connection details.
    2.  Save the file as `wp-config.php` and upload it.
    3.  Open [wp-admin/install.php](wp-admin/install.php) in your browser.
3.  Once the configuration file is set up, the installer will set up the tables needed for your site. If there is an error, double check your `wp-config.php` file, and try again. If it fails again, please go to the [WordPress support forums](https://wordpress.org/support/forums/) with as much data as you can gather.
4.  **If you did not enter a password, note the password given to you.** If you did not provide a username, it will be `admin`.
5.  The installer should then send you to the [login page](wp-login.php). Sign in with the username and password you chose during the installation. If a password was generated for you, you can then click on “Profile” to change the password.

Updating
--------

### Using the Automatic Updater

1.  Open [wp-admin/update-core.php](wp-admin/update-core.php) in your browser and follow the instructions.
2.  You wanted more, perhaps? That’s it!

### Updating Manually

1.  Before you update anything, make sure you have backup copies of any files you may have modified such as `index.php`.
2.  Delete your old WordPress files, saving ones you’ve modified.
3.  Upload the new files.
4.  Point your browser to [/wp-admin/upgrade.php](wp-admin/upgrade.php).

Migrating from other systems
----------------------------

WordPress can [import from a number of systems](https://developer.wordpress.org/advanced-administration/wordpress/import/). First you need to get WordPress installed and working as described above, before using [our import tools](wp-admin/import.php).

System Requirements
-------------------

*   [PHP](https://www.php.net/) version **7.2.24** or greater.
*   [MySQL](https://www.mysql.com/) version **5.5.5** or greater.

### Recommendations

*   [PHP](https://www.php.net/) version **7.4** or greater.
*   [MySQL](https://www.mysql.com/) version **8.0** or greater OR [MariaDB](https://mariadb.org/) version **10.5** or greater.
*   The [mod\_rewrite](https://httpd.apache.org/docs/2.2/mod/mod_rewrite.html) Apache module.
*   [HTTPS](https://wordpress.org/news/2016/12/moving-toward-ssl/) support.
*   A link to [wordpress.org](https://wordpress.org/) on your site.

Online Resources
----------------

If you have any questions that are not addressed in this document, please take advantage of WordPress’ numerous online resources:

[HelpHub](https://wordpress.org/documentation/)

HelpHub is the encyclopedia of all things WordPress. It is the most comprehensive source of information for WordPress available.

[The WordPress Blog](https://wordpress.org/news/)

This is where you’ll find the latest updates and news related to WordPress. Recent WordPress news appears in your administrative dashboard by default.

[WordPress Planet](https://planet.wordpress.org/)

The WordPress Planet is a news aggregator that brings together posts from WordPress blogs around the web.

[WordPress Support Forums](https://wordpress.org/support/forums/)

If you’ve looked everywhere and still cannot find an answer, the support forums are very active and have a large community ready to help. To help them help you be sure to use a descriptive thread title and describe your question in as much detail as possible.

[WordPress IRC (Internet Relay Chat) Channel](https://make.wordpress.org/support/handbook/appendix/other-support-locations/introduction-to-irc/)

There is an online chat channel that is used for discussion among people who use WordPress and occasionally support topics. The above wiki page should point you in the right direction. ([irc.libera.chat #wordpress](https://web.libera.chat/#wordpress))

Final Notes
-----------

*   If you have any suggestions, ideas, or comments, or if you (gasp!) found a bug, join us in the [Support Forums](https://wordpress.org/support/forums/).
*   WordPress has a robust plugin API (Application Programming Interface) that makes extending the code easy. If you are a developer interested in utilizing this, see the [Plugin Developer Handbook](https://developer.wordpress.org/plugins/). You shouldn’t modify any of the core code.

Share the Love
--------------

WordPress has no multi-million dollar marketing campaign or celebrity sponsors, but we do have something even better—you. If you enjoy WordPress please consider telling a friend, setting it up for someone less knowledgeable than yourself, or writing the author of a media article that overlooks us.

WordPress is the official continuation of [b2/cafélog](https://cafelog.com/), which came from Michel V. The work has been continued by the [WordPress developers](https://wordpress.org/about/). If you would like to support WordPress, please consider [donating](https://wordpress.org/donate/).

License
-------

WordPress is free software, and is released under the terms of the GPL (GNU General Public License) version 2 or (at your option) any later version. See [license.txt](license.txt).
