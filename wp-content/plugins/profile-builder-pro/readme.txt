=== Profile Builder Pro === 

Contributors: reflectionmedia, barinagabriel
Donate link: http://www.cozmoslabs.com/wordpress-profile-builder/
Tags: registration, profile, user registration, custom field registration, customize profile, user fields, builder, profile builder, custom profile, user profile, custom user profile, user profile page, 
custom registration, custom registration form, custom registration page, extra user fields, registration page, user custom fields, user listing, user login, user registration form, front-end login, 
front-end register, front-end registration, frontend edit profile, edit profileregistration, customize profile, user fields, builder, profile builder, custom fields, avatar
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 1.3.12


Login, registration and edit profile shortcodes for the front-end. Also you can chose what fields should be displayed or add custom ones.

 
== Description ==

Profile Builder is WordPress registration done right. 

It lets you customize your website by adding a front-end menu for all your users, 
giving them a more flexible way to modify their user-information or register new users (front-end registration). 
Also, grants users with administrator rights to customize basic user fields or add custom ones. 

To achieve this, just create a new page and give it an intuitive name(i.e. Edit Profile).
Now all you need to do is add the following shortcode(for the previous example): [wppb-edit-profile]. 
Publish the page and you are done!

You can use the following shortcodes:

* **[wppb-edit-profile]** - to grant users front-end access to their personal information (requires user to be logged in).
* **[wppb-login]** - to add a front-end log-in form.
* **[wppb-register]** - to add a front-end registration form.
* **[wppb-recover-password]** - to add a password recovery form.

Users with administrator rights have access to the following features:

* add a custom stylesheet/inherit values from the current theme or use one of the following built into this plugin: default, white or black.
* select whether to display or not the admin bar in the front end for a specific user-group registered to the site.
* select which information-field can users see/modify. The hidden fields values remain unmodified.
* add custom fields to the existing ones, with several types to choose from: heading, text, textarea, select, checkbox, radio, and/or upload.
* add an avatar upload for users.
* create custom redirects
* front-end userlisting using the **[wppb-list-users]** shortcode.

NOTE:

This plugin only adds/removes fields in the front-end. The default information-fields will still be visible(and thus modifiable) from the back-end, while custom fields will only be visible in the front-end.
	


== Installation ==

1. Upload the profile-builder folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use one of the shortcodes available

== Frequently Asked Questions ==

= I navigated away from Profile Builder and now I can’t find it anymore; where is it? =
	
	Profile Builder can be found in the default menu of your WordPress installation under the “Users” sub-menu.

= Why do the custom WordPress fields still show up, even though I set it to be "hidden"? =

	Profile Builder only disables the default fields in the front-end of your site/blog, it does absolutely nothing in the dashboard.

= I entered the serial number I received in the confirmation e-mail, but the indicator still is red. What’s wrong? =

	The validation, as well as the update checks require an active internet connection. If you are currently working on a localhost, check your internet connection. If you, however, are on a live server and your serial number won't validate, it means that either our servers are/were down or your server blocked the validation request.

= I see that there is a heading in the Extra Profile Fields section of Profile Builder, but it isn’t displaying in the front-end, neither in the back-end. How come? =

	If you mean the default Header item "Extra Profile Fields", as long as you don't change the title, it won't show up.

= I deleted the default header from the Extra Profile Fields section, but when I refreshed the page, it was there again. Am I seeing things? =

	Luckily for you, you aren't imagining it! The plugin is designed in such way, that there must always be a header item in the list. But don't worry: your users won't see the default header.
 

= I can’t find a question similar to my issue; Where can I find support? =

	For more information please visit http://www.cozmoslabs.com and check out the faq section from Profile Builder


== Screenshots ==
1. Plugin Layout (Control Panel): screenshot1.jpg
2. Show/Hide the Admin Bar (Control Panel): screenshot2.jpg
3. Default Profile Fields (Control Panel): screenshot3.jpg
4. Extra Profile Fields (Control Panel): screenshot4.jpg
5. Register Your Version (Control Panel): screenshot5.jpg
6. Edit Profile Page: screenshot6.jpg
7. Login Page: screenshot7.jpg
8. Registration Page: screenshot8.jpg
9. Customizable Userlisting (Control Panel): screenshot9.png
10.Userlisting: screenshot10.png



== Changelog ==
= 1.3.12 =
Minor upgrades to the plugin.

= 1.3.11 =
Improved the "Admin Approval" userlisting, added full HTTPS compatibility.

