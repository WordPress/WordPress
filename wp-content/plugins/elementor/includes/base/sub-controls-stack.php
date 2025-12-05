<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor sub controls stack.
 *
 * An abstract class that can be used to divide a large ControlsStack into small parts.
 *
 * @abstract
 */
abstract class Sub_Controls_Stack {
	/**
	 * @var Controls_Stack
	 */
	protected $parent;

	/**
	 * Get self ID.
	 *
	 * Retrieve the self ID.
	 *
	 * @access public
	 * @abstract
	 */
	abstract public function get_id();

	/**
	 * Get self title.
	 *
	 * Retrieve the self title.
	 *
	 * @access public
	 * @abstract
	 */
	abstract public function get_title();

	/**
	 * Constructor.
	 *
	 * Initializing the base class by setting parent stack.
	 *
	 * @access public
	 * @param Controls_Stack $element_parent
	 */
	public function __construct( $element_parent ) {
		$this->parent = $element_parent;
	}

	/**
	 * Get control ID.
	 *
	 * Retrieve the control ID. Note that the sub controls stack may have a special prefix
	 * to distinguish them from regular controls, and from controls in other
	 * sub stack.
	 *
	 * By default do nothing, and return the original id.
	 *
	 * @access protected
	 *
	 * @param string $control_base_id Control base ID.
	 *
	 * @return string Control ID.
	 */
	protected function get_control_id( $control_base_id ) {
		return $control_base_id;
	}

	/**
	 * Add new control.
	 *
	 * Register a single control to allow the user to set/update data.
	 *
	 * @access public
	 *
	 * @param string $id   Control ID.
	 * @param array  $args Control arguments.
	 * @param array  $options
	 *
	 * @return bool True if added, False otherwise.
	 */
	public function add_control( $id, $args, $options = [] ) {
		return $this->parent->add_control( $this->get_control_id( $id ), $args, $options );
	}

	/**
	 * Update control.
	 *
	 * Change the value of an existing control.
	 *
	 * @access public
	 *
	 * @param string $id      Control ID.
	 * @param array  $args    Control arguments. Only the new fields you want to update.
	 * @param array  $options Optional. Some additional options.
	 */
	public function update_control( $id, $args, array $options = [] ) {
		$this->parent->update_control( $this->get_control_id( $id ), $args, $options );
	}

	/**
	 * Remove control.
	 *
	 * Unregister an existing control.
	 *
	 * @access public
	 *
	 * @param string $id Control ID.
	 */
	public function remove_control( $id ) {
		$this->parent->remove_control( $this->get_control_id( $id ) );
	}

	/**
	 * Add new group control.
	 *
	 * Register a set of related controls grouped together as a single unified
	 * control.
	 *
	 * @access public
	 *
	 * @param string $group_name Group control name.
	 * @param array  $args       Group control arguments. Default is an empty array.
	 * @param array  $options
	 */
	public function add_group_control( $group_name, $args, $options = [] ) {
		$args['name'] = $this->get_control_id( $args['name'] );
		$this->parent->add_group_control( $group_name, $args, $options );
	}

	/**
	 * Add new responsive control.
	 *
	 * Register a set of controls to allow editing based on user screen size.
	 *
	 * @access public
	 *
	 * @param string $id   Responsive control ID.
	 * @param array  $args Responsive control arguments.
	 * @param array  $options
	 */
	public function add_responsive_control( $id, $args, $options = [] ) {
		$this->parent->add_responsive_control( $this->get_control_id( $id ), $args, $options );
	}

	/**
	 * Update responsive control.
	 *
	 * Change the value of an existing responsive control.
	 *
	 * @access public
	 *
	 * @param string $id   Responsive control ID.
	 * @param array  $args Responsive control arguments.
	 */
	public function update_responsive_control( $id, $args ) {
		$this->parent->update_responsive_control( $this->get_control_id( $id ), $args );
	}

	/**
	 * Remove responsive control.
	 *
	 * Unregister an existing responsive control.
	 *
	 * @access public
	 *
	 * @param string $id Responsive control ID.
	 */
	public function remove_responsive_control( $id ) {
		$this->parent->remove_responsive_control( $this->get_control_id( $id ) );
	}

	/**
	 * Start controls section.
	 *
	 * Used to add a new section of controls to the stack.
	 *
	 * @access public
	 *
	 * @param string $id   Section ID.
	 * @param array  $args Section arguments.
	 */
	public function start_controls_section( $id, $args = [] ) {
		$this->parent->start_controls_section( $this->get_control_id( $id ), $args );
	}

	/**
	 * End controls section.
	 *
	 * Used to close an existing open controls section.
	 *
	 * @access public
	 */
	public function end_controls_section() {
		$this->parent->end_controls_section();
	}

	/**
	 * Start controls tabs.
	 *
	 * Used to add a new set of tabs inside a section.
	 *
	 * @access public
	 *
	 * @param string $id Control ID.
	 */
	public function start_controls_tabs( $id ) {
		$this->parent->start_controls_tabs( $this->get_control_id( $id ) );
	}

	public function start_controls_tab( $id, $args ) {
		$this->parent->start_controls_tab( $this->get_control_id( $id ), $args );
	}


	/**
	 * End controls tabs.
	 *
	 * Used to close an existing open controls tabs.
	 *
	 * @access public
	 */
	public function end_controls_tab() {
		$this->parent->end_controls_tab();
	}

	/**
	 * End controls tabs.
	 *
	 * Used to close an existing open controls tabs.
	 *
	 * @access public
	 */
	public function end_controls_tabs() {
		$this->parent->end_controls_tabs();
	}
}
