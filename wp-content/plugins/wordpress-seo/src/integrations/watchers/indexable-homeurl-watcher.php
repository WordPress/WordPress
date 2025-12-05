<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use WP_CLI;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Home url option watcher.
 *
 * Handles updates to the home URL option for the Indexables table.
 */
class Indexable_HomeUrl_Watcher implements Integration_Interface {

	/**
	 * Represents the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Indexable_HomeUrl_Watcher constructor.
	 *
	 * @param Post_Type_Helper $post_type The post type helper.
	 * @param Options_Helper   $options   The options helper.
	 * @param Indexable_Helper $indexable The indexable helper.
	 */
	public function __construct( Post_Type_Helper $post_type, Options_Helper $options, Indexable_Helper $indexable ) {
		$this->post_type        = $post_type;
		$this->options_helper   = $options;
		$this->indexable_helper = $indexable;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_home', [ $this, 'reset_permalinks' ] );
		\add_action( 'wpseo_permalink_structure_check', [ $this, 'force_reset_permalinks' ] );
	}

	/**
	 * Resets the permalinks for everything that is related to the permalink structure.
	 *
	 * @return void
	 */
	public function reset_permalinks() {
		$this->indexable_helper->reset_permalink_indexables( null, null, Indexing_Reasons::REASON_HOME_URL_OPTION );

		// Reset the home_url option.
		$this->options_helper->set( 'home_url', \get_home_url() );
	}

	/**
	 * Resets the permalink indexables automatically, if necessary.
	 *
	 * @return bool Whether the request ran.
	 */
	public function force_reset_permalinks() {
		if ( $this->should_reset_permalinks() ) {
			$this->reset_permalinks();

			if ( \defined( 'WP_CLI' ) && \WP_CLI ) {
				WP_CLI::success( \__( 'All permalinks were successfully reset', 'wordpress-seo' ) );
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks whether permalinks should be reset.
	 *
	 * @return bool Whether the permalinks should be reset.
	 */
	public function should_reset_permalinks() {
		return \get_home_url() !== $this->options_helper->get( 'home_url' );
	}
}
