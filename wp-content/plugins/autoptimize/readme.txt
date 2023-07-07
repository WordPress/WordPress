=== Autoptimize ===
Contributors: futtta, optimizingmatters, zytzagoo, turl
Tags: optimize, minify, performance, images, core web vitals, lazy-load, pagespeed, google fonts
Donate link: http://blog.futtta.be/2013/10/21/do-not-donate-to-me/
Requires at least: 5.3 
Tested up to: 6.2
Requires PHP: 5.6
Stable tag: 3.1.8.1

Autoptimize speeds up your website by optimizing JS, CSS, images (incl. lazy-load), HTML and Google Fonts, asyncing JS, removing emoji cruft and more.

== Description ==

Autoptimize makes optimizing your site really easy. It can aggregate, minify and cache scripts and styles, injects CSS in the page head by default but can also inline critical CSS and defer the aggregated full CSS, moves and defers scripts to the footer and minifies HTML. You can optimize and lazy-load images (with support for WebP and AVIF formats), optimize Google Fonts, async non-aggregated JavaScript, remove WordPress core emoji cruft and more. As such it can improve your site's performance even when already on HTTP/2! There is extensive API available to enable you to tailor Autoptimize to each and every site's specific needs.
If you consider performance important, you really should use one of the many caching plugins to do page caching. Some good candidates to complement Autoptimize that way are e.g. [Speed Booster pack](https://wordpress.org/plugins/speed-booster-pack/), [KeyCDN's Cache Enabler](https://wordpress.org/plugins/cache-enabler), [WP Super Cache](http://wordpress.org/plugins/wp-super-cache/) or if you use Cloudflare [WP Cloudflare Super Page Cache](https://wordpress.org/plugins/wp-cloudflare-page-cache/).

> <strong>Autoptimize Pro</strong><br>
> [Autoptimize Pro is a premium Power-Up](https://misc.optimizingmatters.com/partners/?from=partnertab&partner=aopro),  adding image optimization, CDN, automatic critical CSS rules and extra “booster” options, all in one handy subscription to [make your site even faster!](https://misc.optimizingmatters.com/partners/?from=partnertab&partner=aopro)!

> <strong>Premium Support</strong><br>
> We provide great [Premium Support and Web Performance Optimization services](https://misc.optimizingmatters.com/partners/?from=partnertab&partner=autoptimizepro), check out our offering on [https://accelera.autoptimize.com/](https://misc.optimizingmatters.com/partners/?from=partnertab&partner=autoptimizepro)!

(Speed-surfing image  under creative commons [by LL Twistiti](https://www.flickr.com/photos/twistiti/818552808/))

== Installation ==

Just install from your WordPress "Plugins > Add New" screen and all will be well. Manual installation is very straightforward as well:

1. Upload the zip file and unzip it in the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Settings > Autoptimize` and enable the options you want. Generally this means "Optimize HTML/ CSS/ JavaScript".

== Frequently Asked Questions ==

= What does the plugin do to help speed up my site? =

It minifies all scripts and styles and configures your webserver to compresses them with good expires headers. JavaScript be default will be made non-render-blocking and CSS can be too by adding critical CSS. You can configure it to combine (aggregate) CSS & JS-files, in which case styles are moved to the page head, and scripts to the footer. It also minifies the HTML code and can also optimize images and Google Fonts, making your page really lightweight.

= But I'm on HTTP/2, so I don't need Autoptimize? =

HTTP/2 is a great step forward for sure, reducing the impact of multiple requests from the same server significantly by using the same connection to perform several concurrent requests and for that reason on new installations Autoptimize will not aggregate CSS and JS files any more. That being said, [concatenation of CSS/ JS can still make a lot of sense](http://engineering.khanacademy.org/posts/js-packaging-http2.htm), as described in [this css-tricks.com article](https://css-tricks.com/http2-real-world-performance-test-analysis/) and this [blogpost from one of the Ebay engineers](http://calendar.perfplanet.com/2015/packaging-for-performance/). The conclusion; configure, test, reconfigure, retest, tweak and look what works best in your context. Maybe it's just HTTP/2, maybe it's HTTP/2 + aggregation and minification, maybe it's HTTP/2 + minification (which AO can do as well, simply untick the "aggregate JS-files" and/ or "aggregate CSS-files" options). And Autoptimize can do a lot more then "just" optimizing your JS & CSS off course ;-)

= Will this work with my blog? =

Although Autoptimize comes without any warranties, it will in general work flawlessly if you configure it correctly. See "Troubleshooting" below for info on how to configure in case of problems. If you want you can [test Autoptimize on a new free dummy site, courtesy of tastewp.com](https://demo.tastewp.com/autoptimize).

= Why is jquery.min.js not optimized when aggregating JavaScript? =

Starting from AO 2.1 WordPress core's jquery.min.js is not optimized for the simple reason a lot of popular plugins inject inline JS that is not aggregated either (due to possible cache size issues with unique code in inline JS) which relies on jquery being available, so excluding jquery.min.js ensures that most sites will work out of the box. If you want optimize jquery as well, you can remove it from the JS optimization exclusion-list (you might have to enable "also aggregate inline JS" as well or switch to "force JS in head").

= Why is Autoptimized JS render blocking? =

This happens when aggregating JavaSCript and ticking the "force in head" option or when not aggregating and not deferring. Consider changing settings.

= Why is the autoptimized CSS still called out as render blocking? =

With the default Autoptimize configuration the CSS is linked in the head, which is a safe default but has Google PageSpeed Insights complaining. You can look into "inline all CSS" (easy) or "inline and defer CSS" (better) which are explained in this FAQ as well.

= What is the use of "inline and defer CSS"? =

CSS in general should go in the head of the document. Recently a.o. Google started promoting deferring non-essential CSS, while inlining those styles needed to build the page above the fold. This is especially important to render pages as quickly as possible on mobile devices. As from Autoptimize 1.9.0 this is easy; select "inline and defer CSS", paste the block of "above the fold CSS" in the input field (text area) and you're good to go!

= But how can one find out what the "above the fold CSS" is? =

There's no easy solution for that as "above the fold" depends on where the fold is, which in turn depends on screensize. There are some tools available however, which try to identify just what is "above the fold". [This list of tools](https://github.com/addyosmani/above-the-fold-css-tools) is a great starting point. The [Sitelocity critical CSS generator](https://www.sitelocity.com/critical-path-css-generator) and [Jonas Ohlsson's criticalpathcssgenerator](http://jonassebastianohlsson.com/criticalpathcssgenerator/) are nice basic solutions and [http://criticalcss.com/](http://misc.optimizingmatters.com/partners/?from=faq&amp;partner=critcss) is a premium solution by the same Jonas Ohlsson. Alternatively [this bookmarklet](https://gist.github.com/PaulKinlan/6284142) (Chrome-only) can be helpful as well.

= Or should you inline all CSS? =

The short answer: probably not. Although inlining all CSS will make the CSS non-render blocking, it will result in your base HTML-page getting significantly bigger thus requiring more "roundtrip times". Moreover when considering multiple pages being requested in a browsing session the inline CSS is sent over each time, whereas when not inlined it would be served from cache. Finally the inlined CSS will push the meta-tags in the HTML down to a position where Facebook or Whatsapp might not look for it any more, breaking e.g. thumbnails when sharing on these platforms.

= My cache is getting huge, doesn't Autoptimize purge the cache? =

Autoptimize does not have its proper cache purging mechanism, as this could remove optimized CSS/JS which is still referred to in other caches, which would break your site. Moreover a fast growing cache is an indication of [other problems you should avoid](http://blog.futtta.be/2016/09/15/autoptimize-cache-size-the-canary-in-the-coal-mine/).

Instead you can keep the cache size at an acceptable level by either:

* disactivating the "aggregate inline JS" and/ or "aggregate inline CSS" options
* excluding JS-variables (or sometimes CSS-selectors) that change on a per page (or per pageload) basis. You can read how you can do that [in this blogpost](http://blog.futtta.be/2014/03/19/how-to-keep-autoptimizes-cache-size-under-control-and-improve-visitor-experience/).

Despite above objections, there are 3rd party solutions to automatically purge the AO cache, e.g. using [this code](https://wordpress.org/support/topic/contribution-autoptimize-cache-size-under-control-by-schedule-auto-cache-purge/) or [this plugin](https://wordpress.org/plugins/bi-clean-cache/), but for reasons above these are to be used only if you really know what you're doing.

= "Clear cache" doesn't seem to work? =

When clicking the "Delete Cache" link in the Autoptimize dropdown in the admin toolbar, you might to get a "Your cache might not have been purged successfully". In that case go to Autoptimizes setting page and click the "Save changes & clear cache"-button.

Moreover don't worry if your cache never is down to 0 files/ 0KB, as Autoptimize (as from version 2.2) will automatically preload the cache immediately after it has been cleared to speed further minification significantly up.

= My site looks broken when I purge Autoptimize's cache! =

When clearing AO's cache, no page cache should contain pages (HTML) that refers to the removed optimized CSS/ JS. Although for that purpose there is integration between Autoptimize and some page caches, this integration does not cover 100% of setups so you might need to purge your page cache manually.

= Can I still use Cloudflare's Rocket Loader? =

Cloudflare Rocket Loader is a pretty advanced but invasive way to make JavaScript non-render-blocking, which [Cloudflare still considers Beta](https://wordpress.org/support/topic/rocket-loader-breaking-onload-js-on-linked-css/#post-9263738). Sometimes Autoptimize & Rocket Loader work together, sometimes they don't. The best approach is to disable Rocket Loader, configure Autoptimize and re-enable Rocket Loader (if you think it can help) after that and test if everything still works.

At the moment (June 2017) it seems RocketLoader might break AO's "inline & defer CSS", which is based on [Filamentgroup’s loadCSS](https://github.com/filamentgroup/loadCSS), resulting in the deferred CSS not loading.

= I tried Autoptimize but my Google Pagespeed Scored barely improved =

Autoptimize is not a simple "fix my Pagespeed-problems" plugin; it "only" aggregates & minifies (local) JS & CSS and images and allows for some nice extra's as removing Google Fonts and deferring the loading of the CSS. As such Autoptimize will allow you to improve your performance (load time measured in seconds) and will probably also help you tackle some specific Pagespeed warnings. If you want to improve further, you will probably also have to look into e.g. page caching and your webserver configuration, which will improve real performance (again, load time as measured by e.g. https://webpagetest.org) and your "performance best practice" pagespeed ratings.

= What can I do with the API? =

A whole lot; there are filters you can use to conditionally disable Autoptimize per request, to change the CSS- and JS-excludes, to change the limit for CSS background-images to be inlined in the CSS, to define what JS-files are moved behind the aggregated one, to change the defer-attribute on the aggregated JS script-tag, ... There are examples for some filters in autoptimize_helper.php_example and in this FAQ.

= How does CDN work? =

Starting from version 1.7.0, CDN is activated upon entering the CDN blog root directory (e.g. http://cdn.example.net/wordpress/). If that URL is present, it will used for all Autoptimize-generated files (i.e. aggregated CSS and JS), including background-images in the CSS (when not using data-uri's).

If you want your uploaded images to be on the CDN as well, you can change the upload_url_path in your WordPress configuration (/wp-admin/options.php) to the target CDN upload directory (e.g. http://cdn.example.net/wordpress/wp-content/uploads/). Do take into consideration this only works for images uploaded from that point onwards, not for images that already were uploaded. Thanks to [BeautyPirate for the tip](http://wordpress.org/support/topic/please-don%c2%b4t-remove-cdn?replies=15#post-4720048)!

= Why aren't my fonts put on the CDN as well? =

Autoptimize supports this, but it is not enabled by default because [non-local fonts might require some extra configuration](http://davidwalsh.name/cdn-fonts). But if you have your cross-origin request policy in order, you can tell Autoptimize to put your fonts on the CDN by hooking into the API, setting `autoptimize_filter_css_fonts_cdn` to `true` this way;

`add_filter( 'autoptimize_filter_css_fonts_cdn', '__return_true' );`

= I'm using Cloudflare, what should I enter as CDN root directory =

Nothing, when on Cloudflare your autoptimized CSS/ JS is on the Cloudflare's CDN automatically.

= How can I force the aggregated files to be static CSS or JS instead of PHP? =

If your webserver is properly configured to handle compression (gzip or deflate) and cache expiry (expires and cache-control with sufficient cacheability), you don't need Autoptimize to handle that for you. In that case you can check the "Save aggregated script/css as static files?"-option, which will force Autoptimize to save the aggregated files as .css and .js-files (meaning no PHP is needed to serve these files). This setting is default as of Autoptimize 1.8.

= How does "exclude from optimizing" work? =

Both CSS and JS optimization can skip code from being aggregated and minimized by adding "identifiers" to the comma-separated exclusion list. The exact identifier string to use can be determined this way:

* if you want to exclude a specific file, e.g. wp-content/plugins/funkyplugin/css/style.css, you could simply exclude "funkyplugin/css/style.css"
* if you want to exclude all files of a specific plugin, e.g. wp-content/plugins/funkyplugin/js/*, you can exclude for example "funkyplugin/js/" or "plugins/funkyplugin"
* if you want to exclude inline code, you'll have to find a specific, unique string in that block of code and add that to the exclusion list. Example: to exclude `<script>funky_data='Won\'t you take me to, Funky Town'</script>`, the identifier is "funky_data".

= Troubleshooting Autoptimize =

Have a look at the troubleshooitng instructions at https://blog.futtta.be/2022/05/05/what-to-do-when-autoptimize-breaks-your-site/

= I excluded files but they are still being autoptimized? =

AO minifies excluded JS/ CSS if the filename indicates the file is not minified yet. As of AO 2.5 you can disable this on the "JS, CSS & HTML"-tab under misc. options by unticking "minify excluded files".

= Help, I have a blank page or an internal server error after enabling Autoptimize!! =

Make sure you're not running other HTML, CSS or JS minification plugins (BWP minify, WP minify, ...) simultaneously with Autoptimize or disable that functionality your page caching plugin (W3 Total Cache, WP Fastest Cache, ...). Try enabling only CSS or only JS optimization to see which one causes the server error and follow the generic troubleshooting steps to find a workaround.

= But I still have blank autoptimized CSS or JS-files! =

If you are running Apache, the .htaccess file written by Autoptimize can in some cases conflict with the AllowOverrides settings of your Apache configuration (as is the case with the default configuration of some Ubuntu installations), which results in "internal server errors" on the autoptimize CSS- and JS-files. This can be solved by [setting AllowOverrides to All](http://httpd.apache.org/docs/2.4/mod/core.html#allowoverride).

= Can't log in on domain mapped multisites =

Domain mapped multisites require Autoptimize to be initialized at a different WordPress action, add this line of code to your wp-config.php to make it so to hook into `setup_theme` for example:

`define( 'AUTOPTIMIZE_SETUP_INITHOOK', 'setup_theme' );`

= I get no error, but my pages are not optimized at all? =

Autoptimize does a number of checks before actually optimizing. When one of the following is true, your pages won't be optimized:

* when in the customizer
* if there is no opening `<html` tag
* if there is `<xsl:stylesheet` in the response (indicating the output is not HTML but XML)
* if there is `<html amp` in the response (as AMP-pages are optimized already)
* if the output is an RSS-feed (is_feed() function)
* if the output is a WordPress administration page (is_admin() function)
* if the page is requested with ?ao_noptimize=1 appended to the URL
* if code hooks into Autoptimize to disable optimization (see topic on Visual Composer)
* if other plugins use the output buffer in an incompatible manner (disable other plugins selectively to identify the culprit)

= Visual Composer, Beaver Builder and similar page builder solutions are broken!! =

Disable the option to have Autoptimize active for logged on users and go crazy dragging and dropping ;-)

= Help, my shop checkout/ payment don't work!! =

Disable the option to optimize cart/ checkout pages (works for WooCommerce, Easy Digital Downloads and WP eCommerce).

= Revolution Slider is broken! =

Make sure `js/jquery/jquery.min.js` is in the comma-separated list of JS optimization exclusions (this is excluded in the default configuration).

= I'm getting "jQuery is not defined" errors =

In that case you have un-aggregated JavaScript that requires jQuery to be loaded, so you'll have to add `js/jquery/jquery.min.js` to the comma-separated list of JS optimization exclusions.

= I use NextGen Galleries and a lot of JS is not aggregated/ minified? =

NextGen Galleries does some nifty stuff to add JavaScript. In order for Autoptimize to be able to aggregate that, you can either disable Nextgen Gallery's resourced manage with this code snippet `add_filter( 'run_ngg_resource_manager', '__return_false' );` or you can tell Autoptimize to initialize earlier, by adding this to your wp-config.php: `define("AUTOPTIMIZE_INIT_EARLIER","true");`

= What is noptimize? =

Starting with version 1.6.6 Autoptimize excludes everything inside noptimize tags, e.g.:
`&lt;!&#45;&#45;noptimize&#45;&#45;>&lt;script>alert('this will not get autoptimized');&lt;/script>&lt;!&#45;&#45;/noptimize&#45;&#45;>`

You can do this in your page/ post content, in widgets and in your theme files (consider creating [a child theme](http://codex.wordpress.org/Child_Themes) to avoid your work being overwritten by theme updates).

= Can I change the directory & filename of cached autoptimize files? =

Yes, if you want to serve files from e.g. /wp-content/resources/aggregated_12345.css instead of the default /wp-content/cache/autoptimize/autoptimize_12345.css, then add this to wp-config.php:
`
define('AUTOPTIMIZE_CACHE_CHILD_DIR','/resources/');
define('AUTOPTIMIZE_CACHEFILE_PREFIX','aggregated_');
`

= Does this work with non-default WP_CONTENT_URL ? =

No, Autoptimize does not support a non-default WP_CONTENT_URL out-of-the-box, but this can be accomplished with a couple of lines of code hooking into Autoptimize's API.

= Can the generated JS/ CSS be pre-gzipped? =

Yes, but this is off by default. You can enable this by passing ´true´ to ´autoptimize_filter_cache_create_static_gzip´. You'll obviously still have to configure your webserver to use these files instead of the non-gzipped ones to avoid the overhead of on-the-fly compression.

= What does "remove emojis" do? =

This new option in Autoptimize 2.3 removes the inline CSS, inline JS and linked JS-file added by WordPress core. As such is can have a small positive impact on your site's performance.

= Is "remove query strings" useful? =

Although some online performance assessment tools will single out "query strings for static files" as an issue for performance, in general the impact of these is almost non-existant. As such Autoptimize, since version 2.3, allows you to have the query string (or more precisely the "ver"-parameter) removed, but ticking "remove query strings from static resources" will have little or no impact of on your site's performance as measured in (milli-)seconds.

= (How) should I optimize Google Fonts? =

Google Fonts are typically loaded by a "render blocking" linked CSS-file. If you have a theme and plugins that use Google Fonts, you might end up with multiple such CSS-files. Autoptimize (since version 2.3) now let's you lessen the impact of Google Fonts by either removing them alltogether or by optimizing the way they are loaded. There are two optimization-flavors; the first one is "combine and link", which replaces all requests for Google Fonts into one request, which will still be render-blocking but will allow the fonts to be loaded immediately (meaning you won't see fonts change while the page is loading). The alternative is "combine and load async" which uses JavaScript to load the fonts in a non-render blocking manner but which might cause a "flash of unstyled text".

= Should I use "preconnect" =

Preconnect is a somewhat advanced feature to instruct browsers ([if they support it](https://caniuse.com/#feat=link-rel-preconnect)) to make a connection to specific domains even if the connection is not immediately needed. This can be used e.g. to lessen the impact of 3rd party resources on HTTPS (as DNS-request, TCP-connection and SSL/TLS negotiation are executed early). Use with care, as preconnecting to too many domains can be counter-productive.

= When can('t) I async JS? =

JavaScript files that are not autoptimized (because they were excluded or because they are hosted elsewhere) are typically render-blocking. By adding them in the comma-separated "async JS" field, Autoptimize will add the async flag causing the browser to load those files asynchronously (i.e. non-render blocking). This can however break your site (page), e.g. if you async "js/jquery/jquery.min.js" you will very likely get "jQuery is not defined"-errors. Use with care.

= How does image optimization work? =

When image optimization is on, Autoptimize will look for png, gif, jpeg (.jpg) files in image tags and in your CSS files that are loaded from your own domain and change the src (source) to the ShortPixel CDN for those. Important: this can only work for publicly available images, otherwise the image optimization proxy will not be able to get the image to optimize it, so firewalls or proxies or password protection or even hotlinking-prevention might break image optimization.

= Can I use image optimization for my intranet/ protected site? =

No; Image optimization depends on the ability of the external image optimization service to fetch the original image from your site, optimize it and save it on the CDN. If you images cannot be downloaded by anonymous visitors (due to firewall/ proxy/ password protection/ hotlinking-protection), image optimization will not work.

= Where can I get more info on image optimization? =

Have a look at [Shortpixel's FAQ](https://shortpixel.helpscoutdocs.com/category/60-shortpixel-ai-cdn).

= Can I disable AO listening to page cache purges? =

As from AO 2.4 AO "listens" to page cache purges to clear its own cache. You can disable this behavior with this filter;

`
add_filter('autoptimize_filter_main_hookpagecachepurge','__return_false');`

= Some of the non-ASCII characters get lost after optimization =

By default AO uses non multibyte-safe string methods, but if your PHP has the mbstring extension you can enable multibyte-safe string functions with this filter;

`
add_filter('autoptimize_filter_main_use_mbstring', '__return_true');`

= I can't get Critical CSS working =

Check [the FAQ on the (legacy) "power-up" here](https://wordpress.org/plugins/autoptimize-criticalcss/#faq), this info will be integrated in this FAQ at a later date.

= Do I still need the Critical CSS power-up when I have Autoptimize 2.7 or higher? =

No, the Critical CSS power-up is not needed any more, all functionality (and many fixes/ improvements) are now part of Autoptimize.

= What does "enable 404 fallbacks" do? Why would I need this? =

Autoptimize caches aggregated & optimized CSS/ JS and links to those cached files are stored in the HTML, which will be stored in a page cache (which can be a plugin, can be at host level, can be at 3rd party, in the Google cache, in a browser). If there is HTML in a page cache that links to Autoptimized CSS/ JS that has been removed in the mean time (when the cache was cleared) then the page from cache will not look/ work as expected as the CSS or JS were not found (a 404 error).

This setting aims to prevent things from breaking by serving "fallback" CSS or JS. The fallback-files are copies of the first Autoptimized CSS & JS files created after the cache was emptied and as such will based on the homepage. This means that the CSS/ JS migth not apply 100% on other pages, but at least the impact of missing CSS/ JS will be lessened (often significantly).

When the option is enabled, Autoptimize adds an `ErrorDocument 404` to the .htaccess (as used by Apache) and will also hook into WordPress core `template_redirect` to capture 404's handled by Wordpress. When using NGINX something like below should work (I'm not an NGINX specialist, but it does work for me);

`
location ~* /wp-content/cache/autoptimize/.*\.(js|css)$ {
    try_files $uri $uri/ /wp-content/autoptimize_404_handler.php;
}`

And this a nice alternative approach (provided by fboylovesyou);

`location ~* /wp-content/cache/autoptimize/.*\.(css)$ {
    try_files $uri $uri/ /wp-content/cache/autoptimize/css/autoptimize_fallback.css;
}
location ~* /wp-content/cache/autoptimize/.*\.(js)$ {
    try_files $uri $uri/ /wp-content/cache/autoptimize/js/autoptimize_fallback.js;
}`

= What open source software/ projects are used in Autoptimize? =

The following great open source projects are used in Autoptimize in some form or another:

* [Mr Clay's Minify](https://github.com/mrclay/minify/) for JS & HTML minification
* [YUI CSS compressor PHP Port](https://github.com/tubalmartin/YUI-CSS-compressor-PHP-port) for CSS minification
* [Lazysizes](https://github.com/aFarkas/lazysizes) for lazyload
* [Persist Admin Notices Dismissal](https://github.com/w3guy/persist-admin-notices-dismissal) for notices in the administration screens
* [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker/) for automated updates from Github for the beta version
* [LoadCSS](https://github.com/filamentgroup/loadCSS) for deferring full CSS
* [jQuery cookie](https://github.com/carhartl/jquery-cookie) to store the "futtta about" category selection in a cookie
* [jQuery tablesorter](https://github.com/christianbach/tablesorter) for the critical CSS rules/ jobs display
* [jQuery unslider](https://github.com/idiot/unslider/) for the mini-slider in the top right corner on the main settings page (repo gone)
* [JavaScript-md5](https://github.com/blueimp/JavaScript-MD5) for critical CSS rules editing
* [Speed Booster Pack](https://wordpress.org/plugins/speed-booster-pack/) for advanced JS deferring
* [Disable Remove Google Fonts](https://wordpress.org/plugins/disable-remove-google-fonts/) for additional Google Font removal

= Where can I get help? =

You can get help on the [wordpress.org support forum](http://wordpress.org/support/plugin/autoptimize). If you are 100% sure this your problem cannot be solved using Autoptimize configuration and that you in fact discovered a bug in the code, you can [create an issue on GitHub](https://github.com/futtta/autoptimize/issues). If you're looking for premium support, check out our [Autoptimize Pro Support and Web Performance Optimization services](http://autoptimize.com/).

= I want out, how should I remove Autoptimize? =

* Disable the plugin (this will remove options and cache)
* Remove the plugin
* Clear any cache that might still have pages which reference Autoptimized CSS/JS (e.g. of a page caching plugin such as WP Super Cache)

= How can I help/ contribute? =

Just [fork Autoptimize on Github](https://github.com/futtta/autoptimize) and code away!

== Changelog ==

= 3.1.8.1 =
* urgent fix for PHP error, sorry about that!

= 3.1.8 =
* Images: improve optmization logic for background images
* Critical CSS: don't trigger custom_post rule if not is_singular + adding debug logging for rule selection
* some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.7 =
* security: improve validation (import) and sanitization (output) of critical CSS rules, to fix a medium severity Admin+ Stored Cross-Site Scripting vulnerability as reported by WP Scan Security.

= 3.1.6 =
* CSS: removing trailing slashes in <link tags for more W3 HTML validation love
* Extra: also dequeue WooCommerce block CSS if "remove WordPress block CSS" option is active
* imgopt: also act on non-aggregated inline CSS
* imgopt: added logic to warn users if Shortpixel can't reach their site
* backend: AO toolbar JS/ CSS is finally minified as well.
* explicitly disable optimization of login pages
* some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.5 =
* improvements to JSMin by Robert Ehrenleitner (big thanks Robert!).
* do not consider jquery.js as minified any more (WordPress now uses jquery.min.js by default and jquery.js is the unminified version).
* fix for "undefined array key" PHP errors in autoptimizeCriticalCSSCron.php
* some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.4 =
* Improvement: when all CSS is inlined, try doing so after SEO meta-tags (just before ld+json script tag which most SEO plugins add as last item on their list).
* Img opt: also optimize images set in data-background and data-retina attributes (+ filter to easily add other attributes)
* CSS opt: filter to enable AO to skip minification of calc formulas in CSS (as the CSS minifier on rare occasions breaks those)
* Multiple other filters added
* Some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.3 =
* Multiple fixes for metabox LCP image preloads (thanks [Kishorchand](https://foxscribbler.com/) for notifying & providing a staging environment to debug on).
* Fix in revslider compatibility (hat tip [Waqar Ahmed for reporting & helping out](https://wordpress.org/support/topic/issue-with-latest-version-of-slider-revolution/) ).
* No image optimization or criticalcss attempts on localhost installations any more + notification of that fact if localhost detected.
* Some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.2 =
* Google Fonts: some more removal logic
* fix for 404 fallback bug (hat tip to Asif for finding & reporting)
* Some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.1.1 =
* Quick workaround for an autoload conflict with JetFormBuilder (and maybe other Crocoblock plugins?) that causes a critical error on the AO settings page.

= 3.1.1 =
* images: when optimizing images and lazyloading is on, then by default do not set an LQIP (low quality image placeholder) any more (reason: it might *look* nice but it comes with a small-ish perf. penalty). This can be re-enabled by returning true to the `autoptimize_filter_imgopt_lazyload_dolqip` filter.
* security: further improvements to critical CSS settings page (again with the great assistance of WPScan Security).
* some other minor changes/ improvements/ filters, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta).

= 3.1.0 =
* new HTML sub-option: "minify inline CSS/ JS" (off by default).
* new Misc option: permanently allow the "do not run compatibility logic" flag to be removed (which was set for users upgrading from AO 2.9.* to AO 3.0.* as the assumption was things were working anyway).
* security: improvements to the critical CSS settings page to fix authenticated cross site scripting issues as reported by WPScan Security.
* bugfix: "defer inline JS" of very large chunks of inline JS could cause server errors (PCRE crash actually) so not deferring if string is more then 200000 characters (filter available).
* some other minor changes/ improvements/ hooks, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta)

= 3.0.4 =
* fix for "undefined array key ao_post_preload” on post/ page edit screens
* fix for image optimization altering inline JS that contains an `<img` tag if lazyload is not active
* improvements to exit survey
* confirmed working with WordPress 6.0

= 3.0.3 =
* fix for images being preloaded without this being configured when lazyload is on and per page/post settings are off.
* ensure critical CSS schedule is always known.
* when deferring non-aggregated JS, make the optimatization exclusions take the full script-tag into account instead of just the src URL.

= 3.0.2 =
* rollback automatic "minify inline CSS/ JS" which broke more then expected, this will come back as a separate default off option later and can now be enabled with a simple filter: `add_filter( 'autoptimize_html_minify_inline_js_css', '__return_true');` .
* fix for "Call to undefined method autoptimizeOptionWrapper::delete_option()" in autoptimizeVersionUpdatesHandler.php

= 3.0.1 =
* fix for minification of inline script with type text/template breaking the template (e.g. ninja forms), hat tip to @bobsled.
* fix for regression in import of CSS-files where e.g. fontawesome CSS was broken due to being escaped again with help of @bobsled, thanks man!

= 3.0.0 =
* fundamental change for new installations: by default Autoptimize will not aggregate JS/ CSS any more (HTTP/2 is ubiquitous and there are other advantages to not aggregating esp. re. inline JS/ CSS and dependancies)
* new: no API needed any more to create manual critical CSS rules. 
* new: "Remove WordPress blocks CSS" option on the "Extra" tab to remove block- and global styles (and SVG).
* new: compatibility logic for "edit with elementor", "revolution slider", for non-aggregated inline JS requiring jQuery even if not excluded (= auto-exclude of jQuery) and JS-heavy WordPress blocks (Gutenberg)
* new: configure an image to be preloaded on a per page/ post basis for better LCP.
* improvement: defer inline now also allowed if inline JS contains nonce or post_id.
* improvement: settings export/ import on critical CSS tab now takes into account all Autoptimize settings, not just the critical CSS ones.
* technical improvement: all criticalCSS classes were refactored, removing use of global variables.
* technical improvement: automated unit tests on Travis-CI for PHP versions 7.2 to 8.1.
* fix: stop Divi from clearing Autoptimize's cache [which is pretty counter-productive](https://blog.futtta.be/2018/11/17/warning-divi-purging-autoptimizes-cache/).
* misc smaller fixes/ improvements, see the [GitHub commit log](https://github.com/futtta/autoptimize/commits/beta)

= older =
* see [https://plugins.svn.wordpress.org/autoptimize/tags/2.9.5.1/readme.txt](https://plugins.svn.wordpress.org/autoptimize/tags/2.9.5.1/readme.txt)
