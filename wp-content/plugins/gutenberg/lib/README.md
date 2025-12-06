# Gutenberg PHP

This documentation is intended for developers who are contributing to the PHP code in the Gutenberg plugin, and pertains to files in the `lib` directory.

The Gutenberg plugin is continuously enhancing existing features and creating new ones. Some features, once considered stable and useful, are merged into Core (the WordPress source code) during a WordPress release. Others remain in the plugin forever or are eventually removed as the minimum supported WordPress version changes.

During a WordPress release, new features, bugfixes and other changes are "synced" between the Gutenberg plugin and WordPress Core. Consistent naming and directory structures make this process easier by preventing naming conflicts and compartmentalizing release-specific code.

The following documentation is intended to act as a guide only. If you're unsure about naming or where to place new PHP files, please don't hesitate to ping other contributors on GitHub or ask in the #core-editor channel on [WordPress Slack](https://make.wordpress.org/chat/).

## File structure

To make it easier for contributors to identify features that should be merged into Core and those that can be deleted, Gutenberg uses the following file structure for its PHP code:

- `lib/experimental` - Experimental features that exist only in the plugin. They should not be merged into Core.
- `lib/compat/wordpress-X.Y` - Stable features that are intended to be merged into Core in a future `X.Y` release, or that were previously merged to Core in the `X.Y` release and remain in the plugin for backwards compatibility when running the plugin on older versions of WordPress.
- `lib/compat/plugin` - Features for backwards compatibility for the plugin consumers. These files don't need to be merged into Core and should have a timeline for when they should be removed from the plugin.

Files at the root of `/lib` are generally considered to contain "evergreen" code. Such code is both fundamental to the proper functioning of the plugin, and also so often updated that versioning it between WordPress releases is not practical. Changes to these files are merged into Core as required.

## Best practices

There are a few best practices that should be observed when adding new files to the Gutenberg plugin.

### Using `gutenberg` suffixes/prefixes vs. `wp` prefixes

To avoid naming conflicts with WordPress Core and other plugins, the Gutenberg plugin uses the `gutenberg` identifier in many of its PHP classes and function names, e.g., `WP_Theme_JSON_Gutenberg` and `gutenberg_get_block_editor_settings`.

This is especially so for classes and functions whose functionality is ubiquitous and constantly being updated — so-called "evergreen" code. Anything related to `WP_Theme_JSON_Gutenberg` is a good example of this: this class controls the way Gutenberg processes and outputs global styles and much more, and its methods are called in many places. In every aspect of plugin functionality, we want plugin users to have access to the latest versions of these files, even if they are running an older version of WordPress.

```php
/**
* Returns something useful.
*
* @since 6.2.0 Updates to something even more useful.
* @since 6.3.0 Now more useful than ever.
*
* @return string Something useful.
*/
function gutenberg_get_something_useful() {
	// ...
}
```

When porting new functions into Core, the function must be renamed to use the `wp_` prefix for functions or a `WP_` prefix for classes.

```php
/**
* Returns something useful.
*
* @since 6.2.0 Updates to something even more useful.
* @since 6.3.0 Now more useful than ever.
*
* @return string Something useful.
*/
function wp_get_something_useful() {
	// ...
}
```

Plugin code that is stable and expected to be merged "as-is" into Core in the near future can use the `wp_` prefix for functions or a `WP_` prefix for classes.

#### Avoiding duplicate declarations

When doing so, care must be taken to ensure that no duplicate declarations to create functions or classes exist between Gutenberg and WordPress core code. A quick codebase search will also help you know if your new names are unique.

Wrapping such code in `class_exists()` and `function_exists()` checks should be used to ensure it executes in the plugin up until it is merged to Core, or when running the plugin on older versions of WordPress.

```php
if ( ! function_exists( 'wp_a_new_and_stable_feature' ) ) {
	/**
	* A very new and stable feature.
	*
	* @return string Something useful.
	*/
	function wp_a_new_and_stable_feature() {
		// ...
	}
}
```

Or for classes:

```php
/**
 * WP_A_Stable_Class class
 *
 * @package WordPress
 * @since 6.3.0
 */
if ( ! class_exists( 'WP_A_Stable_Class' ) ) {
	// Do not invert this pattern with an early `return`.
	// See below for details...
	class WP_A_Stable_Class { ... }
}
```

Wrapping code in `class_exists()` and `function_exists()` is usually inappropriate for evergreen code, or any plugin code that we expect to undergo constant change between WordPress releases, because it would prevent the latest versions of the code from being used. For example, the statement `class_exists( 'WP_Theme_JSON' )` would return `true` because the class already exists in Core.

