<?php
if( isset( $current_site ) && isset( $current_blog ) )
	return;

// depreciated
$wpmuBaseTablePrefix = $table_prefix;

$domain = addslashes( $_SERVER['HTTP_HOST'] );
if( substr( $domain, 0, 4 ) == 'www.' )
	$domain = substr( $domain, 4 );
if( strpos( $domain, ':' ) ) {
	if( substr( $domain, -3 ) == ':80' ) {
		$domain = substr( $domain, 0, -3 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -3 );
	} elseif( substr( $domain, -4 ) == ':443' ) {
		$domain = substr( $domain, 0, -4 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -4 );
	} else {
		die( 'WPMU only works without the port number in the URL.' );
	}
}
$domain = preg_replace('/:.*$/', '', $domain); // Strip ports
if( substr( $domain, -1 ) == '.' )
	$domain = substr( $domain, 0, -1 );

$path = preg_replace( '|([a-z0-9-]+.php.*)|', '', $_SERVER['REQUEST_URI'] );
$path = str_replace ( '/wp-admin/', '/', $path );
$path = preg_replace( '|(/[a-z0-9-]+?/).*|', '$1', $path );

function get_current_site_name( $current_site ) {
	global $wpdb;
	$current_site->site_name = wp_cache_get( $current_site->id . ':current_site_name', "site-options" );
	if ( !$current_site->site_name ) {
		$current_site->site_name = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = %d AND meta_key = 'site_name'", $current_site->id ) );
		if( $current_site->site_name == null )
			$current_site->site_name = ucfirst( $current_site->domain );
		wp_cache_set( $current_site->id . ':current_site_name', $current_site->site_name, 'site-options');
	}
	return $current_site;
}

function wpmu_current_site() {
	global $wpdb, $current_site, $domain, $path, $sites;
	if( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
		$current_site->id = (defined( 'SITE_ID_CURRENT_SITE' ) ? constant('SITE_ID_CURRENT_SITE') : 1);
		$current_site->domain = DOMAIN_CURRENT_SITE;
		$current_site->path   = $path = PATH_CURRENT_SITE;
		if( defined( 'BLOGID_CURRENT_SITE' ) )
			$current_site->blog_id = BLOGID_CURRENT_SITE;
		return $current_site;
	}

	$current_site = wp_cache_get( "current_site", "site-options" );
	if( $current_site )
		return $current_site;
		
	$wpdb->suppress_errors();
	$sites = $wpdb->get_results( "SELECT * FROM $wpdb->site" ); // usually only one site
	if( count( $sites ) == 1 ) {
		$current_site = $sites[0];
		$path = $current_site->path;
		$current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
		$current_site = get_current_site_name( $current_site );
		wp_cache_set( "current_site", $current_site, "site-options" );
		return $current_site;
	}
	$path = substr( $_SERVER[ 'REQUEST_URI' ], 0, 1 + strpos( $_SERVER[ 'REQUEST_URI' ], '/', 1 ) );
	if( constant( 'VHOST' ) == 'yes' ) {
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $domain, $path) );
		if( $current_site != null )
			return $current_site;
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $domain) );
		if( $current_site != null ) {
			$path = '/';
			return $current_site;
		}

		$sitedomain = substr( $domain, 1 + strpos( $domain, '.' ) );
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path) );
		if( $current_site != null )
			return $current_site;
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $sitedomain) );
		if( $current_site == null && defined( "WP_INSTALLING" ) == false ) {
			if( count( $sites ) == 1 ) {
				$current_site = $sites[0];
				die( "That blog does not exist. Please try <a href='http://{$current_site->domain}{$current_site->path}'>http://{$current_site->domain}{$current_site->path}</a>" );
			} else {
				die( "No WPMU site defined on this host. If you are the owner of this site, please check <a href='http://codex.wordpress.org/Debugging_WPMU'>Debugging WPMU</a> for further assistance." );
			}
		} else {
			$path = '/';
		}
	} else {
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $domain, $path) );
		if( $current_site != null )
			return $current_site;
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $domain) );
		if( $current_site == null && defined( "WP_INSTALLING" ) == false ) {
			if( count( $sites ) == 1 ) {
				$current_site = $sites[0];
				die( "That blog does not exist. Please try <a href='http://{$current_site->domain}{$current_site->path}'>http://{$current_site->domain}{$current_site->path}</a>" );
			} else {
				die( "No WPMU site defined on this host. If you are the owner of this site, please check <a href='http://codex.wordpress.org/Debugging_WPMU'>Debugging WPMU</a> for further assistance." );
			}
		} else {
			$path = '/';
		}
	}
	return $current_site;
}

$current_site = wpmu_current_site();
if( !isset( $current_site->blog_id ) )
	$current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );

