<?php
/**
 * General template tags that can go anywhere in a template.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Loads header template.
 *
 * Includes the header template for a theme or if a name is specified then a
 * specialized header will be included.
 *
 * For the parameter, if the file is called "header-special.php" then specify
 * "special".
 *
 * @since 1.5.0
 * @since 5.5.0 A return value was added.
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @param string $name The name of the specialized header.
 * @param array  $args Optional. Additional arguments passed to the header template.
 *                     Default empty array.
 * @return void|false Void on success, false if the template does not exist.
 */
function get_header( $name = null, $args = array() ) {
	/**
	 * Fires before the header template file is loaded.
	 *
	 * @since 2.1.0
	 * @since 2.8.0 The `$name` parameter was added.
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string|null $name Name of the specific header file to use. Null for the default header.
	 * @param array       $args Additional arguments passed to the header template.
	 */
	do_action( 'get_header', $name, $args );

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
	}

	$templates[] = 'header.php';

	if ( ! locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

/**
 * Loads footer template.
 *
 * Includes the footer template for a theme or if a name is specified then a
 * specialized footer will be included.
 *
 * For the parameter, if the file is called "footer-special.php" then specify
 * "special".
 *
 * @since 1.5.0
 * @since 5.5.0 A return value was added.
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @param string $name The name of the specialized footer.
 * @param array  $args Optional. Additional arguments passed to the footer template.
 *                     Default empty array.
 * @return void|false Void on success, false if the template does not exist.
 */
function get_footer( $name = null, $args = array() ) {
	/**
	 * Fires before the footer template file is loaded.
	 *
	 * @since 2.1.0
	 * @since 2.8.0 The `$name` parameter was added.
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string|null $name Name of the specific footer file to use. Null for the default footer.
	 * @param array       $args Additional arguments passed to the footer template.
	 */
	do_action( 'get_footer', $name, $args );

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
	}

	$templates[] = 'footer.php';

	if ( ! locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

/**
 * Loads sidebar template.
 *
 * Includes the sidebar template for a theme or if a name is specified then a
 * specialized sidebar will be included.
 *
 * For the parameter, if the file is called "sidebar-special.php" then specify
 * "special".
 *
 * @since 1.5.0
 * @since 5.5.0 A return value was added.
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @param string $name The name of the specialized sidebar.
 * @param array  $args Optional. Additional arguments passed to the sidebar template.
 *                     Default empty array.
 * @return void|false Void on success, false if the template does not exist.
 */
function get_sidebar( $name = null, $args = array() ) {
	/**
	 * Fires before the sidebar template file is loaded.
	 *
	 * @since 2.2.0
	 * @since 2.8.0 The `$name` parameter was added.
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string|null $name Name of the specific sidebar file to use. Null for the default sidebar.
	 * @param array       $args Additional arguments passed to the sidebar template.
	 */
	do_action( 'get_sidebar', $name, $args );

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "sidebar-{$name}.php";
	}

	$templates[] = 'sidebar.php';

	if ( ! locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

/**
 * Loads a template part into a template.
 *
 * Provides a simple mechanism for child themes to overload reusable sections of code
 * in the theme.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialized part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * The template is included using require, not require_once, so you may include the
 * same template part multiple times.
 *
 * For the $name parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 *
 * @since 3.0.0
 * @since 5.5.0 A return value was added.
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialized template.
 * @param array  $args Optional. Additional arguments passed to the template.
 *                     Default empty array.
 * @return void|false Void on success, false if the template does not exist.
 */
function get_template_part( $slug, $name = null, $args = array() ) {
	/**
	 * Fires before the specified template part file is loaded.
	 *
	 * The dynamic portion of the hook name, `$slug`, refers to the slug name
	 * for the generic template part.
	 *
	 * @since 3.0.0
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string      $slug The slug name for the generic template.
	 * @param string|null $name The name of the specialized template.
	 * @param array       $args Additional arguments passed to the template.
	 */
	do_action( "get_template_part_{$slug}", $slug, $name, $args );

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	/**
	 * Fires before an attempt is made to locate and load a template part.
	 *
	 * @since 5.2.0
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string   $slug      The slug name for the generic template.
	 * @param string   $name      The name of the specialized template.
	 * @param string[] $templates Array of template files to search for, in order.
	 * @param array    $args      Additional arguments passed to the template.
	 */
	do_action( 'get_template_part', $slug, $name, $templates, $args );

	if ( ! locate_template( $templates, true, false, $args ) ) {
		return false;
	}
}

/**
 * Displays search form.
 *
 * Will first attempt to locate the searchform.php file in either the child or
 * the parent, then load it. If it doesn't exist, then the default search form
 * will be displayed. The default search form is HTML, which will be displayed.
 * There is a filter applied to the search form HTML in order to edit or replace
 * it. The filter is {@see 'get_search_form'}.
 *
 * This function is primarily used by themes which want to hardcode the search
 * form into the sidebar and also by the search widget in WordPress.
 *
 * There is also an action that is called whenever the function is run called,
 * {@see 'pre_get_search_form'}. This can be useful for outputting JavaScript that the
 * search relies on or various formatting that applies to the beginning of the
 * search. To give a few examples of what it can be used for.
 *
 * @since 2.7.0
 * @since 5.2.0 The `$args` array parameter was added in place of an `$echo` boolean flag.
 *
 * @param array $args {
 *     Optional. Array of display arguments.
 *
 *     @type bool   $echo       Whether to echo or return the form. Default true.
 *     @type string $aria_label ARIA label for the search form. Useful to distinguish
 *                              multiple search forms on the same page and improve
 *                              accessibility. Default empty.
 * }
 * @return void|string Void if 'echo' argument is true, search form HTML if 'echo' is false.
 */
function get_search_form( $args = array() ) {
	/**
	 * Fires before the search form is retrieved, at the start of get_search_form().
	 *
	 * @since 2.7.0 as 'get_search_form' action.
	 * @since 3.6.0
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @link https://core.trac.wordpress.org/ticket/19321
	 *
	 * @param array $args The array of arguments for building the search form.
	 *                    See get_search_form() for information on accepted arguments.
	 */
	do_action( 'pre_get_search_form', $args );

	$echo = true;

	if ( ! is_array( $args ) ) {
		/*
		 * Back compat: to ensure previous uses of get_search_form() continue to
		 * function as expected, we handle a value for the boolean $echo param removed
		 * in 5.2.0. Then we deal with the $args array and cast its defaults.
		 */
		$echo = (bool) $args;

		// Set an empty array and allow default arguments to take over.
		$args = array();
	}

	// Defaults are to echo and to output no custom label on the form.
	$defaults = array(
		'echo'       => $echo,
		'aria_label' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the array of arguments used when generating the search form.
	 *
	 * @since 5.2.0
	 *
	 * @param array $args The array of arguments for building the search form.
	 *                    See get_search_form() for information on accepted arguments.
	 */
	$args = apply_filters( 'search_form_args', $args );

	// Ensure that the filtered arguments contain all required default values.
	$args = array_merge( $defaults, $args );

	$format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';

	/**
	 * Filters the HTML format of the search form.
	 *
	 * @since 3.6.0
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string $format The type of markup to use in the search form.
	 *                       Accepts 'html5', 'xhtml'.
	 * @param array  $args   The array of arguments for building the search form.
	 *                       See get_search_form() for information on accepted arguments.
	 */
	$format = apply_filters( 'search_form_format', $format, $args );

	$search_form_template = locate_template( 'searchform.php' );

	if ( '' !== $search_form_template ) {
		ob_start();
		require $search_form_template;
		$form = ob_get_clean();
	} else {
		// Build a string containing an aria-label to use for the search form.
		if ( $args['aria_label'] ) {
			$aria_label = 'aria-label="' . esc_attr( $args['aria_label'] ) . '" ';
		} else {
			/*
			 * If there's no custom aria-label, we can set a default here. At the
			 * moment it's empty as there's uncertainty about what the default should be.
			 */
			$aria_label = '';
		}

		if ( 'html5' === $format ) {
			$form = '<form role="search" ' . $aria_label . 'method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
				<label>
					<span class="screen-reader-text">' .
					/* translators: Hidden accessibility text. */
					_x( 'Search for:', 'label' ) .
					'</span>
					<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder' ) . '" value="' . get_search_query() . '" name="s" />
				</label>
				<input type="submit" class="search-submit" value="' . esc_attr_x( 'Search', 'submit button' ) . '" />
			</form>';
		} else {
			$form = '<form role="search" ' . $aria_label . 'method="get" id="searchform" class="searchform" action="' . esc_url( home_url( '/' ) ) . '">
				<div>
					<label class="screen-reader-text" for="s">' .
					/* translators: Hidden accessibility text. */
					_x( 'Search for:', 'label' ) .
					'</label>
					<input type="text" value="' . get_search_query() . '" name="s" id="s" />
					<input type="submit" id="searchsubmit" value="' . esc_attr_x( 'Search', 'submit button' ) . '" />
				</div>
			</form>';
		}
	}

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @since 2.7.0
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $args The array of arguments for building the search form.
	 *                     See get_search_form() for information on accepted arguments.
	 */
	$result = apply_filters( 'get_search_form', $form, $args );

	if ( null === $result ) {
		$result = $form;
	}

	if ( $args['echo'] ) {
		echo $result;
	} else {
		return $result;
	}
}

/**
 * Displays the Log In/Out link.
 *
 * Displays a link, which allows users to navigate to the Log In page to log in
 * or log out depending on whether they are currently logged in.
 *
 * @since 1.5.0
 *
 * @param string $redirect Optional path to redirect to on login/logout.
 * @param bool   $display  Default to echo and not return the link.
 * @return void|string Void if `$display` argument is true, log in/out link if `$display` is false.
 */
function wp_loginout( $redirect = '', $display = true ) {
	if ( ! is_user_logged_in() ) {
		$link = '<a href="' . esc_url( wp_login_url( $redirect ) ) . '">' . __( 'Log in' ) . '</a>';
	} else {
		$link = '<a href="' . esc_url( wp_logout_url( $redirect ) ) . '">' . __( 'Log out' ) . '</a>';
	}

	if ( $display ) {
		/**
		 * Filters the HTML output for the Log In/Log Out link.
		 *
		 * @since 1.5.0
		 *
		 * @param string $link The HTML link content.
		 */
		echo apply_filters( 'loginout', $link );
	} else {
		/** This filter is documented in wp-includes/general-template.php */
		return apply_filters( 'loginout', $link );
	}
}

/**
 * Retrieves the logout URL.
 *
 * Returns the URL that allows the user to log out of the site.
 *
 * @since 2.7.0
 *
 * @param string $redirect Path to redirect to on logout.
 * @return string The logout URL. Note: HTML-encoded via esc_html() in wp_nonce_url().
 */
function wp_logout_url( $redirect = '' ) {
	$args = array();
	if ( ! empty( $redirect ) ) {
		$args['redirect_to'] = urlencode( $redirect );
	}

	$logout_url = add_query_arg( $args, site_url( 'wp-login.php?action=logout', 'login' ) );
	$logout_url = wp_nonce_url( $logout_url, 'log-out' );

	/**
	 * Filters the logout URL.
	 *
	 * @since 2.8.0
	 *
	 * @param string $logout_url The HTML-encoded logout URL.
	 * @param string $redirect   Path to redirect to on logout.
	 */
	return apply_filters( 'logout_url', $logout_url, $redirect );
}

/**
 * Retrieves the login URL.
 *
 * @since 2.7.0
 *
 * @param string $redirect     Path to redirect to on log in.
 * @param bool   $force_reauth Whether to force reauthorization, even if a cookie is present.
 *                             Default false.
 * @return string The login URL. Not HTML-encoded.
 */
function wp_login_url( $redirect = '', $force_reauth = false ) {
	$login_url = site_url( 'wp-login.php', 'login' );

	if ( ! empty( $redirect ) ) {
		$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
	}

	if ( $force_reauth ) {
		$login_url = add_query_arg( 'reauth', '1', $login_url );
	}

	/**
	 * Filters the login URL.
	 *
	 * @since 2.8.0
	 * @since 4.2.0 The `$force_reauth` parameter was added.
	 *
	 * @param string $login_url    The login URL. Not HTML-encoded.
	 * @param string $redirect     The path to redirect to on login, if supplied.
	 * @param bool   $force_reauth Whether to force reauthorization, even if a cookie is present.
	 */
	return apply_filters( 'login_url', $login_url, $redirect, $force_reauth );
}

/**
 * Returns the URL that allows the user to register on the site.
 *
 * @since 3.6.0
 *
 * @return string User registration URL.
 */
function wp_registration_url() {
	/**
	 * Filters the user registration URL.
	 *
	 * @since 3.6.0
	 *
	 * @param string $register The user registration URL.
	 */
	return apply_filters( 'register_url', site_url( 'wp-login.php?action=register', 'login' ) );
}

/**
 * Provides a simple login form for use anywhere within WordPress.
 *
 * The login form HTML is echoed by default. Pass a false value for `$echo` to return it instead.
 *
 * @since 3.0.0
 *
 * @param array $args {
 *     Optional. Array of options to control the form output. Default empty array.
 *
 *     @type bool   $echo           Whether to display the login form or return the form HTML code.
 *                                  Default true (echo).
 *     @type string $redirect       URL to redirect to. Must be absolute, as in "https://example.com/mypage/".
 *                                  Default is to redirect back to the request URI.
 *     @type string $form_id        ID attribute value for the form. Default 'loginform'.
 *     @type string $label_username Label for the username or email address field. Default 'Username or Email Address'.
 *     @type string $label_password Label for the password field. Default 'Password'.
 *     @type string $label_remember Label for the remember field. Default 'Remember Me'.
 *     @type string $label_log_in   Label for the submit button. Default 'Log In'.
 *     @type string $id_username    ID attribute value for the username field. Default 'user_login'.
 *     @type string $id_password    ID attribute value for the password field. Default 'user_pass'.
 *     @type string $id_remember    ID attribute value for the remember field. Default 'rememberme'.
 *     @type string $id_submit      ID attribute value for the submit button. Default 'wp-submit'.
 *     @type bool   $remember       Whether to display the "rememberme" checkbox in the form.
 *     @type string $value_username Default value for the username field. Default empty.
 *     @type bool   $value_remember Whether the "Remember Me" checkbox should be checked by default.
 *                                  Default false (unchecked).
 *
 * }
 * @return void|string Void if 'echo' argument is true, login form HTML if 'echo' is false.
 */
function wp_login_form( $args = array() ) {
	$defaults = array(
		'echo'           => true,
		// Default 'redirect' value takes the user back to the request URI.
		'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'form_id'        => 'loginform',
		'label_username' => __( 'Username or Email Address' ),
		'label_password' => __( 'Password' ),
		'label_remember' => __( 'Remember Me' ),
		'label_log_in'   => __( 'Log In' ),
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'remember'       => true,
		'value_username' => '',
		// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
		'value_remember' => false,
	);

	/**
	 * Filters the default login form output arguments.
	 *
	 * @since 3.0.0
	 *
	 * @see wp_login_form()
	 *
	 * @param array $defaults An array of default login form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

	/**
	 * Filters content to display at the top of the login form.
	 *
	 * The filter evaluates just following the opening form tag element.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content Content to display. Default empty.
	 * @param array  $args    Array of login form arguments.
	 */
	$login_form_top = apply_filters( 'login_form_top', '', $args );

	/**
	 * Filters content to display in the middle of the login form.
	 *
	 * The filter evaluates just following the location where the 'login-password'
	 * field is displayed.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content Content to display. Default empty.
	 * @param array  $args    Array of login form arguments.
	 */
	$login_form_middle = apply_filters( 'login_form_middle', '', $args );

	/**
	 * Filters content to display at the bottom of the login form.
	 *
	 * The filter evaluates just preceding the closing form tag element.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content Content to display. Default empty.
	 * @param array  $args    Array of login form arguments.
	 */
	$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

	$form =
		sprintf(
			'<form name="%1$s" id="%1$s" action="%2$s" method="post">',
			esc_attr( $args['form_id'] ),
			esc_url( site_url( 'wp-login.php', 'login_post' ) )
		) .
		$login_form_top .
		sprintf(
			'<p class="login-username">
				<label for="%1$s">%2$s</label>
				<input type="text" name="log" id="%1$s" autocomplete="username" class="input" value="%3$s" size="20" />
			</p>',
			esc_attr( $args['id_username'] ),
			esc_html( $args['label_username'] ),
			esc_attr( $args['value_username'] )
		) .
		sprintf(
			'<p class="login-password">
				<label for="%1$s">%2$s</label>
				<input type="password" name="pwd" id="%1$s" autocomplete="current-password" spellcheck="false" class="input" value="" size="20" />
			</p>',
			esc_attr( $args['id_password'] ),
			esc_html( $args['label_password'] )
		) .
		$login_form_middle .
		( $args['remember'] ?
			sprintf(
				'<p class="login-remember"><label><input name="rememberme" type="checkbox" id="%1$s" value="forever"%2$s /> %3$s</label></p>',
				esc_attr( $args['id_remember'] ),
				( $args['value_remember'] ? ' checked="checked"' : '' ),
				esc_html( $args['label_remember'] )
			) : ''
		) .
		sprintf(
			'<p class="login-submit">
				<input type="submit" name="wp-submit" id="%1$s" class="button button-primary" value="%2$s" />
				<input type="hidden" name="redirect_to" value="%3$s" />
			</p>',
			esc_attr( $args['id_submit'] ),
			esc_attr( $args['label_log_in'] ),
			esc_url( $args['redirect'] )
		) .
		$login_form_bottom .
		'</form>';

	if ( $args['echo'] ) {
		echo $form;
	} else {
		return $form;
	}
}

/**
 * Returns the URL that allows the user to reset the lost password.
 *
 * @since 2.8.0
 *
 * @param string $redirect Path to redirect to on login.
 * @return string Lost password URL.
 */
function wp_lostpassword_url( $redirect = '' ) {
	$args = array(
		'action' => 'lostpassword',
	);

	if ( ! empty( $redirect ) ) {
		$args['redirect_to'] = urlencode( $redirect );
	}

	if ( is_multisite() ) {
		$blog_details  = get_blog_details();
		$wp_login_path = $blog_details->path . 'wp-login.php';
	} else {
		$wp_login_path = 'wp-login.php';
	}

	$lostpassword_url = add_query_arg( $args, network_site_url( $wp_login_path, 'login' ) );

	/**
	 * Filters the Lost Password URL.
	 *
	 * @since 2.8.0
	 *
	 * @param string $lostpassword_url The lost password page URL.
	 * @param string $redirect         The path to redirect to on login.
	 */
	return apply_filters( 'lostpassword_url', $lostpassword_url, $redirect );
}

/**
 * Displays the Registration or Admin link.
 *
 * Display a link which allows the user to navigate to the registration page if
 * not logged in and registration is enabled or to the dashboard if logged in.
 *
 * @since 1.5.0
 *
 * @param string $before  Text to output before the link. Default `<li>`.
 * @param string $after   Text to output after the link. Default `</li>`.
 * @param bool   $display Default to echo and not return the link.
 * @return void|string Void if `$display` argument is true, registration or admin link
 *                     if `$display` is false.
 */
function wp_register( $before = '<li>', $after = '</li>', $display = true ) {
	if ( ! is_user_logged_in() ) {
		if ( get_option( 'users_can_register' ) ) {
			$link = $before . '<a href="' . esc_url( wp_registration_url() ) . '">' . __( 'Register' ) . '</a>' . $after;
		} else {
			$link = '';
		}
	} elseif ( current_user_can( 'read' ) ) {
		$link = $before . '<a href="' . admin_url() . '">' . __( 'Site Admin' ) . '</a>' . $after;
	} else {
		$link = '';
	}

	/**
	 * Filters the HTML link to the Registration or Admin page.
	 *
	 * Users are sent to the admin page if logged-in, or the registration page
	 * if enabled and logged-out.
	 *
	 * @since 1.5.0
	 *
	 * @param string $link The HTML code for the link to the Registration or Admin page.
	 */
	$link = apply_filters( 'register', $link );

	if ( $display ) {
		echo $link;
	} else {
		return $link;
	}
}

/**
 * Theme container function for the 'wp_meta' action.
 *
 * The {@see 'wp_meta'} action can have several purposes, depending on how you use it,
 * but one purpose might have been to allow for theme switching.
 *
 * @since 1.5.0
 *
 * @link https://core.trac.wordpress.org/ticket/1458 Explanation of 'wp_meta' action.
 */
function wp_meta() {
	/**
	 * Fires before displaying echoed content in the sidebar.
	 *
	 * @since 1.5.0
	 */
	do_action( 'wp_meta' );
}

/**
 * Displays information about the current site.
 *
 * @since 0.71
 *
 * @see get_bloginfo() For possible `$show` values
 *
 * @param string $show Optional. Site information to display. Default empty.
 */
function bloginfo( $show = '' ) {
	echo get_bloginfo( $show, 'display' );
}

/**
 * Retrieves information about the current site.
 *
 * Possible values for `$show` include:
 *
 * - 'name' - Site title (set in Settings > General)
 * - 'description' - Site tagline (set in Settings > General)
 * - 'wpurl' - The WordPress address (URL) (set in Settings > General)
 * - 'url' - The Site address (URL) (set in Settings > General)
 * - 'admin_email' - Admin email (set in Settings > General)
 * - 'charset' - The "Encoding for pages and feeds"  (set in Settings > Reading)
 * - 'version' - The current WordPress version
 * - 'html_type' - The Content-Type (default: "text/html"). Themes and plugins
 *   can override the default value using the {@see 'pre_option_html_type'} filter
 * - 'text_direction' - The text direction determined by the site's language. is_rtl()
 *   should be used instead
 * - 'language' - Language code for the current site
 * - 'stylesheet_url' - URL to the stylesheet for the active theme. An active child theme
 *   will take precedence over this value
 * - 'stylesheet_directory' - Directory path for the active theme.  An active child theme
 *   will take precedence over this value
 * - 'template_url' / 'template_directory' - URL of the active theme's directory. An active
 *   child theme will NOT take precedence over this value
 * - 'pingback_url' - The pingback XML-RPC file URL (xmlrpc.php)
 * - 'atom_url' - The Atom feed URL (/feed/atom)
 * - 'rdf_url' - The RDF/RSS 1.0 feed URL (/feed/rdf)
 * - 'rss_url' - The RSS 0.92 feed URL (/feed/rss)
 * - 'rss2_url' - The RSS 2.0 feed URL (/feed)
 * - 'comments_atom_url' - The comments Atom feed URL (/comments/feed)
 * - 'comments_rss2_url' - The comments RSS 2.0 feed URL (/comments/feed)
 *
 * Some `$show` values are deprecated and will be removed in future versions.
 * These options will trigger the _deprecated_argument() function.
 *
 * Deprecated arguments include:
 *
 * - 'siteurl' - Use 'url' instead
 * - 'home' - Use 'url' instead
 *
 * @since 0.71
 *
 * @global string $wp_version The WordPress version string.
 *
 * @param string $show   Optional. Site info to retrieve. Default empty (site name).
 * @param string $filter Optional. How to filter what is retrieved. Default 'raw'.
 * @return string Mostly string values, might be empty.
 */
function get_bloginfo( $show = '', $filter = 'raw' ) {
	switch ( $show ) {
		case 'home':    // Deprecated.
		case 'siteurl': // Deprecated.
			_deprecated_argument(
				__FUNCTION__,
				'2.2.0',
				sprintf(
					/* translators: 1: 'siteurl'/'home' argument, 2: bloginfo() function name, 3: 'url' argument. */
					__( 'The %1$s option is deprecated for the family of %2$s functions. Use the %3$s option instead.' ),
					'<code>' . $show . '</code>',
					'<code>bloginfo()</code>',
					'<code>url</code>'
				)
			);
			// Intentional fall-through to be handled by the 'url' case.
		case 'url':
			$output = home_url();
			break;
		case 'wpurl':
			$output = site_url();
			break;
		case 'description':
			$output = get_option( 'blogdescription' );
			break;
		case 'rdf_url':
			$output = get_feed_link( 'rdf' );
			break;
		case 'rss_url':
			$output = get_feed_link( 'rss' );
			break;
		case 'rss2_url':
			$output = get_feed_link( 'rss2' );
			break;
		case 'atom_url':
			$output = get_feed_link( 'atom' );
			break;
		case 'comments_atom_url':
			$output = get_feed_link( 'comments_atom' );
			break;
		case 'comments_rss2_url':
			$output = get_feed_link( 'comments_rss2' );
			break;
		case 'pingback_url':
			$output = site_url( 'xmlrpc.php' );
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
			$output = get_option( 'admin_email' );
			break;
		case 'charset':
			$output = get_option( 'blog_charset' );
			if ( '' === $output ) {
				$output = 'UTF-8';
			}
			break;
		case 'html_type':
			$output = get_option( 'html_type' );
			break;
		case 'version':
			global $wp_version;
			$output = $wp_version;
			break;
		case 'language':
			/*
			 * translators: Translate this to the correct language tag for your locale,
			 * see https://www.w3.org/International/articles/language-tags/ for reference.
			 * Do not translate into your own language.
			 */
			$output = __( 'html_lang_attribute' );
			if ( 'html_lang_attribute' === $output || preg_match( '/[^a-zA-Z0-9-]/', $output ) ) {
				$output = determine_locale();
				$output = str_replace( '_', '-', $output );
			}
			break;
		case 'text_direction':
			_deprecated_argument(
				__FUNCTION__,
				'2.2.0',
				sprintf(
					/* translators: 1: 'text_direction' argument, 2: bloginfo() function name, 3: is_rtl() function name. */
					__( 'The %1$s option is deprecated for the family of %2$s functions. Use the %3$s function instead.' ),
					'<code>' . $show . '</code>',
					'<code>bloginfo()</code>',
					'<code>is_rtl()</code>'
				)
			);
			if ( function_exists( 'is_rtl' ) ) {
				$output = is_rtl() ? 'rtl' : 'ltr';
			} else {
				$output = 'ltr';
			}
			break;
		case 'name':
		default:
			$output = get_option( 'blogname' );
			break;
	}

	$url = true;
	if ( strpos( $show, 'url' ) === false &&
		strpos( $show, 'directory' ) === false &&
		strpos( $show, 'home' ) === false ) {
		$url = false;
	}

	if ( 'display' === $filter ) {
		if ( $url ) {
			/**
			 * Filters the URL returned by get_bloginfo().
			 *
			 * @since 2.0.5
			 *
			 * @param string $output The URL returned by bloginfo().
			 * @param string $show   Type of information requested.
			 */
			$output = apply_filters( 'bloginfo_url', $output, $show );
		} else {
			/**
			 * Filters the site information returned by get_bloginfo().
			 *
			 * @since 0.71
			 *
			 * @param mixed  $output The requested non-URL site information.
			 * @param string $show   Type of information requested.
			 */
			$output = apply_filters( 'bloginfo', $output, $show );
		}
	}

	return $output;
}

/**
 * Returns the Site Icon URL.
 *
 * @since 4.3.0
 *
 * @param int    $size    Optional. Size of the site icon. Default 512 (pixels).
 * @param string $url     Optional. Fallback url if no site icon is found. Default empty.
 * @param int    $blog_id Optional. ID of the blog to get the site icon for. Default current blog.
 * @return string Site Icon URL.
 */
function get_site_icon_url( $size = 512, $url = '', $blog_id = 0 ) {
	$switched_blog = false;

	if ( is_multisite() && ! empty( $blog_id ) && get_current_blog_id() !== (int) $blog_id ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}

	$site_icon_id = get_option( 'site_icon' );

	if ( $site_icon_id ) {
		if ( $size >= 512 ) {
			$size_data = 'full';
		} else {
			$size_data = array( $size, $size );
		}
		$url = wp_get_attachment_image_url( $site_icon_id, $size_data );
	}

	if ( $switched_blog ) {
		restore_current_blog();
	}

	/**
	 * Filters the site icon URL.
	 *
	 * @since 4.4.0
	 *
	 * @param string $url     Site icon URL.
	 * @param int    $size    Size of the site icon.
	 * @param int    $blog_id ID of the blog to get the site icon for.
	 */
	return apply_filters( 'get_site_icon_url', $url, $size, $blog_id );
}

/**
 * Displays the Site Icon URL.
 *
 * @since 4.3.0
 *
 * @param int    $size    Optional. Size of the site icon. Default 512 (pixels).
 * @param string $url     Optional. Fallback url if no site icon is found. Default empty.
 * @param int    $blog_id Optional. ID of the blog to get the site icon for. Default current blog.
 */
function site_icon_url( $size = 512, $url = '', $blog_id = 0 ) {
	echo esc_url( get_site_icon_url( $size, $url, $blog_id ) );
}

/**
 * Determines whether the site has a Site Icon.
 *
 * @since 4.3.0
 *
 * @param int $blog_id Optional. ID of the blog in question. Default current blog.
 * @return bool Whether the site has a site icon or not.
 */
function has_site_icon( $blog_id = 0 ) {
	return (bool) get_site_icon_url( 512, '', $blog_id );
}

/**
 * Determines whether the site has a custom logo.
 *
 * @since 4.5.0
 *
 * @param int $blog_id Optional. ID of the blog in question. Default is the ID of the current blog.
 * @return bool Whether the site has a custom logo or not.
 */
function has_custom_logo( $blog_id = 0 ) {
	$switched_blog = false;

	if ( is_multisite() && ! empty( $blog_id ) && get_current_blog_id() !== (int) $blog_id ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $switched_blog ) {
		restore_current_blog();
	}

	return (bool) $custom_logo_id;
}

/**
 * Returns a custom logo, linked to home unless the theme supports removing the link on the home page.
 *
 * @since 4.5.0
 * @since 5.5.0 Added option to remove the link on the home page with `unlink-homepage-logo` theme support
 *              for the `custom-logo` theme feature.
 * @since 5.5.1 Disabled lazy-loading by default.
 *
 * @param int $blog_id Optional. ID of the blog in question. Default is the ID of the current blog.
 * @return string Custom logo markup.
 */
function get_custom_logo( $blog_id = 0 ) {
	$html          = '';
	$switched_blog = false;

	if ( is_multisite() && ! empty( $blog_id ) && get_current_blog_id() !== (int) $blog_id ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );

	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class'   => 'custom-logo',
			'loading' => false,
		);

		$unlink_homepage_logo = (bool) get_theme_support( 'custom-logo', 'unlink-homepage-logo' );

		if ( $unlink_homepage_logo && is_front_page() && ! is_paged() ) {
			/*
			 * If on the home page, set the logo alt attribute to an empty string,
			 * as the image is decorative and doesn't need its purpose to be described.
			 */
			$custom_logo_attr['alt'] = '';
		} else {
			/*
			 * If the logo alt attribute is empty, get the site title and explicitly pass it
			 * to the attributes used by wp_get_attachment_image().
			 */
			$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
			if ( empty( $image_alt ) ) {
				$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
			}
		}

		/**
		 * Filters the list of custom logo image attributes.
		 *
		 * @since 5.5.0
		 *
		 * @param array $custom_logo_attr Custom logo image attributes.
		 * @param int   $custom_logo_id   Custom logo attachment ID.
		 * @param int   $blog_id          ID of the blog to get the custom logo for.
		 */
		$custom_logo_attr = apply_filters( 'get_custom_logo_image_attributes', $custom_logo_attr, $custom_logo_id, $blog_id );

		/*
		 * If the alt attribute is not empty, there's no need to explicitly pass it
		 * because wp_get_attachment_image() already adds the alt attribute.
		 */
		$image = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );

		if ( $unlink_homepage_logo && is_front_page() && ! is_paged() ) {
			// If on the home page, don't link the logo to home.
			$html = sprintf(
				'<span class="custom-logo-link">%1$s</span>',
				$image
			);
		} else {
			$aria_current = is_front_page() && ! is_paged() ? ' aria-current="page"' : '';

			$html = sprintf(
				'<a href="%1$s" class="custom-logo-link" rel="home"%2$s>%3$s</a>',
				esc_url( home_url( '/' ) ),
				$aria_current,
				$image
			);
		}
	} elseif ( is_customize_preview() ) {
		// If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
		$html = sprintf(
			'<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo" alt="" /></a>',
			esc_url( home_url( '/' ) )
		);
	}

	if ( $switched_blog ) {
		restore_current_blog();
	}

	/**
	 * Filters the custom logo output.
	 *
	 * @since 4.5.0
	 * @since 4.6.0 Added the `$blog_id` parameter.
	 *
	 * @param string $html    Custom logo HTML output.
	 * @param int    $blog_id ID of the blog to get the custom logo for.
	 */
	return apply_filters( 'get_custom_logo', $html, $blog_id );
}

/**
 * Displays a custom logo, linked to home unless the theme supports removing the link on the home page.
 *
 * @since 4.5.0
 *
 * @param int $blog_id Optional. ID of the blog in question. Default is the ID of the current blog.
 */
function the_custom_logo( $blog_id = 0 ) {
	echo get_custom_logo( $blog_id );
}

/**
 * Returns document title for the current page.
 *
 * @since 4.4.0
 *
 * @global int $page  Page number of a single post.
 * @global int $paged Page number of a list of posts.
 *
 * @return string Tag with the document title.
 */
function wp_get_document_title() {

	/**
	 * Filters the document title before it is generated.
	 *
	 * Passing a non-empty value will short-circuit wp_get_document_title(),
	 * returning that value instead.
	 *
	 * @since 4.4.0
	 *
	 * @param string $title The document title. Default empty string.
	 */
	$title = apply_filters( 'pre_get_document_title', '' );
	if ( ! empty( $title ) ) {
		return $title;
	}

	global $page, $paged;

	$title = array(
		'title' => '',
	);

	// If it's a 404 page, use a "Page not found" title.
	if ( is_404() ) {
		$title['title'] = __( 'Page not found' );

		// If it's a search, use a dynamic search results title.
	} elseif ( is_search() ) {
		/* translators: %s: Search query. */
		$title['title'] = sprintf( __( 'Search Results for &#8220;%s&#8221;' ), get_search_query() );

		// If on the front page, use the site title.
	} elseif ( is_front_page() ) {
		$title['title'] = get_bloginfo( 'name', 'display' );

		// If on a post type archive, use the post type archive title.
	} elseif ( is_post_type_archive() ) {
		$title['title'] = post_type_archive_title( '', false );

		// If on a taxonomy archive, use the term title.
	} elseif ( is_tax() ) {
		$title['title'] = single_term_title( '', false );

		/*
		* If we're on the blog page that is not the homepage
		* or a single post of any post type, use the post title.
		*/
	} elseif ( is_home() || is_singular() ) {
		$title['title'] = single_post_title( '', false );

		// If on a category or tag archive, use the term title.
	} elseif ( is_category() || is_tag() ) {
		$title['title'] = single_term_title( '', false );

		// If on an author archive, use the author's display name.
	} elseif ( is_author() && get_queried_object() ) {
		$author         = get_queried_object();
		$title['title'] = $author->display_name;

		// If it's a date archive, use the date as the title.
	} elseif ( is_year() ) {
		$title['title'] = get_the_date( _x( 'Y', 'yearly archives date format' ) );

	} elseif ( is_month() ) {
		$title['title'] = get_the_date( _x( 'F Y', 'monthly archives date format' ) );

	} elseif ( is_day() ) {
		$title['title'] = get_the_date();
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		/* translators: %s: Page number. */
		$title['page'] = sprintf( __( 'Page %s' ), max( $paged, $page ) );
	}

	// Append the description or site title to give context.
	if ( is_front_page() ) {
		$title['tagline'] = get_bloginfo( 'description', 'display' );
	} else {
		$title['site'] = get_bloginfo( 'name', 'display' );
	}

	/**
	 * Filters the separator for the document title.
	 *
	 * @since 4.4.0
	 *
	 * @param string $sep Document title separator. Default '-'.
	 */
	$sep = apply_filters( 'document_title_separator', '-' );

	/**
	 * Filters the parts of the document title.
	 *
	 * @since 4.4.0
	 *
	 * @param array $title {
	 *     The document title parts.
	 *
	 *     @type string $title   Title of the viewed page.
	 *     @type string $page    Optional. Page number if paginated.
	 *     @type string $tagline Optional. Site description when on home page.
	 *     @type string $site    Optional. Site title when not on home page.
	 * }
	 */
	$title = apply_filters( 'document_title_parts', $title );

	$title = implode( " $sep ", array_filter( $title ) );

	/**
	 * Filters the document title.
	 *
	 * @since 5.8.0
	 *
	 * @param string $title Document title.
	 */
	$title = apply_filters( 'document_title', $title );

	return $title;
}

/**
 * Displays title tag with content.
 *
 * @ignore
 * @since 4.1.0
 * @since 4.4.0 Improved title output replaced `wp_title()`.
 * @access private
 */
function _wp_render_title_tag() {
	if ( ! current_theme_supports( 'title-tag' ) ) {
		return;
	}

	echo '<title>' . wp_get_document_title() . '</title>' . "\n";
}

/**
 * Displays or retrieves page title for all areas of blog.
 *
 * By default, the page title will display the separator before the page title,
 * so that the blog title will be before the page title. This is not good for
 * title display, since the blog title shows up on most tabs and not what is
 * important, which is the page that the user is looking at.
 *
 * There are also SEO benefits to having the blog title after or to the 'right'
 * of the page title. However, it is mostly common sense to have the blog title
 * to the right with most browsers supporting tabs. You can achieve this by
 * using the seplocation parameter and setting the value to 'right'. This change
 * was introduced around 2.5.0, in case backward compatibility of themes is
 * important.
 *
 * @since 1.0.0
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param string $sep         Optional. How to separate the various items within the page title.
 *                            Default '&raquo;'.
 * @param bool   $display     Optional. Whether to display or retrieve title. Default true.
 * @param string $seplocation Optional. Location of the separator ('left' or 'right').
 * @return string|void String when `$display` is false, nothing otherwise.
 */
function wp_title( $sep = '&raquo;', $display = true, $seplocation = '' ) {
	global $wp_locale;

	$m        = get_query_var( 'm' );
	$year     = get_query_var( 'year' );
	$monthnum = get_query_var( 'monthnum' );
	$day      = get_query_var( 'day' );
	$search   = get_query_var( 's' );
	$title    = '';

	$t_sep = '%WP_TITLE_SEP%'; // Temporary separator, for accurate flipping, if necessary.

	// If there is a post.
	if ( is_single() || ( is_home() && ! is_front_page() ) || ( is_page() && ! is_front_page() ) ) {
		$title = single_post_title( '', false );
	}

	// If there's a post type archive.
	if ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object->has_archive ) {
			$title = post_type_archive_title( '', false );
		}
	}

	// If there's a category or tag.
	if ( is_category() || is_tag() ) {
		$title = single_term_title( '', false );
	}

	// If there's a taxonomy.
	if ( is_tax() ) {
		$term = get_queried_object();
		if ( $term ) {
			$tax   = get_taxonomy( $term->taxonomy );
			$title = single_term_title( $tax->labels->name . $t_sep, false );
		}
	}

	// If there's an author.
	if ( is_author() && ! is_post_type_archive() ) {
		$author = get_queried_object();
		if ( $author ) {
			$title = $author->display_name;
		}
	}

	// Post type archives with has_archive should override terms.
	if ( is_post_type_archive() && $post_type_object->has_archive ) {
		$title = post_type_archive_title( '', false );
	}

	// If there's a month.
	if ( is_archive() && ! empty( $m ) ) {
		$my_year  = substr( $m, 0, 4 );
		$my_month = substr( $m, 4, 2 );
		$my_day   = (int) substr( $m, 6, 2 );
		$title    = $my_year .
			( $my_month ? $t_sep . $wp_locale->get_month( $my_month ) : '' ) .
			( $my_day ? $t_sep . $my_day : '' );
	}

	// If there's a year.
	if ( is_archive() && ! empty( $year ) ) {
		$title = $year;
		if ( ! empty( $monthnum ) ) {
			$title .= $t_sep . $wp_locale->get_month( $monthnum );
		}
		if ( ! empty( $day ) ) {
			$title .= $t_sep . zeroise( $day, 2 );
		}
	}

	// If it's a search.
	if ( is_search() ) {
		/* translators: 1: Separator, 2: Search query. */
		$title = sprintf( __( 'Search Results %1$s %2$s' ), $t_sep, strip_tags( $search ) );
	}

	// If it's a 404 page.
	if ( is_404() ) {
		$title = __( 'Page not found' );
	}

	$prefix = '';
	if ( ! empty( $title ) ) {
		$prefix = " $sep ";
	}

	/**
	 * Filters the parts of the page title.
	 *
	 * @since 4.0.0
	 *
	 * @param string[] $title_array Array of parts of the page title.
	 */
	$title_array = apply_filters( 'wp_title_parts', explode( $t_sep, $title ) );

	// Determines position of the separator and direction of the breadcrumb.
	if ( 'right' === $seplocation ) { // Separator on right, so reverse the order.
		$title_array = array_reverse( $title_array );
		$title       = implode( " $sep ", $title_array ) . $prefix;
	} else {
		$title = $prefix . implode( " $sep ", $title_array );
	}

	/**
	 * Filters the text of the page title.
	 *
	 * @since 2.0.0
	 *
	 * @param string $title       Page title.
	 * @param string $sep         Title separator.
	 * @param string $seplocation Location of the separator ('left' or 'right').
	 */
	$title = apply_filters( 'wp_title', $title, $sep, $seplocation );

	// Send it out.
	if ( $display ) {
		echo $title;
	} else {
		return $title;
	}
}

