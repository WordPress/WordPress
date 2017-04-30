=== EventON ===
Contributors: Ashan Jay
Plugin Name: EventON
Author URI: http://ashanjay.com/
Tags: calendar, event calendar, event posts
Requires at least: 3.7
Tested up to: 3.9
Stable tag: 2.2.14

Event calendar plugin for wordpress that utilizes WP's custom post type.  

== Description ==
Event calendar plugin for wordpress that utilizes WP's custom post type. This plugin integrate eventbrite API to create paid events, add limited capacity to events, and accept payments for paid events or allow registration for free events. This plugin will add an AJAX driven calendar with month-view of events to front-end of your website. Events on front-end can be sorted by date or title. You can easily add events with multiple attributes and customize the calendar layout or build your own calendar using event post meta data. 

== Installation ==

1. Unzip the download zip file
1. Upload 'eventon' to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 2.2.14(2014-7-3)
ADDED: Ability to exclude events from calendars without deleting them
ADDED: Overall calendar user interaction do not interact value
UPDATED: Removed month header blank space from event list
UPDATED: Schema SEO data to have end date that was missing
UPDATED: Improvements to Paypal intergration into front-end
UPDATED: Seperate function to load for calendar footer with action hook evo_cal_footer
UPDATED: Pretty time on eventcard converted into proper language
FIXED: Repeat events for week of the month not showing correct
FIXED: Addon license activation page not working correctly
FIXED: Hide multiple occurence not showing events on other calendars on same page
FIXED: Repeating events time now showing correct on event card
FIXED: Schema SEO showing event page URL when someone dont have single events addon
FIXED: shortcode generator showing a body double

= 2.2.13 (2014-6-16)
FIXED: Option for adding dynamic styles to inline page when dynamic styles are not saved
FIXED: featured image on eventTop not showing
FIXED: shortcode generator not opening from wysiwyg editor button
FIXED: eventtop styles and HTML that usually get overridden by theme styles
UPDATED: Eventon addons page to now use ajax to load content
UPDATED: New welcome screen - hope you guys will like this

= 2.2.12 (2014-6-1)
ADDED: yes no buttons to be translatable via I18n
ADDED: the ability to select start or end date for past event cut off
ADDED: option to limit remote server checks option if eventon wp-admin pages are loading slow due to remote server checks
ADDED: Addon license activation system 
UPDATE: Did some serious improvements to cut down remote server check to increase speed
UPDATED: improvements to addon class and eventon remote updater classes
UPDATED: UI layout for addons and license page
FIXED: removed eventon shortcode button from WYSIWYG editor on event-edit post page
FIXED: error on class-calendar_generator line 1595 with event color value
FIXED: styles not saving correct in the settings
FIXED: on widget time and location to be rows by itself
FIXED: several other minor bugs

= 2.2.11 (2014-5-19)
ADDED: rtl support
ADDED: event type #3 into shortcode options if activated
ADDED: shortcode option to expand sort options section on load per calendar
ADDED: the ability to show featured image for events at 100% height
ADDED: the ability to turn off schema data for events
ADDED: the ability to turn off google fonts completely
ADDED: extended repeat feature to support first, second etc. friday type repeat events
ADDED: option to copy auto generated dynamic styles in case appearance doesnt save changes
UPDATED: UI super smooth all CSS yes/no buttons
UPDATED: Color picker rainbow circle no more changed it to a button
UPDATED: unix for virtual repeat events to be stored from back-end to reduce load on front-end
UPDATED: sort options and filters to close when clicked outside
FIXED: jumper month names
FIXED: eventon javascripts to load only on events pages in backend
FIXED: license activation issue solved
FIXED: events menu not showing up for some on left menu
FIXED: eventon popup box not showing correct solved z-index
FIXED: small bugs

