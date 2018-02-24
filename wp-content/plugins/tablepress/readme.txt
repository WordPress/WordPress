=== TablePress ===
Contributors: TobiasBg
Donate link: https://tablepress.org/donate/
Tags: table,data,html,csv,excel
Requires at least: 4.9.1
Tested up to: 4.9.1
Stable tag: 1.9
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed beautiful and feature-rich tables into your posts and pages, without having to write code.

== Description ==

TablePress allows you to easily create and manage beautiful tables. You can embed the tables into posts, pages, or text widgets with a simple Shortcode. Table data can be edited in a speadsheet-like interface, so no coding is necessary. Tables can contain any type of data, even formulas that will be evaluated. An additional JavaScript library adds features like sorting, pagination, filtering, and more for site visitors. Tables can be imported and exported from/to Excel, CSV, HTML, and JSON files.

= More information =
Please visit the plugin website at https://tablepress.org/ for more information or a [demo](https://tablepress.org/demo/)).

= Supporting future development =
If you like TablePress, please rate and review it here in the WordPress Plugin Directory or support it with your [donation](https://tablepress.org/donate/). Thank you!

= TablePress Extensions =
Additional features and useful enhancements are available as separate plugins, called [TablePress Extensions](https://tablepress.org/extensions/), on the plugin website.

== Screenshots ==

1. "All Tables" screen
2. "Edit" screen
3. "Add new Table" screen
4. "Import" screen
5. "Export" screen
6. "Plugin Options" screen
7. "About" screen
8. An example table (as it can be seen on the [TablePress website](https://tablepress.org/demo/))

== Installation ==

The easiest way to install TablePress is via your WordPress Dashboard. Go to the "Plugins" screen, click "Add New", and search for "TablePress" in the WordPress Plugin Directory. Then, click "Install Now" and the following steps will be done for you automatically. After the installation, you'll just have to activate the TablePress plugin.

Manual installation works just as for other WordPress plugins:

1. [Download](https://downloads.wordpress.org/plugin/tablepress.latest-stable.zip) and extract the ZIP file.
1. Move the folder "tablepress" into the "wp-content/plugins/" directory of your WordPress installation.
1. Activate the plugin "TablePress" on the "Plugins" screen of your WordPress Dashboard.
1. Create and manage tables by going to the "TablePress" screen in the admin menu.
1. Add a table to a page, post, or text widget, by embedding the Shortcode `[table id=<your-table's-ID> /]` into its content, or by using the "Table" button in the editor toolbar.
1. You can change the table styling by using CSS code, which can be entered into the "Custom CSS" textarea on the "Plugin Options" screen.

== Frequently Asked Questions ==

= Where can I find answers to Frequently Asked Questions? =
Many questions, regarding different features or styling, have been answered on the [FAQ page](https://tablepress.org/faq/) on the plugin website.

= Support? =
For support questions, bug reports, or feature requests, please use the [WordPress Support Forums](https://wordpress.org/support/plugin/tablepress). Please search through the forums first, and only [create a new topic](https://wordpress.org/support/plugin/tablepress#new-post) if you don't find an existing answer. Thank you!

= Requirements? =
In short: WordPress 4.9.1 or higher, while the latest version of WordPress is always recommended.

= Languages and Localization? =
TablePress supports the ["Translate WordPress" platform](https://translate.wordpress.org/). With that, translating is possible on a website from which so-called Language Packs are automatically generated and shipped to plugin users. For a list of existing Language Packs, please see the sidebar on the TablePress page in the [WordPress Plugin Directory](https://wordpress.org/plugins/tablepress/).

It is therefore no longer necessary to generate and translate *.po and *.mo files manually. Instead, just go to the [TablePress translations page](https://translate.wordpress.org/projects/wp-plugins/tablepress), log in with a free wordpress.org account and start translating TablePress into your language.

If you want to become a Translation Editor for your language, who can confirm or reject translation suggestions by other users, please get in touch.

= Migration from WP-Table Reloaded =
TablePress is the official successor of the WP-Table Reloaded plugin. It has been rewritten from the ground up and uses an entirely new internal structure. This fixes some major flaws of WP-Table Reloaded and prepares the plugin for easier, safer, and better future development.
If you are currently using WP-Table Reloaded, it is highly recommended that you switch to TablePress. WP-Table Reloaded will no longer be maintained or developed. For further information on how to switch from WP-Table Reloaded to TablePress, please see the [migration guide](https://tablepress.org/migration-from-wp-table-reloaded/) on the plugin website.

= Development =
You can follow the development of TablePress more closely in its official [GitHub repository](https://github.com/TobiasBg/TablePress).

= Where can I get more information? =
Please visit the [official plugin website](https://tablepress.org/) for the latest information on this plugin, or [follow @TablePress](https://twitter.com/TablePress) on Twitter.

== Usage ==

After installing the plugin, you can create and manage tables on the "TablePress" screen in the WordPress Dashboard.
Everything should be self-explaining there.

To show one of your tables in a post, on a page, or in a text widget, just embed the Shortcode `[table id=<the-ID> /]` into the post/page/text widget, where `<the-ID>` is the ID of your table (can be found on the left side of the "All Tables" screen.)
Alternatively, you can also insert tables by clicking the "Table" button in the editor toolbar, and then selecting the desired table.

After that, you might want to change the styling of the table. You can do this by entering CSS commands into the "Custom CSS" textarea on the "Plugin Options" screen. Some examples for common styling changes can be found on the [TablePress FAQ page](https://tablepress.org/faq/).
You may also add certain features (like sorting, pagination, filtering, alternating row colors, row highlighting, print name and/or description, ...) by enabling the corresponding checkboxes on a table's "Edit" screen.

== Acknowledgements ==

Special thanks go to [Allan Jardine](https://www.sprymedia.co.uk/) for the [DataTables JavaScript library](https://www.datatables.net/).
Thanks to all language file translators!
Thanks to every donor, supporter, and bug reporter!

== License ==

This plugin is Free Software, released and licensed under the GPL, version 2 (https://www.gnu.org/licenses/gpl-2.0.html).
You may use it free of charge for any purpose.

== Changelog ==

= Version 1.9 =
* Full compatibility with WordPress 4.9
* Feature: The "Custom CSS" text field highlights and notifies about CSS code syntax errors.
* Enhancement: Update list of allowed CSS properties in "Custom CSS".
* Enhancement: Make the CSV import more robust against malformed input.
* Bugfix: The integration into the WordPress search was broken since a WordPress core change.
* Bugfix: The HTML import was broken on certain server configurations.
* Updated external libraries (Build tools).
* Some internal changes and fixes for better stability, cleaner code, translations, and documentation.
* TablePress 1.9 requires WordPress 4.9.1!

= Version 1.8.1 =
* Enhancement: Make HTML import more robust and faster.
* Enhancement: The HTML import can now import merged cells in a row.
* Enhancement: Harden the XLSX import against potential security issues (thanks to Yuji Tounai).
* Enhancement: Allow easier changes to import data for developers, by adding a filter hook.
* Enhancement: The layout of the TablePress admin screens will look better on small screens.
* Enhancement: Update list of allowed CSS properties in "Custom CSS".
* Enhancement: Reduce size of default CSS, by removing old hacks for Internet Explorer.
* Updated external libraries (CodeMirror, DataTables, SimpleXLSX, CSSTidy, Build tools).

= Version 1.8 =
* Full compatibility with WordPress 4.7
* Updated external libraries (CodeMirror, DataTables, Build tools).
* Enhancement: Better spacing between the label and input field for the search in tables.
* Enhancement: Update list of allowed CSS properties in "Custom CSS".
* Enhancement: Make it easier for other plugins to clear the TablePress output cache.
* Enhancement: Simplification of some strings/text, to make translations easier.
* Bugfix: Better sanitization of HTML code in the Preview (thanks to Gerard Arall).
* Some internal changes and fixes for better stability, cleaner code, translations, and documentation.
* TablePress 1.8 requires WordPress 4.7!

= Version 1.7 =
* Full compatibility with WordPress 4.4
* Bugfix: Properly align tabs and heading in the main navigation bar at the top.
* Bugfix: Restore layout on small screens (responsiveness) for the "All Tables" screen.
* Bugfix: Restore layout of the overlay when inserting tables into posts/pages.
* Bugfix: Make input fields on the "Edit" screen resizable in both directions again.
* Bugfix: Restore sorting arrows on the "Edit" screen.
* Bugfix: Some strings were unclear, had typos, or used wrong HTML entities.
* Bugfix: Prevent certain "Custom Commands" from being rewritten to a new syntax in the wrong way.
* Bugfix: Make sure that the table preview is properly translated to other languages.
* Enhancement: Make the "Custom CSS" textarea vertically resizable.
* Enhancement: Support more CSS3 properties when cleaning "Custom CSS" code.
* Enhancement: Increase reliability when internally converting tables to their storage format (JSON).
* Enhancement: Use correct HTML markup for better accessibility on the admin screens.
* Enhancement: Only load required parts of jQuery, for faster page loads in some environments.
* Updated external libraries (CodeMirror, DataTables, Build tools).
* Translations: Switched from .po/.mo files to WordPress Plugin Language Packs.
* Some internal changes and fixes for better stability, cleaner code, and documentation.
* TablePress 1.7 requires WordPress 4.3!

= Version 1.6.1 =
* Bugfix: Update the DataTables JS library to fix issues with the JS features after the release of WordPress 4.3.
* Updated translations (Chinese (Simplified)).

= Version 1.6 =
* Full compatibility with WordPress 4.2
* Bugfix: Fixed integration of the "Insert Link" dialog.
* Bugfix: Divisions by zero were not caught properly in formulas in cells.
* Bugfix: Numbers were sometimes not imported correctly in the Excel importer.
* Enhancement: Importing files encoded as UTF-16 should work better now.
* Enhancement: Support dismissible notices in the admin screens.
* Enhancement: Support better tabbing on the "Edit" screen.
* Enhancement: Add page cache clearing for the WP Fastest Cache plugin.
* Enhancement: Add a plugin filter hook that allows modifying the exported data by plugins.
* Enhancement: Support more CSS3 properties when cleaning "Custom CSS" code.
* Updated external libraries (CodeMirror, DataTables, Build tools).
* Added Korean translation.
* Updated several translations (Chinese (Taiwan), English, German).
* Many internal changes and fixes for better stability, cleaner code, and documentation.
* TablePress 1.6 requires WordPress 4.2!

= Version 1.5.1 =
* Bugfix: Some properties in Custom CSS code were erroneously removed.
* Updated the Spanish translation.

= Version 1.5 =
* Full compatibility with WordPress 4.0
* Feature: Support for the new Media Manager when inserting images
* Feature: Support for the integrated WP importer/exporter
* Bugfix: The "Insert Link" dialog in the "Advanced Editor" works now.
* Bugfix: Moving the admin menu entry somewhere else was broken in rare cases.
* Bugfix: The HTML export creates valid HTML files now.
* Enhancement: Tables are now stored with extra information about the format, so that other plugins are less likely to break it.
* Extended unit tests for the plugin and some external libraries.
* Updated external libraries (CodeMirror, DataTables, Build tools).
* Added Ukrainian translation.
* Updated several translations (Chinese (Simplified), Dutch, English, French, German, Hebrew, Italian, Japanese, Russian, Turkish).
* Added and updated more language files for the DataTables library.
* TablePress 1.5 requires WordPress 4.0!

= Version 1.4 =
* Compatibility with WordPress 3.9
* Bugfix: Determine the correct Worksheet ID during XLSX import
* Bugfix: Displaying empty Shortcodes was broken
* Enhancement: Improve JSON import to also allow import of JSON objects
* Enhancement: Use more sophisticated error handling and debugging
* Enhancement: Reduce memory usage when loading tables
* Added inline documentation to all plugin filter and action hooks
* Updated external libraries
* Internal improvements to coding standards, inline documentation, and build tools
* Added Serbian translation
* Updated several translations (Chinese (Simplified), Croatian, German, Spanish)

= Version 1.3 =
* Compatibility with WordPress 3.8 and the new admin styling
* Bugfix: Import of JSON files did not take row/column visibility into account
* Bugfix: File names of exported files were sometimes broken
* Bugfix: Translations for some strings were not loaded properly
* Enhancement: Don't search for tables outside of the main search query
* Enhancement: Broken tables are now skipped
* Updated external libraries
* Added Chinese (Taiwan) translation
* Internal improvements to coding standards, inline documentation, and build tools
* TablePress 1.3 requires WordPress 3.8!

= Version 1.2 =
* Compatibility with WordPress 3.7
* Bugfix: WordPress search did not find tables in some cases
* Bugfix: Cells were sometimes erroneously interpreted as formulas
* Bugfix: HTML export did not encode entities properly
* Bugfix: Wrong variable name in table render code
* Enhancement: Add logarithm to math functions for formulas
* Enhancement: Better internal code documentation and variable type checks
* Enhancement: Add parameter to Shortcode that allows showing debug information
* Updated external libraries
* Updated several translations (Brazilian Portuguese, Czech, French, German, Latvian)
* Many more internal code improvements
* TablePress 1.2 requires WordPress 3.6!

= Version 1.1.1 =
* Fixed a bug with CSS handling that broke some TablePress Extensions

= Version 1.1 =
* Experimental import for Excel files (.xls and .xlsx)
* More math functions in formulas (including if-conditionals, statistical functions, ...)
* Better "Custom CSS" saving for higher performance
* Bugfix: Encoding problem during HTML import
* Bugfix: Roles are now deleted during uninstallation
* Bugfix: Search for tables was broken, if Shortcode had been changed
* Plugin Unit Tests for automated code testing
* Added several new translations (Brazilian Portuguese, Czech, Dutch, Finnish, Hebrew, Icelandic, Italian, Japanese, Latvian, Russian, and Turkish)
* Many more internal improvements of code and usability
* Updated external libraries

= Version 1.0 =
Official release with a few fixes and many enhancements and improvements

= Version 0.9-RC =
Release candidate in which all intended features are included and very stable.

= Version 0.8.1-beta =
Initial version where most features are ready and pretty stable.

== Upgrade Notice ==

= 1.9 =
This update includes several new features, enhancements, and bugfixes. Updating is recommended.

= 1.8.1 =
This update is a stability, security, and maintenance release. Updating is highly recommended.

= 1.8 =
This update is a stability, maintenance, and compatibility release. Updating is recommended.

= 1.7 =
This update is a stability, maintenance, and compatibility release. Updating is recommended.

= 1.6.1 =
This update fixes an issue with the JavaScript features after the update to WordPress 4.3.

= 1.6 =
This update is a stability, maintenance, and compatibility release. Updating is recommended.

= 1.5.1 =
This update includes several new features, enhancements, and bugfixes. Updating is recommended.

= 1.5 =
This update includes several new features, enhancements, and bugfixes. Updating is recommended.

= 1.4 =
This update is a stability, maintenance, and compatibility release. Updating is recommended.

= 1.3 =
This update is a stability, maintenance, and compatibility release. Updating is recommended.

= 1.2 =
This update is a stability, maintenance, and compatibility release. Updating is recommended.

= 1.1.1 =
This upgrade includes several new features, enhancements, and bugfixes, and is a recommended maintenance release.

= 1.1 =
This upgrade includes several new features, enhancements, and bugfixes, and is a recommended maintenance release.

= 1.0 =
This release contains a few bug fixed and many enhancements and new features, and is a recommended update.

= 0.9-RC =
This release contains many enhancements and bug fixes.
