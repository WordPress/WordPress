=== Ultimate Member - User Profile & Membership Plugin ===
Author URI: https://ultimatemember.com/
Plugin URI: https://ultimatemember.com/
Contributors: ultimatemember, champsupertramp
Donate link: 
Tags: community, member, membership, user-profile, user-registration
Requires at least: 4.1
Tested up to: 4.8
Stable tag: 1.3.88
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

The #1 user profile & membership plugin for WordPress.

== Description ==

= Best User Profile & Membership Plugin for WordPress =

Ultimate Member is the #1 user profile & membership plugin for WordPress. The plugin makes it a breeze for users to sign-up and become members of your website. The plugin allows you to add beautiful user profiles to your site and is perfect for creating advanced online communities and membership sites. Lightweight and highly extendible, Ultimate Member will enable you to create almost any type of site where users can join and become members with absolute ease.

= Features of the plugin include: =

* Front-end user profiles
* Front-end user registration
* Front-end user login
* Custom form fields
* Conditional logic for form fields
* Drag and drop form builder
* User account page
* Custom user roles
* Member directories
* User emails
* Content restriction
* Conditional nav menus
* Show author posts & comments on user profiles
* Developer friendly with dozens of actions and filters

Read about all of the plugin's features at [Ultimate Member](https://ultimatemember.com)

= Paid Extensions =

Ultimate Member has a range of extensions that allow you to extend the power of the plugin

