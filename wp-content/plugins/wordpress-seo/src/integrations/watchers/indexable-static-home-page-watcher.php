<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Watcher that checks for changes in the page used as homepage.
 *
 * Watches the static homepage option and updates the permalinks accordingly.
 */
class Indexable_Static_Home_Page_Watcher implements Integration_Interface {

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $repository;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Indexable_Static_Home_Page_Watcher constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Indexable_Repository $repository The repository to use.
	 */
	public function __construct( Indexable_Repository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_page_on_front', [ $this, 'update_static_homepage_permalink' ], 10, 2 );
	}

	/**
	 * Updates the new and previous homepage's permalink when the static home page is updated.
	 *
	 * @param string $old_value The previous homepage's ID.
	 * @param int    $value     The new homepage's ID.
	 *
	 * @return void
	 */
	public function update_static_homepage_permalink( $old_value, $value ) {
		if ( \is_string( $old_value ) ) {
			$old_value = (int) $old_value;
		}

		if ( $old_value === $value ) {
			return;
		}

		$this->update_permalink_for_page( $old_value );
		$this->update_permalink_for_page( $value );
	}

	/**
	 * Updates the permalink based on the selected homepage settings.
	 *
	 * @param int $page_id The page's id.
	 *
	 * @return void
	 */
	private function update_permalink_for_page( $page_id ) {
		if ( $page_id === 0 ) {
			return;
		}

		$indexable = $this->repository->find_by_id_and_type( $page_id, 'post', false );

		if ( $indexable === false ) {
			return;
		}

		$indexable->permalink = \get_permalink( $page_id );

		$indexable->save();
	}
}
