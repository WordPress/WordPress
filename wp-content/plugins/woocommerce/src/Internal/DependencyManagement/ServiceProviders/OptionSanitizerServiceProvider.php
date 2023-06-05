<?php
/**
 * OptionSanitizerServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\Settings\OptionSanitizer;

/**
 * Service provider for the OptionSanitizer class.
 */
class OptionSanitizerServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		OptionSanitizer::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( OptionSanitizer::class );
	}

}