= 2.2.10 (2014-5-5)
ADDED: you can now show only featured events in the calendar with only_ft shortcode variable
ADDED: load calendars pre-sorted by date or title with sort_by variable
ADDED: add to google calendar button and updated add to calendar button
ADDED: one letter month names for language translation for month jumper
ADDED: accordion like event card opening capabilty controlled via shortcode
ADDED: You can now add custom meta fields to eventTop
ADDED: custom meta field names can be translated in languages now
ADDED: End 3 letter month to eventTop date - now month shortname is always on
ADDED: ability to customize the eventCard time format
ADDED: ability to open links in new window for custom field content type = buttons
ADDED: wp-admin sort events by event location column
UPDATED: Month jumper to jump months upon first change in time
UPDATED: PO file for eventon Admin pages
UPDATED: Sort options section to be more intuitive for user
UPDATED: Events list event order DESC now order months in descending order as well
UPDATED: matching events menu icon based off font icons
FIXED: Arrow circle CSS for IE
FIXED: default event color missing # on hex code
FIXED: Wysiwyg editor eventon shortcode generator icon not opening lightbox
FIXED: Event type ID column for additional event type categories
FIXED: Lon lat not saving for location addresses
FIXED: Secondary languages not getting correct words when switching months
FIXED: improvements to speed eventON and cut down server requests
FIXED: featured image hover issues
FIXED: Custom meta field activation on eventCard and reordering bug
FIXED: font bold not reflecting on event details
FIXED: the content filter disable settings issue

= 2.2.9 (2014-3-26)
ADDED: More/less text background gradient to be able to change from settings
ADDED: ability to enable upto 5 additional event type categories for events
ADDED: shortcode generator button to wysiwyg editor
ADDED: the ability to turn off content filter on event details
ADDED: Language field to widget
FIXED: minor responsive styles
FIXED: zoom cursor arrow now loads from element class
FIXED: Capitalize date format on eventcard
FIXED: Featured image hover effect removal issues
FIXED: Jump months missing month and year text added to Language
CHANGED: plugin url to use a function to support SSL links

= 2.2.8 (2014-3-13)
ADDED: Reset to default colors button for appearance settings
ADDED: Jump months option to jump to any month you want
ADDED: Ability to assign colors by event type
ADDED: the ability to create custom field as a button
ADDED: User Interaction for events be able to override by overall variable value
UPDATED: We have integrated chat support direct into eventON settings
UPDATED: the Calendar header Interface design new arrows and cleaner design
TWEAKED: main event wp_Query closing function
FIXED: bulk edit event deleting meta values for event
FIXED: Lan lat driven google map centering on the marker issue solved
FIXED: all text translations to be included in sort menu

=2.2.7 (2014-2-13)
ADDED: filter to eventCard and eventTop time and date strings
ADDED: filter 'eventon_eventtop_html' to allow customization for eventTop html
ADDED: filter 'eventon_google_map_url' to load custom google maps API url with custom map languages
ADDED: ability to disable featured image hover effect
ADDED: shortcode support to open event card at first load
UPDATED: shortcode generator code to support conditional variable fields
UPDATED: html element attributes changed to data- in front-end calendar
UPDATED: new data element in calendar front-end to hold all attributes to keep the calendar HTML clean
UPDATED: event locations tax posts column removed - which was no use
FIXED: schema event url itemprop
FIXED: 'less' text not getting translated on eventcard
FIXED: timezone issues to correct hide past events hiding at correct time
FIXED: loading bar not appearing due to style error
FIXED: open event card at first on events list
FIXED: Custom language other than L1 to be updated for new calendars
FIXED: add to calendar ICS file content and timezone issue resolved
FIXED: hide multiple occurance for repeating events shortcode support

=2.2.6 (2014-1-30)
ADDED: Ability to collpase eventON setting menus
UPDATED: settings apperance sections can now be closed for space management
UPDATED: Language page UI and pluggability
FIXED: Missing sort option selector colors from setting appearance
FIXED: quick edit incorrect saving event data when 24hour format in active
FIXED: Event popup lightbox click on page scroll bar closing popup
FIXED: eventop background color not saving issue
FIXED: Custom meta fields not saving values for events
FIXED: Widget title to use wp universal filters

