=== OptionTree ===
Contributors: valendesigns
Donate link: http://bit.ly/NuXI3T
Tags: admin, theme options, meta boxes, options, admin interface, ajax
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 2.1.4
License: GPLv3

Theme Options UI Builder for WordPress. A simple way to create & save Theme Options and Meta Boxes for free or premium themes.

== Description ==

Theme Options are what make a WordPress Theme truly custom. OptionTree attempts to bridge the gap between developers, designers and end-users by solving the admin User Interface issues that arise when creating a custom theme. Designers shouldn't have to be limited to what they can create visually because their programming skills aren't as developed as they would like. Also, programmers shouldn't have to recreate the wheel for every new project, so in walks OptionTree.

With OptionTree you can create as many Theme Options as your project requires and use them how you see fit. When you add a option to the Settings page, it will be available on the Theme Options page for use in your theme. 

Included is the ability to Import/Export all the theme options and data for packaging with custom themes or local development. With the Import/Export feature you can get a theme set up on a live server in minutes. Theme authors can now create different version of their themes and include them with the download. It makes setting up different theme styles & options easier than ever because a theme user installs the plugin and theme and either adds their own settings or imports your defaults.

A new feature in OptionTree 2.0 is the ability to include the plugin directly in your themes root directory. Not only does that mean your theme is guaranteed to have the plugin installed you also get the ability to interact directly with OptionTree through settings and meta box arrays. You can now tell OptionTree what settings you want and know that nobody will break your theme by changing settings through the UI Builder. It's just a better plugin now!

OptionTree is a project sponsored by <a href="http://themeforest.net/?ref=valendesigns">ThemeForest</a>, the largest WordPress theme marketplace on the web, and was originally conceived to help ThemeForest authors quickly power up their themes. But it's here for the benefit of one and all, so option up folks!

== Installation ==

1. Upload `option-tree` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Click the `OptionTree->Documentation` link in the WordPress admin sidebar menu for further setup assistance.

== Frequently Asked Questions ==

= Is this plugin PHP5 only? =

Yes. OptionTree & WordPress both require PHP5.

== Screenshots ==

1. Theme Options
2. Settings
3. Documentation

== Changelog ==

= 2.1.4 =
* Hotfix - Fixed the Numeric Slider not work inside of a newly added List item.
* Hotfix - Fixed the numeric slider fallback value being set to 0, it now becomes the minimum value if no standard is set.
* Hotfix - Allow single quotes in std and choice value when exporting theme-options.php. Contributors via github @maimairel.
* Hotfix - Additional Themecheck bypass for required functions. Contributors via github @maimairel.
* Hotfix - Fixed post meta information being lost when loading revisions. Contributors via github @live-mesh.
* Hotfix - Removed template queries in option types. Contributors via github @live-mesh.

= 2.1.3 =
* Hotfix - Loading OptionTree on the 'init' action proved to be wrong, it now loads on 'after_setup_theme'.
* Hotfix - Layouts were not being imported properly due to using the wrong path variable.

= 2.1.2 =
* Hotfix - Fixed a JS mistake that caused upload in list items and sliders to not open the media uploader until saved first.
* Hotfix - Load OptionTree on the 'init' action, which allows the UI filters to properly function when not in theme mode.

= 2.1.1 =
* Hotfix - The OT_SHOW_SETTINGS_EXPORT constant was incorrectly set to false as the default.

