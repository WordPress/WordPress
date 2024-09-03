<?php
/**
 * WP_Privacy_Policy_Content class.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.9.6
 */

#[AllowDynamicProperties]
final class WP_Privacy_Policy_Content {

	private static $policy_content = array();

	/**
	 * Constructor
	 *
	 * @since 4.9.6
	 */
	private function __construct() {}

	/**
	 * Adds content to the postbox shown when editing the privacy policy.
	 *
	 * Plugins and themes should suggest text for inclusion in the site's privacy policy.
	 * The suggested text should contain information about any functionality that affects user privacy,
	 * and will be shown in the Suggested Privacy Policy Content postbox.
	 *
	 * Intended for use from `wp_add_privacy_policy_content()`.
	 *
	 * @since 4.9.6
	 *
	 * @param string $plugin_name The name of the plugin or theme that is suggesting content for the site's privacy policy.
	 * @param string $policy_text The suggested content for inclusion in the policy.
	 */
	public static function add( $plugin_name, $policy_text ) {
		if ( empty( $plugin_name ) || empty( $policy_text ) ) {
			return;
		}

		$data = array(
			'plugin_name' => $plugin_name,
			'policy_text' => $policy_text,
		);

		if ( ! in_array( $data, self::$policy_content, true ) ) {
			self::$policy_content[] = $data;
		}
	}

	/**
	 * Performs a quick check to determine whether any privacy info has changed.
	 *
	 * @since 4.9.6
	 */
	public static function text_change_check() {

		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		// The site doesn't have a privacy policy.
		if ( empty( $policy_page_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $policy_page_id ) ) {
			return false;
		}

		$old = (array) get_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );

		// Updates are not relevant if the user has not reviewed any suggestions yet.
		if ( empty( $old ) ) {
			return false;
		}

		$cached = get_option( '_wp_suggested_policy_text_has_changed' );

		/*
		 * When this function is called before `admin_init`, `self::$policy_content`
		 * has not been populated yet, so use the cached result from the last
		 * execution instead.
		 */
		if ( ! did_action( 'admin_init' ) ) {
			return 'changed' === $cached;
		}

		$new = self::$policy_content;

		// Remove the extra values added to the meta.
		foreach ( $old as $key => $data ) {
			if ( ! is_array( $data ) || ! empty( $data['removed'] ) ) {
				unset( $old[ $key ] );
				continue;
			}

			$old[ $key ] = array(
				'plugin_name' => $data['plugin_name'],
				'policy_text' => $data['policy_text'],
			);
		}

		// Normalize the order of texts, to facilitate comparison.
		sort( $old );
		sort( $new );

		/*
		 * The == operator (equal, not identical) was used intentionally.
		 * See https://www.php.net/manual/en/language.operators.array.php
		 */
		if ( $new != $old ) {
			/*
			 * A plugin was activated or deactivated, or some policy text has changed.
			 * Show a notice on the relevant screens to inform the admin.
			 */
			add_action( 'admin_notices', array( 'WP_Privacy_Policy_Content', 'policy_text_changed_notice' ) );
			$state = 'changed';
		} else {
			$state = 'not-changed';
		}

		// Cache the result for use before `admin_init` (see above).
		if ( $cached !== $state ) {
			update_option( '_wp_suggested_policy_text_has_changed', $state, false );
		}

