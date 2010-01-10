<?php

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/wp-load.php' );

add_action( 'wp_head', 'signuppageheaders' ) ;

require( 'wp-blog-header.php' );
require_once( ABSPATH . WPINC . '/registration.php' );

if( is_array( get_site_option( 'illegal_names' )) && $_GET[ 'new' ] != '' && in_array( $_GET[ 'new' ], get_site_option( 'illegal_names' ) ) == true ) {
	wp_redirect( "http://{$current_site->domain}{$current_site->path}" );
	die();
}

function do_signup_header() {
	do_action("signup_header");
}
add_action( 'wp_head', 'do_signup_header' );

function signuppageheaders() {
	echo "<meta name='robots' content='noindex,nofollow' />\n";
}

if( $current_blog->domain . $current_blog->path != $current_site->domain . $current_site->path ) {
	wp_redirect( "http://" . $current_site->domain . $current_site->path . "wp-signup.php" );
	die();
}

function wpmu_signup_stylesheet() {
	?>
	<style type="text/css">	
		.mu_register { width: 90%; margin:0 auto; }
		.mu_register form { margin-top: 2em; }
		.mu_register .error { font-weight:700; padding:10px; color:#333333; background:#FFEBE8; border:1px solid #CC0000; }
		.mu_register input[type="submit"],
			.mu_register #blog_title,
			.mu_register #user_email, 
			.mu_register #blogname,
			.mu_register #user_name { width:100%; font-size: 24px; margin:5px 0; }	
		.mu_register .prefix_address,
			.mu_register .suffix_address {font-size: 18px;display:inline; }			
		.mu_register label { font-weight:700; font-size:15px; display:block; margin:10px 0; }
		.mu_register label.checkbox { display:inline; }
		.mu_register .mu_alert { font-weight:700; padding:10px; color:#333333; background:#ffffe0; border:1px solid #e6db55; }
	</style>
	<?php
}

add_action( 'wp_head', 'wpmu_signup_stylesheet' );
get_header();
?>
<div id="content" class="widecolumn">
<div class="mu_register">
<?php
function show_blog_form($blogname = '', $blog_title = '', $errors = '') {
	global $current_site;
	// Blog name
	if( !is_subdomain_install() )
		echo '<label for="blogname">' . __('Blog Name:') . '</label>';
	else
		echo '<label for="blogname">' . __('Blog Domain:') . '</label>';

	if ( $errmsg = $errors->get_error_message('blogname') ) { ?>
		<p class="error"><?php echo $errmsg ?></p>
	<?php }

	if( !is_subdomain_install() ) {
		echo '<span class="prefix_address">' . $current_site->domain . $current_site->path . '</span><input name="blogname" type="text" id="blogname" value="'. esc_attr($blogname) .'" maxlength="50" /><br />';
	} else {
		echo '<input name="blogname" type="text" id="blogname" value="'.esc_attr($blogname).'" maxlength="50" /><span class="suffix_address">.' . $current_site->domain . $current_site->path . '</span><br />';
	}
	if ( !is_user_logged_in() ) {
		print '(<strong>' . __( 'Your address will be ' );
		if( !is_subdomain_install() ) {
			print $current_site->domain . $current_site->path . __( 'blogname' );
		} else {
			print __( 'domain.' ) . $current_site->domain . $current_site->path;
		}
		echo '.</strong> ' . __( 'Must be at least 4 characters, letters and numbers only. It cannot be changed so choose carefully!)' ) . '</p>';
	}

	// Blog Title
	?>
	<label for="blog_title"><?php _e('Blog Title:') ?></label>	
	<?php if ( $errmsg = $errors->get_error_message('blog_title') ) { ?>
		<p class="error"><?php echo $errmsg ?></p>
	<?php }
	echo '<input name="blog_title" type="text" id="blog_title" value="'.esc_attr($blog_title).'" /></p>';
	?>

	<div id="privacy">
        <p class="privacy-intro">
            <label for="blog_public_on"><?php _e('Privacy:') ?></label>
            <?php _e('I would like my blog to appear in search engines like Google and Technorati, and in public listings around this site.'); ?> 
            <div style="clear:both;"></div>
            <label class="checkbox" for="blog_public_on">
                <input type="radio" id="blog_public_on" name="blog_public" value="1" <?php if( !isset( $_POST['blog_public'] ) || $_POST['blog_public'] == '1' ) { ?>checked="checked"<?php } ?> />
                <strong><?php _e( 'Yes' ); ?></strong>
            </label>
            <label class="checkbox" for="blog_public_off">
                <input type="radio" id="blog_public_off" name="blog_public" value="0" <?php if( isset( $_POST['blog_public'] ) && $_POST['blog_public'] == '0' ) { ?>checked="checked"<?php } ?> />
                <strong><?php _e( 'No' ); ?></strong>
            </label>
        </p>
	</div>
	
	<?php
	do_action('signup_blogform', $errors);
}

function validate_blog_form() {
	$user = '';
	if ( is_user_logged_in() )
		$user = wp_get_current_user();

	return wpmu_validate_blog_signup($_POST['blogname'], $_POST['blog_title'], $user);
}

function show_user_form($user_name = '', $user_email = '', $errors = '') {
	// User name
	echo '<label for="user_name">' . __('Username:') . '</label>';
	if ( $errmsg = $errors->get_error_message('user_name') ) {
		echo '<p class="error">'.$errmsg.'</p>';
	}
	echo '<input name="user_name" type="text" id="user_name" value="'. esc_attr($user_name) .'" maxlength="50" /><br />';
	_e('(Must be at least 4 characters, letters and numbers only.)');
	?>

	<label for="user_email"><?php _e('Email&nbsp;Address:') ?></label>
	<?php if ( $errmsg = $errors->get_error_message('user_email') ) { ?>
		<p class="error"><?php echo $errmsg ?></p>
	<?php } ?>		
	<input name="user_email" type="text" id="user_email" value="<?php  echo esc_attr($user_email) ?>" maxlength="200" /><br /><?php _e('(We&#8217;ll send your password to this address, so <strong>triple-check it</strong>.)') ?>
	<?php
	if ( $errmsg = $errors->get_error_message('generic') ) {
		echo '<p class="error">'.$errmsg.'</p>';
	}
	do_action( 'signup_extra_fields', $errors );
}

function validate_user_form() {
	return wpmu_validate_user_signup($_POST['user_name'], $_POST['user_email']);
}

function signup_another_blog($blogname = '', $blog_title = '', $errors = '') {
	global $current_user, $current_site;
	
	if ( ! is_wp_error($errors) ) {
		$errors = new WP_Error();
	}

	// allow definition of default variables
	$filtered_results = apply_filters('signup_another_blog_init', array('blogname' => $blogname, 'blog_title' => $blog_title, 'errors' => $errors ));
	$blogname = $filtered_results['blogname'];
	$blog_title = $filtered_results['blog_title'];
	$errors = $filtered_results['errors'];

	echo '<h2>' . sprintf( __('Get <em>another</em> %s blog in seconds'), $current_site->site_name ) . '</h2>';

	if ( $errors->get_error_code() ) {
		echo "<p>" . __('There was a problem, please correct the form below and try again.') . "</p>";
	}
	?>
	<p><?php printf(__("Welcome back, %s. By filling out the form below, you can <strong>add another blog to your account</strong>. There is no limit to the number of blogs you can have, so create to your heart's content, but blog responsibly."), $current_user->display_name) ?></p>
	
	<?php
	$blogs = get_blogs_of_user($current_user->ID);	
	if ( !empty($blogs) ) { ?>
		<p>
			<?php _e('Blogs you are already a member of:') ?>
			<ul>
				<?php foreach ( $blogs as $blog ) {
					echo "<li><a href='http://" . $blog->domain . $blog->path . "'>" . $blog->domain . $blog->path . "</a></li>";
				} ?>
			</ul>
		</p>
	<?php } ?>
	
	<p><?php _e("If you&#8217;re not going to use a great blog domain, leave it for a new user. Now have at it!") ?></p>
	<form id="setupform" method="post" action="wp-signup.php">
		<input type="hidden" name="stage" value="gimmeanotherblog" />
		<?php do_action( "signup_hidden_fields" ); ?>
		<?php show_blog_form($blogname, $blog_title, $errors); ?>
		<p class="submit"><input type="submit" name="submit" class="submit" value="<?php esc_attr_e('Create Blog') ?>" /></p>
	</form>
	<?php
}

function validate_another_blog_signup() {
	global $wpdb, $current_user, $blogname, $blog_title, $errors, $domain, $path;
	$current_user = wp_get_current_user();
	if( !is_user_logged_in() )
		die();

	$result = validate_blog_form();
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_another_blog($blogname, $blog_title, $errors);
		return false;
	}

	$public = (int) $_POST['blog_public'];
	$meta = apply_filters('signup_create_blog_meta', array ('lang_id' => 1, 'public' => $public)); // deprecated
	$meta = apply_filters( "add_signup_meta", $meta );

	wpmu_create_blog( $domain, $path, $blog_title, $current_user->id, $meta, $wpdb->siteid );
	confirm_another_blog_signup($domain, $path, $blog_title, $current_user->user_login, $current_user->user_email, $meta);
	return true;
}

function confirm_another_blog_signup($domain, $path, $blog_title, $user_name, $user_email = '', $meta = '') {
	?>
	<h2><?php printf(__('The blog %s is yours.'), "<a href='http://{$domain}{$path}'>{$blog_title}</a>" ) ?></h2>
	<p>
		<?php printf(__('<a href="http://%1$s">http://%2$s</a> is your new blog.  <a href="%3$s">Login</a> as "%4$s" using your existing password.'), $domain.$path, $domain.$path, "http://" . $domain.$path . "wp-login.php", $user_name) ?>
	</p>
	<?php
	do_action('signup_finished');
}

function signup_user($user_name = '', $user_email = '', $errors = '') {
	global $current_site, $active_signup;

	if ( !is_wp_error($errors) )
		$errors = new WP_Error();
	if( isset( $_POST[ 'signup_for' ] ) ) {
		$signup[ wp_specialchars( $_POST[ 'signup_for' ] ) ] = 'checked="checked"';
	} else {
		$signup[ 'blog' ] = 'checked="checked"';
	}

	// allow definition of default variables
	$filtered_results = apply_filters('signup_user_init', array('user_name' => $user_name, 'user_email' => $user_email, 'errors' => $errors ));
	$user_name = $filtered_results['user_name'];
	$user_email = $filtered_results['user_email'];
	$errors = $filtered_results['errors'];

	?>
	
	<h2><?php printf( __('Get your own %s account in seconds'), $current_site->site_name ) ?></h2>
	<form id="setupform" method="post" action="wp-signup.php">
		<input type="hidden" name="stage" value="validate-user-signup" />
		<?php do_action( "signup_hidden_fields" ); ?>
		<?php show_user_form($user_name, $user_email, $errors); ?>
		
		<p>
		<?php if( $active_signup == 'blog' ) { ?>
			<input id="signupblog" type="hidden" name="signup_for" value="blog" />
		<?php } elseif( $active_signup == 'user' ) { ?>
			<input id="signupblog" type="hidden" name="signup_for" value="user" />
		<?php } else { ?>
			<input id="signupblog" type="radio" name="signup_for" value="blog" <?php echo $signup['blog'] ?> />
			<label class="checkbox" for="signupblog"><?php _e('Gimme a blog!') ?></label>	
			<br />			
			<input id="signupuser" type="radio" name="signup_for" value="user" <?php echo $signup['user'] ?> />			
			<label class="checkbox" for="signupuser"><?php _e('Just a username, please.') ?></label>
		<?php } ?>
		</p>
		
		<p class="submit"><input type="submit" name="submit" class="submit" value="<?php esc_attr_e('Next') ?>" /></p>
	</form>
	<?php
}

function validate_user_signup() {
	$result = validate_user_form();
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_user($user_name, $user_email, $errors);
		return false;
	}

	if ( 'blog' == $_POST['signup_for'] ) {
		signup_blog($user_name, $user_email);
		return false;
	}

	wpmu_signup_user($user_name, $user_email, apply_filters( "add_signup_meta", array() ) );

	confirm_user_signup($user_name, $user_email);
	return true;
}

