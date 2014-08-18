=== Recent Posts FlexSlider ===
Contributors: davidjlaietta
Donate link: http://davidlaietta.com/
Tags: slider, responsive
Requires at least: 3.1
Tested up to: 3.9
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl.html

Simple setup responsive slider of recent posts selected by category or post type using FlexSlider by WooThemes.

== Description ==

This slider uses the FlexSlider framework by [WooThemes](http://www.woothemes.com/flexslider/). Recent posts are displayed as a responsive slider. Posts can be selected by category or by post type if your theme uses custom post types.

= Options that can be set: =
* Title
* Category
* Post Type
* Slide Duration
* Slide Pause
* Number of Slides
* Slider Height
* Slider Animation Style
* Post Title
* Post Excerpt & Length
* Toggle Post Link

== Installation ==

1. Upload the files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `Recent Post Flexslider` onto a sidebar on the Appearance->Widgets page

== Frequently Asked Questions ==

= Can I use both categories and post type to select posts? =

No. If a custom post type is used, categories must be set to "All Categories".

= Can I use custom taxonomies? =

Currently the plugin allows the selection of a category of posts or a custom post type only.


== Changelog ==

= 1.5 - 13 January 2014 =
* Updated to Flexslider v2.2.2
* Fixed image positioning
* Added ability to toggle post links on/off
* Added functionality to allow multiple sliders on one page
* Changed from get_the_excerpt() to get_the_content and stripped tags to allow longer excerpt captions than set by theme

= 1.4 - 2 October 2013 =
* Added Slider Animation Style
* Updated dropdowns to select() function

= 1.3 =
* Updated WP_Query
* Sticky Posts now ignored in slider
* Sizing bug for single slide fixed
* Margin removed for single slide
* Image vertically aligned in slide

= 1.2 =
* Reupload of core files

= 1.1 =
* CSS Optimization
* Dynamically load scripts and stylesheet only on views that include widget
* Load scripts in footer

= 1.0 =
* First Version