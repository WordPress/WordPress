=== Matomo Analytics - Ethical Stats. Powerful Insights. ===
Contributors: matomoteam
Tags: matomo,analytics,statistics,stats,ecommerce
Requires at least: 4.8
Tested up to: 6.8.2
Stable tag: 5.3.3
Requires PHP: 7.2.5
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Privacy friendly, GDPR compliant and self-hosted. Matomo is the #1 Google Analytics alternative that gives you control of your data. Free and secure.

== Description ==

_Already a Matomo On-Premise or Matomo Cloud user? You need to use the [Connect Matomo plugin](https://wordpress.org/plugins/wp-piwik/) instead of this plugin._

[youtube https://www.youtube.com/watch?v=puxi_Ey0iLc]

For all you WordPress website owners wanting an easier way to get customer insights to grow your business, you can now get the solution the professionals use, for free!

Matomo Analytics is the #1 used Google Analytics alternative that offers a powerful range of features, security and protects the privacy of your users. This enables you to learn how to improve your website, make the right decisions for your business and stand out in the crowd in a safe and trustworthy way.

Matomo’s mission is to give control and data ownership back to the user. By hosting web analytics on your own servers there’s no third-parties taking ownership, no on-selling of data and no-one looking in. This means when you install Matomo in a matter of a few clicks, you’re in full control.

It’s also easier for you to get insights from Matomo Analytics with it’s time-saving interface design and out-of-the-box features, which require less manual configuration than Google Analytics.

Matomo is free, secure and open - your ethical user insights platform.

**How Matomo Analytics for Wordpress solves problems:**

* 100% data ownership, no one else can see your data
* All data is stored in your WordPress and not sent to any third party or different country
* Super easy to install. No coding or technical knowledge needed #nocode
* Free to use forever
* Designed to save you time as an out-of-box solution (including many Ecommerce stores)
* Protects the privacy of your users
* GDPR Manager
* No data sampling
* Opportunities to extend with an ever-growing marketplace
* Supports over 50 languages
* Comes equipped with Matomo Tag Manager

**How Matomo focuses on privacy:**

* Superb user privacy protection
* Heaps of features to anonymize data and IP addresses
* WP shortcode to embed an opt-out feature into your website
* Features to export and delete data for GDPR
* Ability to configure data retention

**Ultimately, Matomo lets you:** learn who your customers are and their needs; understand what content works and how engaged your audience is; identify which marketing channels give you the highest ROI and invest with confidence in channels that convert for your business; and discover blockages and fix pain points for confused visitors to ensure they become satisfied customers.

**Features include:**

* Ecommerce features (supports WooCommerce, Easy Digital Downloads and MemberPress out of the box)
* Campaign tracking
* Visitor profiles
* Tag Manager
* Dashboards
* Segmentation
* Real time reports
* Transitions
* JavaScript error tracking
* Extensive geolocation reports / maps
* Many different visualisations
* Row evolution
* Report comparisons
* Export features
* See the most drastic changes within a given time period
* Supports WP Rest API
* And hundreds of other features
* Easily give your colleagues access to your reports if / when needed
* Easily exclude certain roles, visitors and pages from being tracked
* Supports WordPress Multisite. (Note: Tag Manager feature does not work in MultiSite.)
* Import historical data from Google Analytics or WP Statistics

**[Premium paid features:](https://plugins.matomo.org/premium?wp=1)**

* Heatmaps & Session Recordings
* Form Analytics
* Media Analytics
* Funnels
* SEO features - Keyword rankings
* Custom reporting
* Cohorts
* Users flow

**Prerequisites and technical requirements:**

Running Matomo Analytics on your server can use significant resources. Whenever someone visits your WordPress website, your server will need to serve your WordPress pages to the user, as well as tracking the user journey in Matomo, resulting in an additional request for each page view.

* The minimum PHP memory limit is 128Mb, but we recommend to use a higher limit (memory_limit = 256M).
* PHP 7.2 at minimum is required.
* If you have high traffic website, or manage a lot of websites with WordPress MultiSite, we recommend installing [Matomo On-Premise](https://matomo.org/docs/installation/) or signup to [Matomo Cloud](https://matomo.org/hosting/) and install the [Connect Matomo plugin](https://wordpress.org/plugins/wp-piwik/) instead.
* Needing to know more before you install? Have a [read through the most popular FAQs to ensure you’re making the right choice for you](https://matomo.org/faq/wordpress/what-are-the-requirements-for-matomo-for-wordpress/).

Over 1 million websites in over 190 countries are using Matomo already. Join the revolution too! Install Matomo on your Wordpress website completely free and take back full control of your data!

**Third party resources we use:**

* After activating this plugin, it will download a Geolocation database (DBIP-City.mmdb) from [DB IP](https://db-ip.com/db/download/ip-to-city-lite?refid=mtm) into your uploads directory. The DB-IP database is distributed under the [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/). This database is needed to detect the location of your visitors based on their IP.
* When you enter a URL in the SEO ranking widget, then a request with the entered URL may be sent to Google, Alexa, Bing, and other SEO providers.

== Installation ==

= Minimum Requirements =

* PHP 7.2 or greater
* MySQL 5.5 or greater is recommended
* 128MB memory or greater is recommended

= Automatic installation =

* Log in to your WordPress Admin Dashboard
* Navigate to the "Plugins" menu
* Click "Add New"
* Search for "Matomo Analytics"
* Click "Install Now" and then "Activate"

= Manual installation =

* Downloading the plugin
* Upload it to your web server using an FTP application. [Learn more](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation)

= Once installed =

* Go to "Matomo Analytics" in the WordPress Admin Dashboard.
* Click on "Activate tracking" in the "Get started" page.
* That's it! You can further customize the tracking in the settings page.

== Frequently Asked Questions ==

= Is there a demo available?
Yes, check out the online demo for Matomo at [demo.matomo.cloud](https://demo.matomo.cloud)

= Does Matomo care about security?
Security is a top priority at Matomo. As potential issues are discovered, we validate, patch and release fixes as quickly as we can. We have a security bug bounty program in place that rewards researchers for finding security issues and disclosing them to us.
[Learn more](https://matomo.org/security/) or check out our [HackerOne program](https://hackerone.com/matomo).

= How can I get involved?
We believe in liberating Web Analytics, providing a free platform for simple and advanced analytics. Matomo was built by dozens of people like you,
and we need your help to make Matomo better… Why not participate in a useful project today? [Learn how you can contribute to Matomo.](https://matomo.org/get-involved)

= How do you ensure quality?
The Matomo project uses an ever-expanding comprehensive set of thousands of unit tests and hundreds of automated integration tests, system tests, JavaScript tests, and screenshot UI tests, running on a continuous integration server as part of its software quality assurance. [Learn more](https://developer.matomo.org/guides/tests)

= Can I disable the Tag Manager?
The Tag Manager can be disabled by placing `define('MATOMO_ENABLE_TAG_MANAGER', false);` in your `wp-config.php`.

The Tag Manager does currently not work in WP Multisite mode.

= How do you support WP Multisite?
[Click here to learn more](https://matomo.org/faq/wordpress/does-it-support-wp-multisite/)

= Which MySQL versions are supported?
Matomo should run on most MySQL versions. However, we only support MySQL 5.5 and newer. It should also work with MariaDB and other MySQL compatible databases.

= Which browsers do you support?
* Tracking: We support pretty much all browsers even very old browsers
* Admin: The Matomo UI does not support IE9 or older.

= What are your contact details?
Website: [matomo.org](https://matomo.org)
About us: [matomo.org/team/](https://matomo.org/team/)
Contact us: [matomo.org/contact/](https://matomo.org/contact/)

= Can I import reports from WP Statistics plugin?

Yes, you can [import historical data from WP Statistics](https://matomo.org/faq/wordpress/how-do-i-import-data-from-wp-statistics-plugin-into-matomo-for-wordpress/) so you don't lose any data when migrating to our plugin.

= Where do I find all other available FAQs? =

Needing to know more? [Click here to view all of our FAQs on our website](https://matomo.org/faq/wordpress/)

== Credits ==

* The entire Matomo team and everyone who contributed
* Andr&eacute; Br&auml;kling who is the Author of the [Connect Matomo plugin](https://github.com/braekling/WP-Matomo)

== Screenshots ==

1. Customizable Matomo Dashboard. Choose from many widgets and adjust layout.
2. Visits log. See every action your visitors took.
3. Behaviour insights.
4. Acquisition insights.
5. Ecommerce insights.
6. Configure tracking code without developer knowledge.
7. Easily give your co-workers access to your Matomo reports.
8. Define who and what should not be tracked.
9. Options to anonymize data so you don't track personal data.
10. Automatically delete old data you no longer need to be privacy compliant and to free your server from not needed data.
11. Summary page for getting a quick overview.

== Changelog ==

[See changelog for all versions](https://github.com/matomo-org/matomo-for-wordpress/blob/develop/CHANGELOG.md).
