<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor skins manager.
 *
 * Elementor skins manager handler class is responsible for registering and
 * initializing all the supported skins.
 *
 * @since 1.0.0
 */
class Skins_Manager {

	/**
	 * Registered Skins.
	 *
	 * Holds the list of all the registered skins for all the widgets.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array Registered skins.
	 */
	private $_skins = [];

	/**
	 * Add new skin.
	 *
	 * Register a single new skin for a widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Widget_Base $widget Elementor widget.
	 * @param Skin_Base   $skin   Elementor skin.
	 *
	 * @return true True if skin added.
	 */
	public function add_skin( Widget_Base $widget, Skin_Base $skin ) {
		$widget_name = $widget->get_name();

		if ( ! isset( $this->_skins[ $widget_name ] ) ) {
			$this->_skins[ $widget_name ] = [];
		}

		$this->_skins[ $widget_name ][ $skin->get_id() ] = $skin;

		return true;
	}

	/**
	 * Remove a skin.
	 *
	 * Unregister an existing skin from a widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Widget_Base $widget  Elementor widget.
	 * @param string      $skin_id Elementor skin ID.
	 *
	 * @return true|\WP_Error True if skin removed, `WP_Error` otherwise.
	 */
	public function remove_skin( Widget_Base $widget, $skin_id ) {
		$widget_name = $widget->get_name();

		if ( ! isset( $this->_skins[ $widget_name ][ $skin_id ] ) ) {
			return new \WP_Error( 'Cannot remove not-exists skin.' );
		}

		unset( $this->_skins[ $widget_name ][ $skin_id ] );

		return true;
	}

	/**
	 * Get skins.
	 *
	 * Retrieve all the skins assigned for a specific widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Widget_Base $widget Elementor widget.
	 *
	 * @return false|array Skins if the widget has skins, False otherwise.
	 */
	public function get_skins( Widget_Base $widget ) {
		$widget_name = $widget->get_name();

		if ( ! isset( $this->_skins[ $widget_name ] ) ) {
			return false;
		}

		return $this->_skins[ $widget_name ];
	}

	/**
	 * Skins manager constructor.
	 *
	 * Initializing Elementor skins manager by requiring the skin base class.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		require ELEMENTOR_PATH . 'includes/base/skin-base.php';
	}
}