= 2.1 =
* Added support for WordPress 3.6.
* UI got a small but needed update, and is now more inline with WordPress.
* Added WPML support for the Text, Textarea, and Textarea Simple option types, and within list items; even after drag & drop.
* Upload now uses the media uploader introduced in WordPress 3.5. Contributors via github @htvu, @maimairel, and @valendesigns.
* Added a horizontal Numeric Slider option type. Contributors via github @maimairel and @valendesigns.
* Added a Sidebar Select option type. Contributors via github @maimairel.
* Removed additional deprecated assigning of return value in PHP.
* Fix missing "Send to OptionTree" button in CPT. Contributors via github @jomaddim.
* Fix option types that use $count instead of an array key to select the option value.
* Created functions to register the Theme Options & Settings pages, and with better filtering.
* Added relative path support for Radio Image choices.
* Added dynamic replacement of 'OT_URL' & 'OT_THEME_URL' in the Radio Image source path.
* Make '0' possible as a field value. Validate for empty strings instead of empty(). Contributors via github @maimairel.
* The 'ot_theme_options_capability' filter is now working for different capabilities like editor.
* The 'ot_display_by_type' filter is now being assigned to a value.
* Added filter 'ot_show_options_ui' which allows you to hide the Theme Options UI Builder.
* Added filter 'ot_show_settings_import' which allows you to hide the Settings Import options on the Import page.
* Added filter 'ot_show_settings_export' which allows you to hide the Settings Export options on the Export page.
* Added filter 'ot_show_docs' which allows you to hide the Documentation.
* Added filter 'ot_use_theme_options' which allows you to hide the OptionTree Theme Option page (not recommended for beginners).
* Added filter 'ot_list_item_description' which allows you to change the default list item description text.
* Added filter 'ot_type_custom_post_type_checkbox_query' which allows you to filter the get_posts() args for Custom Post Type Checkbox.
* Added filter 'ot_type_custom_post_type_select_query' which allows you to filter the get_posts() args for Custom Post Type Select.
* Added filter 'ot_type_page_checkbox_query' which allows you to filter the get_posts() args for Page Checkbox.
* Added filter 'ot_type_page_select_query' which allows you to filter the get_posts() args for Page Select.
* Added filter 'ot_type_post_checkbox_query' which allows you to filter the get_posts() args for Post Checkbox.
* Added filter 'ot_type_post_select_query' which allows you to filter the get_posts() args for Post Select.

= 2.0.16 =
* Fixed an urgent JS regression bug that caused the upload option type to break. Code contributed by @anonumus via github.
* Added 'font-color' to the typography filter.

= 2.0.15 =
* Added support for Child Theme mode.
* Improved handling of standard values when settings are written manually.
* Add filter for CSS insertion value.
* Added 'ot_before_theme_options_save' action hook.
* Fix 'indexOf' JS error when upload is closed without uploading.
* Add textarea std value when option type is 'textarea', 'textarea-simple', or 'css'.
* Remove load_template and revert back to include_once.
* Fixed dynamic.css regression from 2.0.13 that caused the file to not save.

= 2.0.14 =
* Removed deprecated assigning of return value in PHP.
* Patch to fix PHP notice regression with the use of load_template in a plugin after Theme Check update.
* Fixed missing required arguments in OT_Loader::add_layout.
* Removed esc_attr() on font-family check.
* Added a 'ot_theme_options_parent_slug' filter in ot-ui-theme-options.php
* Fixed WP_Error from the use of wp_get_remote() instead of file_get_contents().

= 2.0.13 =
* Removed almost all of the Theme Check nag messages when in 'ot_theme_mode'.
* Fix an issue where Media Upload stopped working on some servers.

= 2.0.12 =
* Added additional filters to the array that builds the Theme Option UI.
* Made option-tree post type private.
* Revert capabilities back to manage_options in ot-ui-admin.php.
* Upload now sends the URL of the selected image size to OptionTree.
* Added new range interval filter to font-size, letter-spacing, & line-height.
* Allow Typography fields to be filtered out of the UI.

= 2.0.11 =
* Added filters to the array that builds the Theme Option UI.
* Added .format-setting-wrap div to allow for complex CSS layouts.
* Added better namespacing for the Colorpicker option type.
* Fixed theme-options.php export where it was adding an extra comma.

