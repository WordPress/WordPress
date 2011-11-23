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

list( $display_version ) = explode( '-', $wp_version );

include( './admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s!' ), $display_version ); ?></h1>

<div class="about-text"><?php _e( 'WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.' ); ?></div>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper">
	<a href="about.php" class="nav-tab">
		<?php printf( __( 'What&#8217;s New in %s' ), $display_version ); ?>
	</a><a href="credits.php" class="nav-tab nav-tab-active">
		<?php _e( 'Credits' ); ?>
	</a><a href="freedoms.php" class="nav-tab">
		<?php _e( 'Freedoms' ); ?>
	</a>
</h2>

<?php

$credits = wp_credits();

if ( ! $credits ) {
	echo '<p class="about-description">' . sprintf( __( 'WordPress is created by a <a href="%1$s">worldwide team</a> of passionate individuals. <a href="%2$s">Get involved in WordPress</a>.' ),
		'http://wordpress.org/about/',
		/* translators: Url to the codex documentation on contributing to WordPress used on the credits page */
		__( 'http://codex.wordpress.org/Contributing_to_WordPress' ) ) . '</p>';
	include( './admin-footer.php' );
	exit;
}

echo '<p class="about-description">' . __( 'WordPress is created by a worldwide team of passionate individuals.' ) . "</p>\n";

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

		echo '<h4 class="wp-people-group">' . $title . "</h4>\n";
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
					echo '<span class="title">' . translate( $person_data[3] ) . "</span>\n";
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
__( 'Core Developers' );
__( 'Recent Rockstars' );
__( 'Core Contributors to WordPress %s' );
__( 'Cofounder, Project Lead' );
__( 'Lead Developer' );
__( 'User Experience Lead' );
__( 'Core Developer' );
__( 'Core Committer' );
__( 'Guest Committer' );
__( 'Developer' );
__( 'Designer' );
__( 'XML-RPC' );
__( 'Internationalization' );
__( 'External Libraries' );
__( 'Icon Design' );

?>
