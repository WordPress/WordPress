  ___  ____  __ _  ____  ____  __  ___  __   __ _  ____ 
 / __)(  __)(  ( \(  __)(  _ \(  )/ __)/  \ (  ( \/ ___)
( (_ \ ) _) /    / ) _)  )   / )(( (__(  O )/    /\___ \
 \___/(____)\_)__)(____)(__\_)(__)\___)\__/ \_)__)(____/


Genericons are vector icons embedded in a webfont designed to be clean and simple keeping with a generic aesthetic.

Use genericons for instant HiDPI, to change icon colors on the fly, or even with CSS effects such as drop-shadows or gradients!


_  _ ____ ____ ____ ____ 
|  | [__  |__| | __ |___ 
|__| ___] |  | |__] |___ 


To use it, place the font folder in your stylesheet directory and paste this in your CSS file:

/* =Genericons, thanks to FontSquirrel.com for conversion!
-------------------------------------------------------------- */
@font-face {
    font-family: 'Genericons';
    src: url('font/genericons-regular-webfont.eot');
    src: url('font/genericons-regular-webfont.eot?#iefix') format('embedded-opentype'),
         url('font/genericons-regular-webfont.woff') format('woff'),
         url('font/genericons-regular-webfont.ttf') format('truetype'),
         url('font/genericons-regular-webfont.svg#genericonsregular') format('svg');
    font-weight: normal;
    font-style: normal;

}

Note: the above only works if you don't use a CDN. If you do, or don't know what that is, you should use the syntax that's embedded in genericons.css.

From then on, you can create an icon like this:

.my-icon:before {
	content: '\f101';
	display: inline-block;
	-webkit-font-smoothing: antialiased;
	font: normal 16px/1 'Genericons';
	vertical-align: top;
}

This will output a comment icon before every element with the class "my-icon". The "content: '\f101';" part of this CSS is easily copied from the helper tool at http://genericons.com/

You can also use the bundled example.css if you'd rather insert the icons using HTML tags.


_  _ ____ ___ ____ ____ 
|\ | |  |  |  |___ [__  
| \| |__|  |  |___ ___]


Photoshop mockups:

Genericons-Regular.otf found in the root directory of this zip has not been web-font-ified. So you can drop it in your system fonts folder and use the font in Photoshop if you like.

For those of you using Genericons in your Photoshop mockup, remember to delete the old version of the font from Font Book, and grab the new one from the zip file. This also affects using it in your webdesigns: if you have an old version of the font installed locally, that's the font that'll be used in your website as well, so if you're missing icons, check for old versions of the font on your system.

Pixel grid:

Note that Genericons has been designed for a 16x16 pixel grid. That means it'll look sharp at font-size: 16px exactly. It'll also be crisp at multiples thereof, such as 32px or 64px. It'll also look reasonably crisp at in-between font sizes such as 24px or 48px, but not quite as crisp as 16 or 32. Please don't set the font-size to 17px, though, that'll just look terrible.

Also note the CSS property "-webkit-font-smoothing: antialiased". That makes the icons look great in WebKit browsers. Please see http://noscope.com/2012/font-smoothing for more info.

Updates:

We don't often update icons, but do very carefully when we get good feedback suggesting improvements. Please be mindful if you upgrade, and check that the updated icons behave as you intended.



____ _  _ ____ _  _ ____ ____ _    ____ ____ 
|    |__| |__| |\ | | __ |___ |    |  | | __ 
|___ |  | |  | | \| |__] |___ |___ |__| |__] 

V3.0.3:
Bunch of updates mostly.
- Two new icons, Dropbox and Fullscreen.
- Updates to all icons containing an exclamation mark.
- Updates to Image and Quote.
- Nicer "Share" icon.
- Bigger default Linkedin icon.

V3.0.2: 
A slew of new stuff and updates.
- Social icons: Skype, Digg, Reddit, Stumbleupon, Pocket.
- New generic icons: heart, lock and print.
- New editing icons: code, bold, italic, image
- New interaction icons: subscribe, unsubscribe, subscribed, reply all, reply, flag.
- The hyperlink icon has been updated to be clearer, chunkier.
- The "home" icon has been updated for style, size and clarity.
- The email icon has been updated for style and clarity, and to fit with the new subscribe icons.
- The document icon has been updated for style.
- The "pin" icon has been updated for style and clarity.
- The Twitter icon has been scaled down to fit with the other social icons.

V3.0.1: 
Mostly maintenance. 
- Fixed an issue with the example page that showed an old "top" icon instead of the actual NEW "refresh" icon.
- Added inverse Google+ and Path.
- Replaced tabs with spaces in the helper CSS.
- Changed the Genericons.com copy/paste tool to serve span's instead of div's for casual icon insertion. It's being converted to "inline-block" anyway.

V3.0:
Mainly maintenance and a few new icons.
- Fast forward, rewind, PollDaddy, Notice, Info, Help, Portfolio
- Updated the feed icon. It's a bit smaller now for consistency, the previous one was rather big.
- So, the previous version numbering, 2.09, wasn't very PHP version compare friendly. So from now on it'll be 3.0, 3.1 etc. Props Ipstenu.
- Genericons.com now has a mini release blog.
- The CSS has prettier formatting, props Konstantin Obenland.

V2.09:
Updated Facebook icon to new version. Updated Instagram logo to use new one-color version. Updated Google+ icon to use same radius as Instagram and Facebook. Added a bunch of new icons, cog, unapprove, cart, media player buttons, tablet, send to tablet.                                            

V2.06:
Included Base64 encoded version. This is necessary for Genericons to work with CDNs in Firefox. Firefox blocks fonts linked from a different domain. A CDN (typically s.example.com) usually puts the font on a subdomain, and is hence blocked in Firefox.

V2.05:
Added a bunch of new icons, including upload to cloud, download to cloud, many more.

V2:
Initial public release