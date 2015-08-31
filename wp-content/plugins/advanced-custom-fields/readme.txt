=== Advanced Custom Fields ===
Contributors: elliotcondon
Tags: custom, field, custom field, advanced, simple fields, magic fields, more fields, repeater, matrix, post, type, text, textarea, file, image, edit, admin
Requires at least: 3.5.0
Tested up to: 4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customise WordPress with powerful, professional and intuitive fields

== Description ==

Advanced Custom Fields is the perfect solution for any wordpress website which needs more flexible data like other Content Management Systems. 

* Visually create your Fields
* Select from multiple input types (text, textarea, wysiwyg, image, file, page link, post object, relationship, select, checkbox, radio buttons, date picker, true / false, repeater, flexible content, gallery and more to come!)
* Assign your fields to multiple edit pages (via custom location rules)
* Easily load data through a simple and friendly API
* Uses the native WordPress custom post type for ease of use and fast processing
* Uses the native WordPress metadata for ease of use and fast processing

= Field Types =
* Text (type text, api returns text)
* Text Area (type text, api returns text)
* Number (type number, api returns integer)
* Email (type email, api returns text)
* Password (type password, api returns text)
* WYSIWYG (a wordpress wysiwyg editor, api returns html)
* Image (upload an image, api returns the url)
* File (upload a file, api returns the url)
* Select (drop down list of choices, api returns chosen item)
* Checkbox (tickbox list of choices, api returns array of choices)
* Radio Buttons ( radio button list of choices, api returns chosen item)
* True / False (tick box with message, api returns true or false)
* Page Link (select 1 or more page, post or custom post types, api returns the selected url)
* Post Object (select 1 or more page, post or custom post types, api returns the selected post objects)
* Relationship (search, select and order post objects with a tidy interface, api returns the selected post objects)
* Taxonomy (select taxonomy terms with options to load, display and save, api returns the selected term objects)
* User (select 1 or more WP users, api returns the selected user objects)
* Google Maps (interactive map, api returns lat,lng,address data)
* Date Picker (jquery date picker, options for format, api returns string)
* Color Picker (WP color swatch picker)
* Tab (Group fields into tabs)
* Message (Render custom messages into the fields)
* Repeater (ability to create repeatable blocks of fields!)
* Flexible Content (ability to create flexible blocks of fields!)
* Gallery (Add, edit and order multiple images in 1 simple field)
* [Custom](http://www.advancedcustomfields.com/resources/tutorials/creating-a-new-field-type/) (Create your own field type!)

= Tested on =
* Mac Firefox 	:)
* Mac Safari 	:)
* Mac Chrome	:)
* PC Safari 	:)
* PC Chrome		:)
* PC Firefox	:)
* iPhone Safari :)
* iPad Safari 	:)
* PC ie7		:S

= Website =
http://www.advancedcustomfields.com/

