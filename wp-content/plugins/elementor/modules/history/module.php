<?php
namespace Elementor\Modules\History;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor history module.
 *
 * Elementor history module handler class is responsible for registering and
 * managing Elementor history modules.
 *
 * @since 1.7.0
 */
class Module extends BaseModule {

	/**
	 * Get module name.
	 *
	 * Retrieve the history module name.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'history';
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function add_templates() {
		Plugin::$instance->common->add_template( __DIR__ . '/views/history-panel-template.php' );
		Plugin::$instance->common->add_template( __DIR__ . '/views/revisions-panel-template.php' );
	}

	/**
	 * History module constructor.
	 *
	 * Initializing Elementor history module.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/editor/init', [ $this, 'add_templates' ] );
	}
}
