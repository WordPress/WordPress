# Free Open Source Matomo Tag Manager

[![Build Status](https://github.com/matomo-org/tag-manager/actions/workflows/matomo-tests.yml/badge.svg?branch=5.x-dev)](https://github.com/matomo-org/tag-manager/actions/workflows/matomo-tests.yml)

## Description

<div class="alert alert-info">
Are you a developer? Please consider contributing more tags to our open source tag manager. Learn more on how to develop custom tags, triggers, and variables.
</div>

<div class="alert">
When you install this version of Matomo Tag Manager, users with admin access will be able to create custom HTML tags, triggers, and variables that may execute JavaScript on your website. These custom templates could be misused to steal for example sensitive information from users (known as <a href="https://en.wikipedia.org/wiki/Cross-site_scripting">XSS</a>). You can disable these custom templates under "Administration => General Settings". We will later have new permissions in Matomo that allow you to configure who will be able to use these kind of templates.
</div>

Matomo Tag Manager lets you manage and unify all your tracking and marketing tags. Tags are also known as snippets or pixels. Such tags are typically JavaScript code or HTML and let you integrate various features into your site in just a few clicks, for example:

* Analytics
* Conversion Tracking
* Newsletter signups
* Exit popups and surveys
* Remarketing
* Social widgets
* Affiliates
* Ads
* and more

It makes your life easier when you want to modify any of these snippets on your website as you will no longer need a developer to make the needed changes for you. Instead of waiting for someone to make these changes and to deploy your website, you can now easily make the needed changes yourself. This lets you not only bring changes to the market faster, but also reduces cost.

For example want to track an event into your Matomo whenever a certain button is clicked? It will take you only a few clicks to get the insights you need when you need them which in return lets you make decisions faster.

Matomo Tag Manager also comes in handy if you embed many third-party snippets into your website and want to bring in some order to oversee all the snippets that are embedded and have a convenient way to manage them. It also makes sure all that all snippets are implemented correctly and improves the performance of your website.

If you have different environments for your website or platform or want to test new changes before making them available globally you will be please to hear that we have you covered. With a click of a button you can deploy your tags to an environment of your choice.

Last but not least Matomo Tag Manager keeps track of all changes that you make and lets you restore snapshots at any given time.

### Features
* Create an unlimited number of containers
* Create versions and releases of containers
* See what changed in each version
* Export / import of containers
* Preview / debug mode
* Configure multiple environments
* Blacklist certain tags, triggers, or variables
* Easily define your own tags, triggers, or variables
* Schedule tags to run only during a certain time range
* Limit how often a tag should run
* Assign block triggers to avoid a tag from being executed as soon as this trigger was fired
* Optionally delay the execution of a tag
* Assign conditions to a trigger to restrict when a specific trigger should be fired
* Define a default value for variables
* Define a lookup/regexp/match table for variable values with various available comparisons (contains, equals, starts with, ends with, regexp, ...)
* Data-Layer
* Supports [Activity Log](https://plugins.matomo.org/ActivityLog)

### Tags
* Matomo
* CustomHtml
* CustomImage
* Google Analytics
* More will follow

Are you a developer? If you use features regulary which are not available yet, or you have a product you want to integrate into the Tag Manager, please check out our [developer documentation](https://developer.matomo.org/guides/tagmanager/settingup) on how to add your own tags, triggers, and variables. It is really easy. Matomo Tag Manager is open source and we would love it if you contribute tags, triggers and variables to our [project](https://github.com/matomo-org/tag-manager).

### Triggers
* All Elements
* All Links
* All Download links
* Custom Event
* Dom Ready
* Element Visibility
* Form Submit
* Fullscreen
* History Change
* JavaScript Errors
* Page View
* Scroll Depth
* Timer 
* Window Leave
* Window Loaded
* Window Unloaded

### Variables
* Constant Value
* Random Number
* Cookie
* DataLayer
* Dom Element
* JavaScript Variable
* HTML Meta Content
* Referrer URL
* Time Since Load
* Page URL
* Page Title
* Page Hostname
* Page Path
* Page Hash
* Page URL Parameter
* First directory of page url
* Environment
* Container ID
* Container Version
* Container Revision
* Local Hour
* Local Date
* Local Time
* UTC Date
* ISO Date
* Weekday
* Screen size width
* Screen size height
* Screen size width (available)
* Screen size height (available)
* User Agent
* Lots of more variables for elements, clicks, scrolls, error, history, and form triggers

## License

Matomo Tag Manager is released under the GPL v3 (or later) license, see [LICENSE](LICENSE)

## Installation & Requirements

Matomo Tag Manager requires an installed [Matomo Analytics](https://github.com/matomo-org/matomo) see our [installation guide](https://matomo.org/docs/installation/) and [requirements guide](https://matomo.org/docs/requirements/).

Afterwards you can install the Tag Manager plugin in one click by going to "Administration => Marketplace".

## Get involved!

We believe in liberating Web Analytics, providing a free platform for simple and advanced analytics and tag manager. Matomo was built by dozens of people like you,
and we need your help to make Matomo better. Why not participate in a useful project today? [Learn how you can contribute to Matomo.](https://matomo.org/get-involved)

Developer guides are available at [developer.matomo.org/guides/tagmanager/settingup](https://developer.matomo.org/guides/tagmanager/settingup).

## Security

Security is a top priority at Matomo. As potential issues are discovered, we validate, patch and release fixes as quickly as we can. We have a security bug bounty program in place that rewards researchers for finding security issues and disclosing them to us.

[Learn more](https://matomo.org/security/)

## Support for Matomo Tag Manager

For **Free support**, post a message in our community forums: [forum.matomo.org](https://forum.matomo.org/)

For **Professional paid support**, send a message to our network of Matomo professionals: [matomo.org/support](https://matomo.org/contact/)

## Contact

Website: [matomo.org](https://matomo.org)

About us: [matomo.org/team/](https://matomo.org/team/)

Contact us: [matomo.org/contact/](https://matomo.org/contact/)

