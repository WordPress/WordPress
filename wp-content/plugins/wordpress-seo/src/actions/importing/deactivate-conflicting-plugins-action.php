<?php

namespace Yoast\WP\SEO\Actions\Importing;

use Yoast\WP\SEO\Conditionals\Updated_Importer_Framework_Conditional;
use Yoast\WP\SEO\Config\Conflicting_Plugins;
use Yoast\WP\SEO\Helpers\Import_Cursor_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Sanitization_Helper;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Replacevar_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Provider_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Transformer_Service;
use Yoast\WP\SEO\Services\Importing\Conflicting_Plugins_Service;

/**
 * Deactivates plug-ins that cause conflicts with Yoast SEO.
 */
class Deactivate_Conflicting_Plugins_Action extends Abstract_Aioseo_Importing_Action {

	/**
	 * The plugin the class deals with.
	 *
	 * @var string
	 */
	public const PLUGIN = 'conflicting-plugins';

	/**
	 * The type the class deals with.
	 *
	 * @var string
	 */
	public const TYPE = 'deactivation';

	/**
	 * The replacevar handler.
	 *
	 * @var Aioseo_Replacevar_Service
	 */
	protected $replacevar_handler;

	/**
	 * Knows all plugins that might possibly conflict.
	 *
	 * @var Conflicting_Plugins_Service
	 */
	protected $conflicting_plugins;

	/**
	 * The list of conflicting plugins
	 *
	 * @var array
	 */
	protected $detected_plugins;

	/**
	 * Class constructor.
	 *
	 * @param Import_Cursor_Helper              $import_cursor               The import cursor helper.
	 * @param Options_Helper                    $options                     The options helper.
	 * @param Sanitization_Helper               $sanitization                The sanitization helper.
	 * @param Aioseo_Replacevar_Service         $replacevar_handler          The replacevar handler.
	 * @param Aioseo_Robots_Provider_Service    $robots_provider             The robots provider service.
	 * @param Aioseo_Robots_Transformer_Service $robots_transformer          The robots transfomer service.
	 * @param Conflicting_Plugins_Service       $conflicting_plugins_service The Conflicting plugins Service.
	 */
	public function __construct(
		Import_Cursor_Helper $import_cursor,
		Options_Helper $options,
		Sanitization_Helper $sanitization,
		Aioseo_Replacevar_Service $replacevar_handler,
		Aioseo_Robots_Provider_Service $robots_provider,
		Aioseo_Robots_Transformer_Service $robots_transformer,
		Conflicting_Plugins_Service $conflicting_plugins_service
	) {
		parent::__construct( $import_cursor, $options, $sanitization, $replacevar_handler, $robots_provider, $robots_transformer );

		$this->conflicting_plugins = $conflicting_plugins_service;
		$this->detected_plugins    = [];
	}

	/**
	 * Get the total number of conflicting plugins.
	 *
	 * @return int
	 */
	public function get_total_unindexed() {
		return \count( $this->get_detected_plugins() );
	}

	/**
	 * Returns whether the updated importer framework is enabled.
	 *
	 * @return bool True if the updated importer framework is enabled.
	 */
	public function is_enabled() {
		$updated_importer_framework_conditional = \YoastSEO()->classes->get( Updated_Importer_Framework_Conditional::class );

		return $updated_importer_framework_conditional->is_met();
	}

	/**
	 * Deactivate conflicting plugins.
	 *
	 * @return array
	 */
	public function index() {
		$detected_plugins = $this->get_detected_plugins();
		$this->conflicting_plugins->deactivate_conflicting_plugins( $detected_plugins );

		// We need to conform to the interface, so we report that no indexables were created.
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_limit() {
		return \count( Conflicting_Plugins::all_plugins() );
	}

	/**
	 * Returns the total number of unindexed objects up to a limit.
	 *
	 * @param int $limit The maximum.
	 *
	 * @return int The total number of unindexed objects.
	 */
	public function get_limited_unindexed_count( $limit ) {
		$count = \count( $this->get_detected_plugins() );
		return ( $count <= $limit ) ? $count : $limit;
	}

	/**
	 * Returns all detected plugins.
	 *
	 * @return array The detected plugins.
	 */
	protected function get_detected_plugins() {
		// The active plugins won't change much. We can reuse the result for the duration of the request.
		if ( \count( $this->detected_plugins ) < 1 ) {
			$this->detected_plugins = $this->conflicting_plugins->detect_conflicting_plugins();
		}
		return $this->detected_plugins;
	}
}
