<?php
/**
 * Credits administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( 'Credits' );
$parent_file = 'index.php';

add_contextual_help($current_screen,
	'<p>' . __('Each name or handle is a link to that person&#8217;s profile in the WordPress.org community directory.') . '</p>' .
	'<p>' . __('You can register your own profile at <a href="http://wordpress.org/support/register.php" target="_blank">this link</a> to start contributing.') . '</p>' .
	'<p>' . __('WordPress always needs more people to report bugs, patch bugs, test betas, work on UI design, translate strings, write documentation, and add questions/answers/suggestions to the Support Forums. Join in!') . '</p>'
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Contributing_to_WordPress" target="_blank">Documentation on Contributing to WordPress</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

add_action( 'admin_head', '_wp_credits_add_css' );
function _wp_credits_add_css() { ?>
<style type="text/css">
div.wrap { max-width: 750px }
h3.wp-people-group, p.wp-credits-list { clear: both; }
ul.compact { margin-bottom: 0 }

<?php if ( is_rtl() ) { ?>
li.wp-person { float: right; margin-left: 10px; }
li.wp-person img.gravatar { float: right; margin-left: 10px; }
<?php } else { ?>
li.wp-person { float: left; margin-right: 10px; }
li.wp-person img.gravatar { float: left; margin-right: 10px; }
<?php } ?>
li.wp-person img.gravatar { width: 60px; height: 60px; margin-bottom: 10px; }
ul.compact li.wp-person img.gravatar { width: 30px; height: 30px; }
li.wp-person { height: 70px; width: 220px; }
ul.compact li.wp-person { height: 40px; width: auto; white-space: nowrap }
li.wp-person a.web { font-size: 16px; text-decoration: none; }
p.wp-credits-list a { white-space: nowrap; }
</style>
<?php }

function wp_credits() {
	global $wp_version;
	$locale = get_locale();

	$results = get_site_transient( 'wordpress_credits_' . $locale );

	if ( ! is_array( $results ) ) {
		$response = wp_remote_get( "http://api.wordpress.org/core/credits/1.0/?version=$wp_version&locale=$locale" );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return false;

		$results = maybe_unserialize( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $results ) )
			return false;

		set_site_transient( 'wordpress_credits_' . $locale, $results, 86400 ); // One day
	}

	return $results;
}

function _wp_credits_add_profile_link( &$display_name, $username, $profiles ) {
	$display_name = '<a href="' . esc_url( sprintf( $profiles, $username ) ) . '">' . esc_html( $display_name ) . '</a>';
}

function _wp_credits_build_object_link( &$data ) {
	$data = '<a href="' . esc_url( $data[1] ) . '">' . $data[0] . '</a>';
}

include( './admin-header.php' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e( 'WordPress Credits' ); ?></h2>

<?php

$credits = wp_credits();

if ( ! $credits ) {
	echo '<p>' . sprintf( __( 'WordPress is created by a <a href="%1$s">worldwide team</a> of passionate individuals. <a href="%2$s">Get involved in WordPress</a>.' ),
		'http://wordpress.org/about/',
		/* translators: Url to the codex documentation on contributing to WordPress used on the credits page */
		__( 'http://codex.wordpress.org/Contributing_to_WordPress' ) ) . '</p>';
	include( './admin-footer.php' );
	exit;
}

echo '<p>' . __( 'WordPress is created by a worldwide team of passionate individuals. We couldn&#8217;t possibly list them all, but here some of the most influential people currently involved with the project:' ) . "</p>\n";

$gravatar = is_ssl() ? 'https://secure.gravatar.com/avatar/' : 'http://0.gravatar.com/avatar/';

foreach ( $credits['groups'] as $group_slug => $group_data ) {
	if ( $group_data['name'] ) {
		if ( 'Translators' == $group_data['name'] ) {
			// Considered a special slug in the API response. (Also, will never be returned for en_US.)
			$title = _x( 'Translators', 'Translate this to be the equivalent of English Translators in your language for the credits page Translators section' );
		} elseif ( isset( $group_data['placeholders'] ) ) {
			$title = vsprintf( translate( $group_data['name'] ), $group_data['placeholders'] );
		} else {
			$title = translate( $group_data['name'] );
		}

		echo '<h3 class="wp-people-group">' . $title . "</h3>\n";
	}

	if ( ! empty( $group_data['shuffle'] ) )
		shuffle( $group_data['data'] ); // We were going to sort by ability to pronounce "hierarchical," but that wouldn't be fair to Matt.

	switch ( $group_data['type'] ) {
		case 'list' :
			array_walk( $group_data['data'], '_wp_credits_add_profile_link', $credits['data']['profiles'] );
			echo '<p class="wp-credits-list">' . wp_sprintf( '%l.', $group_data['data'] ) . "</p>\n\n";
			break;
		case 'libraries' :
			array_walk( $group_data['data'], '_wp_credits_build_object_link' );
			echo '<p class="wp-credits-list">' . wp_sprintf( '%l.', $group_data['data'] ) . "</p>\n\n";
			break;
		default:
			$compact = 'compact' == $group_data['type'];
			$classes = 'wp-people-group ' . ( $compact ? 'compact' : '' );
			echo '<ul class="' . $classes . '" id="wp-people-group-' . $group_slug . '">' . "\n";
			foreach ( $group_data['data'] as $person_data ) {
				echo '<li class="wp-person" id="wp-person-' . $person_data[2] . '">' . "\n\t";
				echo '<a href="' . sprintf( $credits['data']['profiles'], $person_data[2] ) . '">';
				$size = 'compact' == $group_data['type'] ? '30' : '60';
				echo '<img src="' . $gravatar . $person_data[1] . '?s=' . $size . '" class="gravatar" alt="' . esc_attr( $person_data[0] ) . '" /></a>' . "\n\t";
				echo '<a class="web" href="' . sprintf( $credits['data']['profiles'], $person_data[2] ) . '">' . $person_data[0] . "</a>\n\t";
				if ( ! $compact )
					echo '<br /><span class="title">' . translate( $person_data[3] ) . "</span>\n";
				echo "</li>\n";
			}
			echo "</ul>\n";
		break;
	}
}

?>
<p class="clear"><?php printf( __( 'Want to see your name in lights on this page? <a href="%s">Get involved in WordPress</a>.' ),
	/* translators: Url to the codex documentation on contributing to WordPress used on the credits page */
	__( 'http://codex.wordpress.org/Contributing_to_WordPress' ) ); ?></p>

</div>
<?php

include( './admin-footer.php' );

return;

// These are strings returned by the API that we want to be translatable
__( 'Project Leaders' );
__( 'Extended Core Team' );
__( 'Recent Rockstars' );
__( 'Core Contributors to WordPress %s' );
__( 'Cofounder, Project Lead' );
__( 'Lead Developer' );
__( 'User Experience Lead' );
__( 'Core Committer' );
__( 'Guest Committer' );
__( 'Developer' );
__( 'Designer' );
__( 'XML-RPC' );
__( 'Internationalization' );
__( 'External Libraries' );
__( 'Icon Design' );
__( 'Blue Color Scheme' );

?>
