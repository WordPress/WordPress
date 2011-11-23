<?php
/**
 * General template tags that can go anywhere in a template.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Load header template.
 *
 * Includes the header template for a theme or if a name is specified then a
 * specialised header will be included.
 *
 * For the parameter, if the file is called "header-special.php" then specify
 * "special".
 *
 * @uses locate_template()
 * @since 1.5.0
 * @uses do_action() Calls 'get_header' action.
 *
 * @param string $name The name of the specialised header.
 */
function get_header( $name = null ) {
	do_action( 'get_header', $name );

	$templates = array();
	if ( isset($name) )
		$templates[] = "header-{$name}.php";

	$templates[] = 'header.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . '/theme-compat/header.php');
}

/**
 * Load footer template.
 *
 * Includes the footer template for a theme or if a name is specified then a
 * specialised footer will be included.
 *
 * For the parameter, if the file is called "footer-special.php" then specify
 * "special".
 *
 * @uses locate_template()
 * @since 1.5.0
 * @uses do_action() Calls 'get_footer' action.
 *
 * @param string $name The name of the specialised footer.
 */
function get_footer( $name = null ) {
	do_action( 'get_footer', $name );

	$templates = array();
	if ( isset($name) )
		$templates[] = "footer-{$name}.php";

	$templates[] = 'footer.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . '/theme-compat/footer.php');
}

/**
 * Load sidebar template.
 *
 * Includes the sidebar template for a theme or if a name is specified then a
 * specialised sidebar will be included.
 *
 * For the parameter, if the file is called "sidebar-special.php" then specify
 * "special".
 *
 * @uses locate_template()
 * @since 1.5.0
 * @uses do_action() Calls 'get_sidebar' action.
 *
 * @param string $name The name of the specialised sidebar.
 */
function get_sidebar( $name = null ) {
	do_action( 'get_sidebar', $name );

	$templates = array();
	if ( isset($name) )
		$templates[] = "sidebar-{$name}.php";

	$templates[] = 'sidebar.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . '/theme-compat/sidebar.php');
}

/**
 * Load a template part into a template
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialised part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * The template is included using require, not require_once, so you may include the
 * same template part multiple times.
 *
 * For the $name parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 *
 * @uses locate_template()
 * @since 3.0.0
 * @uses do_action() Calls 'get_template_part_{$slug}' action.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function get_template_part( $slug, $name = null ) {
	do_action( "get_template_part_{$slug}", $slug, $name );

	$templates = array();
	if ( isset($name) )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	locate_template($templates, true, false);
}

/**
 * Display search form.
 *
 * Will first attempt to locate the searchform.php file in either the child or
 * the parent, then load it. If it doesn't exist, then the default search form
 * will be displayed. The default search form is HTML, which will be displayed.
 * There is a filter applied to the search form HTML in order to edit or replace
 * it. The filter is 'get_search_form'.
 *
 * This function is primarily used by themes which want to hardcode the search
 * form into the sidebar and also by the search widget in WordPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'get_search_form'. This can be useful for outputting JavaScript that the
 * search relies on or various formatting that applies to the beginning of the
 * search. To give a few examples of what it can be used for.
 *
 * @since 2.7.0
 * @param boolean $echo Default to echo and not return the form.
 */
function get_search_form($echo = true) {
	do_action( 'get_search_form' );

	$search_form_template = locate_template('searchform.php');
	if ( '' != $search_form_template ) {
		require($search_form_template);
		return;
	}

	$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/' ) ) . '" >
	<div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
	<input type="text" value="' . get_search_query() . '" name="s" id="s" />
	<input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
	</div>
	</form>';

	if ( $echo )
		echo apply_filters('get_search_form', $form);
	else
		return apply_filters('get_search_form', $form);
}

/**
 * Display the Log In/Out link.
 *
 * Displays a link, which allows users to navigate to the Log In page to log in
 * or log out depending on whether they are currently logged in.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'loginout' hook on HTML link content.
 *
 * @param string $redirect Optional path to redirect to on login/logout.
 * @param boolean $echo Default to echo and not return the link.
 */
function wp_loginout($redirect = '', $echo = true) {
	if ( ! is_user_logged_in() )
		$link = '<a href="' . esc_url( wp_login_url($redirect) ) . '">' . __('Log in') . '</a>';
	else
		$link = '<a href="' . esc_url( wp_logout_url($redirect) ) . '">' . __('Log out') . '</a>';

	if ( $echo )
		echo apply_filters('loginout', $link);
	else
		return apply_filters('loginout', $link);
}

/**
 * Returns the Log Out URL.
 *
 * Returns the URL that allows the user to log out of the site
 *
 * @since 2.7.0
 * @uses wp_nonce_url() To protect against CSRF
 * @uses site_url() To generate the log in URL
 * @uses apply_filters() calls 'logout_url' hook on final logout url
 *
 * @param string $redirect Path to redirect to on logout.
 */
function wp_logout_url($redirect = '') {
	$args = array( 'action' => 'logout' );
	if ( !empty($redirect) ) {
		$args['redirect_to'] = urlencode( $redirect );
	}

	$logout_url = add_query_arg($args, site_url('wp-login.php', 'login'));
	$logout_url = wp_nonce_url( $logout_url, 'log-out' );

	return apply_filters('logout_url', $logout_url, $redirect);
}

/**
 * Returns the Log In URL.
 *
 * Returns the URL that allows the user to log in to the site
 *
 * @since 2.7.0
 * @uses site_url() To generate the log in URL
 * @uses apply_filters() calls 'login_url' hook on final login url
 *
 * @param string $redirect Path to redirect to on login.
 * @param bool $force_reauth Whether to force reauthorization, even if a cookie is present. Default is false.
 * @return string A log in url
 */
function wp_login_url($redirect = '', $force_reauth = false) {
	$login_url = site_url('wp-login.php', 'login');

	if ( !empty($redirect) )
		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);

	if ( $force_reauth )
		$login_url = add_query_arg('reauth', '1', $login_url);

	return apply_filters('login_url', $login_url, $redirect);
}

/**
 * Provides a simple login form for use anywhere within WordPress. By default, it echoes
 * the HTML immediately. Pass array('echo'=>false) to return the string instead.
 *
 * @since 3.0.0
 * @param array $args Configuration options to modify the form output
 * @return Void, or string containing the form
 */
function wp_login_form( $args = array() ) {
	$defaults = array( 'echo' => true,
						'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // Default redirect is back to the current page
	 					'form_id' => 'loginform',
						'label_username' => __( 'Username' ),
						'label_password' => __( 'Password' ),
						'label_remember' => __( 'Remember Me' ),
						'label_log_in' => __( 'Log In' ),
						'id_username' => 'user_login',
						'id_password' => 'user_pass',
						'id_remember' => 'rememberme',
						'id_submit' => 'wp-submit',
						'remember' => true,
						'value_username' => '',
						'value_remember' => false, // Set this to true to default the "Remember me" checkbox to checked
					);
	$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

	$form = '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
			' . apply_filters( 'login_form_top', '', $args ) . '
			<p class="login-username">
				<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
				<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="input" value="' . esc_attr( $args['value_username'] ) . '" size="20" tabindex="10" />
			</p>
			<p class="login-password">
				<label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
				<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="input" value="" size="20" tabindex="20" />
			</p>
			' . apply_filters( 'login_form_middle', '', $args ) . '
			' . ( $args['remember'] ? '<p class="login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever" tabindex="90"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></p>' : '' ) . '
			<p class="login-submit">
				<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="button-primary" value="' . esc_attr( $args['label_log_in'] ) . '" tabindex="100" />
				<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
			</p>
			' . apply_filters( 'login_form_bottom', '', $args ) . '
		</form>';

	if ( $args['echo'] )
		echo $form;
	else
		return $form;
}

/**
 * Returns the Lost Password URL.
 *
 * Returns the URL that allows the user to retrieve the lost password
 *
 * @since 2.8.0
 * @uses site_url() To generate the lost password URL
 * @uses apply_filters() calls 'lostpassword_url' hook on the lostpassword url
 *
 * @param string $redirect Path to redirect to on login.
 */