= 1.3.10 =
Improved the userlisting feature.

= 1.3.9 =
Fixed "Edit Profile" bug and impred the "Admin Approval" default listing.

= 1.3.8 =
Improved a few existing features (like "Admin Approval", required fields, WPML compatibility), and added a new feature: login with email address.

= 1.3.7 =
Fixed the rewrite rule in the userlisting, so that it is compatible with several hierarchy-leveled pages.

= 1.3.6 =
Fixed a few bugs from v1.3.5

= 1.3.5 =
Added new options for the "Userlisting" feature.
Added translations: persian (thanks to Ali Mirzaei, info@alimir.ir).

= 1.3.4 =
Improved the Email Confirmation feature.

= 1.3.3 =
Improved a few existing functions.

= 1.3.2 =
Fixed a few warnings on the register page.

= 1.3.1 =
Fixed the issue where the admin bar wouldn't display anymore once set to hidden.

= 1.3.0 =
Added the "Email Customizer" feature. Also, fixed a few existing bugs.

= 1.2.9 =
Minor security fix.

= 1.2.8 =
Email Confirmation bug on WPMU fixed.

= 1.2.7 =
Fixed reCAPTCHA compatibility issue with wp-recaptcha WP plugin.

= 1.2.6 =
Security issue fixed regarding the "Email Confirmation" feature.

