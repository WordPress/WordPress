<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Indexing_Notification_Presenter.
 *
 * @package Yoast\WP\SEO\Presenters\Admin
 */
class Indexing_Notification_Presenter extends Abstract_Presenter {

	/**
	 * The total number of unindexed objects.
	 *
	 * @var int
	 */
	protected $total_unindexed;

	/**
	 * The message to show in the notification.
	 *
	 * @var string
	 */
	protected $reason;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * Indexing_Notification_Presenter constructor.
	 *
	 * @param Short_Link_Helper $short_link_helper The short link helper.
	 * @param int               $total_unindexed   Total number of unindexed objects.
	 * @param string            $reason            The reason to show in the notification.
	 */
	public function __construct( $short_link_helper, $total_unindexed, $reason ) {
		$this->short_link_helper = $short_link_helper;
		$this->total_unindexed   = $total_unindexed;
		$this->reason            = $reason;
	}

	/**
	 * Returns the notification as an HTML string.
	 *
	 * @return string The HTML string representation of the notification.
	 */
	public function present() {
		$notification_text  = '<p>' . $this->get_message( $this->reason );
		$notification_text .= $this->get_time_estimate( $this->total_unindexed ) . '</p>';
		$notification_text .= '<a class="button" href="' . \get_admin_url( null, 'admin.php?page=wpseo_tools&start-indexation=true' ) . '">';
		$notification_text .= \esc_html__( 'Start SEO data optimization', 'wordpress-seo' );
		$notification_text .= '</a>';

		return $notification_text;
	}

	/**
	 * Determines the message to show in the indexing notification.
	 *
	 * @param string $reason The reason identifier.
	 *
	 * @return string The message to show in the notification.
	 */
	protected function get_message( $reason ) {
		switch ( $reason ) {
			case Indexing_Reasons::REASON_PERMALINK_SETTINGS:
				$text = \esc_html__( 'Because of a change in your permalink structure, some of your SEO data needs to be reprocessed.', 'wordpress-seo' );
				break;
			case Indexing_Reasons::REASON_HOME_URL_OPTION:
				$text = \esc_html__( 'Because of a change in your home URL setting, some of your SEO data needs to be reprocessed.', 'wordpress-seo' );
				break;
			case Indexing_Reasons::REASON_CATEGORY_BASE_PREFIX:
				$text = \esc_html__( 'Because of a change in your category base setting, some of your SEO data needs to be reprocessed.', 'wordpress-seo' );
				break;
			case Indexing_Reasons::REASON_TAG_BASE_PREFIX:
				$text = \esc_html__( 'Because of a change in your tag base setting, some of your SEO data needs to be reprocessed.', 'wordpress-seo' );
				break;
			case Indexing_Reasons::REASON_POST_TYPE_MADE_PUBLIC:
				$text = \esc_html__( 'We need to re-analyze some of your SEO data because of a change in the visibility of your post types. Please help us do that by running the SEO data optimization.', 'wordpress-seo' );
				break;
			case Indexing_Reasons::REASON_TAXONOMY_MADE_PUBLIC:
				$text = \esc_html__( 'We need to re-analyze some of your SEO data because of a change in the visibility of your taxonomies. Please help us do that by running the SEO data optimization.', 'wordpress-seo' );
				break;
			case Indexing_Reasons::REASON_ATTACHMENTS_MADE_ENABLED:
				$text = \esc_html__( 'It looks like you\'ve enabled media pages. We recommend that you help us to re-analyze your site by running the SEO data optimization.', 'wordpress-seo' );
				break;
			default:
				$text = \esc_html__( 'You can speed up your site and get insight into your internal linking structure by letting us perform a few optimizations to the way SEO data is stored.', 'wordpress-seo' );
		}

		/**
		 * Filter: 'wpseo_indexables_indexation_alert' - Allow developers to filter the reason of the indexation
		 *
		 * @param string $text   The text to show as reason.
		 * @param string $reason The reason value.
		 */
		return (string) \apply_filters( 'wpseo_indexables_indexation_alert', $text, $reason );
	}

	/**
	 * Creates a time estimate based on the total number on unindexed objects.
	 *
	 * @param int $total_unindexed The total number of unindexed objects.
	 *
	 * @return string The time estimate as a HTML string.
	 */
	protected function get_time_estimate( $total_unindexed ) {
		if ( $total_unindexed < 400 ) {
			return \esc_html__( ' We estimate this will take less than a minute.', 'wordpress-seo' );
		}

		if ( $total_unindexed < 2500 ) {
			return \esc_html__( ' We estimate this will take a couple of minutes.', 'wordpress-seo' );
		}

		$estimate  = \esc_html__( ' We estimate this could take a long time, due to the size of your site. As an alternative to waiting, you could:', 'wordpress-seo' );
		$estimate .= '<ul class="ul-disc">';
		$estimate .= '<li>';
		$estimate .= \sprintf(
			/* translators: 1: Expands to Yoast SEO */
			\esc_html__( 'Wait for a week or so, until %1$s automatically processes most of your content in the background.', 'wordpress-seo' ),
			'Yoast SEO'
		);
		$estimate .= '</li>';
		$estimate .= '<li>';
		$estimate .= \sprintf(
			/* translators: 1: Link to article about indexation command, 2: Anchor closing tag, 3: Link to WP CLI. */
			\esc_html__( '%1$sRun the indexation process on your server%2$s using %3$sWP CLI%2$s.', 'wordpress-seo' ),
			'<a href="' . \esc_url( $this->short_link_helper->get( 'https://yoa.st/3-w' ) ) . '" target="_blank">',
			'</a>',
			'<a href="https://wp-cli.org/" target="_blank">'
		);

		$estimate .= '</li>';
		$estimate .= '</ul>';

		return $estimate;
	}
}