		return 'changed' === $state;
	}

	/**
	 * Outputs a warning when some privacy info has changed.
	 *
	 * @since 4.9.6
	 */
	public static function policy_text_changed_notice() {
		$screen = get_current_screen()->id;

		if ( 'privacy' !== $screen ) {
			return;
		}

		$privacy_message = sprintf(
			/* translators: %s: Privacy Policy Guide URL. */
			__( 'The suggested privacy policy text has changed. Please <a href="%s">review the guide</a> and update your privacy policy.' ),
			esc_url( admin_url( 'privacy-policy-guide.php?tab=policyguide' ) )
		);

		wp_admin_notice(
			$privacy_message,
			array(
				'type'               => 'warning',
				'additional_classes' => array( 'policy-text-updated' ),
				'dismissible'        => true,
			)
		);
	}

	/**
	 * Updates the cached policy info when the policy page is updated.
	 *
	 * @since 4.9.6
	 * @access private
	 *
	 * @param int $post_id The ID of the updated post.
	 */
	public static function _policy_page_updated( $post_id ) {
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		if ( ! $policy_page_id || $policy_page_id !== (int) $post_id ) {
			return;
		}

		// Remove updated|removed status.
		$old          = (array) get_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
		$done         = array();
		$update_cache = false;

		foreach ( $old as $old_key => $old_data ) {
			if ( ! empty( $old_data['removed'] ) ) {
				// Remove the old policy text.
				$update_cache = true;
				continue;
			}

			if ( ! empty( $old_data['updated'] ) ) {
				// 'updated' is now 'added'.
				$done[]       = array(
					'plugin_name' => $old_data['plugin_name'],
					'policy_text' => $old_data['policy_text'],
					'added'       => $old_data['updated'],
				);
				$update_cache = true;
			} else {
				$done[] = $old_data;
			}
		}

		if ( $update_cache ) {
			delete_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
			// Update the cache.
			foreach ( $done as $data ) {
				add_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content', $data );
			}
		}
	}

	/**
	 * Checks for updated, added or removed privacy policy information from plugins.
	 *
	 * Caches the current info in post_meta of the policy page.
	 *
	 * @since 4.9.6
	 *
	 * @return array The privacy policy text/information added by core and plugins.
	 */
	public static function get_suggested_policy_text() {
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		$checked        = array();
		$time           = time();
		$update_cache   = false;
		$new            = self::$policy_content;
		$old            = array();

		if ( $policy_page_id ) {
			$old = (array) get_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
		}

		// Check for no-changes and updates.
		foreach ( $new as $new_key => $new_data ) {
			foreach ( $old as $old_key => $old_data ) {
				$found = false;

				if ( $new_data['policy_text'] === $old_data['policy_text'] ) {
					// Use the new plugin name in case it was changed, translated, etc.
					if ( $old_data['plugin_name'] !== $new_data['plugin_name'] ) {
						$old_data['plugin_name'] = $new_data['plugin_name'];
						$update_cache            = true;
					}

					// A plugin was re-activated.
					if ( ! empty( $old_data['removed'] ) ) {
						unset( $old_data['removed'] );
						$old_data['added'] = $time;
						$update_cache      = true;
					}

					$checked[] = $old_data;
					$found     = true;
				} elseif ( $new_data['plugin_name'] === $old_data['plugin_name'] ) {
					// The info for the policy was updated.
					$checked[]    = array(
						'plugin_name' => $new_data['plugin_name'],
						'policy_text' => $new_data['policy_text'],
						'updated'     => $time,
					);
					$found        = true;
					$update_cache = true;
				}

				if ( $found ) {
					unset( $new[ $new_key ], $old[ $old_key ] );
					continue 2;
				}
			}
		}

		if ( ! empty( $new ) ) {
			// A plugin was activated.
			foreach ( $new as $new_data ) {
				if ( ! empty( $new_data['plugin_name'] ) && ! empty( $new_data['policy_text'] ) ) {
					$new_data['added'] = $time;
					$checked[]         = $new_data;
				}
			}
			$update_cache = true;
		}

		if ( ! empty( $old ) ) {
			// A plugin was deactivated.
			foreach ( $old as $old_data ) {
				if ( ! empty( $old_data['plugin_name'] ) && ! empty( $old_data['policy_text'] ) ) {
					$data = array(
						'plugin_name' => $old_data['plugin_name'],
						'policy_text' => $old_data['policy_text'],
						'removed'     => $time,
					);

					$checked[] = $data;
				}
			}
			$update_cache = true;
		}

		if ( $update_cache && $policy_page_id ) {
			delete_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
			// Update the cache.
			foreach ( $checked as $data ) {
				add_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content', $data );
			}
		}

		return $checked;
	}

	/**
	 * Adds a notice with a link to the guide when editing the privacy policy page.
	 *
	 * @since 4.9.6
	 * @since 5.0.0 The `$post` parameter was made optional.
	 *
	 * @global WP_Post $post Global post object.
	 *
	 * @param WP_Post|null $post The currently edited post. Default null.
	 */
	public static function notice( $post = null ) {
		if ( is_null( $post ) ) {
			global $post;
		} else {
			$post = get_post( $post );
		}

		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_privacy_options' ) ) {
			return;
		}

		$current_screen = get_current_screen();
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		if ( 'post' !== $current_screen->base || $policy_page_id !== $post->ID ) {
			return;
		}

		$message = __( 'Need help putting together your new Privacy Policy page? Check out our guide for recommendations on what content to include, along with policies suggested by your plugins and theme.' );
		$url     = esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) );
		$label   = __( 'View Privacy Policy Guide.' );

		if ( get_current_screen()->is_block_editor() ) {
			wp_enqueue_script( 'wp-notices' );
			$action = array(
				'url'   => $url,
				'label' => $label,
			);
			wp_add_inline_script(
				'wp-notices',
				sprintf(
					'wp.data.dispatch( "core/notices" ).createWarningNotice( "%s", { actions: [ %s ], isDismissible: false } )',
					$message,
					wp_json_encode( $action )
				),
				'after'
			);
		} else {
			$message .= sprintf(
				' <a href="%s" target="_blank">%s <span class="screen-reader-text">%s</span></a>',
				$url,
				$label,
				/* translators: Hidden accessibility text. */
				__( '(opens in a new tab)' )
			);
			wp_admin_notice(
				$message,
				array(
					'type'               => 'warning',
					'additional_classes' => array( 'inline', 'wp-pp-notice' ),
				)
			);
		}
	}

	/**
	 * Outputs the privacy policy guide together with content from the theme and plugins.
	 *
	 * @since 4.9.6
	 */
	public static function privacy_policy_guide() {

		$content_array = self::get_suggested_policy_text();
		$content       = '';
		$date_format   = __( 'F j, Y' );

		foreach ( $content_array as $section ) {
			$class   = '';
			$meta    = '';
			$removed = '';

			if ( ! empty( $section['removed'] ) ) {
				$badge_class = ' red';
				$date        = date_i18n( $date_format, $section['removed'] );
				/* translators: %s: Date of plugin deactivation. */
				$badge_title = sprintf( __( 'Removed %s.' ), $date );

				/* translators: %s: Date of plugin deactivation. */
				$removed = sprintf( __( 'You deactivated this plugin on %s and may no longer need this policy.' ), $date );
				$removed = wp_get_admin_notice(
					$removed,
					array(
						'type'               => 'info',
						'additional_classes' => array( 'inline' ),
					)
				);
			} elseif ( ! empty( $section['updated'] ) ) {
				$badge_class = ' blue';
				$date        = date_i18n( $date_format, $section['updated'] );
				/* translators: %s: Date of privacy policy text update. */
				$badge_title = sprintf( __( 'Updated %s.' ), $date );
			}

			$plugin_name = esc_html( $section['plugin_name'] );

			$sanitized_policy_name = sanitize_title_with_dashes( $plugin_name );
			?>
			<h4 class="privacy-settings-accordion-heading">
			<button aria-expanded="false" class="privacy-settings-accordion-trigger" aria-controls="privacy-settings-accordion-block-<?php echo $sanitized_policy_name; ?>" type="button">
				<span class="title"><?php echo $plugin_name; ?></span>
				<?php if ( ! empty( $section['removed'] ) || ! empty( $section['updated'] ) ) : ?>
				<span class="badge <?php echo $badge_class; ?>"> <?php echo $badge_title; ?></span>
				<?php endif; ?>
				<span class="icon"></span>
			</button>
			</h4>
			<div id="privacy-settings-accordion-block-<?php echo $sanitized_policy_name; ?>" class="privacy-settings-accordion-panel privacy-text-box-body" hidden="hidden">
				<?php
				echo $removed;
				echo $section['policy_text'];
				?>
				<?php if ( empty( $section['removed'] ) ) : ?>
				<div class="privacy-settings-accordion-actions">
					<span class="success" aria-hidden="true"><?php _e( 'Copied!' ); ?></span>
					<button type="button" class="privacy-text-copy button">
						<span aria-hidden="true"><?php _e( 'Copy suggested policy text to clipboard' ); ?></span>
						<span class="screen-reader-text">
							<?php
							/* translators: Hidden accessibility text. %s: Plugin name. */
							printf( __( 'Copy suggested policy text from %s.' ), $plugin_name );
							?>
						</span>
					</button>
				</div>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * Returns the default suggested privacy policy content.
	 *
	 * @since 4.9.6
	 * @since 5.0.0 Added the `$blocks` parameter.
	 *
	 * @param bool $description Whether to include the descriptions under the section headings. Default false.
	 * @param bool $blocks      Whether to format the content for the block editor. Default true.
	 * @return string The default policy content.
	 */
	public static function get_default_content( $description = false, $blocks = true ) {
		$suggested_text = '<strong class="privacy-policy-tutorial">' . __( 'Suggested text:' ) . ' </strong>';
		$content        = '';
		$strings        = array();

		// Start of the suggested privacy policy text.
		if ( $description ) {
			$strings[] = '<div class="wp-suggested-text">';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'Who we are' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should note your site URL, as well as the name of the company, organization, or individual behind it, and some accurate contact information.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'The amount of information you may be required to show will vary depending on your local or national business regulations. You may, for example, be required to display a physical address, a registered address, or your company registration number.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. %s: Site URL. */
			$strings[] = '<p>' . $suggested_text . sprintf( __( 'Our website address is: %s.' ), get_bloginfo( 'url', 'display' ) ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'What personal data we collect and why we collect it' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should note what personal data you collect from users and site visitors. This may include personal data, such as name, email address, personal account preferences; transactional data, such as purchase information; and technical data, such as information about cookies.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'You should also note any collection and retention of sensitive personal data, such as data concerning health.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In addition to listing what personal data you collect, you need to note why you collect it. These explanations must note either the legal basis for your data collection and retention or the active consent the user has given.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'Personal data is not just created by a user&#8217;s interactions with your site. Personal data is also generated from technical processes such as contact forms, comments, cookies, analytics, and third party embeds.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'By default WordPress does not collect any personal data about visitors, and only collects the data shown on the User Profile screen from registered users. However some of your plugins may collect personal data. You should add the relevant information below.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'Comments' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this subsection you should note what information is captured through comments. We have noted the data which WordPress collects by default.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( 'An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'Media' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this subsection you should note what information may be disclosed by users who can upload media files. All uploaded files are usually publicly accessible.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'Contact forms' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'By default, WordPress does not include a contact form. If you use a contact form plugin, use this subsection to note what personal data is captured when someone submits a contact form, and how long you keep it. For example, you may note that you keep contact form submissions for a certain period for customer service purposes, but you do not use the information submitted through them for marketing purposes.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'Cookies' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this subsection you should list the cookies your website uses, including those set by your plugins, social media, and analytics. We have provided the cookies which WordPress installs by default.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( 'If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( 'When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( 'If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.' ) . '</p>';
		}

		if ( ! $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2 class="wp-block-heading">' . __( 'Embedded content from other websites' ) . '</h2>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( 'These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'Analytics' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this subsection you should note what analytics package you use, how users can opt out of analytics tracking, and a link to your analytics provider&#8217;s privacy policy, if any.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'By default WordPress does not collect any analytics data. However, many web hosting accounts collect some anonymous analytics data. You may also have installed a WordPress plugin that provides analytics services. In that case, add information from that plugin here.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'Who we share your data with' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should name and list all third party providers with whom you share site data, including partners, cloud-based services, payment processors, and third party service providers, and note what data you share with them and why. Link to their own privacy policies if possible.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'By default WordPress does not share any personal data with anyone.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'If you request a password reset, your IP address will be included in the reset email.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'How long we retain your data' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should explain how long you retain personal data collected or processed by the website. While it is your responsibility to come up with the schedule of how long you keep each dataset for and why you keep it, that information does need to be listed here. For example, you may want to say that you keep contact form entries for six months, analytics records for a year, and customer purchase records for ten years.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( 'For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'What rights you have over your data' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should explain what rights your users have over their data and how they can invoke those rights.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2 class="wp-block-heading">' . __( 'Where your data is sent' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should list all transfers of your site data outside the European Union and describe the means by which that data is safeguarded to European data protection standards. This could include your web hosting, cloud storage, or other third party services.' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'European data protection law requires data about European residents which is transferred outside the European Union to be safeguarded to the same standards as if the data was in Europe. So in addition to listing where data goes, you should describe how you ensure that these standards are met either by yourself or by your third party providers, whether that is through an agreement such as Privacy Shield, model clauses in your contracts, or binding corporate rules.' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( 'Visitor comments may be checked through an automated spam detection service.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'Contact information' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should provide a contact method for privacy-specific concerns. If you are required to have a Data Protection Officer, list their name and full contact details here as well.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'Additional information' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'If you use your site for commercial purposes and you engage in more complex collection or processing of personal data, you should note the following information in your privacy policy in addition to the information we have already discussed.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'How we protect your data' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should explain what measures you have taken to protect your users&#8217; data. This could include technical measures such as encryption; security measures such as two factor authentication; and measures such as staff training in data protection. If you have carried out a Privacy Impact Assessment, you can mention it here too.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'What data breach procedures we have in place' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'In this section you should explain what procedures you have in place to deal with data breaches, either potential or real, such as internal reporting systems, contact mechanisms, or bug bounties.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'What third parties we receive data from' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'If your website receives data about users from third parties, including advertisers, this information must be included within the section of your privacy policy dealing with third party data.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'What automated decision making and/or profiling we do with user data' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'If your website provides a service which includes automated decision making - for example, allowing customers to apply for credit, or aggregating their data into an advertising profile - you must note that this is taking place, and include information about how that information is used, what decisions are made with that aggregated data, and what rights users have over decisions made without human intervention.' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( 'Industry regulatory disclosure requirements' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'If you are a member of a regulated industry, or if you are subject to additional privacy laws, you may be required to disclose that information here.' ) . '</p>';
			$strings[] = '</div>';
		}

		if ( $blocks ) {
			foreach ( $strings as $key => $string ) {
				if ( str_starts_with( $string, '<p>' ) ) {
					$strings[ $key ] = "<!-- wp:paragraph -->\n" . $string . "\n<!-- /wp:paragraph -->\n";
				}

				if ( str_starts_with( $string, '<h2 ' ) ) {
					$strings[ $key ] = "<!-- wp:heading -->\n" . $string . "\n<!-- /wp:heading -->\n";
				}
			}
		}

		$content = implode( '', $strings );
		// End of the suggested privacy policy text.

		/**
		 * Filters the default content suggested for inclusion in a privacy policy.
		 *
		 * @since 4.9.6
		 * @since 5.0.0 Added the `$strings`, `$description`, and `$blocks` parameters.
		 * @deprecated 5.7.0 Use wp_add_privacy_policy_content() instead.
		 *
		 * @param string   $content     The default policy content.
		 * @param string[] $strings     An array of privacy policy content strings.
		 * @param bool     $description Whether policy descriptions should be included.
		 * @param bool     $blocks      Whether the content should be formatted for the block editor.
		 */
		return apply_filters_deprecated(
			'wp_get_default_privacy_policy_content',
			array( $content, $strings, $description, $blocks ),
			'5.7.0',
			'wp_add_privacy_policy_content()'
		);
	}

	/**
	 * Adds the suggested privacy policy text to the policy postbox.
	 *
	 * @since 4.9.6
	 */
	public static function add_suggested_content() {
		$content = self::get_default_content( false, false );
		wp_add_privacy_policy_content( __( 'WordPress' ), $content );
	}
}