if( constant( 'VHOST' ) == 'yes' ) {
	$current_blog = wp_cache_get( 'current_blog_' . $domain, 'site-options' );
	if( !$current_blog ) {
		$current_blog = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE domain = %s", $domain) );
		if( $current_blog )
			wp_cache_set( 'current_blog_' . $domain, $current_blog, 'site-options' );
	}
	if( $current_blog != null && $current_blog->site_id != $current_site->id ) {
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE id = %d", $current_blog->site_id) );
	} else {
		$blogname = substr( $domain, 0, strpos( $domain, '.' ) );
	}
} else {
	$blogname = htmlspecialchars( substr( $_SERVER[ 'REQUEST_URI' ], strlen( $path ) ) );
	if( strpos( $blogname, '/' ) )
		$blogname = substr( $blogname, 0, strpos( $blogname, '/' ) );
	if( strpos( " ".$blogname, '?' ) )
		$blogname = substr( $blogname, 0, strpos( $blogname, '?' ) );
	$reserved_blognames = array( 'page', 'comments', 'blog', 'wp-admin', 'wp-includes', 'wp-content', 'files', 'feed' );
	if ( $blogname != '' && !in_array( $blogname, $reserved_blognames ) && !is_file( $blogname ) ) {
		$path = $path . $blogname . '/';
	}
	$current_blog = wp_cache_get( 'current_blog_' . $domain . $path, 'site-options' );
	if( !$current_blog ) {
		$current_blog = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE domain = %s AND path = %s", $domain, $path) );
		if( $current_blog )
			wp_cache_set( 'current_blog_' . $domain . $path, $current_blog, 'site-options' );
	}
}

if( defined( "WP_INSTALLING" ) == false && constant( 'VHOST' ) == 'yes' && !is_object( $current_blog ) ) {
	if( defined( 'NOBLOGREDIRECT' ) ) {
		$destination = constant( 'NOBLOGREDIRECT' );
		if ( $destination == '%siteurl%' )
			$destination = "http://" . $current_site->domain . $current_site->path;
		header( "Location: " .  $destination);
		die();
	} else {
		header( "Location: http://" . $current_site->domain . $current_site->path . "wp-signup.php?new=" . str_replace( '.' . $current_site->domain, '', $domain ) );
		die();
	}

}

if( defined( "WP_INSTALLING" ) == false ) {
	if( $current_site && $current_blog == null ) {
		if( $current_site->domain != $_SERVER[ 'HTTP_HOST' ] ) {
			header( "Location: http://" . $current_site->domain . $current_site->path );
			exit;
		}
		$current_blog = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE domain = %s AND path = %s", $current_site->domain, $current_site->path) );
	}
	if( $current_blog == false || $current_site == false )
		is_installed();
}

$blog_id = $current_blog->blog_id;
$public  = $current_blog->public;

if( $current_blog->site_id == 0 || $current_blog->site_id == '' )
	$current_blog->site_id = 1;
$site_id = $current_blog->site_id;

$current_site = get_current_site_name( $current_site );

if( $blog_id == false ) {
    // no blog found, are we installing? Check if the table exists.
    if ( defined('WP_INSTALLING') ) {
	$blog_id = $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs LIMIT 0,1" );
	if( $blog_id == false ) {
	    // table doesn't exist. This is the first blog
	    $blog_id = 1;
	} else {
	    // table exists
	    // don't create record at this stage. we're obviously installing so it doesn't matter what the table vars below are like.
	    // default to using the "main" blog.
	    $blog_id = 1;
	}
	$current_blog->blog_id = $blog_id;
    } else {
	$check = $wpdb->get_results( "SELECT * FROM $wpdb->site" );
	if( $check == false ) {
	    $msg = ': DB Tables Missing';
	} else {
	    $msg = '';
	}
	die( "No Blog by that name on this system." . $msg );
    }
}

$wpdb->suppress_errors( false );

if( '0' == $current_blog->public ) {
	// This just means the blog shouldn't show up in google, etc. Only to registered members
}

function is_installed() {
	global $wpdb, $domain, $path;
	$base = stripslashes( $base );
	if( defined( "WP_INSTALLING" ) == false ) {
		$check = $wpdb->get_results( "SELECT * FROM $wpdb->site" );
		$msg = "If your blog does not display, please contact the owner of this site.<br /><br />If you are the owner of this site please check that MySQL is running properly and all tables are error free.<br /><br />";
		if( $check == false ) {
			$msg .= "<strong>Database Tables Missing.</strong><br />Database tables are missing. This means that MySQL is either not running, WPMU was not installed properly, or someone deleted {$wpdb->site}. You really <em>should</em> look at your database now.<br />";
		} else {
			$msg .= '<strong>Could Not Find Blog!</strong><br />';
			$msg .= "Searched for <em>" . $domain . $path . "</em> in " . DB_NAME . "::" . $wpdb->blogs . " table. Is that right?<br />";
		}
		$msg .= "<br />\n<h1>What do I do now?</h1>";
		$msg .= "Read the <a target='_blank' href='http://codex.wordpress.org/Debugging_WPMU'>bug report</a> page. Some of the guidelines there may help you figure out what went wrong.<br />";
		$msg .= "If you're still stuck with this message, then check that your database contains the following tables:<ul>
			<li> $wpdb->blogs </li>
			<li> $wpdb->users </li>
			<li> $wpdb->usermeta </li>
			<li> $wpdb->site </li>
			<li> $wpdb->sitemeta </li>
			<li> $wpdb->sitecategories </li>
			</ul>";
		$msg .= "If you suspect a problem please report it to the support forums but you must include the information asked for in the <a href='http://codex.wordpress.org/Debugging_WPMU'>WPMU bug reporting guidelines</a>!<br /><br />";
		if( is_file( 'release-info.txt' ) ) {
			$msg .= 'Your bug report must include the following text: "';
			$info = file( 'release-info.txt' );
			$msg .= $info[ 4 ] . '"';
		}

		die( "<h1>Fatal Error</h1> " . $msg );
	}
}

?>
