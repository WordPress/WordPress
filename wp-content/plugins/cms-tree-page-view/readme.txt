=== CMS Tree Page View ===
Contributors: eskapism, MarsApril
Donate link: http://eskapism.se/sida/donate/
Tags: page, pages, posts, custom posts, tree, cms, dashboard, overview, drag-and-drop, rearrange, management, manage, admin
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 1.2.32

Adds a tree view of all pages & custom posts. Get a great overview + options to drag & drop to reorder & option to add multiple pages.

== Description ==

Adds a CMS-like tree overview of all your pages and custom posts to WordPress - much like the view often found in a page-focused CMS.

Within this tree you can edit pages, view pages, add pages, search pages, and drag and drop pages to rearrange the order.

CMS Tree Page View is a good alternative to plugins such as pageMash, WordPress Page Tree
and My Page Order.

Page management in WordPress won't get any easier than this!

#### Features and highlights:

* View your pages & posts in a tree-view, like you view files in Windows Explorer or the Finder in OS X
* Drag and drop to rearrange/order your pages
* Add pages after or inside a page
* Add multiple pages at once - perfect for setting up a new site structure
* Edit pages
* View pages
* Search pages
* Available for both regular pages and custom posts
* Works with both hierarchical and non-hierarchical post types
* View your site hierarchy directly from the WordPress dashboard
* Drag and drop between trees with different post types to change to post type of the draged item, i.e. change a regular page to became any custom post type
* Support for translation plugin [WPML](http://wordpress.org/extend/plugins/sitepress-multilingual-cms/), so you can manage all the languages of your site

#### Show your pages on your site in the same order as they are in CMS Tree Page View
To show your pages on your website in the same order as they appear in this plugin, you must
sort them by "menu order".

`
// Example using query_posts
$args = array(
  'post_type' => 'page',
  'orderby'=> 'menu_order',
  'order'=>'ASC',
);
$posts = query_posts($args);

// Example using wp_query
$args = array(
	'post_type' => 'page',
	'orderby' => 'menu_order',
	'order' => 'ASC',
);
$query = new WP_Query( $args );

`

#### Screencast
Watch this screencast to see how easy you could be managing your pages:
[youtube http://www.youtube.com/watch?v=H4BGomLi_FU]

#### Translations/Languages
This plugin is available in the following languages:

* English
* German
* French
* Spanish
* Russian
* Belorussian
* Swedish
* Czech
* Italian
* Dutch
* Hungarian
* Norwegian
* Polish
* Greek
* Danish
* Lithuanian
* Estonian
* Finnish
* Japanese
* Ukrainian, by [getvoip.com](http://getvoip.com)
* Slovak

#### Always show your pages in the admin area
If you want to always have a list of your pages available in your WordPress admin area, please check out the plugin
[Admin Menu Tree Page View](http://wordpress.org/extend/plugins/admin-menu-tree-page-view/).

#### Donation and more plugins
* If you like this plugin don't forget to [donate to support further development](http://eskapism.se/sida/donate/).
* More [WordPress CMS plugins](http://wordpress.org/extend/plugins/profile/eskapism) by the same author.

== Installation ==

1. Upload the folder "cms-tree-page-view" to "/wp-content/plugins/"
1. Activate the plugin through the "Plugins" menu in WordPress
1. Done!

Now the tree with the pages will be visible both on the dashboard and in the menu under pages.

== Screenshots ==

1. The page tree in action
2. Edit, view and even add multiple pages at once!
3. Search pages
4. Drag-and-drop to rearrange/change the order of the pages.
5. The tree is also available on the dashboard and therefore available immediately after you login.
6. Users of WPML can find all their languages in the tree
7. Quickly switch between regular list view and tree view using the switch icon


== Changelog ==

= 1.2.32 =

- Fix for possible XSS attack.

= 1.2.31 =

- Fixed so existing pages/posts keep their original author and last modified time. Thanks Heikki Paananen for finding and fixing this.

= 1.2.30 =

- Updated German translation. Thank you translator!

= 1.2.29 =

- Added Slovak translation. Thank you translator!

= 1.2.28 =

- Added Ukranian translation by [getvoip.com](http://getvoip.com).

= 1.2.27 =

- Add new filter "cms_tree_page_view_post_title". Use this filter to change to title being used to build the tree.

= 1.2.26 =

- Roll back the feature with permissions to move pages. Too many people had problems with it.

= 1.2.25 =

- Just a version bump because wordpress.org did not show version 1.2.24.

= 1.2.24 =

- Fix problems with users not getting permissions to move pages.

= 1.2.23 =

- Now only users with permission may move pages and custom posts and publish new posts. By default administrator and editors have these rights. Is checked using capability "move_cms_tree_view_page", so add that to any user you want to allow this for. Props mateuszdw, who made [the very first pull request for this plugin](https://github.com/bonny/WordPress-CMS-Tree-Page-View/pull/1)! Thanks a lot!

= 1.2.22 =

- Fixed top links being squashed in dashboard. Fixes http://wordpress.org/support/topic/bug-top-links-on-dashboard-widget-misaligned-in-wp-38. Props tim.wakeling.
- Fixed: Now prevents long titles from overflowing the dashboard widget area. Prop tim.wakeling.

= 1.2.21 =

- Fixed incompatibility issue with plugin Advanced Custom Fields.

= 1.2.20 =

- Removed part of a comment beacuse it mentionened a file on another domain and therefore violated the repository guidelines. Also moved some sprites in CSS from loading external to loading internal.

= 1.2.19 =

- Added action "cms_tree_page_view_node_move_finish" that is called after a page is moved with drag and drop. Useful to for example clear caches.

= 1.2.18 =

- Added Japanese translation

= 1.2.17 =

- Removed references to, and files for, FirePHP, since it was not used anyway.

= 1.2.16 =

- Just a version bump to make wordpress.org see my changes...

= 1.2.15 =

- Fixed a PHP shortcode.
- Fixed arguments passed to filter get_pages.

= 1.2.14 =

- Now the tree view is enabled by default for hiearchical post types. Should make it easier for new users to get started.
- Removed some annoying calls to console.log().
- Fixed bulk edit and quick edit not working for posts. Fixes http://wordpress.org/support/topic/breaks-bulk-edit-feature.
- Fixed error with removed users. Fixes http://wordpress.org/support/topic/better-wp-security-conflict-1.
- Order now also includes post_title instead of just menu_order. Fixes http://wordpress.org/support/topic/orderby-should-include-post_title.
- Updated norwegian translation.

= 1.2.13 =

- Added Serbo-Croatian translation by Andrijana Nikolic from [webhostinggeeks](http://webhostinggeeks.com/)

= 1.2.12 =
- Fixed search not working
- Fix height of clear search icon

= 1.2.11 =
- Updated german translation
- Updated POT file for translators

= 1.2.10 =
- New fix for wp-Typography. Thanks to eceleste for digging into the problem and fixing it.

= 1.2.9 =
- Fixed an incompatibility with wp-Typography (http://wordpress.org/extend/plugins/wp-typography/). Fixes http://wordpress.org/support/topic/html-in-titles.

= 1.2.8 =
- Fix for post types with dashes in them. Fixes http://wordpress.org/support/topic/custom-posts-with-in-post_type.

= 1.2.7 =
- Fix some notice errors/warning. Props damienwhaley. Fixes http://wordpress.org/support/topic/fix-for-three-non-fatal-errors.

= 1.2.6 =
- Fixed loading CSS over HTTPS.
Fixes http://wordpress.org/support/topic/update-noticonscss-reference-in-stylescss-for-https-sites-too.
- Fixed some styling issues in IE 8.

= 1.2.5 =
- Fixed some notice warnings
- Added Finnish translation
- Fixed a security issue. Thanks to Julio POTIER (<a href="http://secu.boiteaweb.fr/">http://secu.boiteaweb.fr/</a>) for finding and reporting.
- Added nonce checks for options page and for adding new pages

= 1.2.4 =
- Small design changes for the icons in the post overview screen
- Added actions to check permissions when adding pages with AJAX

= 1.2.3 =
- Removed an ugly pixel in the tree icon
- Minor CSS changes to tree icon
- Updated French translation

= 1.2.2 =
- Hide "inside" link if post type is draft, since you can't create new post inside a page with status draft (limitation/bug with Wordpress)
- Added actions so other developers or plugins can control what pages/posts that are editiable and so on. Added filters are: cms_tree_page_view_post_can_edit, cms_tree_page_view_post_user_can_add_inside, cms_tree_page_view_post_user_can_add_after

= 1.2.1 =
- Fixed wrong count if WPML where activated and future or private posts existed for a language
- Show info message if no posts found after fetching posts with AJAX
- Updated Swedish translation
- Updated Russian translation - hopefully it works this time!
- Updated POT file
- Minor language fixes, like actually loading plugin textdomain before using any texts...

= 1.2 =
- Added option to show the tree in the regular post overview screen. Makes the tree view fit into the regular workflow and GUI much better. To enable: go to settings > CMS Tree Page View > Tick the option "On post overview screen". Then go to for example the pages overview screen and in the upper right corner there will be an icon to switch between the regular list view and the tree view of this plugin.
- Fixed so search button now looks more like the rest of the WordPress GUI
- Fixed a undefined index warning
- Fixed wrong language count for WPML-enabled post types
- Perhaps fixed a problem with some other plugins, for example Formidable Pro
- Added icon to settings page
- Added updated Russian translation
- Updated POT-file for translators

= 1.1 =
- Added "Add new"-link next to headline, to better match the regular post overview page + it makes it possible to add new pages/posts when there are no pages/posts added (previously there needed to be at least one post added to be able to add new posts)
- Added post count in parenthesis after each post status. Also makes the page match the regular post overview page a it more. Works for both built in post types and custom post types + if WPML is installed it will show post count for each language too.
- Fixed a bug with sortables (well, I kinda forgot to load that script at all!) that made the plugin only work on the dashboard.
- Fixed some IE-bugs

= 1.0 =
- New: create multiple pages at once! Add multiple pages faster than ever before! You can even select if the new pages should be drafts or published. And ever drag and drop the pages to get the correct order even before adding them. I know - it's awesome!
- Fixed: adds new pages with the correct and selected WPML-language
- Added: you can now change the type of a post by draging the post between different trees on the dashboard. So if you have one custom post type called "Cars" and another called "Bicycles" you can now drag a page from the cars tree to the bicicyles tree and the post will converted to that post type. Pretty powerful feature that you used to need a separately plugin to be able to do.
- Misc fixes
- Added new POT-file for translators
- I decided to call this version 1. I've been using this plugin for so long time now and I use it in almost every WordPress project I participate in (all projects with lots of pages), so with this new add-mulitple-page-feature it feels like it's time to go to version 1. Hope you'll agree! :)

= 0.10.1 =
- Fixed popup closing to fast on Firefox.
- Enable menu item setting by default for hierarchical post types during first install. It was confusing when it was enabled for pages but not for other post types. Consistency!
- Added link to settings page to plugin listing.
- Fixed: WPML-stuff now also works on custom post types

= 0.10 =
- Fixed position of action div. Now it's always to the right of the page name.
- Fixed so action div never is below the fold of the browser. Instead it's moved up until it's visible.
- Fixed problem related to hoverIntent and mouseover and drag and drop. There was just to many wierd things going on so I switched to my own solution instead. Let me know if it works ok for you too now again!

= 0.9 =
- Only output scripts and styles on pages that the plugin uses. This should speed up other parts of the WordPress admin a little tiny itsy bitsy bit.
- Added a hopefully not to spammy box about donation and stuff. Hopefully it it encourages some of you to give it a good review or maybe even donate some money. I've spent a lot, lot, LOT of time developing this plugin you know ;)
- Changed title on dashboard widgets and changed name of the menu item under each supported post type. Makes the titles/names look/feel a bit less dorky.
- Show icons next to the headline of top of pages with the tree
- Minor CSS changes like a little bit bigger text on the pages and a bit more spacing between each page. Makes a bit easier to drag and drop/move them around.
- Changed javascript to to use on() istead on live()
- Removed hoverIntent since that is included in WordPress by default
- Started using hoverIndent to make the popup with page actions show after a short while for each page. This also means that you can move outside the actions-pop-up for a short while without the pop up being closed - a thing that annoyed me very much. This makes the whole popup actions div thingie feels less in-your-face all the time. Hope you like it as much as I do!

= 0.8.14 =
- Added Estonian translation

= 0.8.13 =
- Updated Lithuanian language

= 0.8.12 =
- Fix for forever loading tree
- No plus-sign on nodes that has no children

= 0.8.11 =
- Changed the way to find the plugin url. Hopefully works better now. Thanks https://twitter.com/windyjonas for the patch.

= 0.8.10 =
- Updated Polish translation, including .mo-file

= 0.8.9 =
- Added Belarusian translation. thanks Web Geek Science  (<a href="http://webhostinggeeks.com/">Web Hosting Geeks</a>)
- Fixed XSS vulnerability as described here: https://www.htbridge.com/advisory/HTB23083

= 0.8.8 =
- Fix for tree not remembering state
- Fix for tree not opening on first click

= 0.8.7 =
- Updated German translation
- Fixed PHP notice messages
- Updated swedish translation
- Changed the way scripts and styles load, so it won't add scripts and styles to pages it shouldn't add scripts and styles to

= 0.8.6 =
- Ops, forgot the .mo-file for the Danish translation. Hopefully I did it correct this time...

= 0.8.5 =
- Added Danish translation

= 0.8.4 =
- Hopefully fixed so scripts and styles can be loaded over https, if WP is accessed over https.

= 0.8.3 =
- Added Lithuanian translation by www.kerge.lt. Thank you!

= 0.8.2 =
- Celebrating more than 100.000 downloads and as a gift to you, the user of this plugin, I have removed the "Support the author"-text from the right column. No more nagging! Donations are still welcome though...

= 0.8.1 =
- Polish translation added.

= 0.8 =
- Added: You can now show the tree for regular posts. Appearently there are som plugins that use the hierarchy on posts.
- Fixed: The capability required to show the tree for a post type should now be correct. Previously it was hard-coded to "edit_pages". Thanks to Kevin Behrens, author of plugin Role Scoper, for solving this.

= 0.7.20 =
* Changed caller_get_posts (deprecated since 3.1) to ignore_sticky_posts
* Norwegian translation added by Eigil Moe (http://www.eimoe.com)

= 0.7.19 =
* Greek translation added by Mihalis Papanousis (http://aenaon.biz)
* Hopefully fixed some more problems with columns

= 0.7.18 =
* Second try: Hopefully fixed the problem that moving a page resulted in WPML losing the connection between the languages
* Hungarian translation added
* Small CSS fixes
* Fixed compatiblity issue with ALO EasyMail Newsletter

= 0.7.17 =
* Removed cookie.js
* Updated jstree
* If Keyboard Shortcuts was enabled for a user, title and content of a post could not be edited.
* Drag and drop is now a bit more accurate and less "jerky"
* Hopefully fixed the problem that moving a page resulted in WPML losing the connection between the languages
* Dutch translation added
* Hebrew translation added
* Updated POT-file. Translators may want to check for added or updated words and sentences.
* Fixed a notice-message

= 0.7.16 =
* Fix for wpml-languages with "-" in them, like chinese simplified or chinese traditional.
http://wordpress.org/support/topic/plugin-cms-tree-page-view-broken-for-languages-with-a-in
* Fixed some problems with columns and utf-encoding
* Moved adding page to a box above the tree, so you won't get the feeling that the tree has been deleted when you add a page.

= 0.7.15 =
* Czech translation added
* Italian translation added, by Andrea Bersi (http://www.andreabersi.com)
* require(dirname(__FILE__)."/functions.php"); instead of just require("functions.php");. Should fix problems with for example BackWPup.

= 0.7.14 =
- Added links to PayPal and Flattr, so users who like the plugin can donate.

= 0.7.13 =
- Upgraded jstree to rc2. This fixes the problems with drag & drop and mouse over that occured in WordPress 3.1 beta.

= 0.7.12 =
- Readme-fix...

= 0.7.11 =
- If a post has a custom post status, that status will be shown instead of "undefined". So now CMS Tree Page View works better together with plugins like "Edit flow".

= 0.7.10 =
- CSS images loaded from google via https instead of http. Does this solve the problems you guys with https-sites had?
- Users of IE could not add pages at the right place. All pages where added at the top instead of after or inside another page. Only tested in IE 8, please let me know of the other version..

= 0.7.9 =
- changed so some icons are loaded from ajax.googleapis.com instead of Google Code. Google Code was a bit slow.

= 0.7.8 =
- Something went wrong with last update at wordpress.org, people got 404-error when trying to download plugin. Let's see if this update helps..

= 0.7.7 =
- Added Portuguese translation by Ricardo Tomasi. Thank you!
- Celebration Edition: over 25.000 downloads of this plugin at WordPress.org!

= 0.7.6 =
- You can now view items in the trash. A bit closer to a complete take over of the pages-page :)

= 0.7.5 =
- fixed some notice-errors and switched some deprecated functions
- updated swedish translation
- fixed some strings that where untranslatable and updated POT-file (if I missed any, please let me know)
- no longer allowed to add sub pages to a page with status draft, because if you edit the page and save it, wordpress will forget about the parent (and you will get confused)
- started using hoverIntent for popup instead of regular mouseover, so the popups won't feel so aggressive - or no.. reverted this :(
- when adding a page a text comes up so you know that something is going on
- possible fix for magic fields and other plugins that deal with post columns

= 0.7.4 =
- Updated POT-file, so translators may wan't to check their translations.
- Added Spanish translation by Carlos Janini. Thank you!

= 0.7.3 =
- a page can now be moved above a page with the same menu order. moved page will get the menu order of the page that it's moved aboved, and the other page will get a menu order of previous menu order + 1. i think/hope this is finally solved now!
- using wp_update_post when moving pages (instead of sql directly). this should make this plugin work better with some cache plugins, for example DB Cache Reloaded
- root of tree is added initially, without the need to run an ajax query. loading the root of the tree = super fast! child nodes that are not previosly open are still loaded with ajax, because I want to be sure that the plugin does not hang if there is a page with super-mega-lots of children.

= 0.7.2 =
- pages that the user is not allowed to edit now get "dimmed". they will still be visible becuase a page a user is not allowed to edit, may have a child-page that they are allowed to edit, so the sub-pages must still be accessible
- some problems with Ozh' Admin Drop Down Menu fixed (tree showed posts instead of pages)

= 0.7.1 =
- quick fix: capability edit_pages required to view the tree menu, instead of editor (which led to administrators not being able to view the tree...)

= 0.7 =
- added comment count to pop up
- added support for custom columns in pop up = now you have the same information available in CMS Tree Vage View as in the normal page/post edit screen
- fixed some colors to better match wordpress own style
- editor capability required to view tree. previosly only administators chould see the tree  in the menu, while everyone could view the tree on the dashboard.
- no more infinite loops with role scoper installed
- tested on WordPress Multisite

= 0.6.3 =
- tree is activated for pages during install, so the user does not need to set up anything during first run

= 0.6.2 =
- Was released only as a public beta together with wpml.org, to test the wpml-integration
- Now supports custom post types.
- Now compatible with WPML Multilangual CMS (wpml.org).
- Uses WordPress own functions at some more places.
- When searching and no posts found you now get a message so you know that there were no matches.
- German translation added, by Thomas Dullnig (www.sevenspire.com). Thank you!
- Lots of code rewritten for this update of CMS Tree Page View, so please let me know if it works or if I broke something!

= 0.6.1 =
- Forgot to close a p-tag correctly. Now it should validate again!
- Fixed a problem where move could seem to not work when trying to move pages when several pages had the same menu_order, so they where sorted by alpha instead.
- fixed a problem with qtranslate that resulted in endless "loading tree..."
- the thank you/need help/please donate-box is re-enabled upon upgrade/re-activation of the plugin. Just so you won't forget that you can donate! :)

= 0.6 =
- updated french translation
- new box for mouse-over/pop-up - please let me know what you think about it
- new box: it's bigger so it's less likely that you slide out of it with your mouse (happend to me all the time! very annoying...) .
- new box: more information can be fitted there. let me know if there is any information you would like to see in the popup (right now it will show you the last modified date + the id of the page)
- new box: edit and view links are real links now, so you can edit or view pages in for example a new tab
- new box: oh.. and it's much better looking! :)

= 0.5.7 =
- jquery.cookie.js renamed to jquery.biscuit.js to fix problems with apache module mod_security. let me know if it actually works! :)
- updated .pot-file, so translators out there may want to check if everything is up to date

= 0.5.6 =
- password protected posts now show a lock icon (thanks to [Seebz](http://seebz.net) for contributing)

= 0.5.5 =
- ok, now the texts should be translated. for real! thanks for the bug report!

= 0.5.4 =
- when mouse over the litte arrow the cursor is now a hand again. it just feels a little bit better that way.
- some texts where not translated due to wp_localize_script being called before load_plugin_textdomain. thanks for reporting this.

= 0.5.3 =
- link to "add new page" when there were no pages now work
- changed native js prompt to http://abeautifulsite.net/2008/12/jquery-alert-dialogs/ (mostly because you can use your other browser tabs while the dialog/prompt is open)
- added a thank-you-please-donate-box. please do what it says! :)
- started using menu_page_url instead of hard-coding path to plugin
- now requires WordPress 3

= 0.5.2 =
- you could get an error if used together with the "Simple Fields" WordPress plugin (yes, I used the same function name in both plugin! Fool me twice, shame on me.)

= 0.5.1 =
- forgot to add styles to svn

= 0.5 =
- Uses wp_localize_script to translate script. Previous method could lead to 404-error, although the file did exist.
- More valid output
- jsTree upgraded to 1.0rc
- Code rewritten for upgraded jsTree
- Added a "clear search"-button to the search box
- Dashboard widget added again! Hooray!
- Requires WordPress 3 because of jquery 1.4.2. If you are using WP 2.x you can try version 0.4.9 instead: http://downloads.wordpress.org/plugin/cms-tree-page-view.0.4.9.zip

= 0.4.9 =
- added French translation by Bertrand Andres

= 0.4.8 =
- added russian translation by Alexufo (www.serebniti.ru)
- fixed a link that didn't change color on mouse over

= 0.4.7 =
- remove some code that did not belong...
- does not show auto-draft-posts in wp3

= 0.4.6 =
- could get database error because post_content had no default value
- removed usage of console.log and one alert. ouch!
- when adding page inside, several posts could get menu_order = 0, which led to sorting problems

= 0.4.5 =
- added Belorussian translation by [Marcis G.](http://pc.de/)
- settings page did not check checkboxes by default
- tree removed from dashboard due some problems with event bubbling (will be re-added later when problem is fixed)

= 0.4.4 =
- translation now works in javascript (forgot to use load_plugin_textdomain)
- added swedish translation by Måns Jonasson

= 0.4.3 =
- forgot the domain for _e at some places

= 0.4.2 =
- added .pot-file

= 0.4.1 =
- more prepare for translation
- fixed some <? into <?php

= 0.4 =
- uses strict json (fix for jquery 1.4)
- pages with no title now show "untitled" instead of just disappearing
- uses get_the_title instead of getting the title direct from the db, making plugins such as qtranslate work
- preparing for translation, using __ and _e

= 0.3 =
- all | public: works on the dasboard
- all | public: are now loaded using ajax. no more reloads!
- added options page so you can choose where to show the tree (i.e. the dasboard or under "pages"...or both, of course!). only available for admins.
- capability "edit_pages" required to view the tree

= 0.2 =
- Possible fix for Fluency Admin

= 0.1a =
- First public version.
