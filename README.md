## Synopsis

### Version 4.3

Semantic Personal Publishing Platform.

WordPress is web software you can use to create a beautiful website or blog.

## First Things First

Welcome. WordPress is a very special project to me. Every developer and contributor adds something unique to the mix, and together we create something beautiful that I’m proud to be a part of. Thousands of hours have gone into WordPress, and we’re dedicated to making it better every day. Thank you for making it part of your world.

— Matt Mullenweg

## Installation: Famous 5-minute install

- Unzip the package in an empty directory and upload everything.
- Open [wp-admin/install.php](/wp-admin/install.php) in your browser. It will take you through the process to set up a wp-config.php file with your database connection details.
- If for some reason this doesn’t work, don’t worry. It doesn’t work on all web hosts. Open up wp-config-sample.php with a text editor like WordPad or similar and fill in your database connection details.
- Save the file as wp-config.php and upload it.
- Open [wp-admin/install.php](/wp-admin/install.php) in your browser.
- Once the configuration file is set up, the installer will set up the tables needed for your blog. If there is an error, double check your wp-config.php file, and try again. If it fails again, please go to the [support forums](https://wordpress.org/support/) with as much data as you can gather.
- If you did not enter a password, note the password given to you. If you did not provide a username, it will be admin.
- The installer should then send you to the [login page](/wp-login.php). Sign in with the username and password you chose during the installation. If a password was generated for you, you can then click on “Profile” to change the password.

## Updating

### Using the Automatic Updater

If you are updating from version 2.7 or higher, you can use the automatic updater:

- Open [wp-admin/update-core.php](/wp-admin/update-core.php) in your browser and follow the instructions.
- You wanted more, perhaps? That's it!

### Updating Manually

- Before you update anything, make sure you have backup copies of any files you may have modified such as `index.php`.
- Delete your old WordPress files, saving ones you've modified.
- Upload the new files.
- Point your browser to [/wp-admin/upgrade.php](/wp-admin/upgrade.php).

## Migrating from other systems

WordPress can [import from a number of systems](https://codex.wordpress.org/Importing_Content). First you need to get WordPress installed and working as described above, before using [our import tools](/wp-admin/import.php).

## System Requirements

- [PHP](http://php.net/) version _5.2.4_ or higher.
- [MySQL](http://www.mysql.com/) version _5.0_ or higher.

## Recommendations

- [PHP](http://php.net/) version _5.4_ or higher.
- [MySQL](http://www.mysql.com/) version _5.5_ or higher.
- The [mod_rewrite](http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html) Apache module.
- A link to [wordpress.org](https://wordpress.org/) on your site.

## Online Resources

If you have any questions that aren't addressed in this document, please take advantage of WordPress' numerous online resources:
- [The WordPress Codex](https://codex.wordpress.org/) - The Codex is the encyclopedia of all things WordPress. It is the most comprehensive source of information for WordPress available.
- [The WordPress Blog](https://wordpress.org/news/) - This is where you'll find the latest updates and news related to WordPress. Recent WordPress news appears in your administrative dashboard by default.
- [WordPress Planet](https://planet.wordpress.org/) - The WordPress Planet is a news aggregator that brings together posts from WordPress blogs around the web.
- [WordPress Support Forums](https://wordpress.org/support/) - If you've looked everywhere and still can't find an answer, the support forums are very active and have a large community ready to help. To help them help you be sure to use a descriptive thread title and describe your question in as much detail as possible.
- [WordPress IRC Channel](https://codex.wordpress.org/IRC) - There is an online chat channel that is used for discussion among people who use WordPress and occasionally support topics. The above wiki page should point you in the right direction. ([irc.freenode.net #wordpress](irc://irc.freenode.net/wordpress))

## Final Notes

- If you have any suggestions, ideas, or comments, or if you (gasp!) found a bug, join us in the [Support Forums](https://wordpress.org/support/).
- WordPress has a robust plugin API that makes extending the code easy. If you are a developer interested in utilizing this, see the [plugin documentation in the Codex](https://codex.wordpress.org/Plugin_API). You shouldn't modify any of the core code.

## Share the Love

WordPress has no multi-million dollar marketing campaign or celebrity sponsors, but we do have something even better—you. If you enjoy WordPress please consider telling a friend, setting it up for someone less knowledgable than yourself, or writing the author of a media article that overlooks us.

WordPress is the official continuation of [b2/cafélog](http://cafelog.com/), which came from Michel V. The work has been continued by the [WordPress developers](https://wordpress.org/about/). If you would like to support WordPress, please consider [donating](https://wordpress.org/donate/).

## License

WordPress is free software, and is released under the terms of the GPL version 2 or (at your option) any later version. See [license.txt](license.txt).