function wp_lostpassword_url( $redirect = '' ) {
	$args = array( 'action' => 'lostpassword' );
	if ( !empty($redirect) ) {
		$args['redirect_to'] = $redirect;
	}

	$lostpassword_url = add_query_arg( $args, network_site_url('wp-login.php', 'login') );
	return apply_filters( 'lostpassword_url', $lostpassword_url, $redirect );
}

/**
 * Display the Registration or Admin link.
 *
 * Display a link which allows the user to navigate to the registration page if
 * not logged in and registration is enabled or to the dashboard if logged in.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'register' hook on register / admin link content.
 *
 * @param string $before Text to output before the link (defaults to <li>).
 * @param string $after Text to output after the link (defaults to </li>).
 * @param boolean $echo Default to echo and not return the link.
 */
function wp_register( $before = '<li>', $after = '</li>', $echo = true ) {

	if ( ! is_user_logged_in() ) {
		if ( get_option('users_can_register') )
			$link = $before . '<a href="' . site_url('wp-login.php?action=register', 'login') . '">' . __('Register') . '</a>' . $after;
		else
			$link = '';
	} else {
		$link = $before . '<a href="' . admin_url() . '">' . __('Site Admin') . '</a>' . $after;
	}

	if ( $echo )
		echo apply_filters('register', $link);
	else
		return apply_filters('register', $link);
}

/**
 * Theme container function for the 'wp_meta' action.
 *
 * The 'wp_meta' action can have several purposes, depending on how you use it,
 * but one purpose might have been to allow for theme switching.
 *
 * @since 1.5.0
 * @link http://trac.wordpress.org/ticket/1458 Explanation of 'wp_meta' action.
 * @uses do_action() Calls 'wp_meta' hook.
 */
function wp_meta() {
	do_action('wp_meta');
}

/**
 * Display information about the blog.
 *
 * @see get_bloginfo() For possible values for the parameter.
 * @since 0.71
 *
 * @param string $show What to display.
 */
function bloginfo( $show='' ) {
	echo get_bloginfo( $show, 'display' );
}

/**
 * Retrieve information about the blog.
 *
 * Some show parameter values are deprecated and will be removed in future
 * versions. These options will trigger the _deprecated_argument() function.
 * The deprecated blog info options are listed in the function contents.
 *
 * The possible values for the 'show' parameter are listed below.
 * <ol>
 * <li><strong>url</strong> - Blog URI to homepage.</li>
 * <li><strong>wpurl</strong> - Blog URI path to WordPress.</li>
 * <li><strong>description</strong> - Secondary title</li>
 * </ol>
 *
 * The feed URL options can be retrieved from 'rdf_url' (RSS 0.91),
 * 'rss_url' (RSS 1.0), 'rss2_url' (RSS 2.0), or 'atom_url' (Atom feed). The
 * comment feeds can be retrieved from the 'comments_atom_url' (Atom comment
 * feed) or 'comments_rss2_url' (RSS 2.0 comment feed).
 *
 * @since 0.71
 *
 * @param string $show Blog info to retrieve.
 * @param string $filter How to filter what is retrieved.
 * @return string Mostly string values, might be empty.
 */
function get_bloginfo( $show = '', $filter = 'raw' ) {

	switch( $show ) {
		case 'home' : // DEPRECATED
		case 'siteurl' : // DEPRECATED
			_deprecated_argument( __FUNCTION__, '2.2', sprintf( __('The <code>%s</code> option is deprecated for the family of <code>bloginfo()</code> functions.' ), $show ) . ' ' . sprintf( __( 'Use the <code>%s</code> option instead.' ), 'url'  ) );
		case 'url' :
			$output = home_url();
			break;
		case 'wpurl' :
			$output = site_url();
			break;
		case 'description':
			$output = get_option('blogdescription');
			break;
		case 'rdf_url':
			$output = get_feed_link('rdf');
			break;
		case 'rss_url':
			$output = get_feed_link('rss');
			break;
		case 'rss2_url':
			$output = get_feed_link('rss2');
			break;
		case 'atom_url':
			$output = get_feed_link('atom');
			break;
		case 'comments_atom_url':
			$output = get_feed_link('comments_atom');
			break;
		case 'comments_rss2_url':
			$output = get_feed_link('comments_rss2');
			break;
		case 'pingback_url':
			$output = get_option('siteurl') .'/xmlrpc.php';
			break;
		case 'stylesheet_url':
			$output = get_stylesheet_uri();
			break;
		case 'stylesheet_directory':
			$output = get_stylesheet_directory_uri();
			break;
		case 'template_directory':
		case 'template_url':
			$output = get_template_directory_uri();
			break;
		case 'admin_email':
			$output = get_option('admin_email');
			break;
		case 'charset':
			$output = get_option('blog_charset');
			if ('' == $output) $output = 'UTF-8';
			break;
		case 'html_type' :
			$output = get_option('html_type');
			break;
		case 'version':
			global $wp_version;
			$output = $wp_version;
			break;
		case 'language':
			$output = get_locale();
			$output = str_replace('_', '-', $output);
			break;
		case 'text_direction':
			//_deprecated_argument( __FUNCTION__, '2.2', sprintf( __('The <code>%s</code> option is deprecated for the family of <code>bloginfo()</code> functions.' ), $show ) . ' ' . sprintf( __( 'Use the <code>%s</code> function instead.' ), 'is_rtl()'  ) );
			if ( function_exists( 'is_rtl' ) ) {
				$output = is_rtl() ? 'rtl' : 'ltr';
			} else {
				$output = 'ltr';
			}
			break;
		case 'name':
		default:
			$output = get_option('blogname');
			break;
	}

	$url = true;
	if (strpos($show, 'url') === false &&
		strpos($show, 'directory') === false &&
		strpos($show, 'home') === false)
		$url = false;

	if ( 'display' == $filter ) {
		if ( $url )
			$output = apply_filters('bloginfo_url', $output, $show);
		else
			$output = apply_filters('bloginfo', $output, $show);
	}

	return $output;
}

/**
 * Retrieve the current blog id
 *
 * @since 3.1.0
 *
 * @return int Blog id
 */
function get_current_blog_id() {
	global $blog_id;
	return absint($blog_id);
}

/**
 * Display or retrieve page title for all areas of blog.
 *
 * By default, the page title will display the separator before the page title,
 * so that the blog title will be before the page title. This is not good for
 * title display, since the blog title shows up on most tabs and not what is
 * important, which is the page that the user is looking at.
 *
 * There are also SEO benefits to having the blog title after or to the 'right'
 * or the page title. However, it is mostly common sense to have the blog title
 * to the right with most browsers supporting tabs. You can achieve this by
 * using the seplocation parameter and setting the value to 'right'. This change
 * was introduced around 2.5.0, in case backwards compatibility of themes is
 * important.
 *
 * @since 1.0.0
 *
 * @param string $sep Optional, default is '&raquo;'. How to separate the various items within the page title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @return string|null String on retrieve, null when displaying.
 */
