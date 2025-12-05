<?php


namespace Yoast\WP\SEO\Introductions\Application;

use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Introductions\Domain\Introduction_Interface;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Represents the introduction for the Black Friday announcement.
 */
class Black_Friday_Announcement implements Introduction_Interface {

	public const ID = 'black-friday-announcement';

	/**
	 * Holds the current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Holds the promotion manager.
	 *
	 * @var Promotion_Manager
	 */
	private $promotion_manager;

	/**
	 * Holds the product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * Constructs the introduction.
	 *
	 * @param Current_Page_Helper $current_page_helper The current page helper.
	 * @param Promotion_Manager   $promotion_manager   The promotion manager.
	 * @param Product_Helper      $product_helper      The product helper.
	 */
	public function __construct(
		Current_Page_Helper $current_page_helper,
		Promotion_Manager $promotion_manager,
		Product_Helper $product_helper
	) {
		$this->current_page_helper = $current_page_helper;
		$this->promotion_manager   = $promotion_manager;
		$this->product_helper      = $product_helper;
	}

	/**
	 * Returns the ID.
	 *
	 * @return string The ID.
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Returns the name of the introduction.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		\_deprecated_function( __METHOD__, 'Yoast SEO Premium 21.6', 'Please use get_id() instead' );

		return self::ID;
	}

	/**
	 * Returns the requested pagination priority. Lower means earlier.
	 *
	 * @return int The priority.
	 */
	public function get_priority() {
		return 10;
	}

	/**
	 * Returns whether this introduction should show.
	 *
	 * @return bool Whether this introduction should show.
	 */
	public function should_show() {
		return $this->current_page_helper->is_yoast_seo_page() && ! $this->product_helper->is_premium() && $this->promotion_manager->is( 'black-friday-promotion' );
	}
}
