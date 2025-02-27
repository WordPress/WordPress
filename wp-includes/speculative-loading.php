<?php
/**
 * Speculative loading functions.
 *
 * @package WordPress
 * @subpackage Speculative Loading
 * @since 6.8.0
 */

/**
 * Returns the speculation rules configuration.
 *
 * @since 6.8.0
 *
 * @return array<string, string>|null Associative array with 'mode' and 'eagerness' keys, or null if speculative
 *                                    loading is disabled.
 */
function wp_get_speculation_rules_configuration(): ?array {
	// By default, speculative loading is only enabled for sites with pretty permalinks when no user is logged in.
	if ( ! is_user_logged_in() && get_option( 'permalink_structure' ) ) {
		$config = array(
			'mode'      => 'auto',
			'eagerness' => 'auto',
		);
	} else {
		$config = null;
	}

	/**
	 * Filters the way that speculation rules are configured.
	 *
	 * The Speculation Rules API is a web API that allows to automatically prefetch or prerender certain URLs on the
	 * page, which can lead to near-instant page load times. This is also referred to as speculative loading.
	 *
	 * There are two aspects to the configuration:
	 * * The "mode" (whether to "prefetch" or "prerender" URLs).
	 * * The "eagerness" (whether to speculatively load URLs in an "eager", "moderate", or "conservative" way).
	 *
	 * By default, the speculation rules configuration is decided by WordPress Core ("auto"). This filter can be used
	 * to force a certain configuration, which could for instance load URLs more or less eagerly.
	 *
	 * For logged-in users or for sites that are not configured to use pretty permalinks, the default value is `null`,
	 * indicating that speculative loading is entirely disabled.
	 *
	 * @since 6.8.0
	 * @see https://developer.chrome.com/docs/web-platform/prerender-pages
	 *
	 * @param array<string, string>|null $config Associative array with 'mode' and 'eagerness' keys, or `null`. The
	 *                                           default value for both of the keys is 'auto'. Other possible values
	 *                                           for 'mode' are 'prefetch' and 'prerender'. Other possible values for
	 *                                           'eagerness' are 'eager', 'moderate', and 'conservative'. The value
	 *                                           `null` is used to disable speculative loading entirely.
	 */
	$config = apply_filters( 'wp_speculation_rules_configuration', $config );

	// Allow the value `null` to indicate that speculative loading is disabled.
	if ( null === $config ) {
		return null;
	}

	// Sanitize the configuration and replace 'auto' with current defaults.
	$default_mode      = 'prefetch';
	$default_eagerness = 'conservative';
	if ( ! is_array( $config ) ) {
		return array(
			'mode'      => $default_mode,
			'eagerness' => $default_eagerness,
		);
	}
	if (
		! isset( $config['mode'] ) ||
		'auto' === $config['mode'] ||
		! WP_Speculation_Rules::is_valid_mode( $config['mode'] )
	) {
		$config['mode'] = $default_mode;
	}
	if (
		! isset( $config['eagerness'] ) ||
		'auto' === $config['eagerness'] ||
		! WP_Speculation_Rules::is_valid_eagerness( $config['eagerness'] ) ||
		// 'immediate' is a valid eagerness, but for safety WordPress does not allow it for document-level rules.
		'immediate' === $config['eagerness']
	) {
		$config['eagerness'] = $default_eagerness;
	}

	return array(
		'mode'      => $config['mode'],
		'eagerness' => $config['eagerness'],
	);
}

/**
 * Returns the full speculation rules data based on the configuration.
 *
 * Plugins with features that rely on frontend URLs to exclude from prefetching or prerendering should use the
 * {@see 'wp_speculation_rules_href_exclude_paths'} filter to ensure those URL patterns are excluded.
 *
 * Additional speculation rules other than the default rule from WordPress Core can be provided by using the
 * {@see 'wp_load_speculation_rules'} action and amending the passed WP_Speculation_Rules object.
 *
 * @since 6.8.0
 * @access private
 *
 * @return WP_Speculation_Rules|null Object representing the speculation rules to use, or null if speculative loading
 *                                   is disabled in the current context.
 */