/**
 * Displays or retrieves page title for post.
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
 * @param string $prefix  Optional. What to display before the title.
 * @param bool   $display Optional. Whether to display or retrieve title. Default true.
 * @return string|void Title when retrieving.
 */
function single_post_title( $prefix = '', $display = true ) {
	$_post = get_queried_object();

	if ( ! isset( $_post->post_title ) ) {
		return;
	}

	/**
	 * Filters the page title for a single post.
	 *
	 * @since 0.71
	 *
	 * @param string  $_post_title The single post page title.
	 * @param WP_Post $_post       The current post.
	 */
	$title = apply_filters( 'single_post_title', $_post->post_title, $_post );
	if ( $display ) {
		echo $prefix . $title;
	} else {
		return $prefix . $title;
	}
}

/**
 * Displays or retrieves title for a post type archive.
 *
 * This is optimized for archive.php and archive-{$post_type}.php template files
 * for displaying the title of the post type.
 *
 * @since 3.1.0
 *
 * @param string $prefix  Optional. What to display before the title.
 * @param bool   $display Optional. Whether to display or retrieve title. Default true.
 * @return string|void Title when retrieving, null when displaying or failure.
 */
function post_type_archive_title( $prefix = '', $display = true ) {
	if ( ! is_post_type_archive() ) {
		return;
	}

	$post_type = get_query_var( 'post_type' );
	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type );
	}

	$post_type_obj = get_post_type_object( $post_type );

	/**
	 * Filters the post type archive title.
	 *
	 * @since 3.1.0
	 *
	 * @param string $post_type_name Post type 'name' label.
	 * @param string $post_type      Post type.
	 */
	$title = apply_filters( 'post_type_archive_title', $post_type_obj->labels->name, $post_type );

	if ( $display ) {
		echo $prefix . $title;
	} else {
		return $prefix . $title;
	}
}