= Documentation =
* [Getting Started](http://www.advancedcustomfields.com/resources/#getting-started)
* [Field Types](http://www.advancedcustomfields.com/resources/#field-types)
* [Functions](http://www.advancedcustomfields.com/resources/#functions)
* [Actions](http://www.advancedcustomfields.com/resources/#actions)
* [Filters](http://www.advancedcustomfields.com/resources/#filters)
* [How to guides](http://www.advancedcustomfields.com/resources/#how-to)
* [Tutorials](http://www.advancedcustomfields.com/resources/#tutorials)

= Bug Submission and Forum Support =
http://support.advancedcustomfields.com/

= Please Vote and Enjoy =
Your votes really make a difference! Thanks.


== Installation ==

1. Upload 'advanced-custom-fields' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the new menu item "Custom Fields" and create your first Custom Field Group!
4. Your custom field group will now appear on the page / post / template you specified in the field group's location rules!
5. Read the documentation to display your data: 


== Frequently Asked Questions ==

= Q. I have a question =
A. Chances are, someone else has asked it. Check out the support forum at: 
http://support.advancedcustomfields.com/


== Screenshots ==

1. Creating the Advanced Custom Fields

2. Adding the Custom Fields to a page and hiding the default meta boxes

3. The Page edit screen after creating the Advanced Custom Fields

4. Simple and intuitive API. Read the documentation at: http://www.advancedcustomfields.com/resources/


== Changelog ==

= 4.4.2 =
* Image field: Fixed UI bug when image has been removed via media library
* Core: Minor fixes and improvements

= 4.4.1 =
* Taxonomy field: Added compatibility for upcoming 'term splitting' in WP 4.2
* Taxonomy field: Major improvement to save/load setting allowing for different values on multiple sub fields
* Core: Minor fixes and improvements

= 4.4.0 =
* Core: Fixed depreciated warnings

= 4.3.9 =
* Core: Added compatibility for WP4 media grid
* Relationship field: Fixed bug showing incorrect post type
* Language: Added Slovak translations - Thanks to wp.sk
* Language: Added Serbo-Croatian translation - thanks to Borisa Djuraskovic
* Language: Updating Persian translation - Thanks to Ghaem Omidi

= 4.3.8 =
* Validation: Fixed disabled button issue in WP 3.9

= 4.3.7 =
* WYSIWYG field: Fixed missing tinyMCE buttons in WP 3.9

= 4.3.6 =
* Core: Improved efficiency and speed when saving values by removing ACF meta from the native WP postmeta box
* Field Group: Fixed cache issue causing field settings to not update
* WYSIWYG field: Added support for new tinymce 4 in WP 3.9
* Number field: Fixed bug causing blank values to save as 0
* Google Maps field: Fixed JS bug causing google maps to not render when Google library is already loaded
* Validation: Fixed JS bug where hidden field groups's fields were being validated

= 4.3.5 =
* Textarea field: Added new `rows` setting
* API: Added `$format_value` parameter to the `get_fields` function
* Core: Improved conditional logic & tab JS performance
* Core: Removed changelog anouncement in plugins update list
* Core: Fixed anoying `wp is not defined` JS error
* Core: Added logic to load full or minified scripts using the `SCRIPT_DEBUG` constant
* Core: Improved loading structure to better allow ACF functions within the functions.php file
* Core: Fixed revisions bug causing sub field data to not restore
* Core: Made use of WP datepicker UI
* Field Group: Changed post location rule to show all post types
* Field Group: Changed page location rule to show only page post type
* Field Group: Added new filter for meta box priority `acf/input/meta_box_priority`
* Language: Added missing translation support in multiple fields
* Language: Added Hebrew translation - Thanks to Erez Lieberman
* Language: Updating Czech translations - Thanks to webeescz

= 4.3.4 =
* Post Object field: Fixed get_pages bug cuasing 'pages' to not appear
* Page Link field: Fixed get_pages bug cuasing 'pages' to not appear
* Tab field: Fixed JS bug causing multiple tab groups on page to render incorrectly
* Language: Updated Russian translation - Thanks to Alex Torscho

= 4.3.3 =
* Core: Updated styling to suit WP 3.8
* Core: Added new logic to set 'autoload' to 'off' on all values saved to the wp_options table to help improve load speed
* API: Added new logic to the $post_id parameter to accept an object of type post, user or taxonomy term
* Tab field: Added compatibility with taxonomy term and user edit screens (table layout)
* Tab field: Fixed JS bug causing incorrect tab to show when validation fails
* Text field: Fixed bug causing append setting of '+50' to appear as '50'

= 4.3.2 =
* Color Picker field: Fixed JS bug preventing wpColorPicker from updating value correctly
* Google Map field: Added new setting for initial zoom level
* Relationship field: minor update to fix compatibility issue with Polylang plugin
* Relationship field: Fixed bug causing filters / actions using $field['name'] to not fire correctly
* API: Fixed bug with have_rows/has_sub_field function where looping through multiple posts each containing nested repeater fields would result in an endless loop
* Export: Fixed bug causing exported XML fields to become corrupt due to line breaks
* Core: Fixed bug where duplicating a field would cause conditional logic to appear blank
* Core: Added Conditional Logic support to hide entire column of a repeater field where max_row is 1.
* Core: Added new field group 'hide on screen' option for 'permalink' which hides the permalink URL and buttons below the post title

= 4.3.1 =
* API: Fixed bug with has_sub_field and have_rows functions causing complicated nested loops to produce incorrect results
* API: Fixed bug with get_fields function preventing values to be returned from options page and taxonomy terms
* Core: Fixed bug causing some SQL LIKE statements to not work correctly on windows servers
* Core: Removed __() wrappers from PHP export, as these did not work as expected
* Core: Fixed bug with get_pages() causing sort order issue in child page location rule
* Core: Added specific position to ACF menu item to reduce conflicts with 3rd party plugins
* JS: Fixed bug where conditional logic rules did not save when added using a '+' button above the last rule
* Radio field: Fixed bug where 'other' would be selected when no value exists
* WYSIWYG field: Added support for users with disabled visual editor setting
* JS: Improved validation for fields that are hidden by a tab
* Google maps field: Add refresh action when hidden / shown by a tab

= 4.3.0 =
* Core: get_field can now be used within the functions.php file
* Core: Added new Google maps field
* Core: Added conditional logic support for sub fields - will also require an update to the repeater / flexible content field add-on to work
* Core: Added required validation support for sub fields - will also require an update to the repeater / flexible content field add-on to work
* API: Added new function have_rows()
* API: Added new function the_row()
* API: Fixed front end form upload issues when editing a user - http://support.advancedcustomfields.com/forums/topic/repeater-image-upload-failing/
* API: Fixed front end form bug where the wrong post_id is being passed to JS - http://support.advancedcustomfields.com/forums/topic/attachments-parent-id/
* Export: wrapped title and instructions in __() function - http://support.advancedcustomfields.com/forums/topic/wrap-labels-and-descriptions-with-__-in-the-php-export-file/
* Core: Filter out ACF fields from the native custom field dropdown - http://support.advancedcustomfields.com/forums/topic/meta-key-instead-of-name-on-add-new-custom-field-instead-of-name/ - http://support.advancedcustomfields.com/forums/topic/odd-sub-field-names-in-custom-fields/
* Revisions: Improved save functionality to detect post change when custom fields are edited - http://support.advancedcustomfields.com/forums/topic/wordpress-3-6-revisions-custom-fields-no-longer-tracked/
* Core: Add field group title for user edit screen - http://support.advancedcustomfields.com/forums/topic/can-you-add-a-title-or-hr-tag-when-using-acf-in-taxonomy-edit-screen/
* Field group: Add 'toggle all' option to hide from screen - http://support.advancedcustomfields.com/forums/topic/hidecheck-all-single-checkbox-when-hiding-items-from-pagepost-edit-screen/
* Taxonomy field: Add new filter for wp_list_categories args - http://support.advancedcustomfields.com/forums/topic/taxonomy-field-type-filter-to-only-show-parents/
* Taxonomy field: Fixed JS bug causing attachment field groups to disappear due to incorrect AJAX location data - http://support.advancedcustomfields.com/forums/topic/taxonomy-checkboxes/
* WYSIWYG field: Fixed JS bug where formatting is removed when drag/drop it's repeater row
* Tab field: Corrected minor JS bugs with conditional logic - http://support.advancedcustomfields.com/forums/topic/tabs-logic-hide-issue/
* Relationship field: Values now save correctly as an array of strings (for LIKE querying)
* Post object field: Values now save correctly as an array of strings (for LIKE querying)
* Image field: Added mime_type data to returned value
* Field field: Added mime_type data to returned value
* Core: Lots of minor improvements

= 4.2.2 =
* Field group: Added 'High (after title)' position for a metabox - http://support.advancedcustomfields.com/forums/topic/position-after-title-solution-inside/
* Relationship field: Fixed bug with 'exclude_from_search' post types
* Image / File field: Improved edit popup efficiency and fixed bug when 'upload' is last active mode - http://support.advancedcustomfields.com/forums/topic/edit-image-only-shows-add-new-screen/
* JS: Added un compressed input.js file
* JS: Fixed but with options page / taxonomy field - http://support.advancedcustomfields.com/forums/topic/checkbox-issues/
* Language: Updated Persian Translation - thanks to Ghaem Omidi

= 4.2.1 =
* Taxonomy field: Fixed issue causing selected terms to appear as numbers - http://support.advancedcustomfields.com/forums/topic/latest-update-4-2-0-taxonomy-field-not-working-correctly/
* Revisions: Fixed WP 3.6 revisions - http://support.advancedcustomfields.com/forums/topic/wordpress-3-6-revisions-custom-fields-no-longer-tracked/
* Relationship Field: Add new option for return_format
* Location Rule - Add new rule for post status - http://support.advancedcustomfields.com/forums/topic/location-rules-post-status/
* Location Rule: Add 'super admin' to users rule - thanks to Ryan Nielson - https://github.com/RyanNielson/acf/commit/191abf35754c242f2ff75ac33ff8a4dca963a6cc
* Core: Fixed pre_save_post $post_id issue - http://support.advancedcustomfields.com/forums/topic/frontend-form-issues-pre_save_post-save_post/
* Core: Fix minor CSS but in media modal - http://support.advancedcustomfields.com/forums/topic/minor-css-issue-in-media-upload-lightbox/#post-2138
* File field: Fix minor 'strict standards' warning - http://support.advancedcustomfields.com/forums/topic/strict-standards-error-on-file-upload/
* Image field: Fix minor CSS issue - http://support.advancedcustomfields.com/forums/topic/firefox-repeaterimage-css/

= 4.2.0 =
* IMPORTANT: ACF now requires a minimum WordPress version of 3.5.0
* Full integration between attachments and custom fields!
* Text field: Added new options for prepend, append, placeholder and character limit
* Textarea field: Added new options for prepend, append, placeholder and character limit
* Number field: Added new options for prepend, append and placeholder
* Email field: Added new options for prepend, append and placeholder
* Password field: Added new options for prepend, append and placeholder
* Image field: fixed safari bug causing all images to appear small
* Core: Improved save_lock functionality to prevent inifinite loops when creating a post on the fly
* Core: Major JS improvements including .live changed to .on
* Compatibility: Fixed WYSIWYG JS bug with Visual Composer plugin
* Language: Added Persian Translation - thanks to Ghaem Omidi
* Language: Updated German translation - thanks to Thomas Meyer
* Language: Added Swedish translation - thanks to Mikael Jorhult

= 4.1.8.1 =
* Select field: Revert choices logic - http://support.advancedcustomfields.com/forums/topic/select-field-label-cut-off-at/#post-529
* CSS: Revert metabox CSS - http://support.advancedcustomfields.com/forums/topic/standard-metabox-margins-reversed/#post-456
* Core: Fixed save_post conflict with Shopp plugin - http://support.advancedcustomfields.com/forums/topic/no-data-is-saving-with-shopp-acf-4-1-8/

= 4.1.8 =
* Core: Fix issue with cache $found variable preventing values from being loaded
* Select field: Improve choices textarea detection - http://old.support.advancedcustomfields.com/discussion/6598/select-on-repeater-field
* Language: Added Swedish translation - https://github.com/elliotcondon/acf/pull/93
* Language: Updated Russian translation - https://github.com/elliotcondon/acf/pull/94

= 4.1.7 =
* Language: Added Russian translation - Thanks to Alex Torscho
* Core: Improved the save_post function to compare post_id and only run once.
* Core: Improved cache handling
* Number field: Fixed step size decimal bug
* Radio button field: Add option for 'other' and to also update field choices
* Image / File field: Updated JS to add multiple items to the correct sub field - http://support.advancedcustomfields.com/discussion/6391/repeater-with-images-bug
* JS: Remove redundant return ajax value - http://support.advancedcustomfields.com/discussion/6375/js-syntax-error-in-ie
* Add-ons page: fix JS issue - http://support.advancedcustomfields.com/discussion/6405/add-ons-page-div-height-problem
* Options Page: Fixed issue with load_value preventing the options page using default values - http://support.advancedcustomfields.com/discussion/4612/true-false-field-allow-default-value
* AJAX: Fix chrome bug - untick category - http://support.advancedcustomfields.com/discussion/6419/disabling-a-category-still-shows-fields
* JS: Fixed multiple Internet Explorer issues

= 4.1.6 =
* General: Improved load_value function to better handle false and default values
* Number field: Added new options for min, max and step - http://support.advancedcustomfields.com/discussion/6263/fork-on-numbers-field
* Radio field: Improved logic for selecting the value. Now works with 0, false, null and any other 'empty' value - http://support.advancedcustomfields.com/discussion/6305/radio-button-issue-with-0-values-fix-included-
* Date picker field: Fixed PHP error - http://support.advancedcustomfields.com/discussion/6312/date-picker-php-error-date_picker-php-line-138-screenshot-attached
* Language: Added Portuguese translation - https://github.com/elliotcondon/acf/pull/64
* Taxonomy: Updated JS to clear image / file and checkbox elements when a new category is added via AJAX - http://support.advancedcustomfields.com/discussion/6326/image-field-added-to-categories-field-remains-set-after-category-created
* Validation: Added logic to allow a field to bypass validation if it is part of a tab group which is hidden via conditional logic
* API: Improved the acf_form function to better handle form attributes

= 4.1.5.1 =
* Image field: Fixed JS error causing uploader to not work correctly
* File field: Fixed JS error causing uploader to not work correctly
* Gallery field: Fixed JS error causing uploader to not work correctly
* General: Fixed JS error causing field groups to not appear when dynamically loaded

= 4.1.5 =
* WYSIWYG Field: Fixed WYSIWYG the_content / shortcode issues - http://support.advancedcustomfields.com/discussion/5939/inconsistencies-between-standard-wysiwyg-and-acf-wysiwyg
* Image field: Added option for library restriction - http://support.advancedcustomfields.com/discussion/6102/making-uploaded-to-this-post-default-state-for-image-upload
* File field: Added option for library restriction
* File field: Field UI refresh
* Checkbox field: Added horizontal option - http://support.advancedcustomfields.com/discussion/5925/horizontal-select-boxes
* Image field: fixed UI bug when image is deleted in file system - http://support.advancedcustomfields.com/discussion/5988/provide-a-fallback-if-
* Validation: Added support for email field - http://support.advancedcustomfields.com/discussion/6125/email-field-required-validation-on-submit
* Validation: Added support for taxonomy field - http://support.advancedcustomfields.com/discussion/6169/validation-of-taxonomy-field
* Language: Added Chinese Translation - https://github.com/elliotcondon/acf/pull/63
* General: Added changelog message to update plugin screen
* General: Lots of minor improvements

= 4.1.4 =
* [Fixed] Page Link: Fixed errors produced by recent changes to post object field - http://support.advancedcustomfields.com/discussion/6044/page-links-hierarchy-broken-and-does-not-order-correctly

= 4.1.3 =
* [Fixed] Relationship field: Fix global $post conflict issues - http://support.advancedcustomfields.com/discussion/6022/bug-with-4-1-2-acf-rewrite-global-post

= 4.1.2 =
* [Added] Post Object field: Add filter to customize choices - http://support.advancedcustomfields.com/discussion/5883/show-extra-post-info-in-a-post-object-dropdown-list
* [Fixed] Relationship field: Fix error when used as grand child - http://support.advancedcustomfields.com/discussion/5898/in_array-errors-on-relationship-field
* [Added] User field: Add sanitisation into update_value function to allow for array / object with ID attribute
* [Added] Relationship field: Add sanitisation into update_value function to allow for array of post object to be saved
* [Added] Post Object field: Add sanitisation into update_value function to allow for a post object or an array of post objects to be saved
* [Added] Image field: Add sanitisation into update_value function to allow for a post object or an image array to be saved
* [Added] File field: Add sanitisation into update_value function to allow for a post object or an file array to be saved
* [Fixed] Revisions: Fix PHP warning if array value exists as custom field - http://support.advancedcustomfields.com/discussion/984/solvedwarning-htmlspecialchars-text-php-on-line-109
* [Updated] Translation: Update French Translation - http://support.advancedcustomfields.com/discussion/5927/french-translation-for-4-1-1
* [Fixed] General: Minor PHP errors fixed

= 4.1.1 =
* [Fixed] Relationship field: Fix bug causing sub field to not load $field object / use elements option correctly
* [Updated] Update German translations

= 4.1.0 =
* [Added] Field group: location rules can now be grouped into AND / OR statements
* [Added] Relationship field: Add option for filters (search / post_type)
* [Added] Relationship field: Add option for elements (featured image / title / post_type)
* [Added] Relationship field: Add post_id and field parameters to both ajax filter functions
* [Added] Date Picker field: Add options for first_day
* [Added] Date Picker field: Add text strings for translation
* [Added] Select field: Add support for multiple default values
* [Added] Checkbox field: Add support for multiple default values - http://support.advancedcustomfields.com/discussion/5635/checkbox-field-setting-multiple-defaults
* [Updated] Minor JS + CSS improvements
* [Added] Added free Add-ons to the admin page
* [Fixed] Fixed minor bugs

= 4.0.3 =
* [Fixed] Fix bug when appending taxonomy terms - http://support.advancedcustomfields.com/discussion/5522/append-taxonomies
* [Fixed] Fix embed shortcode for WYSIWYG field - http://support.advancedcustomfields.com/discussion/5503/embed-video-wysiwyg-field-doesn039t-work-since-update
* [Fixed] Fix issues with loading numbers - http://support.advancedcustomfields.com/discussion/5538/zero-first-number-problem-in-text-fields
* [Fixed] Fix bug with user field and format_value_for_api - http://support.advancedcustomfields.com/discussion/5542/user-field-weirdness-after-update
* [Fixed] Fix capitalization issue on field name - http://support.advancedcustomfields.com/discussion/5527/field-name-retains-capitalization-from-field-title
* [Fixed] Fix tabs not hiding from conditional logic - http://support.advancedcustomfields.com/discussion/5506/conditional-logic-not-working-with-tabs
* [Updated] Update dir / path to allow for SSL - http://support.advancedcustomfields.com/discussion/5518/in-admin-page-got-error-javascript-when-open-with-https
* [Updated] Updated relationship JS - http://support.advancedcustomfields.com/discussion/5550/relationship-field-search-box

= 4.0.2 =
* [Added] Add auto video filter to WYSIWYG value - http://support.advancedcustomfields.com/discussion/5378/video-embed-in-wysiwyg-field
* [Fixed] Fix Repeater + WYSIWYG loosing p tags on drag/drop - http://support.advancedcustomfields.com/discussion/5476/acf-4-0-0-wysiwyg-p-tag-disappearing-after-drag-drop-save
* [Fixed] Fix upgrade message appearing in iframe
* [Fixed] Fix value sanitation - http://support.advancedcustomfields.com/discussion/5499/post-relationship-field-value-storage-in-update-to-acf4
* [Added] Add JS field name validation - http://support.advancedcustomfields.com/discussion/5500/replace-foreign-letters-when-creating-input-name-from-label-in-javascript
* [Fixed] Fix error when duplicating field group in WPML - http://support.advancedcustomfields.com/discussion/5501/4-0-1-broke-wpml-functionality-
* [Fixed] Fix pares_type issue. Maybe remove it? - http://support.advancedcustomfields.com/discussion/5502/zeros-get-removed-major-bug

= 4.0.1 =
* [Improved] Improving welcome message with download instructions
* [Fixed] Text / Fix JS issue where metaboxes are not hiding - http://support.advancedcustomfields.com/discussion/5443/bug-content-editor
* [Fixed] Test / Fix lite mode issue causing category / user fields not to show
* [Fixed] Sanitize field names - http://support.advancedcustomfields.com/discussion/5262/sanitize_title-on-field-name
* [Fixed] Test / Fix conditional logic not working for mutli-select - http://support.advancedcustomfields.com/discussion/5409/conditional-logic-with-multiple-select-field
* [Fixed] Test / Fix field group duplication in WooCommerce category w SEO plugin - http://support.advancedcustomfields.com/discussion/5440/acf-woocommerce-product-category-taxonomy-bug

= 4.0.0 =
* [IMPORTANT] This update contains major changes to premium and custom field type Add-ons. Please read the [Migrating from v3 to v4 guide](http://www.advancedcustomfields.com/resources/getting-started/migrating-from-v3-to-v4/)
* [Optimized] Optimize performance by removing heavy class structure and implementing light weight hooks & filters!
* [Changed] Remove all Add-on code from the core plugin and separate into individual plugins with self hosted updates
* [Added] Add field 'Taxonomy'
* [Added] Add field 'User'
* [Added] Add field 'Email'
* [Added] Add field 'Password'
* [Added] Add field group title validation
* [Fixed] Fix issue where get_field_object returns the wrong field when using WPML
* [Fixed] Fix duplicate functionality - http://support.advancedcustomfields.com/discussion/4471/duplicate-fields-in-admin-doesn039t-replicate-repeater-fields 
* [Added] Add conditional statements to tab field - http://support.advancedcustomfields.com/discussion/4674/conditional-tabs
* [Fixed] Fix issue with Preview / Draft where preview would not save custom field data - http://support.advancedcustomfields.com/discussion/4401/cannot-preview-or-schedule-content-to-be-published
* [Added] Add function get_field_groups()
* [Added] Add function delete_field() - http://support.advancedcustomfields.com/discussion/4788/deleting-a-field-through-php
* [Added] Add get_sub_field_object function - http://support.advancedcustomfields.com/discussion/4991/select-inside-repeaterfield
* [Added] Add 'Top Level' option to page type location rule
* [Fixed] Fix taxonomy location rule - http://support.advancedcustomfields.com/discussion/5004/field-group-rules-issue
* [Fixed] Fix tab field with conditional logic - https://github.com/elliotcondon/acf4/issues/14
* [Fixed] Revert back to original field_key idea. attractive field key's cause too many issues with import / export
* [Added] Add message field - http://support.advancedcustomfields.com/discussion/5263/additional-description-field
* [Removed] Removed the_content filter from WYSIWYG field

= 3.5.8.1 =
* [Fixed] Fix PHP error in text / textarea fields

= 3.5.8 =
* [Fixed] Fix bug preventing fields to load on user / taxonomy front end form - http://support.advancedcustomfields.com/discussion/4393/front-end-user-profile-field-form-causes-referenceerror
* [Added] Added 'acf/fields/wysiwyg/toolbars' filter to customize WYSIWYG toolbars - http://support.advancedcustomfields.com/discussion/2205/can-we-change-wysiwyg-basic-editor-buttons
* [Fixed] Fix acf_load_filters as they are not working! - http://support.advancedcustomfields.com/discussion/comment/12770#Comment_12770
* [Added] Clean up wp_options after term delete - http://support.advancedcustomfields.com/discussion/4396/delete-taxonomy-term-custom-fields-after-term-delete
* [Fixed] Fix location rule - category / taxonomy on new post - http://support.advancedcustomfields.com/discussion/3635/show-custom-fields-on-post-adding
* [Added] Added 'acf/create_field' action for third party usage - docs to come soon
* [Added] Add support for new media uploader in WP 3.5!
* [Fixed] Fix conditional logic error - http://support.advancedcustomfields.com/discussion/4502/conditional-logic-script-output-causes-events-to-fire-multiple-times
* [Fixed] Fix Uploader not working on taxonomy edit screens - http://support.advancedcustomfields.com/discussion/4536/media-upload-button-for-wysiwyg-does-not-work-when-used-on-a-taxonomy-term
* [Added] Add data cleanup after removing a repeater / flexible content row - http://support.advancedcustomfields.com/discussion/1994/deleting-single-repeater-fields-does-not-remove-entry-from-database 


= 3.5.7.2 =
* [Fixed] Fix fields not showing on attachment edit page in WP 3.5 - http://support.advancedcustomfields.com/discussion/4261/after-upgrading-to-3-5-acf-fields-assigned-to-show-on-attachments-media-edit-are-not-showing
* [Fixed] Fix sub repeater css bug - http://support.advancedcustomfields.com/discussion/4361/repeater-add-button-inappropriately-disabled
* [Fixed] Fix issue where acf_form includes scripts twice - http://support.advancedcustomfields.com/discussion/4372/afc-repeater-on-front-end
* [Fixed] Fix location rule bug with new shopp product - http://support.advancedcustomfields.com/discussion/4406/shopp-idnew-product-page-doesn039t-have-acf-fields
* [Fixed] Fix location rule bug with taxonomy / post_taxonomy - http://support.advancedcustomfields.com/discussion/4407/taxonomy-rules-ignored-until-toggling-the-taxonomy

= 3.5.7.1 =
* [Fixed] Fix issues with location rules wrongly matching

= 3.5.7 =
* [Fixed] Fix sub field default value - http://support.advancedcustomfields.com/discussion/3706/select-field-default-value-not-working
* [Added] Add filters for custom location rules - http://support.advancedcustomfields.com/discussion/4285/how-to-retrieve-a-custom-field-within-the-function-php
* [Fixed] Fix XML import to create unique field ID's - http://support.advancedcustomfields.com/discussion/4328/check-acf_next_field_id-to-avoid-data-corruption
* [Fixed] Fix conditional logic with validation - http://support.advancedcustomfields.com/discussion/4295/issue-with-conditional-logic-and-obrigatory-fields
* [Fixed] Fix repeater + relationship bug - http://support.advancedcustomfields.com/discussion/4296/relationship-field-bug

= 3.5.6.3 =
* [Fixed] Fix bug with 3.5.6 not showing front end form

= 3.5.6.2 =
* [Fixed] Fix WYSIWYG webkit browser issues.

= 3.5.6.1 =
* [Fixed] Fix bug causing field groups to not display on the options page.

= 3.5.6 =
* [Fixed] Fix content editor double in webkit browser - http://support.advancedcustomfields.com/discussion/4223/duplicate-editor-box-safari-bug-has-returned
* [Fixed] Fix bug with post format location rule not working - http://support.advancedcustomfields.com/discussion/4264/not-recognizing-post-type-formats-following-upgrade-to-version-3-5-5
* [Fixed] Fix conditional logic with tabs - http://support.advancedcustomfields.com/discussion/4201/tabs-and-logical-condition
* [Fixed] Fix missing icons for conditional logic / menu in older WP
* [Added] Add PHP fix for new lines in field key - http://support.advancedcustomfields.com/discussion/4087/can039t-add-new-field

= 3.5.5 =
* [Added] Add new Tab field
* [Fixed] Improve WYSIWYG code for better compatibility
* [Fixed] Fix PHP / AJAX error during database update for older versions
* [Fixed] WYSIWYG insert attachment focus bug - http://support.advancedcustomfields.com/discussion/4076/problem-with-upload-in-wysiwyg-editors-in-combination-with-flexible-content
* [Fixed] Fix JS coma issues for IE - http://support.advancedcustomfields.com/discussion/4064/ie-javascript-issues-on-editing-field-group
* [Added] Add no cache to relationship field results - http://support.advancedcustomfields.com/discussion/2325/serious-memory-issue-using-post-objectrelationship-field-with-only-5000-posts
* [Added] Add retina support
* [Fixed] Fix WYSIWYG validation for preview post - http://support.advancedcustomfields.com/discussion/4055/validation-failing-on-required-wysiwyg-field
* [Fixed] Fix undefined index error in field's conditional logic - http://support.advancedcustomfields.com/discussion/4165/undefined-index-notice-on-php-export
* [Updated] Update post types in field options - http://support.advancedcustomfields.com/discussion/3656/acf-for-custom-post-type
* [Added] Add filters to relationship field results
* [Added] Add file name bellow title in popup for selecting a file

= 3.5.4.1 =
* [Fixed] Fix bug preventing options pages from appearing in the field group's location rules

= 3.5.4 =
* [Added] Add new filter for ACF settings - http://www.advancedcustomfields.com/docs/filters/acf_settings/
* [Updated] Updated field keys to look nicer. eg field_12
* [Added] Update admin_head to use hooks / enque all scripts / styles
* [Added] Add duplicate function for flexible content layouts
* [Fixed] Fix $post_id bug - http://support.advancedcustomfields.com/discussion/3852/acf_form-uses-global-post_id-instead-of-argument
* [Fixed] Fix WYSIWYG JS issue - http://support.advancedcustomfields.com/discussion/3644/flexible-layout-field-reordering-breaks-when-visual-editor-disabled
* [Fixed] Fix Gallery PHP error - http://support.advancedcustomfields.com/discussion/3856/undefined-index-error-gallery-on-options-page
* [Added] Add compatibility for Shopp categories - http://support.advancedcustomfields.com/discussion/3647/custom-fields-not-showing-up-in-shopp-catalog-categories
* [Fixed] Fix "Parent Page" location rule - http://support.advancedcustomfields.com/discussion/3885/parent-page-type-check
* [Fixed] Fix options page backwards compatibility - support.advancedcustomfields.com/discussion/3908/acf-options-page-groups-are-not-backward-compatible
* [Fixed] Fix update_field for content - http://support.advancedcustomfields.com/discussion/3916/add-flexible-layout-row-with-update_field
* [Added] Add new filter for acf_defaults! - http://support.advancedcustomfields.com/discussion/3947/options-page-plugin-user-capabilites-limitation
* [Fixed] Fix gallery detail update after edit - http://support.advancedcustomfields.com/discussion/3899/gallery-image-attributes-not-updating-after-change
* [Fixed] Fix front end uploading issue - http://support.advancedcustomfields.com/discussion/comment/10502#Comment_10502

= 3.5.3.1 =
* Minor bug fixes for 3.5.3

= 3.5.3 =
* [Updated] Update / overhaul flexible content field UI
* [Added] Add Show / Hide for flexible content layouts
* [Added] Add column width for flexible content - http://support.advancedcustomfields.com/discussion/3382/percentage-widths-on-fc-fields
* [Added] Add instructions for flexible content sub fields
* [Added] Add new parameter to get_field to allow for no formatting - http://support.advancedcustomfields.com/discussion/3188/update_field-repeater
* [Fixed] Fix compatibility issue with post type switcher plugin - http://support.advancedcustomfields.com/discussion/3493/field-group-changes-to-post-when-i-save
* [Added] Add new location rules for "Front Page" "Post Page" - http://support.advancedcustomfields.com/discussion/3485/groups-association-whit-page-slug-instead-of-id
* [Fixed] Fix flexible content + repeater row limit bug - http://support.advancedcustomfields.com/discussion/3557/repeater-fields-inside-flexible-field-on-backend-not-visible-before-first-savingpublishing
* [Added] Add filter "acf_load_value" for values - http://support.advancedcustomfields.com/discussion/3725/a-filter-for-get_field
* [Fixed] Fix choices backslash issue - http://support.advancedcustomfields.com/discussion/3796/backslash-simple-quote-bug-in-radio-button-values-fields
* [Updated] acf_options_page_title now overrides the menu and title. If your field groups are not showing after update, please re-save them to update the location rules.
* [Updated] Update location rules to show all post types in page / page_parent / post
* [Added] Change all "pre_save_field" functions to "acf_save_field" hooks
* [Improved] Improve general CSS / JS

= 3.5.2 =
* Security update

= 3.5.1 =
* [Added] Add Conditional logic for fields (toggle fields are select, checkbox, radio and true / false)
* [Added] More hooks + filters - acf_options_page_title, acf_load_field, acf_update_value - http://support.advancedcustomfields.com/discussion/3454/more-hooks-filters-ability-for-inheritance
* [Removed] Remove public param from post types list - http://support.advancedcustomfields.com/discussion/3251/fields-on-a-non-public-post-type
* [Added] Add field group headings into the acf_form function
* [Updated] Update button design to match WP 3.5
* [Fixed] Test / Fix XML export issue - http://support.advancedcustomfields.com/discussion/3415/can039t-export-xml-since-upgrade-to-3-5-0
* [Added] Add more options to the "hide on screen" - http://support.advancedcustomfields.com/discussion/3418/screen-options
* [Added] Add compatibility for Tabify plugin - http://wordpress.org/support/topic/plugin-tabify-edit-screen-compatibility-with-other-custom-fields-plugins/page/2?replies=36#post-3238051
* [Added] Add compatibility for Duplicate Post plugin
* [Added] Add new params to acf_form function - http://support.advancedcustomfields.com/discussion/3445/issue-with-the-acf_form-array
* [Updated] Increase date picker range to 100
* [Fixed] WYSIWYG looses formatting when it's row gets reordered (in a repeater / flexible content field)
* [Fixed] Fix has_sub_field break issue - http://support.advancedcustomfields.com/discussion/3528/ability-to-reset-has_sub_field
* [Fixed] Fix Textarea / Text encoding bugs - http://support.advancedcustomfields.com/discussion/comment/5147#Comment_5147
* [Added] Add publish status for field groups - http://support.advancedcustomfields.com/discussion/3695/draft-status-for-field-groups
* [Updated] General tidy up & improvement of HTML / CSS / Javascript

= 3.5.0 =
* [Fixed] Fix missing title of PHP registered field groups on the media edit page
* [Added] Add revision support
* [Added] Allow save draft to bypass validation
* [Updated] Update Czech translation
* [Fixed] Fix XML export issue with line break - http://support.advancedcustomfields.com/discussion/3219/export-and-import-problem-mixed-line-endings
* [Fixed] Fix export to XML abspath issue - http://support.advancedcustomfields.com/discussion/2641/require-paths-in-export-php
* Update location rules for post_type - http://support.advancedcustomfields.com/discussion/3251/fields-on-a-non-public-post-type 
* Add "revisions" to list of hide-able options
* [Fixed] Fix bug with custom post_id param in acf_form - http://support.advancedcustomfields.com/discussion/2991/acf_form-outside-loop
* [Fixed] Fix bug in has_sub_field function where new values are not loaded for different posts if the field name is the same - http://support.advancedcustomfields.com/discussion/3331/repeater-field-templating-help-categories
* [Updated] Allow get_field to use field_key or field_name
* [Fixed] Fix update_field bug with nested repeaters
* [Updated] Update German translation files - thanks to Martin Lettner

= 3.4.3 =
* [Fixed] Fix PHP registered field groups not showing via AJAX - http://support.advancedcustomfields.com/discussion/3143/exported-php-code-doesnt-work-with-post-formats
* [Added] Add new return value for file { file object
* [Fixed] Test / Fix save_post priority with WPML + events + shopp plugin
* [Fixed] Fix bug where field groups don't appear on shopp product edit screens
* [Fixed] Fix bug with image field { selecting multiple images puts first image into the .row-clone tr - http://support.advancedcustomfields.com/discussion/3157/image-field-repeater

= 3.4.2 =
* [Fixed] Fix API functions for 'user_$ID' post ID parameter
* [Added] Color Picker Field: Default Value
* [Added] Add custom save action for all saves - http://support.advancedcustomfields.com/discussion/2954/hook-on-save-options
* [Updated] Update Dutch translations
* [Updated] Update get_field_object function to allow for field_key / field_name + option to load_value

= 3.4.1 =
* [Added] Save user fields into wp_usermeta http://support.advancedcustomfields.com/discussion/2758/get_users-and-meta_key
* [Added] Add compatibility with media tags plugin - http://support.advancedcustomfields.com/discussion/comment/7596#Comment_7596
* [Added] Wysiwyg Field: Add Default value option
* [Added] Number Field: Add Default value option
* [Fixed] Validate relationship posts - http://support.advancedcustomfields.com/discussion/3033/relationship-field-throws-error-when-related-item-is-trashed
* [Added] Allow "options" as post_id for get_fields - http://support.advancedcustomfields.com/discussion/1926/3-1-8-broke-get_fields-for-options
* [Added] Repeater Field: Add sub field width option
* [Added] Repeater Field: Add sub field description option
* [Updated] Repeater Field: Update UI design
* [Fixed] Fix missing ajax event on page parent - http://support.advancedcustomfields.com/discussion/3060/show-correct-box-based-on-page-parent
* [Updated] Update french translation - http://support.advancedcustomfields.com/discussion/3088/french-translation-for-3-4-0

= 3.4.0 =
* [Fixed] Fix validation rules for multiple select - http://support.advancedcustomfields.com/discussion/2858/multiple-select-validation-doesnt-work
* [Added] Add support for options page toggle open / close metabox
* [Fixed] Fix special characters in registered options page - http://support.advancedcustomfields.com/discussion/comment/7500#Comment_7500
* [Updated] CSS tweak for relationship field - http://support.advancedcustomfields.com/discussion/2877/relation-field-with-multiple-post-types-css-styling-problem-
* [Fixed] Fix datepicker blank option bug - http://support.advancedcustomfields.com/discussion/2896/3-3-9-date-picker-not-popping-up
* [Added] Add new function get_field_object to API - http://support.advancedcustomfields.com/discussion/290/field-label-on-frontend
* [Fixed] Fix field groups not showing for Shopp add new product - http://support.advancedcustomfields.com/discussion/3005/acf-shopp
* [Fixed] Move acf.data outside of the doc.ready in input-ajax.js
* [Fixed] Fix IE7 JS bug - http://support.advancedcustomfields.com/discussion/3020/ie7-fix-on-is_clone_field-function
* [Fixed] Fix relationship search - Only search title, not content
* [Updated] Update function update_field to use field_key or field_name
* [Added] Add field group screen option to show field keys (to use in save_field / update field)
* [Added] Add actions on all save events (action is called "acf_save_post", 1 param = $post_id)

= 3.3.9 =
* [Added] Add basic support for WPML - duplicate field groups, pages and posts for each language without corrupting ACF data!
* [Fixed] Fix date picker save null - http://support.advancedcustomfields.com/discussion/2844/bug-with-the-date-picker
* [Fixed] Fix color picker save null - http://support.advancedcustomfields.com/discussion/2683/allow-null-on-colour-pickers#Item_1
* [Fixed] Fix image object null result - http://support.advancedcustomfields.com/discussion/2852/3.3.8-image-field-image-object-always-returns-true-
* [Updated] Update Japanese translation - http://support.advancedcustomfields.com/discussion/comment/7384#Comment_7384
* [Added] WYSIWYG field option - disable "the_content" filter to allow for compatibility issues with plugins / themes - http://support.advancedcustomfields.com/discussion/comment/7020#Comment_7020

= 3.3.8 =
* [Added] Gallery field { auto add image on upload, new style to show already added images
* [Fixed] Fix saving value issue with WP e-commerce http://support.advancedcustomfields.com/discussion/comment/7026#Comment_7026
* [Updated] Date picker field { new display format option (different from save format), UI overhaul
* [Added] Add new field - Number
* [Fixed] Test post object / select based fields for saving empty value - http://support.advancedcustomfields.com/discussion/2759/post-object-and-conditional-statement

= 3.3.7 =
* [Added] Add new return value for image { image object
* [Updated] Update Dutch translation (thanks to Derk Oosterveld - www.inpoint.nl)
* [Updated] Update UI Styles
* [Updated] Refresh settings page UI and fix exported PHP code indentation Styles
* [Fixed] Fix post object hierarchy display bug - http://support.advancedcustomfields.com/discussion/2650/post_object-showing-posts-in-wrong-hierarchy
* [Fixed] Fix metabox position from high to core - http://support.advancedcustomfields.com/discussion/comment/6846#Comment_6846
* [Fixed] Fix flexible content field save layout with no fields - http://support.advancedcustomfields.com/discussion/2639/flexible-content-field-support-for-empty-layoutss
* [Fixed] Text / Fix field group limit - http://support.advancedcustomfields.com/discussion/2675/admin-only-showing-20-fields-groups

= 3.3.6 =
* [Fixed] Fix IE regex issue (thanks to Ben Heller - http://spruce.it)
* [Added] Check for more translatable strings (thanks to Derk Oosterveld - www.inpoint.nl)
* [Fixed] Fix location rule post category bug
* [Updated] Added all post status to page / post location rules - http://support.advancedcustomfields.com/discussion/2624/scheduled-pages
* [Updated] Updated the page link field to rely on the post_object field
* [Added] Add $post_id parameter to the [acf] shortcode

= 3.3.5 =
* [Fixed] Fix location rule bug for taxonomy.

= 3.3.4 = 
* [Added] Added new API function: has_sub_field - replacement for the_repeater_field and the_flexible_field. Allows for nested while loops! 
* [Improved] Improve save_post functions- http://support.advancedcustomfields.com/discussion/2540/bug-fix-for-taxonomies-and-revisions-solved
* [Fixed] Fix relationship AJAX abort for multiple fields - http://support.advancedcustomfields.com/discussion/2555/problem-width-relationship-after-update-the-latest-version

= 3.3.3 =
* [Upgrade] Database Upgrade is required to modify the taxonomy filtering data for fields. This allows for performance boosts throughout ACF.
* [Improved] relationship field: Improve querying posts / results and use AJAX powered search to increase performance on large-scale websites
* [Improved] post object field: Improve querying posts / results

= 3.3.2 =
* [Fixed] Integrate with Shopp plugin

= 3.3.1 =
* [Fixed] Fix gallery sortable in repeater - http://support.advancedcustomfields.com/discussion/2463/gallery-within-a-repeater-image-reorder-not-working
* [Fixed] Test / Fix two gallery fields - http://support.advancedcustomfields.com/discussion/2467/gallery-two-gallery-fieldss
* [Fixed] Fix tinymce undefined visual editor off - http://support.advancedcustomfields.com/discussion/2465/solved-admin-conflicts-after-upgrade
* [Updated] Update Polish translation - Thanks to www.digitalfactory.pl

= 3.3.0 =
* [Fixed] Gallery not returning correct order

= 3.2.9 =
* [Added] Add new Gallery Field
* [Fixed] Test / Fix update_field on repeater / flexible content
* [Fixed] Fix regex JS issue with adding nested repeaters
* [Added] Add new Czech translation - Thanks to Webees ( http://www.webees.cz/ )

= 3.2.8 =
* [Added] Repeater - Add option for min rows + max rows - http://www.advancedcustomfields.com/support/discussion/2111/repeater-empty-conditional-statements#Item_4
* [Fixed] Test / Fix Chrome Double WYSIWYG. Again...
* [Added] Add "future" to post status options - http://advancedcustomfields.com/support/discussion/1975/changed-line-81-and-94-of-corefieldspost_object-to-show-future-entries
* [Added] Make image sizes strings "Pretty" for preview size options
* [Fixed] Test / Fix WYSIWYG insert image inside a repeater bug - http://www.advancedcustomfields.com/support/discussion/2404/problem-with-repeater-wysiwyg-fields-and-images

= 3.2.7 =
* [Fixed] Rename controller classes - http://www.advancedcustomfields.com/support/discussion/2363/fatal-error-after-update-to-3.2.6
* [Added] Add edit button to image / file fields
* [Fixed] WYSIWYG toolbar buttons dissapearing in HTML tab mode

= 3.2.6 =
* [Fixed] Fix flexible content inside repeater add extra row jquery bug - http://www.advancedcustomfields.com/support/discussion/2134/add-flexible-content-button-in-repeater-field-adds-new-repeater-row
* [Added] Add suppress_filters to relationship field for WPML compatibility - http://www.advancedcustomfields.com/support/discussion/comment/5401#Comment_5401
* [Added] Add new German translation - http://www.advancedcustomfields.com/support/discussion/2197/german-translation
* [Added] Add new Italian translation - Alessandro Mignogna (www.asernet.it)
* [Added] Add new Japanese translation - http://www.advancedcustomfields.com/support/discussion/2219/japanese-translation
* [Fixed] Test / Fix WYSIWYG removing p tags - http://www.advancedcustomfields.com/support/discussion/comment/5482#Comment_5482
* [Added] edit basic toolbar buttons to match WP teeny mode - WYSIWYG
* [Fixed] Test front end form hiding - http://www.advancedcustomfields.com/support/discussion/2226/frontend-form-disppears-on-acf-3.2.5
* [Fixed] Test saving user custom fields - http://www.advancedcustomfields.com/support/discussion/2231/custom-fields-not-saving-data-on-initial-user-registration
* [Fixed] Fix options page translation bug - http://www.advancedcustomfields.com/support/discussion/2098/change-language-and-options-page-fields-disappear
* [Fixed] Pages rule not returning private pages - http://www.advancedcustomfields.com/support/discussion/2275/attach-field-group-to-privately-published-pages
* [Added] Add custom add_image_size() Image field preview sizes - http://www.advancedcustomfields.com/support/discussion/comment/5800#Comment_5800

= 3.2.5 =
* [IMPORTANT] Change field group option "Show on page" to "Hide on Screen" to allow for future proof adding new elements to list. Previously exported and registered field groups via PHP will still work as expected! This change will prompt you for a database upgrade.
* [Added] Add in edit button to upload image / file thickbox
* [Improved] Changed loading default values. Now behaves as expected!
* [Fixed] Test / Fix full screen mode dissapearing from editor - http://www.advancedcustomfields.com/support/discussion/2124/full-screen-button-for-zen-mode-is-gone
* [Fixed] get_field returning false for 0 - http://advancedcustomfields.com/support/discussion/2115/get_field-returns-false-if-field-has-value-0
* [Improved] Improve relationship sortable code with item param - http://www.advancedcustomfields.com/support/discussion/comment/3536#Comment_3536
* [Fixed] IE category js bug - http://www.advancedcustomfields.com/support/discussion/2127/ie-78-category-checkbox-bug
* [Fixed] Flexible content field row css bug - http://www.advancedcustomfields.com/support/discussion/2126/space-between-fields-is-a-little-tight-in-3.2.33.2.4
* [Fixed] Repeater row limit in flexible field bug - http://www.advancedcustomfields.com/support/discussion/1635/repeater-with-row-limit-of-1-inside-flexible-field-no-rows-show
* [Fixed] Fix update message - appears on first activation
* [Fixed] Fix options page sidebar drag area - no border needed
* [Fixed] Fix export options page activation - http://www.advancedcustomfields.com/support/discussion/2112/options-page-not-working-in-functions.php

= 3.2.4 =
* [Fixed] Remove translation from validation class - http://www.advancedcustomfields.com/support/discussion/2110/custom-validation-broken-in-other-languages
* [Fixed] Test fix WYSIWYG insert media issues
* [Added] Add Excerpt to the field group "show on page" options

= 3.2.3 =
* [Fixed] Include Wysiwyg scripts / styles through the editor class
* [Fixed] Wysiwyg in repeater not working
* [Fixed] Remove Swedish translation until string / js bugs are fixed
* [Fixed] Checkbox  array value issue: http://wordpress.org/support/topic/plugin-advanced-custom-fields-php-warning-in-corefieldscheckboxphp?replies=6
* [Added] Add inherit to relationship posts query - http://www.advancedcustomfields.com/support/discussion/comment/3826#Comment_3826
* [Fixed] Relationship shows deleted posts - http://www.advancedcustomfields.com/support/discussion/2080/strange-behavior-of-relationship-field-trash-posts
* [Fixed] Wysiwyg editor not working on taxonomy edit page 

= 3.2.2 =
* [Fixed] Fix layout bug: Nested repeaters of different layouts
* [Fixed] Fix strip slashes bug
* [Fixed] Fix nested repeater bug - http://www.advancedcustomfields.com/support/discussion/2068/latest-update-broken-editing-environment-
* [Fixed] Test / Fix add multiple images to repeater

= 3.2.1 =
* Field groups can now be added to options page with layout "side"
* Fixed debug error when saving a taxonomy:
* Fixed unnecessary code: Remove Strip Slashes on save functions
* Added new add row buttons to the repeater field and upgraded the css / js
* Fixed debug error caused by the WYSIWYG field: wp_tiny_mce is deprecated since version 3.3! Use wp_editor() instead.
* Fixed duplicate field error where all sub fields became repeater fields.
* Add Swedish translation: http://advancedcustomfields.com/support/discussion/1993/swedish-translation
* CSS improvements
* Fixed IE9 Bug not returning an image preview on upload / select
* Fixed Multi export php syntax bug.

= 3.2.0 =
* Fixed Browser bug with Flexible Field: Add Row button works again
* Added Brazilian Translation. Thanks to Marcelo Paoli Graciano - www.paolidesign.com.br
* Reverted input CSS to separate field label / instructions onto new lines.

= 3.1.9 =
* Updated Images / JS - Please hard refresh your browser to clear your cache
* Remove caching from acf_field_groups, replace with temp cache
* Add "Duplicate Field" on field group edit page
* Fix link to documentation on field group edit page
* add "update_value" to API
* Include new Polish translation
* Create a nicer style for flexible content
* Create a nicer style for repeater fields with row layout
* Create a nicer style for "no metabox" fields
* Add Spanish translation. Thanks to @hectorgarrofe
* Fix css for options page no metabox
* Added custom post_updated_messages
* Changed "Drag and drop to reorder" from an image to a string for translation

= 3.1.8 =
* Options page fields now save their data in the wp_options table. This will require a "Database Upgrade" when you update ACF. This upgrade will move your Options page data from the postmeta table to the options table.
* Added _e() and __() functions to more text throughout plugin
* Added new French translation. Thanks to Martin Vauchel @littlbr http://littleboyrunning.com
* Fixed duplicate WYSIWYG in chrome bug
* New Location rules: add fields to a user / taxonomy / attachment
* Bug Fix: Color picker now shows color on page load. Thanks to Kev http://www.popcreative.co.uk
* CSS tweaks File clearfix, new style for selects with optgroups
* Simplified get_value to return default value if value == ""
* API now allows for "option" and "options" for the $post_id value in API functions

= 3.1.7 =
* Bug fix: Image field returns correct url after selecting one or more images
* Translation: Added Polish translation. Thank you Bartosz Arendt - Digital Factory - www.digitalfactory.pl
* Update : Added id attribute to all div.field (id="acf-$field_name")

= 3.1.6 =
* New style for buttons
* Bug Fix: Repeater maximum row setting was disabling the "add row" button 1 row early.
* Performance: Field options are now loaded in via ajax. This results in much less HTML on the edit field group page
* Performance: Field inputs are now loaded in via ajax. Again, less HTML on edit screens improves load times / memory usage
* Bug Fix: Field groups registered by code were not showing on ajax change (category / page type / page template / etc). To fix this, your field group needs a unique ID. When you export a field group, you will now be given a unique ID to fix this issue. Field groups without a fixed id will still show on page load.
* New Option: Repeater field can now have a custom button label
* New Option: Flexible content field can now have a custom button label
* Improvement: Updated the HTML / CSS for file fields with icon
* Bug Fix: Fixed multi upload / select image in repeater. 
* Performance: Added caching to the get_field function. Templates will now render quicker.
* Bug Fix: Fixed Post formats location rule - it now works.
* Nested repeaters are now possible!

= 3.1.5 =
* Improvement: Redesigned the experience for uploading and selecting images / files in fields and sub fields. Image / File fields within a repeater can now add multiple images / files

= 3.1.4 =
* New Feature: Front end form (Please read documentation on website for usage)
* Performance: compiled all field script / style into 1 .js file
* Bug Fix: Editor now remembers mode (Visual / HTML) without causing errors when loading in HTML mode
* Improvement: Added draft / private labels to post objects in relationship, post object and page link fields

= 3.1.3 =
* Bug Fix: Options page fields were rendered invisible in v3.1.2 (now fixed)
* Updated POT file with new texts

= 3.1.2 =
* New Feature: Required field validation. Note: Repeater / Flexible content fields can be required but their sub fields can not.
* Field update: Select field: API now returns false when "null" is selected
* Field update: Radio button: When editing a post / page, the radio button will select the first choice if there is no saved value for the field
* Bug fix: You can now use a repeater field inside a flexible field! Please note that the_repeater_field will not work as expected. Please use get_sub_field to get the sub repeater field, then use php to loop through it.

= 3.1.1 =
* New Feature: Added shortcode support. usage: [acf field="field_name"]
* Bug Fix: Fixed menu disappearing by changing the function "add_menu" to "add_utility_page"
* Visual: Changed post object / page link fields to display post type label instead of post type name for the select optgroup label. Thanks to kevwaddell for the code

= 3.1.0 =
* New Field: Flexible Content Field (license required)
* Bug Fix: ACF data now saves for draft posts (please do a hard refresh on an edit screen to remove cached js)
* Bug fix: Fixed multiple content editors
 
= 3.0.7 =
* Added export / register support via PHP
* Moved menu position under Settings
* Improve speed / php memory by introducing cached data
* Temp bug fix: sets content editor to "visual mode" to stop wysiwyg breaking
* Visual: Removed "Screen Options" tab from the admin acf edit page. Added filter to always show 99 acf's
* Minor JS improvements

= 3.0.6 =
* Bug Fix: Location meta box now shows all pages / posts
* Bug Fix: upgrade and settings url should now work / avoid conflicts with other plugins

= 3.0.5 =
* Support: use wp native functions to add all user roles to location metabox
* Update: gave acf a css update + new menu structure
* Bug fix: fixed a few issues with wysiwyg js/css in wp3.3
* Bug fix:  fixed page_name conflicting with normal pages / posts by adding a "acf_" to the page_name on save / update
* Performance: location metabox - limited taxonomies to hierarchial only. Posts and Pages have now been limited to 25

= 3.0.4 =
* Bug fix: WYSIWYG is now compatible with WP 3.3 (May have incidentally added support for gravity forms media button! But not 100% sure...)
* Fix : Taxonomy Location rule now only shows hierarchal taxonomies to improve speed and reduce php memory issues

= 3.0.3 =
* New translation: French (thanks to Netactions)
* Support: added support for new wp3.3 editor
* Bug fix: fixed WYSIWYG editor localised errors
* Bug fix: removed trailing commas for ie7

= 3.0.2 =
* New Feature: Added Export tab to export a WP native .xml file
* New Option: Relationship / Post type - filter by taxonomy
* New Option: default values for checkbox, select and radio
* New Function: register_options_page - add custom options pages (Requires the option page addon)
* Bug fix: WYSIWYG + repeater button issues
* Bug fix: general house keeping

= 3.0.1 =
* Bug Fix - repeater + wysiwyg delete / add duplicate id error
* Bug fix - repeater + file - add file not working
* Bug Fix - image / file no longer need the post type to support "editor"
* WYSIWYG - fixed broken upload images
* misc updates to accommodate the soon to be released "Flexible Field"

= 3.0.0 =
* ACF doesn't use any custom tables anymore! All data is saved as post_meta!
* Faster and more stable across different servers
* Drag-able / order-able metaboxes
* Fields extend from a parent object! Now you can create you own field types!
* New location rule: Taxonomy
* New function: register_field($class, $url);
* New Field: Color Picker
* New Option: Text + Textarea formatting
* New Option: WYSIWYG Show / Hide media buttons, Full / Basic Toolbar buttons (Great for a basic wysiwyg inside a repeater for your clients)
* Lots of bug fixes

= 2.1.4 =
* Fixed add image tinymce error for options Page WYSIWYG
* API: added new function: update_the_field($field_name, $value, $post_id)
* New field: Relationship field
* New Option for Relationship + Post Object: filter posts via meta_key and meta_value
* Added new option: Image preview size (thumb, medium, large, full)
* Fixed duplicate posts double value problem
* API update: get_field($repeater) will return an array of values in order, or false (like it used to!)
* Radio Button: added labels around values
* Post object + Page Link: select drop down is now hierarchal
* Input save errors fixed
* Add 'return_id' option to get_field / get_sub_field
* Many bug fixes

= 2.1.3 =
* Fixed API returning true for repeater fields with no data
* Added get_fields back into the api!
* Fixed field type select from showing multiple repeater activation messages 

= 2.1.2 =
* Fixed repeater sortable bug on options page
* Fixed wysiwyg image insert on options page
* Fixed checkbox value error
* Tidied up javascript + wysiwyg functions


= 2.1.1 =
* Fixed Javascript bugs on edit pages

= 2.1.0 =
* Integrate acf_values and wp_postmeta! Values are now saved as custom fields!
* Ajax load in fields + update fields when the page / post is modified
* API has been completely re written for better performance
* Default Value - text / textarea
* New upgrade database message / system
* Separate upgrade / activate scripts
* Select / page link / post object add Null option
* Integrate with Duplicate Posts plugin
* New location rule: post format
* Repeater field attach image to post
* Location: add children to drop down menu for page parent
* Update script replaces image urls with their id's
* All images / Files save as id's now, api formats the value back into a url
* Simple CSS + JS improvements
* New Field: Radio Buttons (please note Firefox has a current bug with jquery and radio buttons with the checked attribute)

= 2.0.5 =
* New Feature: Import / Export
* Bug Fixed: Wysiwyg javascript conflicts
* Bug Fixed: Wysiwyg popups conflicting with the date picker field
* New style for the date picker field

= 2.0.4 = 
* New Addon: Options Page (available on the plugins store: http://plugins.elliotcondon.com/shop/) 
* API: all functions now accept 'options' as a second parameter to target the options page
* API: the_field() now implodes array's and returns as a string separated by comma's
* Fixed Bug: Image upload should now work on post types without editor
* Fixed Bug: Location rule now returns true if page_template is set to 'Default' and a new page is created
* General Housekeeping

= 2.0.3 =
* Added Option: Repeater Layout (Row / Table)
* Fixed bug: Now you can search for media in the image / file fields
* Added Option: Image field save format (image url / attachment id)
* Added Option: File field save format (file url / attachment id)
* Fixed bug: Location rules for post categories now work
* Added rule: Page parent
* Fixed bug: "what's new" button now shows the changelog
* included new css style to fit in with WordPress 3.2
* minor JS improvements

= 2.0.2 =
* Added new database table "acf_rules"
* Removed database table "ac_options"
* Updated location meta box to now allow for custom location queries
* Hid Activation Code from logged in users
* Fixed JS bugs with wp v3.2 beta 2
* Added new option "Field group layout" - you can now wrap your fields in a metabox!
* General housekeeping

= 2.0.1 =
* Added Field Option: Field Instructions
* Added Field Option: Is field searchable? (saves field value as a normal custom field so you can use the field against wp queries)
* Added Media Search / Pagination to Image / File thickbox
* Added Media Upload support to post types which do not have a Content Editor.
* Fixed "Select Image" / "Select File" text on thickbox buttons after upload
* Repeater field now returns null if no data was added

= 2.0.0 =
* Completely re-designed the ACF edit page
* Added repeater field (unlocked through external purchase)
* Fixed minor js bugs
* Fixed PHP error handling
* Fixed problem with update script not running
* General js + css improvements

= 1.1.4 =
* Fixed Image / File upload issues
* Location now supports category names
* Improved API - now it doesn't need any custom fields!
* Fixed table encoding issue
* Small CSS / Field changes to ACF edit screen


= 1.1.3 =
* Image Field now uses WP thickbox!
* File Field now uses WP thickbox!
* Page Link now supports multiple select
* All Text has been wrapped in the _e() / __() functions to support translations!
* Small bug fixes / housekeeping
* Added ACF_WP_Query API function

= 1.1.2 =
* Fixed WYSIWYG API format issue
* Fixed Page Link API format issue
* Select / Checkbox can now contain a url in the value or label
* Can now unselect all user types form field options
* Updated value save / read functions
* Lots of small bug fixes

= 1.1.1 =
* Fixed Slashes issue on edit screens for text based fields

= 1.1.0 =
* Lots of Field Type Bug Fixes
* Now uses custom database tables to save and store data!
* Lots of tidying up
* New help button for location meta box
* Added $post_id parameter to API functions (so you can get fields from any post / page)
* Added support for key and value for select and checkbox field types
* Re wrote most of the core files due to new database tables
* Update script should copy across your old data to the new data system
* Added True / False Field Type

= 1.0.5 =
* New Field Type: Post Object
* Added multiple select option to Select field type

= 1.0.4 =
* Updated the location options. New Override Option!
* Fixed un ticking post type problem
* Added JS alert if field has no type

= 1.0.3 =
* Heaps of js bug fixes
* API will now work with looped posts
* Date Picker returns the correct value
* Added Post type option to Page Link Field
* Fixed Image + File Uploads!
* Lots of tidying up!

= 1.0.2 =
* Bug Fix: Stopped Field Options from loosing data
* Bug Fix: API will now work with looped posts

= 1.0.1 =
* New Api Functions: get_fields(), get_field(), the_field()
* New Field Type: Date Picker
* New Field Type: File
* Bug Fixes
* You can now add multiple ACF's to an edit page
* Minor CSS + JS improvements

= 1.0.0 =
* Advanced Custom Fields.


== Upgrade Notice ==

= 3.0.0 =
* Editor is broken in WordPress 3.3

= 2.1.4 =
* Adds post_id column back into acf_values