<?php

namespace Yoast\WP\SEO\Actions\Importing;

use Yoast\WP\SEO\Actions\Indexing\Limited_Indexing_Action_Interface;

interface Importing_Action_Interface extends Importing_Indexation_Action_Interface, Limited_Indexing_Action_Interface {

	/**
	 * Returns the name of the plugin we import from.
	 *
	 * @return string The plugin name.
	 */
	public function get_plugin();

	/**
	 * Returns the type of data we import.
	 *
	 * @return string The type of data.
	 */
	public function get_type();

	/**
	 * Whether or not this action is capable of importing given a specific plugin and type.
	 *
	 * @param string|null $plugin The name of the plugin being imported.
	 * @param string|null $type   The component of the plugin being imported.
	 *
	 * @return bool True if the action can import the given plugin's data of the given type.
	 */
	public function is_compatible_with( $plugin = null, $type = null );
}
