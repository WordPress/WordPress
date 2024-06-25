## Genericons

Genericons are vector icons embedded in a webfont designed to be clean and simple keeping with a generic aesthetic.

Use genericons for instant HiDPI, to change icon colors on the fly, or even with CSS effects such as drop-shadows or gradients!


### Usage

To use it, place the `font` folder in your stylesheet directory and enqueue the genericons.css file. Now you can create an icon like this:

```
.my-icon:before {
	content: '\f101';
	font: normal 16px/1 'Genericons';
	display: inline-block;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}
```

This will output a comment icon before every element with the class "my-icon". The `content: '\f101';` part of this CSS is easily copied from the helper tool at http://genericons.com/, or `example.html` in the `font` directory.

You can also use the bundled example.css if you'd rather insert the icons using HTML tags.


### Notes

**Photoshop mockups**

The `Genericons.ttf` file found in the `font` directory can be placed in your system fonts folder and used Photoshop or other graphics apps if you like.

If you're using Genericons in your Photoshop mockups, please remember to delete the old version of the font from Font Book, and grab the new one from the zip file. This also affects using it in your webdesigns: if you have an old version of the font installed locally, that's the font that'll be used in your website as well, so if you're missing icons, check for old versions of the font on your system.

**Pixel grid**

Genericons has been designed for a 16x16px grid. That means it'll look sharp at font-size: 16px exactly. It'll also be crisp at multiples thereof, such as 32px or 64px. It'll look reasonably crisp at in-between font sizes such as 24px or 48px, but not quite as crisp as 16 or 32. Please don't set the font-size to 17px, though, that'll just look terrible blurry.

**Antialiasing**

If you keep intact the `-webkit-font-smoothing: antialiased;` and `-moz-osx-font-smoothing: grayscale;` CSS properties. That'll make the icons look their best possible, in Firefox and WebKit based browsers.

**optimizeLegibility**

Note: On Android browsers with version 4.2, 4.3, and probably later, Genericons will simply not show up if you're using the CSS property "text-rendering" set to "optimizeLegibility.

**Updates**

We don't often update icons, but do very carefully when we get good feedback suggesting improvements. Please be mindful if you upgrade, and check that the updated icons behave as you intended.


### Changelog

**3.2**

A number of new icons and a couple of quick updates. 

* New: Activity
* New: HTML anchor
* New: Bug
* New: Download
* New: Handset
* New: Microphone
* New: Minus
* New: Plus
* New: Move
* New: Rating stars, empty, half, full
* New: Shuffle
* New: video camera
* New: Spotify
* New: Twitch
* Update: Fixed geometry in Edit icon
* Update: Updated Foursquare icon

Twitch and Spotify mark the last social icons that will be added to Genericons.
Future social icons will have to happen in a separate font. 

**3.1**

Genericons is now generated using a commandline tool called FontCustom. This makes it far easier to add new icons to the font, but the switch means the download zip now has a different layout, fonts have different filenames, there's now no .otf font included (but the .ttf should suffice), and the font now has slightly different metrics. I've taken great care to ensure this new version should work as a drop-in replacement, but please be mindful and test carefully if you choose to upgrade.

* Per feedback, the baked-in 16px width and height has been removed from the helper CSS. It wasn't really necessary (the glyph itself has these dimensions naturally), and it caused some headaches.
* Base64 encoding is now included by default in the helper CSS. This makes it drop-in easy to get Genericons working in Firefox even when using a CDN. 
* Title attribute on website tool.
* New: Website.
* New: Ellipsis.
* New: Foursquare.
* New: X-post.
* New: Sitemap.
* New: Hierarchy.
* New: Paintbrush.
* Updated: Show and Hide icons were updated for clarity.

**3.0.3**

Bunch of updates mostly.

* Two new icons, Dropbox and Fullscreen.
* Updates to all icons containing an exclamation mark.
* Updates to Image and Quote.
* Nicer "Share" icon.
* Bigger default Linkedin icon.

**3.0.2**

A slew of new stuff and updates.

* Social icons: Skype, Digg, Reddit, Stumbleupon, Pocket.
* New generic icons: heart, lock and print.
* New editing icons: code, bold, italic, image
* New interaction icons: subscribe, unsubscribe, subscribed, reply all, reply, flag.
* The hyperlink icon has been updated to be clearer, chunkier.
* The "home" icon has been updated for style, size and clarity.
* The email icon has been updated for style and clarity, and to fit with the new subscribe icons.
* The document icon has been updated for style.
* The "pin" icon has been updated for style and clarity.
* The Twitter icon has been scaled down to fit with the other social icons.

**3.0.1**

Mostly maintenance. 

* Fixed an issue with the example page that showed an old "top" icon instead of the actual NEW "refresh" icon.
* Added inverse Google+ and Path.
* Replaced tabs with spaces in the helper CSS.
* Changed the Genericons.com copy/paste tool to serve span's instead of div's for casual icon insertion. It's being converted to "inline-block" anyway.

**3.0**

Mainly maintenance and a few new icons.

* Fast forward, rewind, PollDaddy, Notice, Info, Help, Portfolio
* Updated the feed icon. It's a bit smaller now for consistency, the previous one was rather big.
* So, the previous version numbering, 2.09, wasn't very PHP version compare friendly. So from now on it'll be 3.0, 3.1 etc. Props Ipstenu.
* Genericons.com now has a mini release blog.
* The CSS has prettier formatting, props Konstantin Obenland.

**2.09**

Updated Facebook icon to new version. Updated Instagram logo to use new one-color version. Updated Google+ icon to use same radius as Instagram and Facebook. Added a bunch of new icons, cog, unapprove, cart, media player buttons, tablet, send to tablet.                                            

**2.06**

Included Base64 encoded version. This is necessary for Genericons to work with CDNs in Firefox. Firefox blocks fonts linked from a different domain. A CDN (typically s.example.com) usually puts the font on a subdomain, and is hence blocked in Firefox.

**2.05**

Added a bunch of new icons, including upload to cloud, download to cloud, many more.

**2.0**

Initial public release
