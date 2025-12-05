<?php
namespace Elementor\Core\Base\Elements_Iteration_Actions;

use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base {
	/**
	 * The current document that the Base class instance was created from.
	 *
	 * @var \Elementor\Core\Document
	 */
	protected $document;

	/**
	 * Indicates if the methods are being triggered on page save or at render time (value will be either 'save' or 'render').
	 *
	 * @var string
	 */
	protected $mode = '';

	/**
	 * Is Action Needed.
	 *
	 * Runs only at runtime and used as a flag to determine if all methods should run on page render.
	 * If returns false, all methods will run only on page save.
	 * If returns true, all methods will run on both page render and on save.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return bool
	 */
	abstract public function is_action_needed();

	/**
	 * Unique Element Action.
	 *
	 * Will be triggered for each unique page element - section / column / widget unique type (heading, icon etc.).
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return void
	 */
	public function unique_element_action( Element_Base $element_data ) {}

	/**
	 * Element Action.
	 *
	 * Will be triggered for each page element - section / column / widget.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return void
	 */
	public function element_action( Element_Base $element_data ) {}

	/**
	 * After Elements Iteration.
	 *
	 * Will be triggered after all page elements iteration has ended.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return void
	 */
	public function after_elements_iteration() {}

	public function set_mode( $mode ) {
		$this->mode = $mode;
	}

	public function __construct( $document ) {
		$this->document = $document;
	}
}