function confirm_user_signup($user_name, $user_email) {
	?>
	<h2><?php printf(__('%s is your new username'), $user_name) ?></h2>
	<p><?php _e('But, before you can start using your new username, <strong>you must activate it</strong>.') ?></p>
	<p><?php printf(__('Check your inbox at <strong>%1$s</strong> and click the link given.'),  $user_email) ?></p>
	<p><?php _e('If you do not activate your username within two days, you will have to sign up again.'); ?></p>
	<?php
	do_action('signup_finished');
}

function signup_blog($user_name = '', $user_email = '', $blogname = '', $blog_title = '', $errors = '') {
	if ( !is_wp_error($errors) )
		$errors = new WP_Error();

	// allow definition of default variables
	$filtered_results = apply_filters('signup_blog_init', array('user_name' => $user_name, 'user_email' => $user_email, 'blogname' => $blogname, 'blog_title' => $blog_title, 'errors' => $errors ));
	$user_name = $filtered_results['user_name'];
	$user_email = $filtered_results['user_email'];
	$blogname = $filtered_results['blogname'];
	$blog_title = $filtered_results['blog_title'];
	$errors = $filtered_results['errors'];

	if ( empty($blogname) )
		$blogname = $user_name;
	?>
	<form id="setupform" method="post" action="wp-signup.php">
		<input type="hidden" name="stage" value="validate-blog-signup" />
		<input type="hidden" name="user_name" value="<?php echo esc_attr($user_name) ?>" />
		<input type="hidden" name="user_email" value="<?php echo esc_attr($user_email) ?>" />
		<?php do_action( "signup_hidden_fields" ); ?>
		<?php show_blog_form($blogname, $blog_title, $errors); ?>
		<p class="submit"><input type="submit" name="submit" class="submit" value="<?php esc_attr_e('Signup') ?>" /></p>
	</form>
	<?php
}