function wp_get_speculation_rules(): ?WP_Speculation_Rules {
	$configuration = wp_get_speculation_rules_configuration();
	if ( null === $configuration ) {
		return null;
	}

	$mode      = $configuration['mode'];
	$eagerness = $configuration['eagerness'];

	$prefixer = new WP_URL_Pattern_Prefixer();

	$base_href_exclude_paths = array(
		$prefixer->prefix_path_pattern( '/wp-*.php', 'site' ),
		$prefixer->prefix_path_pattern( '/wp-admin/*', 'site' ),
		$prefixer->prefix_path_pattern( '/*', 'uploads' ),
		$prefixer->prefix_path_pattern( '/*', 'content' ),
		$prefixer->prefix_path_pattern( '/*', 'plugins' ),
		$prefixer->prefix_path_pattern( '/*', 'template' ),
		$prefixer->prefix_path_pattern( '/*', 'stylesheet' ),
	);

	/*
	 * If pretty permalinks are enabled, exclude any URLs with query parameters.
	 * Otherwise, exclude specifically the URLs with a `_wpnonce` query parameter or any other query parameter
	 * containing the word `nonce`.
	 */
	if ( get_option( 'permalink_structure' ) ) {
		$base_href_exclude_paths[] = $prefixer->prefix_path_pattern( '/*\\?(.+)', 'home' );
	} else {
		$base_href_exclude_paths[] = $prefixer->prefix_path_pattern( '/*\\?*(^|&)*nonce*=*', 'home' );
	}

	/**
	 * Filters the paths for which speculative loading should be disabled.
	 *
	 * All paths should start in a forward slash, relative to the root document. The `*` can be used as a wildcard.
	 * If the WordPress site is in a subdirectory, the exclude paths will automatically be prefixed as necessary.
	 *
	 * Note that WordPress always excludes certain path patterns such as `/wp-login.php` and `/wp-admin/*`, and those
	 * cannot be modified using the filter.
	 *
	 * @since 6.8.0
	 *
	 * @param string[] $href_exclude_paths Additional path patterns to disable speculative loading for.
	 * @param string   $mode               Mode used to apply speculative loading. Either 'prefetch' or 'prerender'.
	 */
	$href_exclude_paths = (array) apply_filters( 'wp_speculation_rules_href_exclude_paths', array(), $mode );

	// Ensure that:
	// 1. There are no duplicates.
	// 2. The base paths cannot be removed.
	// 3. The array has sequential keys (i.e. array_is_list()).
	$href_exclude_paths = array_values(
		array_unique(
			array_merge(
				$base_href_exclude_paths,
				array_map(
					static function ( string $href_exclude_path ) use ( $prefixer ): string {
						return $prefixer->prefix_path_pattern( $href_exclude_path );
					},
					$href_exclude_paths
				)
			)
		)
	);

	$speculation_rules = new WP_Speculation_Rules();

	$main_rule_conditions = array(
		// Include any URLs within the same site.
		array(
			'href_matches' => $prefixer->prefix_path_pattern( '/*' ),
		),
		// Except for excluded paths.
		array(
			'not' => array(
				'href_matches' => $href_exclude_paths,
			),
		),
		// Also exclude rel=nofollow links, as certain plugins use that on their links that perform an action.
		array(
			'not' => array(
				'selector_matches' => 'a[rel~="nofollow"]',
			),
		),
		// Also exclude links that are explicitly marked to opt out, either directly or via a parent element.
		array(
			'not' => array(
				'selector_matches' => ".no-{$mode}, .no-{$mode} a",
			),
		),
	);

	// If using 'prerender', also exclude links that opt out of 'prefetch' because it's part of 'prerender'.
	if ( 'prerender' === $mode ) {
		$main_rule_conditions[] = array(
			'not' => array(
				'selector_matches' => '.no-prefetch, .no-prefetch a',
			),
		);
	}

	$speculation_rules->add_rule(
		$mode,
		'main',
		array(
			'source'    => 'document',
			'where'     => array(
				'and' => $main_rule_conditions,
			),
			'eagerness' => $eagerness,
		)
	);

	/**
	 * Fires when speculation rules data is loaded, allowing to amend the rules.
	 *
	 * @since 6.8.0
	 *
	 * @param WP_Speculation_Rules $speculation_rules Object representing the speculation rules to use.
	 */
	do_action( 'wp_load_speculation_rules', $speculation_rules );

	return $speculation_rules;
}

/**
 * Prints the speculation rules.
 *
 * For browsers that do not support speculation rules yet, the `script[type="speculationrules"]` tag will be ignored.
 *
 * @since 6.8.0
 * @access private
 */
function wp_print_speculation_rules(): void {
	$speculation_rules = wp_get_speculation_rules();
	if ( null === $speculation_rules ) {
		return;
	}

	wp_print_inline_script_tag(
		(string) wp_json_encode(
			$speculation_rules
		),
		array( 'type' => 'speculationrules' )
	);
}
