=== Plugin Name ===
Contributors: WPMUDEV, marquex
Tags:         custom sidebars, widgets, sidebars, custom, sidebar, widget, personalize
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag:   trunk

Create your own widgetized areas and choose on which pages they show up - "Easy to use. Even with complex themes. Made my work much easier."

== Description ==

If you'd like to show different widgets on the sidebars or footers of any area of your site - then this is the plugin for you.

Custom Sidebars allows you to create all the widgetized areas you need, your own custom sidebars, configure them adding widgets, and replace the default sidebars on the posts or pages you want in just few clicks.

Find out why it's the most popular widget extension plugin for WordPress available today with over 400,000 downloads.

<blockquote>
<h4>And if you like this, you'll love Custom Sidebars Pro</h4>
<br />
<a href="http://premium.wpmudev.org/project/custom-sidebars-pro/">Custom Sidebars Pro</a> gives you everything you'll find in Custom Sidebars, and much, much more.

Included with the Pro Version:

<ul>
<li>Set widget visibility based on rules ranging from user roles to post types, individual pages or taxonomies</li>
<li>Clone sidebars to save hours of work and then link them to update synchronously or allow them to be individually edited</li>
<li>Import and export custom sidebars for easy backup, sharing and deployment across multiple sites</li>
<li>24/7/365 under and hour support from <a href="http://premium.wpmudev.org/support/">the best WordPress support team on the planet</a></li>
<li><a href="http://premium.wpmudev.org/join/">Over 400 other premium plugins and theme</a> included in your membership</li>
</ul>

So checkout <a href="http://premium.wpmudev.org/project/custom-sidebars-pro/">Custom Sidebars Pro</a> and take your site to a brand new level.
</blockquote>

The free version is pretty rocking too though, for example, with this plugin you can customize every widget area by setting new default sidebars for a group of posts or pages easily, keeping the chance of changing them individually.

For example, you can change...

<ul>
<li>Sidebars for all the posts that belong to a category.</li>
<li>Sidebars for all the posts that belong to a post-type.</li>
<li>Sidebars for archives (by category, post-type, author, tag).</li>
<li>Sidebars for the main blog page.</li>
<li>Sidebars for search results.</li>
</ul>

And of course both this plugin and the Pro version are <a href="http://premium.wpmudev.org/translate/projects/custom_sidebars_pro">fully internationalized</a>.

And if you're not convinced yet, <a href="https://wordpress.org/support/view/plugin-reviews/custom-sidebars?filter=5">check out some of the 5 star reviews for this plugin</a> - and please feel free to add your own :)

== Installation ==

There are two ways of installing the plugin:

