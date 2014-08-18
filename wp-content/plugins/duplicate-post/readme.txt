=== Duplicate Post ===
Contributors: lopo
Donate link: http://lopo.it/duplicate-post-plugin/
Tags: duplicate post, copy, clone
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 2.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Clone posts and pages.

== Description ==

This plugin allows to clone a post or page, or edit it as a new draft.
If you find this useful, [**please consider donating**](http://lopo.it/duplicate-post-plugin/) whatever sum you choose, **even just 10 cents**. It's been downloaded thousands of times: just a few cents from every user would help me develop the plugin and improve support.


How it works:

1. In 'Edit Posts'/'Edit Pages', you can click on 'Clone' link below the post/page title: this will immediately create a copy and return to the list.

2. In 'Edit Posts'/'Edit Pages', you can click on 'New Draft' link below the post/page title.

3. On the post edit screen, you can click on 'Copy to a new draft' above "Cancel"/"Move to trash". 

4. While viewing a post as a logged in user, you can click on 'Copy to a new draft' as a dropdown link under "Edit Post" in the admin bar.

2, 3 and 4 will lead to the edit page for the new draft: change what you want, click on 'Publish' and you're done.

**Pay attention to the new behaviour!** The first way now allows you to clone a post with a single click, speeding up your work if you have many posts to duplicate.

There is also a **template tag**, so you can put it in your templates and clone your posts/pages from the front-end. Clicking on the link will lead you to the edit page for the new draft, just like the admin bar link.

In the Options page under Settings it is now possible to choose what to copy:

* the original post/page date
* the original post/page status (draft, published, pending), when cloning from the posts list
* the original post/page excerpt
* the original post/page attachments (actual files won't be copied)
* all the children of the original page
* which taxonomies and custom fields

You can also set a prefix (or a suffix) to place before (or after) the title of the cloned post/page, and the roles allowed to clone posts or pages.

If you want to contribute to translate the plugin in languages other than English, there is a [GlotPress translation project](http://lopo.it/glotpress/projects/duplicate-post) available (no registration required! — You can also send me an e-mail using [the form on my website](http://lopo.it/contatti/)).

**If you're a plugin developer**, I suggest to read the section made just for you under "Other Notes", to ensure compatibility between your plugin(s) and mine.

Thanks for all the suggestions, bug reports, translations and donations, they're frankly too many to be listed here!

== Installation ==

Use WordPress' Add New Plugin feature, searching "Duplicate Post", or download the archive and:

1. Unzip the archive on your computer  
2. Upload `duplicate-post` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings -> Duplicate Post and customize behaviour as needed

== Frequently Asked Questions ==

= The plugin doesn't work, why? =

First, check your version of WordPress: the plugin is not supposed to work on old versions anymore. Make sure also to upgrade to the last version of the plugin!

Then try to deactivate and re-activate it, some user have reported that this fixes the problem.

Pay also attention to the new "Roles allowed to copy" option: it should convert the former "user level" option to the new standard, but unknown problems may arise. Make sure that your role is enabled.

If not, maybe there is some kind of conflict with other plugins: feel free [to write me](http://lopo.it/contatti/) and we'll try to discover a solution (it will be *really* helpful if you try to deactivate all your other plugins one by one to see which one conflicts with mine... But do it only if you know what you're doing, I will not be responsible of any problem you may experience).

= Can you add it to the bulk actions in the post/page list? =

I can't. There is no way to do it without hacking the core code of WordPress.
There is an open ticket in WordPress Trac, as other plugin developers too are interested to this feature: we can only hope that eventually our wish will be fulfilled.


== Screenshots ==

1. Here you can copy the post you're editing to a new draft.
2. By clicking on "Clone" the post is cloned immediately. "New draft" leads to the edit screen.
3. The options page.
4. The template tag manually added to Twenty Ten theme. Click on the "Copy to a new draft" link and you're redirected to the edit screen for a new draft copy of your post.
5. The admin bar link. 

== Upgrade Notice ==

= 2.6 =
PHP 5.4 (Strict Standards) compatible + Fixed possible XSS and SQL injections + other bugs 

= 2.4.1 =
Fixes a couple of bug. Recommended if you have problems with v2.4

= 2.4 =
Copy child pages + a couple of bugfixes + licence switch to GPLv2

= 2.3 =
Fixes a bunch of bugs + copy attachments + choose where to show the links.

= 2.2 =
VERY IMPORTANT UPGRADE to get rid of problems with complex custom fields, afflicting both 2.1.* releases.

= 2.1.1 =
Fix for upgrade problem 

= 2.1 =
Copy from admin bar + user levels out, roles and capabilities in. 

= 2.0.2 =
Fixed permalink bug + double choice on posts list

= 2.0.1 =
Bug fix + new option

= 2.0 =
Several improvements and new features, see changelog. Requires WP 3.0+.

= 1.1.1 =
Some users have experienced a fatal error when upgrading to v1.1: this may fix it, if it's caused by a plugin conflict.

= 1.1 =
New features and customization, WP 3.0 compatibility: you should upgrade if you want to copy Custom Posts with Custom Taxonomies.

== Changelog ==

= 2.6 =
* PHP 5.4 (Strict Standards) compatible
* Fixed possible XSS and SQL injections
* other bugs 
* Updated and added translations
* Tested up to WP 3.8.1

= 2.4.1 =
* Fixed regression about draft permalinks
* Fixed bug with guid
* Don't clone to_ping and pinged (maybe there will be an option about those later)

= 2.4 =
* New option to clone the children of the original page
* Licence changed to GPLv2 or later
* Fixed publishing dates for drafts 
* Fixed bug with prefix/suffix
* Translation project moved to GlotPress

= 2.3 =
* Added options to choose where to show the "Clone" links
* Clone attachments (i.e. references in the DB, not physical files) 
* Fix for untranslated user roles
* Some other fixes (missing checks, PHP warnings and errors, etc.)

= 2.2 =
* Fix for problems when copying serialized meta fields
* Fix for multiple _dp_original field
* Removed deprecated parameter when adding options

= 2.1.1 =
* Can't rely on activation hook for upgrade, this caused problems with new options

= 2.1 =
* Even more code cleaning (no more custom queries, using WP API)
* Term order preserved when copying
* Stopped using deprecated User levels, now it uses Roles and Capabilities
* 'Copy to a new draft' link in admin bar
* duplicate_post_get_original template tag
* Settings link in plugin list, 'Donate' and 'Translate' link in option page

= 2.0.2 =
* Fixed bug for permalinks
* Two links on posts list: clone immediately or copy to a new draft to edit.
* Tested on multisite mode.

= 2.0.1 =
* Fixed bug for action filters
* New option so you can choose if cloning from the posts list must copy the post status (draft, published, pending) too.

= 2.0 =
* WP 3.3 compatibility (still not tested against multiblog feature, so beware)
* Minimum WP version: 3.0
* Code cleanup
* Immediate cloning from post list
* Added options for taxonomies and post excerpt
* Added suffix option
* Added template tag

= 1.1.2 =
* WP 3.1.1 compatibility (still not tested against multiblog feature, so beware)
* Added complete Polish language files

= 1.1.1 =
* Plugin split in two files for faster opening in Plugins list page
* fix conflicts with a few other plugins
* Added Dutch language files

= 1.1 =
* WP 3.0 compatibility (not tested against multiblog feature, so beware)
* Option page: minimum user level, title prefix, fields not to be copied, copy post/page date also
* Added German, Swedish, Romanian, Hebrew, Catalan (incomplete) and Polish (incomplete) language files

= 1.0 =
* Better integration with WP 2.7+ interface
* Added actions for plugins which store post metadata in self-managed tables
* Added French and Spanish language files
* Dropped WP 2.6.5 compatibility

= 0.6.1 =
* Tested WP 2.9 compatibility

= 0.6 =
* Fix for WP 2.8.1
* WPMU compatibility
* Internationalization (Italian and Japanese language files shipped)

= 0.5 =
* Fix for post-meta
* WP2.7 compatibility 

= 0.4 =
* Support for new WP post revision feature



== Template tags ==

I have added the template tag `duplicate_post_clone_post_link( $link, $before, $after, $id )`, which behaves just like [edit_post_link()](http://codex.wordpress.org/Function_Reference/edit_post_link).
That means that you can put it in your template (e.g., in single.php or page.php) so you can get a "Clone" link when displaying a post or page.

The parameters are:

* *link*
    (string) (optional) The link text. Default: __('Clone','duplicate-post') 

* *before*
    (string) (optional) Text to put before the link text. Default: None 

* *after*
    (string) (optional) Text to put after the link text. Default: None 

* *id*
    (integer) (optional) Post ID. Default: Current post ID
    
Another available template tag is `duplicate_post_get_original($id, $output)` which returns the original post, either as a post object, an associative array or a numeric array (depending on the $output parameter), jus as [get_post()](http://codex.wordpress.org/Function_Reference/get_post) does.
`duplicate_post_get_original()` relies on the `_dp_original` custom field.


== For plugin developers ==

From version 1.0 onwards, thanks to [Simon Wheatley](http://www.simonwheatley.co.uk/)'s suggestion, Duplicate Post adds two actions (*dp_duplicate_post* and *dp_duplicate_page*) which can be used by other developers if their plugins store extra data for posts in non-standard WP tables.
Since Duplicate Post knows only of standard WP tables, it can't copy other data relevant to the post which is being copied if this information is stored elsewhere. So, if you're a plugin developer which acts this way, and you want to ensure compatibility with Duplicate Post, you can hook your functions to those actions to make sure that they will be called when a post (or page) is cloned.

It's very simple. Just write your function that copies post metadata to a new row of your table:
`function myplugin_copy_post($new_post_id, $old_post_object){
/* your code */
}`

Then hook the function to the action:
`add_action( "dp_duplicate_post", "myplugin_copy_post", $priority, 2);`

dp_duplicate_page is used for pages and hierarchical custom post types; for every other type of posts, dp_duplicate_post is used.

Please refer to the [Plugin API](http://codex.wordpress.org/Plugin_API) for every information about the subject.

== Contribute ==

If you find this useful and you if you want to contribute, there are three ways:

   1. You can [write me](http://lopo.it/contatti/) and submit your bug reports, suggestions and requests for features;
   2. If you want to translate it to your language (there are just a few lines of text), you can use the [GlotPress translation project](http://lopo.it/glotpress/projects/duplicate-post), or [contact me](http://lopo.it/contatti/) and I’ll send you the .pot catalogue; your translation could be featured in next releases;
   3. Using the plugin is free, but if you want you can send me some money with PayPal [here](http://lopo.it/duplicate-post-plugin/)