function wp_title($sep = '&raquo;', $display = true, $seplocation = '') {
	global $wpdb, $wp_locale;

	$m = get_query_var('m');
	$year = get_query_var('year');
	$monthnum = get_query_var('monthnum');
	$day = get_query_var('day');
	$search = get_query_var('s');
	$title = '';

	$t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

	// If there is a post
	if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
		$title = single_post_title( '', false );
	}

	// If there's a category or tag
	if ( is_category() || is_tag() ) {
		$title = single_term_title( '', false );
	}

	// If there's a taxonomy
	if ( is_tax() ) {
		$term = get_queried_object();
		$tax = get_taxonomy( $term->taxonomy );
		$title = single_term_title( $tax->labels->name . $t_sep, false );
	}

	// If there's an author
	if ( is_author() ) {
		$author = get_queried_object();
		$title = $author->display_name;
	}

	// If there's a post type archive
	if ( is_post_type_archive() )
		$title = post_type_archive_title( '', false );

	// If there's a month
	if ( is_archive() && !empty($m) ) {
		$my_year = substr($m, 0, 4);
		$my_month = $wp_locale->get_month(substr($m, 4, 2));
		$my_day = intval(substr($m, 6, 2));
		$title = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
	}

	// If there's a year
	if ( is_archive() && !empty($year) ) {
		$title = $year;
		if ( !empty($monthnum) )
			$title .= $t_sep . $wp_locale->get_month($monthnum);
		if ( !empty($day) )
			$title .= $t_sep . zeroise($day, 2);
	}

	// If it's a search
	if ( is_search() ) {
		/* translators: 1: separator, 2: search phrase */
		$title = sprintf(__('Search Results %1$s %2$s'), $t_sep, strip_tags($search));
	}

	// If it's a 404 page
	if ( is_404() ) {
		$title = __('Page not found');
	}

	$prefix = '';
	if ( !empty($title) )
		$prefix = " $sep ";

 	// Determines position of the separator and direction of the breadcrumb
	if ( 'right' == $seplocation ) { // sep on right, so reverse the order
		$title_array = explode( $t_sep, $title );
		$title_array = array_reverse( $title_array );
		$title = implode( " $sep ", $title_array ) . $prefix;
	} else {
		$title_array = explode( $t_sep, $title );
		$title = $prefix . implode( " $sep ", $title_array );
	}

	$title = apply_filters('wp_title', $title, $sep, $seplocation);

	// Send it out
	if ( $display )
		echo $title;
	else
		return $title;

}

/**
 * Display or retrieve page title for post.
 *
 * This is optimized for single.php template file for displaying the post title.
 *
 * It does not support placing the separator after the title, but by leaving the
 * prefix parameter empty, you can set the title separator manually. The prefix
 * does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 0.71
 *
 * @param string $prefix Optional. What to display before the title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @return string|null Title when retrieving, null when displaying or failure.
 */
function single_post_title($prefix = '', $display = true) {
	$_post = get_queried_object();

	if ( !isset($_post->post_title) )
		return;

	$title = apply_filters('single_post_title', $_post->post_title, $_post);
	if ( $display )
		echo $prefix . $title;
	else
		return $title;
}

/**
 * Display or retrieve title for a post type archive.
 *
 * This is optimized for archive.php and archive-{$post_type}.php template files
 * for displaying the title of the post type.
 *
 * @since 3.1.0
 *
 * @param string $prefix Optional. What to display before the title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @return string|null Title when retrieving, null when displaying or failure.
 */
function post_type_archive_title( $prefix = '', $display = true ) {
	if ( ! is_post_type_archive() )
		return;

	$post_type_obj = get_queried_object();
	$title = apply_filters('post_type_archive_title', $post_type_obj->labels->name );

	if ( $display )
		echo $prefix . $title;
	else
		return $title;
}

/**
 * Display or retrieve page title for category archive.
 *
 * This is useful for category template file or files, because it is optimized
 * for category page title and with less overhead than {@link wp_title()}.
 *
 * It does not support placing the separator after the title, but by leaving the
 * prefix parameter empty, you can set the title separator manually. The prefix
 * does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 0.71
 *
 * @param string $prefix Optional. What to display before the title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @return string|null Title when retrieving, null when displaying or failure.
 */
function single_cat_title( $prefix = '', $display = true ) {
	return single_term_title( $prefix, $display );
}

/**
 * Display or retrieve page title for tag post archive.
 *
 * Useful for tag template files for displaying the tag page title. It has less
 * overhead than {@link wp_title()}, because of its limited implementation.
 *
 * It does not support placing the separator after the title, but by leaving the
 * prefix parameter empty, you can set the title separator manually. The prefix
 * does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 2.3.0
 *
 * @param string $prefix Optional. What to display before the title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @return string|null Title when retrieving, null when displaying or failure.
 */
function single_tag_title( $prefix = '', $display = true ) {
	return single_term_title( $prefix, $display );
}

/**
 * Display or retrieve page title for taxonomy term archive.
 *
 * Useful for taxonomy term template files for displaying the taxonomy term page title.
 * It has less overhead than {@link wp_title()}, because of its limited implementation.
 *
 * It does not support placing the separator after the title, but by leaving the
 * prefix parameter empty, you can set the title separator manually. The prefix
 * does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 3.1.0
 *
 * @param string $prefix Optional. What to display before the title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @return string|null Title when retrieving, null when displaying or failure.
 */
function single_term_title( $prefix = '', $display = true ) {
	$term = get_queried_object();

	if ( !$term )
		return;

	if ( is_category() )
		$term_name = apply_filters( 'single_cat_title', $term->name );
	elseif ( is_tag() )
		$term_name = apply_filters( 'single_tag_title', $term->name );
	elseif ( is_tax() )
		$term_name = apply_filters( 'single_term_title', $term->name );
	else
		return;

	if ( empty( $term_name ) )
		return;

	if ( $display )
		echo $prefix . $term_name;
	else
		return $term_name;
}

/**
 * Display or retrieve page title for post archive based on date.
 *
 * Useful for when the template only needs to display the month and year, if
 * either are available. Optimized for just this purpose, so if it is all that
 * is needed, should be better than {@link wp_title()}.
 *
 * It does not support placing the separator after the title, but by leaving the
 * prefix parameter empty, you can set the title separator manually. The prefix
 * does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 0.71
 *
 * @param string $prefix Optional. What to display before the title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @return string|null Title when retrieving, null when displaying or failure.
 */
function single_month_title($prefix = '', $display = true ) {
	global $wp_locale;

	$m = get_query_var('m');
	$year = get_query_var('year');
	$monthnum = get_query_var('monthnum');

	if ( !empty($monthnum) && !empty($year) ) {
		$my_year = $year;
		$my_month = $wp_locale->get_month($monthnum);
	} elseif ( !empty($m) ) {
		$my_year = substr($m, 0, 4);
		$my_month = $wp_locale->get_month(substr($m, 4, 2));
	}

	if ( empty($my_month) )
		return false;

	$result = $prefix . $my_month . $prefix . $my_year;

	if ( !$display )
		return $result;
	echo $result;
}

/**
 * Retrieve archive link content based on predefined or custom code.
 *
 * The format can be one of four styles. The 'link' for head element, 'option'
 * for use in the select element, 'html' for use in list (either ol or ul HTML
 * elements). Custom content is also supported using the before and after
 * parameters.
 *
 * The 'link' format uses the link HTML element with the <em>archives</em>
 * relationship. The before and after parameters are not used. The text
 * parameter is used to describe the link.
 *
 * The 'option' format uses the option HTML element for use in select element.
 * The value is the url parameter and the before and after parameters are used
 * between the text description.
 *
 * The 'html' format, which is the default, uses the li HTML element for use in
 * the list HTML elements. The before parameter is before the link and the after
 * parameter is after the closing link.
 *
 * The custom format uses the before parameter before the link ('a' HTML
 * element) and the after parameter after the closing link tag. If the above
 * three values for the format are not used, then custom format is assumed.
 *
 * @since 1.0.0
 *
 * @param string $url URL to archive.
 * @param string $text Archive text description.
 * @param string $format Optional, default is 'html'. Can be 'link', 'option', 'html', or custom.
 * @param string $before Optional.
 * @param string $after Optional.
 * @return string HTML link content for archive.
 */
function get_archives_link($url, $text, $format = 'html', $before = '', $after = '') {
	$text = wptexturize($text);
	$title_text = esc_attr($text);
	$url = esc_url($url);

	if ('link' == $format)
		$link_html = "\t<link rel='archives' title='$title_text' href='$url' />\n";
	elseif ('option' == $format)
		$link_html = "\t<option value='$url'>$before $text $after</option>\n";
	elseif ('html' == $format)
		$link_html = "\t<li>$before<a href='$url' title='$title_text'>$text</a>$after</li>\n";
	else // custom
		$link_html = "\t$before<a href='$url' title='$title_text'>$text</a>$after\n";

	$link_html = apply_filters( 'get_archives_link', $link_html );

	return $link_html;
}

