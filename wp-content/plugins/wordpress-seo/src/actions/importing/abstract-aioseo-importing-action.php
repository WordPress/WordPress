<?php

namespace Yoast\WP\SEO\Actions\Importing;

use Exception;
use Yoast\WP\SEO\Helpers\Aioseo_Helper;
use Yoast\WP\SEO\Helpers\Import_Cursor_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Sanitization_Helper;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Replacevar_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Provider_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Transformer_Service;

/**
 * Importing action interface.
 */
abstract class Abstract_Aioseo_Importing_Action implements Importing_Action_Interface {

	/**
	 * The plugin the class deals with.
	 *
	 * @var string
	 */
	public const PLUGIN = null;

	/**
	 * The type the class deals with.
	 *
	 * @var string
	 */
	public const TYPE = null;

	/**
	 * The AIOSEO helper.
	 *
	 * @var Aioseo_Helper
	 */
	protected $aioseo_helper;

	/**
	 * The import cursor helper.
	 *
	 * @var Import_Cursor_Helper
	 */
	protected $import_cursor;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * The sanitization helper.
	 *
	 * @var Sanitization_Helper
	 */
	protected $sanitization;

	/**
	 * The replacevar handler.
	 *
	 * @var Aioseo_Replacevar_Service
	 */
	protected $replacevar_handler;

	/**
	 * The robots provider service.
	 *
	 * @var Aioseo_Robots_Provider_Service
	 */
	protected $robots_provider;

	/**
	 * The robots transformer service.
	 *
	 * @var Aioseo_Robots_Transformer_Service
	 */
	protected $robots_transformer;

	/**
	 * Abstract_Aioseo_Importing_Action constructor.
	 *
	 * @param Import_Cursor_Helper              $import_cursor      The import cursor helper.
	 * @param Options_Helper                    $options            The options helper.
	 * @param Sanitization_Helper               $sanitization       The sanitization helper.
	 * @param Aioseo_Replacevar_Service         $replacevar_handler The replacevar handler.
	 * @param Aioseo_Robots_Provider_Service    $robots_provider    The robots provider service.
	 * @param Aioseo_Robots_Transformer_Service $robots_transformer The robots transfomer service.
	 */
	public function __construct(
		Import_Cursor_Helper $import_cursor,
		Options_Helper $options,
		Sanitization_Helper $sanitization,
		Aioseo_Replacevar_Service $replacevar_handler,
		Aioseo_Robots_Provider_Service $robots_provider,
		Aioseo_Robots_Transformer_Service $robots_transformer
	) {
		$this->import_cursor      = $import_cursor;
		$this->options            = $options;
		$this->sanitization       = $sanitization;
		$this->replacevar_handler = $replacevar_handler;
		$this->robots_provider    = $robots_provider;
		$this->robots_transformer = $robots_transformer;
	}

	/**
	 * Sets the AIOSEO helper.
	 *
	 * @required
	 *
	 * @param Aioseo_Helper $aioseo_helper The AIOSEO helper.
	 *
	 * @return void
	 */
	public function set_aioseo_helper( Aioseo_Helper $aioseo_helper ) {
		$this->aioseo_helper = $aioseo_helper;
	}

	/**
	 * The name of the plugin we import from.
	 *
	 * @return string The plugin we import from.
	 *
	 * @throws Exception If the PLUGIN constant is not set in the child class.
	 */
	public function get_plugin() {
		$class  = static::class;
		$plugin = $class::PLUGIN;

		if ( $plugin === null ) {
			throw new Exception( 'Importing action without explicit plugin' );
		}

		return $plugin;
	}

	/**
	 * The data type we import from the plugin.
	 *
	 * @return string The data type we import from the plugin.
	 *
	 * @throws Exception If the TYPE constant is not set in the child class.
	 */
	public function get_type() {
		$class = static::class;
		$type  = $class::TYPE;

		if ( $type === null ) {
			throw new Exception( 'Importing action without explicit type' );
		}

		return $type;
	}

	/**
	 * Can the current action import the data from plugin $plugin of type $type?
	 *
	 * @param string|null $plugin The plugin to import from.
	 * @param string|null $type   The type of data to import.
	 *
	 * @return bool True if this action can handle the combination of Plugin and Type.
	 *
	 * @throws Exception If the TYPE constant is not set in the child class.
	 */
	public function is_compatible_with( $plugin = null, $type = null ) {
		if ( empty( $plugin ) && empty( $type ) ) {
			return true;
		}

		if ( $plugin === $this->get_plugin() && empty( $type ) ) {
			return true;
		}

		if ( empty( $plugin ) && $type === $this->get_type() ) {
			return true;
		}

		if ( $plugin === $this->get_plugin() && $type === $this->get_type() ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets the completed id (to be used as a key for the importing_completed option).
	 *
	 * @return string The completed id.
	 */
	public function get_completed_id() {
		return $this->get_cursor_id();
	}

	/**
	 * Returns the stored state of completedness.
	 *
	 * @return int The stored state of completedness.
	 */
	public function get_completed() {
		$completed_id          = $this->get_completed_id();
		$importers_completions = $this->options->get( 'importing_completed', [] );

		return ( isset( $importers_completions[ $completed_id ] ) ) ? $importers_completions[ $completed_id ] : false;
	}

	/**
	 * Stores the current state of completedness.
	 *
	 * @param bool $completed Whether the importer is completed.
	 *
	 * @return void
	 */
	public function set_completed( $completed ) {
		$completed_id                  = $this->get_completed_id();
		$current_importers_completions = $this->options->get( 'importing_completed', [] );

		$current_importers_completions[ $completed_id ] = $completed;
		$this->options->set( 'importing_completed', $current_importers_completions );
	}

	/**
	 * Returns whether the importing action is enabled.
	 *
	 * @return bool True by default unless a child class overrides it.
	 */
	public function is_enabled() {
		return true;
	}

	/**
	 * Gets the cursor id.
	 *
	 * @return string The cursor id.
	 */
	protected function get_cursor_id() {
		return $this->get_plugin() . '_' . $this->get_type();
	}

	/**
	 * Minimally transforms data to be imported.
	 *
	 * @param string $meta_data The meta data to be imported.
	 *
	 * @return string The transformed meta data.
	 */
	public function simple_import( $meta_data ) {
		// Transform the replace vars into Yoast replace vars.
		$transformed_data = $this->replacevar_handler->transform( $meta_data );

		return $this->sanitization->sanitize_text_field( \html_entity_decode( $transformed_data ) );
	}

	/**
	 * Transforms URL to be imported.
	 *
	 * @param string $meta_data The meta data to be imported.
	 *
	 * @return string The transformed URL.
	 */
	public function url_import( $meta_data ) {
		// We put null as the allowed protocols here, to have the WP default allowed protocols, see https://developer.wordpress.org/reference/functions/wp_allowed_protocols.
		return $this->sanitization->sanitize_url( $meta_data, null );
	}
}