= 2.2.5 (2014-1-27)
ADDED: Event Location Name to eventTop
ADDED: Custom fields can now have Wysiwyg editor or single line text field to enter data
UPDATED: dynamic styles loading method to create a tangible eventon_dynamic_styles.css file instead of using admin-ajax.php to avoid long load times
UPDATED: Appreance color picker UI and the ability to support pluggability
UPDATED: Datepicker to consider start date when selecting end date
FIXED: 3rd custom field value not showing on calendar
FIXED: make sure settings page styles are loaded in page header

NOTE: Make sure to click save on eventON appearance to save new styles

= 2.2.4 (2014-1-12)
FIXED: Custom meta field values not appearing correct on events page and calendar

= 2.2.3(2014-1-10)
ADDED: Event locations can now be saved and used again for new events
ADDED: Event location name field
ADDED: featured event color can not be selected from Settings> Appearance and override the set event color with this
ADDED: event class name for featured events
ADDED: New widget to execute any eventON shortcode on sidebar
ADDED: One additional custom meta field, now we have 3 extra fields
ADDED: Font-awesome Vector/SVG icons for retina-readiness
ADDED: more options to change appearances of eventON easily
UPDATED: eventon settings UI for color picker
CHANGED: month nav arrows are now <span> elements instead of <a> elements - to avoid redirects on arrow click
FIXED: 3 letter month name not showing under event date for eventTop
FIXED: eventON widget upcoming event small bug that stopping it from showing the calendar

= 2.2.2(2013-12-21)
ADDED: capability to add magnifying glass cursor for featured images
ADDED: event type names translatability with eventON dual lang
UPDATED: UI compatibility with wp 3.8
UPDATED: shortcode generator tooltips UI
FIXED: missing eventon settings page i18n 
FIXED: eventTop line will be a <div> if the event slideDown or else it will be <a>
FIXED: more/less text translatability and other translation issues
FIXED: L2 calendar month name switching back to L1 language when switching months
FIXED: All (sort options) text added to language translation
FIXED: event popup CSS/HTML for feature image and event type line CSS
FIXED: ics file date zone to use wordpress i18n date and location incorrect value
FIXED: event custom meta values to go through formatted filter

= 2.2.1(2013-11-30)
ADDED: couple of wordpress pluggable functions to main calendar
FIXED: event time hours difference on front end than whats saved - using date_i18n() instead of date() now
FIXED: dual language saved value disappearing when switching languages
FIXED: draft events showing up on calendar when switching months
FIXED: month increment messing up due to february
FIXED: all day translation fixed
FIXED: ics file download error on date()
FIXED: event organizer field missing in action
UPDATED: widget to be able to set ID and hide empty months for list
UPDATED: Changed dynamic styles to load as a file and not print on header

= 2.2 (2013-11-21)
ADDED: event quick edit can now edit more event data on the fly
ADDED: class attribute names to events based on event type category event belong to
ADDED: Get directions field to eventCard - selectable from eventCard settings. Credit to Morten Bech for the suggestion
ADDED: The ability to rearrange the order of the eventCard data fields. Credit to Gilbert Dawed for the suggestion
ADDED: ICS file for each event so events can be added a users calendar
ADDED: new license activation server to stop all errors when activating eventON
ADDED: new add eventon shortcode button next to add media button on WYSIWYG editor
ADDED: brand spanking new shortcode generator popup box with super easy intuitive steps to customize shortcodes
ADDED: ability to reverse the event order ASC or DESC
ADDED: new shortcode "event_order" -- allow ability to set reverse order per calendar
ADDED: ability to add featured image thumbnail to eventTop
ADDED: new shortcode "show_et_ft_img" - allow to show featured image on eventTop or not
ADDED: new support tab to settings page
ADDED: i18n ready and compatible POT file for translation
UPDATED: we removed events lists options area from eventon settings and its now inside shortcode box
UPDATED: template loader function to look up templates in order
UPDATED: better event image full sizing when clicked to fit calendar
UPDATED: calendar eventCard UI - including a new close button
UPDATED: eventon wp-admin wide popup box design and functionality
UPDATED: wp-admin event edit UI - now you can hide each section of event meta data and declutter the space
FIXED: widget checkbox malfunction when there are more than one widgets.
FIXED: unnecessary google maps loading in wp-admin pages
FIXED: Addons & License tab errors some people were experiencing due to XML get file from myeventon server with addons latest info