/**
 * Display archive links based on type and format.
 *
 * The 'type' argument offers a few choices and by default will display monthly
 * archive links. The other options for values are 'daily', 'weekly', 'monthly',
 * 'yearly', 'postbypost' or 'alpha'. Both 'postbypost' and 'alpha' display the
 * same archive link list, the difference between the two is that 'alpha'
 * will order by post title and 'postbypost' will order by post date.
 *
 * The date archives will logically display dates with links to the archive post
 * page. The 'postbypost' and 'alpha' values for 'type' argument will display
 * the post titles.
 *
 * The 'limit' argument will only display a limited amount of links, specified
 * by the 'limit' integer value. By default, there is no limit. The
 * 'show_post_count' argument will show how many posts are within the archive.
 * By default, the 'show_post_count' argument is set to false.
 *
 * For the 'format', 'before', and 'after' arguments, see {@link
 * get_archives_link()}. The values of these arguments have to do with that
 * function.
 *
 * @since 1.2.0
 *
 * @param string|array $args Optional. Override defaults.
 */
function wp_get_archives($args = '') {
	global $wpdb, $wp_locale;

	$defaults = array(
		'type' => 'monthly', 'limit' => '',
		'format' => 'html', 'before' => '',
		'after' => '', 'show_post_count' => false,
		'echo' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( '' == $type )
		$type = 'monthly';

	if ( '' != $limit ) {
		$limit = absint($limit);
		$limit = ' LIMIT '.$limit;
	}

	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
	$archive_date_format_over_ride = 0;

	// options for daily archive (only if you over-ride the general date format)
	$archive_day_date_format = 'Y/m/d';

	// options for weekly archive (only if you over-ride the general date format)
	$archive_week_start_date_format = 'Y/m/d';
	$archive_week_end_date_format	= 'Y/m/d';

	if ( !$archive_date_format_over_ride ) {
		$archive_day_date_format = get_option('date_format');
		$archive_week_start_date_format = get_option('date_format');
		$archive_week_end_date_format = get_option('date_format');
	}

	//filters
	$where = apply_filters( 'getarchives_where', "WHERE post_type = 'post' AND post_status = 'publish'", $r );
	$join = apply_filters( 'getarchives_join', '', $r );

	$output = '';

	if ( 'monthly' == $type ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ( $arcresults ) {
			$afterafter = $after;
			foreach ( (array) $arcresults as $arcresult ) {
				$url = get_month_link( $arcresult->year, $arcresult->month );
				/* translators: 1: month name, 2: 4-digit year */
				$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month($arcresult->month), $arcresult->year);
				if ( $show_post_count )
					$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
				$output .= get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ('yearly' == $type) {
		$query = "SELECT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ($arcresults) {
			$afterafter = $after;
			foreach ( (array) $arcresults as $arcresult) {
				$url = get_year_link($arcresult->year);
				$text = sprintf('%d', $arcresult->year);
				if ($show_post_count)
					$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
				$output .= get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ( 'daily' == $type ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ( $arcresults ) {
			$afterafter = $after;
			foreach ( (array) $arcresults as $arcresult ) {
				$url	= get_day_link($arcresult->year, $arcresult->month, $arcresult->dayofmonth);
				$date = sprintf('%1$d-%2$02d-%3$02d 00:00:00', $arcresult->year, $arcresult->month, $arcresult->dayofmonth);
				$text = mysql2date($archive_day_date_format, $date);
				if ($show_post_count)
					$after = '&nbsp;('.$arcresult->posts.')'.$afterafter;
				$output .= get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ( 'weekly' == $type ) {
		$week = _wp_mysql_week( '`post_date`' );
		$query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		$arc_w_last = '';
		$afterafter = $after;
		if ( $arcresults ) {
				foreach ( (array) $arcresults as $arcresult ) {
					if ( $arcresult->week != $arc_w_last ) {
						$arc_year = $arcresult->yr;
						$arc_w_last = $arcresult->week;
						$arc_week = get_weekstartend($arcresult->yyyymmdd, get_option('start_of_week'));
						$arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
						$arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
						$url  = sprintf('%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $arc_year, '&amp;', '=', $arcresult->week);
						$text = $arc_week_start . $archive_week_separator . $arc_week_end;
						if ($show_post_count)
							$after = '&nbsp;('.$arcresult->posts.')'.$afterafter;
						$output .= get_archives_link($url, $text, $format, $before, $after);
					}
				}
		}
	} elseif ( ( 'postbypost' == $type ) || ('alpha' == $type) ) {
		$orderby = ('alpha' == $type) ? 'post_title ASC ' : 'post_date DESC ';
		$query = "SELECT * FROM $wpdb->posts $join $where ORDER BY $orderby $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ( $arcresults ) {
			foreach ( (array) $arcresults as $arcresult ) {
				if ( $arcresult->post_date != '0000-00-00 00:00:00' ) {
					$url  = get_permalink( $arcresult );
					if ( $arcresult->post_title )
						$text = strip_tags( apply_filters( 'the_title', $arcresult->post_title, $arcresult->ID ) );
					else
						$text = $arcresult->ID;
					$output .= get_archives_link($url, $text, $format, $before, $after);
				}
			}
		}
	}
	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Get number of days since the start of the week.
 *
 * @since 1.5.0
 * @usedby get_calendar()
 *
 * @param int $num Number of day.
 * @return int Days since the start of the week.
 */
function calendar_week_mod($num) {
	$base = 7;
	return ($num - $base*floor($num/$base));
}

/**
 * Display calendar with days that have posts as links.
 *
 * The calendar is cached, which will be retrieved, if it exists. If there are
 * no posts for the month, then it will not be displayed.
 *
 * @since 1.0.0
 *
 * @param bool $initial Optional, default is true. Use initial calendar names.
 * @param bool $echo Optional, default is true. Set to false for return.
 */
function get_calendar($initial = true, $echo = true) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

	$cache = array();
	$key = md5( $m . $monthnum . $year );
	if ( $cache = wp_cache_get( 'get_calendar', 'calendar' ) ) {
		if ( is_array($cache) && isset( $cache[ $key ] ) ) {
			if ( $echo ) {
				echo apply_filters( 'get_calendar',  $cache[$key] );
				return;
			} else {
				return apply_filters( 'get_calendar',  $cache[$key] );
			}
		}
	}

	if ( !is_array($cache) )
		$cache = array();

	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 1");
		if ( !$gotsome ) {
			$cache[ $key ] = '';
			wp_cache_set( 'get_calendar', $cache, 'calendar' );
			return;
		}
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp'));
		$thismonth = gmdate('m', current_time('timestamp'));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
	$last_day = date('t', $unixmonth);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$thisyear-$thismonth-01'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");
	$next = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date ASC
			LIMIT 1");

	/* translators: Calendar caption: 1: month name, 2: 4-digit year */
	$calendar_caption = _x('%1$s %2$s', 'calendar caption');
	$calendar_output = '<table id="wp-calendar">
	<caption>' . sprintf($calendar_caption, $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		$wd = esc_attr($wd);
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ( $previous ) {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="prev"><a href="' . get_month_link($previous->year, $previous->month) . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month), date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	$calendar_output .= "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="next"><a href="' . get_month_link($next->year, $next->month) . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month), date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) ) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
	} else {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	$calendar_output .= '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
		AND post_type = 'post' AND post_status = 'publish'
		AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N);
	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT ID, post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts "
		."WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00' "
		."AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' "
		."AND post_type = 'post' AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( (array) $ak_post_titles as $ak_post_title ) {

				$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
		}
	}


	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr($pad) .'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')) )
			$calendar_output .= '<td id="today">';
		else
			$calendar_output .= '<td>';

		if ( in_array($day, $daywithpost) ) // any posts today?
				$calendar_output .= '<a href="' . get_day_link( $thisyear, $thismonth, $day ) . '" title="' . esc_attr( $ak_titles_for_day[ $day ] ) . "\">$day</a>";
		else
			$calendar_output .= $day;
		$calendar_output .= '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr($pad) .'">&nbsp;</td>';

	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

	$cache[ $key ] = $calendar_output;
	wp_cache_set( 'get_calendar', $cache, 'calendar' );

	if ( $echo )
		echo apply_filters( 'get_calendar',  $calendar_output );
	else
		return apply_filters( 'get_calendar',  $calendar_output );

}

/**
 * Purge the cached results of get_calendar.
 *
 * @see get_calendar
 * @since 2.1.0
 */
function delete_get_calendar_cache() {
	wp_cache_delete( 'get_calendar', 'calendar' );
}
add_action( 'save_post', 'delete_get_calendar_cache' );
add_action( 'delete_post', 'delete_get_calendar_cache' );
add_action( 'update_option_start_of_week', 'delete_get_calendar_cache' );
add_action( 'update_option_gmt_offset', 'delete_get_calendar_cache' );

/**
 * Display all of the allowed tags in HTML format with attributes.
 *
 * This is useful for displaying in the comment area, which elements and
 * attributes are supported. As well as any plugins which want to display it.
 *
 * @since 1.0.1
 * @uses $allowedtags
 *
 * @return string HTML allowed tags entity encoded.
 */
function allowed_tags() {
	global $allowedtags;
	$allowed = '';
	foreach ( (array) $allowedtags as $tag => $attributes ) {
		$allowed .= '<'.$tag;
		if ( 0 < count($attributes) ) {
			foreach ( $attributes as $attribute => $limits ) {
				$allowed .= ' '.$attribute.'=""';
			}
		}
		$allowed .= '> ';
	}
	return htmlentities($allowed);
}

/***** Date/Time tags *****/

/**
 * Outputs the date in iso8601 format for xml files.
 *
 * @since 1.0.0
 */
function the_date_xml() {
	global $post;
	echo mysql2date('Y-m-d', $post->post_date, false);
}

/**
 * Display or Retrieve the date the current $post was written (once per date)
 *
 * Will only output the date if the current post's date is different from the
 * previous one output.
 *
 * i.e. Only one date listing will show per day worth of posts shown in the loop, even if the
 * function is called several times for each post.
 *
 * HTML output can be filtered with 'the_date'.
 * Date string output can be filtered with 'get_the_date'.
 *
 * @since 0.71
 * @uses get_the_date()
 * @param string $d Optional. PHP date format defaults to the date_format option if not specified.
 * @param string $before Optional. Output before the date.
 * @param string $after Optional. Output after the date.
 * @param bool $echo Optional, default is display. Whether to echo the date or return it.
 * @return string|null Null if displaying, string if retrieving.
 */
function the_date( $d = '', $before = '', $after = '', $echo = true ) {
	global $currentday, $previousday;
	$the_date = '';
	if ( $currentday != $previousday ) {
		$the_date .= $before;
		$the_date .= get_the_date( $d );
		$the_date .= $after;
		$previousday = $currentday;

		$the_date = apply_filters('the_date', $the_date, $d, $before, $after);

		if ( $echo )
			echo $the_date;
		else
			return $the_date;
	}

	return null;
}

/**
 * Retrieve the date the current $post was written.
 *
 * Unlike the_date() this function will always return the date.
 * Modify output with 'get_the_date' filter.
 *
 * @since 3.0.0
 *
 * @param string $d Optional. PHP date format defaults to the date_format option if not specified.
 * @return string|null Null if displaying, string if retrieving.
 */
function get_the_date( $d = '' ) {
	global $post;
	$the_date = '';

	if ( '' == $d )
		$the_date .= mysql2date(get_option('date_format'), $post->post_date);
	else
		$the_date .= mysql2date($d, $post->post_date);

	return apply_filters('get_the_date', $the_date, $d);
}

/**
 * Display the date on which the post was last modified.
 *
 * @since 2.1.0
 *
 * @param string $d Optional. PHP date format defaults to the date_format option if not specified.
 * @param string $before Optional. Output before the date.
 * @param string $after Optional. Output after the date.
 * @param bool $echo Optional, default is display. Whether to echo the date or return it.
 * @return string|null Null if displaying, string if retrieving.
 */
function the_modified_date($d = '', $before='', $after='', $echo = true) {

	$the_modified_date = $before . get_the_modified_date($d) . $after;
	$the_modified_date = apply_filters('the_modified_date', $the_modified_date, $d, $before, $after);

	if ( $echo )
		echo $the_modified_date;
	else
		return $the_modified_date;

}

/**
 * Retrieve the date on which the post was last modified.
 *
 * @since 2.1.0
 *
 * @param string $d Optional. PHP date format. Defaults to the "date_format" option
 * @return string
 */
function get_the_modified_date($d = '') {
	if ( '' == $d )
		$the_time = get_post_modified_time(get_option('date_format'), null, null, true);
	else
		$the_time = get_post_modified_time($d, null, null, true);
	return apply_filters('get_the_modified_date', $the_time, $d);
}

/**
 * Display the time at which the post was written.
 *
 * @since 0.71
 *
 * @param string $d Either 'G', 'U', or php date format.
 */
function the_time( $d = '' ) {
	echo apply_filters('the_time', get_the_time( $d ), $d);
}

/**
 * Retrieve the time at which the post was written.
 *
 * @since 1.5.0
 *
 * @param string $d Optional Either 'G', 'U', or php date format defaults to the value specified in the time_format option.
 * @param int|object $post Optional post ID or object. Default is global $post object.
 * @return string
 */
function get_the_time( $d = '', $post = null ) {
	$post = get_post($post);

	if ( '' == $d )
		$the_time = get_post_time(get_option('time_format'), false, $post, true);
	else
		$the_time = get_post_time($d, false, $post, true);
	return apply_filters('get_the_time', $the_time, $d, $post);
}

/**
 * Retrieve the time at which the post was written.
 *
 * @since 2.0.0
 *
 * @param string $d Optional Either 'G', 'U', or php date format.
 * @param bool $gmt Optional, default is false. Whether to return the gmt time.
 * @param int|object $post Optional post ID or object. Default is global $post object.
 * @param bool $translate Whether to translate the time string
 * @return string
 */
function get_post_time( $d = 'U', $gmt = false, $post = null, $translate = false ) { // returns timestamp
	$post = get_post($post);

	if ( $gmt )
		$time = $post->post_date_gmt;
	else
		$time = $post->post_date;

	$time = mysql2date($d, $time, $translate);
	return apply_filters('get_post_time', $time, $d, $gmt);
}

/**
 * Display the time at which the post was last modified.
 *
 * @since 2.0.0
 *
 * @param string $d Optional Either 'G', 'U', or php date format defaults to the value specified in the time_format option.
 */
function the_modified_time($d = '') {
	echo apply_filters('the_modified_time', get_the_modified_time($d), $d);
}

/**
 * Retrieve the time at which the post was last modified.
 *
 * @since 2.0.0
 *
 * @param string $d Optional Either 'G', 'U', or php date format defaults to the value specified in the time_format option.
 * @return string
 */
function get_the_modified_time($d = '') {
	if ( '' == $d )
		$the_time = get_post_modified_time(get_option('time_format'), null, null, true);
	else
		$the_time = get_post_modified_time($d, null, null, true);
	return apply_filters('get_the_modified_time', $the_time, $d);
}

/**
 * Retrieve the time at which the post was last modified.
 *
 * @since 2.0.0
 *
 * @param string $d Optional, default is 'U'. Either 'G', 'U', or php date format.
 * @param bool $gmt Optional, default is false. Whether to return the gmt time.
 * @param int|object $post Optional, default is global post object. A post_id or post object
 * @param bool $translate Optional, default is false. Whether to translate the result
 * @return string Returns timestamp
 */
function get_post_modified_time( $d = 'U', $gmt = false, $post = null, $translate = false ) {
	$post = get_post($post);

	if ( $gmt )
		$time = $post->post_modified_gmt;
	else
		$time = $post->post_modified;
	$time = mysql2date($d, $time, $translate);

	return apply_filters('get_post_modified_time', $time, $d, $gmt);
}

/**
 * Display the weekday on which the post was written.
 *
 * @since 0.71
 * @uses $wp_locale
 * @uses $post
 */
function the_weekday() {
	global $wp_locale, $post;
	$the_weekday = $wp_locale->get_weekday(mysql2date('w', $post->post_date, false));
	$the_weekday = apply_filters('the_weekday', $the_weekday);
	echo $the_weekday;
}

/**
 * Display the weekday on which the post was written.
 *
 * Will only output the weekday if the current post's weekday is different from
 * the previous one output.
 *
 * @since 0.71
 *
 * @param string $before Optional Output before the date.
 * @param string $after Optional Output after the date.
 */
function the_weekday_date($before='',$after='') {
	global $wp_locale, $post, $day, $previousweekday;
	$the_weekday_date = '';
	if ( $currentday != $previousweekday ) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $wp_locale->get_weekday(mysql2date('w', $post->post_date, false));
		$the_weekday_date .= $after;
		$previousweekday = $currentday;
	}
	$the_weekday_date = apply_filters('the_weekday_date', $the_weekday_date, $before, $after);
	echo $the_weekday_date;
}

/**
 * Fire the wp_head action
 *
 * @since 1.2.0
 * @uses do_action() Calls 'wp_head' hook.
 */
function wp_head() {
	do_action('wp_head');
}

/**
 * Fire the wp_footer action
 *
 * @since 1.5.1
 * @uses do_action() Calls 'wp_footer' hook.
 */
function wp_footer() {
	do_action('wp_footer');
}

/**
 * Display the links to the general feeds.
 *
 * @since 2.8.0
 *
 * @param array $args Optional arguments.
 */
function feed_links( $args = array() ) {
	if ( !current_theme_supports('automatic-feed-links') )
		return;

	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links */
		'separator'	=> _x('&raquo;', 'feed link'),
		/* translators: 1: blog title, 2: separator (raquo) */
		'feedtitle'	=> __('%1$s %2$s Feed'),
		/* translators: %s: blog title, 2: separator (raquo) */
		'comstitle'	=> __('%1$s %2$s Comments Feed'),
	);

	$args = wp_parse_args( $args, $defaults );

	echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr(sprintf( $args['feedtitle'], get_bloginfo('name'), $args['separator'] )) . '" href="' . get_feed_link() . "\" />\n";
	echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr(sprintf( $args['comstitle'], get_bloginfo('name'), $args['separator'] )) . '" href="' . get_feed_link( 'comments_' . get_default_feed() ) . "\" />\n";
}

/**
 * Display the links to the extra feeds such as category feeds.
 *
 * @since 2.8.0
 *
 * @param array $args Optional arguments.
 */
function feed_links_extra( $args = array() ) {
	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links */
		'separator'   => _x('&raquo;', 'feed link'),
		/* translators: 1: blog name, 2: separator(raquo), 3: post title */
		'singletitle' => __('%1$s %2$s %3$s Comments Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: category name */
		'cattitle'    => __('%1$s %2$s %3$s Category Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: tag name */
		'tagtitle'    => __('%1$s %2$s %3$s Tag Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: author name  */
		'authortitle' => __('%1$s %2$s Posts by %3$s Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: search phrase */
		'searchtitle' => __('%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed'),
	);

	$args = wp_parse_args( $args, $defaults );

	if ( is_single() || is_page() ) {
		$id = 0;
		$post = &get_post( $id );

		if ( comments_open() || pings_open() || $post->comment_count > 0 ) {
			$title = sprintf( $args['singletitle'], get_bloginfo('name'), $args['separator'], esc_html( get_the_title() ) );
			$href = get_post_comments_feed_link( $post->ID );
		}
	} elseif ( is_category() ) {
		$term = get_queried_object();

		$title = sprintf( $args['cattitle'], get_bloginfo('name'), $args['separator'], $term->name );
		$href = get_category_feed_link( $term->term_id );
	} elseif ( is_tag() ) {
		$term = get_queried_object();

		$title = sprintf( $args['tagtitle'], get_bloginfo('name'), $args['separator'], $term->name );
		$href = get_tag_feed_link( $term->term_id );
	} elseif ( is_author() ) {
		$author_id = intval( get_query_var('author') );

		$title = sprintf( $args['authortitle'], get_bloginfo('name'), $args['separator'], get_the_author_meta( 'display_name', $author_id ) );
		$href = get_author_feed_link( $author_id );
	} elseif ( is_search() ) {
		$title = sprintf( $args['searchtitle'], get_bloginfo('name'), $args['separator'], get_search_query( false ) );
		$href = get_search_feed_link();
	}

	if ( isset($title) && isset($href) )
		echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '" />' . "\n";
}

/**
 * Display the link to the Really Simple Discovery service endpoint.
 *
 * @link http://archipelago.phrasewise.com/rsd
 * @since 2.0.0
 */
function rsd_link() {
	echo '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="' . get_bloginfo('wpurl') . "/xmlrpc.php?rsd\" />\n";
}

/**
 * Display the link to the Windows Live Writer manifest file.
 *
 * @link http://msdn.microsoft.com/en-us/library/bb463265.aspx
 * @since 2.3.1
 */
function wlwmanifest_link() {
	echo '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="'
		. get_bloginfo('wpurl') . '/wp-includes/wlwmanifest.xml" /> ' . "\n";
}

/**
 * Display a noindex meta tag if required by the blog configuration.
 *
 * If a blog is marked as not being public then the noindex meta tag will be
 * output to tell web robots not to index the page content. Add this to the wp_head action.
 * Typical usage is as a wp_head callback. add_action( 'wp_head', 'noindex' );
 *
 * @see wp_no_robots
 *
 * @since 2.1.0
 */
function noindex() {
	// If the blog is not public, tell robots to go away.
	if ( '0' == get_option('blog_public') )
		wp_no_robots();
}

/**
 * Display a noindex meta tag.
 *
 * Outputs a noindex meta tag that tells web robots not to index the page content.
 * Typical usage is as a wp_head callback. add_action( 'wp_head', 'wp_no_robots' );
 *
 * @since 3.3.0
 */
function wp_no_robots() {
	echo "<meta name='robots' content='noindex,nofollow' />\n";
}

/**
 * Determine if TinyMCE is available.
 *
 * Checks to see if the user has deleted the tinymce files to slim down there WordPress install.
 *
 * @since 2.1.0
 *
 * @return bool Whether TinyMCE exists.
 */
function rich_edit_exists() {
	global $wp_rich_edit_exists;
	if ( !isset($wp_rich_edit_exists) )
		$wp_rich_edit_exists = file_exists(ABSPATH . WPINC . '/js/tinymce/tiny_mce.js');
	return $wp_rich_edit_exists;
}

/**
 * Whether the user should have a WYSIWIG editor.
 *
 * Checks that the user requires a WYSIWIG editor and that the editor is
 * supported in the users browser.
 *
 * @since 2.0.0
 *
 * @return bool
 */
function user_can_richedit() {
	global $wp_rich_edit, $is_gecko, $is_opera, $is_safari, $is_chrome, $is_iphone, $is_IE;

	if ( !isset( $wp_rich_edit) ) {
		$wp_rich_edit = false;

		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			if ( $is_safari ) {
				if ( !$is_iphone || ( preg_match( '!AppleWebKit/(\d+)!', $_SERVER['HTTP_USER_AGENT'], $match ) && intval($match[1]) >= 534 ) )
					$wp_rich_edit = true;
			} elseif ( $is_gecko || $is_opera || $is_chrome || $is_IE ) {
				$wp_rich_edit = true;
			}
		}
	}

	return apply_filters('user_can_richedit', $wp_rich_edit);
}

/**
 * Find out which editor should be displayed by default.
 *
 * Works out which of the two editors to display as the current editor for a
 * user.
 *
 * @since 2.5.0
 *
 * @return string Either 'tinymce', or 'html', or 'test'
 */
function wp_default_editor() {
	$r = user_can_richedit() ? 'tinymce' : 'html'; // defaults
	if ( $user = wp_get_current_user() ) { // look for cookie
		$ed = get_user_setting('editor', 'tinymce');
		$r = ( in_array($ed, array('tinymce', 'html', 'test') ) ) ? $ed : $r;
	}
	return apply_filters( 'wp_default_editor', $r ); // filter
}

/**
 * Renders an editor.
 *
 * Using this function is the proper way to output all needed components for both TinyMCE and Quicktags.
 * _WP_Editors should not be used directly. See http://core.trac.wordpress.org/ticket/17144.
 * 
 * NOTE: Once initialized the TinyMCE editor cannot be safely moved in the DOM. For that reason
 * running wp_editor() inside of a metabox is not a good idea unless only Quicktags is used.
 * On the post edit screen several actions can be used to include additional editors
 * containing TinyMCE: 'edit_page_form', 'edit_form_advanced' and 'dbx_post_sidebar'.
 * See http://core.trac.wordpress.org/ticket/19173 for more information.
 * 
 * @see wp-includes/class-wp-editor.php
 * @since 3.3
 *
 * @param string $content Initial content for the editor.
 * @param string $editor_id HTML ID attribute value for the textarea and TinyMCE. Can only be /[a-z]+/.
 * @param array $settings See _WP_Editors::editor().
 */
function wp_editor( $content, $editor_id, $settings = array() ) {
	if ( ! class_exists( '_WP_Editors' ) )
		require( ABSPATH . WPINC . '/class-wp-editor.php' );

	_WP_Editors::editor($content, $editor_id, $settings);
}

/**
 * Retrieve the contents of the search WordPress query variable.
 *
 * The search query string is passed through {@link esc_attr()}
 * to ensure that it is safe for placing in an html attribute.
 *
 * @since 2.3.0
 * @uses esc_attr()
 *
 * @param bool $escaped Whether the result is escaped. Default true.
 * 	Only use when you are later escaping it. Do not use unescaped.
 * @return string
 */
function get_search_query( $escaped = true ) {
	$query = apply_filters( 'get_search_query', get_query_var( 's' ) );
	if ( $escaped )
		$query = esc_attr( $query );
	return $query;
}

/**
 * Display the contents of the search query variable.
 *
 * The search query string is passed through {@link esc_attr()}
 * to ensure that it is safe for placing in an html attribute.
 *
 * @uses esc_attr()
 * @since 2.1.0
 */
function the_search_query() {
	echo esc_attr( apply_filters( 'the_search_query', get_search_query( false ) ) );
}

/**
 * Display the language attributes for the html tag.
 *
 * Builds up a set of html attributes containing the text direction and language
 * information for the page.
 *
 * @since 2.1.0
 *
 * @param string $doctype The type of html document (xhtml|html).
 */
function language_attributes($doctype = 'html') {
	$attributes = array();
	$output = '';

	if ( function_exists( 'is_rtl' ) )
		$attributes[] = 'dir="' . ( is_rtl() ? 'rtl' : 'ltr' ) . '"';

	if ( $lang = get_bloginfo('language') ) {
		if ( get_option('html_type') == 'text/html' || $doctype == 'html' )
			$attributes[] = "lang=\"$lang\"";

		if ( get_option('html_type') != 'text/html' || $doctype == 'xhtml' )
			$attributes[] = "xml:lang=\"$lang\"";
	}

	$output = implode(' ', $attributes);
	$output = apply_filters('language_attributes', $output);
	echo $output;
}

/**
 * Retrieve paginated link for archive post pages.
 *
 * Technically, the function can be used to create paginated link list for any
 * area. The 'base' argument is used to reference the url, which will be used to
 * create the paginated links. The 'format' argument is then used for replacing
 * the page number. It is however, most likely and by default, to be used on the
 * archive post pages.
 *
 * The 'type' argument controls format of the returned value. The default is
 * 'plain', which is just a string with the links separated by a newline
 * character. The other possible values are either 'array' or 'list'. The
 * 'array' value will return an array of the paginated link list to offer full
 * control of display. The 'list' value will place all of the paginated links in
 * an unordered HTML list.
 *
 * The 'total' argument is the total amount of pages and is an integer. The
 * 'current' argument is the current page number and is also an integer.
 *
 * An example of the 'base' argument is "http://example.com/all_posts.php%_%"
 * and the '%_%' is required. The '%_%' will be replaced by the contents of in
 * the 'format' argument. An example for the 'format' argument is "?page=%#%"
 * and the '%#%' is also required. The '%#%' will be replaced with the page
 * number.
 *
 * You can include the previous and next links in the list by setting the
 * 'prev_next' argument to true, which it is by default. You can set the
 * previous text, by using the 'prev_text' argument. You can set the next text
 * by setting the 'next_text' argument.
 *
 * If the 'show_all' argument is set to true, then it will show all of the pages
 * instead of a short list of the pages near the current page. By default, the
 * 'show_all' is set to false and controlled by the 'end_size' and 'mid_size'
 * arguments. The 'end_size' argument is how many numbers on either the start
 * and the end list edges, by default is 1. The 'mid_size' argument is how many
 * numbers to either side of current page, but not including current page.
 *
 * It is possible to add query vars to the link by using the 'add_args' argument
 * and see {@link add_query_arg()} for more information.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override defaults.
 * @return array|string String of page links or array of page links.
 */
function paginate_links( $args = '' ) {
	$defaults = array(
		'base' => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
		'total' => 1,
		'current' => 0,
		'show_all' => false,
		'prev_next' => true,
		'prev_text' => __('&laquo; Previous'),
		'next_text' => __('Next &raquo;'),
		'end_size' => 1,
		'mid_size' => 2,
		'type' => 'plain',
		'add_args' => false, // array of query args to add
		'add_fragment' => ''
	);

	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);

	// Who knows what else people pass in $args
	$total = (int) $total;
	if ( $total < 2 )
		return;
	$current  = (int) $current;
	$end_size = 0  < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
	$mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
	$add_args = is_array($add_args) ? $add_args : false;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	if ( $prev_next && $current && 1 < $current ) :
		$link = str_replace('%_%', 2 == $current ? '' : $format, $base);
		$link = str_replace('%#%', $current - 1, $link);
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$link .= $add_fragment;
		$page_links[] = '<a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $prev_text . '</a>';
	endif;
	for ( $n = 1; $n <= $total; $n++ ) :
		$n_display = number_format_i18n($n);
		if ( $n == $current ) :
			$page_links[] = "<span class='page-numbers current'>$n_display</span>";
			$dots = true;
		else :
			if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace('%_%', 1 == $n ? '' : $format, $base);
				$link = str_replace('%#%', $n, $link);
				if ( $add_args )
					$link = add_query_arg( $add_args, $link );
				$link .= $add_fragment;
				$page_links[] = "<a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>$n_display</a>";
				$dots = true;
			elseif ( $dots && !$show_all ) :
				$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
				$dots = false;
			endif;
		endif;
	endfor;
	if ( $prev_next && $current && ( $current < $total || -1 == $total ) ) :
		$link = str_replace('%_%', $format, $base);
		$link = str_replace('%#%', $current + 1, $link);
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$link .= $add_fragment;
		$page_links[] = '<a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $next_text . '</a>';
	endif;
	switch ( $type ) :
		case 'array' :
			return $page_links;
			break;
		case 'list' :
			$r .= "<ul class='page-numbers'>\n\t<li>";
			$r .= join("</li>\n\t<li>", $page_links);
			$r .= "</li>\n</ul>\n";
			break;
		default :
			$r = join("\n", $page_links);
			break;
	endswitch;
	return $r;
}

/**
 * Registers an admin colour scheme css file.
 *
 * Allows a plugin to register a new admin colour scheme. For example:
 * <code>
 * wp_admin_css_color('classic', __('Classic'), admin_url("css/colors-classic.css"),
 * array('#07273E', '#14568A', '#D54E21', '#2683AE'));
 * </code>
 *
 * @since 2.5.0
 *
 * @param string $key The unique key for this theme.
 * @param string $name The name of the theme.
 * @param string $url The url of the css file containing the colour scheme.
 * @param array $colors Optional An array of CSS color definitions which are used to give the user a feel for the theme.
 */
function wp_admin_css_color($key, $name, $url, $colors = array()) {
	global $_wp_admin_css_colors;

	if ( !isset($_wp_admin_css_colors) )
		$_wp_admin_css_colors = array();

	$_wp_admin_css_colors[$key] = (object) array('name' => $name, 'url' => $url, 'colors' => $colors);
}

/**
 * Registers the default Admin color schemes
 *
 * @since 3.0.0
 */
function register_admin_color_schemes() {
	wp_admin_css_color( 'classic', _x( 'Blue', 'admin color scheme' ), admin_url( 'css/colors-classic.css' ),
		array( '#5589aa', '#cfdfe9', '#d1e5ee', '#eff8ff' ) );
	wp_admin_css_color( 'fresh', _x( 'Gray', 'admin color scheme' ), admin_url( 'css/colors-fresh.css' ),
		array( '#7c7976', '#c6c6c6', '#e0e0e0', '#f1f1f1' ) );
}

/**
 * Display the URL of a WordPress admin CSS file.
 *
 * @see WP_Styles::_css_href and its style_loader_src filter.
 *
 * @since 2.3.0
 *
 * @param string $file file relative to wp-admin/ without its ".css" extension.
 */
function wp_admin_css_uri( $file = 'wp-admin' ) {
	if ( defined('WP_INSTALLING') ) {
		$_file = "./$file.css";
	} else {
		$_file = admin_url("$file.css");
	}
	$_file = add_query_arg( 'version', get_bloginfo( 'version' ),  $_file );

	return apply_filters( 'wp_admin_css_uri', $_file, $file );
}

/**
 * Enqueues or directly prints a stylesheet link to the specified CSS file.
 *
 * "Intelligently" decides to enqueue or to print the CSS file. If the
 * 'wp_print_styles' action has *not* yet been called, the CSS file will be
 * enqueued. If the wp_print_styles action *has* been called, the CSS link will
 * be printed. Printing may be forced by passing TRUE as the $force_echo
 * (second) parameter.
 *
 * For backward compatibility with WordPress 2.3 calling method: If the $file
 * (first) parameter does not correspond to a registered CSS file, we assume
 * $file is a file relative to wp-admin/ without its ".css" extension. A
 * stylesheet link to that generated URL is printed.
 *
 * @package WordPress
 * @since 2.3.0
 * @uses $wp_styles WordPress Styles Object
 *
 * @param string $file Optional. Style handle name or file name (without ".css" extension) relative
 * 	 to wp-admin/. Defaults to 'wp-admin'.
 * @param bool $force_echo Optional.  Force the stylesheet link to be printed rather than enqueued.
 */
function wp_admin_css( $file = 'wp-admin', $force_echo = false ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	// For backward compatibility
	$handle = 0 === strpos( $file, 'css/' ) ? substr( $file, 4 ) : $file;

	if ( $wp_styles->query( $handle ) ) {
		if ( $force_echo || did_action( 'wp_print_styles' ) ) // we already printed the style queue.  Print this one immediately
			wp_print_styles( $handle );
		else // Add to style queue
			wp_enqueue_style( $handle );
		return;
	}

	echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . esc_url( wp_admin_css_uri( $file ) ) . "' type='text/css' />\n", $file );
	if ( is_rtl() )
		echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . esc_url( wp_admin_css_uri( "$file-rtl" ) ) . "' type='text/css' />\n", "$file-rtl" );
}