The `return` operator is considered an anti-pattern in the context provided below because it [does not halt](https://www.php.net/manual/en/function.return.php#112515) the parsing of the PHP script and can cause [unexpected side effects](https://github.com/WordPress/gutenberg/pull/58429#issuecomment-1916670097):
```php
/**
 * ANTI-PATTERN
 * DO NOT COPY!
 *
 */
if ( class_exists( 'WP_A_Stable_Class' ) ) {
	return; // do not do this.
}
```

When to use which prefix is a judgement call, but the general rule is that if you're unsure, use the `gutenberg` prefix because it will less likely give rise to naming conflicts.

#### When not to use plugin-specific prefixes/suffixes

The above recommendations in relation to plugin-specific prefixes/suffixes are relevant only to files in the `lib` directory and only in the Gutenberg plugin.

`Gutenberg` prefixes/suffixes _should not_ be used in Core PHP code. When syncing `/lib` files to Core, plugin-specific prefixes/suffixes are generally replaced with their `WP_` or `wp_` equivalents manually.

Accordingly, unless required to run plugin-only code, you should avoid using plugin-specific prefixes/suffixes in any block PHP code. Core blocks in the plugin are [published as NPM packages](https://github.com/WordPress/gutenberg/blob/trunk/docs/contributors/code/release.md#packages-releases-to-npm-and-wordpress-core-updates), which Core consumes as NPM dependencies.

See [block naming conventions](https://github.com/WordPress/gutenberg/tree/trunk/packages/block-library#naming-convention-for-php-functions) for more information on block naming conventions.

As always, get in touch with your fellow contributors if you're unsure.

### Documentation and annotations

For every class, method and function in the plugin, refer to the [WordPress PHP documentation standards](https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/) when documenting your code.

It's particularly important to observe annotation standards, and `@since` descriptions that specify the target WordPress version, so that all contributors can easily identify what needs to be (or what already has been) merged to Core and when.

Developers should also write a brief note about _how_ their feature should be merged to Core, for example, which Core file or function should be patched.

Notes can be included in the doc comment.

This helps future developers know what to do when merging Gutenberg features into Core.

```php
/**
 * Returns a navigation object for the given slug.
 *
 * Should live in `wp-includes/navigation.php` when merged to Core.
 *
 * @since 6.3.0
 *
 * @param string $slug
 * @return WP_Navigation
 */
function wp_get_navigation( $slug ) { ... }
```

### Group PHP code by _feature_

Developers should organize PHP into files or folders by _feature_, not by _component_.

When defining a function that will be hooked, developers should call `add_action` and `add_filter` immediately after the function declaration.

These two practices make it easier for PHP code to start in one folder (e.g., `lib/experimental`) and eventually move to another using a simple `git mv`.

#### Good

```php
// lib/experimental/navigation.php

function wp_get_navigation( $slug ) { ... }

function wp_register_navigation_cpt() { ... }

add_action( 'init', 'wp_register_navigation_cpt' );
```

#### Not so good

```php
// lib/experimental/functions.php

function wp_get_navigation( $slug ) { ... }

// lib/experimental/post-types.php

function wp_register_navigation_cpt() { ... }

// lib/experimental/init.php
add_action( 'init', 'wp_register_navigation_cpt' );
```

### Requiring files in lib/load.php

Should the load order allow it, try to group imports according to WordPress release, then feature. It'll help everyone to quickly recognise the files that belong to specific WordPress releases.

Existing comments in `lib/load.php` should act as a guide.

## When to sync changes to Gutenberg PHP with Core and vice versa

On open Gutenberg PRs, changes to certain files are flagged as requiring syncing (also called "backporting") to WordPress Core, for example, PHP files in `/lib` and PHP unit tests.

The CI checks will indicate whether you need to create a Core PR. If you do, you'll need to create a corresponding markdown file and place it within the appropriate release subdirectory in the [Core backport changelog](https://github.com/WordPress/gutenberg/tree/trunk/backport-changelog/).

For more information, please refer to the [Core backport changelog documentation](https://github.com/WordPress/gutenberg/tree/trunk/backport-changelog/readme.md).

So too, if you've made changes in WordPress Core to code that also lives in the Gutenberg plugin, these changes will need to be synced to Gutenberg. The relevant Gutenberg GitHub pull request should be labeled with the `Backport from WordPress Core` label.

If you're unsure, you can always ask for help in the #core-editor channel in [WordPress Slack](https://make.wordpress.org/chat/).
