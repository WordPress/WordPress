=== Codestyling Localization ===
Contributors: codestyling
Tags: gettext, language, translation, poedit, localization, plugin, wpmu, buddypress, bbpress, themes, translator, l10n, i18n, google-translate, microsoft-translate, compatibility, mo, po, po-mo, polyglot
Requires at least: 2.5
Tested up to: 3.5.1
Stable tag: 1.99.30

You can manage and edit all gettext translation files (*.po/*.mo) directly out of WordPress Admin Center without any need of an external editor.

== Description ==

You can manage and edit all gettext translation files (*.po/*.mo) directly out of your WordPress Admin Center without any need of an external editor.
It automatically detects the gettext ready components like **WordPress** itself or any **Plugin** / **Theme** supporting gettext, is able to scan the related source files and can assists you using **Google Translate API** or **Microsoft Translator API** during translation.
This plugin supports **WordPress MU** and allows explicit **WPMU Plugin** translation too. It newly introduces ignore-case and regular expression search during translation.
**BuddyPress** and **bbPress** as part of BuddyPress can be translated too. Produces transalation files are 100% compatible to **PoEdit**.

= Requirements =
1. WordPress version 2.5 and later
1. PHP Interpreter version 4.4.2 or later
1. PHP Tokenizer Module (normally standard, required since version 1.90)
1. PHP Curl Library (if Microsoft translation API services should be used)

Please visit [the official website](http://www.code-styling.de/english/development/wordpress-plugin-codestyling-localization-en "Codestyling Localization") for further details and the latest information on this plugin.

= Details =
1. automatic detection of gettext ready components like WordPress, Plugins or Themes
1. creation of new language translation files at choosen language (ensures correct plural definitions too)
1. inplace adjusting of *.mo/*.po file permissions to be able to tranlate it
1. component specific (re)scan of source file to build the updated catalog entries
1. wrapping multiple plugins using same textdomain into one translation unit (like plugin and it's widget(s))
1. extended editing of full gettext catalog assisted by using Google or Microsoft translate API
1. full catalog search (exact match) with instant result set view for source or target language
1. correct handling of language dependend plural forms by providing appropriated edit dialog
1. first support of WMPU plugins started at version 1.60
1. complete WordPress support related to multiple textdomains included (since WP 2.8 and higher)
1. complete support of developer code comments for translators
1. complete support of context based gettext functions and displays this at editor qualified
1. supports also translation of non gettexted code parts, that marked as to be replaced in PHP files directly
1. handles textdomain separation for each module (WP, Plugins, Themes) to avoid standard textdomain usage been part of *.mo file
1. support of Theme language file sub folder (introduced at WordPress version 2.7 and higher)
1. support of BuddyPress and also bbPress as integration part of BuddyPress
1. support of *.pot file content during new language file creation
1. support of encrypted premium plugins but with security risk warning
1. support of low memory conditions (32M memory_limit) with big translation files or source codes
1. support of IDN based installations if PHP version is 5.0 or higher

= Scripting Guard =
This plugin work with a world unique technology to protect it's proper function against malfunction 3rd party plugins or bad behavior themes (see screenshot section).
Often Authors are attaching javascripts at global space regardless if they damage other plugins backend pages. This plugin detects now any kind of unrelated javascripts that have been attached to it's pages but are bad behavior.
In such cases this scripts will be stipped and a warning message occures. Furthermore the protection also detects runtime exceptions of injected inline scripts and displays them too.

= Announcement =
Starting with version 3.4 of WordPress I faced a restructured handling of localization within the Core Files, can be read here: [Important Changes for WordPress 3.4](http://wppolyglots.wordpress.com/important-changes-for-wordpress-3-4/)
Because I had to cope with this, the translation process of WordPress itself has been rewritten. The pugin now supports backward compatibility (for older WordPress versions) and also generates two new structured *.mo files.
It depends on your installed version, if it's less than 3.4-alpha, than you will get the old files generated otherwise the new files.

= Support & Development =
The plugin stays for a long time at major version 1.x now and it was planned to come up with a new major release 2.x several month ago. But because of massive changes at the WordPress core, not having that much time I would need, the new major version will be delayed again.
I can't estimate currently a timeframe for availability. That's why I continue maintainance of version 1.x as long as I'm working on version 2.x at alpha stage in parallel.

= Translation API's & User Interface =
Introduced with version 1.99.17 of this plugin, the translation API's of Google and Microsoft have been integrated. Both require at least subscriptions and the Google API is a paid service.
The plugin knows, which API is able to translated what language, so you will find the information next to your language file to be translated at the list.
I did reshape the User Interface a bit more closer to WordPress Standard UI, so it should be more intuitive to use it from now on.

= Translations =
The german translation has been created with this plugin itself. Feel free to make your translation related to your native language.
If you have a ready to publish translation of this plugin not pre-packaged yet, please send me an email. I will extend the package and remark your work at Acknowledgements.


== Installation ==

1. Uncompress the download package
1. Upload folder including all files and sub directories to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Translate your resources using 'Manage' menu at new 'Localization' sub menu

= Activation of Translation API's =

You can use 2 translation API's with this plugin. Normally they are disabled at default installation. You will find inside the plugin main page a link that expands the required description how to work with API's.

== Changelog ==

= Version 1.99.30 =
* Bugfix: defect regexp forced PHP warnings at non escapable languages (e.g. ja_JP)
* Bugfix: PHP error constants not available at each version of PHP are defined conditionally now
* Bugfix: separation between self domain and external domain done different to avoid false positives
* Bugfix: Turkish plural forms adjusted
* Feature: X-Generator field added to po/mo header
* Feature: smaller mo file header will be created
* Feature: added Armenian Language support
* Feature: permit WP Piwik Plugin for script injections

= Version 1.99.29 = 
* Bugfix: removed .htaccess file inside plugin folder
* Bugfix: improved javascipt exception handling within jQuery initializer chains
* Bugfix: updated re-introduced js libraries

= Version 1.99.28 =
* Bugfix: function missing in very old WordPress versions covered
* Bugfix: Scripting Guard now detects broken ready handler of injected javascripts
* Bugfix: redirection of prototype/scriptaculous done only, if WP version >= 3.5-alpha

= Version 1.99.27 = 
* Bugfix: WordPress version 3.5 starts removing prototype.js and scriptaculous but falls back to google CDN include which brokes JSON requests
* Bugfix: CDN verification returned an error object and caused a fatal error if external script can't be verified
* Bugfix: german translation contains untranslated terms
* Bugfix: compatibility improved and checked, still compatible with WordPress >= 2.5

= Version 1.99.26 = 
* Bugfix: WordPress version 3.5 starts removing prototype.js and scriptaculous but falls back to google CDN include which brokes JSON requests
* Bugfix: CDN verification returned an error object and caused a fatal error if external script can't be verified
* Bugfix: german translation contains untranslated terms
* Bugfix: compatibility improved and checked, still compatible with WordPress >= 2.5

= Version 1.99.25 = 
* Bugfix: trim of string sometimes broken like:  'Result: \'text\''
* Bugfix: 'Domain Path' of plugins will be accepted if set and existing
* Bugfix: uppercase letters used at folders or filenames did break the file handling
* Bugfix: SSL fully supported, all scripts stripped accidentally
* Bugfix: multisite support accidentally sometime broken
* Bugfix: accidentally markup polution
* Bugfix: theme detection for WP >= 3.3 was broken
* Bugfix: statements of memory limits with 0MB indicates mostly misused translation functions at themes/plugins, handled now (e.g. target theme, single.php).
* Bugfix: pofile sometimes created with wrong pluralization
* Bugfix: mofile didn't got the correct pofile header and loads afterwards the wrong pluralization function
* Feature: misused translation functions mapped into artificial textdomain '{bug-detected}' and excluded from mo file generation
* Feature: Scripting Guard supports now 'debug-bar', 'debug-bar-console', 'wp-native-dashboard'
* Feature: Scripting Guard now monitors PHP script errors provoked by other plugins/themes during page generation

= Version 1.99.24 =
* Bugfix: at Multisite installations the global var $domain was accidentally overwritten by dealing with "same origin policy" and did break new blog creation
* Bugfix: Scripting Guard now separates external script access from CDN mapped scripts and threat them as "dubious" an warnings
* Bugfix: external script validation failed, if scripts are at SSL locations, verify SSL option ste to false now
* Feature: none CDN external scripts will be stripped from page creation now always

= Version 1.99.23 = 
* Bugfix: injected stylesheets may modifiy the thickbox content and will not have any influence now.
* Bugfix: potfile indicator was created without filesystem api
* Bugfix: WordPress Language directory creation was done without filesystem api
* Feature: Scripting Guard now additionally reports CDN based script redirection as warning hint (e.g. ajax.googleapis.com) 
* Feature: Child Theme translations will be supported completely
* Feature: Language files from Main Theme can be synchronized with existing Child Theme files (see help)
* Feature: Plugin Help System has been extended again (upper right corner help)
* Languages: added Hindi Translation based on 1.99.23

= Version 1.99.22 = 
* Bugfix: Scripting Guard message was damaged, only affects the message itself not the function behind, repaired
* Bugfix: Thickbox gets damaged by Themes/Plugins injecting wp-admin/js/media-upload.js, has been handled and will be reported too.
* Bugfix: FTP filesystem handling changed to more server installations.

= Version 1.99.21 =
* Bugfix: missing space char at plugin description added
* Bugfix: warning message at profile page handled and removed
* Bugfix: active theme detection was not working at WP 3.4
* Bugfix: consider last component the editor was launche for and scroll to it, if "back to overview page" was clicked or F5 gets pressed
* Bugfix: minor CSS changes for RTL target languages. Editor have to show source box in LTR anyway even if target remains as RTL
* Feature: plugin stylesheet removed from code into dedicated CSS file
* Feature: using WordPress filesystem if direct modification of files are not permitted at the webspace
* Feature: first introducing "Scripting Guard" plugin self protection
* Feature: help system again extended for WordPress versions >= 3.3 
* Compatibility: still backward compatible downto WordPress 2.5 but without the filesystem writing (direct write required)

= Version 1.99.20 =
* Bugfix: translating the plugin/theme descriptions accidentally states, that Codestyling Localization uses more than one textdomain
* Bugfix: admin ajax url not longer lowercase completely, only "same origin policy" parts will be lowercased to avoid broken ajax calls
* Bugfix: false positive XSS vulnerablities handled anyway, all reported vulnerabilities require always an admin login (admin permission) to be executable and are all false positive
* Bugfix: last 4 plugin versions damaged the backward compatibility downto WordPress version 2.5 and has been repaired to support also this old versions of WordPress again
* Bugfix: writing indicator during long time write operations of large mo files was missing
* Bugfix: *.po files writen with refreshed revision date automatically
* Bugfix: re-scan process refreshes the Product-Id-Version field of *.po file
* Bugfix: minor visibility adjustments 
* Bugfix: locale nn_NN not longer supported, replaced by nn_NO
* Feature: using the WordPress backend help system at WordPress version >= 3.3

= Version 1.99.19 = 
* Bugfix: pot file indicator writes unstructured content and blocked new language creation
* Bugfix: valid specified UTF-8 character breaks JSON response during editor call (LINE SEPARATOR / decimal: 8232)
* Bugfix: trailing NUL chars may comming out of a reversed *.mo files and will be skipped now
* Bugfix: Chinese Traditional and Simplified can be used with translation API now
* Bugfix: disabled (unavailable) API's may hang the editor call
* Bugfix: Plugin/Theme Descriptions which are translatable (plugin loaded) shows up translated if possible.
* Feature: User Interface has been polished to be closer at WordPress standard backend look and feel.
* Feature: Translation API availabilities updated, visualization of which API is able to translate what language
* Feature: special workaround only for WooCoomerce none standard German language file handling (temporary solution)
* Feature: complete support of WordPress 3.4 changed localization behavior at core files
* Feature: special handling for improper context based "Center" text, used at UI and as continent/cities
* Feature: complete translation of WordPress within one *.po (merges now first splitted po files) file but separate saving of *.mo files
* Feature: qualified support of older and newer versions of WordPress out of the box
* Feature: supports Uighur language
* Feature: supports Albanian language
* Feature: supports Burmese language

= Version 1.99.18 =
* Bugfix: compatibility with upcomming WordPress 3.4 and rewritten WordPress Theme Core
* Bugfix: skips now protected folder during scan process instead of warning message output
* Bugfix: simple plugins (main folder) but translatable now being supported correctly
* Bugfix: google translate response leads to hidden exception and idle dialog.
* Bugfix: minor changes for RTL language CSS 
* Feature: per user translation API configuration as second option extends User Profile Settings
* Feature: provider mode possible to disable the help screen for API configuration
* Feature: supports galego language
* Feature: supports mongolian language
* Feature: supports georgian language

= Version 1.99.17 =
* Bugfix: old Google Translate API v1 removed (obsolete and not longer supported)
* Bugfix: WordPress 3.4 changes Theme handling, scan process adapted
* Bugfix: additional warnings and errors for textdomain issues and textdomain detection
* Bugfix: avoid that NomNom Theme damages the plugins pages by unwanted script injection
* Feature: Implementation of Google Translate API v2, requires API Key (paid service)
* Feature: Implementation of Microsoft Translate API, requires Access Tokens and Curl (free service for 2M characters / month)
* Languages: updated german translation

= Version 1.99.16 =
* Bugfix: failed preg_match repaired
* Bugfix: compatible with latest WP 3.3 pre-release because of modified ThickBox script.

= Version 1.99.15 =
* Bugfix: detection of theme textdomains eighter loaded by variable or defined as constant by variable

= Version 1.99.14 =
* Bugfix: bbPress plugin translation show index wrong messages (because of stand alone instead of packaged with BuddyPress)
* Bugfix: bbPress uses own _nx_noop translations accidentally mapped to default textdomain

= Version 1.99.13 =
* Bugfix: WordPress mu-plugin textdomains partially not detected even if present

= Version 1.99.12 =
* Bugfix: blog urls configured with uppercase letters breaking ajax JSON calls because of false positive brower XSS detection
* Bugfix: textdomain scanning has problems with textdomain names in vars
* Bugfix: WP e-Commerce plugin don't play cooperative and hardly unregisters prototype.js library at all backend pages (worked arround)

= Version 1.99.11 =
* Bugfix: IDNA support requires now PHP 5.2.1 or higher, not longer possible at lower PHP versions to support IDN
* Bugfix: avoid PHP serialization warnings at low memory mode scanning
* Bugfix: avoid warnings for not registered locale abbreviations
* Bugfix: if used mbstring.func_overload at php.ini with at least mode 2 containing, the plugin didn't work as expected.

= Version 1.99.10 =
* Feature: detection of plugins with code created textdomains, but with warning
* Feature: experimental parsing of .phtml files now supported 
* Bugfix: IDNA support requires now PHP 5.0 or higher, PHP 4.x not longer possible with IDN
* Languages: updated german translation

= Version 1.99.9 =
* Bugfix: IDN support works different for WebKit based browsers (Chrome / Safari) than all others currently


= Version 1.99.8 =
* Bugfix: translation file worked with wrong pluralization for different languages if *.pot files have been read and used as translation base
* Bugfix: translation file did not recognize the correct language requested if *.pot file was the base of catalog
* Bugfix: if catalog contains no translations, an illegal *.mo file can be produced and shows error at blog pages during load.
* Bugfix: wrong handling of plural counts leads to stopped working at translation page
* Bugfix: IDN based installations won't work because of JSON same origin problem forced by PunyCode Domain Names
* Languages: added polish translation based on 1.99.7

= Version 1.99.7 =
* Feature: support of low memory condition option for scanning and editing language files to avoid out of memory messages and aborts
* Bugfix: reduced the ammount of memory necessary during processing, worked arround PHP internal bugs
* Bugfix: serveral english typo's corrected
* Bugfix: loading stylesheet for RTL based languages correctly
* Languages: added hebrew translation based on 1.99.7
* Languages: added dutch translation based on 1.99.7

= Version 1.99.6 =
* Feature: detection of trailing spaces at text phrases and visualization
* Feature: filter for entries with trailing spaces
* Feature: support of translatable plugin header files
* Maintenance: language files moved into a subfolder for better handling
* Maintenance: screenshots shrinked, updated and extended
* Bugfix: deleting prior translated entries (left empty now) doesn't refesh untranslated counter

= Version 1.99.5 =
* Feature: support of existing *.pot files for initial content after creation of new language
* Feature: support of full encrypted premium plugins but with security risk warning
* Feature: fully encrypted plugins won't be scanned any longer at source level
* Feature: additional filter for components in terms of compatibility and security
* Bugfix: new component type icons for better understanding
* Bugfix: detection of some kind of existing translation files was wrong

= Version 1.99.4 =
* Bugfix: missing gettext keyword introduced by WordPress 3.0 added to *.po file header generation (ensure qualified PoEdit scan)
* Bugfix: accidentally skipped components because of incomplete detection, as example "Contact Forms 7" had been skipped
* Bugfix: Source Code Viewer was broken at PHP 5 because of changed behavoir of htmlentities() PHP core function
* Feature: Albanian Language support added
* Feature: first implementation of access keys for translation dialog: Alt + Shift + (**p**)revious | (**s**)ave | (**n**)ext 
* Remark: access keys works for Windows Browser like Google Chrome, Firefox, Safari and IE
* Limitation: access keys doesn't work for Opera because of dynamic content replacement 
* Limitation: access keys at IE doesn't set the focus well in all cases
* Limitation: access keys printed at the upper right corner of dialog are for Windows bases OS only.

= Version 1.99.3 =
* Bugfix: somebody tried to place themes inside lower levels that /wp-content/themes/ resulting in preg_match error
* Bugfix: scanning WordPress itself now exactly scans WordPress only files and not the entired domain root.
* Bugfix: avoid warnings for not existing files during scan process

= Version 1.99.2 =
* Bugfix: scanning WordPress itself did freeze because of low memory_limit, requires 58MB, extra warning and fatal detection included now
* Bugfix: WPMU plugins and simple plugins (1 file only) has not been qualified detected and forced to read the wrong source file
* Bugfix: Themes with textdomain name used by constant has been accidentally taken as is
* Bugfix: few translation strings have been simplified and/or rewritten
* Remark: open pending issue at a very small count of hosts where scanning plugin/themes works but scanning WordPress returns 504 Gateway Timeout

= Version 1.99.1 =
* Bugfix: major bug correction to produce in any case valid *.po files also working at PoEdit
* Feature: show reasons at code comment entries if merged duplicates of texts, read here: [blogpost](http://www.code-styling.de/english/codestyling-localization-and-poedit-are-compatible-now "Codestyling Localization and PoEdit are compatible now")

= Version 1.99 =
* Bugfix: displays now again correctly active plugins / themes
* Feature: scanning process extended to find most non standard translatable plugins and/or themes too
* Feature: compatibility warnings introduced for several known issues
* Feature: added new filter for all components with real or potential compatibility issues

= Version 1.98 =
* Bugfix: supports now again PHP 4 without crashing at installation
* Bugfix: WordPress 3.x Core with multiple *.mo files (default/continent-cities/ms) supported
* Bugfix: correct handling of "default" textdomain translations phrases
* Bugfix: AuthorURI linking repaired
* Feature: detects and handles BuddyPress plugin completely
* Feature: detects bbPress as part of BuddyPress and permits translation too.
* Feature: skipps scanning of "default" and "classic" theme at scope of WordPress, if version is >= 3.0
* Hint: 2 wrong/broken textdomains at WordPress scans, submitted as bug ticket http://core.trac.wordpress.org/ticket/14555

= Version 1.97 =
* Bugfix: detection of WP 3.x as WPMU version
* Bugfix: Opera bug work arround, content replacements
* Bugfix: Source Inspector Script crash at Opera
* Bugfix: works with debug mode / avoid deprecated functions
* Bugfix: error detection
* Bugfix: modified for Google Chrome support
* Bugfix: plural forms changed to meet http://translate/sourceforge.net/wiki/l10n/pluralforms
* Feature: introducing new L10N methods at WP 2.9/3.0 during scan process
* Languages: Português/Brasil added
* Languages: Norsk Bokmål added
* Known Limit: works with child themes now, first implementation but incomplete

= Version 1.96 =
* Bugfix: WPMU plugin handling problems fixed
* Bugfix: changes at WP 3.0 help system adapted (temporary work arround)
* Bugfix: WordPress scanning sanatized because of WP 3.0 default theme changes
* Bugfix: changed javascipt escape functions adapted
* Bugfix: level_10 security replaced by manage_options
* Hint: not fully tested at activated multi-site installations 

= Version 1.95 =
* Bugfix: WP localization engine has been changed in 2.9, freezes parsing and avoids loading of generated mo files! 

= Version 1.94 =
* Bugfix: WordPress theme core management uses full path instead of relative path, WordPress behavior change! 

= Version 1.93 =
* Bugfix: sub directory detection for language files at plugins/themes sometimes accidentally failed.

= Version 1.92 =
* Bugfix: final parsing cleanup sometimes accidentally wiped out more than 80% as old (obsolete) entries.
* Bugfix: WordPress 2.8 new textdomain "continents-cities" accidentally detected as main textdomain.

= Version 1.91 =
* Bugfix: work around a currently not fixed preg_match bug in PHP module PCRE, versions >= 5.2.0
* Bugfix: broken encoding of *.po file will be repaired to UTF-8 during load (2nd preg_match PHP bug)
* Bugfix: pre-select of primary textdomain didn't work as expected
* Bugfix: avoid saving any default (WordPress) depended *.mo files at plugin or theme section
* Bugfix: skip bad gettext function arguments (mostly stacked function calls inside gettext functions)
* Bugfix: safe *.po header handling to avoid erase of header fields
* Bugfix: plugin own untranslatable UI phrase made tranlatable
* Bugfix: description row resized and editor hint description added
* Bugfix: Changelog typo damaged the backend installation UI
* Limitation: javascript runtime in IE only gets exceeded if tried to translate WordPress itself (gets done in 2.0)
* Limitation: file permissions won't work at safemode based server/hosts (comming soon)
* Limitation: on safemode based systems creation of unaccessable *.po / *.mo files (comming soon)

= Version 1.90 =
* Bugfix: having only a *.mo file accidentally states a writing error
* Bugfix: accidentally wrong parsing in  rare cases of *.po files
* Feature: support of textdomain separation to avoid unnecessary default textdomain phrases in component *.mo files
* Feature: introducing a *.po file extension, that supports multiple textdomains inside one *.po file
* Feature: complete 2.8+ support of all newly introduced gettext functions and extensions
* Feature: supports visualization of developer comments for translators from code
* Feature: visualization of context dependend gettext phrases
* Feature: scanning speed improved, WordPress scan time >= 270s now needs 20s upto 40s (upto 10 times faster)
* Feature: new scanning and translation file engine
* Feature: supports for WordPress versions >= 2.7 that themes can have their own sub directory for translations.
* Languages: Belarusian translation based on 1.80 attached
* Languages: Arabic (Saudi Arabia) translation based on 1.80 attached


= Version 1.80 =
* Bugfix: open entities at markup closed
* Bugfix: Opera Bug solved, embedded 0 in string issue damaged plural saving
* Bugfix: IE Bugs solved, table cellpadding and coloring fixed
* Bugfix: page size and search fields now updates correctly because of autocomplete issues
* Bugfix: languages with only 1 plural form (japanes as example) now working as expected
* Feature: Pagination repeated at end of table
* Feature: introduced jump to top button at the end of table
* Feature: editing dialog now supports "save & previous" and "save & next"
* Feature: actual row been edited will be highlighted in background
* Feature: japanese language file attached
* Feature: Czech language file attached
* Feature: Danish language file attached

= Version 1.72 =
* Bugfix: enabled fa_IR (persian Language) for initial creation
* Feature: updated russian language file (meets now 1.71/1.72 content fully)
* Feature: udpated spain language file (translation mistake contained)

= Version 1.70 =
* Bugfix: setting pages of other plugins like vipers-video-quicktags did crash, if this plugin has been actived
* Bugfix: not all potential translateable plugins has been detected (analysis of coding syntax too strict)
* Bugfix: final compatibility on WordPress 2.7 have not been meet fully
* Feature: if language file path detection of plugins is not clear, directory tree will be shown for choise
* Feature: primary search function now also possible with ignore-case
* Feature: secondary search function introduced that enables regular expressions by dialog

= Version 1.65 =
* Bugfix: scan process of WPMU plugins shows warning message at produced page
* Bugfix: several plugins/themes could not be detected as translatable because of extra whitespace in function call
* Bugfix: wrong named theme localization files were shown but not editable, illegal syntax will now be skipped
* Feature: inital integration into WordPress 2.7 Context Help System to provide plugin specific help topics
* Feature: display new WordPress 2.7 tools icon at plugin page headline

= Version 1.60 =
* Bugfix: CSS style at WP 2.7 has been changed, minor adaption to be able to display dialogs correctly
* Bugfix: empty WordPress main language directory forces new file creation at wrong folder
* Bugfix: none existing WordPress main languages directories (US version) leads to error display
* Bugfix: prevent directory listing of plugin by .htaccess file attached to package
* Feature: none existing WordPress main languages directory can be created by plugin
* Feature: WPMU plugin support, detection of normal and WPMU version and ability to translate mu-plugins

= Version 1.55 =
* Bugfix: WordPress 2.7-hemorrhage introduces WP_ADMIN set additional during DOING_AJAX (ajax requests)

= Version 1.51 =
* Bugfix: SVN version number fix
* Feature: language attached, Traditional Chinese Taiwan by Gary

= Version 1.30 =
* Feature: supports now PHP 4, tested with PHP 4.4.2 and higher
* Feature: provides a copy action at each row, that makes original persistent as translation

= Version 1.21 =
* Bugfix: stylesheet now only gets loaded at plugins pages, backward compatibility support has broken thickbox styles at other pages
* Feature: Italian translation by Gianni has been added

= Version 1.2 =
* Feature: published at "http://wordpress.org/extend/plugins/codestyling-localization/"

= Version 1.1 =
* Bugfix: backslahed po-file header values causes IE to stop reponding during editor launch
* Bugfix: special path detection doesn&#8217;t recognize deep folder structure related textdomain loading
* Bugfix: stylesheet doesn&#8217;t show file/comment tooltip inside visble client area
* Bugfix: scrolling to source line number fails with script error, if *.po file is outdated and line exceeds total count
* Feature: &#8220;X-Poedit-Basepath&#8221; and &#8220;X-Poedit-SearchPath-0&#8243; at *.po file header will be relativized during read
* Feature: now supports file permission display and change capability
* Feature: collects multiple plugins at same textdomain as childs at first occurance
* Feature: show error message using thickbox instead of alert() except Google Translate errors

= Version 1.02 =
* Feature: downgrade code, runs now with WordPress 2.5 and above

= Version 1.01 =
* Bugfix: potential modified plugin path using constants has not been respected

= Version 1.0 =
* Bugfix: version control has been done too strictly, WP 2.6.0 doesn&#8217;t exist, reduced to 2.6
* Bugfix: locale definitions accidentally states, that zu_ZU (isiZulu) will be supported by Google translate, disabled

= Version 0.99 =
* Bugfix: remove accidentally usage of global $file, breaks plugin and theme editor
* Bugfix: plugins, that are gettext ready but textdomain can&#8217;t be detected, will be handled with their plugin filename as textdomain by default
* Feature: plugin activation checks now required versions (WordPress / PHP) and reports qualified messages at fail case

= Version 0.98 =
* Feature: extended to use Google translate API

= Version 0.97 =
* Bugfix: simple plugin analysis runs recursive over total plugin path
* Bugfix: language file naming convention was wrong
* Bugfix: missing escapement of names breaks javascript
* Bugfix: avoid deprecated constant usage
* Bugfix: fix table size avoid using full screen width at adminimize plugin

= Version 0.96 =
* Bugfix: Valid XHTML 1.0 Transitional
* Feature: switching to editor now shows loading indicator
* community closed tests lauched

= Version 0.10 =
* start of coding (alpha) @ 2008-06-21


== Frequently Asked Questions ==

= History? =
Please visit [the official website](http://www.code-styling.de/english/development/wordpress-plugin-codestyling-localization-en "Codestyling Localization") for the latest information on this plugin.

= Where can I get more information? =
Please visit [the official website](http://www.code-styling.de/english/development/wordpress-plugin-codestyling-localization-en "Codestyling Localization") for the latest information on this plugin.

== Screenshots ==
1. Codestyling Localization management center (menu -> tools)
1. language creation dialog
1. rescan of components based on actual source files
1. catalog content overview and editing center
1. simple gettext content editor dialog
1. plural gettext content editor dialog 
1. non gettext code hints for translators (source file patches)
1. separate multiple textdomains during mo-file generation
1. BuddyPress and bbPress will be special supported
1. texts with trailing space detection and visualization
1. Scripting Guard - plugin self protection feature


== Other Notes ==
= Acknowledgements =
Thanks to [Frank Bueltge](http://bueltge.de/ "Frank Bueltge") , Ingo Henze and  [Alphawolf](http://www.schloebe.de/ "Alphawolf") for qualified beta testing and improvement comments and [Knut Sparhell](http://sparhell.no/knut/ "Knut Sparhell") who detects the 'short_open_tag = off' Bug contained.

Also many thanks for all that qualified translations:
* [Gianni](http://gidibao.net/ "Gianni") for the quick Italiano translation 
* [Gary](http://www.gary711.net/ "Gary") for traditional Chinese Taiwan 中文(台灣)
* jtoth for Română translation
* Дмитрий for Русский translation
* [keopx](http://www.keopx.net/ "keopx") for Basque and Spain translation
* Lionel Chollet, Gilles Wittezaele, [Fabien Waroux](http://wp.fabonweb.net/ "Fabien Waroux") for French translation
* dreamcolor for Chinese China (中华人民共和国)
* [Ofer Wald](http://transposh.org "Ofer Wald") for Hebrew translation.
* Rene wpwebshop.com for dutch translation.

Thanks to [Thomas Urban](http://www.toxa.de "Thomas Urban") for contributing a faster mo file reading implementation.

= Licence =
This plugins is released under the GPL, you can use it free of charge on your personal or commercial blog. 

= Translations =
The german translation has been created with this plugin itself. Feel free to make your translation related to your native language.
If you have a ready to publish translation of this plugin not pre-packaged yet, please send me an email. I will extend the package and remark your work at Acknowledgements.