/**
 * Enqueues the default ThickBox js and css.
 *
 * If any of the settings need to be changed, this can be done with another js
 * file similar to media-upload.js and theme-preview.js. That file should
 * require array('thickbox') to ensure it is loaded after.
 *
 * @since 2.5.0
 */
function add_thickbox() {
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );

	if ( is_network_admin() )
		add_action( 'admin_head', '_thickbox_path_admin_subfolder' );
}

/**
 * Display the XHTML generator that is generated on the wp_head hook.
 *
 * @since 2.5.0
 */
function wp_generator() {
	the_generator( apply_filters( 'wp_generator_type', 'xhtml' ) );
}

/**
 * Display the generator XML or Comment for RSS, ATOM, etc.
 *
 * Returns the correct generator type for the requested output format. Allows
 * for a plugin to filter generators overall the the_generator filter.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'the_generator' hook.
 *
 * @param string $type The type of generator to output - (html|xhtml|atom|rss2|rdf|comment|export).
 */
function the_generator( $type ) {
	echo apply_filters('the_generator', get_the_generator($type), $type) . "\n";
}

/**
 * Creates the generator XML or Comment for RSS, ATOM, etc.
 *
 * Returns the correct generator type for the requested output format. Allows
 * for a plugin to filter generators on an individual basis using the
 * 'get_the_generator_{$type}' filter.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'get_the_generator_$type' hook.
 *
 * @param string $type The type of generator to return - (html|xhtml|atom|rss2|rdf|comment|export).
 * @return string The HTML content for the generator.
 */
