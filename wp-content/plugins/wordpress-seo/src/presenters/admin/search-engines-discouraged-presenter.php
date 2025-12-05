<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Search_Engines_Discouraged_Presenter.
 */
class Search_Engines_Discouraged_Presenter extends Abstract_Presenter {

	/**
	 * Returns the notification as an HTML string.
	 *
	 * @return string The notification in an HTML string representation.
	 */
	public function present() {
		$notification_text  = '<p>';
		$notification_text .= $this->get_message();
		$notification_text .= '</p>';

		return $notification_text;
	}

	/**
	 * Returns the message to show.
	 *
	 * @return string The message.
	 */
	protected function get_message() {
		return \sprintf(
			'<strong>%1$s</strong> %2$s <button type="button" id="robotsmessage-dismiss-button" class="button-link hide-if-no-js" data-nonce="%3$s">%4$s</button>',
			\esc_html__( 'Huge SEO Issue: You\'re blocking access to robots.', 'wordpress-seo' ),
			\sprintf(
			/* translators: 1: Link start tag to the WordPress Reading Settings page, 2: Link closing tag. */
				\esc_html__( 'If you want search engines to show this site in their results, you must %1$sgo to your Reading Settings%2$s and uncheck the box for Search Engine Visibility.', 'wordpress-seo' ),
				'<a href="' . \esc_url( \admin_url( 'options-reading.php' ) ) . '">',
				'</a>'
			),
			\esc_js( \wp_create_nonce( 'wpseo-ignore' ) ),
			\esc_html__( 'I don\'t want this site to show in the search results.', 'wordpress-seo' )
		);
	}
}