= 1.2.5 =
Added a fix (suggested by http://wordpress.org/support/profile/maximinime) regarding the admin bar not displaying properly in some instances.

= 1.2.4 =
Improved localizations.

= 1.2.3 =
Minor changes to the redirect function.

= 1.2.2 =
Minor changes to the plugin's files.

= 1.2.1 =
Added support for WP 3.5.

= 1.2.0 =
Added support for Profile Builder Hobbyist.

= 1.1.59 =
Fixed CSS issue in the back-end.

= 1.1.58 =
A few bugs fixed.

= 1.1.57 =
Separated the plugins files.

= 1.1.56 =
Replaced include with include_once to stop the class already declared error. 
Changed the plugin url in the plugin headers. 

= 1.1.55 =
Minor changes to the plugin.

= 1.1.54 =
Minor changes to the plugin.

= 1.1.53 =
Minor improvements to the userlisting feature.

= 1.1.52 =
Hotfix for the registration page.

= 1.1.51 =
Added a few more specicif notifications to the registration page and WP dashboard.

= 1.1.50 =
Added a login widget for dynamic redirecting.

= 1.1.49 =
Few more notices fixed.

= 1.1.48 =
Fixed a few notices.

= 1.1.47 =
Unapproved users don't have access to the recover password feature either.

= 1.1.46 =
Fixed an issue where users couldn't sign up with the same username, even though their account has been deleted. (only present when email confirmation was activated, or on a wpmu site).

= 1.1.45 =
Fixed a warning where the uploaded avatar size couldn't be returned because of "allow_url_fopen" being turned off.

= 1.1.44 =
Fixed a few warnings and a fatal error on registration specific for users with PHP v5.4.x.

= 1.1.43 =
Fixed a few warnings.

= 1.1.42 =
Added reCAPTCHA as a permanent addon into Profile Builder.

= 1.1.41 =
A few bugfixes and security improvements.

= 1.1.40 =
Added the long-awaited "Admin Approval" feature.

= 1.1.39 =
Fixed an error when trying to edit a select/checkbox field gave a bad error message.

= 1.1.38 =
Userlisting bugfix.

= 1.1.37 =
Added email confirmation for both single site and multi-site installations, added new filters for the datepicker date format, and many more.

= 1.1.36 =
Changes made in the update function.

= 1.1.35 =
Changed path for the update handler to a more stable url.

= 1.1.34 =
Security issue fix on the recover password page.

= 1.1.33 =
Minor update.

= 1.1.32 =
Added 2 extra filters on the add/edit custom field script.

= 1.1.31 =
Added a few extra filter-parameters to the edit profile page.

= 1.1.30 =
Serial Number validation bugfix.

= 1.1.29 =
Userlisting bugfix.

= 1.1.28 =
Plugin performance and stability increased.

= 1.1.27 =
Minor addons. Compatibility fix for AIOEC plugin.

= 1.1.26 =
Plugin performance increased.

= 1.1.25 =
Minor bugfixes.

= 1.1.24 =
Minor bugfixes.

= 1.1.23 =
Minor bugfixes.
Updated English translation.

= 1.1.22 =
Added Romanian translation.

= 1.1.21 =
Customizable userlisting.

= 1.1.20 =
Userlisting query improvement.

= 1.1.19 =
Avatar bugfix.

= 1.1.18 =
Avatar bugfixes (would't display image when image was smaller then the given size)

= 1.1.17 =
Minor bugfixes and few minor features.

= 1.1.16 =
Minor bugfixes and layout/functionality modifications.

= 1.1.15 =
Minor bugfix: the accepted and declined sign didn't appear on the Register Profile Builder page within the plugin.
Added translation:
*dutch (thanks to Guido vd Leest, gjvdleest@yahoo.com)

= 1.1.14 = 
Minor bugfix on the extra fields (terms and agreement checkbox description).
Updated the .po file.

= 1.1.13 = 
Code review and minor bugfixes. Also updated the readme.txt file.
Added translation:
*polish - update to existing one (thanks to krys, krys@krys.info).

= 1.1.12 =
Minor changes to the code and improvements.

= 1.1.11 =
Avatar and JS include bugfixes.

= 1.1.10 =
Had to revert to an older version as the bugfixes produced even more bugs.

= 1.1.9 =
Minor modifications and bugfixes.

= 1.1.8 = 
Avatar bugfix (error occured sometimes when trying to delete the avatar image).

= 1.1.7 = 
Minor bugfix.

= 1.1.6 =
Added more redirect options for more control over your site/blog, and a password recovery shortcode to go with the rest of the plugin's theme. 
Also added the possibility to set both the default and the custom fields as required (only works in the front end for now), a lot of new filters for a better and easier way to personalize the plugin, and a password recovery feature (shortcode) to be in tune with the rest of the plugin.
Added translations:
*italian (thanks to Gabriele, globalwebadvices@gmail.com)
*updated the english translation

= 1.1.5 =
Minor bugfix on the registration page. The user was prompted to agree to the terms and conditions even though this was not set on the register page.
Added translations:
*czech (thanks to Martin Jurica, martin@jurica.info)
*updated the english translation

= 1.1.4 =
Added the possibility to set up the default user-role on registration; by adding the role="role_name" argument (e.g. [wppb-register role="editor"]) the role is automaticly set to all new users. Also, you can find new custom fields, like a time-zone select, a datepicker, country select etc. 
Added addons feature: 
*custom redirect url after registration/login
*added user-listing (use short-code: [wppb-list-users])
Added translations:
*norvegian (thanks to Havard Ulvin, haavard@ulvin.no)
*dutch (thanks to Pascal Frencken, pascal.frencken@dedeelgaard.nl)
*german (thanks to Simon Stich, simon@1000ff.de)
*spanish (thanks to redywebs, www.redywebs.com) 
 

= 1.1.3 =
Avatar bugfix.

= 1.1.2 =
Added translations to: 
*hungarian(thanks to Peter VIOLA, info@violapeter.hu)
*french(thanks to Sebastien CEZARD, sebastiencezard@orange.fr)

Bugfixes/enhancements:
*login page now automaticly refreshes itself after 1 second, a little less annoying than clicking the refresh button manually
*fixed bug where translation didn't load like it should
*added new user notification: the admin will now know about every new subscriber
*fixed issue where adding one or more spaces in the checkbox options list, the user can't save values.


= 1.1.1 =
Avatar bugfix where the image appeared from another account's ID

= 1.1 =
Added a new user-interface (borrowed from the awesome plugin OptionTree created by Derek Herman) and the posibility to add custom fields to the list.

= 1.0.10 =
Bugfix - The wp_update_user attempts to clear and reset cookies if it's updating the password.
 Because of that we get "headers already sent". Fixed by hooking into the init.

= 1.0.9 =
Bugfix - On the edit profile page the website field added a new http:// everytime you updated your profile.
Bugfix/ExtraFeature - Add support for shortcodes to be run in a text widget area.

= 1.0.6 =
Apparently the WordPress.org svn converts my EOL from Windows to Mac and because of that you get "The plugin does not have a valid header."

= 1.0.5 =
You can now actualy install the plugin. All because of a silly line break.

= 1.0.4 =
Still no Change.

= 1.0.3 =
No Change.

= 1.0.2 =
Small changes.

= 1.0.1 =
Changes to the ReadMe File

= 1.0 =
Added the posibility of displaying/hiding default WordPress information-fields, and to modify basic layout.



























Downloaded from

                       ScriptMasters.info