function get_the_generator( $type = '' ) {
	if ( empty( $type ) ) {

		$current_filter = current_filter();
		if ( empty( $current_filter ) )
			return;

		switch ( $current_filter ) {
			case 'rss2_head' :
			case 'commentsrss2_head' :
				$type = 'rss2';
				break;
			case 'rss_head' :
			case 'opml_head' :
				$type = 'comment';
				break;
			case 'rdf_header' :
				$type = 'rdf';
				break;
			case 'atom_head' :
			case 'comments_atom_head' :
			case 'app_head' :
				$type = 'atom';
				break;
		}
	}

	switch ( $type ) {
		case 'html':
			$gen = '<meta name="generator" content="WordPress ' . get_bloginfo( 'version' ) . '">';
			break;
		case 'xhtml':
			$gen = '<meta name="generator" content="WordPress ' . get_bloginfo( 'version' ) . '" />';
			break;
		case 'atom':
			$gen = '<generator uri="http://wordpress.org/" version="' . get_bloginfo_rss( 'version' ) . '">WordPress</generator>';
			break;
		case 'rss2':
			$gen = '<generator>http://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '</generator>';
			break;
		case 'rdf':
			$gen = '<admin:generatorAgent rdf:resource="http://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '" />';
			break;
		case 'comment':
			$gen = '<!-- generator="WordPress/' . get_bloginfo( 'version' ) . '" -->';
			break;
		case 'export':
			$gen = '<!-- generator="WordPress/' . get_bloginfo_rss('version') . '" created="'. date('Y-m-d H:i') . '" -->';
			break;
	}
	return apply_filters( "get_the_generator_{$type}", $gen, $type );
}

