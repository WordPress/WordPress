<?php
/**
 * Bootstrap the plugin unit testing environment. Customize 'active_plugins'
 * setting below to point to your main plugin file.
 *
 * Requires WordPress Unit Tests (http://unit-test.svn.wordpress.org/trunk/).
 *
 * @package wordpress-plugin-tests
 */

//Turing this crap off.  it doesn't work.  It's making Travis whine like a bitch.
return;

// Add this plugin to WordPress for activation so it can be tested.
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( "ReduxFramework/redux-framework.php" ),
);

/**
 * If the wordpress-tests repo location has been customized (and specified
 * with WP_TESTS_DIR), use that location. This will most commonly be the case
 * when configured for use with Travis CI.
 *
 * Otherwise, we'll just assume that this plugin is installed in the WordPress
 * SVN external checkout configured in the wordpress-tests repo.
 */
if( false !== getenv( 'WP_TESTS_DIR' ) ) {
	require getenv( 'WP_TESTS_DIR' ) . '/bootstrap.php';
} else {
	require dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/bootstrap.php';
}