**From the [WordPress plugins page](http://wordpress.org/extend/plugins/)**

1. Download the plugin, extract the zip file.
2. Upload the `custom-sidebars` folder to your `/wp-content/plugins/` directory.
3. Active the plugin in the plugin menu panel in your administration area.

**From inside your WordPress installation, in the plugin section.**

1. Search for custom sidebars plugin.
2. Download it and then active it.

Once you have the plugin activated you will find all new features inside your "Widgets" screen! There you will be able to create and manage your own sidebars.

Find more functionality and usage information on the [Custom Sidebars Pro page](http://premium.wpmudev.org/project/custom-sidebars-pro/).

== Frequently Asked Questions ==

= How do I begin working with this plugin? =

Please refer to <a href="http://premium.wpmudev.org/project/custom-sidebars-pro/#usage">the usage section of Custom Sidebars Pro</a>

= Why can't I see a widget menu? =

This plugin requires your theme to have widget areas enabled, if you don't have widget areas enabled you probably need to use a different theme that does!

= Where do I set my sidebars up? =

You have a sidebar box when editing a entry. Also you can define default sidebars for different posts and archives.

= Why do I get a message 'There are no replaceable sidebars selected'?  =

You can create all the sidebars you want, but you need some sidebars of your theme to be replaced by the ones that you have created. You have to select which sidebars from your theme are suitable to be replaced in the Custom Sidebars settings page and you will have them available to switch.

= Everything is working properly on Admin area, but the site is not displaying the sidebars. Why? =

 You are probably using a theme that doesn’t load dynamic sidebars properly or doesn’t use the wp_head() function in its header. The plugin will replace the sidebars inside that function, many other plugins also hook there, so it is [more than recommended to use it](http://josephscott.org/archives/2009/04/wordpress-theme-authors-dont-forget-the-wp_head-function/).

= It appears that only an Admin can choose to add a sidebar. How can Editors (or any other role) edit customs sidebars? =

Any user that can switch themes, can create sidebars. Switch_themes is the capability needed to manage widgets, so if you can’t edit widgets you can’t create custom sidebars. There are some plugins to give capabilities to the roles, so you can make your author be able to create the sidebars. Try [User role editor](http://wordpress.org/extend/plugins/user-role-editor/)

= Does it have custom taxonomies support? =

This plugin supports showing your posts on all different categories or tags, it's awesome, for more visibility controls try <a href="http://premium.wpmudev.org/project/custom-sidebars-pro/">Custom Sidebars Pro</a>

= Can I use the plugin in commercial projects? =

Custom Sidebars has the same license as WordPress, so you can use it wherever you want for free. Yay!

= I like the plugin, but what can I do if my website is based in a WP version older than 3.3 =

If you are running a earlier version of Wordpress download Custom Sidebars 0.8.2.

== Screenshots ==

1. screenshot-1.png The WordPress Widgets section is now packed with new features to create and manage your sidebars.
2. screenshot-2.png Create and edit sidebars directly inside the widgets page. Easy and fast!
3. screenshot-3.png In the Location popup you can decide what page should display which sidebars.
4. screenshot-4.png Or finetune the sidebars by selecting them directly for a special post or page!

== Changelog ==

= 2.0.7 =
*		Fix: Fixed issue with some people losing some sidebar settings after update.

= 2.0.6.1 =
*		Minor fix: Use WordPress core functions to get URL to javascript files.
*		Minor fix: Refactor function name to avoid misunderstandings.

= 2.0.5 =
*		Fixed: Meta box in post editor did show missing sidebars (e.g. after switching the theme)
*		Fixed: PHP warning about strict standards.

= 2.0.3 =
*		Fixed: Javascript errors on Windows servers are fixed.

= 2.0.2 =
*		Fixed: Dashboard notification is now removed when clicking "dismiss"

= 2.0.1 =
*		PHP 5.2 compatibility layer.

= 2.0 =
*		Complete UI redesign!
*		Many small bugfixes.

= 1.6 =
*		Added: WordPress filter "cs_sidebar_params" is called before a custom sidebar is registered.
*		Added: Add setting "CUSTOM_SIDEBAR_DISABLE_METABOXES" in wp-config.php to remove custom-sidebar meta boxes.

= 1.5 =
*		Added: Custom sidebars now works with buddypress pages.

= 1.4 =
*		Fixed: Individual post sidebar selection when default sidebars for single posts are defined
*		Fixed: Category sidebars sorting
*		Added: WP 3.8 new admin design (MP6) support

= 1.3.1 =
*		Fixed: Absolute paths that leaded to the outdated browser error
*		Fixed: Stripped slashes for the pre/post widget/title fields

= 1.3 =
*		Fixed: A lot of warnings with the PHP debug mode on
*		Improved: Styles to make them compatible with WP 3.6
*		Fixed: Creation of sidebars from the custom sidebars option
*		Fixed: Missing loading icons in the admin area
*		Removed: Donate banner. Thanks to the ones that have be supporting Custom Sidebar so far.

= 1.2 =
*       Fixed: Searches with no results shows default sidebar.
*		Added: RTL support (thanks to Dvir http://foxy.co.il/blog/)
*		Improved: Minor enhancements in the interface to adapt it to wp3.
*		Added: French and Hebrew translations
*		Fixed: Slashes are added to the attributes of before and after title/widget

= 1.1 =
*       Fixed: Where lightbox not showing for everybody (Thanks to Robert Utnehmer)
*       Added: Default sidebar for search results pages
*       Added: Default sidebar for date archives
*	Added: Default sidebar for Uncategorized posts

= 1.0 =
*       Fixed: Special characters make sidebars undeletable
*       Added: Child/parent pages support
*       Improved interface to handle hundreds of sidebars easily
*       Added: Ajax support for creating an editing sidebars from the widget page
*       Added: Italian translation

= 0.8.2 =
* 	Fixed: Problems with spanish translation
*	Added: Dutch and German language files
* 	Fixed: Some css issues with WP3.3

= 0.8.1 =
*	Fixed: You can assign sidebars to your pages again.

= 0.8 =
*	Fixed: Category hierarchy is now handled properly by the custom sidebars plugin.
*	Added: Sidebars can be set for every custom post type post individually.
*	Improved the way it replace the sidebars.
*	Improved some text and messages in the back-end.

= 0.7.1 =
* 	Fixed: Now the plugin works with themes like Thesis that don't use the the_header hook. Changed the hook where execute the replacement code to wp_head.
*	Fixed: When a second sidebar is replaced with the originally first sidebar, it is replaced by the first sidebar replacement instead.

= 0.7 =
*	Fixed: Bulk and Quick editing posts and pages reset their custom sidebars.
*	Changed capability needed to switch_themes, and improved capability management.

= 0.6 =

*	New interface, more user friendly
*	Added the possibility of customize the main blog page sidebars
*	Added the sidebars by category, so now you can personalize all the post that belongs to a category easily in a hierarchycal way
*	Added the possibility of customize the authors page sidebars
*	Added the possibility of customize the tags page sidebars
*	Added, now it is possible to edit the sidebars names, as well as the pre-widget, post-widget, pre-title, post-title for a sidebar.
*	Added the possibility of customize the sidebars of posts list by category or post-type.


= 0.5 =

*	Fixed a bug that didn't allow to create new bars when every previous bars were deleted.
*	Fixed a bug introduced in v0.4 that did not allow to assign bars per post-types properly
*	Added an option to remove all the Custom Sidebars data from the database easily.

= 0.4 =

*	Empty sidebars will now be shown as empty, instead of displaying the theme's default sidebar.

= 0.3 =

*	PHP 4 Compatible (Thanks to Kay Larmer)
*	Fixed a bug introduced in v0.2 that did not allow to save the replaceable bars options

= 0.2 =

*	Improved security by adding wp_nonces to the forms.
*	Added the pt-widget post type to the ignored post types.
*	Improved i18n files.
*	Fixed screenshots for documentation.

= 0.1 =

*	Initial release

== Upgrade Notice ==

= 1.0 =
*Caution:* Version 1.0 needs Wordpress 3.3 to work. If you are running an earlier version *do not upgrade*.

= 0.7.1 =
Now custom sidebars works with Thesis theme and some minor bugs have been solved.

= 0.7 =
This version fix a bug of v0.6 and before that reset the custom sidebars of posts and pages when they are quick edited or bulk edited, so upgrade is recommended.
This version also changes the capability for managing custom sidebars to 'switch_themes' the one that allows to see the appearance menu in the admin page. I think the plugin is more coherent this way, but anyway it is easy to modify under plugin edit.

= 0.6 =
This version adds several options for customize the sidebars by categories and replace the default blog page sidebars. Now it's possible to edit sidebar properties. Also fixes some minor bugs.

== Contact and Credits ==

Custom sidebars is maintained and developed by <a href="http://premium.wpmudev.org">WPMU DEV</a>.

Original development completed by <a href="http://marquex.es/">Javier Marquez</a>

Custom Sidebars uses the great jQuery plugin [Tiny Scrollbar](http://www.baijs.nl/tinyscrollbar/) by Maarten Baijs.