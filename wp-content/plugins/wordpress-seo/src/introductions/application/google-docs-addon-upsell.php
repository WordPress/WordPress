<?php


namespace Yoast\WP\SEO\Introductions\Application;

use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Introductions\Domain\Introduction_Interface;

/**
 * Represents the introduction for the Google Docs Addon feature.
 */
class Google_Docs_Addon_Upsell implements Introduction_Interface {

	use User_Allowed_Trait;

	public const ID = 'google-docs-addon-upsell';

	/**
	 * Holds the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

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
	 * Constructs the introduction.
	 *
	 * @param User_Helper         $user_helper         The user helper.
	 * @param Product_Helper      $product_helper      The product helper.
	 * @param Current_Page_Helper $current_page_helper The current page helper.
	 */
	public function __construct( User_Helper $user_helper, Product_Helper $product_helper, Current_Page_Helper $current_page_helper ) {
		$this->user_helper         = $user_helper;
		$this->product_helper      = $product_helper;
		$this->current_page_helper = $current_page_helper;
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
		return 20;
	}

	/**
	 * Returns whether this introduction should show.
	 * We no longer show this introduction, so we always return false.
	 *
	 * @return bool Whether this introduction should show.
	 */
	public function should_show() {
		return false;
	}
}
