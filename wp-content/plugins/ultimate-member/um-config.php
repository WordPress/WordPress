<?php

	global $ultimatemember;


/***
***	@
***/

$core_pages = array(
	'user' => __('User page','ultimate-member'),
	'account' => __('Account page','ultimate-member'),
	'members' => __('Members page','ultimate-member'),
	'register' => __('Register page','ultimate-member'),
	'login' => __('Login page','ultimate-member'),
	'logout' => __('Logout page','ultimate-member'),
	'password-reset' => __('Password reset page','ultimate-member'),
);

$core_pages = apply_filters('um_core_pages', $core_pages );

foreach( $core_pages as $page_s => $page ) {

	$have_pages = $ultimatemember->query->wp_pages();

	$page_id = 'core_' . $page_s;
	$page_id = apply_filters('um_core_page_id_filter', $page_id );

	if( 'reached_maximum_limit' == $have_pages ){
			$page_setup[] = array(
		                'id'       		=> $page_id,
		                'type'     		=> 'text',
		                'title'    		=> $page,
	                	'placeholder' 	=> __('Add page ID','ultimate-member'),
						'default'       => ( isset( $ultimatemember->permalinks->core[ $page_id ] ) ) ? $ultimatemember->permalinks->core[ $page_id ] : '',
		    			'compiler' 		=> true,
	        );
	}else{
			$page_setup[] = array(
						'id'       		=> $page_id,
		                'type'     		=> 'select',
						'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
		                'title'    		=> $page,
		                'default'  		=> ( isset( $ultimatemember->permalinks->core[ $page_id ] ) ) ? $ultimatemember->permalinks->core[ $page_id ] : '' ,
						'options' 		=> $ultimatemember->query->wp_pages(),
						'placeholder' 	=> __('Choose a page...','ultimate-member'),
						'compiler' 		=> true,
	        );
	}
	
}

$this->sections[] = array(

    'icon'       => 'um-faicon-cog',
    'title'      => __( 'Setup','ultimate-member'),
    'fields'     => $page_setup

);

/***
***	@
***/

