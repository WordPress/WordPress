<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor group control base.
 *
 * An abstract class for creating new group controls in the panel.
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Group_Control_Base implements Group_Control_Interface {

	/**
	 * Arguments.
	 *
	 * Holds all the group control arguments.
	 *
	 * @access private
	 *
	 * @var array Group control arguments.
	 */
	private $args = [];

	/**
	 * Options.
	 *
	 * Holds all the group control options.
	 *
	 * Currently supports only the popover options.
	 *
	 * @access private
	 *
	 * @var array Group control options.
	 */
	private $options;

	/**
	 * Get options.
	 *
	 * Retrieve group control options. If options are not set, it will initialize default options.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @param array $option Optional. Single option.
	 *
	 * @return mixed Group control options. If option parameter was not specified, it will
	 *               return an array of all the options. If single option specified, it will
	 *               return the option value or `null` if option does not exists.
	 */
	final public function get_options( $option = null ) {
		if ( null === $this->options ) {
			$this->init_options();
		}

		if ( $option ) {
			if ( isset( $this->options[ $option ] ) ) {
				return $this->options[ $option ];
			}

			return null;
		}

		return $this->options;
	}

	/**
	 * Add new controls to stack.
	 *
	 * Register multiple controls to allow the user to set/update data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Controls_Stack $element   The element stack.
	 * @param array          $user_args The control arguments defined by the user.
	 * @param array          $options   Optional. The element options. Default is
	 *                                  an empty array.
	 */
	final public function add_controls( Controls_Stack $element, array $user_args, array $options = [] ) {
		$this->init_args( $user_args );

		// Filter which controls to display
		$filtered_fields = $this->filter_fields();
		$filtered_fields = $this->prepare_fields( $filtered_fields );

		// For php < 7
		reset( $filtered_fields );

		if ( isset( $this->args['separator'] ) ) {
			$filtered_fields[ key( $filtered_fields ) ]['separator'] = $this->args['separator'];
		}

		$has_injection = false;

		if ( ! empty( $options['position'] ) ) {
			$has_injection = true;

			$element->start_injection( $options['position'] );

			unset( $options['position'] );
		}

		if ( $this->get_options( 'popover' ) ) {
			$this->start_popover( $element );
		}

		foreach ( $filtered_fields as $field_id => $field_args ) {
			// Add the global group args to the control
			$field_args = $this->add_group_args_to_field( $field_id, $field_args );

			// Register the control
			$id = $this->get_controls_prefix() . $field_id;

			if ( ! empty( $field_args['responsive'] ) ) {
				unset( $field_args['responsive'] );

				$element->add_responsive_control( $id, $field_args, $options );
			} else {
				$element->add_control( $id, $field_args, $options );
			}
		}

		if ( $this->get_options( 'popover' ) ) {
			$element->end_popover();
		}

		if ( $has_injection ) {
			$element->end_injection();
		}
	}

	/**
	 * Get arguments.
	 *
	 * Retrieve group control arguments.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Group control arguments.
	 */
	final public function get_args() {
		return $this->args;
	}

	/**
	 * Get fields.
	 *
	 * Retrieve group control fields.
	 *
	 * @since 1.2.2
	 * @access public
	 *
	 * @return array Control fields.
	 */
	final public function get_fields() {
		if ( null === static::$fields ) {
			static::$fields = $this->init_fields();
		}

		return static::$fields;
	}

	/**
	 * Get controls prefix.
	 *
	 * Retrieve the prefix of the group control, which is `{{ControlName}}_`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control prefix.
	 */
	public function get_controls_prefix() {
		return $this->args['name'] . '_';
	}

	/**
	 * Get group control classes.
	 *
	 * Retrieve the classes of the group control.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Group control classes.
	 */
	public function get_base_group_classes() {
		return 'elementor-group-control-' . static::get_type() . ' elementor-group-control';
	}

	/**
	 * Init fields.
	 *
	 * Initialize group control fields.
	 *
	 * @abstract
	 * @since 1.2.2
	 * @access protected
	 */
	abstract protected function init_fields();

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the group control. Used to return the
	 * default options while initializing the group control.
	 *
	 * @since 1.9.0
	 * @access protected
	 *
	 * @return array Default group control options.
	 */
	protected function get_default_options() {
		return [];
	}

	/**
	 * Get child default arguments.
	 *
	 * Retrieve the default arguments for all the child controls for a specific group
	 * control.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @return array Default arguments for all the child controls.
	 */
	protected function get_child_default_args() {
		return [];
	}

	/**
	 * Filter fields.
	 *
	 * Filter which controls to display, using `include`, `exclude` and the
	 * `condition` arguments.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @return array Control fields.
	 */
	protected function filter_fields() {
		$args = $this->get_args();

		$fields = $this->get_fields();

		if ( ! empty( $args['include'] ) ) {
			$fields = array_intersect_key( $fields, array_flip( $args['include'] ) );
		}

		if ( ! empty( $args['exclude'] ) ) {
			$fields = array_diff_key( $fields, array_flip( $args['exclude'] ) );
		}

		return $fields;
	}

	/**
	 * Add group arguments to field.
	 *
	 * Register field arguments to group control.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @param string $control_id Group control id.
	 * @param array  $field_args Group control field arguments.
	 *
	 * @return array
	 */
	protected function add_group_args_to_field( $control_id, $field_args ) {
		$args = $this->get_args();

		if ( ! empty( $args['tab'] ) ) {
			$field_args['tab'] = $args['tab'];
		}

		if ( ! empty( $args['section'] ) ) {
			$field_args['section'] = $args['section'];
		}

		$field_args['classes'] = $this->get_base_group_classes() . ' elementor-group-control-' . $control_id;

		foreach ( [ 'condition', 'conditions' ] as $condition_type ) {
			if ( ! empty( $args[ $condition_type ] ) ) {
				if ( empty( $field_args[ $condition_type ] ) ) {
					$field_args[ $condition_type ] = [];
				}

				$field_args[ $condition_type ] += $args[ $condition_type ];
			}
		}

		return $field_args;
	}

	/**
	 * Prepare fields.
	 *
	 * Process group control fields before adding them to `add_control()`.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @param array $fields Group control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$popover_options = $this->get_options( 'popover' );

		$popover_name = ! $popover_options ? null : $popover_options['starter_name'];

		foreach ( $fields as $field_key => &$field ) {
			if ( $popover_name ) {
				$field['condition'][ $popover_name . '!' ] = '';
			}

			if ( isset( $this->args['fields_options']['__all'] ) ) {
				$field = array_merge( $field, $this->args['fields_options']['__all'] );
			}

			if ( isset( $this->args['fields_options'][ $field_key ] ) ) {
				$field = array_merge( $field, $this->args['fields_options'][ $field_key ] );
			}

			if ( ! empty( $field['condition'] ) ) {
				$field = $this->add_condition_prefix( $field );
			}

			if ( ! empty( $field['conditions'] ) ) {
				$field['conditions'] = $this->add_conditions_prefix( $field['conditions'] );
			}

			if ( ! empty( $field['selectors'] ) ) {
				$field['selectors'] = $this->handle_selectors( $field['selectors'] );
			}

			if ( ! empty( $field['device_args'] ) ) {
				foreach ( $field['device_args'] as $device => $device_arg ) {
					if ( ! empty( $field['device_args'][ $device ]['condition'] ) ) {
						$field['device_args'][ $device ] = $this->add_condition_prefix( $field['device_args'][ $device ] );
					}

					if ( ! empty( $field['device_args'][ $device ]['conditions'] ) ) {
						$field['device_args'][ $device ]['conditions'] = $this->add_conditions_prefix( $field['device_args'][ $device ]['conditions'] );
					}

					if ( ! empty( $device_arg['selectors'] ) ) {
						$field['device_args'][ $device ]['selectors'] = $this->handle_selectors( $device_arg['selectors'] );
					}
				}
			}
		}

		return $fields;
	}

	/**
	 * Init options.
	 *
	 * Initializing group control options.
	 *
	 * @since 1.9.0
	 * @access private
	 */
	private function init_options() {
		$default_options = [
			'popover' => [
				'starter_name' => 'popover_toggle',
				'starter_value' => 'custom',
				'starter_title' => '',
			],
		];

		$this->options = array_replace_recursive( $default_options, $this->get_default_options() );
	}

	/**
	 * Init arguments.
	 *
	 * Initializing group control base class.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @param array $args Group control settings value.
	 */
	protected function init_args( $args ) {
		$this->args = array_merge( $this->get_default_args(), $this->get_child_default_args(), $args );

		if ( isset( $this->args['scheme'] ) ) {
			$this->args['global']['default'] = Plugin::$instance->kits_manager->convert_scheme_to_global( $this->args['scheme'] );
		}
	}

	/**
	 * Get default arguments.
	 *
	 * Retrieve the default arguments of the group control. Used to return the
	 * default arguments while initializing the group control.
	 *
	 * @since 1.2.2
	 * @access private
	 *
	 * @return array Control default arguments.
	 */
	private function get_default_args() {
		return [
			'default' => '',
			'selector' => '{{WRAPPER}}',
			'fields_options' => [],
		];
	}

	/**
	 * Add condition prefix.
	 *
	 * Used to add the group prefix to controls with conditions, to
	 * distinguish them from other controls with the same name.
	 *
	 * This way Elementor can apply condition logic to a specific control in a
	 * group control.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $field Group control field.
	 *
	 * @return array Group control field.
	 */
	private function add_condition_prefix( $field ) {
		$controls_prefix = $this->get_controls_prefix();

		$prefixed_condition_keys = array_map(
			function( $key ) use ( $controls_prefix ) {
				return $controls_prefix . $key;
			},
			array_keys( $field['condition'] )
		);

		$field['condition'] = array_combine(
			$prefixed_condition_keys,
			$field['condition']
		);

		return $field;
	}

	private function add_conditions_prefix( $conditions ) {
		$controls_prefix = $this->get_controls_prefix();

		foreach ( $conditions['terms'] as & $condition ) {
			if ( isset( $condition['terms'] ) ) {
				$condition = $this->add_conditions_prefix( $condition );

				continue;
			}

			$condition['name'] = $controls_prefix . $condition['name'];
		}

		return $conditions;
	}

	/**
	 * Handle selectors.
	 *
	 * Used to process the CSS selector of group control fields. When using
	 * group control, Elementor needs to apply the selector to different fields.
	 * This method handles the process.
	 *
	 * In addition, it handles selector values from other fields and process the
	 * css.
	 *
	 * @since 1.2.2
	 * @access private
	 *
	 * @param array $selectors An array of selectors to process.
	 *
	 * @return array Processed selectors.
	 */
	private function handle_selectors( $selectors ) {
		$args = $this->get_args();

		$selectors = array_combine(
			array_map(
				function( $key ) use ( $args ) {
					return str_replace( '{{SELECTOR}}', $args['selector'], $key );
				}, array_keys( $selectors )
			),
			$selectors
		);

		if ( ! $selectors ) {
			return $selectors;
		}

		$controls_prefix = $this->get_controls_prefix();

		foreach ( $selectors as &$selector ) {
			$selector = preg_replace_callback( '/{{\K(.*?)(?=}})/', function( $matches ) use ( $controls_prefix ) {
				$is_external_reference = false;

				return preg_replace_callback( '/[^ ]+?(?=\.)\./', function( $sub_matches ) use ( $controls_prefix, &$is_external_reference ) {
					$placeholder = $sub_matches[0];

					if ( 'external.' === $placeholder ) {
						$is_external_reference = true;

						return '';
					}

					if ( $is_external_reference ) {
						$is_external_reference = false;

						return $placeholder;
					}

					return $controls_prefix . $placeholder;
				}, $matches[1] );
			}, $selector );
		}

		return $selectors;
	}

	/**
	 * Start popover.
	 *
	 * Starts a group controls popover.
	 *
	 * @since 1.9.1
	 * @access private
	 * @param Controls_Stack $element Element.
	 */
	private function start_popover( Controls_Stack $element ) {
		$popover_options = $this->get_options( 'popover' );

		$settings = $this->get_args();

		if ( isset( $settings['global'] ) ) {
			if ( ! isset( $popover_options['settings']['global'] ) ) {
				$popover_options['settings']['global'] = [];
			}

			$popover_options['settings']['global'] = array_replace_recursive( $popover_options['settings']['global'], $settings['global'] );
		}

		if ( isset( $settings['label'] ) ) {
			$label = $settings['label'];
		} else {
			$label = $popover_options['starter_title'];
		}

		$control_params = [
			'type' => Controls_Manager::POPOVER_TOGGLE,
			'label' => $label,
			'return_value' => $popover_options['starter_value'],
		];

		if ( ! empty( $popover_options['settings'] ) ) {
			$control_params = array_replace_recursive( $control_params, $popover_options['settings'] );
		}

		foreach ( [ 'condition', 'conditions' ] as $key ) {
			if ( ! empty( $settings[ $key ] ) ) {
				$control_params[ $key ] = $settings[ $key ];
			}
		}

		$starter_name = $popover_options['starter_name'];

		if ( isset( $this->args['fields_options'][ $starter_name ] ) ) {
			$control_params = array_merge( $control_params, $this->args['fields_options'][ $starter_name ] );
		}

		$control_params['groupPrefix'] = $this->get_controls_prefix();

		$element->add_control( $this->get_controls_prefix() . $starter_name, $control_params );

		$element->start_popover();
	}
}
