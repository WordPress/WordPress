Thanks for reading our contribution guidelines! What do you want to do:

* [File a bug / an issue](#filing-issue)
* [Security issues](#security)
* [Contribute to Yoast SEO](#contribute)

<a name="filing-issue"></a>
#Filing issues

__Please Note:__ GitHub is for bug reports and code contributions only - if you have a support question or a request for a customisation don't post here, go to our [Support Forum](http://wordpress.org/support/plugin/wordpress-seo) instead.

For localization, please refer to [translate.yoast.com](http://translate.yoast.com/projects/wordpress-seo), though bugs with strings that can't be translated are welcome here.

## How to write a useful bug report
If you think you have found a bug (we acknowledge that that's a possibility), please make sure you're using the latest version of the plugin. If possible check out the latest version from GitHub and see if the bug still exists there.

A useful bug report explains:

1. What you were trying to achieve.
2. What you were expecting to happen.
3. What actually happened, illustrated with screenshots if possible.

Your bug report should also contain your WordPress version and if there are any errors, the _exact_ error text, including line numbers.

### Blank / white screen
If you're getting a blank screen and you report just that, we can do _absolutely_ nothing. By default, your WordPress install suppresses all errors, to prevent information leaks, but we need those errors to be able to help you. If you apply the small piece of code in [this post on WP_DEBUG](https://yoast.com/wordpress-debug/) to your site, you should be able to open the URL that gave you a white screen, append `?debug=debug` to the URL and get the actual error.

That error will help us, without that error, we're completely in the dark about your white page problem...

### Interface errors
If you're reporting a bug about specific interface elements not working as expected, there's probably an error showing in your browsers JavaScript console. Please open your browsers console and copy the exact error showing there, or make a screenshot. If you don't know how to open your browsers console, here is info for [Chrome](https://developer.chrome.com/devtools/docs/console) and [Firefox](https://developer.mozilla.org/en/docs/Tools/Web_Console). For IE, some Googling will help but it changes with every version.

<a name="security"></a>
#Security issues
Please do _not_ report security issues here, but email them to security at yoast dot com so we can deal with them securely and quickly.

<a name="contribute"></a>
#Contribute to Yoast SEO

Community made patches, localisations, bug reports and contributions are very welcome and help make Yoast SEO remains the #1 SEO plugin for WordPress.

When contributing please ensure you follow the guidelines below so that we can keep on top of things.

## Getting Started

If there is no ticket for your issue, submit it first, following the above guidelines.

## Making Changes

* Fork the repository on GitHub (make sure to use the trunk branch, not master).
* Make the changes to your forked repository.
  * Ensure you stick to the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/) and have properly documented any new functions, actions and filters following the [documentation standards](http://make.wordpress.org/core/handbook/inline-documentation-standards/php-documentation-standards/).
* When committing, reference your issue (if present) and include a note about the fix.
* Push the changes to your fork and submit a pull request to the 'trunk' branch of the Yoast SEO repository.

At this point you're waiting on us to merge your pull request. We'll review all pull requests, and make suggestions and changes if necessary.

<a name="patch-welcome"></a>
## "Patch welcome" issues

Some issues are labeled "Patch-welcome". This means we see the value in the particular enhancement being suggested but have decided for now not to prioritize it. If you however decide to write a patch for it we'll gladly include it after some code review.

# Additional Resources
* [Yoast SEO API](https://yoast.com/wordpress/plugins/seo/api/)
* [General GitHub Documentation](http://help.github.com/)
* [GitHub Pull Request documentation](http://help.github.com/send-pull-requests/)