= 2.0.10 =
* Fixed a bug where the Textarea row count wasn't working for List Items.
* Added an apply_filter to the exported theme-options.php file.
* Added CSS id's to tabs and settings.
* Allow "New Layout" section to be hidden on the theme options page via a filter.
* Fixed a bug where the Colorpicker was not closing in List Items.
* Change capabilities from manage_options to edit_theme_options.
* Remove Textblock title in List Items & Metaboxes.
* Fixed a List Item bug that incorrectly added ID's based on counting objects - submitted by Spark
* Fixed incorrect text domain paths for both plugin and theme mode.
* Fixed a bug with UI Sortable not properly calculating the container height.
* Fixed Select dropdown selector bug - submitted by Manfred Haltner
* Fixed Radio Image remove class bug - submitted by designst
* Added new typography fields - submitted by darknailblue
* Added dynamic CSS support for new typography fields.
* Added new filters to typography fields, including low/high range & unit types.

= 2.0.9 =
* Fixed the issue where the Textarea Simple and CSS option types were mysteriously being ran through wpautop.
* Added missing class setting to Textarea, Textarea Simple, & CSS option types.
* Fixed theme-options.php exported array where label values were not correct.
* Change GET to POST for all AJAX calls to fix a bug where some servers would not allow long strings to be passed in GET variables.
* Added the 'ot_after_validate_setting' filter to the validation function.
* Added $field_id to the ot_validate_setting() for more precise filtering.
* Added the ot_reverse_wpautop() function that you can run input through just incase you need it.
* Updated the docs to include information on why WYSIWYG editors are not allowed in meta boxes and that they revert to a Textarea Simple.
* Update option-tree.pot file.

= 2.0.8 =
* Add auto import for backwards compatibility of old 1.x files.
* Added the ability to export settings into a fully functional theme-options.php.
* Fix typo in docs regarding the filter demo code.
* Removed slashes in the section and contextual help titles.
* Made colorpicker input field alignment more cross browser compatible.

= 2.0.7 =
* Fixed the load order to be compatible with 1.x version themes that think the get_option_tree() function doesn't exist yet.
* Tested and compatible with Cudazi themes, but the nag message is still visible.

= 2.0.6 =
* Run the 'option_tree' array through validation when importing data and layouts.
* Fix a bug where list items and sliders were not allowing the user to select the input field.
* Add a filter that allows you to not load resources for meta boxes if you're not going to use them.
* Update option-tree.pot file.

= 2.0.5 =
* Change the way the 'option_tree_settings' array validates. Strip out those damn slashes!

= 2.0.4 =
* Run the 'option_tree' array through validation when upgrading from the 1.0 branch to the 2.0 branch for the first time.
* Fix a typo in the slider array where textarea's were not saving the first time due to an incorrect array key.

= 2.0.3 =
* Had an incorrect conditional statement causing an issue where the plugin was attempting to create the 'option-tree' image attachment page, even though it was already created.
* The above also fixed a conflict with 'The Events Calendar' plugin.

= 2.0.2 =
* Added I18n support, let the translations begin. The option-tree.pot file is inside the languages directory.
* Trim whitespace on imported choices array.
* Fixed the CSS insert function not having a value to save.

= 2.0.1 =
* Import from table was not mapping settings correctly. It is now.

= 2.0 =
* Complete rewrite form the ground up.
* Better Theme Options UI Builder.
* New in-plugin documentation.
* Brand new responsive UI.
* Add new option types, most notable the List Item which should eventually replace the Slider.
* Added the simpler ot_get_option() function to eventually replace get_option_tree().
* Added support for Meta Boxes.
* Added Theme Mode where you can now include the plugin directly in your theme.
* Better validation on saved data.
* Simplified the import process.
* Added support for contextual help.
* Permanently move the Theme Option to the Appearance tab.
* Added a ton of filters.
* Made huge improvements to the code base and tested rigorously.

= 1.1.8.1 =
* Removed get_option_tree() in the WordPress admin area due to theme conflicts.
* Removed demo files in the assets folder at the request of WordPress

= 1.1.8 =
* Fixed scrolling issue on extra tall pages
* Added ability to show/hide settings & documentation via the User Profile page.
* Added Background option type.
* Added Typography option type.
* Added CSS option type.
* Better looking selects with 1=Yes,2=No where '1' is the value and 'Yes' is the text in the select.
* Made the AJAX message CSS more prominent.
* functions.load.php will now only load option type functions if viewing an OT admin page.
* Deregistered the custom jQuery UI in the 'Cispm Mail Contact' plugin when viewing an OptionTree page.
* Can now save layouts from the Theme Options page.
* You can now change the slider fields by targeting a specific "Option Key"
* Modified upload for situations where you manually enter a relative path
* Allow get_option_tree() function to be used in WP admin
* Changed permissions to edit_theme_options

