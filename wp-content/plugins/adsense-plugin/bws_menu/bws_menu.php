<?php
/*
* Function for displaying BestWebSoft menu
* Version: 1.3.0
*/

if ( ! function_exists( 'bws_add_menu_render' ) ) {
	function bws_add_menu_render() {
		global $wpdb, $wpmu, $wp_version, $bws_plugin_info;
		$error = $message = $bwsmn_form_email = '';
		$bws_donate_link = 'https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=1&product_id=94';

		if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$bws_plugins = array(
			'captcha/captcha.php' => array(
				'name'			=> 'Captcha',
				'description'	=> 'Plugin intended to prove that the visitor is a human being and not a spam robot.',
				'link'			=> 'http://bestwebsoft.com/plugin/captcha-plugin/?k=d678516c0990e781edfb6a6c874f0b8a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/captcha-plugin/?k=d678516c0990e781edfb6a6c874f0b8a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Captcha+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=captcha.php',
				'pro_version'	=> 'captcha-pro/captcha_pro.php'
			),
			'contact-form-plugin/contact_form.php' => array(
				'name'			=> 'Contact form',
				'description'	=> 'Add Contact Form to your WordPress website.',
				'link'			=> 'http://bestwebsoft.com/plugin/contact-form/?k=012327ef413e5b527883e031d43b088b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/contact-form/?k=012327ef413e5b527883e031d43b088b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Contact+Form+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=contact_form.php',
				'pro_version'	=> 'contact-form-pro/contact_form_pro.php'
			), 
			'facebook-button-plugin/facebook-button-plugin.php' => array(
				'name'			=> 'Facebook Like Button',
				'description'	=> 'Allows you to add the Follow and Like buttons the easiest way.',
				'link'			=> 'http://bestwebsoft.com/plugin/facebook-like-button-plugin/?k=05ec4f12327f55848335802581467d55&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/facebook-like-button-plugin/?k=05ec4f12327f55848335802581467d55&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Facebook+Like+Button+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=facebook-button-plugin.php',
				'pro_version'	=> 'facebook-button-pro/facebook-button-pro.php'
			),
			'twitter-plugin/twitter.php' => array(
				'name'			=> 'Twitter',
				'description'	=> 'Allows you to add the Twitter "Follow" and "Like" buttons the easiest way.',
				'link'			=> 'http://bestwebsoft.com/plugin/twitter-plugin/?k=f8cb514e25bd7ec4974d64435c5eb333&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/twitter-plugin/?k=f8cb514e25bd7ec4974d64435c5eb333&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Twitter+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=twitter.php',
				'pro_version'	=> 'twitter-pro/twitter-pro.php'
			), 
			'portfolio/portfolio.php' => array(
				'name'			=> 'Portfolio',
				'description'	=> 'Allows you to create a page with the information about your past projects.',
				'link'			=> 'http://bestwebsoft.com/plugin/portfolio-plugin/?k=1249a890c5b7bba6bda3f528a94f768b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/portfolio-plugin/?k=1249a890c5b7bba6bda3f528a94f768b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Portfolio+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=portfolio.php'
			),
			'gallery-plugin/gallery-plugin.php' => array(
				'name'			=> 'Gallery',
				'description'	=> 'Allows you to implement a Gallery page into your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/gallery-plugin/?k=2da21c0a64eec7ebf16337fa134c5f78&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/gallery-plugin/?k=2da21c0a64eec7ebf16337fa134c5f78&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Gallery+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=gallery-plugin.php',
				'pro_version'	=> 'gallery-plugin-pro/gallery-plugin-pro.php'
			),
			'adsense-plugin/adsense-plugin.php'=> array(
				'name'			=> 'Google AdSense',
				'description'	=> 'Allows Google AdSense implementation to your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-adsense-plugin/?k=60e3979921e354feb0347e88e7d7b73d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/google-adsense-plugin/?k=60e3979921e354feb0347e88e7d7b73d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Adsense+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=adsense-plugin.php'
			),
			'custom-search-plugin/custom-search-plugin.php'=> array(
				'name'			=> 'Custom Search',
				'description'	=> 'Allows to extend your website search functionality by adding a custom post type.',
				'link'			=> 'http://bestwebsoft.com/plugin/custom-search-plugin/?k=933be8f3a8b8719d95d1079d15443e29&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/custom-search-plugin/?k=933be8f3a8b8719d95d1079d15443e29&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Custom+Search+plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=custom_search.php'
			),
			'quotes-and-tips/quotes-and-tips.php'=> array(
				'name'			=> 'Quotes and Tips',
				'description'	=> 'Allows you to implement quotes & tips block into your web site.',
				'link'			=> 'http://bestwebsoft.com/plugin/quotes-and-tips/?k=5738a4e85a798c4a5162240c6515098d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/quotes-and-tips/?k=5738a4e85a798c4a5162240c6515098d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Quotes+and+Tips+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=quotes-and-tips.php'
			),
			'google-sitemap-plugin/google-sitemap-plugin.php'=> array(
				'name'			=> 'Google Sitemap',
				'description'	=> 'Allows you to add sitemap file to Google Webmaster Tools.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-sitemap-plugin/?k=5202b2f5ce2cf85daee5e5f79a51d806&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/google-sitemap-plugin/?k=5202b2f5ce2cf85daee5e5f79a51d806&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Google+sitemap+plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=google-sitemap-plugin.php',
				'pro_version'	=> 'google-sitemap-pro/google-sitemap-pro.php'
			),
			'updater/updater.php'=> array(
				'name'			=> 'Updater',
				'description'	=> 'Allows you to update plugins and WP core.',
				'link'			=> 'http://bestwebsoft.com/plugin/updater-plugin/?k=66f3ecd4c1912009d395c4bb30f779d1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/updater-plugin/?k=66f3ecd4c1912009d395c4bb30f779d1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=updater+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=updater-options',
				'pro_version'	=> 'updater-pro/updater_pro.php'
			),
			'custom-fields-search/custom-fields-search.php'=> array(
				'name'			=> 'Custom Fields Search',
				'description'	=> 'Allows you to add website search any existing custom fields.',
				'link'			=> 'http://bestwebsoft.com/plugin/custom-fields-search/?k=f3f8285bb069250c42c6ffac95ed3284&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/custom-fields-search/?k=f3f8285bb069250c42c6ffac95ed3284&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Custom+Fields+Search+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=custom_fields_search.php'
			),
			'google-one/google-plus-one.php' => array(
				'name'			=> 'Google +1',
				'description'	=> 'Allows you to celebrate liked the article.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-plus-one/?k=ce7a88837f0a857b3a2bb142f470853c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/google-plus-one/?k=ce7a88837f0a857b3a2bb142f470853c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&type=term&s=Google+%2B1+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=google-plus-one.php',
				'pro_version'	=> 'google-one-pro/google-plus-one-pro.php'
			),
			'relevant/related-posts-plugin.php' => array(
				'name'			=> 'Relevant - Related Posts',
				'description'	=> 'Allows you to display related posts with similar words in category, tags, title or by adding special meta key for posts.',
				'link'			=> 'http://bestwebsoft.com/plugin/related-posts-plugin/?k=73fb737037f7141e66415ec259f7e426&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/related-posts-plugin/?k=73fb737037f7141e66415ec259f7e426&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Related+Posts+Plugin+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=related-posts-plugin.php'
			),
			'contact-form-to-db/contact_form_to_db.php' => array(
				'name'			=> 'Contact Form To DB',
				'description'	=> 'Allows you to manage the messages that have been sent from your site.',
				'link'			=> 'http://bestwebsoft.com/plugin/contact-form-to-db/?k=ba3747d317c2692e4136ca096a8989d6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/contact-form-to-db/?k=ba3747d317c2692e4136ca096a8989d6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Contact+Form+to+DB+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=cntctfrmtdb_settings',
				'pro_version'	=> 'contact-form-to-db-pro/contact_form_to_db_pro.php'
			),
			'pdf-print/pdf-print.php' => array(
				'name'			=> 'PDF & Print',
				'description'	=> 'Allows you to create PDF and Print page with adding appropriate buttons to the content.',
				'link'			=> 'http://bestwebsoft.com/plugin/pdf-print/?k=bfefdfb522a4c0ff0141daa3f271840c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/pdf-print/?k=bfefdfb522a4c0ff0141daa3f271840c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=PDF+Print+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=pdf-print.php',
				'pro_version'	=> 'pdf-print-pro/pdf-print-pro.php'
			),
			'donate-button/donate.php' => array(
				'name'			=> 'Donate',
				'description'	=> 'Makes it possible to place donation buttons of various payment systems on your web page.',
				'link'			=> 'http://bestwebsoft.com/plugin/donate/?k=a8b2e2a56914fb1765dd20297c26401b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/donate/?k=a8b2e2a56914fb1765dd20297c26401b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Donate+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=donate.php'
			),
			'post-to-csv/post-to-csv.php' => array(
				'name'			=> 'Post To CSV',
				'description'	=> 'The plugin allows to export posts of any types to a csv file.',
				'link'			=> 'http://bestwebsoft.com/plugin/post-to-csv/?k=653aa55518ae17409293a7a894268b8f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/post-to-csv/?k=653aa55518ae17409293a7a894268b8f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Post+To+CSV+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=post-to-csv.php'
			),
			'google-shortlink/google-shortlink.php' => array(
				'name'			=> 'Google Shortlink',
				'description'	=> 'Allows you to get short links from goo.gl servise without leaving your site.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-shortlink/?k=afcf3eaed021bbbbeea1090e16bc22db&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/google-shortlink/?k=afcf3eaed021bbbbeea1090e16bc22db&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Google+Shortlink+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=gglshrtlnk_options'
			),
			'htaccess/htaccess.php' => array(
				'name'			=> 'Htaccess',
				'description'	=> 'Allows controlling access to your website using the directives Allow and Deny.',
				'link'			=> 'http://bestwebsoft.com/plugin/htaccess/?k=2b865fcd56a935d22c5c4f1bba52ed46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/htaccess/?k=2b865fcd56a935d22c5c4f1bba52ed46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Htaccess+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=htaccess.php'
			),
			'google-captcha/google-captcha.php' => array(
				'name'			=> 'Google Captcha (reCAPTCHA)',
				'description'	=> 'Plugin intended to prove that the visitor is a human being and not a spam robot.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-captcha/?k=7b59fbe542acf950b29f3e020d5ad735&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/google-captcha/?k=7b59fbe542acf950b29f3e020d5ad735&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Google+Captcha+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=google-captcha.php'
			),
			'sender/sender.php' => array(
				'name'			=> 'Sender',
				'description'	=> 'You can send mails to all users or to certain categories of users.',
				'link'			=> 'http://bestwebsoft.com/plugin/sender/?k=89c297d14ba85a8417a0f2fc05e089c7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/sender/?k=89c297d14ba85a8417a0f2fc05e089c7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Sender+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=sndr_settings'
			),
			'subscriber/subscriber.php' => array(
				'name'			=> 'Subscriber',
				'description'	=> 'This plugin allows you to subscribe users for newsletters from your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/subscriber/?k=a4ecc1b7800bae7329fbe8b4b04e9c88&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/subscriber/?k=a4ecc1b7800bae7329fbe8b4b04e9c88&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Subscriber+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=sbscrbr_settings_page'
			),
			'contact-form-multi/contact-form-multi.php' => array(
				'name'			=> 'Contact Form Multi',
				'description'	=> 'This plugin allows you to subscribe users for newsletters from your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/contact-form-multi/?k=83cdd9e72a9f4061122ad28a67293c72&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/contact-form-multi/?k=83cdd9e72a9f4061122ad28a67293c72&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=Contact+Form+Multi+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> '',
				'pro_version'	=> 'contact-form-multi-pro/contact-form-multi-pro.php'
			),
			'bws-google-maps/bws-google-maps.php' => array(
				'name'			=> 'BestWebSoft Google Maps',
				'description'	=> 'Easy to set up and insert Google Maps to your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/bws-google-maps/?k=d8fac412d7359ebaa4ff53b46572f9f7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/bws-google-maps/?k=d8fac412d7359ebaa4ff53b46572f9f7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=BestWebSoft+Google+Maps&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=bws-google-maps.php',
				'pro_version'	=> 'bws-google-maps-pro/bws-google-maps-pro.php'
			),
			'bws-google-analytics/bws-google-analytics.php' => array(
				'name'			=> 'BestWebSoft Google Analytics',
				'description'	=> 'Allows you to retrieve basic stats from Google Analytics account and add the tracking code to your blog.',
				'link'			=> 'http://bestwebsoft.com/plugin/bws-google-analytics/?k=261c74cad753fb279cdf5a5db63fbd43&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/bws-google-analytics/?k=261c74cad753fb279cdf5a5db63fbd43&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> '/wp-admin/plugin-install.php?tab=search&s=BestWebSoft+Google+Analytics&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=bws-google-analytics.php'
			),
			'db-manager/db-manager.php' => array(
				'name'			=> 'DB manager',
				'description'	=> 'Allows you to download the latest version of PhpMyadmin and Dumper and manage your site.',
				'link'			=> 'http://bestwebsoft.com/plugin/db-manager/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/db-manager/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> 'http://bestwebsoft.com/plugin/db-manager/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'settings'		=> 'admin.php?page=db-manager.php'
			),
			'user-role/user-role.php' => array(
				'name'			=> 'User Role',
				'description'	=> 'Allows to change wordpress user role capabilities.',
				'link'			=> 'http://bestwebsoft.com/plugin/user-role/?k=dfe2244835c6fbf601523964b3f34ccc&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/plugin/user-role/?k=dfe2244835c6fbf601523964b3f34ccc&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'wp_install'	=> 'http://bestwebsoft.com/plugin/user-role/?k=dfe2244835c6fbf601523964b3f34ccc&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#download',
				'settings'		=> 'admin.php?page=user-role.php',
				'pro_version'	=> 'user-role-pro/user-role-pro.php'
			)
		);
		$bws_plugins_pro	= array(
			'gallery-plugin-pro/gallery-plugin-pro.php' => array(
				'name'			=> 'Gallery Pro',
				'description'	=> 'Allows you to implement as many galleries as you want into your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/gallery-pro/?k=382e5ce7c96a6391f5ffa5e116b37fe0&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase'		=> 'http://bestwebsoft.com/plugin/gallery-pro/?k=382e5ce7c96a6391f5ffa5e116b37fe0&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings'		=> 'admin.php?page=gallery-plugin-pro.php'
			),
			'contact-form-pro/contact_form_pro.php' => array(
				'name'			=> 'Contact form Pro',
				'description'	=> 'Allows you to implement a feedback form to a web-page or a post in no time.',
				'link'			=> 'http://bestwebsoft.com/plugin/contact-form-pro/?k=773dc97bb3551975db0e32edca1a6d71&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase'		=> 'http://bestwebsoft.com/plugin/contact-form-pro/?k=773dc97bb3551975db0e32edca1a6d71&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings'		=> 'admin.php?page=contact_form_pro.php'
			),
			'captcha-pro/captcha_pro.php' => array(
				'name'			=> 'Captcha Pro',
				'description'	=> 'Allows you to implement a super security captcha form into web forms.',
				'link'			=> 'http://bestwebsoft.com/plugin/captcha-pro/?k=ff7d65e55e5e7f98f219be9ed711094e&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase'		=> 'http://bestwebsoft.com/plugin/captcha-pro/?k=ff7d65e55e5e7f98f219be9ed711094e&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings'		=> 'admin.php?page=captcha_pro.php'
			),
			'updater-pro/updater_pro.php' => array(
				'name'			=> 'Updater Pro',
				'description'	=> 'Allows you to update plugins and WordPress core on your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/updater-pro/?k=cf633acbefbdff78545347fe08a3aecb&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/updater-pro?k=cf633acbefbdff78545347fe08a3aecb&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=updater-pro-options'
			),
			'contact-form-to-db-pro/contact_form_to_db_pro.php' => array(
				'name'			=> 'Contact form to DB Pro',
				'description'	=> 'The plugin provides a unique opportunity to manage messages sent from your site via the contact form.',
				'link' 			=> 'http://bestwebsoft.com/plugin/contact-form-to-db-pro/?k=6ce5f4a9006ec906e4db643669246c6a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/contact-form-to-db-pro/?k=6ce5f4a9006ec906e4db643669246c6a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=cntctfrmtdbpr_settings'
			),
			'google-sitemap-pro/google-sitemap-pro.php'=> array(
				'name'			=> 'Google Sitemap Pro',
				'description'	=> 'Allows you to add sitemap file to Google Webmaster Tools.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-sitemap-pro/?k=7ea384a5cc36cb4c22741caa20dcd56d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase'		=> 'http://bestwebsoft.com/plugin/google-sitemap-pro/?k=7ea384a5cc36cb4c22741caa20dcd56d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings'		=> 'admin.php?page=google-sitemap-pro.php'
			),
			'twitter-pro/twitter-pro.php' => array(
				'name'			=> 'Twitter Pro',
				'description'	=> 'Allows you to add the Twitter "Follow" and "Like" buttons the easiest way.',
				'link'			=> 'http://bestwebsoft.com/plugin/twitter-pro/?k=63ecbf0cc9cebf060b5a3c9362299700&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/twitter-pro?k=63ecbf0cc9cebf060b5a3c9362299700&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=twitter-pro.php'
			),
			'google-one-pro/google-plus-one-pro.php' => array(
				'name'			=> 'Google +1 Pro',
				'description'	=> 'Allows you to celebrate liked the article.',
				'link'			=> 'http://bestwebsoft.com/plugin/google-plus-one-pro/?k=f4b0a62d155c9df9601a0531ad5bd832&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/google-plus-one-pro?k=f4b0a62d155c9df9601a0531ad5bd832&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=google-plus-one-pro.php'
			),
			'facebook-button-pro/facebook-button-pro.php' => array(
				'name'			=> 'Facebook Like Button Pro',
				'description'	=> 'Allows you to add the Follow and Like buttons the easiest way.',
				'link'			=> 'http://bestwebsoft.com/plugin/facebook-like-button-pro/?k=8da168e60a831cfb3525417c333ad275&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/facebook-like-button-pro?k=8da168e60a831cfb3525417c333ad275&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=facebook-button-pro.php'
			),
			'pdf-print-pro/pdf-print-pro.php' => array(
				'name'			=> 'PDF & Print Pro',
				'description'	=> 'Allows you to create PDF and Print page with adding appropriate buttons to the content.',
				'link'			=> 'http://bestwebsoft.com/plugin/pdf-print-pro/?k=fd43a0e659ddc170a9060027cbfdcc3a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/pdf-print-pro?k=fd43a0e659ddc170a9060027cbfdcc3a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=pdf-print-pro.php'
			),
			'bws-google-maps-pro/bws-google-maps-pro.php' => array(
				'name'			=> 'BestWebSoft Google Maps Pro',
				'description'	=> 'Easy to set up and insert Google Maps to your website.',
				'link'			=> 'http://bestwebsoft.com/plugin/bws-google-maps-pro/?k=117c3f9fc17f2c83ef430a8a9dc06f56&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/bws-google-maps-pro/?k=117c3f9fc17f2c83ef430a8a9dc06f56&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=bws-google-maps-pro.php'
			),
			'contact-form-multi-pro/contact-form-multi-pro.php' => array(
				'name'			=> 'Contact Form Multi Pro',
				'description'	=> 'This plugin is an exclusive add-on to the Contact Form Pro. Allows to create multiple contact forms.',
				'link'			=> 'http://bestwebsoft.com/plugin/contact-form-multi-pro/?k=fde3a18581c143654f060c398b07e8ac&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/contact-form-multi-pro/?k=fde3a18581c143654f060c398b07e8ac&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> ''
			),
			'user-role-pro/user-role-pro.php' => array(
				'name'			=> 'User Role',
				'description'	=> 'Allows to change wordpress user role capabilities.',
				'link'			=> 'http://bestwebsoft.com/plugin/user-role-pro/?k=cfa9cea6613fb3d7c0a3622fa2faaf46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'purchase' 		=> 'http://bestwebsoft.com/plugin/user-role-pro/?k=cfa9cea6613fb3d7c0a3622fa2faaf46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version . '#purchase',
				'settings' 		=> 'admin.php?page=user-role-pro.php'
			)
		);
		
		$all_plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins' );

		$recommend_plugins = array_diff_key( $bws_plugins, $all_plugins );

		foreach ( $bws_plugins as $key_plugin => $value_plugin ) {
			if ( ! isset( $all_plugins[ $key_plugin ] ) && isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $all_plugins[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) {				
				unset( $recommend_plugins[ $key_plugin ] );
			}
		}

		foreach ( $all_plugins as $key_plugin => $value_plugin ) {
			if ( ! isset( $bws_plugins[ $key_plugin ] ) && ! isset( $bws_plugins_pro[ $key_plugin ] ) )
				unset( $all_plugins[ $key_plugin ] );

			if ( isset( $bws_plugins[ $key_plugin ] ) && isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $all_plugins[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) {				
				unset( $all_plugins[ $key_plugin ] );
			}
		}

		if ( isset( $_GET['action'] ) && 'system_status' == $_GET['action'] ) {
			$all_plugins = get_plugins();
			$active_plugins = get_option( 'active_plugins' );
			$sql_version = $wpdb->get_var( "SELECT VERSION() AS version" );
		    $mysql_info = $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
		    if ( is_array( $mysql_info) )
		    	$sql_mode = $mysql_info[0]->Value;
		    if ( empty( $sql_mode ) )
		    	$sql_mode = __( 'Not set', 'bestwebsoft' );
		    if ( ini_get( 'safe_mode' ) )
		    	$safe_mode = __( 'On', 'bestwebsoft' );
		    else
		    	$safe_mode = __( 'Off', 'bestwebsoft' );
		    if ( ini_get( 'allow_url_fopen' ) )
		    	$allow_url_fopen = __( 'On', 'bestwebsoft' );
		    else
		    	$allow_url_fopen = __( 'Off', 'bestwebsoft' );
		    if ( ini_get( 'upload_max_filesize' ) )
		    	$upload_max_filesize = ini_get( 'upload_max_filesize' );
		    else
		    	$upload_max_filesize = __( 'N/A', 'bestwebsoft' );
		    if ( ini_get('post_max_size') )
		    	$post_max_size = ini_get('post_max_size');
		    else
		    	$post_max_size = __( 'N/A', 'bestwebsoft' );
		    if ( ini_get( 'max_execution_time' ) )
		    	$max_execution_time = ini_get( 'max_execution_time' );
		    else
		    	$max_execution_time = __( 'N/A', 'bestwebsoft' );
		    if ( ini_get( 'memory_limit' ) )
		    	$memory_limit = ini_get( 'memory_limit' );
		    else
		    	$memory_limit = __( 'N/A', 'bestwebsoft' );
		    if ( function_exists( 'memory_get_usage' ) )
		    	$memory_usage = round( memory_get_usage() / 1024 / 1024, 2 ) . __( ' Mb', 'bestwebsoft' );
		    else
		    	$memory_usage = __( 'N/A', 'bestwebsoft' );
		    if ( is_callable( 'exif_read_data' ) )
		    	$exif_read_data = __( 'Yes', 'bestwebsoft' ) . " ( V" . substr( phpversion( 'exif' ), 0,4 ) . ")" ;
		    else
		    	$exif_read_data = __( 'No', 'bestwebsoft' );
		    if ( is_callable( 'iptcparse' ) )
		    	$iptcparse = __( 'Yes', 'bestwebsoft' );
		    else
		    	$iptcparse = __( 'No', 'bestwebsoft' );
		    if ( is_callable( 'xml_parser_create' ) )
		    	$xml_parser_create = __( 'Yes', 'bestwebsoft' );
		    else
		    	$xml_parser_create = __( 'No', 'bestwebsoft' );

			if ( function_exists( 'wp_get_theme' ) )
				$theme = wp_get_theme();
			else
				$theme = get_theme( get_current_theme() );

			if ( function_exists( 'is_multisite' ) ) {
				if ( is_multisite() ) {
					$multisite = __( 'Yes', 'bestwebsoft' );
				} else {
					$multisite = __( 'No', 'bestwebsoft' );
				}
			} else
				$multisite = __( 'N/A', 'bestwebsoft' );

			$site_url = get_option( 'siteurl' );
			$home_url = get_option( 'home' );
			$db_version = get_option( 'db_version' );
			$system_info = array(
				'system_info'		=> '',
				'active_plugins'	=> '',
				'inactive_plugins'	=> ''
			);
			$system_info['system_info'] = array(
		        __( 'Operating System', 'bestwebsoft' )				=> PHP_OS,
		        __( 'Server', 'bestwebsoft' )						=> $_SERVER["SERVER_SOFTWARE"],
		        __( 'Memory usage', 'bestwebsoft' )					=> $memory_usage,
		        __( 'MYSQL Version', 'bestwebsoft' )				=> $sql_version,
		        __( 'SQL Mode', 'bestwebsoft' )						=> $sql_mode,
		        __( 'PHP Version', 'bestwebsoft' )					=> PHP_VERSION,
		        __( 'PHP Safe Mode', 'bestwebsoft' )				=> $safe_mode,
		        __( 'PHP Allow URL fopen', 'bestwebsoft' )			=> $allow_url_fopen,
		        __( 'PHP Memory Limit', 'bestwebsoft' )				=> $memory_limit,
		        __( 'PHP Max Upload Size', 'bestwebsoft' )			=> $upload_max_filesize,
		        __( 'PHP Max Post Size', 'bestwebsoft' )			=> $post_max_size,
		        __( 'PHP Max Script Execute Time', 'bestwebsoft' )	=> $max_execution_time,
		        __( 'PHP Exif support', 'bestwebsoft' )				=> $exif_read_data,
		        __( 'PHP IPTC support', 'bestwebsoft' )				=> $iptcparse,
		        __( 'PHP XML support', 'bestwebsoft' )				=> $xml_parser_create,
				__( 'Site URL', 'bestwebsoft' )						=> $site_url,
				__( 'Home URL', 'bestwebsoft' )						=> $home_url,
				'$_SERVER[HTTP_HOST]'								=> $_SERVER['HTTP_HOST'],
				'$_SERVER[SERVER_NAME]'								=> $_SERVER['SERVER_NAME'],
				__( 'WordPress Version', 'bestwebsoft' )			=> $wp_version,
				__( 'WordPress DB Version', 'bestwebsoft' )			=> $db_version,
				__( 'Multisite', 'bestwebsoft' )					=> $multisite,
				__( 'Active Theme', 'bestwebsoft' )					=> $theme['Name'] . ' ' . $theme['Version']
			);
			foreach ( $all_plugins as $path => $plugin ) {
				if ( is_plugin_active( $path ) ) {
					$system_info['active_plugins'][ $plugin['Name'] ] = $plugin['Version'];
				} else {
					$system_info['inactive_plugins'][ $plugin['Name'] ] = $plugin['Version'];
				}
			} 
		}

		if ( ( isset( $_REQUEST['bwsmn_form_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'bwsmn_nonce_submit' ) ) ||
			 ( isset( $_REQUEST['bwsmn_form_submit_custom_email'] ) && check_admin_referer( plugin_basename(__FILE__), 'bwsmn_nonce_submit_custom_email' ) ) ) {
			if ( isset( $_REQUEST['bwsmn_form_email'] ) ) {
				$bwsmn_form_email = trim( $_REQUEST['bwsmn_form_email'] );
				if ( $bwsmn_form_email == "" || !preg_match( "/^((?:[a-z0-9']+(?:[a-z0-9\-_\.']+)?@[a-z0-9]+(?:[a-z0-9\-\.]+)?\.[a-z]{2,5})[, ]*)+$/i", $bwsmn_form_email ) ) {
					$error = __( "Please enter a valid email address.", 'bestwebsoft' );
				} else {
					$email = $bwsmn_form_email;
					$bwsmn_form_email = '';
					$message = __( 'Email with system info is sent to ', 'bestwebsoft' ) . $email;			
				}
			} else {
				$email = 'plugin_system_status@bestwebsoft.com';
				$message = __( 'Thank you for contacting us.', 'bestwebsoft' );
			}

			if ( $error == '' ) {
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
				$headers .= 'From: ' . get_option( 'admin_email' );
				$message_text = '<html><head><title>System Info From ' . $home_url . '</title></head><body>
				<h4>Environment</h4>
				<table>';
				foreach ( $system_info['system_info'] as $key => $value ) {
					$message_text .= '<tr><td>'. $key .'</td><td>'. $value .'</td></tr>';	
				}
				$message_text .= '</table>
				<h4>Active Plugins</h4>
				<table>';
				foreach ( $system_info['active_plugins'] as $key => $value ) {	
					$message_text .= '<tr><td scope="row">'. $key .'</td><td scope="row">'. $value .'</td></tr>';	
				}
				$message_text .= '</table>
				<h4>Inactive Plugins</h4>
				<table>';
				foreach ( $system_info['inactive_plugins'] as $key => $value ) {
					$message_text .= '<tr><td scope="row">'. $key .'</td><td scope="row">'. $value .'</td></tr>';
				}
				$message_text .= '</table></body></html>';
				$result = wp_mail( $email, 'System Info From ' . $home_url, $message_text, $headers );
				if ( $result != true )
					$error = __( "Sorry, email message could not be delivered.", 'bestwebsoft' );
			}
		} ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2>BestWebSoft</h2>			
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=bws_plugins"><?php _e( 'Plugins', 'bestwebsoft' ); ?></a>
				<?php if ( $wp_version >= '3.4' ) { ?>
					<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'themes' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=bws_plugins&amp;action=themes"><?php _e( 'Themes', 'bestwebsoft' ); ?></a>
				<?php } ?>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'system_status' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=bws_plugins&amp;action=system_status"><?php _e( 'System status', 'bestwebsoft' ); ?></a>
			</h2>			
			<?php if ( !isset( $_GET['action'] ) ) { ?>				
				<ul class="subsubsub">
					<li><a <?php if ( !isset( $_GET['sub'] ) ) echo 'class="current" '; ?>href="admin.php?page=bws_plugins"><?php _e( 'All', 'bestwebsoft' ); ?></a></li> |
					<li><a <?php if ( isset( $_GET['sub'] ) && 'installed' == $_GET['sub'] ) echo 'class="current" '; ?>href="admin.php?page=bws_plugins&amp;sub=installed"><?php _e( 'Installed', 'bestwebsoft' ); ?></a></li> |
					<li><a <?php if ( isset( $_GET['sub'] ) && 'recommended' == $_GET['sub'] ) echo 'class="current" '; ?>href="admin.php?page=bws_plugins&amp;sub=recommended"><?php _e( 'Recommended', 'bestwebsoft' ); ?></a></li>
				</ul>
				<div class="clear"></div>
				<?php if ( ( isset( $_GET['sub'] ) && 'installed' == $_GET['sub'] ) || !isset( $_GET['sub'] ) ) { ?>	
					<h4 class="bws_installed"><?php _e( 'Installed plugins', 'bestwebsoft' ); ?></h4>
					<?php foreach ( $all_plugins as $key_plugin => $value_plugin ) {
						if ( isset( $bws_plugins_pro[ $key_plugin ] ) ) {
							$key_plugin_explode = explode( '-plugin-pro/', $key_plugin );
							if ( isset( $key_plugin_explode[1] ) )
								$icon = $key_plugin_explode[0];
							else {
								$key_plugin_explode = explode( '-pro/', $key_plugin );
								$icon = $key_plugin_explode[0];
							}
						} elseif ( isset( $bws_plugins[ $key_plugin ] ) ) {
							$key_plugin_explode = explode( '-plugin/', $key_plugin );
							if ( isset( $key_plugin_explode[1] ) )
								$icon = $key_plugin_explode[0];
							else {
								$key_plugin_explode = explode( '/', $key_plugin );
								$icon = $key_plugin_explode[0];
							}
						}

						if ( in_array( $key_plugin, $active_plugins ) || is_plugin_active_for_network( $key_plugin ) ) { ?>
							<?php if ( isset( $bws_plugins_pro[ $key_plugin ] ) ) { ?>									
								<div class="bws_product_box bws_exist_overlay">
									<div class="bws_product">				
										<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
										<div class="bws_product_content">
											<div class="bws_product_icon">
												<div class="bws_product_icon_pro">PRO</div>
												<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>												
											</div>			
											<div class="bws_product_description"><?php echo $value_plugin["Description"]; ?></div>
										</div>
										<div class="clear"></div>
									</div>
									<div class="bws_product_links">								
										<a href="<?php echo $bws_plugins_pro[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
										<span> | </span>
										<a href="<?php echo $bws_plugins_pro[ $key_plugin ]["settings"]; ?>" target="_blank"><?php _e( "Settings", 'bestwebsoft' ); ?></a>
									</div>
								</div>
							<?php } elseif ( isset( $bws_plugins[ $key_plugin ] ) ) { ?>
								<div class="bws_product_box bws_product_free">
									<div class="bws_product">				
										<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
										<div class="bws_product_content">
											<div class="bws_product_icon">
												<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
											</div>
											<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
										</div>
										<?php if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) { ?>
											<a class="bws_product_button" href="<?php echo $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ]["purchase"]; ?>" target="_blank">
												<?php _e( 'Go', 'bestwebsoft' );?> <strong>PRO</strong>
											</a>
										<?php } else { ?>
											<a class="bws_product_button bws_donate_button" href="<?php echo $bws_donate_link; ?>" target="_blank">
												<strong><?php _e( 'DONATE', 'bestwebsoft' );?></strong>
											</a>
										<?php } ?>
										<div class="clear"></div>
									</div>									
									<div class="bws_product_links">
										<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
										<span> | </span>
										<a href="<?php echo $bws_plugins[ $key_plugin ]["settings"]; ?>" target="_blank"><?php _e( "Settings", 'bestwebsoft' ); ?></a>
									</div>
								</div>
							<?php }
						} else {
							if ( isset( $bws_plugins_pro[ $key_plugin ] ) ) { ?>
								<div class="bws_product_box bws_product_deactivated">
									<div class="bws_product">					
										<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
										<div class="bws_product_content">
											<div class="bws_product_icon">
												<div class="bws_product_icon_pro">PRO</div>
												<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
											</div>
											<div class="bws_product_description"><?php echo $bws_plugins_pro[ $key_plugin ]["description"]; ?></div>
										</div>
										<div class="clear"></div>
									</div>
									<div class="bws_product_links">
										<a href="<?php echo $bws_plugins_pro[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
										<span> | </span>
										<a class="bws_activate" href="plugins.php" title="<?php _e( "Activate this plugin", 'bestwebsoft' ); ?>" target="_blank"><?php _e( "Activate", 'bestwebsoft' ); ?></a>
									</div>
								</div>
							<?php } elseif ( isset( $bws_plugins[ $key_plugin ] ) ) { ?>
								<div class="bws_product_box bws_product_deactivated bws_product_free">
									<div class="bws_product">					
										<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
										<div class="bws_product_content">	
											<div class="bws_product_icon">
												<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
											</div>
											<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
										</div>
										<?php if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) { ?>
											<a class="bws_product_button" href="<?php echo $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ]["purchase"]; ?>" target="_blank">
												<?php _e( 'Go', 'bestwebsoft' );?> <strong>PRO</strong>
											</a>
										<?php } else { ?>
											<a class="bws_product_button bws_donate_button" href="<?php echo $bws_donate_link; ?>" target="_blank">
												<strong><?php _e( 'DONATE', 'bestwebsoft' );?></strong>
											</a>
										<?php } ?>
										<div class="clear"></div>
									</div>
									<div class="bws_product_links">
										<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
										<span> | </span>
										<a class="bws_activate" href="plugins.php" title="<?php _e( "Activate this plugin", 'bestwebsoft' ); ?>" target="_blank"><?php _e( "Activate", 'bestwebsoft' ); ?></a>
									</div>
								</div>
							<?php }
						}
					}
				} ?>
				<div class="clear"></div>
				<?php if ( ( isset( $_GET['sub'] ) && 'recommended' == $_GET['sub'] ) || !isset( $_GET['sub'] ) ) { ?>
					<h4 class="bws_recommended"><?php _e( 'Recommended plugins', 'bestwebsoft' ); ?></h4>
					<?php foreach ( $recommend_plugins as $key_plugin => $value_plugin ) {

						if ( isset( $bws_plugins_pro[ $key_plugin ] ) ) {
							$key_plugin_explode = explode( '-plugin-pro/', $key_plugin );
							if ( isset( $key_plugin_explode[1] ) )
								$icon = $key_plugin_explode[0];
							else {
								$key_plugin_explode = explode( '-pro/', $key_plugin );
								$icon = $key_plugin_explode[0];
							}
						} elseif ( isset( $bws_plugins[ $key_plugin ] ) ) {
							$key_plugin_explode = explode( '-plugin/', $key_plugin );
							if ( isset( $key_plugin_explode[1] ) )
								$icon = $key_plugin_explode[0];
							else {
								$key_plugin_explode = explode( '/', $key_plugin );
								$icon = $key_plugin_explode[0];
							}
						} ?>
						<div class="bws_product_box">
							<div class="bws_product">				
								<div class="bws_product_title"><?php echo $value_plugin["name"]; ?></div>
								<div class="bws_product_content">
									<div class="bws_product_icon">
										<?php if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) { ?>								
											<div class="bws_product_icon_pro">PRO</div>
										<?php } ?>
										<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
									</div>
									<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
								</div>
								<?php if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) { ?>								
									<a class="bws_product_button" href="<?php echo $bws_plugins_pro[ $bws_plugins[ $key_plugin ]['pro_version'] ]["link"]; ?>" target="_blank">
										<?php echo _e( 'Go', 'bestwebsoft' );?> <strong>PRO</strong>
									</a> 
								<?php } else { ?>
									<a class="bws_product_button bws_donate_button" href="<?php echo $bws_donate_link; ?>" target="_blank">
										<strong><?php echo _e( 'DONATE', 'bestwebsoft' );?></strong>
									</a>
								<?php } ?>
							</div>
							<div class="clear"></div>
							<div class="bws_product_links">								
								<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php echo __( "Learn more", 'bestwebsoft' ); ?></a>
								<span> | </span>
								<a href="<?php echo $bws_plugins[ $key_plugin ]["wp_install"]; ?>" target="_blank"><?php echo __( "Install now", 'bestwebsoft' ); ?></a>
							</div>
						</div>
					<?php }						
				} ?>	
			<?php } elseif ( 'themes' == $_GET['action'] ) { ?>	
				<div id="availablethemes">
					<?php global $tabs, $tab, $paged, $type, $theme_field_defaults;
					include( ABSPATH . 'wp-admin/includes/theme-install.php' );
					include( ABSPATH . 'wp-admin/includes/class-wp-themes-list-table.php' );
					include( ABSPATH . 'wp-admin/includes/class-wp-theme-install-list-table.php' );

					$theme_class = new WP_Theme_Install_List_Table();
					$paged = $theme_class->get_pagenum();
					$per_page = 36;
					$args = array( 'page' => $paged, 'per_page' => $per_page, 'fields' => $theme_field_defaults );
					$args['author'] = 'bestwebsoft';
					$args = apply_filters( 'install_themes_table_api_args_search', $args );
					$api = themes_api( 'query_themes', $args );

					if ( is_wp_error( $api ) )
						wp_die( $api->get_error_message() . '</p> <p><a href="#" onclick="document.location.reload(); return false;">' . __( 'Try again' ) . '</a>' );

					$theme_class->items = $api->themes;
					$theme_class->set_pagination_args( array(
						'total_items' => $api->info['results'],
						'per_page' => $per_page,
						'infinite_scroll' => true,
					) );
					$themes = $theme_class->items;
					if ( $wp_version < '3.9' ) {
						foreach ( $themes as $theme ) { ?>
							<div class="available-theme installable-theme"><?php
								global $themes_allowedtags;
								if ( empty( $theme ) )
									return;

								$name   = wp_kses( $theme->name,   $themes_allowedtags );
								$author = wp_kses( $theme->author, $themes_allowedtags );
								$preview_title = sprintf( __('Preview &#8220;%s&#8221;'), $name );
								$preview_url   = add_query_arg( array(
									'tab'   => 'theme-information',
									'theme' => $theme->slug,
								), self_admin_url( 'theme-install.php' ) );

								$actions = array();

								$install_url = add_query_arg( array(
									'action' => 'install-theme',
									'theme'  => $theme->slug,
								), self_admin_url( 'update.php' ) );

								$update_url = add_query_arg( array(
									'action' => 'upgrade-theme',
									'theme'  => $theme->slug,
								), self_admin_url( 'update.php' ) );

								$status = 'install';
								$installed_theme = wp_get_theme( $theme->slug );
								if ( $installed_theme->exists() ) {
									if ( version_compare( $installed_theme->get('Version'), $theme->version, '=' ) )
										$status = 'latest_installed';
									elseif ( version_compare( $installed_theme->get('Version'), $theme->version, '>' ) )
										$status = 'newer_installed';
									else
										$status = 'update_available';
								}
								switch ( $status ) {
									default:
									case 'install':
										$actions[] = '<a class="install-now" href="' . esc_url( wp_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ) . '" title="' . esc_attr( sprintf( __( 'Install %s' ), $name ) ) . '">' . __( 'Install Now' ) . '</a>';
										break;
									case 'update_available':
										$actions[] = '<a class="install-now" href="' . esc_url( wp_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) ) . '" title="' . esc_attr( sprintf( __( 'Update to version %s' ), $theme->version ) ) . '">' . __( 'Update' ) . '</a>';
										break;
									case 'newer_installed':
									case 'latest_installed':
										$actions[] = '<span class="install-now" title="' . esc_attr__( 'This theme is already installed and is up to date' ) . '">' . _x( 'Installed', 'theme' ) . '</span>';
										break;
								}
								$actions[] = '<a class="install-theme-preview" href="' . esc_url( $preview_url ) . '" title="' . esc_attr( sprintf( __( 'Preview %s' ), $name ) ) . '">' . __( 'Preview' ) . '</a>';
								$actions = apply_filters( 'theme_install_actions', $actions, $theme ); ?>
								<a class="screenshot install-theme-preview" href="<?php echo esc_url( $preview_url ); ?>" title="<?php echo esc_attr( $preview_title ); ?>">
									<img src='<?php echo esc_url( $theme->screenshot_url ); ?>' width='150' />
								</a>
								<h3><?php echo $name; ?></h3>
								<div class="theme-author"><?php printf( __( 'By %s' ), $author ); ?></div>
								<div class="action-links">
									<ul>
										<?php foreach ( $actions as $action ): ?>
											<li><?php echo $action; ?></li>
										<?php endforeach; ?>
										<li class="hide-if-no-js"><a href="#" class="theme-detail"><?php _e('Details') ?></a></li>
									</ul>
								</div>
								<?php $theme_class->install_theme_info( $theme ); ?>
							</div>
						<?php }
						// end foreach $theme_names
						$theme_class->theme_installer();
					} else { ?>
						<div class="theme-browser">
							<div class="themes">
						<?php foreach ( $themes as $key => $theme ) {
							$installed_theme = wp_get_theme( $theme->slug );
							if ( $installed_theme->exists() )
								$theme->installed = true;
							else
								$theme->installed = false;
							?>
							<div class="theme" tabindex="0">
								<?php if ( $theme->screenshot_url ) { ?>
									<div class="theme-screenshot">
										<img src="<?php echo $theme->screenshot_url; ?>" alt="" />
									</div>
								<?php } else { ?>
									<div class="theme-screenshot blank"></div>
								<?php } ?>
								<div class="theme-author"><?php printf( __( 'By %s' ), $theme->author ); ?></div>
								<h3 class="theme-name"><?php echo $theme->name; ?></h3>
								<div class="theme-actions">
									<a class="button button-secondary preview install-theme-preview" href="theme-install.php?theme=<?php echo $theme->slug ?>"><?php esc_html_e( 'Learn More' ); ?></a>
								</div>
								<?php if ( $theme->installed ) { ?>
									<div class="theme-installed"><?php _e( 'Already Installed' ); ?></div>
								<?php } ?>
							</div>
						<?php } ?>
							<br class="clear" />
							</div>
						</div>
						<div class="theme-overlay"></div>
					<?php } ?>
				</div>
			<?php } elseif ( 'system_status' == $_GET['action'] ) {	?>
				<div class="updated fade" <?php if ( ! ( isset( $_REQUEST['bwsmn_form_submit'] ) || isset( $_REQUEST['bwsmn_form_submit_custom_email'] ) ) || $error != "" ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
				<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
				<h3><?php _e( 'System status', 'bestwebsoft' ); ?></h3>
				<div class="inside">
					<table class="bws_system_info">
						<thead><tr><th><?php _e( 'Environment', 'bestwebsoft' ); ?></th><td></td></tr></thead>
						<tbody>
						<?php foreach ( $system_info['system_info'] as $key => $value ) { ?>	
							<tr>
								<td scope="row"><?php echo $key; ?></td>
								<td scope="row"><?php echo $value; ?></td>
							</tr>	
						<?php } ?>
						</tbody>
					</table>
					<table class="bws_system_info">
						<thead><tr><th><?php _e( 'Active Plugins', 'bestwebsoft' ); ?></th><th></th></tr></thead>
						<tbody>
						<?php foreach ( $system_info['active_plugins'] as $key => $value ) { ?>	
							<tr>
								<td scope="row"><?php echo $key; ?></td>
								<td scope="row"><?php echo $value; ?></td>
							</tr>	
						<?php } ?>
						</tbody>
					</table>
					<table class="bws_system_info">
						<thead><tr><th><?php _e( 'Inactive Plugins', 'bestwebsoft' ); ?></th><th></th></tr></thead>
						<tbody>
						<?php if ( ! empty( $system_info['inactive_plugins'] ) ) {
							foreach ( $system_info['inactive_plugins'] as $key => $value ) { ?>	
								<tr>
									<td scope="row"><?php echo $key; ?></td>
									<td scope="row"><?php echo $value; ?></td>
								</tr>	
							<?php } 
						} ?>
						</tbody>
					</table>
					<div class="clear"></div>						
					<form method="post" action="admin.php?page=bws_plugins&amp;action=system_status">
						<p>			
							<input type="hidden" name="bwsmn_form_submit" value="submit" />
							<input type="submit" class="button-primary" value="<?php _e( 'Send to support', 'bestwebsoft' ) ?>" />
							<?php wp_nonce_field( plugin_basename(__FILE__), 'bwsmn_nonce_submit' ); ?>		
						</p>		
					</form>				
					<form method="post" action="admin.php?page=bws_plugins&amp;action=system_status">	
						<p>			
							<input type="hidden" name="bwsmn_form_submit_custom_email" value="submit" />						
							<input type="submit" class="button" value="<?php _e( 'Send to custom email &#187;', 'bestwebsoft' ) ?>" />
							<input type="text" value="<?php echo $bwsmn_form_email; ?>" name="bwsmn_form_email" />
							<?php wp_nonce_field( plugin_basename(__FILE__), 'bwsmn_nonce_submit_custom_email' ); ?>
						</p>				
					</form>						
				</div>
			<?php } ?>
		</div>
	<?php }
}

if ( ! function_exists ( 'bws_plugin_init' ) ) {
	function bws_plugin_init() {
		// Internationalization, first(!)
		load_plugin_textdomain( 'bestwebsoft', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
	}
}

if ( ! function_exists ( 'bws_admin_enqueue_scripts' ) ) {
	function bws_admin_enqueue_scripts() {
		global $wp_version;
		if ( $wp_version < 3.8 )
			wp_enqueue_style( 'bws-admin-stylesheet', plugins_url( 'css/general_style_wp_before_3.8.css', __FILE__ ) );	
		else
			wp_enqueue_style( 'bws-admin-stylesheet', plugins_url( 'css/general_style.css', __FILE__ ) );

		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_plugins" ) {
			wp_enqueue_style( 'bws_menu_style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'bws_menu_script', plugins_url( 'js/bws_menu.js' , __FILE__ ) );			
			if ( $wp_version >= '3.8' )
				wp_enqueue_script( 'theme-install' );
			elseif ( $wp_version >= '3.4' )
				wp_enqueue_script( 'theme' );
		}
	}
}

if ( ! function_exists ( 'bws_admin_head' ) ) {
	function bws_admin_head() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_plugins" ) { ?>
			<noscript>
				<style type="text/css">
					.bws_product_button {
						display: inline-block;
					}
				</style>
			</noscript>
		<?php }
	}
}

add_action( 'admin_init', 'bws_plugin_init' );
add_action( 'admin_enqueue_scripts', 'bws_admin_enqueue_scripts' );
add_action( 'admin_head', 'bws_admin_head' );
?>