/**
 * Outputs the html checked attribute.
 *
 * Compares the first two arguments and if identical marks as checked
 *
 * @since 1.0.0
 *
 * @param mixed $checked One of the values to compare
 * @param mixed $current (true) The other value to compare if not just true
 * @param bool $echo Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function checked( $checked, $current = true, $echo = true ) {
	return __checked_selected_helper( $checked, $current, $echo, 'checked' );
}

/**
 * Outputs the html selected attribute.
 *
 * Compares the first two arguments and if identical marks as selected
 *
 * @since 1.0.0
 *
 * @param mixed $selected One of the values to compare
 * @param mixed $current (true) The other value to compare if not just true
 * @param bool $echo Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function selected( $selected, $current = true, $echo = true ) {
	return __checked_selected_helper( $selected, $current, $echo, 'selected' );
}

/**
 * Outputs the html disabled attribute.
 *
 * Compares the first two arguments and if identical marks as disabled
 *
 * @since 3.0.0
 *
 * @param mixed $disabled One of the values to compare
 * @param mixed $current (true) The other value to compare if not just true
 * @param bool $echo Whether to echo or just return the string
 * @return string html attribute or empty string
 */
function disabled( $disabled, $current = true, $echo = true ) {
	return __checked_selected_helper( $disabled, $current, $echo, 'disabled' );
}

/**
 * Private helper function for checked, selected, and disabled.
 *
 * Compares the first two arguments and if identical marks as $type
 *
 * @since 2.8.0
 * @access private
 *
 * @param any $helper One of the values to compare
 * @param any $current (true) The other value to compare if not just true
 * @param bool $echo Whether to echo or just return the string
 * @param string $type The type of checked|selected|disabled we are doing
 * @return string html attribute or empty string
 */
function __checked_selected_helper( $helper, $current, $echo, $type ) {
	if ( (string) $helper === (string) $current )
		$result = " $type='$type'";
	else
		$result = '';

	if ( $echo )
		echo $result;

	return $result;
}

?>
