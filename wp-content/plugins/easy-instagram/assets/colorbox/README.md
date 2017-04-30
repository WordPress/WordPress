## About Colorbox:
A customizable lightbox plugin for jQuery.  See the [project page](http://jacklmoore.com/colorbox/) for documentation and a demonstration, and the [FAQ](http://jacklmoore.com/colorbox/faq/) for solutions and examples to common issues.  Released under the [MIT license](http://www.opensource.org/licenses/mit-license.php).

## Changelog:

### Version 1.4.33 - 2013/10/31

* Fixed an issue where private events propagated to the document in versions of jQuery prior to 1.7.  Fixes #525, Fixes #526

### Version 1.4.32 - 2013/10/16

* Updated stylesheets to avoid issue with using `div {max-width:100%}` (Fixes #520)

### Version 1.4.31 - 2013/9/25

* Used setAttribute to set londesc, so that the value is accessible via DOM Node longDesc property #508

### Version 1.4.30 - 2013/9/24

* Added longdesc and aria-describedby attributes to photos.  Fixes #508

### Version 1.4.29 - 2013/9/10

* Fixed a slideshow regression from 1.4.27
* Fixed a potential issue with the starting size of #cboxLoadedContent

### Version 1.4.28 - 2013/9/4

* Fixed a potential issue with using the open property with mixed slideshow and non-slideshow groups

### Version 1.4.27 - 2013/7/16

* Fixed a width calculation issue relating to using margin:auto on #cboxLoadedContent.

### Version 1.4.26 - 2013/6/30

* Fixed a regression in IE7 and IE8 that was causing an error.

### Version 1.4.25 - 2013/6/28

* Use an animation speed of zero between same-sized content (fixed).
* Removed temporary fix for jQuery UI 1.8

### Version 1.4.24 - 2013/6/24

* Added closeButton option.  Set to false to remove the close button.

### Version 1.4.23 - 2013/6/23

* Bugfix loading overlay/graphic append order

### Version 1.4.22 - 2013/6/19

* Updated manifest files for the jQuery plugin repository and Bower (no changes to plugin)

### Version 1.4.21 - 2013/6/6

* Replaced new Image() with document.createElement('img') to avoid a potential bug in Chrome 27.

### Version 1.4.20 - 2013/6/5

* Fixing bug/typo from last update.

### Version 1.4.19 - 2013/6/3

* Fixed bug where Colorbox was capturing ctrl+click on assigned links on windows browsers with jQuery 1.7+, rather than ignoring.

### Version 1.4.18 - 2013/5/30

* Fixed a scroll position issue when using $.colorbox.resize()

### Version 1.4.17 - 2013/5/23

* Possible fix for a Chrome 27 issue (https://github.com/jackmoore/colorbox/pull/438#issuecomment-18334804)

### Version 1.4.16 - 2013/5/20

* Added trapFocus setting to allow disabling of focus trapping

### Version 1.4.15 - 2013/4/22

* Added .webp to list of recognized image extensions

### Version 1.4.14 - 2013/4/16

* Added fadeOut property to control the closing fadeOut speed.
* Removed longdesc attribute for now.

### Version 1.4.13 - 2013/4/11

* Fixed an error involving IE7/IE8 and legacy versions of jQuery

### Version 1.4.12 - 2013/4/9

* Fixed a potential conflict with Twitter Bootstrap default img styles.

### Version 1.4.11 - 2013/4/9

* Added `type='button'` to buttons to prevent accidental form submission
* Added alt and longdesc attributes to photo content if they are present on the calling element.

### Version 1.4.10 - 2013/4/2

* Better 'old IE' feature detection that fixes an error with jQuery 2.0.0pre.

### Version 1.4.9 - 2013/4/2

* Fixes bug introduced in previous version.

### Version 1.4.8 - 2013/4/2

* Dropped IE6 support.
* Fixed other issues with $.colorbox.remove.

### Version 1.4.7 - 2013/4/1

* Prevented an error if $.colorbox.remove is called during the transition.

### Version 1.4.6 - 2013/3/19

* Minor change to work around a jQuery 1.4.2 bug for legacy users.

### Version 1.4.5 - 2013/3/10

* Minor change to apply the close and className properties sooner.

### Version 1.4.4 - 2013/3/10

* Fixed an issue with percent-based heights in iOS
* Fixed an issue with ajax requests being applied at the wrong time.

### Version 1.4.3 - 2013/2/18

* Made image preloading aware of retina settings.

### Version 1.4.2 - 2013/2/18

* Removed $.contains for compatibility with jQuery 1.3.x

### Version 1.4.1 - 2013/2/14

* Ignored left and right arrow keypresses if combined with the alt key.

### Version 1.4.0 - 2013/2/12

* Better accessibility:
	* Replaced div controls with buttons
	* Tabbed navigation confined to modal window
	* Added aria role

### Version 1.3.34 - 2013/2/4

* Updated manifest for plugins.jquery.com

### Version 1.3.33 - 2013/2/4

* Added retina display properties: retinaImage, retinaUrl, retinaSuffix
* Fixed iframe scrolling on iOS devices.

### Version 1.3.32 - 2013/1/31

* Improved internal event subscribing & fixed event bug introduced in v1.3.21

### Version 1.3.31 - 2013/1/28

* Fixed a size-calculation bug introduced in the previous commit.

### Version 1.3.30 - 2013/1/25

* Delayed border-width calculations until after opening, to avoid a bug in FF when using Colorbox in a hidden iframe.

### Version 1.3.29 - 2013/1/24

* Fixes bug with bubbling delegated events, introduced in the previous commit.

### Version 1.3.28 - 2013/1/24

* Fixed compatibility issue with old versions of jQuery (1.3.2-1.4.2)

### Version 1.3.27 - 2013/1/23

* Added className property.

### Version 1.3.26 - 2013/1/23

* Minor bugfix: clear the onload event handler after photo has loaded.

### Version 1.3.25 - 2013/1/23

* Removed grunt file & added Bower component.json.

### Version 1.3.24 - 2013/1/22

* Added generated files (jquery.colorbox.js / jquery.colorbox-min.js) back to the repository.

### Version 1.3.23 - 2013/1/18

* Minor bugfix for calling Colorbox on empty jQuery collections without a selector.

### Version 1.3.22 - 2013/1/17

* Recommit for plugins.jquery.com

### Version 1.3.21 - 2013/1/15
Files Changed: *.js

* Fixed compatibility issues with jQuery 1.9

### Version 1.3.20 - August 15 2012
Files Changed:jquery.colorbox.js

* Added temporary workaround for jQuery-UI 1.8 bug (http://bugs.jquery.com/ticket/12273)
* Added *.jpe extension to the list of image types.

### Version 1.3.19 - December 08 2011
Files Changed:jquery.colorbox.js, colorbox.css (all)

* Fixed bug related to using the 'fixed' property.
* Optimized the setup procedure to be more efficient.
* Removed $.colorbox.init() as it will no longer be needed (will self-init when called).
* Removed use of $.browser.

### Version 1.3.18 - October 07 2011
Files Changed:jquery.colorbox.js/jquery.colorbox-min.js, colorbox.css (all) and example 1's controls.png

* Fixed a regression where Flash content displayed in Colorbox would be reloaded if the browser window was resized.
* Added safety check to make sure that Colorbox's markup is only added to the DOM a single time, even if $.colorbox.init() is called multiple times.  This will allow site owners to manually initialize Colorbox if they need it before the DOM has finished loading.
* Updated the example index.html files to be HTML5 compliant.
* Changed the slideshow behavior so that it immediately moves to the next slide when the slideshow is started.
* Minor regex bugfix to allow automatic detection of image URLs that include fragments.

### Version 1.3.17 - May 11 2011
Files Changed:jquery.colorbox.js/jquery.colorbox-min.js

* Added properties "top", "bottom", "left" and "right" to specify a position relative to the viewport, rather than using the default centering.
* Added property "data" to specify GET or POST data when using Ajax.  Colorbox's ajax functionality is handled by jQuery's .load() method, so the data property works the same way as it does with .load().
* Added property "fixed" which can provide fixed positioning for Colorbox, rather than absolute positioning.  This will allow Colorbox to remain in a fixed position within the visitors viewport, despite scrolling.  IE6 support for this was not added, it will continue to use the default absolute positioning.
* Fixed ClearType problem with IE7.
* Minor fixes.

### Version 1.3.16 - March 01 2011
Files Changed:jquery.colorbox.js/jquery.colorbox-min.js, colorbox.css (all) and example 4 background png files

* Better IE related transparency workarounds.  IE7 and up now uses the same background image sprite as other browsers.
* Added error handling for broken image links. A message will be displayed telling the user that the image could not be loaded.
* Added new property: 'fastIframe' and set it to true by default.  Setting to fastIframe:false will delay the loading graphic removal and onComplete event until iframe has completely loaded.
* Ability to redefine $.colorbox.close (or prev, or next) at any time.

### Version 1.3.15 - October 27 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Minor fixes for specific cases.

### Version 1.3.14 - October 27 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* In IE6, closing an iframe when using HTTPS no longer generates a security warning.

### Version 1.3.13 - October 22 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Changed the index.html example files to use YouTube's new embedded link format.
* By default, Colorbox returns focus to the element it was launched from once it closes.  This can now be disabled by setting the 'returnFocus' property to false.  Focus was causing problems for some users who had their anchor elements inside animated containers.
* Minor bug fix involved in using a combination of slideshow and non-slideshow content.

### Version 1.3.12 - October 20 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Minor bug fix involved in preloading images when using a function as a value for the href property.

### Version 1.3.11 - October 19 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Fixed the slideshow functionality that broke with 1.3.10
* The slideshow now respects the loop property.

### Version 1.3.10 - October 16 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Fixed compatibility with jQuery 1.4.3
* The 'open' property now accepts a function as a value, like all of the other properties.
* Preloading now loads the correct href for images when using a dynamic (function) value for the href property.
* Fixed bug in Safari 3 for Win where Colorbox centered on the document, rather than the visitor's viewport.
* May have fixed an issue in Opera 10.6+ where Colorbox would rarely/randomly freeze up while switching between photos in a group.
* Some functionality better encapsulated & minor performance improvements.

### Version 1.3.9 - July 7 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js/ all colorbox.css (the core styles)

* Fixed a problem where iframed youtube videos would cause a security alert in IE.
* More code is event driven now, making the source easier to grasp.
* Removed some unnecessary style from the core CSS.

### Version 1.3.8 - June 21 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Fixed a bug in Chrome where it would sometimes render photos at 0 by 0 width and height (behavior introduced in recent update to Chrome).
* Fixed a bug where the onClosed callback would fire twice (only affected 1.3.7).
* Fixed a bug in IE7 that existed with some iframed websites that use JS to reposition the viewport caused Colorbox to move out of position.
* Abstracted the identifiers (HTML ids & classes, and JS plugin name, method, and events) so that the plugin can be easily rebranded.
* Small changes to improve either code readability or compression.

### Version 1.3.7 - June 13 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js/index.html

* $.colorbox can now be used for direct calls and accessing public methods. Example: $.colorbox.close();
* Resize now accepts 'width', 'innerWidth', 'height' and 'innerHeight'. Example: $.colorbox.resize({width:"100%"})
* Added option (loop:false) to disable looping in a group.
* Added options (escKey:false, arrowKey:false) to disable esc-key and arrow-key bindings.
* Added method for removing Colorbox from a document: $.colorbox.remove();
* Fixed a bug where iframed URLs would be truncated if they contained an unencoded apostrophe.
* Now uses the exact href specified on an anchor, rather than the version returned by 'this.href'. This was causing "#example" to be normalized to "http://domain/#example" which interfered with how some users were setting up links to inline content.
* Changed example documents over to HTML5.

### Version 1.3.6 - Jan 13 2010
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Small change to make Colorbox compatible with jQuery 1.4

### Version 1.3.5 - December 15 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Fixed a bug introduced in 1.3.4 with IE7's display of example 2 and 3, and auto-width in Opera.
* Fixed a bug introduced in 1.3.4 where colorbox could not be launched by triggering an element's click event through JavaScript.
* Minor refinements.

### Version 1.3.4 - December 5 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Event delegation is now used for elements that Colorbox is assigned to, rather than individual click events.
* Additional callbacks have been added to represent other stages of Colorbox's lifecycle. Available callbacks, in order of their execution: onOpen, onLoad, onComplete, onCleanup, onClosed These take place at the same time as the event hooks, but will be better suited than the hooks for targeting specific instances of Colorbox.
* Ajax content is now immediately added to the DOM to be more compatible if that content contains script tags.
* Focus is now returned to the calling element on closing.
* Fixed a bug where maxHeight and maxWidth did not work for non-photo content.
* Direct calls no longer need 'open:true', it is assumed.  Example: `$.colorbox({html:'<p>Hi</p>'});`

### Version 1.3.3 - November 7 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Changed $.colorbox.element() to return a jQuery object rather the DOM element.
* jQuery.colorbox-min.js is compressed with Google's Closure Compiler rather than YUI Compressor.

### Version 1.3.2 - October 27 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Added 'innerWidth' and 'innerHeight' options to allow people to easily set the size dimensions for Colorbox, without having to anticipate the size of the borders and buttons.
* Renamed 'scrollbars' option to 'scrolling' to be in keeping with the existing HTML attribute. The option now also applies to iframes.
* Bug fix: In Safari, positioning occassionally incorrect when using '100%' dimensions.
* Bug fix: In IE6, the background overlay is briefly not full size when first viewing.
* Bug fix: In Firefox, opening Colorbox causes a split second shift with a small minority of webpage layouts.
* Simplified code in a few areas.

### Version 1.3.1 - September 16 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js/colorbox.css/colorbox-ie.css(removed)

* Removed the IE-only stylesheets and conditional comments for example styles 1 & 4.  All CSS is handled by a single CSS file for all examples.
* Removed user-agent sniffing from the js and replaced it with feature detection.  This will allow correct rendering for visitors masking their agent type.

### Version 1.3.0 - September 15 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js/colorbox.css

* Added $.colorbox.resize() method to allow Colorbox to resize it's height if it's contents change.
* Added 'scrollbars' option to allow users to turn off scrollbars when using the resize() method.
* Renamed the 'resize' option to be less ambiguous.  It's now 'scalePhotos'.
* Renamed the 'cbox_close' event to be less ambiguous.  It's now 'cbox_cleanup'.  It is the first thing to happen in the close method while the 'cbox_closed' event is the last to happen.
* Fixed a bug with the slideshow mouseover graphics that appeared after Colorbox is opened a 2nd time.
* Fixed a bug where ClearType may not work in IE6&7 if using the fade transition.
* Minor code optimizations to increase compression.

### Version 1.2.9 - August 7 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Minor change to enable use with $.getScript();
* Minor change to the timing of the 'cbox_load' event so that it is more useful.
* Added a direct link to a YouTube video to the examples.

### Version 1.2.8 - August 5 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Fixed a bug with the overlay in IE6
* Fixed a bug where left & right keypress events might be prematurely unbound.

### Version 1.2.7 - July 31 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js, example stylesheets and background images (core styles have not changed and the updates will not affect existing user themes / old example themes)

* Code cleanup and reduction, better organization and documentation in the full source.
* Added ability to use functions in place of static values for Colorbox's options (thanks Ken!).
* Added an option for straight HTML.  Example: `$.colorbox({html:'<p>Howdy</p>', open:true})`
* Added an event for the beginning of the closing process.  This is in addition to the event that already existed for when Colorbox had completely closed.  'cbox_close' and 'cbox_closed' respectively.
* Fixed a minor bug in IE6 that would cause a brief content shift in the parent document when opening Colorbox.
* Fixed a minor bug in IE6 that would reveal select elements that had a hidden visibility after closing Colorbox.
* The 'esc' key is unbound now when Colorbox is not open, to avoid any potential conflicts.
* Used background sprites for examples 1 & 4.  Put IE-only (non-sprite) background images in a separate folder.
* Example themes 1, 3, & 4 received slight visual tweaks.
* Optimized pngs for smaller file size.
* Added slices, grid, and correct sizing to the Adobe Illustrator file, all theme files are now export ready!

### Version 1.2.6 - July 15 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Fixed a bug with fixed width/height images in Opera 9.64.
* Fixed a bug with trying to set a value for rel during a direct call to Colorbox. Example: `$.colorbox({rel:'foo', open:true});`
* Changed how href/rel/title settings are determined to avoid users having to manually update Colorbox settings if they use JavaScript to update any of those attributes, after Colorbox has been defined.
* Fixed a FF3 bug where the back button was disabled after closing an iframe.

### Version 1.2.5 - June 23 2009
Files Changed: jquery.colorbox.js/jquery.colorbox-min.js

* Changed the point at which iframe srcs are set (to eliminate the need to refresh the iframe once it has been added to the DOM).
* Removed unnecessary return values for a very slight code reduction.

### Version 1.2.4 - June 9 2009
Files Changed: jquery.colorbox.js, jquery.colorbox-min.js

* Fixed an issue where Colorbox may not close completely if it is closed during a transition animation.
* Minor code reduction.

### Version 1.2.3 - June 4 2009
* Fixed a png transparency stacking issue in IE.
* More accurate Ajax auto-sizing if the user was depending on the #cboxLoadedContent ID for CSS styling.
* Added a public function for returning the current html element that Colorbox is associated with. Example use: var that = $.colorbox.element();
* Added bicubic scaling for resized images in the original IE7.
* Removed the IE6 stylesheet and png files from Example 3.  It now uses the same png file for the controls that the rest of the browsers use (an alpha transparency PNG8).  This example now only has 2 graphics files and 1 stylesheet.

### Version 1.2.2 - May 28 2009
* Fixed an issue with the 'resize' option.

### Version 1.2.1 - May 28 2009
* Note: If you are upgrading, update your jquery.colorbox.js and colorbox.css files.
* Added photo resizing.
* Added a maximum width and maximum height. Example: {height:800, maxHeight:'100%'}, would allow the box to be a maximum potential height of 800px, instead of a fixed height of 800px.  With maxHeight of 100% the height of Colorbox cannot exceed the height of the browser window.
* Added 'rel' setting to add the ability to set an alternative rel for any Colorbox call.  This allows the user to group any combination of elements together for a gallery, or to override an existing rel. attribute so those element are not grouped together, without having to alter their rel in the HTML.
* Added a 'photo' setting to force Colorbox to display a link as a photo.  Use this when automatic photo detection fails (such as using a url like 'photo.php' instead of 'photo.jpg', 'photo.jpg#1', or 'photo.jpg?pic=1')
* Removed the need to ever create disposable elements to call colorbox on.  Colorbox can now be called directly, without being associated with any existing element, by using the following format:
  `$.colorbox({open:true, href:'yourLink.xxx'});`
* Colorbox settings are now persistent and unique for each element.  This allows for extremely flexible options for individual elements.  You could use this to create a gallery in which each page in the gallery has different settings.  One could be a photo with a fade transition, next could be an inline element with an elastic transition with a set width and height, etc.
* For user callbacks, 'this' now refers to the element colorbox was opened from.
* Fixed a minor grouping issue with IE6, when transition type is set to 'none'.
* Added an Adobe Illustrator file that contains the borders and buttons used in the various examples.

### Version 1.2 - May 13 2009
* Added a slideshow feature.
* Added re-positioning on browser resize.  If the browser is resized, Colorbox will recenter itself onscreen.
* Added hooks for key events: cbox_open, cbox_load, cbox_complete, cbox_closed.
* Fixed an IE transparency-stacking problem, where transparent PNGs would show through to the background overlay.
* Fixed an IE iframe issue where the ifame might shift up and to the left under certain circumstances.
* Fixed an IE6 bug where the loading overlay was not at full height.
* Removed the delay in switching between same-sized gallery content when using transitions.
* Changed how iframes are loaded to make it more compatible with iframed pages that use DOM dependent JavaScript.
* Changed how the JS is structured to be better organized and increase compression.  Increased documentation.
* Changed CSS :hover states to a .hover class.  This sidesteps a minor IE8 bug with css hover states and allows easier access to hover state user styles from the JavaScript.
* Changed: elements added to the DOM have new ID's.  The naming is more consistent and less likely to cause conflicts with existing website stylesheets.  All stylesheets have been updated.
* Changed the behavior for prev/next links so that Colorbox does not get hung up on broken links.  A visitor can now skip through broken or long-loading links by clicking prev/next buttons.
* Changed the naming of variables in the parameter map to be more concise and intuitive.
* Removed colorbox.css.  Combined the colorbox.css styles with jquery.colorbox.js: the css file was not large enough to warrant being a separate file.

### Version 1.1.6 - April 28 2009
* Prevented the default action of the next & previous anchors and the left and right keys for gallery mode.
* Fixed a bug where the title element was being added back to the DOM when closing Colorbox while using inline content.
* Fixed a bug where IE7 would crash for example 2.
* Smaller filesize: removed a small amount of unused code and rewrote the HTML injection with less syntax.
* Added a public method for closing Colorbox: $.colorbox.close().  This will allow iframe users to add an event to close Colorbox without having to create an additional function.

### Version 1.1.5 - April 11 2009
* Fixed minor issues with exiting Colorbox.
 
### Version 1.1.4 - April 08 2009
* Fixed a bug in the fade transition where Colorbox not close completely if instructed to close during the fade-in portion of the transition.

### Version 1.1.3 - April 06 2009
* Fixed an IE6&7 issue with using Colorbox to display animated GIFs.

### Version 1.1.2 - April 05 2009
* Added ability to change content when Colorbox is already open.
* Added vertical photo centering now works for all browsers (this feature previously excluded IE6&7).
* Added namespacing to the esc-key keydown event for people who want to disable it: "keydown.colorClose"
* Added 'title' setting to add the ability to set an alternative title for any Colorbox call.
* Fixed rollover navigation issue with IE8. (Added JS-based rollover state due to a browser-bug.)
* Fixed an overflow issue for when the fixed width/height is smaller than the size of a photo.
* Fixed a bug in the fade transition where the border would still come up if Colorbox was closed mid-transition.
* Switch from JSMin to Yui Compressor for minification.  Minified code now under 7KB.

### Version 1.1.1 - March 31 2009
* More robust image detection regex.  Now detects image file types with url fragments and/or query strings.
* Added 'nofollow' exception to rel grouping.
* Changed how images are loaded into the DOM to prevent premature size calculation by Colorbox.
* Added timestamp to iframe name to prevent caching - this was a problem in some browsers if the user had multiple iframes and the visitor left the page and came back, or if they refreshed the page.

### Version 1.1.0 - March 21 2009
* Animation is now much smoother and less resource intensive.
* Added support for % sizing.
* Callback option added.
* Inline content now preserves JavaScript events, and changes made while Colorbox is open are also preserved.
* Added 'href' setting to add the ability to set an alternative href for any anchor, or to assign the Colorbox event to non-anchors. 
  Example: $('button').colorbox({'href':'process.php'})
  Example: $('a[href='http://msn.com']).colorbox({'href':'http://google.com', iframe:true});
* Photos are now horizontally centered if they are smaller than the lightbox size.  Also vertically centered for browsers newer than IE7.
* Buttons in the examples are now included in the 'protected zone'.  The lightbox will never expand it's borders or buttons beyond an accessible area of the screen.
* Keypress events don't queue up by holding down the arrow keys.
* Added option to close Colorbox by clicking on the background overlay.
* Added 'none' transition setting.
* Changed 'contentIframe' and 'contentInline' to 'inline' and 'iframe'.  Removed 'contentAjax' because it  is automatically assumed for non-image file types.
* Changed 'contentWidth' and 'contentHeight' to 'fixedWidth' and 'fixedHeight'.  These sizes now reflect  the total size of the lightbox, not just the inner content.  This is so users can accurately anticipate  % sizes without fear of creating scrollbars.
* Clicking on a photo will now switch to the next photo in a set.
* Loading.gif is more stable in it's position.
* Added a minified version.
* Code passes JSLint.

### Version 1.0.5 - March 11 2009
* Redo: Fixed a bug where IE would cut off the bottom portion of a photo, if the photo was larger than the document dimensions.

### Version 1.0.4 - March 10 2009
* Added an option to allow users to automatically open the lightbox. Example usage: $(".colorbox").colorbox({open:true});
* Fixed a bug where IE would cut off the bottom portion of a photo, if the photo was larger than the document dimensions.

### Version 1.0.3 - March 09 2009
* Fixed vertical centering for Safari 3.0.x.

### Version 1.0.2 - March 06 2009
* Corrected a typo.
* Changed the content-type check so that it does not assume all links to photos should actually display photos. This allows for Ajax/inline/and iframe calls on anchors linking to picture file types.

### Version 1.0.1 - March 05 2009
* Fixed keydown events (esc, left arrow, right arrow) for Webkit browsers.

### Version 1.0 - March 03 2009
* First release
