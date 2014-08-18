=== OptionTree ===
Contributors: valendesigns
Donate link: http://bit.ly/NuXI3T
Tags: options, theme options, meta boxes
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 2.4.0
License: GPLv3

Theme Options UI Builder for WordPress. A simple way to create & save Theme Options and Meta Boxes for free or premium themes.

== Description ==

OptionTree attempts to bridge the gap between WordPress developers, designers and end-users by creating fully responsive option panels and meta boxes with an ease unlike any other plugin. Packed full of advanced features with tons of hooks and filters to adjust every aspect of the user experience. You can build your theme options with a drag & drop interface locally and export a fully functioning `theme-options.php` file for production use that's i18n translation ready with you own custom text domain. And, in just a few simple lines of code, all your themes can have separate settings arrays saved to the database with unique IDs. Not only that, but OptionTree takes full advantage of the new color schemes introduced in WordPress 3.8, it looks and feels built-in.

#### Theme Integration
If you're like me, you want to know how everything works. Download and activate the [OptionTree Theme](https://github.com/valendesigns/option-tree-theme) and see first hand how to integrate OptionTree into your own project. I'll walk you through installing OptionTree and you'll get a chance to see all the various options and filters first hand and in the wild.

#### Contributing
To contribute or report bugs, please go to the [OptionTree Github](https://github.com/valendesigns/option-tree) repository.

#### Sponsorship
OptionTree is a project partly sponsored by <a href="http://themeforest.net/?ref=valendesigns">ThemeForest</a>, the largest WordPress theme marketplace on the web.

#### Option Types
This is a complete list of all the available option types that come shipped with OptionTree.

* Background
* Category Checkbox
* Category Select
* Checkbox
* Color Picker
* CSS
* Custom Post Type Checkbox
* Custom Post Type Select
* Date Picker
* Date Time Picker
* Gallery
* List Item
* Measurement
* Numeric Slider
* On/Off
* Page Checkbox
* Page Select
* Post Checkbox
* Post Select
* Radio
* Radio Image
* Select
* Sidebar Select
* Slider
* Social Links
* Tab
* Tag Checkbox
* Tag Select
* Taxonomy Checkbox
* Taxonomy Select
* Text
* Textarea
* Textarea Simple
* Textblock
* Textblock Titled
* Typography
* Upload

== Installation ==

**Plugin Mode**

1. Upload `option-tree` to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress
1. Click the `OptionTree->Documentation` link in the WordPress admin sidebar menu for further setup assistance.

**Theme Mode**

1. Download the latest version of OptionTree and unarchive the `.zip` directory.
1. Put the `option-tree` directory in the root of your theme. For example, the server path would be `/wp-content/themes/theme-name/option-tree/`.
1. You must deactivate and/or delete the plugin version of OptionTree.
1. Add the following code to the beginning of your `functions.php`.

`/**
 * Required: set 'ot_theme_mode' filter to true.
 */
add_filter( 'ot_theme_mode', '__return_true' );

/**
 * Required: include OptionTree.
 */
load_template( trailingslashit( get_template_directory() ) . 'option-tree/ot-loader.php' );`

For a list of all the OptionTree UI display filters refer to the `demo-functions.php` file found in the `/assets/theme-mode/` directory of this plugin. This file is the starting point for developing themes with Theme Mode.

== Frequently Asked Questions ==

= I get errors or a blank screen when I activate the plugin. What's the deal? =

The most likely scenario is your theme already has OptionTree installed in Theme Mode. And since the plugin and theme version can't both be active at the same time without the sky falling on your head, your site has decided to throw in the towel. If that's not your issue, open up a support request and we'll figure it out together.

= Is there a demo theme I can install? =

There sure is, and I'm glad you asked. Download and activate the [OptionTree Theme](https://github.com/valendesigns/option-tree-theme) and get some experience setting up OptionTree on your own with detailed directions and tips.

== Screenshots ==

1. Theme Options
2. Settings
3. Documentation

== Changelog ==

= 2.4.0 =
* Added filter 'ot_post_formats' which loads meta boxes specifically for post formats.
* Added the Social Links option type.
* Fixed OptionTree being conflicted due to having both the plugin and theme version activated. Contributors via github @valendesigns and @bitcommit.
* Added an admin notice when the UI Builder is being overridden by custom theme options.
* Allow the Upload option type to be stored as an attachment ID by adding `ot-upload-attachment-id` to the elements `class` attribute. Contributors via github @valendesigns and @krisarsov.
* Fixed an issue with the CSS option type not showing the Ace editor in a metabox that is broken into tabbed content.
* Fixed missing option type translation strings. Contributors via github @RistoNiinemets.
* Replaced mysql functions with the wpdb equivalent. Contributors via github @joshlevinson.
* Fixed search order of the `contains` condition string. Contributors via github @designst.
* Added meta box field wrapper class if a custom field class is defined in the settings. Contributors via github @designst.
* Added filter 'ot_type_select_choices' to dynamically change select choices. Contributors via github @maimairel and @valendesigns.
* Fixed a bug that added an unnecessary directory separator to the `load_theme_textdomain()` `$path` variable. Contributors via github @PatrickDelancy and @valendesigns.
* Fixed the state of metabox radio buttons after a Drag & Drop event. Contributors via github @themovation and @valendesigns.
* Fixed conditions not working correctly within list items.
* Fixed the min-height issue when using tabs in metaboxes.
* Added filter `ot_recognized_font_sizes` to dynamically change the font sizes by field ID.
* Added filter `ot_recognized_letter_spacing` to dynamically change the letter spacing by field ID.
* Added filter `ot_recognized_line_heights` to dynamically change the line heights by field ID.
* Fixed a style issue where list item labels in metaboxes were not displaying correctly.
* Fixed an issue where the WooCommerce plugin would alter the style of metabox tabs on product pages.

= 2.3.4 =
* Hotfix - Fixed an issue where condition number values were being treated like strings and not returning a correct boolean response.

= 2.3.3 =
* Hotfix - Fixed subfolder compatibility with versions of Windows that use backslashes instead of forward slashes. Contributors via github @primozcigler and @valendesigns.
* Hotfix - Fixed missing text domain in demo files. Contributors via github @jetonr.
* Hotfix - Added filter `ot_migrate_settings_id` to migrate themes that used `option_tree_settings` and now use a custom settings ID.
* Hotfix - Added filter `ot_migrate_options_id` to migrate themes that used `option_tree` and now use a custom options ID.
* Hotfix - Added filter `ot_migrate_layouts_id` to migrate themes that used `option_tree_layouts` and now use a custom layouts ID.

= 2.3.2 =
* Hotfix - Fixed an issue with the `ot_create_media_post` function creating multiple `option-tree` posts.
* Hotfix - Change the icon used by the layout management option type to differentiate it from the edit button.
* Hotfix - Suppress PHP warning in the Background option type "Invalid argument supplied for foreach()". Contributors via github @tomkwok.
* Hotfix - Added filter `ot_type_date_picker_date_format` to change the date format of the Date Picker option type.
* Hotfix - Added filter `ot_type_date_time_picker_date_format` to change the date format of the Date Time Picker option type.

= 2.3.1 =
* Hotfix - Fixed a bug with the Gallery option type that would show attachments in the media window when none had been added yet.
* Hotfix - Added the option to save the Gallery as a shortcode by adding `ot-gallery-shortcode` to the elements `class` attribute.
* Hotfix - Fixed conditions not being effective in List Items directly after clicking "Add New". Contributors via github @bitcommit.

= 2.3.0 =
* Added the Tab option type.
* Added Ace Editor to the CSS option type. Contributors via github @imangm and @valendesigns.
* Added support for WordPress 3.8 color schemes.
* Added support for RTL languages. Contributors via github @omid-khd and @valendesigns.
* Added actions before and after the enqueue styles and scripts.
* Added Date Picker option type. Contributors via github @jetonr and @valendesigns.
* Added Date Time Picker option type. Contributors via github @jetonr and @valendesigns.
* Added filter 'ot_list_item_title_label' to change the label for a List Item's required title field.
* Added filter 'ot_list_item_title_desc' to change the description for a List Item's required title field.
* Added filter 'ot_options_id' to change the 'option_tree' option ID to a unique value.
* Added filter 'ot_settings_id' to change the 'option_tree_settings' option ID to a unique value.
* Added filter 'ot_layouts_id' to change the 'option_tree_layouts' option ID to a unique value.
* Added filter 'ot_header_logo_link' to change the logo link inside the header of OptionTree.
* Added filter 'ot_header_version_text' to change the version text inside the header of OptionTree.
* Added action 'ot_header_list' to add additional theme specific list items to the header of OptionTree.
* Added filter 'ot_upload_text' to change the "Send to OptionTree" text.
* Added the CSS Class field value to the parent `.format-settings` div in addition to the class being added to the element. Each class is now appended with `-wrap`.
* Added support for [Composer](https://github.com/composer/composer). Contributors via github @designst.
* Added support for adding I18n text domains to the exported `theme-options.php` file.
* Fixed a bug that kept the UI from displaying when using the `ot_type_background_size_choices` filter.
* Fixed a bug that caused the Gallery option type to save a single space instead of `null`.
* Fixed the return value of the Background, Measurement, and Typography option types. They now return `null` if no values are saved to the array.
* Fixed a bug that resulted in a PHP warning if the choices array was set to an empty string.
* Updated the documentation, including this `readme.txt` and a new demo [OptionTree Theme](https://github.com/valendesigns/option-tree-theme) to parallel OptionTree.
* Added filter 'ot_type_radio_image_src' which allows the Radio Image option type source URI to be changed. Contributors via github @bitcommit.

= 2.2.3 =
* Hotfix - Allow empty condition values. For example, `field_id:is()` or `field_id:not()` would now be valid syntax.
* Hotfix - Fixed a bug in the `init_upload_fix` JavaScript method.
* Hotfix - Fixed a bug in the `url_exists` javaScript method. The code will no longer will check if a URL exists on another domain.

= 2.2.2 =
* Hotfix - Added support for both upper and lower case conditions operator.
* Hotfix - Updated the color and font size of inline code.
* Hotfix - Fix an issue with IE filter and updated the style of the On/Off option type.
* Hotfix - Added opacity to radio images to improve distinction. Contributors via github @jetonr.

= 2.2.1 =
* Hotfix - Fixed a UI bug that caused the layouts input to cover the wp menu.
* Hotfix - Moved the screen shots to the WordPress SVN assets directory.

= 2.2.0 =
* Added the Gallery option type.
* Added the On/Off option type.
* Replaced the old Color Picker with the default WP Color Picker.
* Added UI support for WordPress 3.8. Contributors via github @AlxMedia, and @valendesigns.
* Added support for conditional toggling of settings fields. Contributors via github @maimairel, @valendesigns, @doitmax, and @imangm.
* Replaced the OptionTree image icon with a font version.
* Added 'background-size' to the Background option type.
* Added fallback text when displaying posts without titles in various option types.
* Added filter 'ot_recognized_background_fields' to show/hide fields for background option types.
* Added filter 'ot_filter_description' that allows the theme option descriptions to be filtered before being displayed.
* Added subfolder compatibility in theme mode. Contributors via github @doitmax, and @valendesigns.
* Fixed a bug caused by using 'home_url' when loading dynamic CSS files.
* Fixed an issue where you could not save metabox text field values as "0". Contributors via github @sparkdevelopment, and @valendesigns.
* Fixed the broken localization directory path in theme mode. Contributors via github @youri--, and @valendesigns.
* Fixed missing custom class for the Numeric Slider. Contributors via github @doitmax.
* Added filter 'ot_type_category_checkbox_query' which allows you to filter the get_categories() args for Category Checkbox.
* Added filter 'ot_type_category_select_query' which allows you to filter the get_categories() args for Category Select.
* Added filter 'ot_type_taxonomy_checkbox_query' which allows you to filter the get_categories() args for Taxonomy Checkbox.
* Added filter 'ot_type_taxonomy_select_query' which allows you to filter the get_categories() args for Taxonomy Select.
* Added the 'ot_echo_option' function. Contributors via github @joshlevinson.
* Added filter 'ot_theme_options_contextual_help' which allows you to filter the Contextual Help on the Theme Options page.
* Added filter 'ot_theme_options_sections' which allows you to filter the Sections on the Theme Options page. Contributors via github @joshlevinson.
* Added filter 'ot_theme_options_settings' which allows you to filter the Settings on the Theme Options page. Contributors via github @joshlevinson.

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

= 2.3.0 =
As with each major release, please install OptionTree on a test server before upgrading your live site.

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