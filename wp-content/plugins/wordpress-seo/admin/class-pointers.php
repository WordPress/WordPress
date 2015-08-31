<?php
/**
 * @package WPSEO\Admin
 */

/**
 * This class handles the pointers used in the introduction tour.
 *
 * @todo Add an introductory pointer on the edit post page too.
 */
class WPSEO_Pointers {

	/**
	 * @var object Instance of this class
	 */
	public static $instance;

	/**
	 * @var array Holds the buttons to be put out
	 */
	private $button_array;

	/**
	 * @var array Holds the admin pages we have pointers for and the callback that generates the pointers content
	 */
	private $admin_pages = array(
		'dashboard' => 'dashboard_pointer',
		'titles'    => 'titles_pointer',
		'social'    => 'social_pointer',
		'xml'       => 'xml_sitemaps_pointer',
		'advanced'  => 'advanced_pointer',
		'licenses'  => 'licenses_pointer',
	);

	/**
	 * Class constructor.
	 */
	private function __construct() {
		if ( current_user_can( 'manage_options' ) ) {

			if ( ! get_user_meta( get_current_user_id(), 'wpseo_ignore_tour' ) ) {
				wp_enqueue_style( 'wp-pointer' );
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'wp-pointer' );
				add_action( 'admin_print_footer_scripts', array( $this, 'intro_tour' ) );
			}
		}
	}

	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load the introduction tour
	 */
	public function intro_tour() {
		global $pagenow;

		$page = preg_replace( '/^(wpseo_)/', '', filter_input( INPUT_GET, 'page' ) );

		if ( 'admin.php' === $pagenow && array_key_exists( $page, $this->admin_pages ) ) {
			$this->do_page_pointer( $page );
		}
		else {
			$this->start_tour_pointer();
		}
	}

	/**
	 * Prints the pointer script
	 *
	 * @param string $selector The CSS selector the pointer is attached to.
	 * @param array  $options  The options for the pointer.
	 */
	public function print_scripts( $selector, $options ) {
		// Button1 is the close button, which always exists.
		$button_array_defaults = array(
			'button2' => array(
				'text'     => false,
				'function' => '',
			),
			'button3' => array(
				'text'     => false,
				'function' => '',
			),
		);
		$this->button_array          = wp_parse_args( $this->button_array, $button_array_defaults );

		if ( function_exists( 'wp_json_encode' ) ) {
			$json_options = wp_json_encode( $options );
		}
		else {
			// @codingStandardsIgnoreStart
			$json_options = json_encode( $options );
			// @codingStandardsIgnoreEnd
		}

		?>
		<script type="text/javascript">
			//<![CDATA[
			(function ($) {
				// Don't show the tour on screens with an effective width smaller than 1024px or an effective height smaller than 768px.
				if (jQuery(window).width() < 1024 || jQuery(window).availWidth < 1024) {
					return;
				}

				var wpseo_pointer_options = <?php echo $json_options; ?>, setup;

				wpseo_pointer_options = $.extend(wpseo_pointer_options, {
					buttons: function (event, t) {
						var button = jQuery('<a href="<?php echo $this->get_ignore_url(); ?>" id="pointer-close" style="margin:0 5px;" class="button-secondary">' + '<?php _e( 'Close', 'wordpress-seo' ) ?>' + '</a>');
						button.bind('click.pointer', function () {
							t.element.pointer('close');
						});
						return button;
					},
					close: function () {
					}
				});

				setup = function () {
					$('<?php echo $selector; ?>').pointer(wpseo_pointer_options).pointer('open');
					<?php
					$this->button2();
					$this->button3();
					?>
				};

				if (wpseo_pointer_options.position && wpseo_pointer_options.position.defer_loading)
					$(window).bind('load.wp-pointers', setup);
				else
					$(document).ready(setup);
			})(jQuery);
			//]]>
		</script>
	<?php
	}

	/**
	 * Render button 2, if needed
	 */
	private function button2() {
		if ( $this->button_array['button2']['text'] ) {
			?>
			jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary">' +
				'<?php echo $this->button_array['button2']['text']; ?>' + '</a>');
			jQuery('#pointer-primary').click(function () {
			<?php echo $this->button_array['button2']['function']; ?>
			});
		<?php
		}
	}

	/**
	 * Render button 3, if needed. This is the previous button in most cases
	 */
	private function button3() {
		if ( $this->button_array['button3']['text'] ) {
			?>
			jQuery('#pointer-primary').after('<a id="pointer-ternary" style="float: left;" class="button-secondary">' +
				'<?php echo $this->button_array['button3']['text']; ?>' + '</a>');
			jQuery('#pointer-ternary').click(function () {
			<?php echo $this->button_array['button3']['function']; ?>
			});
		<?php }
	}

	/**
	 * Show a pointer that starts the tour for Yoast SEO
	 */
	private function start_tour_pointer() {
		$selector = 'li.toplevel_page_wpseo_dashboard';
		$content  = '<h3>' . __( 'Congratulations!', 'wordpress-seo' ) . '</h3>'
					/* translators: %1$s expands to Yoast SEO */
		            .'<p>' . sprintf( __( 'You&#8217;ve just installed %1$s! Click &#8220;Start Tour&#8221; to view a quick introduction of this plugin&#8217;s core functionality.', 'wordpress-seo' ), 'Yoast SEO' ) . '</p>';
		$opt_arr  = array(
			'content'  => $content,
			'position' => array( 'edge' => 'bottom', 'align' => 'center' ),
		);

		$this->button_array['button2']['text']     = __( 'Start Tour', 'wordpress-seo' );
		$this->button_array['button2']['function'] = sprintf( 'document.location="%s";', admin_url( 'admin.php?page=wpseo_dashboard' ) );

		$this->print_scripts( $selector, $opt_arr );
	}

	/**
	 * Shows a pointer on the proper pages
	 *
	 * @param string $page
	 */
	private function do_page_pointer( $page ) {
		$selector = '#wpseo-title';

		$pointer = call_user_func( array( $this, $this->admin_pages[ $page ] ) );

		$opt_arr = array(
			'content'      => $pointer['content'],
			'position'     => array(
				'edge'  => 'top',
				'align' => ( is_rtl() ) ? 'left' : 'right',
			),
			'pointerWidth' => 450,
		);
		if ( isset( $pointer['next_page'] ) ) {
			$this->button_array['button2'] = array(
				'text'     => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="' . admin_url( 'admin.php?page=wpseo_' . $pointer['next_page'] ) . '";',
			);
		}
		if ( isset( $pointer['prev_page'] ) ) {
			$this->button_array['button3'] = array(
				'text'     => __( 'Previous', 'wordpress-seo' ),
				'function' => 'window.location="' . admin_url( 'admin.php?page=wpseo_' . $pointer['prev_page'] ) . '";',
			);
		}
		$this->print_scripts( $selector, $opt_arr );
	}

	/**
	 * Returns the content of the General Settings page pointer
	 *
	 * @return array
	 */
	private function dashboard_pointer() {
		global $current_user;

		return array(
			'content'   => '<h3>' . __( 'General settings', 'wordpress-seo' ) . '</h3>'
				/* translators: %1$s expands to Yoast SEO */
			               . '<p>' . sprintf( __( 'These are the General settings for %1$s, here you can restart this tour or revert the %1$s settings to default.', 'wordpress-seo' ), 'Yoast SEO' ) . '</p>'
			               . '<p><strong>' . __( 'Tab: Your Info / Company Info', 'wordpress-seo' ) . '</strong><br/>' . __( 'Add some info here needed for Google\'s Knowledge Graph.', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Tab: Webmaster Tools', 'wordpress-seo' ) . '</strong><br/>' . __( 'You can add the verification codes for the different Webmaster Tools programs here. We highly encourage you to check out both Google and Bing&#8217;s Webmaster Tools.', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Tab: Security', 'wordpress-seo' ) . '</strong><br/>' . __( 'Determine who has access to the plugins advanced settings on the post edit screen.', 'wordpress-seo' ) . '</p>'

				/* translators: %1$s expands to Yoast SEO */
			               . '<p><strong>' . sprintf( __( 'More %1$s', 'wordpress-seo' ), 'Yoast SEO' ) . '</strong><br/>'

				/* @todo What about this translation */
	   					   . sprintf( __( 'There&#8217;s more to learn about WordPress &amp; SEO than just using this plugin. A great start is our article %1$sthe definitive guide to WordPress SEO%2$s.', 'wordpress-seo' ), '<a target="_blank" href="' . esc_url( 'https://yoast.com/articles/wordpress-seo/#utm_source=wpseo_dashboard&utm_medium=wpseo_tour&utm_campaign=tour' ) . '">', '</a>' )
						   . '</p>'
			               . '<p><strong style="font-size:150%;">' . __( 'Subscribe to our Newsletter', 'wordpress-seo' ) . '</strong><br/>'
				/* translators: %1$s expands to Yoast SEO */
			               . sprintf( __( 'If you would like us to keep you up-to-date regarding %1$s and other plugins by Yoast, subscribe to our newsletter:', 'wordpress-seo' ), 'Yoast SEO' ) . '</p>'
			               . '<form target="_blank" action="http://yoast.us1.list-manage1.com/subscribe/post?u=ffa93edfe21752c921f860358&amp;id=972f1c9122" method="post" selector="newsletter-form" accept-charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">'
			               . '<p>'
			               . '<input style="margin: 5px; color:#666" name="EMAIL" value="' . esc_attr( $current_user->user_email ) . '" selector="newsletter-email" placeholder="' . __( 'Email', 'wordpress-seo' ) . '"/>'
			               . '<input type="hidden" name="group" value="2"/>'
			               . '<button type="submit" class="button-primary">' . __( 'Subscribe', 'wordpress-seo' ) . '</button>'
			               . '</p>'
			               . '</form>',
			'next_page' => 'titles',
		);
	}

	/**
	 * Returns the content of the titles page pointer
	 *
	 * @return array
	 */
	private function titles_pointer() {
		return array(
			'content'   => '<h3>' . __( 'Title &amp; Metas settings', 'wordpress-seo' ) . '</h3>'
			               . '<p>' . __( 'This is where you	set the titles and meta-information for all your post types, taxonomies, archives, special pages and for your homepage. The page is divided into different tabs. Make sure you check &#8217;em all out!', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Sitewide settings', 'wordpress-seo' ) . '</strong><br/>' . __( 'The first tab will show you site-wide settings for titles, normally you\'ll only need to change the Title Separator.', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Templates and settings', 'wordpress-seo' ) . '</strong><br/>' . sprintf( __( 'Now click on the &#8216;%1$sPost Types%2$s&#8217;-tab, as this will be our example.', 'wordpress-seo' ), '<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=wpseo_titles#top#post_types' ) ) . '">', 				       '</a>' ) . '<br/>' . __( 'The templates are built using variables. You can find all these variables in the help tab (in the top-right corner of the page). The settings allow you to set specific behavior for the post types.', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Archives', 'wordpress-seo' ) . '</strong><br/>' . __( 'On the archives tab you can set templates for specific pages like author archives, search results and more.', 'wordpress-seo' )
			               . '<p><strong>' . __( 'Other', 'wordpress-seo' ) . '</strong><br/>' . __( 'On the Other tab you can change sitewide meta settings, like enable meta keywords.', 'wordpress-seo' ),
			'next_page' => 'social',
			'prev_page' => 'dashboard',
		);
	}

	/**
	 * Returns the content of the social page pointer
	 *
	 * @return array
	 */
	private function social_pointer() {
		return array(
			'content'   => '<h3>' . __( 'Social settings', 'wordpress-seo' ) . '</h3>'
			               . '<p><strong>' . __( 'Facebook', 'wordpress-seo' ) . '</strong><br/>' . sprintf( __( 'On this tab you can enable the %1$sFacebook Open Graph%2$s functionality from this plugin, as well as assign a Facebook user or Application to be the admin of your site, so you can view the Facebook insights.', 'wordpress-seo' ), '<a target="_blank" href="' . esc_url( 'https://yoast.com/facebook-open-graph-protocol/#utm_source=wpseo_social&utm_medium=wpseo_tour&utm_campaign=tour' ) . '">', '</a>' ) . '</p>'
			               . '<p>' . __( 'The frontpage settings allow you to set meta-data for your homepage, whereas the default settings allow you to set a fallback for all posts/pages without images. ', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Twitter', 'wordpress-seo' ) . '</strong><br/>' . sprintf( __( 'With %1$sTwitter Cards%2$s, you can attach rich photos, videos and media experience to tweets that drive traffic to your website. Simply check the box, sign up for the service, and users who Tweet links to your content will have a &#8220;Card&#8221; added to the tweet that&#8217;s visible to all of their followers.', 'wordpress-seo' ), '<a target="_blank" href="' . esc_url( 'https://yoast.com/twitter-cards/#utm_source=wpseo_social&utm_medium=wpseo_tour&utm_campaign=tour' ) . '">', '</a>' ) . '</p>'
			               . '<p><strong>' . __( 'Pinterest', 'wordpress-seo' ) . '</strong><br/>' . __( 'On this tab you can verify your site with Pinterest and enter your Pinterest account.', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'Google+', 'wordpress-seo' ) . '</strong><br/>' . sprintf( __( 'This tab allows you to add specific post meta data for Google+. And if you have a Google+ page for your business, add that URL here and link it on your %1$sGoogle+%2$s page&#8217;s about page.', 'wordpress-seo' ), '<a target="_blank" href="' . esc_url( 'https://plus.google.com/' ) . '">', '</a>' ) . '</p>'
			               . '<p><strong>' . __( 'Other', 'wordpress-seo' ) . '</strong><br/>' . __( 'On this tab you can enter some more of your social accounts, mostly used for Google\'s Knowledge Graph.', 'wordpress-seo' ) . '</p>',
			'next_page' => 'xml',
			'prev_page' => 'titles',
		);
	}

	/**
	 * Returns the content of the social page pointer
	 *
	 * @return array
	 */
	private function xml_sitemaps_pointer() {
		return array(
			'content'   => '<h3>' . __( 'XML Sitemaps', 'wordpress-seo' ) . '</h3>'
			               . '<p><strong>' . __( 'What are XML sitemaps?', 'wordpress-seo' ) . '</strong><br/>' . __( 'A Sitemap is an XML file that lists the URLs for a site. It allows webmasters to include additional information about each URL: when it was last updated, how often it changes, and how important it is in relation to other URLs in the site. This allows search engines to crawl the site more intelligently.', 'wordpress-seo' ) . '</p>'
			               . '<p><strong>' . __( 'What does the plugin do with XML Sitemaps?', 'wordpress-seo' ) . '</strong><br/>' . __( 'This plugin adds XML sitemaps to your site. The sitemaps are automatically updated when you publish a new post, page or custom post and Google and Bing will be automatically notified.', 'wordpress-seo' ) . '</p>'
			               . '<p>' . __( 'If you want to exclude certain post types and/or taxonomies, you can also set that on this page.', 'wordpress-seo' ) . '</p>'
			               . '<p>' . __( 'Is your webserver low on memory? Decrease the entries per sitemap (default: 1000) to reduce load.', 'wordpress-seo' ) . '</p>',
			'next_page' => 'advanced',
			'prev_page' => 'social',
		);
	}

	/**
	 * Returns the content of the advanced page pointer
	 *
	 * @return array
	 */
	private function advanced_pointer() {
		return array(
			'content'   => '<h3>' . __( 'Advanced Settings', 'wordpress-seo' ) . '</h3><p>' . __( 'All of the options on these tabs are for advanced users only, if you don&#8217;t know whether you should check any, don&#8217;t touch them.', 'wordpress-seo' ) . '</p>',
			'next_page' => 'licenses',
			'prev_page' => 'xml',
		);
	}

	/**
	 * Returns the content of the extensions and licenses page pointer
	 *
	 * @return array
	 */
	private function licenses_pointer() {
		return array(
			'content'   => '<h3>' . __( 'Extensions and Licenses', 'wordpress-seo' ) . '</h3>'
			               . '<p><strong>' . __( 'Extensions', 'wordpress-seo' ) . '</strong><br/>'
				/* translators: %1$s expands to Yoast SEO, %2$s to Yoast SEO Premium, %3$s and %4$s to an anchor with link about our premium plugins */
						   . sprintf( __( 'The powerful functions of %1$s can be extended with %3$sYoast premium plugins%4$s. These premium plugins require the installation of %1$s or %2$s and add specific functionality. You can read all about the Yoast Premium Plugins %3$shere%4$s.', 'wordpress-seo' ), 'Yoast SEO', 'Yoast SEO Premium', '<a target="_blank" href="' . esc_url( 'https://yoast.com/wordpress/plugins/#utm_source=wpseo_licenses&utm_medium=wpseo_tour&utm_campaign=tour' ) . '">', '</a>' )
						   . '</p>'
			               . '<p><strong>' . __( 'Licenses', 'wordpress-seo' ) . '</strong><br/>'
				/* translators: %1$s expands to Yoast SEO Premium */
				           . sprintf( __( 'Once you&#8217;ve purchased %1$s or any other premium Yoast plugin, you&#8217;ll have to enter a license key. You can do so on the Licenses-tab. Once you&#8217;ve activated your premium plugin, you can use all its powerful features.', 'wordpress-seo' ), 'Yoast SEO Premium' )
				           . '</p>'
			               . '<p><strong>' . __( 'Like this plugin?', 'wordpress-seo' ) . '</strong><br/>' . sprintf( __( 'So, we&#8217;ve come to the end of the tour. If you like the plugin, please %srate it 5 stars on WordPress.org%s!', 'wordpress-seo' ), '<a target="_blank" href="https://wordpress.org/plugins/wordpress-seo/">', '</a>' ) . '</p>'
			               . '<p>' . sprintf( __( 'Thank you for using our plugin and good luck with your SEO!<br/><br/>Best,<br/>Team Yoast - %1$sYoast.com%2$s', 'wordpress-seo' ), '<a target="_blank" href="' . esc_url( 'https://yoast.com/#utm_source=wpseo_licenses&utm_medium=wpseo_tour&utm_campaign=tour' ) . '">', '</a>' ) . '</p>',
			'prev_page' => 'advanced',
		);
	}

	/**
	 * Extending the current page URL with two params to be able to ignore the tour.
	 *
	 * @return mixed
	 */
	private function get_ignore_url() {
		$arr_params = array(
			'wpseo_restart_tour' => false,
			'wpseo_ignore_tour'  => '1',
			'nonce'              => wp_create_nonce( 'wpseo-ignore-tour' ),
		);

		return esc_url( add_query_arg( $arr_params ) );
	}
} /* End of class */