add_filter('redux/options/um_options/compiler', 'um_core_page_setting_saved', 100, 3);
function um_core_page_setting_saved($options, $css, $changed_values) {
	$core_pages = array(
		'user' => __('User page','ultimate-member'),
		'account' => __('Account page','ultimate-member'),
		'members' => __('Members page','ultimate-member'),
		'register' => __('Register page','ultimate-member'),
		'login' => __('Login page','ultimate-member'),
		'logout' => __('Logout page','ultimate-member'),
		'password-reset' => __('Password reset page','ultimate-member'),
	);
	$pages = get_option('um_core_pages');

	$core_pages = apply_filters('um_core_pages', $core_pages );

	foreach( $core_pages as $slug => $page ) {
		$pages[ $slug ] = $options['core_' . $slug ];
	}
	update_option( 'um_core_pages', $pages );
}

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-user',
    'title'      => __( 'Users','ultimate-member'),
    'fields'     => array(

		array(
				'id'       		=> 'default_role',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Default New User Role','ultimate-member'),
                'desc' 	   		=> __( 'Select the default role that will be assigned to user after registration If you did not specify custom role settings per form.','ultimate-member'),
                'default'  		=> 'member',
				'options' 		=> $ultimatemember->query->get_roles(),
				'placeholder' 	=> __('Choose user role...','ultimate-member'),
        ),

		array(
				'id'       		=> 'permalink_base',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Permalink Base','ultimate-member'),
                'desc' 	   		=> __( 'Here you can control the permalink structure of the user profile URL globally','ultimate-member'),
                'default'  		=> 'user_login',
				'desc'			=> 'e.g. ' . trailingslashit( um_get_core_page('user') ) .'<strong>username</strong>/',
				'options' 		=> array(
									'user_login' 		=> __('Username','ultimate-member'),
									'name' 				=> __('First and Last Name with \'.\'','ultimate-member'),
									'name_dash' 		=> __('First and Last Name with \'-\'','ultimate-member'),
									'name_plus' 		=> __('First and Last Name with \'+\'','ultimate-member'),
									'user_id' 			=> __('User ID','ultimate-member'),
				),
				'placeholder' 	=> __('Select...','ultimate-member')
        ),

		array(
				'id'       		=> 'display_name',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'User Display Name','ultimate-member'),
                'desc' 	   		=> __( 'This is the name that will be displayed for users on the front end of your site. Default setting uses first/last name as display name if it exists','ultimate-member'),
                'default'  		=> 'full_name',
				'options' 		=> array(
									'default'			=> __('Default WP Display Name','ultimate-member'),
									'nickname'			=> __('Nickname','ultimate-member'),
									'username' 			=> __('Username','ultimate-member'),
									'full_name' 		=> __('First name & last name','ultimate-member'),
									'sur_name' 			=> __('Last name & first name','ultimate-member'),
									'initial_name'		=> __('First name & first initial of last name','ultimate-member'),
									'initial_name_f'	=> __('First initial of first name & last name','ultimate-member'),
									'first_name'		=> __('First name only','ultimate-member'),
									'field' 			=> __('Custom field(s)','ultimate-member'),
				),
				'placeholder' 	=> __('Select...')
        ),

        array(
                'id'       		=> 'display_name_field',
                'type'     		=> 'text',
                'title'   		=> __( 'Display Name Custom Field(s)','ultimate-member'),
				'desc' 	   		=> __('Specify the custom field meta key or custom fields seperated by comma that you want to use to display users name on the frontend of your site','ultimate-member'),
				'required'		=> array( 'display_name', '=', 'field' ),
        ),

        array(
                'id'       		=> 'force_display_name_capitlized',
                'type'     		=> 'switch',
                'title'   		=> __( 'Force display name to be capitalized?','ultimate-member'),
				'default' 		=> 1,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

        array(
                'id'       		=> 'author_redirect',
                'type'     		=> 'switch',
                'title'   		=> __( 'Automatically redirect author page to their profile?','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('If enabled, author pages will automatically redirect to the user\'s profile page','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

        array(
                'id'       		=> 'members_page',
                'type'     		=> 'switch',
                'title'   		=> __( 'Enable Members Directory','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Control whether to enable or disable member directories on this site','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

        array(
                'id'       		=> 'use_gravatars',
                'type'     		=> 'switch',
                'title'   		=> __( 'Use Gravatars?','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Do you want to use gravatars instead of the default plugin profile photo (If the user did not upload a custom profile photo / avatar)','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

		array(
				'id'       		=> 'use_um_gravatar_default_builtin_image',
                'type'     		=> 'select',
				 'title'    	=> __( 'Use Gravatar builtin image','ultimate-member'),
                'desc' 	   		=> __( 'Gravatar has a number of built in options which you can also use as defaults','ultimate-member'),
                'default'  		=> 'default',
				'options' 		=> array(
									'default'		=> __('Default','ultimate-member'),
									'404'			=> __('404 ( File Not Found response )','ultimate-member'),
									'mm'			=> __('Mystery Man','ultimate-member'),
									'identicon'		=> __('Identicon','ultimate-member'),
									'monsterid'		=> __('Monsterid','ultimate-member'),
									'wavatar'		=> __('Wavatar','ultimate-member'),
									'retro'			=> __('Retro','ultimate-member'),
									'blank'			=> __('Blank ( a transparent PNG image )','ultimate-member'),

				),
				'required'		=> array( 'use_gravatars', '=', 1 ),
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
		 ),
        array(
                'id'       		=> 'use_um_gravatar_default_image',
                'type'     		=> 'switch',
                'title'   		=> __( 'Use Default plugin avatar as Gravatar\'s Default avatar','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Do you want to use the plugin default avatar instead of the gravatar default photo (If the user did not upload a custom profile photo / avatar)','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
				'required'		=> array( 'use_um_gravatar_default_builtin_image', '=', 'default' ),
        ),

        array(
                'id'       		=> 'reset_require_strongpass',
                'type'     		=> 'switch',
                'title'   		=> __( 'Require a strong password? (when user resets password only)','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Enable or disable a strong password rules on password reset and change procedure','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'editable_primary_email_in_profile',
                'type'     		=> 'switch',
                'title'   		=> __( 'Editable primary email field in profile view','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Allow users to edit their primary emails in profile view ( when email address field is added only )','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-cog',
    'title'      => __( 'Account','ultimate-member'),
    'fields'     => array(

        array(
                'id'       		=> 'account_tab_password',
                'type'     		=> 'switch',
                'title'   		=> __( 'Password Account Tab','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> 'Enable/disable the Password account tab in account page',
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'account_tab_privacy',
                'type'     		=> 'switch',
                'title'   		=> __( 'Privacy Account Tab','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Enable/disable the Privacy account tab in account page','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'account_tab_notifications',
                'type'     		=> 'switch',
                'title'   		=> __( 'Notifications Account Tab','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Enable/disable the Notifications account tab in account page','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

		array(
                'id'       		=> 'account_tab_delete',
                'type'     		=> 'switch',
                'title'   		=> __( 'Delete Account Tab','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Enable/disable the Delete account tab in account page','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'delete_account_text',
                'type'    		=> 'textarea', // bug with wp 4.4? should be editor
                'title'    		=> __( 'Account Deletion Custom Text','ultimate-member'),
                'default'  		=> __('Are you sure you want to delete your account? This will erase all of your account data from the site. To delete your account enter your password below','ultimate-member'),
				'desc' 	   		=> __('This is custom text that will be displayed to users before they delete their accounts from your site','ultimate-member'),
				'args'     		=> array(
								'teeny'            => false,
								'media_buttons'    => false,
								'textarea_rows'    => 6
				),
        ),

        array(
                'id'       		=> 'account_name',
                'type'     		=> 'switch',
                'title'   		=> __( 'Add a First & Last Name fields','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Whether to enable these fields on the user account page by default or hide them.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),
        array(
                'id'       		=> 'account_name_disable',
                'type'     		=> 'switch',
                'title'   		=> __( 'Disable First & Last Name fields','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Whether to allow users changing their first and last name in account page.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
				'required'		=> array( 'account_name', '=', '1' ),
        ),
        array(
                'id'       		=> 'account_name_require',
                'type'     		=> 'switch',
                'title'   		=> __( 'Require First & Last Name','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Require first and last name?','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
				'required'		=> array( 'account_name', '=', '1' ),
        ),

        array(
                'id'       		=> 'account_email',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow users to change e-mail','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Whether to allow users changing their email in account page.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'account_hide_in_directory',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow users to hide their profiles from directory','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Whether to allow users changing their profile visibility from member directory in account page.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'account_require_strongpass',
                'type'     		=> 'switch',
                'title'   		=> __( 'Require a strong password?','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Enable or disable a strong password rules on account page / change password tab','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-lock',
    'title'      => __( 'Access','ultimate-member'),
    'fields'     => array(

        array(
                'id'       		=> 'panic_key',
                'type'     		=> 'text',
                'title'   		=> __( 'Panic Key','ultimate-member'),
				'desc' 	   		=> __('Panic Key is a random generated key that allow you to access the WordPress backend always regardless of backend settings.','ultimate-member'),
				'default'		=> $ultimatemember->validation->randomize(),
				'desc'			=> trailingslashit( get_bloginfo('url') ).'wp-admin/?um_panic_key=<strong>your_panic_key</strong>'
        ),

        array(
                'id'       		=> 'accessible',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'   		=> __( 'Global Site Access','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Globally control the access of your site, you can have seperate restrict options per post/page by editing the desired item.','ultimate-member'),
				'options' 		=> array(
									0 		=> 'Site accessible to Everyone',
									2 		=> 'Site accessible to Logged In Users'
				)
        ),

        array(
                'id'       		=> 'access_redirect',
                'type'     		=> 'text',
                'title'   		=> __( 'Custom Redirect URL','ultimate-member'),
				'desc' 	   		=> __('A logged out user will be redirected to this url If he is not permitted to access the site','ultimate-member'),
				'required'		=> array( 'accessible', '=', 2 ),
        ),

		array(
				'id'       		=> 'access_exclude_uris',
                'type'     		=> 'multi_text',
				'default'		=> array(),
                'title'    		=> __( 'Exclude the following URLs','ultimate-member'),
                'desc' 	   		=> __( 'Here you can exclude URLs beside the redirect URI to be accessible to everyone','ultimate-member'),
				'add_text'		=> __('Add New URL','ultimate-member'),
				'required'		=> array( 'accessible', '=', 2 ),
		),
 		array(
                'id'       		=> 'home_page_accessible',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow Homepage to be accessible','ultimate-member'),
				'default' 		=> 1,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
				'required'		=> array( 'accessible', '=', 2 ),
        ),
 		array(
                'id'       		=> 'category_page_accessible',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow Category pages to be accessible','ultimate-member'),
				'default' 		=> 1,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
				'required'		=> array( 'accessible', '=', 2 ),
        ),
        array(
                'id'       		=> 'wpadmin_login',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow Backend Login Screen for Guests','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Control whether guests are able to access the WP-admin login screen or not','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

        array(
                'id'       		=> 'deny_admin_frontend_login',
                'type'     		=> 'switch',
                'title'   		=> __( 'Disable Admin Login via Frontend','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('DO NOT turn this option on if you have set the option  "Allow Backend Login Screen for Guests" to NO. This will result in being locked out of admin.','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

		array(
				'id'       		=> 'wpadmin_login_redirect',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Redirect to alternative login page','ultimate-member'),
                'desc' 	   		=> __( 'If you disable backend access to login screen, specify here where a user will be redirected','ultimate-member'),
				'required'		=> array( 'wpadmin_login', '=', 0 ),
                'default'  		=> 'um_login_page',
				'options' 		=> array(
									'um_login_page' 	=> 'UM Login Page',
									'custom_url' 		=> 'Custom URL',
				)
        ),

        array(
                'id'       		=> 'wpadmin_login_redirect_url',
                'type'     		=> 'text',
                'title'   		=> __( 'Custom URL','ultimate-member'),
				'desc' 	   		=> __('Enter an alternate url here to redirect a user If they try to access the backend register screen','ultimate-member'),
				'required'		=> array( 'wpadmin_login_redirect', '=', 'custom_url' ),
        ),

        array(
                'id'       		=> 'wpadmin_register',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow Backend Register Screen for Guests','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Control whether guests are able to access the WP-admin register screen or not','ultimate-member'),
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

		array(
				'id'       		=> 'wpadmin_register_redirect',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Redirect to alternative register page','ultimate-member'),
                'desc' 	   		=> __( 'If you disable backend access to register screen, specify here where a user will be redirected','ultimate-member'),
				'required'		=> array( 'wpadmin_register', '=', 0 ),
                'default'  		=> 'um_register_page',
				'options' 		=> array(
									'um_register_page' 	=> 'UM Register Page',
									'custom_url' 		=> 'Custom URL',
				)
        ),

        array(
                'id'       		=> 'wpadmin_register_redirect_url',
                'type'     		=> 'text',
                'title'   		=> __( 'Custom URL','ultimate-member'),
				'desc' 	   		=> __('Enter an alternate url here to redirect a user If they try to access the backend register screen','ultimate-member'),
				'required'		=> array( 'wpadmin_register_redirect', '=', 'custom_url' ),
        ),

        array(
                'id'       		=> 'access_widget_admin_only',
                'type'     		=> 'switch',
                'title'   		=> __( 'Enable the Access Control widget for Admins only?','ultimate-member'),
				'default' 		=> 1,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

        array(
                'id'       		=> 'enable_reset_password_limit',
                'type'     		=> 'switch',
                'title'   		=> __( 'Enable the Reset Password Limit?','ultimate-member'),
				'default' 		=> 1,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

		array(
                'id'       		=> 'reset_password_limit_number',
                'type'     		=> 'text',
                'title'   		=> __( 'Reset Password Limit','ultimate-member'),
				'desc' 	   		=> __('Set the maximum reset password limit. If reached the maximum limit, user will be locked from using this.','ultimate-member'),
				'default'		=> 3,
				'validate'		=> 'numeric',
				'required'		=> array('enable_reset_password_limit','=',1),
			
        ),

        array(
                'id'       		=> 'disable_admin_reset_password_limit',
                'type'     		=> 'switch',
                'title'   		=> __( 'Disable the Reset Password Limit for Admins only?','ultimate-member'),
				'default' 		=> 0,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
				'required'		=> array('enable_reset_password_limit','=',1),
        ),

        array(
				'id'       		=> 'wpadmin_allow_ips',
                'type'     		=> 'textarea',
                'title'    		=> __( 'Whitelisted Backend IPs','ultimate-member'),
				'desc'			=> __('Always allow the specified IP addresses to access the backend login screen and WP-admin to avoid being locked from site backend.','ultimate-member'),
        ),

        array(
				'id'       		=> 'blocked_ips',
                'type'     		=> 'textarea',
                'title'    		=> __( 'Blocked IP Addresses','ultimate-member'),
				'desc'			=> __('This will block the listed IPs from signing up or signing in to the site, you can use full IP numbers or target specific range with a wildcard','ultimate-member'),
        ),

        array(
				'id'       		=> 'blocked_emails',
                'type'     		=> 'textarea',
                'title'    		=> __( 'Blocked Email Addresses','ultimate-member'),
				'desc'			=> __('This will block the specified e-mail addresses from being able to sign up or sign in to your site. To block an entire domain, use something like *@domain.com','ultimate-member'),
        ),

        array(
				'id'       		=> 'blocked_words',
                'type'     		=> 'textarea',
                'title'    		=> __( 'Blacklist Words','ultimate-member'),
				'desc'			=> __('This option lets you specify blacklist of words to prevent anyone from signing up with such a word as their username','ultimate-member'),
				'default'		=>  'admin' . "\r\n" . 'administrator' . "\r\n" . 'webmaster' . "\r\n" . 'support' . "\r\n" . 'staff'
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-envelope-o',
    'title'      => __( 'Emails','ultimate-member'),
    'fields'     => array(

		array(
				'id'       => 'mail_from',
                'type'     => 'text',
                'title'    => __( 'Mail appears from','ultimate-member'),
                'desc' 	   => __( 'e.g. Site Name','ultimate-member'),
                'default'  => get_bloginfo('name'),
        ),

        array(
                'id'       => 'mail_from_addr',
                'type'     => 'text',
                'title'    => __( 'Mail appears from address','ultimate-member'),
                'desc' => __( 'e.g. admin@companyname.com','ultimate-member'),
                'default'  => get_bloginfo('admin_email'),
        ),

        array(
                'id'       => 'email_html',
                'type'     => 'switch',
                'title'    => __( 'Use HTML for E-mails?','ultimate-member'),
				'default'  => 0,
				'desc' 	   => __('If you enable HTML for e-mails, you can customize the HTML e-mail templates found in <strong>templates/email</strong> folder.','ultimate-member'),
        ),

        array(
                'id'       => 'welcome_email_on',
                'type'     => 'switch',
                'title'    => __( 'Account Welcome Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account is automatically approved','ultimate-member'),
        ),

        array(
                'id'       => 'welcome_email_sub',
                'type'     => 'text',
                'title'    => __( 'Account Welcome Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Welcome to {site_name}!',
				'required' => array( 'welcome_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
				'id'       => 'welcome_email',
                'type'     => 'textarea',
                'title'    => __( 'Account Welcome Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'welcome_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'Thank you for signing up with {site_name}! Your account is now active.' . "\r\n\r\n" .
										  'To login please visit the following url:'  . "\r\n\r\n" .
										  '{login_url}'  . "\r\n\r\n" .
										  'Your account e-mail: {email}' . "\r\n" .
										  'Your account username: {username}' . "\r\n\r\n" .
										  'If you have any problems, please contact us at {admin_email}'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'checkmail_email_on',
                'type'     => 'switch',
                'title'    => __( 'Account Activation Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account needs e-mail activation','ultimate-member'),
        ),

        array(
                'id'       => 'checkmail_email_sub',
                'type'     => 'text',
                'title'    => __( 'Account Activation Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Please activate your account',
				'required' => array( 'checkmail_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'checkmail_email',
                'type'     => 'textarea',
                'title'    => __( 'Account Activation Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'checkmail_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'Thank you for signing up with {site_name}! To activate your account, please click the link below to confirm your email address:' . "\r\n\r\n" .
										  '{account_activation_link}'  . "\r\n\r\n" .
										  'If you have any problems, please contact us at {admin_email}'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'pending_email_on',
                'type'     => 'switch',
                'title'    => __( 'Pending Review Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account needs admin review','ultimate-member'),
        ),

        array(
                'id'       => 'pending_email_sub',
                'type'     => 'text',
                'title'    => __( 'Pending Review Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Your account is pending review',
				'required' => array( 'pending_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'pending_email',
                'type'     => 'textarea',
                'title'    => __( 'Pending Review Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'pending_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'Thank you for signing up with {site_name}! Your account is currently being reviewed by a member of our team.' . "\r\n\r\n" .
										  'Please allow us some time to process your request.' . "\r\n\r\n" .
										  'If you have any problems, please contact us at {admin_email}'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'approved_email_on',
                'type'     => 'switch',
                'title'    => __( 'Account Approved Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account is approved','ultimate-member'),
        ),

        array(
                'id'       => 'approved_email_sub',
                'type'     => 'text',
                'title'    => __( 'Account Approved Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Your account at {site_name} is now active',
				'required' => array( 'approved_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
				'id'       => 'approved_email',
                'type'     => 'textarea',
                'title'    => __( 'Account Approved Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'approved_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'Thank you for signing up with {site_name}! Your account has been approved and is now active.' . "\r\n\r\n" .
										  'To login please visit the following url:'  . "\r\n\r\n" .
										  '{login_url}'  . "\r\n\r\n" .
										  'Your account e-mail: {email}' . "\r\n" .
										  'Your account username: {username}' . "\r\n" .
										  'Set your account password: {password_reset_link}' . "\r\n\r\n" .
										  'If you have any problems, please contact us at {admin_email}'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'rejected_email_on',
                'type'     => 'switch',
                'title'    => __( 'Account Rejected Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account is rejected','ultimate-member'),
        ),

        array(
                'id'       => 'rejected_email_sub',
                'type'     => 'text',
                'title'    => __( 'Account Rejected Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Your account has been rejected',
				'required' => array( 'rejected_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'rejected_email',
                'type'     => 'textarea',
                'title'    => __( 'Account Rejected Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'rejected_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'Thank you for applying for membership to {site_name}! We have reviewed your information and unfortunately we are unable to accept you as a member at this moment.'  . "\r\n\r\n" .
										  'Please feel free to apply again at a future date.'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'inactive_email_on',
                'type'     => 'switch',
                'title'    => __( 'Account Deactivated Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account is deactivated','ultimate-member'),
        ),

        array(
                'id'       => 'inactive_email_sub',
                'type'     => 'text',
                'title'    => __( 'Account Deactivated Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Your account has been deactivated',
				'required' => array( 'inactive_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'inactive_email',
                'type'     => 'textarea',
                'title'    => __( 'Account Deactivated Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'inactive_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'This is an automated email to let you know your {site_name} account has been deactivated.'  . "\r\n\r\n" .
										  'If you would like your account to be reactivated please contact us at {admin_email}'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'deletion_email_on',
                'type'     => 'switch',
                'title'    => __( 'Account Deleted Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when his account is deleted','ultimate-member'),
        ),

        array(
                'id'       => 'deletion_email_sub',
                'type'     => 'text',
                'title'    => __( 'Account Deleted Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Your account has been deleted',
				'required' => array( 'deletion_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
		),

        array(
                'id'       => 'deletion_email',
                'type'     => 'textarea',
                'title'    => __( 'Account Deleted Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'deletion_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										  'This is an automated email to let you know your {site_name} account has been deleted. All of your personal information has been permanently deleted and you will no longer be able to login to {site_name}.'  . "\r\n\r\n" .
										  'If your account has been deleted by accident please contact us at {admin_email}'  . "\r\n\r\n" .
										  'Thanks,' . "\r\n" .
										  '{site_name}',
        ),

        array(
                'id'       => 'resetpw_email_on',
                'type'     => 'switch',
                'title'    => __( 'Password Reset Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send an email when users changed their password (Recommended, please keep on)','ultimate-member'),
        ),

        array(
                'id'       => 'resetpw_email_sub',
                'type'     => 'text',
                'title'    => __( 'Password Reset Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Reset your password',
				'required' => array( 'resetpw_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'resetpw_email',
                'type'     => 'textarea',
                'title'    => __( 'Password Reset Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'resetpw_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										'We received a request to reset the password for your account. If you made this request, click the link below to change your password:'  . "\r\n\r\n" .
										'{password_reset_link}'  . "\r\n\r\n" .
										'If you didn\'t make this request, you can ignore this email'  . "\r\n\r\n" .
										'Thanks,' . "\r\n" .
										'{site_name}',
        ),

        array(
                'id'       => 'changedpw_email_on',
                'type'     => 'switch',
                'title'    => __( 'Password Changed Email','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to send the user an email when he request to reset password (Recommended, please keep on)','ultimate-member'),
        ),

        array(
                'id'       => 'changedpw_email_sub',
                'type'     => 'text',
                'title'    => __( 'Password Changed Email','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => 'Your {site_name} password has been changed',
				'required' => array( 'changedpw_email_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'changedpw_email',
                'type'     => 'textarea',
                'title'    => __( 'Password Changed Email','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
				'required' => array( 'changedpw_email_on', '=', 1 ),
                'default'  => 'Hi {display_name},' . "\r\n\r\n" .
										'You recently changed the password associated with your {site_name} account.'  . "\r\n\r\n" .
										'If you did not make this change and believe your {site_name} account has been compromised, please contact us at the following email address: {admin_email}'  . "\r\n\r\n" .
										'Thanks,' . "\r\n" .
										'{site_name}',
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

	'icon'    => 'um-faicon-bell-o',
    'title'   => __( 'Notifications','ultimate-member'),
    'fields'  => array(

        array(
                'id'       => 'admin_email',
                'type'     => 'text',
                'title'    => __( 'Admin E-mail Address','ultimate-member'),
                'default'  => get_bloginfo('admin_email'),
				'desc' => __( 'e.g. admin@companyname.com','ultimate-member'),
        ),

        array(
                'id'       => 'notification_new_user_on',
                'type'     => 'switch',
                'title'    => __( 'New User Notification','ultimate-member'),
				'default'  => 1,
				'desc' 	   => __('Whether to receive notification when a new user account is approved','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       => 'notification_new_user_sub',
                'type'     => 'text',
                'title'    => __( 'New User Notification','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => '[{site_name}] New user account',
				'required' => array( 'notification_new_user_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'notification_new_user',
                'type'     => 'textarea',
                'title'    => __( 'New User Notification','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
                'default'  => '{display_name} has just created an account on {site_name}. To view their profile click here:' . "\r\n\r\n" .
								'{user_profile_link}'  . "\r\n\r\n" .
								'Here is the submitted registration form:' . "\r\n\r\n" .
								'{submitted_registration}',
				'required' => array( 'notification_new_user_on', '=', 1 ),
				'desc' 	   => __('This is the content of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'notification_review_on',
                'type'     => 'switch',
                'title'    => __( 'Account Needs Review Notification','ultimate-member'),
				'default'  => 0,
				'desc' 	   => __('Whether to receive notification when an account needs admin review','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       => 'notification_review_sub',
                'type'     => 'text',
                'title'    => __( 'Account Needs Review Notification','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => '[{site_name}] New user awaiting review',
				'required' => array( 'notification_review_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'notification_review',
                'type'     => 'textarea',
                'title'    => __( 'Account Needs Review Notification','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
                'default'  => '{display_name} has just applied for membership to {site_name} and is waiting to be reviewed.' . "\r\n\r\n" .
								'To review this member please click the following link:'  . "\r\n\r\n" .
								'{user_profile_link}'  . "\r\n\r\n" .
								'Here is the submitted registration form:' . "\r\n\r\n" .
								'{submitted_registration}',
				'required' => array( 'notification_review_on', '=', 1 ),
				'desc' 	   => __('This is the content of the e-mail','ultimate-member'),
		),

        array(
                'id'       => 'notification_deletion_on',
                'type'     => 'switch',
                'title'    => __( 'Account Deletion Notification','ultimate-member'),
				'default'  => 0,
				'desc' 	   => __('Whether to receive notification when an account is deleted','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       => 'notification_deletion_sub',
                'type'     => 'text',
                'title'    => __( 'Account Deletion Notification','ultimate-member'),
                'subtitle' => __( 'Subject Line','ultimate-member'),
                'default'  => '[{site_name}] Account deleted',
				'required' => array( 'notification_deletion_on', '=', 1 ),
				'desc' 	   => __('This is the subject line of the e-mail','ultimate-member'),
        ),

        array(
                'id'       => 'notification_deletion',
                'type'     => 'textarea',
                'title'    => __( 'Account Deletion Notification','ultimate-member'),
                'subtitle' => __( 'Message Body','ultimate-member'),
                'default'  => '{display_name} has just deleted their {site_name} account.',
				'required' => array( 'notification_deletion_on', '=', 1 ),
				'desc' 	   => __('This is the content of the e-mail','ultimate-member'),
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-cloud-upload',
    'title'      => __( 'Uploads','ultimate-member'),
    'fields'     => array(

		array(
				'id'       		=> 'profile_photo_max_size',
                'type'     		=> 'text',
                'title'    		=> __( 'Profile Photo Maximum File Size (bytes)','ultimate-member'),
                'desc' 	   		=> __( 'Sets a maximum size for the uploaded photo','ultimate-member'),
				'validate' 		=> 'numeric',
        ),

		array(
				'id'       		=> 'cover_photo_max_size',
                'type'     		=> 'text',
                'title'    		=> __( 'Cover Photo Maximum File Size (bytes)','ultimate-member'),
                'desc' 	   		=> __( 'Sets a maximum size for the uploaded cover','ultimate-member'),
				'validate' 		=> 'numeric',
        ),

		array(
				'id'       		=> 'photo_thumb_sizes',
                'type'     		=> 'multi_text',
                'title'    		=> __( 'Profile Photo Thumbnail Sizes (px)','ultimate-member'),
                'desc' 	   		=> __( 'Here you can define which thumbnail sizes will be created for each profile photo upload.','ultimate-member'),
                'default'  		=> array( 40, 80, 190 ),
				'validate' 		=> 'numeric',
				'add_text'		=> __('Add New Size','ultimate-member'),
		),

		array(
				'id'       		=> 'cover_thumb_sizes',
                'type'     		=> 'multi_text',
                'title'    		=> __( 'Cover Photo Thumbnail Sizes (px)','ultimate-member'),
                'desc' 	   		=> __( 'Here you can define which thumbnail sizes will be created for each cover photo upload.','ultimate-member'),
                'default'  		=> array( 300, 600 ),
				'validate' 		=> 'numeric',
				'add_text'		=> __('Add New Size','ultimate-member'),
		),

		array(
				'id'       		=> 'image_compression',
                'type'     		=> 'text',
                'title'    		=> __( 'Image Quality','ultimate-member'),
                'desc' 	   		=> __( 'Quality is used to determine quality of image uploads, and ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file). The default range is 60.','ultimate-member'),
                'default'  		=> 60,
				'validate' 		=> 'numeric',
        ),

		array(
				'id'       		=> 'image_max_width',
                'type'     		=> 'text',
                'title'    		=> __( 'Image Upload Maximum Width (px)','ultimate-member'),
                'desc' 	   		=> __( 'Any image upload above this width will be resized to this limit automatically.','ultimate-member'),
                'default'  		=> 1000,
				'validate' 		=> 'numeric',
        ),

		array(
				'id'       		=> 'cover_min_width',
                'type'     		=> 'text',
                'title'    		=> __( 'Cover Photo Minimum Width (px)','ultimate-member'),
                'desc' 	   		=> __( 'This will be the minimum width for cover photo uploads','ultimate-member'),
                'default'  		=> 1000,
				'validate' 		=> 'numeric',
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-search',
    'title'      => __( 'SEO','ultimate-member'),
    'fields'     => array(

        array(
                'id'      		=> 'profile_title',
                'type'     		=> 'text',
                'title'    		=> __('User Profile Title','ultimate-member'),
                'default'  		=> '{display_name} | ' . get_bloginfo('name'),
				'desc' 	   		=> __('This is the title that is displayed on a specific user profile','ultimate-member'),
        ),

        array(
				'id'       		=> 'profile_desc',
                'type'     		=> 'textarea',
				'default'		=> '{display_name} is on {site_name}. Join {site_name} to view {display_name}\'s profile',
                'title'    		=> __( 'User Profile Dynamic Meta Description','ultimate-member'),
				'desc'			=> __('This will be used in the meta description that is available for search-engines.','ultimate-member')
        ),

	)

);

/***
***	@
***/

$this->sections[] = array(

    'icon'       => 'um-faicon-paint-brush',
    'title'      => __( 'Appearance','ultimate-member'),
    'fields'     => array(

	)

);

$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'General','ultimate-member'),
    'fields'     => array(

		array(
				'id'       		=> 'directory_template',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Members Default Template','ultimate-member'),
                'desc' 	   		=> __( 'This will be the default template to output member directory','ultimate-member'),
                'default'  		=> um_get_metadefault('directory_template'),
				'options' 		=> $ultimatemember->shortcodes->get_templates( 'members' ),
				'required'		=> array( 'xxxxxxxxxxxxx', '=', 'sssssssssssssssss' ),
        ),

        array(
				'id'       		=> 'active_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('active_color'),
                'title'    		=> __( 'General Active Color','ultimate-member'),
                'validate' 		=> 'color',
				'desc'			=> __('Active color is used commonly with many plugin elements as highlighted color or active selection for example. This color demonstrates the primary active color of the plugin','ultimate-member'),
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'secondary_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('secondary_color'),
                'title'    		=> __( 'General Secondary Color','ultimate-member'),
                'validate' 		=> 'color',
				'desc'			=> __('Secondary color is used for hovers, or active state for some elements of the plugin','ultimate-member'),
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'primary_btn_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('primary_btn_color'),
                'title'    		=> __( 'Default Primary Button Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'primary_btn_hover',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('primary_btn_hover'),
                'title'    		=> __( 'Default Primary Button Hover Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'primary_btn_text',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('primary_btn_text'),
                'title'    		=> __( 'Default Primary Button Text Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'secondary_btn_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('secondary_btn_color'),
                'title'    		=> __( 'Default Secondary Button Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'secondary_btn_hover',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('secondary_btn_hover'),
                'title'    		=> __( 'Default Secondary Button Hover Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'secondary_btn_text',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('secondary_btn_text'),
                'title'    		=> __( 'Default Secondary Button Text Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'help_tip_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('help_tip_color'),
                'title'    		=> __( 'Default Help Icon Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

	)

);

$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'Form Inputs','ultimate-member'),
    'fields'     => array(

        array(
				'id'       		=> 'form_field_label',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_field_label'),
                'title'    		=> __( 'Field Label Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
                'id'      		=> 'form_border',
                'type'     		=> 'text',
                'title'    		=> __( 'Field Border','ultimate-member'),
                'default'  		=> um_get_metadefault('form_border'),
				'desc' 	   		=> __('The default border-style for input/fields in UM forms','ultimate-member'),
        ),

        array(
                'id'      		=> 'form_border_hover',
                'type'     		=> 'text',
                'title'    		=> __( 'Field Border on Focus','ultimate-member'),
                'default'  		=> um_get_metadefault('form_border_hover'),
				'desc' 	   		=> __('The default border style for fields on hover state','ultimate-member'),
        ),

        array(
				'id'       		=> 'form_bg_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_bg_color'),
                'title'    		=> __( 'Field Background Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'form_bg_color_focus',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_bg_color_focus'),
                'title'    		=> __( 'Field Background Color on Focus','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'form_text_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_text_color'),
                'title'    		=> __( 'Field Text Color' ),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'form_placeholder',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_placeholder'),
                'title'    		=> __( 'Field Placeholder Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'form_icon_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_icon_color'),
                'title'    		=> __( 'Field Font Icon Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
                'id'       		=> 'form_asterisk',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show an asterisk for required fields','ultimate-member'),
				'default'  		=> 0,
				'on'			=> __('Yes','ultimate-member'),
				'off'			=> __('No','ultimate-member'),
        ),

        array(
				'id'       		=> 'form_asterisk_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('form_asterisk_color'),
                'title'    		=> __( 'Field Required Asterisk Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
				'required'		=> array( 'form_asterisk', '=', '1' ),
        ),

	)

);

$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'Profile','ultimate-member'),
    'fields'     => array(

		array(
				'id'       		=> 'profile_template',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Default Template','ultimate-member'),
                'desc' 	   		=> __( 'This will be the default template to output profile','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_template'),
				'options' 		=> $ultimatemember->shortcodes->get_templates( 'profile' ),
        ),

        array(
                'id'      		=> 'profile_max_width',
                'type'     		=> 'text',
                'title'    		=> __( 'Profile Maximum Width','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_max_width'),
				'desc' 	   		=> 'The maximum width this shortcode can take from the page width',
        ),

        array(
                'id'      		=> 'profile_area_max_width',
                'type'     		=> 'text',
                'title'    		=> __( 'Profile Area Maximum Width','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_area_max_width'),
				'desc' 	   		=> __('The maximum width of the profile area inside profile (below profile header)','ultimate-member'),
        ),

		array(
				'id'       		=> 'profile_align',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Shortcode Alignment','ultimate-member'),
                'desc' 	   		=> __( 'The shortcode is centered by default unless you specify otherwise here' ),
                'default'  		=> um_get_metadefault('profile_align'),
				'options' 		=> array(
									'center' 			=> __('Centered','ultimate-member'),
									'left' 				=> __('Left aligned','ultimate-member'),
									'right' 			=> __('Right aligned','ultimate-member'),
				),
        ),

		array(
				'id'       		=> 'profile_icons',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Field Icons' ),
                'desc' 	   		=> __( 'This is applicable for edit mode only','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_icons'),
				'options' 		=> array(
									'field' 			=> __('Show inside text field','ultimate-member'),
									'label' 			=> __('Show with label','ultimate-member'),
									'off' 				=> __('Turn off','ultimate-member'),
				),
        ),

        array(
                'id'      		=> 'profile_primary_btn_word',
                'type'     		=> 'text',
                'title'    		=> __( 'Profile Primary Button Text','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_primary_btn_word'),
				'desc' 	   		=> __('The text that is used for updating profile button','ultimate-member'),
        ),

        array(
                'id'       		=> 'profile_secondary_btn',
                'type'     		=> 'switch',
                'title'    		=> __( 'Profile Secondary Button','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Switch on/off the secondary button display in the form','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'      		=> 'profile_secondary_btn_word',
                'type'     		=> 'text',
                'title'    		=> __( 'Profile Secondary Button Text','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_secondary_btn_word'),
				'desc' 	   		=> __('The text that is used for cancelling update profile button','ultimate-member'),
				'required'		=> array( 'profile_secondary_btn', '=', 1 ),
        ),

		array(
				'id'       		=> 'profile_role',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Associated Role','ultimate-member'),
                'desc' 	   		=> __( 'Normally, you can leave this to default as this restricts the profile per specified role only','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_role'),
				'options' 		=> $ultimatemember->query->get_roles( $add_default = 'Not specific' ),
        ),

        array(
				'id'       		=> 'profile_main_bg',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_main_bg'),
                'title'    		=> __( 'Profile Base Background Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'profile_header_bg',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_header_bg'),
                'title'    		=> __( 'Profile Header Background Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

		array(
			'id'      			=> 'default_avatar',
			'type'     			=> 'media',
			'title'    			=> __('Default Profile Photo', 'ultimate-member'),
			'desc'     			=> __('You can change the default profile picture globally here. Please make sure that the photo is 300x300px.', 'ultimate-member'),
			'default'  			=> array(
					'url'		=> um_url . 'assets/img/default_avatar.jpg',
			),
		),

		array(
			'id'      			=> 'default_cover',
			'type'     			=> 'media',
			'url'				=> true,
			'preview'			=> false,
			'title'    			=> __('Default Cover Photo', 'ultimate-member'),
			'desc'     			=> __('You can change the default cover photo globally here. Please make sure that the default cover is large enough and respects the ratio you are using for cover photos.', 'ultimate-member'),
		),

        array(
                'id'      		=> 'profile_photosize',
                'type'     		=> 'text',
                'title'    		=> __( 'Profile Photo Size','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_photosize'),
				'desc' 	   		=> __('The global default of profile photo size. This can be overridden by individual form settings','ultimate-member'),
        ),

		array(
				'id'       		=> 'profile_photocorner',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Photo Style','ultimate-member'),
                'desc' 	   		=> __( 'Whether to have rounded profile images, rounded corners, or none for the profile photo','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_photocorner'),
				'options' 		=> array(
									'1' 			=> __('Circle','ultimate-member'),
									'2' 			=> __('Rounded Corners','ultimate-member'),
									'3' 			=> __('Square','ultimate-member'),
				),
        ),

        array(
                'id'       		=> 'profile_cover_enabled',
                'type'     		=> 'switch',
                'title'    		=> __( 'Profile Cover Photos','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Switch on/off the profile cover photos','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

		array(
				'id'       		=> 'profile_cover_ratio',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile Cover Ratio','ultimate-member'),
                'desc' 	   		=> __( 'Choose global ratio for cover photos of profiles','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_cover_ratio'),
				'options' 		=> array(
									'1.6:1' 			=> '1.6:1',
									'2.7:1' 			=> '2.7:1',
									'2.2:1' 			=> '2.2:1',
									'3.2:1' 			=> '3.2:1',
				),
				'required'		=> array( 'profile_cover_enabled', '=', 1 ),
        ),

        array(
                'id'       		=> 'profile_show_metaicon',
                'type'     		=> 'switch',
                'title'    		=> __( 'Profile Header Meta Text Icon','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Display field icons for related user meta fields in header or not','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
				'id'       		=> 'profile_header_text',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_header_text'),
                'title'    		=> __( 'Profile Header Meta Text Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'profile_header_link_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_header_link_color'),
                'title'    		=> __( 'Profile Header Link Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'profile_header_link_hcolor',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_header_link_hcolor'),
                'title'    		=> __( 'Profile Header Link Hover','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'profile_header_icon_color',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_header_icon_color'),
                'title'    		=> __( 'Profile Header Icon Link Color','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
				'id'       		=> 'profile_header_icon_hcolor',
                'type'     		=> 'color',
				'default'		=> um_get_metadefault('profile_header_icon_hcolor'),
                'title'    		=> __( 'Profile Header Icon Link Hover','ultimate-member'),
                'validate' 		=> 'color',
				'transparent'	=> false,
        ),

        array(
                'id'       		=> 'profile_show_name',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show display name in profile header','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_show_name'),
				'desc' 	   		=> __('Switch on/off the user name on profile header','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'profile_show_social_links',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show social links in profile header','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_show_social_links'),
				'desc' 	   		=> __('Switch on/off the social links on profile header','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'profile_show_bio',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show user description in header','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_show_bio'),
				'desc' 	   		=> __('Switch on/off the user description on profile header','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'profile_show_html_bio',
                'type'     		=> 'switch',
                'title'    		=> __( 'Enable html support for user description','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_show_html_bio'),
				'desc' 	   		=> __('Switch on/off to enable/disable support for html tags on user description.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'profile_bio_maxchars',
                'type'     		=> 'text',
                'title'    		=> __( 'User description maximum chars','ultimate-member'),
                'default'  		=> um_get_metadefault('profile_bio_maxchars'),
				'desc' 	   		=> __('Maximum number of characters to allow in user description field in header.','ultimate-member'),
				'required'		=> array( 'profile_show_bio', '=', 1 ),
        ),

        array(
                'id'       		=> 'profile_header_menu',
                'type'     		=> 'select',
                'title'    		=> __( 'Profile Header Menu Position','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_header_menu'),
				'desc' 	   		=> __('For incompatible themes, please make the menu open from left instead of bottom by default.','ultimate-member'),
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
				'options' 		=> array(
									'bc' 		=> 'Bottom of Icon',
									'lc' 		=> 'Left of Icon',
				),
        ),

        array(
                'id'       		=> 'profile_empty_text',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show a custom message if profile is empty','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_empty_text'),
				'desc' 	   		=> __('Switch on/off the custom message that appears when the profile is empty','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'profile_empty_text_emo',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show the emoticon','ultimate-member'),
				'default' 		=> um_get_metadefault('profile_empty_text_emo'),
				'desc' 	   		=> __('Switch on/off the emoticon (sad face) that appears above the message','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
				'required'		=> array( 'profile_empty_text', '=', 1 ),
        ),

	)

);

$tabs = $ultimatemember->profile->tabs_primary();
$tab_options[] = array(
                'id'       		=> 'profile_menu',
                'type'     		=> 'switch',
                'title'    		=> __('Enable profile menu','ultimate-member'),
				'default' 		=> 1,
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
);

foreach( $tabs as $id => $tab ) {

	$tab_options[] = array(
					'id'       		=> 'profile_tab_' . $id,
					'type'     		=> 'switch',
					'title'    		=> sprintf(__('%s Tab','ultimate-member'), $tab ),
					'default' 		=> 1,
					'required'		=> array( 'profile_menu', '=', 1 ),
					'on'			=> __('On','ultimate-member'),
					'off'			=> __('Off','ultimate-member'),
	);

	$tab_options[] = array(
		            'id'       		=> 'profile_tab_' . $id . '_privacy',
		            'type'     		=> 'select',
					'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
		            'title'    		=> sprintf( __( 'Who can see %s Tab?','ultimate-member'), $tab ),
		            'desc' 	   		=> __( 'Select which users can view this tab.','ultimate-member'),
		            'default'  		=> 0,
					'options' 		=> $ultimatemember->profile->tabs_privacy(),
					'required'		=> array( 'profile_tab_' . $id, '=', 1 ),
	);

	$tab_options[] = array(
					'id'       		=> 'profile_tab_' . $id . '_roles',
	                'type'     		=> 'select',
	                'multi'         => true,
					'select2'		=> array( 'allowClear' => 1, 'minimumResultsForSearch' => -1 ),
	                'title'    		=> __( 'Allowed roles','ultimate-member'),
	                'desc' 	   		=> __( 'Select the the user roles allowed to view this tab.','ultimate-member'),
	                'default'  		=> '',
					'options' 		=> $ultimatemember->query->get_roles(),
					'placeholder' 	=> __( 'Choose user roles...','ultimate-member'),
					'required'		=> array( 'profile_tab_' . $id . '_privacy', '=', 4 ),
    );

}

$tab_options[] = array(
                'id'       		=> 'profile_menu_default_tab',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Profile menu default tab','ultimate-member'),
                'desc' 	   		=> __( 'This will be the default tab on user profile page','ultimate-member'),
                'default'  		=> 'main',
				'options' 		=> $ultimatemember->profile->tabs_enabled(),
				'required'		=> array( 'profile_menu', '=', 1 ),
);

$tab_options[] = array(
                'id'       		=> 'profile_menu_icons',
                'type'     		=> 'switch',
                'title'    		=> __('Enable menu icons in desktop view','ultimate-member'),
				'default' 		=> 1,
				'required'		=> array( 'profile_menu', '=', 1 ),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
);

$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'Profile Menu','ultimate-member'),
    'fields'     => $tab_options

);

$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'Registration Form','ultimate-member'),
    'fields'     => array(

		array(
				'id'       		=> 'register_template',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Registration Default Template','ultimate-member'),
                'desc' 	   		=> __( 'This will be the default template to output registration' ),
                'default'  		=> um_get_metadefault('register_template'),
				'options' 		=> $ultimatemember->shortcodes->get_templates( 'register' ),
        ),

        array(
                'id'      		=> 'register_max_width',
                'type'     		=> 'text',
                'title'    		=> __( 'Registration Maximum Width','ultimate-member'),
                'default'  		=> um_get_metadefault('register_max_width'),
				'desc' 	   		=> __('The maximum width this shortcode can take from the page width','ultimate-member'),
        ),

		array(
				'id'       		=> 'register_align',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Registration Shortcode Alignment','ultimate-member'),
                'desc' 	   		=> __( 'The shortcode is centered by default unless you specify otherwise here','ultimate-member'),
                'default'  		=> um_get_metadefault('register_align'),
				'options' 		=> array(
									'center' 			=> __('Centered'),
									'left' 				=> __('Left aligned'),
									'right' 			=> __('Right aligned'),
				),
        ),

		array(
				'id'       		=> 'register_icons',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Registration Field Icons','ultimate-member'),
                'desc' 	   		=> __( 'This controls the display of field icons in the registration form','ultimate-member'),
                'default'  		=> um_get_metadefault('register_icons'),
				'options' 		=> array(
									'field' 			=> __('Show inside text field'),
									'label' 			=> __('Show with label'),
									'off' 				=> __('Turn off'),
				),
        ),

        array(
                'id'      		=> 'register_primary_btn_word',
                'type'     		=> 'text',
                'title'    		=> __( 'Registration Primary Button Text','ultimate-member'),
                'default'  		=> um_get_metadefault('register_primary_btn_word'),
				'desc' 	   		=> __('The text that is used for primary button text','ultimate-member'),
        ),

        array(
                'id'       		=> 'register_secondary_btn',
                'type'     		=> 'switch',
                'title'    		=> __( 'Registration Secondary Button','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Switch on/off the secondary button display in the form','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'      		=> 'register_secondary_btn_word',
                'type'     		=> 'text',
                'title'    		=> __( 'Registration Secondary Button Text','ultimate-member'),
                'default'  		=> um_get_metadefault('register_secondary_btn_word'),
				'desc' 	   		=> __('The text that is used for the secondary button text','ultimate-member'),
				'required'		=> array( 'register_secondary_btn', '=', 1 ),
        ),

        array(
                'id'      		=> 'register_secondary_btn_url',
                'type'     		=> 'text',
                'title'    		=> __( 'Registration Secondary Button URL','ultimate-member'),
                'default'  		=> um_get_metadefault('register_secondary_btn_url'),
				'desc' 	   		=> __('You can replace default link for this button by entering custom URL','ultimate-member'),
				'required'		=> array( 'login_secondary_btn', '=', 1 ),
        ),

		array(
				'id'       		=> 'register_role',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Registration Default Role','ultimate-member'),
                'desc' 	   		=> __( 'This will be the default role assigned to users registering thru registration form','ultimate-member'),
                'default'  		=> um_get_metadefault('register_role'),
				'options' 		=> $ultimatemember->query->get_roles( $add_default = 'Default' ),
        ),

	)

);

$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'Login Form','ultimate-member'),
    'fields'     => array(

		array(
				'id'       		=> 'login_template',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Login Default Template','ultimate-member'),
                'desc' 	   		=> __( 'This will be the default template to output login','ultimate-member'),
                'default'  		=> um_get_metadefault('login_template'),
				'options' 		=> $ultimatemember->shortcodes->get_templates( 'login' ),
        ),

        array(
                'id'      		=> 'login_max_width',
                'type'     		=> 'text',
                'title'    		=> __( 'Login Maximum Width','ultimate-member'),
                'default'  		=> um_get_metadefault('login_max_width'),
				'desc' 	   		=> __('The maximum width this shortcode can take from the page width','ultimate-member'),
        ),

		array(
				'id'       		=> 'login_align',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Login Shortcode Alignment','ultimate-member'),
                'desc' 	   		=> __( 'The shortcode is centered by default unless you specify otherwise here','ultimate-member'),
                'default'  		=> um_get_metadefault('login_align'),
				'options' 		=> array(
									'center' 			=> __('Centered','ultimate-member'),
									'left' 				=> __('Left aligned','ultimate-member'),
									'right' 			=> __('Right aligned','ultimate-member'),
				),
        ),

		array(
				'id'       		=> 'login_icons',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Login Field Icons','ultimate-member'),
                'desc' 	   		=> __( 'This controls the display of field icons in the login form','ultimate-member'),
                'default'  		=> um_get_metadefault('login_icons'),
				'options' 		=> array(
									'field' 			=> __('Show inside text field','ultimate-member'),
									'label' 			=> __('Show with label','ultimate-member'),
									'off' 				=> __('Turn off','ultimate-member'),
				),
        ),

        array(
                'id'      		=> 'login_primary_btn_word',
                'type'     		=> 'text',
                'title'    		=> __( 'Login Primary Button Text','ultimate-member'),
                'default'  		=> um_get_metadefault('login_primary_btn_word'),
				'desc' 	   		=> __('The text that is used for primary button text','ultimate-member'),
        ),

        array(
                'id'       		=> 'login_secondary_btn',
                'type'     		=> 'switch',
                'title'    		=> __( 'Login Secondary Button','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Switch on/off the secondary button display in the form','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'      		=> 'login_secondary_btn_word',
                'type'     		=> 'text',
                'title'    		=> __( 'Login Secondary Button Text','ultimate-member'),
                'default'  		=> um_get_metadefault('login_secondary_btn_word'),
				'desc' 	   		=> __('The text that is used for the secondary button text','ultimate-member'),
				'required'		=> array( 'login_secondary_btn', '=', 1 ),
        ),

        array(
                'id'      		=> 'login_secondary_btn_url',
                'type'     		=> 'text',
                'title'    		=> __( 'Login Secondary Button URL','ultimate-member'),
                'default'  		=> um_get_metadefault('login_secondary_btn_url'),
				'desc' 	   		=> __('You can replace default link for this button by entering custom URL','ultimate-member'),
				'required'		=> array( 'login_secondary_btn', '=', 1 ),
        ),

        array(
                'id'       		=> 'login_forgot_pass_link',
                'type'     		=> 'switch',
                'title'    		=> __( 'Login Forgot Password Link','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Switch on/off the forgot password link in login form','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'login_show_rememberme',
                'type'     		=> 'switch',
                'title'    		=> __( 'Show "Remember Me"','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Allow users to choose If they want to stay signed in even after closing the browser. If you do not show this option, the default will be to not remember login session.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

	)

);

if ( um_get_option('enable_custom_css') ) {
$this->sections[] = array(

    'subsection' => true,
    'title'      => __( 'Custom CSS','ultimate-member'),
    'fields'     => array(

        array(
				'id'       		=> 'custom_css',
                'type'     		=> 'textarea',
                'title'    		=> __( 'Custom CSS','ultimate-member'),
				'desc'			=> __('Any custom css rules that you specify here will be applied globally to the plugin.','ultimate-member'),
				'rows'			=> 20,
        ),

	)

);
}

/***
***	@
***/

$arr_advanced_fields = array(
		
		array(
				'id'            	=> 'import_export',
				'type'          	=> 'import_export',
				'title'         	=> __('Import & Export Settings','ultimate-member'),
				'full_width'    	=> true,
		),

        array(
                'id'       		=> 'um_profile_object_cache_stop',
                'type'     		=> 'switch',
                'title'   		=> __( 'Cache user\'s profile data','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Turn off If you have performance issue.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'um_flush_stop',
                'type'     		=> 'switch',
                'title'   		=> __( 'Stop rewriting rules on every load','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Turn on If you have performance issue and are not getting 404 error/conflicts with other plugins/themes.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'um_generate_slug_in_directory',
                'type'     		=> 'switch',
                'title'   		=> __( 'Stop generating profile slugs in member directory','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Turn on If you have performance issue in member directory.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

		array(
				'id'       		=> 'current_url_method',
                'type'     		=> 'select',
				'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
                'title'    		=> __( 'Current URL Method','ultimate-member'),
                'desc' 	   		=> __( 'Change this If you are having conflicts with profile links or redirections.','ultimate-member'),
                'default'  		=> 'SERVER_NAME',
				'options' 		=> array(
									'SERVER_NAME' 			=> __('Use SERVER_NAME','ultimate-member'),
									'HTTP_HOST' 			=> __('Use HTTP_HOST','ultimate-member'),
				),
        ),

        array(
                'id'      		=> 'um_port_forwarding_url',
                'type'     		=> 'switch',
                'title'    		=> __( 'Allow Port forwarding in URL','ultimate-member'),
                'default'  		=> 0,
				'desc' 	   		=> __('Turn on If you want to include port number in URLs','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'      		=> 'um_force_utf8_strings',
                'type'     		=> 'switch',
                'title'    		=> __( 'Force Strings to UTF-8 Encoding','ultimate-member'),
                'default'  		=> 0,
				'desc' 	   		=> __('Turn on If you want to force labels and fields to use UTF-8 encoding','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'enable_timebot',
                'type'     		=> 'switch',
                'title'   		=> __( 'Enable Time Check Security','ultimate-member'),
				'default' 		=> 1,
				'desc' 	   		=> __('Turn this option off if you have a conflict with other plugins causing a spam bot message to appear unexpectedly.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'disable_minify',
                'type'     		=> 'switch',
                'title'   		=> __( 'Disable JS/CSS Compression','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('Not recommended. This will load all plugin js and css files separately and may slow down your website. Use this setting for development or debugging purposes only.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'disable_menu',
                'type'     		=> 'switch',
                'title'   		=> __( 'Disable Nav Menu Settings','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('This can disable the settings that appear in nav menus to apply custom access settings to nav items.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'js_css_exlcude_home',
                'type'     		=> 'switch',
                'title'   		=> __( 'Never load plugin JS and CSS on homepage','ultimate-member'),
				'default' 		=> 0,
				'desc' 	   		=> __('This can disable loading plugin js and css files on home page.','ultimate-member'),
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

		array(
				'id'       		=> 'js_css_exclude',
                'type'     		=> 'multi_text',
				'default'		=> array(),
                'title'    		=> __( 'Never load plugin JS and CSS on the following pages','ultimate-member'),
                'desc' 	   		=> __( 'Enter a url or page slug (e.g /about/) to disable loading the plugin\'s css and js on that page.','ultimate-member'),
				'add_text'		=> __('Add New Page','ultimate-member'),
		),

		array(
				'id'       		=> 'js_css_include',
                'type'     		=> 'multi_text',
				'default'		=> array(),
                'title'    		=> __( 'Only load plugin JS and CSS on the following pages','ultimate-member'),
                'desc' 	   		=> __( 'Enter a url or page slug (e.g /about/) to enable loading the plugin\'s css and js on that page.','ultimate-member'),
				'add_text'		=> __('Add New Page','ultimate-member'),
		),

        array(
                'id'       		=> 'enable_custom_css',
                'type'     		=> 'switch',
                'title'   		=> __( 'Enable custom css tab?','ultimate-member'),
				'default' 		=> 0,
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

        array(
                'id'       		=> 'allow_tracking',
                'type'     		=> 'switch',
                'title'   		=> __( 'Allow Tracking','ultimate-member'),
				'default' 		=> 0,
				'on'			=> __('On','ultimate-member'),
				'off'			=> __('Off','ultimate-member'),
        ),

);

if( is_multisite() ){
	$arr_advanced_fields[] = array(
					'id'       		=> 'network_permalink_structure',
	                'type'     		=> 'select',
					'select2'		=> array( 'allowClear' => 0, 'minimumResultsForSearch' => -1 ),
	                'title'    		=> __( 'Network Permalink Structure','ultimate-member'),
	                'desc' 	   		=> __( 'Change this If you are having conflicts with profile links or redirections in a multisite setup.','ultimate-member'),
	                'default'  		=> 'sub-domain',
					'options' 		=> array(
										'sub-domain' 			=> __('Sub-Domain','ultimate-member'),
										'sub-directory' 		=> __('Sub-Directory','ultimate-member'),
					)
	);
}

$this->sections[] = array(

    'icon'       => 'um-faicon-wrench',
    'title'      => __('Advanced','ultimate-member'),
    'fields'     => $arr_advanced_fields

);
