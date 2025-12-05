<?php


namespace Yoast\WP\SEO\Introductions\Application;

use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Introductions\Domain\Introduction_Interface;
use Yoast\WP\SEO\Introductions\Infrastructure\Introductions_Seen_Repository;

/**
 * Represents the Premium upsell that shows on Yoast SEO pages after a delay.
 */
class Delayed_Premium_Upsell implements Introduction_Interface {

	public const ID         = 'delayed-premium-upsell';
	public const DELAY_DAYS = 14;

	/**
	 * Holds the repository.
	 *
	 * @var Introductions_Seen_Repository
	 */
	private $introductions_seen_repository;

	/**
	 * Holds the product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * Holds the current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Delayed_Premium_Upsell constructor.
	 *
	 * @param Current_Page_Helper           $current_page_helper           The current page helper.
	 * @param Introductions_Seen_Repository $introductions_seen_repository The introductions seen repository.
	 * @param Options_Helper                $options_helper                The options helper.
	 * @param Product_Helper                $product_helper                The product helper.
	 */
	public function __construct( Current_Page_Helper $current_page_helper, Introductions_Seen_Repository $introductions_seen_repository, Options_Helper $options_helper, Product_Helper $product_helper ) {
		$this->current_page_helper           = $current_page_helper;
		$this->introductions_seen_repository = $introductions_seen_repository;
		$this->options_helper                = $options_helper;
		$this->product_helper                = $product_helper;
	}

	/**
	 * Returns the ID.
	 *
	 * @return string The ID.
	 */
	public function get_id(): string {
		return self::ID;
	}

	/**
	 * Returns the name of the introduction.
	 *
	 * @return string The name.
	 */
	public function get_name(): string {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 21.6', 'Please use get_id() instead' );

		return self::ID;
	}

	/**
	 * Returns the requested pagination priority. Lower means earlier.
	 *
	 * @return int The priority.
	 */
	public function get_priority(): int {
		return 30;
	}

	/**
	 * Returns whether this introduction should show.
	 *
	 * @return bool Whether this introduction should show.
	 */
	public function should_show(): bool {
		// Never show when not on a Yoast SEO page or when the user has Premium activated.
		if ( ! $this->current_page_helper->is_yoast_seo_page() || $this->product_helper->is_premium() ) {
			return false;
		}

		return $this->should_show_after_delay();
	}

	/**
	 * Determines if the introduction should show based on the self:DELAY_DAY delay from installation or update.
	 *
	 * @return bool Whether the introduction should show after the delay.
	 */
	private function should_show_after_delay(): bool {
		$delay              = ( self::DELAY_DAYS * \DAY_IN_SECONDS );
		$current_time       = \time();
		$previous_version   = $this->options_helper->get( 'previous_version' );
		$first_activated_on = $this->options_helper->get( 'first_activated_on' );

		// Case where the user has installed the plugin for the first time and the delay has passed.
		if ( $previous_version === '' ) {
			return ( $current_time - $first_activated_on ) >= $delay && $this->is_last_introduction_seen_older_than_a_week();
		}

		// Case where the user has updated the plugin and the delay has passed since the last update.
		$last_updated_on = $this->options_helper->get( 'last_updated_on' );

		$uniform_last_updated_on = \is_int( $last_updated_on ) ? $last_updated_on : 0;
		if ( ( $current_time - $uniform_last_updated_on ) >= $delay ) {
			return $this->is_last_introduction_seen_older_than_a_week();
		}

		return false;
	}

	/**
	 * Checks if the last introduction seen is older than a week.
	 *
	 * @return bool True if the last introduction seen is older than a week, false otherwise.
	 */
	private function is_last_introduction_seen_older_than_a_week(): bool {
		$seen_introductions = $this->introductions_seen_repository->get_all_introductions( \get_current_user_id() );
		// No other introduction has been seen.
		if ( empty( $seen_introductions ) ) {
			return true;
		}

		$old_format_introductions = \array_filter(
			$seen_introductions,
			static function ( $item ) {
				return \is_bool( $item );
			}
		);

		if ( ! empty( $old_format_introductions ) ) {
			// There are introductions in the old format, so we cannot determine when they were seen.
			// To be safe, we assume the user has seen an introduction recently.
			return false;
		}

		// Find the most recent introduction seen.
		$most_recent_introduction = \array_reduce(
			$seen_introductions,
			static function ( $carry, $item ) {
				if ( $carry === null || $item['seen_on'] > $carry['seen_on'] ) {
					return $item;
				}
				return $carry;
			}
		);

		// If the most recent introduction seen is older than a week, return true.
		if ( ( \time() - $most_recent_introduction['seen_on'] ) >= ( 7 * \DAY_IN_SECONDS ) ) {
			return true;
		}

		return false;
	}
}