= 1.1.7.1 =
* Revert functions.load.php, will fix and update in next version

= 1.1.7 =
* Added layout (theme variation) support with save/delete/activate/import/export capabilities. Contributions form Brian of flauntbooks.com
* Allow layout change on Theme Options page.
* Full Multisite compatibility by manually adding xml mime type for import options.
* Replaced eregi() with preg_match() for 5.3+ compatibility.
* Changed test data in the assets directory for new layout option.
* Made it so when the slider & upload image changes it's reflected on blur.
* Gave the slider image an upload button.
* Added do_action('option_tree_import_data') to option_tree_import_data() function before exit.
* Added do_action('option_tree_array_save') to option_tree_array_save() function before exit.
* Added do_action('option_tree_save_layout') to option_tree_save_layout() function before exit.
* Added do_action('option_tree_delete_layout') to option_tree_delete_layout() function before exit.
* Added do_action('option_tree_activate_layout') to option_tree_activate_layout() function before exit.
* Added do_action('option_tree_import_layout') to option_tree_import_layout() function before redirect.
* Added do_action('option_tree_admin_header') hook before all admin pages.
* Fixed bug where users could add a color without a hash.
* Only load option type function on Theme Options page
* Loading resources with absolute paths, no longer relative.
* Fixed a bug with uploader creating extra option-tree draft pages.
* Fixed slider toggle bug, now the sliders close when you open another or create new slide.

= 1.1.6 =
* Theme Integration added.
* Made the upload XML file openbase_dir compliant.

= 1.1.5 =
* Fixed multiple sliders issue

= 1.1.4 =
* Patch for get_option_tree() $is_array being false and still returning an array

= 1.1.3 =
* Added Slider option type with filter for changing the optional fields
* Fixed the text displayed for Measurement option type after options are reset
* Added filter to measurement units
* Code cleanup in the option_tree_array_save() function
* Fixed double quotes on front-end display

= 1.1.2 =
* Fixed double quotes in Textarea option type
* Added Measurement option type for CSS values
* Fixed Post option type only returning 5 items
* Added a scrolling window for checkboxes > 10

= 1.1.1 =
* Fixed the 'remove' icon from showing when nothing's uploaded

= 1.1 =
* Fixed the Undefined index: notices when WP_DEBUG is set to true

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 2.1.4 =
If you're not the developer of this theme, please ask them to test compatibility with version 2.1 before upgrading. If you are the developer, I urge you to do the same in a controlled environment.

= 2.0.16 =
There was an issue with the upload option type's JavaScript not allowing anything other than images to be sent to the editor. This urgent issue is now fixed and why this version is light on changes.

= 2.0.12 =
The plugin has undertaken a complete rebuild! If you are not the theme developer, I urge you to contact that person before you upgrade and ask them to test the themes compatibility.

= 1.1.8.1 =
Removed get_option_tree() in the WordPress admin area due to theme conflicts.

= 1.1.8 =
Added Typography, Background, & CSS option types. Lots of way to extend them, as well.

= 1.1.7 =
Lots of additions, none critical just fun. Added layouts & upload to slider. As well, started including action hooks for extending and integrating with other plugins.

= 1.1.6 =
Added theme integration for developers. It's now possible to have a default XML file included in your theme to populate the theme options and hide the settings and docs pages. Read more about this in the plugins built in documentation.

= 1.1.5 =
Having multiple sliders caused a naming collision in the JavaScript and is now fixed. Upgrade ASAP to have multiple sliders available in the UI.

= 1.1.4 =
Fixed the returned value of the get_option_tree() function when $is_array is set to false. If you have created any slider or measurement option types please read the updated documentation for examples on how to use them in your theme.