function validate_blog_signup() {
	// Re-validate user info.
	$result = wpmu_validate_user_signup($_POST['user_name'], $_POST['user_email']);
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_user($user_name, $user_email, $errors);
		return false;
	}

	$result = wpmu_validate_blog_signup($_POST['blogname'], $_POST['blog_title']);
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_blog($user_name, $user_email, $blogname, $blog_title, $errors);
		return false;
	}

	$public = (int) $_POST['blog_public'];
	$meta = array ('lang_id' => 1, 'public' => $public);
	$meta = apply_filters( "add_signup_meta", $meta );

	wpmu_signup_blog($domain, $path, $blog_title, $user_name, $user_email, $meta);
	confirm_blog_signup($domain, $path, $blog_title, $user_name, $user_email, $meta);
	return true;
}

function confirm_blog_signup($domain, $path, $blog_title, $user_name = '', $user_email = '', $meta) {
	?>
	<h2><?php printf(__('Congratulations! Your new blog, %s, is almost ready.'), "<a href='http://{$domain}{$path}'>{$blog_title}</a>" ) ?></h2>
	
	<p><?php _e('But, before you can start using your blog, <strong>you must activate it</strong>.') ?></p>
	<p><?php printf(__('Check your inbox at <strong>%s</strong> and click the link given. It should arrive within 30 minutes.'),  $user_email) ?></p>
	<p><?php _e('If you do not activate your blog within two days, you will have to sign up again.'); ?></p>
	<h2><?php _e('Still waiting for your email?'); ?></h2>
	<p>
		<?php _e("If you haven't received your email yet, there are a number of things you can do:") ?>
		<ul id="noemail-tips">
			<li><p><strong><?php _e('Wait a little longer.  Sometimes delivery of email can be delayed by processes outside of our control.') ?></strong></p></li>
			<li><p><?php _e('Check the junk email or spam folder of your email client.  Sometime emails wind up there by mistake.') ?></p></li>
			<li><?php printf(__("Have you entered your email correctly?  We think it's %s but if you've entered it incorrectly, you won't receive it."), $user_email) ?></li>
		</ul>
	</p>
	<?php
	do_action('signup_finished');
}

