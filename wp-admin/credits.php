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

add_action( 'admin_head', 'add_css' );
function add_css() { ?>
<style type="text/css">
h3.wp-people-group { clear: both; }
ul.wp-people-group { margin-bottom: 50px; }
li.wp-person { float: left; height: 100px; width: 240px; margin-right: 20px; }
li.wp-person img.gravatar { float: left; margin-right: 10px; margin-bottom: 10px; width: 60px; height: 60px }
li.wp-person a.web { font-size: 16px; text-decoration: none; }
</style>
<?php }

function wp_credits() {
	global $wp_version;
	$locale = get_locale();

	$people = get_site_transient( 'wordpress_credits' );

	if ( false === $people ) {
		$response = wp_remote_get( "http://api.wordpress.org/core/credits/1.0/?version=$wp_version&locale=$locale" );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return new WP_Error( 'credits_fail', "Oops. We'll need to put a link to WP.org if the HTTP request fails." );
	
		$people = unserialize( wp_remote_retrieve_body( $response ) );
	
		if ( ! $people )
			return new WP_Error( 'credits_fail', "Oops. We'll need to put a link to WP.org if the HTTP request fails." );

		set_site_transient( 'wordpress_credits', $people, 604800 ); // One week.
	}

	return $people;
}

include( './admin-header.php' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e( 'WordPress Credits' ); ?></h2>

<p><?php _e( "WordPress is created by a worldwide team of passionate individuals. We couldn't possibly list them all, but here some of the most influential people currently involved with the project:" ); ?></p>

<?php

$people = wp_credits();
if ( is_wp_error( $people ) ) {
	echo $people->get_error_message();
} else {

	unset( $people['props'] ); // @TODO

	foreach ( $people as $group => $members ) {
		echo '<h3 class="wp-people-group">' . $group . '</h3>';
		echo '<ul class="wp-people-group" id="wp-people-group-' . $group . '">';
		shuffle( $members ); // We were going to sort by ability to pronounce "hierarchical," but that wouldn't be fair to Matt.
		foreach ( $members as $slug => $member ) {
			echo '<li class="wp-person" id="wp-person-' . $slug . '"><img src="http://gravatar.com/avatar/' . $member[3] . '?s=60&r=PG" class="gravatar" /><a class="web" href="' . $member[2] . '">' . $member[0] . '</a><br /><span class="title">' . $member[1] . '</span></li>';
		}
		echo '</ul>';
	}
}

?>
<p class="clear"><?php printf( __( 'Want to see your name in lights on this page? <a href="%s">Get involved in WordPress</a>.' ), 'http://codex.wordpress.org/Contributing_to_WordPress' ); ?></p>

</div>
<?php include( 'admin-footer.php' ); ?>