/**
 * Displays or retrieves page title for category archive.
 *
 * Useful for category template files for displaying the category page title.
 * The prefix does not automatically place a space between the prefix, so if
 * there should be a space, the parameter value will need to have it at the end.
 *
 * @since 0.71
 *
 * @param string $prefix  Optional. What to display before the title.
 * @param bool   $display Optional. Whether to display or retrieve title. Default true.
 * @return string|void Title when retrieving.
 */
function single_cat_title( $prefix = '', $display = true ) {
	return single_term_title( $prefix, $display );
}

/**
 * Displays or retrieves page title for tag post archive.
 *
 * Useful for tag template files for displaying the tag page title. The prefix
 * does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 2.3.0
 *
 * @param string $prefix  Optional. What to display before the title.
 * @param bool   $display Optional. Whether to display or retrieve title. Default true.
 * @return string|void Title when retrieving.
 */
function single_tag_title( $prefix = '', $display = true ) {
	return single_term_title( $prefix, $display );
}

/**
 * Displays or retrieves page title for taxonomy term archive.
 *
 * Useful for taxonomy term template files for displaying the taxonomy term page title.
 * The prefix does not automatically place a space between the prefix, so if there should
 * be a space, the parameter value will need to have it at the end.
 *
 * @since 3.1.0
 *
 * @param string $prefix  Optional. What to display before the title.
 * @param bool   $display Optional. Whether to display or retrieve title. Default true.
 * @return string|void Title when retrieving.
 */
function single_term_title( $prefix = '', $display = true ) {
	$term = get_queried_object();

	if ( ! $term ) {
		return;
	}

	if ( is_category() ) {
		/**
		 * Filters the category archive page title.
		 *
		 * @since 2.0.10
		 *
		 * @param string $term_name Category name for archive being displayed.
		 */
		$term_name = apply_filters( 'single_cat_title', $term->name );
	} elseif ( is_tag() ) {
		/**
		 * Filters the tag archive page title.
		 *
		 * @since 2.3.0
		 *
		 * @param string $term_name Tag name for archive being displayed.
		 */
		$term_name = apply_filters( 'single_tag_title', $term->name );
	} elseif ( is_tax() ) {
		/**
		 * Filters the custom taxonomy archive page title.
		 *
		 * @since 3.1.0
		 *
		 * @param string $term_name Term name for archive being displayed.
		 */
		$term_name = apply_filters( 'single_term_title', $term->name );
	} else {
		return;
	}

	if ( empty( $term_name ) ) {
		return;
	}

	if ( $display ) {
		echo $prefix . $term_name;
	} else {
		return $prefix . $term_name;
	}
}

/**
 * Displays or retrieves page title for post archive based on date.
 *
 * Useful for when the template only needs to display the month and year,
 * if either are available. The prefix does not automatically place a space
 * between the prefix, so if there should be a space, the parameter value
 * will need to have it at the end.
 *
 * @since 0.71
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param string $prefix  Optional. What to display before the title.
 * @param bool   $display Optional. Whether to display or retrieve title. Default true.
 * @return string|false|void False if there's no valid title for the month. Title when retrieving.
 */
function single_month_title( $prefix = '', $display = true ) {
	global $wp_locale;

	$m        = get_query_var( 'm' );
	$year     = get_query_var( 'year' );
	$monthnum = get_query_var( 'monthnum' );

	if ( ! empty( $monthnum ) && ! empty( $year ) ) {
		$my_year  = $year;
		$my_month = $wp_locale->get_month( $monthnum );
	} elseif ( ! empty( $m ) ) {
		$my_year  = substr( $m, 0, 4 );
		$my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
	}

	if ( empty( $my_month ) ) {
		return false;
	}

	$result = $prefix . $my_month . $prefix . $my_year;

	if ( ! $display ) {
		return $result;
	}
	echo $result;
}

/**
 * Displays the archive title based on the queried object.
 *
 * @since 4.1.0
 *
 * @see get_the_archive_title()
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function the_archive_title( $before = '', $after = '' ) {
	$title = get_the_archive_title();

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}

/**
 * Retrieves the archive title based on the queried object.
 *
 * @since 4.1.0
 * @since 5.5.0 The title part is wrapped in a `<span>` element.
 *
 * @return string Archive title.
 */
function get_the_archive_title() {
	$title  = __( 'Archives' );
	$prefix = '';

	if ( is_category() ) {
		$title  = single_cat_title( '', false );
		$prefix = _x( 'Category:', 'category archive title prefix' );
	} elseif ( is_tag() ) {
		$title  = single_tag_title( '', false );
		$prefix = _x( 'Tag:', 'tag archive title prefix' );
	} elseif ( is_author() ) {
		$title  = get_the_author();
		$prefix = _x( 'Author:', 'author archive title prefix' );
	} elseif ( is_year() ) {
		$title  = get_the_date( _x( 'Y', 'yearly archives date format' ) );
		$prefix = _x( 'Year:', 'date archive title prefix' );
	} elseif ( is_month() ) {
		$title  = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
		$prefix = _x( 'Month:', 'date archive title prefix' );
	} elseif ( is_day() ) {
		$title  = get_the_date( _x( 'F j, Y', 'daily archives date format' ) );
		$prefix = _x( 'Day:', 'date archive title prefix' );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title' );
		}
	} elseif ( is_post_type_archive() ) {
		$title  = post_type_archive_title( '', false );
		$prefix = _x( 'Archives:', 'post type archive title prefix' );
	} elseif ( is_tax() ) {
		$queried_object = get_queried_object();
		if ( $queried_object ) {
			$tax    = get_taxonomy( $queried_object->taxonomy );
			$title  = single_term_title( '', false );
			$prefix = sprintf(
				/* translators: %s: Taxonomy singular name. */
				_x( '%s:', 'taxonomy term archive title prefix' ),
				$tax->labels->singular_name
			);
		}
	}

	$original_title = $title;

	/**
	 * Filters the archive title prefix.
	 *
	 * @since 5.5.0
	 *
	 * @param string $prefix Archive title prefix.
	 */
	$prefix = apply_filters( 'get_the_archive_title_prefix', $prefix );
	if ( $prefix ) {
		$title = sprintf(
			/* translators: 1: Title prefix. 2: Title. */
			_x( '%1$s %2$s', 'archive title' ),
			$prefix,
			'<span>' . $title . '</span>'
		);
	}

	/**
	 * Filters the archive title.
	 *
	 * @since 4.1.0
	 * @since 5.5.0 Added the `$prefix` and `$original_title` parameters.
	 *
	 * @param string $title          Archive title to be displayed.
	 * @param string $original_title Archive title without prefix.
	 * @param string $prefix         Archive title prefix.
	 */
	return apply_filters( 'get_the_archive_title', $title, $original_title, $prefix );
}

/**
 * Displays category, tag, term, or author description.
 *
 * @since 4.1.0
 *
 * @see get_the_archive_description()
 *
 * @param string $before Optional. Content to prepend to the description. Default empty.
 * @param string $after  Optional. Content to append to the description. Default empty.
 */
function the_archive_description( $before = '', $after = '' ) {
	$description = get_the_archive_description();
	if ( $description ) {
		echo $before . $description . $after;
	}
}

/**
 * Retrieves the description for an author, post type, or term archive.
 *
 * @since 4.1.0
 * @since 4.7.0 Added support for author archives.
 * @since 4.9.0 Added support for post type archives.
 *
 * @see term_description()
 *
 * @return string Archive description.
 */
function get_the_archive_description() {
	if ( is_author() ) {
		$description = get_the_author_meta( 'description' );
	} elseif ( is_post_type_archive() ) {
		$description = get_the_post_type_description();
	} else {
		$description = term_description();
	}

	/**
	 * Filters the archive description.
	 *
	 * @since 4.1.0
	 *
	 * @param string $description Archive description to be displayed.
	 */
	return apply_filters( 'get_the_archive_description', $description );
}

/**
 * Retrieves the description for a post type archive.
 *
 * @since 4.9.0
 *
 * @return string The post type description.
 */
function get_the_post_type_description() {
	$post_type = get_query_var( 'post_type' );

	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type );
	}

	$post_type_obj = get_post_type_object( $post_type );

	// Check if a description is set.
	if ( isset( $post_type_obj->description ) ) {
		$description = $post_type_obj->description;
	} else {
		$description = '';
	}

	/**
	 * Filters the description for a post type archive.
	 *
	 * @since 4.9.0
	 *
	 * @param string       $description   The post type description.
	 * @param WP_Post_Type $post_type_obj The post type object.
	 */
	return apply_filters( 'get_the_post_type_description', $description, $post_type_obj );
}

/**
 * Retrieves archive link content based on predefined or custom code.
 *
 * The format can be one of four styles. The 'link' for head element, 'option'
 * for use in the select element, 'html' for use in list (either ol or ul HTML
 * elements). Custom content is also supported using the before and after
 * parameters.
 *
 * The 'link' format uses the `<link>` HTML element with the **archives**
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
 * @since 5.2.0 Added the `$selected` parameter.
 *
 * @param string $url      URL to archive.
 * @param string $text     Archive text description.
 * @param string $format   Optional. Can be 'link', 'option', 'html', or custom. Default 'html'.
 * @param string $before   Optional. Content to prepend to the description. Default empty.
 * @param string $after    Optional. Content to append to the description. Default empty.
 * @param bool   $selected Optional. Set to true if the current page is the selected archive page.
 * @return string HTML link content for archive.
 */
function get_archives_link( $url, $text, $format = 'html', $before = '', $after = '', $selected = false ) {
	$text         = wptexturize( $text );
	$url          = esc_url( $url );
	$aria_current = $selected ? ' aria-current="page"' : '';

	if ( 'link' === $format ) {
		$link_html = "\t<link rel='archives' title='" . esc_attr( $text ) . "' href='$url' />\n";
	} elseif ( 'option' === $format ) {
		$selected_attr = $selected ? " selected='selected'" : '';
		$link_html     = "\t<option value='$url'$selected_attr>$before $text $after</option>\n";
	} elseif ( 'html' === $format ) {
		$link_html = "\t<li>$before<a href='$url'$aria_current>$text</a>$after</li>\n";
	} else { // Custom.
		$link_html = "\t$before<a href='$url'$aria_current>$text</a>$after\n";
	}

	/**
	 * Filters the archive link content.
	 *
	 * @since 2.6.0
	 * @since 4.5.0 Added the `$url`, `$text`, `$format`, `$before`, and `$after` parameters.
	 * @since 5.2.0 Added the `$selected` parameter.
	 *
	 * @param string $link_html The archive HTML link content.
	 * @param string $url       URL to archive.
	 * @param string $text      Archive text description.
	 * @param string $format    Link format. Can be 'link', 'option', 'html', or custom.
	 * @param string $before    Content to prepend to the description.
	 * @param string $after     Content to append to the description.
	 * @param bool   $selected  True if the current page is the selected archive.
	 */
	return apply_filters( 'get_archives_link', $link_html, $url, $text, $format, $before, $after, $selected );
}

/**
 * Displays archive links based on type and format.
 *
 * @since 1.2.0
 * @since 4.4.0 The `$post_type` argument was added.
 * @since 5.2.0 The `$year`, `$monthnum`, `$day`, and `$w` arguments were added.
 *
 * @see get_archives_link()
 *
 * @global wpdb      $wpdb      WordPress database abstraction object.
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param string|array $args {
 *     Default archive links arguments. Optional.
 *
 *     @type string     $type            Type of archive to retrieve. Accepts 'daily', 'weekly', 'monthly',
 *                                       'yearly', 'postbypost', or 'alpha'. Both 'postbypost' and 'alpha'
 *                                       display the same archive link list as well as post titles instead
 *                                       of displaying dates. The difference between the two is that 'alpha'
 *                                       will order by post title and 'postbypost' will order by post date.
 *                                       Default 'monthly'.
 *     @type string|int $limit           Number of links to limit the query to. Default empty (no limit).
 *     @type string     $format          Format each link should take using the $before and $after args.
 *                                       Accepts 'link' (`<link>` tag), 'option' (`<option>` tag), 'html'
 *                                       (`<li>` tag), or a custom format, which generates a link anchor
 *                                       with $before preceding and $after succeeding. Default 'html'.
 *     @type string     $before          Markup to prepend to the beginning of each link. Default empty.
 *     @type string     $after           Markup to append to the end of each link. Default empty.
 *     @type bool       $show_post_count Whether to display the post count alongside the link. Default false.
 *     @type bool|int   $echo            Whether to echo or return the links list. Default 1|true to echo.
 *     @type string     $order           Whether to use ascending or descending order. Accepts 'ASC', or 'DESC'.
 *                                       Default 'DESC'.
 *     @type string     $post_type       Post type. Default 'post'.
 *     @type string     $year            Year. Default current year.
 *     @type string     $monthnum        Month number. Default current month number.
 *     @type string     $day             Day. Default current day.
 *     @type string     $w               Week. Default current week.
 * }
 * @return void|string Void if 'echo' argument is true, archive links if 'echo' is false.
 */