// Main
$active_signup = get_site_option( 'registration' );
if( !$active_signup )
	$active_signup = 'all';

$active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"

if( is_super_admin() )
	echo '<div class="mu_alert">' . sprintf( __( "Greetings Site Administrator! You are currently allowing '%s' registrations. To change or disable registration go to your <a href='wp-admin/ms-options.php'>Options page</a>." ), $active_signup ) . '</div>';

$newblogname = isset($_GET['new']) ? strtolower(preg_replace('/^-|-$|[^-a-zA-Z0-9]/', '', $_GET['new'])) : null;

$current_user = wp_get_current_user();
if( $active_signup == "none" ) {
	_e( "Registration has been disabled." );
} elseif( $active_signup == 'blog' && !is_user_logged_in() ){
	if( is_ssl() ) {
		$proto = 'https://';
	} else {
		$proto = 'http://';
	}
	$login_url = site_url( 'wp-login.php?redirect_to=' . urlencode($proto . $_SERVER['HTTP_HOST'] . '/wp-signup.php' ));
	echo sprintf( __( "You must first <a href=\"%s\">login</a>, and then you can create a new blog."), $login_url );
} else {
	switch ($_POST['stage']) {
		case 'validate-user-signup' :
			if( $active_signup == 'all' || $_POST[ 'signup_for' ] == 'blog' && $active_signup == 'blog' || $_POST[ 'signup_for' ] == 'user' && $active_signup == 'user' )
				validate_user_signup();
			else
				_e( "User registration has been disabled." );
		break;
		case 'validate-blog-signup':
			if( $active_signup == 'all' || $active_signup == 'blog' )
				validate_blog_signup();
			else
				_e( "Blog registration has been disabled." );
			break;
		case 'gimmeanotherblog':
			validate_another_blog_signup();
			break;
		default :
			$user_email = $_POST[ 'user_email' ];
			do_action( "preprocess_signup_form" ); // populate the form from invites, elsewhere?
			if ( is_user_logged_in() && ( $active_signup == 'all' || $active_signup == 'blog' ) ) {
				signup_another_blog($newblogname);
			} elseif( is_user_logged_in() == false && ( $active_signup == 'all' || $active_signup == 'user' ) ) {
				signup_user( $newblogname, $user_email );
			} elseif( is_user_logged_in() == false && ( $active_signup == 'blog' ) ) {
				_e( "I'm sorry. We're not accepting new registrations at this time." );
			} else {
				_e( "You're logged in already. No need to register again!" );
			}
			if ($newblogname) {
				if( !is_subdomain_install() )
					$newblog = 'http://' . $current_site->domain . $current_site->path . $newblogname . '/';
				else
					$newblog = 'http://' . $newblogname . '.' . $current_site->domain . $current_site->path;
				if ($active_signup == 'blog' || $active_signup == 'all')
					printf(__("<p><em>The blog you were looking for, <strong>%s</strong> doesn't exist but you can create it now!</em></p>"), $newblog );
				else
					printf(__("<p><em>The blog you were looking for, <strong>%s</strong> doesn't exist.</em></p>"), $newblog );
			}
			break;
	}
}
?>
</div>
</div>

<?php get_footer(); ?>
