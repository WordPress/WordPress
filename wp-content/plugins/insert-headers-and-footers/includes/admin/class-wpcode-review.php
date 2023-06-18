<?php

/**
 * Ask for some love.
 *
 *
 */
class WPCode_Review {

	/**
	 * Primary class constructor.
	 *
	 *
	 */
	public function __construct() {

		// Admin notice requesting review.
		add_action( 'admin_init', array( $this, 'review_request' ) );

		// Admin footer text.
		add_filter( 'admin_footer_text', array( $this, 'admin_footer' ), 1, 2 );
	}

	/**
	 * Add admin notices as needed for reviews.
	 *
	 *
	 */
	public function review_request() {

		// Only consider showing the review request to admin users.
		if ( ! is_super_admin() ) {
			return;
		}

		// Don't show notice to headers & footers mode users.
		if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
			return;
		}

		// Verify that we can do a check for reviews.
		$notices = get_option( 'wpcode_admin_notices', array() );
		$time    = time();
		$load    = false;

		if ( empty( $notices['review_request'] ) ) {
			$notices['review_request'] = array(
				'time'      => $time,
				'dismissed' => false,
			);

			update_option( 'wpcode_admin_notices', $notices );

			return;
		}

		// Check if it has been dismissed or not.
		if (
			( isset( $notices['review_request']['dismissed'] ) &&
			  ! $notices['review_request']['dismissed'] ) &&
			(
				isset( $notices['review_request']['time'] ) &&
				( ( $notices['review_request']['time'] + DAY_IN_SECONDS ) <= $time )
			)
		) {
			$load = true;
		}

		// If we cannot load, return early.
		if ( ! $load ) {
			return;
		}

		$this->review();
	}

	/**
	 * Maybe show Lite review request.
	 */
	public function review() {

		// Fetch when plugin was initially installed.
		$activated = get_option( 'ihaf_activated', array() );

		if ( ! empty( $activated['wpcode'] ) ) {
			// Only continue if plugin has been installed for at least 14 days.
			if ( ( $activated['wpcode'] + ( DAY_IN_SECONDS * 14 ) ) > time() ) {
				return;
			}
		} else {
			$activated['wpcode'] = time();

			update_option( 'ihaf_activated', $activated );

			return;
		}

		// Only proceed with displaying if the user is using an active snippet.
		$snippet_count = wp_count_posts( 'wpcode' );

		if ( empty( $snippet_count->publish ) ) {
			return;
		}

		$feedback_url = add_query_arg( array(
			'siteurl' => untrailingslashit( home_url() ),
			'plugin' => class_exists( 'WPCode_Premium') ? 'pro' : 'lite',
			'version' => WPCODE_VERSION,
		), 'https://www.wpcode.com/plugin-feedback/' );
		$feedback_url = wpcode_utm_url( $feedback_url, 'review-notice', 'feedback' );

		ob_start();

		// We have a candidate! Output a review message.
		?>
		<div class="wpcode-review-step wpcode-review-step-1">
			<p><?php esc_html_e( 'Are you enjoying WPCode?', 'insert-headers-and-footers' ); ?></p>
			<p>
				<a href="#" class="wpcode-review-switch-step"
				   data-step="3"><?php esc_html_e( 'Yes', 'insert-headers-and-footers' ); ?></a><br/>
				<a href="#" class="wpcode-review-switch-step"
				   data-step="2"><?php esc_html_e( 'Not Really', 'insert-headers-and-footers' ); ?></a>
			</p>
		</div>
		<div class="wpcode-review-step wpcode-review-step-2" style="display: none">
			<p><?php esc_html_e( 'We\'re sorry to hear you aren\'t enjoying WPCode. We would love a chance to improve. Could you take a minute and let us know what we can do better?', 'insert-headers-and-footers' ); ?></p>
			<p>
				<a href="<?php echo esc_url( $feedback_url ); ?>"
				   class="wpcode-notice-dismiss" target="_blank"><?php esc_html_e( 'Give Feedback', 'insert-headers-and-footers' ); ?></a><br>
				<a href="#" class="wpcode-notice-dismiss"
				   rel="noopener noreferrer"><?php esc_html_e( 'No thanks', 'insert-headers-and-footers' ); ?></a>
			</p>
		</div>
		<div class="wpcode-review-step wpcode-review-step-3" style="display: none">
			<p><?php esc_html_e( 'That\'s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'insert-headers-and-footers' ); ?></p>
			<p>
				<strong><?php echo wp_kses( __( '~ Syed Balkhi<br>Co-Founder of WPCode', 'insert-headers-and-footers' ), array( 'br' => array() ) ); ?></strong>
			</p>
			<p>
				<a href="https://wordpress.org/support/plugin/insert-headers-and-footers/reviews/?filter=5#new-post" class="wpcode-notice-dismiss wpcode-review-out" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Give Feedback', 'insert-headers-and-footers' ); ?></a><br>
				<a href="#" class="wpcode-notice-dismiss" rel="noopener noreferrer"><?php esc_html_e( 'No thanks', 'insert-headers-and-footers' ); ?></a><br>
			</p>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$(document).on('click', '.wpcode-review-switch-step', function (e) {
					e.preventDefault();
					var target = $(this).attr('data-step');
					if (target) {
						var notice = $(this).closest('.wpcode-review-notice');
						var review_step = notice.find('.wpcode-review-step-' + target);
						if (review_step.length > 0) {
							notice.find('.wpcode-review-step:visible').fadeOut(function () {
								review_step.fadeIn();
							});
						}
					}
				})
			});
		</script>
		<?php

		WPCode_Notice::info(
			ob_get_clean(),
			array(
				'dismiss' => WPCode_Notice::DISMISS_GLOBAL,
				'slug'    => 'review_request',
				'autop'   => false,
				'class'   => 'wpcode-review-notice',
			)
		);
	}

	/**
	 * When user is on a WPCode related admin page, display footer text
	 * that graciously asks them to rate us.
	 *
	 * @param string $text Footer text.
	 *
	 * @return string
	 *
	 *
	 */
	public function admin_footer( $text ) {

		global $current_screen;

		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'wpcode' ) !== false ) {
			$url  = 'https://wordpress.org/support/plugin/insert-headers-and-footers/reviews/?filter=5#new-post';
			$text = sprintf(
				wp_kses( /* translators: $1$s - WPCode plugin name; $2$s - WP.org review link; $3$s - WP.org review link. */
					__( 'Please rate %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thank you from the WPCode team!', 'insert-headers-and-footers' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
					)
				),
				'<strong>WPCode</strong>',
				$url,
				$url
			);
		}

		return $text;
	}

}

new WPCode_Review();