IMPORTANT: all addons need to be updated to latest to run with eventon 2.2



= 2.1.19 (2013-10-12)
ADDED: backend time selection now changes based on WP time format - 24hr
ADDED: All events edit page dates are now sync with sitewide date format
ADDED: new option for user interaction; open an even as a popup box
ADDED: the ability to hide end time on calendar -- end date must be same as start or empty
ADDED: the ability to hide multiple occurance of events spread across several months -- on upcoming list on shortcode calendar and widget
FIXED: shortcode button adding multiples of shortcodes 
FIXED: shortcode popup box appearing empty on second occasion
FIXED: CSS sort options button overlapping
FIXED: Upcoming list featured image expanding issues
FIXED: Gmaps event Location now works w/o the address in eventTop
FIXED: google maps init javascript issue on FF fixed 
UPDATED: Date and time selection UI
UPDATED: changed data-vocabulary microSEO data to schema.org and update to fields

= 2.1.18 (2013-9-17)
ADDED: publish event capability to the list
FIXED: Day abbreviation for custom languages
FIXED: Addon error of scandir failed

= 2.1.17 (2013-9-16)
ADDED: The EvenTop data customization options
ADDED: Hide past events option to eventON Calendar widget
ADDED: The ability to customize the format of calendar header month/year title
ADDED: The ability to edit color of text under event title on eventTop
ADDED: Event ID can now be found by hovering over events list in wp-admin events
ADDED: [core] new filter 'eventon_sorted_dates' to access sorted events list
UPDATED: JQuery UI css to latest version
UPDATED: Backend UI a little
UPDATED: [core] myEventON Settings page tab filter
FIXED: Backend events sorting incorrect issue on all event posts list
FIXED: EventON widget event type filtering issue when switching months
FIXED: EventON Shortcode popup window not closing issue
FIXED: EventCard featured image not expanding full height sometimes
FIXED: array_merge error some people were getting for event types

New Verbage: eventTop - the event line that opens up the eventCard

= 2.1.16 (2013-8-21)
ADDED: UX - click on featured event image to expand the image to full height
TWEAKED: UI of the frontend calendar with clean tiny icons for time and location
FIXED: Event details overflowing when floated images
FIXED: bug with upcoming events set to hide causing events to not show up on full cal and other cals
FIXED: javascript delegate() has been changes to on() based on jQuery's new change
FIXED: time and location icons can now be edited from eventON settings

= 2.1.15 (2013-8-8)
FIXED: sort options text not dissapearing when set to hide
FIXED: javascript issue causing eventON to stop work with WP 3.6
TWEAKED: eventon Addon data are now also checked via cURL if failed with file_get_content

= 2.1.14 (2013-8-6)
UPDATED: Back-end widget UI to a whole new level which you gonna love
ADDED: shortcode variable "hide_past" to give the ability to hide past events per each shortcode
ADDED: Fixed month/year are now supported in widget
ADDED: Ability to select scroll wheel zoom on google maps or disable it
TWEAKED: Addons pull live addon details and the UI got a face lift.
TWEAKED: License tab reside in addons tab under eventon settings now
TWEAKED: events can now be repeated longer than 10 times
TWEAKED: sort options in a minimal dropdown menu
TWEAKED: Javascripts handles can now be called at will for AJAX driven pages
FIXED: Filtering issue when using multiple filters at once
FIXED: Quick edit for events