function wp_get_archives( $args = '' ) {
	global $wpdb, $wp_locale;

	$defaults = array(
		'type'            => 'monthly',
		'limit'           => '',
		'format'          => 'html',
		'before'          => '',
		'after'           => '',
		'show_post_count' => false,
		'echo'            => 1,
		'order'           => 'DESC',
		'post_type'       => 'post',
		'year'            => get_query_var( 'year' ),
		'monthnum'        => get_query_var( 'monthnum' ),
		'day'             => get_query_var( 'day' ),
		'w'               => get_query_var( 'w' ),
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$post_type_object = get_post_type_object( $parsed_args['post_type'] );
	if ( ! is_post_type_viewable( $post_type_object ) ) {
		return;
	}

	$parsed_args['post_type'] = $post_type_object->name;

	if ( '' === $parsed_args['type'] ) {
		$parsed_args['type'] = 'monthly';
	}

	if ( ! empty( $parsed_args['limit'] ) ) {
		$parsed_args['limit'] = absint( $parsed_args['limit'] );
		$parsed_args['limit'] = ' LIMIT ' . $parsed_args['limit'];
	}

	$order = strtoupper( $parsed_args['order'] );
	if ( 'ASC' !== $order ) {
		$order = 'DESC';
	}

	// This is what will separate dates on weekly archive links.
	$archive_week_separator = '&#8211;';

	$sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $parsed_args['post_type'] );

	/**
	 * Filters the SQL WHERE clause for retrieving archives.
	 *
	 * @since 2.2.0
	 *
	 * @param string $sql_where   Portion of SQL query containing the WHERE clause.
	 * @param array  $parsed_args An array of default arguments.
	 */
	$where = apply_filters( 'getarchives_where', $sql_where, $parsed_args );

	/**
	 * Filters the SQL JOIN clause for retrieving archives.
	 *
	 * @since 2.2.0
	 *
	 * @param string $sql_join    Portion of SQL query containing JOIN clause.
	 * @param array  $parsed_args An array of default arguments.
	 */
	$join = apply_filters( 'getarchives_join', '', $parsed_args );

	$output = '';

	$last_changed = wp_cache_get_last_changed( 'posts' );

	$limit = $parsed_args['limit'];

	if ( 'monthly' === $parsed_args['type'] ) {
		$query   = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";
		$key     = md5( $query );
		$key     = "wp_get_archives:$key:$last_changed";
		$results = wp_cache_get( $key, 'posts' );
		if ( ! $results ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $parsed_args['after'];
			foreach ( (array) $results as $result ) {
				$url = get_month_link( $result->year, $result->month );
				if ( 'post' !== $parsed_args['post_type'] ) {
					$url = add_query_arg( 'post_type', $parsed_args['post_type'], $url );
				}
				/* translators: 1: Month name, 2: 4-digit year. */
				$text = sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $result->month ), $result->year );
				if ( $parsed_args['show_post_count'] ) {
					$parsed_args['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$selected = is_archive() && (string) $parsed_args['year'] === $result->year && (string) $parsed_args['monthnum'] === $result->month;
				$output  .= get_archives_link( $url, $text, $parsed_args['format'], $parsed_args['before'], $parsed_args['after'], $selected );
			}
		}
	} elseif ( 'yearly' === $parsed_args['type'] ) {
		$query   = "SELECT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date $order $limit";
		$key     = md5( $query );
		$key     = "wp_get_archives:$key:$last_changed";
		$results = wp_cache_get( $key, 'posts' );
		if ( ! $results ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $parsed_args['after'];
			foreach ( (array) $results as $result ) {
				$url = get_year_link( $result->year );
				if ( 'post' !== $parsed_args['post_type'] ) {
					$url = add_query_arg( 'post_type', $parsed_args['post_type'], $url );
				}
				$text = sprintf( '%d', $result->year );
				if ( $parsed_args['show_post_count'] ) {
					$parsed_args['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$selected = is_archive() && (string) $parsed_args['year'] === $result->year;
				$output  .= get_archives_link( $url, $text, $parsed_args['format'], $parsed_args['before'], $parsed_args['after'], $selected );
			}
		}
	} elseif ( 'daily' === $parsed_args['type'] ) {
		$query   = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date $order $limit";
		$key     = md5( $query );
		$key     = "wp_get_archives:$key:$last_changed";
		$results = wp_cache_get( $key, 'posts' );
		if ( ! $results ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $parsed_args['after'];
			foreach ( (array) $results as $result ) {
				$url = get_day_link( $result->year, $result->month, $result->dayofmonth );
				if ( 'post' !== $parsed_args['post_type'] ) {
					$url = add_query_arg( 'post_type', $parsed_args['post_type'], $url );
				}
				$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
				$text = mysql2date( get_option( 'date_format' ), $date );
				if ( $parsed_args['show_post_count'] ) {
					$parsed_args['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$selected = is_archive() && (string) $parsed_args['year'] === $result->year && (string) $parsed_args['monthnum'] === $result->month && (string) $parsed_args['day'] === $result->dayofmonth;
				$output  .= get_archives_link( $url, $text, $parsed_args['format'], $parsed_args['before'], $parsed_args['after'], $selected );
			}
		}
	} elseif ( 'weekly' === $parsed_args['type'] ) {
		$week    = _wp_mysql_week( '`post_date`' );
		$query   = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` $order $limit";
		$key     = md5( $query );
		$key     = "wp_get_archives:$key:$last_changed";
		$results = wp_cache_get( $key, 'posts' );
		if ( ! $results ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		$arc_w_last = '';
		if ( $results ) {
			$after = $parsed_args['after'];
			foreach ( (array) $results as $result ) {
				if ( $result->week != $arc_w_last ) {
					$arc_year       = $result->yr;
					$arc_w_last     = $result->week;
					$arc_week       = get_weekstartend( $result->yyyymmdd, get_option( 'start_of_week' ) );
					$arc_week_start = date_i18n( get_option( 'date_format' ), $arc_week['start'] );
					$arc_week_end   = date_i18n( get_option( 'date_format' ), $arc_week['end'] );
					$url            = add_query_arg(
						array(
							'm' => $arc_year,
							'w' => $result->week,
						),
						home_url( '/' )
					);
					if ( 'post' !== $parsed_args['post_type'] ) {
						$url = add_query_arg( 'post_type', $parsed_args['post_type'], $url );
					}
					$text = $arc_week_start . $archive_week_separator . $arc_week_end;
					if ( $parsed_args['show_post_count'] ) {
						$parsed_args['after'] = '&nbsp;(' . $result->posts . ')' . $after;
					}
					$selected = is_archive() && (string) $parsed_args['year'] === $result->yr && (string) $parsed_args['w'] === $result->week;
					$output  .= get_archives_link( $url, $text, $parsed_args['format'], $parsed_args['before'], $parsed_args['after'], $selected );
				}
			}
		}
	} elseif ( ( 'postbypost' === $parsed_args['type'] ) || ( 'alpha' === $parsed_args['type'] ) ) {
		$orderby = ( 'alpha' === $parsed_args['type'] ) ? 'post_title ASC ' : 'post_date DESC, ID DESC ';
		$query   = "SELECT * FROM $wpdb->posts $join $where ORDER BY $orderby $limit";
		$key     = md5( $query );
		$key     = "wp_get_archives:$key:$last_changed";
		$results = wp_cache_get( $key, 'posts' );
		if ( ! $results ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			foreach ( (array) $results as $result ) {
				if ( '0000-00-00 00:00:00' !== $result->post_date ) {
					$url = get_permalink( $result );
					if ( $result->post_title ) {
						/** This filter is documented in wp-includes/post-template.php */
						$text = strip_tags( apply_filters( 'the_title', $result->post_title, $result->ID ) );
					} else {
						$text = $result->ID;
					}
					$selected = get_the_ID() === $result->ID;
					$output  .= get_archives_link( $url, $text, $parsed_args['format'], $parsed_args['before'], $parsed_args['after'], $selected );
				}
			}
		}
	}

	if ( $parsed_args['echo'] ) {
		echo $output;
	} else {
		return $output;
	}
}

/**
 * Gets number of days since the start of the week.
 *
 * @since 1.5.0
 *
 * @param int $num Number of day.
 * @return float Days since the start of the week.
 */
function calendar_week_mod( $num ) {
	$base = 7;
	return ( $num - $base * floor( $num / $base ) );
}

/**
 * Displays calendar with days that have posts as links.
 *
 * The calendar is cached, which will be retrieved, if it exists. If there are
 * no posts for the month, then it will not be displayed.
 *
 * @since 1.0.0
 *
 * @global wpdb      $wpdb      WordPress database abstraction object.
 * @global int       $m
 * @global int       $monthnum
 * @global int       $year
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 * @global array     $posts
 *
 * @param bool $initial Optional. Whether to use initial calendar names. Default true.
 * @param bool $display Optional. Whether to display the calendar output. Default true.
 * @return void|string Void if `$display` argument is true, calendar HTML if `$display` is false.
 */
function get_calendar( $initial = true, $display = true ) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

	$key   = md5( $m . $monthnum . $year );
	$cache = wp_cache_get( 'get_calendar', 'calendar' );

	if ( $cache && is_array( $cache ) && isset( $cache[ $key ] ) ) {
		/** This filter is documented in wp-includes/general-template.php */
		$output = apply_filters( 'get_calendar', $cache[ $key ] );

		if ( $display ) {
			echo $output;
			return;
		}

		return $output;
	}

	if ( ! is_array( $cache ) ) {
		$cache = array();
	}

	// Quick check. If we have no posts at all, abort!
	if ( ! $posts ) {
		$gotsome = $wpdb->get_var( "SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 1" );
		if ( ! $gotsome ) {
			$cache[ $key ] = '';
			wp_cache_set( 'get_calendar', $cache, 'calendar' );
			return;
		}
	}

	if ( isset( $_GET['w'] ) ) {
		$w = (int) $_GET['w'];
	}
	// week_begins = 0 stands for Sunday.
	$week_begins = (int) get_option( 'start_of_week' );

	// Let's figure out when we are.
	if ( ! empty( $monthnum ) && ! empty( $year ) ) {
		$thismonth = zeroise( (int) $monthnum, 2 );
		$thisyear  = (int) $year;
	} elseif ( ! empty( $w ) ) {
		// We need to get the month from MySQL.
		$thisyear = (int) substr( $m, 0, 4 );
		// It seems MySQL's weeks disagree with PHP's.
		$d         = ( ( $w - 1 ) * 7 ) + 6;
		$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')" );
	} elseif ( ! empty( $m ) ) {
		$thisyear = (int) substr( $m, 0, 4 );
		if ( strlen( $m ) < 6 ) {
			$thismonth = '01';
		} else {
			$thismonth = zeroise( (int) substr( $m, 4, 2 ), 2 );
		}
	} else {
		$thisyear  = current_time( 'Y' );
		$thismonth = current_time( 'm' );
	}

	$unixmonth = mktime( 0, 0, 0, $thismonth, 1, $thisyear );
	$last_day  = gmdate( 't', $unixmonth );

	// Get the next and previous month and year with at least one post.
	$previous = $wpdb->get_row(
		"SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$thisyear-$thismonth-01'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1"
	);
	$next     = $wpdb->get_row(
		"SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date ASC
			LIMIT 1"
	);

	/* translators: Calendar caption: 1: Month name, 2: 4-digit year. */
	$calendar_caption = _x( '%1$s %2$s', 'calendar caption' );
	$calendar_output  = '<table id="wp-calendar" class="wp-calendar-table">
	<caption>' . sprintf(
		$calendar_caption,
		$wp_locale->get_month( $thismonth ),
		gmdate( 'Y', $unixmonth )
	) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
	}

	foreach ( $myweek as $wd ) {
		$day_name         = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
		$wd               = esc_attr( $wd );
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '
	</tr>
	</thead>
	<tbody>
	<tr>';

	$daywithpost = array();

	// Get days with posts.
	$dayswithposts = $wpdb->get_results(
		"SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
		AND post_type = 'post' AND post_status = 'publish'
		AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'",
		ARRAY_N
	);

	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = (int) $daywith[0];
		}
	}

	// See how much we should pad in the beginning.
	$pad = calendar_week_mod( gmdate( 'w', $unixmonth ) - $week_begins );
	if ( 0 != $pad ) {
		$calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
	}

	$newrow      = false;
	$daysinmonth = (int) gmdate( 't', $unixmonth );

	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset( $newrow ) && $newrow ) {
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		}
		$newrow = false;

		if ( current_time( 'j' ) == $day &&
			current_time( 'm' ) == $thismonth &&
			current_time( 'Y' ) == $thisyear ) {
			$calendar_output .= '<td id="today">';
		} else {
			$calendar_output .= '<td>';
		}

		if ( in_array( $day, $daywithpost, true ) ) {
			// Any posts today?
			$date_format = gmdate( _x( 'F j, Y', 'daily archives date format' ), strtotime( "{$thisyear}-{$thismonth}-{$day}" ) );
			/* translators: Post calendar label. %s: Date. */
			$label            = sprintf( __( 'Posts published on %s' ), $date_format );
			$calendar_output .= sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				get_day_link( $thisyear, $thismonth, $day ),
				esc_attr( $label ),
				$day
			);
		} else {
			$calendar_output .= $day;
		}

		$calendar_output .= '</td>';

		if ( 6 == calendar_week_mod( gmdate( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
			$newrow = true;
		}
	}

	$pad = 7 - calendar_week_mod( gmdate( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins );
	if ( 0 != $pad && 7 != $pad ) {
		$calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
	}

	$calendar_output .= "\n\t</tr>\n\t</tbody>";

	$calendar_output .= "\n\t</table>";

	$calendar_output .= '<nav aria-label="' . __( 'Previous and next months' ) . '" class="wp-calendar-nav">';

	if ( $previous ) {
		$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-prev"><a href="' . get_month_link( $previous->year, $previous->month ) . '">&laquo; ' .
			$wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) .
		'</a></span>';
	} else {
		$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-prev">&nbsp;</span>';
	}

	$calendar_output .= "\n\t\t" . '<span class="pad">&nbsp;</span>';

	if ( $next ) {
		$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-next"><a href="' . get_month_link( $next->year, $next->month ) . '">' .
			$wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) .
		' &raquo;</a></span>';
	} else {
		$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-next">&nbsp;</span>';
	}

	$calendar_output .= '
	</nav>';

	$cache[ $key ] = $calendar_output;
	wp_cache_set( 'get_calendar', $cache, 'calendar' );

	if ( $display ) {
		/**
		 * Filters the HTML calendar output.
		 *
		 * @since 3.0.0
		 *
		 * @param string $calendar_output HTML output of the calendar.
		 */
		echo apply_filters( 'get_calendar', $calendar_output );
		return;
	}
	/** This filter is documented in wp-includes/general-template.php */
	return apply_filters( 'get_calendar', $calendar_output );
}

/**
 * Purges the cached results of get_calendar.
 *
 * @see get_calendar()
 * @since 2.1.0
 */
function delete_get_calendar_cache() {
	wp_cache_delete( 'get_calendar', 'calendar' );
}

/**
 * Displays all of the allowed tags in HTML format with attributes.
 *
 * This is useful for displaying in the comment area, which elements and
 * attributes are supported. As well as any plugins which want to display it.
 *
 * @since 1.0.1
 * @since 4.4.0 No longer used in core.
 *
 * @global array $allowedtags
 *
 * @return string HTML allowed tags entity encoded.
 */
function allowed_tags() {
	global $allowedtags;
	$allowed = '';
	foreach ( (array) $allowedtags as $tag => $attributes ) {
		$allowed .= '<' . $tag;
		if ( 0 < count( $attributes ) ) {
			foreach ( $attributes as $attribute => $limits ) {
				$allowed .= ' ' . $attribute . '=""';
			}
		}
		$allowed .= '> ';
	}
	return htmlentities( $allowed );
}

/***** Date/Time tags */

/**
 * Outputs the date in iso8601 format for xml files.
 *
 * @since 1.0.0
 */
function the_date_xml() {
	echo mysql2date( 'Y-m-d', get_post()->post_date, false );
}

/**
 * Displays or retrieves the date the current post was written (once per date)
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
 *
 * @global string $currentday  The day of the current post in the loop.
 * @global string $previousday The day of the previous post in the loop.
 *
 * @param string $format  Optional. PHP date format. Defaults to the 'date_format' option.
 * @param string $before  Optional. Output before the date. Default empty.
 * @param string $after   Optional. Output after the date. Default empty.
 * @param bool   $display Optional. Whether to echo the date or return it. Default true.
 * @return string|void String if retrieving.
 */
function the_date( $format = '', $before = '', $after = '', $display = true ) {
	global $currentday, $previousday;

	$the_date = '';

	if ( is_new_day() ) {
		$the_date    = $before . get_the_date( $format ) . $after;
		$previousday = $currentday;
	}

	/**
	 * Filters the date a post was published for display.
	 *
	 * @since 0.71
	 *
	 * @param string $the_date The formatted date string.
	 * @param string $format   PHP date format.
	 * @param string $before   HTML output before the date.
	 * @param string $after    HTML output after the date.
	 */
	$the_date = apply_filters( 'the_date', $the_date, $format, $before, $after );

	if ( $display ) {
		echo $the_date;
	} else {
		return $the_date;
	}
}

/**
 * Retrieves the date on which the post was written.
 *
 * Unlike the_date() this function will always return the date.
 * Modify output with the {@see 'get_the_date'} filter.
 *
 * @since 3.0.0
 *
 * @param string      $format Optional. PHP date format. Defaults to the 'date_format' option.
 * @param int|WP_Post $post   Optional. Post ID or WP_Post object. Default current post.
 * @return string|int|false Date the current post was written. False on failure.
 */
function get_the_date( $format = '', $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$_format = ! empty( $format ) ? $format : get_option( 'date_format' );

	$the_date = get_post_time( $_format, false, $post, true );

	/**
	 * Filters the date a post was published.
	 *
	 * @since 3.0.0
	 *
	 * @param string|int  $the_date Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
	 * @param string      $format   PHP date format.
	 * @param WP_Post     $post     The post object.
	 */
	return apply_filters( 'get_the_date', $the_date, $format, $post );
}

/**
 * Displays the date on which the post was last modified.
 *
 * @since 2.1.0
 *
 * @param string $format  Optional. PHP date format. Defaults to the 'date_format' option.
 * @param string $before  Optional. Output before the date. Default empty.
 * @param string $after   Optional. Output after the date. Default empty.
 * @param bool   $display Optional. Whether to echo the date or return it. Default true.
 * @return string|void String if retrieving.
 */
function the_modified_date( $format = '', $before = '', $after = '', $display = true ) {
	$the_modified_date = $before . get_the_modified_date( $format ) . $after;

	/**
	 * Filters the date a post was last modified for display.
	 *
	 * @since 2.1.0
	 *
	 * @param string|false $the_modified_date The last modified date or false if no post is found.
	 * @param string       $format            PHP date format.
	 * @param string       $before            HTML output before the date.
	 * @param string       $after             HTML output after the date.
	 */
	$the_modified_date = apply_filters( 'the_modified_date', $the_modified_date, $format, $before, $after );

	if ( $display ) {
		echo $the_modified_date;
	} else {
		return $the_modified_date;
	}

}

/**
 * Retrieves the date on which the post was last modified.
 *
 * @since 2.1.0
 * @since 4.6.0 Added the `$post` parameter.
 *
 * @param string      $format Optional. PHP date format. Defaults to the 'date_format' option.
 * @param int|WP_Post $post   Optional. Post ID or WP_Post object. Default current post.
 * @return string|int|false Date the current post was modified. False on failure.
 */
function get_the_modified_date( $format = '', $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		// For backward compatibility, failures go through the filter below.
		$the_time = false;
	} else {
		$_format = ! empty( $format ) ? $format : get_option( 'date_format' );

		$the_time = get_post_modified_time( $_format, false, $post, true );
	}

	/**
	 * Filters the date a post was last modified.
	 *
	 * @since 2.1.0
	 * @since 4.6.0 Added the `$post` parameter.
	 *
	 * @param string|int|false $the_time The formatted date or false if no post is found.
	 * @param string           $format   PHP date format.
	 * @param WP_Post|null     $post     WP_Post object or null if no post is found.
	 */
	return apply_filters( 'get_the_modified_date', $the_time, $format, $post );
}

/**
 * Displays the time at which the post was written.
 *
 * @since 0.71
 *
 * @param string $format Optional. Format to use for retrieving the time the post
 *                       was written. Accepts 'G', 'U', or PHP date format.
 *                       Defaults to the 'time_format' option.
 */
function the_time( $format = '' ) {
	/**
	 * Filters the time a post was written for display.
	 *
	 * @since 0.71
	 *
	 * @param string $get_the_time The formatted time.
	 * @param string $format       Format to use for retrieving the time the post
	 *                             was written. Accepts 'G', 'U', or PHP date format.
	 */
	echo apply_filters( 'the_time', get_the_time( $format ), $format );
}

/**
 * Retrieves the time at which the post was written.
 *
 * @since 1.5.0
 *
 * @param string      $format Optional. Format to use for retrieving the time the post
 *                            was written. Accepts 'G', 'U', or PHP date format.
 *                            Defaults to the 'time_format' option.
 * @param int|WP_Post $post   Post ID or post object. Default is global `$post` object.
 * @return string|int|false Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
 *                          False on failure.
 */
function get_the_time( $format = '', $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$_format = ! empty( $format ) ? $format : get_option( 'time_format' );

	$the_time = get_post_time( $_format, false, $post, true );

	/**
	 * Filters the time a post was written.
	 *
	 * @since 1.5.0
	 *
	 * @param string|int  $the_time Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
	 * @param string      $format   Format to use for retrieving the time the post
	 *                              was written. Accepts 'G', 'U', or PHP date format.
	 * @param WP_Post     $post     Post object.
	 */
	return apply_filters( 'get_the_time', $the_time, $format, $post );
}

/**
 * Retrieves the time at which the post was written.
 *
 * @since 2.0.0
 *
 * @param string      $format    Optional. Format to use for retrieving the time the post
 *                               was written. Accepts 'G', 'U', or PHP date format. Default 'U'.
 * @param bool        $gmt       Optional. Whether to retrieve the GMT time. Default false.
 * @param int|WP_Post $post      Post ID or post object. Default is global `$post` object.
 * @param bool        $translate Whether to translate the time string. Default false.
 * @return string|int|false Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
 *                          False on failure.
 */
function get_post_time( $format = 'U', $gmt = false, $post = null, $translate = false ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$source   = ( $gmt ) ? 'gmt' : 'local';
	$datetime = get_post_datetime( $post, 'date', $source );

	if ( false === $datetime ) {
		return false;
	}

	if ( 'U' === $format || 'G' === $format ) {
		$time = $datetime->getTimestamp();

		// Returns a sum of timestamp with timezone offset. Ideally should never be used.
		if ( ! $gmt ) {
			$time += $datetime->getOffset();
		}
	} elseif ( $translate ) {
		$time = wp_date( $format, $datetime->getTimestamp(), $gmt ? new DateTimeZone( 'UTC' ) : null );
	} else {
		if ( $gmt ) {
			$datetime = $datetime->setTimezone( new DateTimeZone( 'UTC' ) );
		}

		$time = $datetime->format( $format );
	}

	/**
	 * Filters the localized time a post was written.
	 *
	 * @since 2.6.0
	 *
	 * @param string|int $time   Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
	 * @param string     $format Format to use for retrieving the time the post was written.
	 *                           Accepts 'G', 'U', or PHP date format.
	 * @param bool       $gmt    Whether to retrieve the GMT time.
	 */
	return apply_filters( 'get_post_time', $time, $format, $gmt );
}

/**
 * Retrieves post published or modified time as a `DateTimeImmutable` object instance.
 *
 * The object will be set to the timezone from WordPress settings.
 *
 * For legacy reasons, this function allows to choose to instantiate from local or UTC time in database.
 * Normally this should make no difference to the result. However, the values might get out of sync in database,
 * typically because of timezone setting changes. The parameter ensures the ability to reproduce backwards
 * compatible behaviors in such cases.
 *
 * @since 5.3.0
 *
 * @param int|WP_Post $post   Optional. Post ID or post object. Default is global `$post` object.
 * @param string      $field  Optional. Published or modified time to use from database. Accepts 'date' or 'modified'.
 *                            Default 'date'.
 * @param string      $source Optional. Local or UTC time to use from database. Accepts 'local' or 'gmt'.
 *                            Default 'local'.
 * @return DateTimeImmutable|false Time object on success, false on failure.
 */
function get_post_datetime( $post = null, $field = 'date', $source = 'local' ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$wp_timezone = wp_timezone();

	if ( 'gmt' === $source ) {
		$time     = ( 'modified' === $field ) ? $post->post_modified_gmt : $post->post_date_gmt;
		$timezone = new DateTimeZone( 'UTC' );
	} else {
		$time     = ( 'modified' === $field ) ? $post->post_modified : $post->post_date;
		$timezone = $wp_timezone;
	}

	if ( empty( $time ) || '0000-00-00 00:00:00' === $time ) {
		return false;
	}

	$datetime = date_create_immutable_from_format( 'Y-m-d H:i:s', $time, $timezone );

	if ( false === $datetime ) {
		return false;
	}

	return $datetime->setTimezone( $wp_timezone );
}

/**
 * Retrieves post published or modified time as a Unix timestamp.
 *
 * Note that this function returns a true Unix timestamp, not summed with timezone offset
 * like older WP functions.
 *
 * @since 5.3.0
 *
 * @param int|WP_Post $post  Optional. Post ID or post object. Default is global `$post` object.
 * @param string      $field Optional. Published or modified time to use from database. Accepts 'date' or 'modified'.
 *                           Default 'date'.
 * @return int|false Unix timestamp on success, false on failure.
 */
function get_post_timestamp( $post = null, $field = 'date' ) {
	$datetime = get_post_datetime( $post, $field );

	if ( false === $datetime ) {
		return false;
	}

	return $datetime->getTimestamp();
}

/**
 * Displays the time at which the post was last modified.
 *
 * @since 2.0.0
 *
 * @param string $format Optional. Format to use for retrieving the time the post
 *                       was modified. Accepts 'G', 'U', or PHP date format.
 *                       Defaults to the 'time_format' option.
 */
function the_modified_time( $format = '' ) {
	/**
	 * Filters the localized time a post was last modified, for display.
	 *
	 * @since 2.0.0
	 *
	 * @param string|false $get_the_modified_time The formatted time or false if no post is found.
	 * @param string       $format                Format to use for retrieving the time the post
	 *                                            was modified. Accepts 'G', 'U', or PHP date format.
	 */
	echo apply_filters( 'the_modified_time', get_the_modified_time( $format ), $format );
}

/**
 * Retrieves the time at which the post was last modified.
 *
 * @since 2.0.0
 * @since 4.6.0 Added the `$post` parameter.
 *
 * @param string      $format Optional. Format to use for retrieving the time the post
 *                            was modified. Accepts 'G', 'U', or PHP date format.
 *                            Defaults to the 'time_format' option.
 * @param int|WP_Post $post   Optional. Post ID or WP_Post object. Default current post.
 * @return string|int|false Formatted date string or Unix timestamp. False on failure.
 */
function get_the_modified_time( $format = '', $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		// For backward compatibility, failures go through the filter below.
		$the_time = false;
	} else {
		$_format = ! empty( $format ) ? $format : get_option( 'time_format' );

		$the_time = get_post_modified_time( $_format, false, $post, true );
	}

	/**
	 * Filters the localized time a post was last modified.
	 *
	 * @since 2.0.0
	 * @since 4.6.0 Added the `$post` parameter.
	 *
	 * @param string|int|false $the_time The formatted time or false if no post is found.
	 * @param string           $format   Format to use for retrieving the time the post
	 *                                   was modified. Accepts 'G', 'U', or PHP date format.
	 * @param WP_Post|null     $post     WP_Post object or null if no post is found.
	 */
	return apply_filters( 'get_the_modified_time', $the_time, $format, $post );
}

/**
 * Retrieves the time at which the post was last modified.
 *
 * @since 2.0.0
 *
 * @param string      $format    Optional. Format to use for retrieving the time the post
 *                               was modified. Accepts 'G', 'U', or PHP date format. Default 'U'.
 * @param bool        $gmt       Optional. Whether to retrieve the GMT time. Default false.
 * @param int|WP_Post $post      Post ID or post object. Default is global `$post` object.
 * @param bool        $translate Whether to translate the time string. Default false.
 * @return string|int|false Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
 *                          False on failure.
 */
function get_post_modified_time( $format = 'U', $gmt = false, $post = null, $translate = false ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$source   = ( $gmt ) ? 'gmt' : 'local';
	$datetime = get_post_datetime( $post, 'modified', $source );

	if ( false === $datetime ) {
		return false;
	}

	if ( 'U' === $format || 'G' === $format ) {
		$time = $datetime->getTimestamp();

		// Returns a sum of timestamp with timezone offset. Ideally should never be used.
		if ( ! $gmt ) {
			$time += $datetime->getOffset();
		}
	} elseif ( $translate ) {
		$time = wp_date( $format, $datetime->getTimestamp(), $gmt ? new DateTimeZone( 'UTC' ) : null );
	} else {
		if ( $gmt ) {
			$datetime = $datetime->setTimezone( new DateTimeZone( 'UTC' ) );
		}

		$time = $datetime->format( $format );
	}

	/**
	 * Filters the localized time a post was last modified.
	 *
	 * @since 2.8.0
	 *
	 * @param string|int $time   Formatted date string or Unix timestamp if `$format` is 'U' or 'G'.
	 * @param string     $format Format to use for retrieving the time the post was modified.
	 *                           Accepts 'G', 'U', or PHP date format. Default 'U'.
	 * @param bool       $gmt    Whether to retrieve the GMT time. Default false.
	 */
	return apply_filters( 'get_post_modified_time', $time, $format, $gmt );
}

/**
 * Displays the weekday on which the post was written.
 *
 * @since 0.71
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 */
function the_weekday() {
	global $wp_locale;

	$post = get_post();

	if ( ! $post ) {
		return;
	}

	$the_weekday = $wp_locale->get_weekday( get_post_time( 'w', false, $post ) );

	/**
	 * Filters the weekday on which the post was written, for display.
	 *
	 * @since 0.71
	 *
	 * @param string $the_weekday
	 */
	echo apply_filters( 'the_weekday', $the_weekday );
}

/**
 * Displays the weekday on which the post was written.
 *
 * Will only output the weekday if the current post's weekday is different from
 * the previous one output.
 *
 * @since 0.71
 *
 * @global WP_Locale $wp_locale       WordPress date and time locale object.
 * @global string    $currentday      The day of the current post in the loop.
 * @global string    $previousweekday The day of the previous post in the loop.
 *
 * @param string $before Optional. Output before the date. Default empty.
 * @param string $after  Optional. Output after the date. Default empty.
 */
function the_weekday_date( $before = '', $after = '' ) {
	global $wp_locale, $currentday, $previousweekday;

	$post = get_post();

	if ( ! $post ) {
		return;
	}

	$the_weekday_date = '';

	if ( $currentday !== $previousweekday ) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $wp_locale->get_weekday( get_post_time( 'w', false, $post ) );
		$the_weekday_date .= $after;
		$previousweekday   = $currentday;
	}

	/**
	 * Filters the localized date on which the post was written, for display.
	 *
	 * @since 0.71
	 *
	 * @param string $the_weekday_date The weekday on which the post was written.
	 * @param string $before           The HTML to output before the date.
	 * @param string $after            The HTML to output after the date.
	 */
	echo apply_filters( 'the_weekday_date', $the_weekday_date, $before, $after );
}

/**
 * Fires the wp_head action.
 *
 * See {@see 'wp_head'}.
 *
 * @since 1.2.0
 */
function wp_head() {
	/**
	 * Prints scripts or data in the head tag on the front end.
	 *
	 * @since 1.5.0
	 */
	do_action( 'wp_head' );
}

/**
 * Fires the wp_footer action.
 *
 * See {@see 'wp_footer'}.
 *
 * @since 1.5.1
 */
function wp_footer() {
	/**
	 * Prints scripts or data before the closing body tag on the front end.
	 *
	 * @since 1.5.1
	 */
	do_action( 'wp_footer' );
}

/**
 * Fires the wp_body_open action.
 *
 * See {@see 'wp_body_open'}.
 *
 * @since 5.2.0
 */
function wp_body_open() {
	/**
	 * Triggered after the opening body tag.
	 *
	 * @since 5.2.0
	 */
	do_action( 'wp_body_open' );
}

/**
 * Displays the links to the general feeds.
 *
 * @since 2.8.0
 *
 * @param array $args Optional arguments.
 */
function feed_links( $args = array() ) {
	if ( ! current_theme_supports( 'automatic-feed-links' ) ) {
		return;
	}

	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links. */
		'separator' => _x( '&raquo;', 'feed link' ),
		/* translators: 1: Blog title, 2: Separator (raquo). */
		'feedtitle' => __( '%1$s %2$s Feed' ),
		/* translators: 1: Blog title, 2: Separator (raquo). */
		'comstitle' => __( '%1$s %2$s Comments Feed' ),
	);

	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters whether to display the posts feed link.
	 *
	 * @since 4.4.0
	 *
	 * @param bool $show Whether to display the posts feed link. Default true.
	 */
	if ( apply_filters( 'feed_links_show_posts_feed', true ) ) {
		printf(
			'<link rel="alternate" type="%s" title="%s" href="%s" />' . "\n",
			feed_content_type(),
			esc_attr( sprintf( $args['feedtitle'], get_bloginfo( 'name' ), $args['separator'] ) ),
			esc_url( get_feed_link() )
		);
	}

	/**
	 * Filters whether to display the comments feed link.
	 *
	 * @since 4.4.0
	 *
	 * @param bool $show Whether to display the comments feed link. Default true.
	 */
	if ( apply_filters( 'feed_links_show_comments_feed', true ) ) {
		printf(
			'<link rel="alternate" type="%s" title="%s" href="%s" />' . "\n",
			feed_content_type(),
			esc_attr( sprintf( $args['comstitle'], get_bloginfo( 'name' ), $args['separator'] ) ),
			esc_url( get_feed_link( 'comments_' . get_default_feed() ) )
		);
	}
}

/**
 * Displays the links to the extra feeds such as category feeds.
 *
 * @since 2.8.0
 *
 * @param array $args Optional arguments.
 */
function feed_links_extra( $args = array() ) {
	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links. */
		'separator'     => _x( '&raquo;', 'feed link' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Post title. */
		'singletitle'   => __( '%1$s %2$s %3$s Comments Feed' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Category name. */
		'cattitle'      => __( '%1$s %2$s %3$s Category Feed' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Tag name. */
		'tagtitle'      => __( '%1$s %2$s %3$s Tag Feed' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Term name, 4: Taxonomy singular name. */
		'taxtitle'      => __( '%1$s %2$s %3$s %4$s Feed' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Author name. */
		'authortitle'   => __( '%1$s %2$s Posts by %3$s Feed' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Search query. */
		'searchtitle'   => __( '%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Post type name. */
		'posttypetitle' => __( '%1$s %2$s %3$s Feed' ),
	);

	$args = wp_parse_args( $args, $defaults );

	if ( is_singular() ) {
		$id   = 0;
		$post = get_post( $id );

		/** This filter is documented in wp-includes/general-template.php */
		$show_comments_feed = apply_filters( 'feed_links_show_comments_feed', true );

		/**
		 * Filters whether to display the post comments feed link.
		 *
		 * This filter allows to enable or disable the feed link for a singular post
		 * in a way that is independent of {@see 'feed_links_show_comments_feed'}
		 * (which controls the global comments feed). The result of that filter
		 * is accepted as a parameter.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show_comments_feed Whether to display the post comments feed link. Defaults to
		 *                                 the {@see 'feed_links_show_comments_feed'} filter result.
		 */
		$show_post_comments_feed = apply_filters( 'feed_links_extra_show_post_comments_feed', $show_comments_feed );

		if ( $show_post_comments_feed && ( comments_open() || pings_open() || $post->comment_count > 0 ) ) {
			$title = sprintf(
				$args['singletitle'],
				get_bloginfo( 'name' ),
				$args['separator'],
				the_title_attribute( array( 'echo' => false ) )
			);

			$feed_link = get_post_comments_feed_link( $post->ID );

			if ( $feed_link ) {
				$href = $feed_link;
			}
		}
	} elseif ( is_post_type_archive() ) {
		/**
		 * Filters whether to display the post type archive feed link.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show Whether to display the post type archive feed link. Default true.
		 */
		$show_post_type_archive_feed = apply_filters( 'feed_links_extra_show_post_type_archive_feed', true );

		if ( $show_post_type_archive_feed ) {
			$post_type = get_query_var( 'post_type' );

			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}

			$post_type_obj = get_post_type_object( $post_type );

			$title = sprintf(
				$args['posttypetitle'],
				get_bloginfo( 'name' ),
				$args['separator'],
				$post_type_obj->labels->name
			);

			$href = get_post_type_archive_feed_link( $post_type_obj->name );
		}
	} elseif ( is_category() ) {
		/**
		 * Filters whether to display the category feed link.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show Whether to display the category feed link. Default true.
		 */
		$show_category_feed = apply_filters( 'feed_links_extra_show_category_feed', true );

		if ( $show_category_feed ) {
			$term = get_queried_object();

			if ( $term ) {
				$title = sprintf(
					$args['cattitle'],
					get_bloginfo( 'name' ),
					$args['separator'],
					$term->name
				);

				$href = get_category_feed_link( $term->term_id );
			}
		}
	} elseif ( is_tag() ) {
		/**
		 * Filters whether to display the tag feed link.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show Whether to display the tag feed link. Default true.
		 */
		$show_tag_feed = apply_filters( 'feed_links_extra_show_tag_feed', true );

		if ( $show_tag_feed ) {
			$term = get_queried_object();

			if ( $term ) {
				$title = sprintf(
					$args['tagtitle'],
					get_bloginfo( 'name' ),
					$args['separator'],
					$term->name
				);

				$href = get_tag_feed_link( $term->term_id );
			}
		}
	} elseif ( is_tax() ) {
		/**
		 * Filters whether to display the custom taxonomy feed link.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show Whether to display the custom taxonomy feed link. Default true.
		 */
		$show_tax_feed = apply_filters( 'feed_links_extra_show_tax_feed', true );

		if ( $show_tax_feed ) {
			$term = get_queried_object();

			if ( $term ) {
				$tax = get_taxonomy( $term->taxonomy );

				$title = sprintf(
					$args['taxtitle'],
					get_bloginfo( 'name' ),
					$args['separator'],
					$term->name,
					$tax->labels->singular_name
				);

				$href = get_term_feed_link( $term->term_id, $term->taxonomy );
			}
		}
	} elseif ( is_author() ) {
		/**
		 * Filters whether to display the author feed link.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show Whether to display the author feed link. Default true.
		 */
		$show_author_feed = apply_filters( 'feed_links_extra_show_author_feed', true );

		if ( $show_author_feed ) {
			$author_id = (int) get_query_var( 'author' );

			$title = sprintf(
				$args['authortitle'],
				get_bloginfo( 'name' ),
				$args['separator'],
				get_the_author_meta( 'display_name', $author_id )
			);

			$href = get_author_feed_link( $author_id );
		}
	} elseif ( is_search() ) {
		/**
		 * Filters whether to display the search results feed link.
		 *
		 * @since 6.1.0
		 *
		 * @param bool $show Whether to display the search results feed link. Default true.
		 */
		$show_search_feed = apply_filters( 'feed_links_extra_show_search_feed', true );

		if ( $show_search_feed ) {
			$title = sprintf(
				$args['searchtitle'],
				get_bloginfo( 'name' ),
				$args['separator'],
				get_search_query( false )
			);

			$href = get_search_feed_link();
		}
	}

	if ( isset( $title ) && isset( $href ) ) {
		printf(
			'<link rel="alternate" type="%s" title="%s" href="%s" />' . "\n",
			feed_content_type(),
			esc_attr( $title ),
			esc_url( $href )
		);
	}
}

/**
 * Displays the link to the Really Simple Discovery service endpoint.
 *
 * @link http://archipelago.phrasewise.com/rsd
 * @since 2.0.0
 */
function rsd_link() {
	printf(
		'<link rel="EditURI" type="application/rsd+xml" title="RSD" href="%s" />' . "\n",
		esc_url( site_url( 'xmlrpc.php?rsd', 'rpc' ) )
	);
}

/**
 * Displays the link to the Windows Live Writer manifest file.
 *
 * @link https://msdn.microsoft.com/en-us/library/bb463265.aspx
 * @since 2.3.1
 */
function wlwmanifest_link() {
	printf(
		'<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="%s" />' . "\n",
		includes_url( 'wlwmanifest.xml' )
	);
}

/**
 * Displays a referrer `strict-origin-when-cross-origin` meta tag.
 *
 * Outputs a referrer `strict-origin-when-cross-origin` meta tag that tells the browser not to send
 * the full URL as a referrer to other sites when cross-origin assets are loaded.
 *
 * Typical usage is as a {@see 'wp_head'} callback:
 *
 *     add_action( 'wp_head', 'wp_strict_cross_origin_referrer' );
 *
 * @since 5.7.0
 */
function wp_strict_cross_origin_referrer() {
	?>
	<meta name='referrer' content='strict-origin-when-cross-origin' />
	<?php
}

/**
 * Displays site icon meta tags.
 *
 * @since 4.3.0
 *
 * @link https://www.whatwg.org/specs/web-apps/current-work/multipage/links.html#rel-icon HTML5 specification link icon.
 */
function wp_site_icon() {
	if ( ! has_site_icon() && ! is_customize_preview() ) {
		return;
	}

	$meta_tags = array();
	$icon_32   = get_site_icon_url( 32 );
	if ( empty( $icon_32 ) && is_customize_preview() ) {
		$icon_32 = '/favicon.ico'; // Serve default favicon URL in customizer so element can be updated for preview.
	}
	if ( $icon_32 ) {
		$meta_tags[] = sprintf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( $icon_32 ) );
	}
	$icon_192 = get_site_icon_url( 192 );
	if ( $icon_192 ) {
		$meta_tags[] = sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( $icon_192 ) );
	}
	$icon_180 = get_site_icon_url( 180 );
	if ( $icon_180 ) {
		$meta_tags[] = sprintf( '<link rel="apple-touch-icon" href="%s" />', esc_url( $icon_180 ) );
	}
	$icon_270 = get_site_icon_url( 270 );
	if ( $icon_270 ) {
		$meta_tags[] = sprintf( '<meta name="msapplication-TileImage" content="%s" />', esc_url( $icon_270 ) );
	}

	/**
	 * Filters the site icon meta tags, so plugins can add their own.
	 *
	 * @since 4.3.0
	 *
	 * @param string[] $meta_tags Array of Site Icon meta tags.
	 */
	$meta_tags = apply_filters( 'site_icon_meta_tags', $meta_tags );
	$meta_tags = array_filter( $meta_tags );

	foreach ( $meta_tags as $meta_tag ) {
		echo "$meta_tag\n";
	}
}

/**
 * Prints resource hints to browsers for pre-fetching, pre-rendering
 * and pre-connecting to web sites.
 *
 * Gives hints to browsers to prefetch specific pages or render them
 * in the background, to perform DNS lookups or to begin the connection
 * handshake (DNS, TCP, TLS) in the background.
 *
 * These performance improving indicators work by using `<link rel"…">`.
 *
 * @since 4.6.0
 */
function wp_resource_hints() {
	$hints = array(
		'dns-prefetch' => wp_dependencies_unique_hosts(),
		'preconnect'   => array(),
		'prefetch'     => array(),
		'prerender'    => array(),
	);

	foreach ( $hints as $relation_type => $urls ) {
		$unique_urls = array();

		/**
		 * Filters domains and URLs for resource hints of relation type.
		 *
		 * @since 4.6.0
		 * @since 4.7.0 The `$urls` parameter accepts arrays of specific HTML attributes
		 *              as its child elements.
		 *
		 * @param array  $urls {
		 *     Array of resources and their attributes, or URLs to print for resource hints.
		 *
		 *     @type array|string ...$0 {
		 *         Array of resource attributes, or a URL string.
		 *
		 *         @type string $href        URL to include in resource hints. Required.
		 *         @type string $as          How the browser should treat the resource
		 *                                   (`script`, `style`, `image`, `document`, etc).
		 *         @type string $crossorigin Indicates the CORS policy of the specified resource.
		 *         @type float  $pr          Expected probability that the resource hint will be used.
		 *         @type string $type        Type of the resource (`text/html`, `text/css`, etc).
		 *     }
		 * }
		 * @param string $relation_type The relation type the URLs are printed for,
		 *                              e.g. 'preconnect' or 'prerender'.
		 */
		$urls = apply_filters( 'wp_resource_hints', $urls, $relation_type );

		foreach ( $urls as $key => $url ) {
			$atts = array();

			if ( is_array( $url ) ) {
				if ( isset( $url['href'] ) ) {
					$atts = $url;
					$url  = $url['href'];
				} else {
					continue;
				}
			}

			$url = esc_url( $url, array( 'http', 'https' ) );

			if ( ! $url ) {
				continue;
			}

			if ( isset( $unique_urls[ $url ] ) ) {
				continue;
			}

			if ( in_array( $relation_type, array( 'preconnect', 'dns-prefetch' ), true ) ) {
				$parsed = wp_parse_url( $url );

				if ( empty( $parsed['host'] ) ) {
					continue;
				}

				if ( 'preconnect' === $relation_type && ! empty( $parsed['scheme'] ) ) {
					$url = $parsed['scheme'] . '://' . $parsed['host'];
				} else {
					// Use protocol-relative URLs for dns-prefetch or if scheme is missing.
					$url = '//' . $parsed['host'];
				}
			}

			$atts['rel']  = $relation_type;
			$atts['href'] = $url;

			$unique_urls[ $url ] = $atts;
		}

		foreach ( $unique_urls as $atts ) {
			$html = '';

			foreach ( $atts as $attr => $value ) {
				if ( ! is_scalar( $value )
					|| ( ! in_array( $attr, array( 'as', 'crossorigin', 'href', 'pr', 'rel', 'type' ), true ) && ! is_numeric( $attr ) )
				) {

					continue;
				}

				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );

				if ( ! is_string( $attr ) ) {
					$html .= " $value";
				} else {
					$html .= " $attr='$value'";
				}
			}

			$html = trim( $html );

			echo "<link $html />\n";
		}
	}
}

/**
 * Prints resource preloads directives to browsers.
 *
 * Gives directive to browsers to preload specific resources that website will
 * need very soon, this ensures that they are available earlier and are less
 * likely to block the page's render. Preload directives should not be used for
 * non-render-blocking elements, as then they would compete with the
 * render-blocking ones, slowing down the render.
 *
 * These performance improving indicators work by using `<link rel="preload">`.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Link_types/preload
 * @link https://web.dev/preload-responsive-images/
 *
 * @since 6.1.0
 */
function wp_preload_resources() {
	/**
	 * Filters domains and URLs for resource preloads.
	 *
	 * @since 6.1.0
	 *
	 * @param array  $preload_resources {
	 *     Array of resources and their attributes, or URLs to print for resource preloads.
	 *
	 *     @type array ...$0 {
	 *         Array of resource attributes.
	 *
	 *         @type string $href        URL to include in resource preloads. Required.
	 *         @type string $as          How the browser should treat the resource
	 *                                   (`script`, `style`, `image`, `document`, etc).
	 *         @type string $crossorigin Indicates the CORS policy of the specified resource.
	 *         @type string $type        Type of the resource (`text/html`, `text/css`, etc).
	 *         @type string $media       Accepts media types or media queries. Allows responsive preloading.
	 *         @type string $imagesizes  Responsive source size to the source Set.
	 *         @type string $imagesrcset Responsive image sources to the source set.
	 *     }
	 * }
	 */
	$preload_resources = apply_filters( 'wp_preload_resources', array() );

	if ( ! is_array( $preload_resources ) ) {
		return;
	}

	$unique_resources = array();

	// Parse the complete resource list and extract unique resources.
	foreach ( $preload_resources as $resource ) {
		if ( ! is_array( $resource ) ) {
			continue;
		}

		$attributes = $resource;
		if ( isset( $resource['href'] ) ) {
			$href = $resource['href'];
			if ( isset( $unique_resources[ $href ] ) ) {
				continue;
			}
			$unique_resources[ $href ] = $attributes;
			// Media can use imagesrcset and not href.
		} elseif ( ( 'image' === $resource['as'] ) &&
			( isset( $resource['imagesrcset'] ) || isset( $resource['imagesizes'] ) )
		) {
			if ( isset( $unique_resources[ $resource['imagesrcset'] ] ) ) {
				continue;
			}
			$unique_resources[ $resource['imagesrcset'] ] = $attributes;
		} else {
			continue;
		}
	}

	// Build and output the HTML for each unique resource.
	foreach ( $unique_resources as $unique_resource ) {
		$html = '';

		foreach ( $unique_resource as $resource_key => $resource_value ) {
			if ( ! is_scalar( $resource_value ) ) {
				continue;
			}

			// Ignore non-supported attributes.
			$non_supported_attributes = array( 'as', 'crossorigin', 'href', 'imagesrcset', 'imagesizes', 'type', 'media' );
			if ( ! in_array( $resource_key, $non_supported_attributes, true ) && ! is_numeric( $resource_key ) ) {
				continue;
			}

			// imagesrcset only usable when preloading image, ignore otherwise.
			if ( ( 'imagesrcset' === $resource_key ) && ( ! isset( $unique_resource['as'] ) || ( 'image' !== $unique_resource['as'] ) ) ) {
				continue;
			}

			// imagesizes only usable when preloading image and imagesrcset present, ignore otherwise.
			if ( ( 'imagesizes' === $resource_key ) &&
				( ! isset( $unique_resource['as'] ) || ( 'image' !== $unique_resource['as'] ) || ! isset( $unique_resource['imagesrcset'] ) )
			) {
				continue;
			}

			$resource_value = ( 'href' === $resource_key ) ? esc_url( $resource_value, array( 'http', 'https' ) ) : esc_attr( $resource_value );

			if ( ! is_string( $resource_key ) ) {
				$html .= " $resource_value";
			} else {
				$html .= " $resource_key='$resource_value'";
			}
		}
		$html = trim( $html );

		printf( "<link rel='preload' %s />\n", $html );
	}
}

/**
 * Retrieves a list of unique hosts of all enqueued scripts and styles.
 *
 * @since 4.6.0
 *
 * @global WP_Scripts $wp_scripts The WP_Scripts object for printing scripts.
 * @global WP_Styles  $wp_styles  The WP_Styles object for printing styles.
 *
 * @return string[] A list of unique hosts of enqueued scripts and styles.
 */
function wp_dependencies_unique_hosts() {
	global $wp_scripts, $wp_styles;

	$unique_hosts = array();

	foreach ( array( $wp_scripts, $wp_styles ) as $dependencies ) {
		if ( $dependencies instanceof WP_Dependencies && ! empty( $dependencies->queue ) ) {
			foreach ( $dependencies->queue as $handle ) {
				if ( ! isset( $dependencies->registered[ $handle ] ) ) {
					continue;
				}

				/* @var _WP_Dependency $dependency */
				$dependency = $dependencies->registered[ $handle ];
				$parsed     = wp_parse_url( $dependency->src );

				if ( ! empty( $parsed['host'] )
					&& ! in_array( $parsed['host'], $unique_hosts, true ) && $parsed['host'] !== $_SERVER['SERVER_NAME']
				) {
					$unique_hosts[] = $parsed['host'];
				}
			}
		}
	}

	return $unique_hosts;
}

/**
 * Determines whether the user can access the visual editor.
 *
 * Checks if the user can access the visual editor and that it's supported by the user's browser.
 *
 * @since 2.0.0
 *
 * @global bool $wp_rich_edit Whether the user can access the visual editor.
 * @global bool $is_gecko     Whether the browser is Gecko-based.
 * @global bool $is_opera     Whether the browser is Opera.
 * @global bool $is_safari    Whether the browser is Safari.
 * @global bool $is_chrome    Whether the browser is Chrome.
 * @global bool $is_IE        Whether the browser is Internet Explorer.
 * @global bool $is_edge      Whether the browser is Microsoft Edge.
 *
 * @return bool True if the user can access the visual editor, false otherwise.
 */
function user_can_richedit() {
	global $wp_rich_edit, $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge;

	if ( ! isset( $wp_rich_edit ) ) {
		$wp_rich_edit = false;

		if ( 'true' === get_user_option( 'rich_editing' ) || ! is_user_logged_in() ) { // Default to 'true' for logged out users.
			if ( $is_safari ) {
				$wp_rich_edit = ! wp_is_mobile() || ( preg_match( '!AppleWebKit/(\d+)!', $_SERVER['HTTP_USER_AGENT'], $match ) && (int) $match[1] >= 534 );
			} elseif ( $is_IE ) {
				$wp_rich_edit = ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0;' ) !== false );
			} elseif ( $is_gecko || $is_chrome || $is_edge || ( $is_opera && ! wp_is_mobile() ) ) {
				$wp_rich_edit = true;
			}
		}
	}

	/**
	 * Filters whether the user can access the visual editor.
	 *
	 * @since 2.1.0
	 *
	 * @param bool $wp_rich_edit Whether the user can access the visual editor.
	 */
	return apply_filters( 'user_can_richedit', $wp_rich_edit );
}

/**
 * Finds out which editor should be displayed by default.
 *
 * Works out which of the two editors to display as the current editor for a
 * user. The 'html' setting is for the "Text" editor tab.
 *
 * @since 2.5.0
 *
 * @return string Either 'tinymce', or 'html', or 'test'
 */
function wp_default_editor() {
	$r = user_can_richedit() ? 'tinymce' : 'html'; // Defaults.
	if ( wp_get_current_user() ) { // Look for cookie.
		$ed = get_user_setting( 'editor', 'tinymce' );
		$r  = ( in_array( $ed, array( 'tinymce', 'html', 'test' ), true ) ) ? $ed : $r;
	}

	/**
	 * Filters which editor should be displayed by default.
	 *
	 * @since 2.5.0
	 *
	 * @param string $r Which editor should be displayed by default. Either 'tinymce', 'html', or 'test'.
	 */
	return apply_filters( 'wp_default_editor', $r );
}

/**
 * Renders an editor.
 *
 * Using this function is the proper way to output all needed components for both TinyMCE and Quicktags.
 * _WP_Editors should not be used directly. See https://core.trac.wordpress.org/ticket/17144.
 *
 * NOTE: Once initialized the TinyMCE editor cannot be safely moved in the DOM. For that reason
 * running wp_editor() inside of a meta box is not a good idea unless only Quicktags is used.
 * On the post edit screen several actions can be used to include additional editors
 * containing TinyMCE: 'edit_page_form', 'edit_form_advanced' and 'dbx_post_sidebar'.
 * See https://core.trac.wordpress.org/ticket/19173 for more information.
 *
 * @see _WP_Editors::editor()
 * @see _WP_Editors::parse_settings()
 * @since 3.3.0
 *
 * @param string $content   Initial content for the editor.
 * @param string $editor_id HTML ID attribute value for the textarea and TinyMCE.
 *                          Should not contain square brackets.
 * @param array  $settings  See _WP_Editors::parse_settings() for description.
 */
function wp_editor( $content, $editor_id, $settings = array() ) {
	if ( ! class_exists( '_WP_Editors', false ) ) {
		require ABSPATH . WPINC . '/class-wp-editor.php';
	}
	_WP_Editors::editor( $content, $editor_id, $settings );
}

/**
 * Outputs the editor scripts, stylesheets, and default settings.
 *
 * The editor can be initialized when needed after page load.
 * See wp.editor.initialize() in wp-admin/js/editor.js for initialization options.
 *
 * @uses _WP_Editors
 * @since 4.8.0
 */
function wp_enqueue_editor() {
	if ( ! class_exists( '_WP_Editors', false ) ) {
		require ABSPATH . WPINC . '/class-wp-editor.php';
	}

	_WP_Editors::enqueue_default_editor();
}

/**
 * Enqueues assets needed by the code editor for the given settings.
 *
 * @since 4.9.0
 *
 * @see wp_enqueue_editor()
 * @see wp_get_code_editor_settings();
 * @see _WP_Editors::parse_settings()
 *
 * @param array $args {
 *     Args.
 *
 *     @type string   $type       The MIME type of the file to be edited.
 *     @type string   $file       Filename to be edited. Extension is used to sniff the type. Can be supplied as alternative to `$type` param.
 *     @type WP_Theme $theme      Theme being edited when on the theme file editor.
 *     @type string   $plugin     Plugin being edited when on the plugin file editor.
 *     @type array    $codemirror Additional CodeMirror setting overrides.
 *     @type array    $csslint    CSSLint rule overrides.
 *     @type array    $jshint     JSHint rule overrides.
 *     @type array    $htmlhint   HTMLHint rule overrides.
 * }
 * @return array|false Settings for the enqueued code editor, or false if the editor was not enqueued.
 */
function wp_enqueue_code_editor( $args ) {
	if ( is_user_logged_in() && 'false' === wp_get_current_user()->syntax_highlighting ) {
		return false;
	}

	$settings = wp_get_code_editor_settings( $args );

	if ( empty( $settings ) || empty( $settings['codemirror'] ) ) {
		return false;
	}

	wp_enqueue_script( 'code-editor' );
	wp_enqueue_style( 'code-editor' );

	if ( isset( $settings['codemirror']['mode'] ) ) {
		$mode = $settings['codemirror']['mode'];
		if ( is_string( $mode ) ) {
			$mode = array(
				'name' => $mode,
			);
		}

		if ( ! empty( $settings['codemirror']['lint'] ) ) {
			switch ( $mode['name'] ) {
				case 'css':
				case 'text/css':
				case 'text/x-scss':
				case 'text/x-less':
					wp_enqueue_script( 'csslint' );
					break;
				case 'htmlmixed':
				case 'text/html':
				case 'php':
				case 'application/x-httpd-php':
				case 'text/x-php':
					wp_enqueue_script( 'htmlhint' );
					wp_enqueue_script( 'csslint' );
					wp_enqueue_script( 'jshint' );
					if ( ! current_user_can( 'unfiltered_html' ) ) {
						wp_enqueue_script( 'htmlhint-kses' );
					}
					break;
				case 'javascript':
				case 'application/ecmascript':
				case 'application/json':
				case 'application/javascript':
				case 'application/ld+json':
				case 'text/typescript':
				case 'application/typescript':
					wp_enqueue_script( 'jshint' );
					wp_enqueue_script( 'jsonlint' );
					break;
			}
		}
	}

	wp_add_inline_script( 'code-editor', sprintf( 'jQuery.extend( wp.codeEditor.defaultSettings, %s );', wp_json_encode( $settings ) ) );

	/**
	 * Fires when scripts and styles are enqueued for the code editor.
	 *
	 * @since 4.9.0
	 *
	 * @param array $settings Settings for the enqueued code editor.
	 */
	do_action( 'wp_enqueue_code_editor', $settings );

	return $settings;
}

/**
 * Generates and returns code editor settings.
 *
 * @since 5.0.0
 *
 * @see wp_enqueue_code_editor()
 *
 * @param array $args {
 *     Args.
 *
 *     @type string   $type       The MIME type of the file to be edited.
 *     @type string   $file       Filename to be edited. Extension is used to sniff the type. Can be supplied as alternative to `$type` param.
 *     @type WP_Theme $theme      Theme being edited when on the theme file editor.
 *     @type string   $plugin     Plugin being edited when on the plugin file editor.
 *     @type array    $codemirror Additional CodeMirror setting overrides.
 *     @type array    $csslint    CSSLint rule overrides.
 *     @type array    $jshint     JSHint rule overrides.
 *     @type array    $htmlhint   HTMLHint rule overrides.
 * }
 * @return array|false Settings for the code editor.
 */
function wp_get_code_editor_settings( $args ) {
	$settings = array(
		'codemirror' => array(
			'indentUnit'       => 4,
			'indentWithTabs'   => true,
			'inputStyle'       => 'contenteditable',
			'lineNumbers'      => true,
			'lineWrapping'     => true,
			'styleActiveLine'  => true,
			'continueComments' => true,
			'extraKeys'        => array(
				'Ctrl-Space' => 'autocomplete',
				'Ctrl-/'     => 'toggleComment',
				'Cmd-/'      => 'toggleComment',
				'Alt-F'      => 'findPersistent',
				'Ctrl-F'     => 'findPersistent',
				'Cmd-F'      => 'findPersistent',
			),
			'direction'        => 'ltr', // Code is shown in LTR even in RTL languages.
			'gutters'          => array(),
		),
		'csslint'    => array(
			'errors'                    => true, // Parsing errors.
			'box-model'                 => true,
			'display-property-grouping' => true,
			'duplicate-properties'      => true,
			'known-properties'          => true,
			'outline-none'              => true,
		),
		'jshint'     => array(
			// The following are copied from <https://github.com/WordPress/wordpress-develop/blob/4.8.1/.jshintrc>.
			'boss'     => true,
			'curly'    => true,
			'eqeqeq'   => true,
			'eqnull'   => true,
			'es3'      => true,
			'expr'     => true,
			'immed'    => true,
			'noarg'    => true,
			'nonbsp'   => true,
			'onevar'   => true,
			'quotmark' => 'single',
			'trailing' => true,
			'undef'    => true,
			'unused'   => true,

			'browser'  => true,

			'globals'  => array(
				'_'        => false,
				'Backbone' => false,
				'jQuery'   => false,
				'JSON'     => false,
				'wp'       => false,
			),
		),
		'htmlhint'   => array(
			'tagname-lowercase'        => true,
			'attr-lowercase'           => true,
			'attr-value-double-quotes' => false,
			'doctype-first'            => false,
			'tag-pair'                 => true,
			'spec-char-escape'         => true,
			'id-unique'                => true,
			'src-not-empty'            => true,
			'attr-no-duplication'      => true,
			'alt-require'              => true,
			'space-tab-mixed-disabled' => 'tab',
			'attr-unsafe-chars'        => true,
		),
	);

	$type = '';
	if ( isset( $args['type'] ) ) {
		$type = $args['type'];

		// Remap MIME types to ones that CodeMirror modes will recognize.
		if ( 'application/x-patch' === $type || 'text/x-patch' === $type ) {
			$type = 'text/x-diff';
		}
	} elseif ( isset( $args['file'] ) && false !== strpos( basename( $args['file'] ), '.' ) ) {
		$extension = strtolower( pathinfo( $args['file'], PATHINFO_EXTENSION ) );
		foreach ( wp_get_mime_types() as $exts => $mime ) {
			if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
				$type = $mime;
				break;
			}
		}

		// Supply any types that are not matched by wp_get_mime_types().
		if ( empty( $type ) ) {
			switch ( $extension ) {
				case 'conf':
					$type = 'text/nginx';
					break;
				case 'css':
					$type = 'text/css';
					break;
				case 'diff':
				case 'patch':
					$type = 'text/x-diff';
					break;
				case 'html':
				case 'htm':
					$type = 'text/html';
					break;
				case 'http':
					$type = 'message/http';
					break;
				case 'js':
					$type = 'text/javascript';
					break;
				case 'json':
					$type = 'application/json';
					break;
				case 'jsx':
					$type = 'text/jsx';
					break;
				case 'less':
					$type = 'text/x-less';
					break;
				case 'md':
					$type = 'text/x-gfm';
					break;
				case 'php':
				case 'phtml':
				case 'php3':
				case 'php4':
				case 'php5':
				case 'php7':
				case 'phps':
					$type = 'application/x-httpd-php';
					break;
				case 'scss':
					$type = 'text/x-scss';
					break;
				case 'sass':
					$type = 'text/x-sass';
					break;
				case 'sh':
				case 'bash':
					$type = 'text/x-sh';
					break;
				case 'sql':
					$type = 'text/x-sql';
					break;
				case 'svg':
					$type = 'application/svg+xml';
					break;
				case 'xml':
					$type = 'text/xml';
					break;
				case 'yml':
				case 'yaml':
					$type = 'text/x-yaml';
					break;
				case 'txt':
				default:
					$type = 'text/plain';
					break;
			}
		}
	}

	if ( in_array( $type, array( 'text/css', 'text/x-scss', 'text/x-less', 'text/x-sass' ), true ) ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => $type,
				'lint'              => false,
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			)
		);
	} elseif ( 'text/x-diff' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode' => 'diff',
			)
		);
	} elseif ( 'text/html' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => 'htmlmixed',
				'lint'              => true,
				'autoCloseBrackets' => true,
				'autoCloseTags'     => true,
				'matchTags'         => array(
					'bothTags' => true,
				),
			)
		);

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$settings['htmlhint']['kses'] = wp_kses_allowed_html( 'post' );
		}
	} elseif ( 'text/x-gfm' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'                => 'gfm',
				'highlightFormatting' => true,
			)
		);
	} elseif ( 'application/javascript' === $type || 'text/javascript' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => 'javascript',
				'lint'              => true,
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			)
		);
	} elseif ( false !== strpos( $type, 'json' ) ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => array(
					'name' => 'javascript',
				),
				'lint'              => true,
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			)
		);
		if ( 'application/ld+json' === $type ) {
			$settings['codemirror']['mode']['jsonld'] = true;
		} else {
			$settings['codemirror']['mode']['json'] = true;
		}
	} elseif ( false !== strpos( $type, 'jsx' ) ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => 'jsx',
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			)
		);
	} elseif ( 'text/x-markdown' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'                => 'markdown',
				'highlightFormatting' => true,
			)
		);
	} elseif ( 'text/nginx' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode' => 'nginx',
			)
		);
	} elseif ( 'application/x-httpd-php' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => 'php',
				'autoCloseBrackets' => true,
				'autoCloseTags'     => true,
				'matchBrackets'     => true,
				'matchTags'         => array(
					'bothTags' => true,
				),
			)
		);
	} elseif ( 'text/x-sql' === $type || 'text/x-mysql' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => 'sql',
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			)
		);
	} elseif ( false !== strpos( $type, 'xml' ) ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode'              => 'xml',
				'autoCloseBrackets' => true,
				'autoCloseTags'     => true,
				'matchTags'         => array(
					'bothTags' => true,
				),
			)
		);
	} elseif ( 'text/x-yaml' === $type ) {
		$settings['codemirror'] = array_merge(
			$settings['codemirror'],
			array(
				'mode' => 'yaml',
			)
		);
	} else {
		$settings['codemirror']['mode'] = $type;
	}

	if ( ! empty( $settings['codemirror']['lint'] ) ) {
		$settings['codemirror']['gutters'][] = 'CodeMirror-lint-markers';
	}

	// Let settings supplied via args override any defaults.
	foreach ( wp_array_slice_assoc( $args, array( 'codemirror', 'csslint', 'jshint', 'htmlhint' ) ) as $key => $value ) {
		$settings[ $key ] = array_merge(
			$settings[ $key ],
			$value
		);
	}

	/**
	 * Filters settings that are passed into the code editor.
	 *
	 * Returning a falsey value will disable the syntax-highlighting code editor.
	 *
	 * @since 4.9.0
	 *
	 * @param array $settings The array of settings passed to the code editor.
	 *                        A falsey value disables the editor.
	 * @param array $args {
	 *     Args passed when calling `get_code_editor_settings()`.
	 *
	 *     @type string   $type       The MIME type of the file to be edited.
	 *     @type string   $file       Filename being edited.
	 *     @type WP_Theme $theme      Theme being edited when on the theme file editor.
	 *     @type string   $plugin     Plugin being edited when on the plugin file editor.
	 *     @type array    $codemirror Additional CodeMirror setting overrides.
	 *     @type array    $csslint    CSSLint rule overrides.
	 *     @type array    $jshint     JSHint rule overrides.
	 *     @type array    $htmlhint   HTMLHint rule overrides.
	 * }
	 */
	return apply_filters( 'wp_code_editor_settings', $settings, $args );
}

/**
 * Retrieves the contents of the search WordPress query variable.
 *
 * The search query string is passed through esc_attr() to ensure that it is safe
 * for placing in an HTML attribute.
 *
 * @since 2.3.0
 *
 * @param bool $escaped Whether the result is escaped. Default true.
 *                      Only use when you are later escaping it. Do not use unescaped.
 * @return string
 */
function get_search_query( $escaped = true ) {
	/**
	 * Filters the contents of the search query variable.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed $search Contents of the search query variable.
	 */
	$query = apply_filters( 'get_search_query', get_query_var( 's' ) );

	if ( $escaped ) {
		$query = esc_attr( $query );
	}
	return $query;
}

/**
 * Displays the contents of the search query variable.
 *
 * The search query string is passed through esc_attr() to ensure that it is safe
 * for placing in an HTML attribute.
 *
 * @since 2.1.0
 */
function the_search_query() {
	/**
	 * Filters the contents of the search query variable for display.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed $search Contents of the search query variable.
	 */
	echo esc_attr( apply_filters( 'the_search_query', get_search_query( false ) ) );
}

/**
 * Gets the language attributes for the 'html' tag.
 *
 * Builds up a set of HTML attributes containing the text direction and language
 * information for the page.
 *
 * @since 4.3.0
 *
 * @param string $doctype Optional. The type of HTML document. Accepts 'xhtml' or 'html'. Default 'html'.
 * @return string A space-separated list of language attributes.
 */
function get_language_attributes( $doctype = 'html' ) {
	$attributes = array();

	if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		$attributes[] = 'dir="rtl"';
	}

	$lang = get_bloginfo( 'language' );
	if ( $lang ) {
		if ( 'text/html' === get_option( 'html_type' ) || 'html' === $doctype ) {
			$attributes[] = 'lang="' . esc_attr( $lang ) . '"';
		}

		if ( 'text/html' !== get_option( 'html_type' ) || 'xhtml' === $doctype ) {
			$attributes[] = 'xml:lang="' . esc_attr( $lang ) . '"';
		}
	}

	$output = implode( ' ', $attributes );

	/**
	 * Filters the language attributes for display in the 'html' tag.
	 *
	 * @since 2.5.0
	 * @since 4.3.0 Added the `$doctype` parameter.
	 *
	 * @param string $output A space-separated list of language attributes.
	 * @param string $doctype The type of HTML document (xhtml|html).
	 */
	return apply_filters( 'language_attributes', $output, $doctype );
}

/**
 * Displays the language attributes for the 'html' tag.
 *
 * Builds up a set of HTML attributes containing the text direction and language
 * information for the page.
 *
 * @since 2.1.0
 * @since 4.3.0 Converted into a wrapper for get_language_attributes().
 *
 * @param string $doctype Optional. The type of HTML document. Accepts 'xhtml' or 'html'. Default 'html'.
 */
function language_attributes( $doctype = 'html' ) {
	echo get_language_attributes( $doctype );
}

/**
 * Retrieves paginated links for archive post pages.
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
 * and see add_query_arg() for more information.
 *
 * The 'before_page_number' and 'after_page_number' arguments allow users to
 * augment the links themselves. Typically this might be to add context to the
 * numbered links so that screen reader users understand what the links are for.
 * The text strings are added before and after the page number - within the
 * anchor tag.
 *
 * @since 2.1.0
 * @since 4.9.0 Added the `aria_current` argument.
 *
 * @global WP_Query   $wp_query   WordPress Query object.
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string|array $args {
 *     Optional. Array or string of arguments for generating paginated links for archives.
 *
 *     @type string $base               Base of the paginated url. Default empty.
 *     @type string $format             Format for the pagination structure. Default empty.
 *     @type int    $total              The total amount of pages. Default is the value WP_Query's
 *                                      `max_num_pages` or 1.
 *     @type int    $current            The current page number. Default is 'paged' query var or 1.
 *     @type string $aria_current       The value for the aria-current attribute. Possible values are 'page',
 *                                      'step', 'location', 'date', 'time', 'true', 'false'. Default is 'page'.
 *     @type bool   $show_all           Whether to show all pages. Default false.
 *     @type int    $end_size           How many numbers on either the start and the end list edges.
 *                                      Default 1.
 *     @type int    $mid_size           How many numbers to either side of the current pages. Default 2.
 *     @type bool   $prev_next          Whether to include the previous and next links in the list. Default true.
 *     @type string $prev_text          The previous page text. Default '&laquo; Previous'.
 *     @type string $next_text          The next page text. Default 'Next &raquo;'.
 *     @type string $type               Controls format of the returned value. Possible values are 'plain',
 *                                      'array' and 'list'. Default is 'plain'.
 *     @type array  $add_args           An array of query args to add. Default false.
 *     @type string $add_fragment       A string to append to each link. Default empty.
 *     @type string $before_page_number A string to appear before the page number. Default empty.
 *     @type string $after_page_number  A string to append after the page number. Default empty.
 * }
 * @return string|string[]|void String of page links or array of page links, depending on 'type' argument.
 *                              Void if total number of pages is less than 2.
 */
function paginate_links( $args = '' ) {
	global $wp_query, $wp_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
		'format'             => $format, // ?page=%#% : %#% is replaced by the page number.
		'total'              => $total,
		'current'            => $current,
		'aria_current'       => 'page',
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => __( '&laquo; Previous' ),
		'next_text'          => __( 'Next &raquo;' ),
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => array(), // Array of query args to add.
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional query vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
		$format_query = isset( $format[1] ) ? $format[1] : '';
		wp_parse_str( $format_query, $format_args );

		// Find the query args of the requested URL.
		wp_parse_str( $url_parts[1], $url_query_args );

		// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
		foreach ( $format_args as $format_arg => $format_arg_value ) {
			unset( $url_query_args[ $format_arg ] );
		}

		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
	}

	// Who knows what else people pass in $args.
	$total = (int) $args['total'];
	if ( $total < 2 ) {
		return;
	}
	$current  = (int) $args['current'];
	$end_size = (int) $args['end_size']; // Out of bounds? Make it the default.
	if ( $end_size < 1 ) {
		$end_size = 1;
	}
	$mid_size = (int) $args['mid_size'];
	if ( $mid_size < 0 ) {
		$mid_size = 2;
	}

	$add_args   = $args['add_args'];
	$r          = '';
	$page_links = array();
	$dots       = false;

	if ( $args['prev_next'] && $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<a class="prev page-numbers" href="%s">%s</a>',
			/**
			 * Filters the paginated links for the given archive pages.
			 *
			 * @since 3.0.0
			 *
			 * @param string $link The paginated link URL.
			 */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['prev_text']
		);
	endif;

	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = sprintf(
				'<span aria-current="%s" class="page-numbers current">%s</span>',
				esc_attr( $args['aria_current'] ),
				$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
			);

			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $add_args ) {
					$link = add_query_arg( $add_args, $link );
				}
				$link .= $args['add_fragment'];

				$page_links[] = sprintf(
					'<a class="page-numbers" href="%s">%s</a>',
					/** This filter is documented in wp-includes/general-template.php */
					esc_url( apply_filters( 'paginate_links', $link ) ),
					$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
				);

				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';

				$dots = false;
			endif;
		endif;
	endfor;

	if ( $args['prev_next'] && $current && $current < $total ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<a class="next page-numbers" href="%s">%s</a>',
			/** This filter is documented in wp-includes/general-template.php */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['next_text']
		);
	endif;

	switch ( $args['type'] ) {
		case 'array':
			return $page_links;

		case 'list':
			$r .= "<ul class='page-numbers'>\n\t<li>";
			$r .= implode( "</li>\n\t<li>", $page_links );
			$r .= "</li>\n</ul>\n";
			break;

		default:
			$r = implode( "\n", $page_links );
			break;
	}

	/**
	 * Filters the HTML output of paginated links for archives.
	 *
	 * @since 5.7.0
	 *
	 * @param string $r    HTML output.
	 * @param array  $args An array of arguments. See paginate_links()
	 *                     for information on accepted arguments.
	 */
	$r = apply_filters( 'paginate_links_output', $r, $args );

	return $r;
}

/**
 * Registers an admin color scheme css file.
 *
 * Allows a plugin to register a new admin color scheme. For example:
 *
 *     wp_admin_css_color( 'classic', __( 'Classic' ), admin_url( "css/colors-classic.css" ), array(
 *         '#07273E', '#14568A', '#D54E21', '#2683AE'
 *     ) );
 *
 * @since 2.5.0
 *
 * @global array $_wp_admin_css_colors
 *
 * @param string $key    The unique key for this theme.
 * @param string $name   The name of the theme.
 * @param string $url    The URL of the CSS file containing the color scheme.
 * @param array  $colors Optional. An array of CSS color definition strings which are used
 *                       to give the user a feel for the theme.
 * @param array  $icons {
 *     Optional. CSS color definitions used to color any SVG icons.
 *
 *     @type string $base    SVG icon base color.
 *     @type string $focus   SVG icon color on focus.
 *     @type string $current SVG icon color of current admin menu link.
 * }
 */
function wp_admin_css_color( $key, $name, $url, $colors = array(), $icons = array() ) {
	global $_wp_admin_css_colors;

	if ( ! isset( $_wp_admin_css_colors ) ) {
		$_wp_admin_css_colors = array();
	}

	$_wp_admin_css_colors[ $key ] = (object) array(
		'name'        => $name,
		'url'         => $url,
		'colors'      => $colors,
		'icon_colors' => $icons,
	);
}

/**
 * Registers the default admin color schemes.
 *
 * Registers the initial set of eight color schemes in the Profile section
 * of the dashboard which allows for styling the admin menu and toolbar.
 *
 * @see wp_admin_css_color()
 *
 * @since 3.0.0
 */
function register_admin_color_schemes() {
	$suffix  = is_rtl() ? '-rtl' : '';
	$suffix .= SCRIPT_DEBUG ? '' : '.min';

	wp_admin_css_color(
		'fresh',
		_x( 'Default', 'admin color scheme' ),
		false,
		array( '#1d2327', '#2c3338', '#2271b1', '#72aee6' ),
		array(
			'base'    => '#a7aaad',
			'focus'   => '#72aee6',
			'current' => '#fff',
		)
	);

}

/**
 * Displays the URL of a WordPress admin CSS file.
 *
 * @see WP_Styles::_css_href() and its {@see 'style_loader_src'} filter.
 *
 * @since 2.3.0
 *
 * @param string $file file relative to wp-admin/ without its ".css" extension.
 * @return string
 */
function wp_admin_css_uri( $file = 'wp-admin' ) {
	if ( defined( 'WP_INSTALLING' ) ) {
		$_file = "./$file.css";
	} else {
		$_file = admin_url( "$file.css" );
	}
	$_file = add_query_arg( 'version', get_bloginfo( 'version' ), $_file );

	/**
	 * Filters the URI of a WordPress admin CSS file.
	 *
	 * @since 2.3.0
	 *
	 * @param string $_file Relative path to the file with query arguments attached.
	 * @param string $file  Relative path to the file, minus its ".css" extension.
	 */
	return apply_filters( 'wp_admin_css_uri', $_file, $file );
}

/**
 * Enqueues or directly prints a stylesheet link to the specified CSS file.
 *
 * "Intelligently" decides to enqueue or to print the CSS file. If the
 * {@see 'wp_print_styles'} action has *not* yet been called, the CSS file will be
 * enqueued. If the {@see 'wp_print_styles'} action has been called, the CSS link will
 * be printed. Printing may be forced by passing true as the $force_echo
 * (second) parameter.
 *
 * For backward compatibility with WordPress 2.3 calling method: If the $file
 * (first) parameter does not correspond to a registered CSS file, we assume
 * $file is a file relative to wp-admin/ without its ".css" extension. A
 * stylesheet link to that generated URL is printed.
 *
 * @since 2.3.0
 *
 * @param string $file       Optional. Style handle name or file name (without ".css" extension) relative
 *                           to wp-admin/. Defaults to 'wp-admin'.
 * @param bool   $force_echo Optional. Force the stylesheet link to be printed rather than enqueued.
 */
function wp_admin_css( $file = 'wp-admin', $force_echo = false ) {
	// For backward compatibility.
	$handle = 0 === strpos( $file, 'css/' ) ? substr( $file, 4 ) : $file;

	if ( wp_styles()->query( $handle ) ) {
		if ( $force_echo || did_action( 'wp_print_styles' ) ) {
			// We already printed the style queue. Print this one immediately.
			wp_print_styles( $handle );
		} else {
			// Add to style queue.
			wp_enqueue_style( $handle );
		}
		return;
	}

	$stylesheet_link = sprintf(
		"<link rel='stylesheet' href='%s' type='text/css' />\n",
		esc_url( wp_admin_css_uri( $file ) )
	);

	/**
	 * Filters the stylesheet link to the specified CSS file.
	 *
	 * If the site is set to display right-to-left, the RTL stylesheet link
	 * will be used instead.
	 *
	 * @since 2.3.0
	 * @param string $stylesheet_link HTML link element for the stylesheet.
	 * @param string $file            Style handle name or filename (without ".css" extension)
	 *                                relative to wp-admin/. Defaults to 'wp-admin'.
	 */
	echo apply_filters( 'wp_admin_css', $stylesheet_link, $file );

	if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		$rtl_stylesheet_link = sprintf(
			"<link rel='stylesheet' href='%s' type='text/css' />\n",
			esc_url( wp_admin_css_uri( "$file-rtl" ) )
		);

		/** This filter is documented in wp-includes/general-template.php */
		echo apply_filters( 'wp_admin_css', $rtl_stylesheet_link, "$file-rtl" );
	}
}

/**
 * Enqueues the default ThickBox js and css.
 *
 * If any of the settings need to be changed, this can be done with another js
 * file similar to media-upload.js. That file should
 * require array('thickbox') to ensure it is loaded after.
 *
 * @since 2.5.0
 */
function add_thickbox() {
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );

	if ( is_network_admin() ) {
		add_action( 'admin_head', '_thickbox_path_admin_subfolder' );
	}
}

/**
 * Displays the XHTML generator that is generated on the wp_head hook.
 *
 * See {@see 'wp_head'}.
 *
 * @since 2.5.0
 */
function wp_generator() {
	/**
	 * Filters the output of the XHTML generator tag.
	 *
	 * @since 2.5.0
	 *
	 * @param string $generator_type The XHTML generator.
	 */
	the_generator( apply_filters( 'wp_generator_type', 'xhtml' ) );
}

/**
 * Displays the generator XML or Comment for RSS, ATOM, etc.
 *
 * Returns the correct generator type for the requested output format. Allows
 * for a plugin to filter generators overall the {@see 'the_generator'} filter.
 *
 * @since 2.5.0
 *
 * @param string $type The type of generator to output - (html|xhtml|atom|rss2|rdf|comment|export).
 */
function the_generator( $type ) {
	/**
	 * Filters the output of the XHTML generator tag for display.
	 *
	 * @since 2.5.0
	 *
	 * @param string $generator_type The generator output.
	 * @param string $type           The type of generator to output. Accepts 'html',
	 *                               'xhtml', 'atom', 'rss2', 'rdf', 'comment', 'export'.
	 */
	echo apply_filters( 'the_generator', get_the_generator( $type ), $type ) . "\n";
}

/**
 * Creates the generator XML or Comment for RSS, ATOM, etc.
 *
 * Returns the correct generator type for the requested output format. Allows
 * for a plugin to filter generators on an individual basis using the
 * {@see 'get_the_generator_$type'} filter.
 *
 * @since 2.5.0
 *
 * @param string $type The type of generator to return - (html|xhtml|atom|rss2|rdf|comment|export).
 * @return string|void The HTML content for the generator.
 */
function get_the_generator( $type = '' ) {
	if ( empty( $type ) ) {

		$current_filter = current_filter();
		if ( empty( $current_filter ) ) {
			return;
		}

		switch ( $current_filter ) {
			case 'rss2_head':
			case 'commentsrss2_head':
				$type = 'rss2';
				break;
			case 'rss_head':
			case 'opml_head':
				$type = 'comment';
				break;
			case 'rdf_header':
				$type = 'rdf';
				break;
			case 'atom_head':
			case 'comments_atom_head':
			case 'app_head':
				$type = 'atom';
				break;
		}
	}

	switch ( $type ) {
		case 'html':
			$gen = '<meta name="generator" content="WordPress ' . esc_attr( get_bloginfo( 'version' ) ) . '">';
			break;
		case 'xhtml':
			$gen = '<meta name="generator" content="WordPress ' . esc_attr( get_bloginfo( 'version' ) ) . '" />';
			break;
		case 'atom':
			$gen = '<generator uri="https://wordpress.org/" version="' . esc_attr( get_bloginfo_rss( 'version' ) ) . '">WordPress</generator>';
			break;
		case 'rss2':
			$gen = '<generator>' . sanitize_url( 'https://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) ) . '</generator>';
			break;
		case 'rdf':
			$gen = '<admin:generatorAgent rdf:resource="' . sanitize_url( 'https://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) ) . '" />';
			break;
		case 'comment':
			$gen = '<!-- generator="WordPress/' . esc_attr( get_bloginfo( 'version' ) ) . '" -->';
			break;
		case 'export':
			$gen = '<!-- generator="WordPress/' . esc_attr( get_bloginfo_rss( 'version' ) ) . '" created="' . gmdate( 'Y-m-d H:i' ) . '" -->';
			break;
	}

	/**
	 * Filters the HTML for the retrieved generator type.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the generator type.
	 *
	 * Possible hook names include:
	 *
	 *  - `get_the_generator_atom`
	 *  - `get_the_generator_comment`
	 *  - `get_the_generator_export`
	 *  - `get_the_generator_html`
	 *  - `get_the_generator_rdf`
	 *  - `get_the_generator_rss2`
	 *  - `get_the_generator_xhtml`
	 *
	 * @since 2.5.0
	 *
	 * @param string $gen  The HTML markup output to wp_head().
	 * @param string $type The type of generator. Accepts 'html', 'xhtml', 'atom',
	 *                     'rss2', 'rdf', 'comment', 'export'.
	 */
	return apply_filters( "get_the_generator_{$type}", $gen, $type );
}

/**
 * Outputs the HTML checked attribute.
 *
 * Compares the first two arguments and if identical marks as checked.
 *
 * @since 1.0.0
 *
 * @param mixed $checked One of the values to compare.
 * @param mixed $current Optional. The other value to compare if not just true.
 *                       Default true.
 * @param bool  $display Optional. Whether to echo or just return the string.
 *                       Default true.
 * @return string HTML attribute or empty string.
 */
function checked( $checked, $current = true, $display = true ) {
	return __checked_selected_helper( $checked, $current, $display, 'checked' );
}

/**
 * Outputs the HTML selected attribute.
 *
 * Compares the first two arguments and if identical marks as selected.
 *
 * @since 1.0.0
 *
 * @param mixed $selected One of the values to compare.
 * @param mixed $current  Optional. The other value to compare if not just true.
 *                        Default true.
 * @param bool  $display  Optional. Whether to echo or just return the string.
 *                        Default true.
 * @return string HTML attribute or empty string.
 */
function selected( $selected, $current = true, $display = true ) {
	return __checked_selected_helper( $selected, $current, $display, 'selected' );
}

/**
 * Outputs the HTML disabled attribute.
 *
 * Compares the first two arguments and if identical marks as disabled.
 *
 * @since 3.0.0
 *
 * @param mixed $disabled One of the values to compare.
 * @param mixed $current  Optional. The other value to compare if not just true.
 *                        Default true.
 * @param bool  $display  Optional. Whether to echo or just return the string.
 *                        Default true.
 * @return string HTML attribute or empty string.
 */
function disabled( $disabled, $current = true, $display = true ) {
	return __checked_selected_helper( $disabled, $current, $display, 'disabled' );
}

/**
 * Outputs the HTML readonly attribute.
 *
 * Compares the first two arguments and if identical marks as readonly.
 *
 * @since 5.9.0
 *
 * @param mixed $readonly_value One of the values to compare.
 * @param mixed $current        Optional. The other value to compare if not just true.
 *                              Default true.
 * @param bool  $display        Optional. Whether to echo or just return the string.
 *                              Default true.
 * @return string HTML attribute or empty string.
 */
function wp_readonly( $readonly_value, $current = true, $display = true ) {
	return __checked_selected_helper( $readonly_value, $current, $display, 'readonly' );
}

/*
 * Include a compat `readonly()` function on PHP < 8.1. Since PHP 8.1,
 * `readonly` is a reserved keyword and cannot be used as a function name.
 * In order to avoid PHP parser errors, this function was extracted
 * to a separate file and is only included conditionally on PHP < 8.1.
 */
if ( PHP_VERSION_ID < 80100 ) {
	require_once __DIR__ . '/php-compat/readonly.php';
}

/**
 * Private helper function for checked, selected, disabled and readonly.
 *
 * Compares the first two arguments and if identical marks as `$type`.
 *
 * @since 2.8.0
 * @access private
 *
 * @param mixed  $helper  One of the values to compare.
 * @param mixed  $current The other value to compare if not just true.
 * @param bool   $display Whether to echo or just return the string.
 * @param string $type    The type of checked|selected|disabled|readonly we are doing.
 * @return string HTML attribute or empty string.
 */
function __checked_selected_helper( $helper, $current, $display, $type ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	if ( (string) $helper === (string) $current ) {
		$result = " $type='$type'";
	} else {
		$result = '';
	}

	if ( $display ) {
		echo $result;
	}

	return $result;
}

/**
 * Assigns a visual indicator for required form fields.
 *
 * @since 6.1.0
 *
 * @return string Indicator glyph wrapped in a `span` tag.
 */
function wp_required_field_indicator() {
	/* translators: Character to identify required form fields. */
	$glyph     = __( '*' );
	$indicator = '<span class="required">' . esc_html( $glyph ) . '</span>';

	/**
	 * Filters the markup for a visual indicator of required form fields.
	 *
	 * @since 6.1.0
	 *
	 * @param string $indicator Markup for the indicator element.
	 */
	return apply_filters( 'wp_required_field_indicator', $indicator );
}

/**
 * Creates a message to explain required form fields.
 *
 * @since 6.1.0
 *
 * @return string Message text and glyph wrapped in a `span` tag.
 */
function wp_required_field_message() {
	$message = sprintf(
		'<span class="required-field-message">%s</span>',
		/* translators: %s: Asterisk symbol (*). */
		sprintf( __( 'Required fields are marked %s' ), wp_required_field_indicator() )
	);

	/**
	 * Filters the message to explain required form fields.
	 *
	 * @since 6.1.0
	 *
	 * @param string $message Message text and glyph wrapped in a `span` tag.
	 */
	return apply_filters( 'wp_required_field_message', $message );
}

/**
 * Default settings for heartbeat.
 *
 * Outputs the nonce used in the heartbeat XHR.
 *
 * @since 3.6.0
 *
 * @param array $settings
 * @return array Heartbeat settings.
 */
function wp_heartbeat_settings( $settings ) {
	if ( ! is_admin() ) {
		$settings['ajaxurl'] = admin_url( 'admin-ajax.php', 'relative' );
	}

	if ( is_user_logged_in() ) {
		$settings['nonce'] = wp_create_nonce( 'heartbeat-nonce' );
	}

	return $settings;
}
