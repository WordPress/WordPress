<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Alerts\Application\Default_SEO_Data;

use Yoast\WP\SEO\Alerts\Infrastructure\Default_SEO_Data\Default_SEO_Data_Collector;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Default_SEO_Data_Alert class.
 */
class Default_SEO_Data_Alert implements Integration_Interface {

	public const NOTIFICATION_ID = 'wpseo-default-seo-data';

	/**
	 * The notifications center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * The default SEO data collector.
	 *
	 * @var Default_SEO_Data_Collector
	 */
	private $default_seo_data_collector;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $short_link_helper;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type_helper;

	/**
	 * Default_SEO_Data_Alert constructor.
	 *
	 * @param Yoast_Notification_Center  $notification_center        The notification center.
	 * @param Default_SEO_Data_Collector $default_seo_data_collector The default SEO data collector.
	 * @param Short_Link_Helper          $short_link_helper          The short link helper.
	 * @param Product_Helper             $product_helper             The product helper.
	 * @param Indexable_Helper           $indexable_helper           The indexable helper.
	 * @param Post_Type_Helper           $post_type_helper           The post type helper.
	 */
	public function __construct(
		Yoast_Notification_Center $notification_center,
		Default_SEO_Data_Collector $default_seo_data_collector,
		Short_Link_Helper $short_link_helper,
		Product_Helper $product_helper,
		Indexable_Helper $indexable_helper,
		Post_Type_Helper $post_type_helper
	) {
		$this->notification_center        = $notification_center;
		$this->default_seo_data_collector = $default_seo_data_collector;
		$this->short_link_helper          = $short_link_helper;
		$this->product_helper             = $product_helper;
		$this->indexable_helper           = $indexable_helper;
		$this->post_type_helper           = $post_type_helper;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'add_notifications' ] );
	}

	/**
	 * Adds notifications (when necessary).
	 *
	 * We want to show this notification only when there are enough posts that have the default SEO title or meta description, or both.
	 * If this is not the case we will not show the notification at all since it does not serve a purpose yet.
	 *
	 * @return void
	 */
	public function add_notifications() {
		if ( ! $this->indexable_helper->should_index_indexables() ) {
			// Do not show the notification when indexables are disabled.
			$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
			return;
		}

		if ( ! $this->post_type_helper->is_indexable( 'post' ) || ! $this->post_type_helper->has_metabox( 'post' ) ) {
			// Do not show the notification when posts are not indexable or have no metabox.
			$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
			return;
		}

		$posts_with_default_seo_title       = $this->default_seo_data_collector->get_posts_with_default_seo_title();
		$posts_with_default_seo_description = $this->default_seo_data_collector->get_posts_with_default_seo_description();

		$has_enough_posts_with_default_title = \count( $posts_with_default_seo_title ) > 4;
		$has_enough_posts_with_default_desc  = \count( $posts_with_default_seo_description ) > 4;

		if ( ! $has_enough_posts_with_default_title && ! $has_enough_posts_with_default_desc ) {
			$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
			return;
		}

		$notification = $this->get_default_seo_data_notification( $has_enough_posts_with_default_title, $has_enough_posts_with_default_desc );

		$this->notification_center->add_notification( $notification );
	}

	/**
	 * Build the default SEO data notification.
	 *
	 * @param bool $has_enough_posts_with_default_title Whether there are content types with default SEO title in their most recent posts.
	 * @param bool $has_enough_posts_with_default_desc  Whether there are content types with default SEO description in their most recent posts.
	 *
	 * @return Yoast_Notification The notification containing the suggested plugin.
	 */
	private function get_default_seo_data_notification( $has_enough_posts_with_default_title, $has_enough_posts_with_default_desc ): Yoast_Notification {
		$message = $this->get_default_seo_data_message( $has_enough_posts_with_default_title, $has_enough_posts_with_default_desc );

		return new Yoast_Notification(
			$message,
			[
				'id'           => self::NOTIFICATION_ID,
				'type'         => Yoast_Notification::WARNING,
				'capabilities' => [ 'wpseo_manage_options' ],
			]
		);
	}

	/**
	 * Creates a message to inform users that they are using only default SEO data lately.
	 *
	 * @param bool $has_enough_posts_with_default_title Whether there are content types with default SEO title in their most recent posts.
	 * @param bool $has_enough_posts_with_default_desc  Whether there are content types with default SEO description in their most recent posts.
	 *
	 * @return string The default SEO data message.
	 */
	private function get_default_seo_data_message( $has_enough_posts_with_default_title, $has_enough_posts_with_default_desc ): string {
		$shortlink = ( $this->product_helper->is_premium() ) ? $this->short_link_helper->get( 'https://yoa.st/ai-generate-alert-premium/' ) : $this->short_link_helper->get( 'https://yoa.st/ai-generate-alert-free/' );

		if ( $has_enough_posts_with_default_title && $has_enough_posts_with_default_desc ) {
			$default_seo_data = \esc_html__( 'SEO titles and meta descriptions', 'wordpress-seo' );
		}
		elseif ( $has_enough_posts_with_default_title ) {
			$default_seo_data = \esc_html__( 'SEO titles', 'wordpress-seo' );
		}
		elseif ( $has_enough_posts_with_default_desc ) {
			$default_seo_data = \esc_html__( 'meta descriptions', 'wordpress-seo' );
		}
		else {
			$default_seo_data = \esc_html__( 'SEO data', 'wordpress-seo' );
		}

		/* translators: %1$s expands to "SEO title" or "meta description", %2$s expands to an opening link tag, %3$s expands to an opening strong tag, %4$s expands to a closing strong tag, %5$s expands to a closing link tag. */
		$message = ( $this->product_helper->is_premium() ) ? \esc_html__( 'Your recent posts are using default %1$s, which can make them easy to overlook in search results. Update them manually or %2$sfind out how %3$sYoast AI Generate%4$s can improve them for you.%5$s', 'wordpress-seo' ) : \esc_html__( 'Your recent posts are using default %1$s, which can make them easy to overlook in search results. Update them for better visibility or %2$stry %3$sYoast AI Generate%4$s for free to do it faster.%5$s', 'wordpress-seo' );

		return \sprintf(
			$message,
			$default_seo_data,
			'<a href="' . \esc_url( $shortlink ) . '" target="_blank">',
			'<strong>',
			'</strong>',
			'</a>'
		);
	}
}