NOTES: If you are using eventON addons most of them will give minor bugs with newer version of eventON and you will NEED to update your eventON Addons to latest versions to get them working properly.


= 2.1.13
ADDED: Ability to add addresses using Latitude Longitude - for addresses that are not found correctly by google.
ADDED: Shortcode guide link to shortcode popup window
FIXED: Single quote values not saving correct for organizer
FIXED: Upcoming event list month text color
FIXED: Eventbrite non-connecting issue

= 2.1.12
FIXED: Google Map display issue when switching months
FIXED: Backend javascript not loading into wp-admin issue
TWEAKED: Minor fixes and compatibility updates for addons

= 2.1.11
FIXED: Colorpicker issue on Firefox
FIXED: Daily repeats addon not working

= 2.1.10 
ADDED: Google microdata for SEO for events included in calendar
ADDED: Ability to choose height of the event's featured image from settings
ADDED: Ability to remove more/less button in long event descriotion
ADDED: You are not limited 5 colors now, you can select your on custom event color
ADDED: Now you can add upto 2 custom fields for events and eventcard
ADDED: Ability to set fixed starting month/year for upcoming events list in shortcode
ADDED: You can now select to show year in upcoming events list
ADDED: Yearly event repeats
ADDED: Ability to set event date without time for multi-day events
UPDATED: Minor improvements to code and UI
FIXED: Event date not saving correct in some languages due to WP default date format and JQ UI datepicker issue. Now you can select either to use wp default date format in backend date selection or not. (if you chose not, the date format will be yyyy/mm/dd)
FIXED: Template locator bug
FIXED: Incorrect new update available notifications

=2.1.9 [2-13-5-6] =
FIXED: error on call to undefined function date_parse_from_format() for those running php 5.2
FIXED: Template error that cause entire site layout for some
FIXED: Widget title not appearing

=2.1.8 [2013-5-1]=
ADDED: basic single event page support and "../events/" url slug can be used to show calendar now - which is coming from a new page called "Events" in WP admin pages. 
ADDED: more/less custom language support
FIXED: new events not showing on calendar
FIXED: issue with EventON widget messing other widgets
FIXED: incorrect day name on multi-day event
FIXED: license version to update to current version after an update
FIXED: weird download issue with autoupdate
FIXED: incorrect date saving for non-american time format

=2.1.7 [2013-4-30]=
FIXED: event start date going to 1st of month error
FIXED: addons not showing issue
FIXED: error you get when saving styles
FIXED: array_merge error for addons

= 2.1.6 [2013-4-28]=
ADDED: ability to get automatic new updates
ADDED: new and exciting license management tab to myEventON settings
ADDED: new plugin update notifications 
ADDED: event date picker date format is now based off your site's date format
UPDATED: Event card got little jazzed up now
UPDATED: Main settings page - removed some junk
UPDATED: in-window pop up box, added new loading animation and notifications
UPDATED: EventON widgets UI
UPDATED: improved event generator class for faster loading
FIXED: issue with event close button not working for new months
FIXED: upcoming events list shortcode
FIXED: event time default value to 00
FIXED: minor style and functionality issues on eventON widget

= 2.1.5 [2013-4-18] =
ADDED: visible event type IDs to event types category page
ADDED: ability to duplicate events 
ADDED: more useful pluggable hooks into base plugin
ADDED: ability to disable google gmaps api partly and fully
ADDED: ability to set google maps zoom level
ADDED: close button at the bottom of each event details
UPDATED: frontend styles
UPDATED: backend settings tabs, better UI for language tab
UPDATED: event repeating UI
FIXED: issue with calendar font settings not working properly
FIXED: external event links not opening
FIXED: php template tag not working correctly

= 2.1.4 [2013-4-8] =
ADDED: a new shortcode popup box for better user experience

= 2.1.3 [2013-4-7] =
* Added support to open learn more links in new window
* Improvements to addon handling
* Few more minor bugs distroyed for good

= 2.1.2 [2013-4-5] =
* Minor bugs fixed
* Added the ability to disable google maps API
* Fix custom event type names on events column in backend
* Improvements on addon handling