* [Private Content](https://ultimatemember.com/extensions/private-content/) - Display private content to logged in users that only they can access
* [Instagram](https://ultimatemember.com/extensions/instagram/) - Allow users to show their Instagram photos on their profile
* [User Tags](https://ultimatemember.com/extensions/user-tags/) - Lets you add a user tag system to your website
* [Social Activity](https://ultimatemember.com/extensions/social-activity/) - Let users create public wall posts & see the activity of other users
* [WooCommerce](https://ultimatemember.com/extensions/woocommerce/) - Allow you to integrate WooCommerce with Ultimate Member
* [Private Messages](https://ultimatemember.com/extensions/private-messages/) - Add a private messaging system to your site & allow users to message each other
* [Followers](https://ultimatemember.com/extensions/followers/) - Allow users to follow each other on your site and protect their profile information
* [Real-time Notifications](https://ultimatemember.com/extensions/real-time-notifications/) - Add a notifications system to your site so users can receive real-time notifications
* [Social Login](https://ultimatemember.com/extensions/social-login/) - Let users register & login to your site via Facebook, Twitter, G+, LinkedIn, Instagram and Vkontakte (VK.com)
* [bbPress](https://ultimatemember.com/extensions/bbpress/) - With the bbPress extension you can beautifully integrate Ultimate Member with bbPress
* [MailChimp](https://ultimatemember.com/extensions/mailchimp/) - Allow users to subscribe to your MailChimp lists when they signup on your site and sync user meta to MailChimp
* [User Reviews](https://ultimatemember.com/extensions/user-reviews/) - Allow users to rate & review each other using a 5 star rate/review system
* [Verified Users](https://ultimatemember.com/extensions/verified-users/) - Add a user verficiation system to your site so user accounts can be verified
* [myCRED](https://ultimatemember.com/extensions/mycred/) - With the myCRED extension you can integrate Ultimate Member with the popular myCRED points management plugin
* [Notices](https://ultimatemember.com/extensions/notices/) - Alert users to important information using conditional notices
* [Profile Completeness](https://ultimatemember.com/extensions/profile-completeness/) - Encourage or force users to complete their profiles with the profile completeness extension
* [Friends](https://ultimatemember.com/extensions/friends/) - Allows users to become friends by sending & accepting/rejecting friend requests

= Free Extensions =

* [Terms & Conditions](https://ultimatemember.com/extensions/terms-conditions/) - Add a terms and condition checkbox to your registration forms & require users to agree to your T&Cs before registering on your site.
* [Google reCAPTCHA](https://ultimatemember.com/extensions/google-recaptcha/) - Stop bots on your registration & login forms with Google reCAPTCHA
* [Online Users](https://ultimatemember.com/extensions/online-users/) - Display what users are online with this extension

= Development * Translations =

If you're a developer and would like to contribute to the source code of the plugin you can do so via our [GitHub Repository](https://github.com/ultimatemember/ultimatemember).

Want to add a new language to Ultimate Member? Great! You can contribute via [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/ultimate-member).

= Documentation & Support =

Got a problem or need help with Ultimate Member? Head over to our [documentation](http://docs.ultimatemember.com/) and perform a search of the knowledge base. If you can’t find a solution to your issue then you can create a topic on the [support forum](https://wordpress.org/support/plugin/ultimate-member).


== Installation ==

1. Activate the plugin
2. That's it. Go to Ultimate Member > Settings to customize plugin options
3. For more details, please visit the official [Documentation](http://docs.ultimatemember.com/) page.

== Frequently Asked Questions ==

= Do I need to know any coding to use this plugin? =

No, we have built Ultimate Member to be extremely easy to use and does not require you to manually build shortcodes or have any coding knowledge.

= Is Ultimate Member mobile responsive? =

Yes. Ultimate Member is designed to adapt nicely to any screen resolution. It includes specific designs for phones, tablets and desktops. 

= Is Ultimate Member multi-site compatible? =

Yes. Ultimate Member works great on both single site and multi-site WordPress installs.

= Does the plugin work with any WordPress theme? =

Yes. Ultimate Member will work with any properly coded theme. However, some themes may cause conflicts with the plugin. If you find a styling issue with your theme please create a post in the community forum.

= Does the plugin work with caching plugins? =

The plugin works with popular caching plugins by automatically excluding Ultimate Member pages from being cached. This ensures other visitors to a page will not see the private information of another user. However, if you add features of Ultimate Member to other pages you have to exclude those pages from being cached through your cache plugin settings panel. 

== Screenshots ==

1. Screenshot 1
2. Screenshot 2
3. Screenshot 3
4. Screenshot 4
5. Screenshot 5
6. Screenshot 6
7. Screenshot 7
8. Screenshot 8
9. Screenshot 9
10. Screenshot 10
11. Screenshot 11
12. Screenshot 12
13. Screenshot 13
14. Screenshot 14
15. Screenshot 15
16. Screenshot 16

== Changelog ==


= 1.3.88: July 25, 2017 =

* Enhancements:
  - Add new filter hook `um_add_user_frontend_submitted`
  - Add class for member tagline in directory grid `um-member-tagline-<field key>`
  - Add recaptcha support and submit button id
  - Update extensions page layout
 
* Bugfixes:
  - Fix Conditional Logic fields
  - Fix required field with specific roles in privacy
  - Remove wpautop from biography
  - Remove notices

= 1.3.87: June 24, 2017 =

* Bugfixes
  - Fix system info

= 1.3.86: June 19, 2017 =

* Enhancements:
  - Update readme.txt
  

= 1.3.85: June 19, 2017 =

* Enhancements:
  * Add new filter hook to modify the profile cancel uri for redirection
    * `um_edit_profile_cancel_uri`
  * Add new filter hook to modify the specific field type's value
    * `um_edit_{$type}_field_value`
  * Add new filter hook that modies the file name
    * `um_upload_file_name`
  * Update en_US translation files

* Bugfixes:
  * Fix file clean up with image/file fields on profile update
  * Fix text domain slug for wp.org translation compatibility
  * Fix change password email notification
  * Fix double click submission in registration forms
  * Fix custom field role validation
  * Fix conditional logic 'content block' field
  * Fix conditional logic field operators and visibility
  * Fix textarea field sanitation
  * Fix system info false positive virus scan results
  * Fix field validation for minimum and maximum numeric values
  * Fix used custom fields visibility in form builders
  * Fix cache user's profile option description
  * Fix double click for android device
  * Fix png image upload with transparency
  * Fix extra slashes in form edit view when invalid input fields occur
  * Remove notices
  
  
= 1.3.84: April 18, 2017 =

* Enhancements:
  * Adds new action hooks before and after WP_User_Query. 
    * `um_user_before_query`
    * `um_user_after_query`
  * Adds a dismiss link in locale / language translation notices
  * Adds correct user profile url in WPML language switcher
  * Adds new option to disable name fields in account page
    * `UM > Settings > Account > Disable First & Last Name fields`
  * Add new filters to modify upload base directories
    * `um_multisite_upload_sites_directory`
    * `um_multisite_upload_directory`
  * Adds new action hook in admin > user edit > Ultimate Member to append any customisation
    * `um_user_profile_section`
  * Adds H2 for UltimateMember section to Add/Edit User Form

* Bugfixes
  * Fix image url cache filter
  * Fix PHP 7.1+ compatibility issues 
  * Fix UTF8 encoding in form fields
  * Fix hide member directory option.
  * Fix conditional logic fields.
  * Fix login access settings for logged-in users.
  * Fix WP role synchronisation on UM role update
  * Fix WP authenticate filter hook
  * Fix radio and checbox field active state colors when disabled.
  * Fix Content Availability conditional fields in edit/add category screens
  * Remove notices

= 1.3.83: February 20, 2017 =

* Enhancements:
   * Adds user avatar's alternate text.  The default text is set to `display_name`
   * Adds new filter hook to modif the user avatar's alternate text. 
       * `um_avatar_image_alternate_text`
   * Set gravatar for newly registered users
   * Adds Tag archive page access settings

* Bugfixes
   * Remove pointer cursor from field areas in profile view mode
   * Fix profile slug in permalinks
   * Fix URL field 'nofollow' issue
   * Fix field icons display
   * Fix an issue with admin roles in editing fields
   * Fix whitepspace issue with Email Address validation
   * Fix profile visibility option in member directories
   * Fix icon display as label in profile view
   * Fix PHP 7.1.1 compatibility
   * Fix redirection on update profile slug
   * Fix dynamic CSS options in member directory
   * Fix edit profile option by specific role
   * Fix Vkontakte view
   * Remove notices

= 1.3.82: January 31, 2017 =

* Enhancements:
   * Add filter hook to disable secure account fields
      * `um_account_secure_fields__enabled`
   * Updates ReduxFramework to version 3.6.2
   * Adds a body class in profile/user page for the current loggedin user
      * `um-own-profile`

* Bugfixes
   *  Fix select/multi-select field options translation
   *  Fix profiles visibility and access permissions in member directories
   *  Fix User deletion in mobile browsers
   *  Fix WPML & PolyLang compatibility issues
   *  Fix field view and edit restriction
   *  Fix author name in recent comments widget
   *  Fix overwrite of multiple image and file uploads with the same filename
   *  Remove notices

= 1.3.81: January 19, 2017 =

* Bugfixes
   * Fix conditional field option with 'contains'
   * Fix WPML compatibility with UM logout

= 1.3.80: January 18, 2017 =

* Bugfixes:
   * Fix loop email notifications on user creation in the back-end

= 1.3.79: January 17, 2017 =

* Enhancements:
   * Adds new username and string validation filter hooks:
      * um_validation_safe_username_regex
      * um_validation_safe_string_regex
   * Adds new filter hook to modify conditional fields
      * um_get_field__{$field_key}
   * Change max limit of users queue's count in cache
   * Adds current logged in users WP and Community roles in the System Info
   * Adds confirmation on user deletion

* Bugfixes:
   * Fix conditional fields
   * Fix logout compatibility issues with WPML
   * Fix select option's custom callback validation
   * Fix translation strings of primary and secondary buttons on Login and Register forms.
   * Fix gender filter and results
   * Fix user deletion in account page for mobile browsers
   * Fix form rows CSS options
   * Fix default text autocomplete
   * Remove notices

= 1.3.78: December 08, 2016 =

* Bugfixes:
  * Fix menu settings compatibility issue with WP 4.7
  * Fix mobile class on account delete tab heading
  * Fixes an issue where tagline shows the current users to all members
  * Fixes notice on updating WP List Table quick edit
  * Remove notices

  = 1.3.77: November 30, 2016 =

* Bugfixes:
  * Fix set and reset password validation.
  * Remove notices

= 1.3.76: November 30, 2016 =

* Bugfixes:
  * Fix invalid security notice in set password.

= 1.3.75: November 29, 2016 =

* Bugfixes:
  * Fix 'Invalid user ID' on profile update
 
= 1.3.74: November 29, 2016 =

* Enhancements:
  * Improves clear users cache. 
  * Removes user id from redirect URL on registration process for pending review and email activation statuses.

* Bugfixes:
  * Fix assigning of role on registration process
  * Fix change email address in edit mode.
  * Fix change password validation.
  * Removes notices when role field is present in the profile form.


= 1.3.73: November 17, 2016 =

* Enhancements:
  * Adds a filter hook to modify the submitted details on registration process
      * `um_before_save_filter_submitted`
  * Adds a filter hook to disable canonical link in header
      * `um_allow_canonical__filter`
  * Adds a filter hook to modify the auto-generated email address on registration process
      * `um_user_register_submitted__email`
  * Adds filter hooks to modify locale, language file path and textdomain
      * `um_language_textdomain`
      * `um_language_locale`
      * `um_language_file`
  * Adds filter hook to modify the data of selected value:
      * `um_is_selected_filter_data`
  * Adds new select/multi-select options to retrieve options from a callback.
      * In the form builder, edit or add a select/multi-select field and add your callback function in `Choices callback` field to get populated.
  * Adds parent select field option to dynamically populate another select field.
      * If `Choices Callback` option is set in the field settings,  the `Parent Option` triggers an Ajax request to populate the child options on `change` event.
  * Updates `um.min.js` file.
  * Updates `en_US` translation file.

* Bugfixes:
  *  Removes notices from WPCLI console.
  *  Removes notices from edit profile mode
  *  Removes autocomplete from search filter fields
  *  Fix translation strings of search filters on a member directory
  *  Fix email notifications not sending on registration process
  *  Fix field selection with special characters on form submission
  *  Fix assigning of role on register submission process

= 1.3.72: October 10, 2016 =

* Enhancements:
  *  Improves the bulk filters, actions and redirection in `User Screens`
  *  Adds new access options to disallow access on homepage and category pages. 
  *  Adds Textarea to show in profile tagline on Member Directory
  * Adds a filter hook `um_allow_frontend_image_uploads` to allow profile and cover photo uploads on front-end pages.
  * Adds new filter hooks to modify image field data on upload: 
      * `um_image_handle_global__option`
      * `um_image_handle_{$field}__option`
  * Adds a new filter hook to modify the redirection of non logged in users who visit the parent user page.
     * `um_locate_user_profile_not_loggedin__redirect`
  * Improves generate dummies tool with `wp_remote_get`
  * Adds a new action hook for a new section in cover area:
     * `um_cover_area_content`
  * Updates the English translation .po and .mo files.
  * Improves the shortcode `um_show_content` to swap the `user_id` with the current profile.

* Bugfixes:
  *  Fixes a bug where multi-select field options doesn't match the user selected options that contain html entities.
  *  Fixes a bug to display correct role slugs in radio and select fields.
  *  Fixes a bug where reset password form is conflicting with register and login form on a page.
  *  Fixes a bug where Users queue count in the Admin > `All Users / Users` menu doesn't update when a user account is in `pending user review` and gets deleted.
  * Fixes a typo in Password Reset Email option's description
  * Fixes a bug where conditional fields 'equals to' validation on registration process
  * Fixes a bug to disable the query with hiding account on member directory
  * Fixes a bug to retrieve specific number of members 
  * Fixes a bug to retrieve all members with `get_members` new parameter `number`
  * Fixes a typo in Welcome Email template.
  * Fixes a bug where login form redirection is set to `wp-admin/admin-ajax.php` instead of the current page when loaded via ajax.
  * Fixes a bug where uninstall link doesn't load.
  * Fixes a bug to redirect users to correct URL after login based from login options.
  * Fixes a bug where non-logged in users are not able to access the profile page when `Global Site Access` is set to `Site accessible to Logged In Users`.
  * Fixes a bug to modify the login redirection url especially when DOING_AJAX is defined.
  * Fixes a bug to retrieve correct community roles per site in a Multisite Network setup.
  
= 1.3.71: September 12, 2016 =

* Enhancements: 
  * Adds a new filter hook to modify the `cover photo` uri.
      * `um_user_cover_photo_uri__filter`
* Bugfixes:
  *  Fixes a bug to allow users change their password in account form
  *  Fixes a bug to allow role validation and assigning of roles to users on registration process 
  *  Fixes a bug to avoid blank admin footer text all around WordPress

= 1.3.70: September 09, 2016 =

* Enhancements: 
    * Adds a new filter hook to modify the profile `cover photo` uri.
        * `um_user_cover_photo_uri__filter`
* Bugfixes:
    *  Fixes a bug to allow users change their password in account form
    *  Fixes a bug to reset passwords

= 1.3.69: September 08, 2016 =

* Enhancements:
    * Adds a system information tool for support purposes
    * Adds a new option to disable generating profile slugs on every load of member directory pages.
         * Located in UM > Settings > Advanced > Stop generating profile slugs in member directory.
         * This improves the performance when loading profiles in directories. It generates profile slug on Profile Update ( front and back-end ), Registration Process and viewing the Users in the back-end.
    * Adds new filter hook `um_activate_url` to modify the account activation url.
    * Adds new filter hooks to modify first and last name cases
        * `um_user_first_name_case` 
        * `um_user_last_name_case`
    * Adds new filter hooks to modify nonces of image and file uploads
        * `um_file_upload_nonce` 
        * `um_image_upload_nonce`
    * Improves search member filters and keyword sensitivity
    * Improves generation of profile slugs
    * Improves force capitalization of display names with dash
    * Improves the pagination and loading of profiles in member directory

* Bugfixes:
    * Fixes a bug where users in member directory are missing after updating their profile
    * Fixes a bug to generate random email when email field is not added in a form.
    * Fixes a bug to show hidden members in member directory for admins
    * Fixes a bug to validate username length on registration process
    * Fixes a bug to display profile name in dynamic menu and profile page title
    * Fixes a bug where frequent security notices show on registration process
    * Fixes a bug to assign correct role to a user on registration process
    * Fixes a bug to display correct roles in radio and dropdown fields
    * Fixes a bug to validate the reset password
    * Fixes a bug to change the assign of role in navigation menu items
    * Fixes a bug to select radio field in profile form
    * Fixes a bug to allow frontpage and posts page to handle custom access settings
    * Fixes a bug where profile role field's conditional logic doesn't show/hide the second field.
    * Fixes a bug to show 'Last Login' field in profile edit and view mode.
    * Fixes a bug to sync roles with users in Edit Roles.
    * Fixes a bug to disable/enable UM profile cache
    * Fixes a bug to disable biography field's character limit when added in form


= 1.3.68: August 02, 2016 =

* Fixed: radio field in account page

= 1.3.67: August 02, 2016 =

* New: allow non-editable fields in registration form
* Fixed: member directory mobile pagination
* Fixed: biography field validation in profile header and forms
* Fixed: remove override 'birth date' label
* Fixed: html support in biography field
* Fixed: select options search
* Fixed: select field's search query
* Fixed: search filters and multi-select fields
* Fixed: select, radio and checkbox field options
* Fixed: multiple select in UM settings
* Fixed: member directory's pagination links
* Fixed: remove nonce and http referer from submitted user details
* Fixed: disallow direct access link to posts with enabled category access restriction
* Added: new filter hook: `um_get_default_cover_uri_filter`
* Added: new filter hook: `um_register_allow_nonce_verification`
* Added: new filter hook: `um_get_option_filter__{$option_id}`
* Added: new filter hook: ` um_profile_{$key}__filter`
* Added: new filter hook: `um_profile_{$key}_empty__filter`
* Added: new filter hook `um_enable_ajax_urls`
* Added: new filter hook `um_field_checkbox_item_title`


= 1.3.66: July 14, 2016 =

* Tweak: update translation strings and English translation file.
* Fixed: alphabetic and lowercase validations
* Fixed: checkbox and radio label encoding
* Fixed: user_login field validation type
* Fixed: registration form process
* Fixed: remove comments with hidden/private posts from comment tab

= 1.3.65: July 06, 2016 =

* Tweak: update ReduxFramework to version 3.6.0.1
* Added: new action hook 'um_registration_after_auto_login'
* Added: new option for Network Permalink Structure
* Added: an account option to require first and last name
* Fixed: account deletion and password confirmation
* Fixed: registration form submission process
* Fixed: access settings in home page and posts conflict
* Fixed: encoding non UTF8 strings

= 1.3.64: June 29, 2016 =
* Fixed: edit profile permission

= 1.3.63: June 28, 2016 =
* Fixed: admin navigation
* Fixed: profile access and redirection

= 1.3.62: June 27, 2016 =
* Fixed: access settings and redirection for logged out users
* Fixed: global access settings
* Fixed: remove notice in permalink

= 1.3.61: June 24, 2016 =
* Fixed: edit profile url in multi-site setup
* Fixed: global access settings

= 1.3.60: June 23, 2016 =
* Fixed: change password
* Fixed: menu settings and roles
* Fixed: cropper issue with Avada theme
* Fixed: image cropper and modal
* Fixed: nonce in registration forms
* Fixed: user redirection for non-loggedin users
* Fixed: global access setting

= 1.3.59: June 17, 2016 =
* Added: filter 'um_register_hidden_role_field'
* Added: filters and action hooks in form post
* Added: cache time filter in avatar url
* Fixed: Nonces added to file uploads
* Fixed: remove notices
* Fixed: upload image cropper
* Fixed: select field multiple select
* Fixed: changing community role by admin
* Fixed: current url method in multisite setup
* Fixed: access settings

= 1.3.58: June 09, 2016 =
* Fixed: change password
* Fixed: select field overlay

= 1.3.57: June 09, 2016 =
* Fixed: admin access restriction

= 1.3.56: June 09, 2016 =
* Fixed: query of pages

= 1.3.55: June 09, 2016 =
* Fixed: select fields styles
* Fixed: select fields with accented characters
* Fixed: select fields with accented text values
* Fixed: select fields in overlay
* Fixed: admin front-end access restriction 
* Fixed: pages query

= 1.3.54: June 02, 2016 =
* Fixed: remove quick edit from Built-in roles row actions
* Fixed: remove notices
* Fixed: dropdown/select fields 
* Fixed: upload file extension's case sensitive issue
* Fixed: reset and change password

= 1.3.53: June 01, 2016 =
* New: generate dummy users tool
* Added: filter 'um_submit_form_error' for custom error messages
* Tweak: update compressed CSS
* Tweak: update select2.js to version 4.0.2
* Fixed: gravatar and transfer tool
* Fixed: permalink base for username
* Fixed: saving account fields
* Fixed: remove notice
* Fixed: upload form error logging
* Fixed: cache option
* Fixed: edit profile url
* Fixed: login redirection
* Fixed: remove account information from welcome email
* Fixed: form error with users tags
* Fixed: select fields with accented characters
* Fixed: image upload

= 1.3.52: May 14, 2016 =
* Added: 'wp_authenticate_username_password_before' action hook
* Tweak: remove access settings in media screens
* Fixed: convert tags format
* Fixed: ReduxFramework notice
* Fixed: remove PHP notice

= 1.3.51: May 10, 2016 =
* Added: 'um_form_fields_textarea_settings' filter
* Added: reset password limit options
* Added: option to force display name to be capitlized
* Fixed: remove notices
* Fixed: redirect url on login
* Fixed: optimize query and object caching 
* Fixed: profile photo as required field
* Fixed: admin access in front-end login
* Fixed: typos in tooltips
* Fixed: embedding video fields
* Fixed: Flush rewrite rules

= 1.3.50: April 21, 2016 =
* Fixed: menu incompatibility issue
* Fixed: username validation
* Fixed: admin css conflict
* Fixed: display name capitalization
* Fixed: search member filter and fields
* Fixed: member directory big SELECT query
* Added: action hook 'um_access_post_type' & 'um_access_post_type_{current_page_type}' for current page type in access settings

= 1.3.49: April 14, 2016 =
* Fixed: remove core notices from ajax requests
* Fixed: upload form and media path

= 1.3.48: April 11, 2016 =
* New: advanced option to disable profile object caching
* Added: ssl media uri function
* Added: first and last name initial as meta key
* Fixed: order by random and pagination
* Fixed: user sort by random
* Fixed: status message encoding
* Fixed: image upload and file name
* Fixed: user login with other provider
* Fixed: translation strings
* Fixed: dependencies fatal errors
* Fixed: remove notices

= 1.3.47: April 6, 2016 =
* Fixed: Fatal errors with language filter file

= 1.3.46: April 6, 2016 =
* Fixed: Search widget fatal error
* Fixed: image jpeg upload sizes

= 1.3.45: April 6, 2016 =
* New: support for wordfence and limit login
* New: search widget
* New: secondary email address
* Added: hook to password reset form fields
* Added: privacy options for profile menu tabs
* Added: option to allow primary email editable in profile view
* Added: member directory sort randomly
* Fixed: user page redirection
* Fixed: admin script error
* Fixed: invalid image path
* Fixed: upload image png with transparency
* Fixed: permalink basename fallback
* Fixed: casting variable and add new filter
* Fixed: remove notices
* Fixed: search users by tag
* Fixed: force UT8 encoding option
* Fixed: email content type
* Fixed: WPML compatibility
* Fixed: permalink base name format and redirect loop in profile page
* Fixed: form labels textdomain
* Fixed: edit profile redirect
* Tweak: accept period in profile url

= 1.3.44: March 11, 2016 =
* New: an option to force Strings to use UTF-8 encoding
* New: an option to change Gravatar default image
* New: South Sudan to the list of countries.
* Fixed: update profile edit
* Fixed: remove feed from content restriction
* Fixed: search username query
* Fixed: support for server query string data after user login
* Fixed: matching fields values
* Fixed: email template path
* Fixed: shortcode within [um_show_content] shortcode
* Tweak: remove notices

= 1.3.43: March 5, 2016 =
* Fixed: redirect URL after login
* Fixed: security check in registration form
* Fixed: email template path string
* Fixed: member directory query

= 1.3.42: March 3, 2016 =
* Fixed: email template and localization
* Fixed: redirect URL

= 1.3.41: March 3, 2016 =
* Fixed: Registration form redirect url

= 1.3.40: March 3, 2016 =

* New: filter `um_<field_type>_form_show_field` for display field
* New: shortcode: show custom content to specific role [um_show_content roles='member'][/um_show_content]
* New: 'not' attribute to [um_show_content not='member,contributor'][/um_show_content] shortcode
* Tweak: update masonry script
* Tweak: sql concatenate with prepare statement
* Fixed: remove notices
* Fixed: missing mCSB_buttons.png
* Fixed: set Gravatar default image as UM default image
* Fixed: fix default gravatar image
* Fixed: select2 multi dropdown for wc orders
* Fixed: show admin bar option
* Fixed: session issue with logout
* Fixed: register using email address if it exists
* Fixed: duplicate full_name permalinks
* Fixed: duplicate profile
* Fixed: show admin bar for non-logged in users
* Fixed: honorifics in full name
* Fixed: unsynced wp role
* Fixed: display wp user role filters
* Fixed: select and radio invalid value
* Fixed: email template path
* Fixed: user profile url with single dash in the last name
* Fixed: function to check meta value existence by meta key
* Fixed: um-admin-dashboard warnings
* Fixed: community role field in profile edit screen
* Fixed: mismatched roles
* Fixed: admin access in profiles
* Fixed: allow multiple member directory shortcode in a page
* Fixed: datepicker for ios and safari
* Fixed: adding of members in wp-admin
* Fixed: Fix redirection and XSS issue in login form

= 1.3.39: February 24, 2016 =

* New: add gravatar transfer tool
* New: show users with gravatar photo in member directory
* New: add upgrade class for data migration
* Tweak: Set last login for new users to show in member directory
* Tweak: validate roles for forms without fields
* Tweak: cropper js update
* Tweak: update minified script
* Tweak: tooltip and comment
* Fixed: member search query
* Fixed: Registration process with pending and show message enabled
* Fixed: Fix form security validation
* Fixed: email content type, template and localization
* Fixed: remove php notices
* Fixed: custom columns for roles
* Fixed: admin bar visibility per user role
* Fixed: community role editing

= 1.3.38: February 19, 2016 =

* Tweak: remove username validation
* Tweak: update minified scripts
* Fixed: Fix email and user submitted data encoding
* Fixed: role validation on register submission
* Fixed: form role and validation
* Fixed: search form pagination visibility

= 1.3.37: February 17, 2016 =

* New: Add password confirmation validation
* New: Add VK url validation
* New: Add Vkontakte as predefined url field
* New: Add additional file types
* New: Add file size limit label in image field
* New: Added password reset limit
* New: Allow redirect_to param after registration
* New: Indonesian language support added
* New: Add bio characters limit
* Tweak: Use native WP masonry script instead of duplicating it
* Tweak: Add image upload notice
* Tweak: Add option to allow users to hide profiles from member page
* Tweak: Add filters to modify output field
* Tweak: Add filter hook for email template path
* Tweak: Tweak upload form styles
* Tweak: Remove masonry from core and gulp
* Tweak: Add admin assets and apply minification
* Tweak: Update pickadate assets
* Tweak: Allowing usertags in search filters
* Tweak: Allow members template to be customized/overridden
* Tweak: Option to login user after clicking the activation link
* Tweak: Remove bio count strings
* Fixed: bio limit javascript error
* Fixed: ssl checker for load balancers
* Fixed: redirect loop with wpml permalink
* Fixed: WPML permalink and form compatibility
* Fixed: blocked words
* Fixed: searching with space
* Fixed: change password
* Fixed: members grid override
* Fixed: tipsy.js error
* Fixed: Plugin conflict causing account page displaying wrong info
* Fixed: email locale tempalte path
* Fixed: invalid role
* Fixed: validation for change password
* Fixed: unchecked access roles
* Fixed: telno input styles
* Fixed: escape display name in title attributes
* Fixed: datepicker css issue with some themes
* Fixed: make sure the hash parameter is a string
* Fixed: loading core assets
* Fixed: title tags not updated
* Fixed: empty uneditable fields
* Fixed: account deletion on one submission
* Fixed: Fixed indentation
* Fixed: user_login fallbacks and remove email address
* Fixed: password changed email template

= 1.3.36: January 6, 2016 =

* New: added in-page content restriction to protect content for logged-in or logged out users
* New: added community role field to user creation in backend
* New: added community role field to user editing in backend
* New: show specific users in members directory
* New: added a new field type: Number
* New: added filter hooks to specific profile fields
* New: added custom admin bulk actions support
* New: added usermeta support to content locking feature in-page
* Tweak: several tweaks in core to be more WordPress native
* Tweak: added fallback for page setup selections
* Tweak: automatic clickable links in profile header bio
* Tweak: trim long field labels in backend fields modal
* Fixed: profile page SEO title
* Fixed: multi-site redirect support
* Fixed: activation hash comparison
* Fixed: page setup fallback field
* Fixed: prevents php warnings and notices
* Fixed: WP-CLI and cronjobs issues
* Fixed: category posts restriction and redirection
* Fixed: category access settings
* Fixed: activation link

= 1.3.35: December 15, 2015 =

* Fixed: registration/login issues resolved

= 1.3.34: December 15, 2015 =

* New: new privacy option for fields: allow profile owner & specific roles to view the field
* Fixed: wrong php syntax in admin notice

= 1.3.33: December 15, 2015 =

* Fixed: Member search on homepage
* Fixed: emoticons support
* Fixed: redux notices, css styles in admin
* Fixed: users not being deleted

= 1.3.32: December 10, 2015 =

* Fixed: array format and notices
* Fixed: users search, delete confirmation and role filter
* Fixed: unique key field validation

= 1.3.31: December 9, 2015 =

* Fixed: Add slash in base url filter for multisite
* Fixed: manage user roles, status and filters
* Fixed: Enable WPML support to all UM url/links
* Fixed: Sanitize referers and printing notices in admin screens

= 1.3.30: December 3, 2015 =

* New: added Simplified Chinese language support
* Tweak: added many new filters (for developers)
* Fixed: Display name update in profile
* Fixed: Photo upload unique IDs
* Fixed: Remove duplicated method um_convert_tags

= 1.3.29: October 31, 2015 =

* New: added new documentation links to plugin files
* New: added filters to control profile photo menu (for developers)
* Fixed: added security patch to remove decrypted passwords from database
* Fixed: bug with blocked words during registration
* Fixed: some localization strings
* Fixed: php warnings/bugs on specific installs

= 1.3.28: October 13, 2015 =

* Fixed: Bug with plugin folder structure

= 1.3.27: October 13, 2015 =

* Fixed: Role name display in Users dashboard

= 1.3.26: September 25, 2015 =

* New: added Greek language support
* Tweak: added custom class to every user meta in member directory
* Tweak: added option to stop flushing rewrite rules every load (performance tweak)
* Fixed: WPML issue on multisite install
* Fixed: Removed redux menu from tools
* Fixed: fix for wp_authenticate_username_password()
* Fixed: searching users by e-mail address
* Fixed: conflict with sites with thousands of pages

= 1.3.25: September 7, 2015 =

* Fixed: 404 error on UM pages

= 1.3.24: September 6, 2015 =

* Tweak: saved some database queries
* Tweak: plugin not compatible with cache plugins out of the box - needs to exclude dynamic urls from cache
* Tweak: added more development filters in backend

= 1.3.23: September 2, 2015 =

* Fixed: PHP strstr() notice on profile

= 1.3.22: September 2, 2015 =

* Fixed: compatibility bug with older PHP versions

= 1.3.21: September 2, 2015 =

* Tweak: added security by sanitizing file/image uploads
* Fixed: issue with account page > notifications tab
* Fixed: image upload path in email notification
* Fixed: php issue with displaying name
* Fixed: missing localisation strings
* Fixed: couple of php notices

= 1.3.20: August 28, 2015 =

* New: added security measure for profile photo uploads
* New: added filter to hook in registration details sent in email notification
* New: added core pages filter to allow you change pages of extensions within plugin settings (e.g. activity)
* Fixed: multi-select field filtering bug
* Fixed: strip slashes from field names in fields modal
* Fixed: show drag and drop footer content only in the drag and drop form builder page
* Fixed: profile photo crop/upload issue
* Fixed: category/post specific restriction conflict
* Fixed: display UM classes only in UM pages
* Fixed: minor code improvements

= 1.3.19: August 20, 2015 =

* Fixed: please update - profile issue

= 1.3.18: August 20, 2015 =

* New: filter for comment types tab in profile
* New: jQuery.scrollto script added (for developers and extensions support)
* Fixed: XSS vulnerability in text input
* Fixed: user goes to profile about tab after editing profile

= 1.3.17: August 13, 2015 =

* New: added Brazilian Portuguese language support
* Tweak: added support for upcoming social activity extension

= 1.3.16: August 11, 2015 =

* New: added option to restrict categories in addition to per post content restriction
* New: added support to use dynamic user/profile ID in shortcode field e.g. [your-shortcode user_id={profile_id}]
* New: added security feature to disable admin logging via frontend (optional)
* New: added filter to um_get_core_page() function (for developers)
* Tweak: removed delay from tooltips
* Fixed: conflict with Podcast feed

= 1.3.15: August 4, 2015 =

* Fixed: issue with logout from adminbar

= 1.3.14: August 4, 2015 =

* New: added last login date support
* New: show user's last login in profile
* New: added sorting members by last login date
* New: added option to re-assign core pages in plugin settings
* Fixed: issue with multi-select required field
* Fixed: URL validation for custom fields
* Fixed: backend user filtering by non-english role
* Fixed: RTL css bugs

= 1.3.13: July 22, 2015 =

* Fixed: Woocommerce manual order dropdown conflict

= 1.3.12: July 22, 2015 =

* New: ability to delete user cache from plugin dashboard
* New: function is_ultimatemember() checks if user is on UM page (developers)
* New: option to disallow editing email in account page
* New: added Spanish (Mexico) language support
* Fixed: bug with profile viewing and user roles
* Fixed: Woocommerce dropdown bugs/conflicts
* Fixed: ipad/tablet css fixes for profile columns
* Fixed: deleting users delete their content

= 1.3.11: July 8, 2015 =

* Fixed: Redux errors and popups in backend

= 1.3.1: July 7, 2015 =

* Fixed: major issue with showing HTML in profiles

= 1.3.0: July 7, 2015 =

* New: easily sync UM roles with WP roles with role settings
* New: first steps towards WPML compatibility
* New: option to show member results only If user has searched
* New: add .um-err class to UM form if the form contains errors
* New: updated redux framework to latest version
* Fixed: feed issue with private / access locked posts

= 1.2.997: June 21, 2015 =

* New: added support for Farsi / Romanian language
* Tweak: adapted core community roles to prevent conflicts
* Fixed: bug with search results pagination
* Fixed: issue with panic key usage and wp-admin screen
* Fixed: bug with custom field validation action

= 1.2.996: June 11, 2015 =

* Fixed: php notice causing errors to appear in both frontend and backend

= 1.2.995: June 11, 2015 =

* New: added required support for WooCommerce extension
* Tweak: added option to fix conflicts of user profile links using different server method to get current url
* Fixed: security fix for redux framework added
* Fixed: button appearance on tablets
* Fixed: member search by display name

= 1.2.994: June 6, 2015 =

* Tweak: added a filter hook to change priority of enqueued styles/scripts
* Fixed: UM forms and elements not appearing in IE
* Fixed: Skype field output
* Fixed: conflict with libraries using Mobile Detect
* Fixed: issue with WP locale (using get_locale() now instead)

= 1.2.993: May 29, 2015 =

* Fixed: correction to last update
* Fixed: Private messages extension bug

= 1.2.99: May 29, 2015 =

* New: added Czech language support
* Fixed: WooCommerce dropdown issues and bugs in backend

= 1.2.98: May 18, 2015 =

* New: added Google map field
* New: added Vimeo video field
* New: added YouTube video field
* New: added SoundCloud track field
* New: added Hebrew language support
* Tweak: do not show captcha response in submitted registration details
* Fixed: profile photo upload issue on profile view mode
* Fixed: user search in backend/frontend
* Fixed: UM login check
* Fixed: admin settings css issue on mobile
* Fixed: cache variable undefined issue

= 1.2.97: May 13, 2015 =

* Fixed: issue with image upload fields
* Fixed: date localization resolved
* Fixed: issue with image upload during registration
* Fixed: JS issues for PM extension

= 1.2.96: May 12, 2015 =

* New: hooks and compatibility with Private Messages extension
* Fixed: bug with empty password on welcome e-mail
* Fixed: bug with member search using default permalinks

= 1.2.95: May 9, 2015 =

* New: RESTful API methods update.user, get.stats, and delete.user
* New: added Danish (Dansk) support
* New: added Swedish (Svenska) support
* Tweak: minor account and logout redirection tweaks
* Fixed: issue with changing user role
* Fixed: bug with login form validation
* Fixed: issue with biography field and html

= 1.2.94: May 6, 2015 =

* Fixed: bug with activation e-mails
* Fixed: bug stopping password reset
* Fixed: bug with changing user role and status in backend

= 1.2.93: May 5, 2015 =

* New: user profiles are cached to speed up load time
* New: emoji support added to bio / user descriptions
* Fixed: issues with bio field HTML 
* Fixed: WP-admin PHP warning
* Fixed: bug with localization of en_US.po file

= 1.2.92: May 2, 2015 =

* New: Important: Introduces the Ultimate Member RESTful API
* Tweak: improved um_user_ip() function
* Fixed: issue with invalid html on profile photo and cover photo

= 1.2.91: April 30, 2015 =

* New: added custom field validation support via hooks um_custom_field_validation_{$hook}
* Fixed: important bug with profile menu tabs / system

= 1.2.9: April 29, 2015 =

* New: display pending users count in backend
* Tweak: improved user deletion process from backend
* Tweak: tweaked filter for register/login buttons
* Tweak: disabled registration timebot for admins
* Fixed: wp-load.php path in image and file upload scripts
* Fixed: RTL compatibility bugs
* Fixed: bug with registration and role field
* Fixed: bug with edit profile and biography length in header

= 1.2.8: April 25, 2015 =

* Fixed: Important WP 4.2 conflict resolved: filtering users in backend

= 1.2.7: April 25, 2015 =

* Tweak: Compatible with WordPress 4.2
* Tweak: general code tweaks and improvements
* Tweak: new action hook when user is deleted
* Tweak: new action/filter hooks for profiles (developers)
* Tweak: new filter hook for profile privacy option
* Fixed: permalink issues

= 1.2.6: April 22, 2015 =

* Fixed: password reset security fix ( do not reveal emails )
* Fixed: bug with custom profile templates
* Fixed: display name in member directories
* Fixed: URL fields display in member directory
* Fixed: many bugs with previous version

= 1.2.5: April 21, 2015 =

* Fixed: e-mail activation bugs

= 1.2.4: April 20, 2015 =

* New: added Russian language support
* Fixed: Security patch related to add_query_arg()
* Fixed: bug with approval HTML e-mail

= 1.2.3: April 16, 2015 =

* Fixed: major bug with admin capability / editing user profiles via frontend

= 1.2.2: April 15, 2015 =

* New: added caching to user roles and user permissions to save queries
* New: added user switching feature to allow super admins to sign in as another user easily (without password)
* New: added new modal css/js for future support
* New: added custom scrollbar support for future development use
* Tweak: added form elements focus css
* Fixed: bug with required radio button
* Fixed: prevent access for backend login/register/lost password for a logged in user

= 1.1.6: April 7, 2015 =

* Fixed: major bug with datepicker (Update recommended)

= 1.1.5: April 4, 2015 =

* New: new action hook that runs when user role is changed um_member_role_upgrade
* Fixed: bug/compatibility issue with caching UM roles data
* Fixed: bug with changing role settings/permissions
* Fixed: bug with setting e-mail activation redirect URL

= 1.1.4: April 2, 2015 =

* Fixed: Major bug with dropdown and date fields (Update recommended)
* Fixed: hard-coded translation issues

= 1.1.3: April 1, 2015 =

* New: added option to manage if access control widgets can be edited by admins only
* Tweak: update to last security patch - deletes user who try to get unauthorized access

= 1.1.2: March 30, 2015 =

* Fixed: Important security patch - please update
* Fixed: conflict with The Events Calendar plugin
* Fixed: bug with edit profile link

= 1.1.1: March 29, 2015 =

* Fixed: bug where you user could use an already existing e-mail in account page
* Fixed: bug with special characaters in username
* Fixed: bug with showing draft posts in user profile

= 1.1.0: March 27, 2015 =

* New: added multi language support to assign different languages to different forms (beta feature)
* New: added RTL support (beta, still partial)
* New: added a dashboard widget to view latest blog posts from the plugin
* Tweak: changed manage_options permission to edit_users on some admin actions
* Fixed: corrected all active color references in the css
* Fixed: bug with user_row_actions filter
* Fixed: do not store user_pass in submitted metakey during registration

= 1.0.96: March 25, 2015 =

* New: Added Arabic language (ar) support
* Fixed: Date fields not working in Safari
* Fixed: issue with HTML e-mails
* Fixed: issue with showing sidebar logout widget on bbpress forums

= 1.0.95: March 24, 2015 =

* Tweak: added more hooks to mail function to allow for sending custom e-mails
* Fixed: issue with content lock settings in backend appearing for non-admins
* Fixed: issue with form errors handling

= 1.0.94: March 23, 2015 =

* Fixed: bug with member directory search
* Fixed: bug with custom role homepage

= 1.0.93: March 22, 2015 =

* Fixed: bug with showing register and login forms on same page

= 1.0.92: March 20, 2015 =

* New: added option to customize redirection URL after e-mail activation
* Fixed: issue with hardcoded ajax/upload URLs - they are now localized
* Fixed: issue with admin notification for a deleted account
* Fixed: admin notifications are in plain text format

= 1.0.91: March 20, 2015 =

* Tweak: featured image in user posts goes to post link
* Fixed: solved a bug with e-mail validation

= 1.0.90: March 19, 2015 =

* Tweak: minor admin css changes
* Tweak: error message for frontend upload with theme/plugin conflicts

= 1.0.89: March 18, 2015 =

* Tweak: Major Performance Improvements (beta)

= 1.0.88: March 18, 2015 =

* Fixed: bug with viewing member profiles

= 1.0.87: March 17, 2015 =

* Tweak: profile edit form tweaked to be processed for profile edit only. allows you to have custom (not nested) valid forms in other profile tabs

= 1.0.86: March 16, 2015 =

* Tweak: UM admins can see unapproved users on front-end members directory
* Fixed: few misspelling errors
* Fixed: bug with custom profile templates (showing blank) resolved

= 1.0.85: March 14, 2015 =

* New: added option to show user social links in profile header (optional)
* New: added option to display post featured image in profile "posts" tab
* Tweak: improved error reporting for theme conflicts on photo upload
* Fixed: issue with comments tab on profile

= 1.0.84: March 13, 2015 =

* New: adds automatic body class to UM core pages automatically
* Fixed: important jQuery issue
* Fixed: upload security issue - extension error was empty

= 1.0.83: March 12, 2015 =

* New: added a logout template If user is already logged in (customizable)
* New: strong password formula not required when resetting password (optional)
* Fixed: jQuery issue with live() method - Thanks to Jim Wetton

= 1.0.82: March 11, 2015 =

* Fixed: issue with saving user account general tab

= 1.0.81: March 11, 2015 =

* New: official support for plugin extensions released

= 1.0.80: March 10, 2015 =

* Tweak: added licensing support to plugin core
* Fixed: issue with account form

= 1.0.79: March 10, 2015 =

* Tweak: Redux up to date
* Fixed: security issue related to deleting a temp file via ajax
* Fixed: bug with a php warning on undefined field type

Credits to "James Golovich http://www.pritect.net" for the security checks

= 1.0.78: March 10, 2015 =

* Fixed: important correction from previous version

= 1.0.77: March 10, 2015 =

* New: integration with comments to show user profile link instead of user link (not compatible with all themes)
* New: option to control maximum size of uploaded profile photo
* New: option to control maximum size of uploaded cover photo
* Tweak: URL fields will are now treated as hyperlinks
* Fixed: bug with member directory privacy option
* Fixed: bug with using # as a character in file or image upload

= 1.0.76: March 7, 2015 =

* New: added {user_avatar_small} tag to display user photo in menu (requires extra css work)
* Tweak: Removed !important css rules from colors and backgrounds
* Fixed: issue with content block field

= 1.0.75: March 5, 2015 =

* New: improved & modern html e-mail templates
* New: addon to transfer BuddyPress profile photos to Ultimate Member (user request)
* New: added option to turn off time bot feature (fixes conflict with plugins)
* New: added built-in addons support
* Tweak: improved backend design and css

= 1.0.74: March 4, 2015 =

* Fixed: bug with numeric validation for a field
* Fixed: bug with conditional logic rules with checkbox

= 1.0.73: March 3, 2015 =

* Tweak: general code improvements

= 1.0.72: March 2, 2015 =

* Fixed: bug with e-mail activation since last update

= 1.0.71: March 2, 2015 =

* Fixed: issue with password reset link
* Fixed: issue with social links showing but user did not fill them

= 1.0.70: March 2, 2015 =

* Tweak: added a filter hook to control profile photo url
* Tweak: harder random generated passwords by making the length 40 characters for a key/password
* Tweak: added option to enable/disable custom css tab (off by default)
* Tweak: changed rewrite rules to be compatible with some themes and plugins
* Fixed: bug with Role field not showing error when required and left empty
* Fixed: bug with showing incorrect age when users did not fill their age
* Fixed: issue with template name for custom profile templates

= 1.0.69: February 28, 2015 =

* Tweak: better WP logout handling
* Tweak: new action and filter hooks added

= 1.0.68: February 27, 2015 =

* New: added support for mp3 as allowed filetype / upload
* Fixed: bug with profile privacy option (on non-english sites)
* Fixed: uncommon php warning caused by um_get_user_avatar_url() function
* Fixed: new translation corrections

= 1.0.67: February 26, 2015 =

* New: Improved the default HTML e-mail templates design
* New: added a bunch of action hooks to account tabs and content
* Tweak: added a few template tags to use in email: {site_url}, {user_account_link}
* Fixed: issue with making a checkbox required prior to registering
* Fixed: issue with comments showing in posts tab under profile
* Fixed: issue with plugin uninstallation link not showing in multisite

= 1.0.66: February 25, 2015 =

* New: added option to send e-mails as HTML
* New: added default HTML templates for e-mail notifications

= 1.0.65: February 24, 2015 =

* New: added option to customize register form secondary button URL
* New: added option to customize login form secondary button URL
* Fixed: issue with global access lock when homepage is excluded
* Fixed: issue with custom account tabs
* Fixed: minor css conflict with profile photo

= 1.0.64: February 22, 2015 =

* Tweak: updated language files on server
* Tweak: modified account page hooks to accept custom hooks easily
* Fixed: important css issues with safari browser
* Fixed: language notice will no longer show on (English UK/Other) wordpress sites

= 1.0.63: February 21, 2015 =

* Tweak: minor changes to dashboard widgets
* Tweak: cleaned dashboard js
* Tweak: a few action hooks refined
* Tweak: added filters to registration form buttons
* Fixed: issue with delete account feature

= 1.0.62: February 20, 2015 =

* New: added Polish (Polski) language
* New: added option to disable Name fields from Account page
* New: added support for custom profile templates selectable from template dropdown
* Tweak: remove rows with no fields from profile view
* Tweak: remove empty rows/row headings from profile
* Fixed: removed plain password user meta key from all users
* Fixed: issue with image upload when form has errors
* Fixed: resolved issue with disabling profile menu / tabs

= 1.0.61: February 20, 2015 =

* Tweak: Upload button text is made translatable
* Fixed: conflicts with Divi theme
* Fixed: issue with Roles dropdown field

= 1.0.60: February 18, 2015 =

* Tweak: added a protection to prevent wp-admin lockout for admin users
* Tweak: new hook for account page form submitting (for developers)
* Tweak: added a few missing translations
* Fixed: issue with roles dropdown not saving its state
* Fixed: issue with roles dropdown when made required in form
* Fixed: issue with tabbing on form fields

= 1.0.59: February 17, 2015 =

* New added Finnish language (fi_FI)
* Tweak: show e-mail column in users backend
* Fixed: issue with showing members directory on frontpage

= 1.0.58: February 16, 2015 =

* Fixed: display name as search field in member directory
* Fixed: translation issues in backend settings
* Fixed: issue with non-english letters in display names
* Fixed: bug with multiple default values for multi-select and checkbox fields
* Fixed: bug with multiple conditional logic based on different checkbox values

= 1.0.57: February 16, 2015 =

* Tweak: Italian language up-to-date
* Fixed: issue with registration where it can trigger a php warning
* Fixed: some translation issues

= 1.0.56: February 15, 2015 =

* Fixed: issue with permalink changes

= 1.0.55: February 15, 2015 =

* New: added Dutch (Nederlands) language
* New: show user registration/joined date in profile and/or member directory
* New: added facebook meta tags on user profiles (You have to disable facebook og tags in your SEO plugin)
* Tweak: sort users by default in "backend" by newest users first
* Tweak: added a close icon to profile and account notices
* Fixed: changed all time features to reflect WordPress installation time
* Fixed: timestamp on registration info shows form submission date/day
* Fixed: updated language files and new translation words

= 1.0.54: February 15, 2015 =

* New: added a remember me checkbox to login forms by default (optional)
* Tweak: keep your users signed in even if they close browser (optional)
* Tweak: minor css changes
* Fixed: bug with double redirects (causing incorrect loop) after login on some sites

= 1.0.53: February 14, 2015 =

* Tweak: when deleting users in backend, users will be deleted upon confirmation only
* Tweak: deleted users content is assigned to admin by default (to avoid losing content)
* Fixed: include plugin js and css on specific pages only

= 1.0.52: February 13, 2015 =

* Fixed: issue with users backend **update recommended**
* Fixed: preview registration info in users backend

= 1.0.51: February 13, 2015 =

* New: show registration info for each user in users backend
* New: show user stats at a glance in plugin dashboard
* New: sort users in backend by account status
* New: sort users in backend by user role
* New: added option to disable plugin css and js on homepage
* Tweak: updated language and translations files
* Fixed: issue with changing user role from Profile page
* Fixed: php bug with user description that has links
* Fixed: small issue with rewrite rules

= 1.0.50: February 12, 2015 =

* New: added option to include plugin js/css only on specific pages (user suggestion)
* Tweak: image and file uploads tweaked
* Fixed: date and time picker localization
* Fixed: bug with date and time fields
* Fixed: issue with searching members with default permalinks used
* Fixed: minor css fixes

= 1.0.49: February 10, 2015 =

* New: added option to disable UM menu (fixes conflict with mega-menu on some themes/plugins)
* New: added option to disable strong password in Account / Password change tab
* New: added a notice to Account page when user updates account
* Fixed: issue with global access settings

= 1.0.48: February 10, 2015 =

* New: added translation downloader/updater in plugin dashboard
* New: added admin notice when language is updated or downloaded
* Tweak: redirect to login page by default if content is restricted
* Tweak: redirect back to the protected content after successful login
* Tweak: small modifications to plugin admin css
* Fixed: issue with registration form per role not appearing (when logged in)
* Fixed: image and file uploads strip illegal characters from file name
* Fixed: small issue with mandrill plugin
* Fixed: bug with role creation that have used slugs that exist in database

= 1.0.47: February 9, 2015 =

* New: A more native dashboard for Ultimate Member
* New: view your temp uploads directory size and purge it from dashboard
* Fixed: user uploads bug with handling photo uploads at once
* Fixed: issue with using UM role as search filter in directory
* Fixed: a little icon issue with directory backend
* Fixed: localized a few words from predefined fields

= 1.0.46: February 8, 2015 =

(Update Recommended)

* New: added Spanish language pack
* Fixed: important JS conflict in admin when UM is active
* Fixed: profile permalink issue for e-mail usernames
* Fixed: installation issue on some WP databases

= 1.0.45: February 8, 2015 =

* Fixed: Multisite bug php Fatal error: call to undefined function wpmu_delete_user()

= 1.0.44: February 8, 2015 =

* Tweak: improved performance: unused user photos are deleted when user upload
* Tweak: cleaned up dashboard code
* Tweak: updated current translations
* Fixed: display name field should be updated with wp_update_user and not in usermeta
* Fixed: admin js and css should are loaded in UM backend only

= 1.0.43: February 7, 2015 =

* New: added German (Deutsch) language support
* Tweak: updated all translation packs
* Fixed: profile social links in member directory
* Fixed: prevent storing user_pass in usermeta table
* Fixed: php error triggered from um-access.php file

= 1.0.42: February 6, 2015 =

* New: added option to block specific e-mail domains from registering
* New: 2 new social profile fields: YouTube and SoundCloud
* New: added option to show field icons in profile header
* New: import/export plugin settings feature added to the backend
* New: added option to duplicate forms in backend with one click
* Tweak: cleaned up installation code
* Tweak: minor css modifications
* Fixed: adding HTML and iframe content to html-allowed textarea fields
* Fixed: upload folders permissions with some configurations and servers
* Fixed: live preview of profile forms in backend

= 1.0.41: February 5, 2015 =

* New: added access control settings to custom post types, you can have custom access settings applied to your custom post types now
* New: divider and spacing fields fully support conditional logic
* Tweak: Turkish language completed
* Tweak: updated some translations
* Fixed: a couple of PHP warnings and errors have been resolved
* Fixed: conflict with MailPoet plugin has been resolved

= 1.0.40: February 4, 2015 =

* Tweak: line-breaks and automatic urls are now working for textarea fields
* Tweak: updated ReduxFramework to latest version
* Tweak: backend sentences are now properly localized / updated translation
* Fixed: important issue with field positions in backend form builder
* Fixed: issue with usernames that have spaces resolved
* Fixed: conflict with page title in bbpress forums resolved
* Fixed: issue with escaping the apostrophes resolved
* Fixed: issue with loading more posts/comments in profile resolved

= 1.0.39: February 3, 2015 =

* New: added support for child templates to allow customizing the templates of plugin via theme or child theme. [See how](https://ultimatemember.com/codex/overriding-default-ultimate-member-profile-templates/)
* New: added French language pack
* Tweak: improved localization notification in backend
* Tweak: improved template loading
* Fixed: a minor css issue with multi-select field placeholder

= 1.0.38: February 3, 2015 =

* New: added a built-in automatic language pack downloader
* New: plugin now available in Italian, Turkish (40%)
* Tweak: If you need the plugin on available language pack you can download it automatically via the dashboard
* Tweak: when user updates account page the page will refresh and stay in the same tab
* Fixed: possible permalinks conflict with some wordpress themes

= 1.0.37: February 2, 2015 =

* Tweak: automatic line breaks in user description field
* Tweak: added gravatar rating parameter to the gravatar function
* Fixed: permalinks issue with customized slug for user and account pages

= 1.0.36: February 1, 2015 =

* New: URLs in user description are automatically converted to hyperlinks
* New: added option to redirect author archive to their UM profile automatically
* New: added option to show asterisk next to required fields (optional)
* New: Turkish language file (40% completed)
* Tweak: updated language file with missing sentences and words
* Tweak: auto redirect user/ base to user profile (e.g. user/username/)
* Fixed: issue with wrong comments count on user profile menu
* Fixed: issue with display name tags in nav menu
* Fixed: issue with conditional logic for select/multi-select fields
* Fixed: issue with conditional logic for content blocks and shortcode fields
* Fixed: conflict with Pods wordpress plugin

= 1.0.35: January 31, 2015 =

* Tweak: exif module is not required anymore and does not stop photo uploads (exif is highly recommended)
* Fixed: issue with changing a WP administrator role to a community administrator role
* Fixed: issue with plugin uploads directory on some multisite installations
* Fixed: conflict with default profile tab and editing profile
* Fixed: minor css conflict on account page with some themes when viewed on tablets

= 1.0.34: January 31, 2015 =

* New: added option to set a default cover photo
* New: added option to hide restricted content from search and archive
* Fixed: php error in title tab with ElegantThemes
* Fixed: theme conflict with photo/cover upload
* Fixed: issue with country field showing country code in profile
* Fixed: issue with setting default tab
* Fixed: issue with 2-name user roles

= 1.0.33: January 30, 2015 =

* New: Introducing profile menu / tab system (optional)
* New: display user posts and comments in profile menu (optional)
* New: added option to force hide adminbar on the frontend even for administrators
* Tweak: added profile menu options to plugin settings panel
* Tweak: added option to enable/disable profile menu and/or profile menu tabs
* Tweak: added option to show or hide post and comment counts (when the tabs are active)
* Tweak: account activation via e-mail redirects user to login page and displays a success message
* Fixed: issue with conditional logic on profile fields has been resolved
* Fixed: bug with searching members by gender
* Fixed: admin nav menus conditional logic conflict with some themes
* Fixed: bug with datepicker field on windows servers

= 1.0.32: January 30, 2015 =

* New: added 3 new tags to use in email templates {first_name}, {last_name}, and {gender}
* Fixed: Issue with account page permalink resolved - [view issue](https://ultimatemember.com/forums/topic/permalink-bug/)
* Fixed: Issue with conditional menu items showing for un-approved users resolved - [view issue](https://ultimatemember.com/forums/topic/registration/)

= 1.0.31: January 29, 2015 =

* Fixed: Issue with custom user page slug resolved [view issue](https://wordpress.org/support/topic/translate-plugin-9)
* Fixed: PHP warning in members directory resolved
* Fixed: Issue with hardcoded user profile URLs in menu

= 1.0.30: January 29, 2015 =

* New: added option to control number of profiles to display in members directory for mobile devices
* New: new admin action hook 'um_extend_admin_menu' to extend plugin administration menu
* New: Improved plugin accessbility e.g add alt text to links and images so people with disabilities can use screen readers
* New: added option to show/hide the message that appears if profile is empty (includes emoticon show/hide)
* Tweak: new translatable strings
* Tweak: added option to customize single-result text for members directory
* Tweak: removed unnecessary code from member directory backend
* Tweak: removed unnecessary js from admin head
* Fixed: Account page is now translatable
* Fixed: content restriction widget css in backend

= 1.0.29: January 28, 2015 =

* New: added feature to show user display name in menu (e.g. Welcome, {display_name})
* New: added option to add text to dividers (which can be added using the drag-and-drop form builder)
* New: security improvement: added whitelisted IP(s) option to allow you to access the WP-admin screen always (prevents lockout)
* New: added filter hook um_whitelisted_wpadmin_access to control access to wp-admin login screens (for developers)
* New: added custom css option to apply extra css styling rules from plugin settings
* New: added custom css option to each form allowing you to apply extra styling rules per form
* New: added option to customize profile fields area width (per form basis) besides global option
* Tweak: compatibility with default permalinks (Pretty permalinks are strongly recommended though!)
* Tweak: improved the function that gets user IP address
* Tweak: performance: inline css from the plugin is automatically compressed/minified

= 1.0.28: January 27, 2015 =

* New: added compatibility with wpMandrill to handle email delivery
* Fixed: Issue with profile edit menu not appearing

= 1.0.27: January 27, 2015 =

* Fixed: WP admin bar issue with some plugins and themes
* Fixed: conflict with WP Recent Comments With Avatars plugin

= 1.0.26: January 26, 2015 =

* Fixed: Important issue fix (update recommended)

= 1.0.25: January 26, 2015 =

* New: addded support to use gravatars (optionally) If the user does not have a custom avatar
* New: um_user_permissions_filter hook (for developer use)
* Tweak: plugin uses get_avatar() properly now
* Fixed: user avatars in backend are fixed
* Fixed: Mobile_Detect class does not throw php error if it was previously called
* Fixed: corrected a few translations errors

= 1.0.24: January 26, 2015 =

* Tweak: predefined fields are now localized (translatable)
* Fixed: PHP warning in comments was fixed
* Fixed: avatars in comments

= 1.0.23: January 25, 2015 =

* Fixed: important bugfix with profile editing

= 1.0.22: January 25, 2015 =

* New: option to set maximum profile fields area width
* New: ajax functions for future development use
* Tweak: improved profile permalinks for e-mail based usernames
* Tweak: disable user from editing username (If admin put the username field by mistake in profile)
* Tweak: minor css changes
* Fixed: ability to clear conditional logic from fields in backend
* Fixed: corrected spacing issue in a multi-column layout in profile

= 1.0.21: January 24, 2015 =

* New: Added ajax action hook for development use
* New: Extended profile hooks
* New: you can restrict / apply access control to woocommerce shop page
* Fixed: content restriction for woocommerce shop page

= 1.0.20: January 24, 2015 =

* New: Custom email is sent to user after resetting/changing password
* New: Added action/filter hooks to profile
* Tweak: Improved a few core functions
* Fixed: Encoding issue with non-english sites fixed

= 1.0.19: January 23, 2015 =

* New: Border thickness option for members directory
* New: Option to show/hide forgot password link on login form
* Tweak: Capital initials on members directory
* Fixed: Issue with row styling in form builder
* Fixed: Conditional logic bug fixes
* Fixed: Icon for conditional rules in backend
* Fixed: php warning in debug mode
* Fixed: Mobile/phone number validation fixed

= 1.0.18: January 23, 2015 =

* Fixed: Issue with drag and drop form builder
* Fixed: Form builder trash icon in backend
* Fixed: Minor css adjustments in frontend

= 1.0.17: January 22, 2015 =

* New: WordPress Multi-site compatibility for user uploads and photos
* Fixed: Searching members by username or email in directory (partial search supported)
* Fixed: Anonymous tracking
* Fixed: Minor css fixes

= 1.0.16: January 22, 2015 =

* Fixed: Settings page: tracking popup removed

= 1.0.15: January 22, 2015 =

* New: User profiles now show a cool message if the user profile field area is empty
* New: Added 'visibility' setting to all field types in backend
* Tweak: Members search function supports partial search matching
* Tweak: Deleting photo or file removes file from server
* Tweak: Deleting a user will delete all his personal uploads from the server
* Fixed: Duplicate tooltip for password field has been removed

= 1.0.10: January 22, 2015 =

* Fixed: Template tags for welcome e-mail
* Fixed: {submitted_registration} e-mail template tag

= 1.0.0: January, 2015 =

* First official release!