=== Category Order and Taxonomy Terms Order  ===
Contributors: nsp-code
Donate link: http://www.nsp-code.com/donate.php
Tags: category order,terms order, taxonomy order, admin order, categories sort, order category
Requires at least: 2.8
Tested up to: 4.8.1
Stable tag: 1.5.2.2

Order Categories and all custom taxonomies terms (hierarchically) and child terms using a Drag and Drop Sortable javascript capability. 

== Description ==

Order Categories and all custom taxonomies terms (hierarchically) using a Drag and Drop Sortable javascript capability. <strong>No Theme update is required</strong> the code will change the query on the fly.
If multiple taxonomies are created for a custom post type, a menu will allow to chose the one need to be sorted. If child categories (terms) are defined, those can be ordered too using the same interface.
<br />Also you can have the admin terms interface sorted per your new sort.
<br />This plugin is developed by <a target="_blank" href="http://www.nsp-code.com">Nsp-Code</a>

== Installation ==

1. Upload `taxonomy-terms-order` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin from Admin > Plugins menu.
3. Once activated you should check with Settings > Taxonomy Terms Order 
4. Use Taxonomy Order link which appears into each post type section to make your sort.

== Screenshots ==

1. Category Order Interface.
2. Multiple Taxonomies Interface.

== Frequently Asked Questions  ==

Feel free to contact me at electronice_delphi@yahoo.com

= I have no PHP knowledge at all, i will still be able to use this plugin? =

Yes, this is the right tool for you. The plugin comes with a unique feature to update the queries on the fly and return the terms in the required order without changing any line of code. Or as an alternative you can do that manually.

= I prefer to apply the sort through code, how can be done? =

Include a 'orderby' => 'term_order' within your get_terms() arguments.

= What taxonomies will allow me to sort? =

You can sort ALL taxonomies, including the default Categories.

= Is there any way i can get my admin interface to use the custom terms order? =

Absolutely, the plugin can do that. In fact you can configure so only the admin will update and the front side template will display the terms as before.

= There is a feature that i want it implemented, can you do something about it? =

All ideas are welcome and i put them on my list to be implemented into the new versions. Anyway this may take time, but if you are in a rush, please consider a small donation and we can arrange something.

= I still need more features =

Consider upgrading to our advanced version of this plugin at a very resonable price <a target="_blank" href="http://www.nsp-code.com/premium-plugins/wordpress-plugins/advanced-taxonomy-terms-order/">Advanced Taxonomy Terms Order Order</a>

== Change Log ==

= 1.5.2.2 =
  - Default admin capability changed from install_plugins to manage_options to prevent DISALLOW_FILE_MODS issue. https://wordpress.org/support/topic/plugin-breaks-when-disallow_file_mods-is-set-to-true/
  - Prepare plugin for Composer package
  - Interface table th elements titles left align
  - Interface Taxonomy terms count fix

= 1.5 =
  - Included 'ignore_term_order' to force menu_order ignore when autosort active.
  - Translations issues update

= 1.4.9 =
  - Remove translations from the package
  - Removed donate banner
  - PHP 7 fix
  - Unused action remove

= 1.4.8 =
  - textdomain folder fix
  - Translation fix for user roles
  - the_title filter replaced with terms_walker 
  - Add Nonce for admin settings

= 1.4.7 =
  - Texdomain change to taxonomy-terms-order to allow translations through translate.wordpress.org
  - WordPress 4.4 compatibility update
  - Css updates  

= 1.4.6.1 =
  - Security bug fix
  
= 1.4.5 =
  - Translation textdomain fix - thanks to Pedro Mendonça
  - Portuguese localization update - Pedro Mendonça

= 1.4.4 =
  - User role switch from deprecated user_level to capabilities 
  - Taxonomy sort for media
  - Admin Options update

= 1.4.2 =
  - Iranian Language (eydaaad@gmail.com) 
  - Admin css updates.

= 1.4.1 = 
  - Polish Language(Pozdrawiam - www.difreo.pl/   ;   Mateusz - www.czar-net.com   )  

= 1.4.0 = 
 - Hungarian Language(Adam Laki - http://codeguide.hu/)
 - Ukrainian translation (Michael Yunat - http://getvoip.com)
 - Czech translation

= 1.3.7 = 
 - Brazilian Portuguese Language (Rafael Forcadell - www.rafaelforcadell.com.br)

= 1.3.6 =
 - Traditional Chineze Language (Danny - http://sofree.cc)
 - Minor admin styling
   
= 1.3.4 =
 - Menu walker update
 - Translations load fix
 - Japanese language

= 1.3.0 = 
 - Headers already sent bug fix
 - Slovak Language (Branco Slovak http://webhostinggeeks.com/user-reviews/)

= 1.2.9 = 
 - Small updates
 - German and French languages.

= 1.2.7 = 
 - Localization implement, Dutch and Romanian.
 - Many thanks to Anja Fokker  http://www.werkgroepen.net/

 
== Upgrade Notice ==

Make sure you get the latest version


== Localization ==

Available in English, Dutch, French, Deutch, Slovak, Japanese, Traditional Chineze, Brazilian Portuguese, Hungarian, Ukrainian, Czech and Romanian
Whant to contribute with a translation to your language? Please check at https://translate.wordpress.org/projects/wp-plugins/taxonomy-terms-order

There isn't any Editors for your native language on plugin Contributors? You can help to moderate! https://translate.wordpress.org/projects/wp-plugins/taxonomy-terms-order/contributors