= 2.1.1 [2013-3-28] =
* Fixed small bugs
* Added auto plugin update notifier for eventon
* Added upcoming events list support to widget

= 2.1 [2013-3-28] =
* Implemented hooks and filters for extensions and further customization
* You can now add addons to extend features of the calendar
* Fixed bunch more bugs
* Changed the name and a whole new shi-bang now
* Quick shortcode button on Page text editor

= 2.0.8 [2013-3-23]=
* Fixed bugs

= 2.0.7 [2013-3-17]=
* Fixed shortcode upcoming list issue
* Added the ability to hide empty months in upcoming list

= 2.0.6 [2013-2-28]=
* fixed minor error with usort array

= 2.0.5 [2013-2-25] =
* Added repeat events capability for monthly and weekly events
* Reconstructed the event computations system to support future expansions
* Now you can hide the sort bar from backend options
* Event card icons can be changed easily from backend now
* Added the template tag support for upcoming events list format
* Primary font for the calendar can also be changed from the backend options

= 2.0.4 [2013-2-11]=
* Added the ability to add an extra custom taxonomy for event sorting
* Custom taxonomies can be given custom names now
* Better control over front-end event sorting options
* Further minimalized the sort bar present on front-end calendar
* Fixed bugs on eventbrite and meetup api
* Added a learn more event link option
* Fixed event redirect when external link is empty
* Added 2 more different google map display types

= 2.0.3 [2013-1-13] =
* Fixed the bug with google map images

= 2.0.2 [2012-12-28] =
* Calendar arrow nav issue fixed in some themes

= 2.0.1 [2012-12-24] =
* Added the ability to create calendars with different starting months.

= 2.0 [2012-12-21] =
* Squished bugs in the code with data save and bunch of other stuff...
* Added Meetup API support to connect to meetup events and get event data in an interactive way.
* Updated eventbrite API to a more interactive event data-bridge setup.
* Added event organizer field.
* You can now link events to a url instead of opening event details.
* Event Calendar now support featured images for events right in the "event card".
* Added more animated effects to frontend of the calendar.
* Ditched the default skin to nail down some of the CSS issues with skins on "Slick"
* Updated event option saving method to streamline load time.
* Added TON of more customizable options

= 1.9 [2012-11- ]=
* Fixed saved dates and other custom event data dissapearing after auto event save in WP
* Improved custom style appending method
* Added Paypal direct link to event ticket payment
* Added easy color picker

= 1.8 [2012-10-23]=
* Added widget support
* UI Update to backend
* Existing skins update
* Improvements to algorithm

= 1.7 [2012-10-16]=
* Updated back-end UI
* Better hidden past event management
* Ability to disable month scrolling on front-end
* Added responsiveness to skins

= 1.6 [2012-5-31] =
* Multiple calendars in one page
* Calendar to show only certain event types with shortcode or template tags
* custom language for "no events"
* "Slick" new skin added
* Correct several CSS issues with parent CSS styles

= 1.5 [2012-5-1] =
* Improvement to code for faster loading
* Added smoother month transitions
* "Event Type" support for events
* Apply multiple colors to events and allow sorting by color
* Added "all day event" support
* Default wordpress main text editor is now used for event description box
* Better event data management

= 1.4 [2012-4-5] =
* CSS issues fixed
* Multiple Skin support 

= 1.3 [2012-1-31] =
* Minor changes to Interface design 
* New Loading spinner on AJAX calls
* Added auto Google Map API integration based on event location address
* Added control over past events display on the calendar
* Improvements to events algorithm for faster load time
* Bug fixed (End month and start month date issue)
* Bug fixed (Month filtering issues)

= 1.2 [2012-1-12] =
* Minor bugged fixed
* Back-end Internationalization
* Added plugin data cleanup upon deactivation

= 1.1 [2012-1-4] =
* Added custom language support

= 1.0 [2011-12-21] =
* Initial release