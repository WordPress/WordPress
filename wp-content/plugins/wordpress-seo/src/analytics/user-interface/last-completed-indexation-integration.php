<?php

namespace Yoast\WP\SEO\Analytics\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Handles setting a timestamp when the indexation of a specific indexation action is completed.
 */
class Last_Completed_Indexation_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper The options helper.
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Registers action hook to maybe save an option.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		\add_action(
			'wpseo_indexables_unindexed_calculated',
			[
				$this,
				'maybe_set_indexables_unindexed_calculated',
			],
			10,
			2
		);
	}

	/**
	 * Saves a timestamp option when there are no unindexed indexables.
	 *
	 * @param string $indexable_name The name of the indexable that is being checked.
	 * @param int    $count          The amount of missing indexables.
	 *
	 * @return void
	 */
	public function maybe_set_indexables_unindexed_calculated( string $indexable_name, int $count ): void {
		if ( $count === 0 ) {
			$no_index                    = $this->options_helper->get( 'last_known_no_unindexed', [] );
			$no_index[ $indexable_name ] = \time();

			\remove_action( 'update_option_wpseo', [ 'WPSEO_Utils', 'clear_cache' ] );
			$this->options_helper->set( 'last_known_no_unindexed', $no_index );
			\add_action( 'update_option_wpseo', [ 'WPSEO_Utils', 'clear_cache' ] );
		}
	}
}
