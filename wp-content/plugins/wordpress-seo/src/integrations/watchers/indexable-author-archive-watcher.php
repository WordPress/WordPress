<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Integrations\Cleanup_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Watches the `wpseo_titles` option for changes to the author archive settings.
 */
class Indexable_Author_Archive_Watcher implements Integration_Interface {

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Indexable_Author_Archive_Watcher constructor.
	 *
	 * @param Indexable_Helper $indexable_helper The indexable helper.
	 */
	public function __construct( Indexable_Helper $indexable_helper ) {
		$this->indexable_helper = $indexable_helper;
	}

	/**
	 * Check if the author archives are disabled whenever the `wpseo_titles` option
	 * changes.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action(
			'update_option_wpseo_titles',
			[ $this, 'reschedule_indexable_cleanup_when_author_archives_are_disabled' ],
			10,
			2
		);
	}

	/**
	 * This watcher should only be run when the migrations have been run.
	 * (Otherwise there may not be an indexable table to clean).
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Reschedule the indexable cleanup routine if the author archives are disabled.
	 * to make sure that all authors are removed from the indexables table.
	 *
	 * When author archives are disabled, they can never be indexed.
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
	 *
	 * @param array $old_value The old `wpseo_titles` option value.
	 * @param array $new_value The new `wpseo_titles` option value.
	 *
	 * @phpcs:enable
	 * @return void
	 */
	public function reschedule_indexable_cleanup_when_author_archives_are_disabled( $old_value, $new_value ) {
		if ( $old_value['disable-author'] !== true && $new_value['disable-author'] === true && $this->indexable_helper->should_index_indexables() ) {
			$cleanup_not_yet_scheduled = ! \wp_next_scheduled( Cleanup_Integration::START_HOOK );
			if ( $cleanup_not_yet_scheduled ) {
				\wp_schedule_single_event( ( \time() + ( \MINUTE_IN_SECONDS * 5 ) ), Cleanup_Integration::START_HOOK );
			}
		}
	}
}
