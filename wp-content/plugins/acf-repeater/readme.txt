=== Advanced Custom Fields: Repeater Field ===
Contributors: elliotcondon
Author: Elliot Condon
Author URI: http://www.elliotcondon.com
Plugin URI: http://www.advancedcustomfields.com
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: trunk
Homepage: http://www.advancedcustomfields.com/add-ons/repeater-field/
Version: 1.1.1


== Copyright ==
Copyright 2011 - 2013 Elliot Condon

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.


== Description ==

= Break free from static inputs and create multiple rows of loop-able data =

The Repeater field acts as a table of data where you can define the columns (sub fields) and add infinite rows.
Any type of field can be added as a sub field which gives you the potential to create very flexible templates.

http://www.advancedcustomfields.com/add-ons/repeater-field/


== Installation ==

This software can be treated as both a WP plugin and a theme include.
However, only when activated as a plugin will updates be available/

= Plugin =
1. Copy the 'acf-repeater' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1. Copy the 'acf-repeater' folder into your theme folder (can use sub folders)
   * You can place the folder anywhere inside the 'wp-content' directory
2. Edit your functions.php file and add the following code to include the field:

`
include_once('acf-repeater/acf-repeater.php');
`
3. Make sure the path is correct to include the acf-repeater.php file
4. Remove the acf-repeater-update.php file from the folder.


== Changelog ==

= 1.1.1 =
* Fixed CSS bug causing any nested flexible content fields to not display add/remove buttons for each layout
* Fixes CSS bug causing cropped tables on small screen sizes

= 1.1.0 =
* Added Support for sub field conditional logic
* Added Support for sub field required validation
* Updated UI for 'column width' option (prepend %)
* Updated UI for 'Maxinmum Rows' option (can be left blank)
* Updated JS to use .on function instead of .live
* Added new update script allowing distribution in premium plugins / themes

= 1.0.1 =
* [Updated] Updated sub field type list to remove 'Tab' as this does not work as a sub field

= 1.0.0 =
* [Updated] Updated update_field parameters
* Official Release

= 0.1.2 =
* [IMPORTANT] This update requires the latest ACF v4 files available on GIT - https://github.com/elliotcondon/acf4
* [Added] Added category to field to appear in the 'Layout' optgroup
* [Updated] Updated dir / path code to use acf filter

= 0.1.1 =
* [IMPORTANT] This update requires the latest ACF v4 files available on GIT - https://github.com/elliotcondon/acf4
* [Updated] Updated naming conventions in field group page

= 0.1.0 =
* [Fixed] Fix wrong str_replace in $dir

= 0.0.9 =
* [Updated] Drop support of old filters / actions

= 0.0.8 =
* [Fixed] Fix bug causing sub fields to not display choices correctly

= 0.0.7 =
* [IMPORTANT] This update requires the latest ACF v4 files available on GIT - https://github.com/elliotcondon/acf4
* [Fixed] Fixed bug where field would appear empty after saving the page. This was caused by a cache conflict which has now been avoided by using the format_value filter to load sub field values instead of load_value

= 0.0.6 =
* [Updated] Update save method to use uniqid for field keys, not pretty field keys

= 0.0.5 =
* [Fixed] Fix wrong css / js urls on WINDOWS server.

= 0.0.4 =
* [Fixed] Fix bug preventing WYSIWYG sub fields to load.

= 0.0.3 =
* [Fixed] Fix load_field hook issues with nested fields.

= 0.0.2 =
* [Fixed] acf_load_field-${parent_hook}-${child_hook} now works!

= 0.0.1 =
* Initial Release.
