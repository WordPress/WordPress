<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Redirect_Helper;
use Yoast\WP\SEO\Helpers\Robots_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Comment_Link_Fixer.
 */
class Comment_Link_Fixer implements Integration_Interface {

	/**
	 * The redirects helper.
	 *
	 * @var Redirect_Helper
	 */
	protected $redirect;

	/**
	 * The robots helper.
	 *
	 * @var Robots_Helper
	 */
	protected $robots;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Comment_Link_Fixer constructor.
	 *
	 * @codeCoverageIgnore It only sets depedencies.
	 *
	 * @param Redirect_Helper $redirect The redirect helper.
	 * @param Robots_Helper   $robots   The robots helper.
	 */
	public function __construct( Redirect_Helper $redirect, Robots_Helper $robots ) {
		$this->redirect = $redirect;
		$this->robots   = $robots;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->clean_reply_to_com() ) {
			\add_filter( 'comment_reply_link', [ $this, 'remove_reply_to_com' ] );
			\add_action( 'template_redirect', [ $this, 'replytocom_redirect' ], 1 );
		}

		// When users view a reply to a comment, this URL parameter is set. These should never be indexed separately.
		if ( $this->get_replytocom_parameter() !== null ) {
			\add_filter( 'wpseo_robots_array', [ $this->robots, 'set_robots_no_index' ] );
		}
	}

	/**
	 * Checks if the url contains the ?replytocom query parameter.
	 *
	 * @codeCoverageIgnore Wraps the filter input.
	 *
	 * @return string|null The value of replytocom or null if it does not exist.
	 */
	protected function get_replytocom_parameter() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['replytocom'] ) && \is_string( $_GET['replytocom'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return \sanitize_text_field( \wp_unslash( $_GET['replytocom'] ) );
		}
		return null;
	}

	/**
	 * Removes the ?replytocom variable from the link, replacing it with a #comment-<number> anchor.
	 *
	 * @todo Should this function also allow for relative urls ?
	 *
	 * @param string $link The comment link as a string.
	 *
	 * @return string The modified link.
	 */
	public function remove_reply_to_com( $link ) {
		return \preg_replace( '`href=(["\'])(?:.*(?:\?|&|&#038;)replytocom=(\d+)#respond)`', 'href=$1#comment-$2', $link );
	}

	/**
	 * Redirects out the ?replytocom variables.
	 *
	 * @return bool True when redirect has been done.
	 */
	public function replytocom_redirect() {
		if ( isset( $_GET['replytocom'] ) && \is_singular() ) {
			$url          = \get_permalink( $GLOBALS['post']->ID );
			$hash         = \sanitize_text_field( \wp_unslash( $_GET['replytocom'] ) );
			$query_string = '';
			if ( isset( $_SERVER['QUERY_STRING'] ) ) {
				$query_string = \remove_query_arg( 'replytocom', \sanitize_text_field( \wp_unslash( $_SERVER['QUERY_STRING'] ) ) );
			}
			if ( ! empty( $query_string ) ) {
				$url .= '?' . $query_string;
			}
			$url .= '#comment-' . $hash;

			$this->redirect->do_safe_redirect( $url, 301 );

			return true;
		}

		return false;
	}

	/**
	 * Checks whether we can allow the feature that removes ?replytocom query parameters.
	 *
	 * @codeCoverageIgnore It just wraps a call to a filter.
	 *
	 * @return bool True to remove, false not to remove.
	 */
	private function clean_reply_to_com() {
		/**
		 * Filter: 'wpseo_remove_reply_to_com' - Allow disabling the feature that removes ?replytocom query parameters.
		 *
		 * @param bool $return True to remove, false not to remove.
		 */
		return (bool) \apply_filters( 'wpseo_remove_reply_to_com', true );
	}
}
