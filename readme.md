# [![WordPress](wp-admin/images/wordpress-logo.png)](https://wordpress.org/)

Semantic Personal Publishing Platform

## First Things First

Welcome. WordPress is a very special project to me. Every developer and contributor adds something unique to the mix, and together we create something beautiful that I’m proud to be a part of. Thousands of hours have gone into WordPress, and we’re dedicated to making it better every day. Thank you for making it part of your world.

— Matt Mullenweg

## Installation: Famous 5-minute install

1.  Unzip the package in an empty directory and upload everything.
2.  Open <span class="file">[wp-admin/install.php](wp-admin/install.php)</span> in your browser. It will take you through the process to set up a `wp-config.php` file with your database connection details.
    1.  If for some reason this doesn’t work, don’t worry. It doesn’t work on all web hosts. Open up `wp-config-sample.php` with a text editor like WordPad or similar and fill in your database connection details.
    2.  Save the file as `wp-config.php` and upload it.
    3.  Open <span class="file">[wp-admin/install.php](wp-admin/install.php)</span> in your browser.
3.  Once the configuration file is set up, the installer will set up the tables needed for your blog. If there is an error, double check your `wp-config.php` file, and try again. If it fails again, please go to the [support forums](https://wordpress.org/support/ "WordPress support") with as much data as you can gather.
4.  **If you did not enter a password, note the password given to you.** If you did not provide a username, it will be `admin`.
5.  The installer should then send you to the [login page](wp-login.php). Sign in with the username and password you chose during the installation. If a password was generated for you, you can then click on “Profile” to change the password.

## Updating

### Using the Automatic Updater

If you are updating from version 2.7 or higher, you can use the automatic updater:

1.  Open <span class="file">[wp-admin/update-core.php](wp-admin/update-core.php)</span> in your browser and follow the instructions.
2.  You wanted more, perhaps? That’s it!

### Updating Manually

1.  Before you update anything, make sure you have backup copies of any files you may have modified such as `index.php`.
2.  Delete your old WordPress files, saving ones you’ve modified.
3.  Upload the new files.
4.  Point your browser to <span class="file">[/wp-admin/upgrade.php](wp-admin/upgrade.php).</span>

## Migrating from other systems

WordPress can [import from a number of systems](https://codex.wordpress.org/Importing_Content). First you need to get WordPress installed and working as described above, before using [our import tools](wp-admin/import.php "Import to WordPress").

## System Requirements

*   [PHP](https://secure.php.net/) version **5.2.4** or higher.
*   [MySQL](https://www.mysql.com/) version **5.0** or higher.

### Recommendations

*   [PHP](https://secure.php.net/) version **7** or higher.
*   [MySQL](https://www.mysql.com/) version **5.6** or higher.
*   The [mod_rewrite](https://httpd.apache.org/docs/2.2/mod/mod_rewrite.html) Apache module.
*   [HTTPS](https://wordpress.org/news/2016/12/moving-toward-ssl/) support.
*   A link to [wordpress.org](https://wordpress.org/) on your site.

## Online Resources

If you have any questions that aren’t addressed in this document, please take advantage of WordPress’ numerous online resources:

<dl>

<dt>[The WordPress Codex](https://codex.wordpress.org/)</dt>

<dd>The Codex is the encyclopedia of all things WordPress. It is the most comprehensive source of information for WordPress available.</dd>

<dt>[The WordPress Blog](https://wordpress.org/news/)</dt>

<dd>This is where you’ll find the latest updates and news related to WordPress. Recent WordPress news appears in your administrative dashboard by default.</dd>

<dt>[WordPress Planet](https://planet.wordpress.org/)</dt>

<dd>The WordPress Planet is a news aggregator that brings together posts from WordPress blogs around the web.</dd>

<dt>[WordPress Support Forums](https://wordpress.org/support/)</dt>

<dd>If you’ve looked everywhere and still can’t find an answer, the support forums are very active and have a large community ready to help. To help them help you be sure to use a descriptive thread title and describe your question in as much detail as possible.</dd>

<dt>[WordPress <abbr title="Internet Relay Chat">IRC</abbr> Channel](https://codex.wordpress.org/IRC)</dt>

<dd>There is an online chat channel that is used for discussion among people who use WordPress and occasionally support topics. The above wiki page should point you in the right direction. ([irc.freenode.net #wordpress](irc://irc.freenode.net/wordpress))</dd>

</dl>

## Final Notes

*   If you have any suggestions, ideas, or comments, or if you (gasp!) found a bug, join us in the [Support Forums](https://wordpress.org/support/).
*   WordPress has a robust plugin <abbr title="application programming interface">API</abbr> that makes extending the code easy. If you are a developer interested in utilizing this, see the [Plugin Developer Handbook](https://developer.wordpress.org/plugins/). You shouldn’t modify any of the core code.

## Share the Love

WordPress has no multi-million dollar marketing campaign or celebrity sponsors, but we do have something even better—you. If you enjoy WordPress please consider telling a friend, setting it up for someone less knowledgable than yourself, or writing the author of a media article that overlooks us.

WordPress is the official continuation of [b2/cafélog](http://cafelog.com/), which came from Michel V. The work has been continued by the [WordPress developers](https://wordpress.org/about/). If you would like to support WordPress, please consider [donating](https://wordpress.org/donate/ "Donate to WordPress").

## License

WordPress is free software, and is released under the terms of the <abbr title="GNU General Public License">GPL</abbr> version 2 or (at your option) any later version. See [license.txt](license.txt).
