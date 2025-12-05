<?php
namespace Elementor\Core\Files\CSS;

use Elementor\Base_Data_Control;
use Elementor\Control_Repeater;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Files\Base as Base_File;
use Elementor\Core\DynamicTags\Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\Frontend\Performance;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Stylesheet;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor CSS file.
 *
 * Elementor CSS file handler class is responsible for generating CSS files.
 *
 * @since 1.2.0
 * @abstract
 */
abstract class Base extends Base_File {

	/**
	 * Elementor CSS file generated status.
	 *
	 * The parsing result after generating CSS file.
	 */
	const CSS_STATUS_FILE = 'file';

	/**
	 * Elementor inline CSS status.
	 *
	 * The parsing result after generating inline CSS.
	 */
	const CSS_STATUS_INLINE = 'inline';

	/**
	 * Elementor CSS empty status.
	 *
	 * The parsing result when an empty CSS returned.
	 */
	const CSS_STATUS_EMPTY = 'empty';

	/**
	 * Fonts.
	 *
	 * Holds the list of fonts.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $fonts = [];

	private $icons_fonts = [];

	private $dynamic_elements_ids = [];

	private $preserved_dynamic_style_values = [];

	/**
	 * Stylesheet object.
	 *
	 * Holds the CSS file stylesheet instance.
	 *
	 * @access protected
	 *
	 * @var Stylesheet
	 */
	protected $stylesheet_obj;

	/**
	 * Printed.
	 *
	 * Holds the list of printed files.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	private static $printed = [];

	/**
	 * Get CSS file name.
	 *
	 * Retrieve the CSS file name.
	 *
	 * @since 1.6.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_name();

	protected function is_global_parsing_supported() {
		return false;
	}

	/**
	 * Use external file.
	 *
	 * Whether to use external CSS file of not. When there are new schemes or settings
	 * updates.
	 *
	 * @since 1.9.0
	 * @access protected
	 *
	 * @return bool True if the CSS requires an update, False otherwise.
	 */
	protected function use_external_file() {
		return 'internal' !== get_option( 'elementor_css_print_method' );
	}

	/**
	 * Update the CSS file.
	 *
	 * Delete old CSS, parse the CSS, save the new file and update the database.
	 *
	 * This method also sets the CSS status to be used later on in the render posses.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function update() {
		$this->update_file();

		$meta = $this->get_meta();

		$meta['time'] = time();

		$content = $this->get_content();

		if ( empty( $content ) ) {
			$meta['status'] = self::CSS_STATUS_EMPTY;
			$meta['css'] = '';
		} else {
			$use_external_file = $this->use_external_file();

			if ( $use_external_file ) {
				$meta['status'] = self::CSS_STATUS_FILE;
			} else {
				$meta['status'] = self::CSS_STATUS_INLINE;
				$meta['css'] = $content;
			}
		}

		$meta['dynamic_elements_ids'] = $this->dynamic_elements_ids;

		$this->update_meta( $meta );
	}

	/**
	 * @since 2.1.0
	 * @access public
	 */
	public function write() {
		if ( $this->use_external_file() ) {
			parent::write();
		}
	}

	/**
	 * @since 3.0.0
	 * @access public
	 */
	public function delete() {
		if ( $this->use_external_file() ) {
			parent::delete();
		} else {
			$this->delete_meta();
		}
	}

	/**
	 * Get Responsive Control Duplication Mode
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	protected function get_responsive_control_duplication_mode() {
		return 'on';
	}

	/**
	 * Enqueue CSS.
	 *
	 * Either enqueue the CSS file in Elementor or add inline style.
	 *
	 * This method is also responsible for loading the fonts.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function enqueue() {
		$handle_id = $this->get_file_handle_id();

		if ( isset( self::$printed[ $handle_id ] ) ) {
			return;
		}

		self::$printed[ $handle_id ] = true;

		$meta = $this->get_meta();

		if ( self::CSS_STATUS_EMPTY === $meta['status'] ) {
			return;
		}

		/**
		 * Enqueue CSS file.
		 *
		 * Fires before enqueuing a CSS file.
		 *
		 * @param Base $this The current CSS file.
		 */
		do_action( 'elementor/css-file/before_enqueue', $this );

		// First time after clear cache and etc.
		if ( '' === $meta['status'] || $this->is_update_required() ) {
			$this->update();

			$meta = $this->get_meta();
		}

		if ( self::CSS_STATUS_INLINE === $meta['status'] ) {
			$dep = $this->get_inline_dependency();
			// If the dependency has already been printed ( like a template in footer )
			if ( wp_styles()->query( $dep, 'done' ) ) {
				printf( '<style id="%1$s">%2$s</style>', $this->get_file_handle_id(), $meta['css'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				wp_add_inline_style( $dep, $meta['css'] );
			}
		} elseif ( self::CSS_STATUS_FILE === $meta['status'] ) { // Re-check if it's not empty after CSS update.
			wp_enqueue_style( $this->get_file_handle_id(), $this->get_url(), $this->get_enqueue_dependencies(), null );
		}

		// Handle fonts.
		if ( ! empty( $meta['fonts'] ) ) {
			foreach ( $meta['fonts'] as $font ) {
				Plugin::$instance->frontend->enqueue_font( $font );
			}
		}

		if ( ! empty( $meta['icons'] ) ) {
			$icons_types = Icons_Manager::get_icon_manager_tabs();
			foreach ( $meta['icons'] as $icon_font ) {
				if ( ! isset( $icons_types[ $icon_font ] ) ) {
					continue;
				}
				Plugin::$instance->frontend->enqueue_font( $icon_font );
			}
		}

		$name = $this->get_name();

		/**
		 * Enqueue CSS file.
		 *
		 * Fires when CSS file is enqueued on Elementor.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
		 *
		 * @since 2.0.0
		 *
		 * @param Base $this The current CSS file.
		 */
		do_action( "elementor/css-file/{$name}/enqueue", $this );

		/**
		 * Enqueue CSS file.
		 *
		 * Fires after enqueuing a CSS file.
		 *
		 * @param Base $this The current CSS file.
		 */
		do_action( 'elementor/css-file/after_enqueue', $this );
	}

	/**
	 * Print CSS.
	 *
	 * Output the final CSS inside the `<style>` tags and all the frontend fonts in
	 * use.
	 *
	 * @since 1.9.4
	 * @access public
	 */
	public function print_css() {
		echo '<style>' . $this->get_content() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		Plugin::$instance->frontend->print_fonts_links();
	}

	/**
	 * Add control rules.
	 *
	 * Parse the CSS for all the elements inside any given control.
	 *
	 * This method recursively renders the CSS for all the selectors in the control.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array    $control        The controls.
	 * @param array    $controls_stack The controls stack.
	 * @param callable $value_callback Callback function for the value.
	 * @param array    $placeholders   Placeholders.
	 * @param array    $replacements   Replacements.
	 * @param array    $values         Global Values.
	 */
	public function add_control_rules( array $control, array $controls_stack, callable $value_callback, array $placeholders, array $replacements, array $values = [] ) {
		if ( empty( $control['selectors'] ) ) {
			return;
		}

		$control_global_key = $control['name'];

		if ( ! empty( $control['groupType'] ) ) {
			$control_global_key = $control['groupPrefix'] . $control['groupType'];
		}

		$global_values = [];
		$global_key = '';

		if ( ! empty( $values['__globals__'] ) ) {
			$global_values = $values['__globals__'];
		}

		if ( ! empty( $global_values[ $control_global_key ] ) ) {
			$global_key = $global_values[ $control_global_key ];
		}

		if ( ! $global_key ) {
			$value = call_user_func( $value_callback, $control );

			if ( null === $value ) {
				return;
			}
		}

		$stylesheet = $this->get_stylesheet();

		$control = apply_filters( 'elementor/files/css/selectors', $control, $value ?? [], $this );

		foreach ( $control['selectors'] as $selector => $css_property ) {
			$output_css_property = '';

			if ( $global_key ) {
				$selector_global_value = $this->get_selector_global_value( $control, $global_key );

				if ( $selector_global_value ) {
					$output_css_property = preg_replace( '/(:)[^;]+(;?)/', '$1' . $selector_global_value . '$2', $css_property );
				}
			} else {
				try {
					if ( $this->unit_has_custom_selector( $control, $value ) ) {
						$css_property = $control['unit_selectors_dictionary'][ $value['unit'] ];
					}

					$output_css_property = preg_replace_callback( '/{{(?:([^.}]+)\.)?([^}| ]*)(?: *\|\| *(?:([^.}]+)\.)?([^}| ]*) *)*}}/', function( $matches ) use ( $control, $value_callback, $controls_stack, $value, $css_property ) {
						$external_control_missing = $matches[1] && ! isset( $controls_stack[ $matches[1] ] );

						$parsed_value = '';

						$value = apply_filters( 'elementor/files/css/property', $value, $css_property, $matches, $control );

						if ( ! $external_control_missing ) {
							$parsed_value = $this->parse_property_placeholder( $control, $value, $controls_stack, $value_callback, $matches[2], $matches[1] );
						}

						if ( '' === $parsed_value ) {
							if ( isset( $matches[4] ) ) {
								$parsed_value = $matches[4];

								$is_string_value = preg_match( '/^([\'"])(.*)\1$/', $parsed_value, $string_matches );

								if ( $is_string_value ) {
									$parsed_value = $string_matches[2];
								} elseif ( ! is_numeric( $parsed_value ) ) {
									if ( $matches[3] && ! isset( $controls_stack[ $matches[3] ] ) ) {
										return '';
									}

									$parsed_value = $this->parse_property_placeholder( $control, $value, $controls_stack, $value_callback, $matches[4], $matches[3] );
								}
							}

							if ( '' === $parsed_value ) {
								if ( $external_control_missing ) {
									return '';
								}

								throw new \Exception();
							}
						}

						if ( '__EMPTY__' === $parsed_value ) {
							$parsed_value = '';
						}

						return $parsed_value;
					}, $css_property );
				} catch ( \Exception $e ) {
					return;
				}
			}

			if ( ! $output_css_property ) {
				continue;
			}

			$device_pattern = '/^(?:\([^\)]+\)){1,2}/';

			preg_match( $device_pattern, $selector, $device_rules );

			$query = [];

			if ( $device_rules ) {
				$selector = preg_replace( $device_pattern, '', $selector );

				preg_match_all( '/\(([^)]+)\)/', $device_rules[0], $pure_device_rules );

				$pure_device_rules = $pure_device_rules[1];

				foreach ( $pure_device_rules as $device_rule ) {
					if ( Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP === $device_rule ) {
						continue;
					}

					$device = preg_replace( '/\+$/', '', $device_rule );

					$endpoint = $device === $device_rule ? 'max' : 'min';

					$query[ $endpoint ] = $device;
				}
			}

			$parsed_selector = str_replace( $placeholders, $replacements, $selector );

			if ( ! $query && ! empty( $control['responsive'] ) ) {
				$query = array_intersect_key( $control['responsive'], array_flip( [ 'min', 'max' ] ) );

				if ( ! empty( $query['max'] ) && Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP === $query['max'] ) {
					unset( $query['max'] );
				}
			}

			$stylesheet->add_rules( $parsed_selector, $output_css_property, $query );
		}
	}

	protected function unit_has_custom_selector( $control, $value ) {
		return isset( $control['unit_selectors_dictionary'] ) && isset( $control['unit_selectors_dictionary'][ $value['unit'] ] );
	}

	/**
	 * @param array    $control
	 * @param mixed    $value
	 * @param array    $controls_stack
	 * @param callable $value_callback
	 * @param string   $placeholder
	 * @param string   $parser_control_name
	 *
	 * @return string
	 */
	public function parse_property_placeholder( array $control, $value, array $controls_stack, $value_callback, $placeholder, $parser_control_name = null ) {
		if ( $parser_control_name ) {
			// If both the processed control and the control name found in the placeholder are responsive
			if ( ! empty( $control['responsive'] ) && ! empty( $controls_stack[ $parser_control_name ]['responsive'] ) ) {
				$device_suffix = Controls_Manager::get_responsive_control_device_suffix( $control );

				$control = $controls_stack[ $parser_control_name . $device_suffix ] ?? $controls_stack[ $parser_control_name ];
			} else {
				$control = $controls_stack[ $parser_control_name ];
			}

			$value = call_user_func( $value_callback, $control );
		}

		// If the control value is empty, check for global default. `0` (integer, string) are falsy but are valid values.
		if ( empty( $value ) && '0' !== $value && 0 !== $value ) {
			$value = $this->get_control_global_default_value( $control );
		}

		if ( Controls_Manager::FONT === $control['type'] ) {
			$this->add_font( $value );
		}

		/** @var Base_Data_Control $control_obj */
		$control_obj = Plugin::$instance->controls_manager->get_control( $control['type'] );

		return (string) $control_obj->get_style_value( $placeholder, $value, $control );
	}

	/**
	 * Get the fonts.
	 *
	 * Retrieve the list of fonts.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return array Fonts.
	 */
	public function get_fonts() {
		return $this->fonts;
	}

	/**
	 * Get stylesheet.
	 *
	 * Retrieve the CSS file stylesheet instance.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Stylesheet The stylesheet object.
	 */
	public function get_stylesheet() {
		if ( ! $this->stylesheet_obj ) {
			$this->init_stylesheet();
		}

		return $this->stylesheet_obj;
	}

	/**
	 * Add controls stack style rules.
	 *
	 * Parse the CSS for all the elements inside any given controls stack.
	 *
	 * This method recursively renders the CSS for all the child elements in the stack.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param Controls_Stack $controls_stack The controls stack.
	 * @param array          $controls       Controls array.
	 * @param array          $values         Values array.
	 * @param array          $placeholders   Placeholders.
	 * @param array          $replacements   Replacements.
	 * @param array          $all_controls   All controls.
	 */
	public function add_controls_stack_style_rules( Controls_Stack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, ?array $all_controls = null ) {
		if ( ! $all_controls ) {
			$all_controls = $controls_stack->get_controls();
		}

		$parsed_dynamic_settings = $controls_stack->parse_dynamic_settings( $values, $controls );

		foreach ( $controls as $control ) {
			if ( ! empty( $control['style_fields'] ) ) {
				$this->add_repeater_control_style_rules( $controls_stack, $control, $values[ $control['name'] ], $placeholders, $replacements );
			}

			if ( ! empty( $control[ Manager::DYNAMIC_SETTING_KEY ][ $control['name'] ] ) ) {
				$this->add_dynamic_control_style_rules( $control, $control[ Manager::DYNAMIC_SETTING_KEY ][ $control['name'] ] );
			}

			if ( Controls_Manager::ICONS === $control['type'] ) {
				$this->icons_fonts[] = $values[ $control['name'] ]['library'];
			}

			if ( ! empty( $parsed_dynamic_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control['name'] ] ) ) {
				// Dynamic CSS should not be added to the CSS files.
				// Instead it's handled by \Elementor\Core\DynamicTags\Dynamic_CSS
				// and printed in a style tag.
				$should_preserve_value = isset( $control['control_type'] ) && 'content' === $control['control_type'];
				if ( $should_preserve_value ) {
					$this->preserved_dynamic_style_values[ $control['name'] ] = $parsed_dynamic_settings[ $control['name'] ];
				}

				unset( $parsed_dynamic_settings[ $control['name'] ] );

				$this->dynamic_elements_ids[] = $controls_stack->get_id();

				continue;
			}

			if ( empty( $control['selectors'] ) ) {
				continue;
			}

			$this->add_control_style_rules( $control, $parsed_dynamic_settings, $all_controls, $placeholders, $replacements );
		}
	}

	/**
	 * Get file handle ID.
	 *
	 * Retrieve the file handle ID.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @abstract
	 *
	 * @return string CSS file handle ID.
	 */
	abstract protected function get_file_handle_id();

	/**
	 * Render CSS.
	 *
	 * Parse the CSS.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @abstract
	 */
	abstract protected function render_css();

	protected function get_default_meta() {
		return array_merge( parent::get_default_meta(), [
			'fonts' => array_unique( $this->fonts ),
			'icons' => array_unique( $this->icons_fonts ),
			'dynamic_elements_ids' => [],
			'status' => '',
		] );
	}

	/**
	 * Get enqueue dependencies.
	 *
	 * Retrieve the name of the stylesheet used by `wp_enqueue_style()`.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return array Name of the stylesheet.
	 */
	protected function get_enqueue_dependencies() {
		return [];
	}

	/**
	 * Get inline dependency.
	 *
	 * Retrieve the name of the stylesheet used by `wp_add_inline_style()`.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return string Name of the stylesheet.
	 */
	protected function get_inline_dependency() {
		return '';
	}

	/**
	 * Is update required.
	 *
	 * Whether the CSS requires an update. When there are new schemes or settings
	 * updates.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return bool True if the CSS requires an update, False otherwise.
	 */
	protected function is_update_required() {
		return false;
	}

	/**
	 * Parse CSS.
	 *
	 * Parsing the CSS file.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function parse_content() {
		Performance::set_use_style_controls( true );

		$initial_responsive_controls_duplication_mode = Plugin::$instance->breakpoints->get_responsive_control_duplication_mode();

		Plugin::$instance->breakpoints->set_responsive_control_duplication_mode( $this->get_responsive_control_duplication_mode() );

		$this->render_css();

		$name = $this->get_name();

		/**
		 * Parse CSS file.
		 *
		 * Fires when CSS file is parsed on Elementor.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
		 *
		 * @since 2.0.0
		 *
		 * @param Base $this The current CSS file.
		 */
		do_action( "elementor/css-file/{$name}/parse", $this );

		Plugin::$instance->breakpoints->set_responsive_control_duplication_mode( $initial_responsive_controls_duplication_mode );

		Performance::set_use_style_controls( false );

		return $this->get_stylesheet()->__toString();
	}

	/**
	 * Add control style rules.
	 *
	 * Register new style rules for the control.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param array $control      The control.
	 * @param array $values       Values array.
	 * @param array $controls     The controls stack.
	 * @param array $placeholders Placeholders.
	 * @param array $replacements Replacements.
	 */
	protected function add_control_style_rules( array $control, array $values, array $controls, array $placeholders, array $replacements ) {
		$this->add_control_rules(
			$control, $controls, function( $control ) use ( $values ) {
				return $this->get_style_control_value( $control, $values );
			}, $placeholders, $replacements, $values
		);
	}

	/**
	 * Get Control Global Default Value
	 *
	 * If the control has a global default value, and the corresponding global default setting is enabled, this method
	 * fetches and returns the global default value. Otherwise, it returns null.
	 *
	 * @since 3.7.0
	 * @access private
	 *
	 * @param $control
	 * @return string|null
	 */
	private function get_control_global_default_value( $control ) {
		if ( empty( $control['global']['default'] ) ) {
			return null;
		}

		// If the control value is empty, and the control has a global default set, fetch the global value and use it.
		$global_enabled = false;

		if ( 'color' === $control['type'] ) {
			$global_enabled = Plugin::$instance->kits_manager->is_custom_colors_enabled();
		} elseif ( isset( $control['groupType'] ) && 'typography' === $control['groupType'] ) {
			$global_enabled = Plugin::$instance->kits_manager->is_custom_typography_enabled();
		}

		$value = null;

		// Only apply the global default if Global Colors are enabled.
		if ( $global_enabled ) {
			$value = $this->get_selector_global_value( $control, $control['global']['default'] );
		}

		return $value;
	}

	/**
	 * Get style control value.
	 *
	 * Retrieve the value of the style control for any give control and values.
	 *
	 * It will retrieve the control name and return the style value.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param array $control The control.
	 * @param array $values  Values array.
	 *
	 * @return mixed Style control value.
	 */
	private function get_style_control_value( array $control, array $values ) {
		if ( ! empty( $values['__globals__'][ $control['name'] ] ) ) {
			// When the control itself has no global value, but it refers to another control global value
			return $this->get_selector_global_value( $control, $values['__globals__'][ $control['name'] ] );
		}

		$value = isset( $values[ $control['name'] ] )
			? $values[ $control['name'] ]
			: $this->preserved_dynamic_style_values[ $control['name'] ] ?? null;

		if ( isset( $control['selectors_dictionary'][ $value ] ) ) {
			$value = $control['selectors_dictionary'][ $value ];
		}

		if ( ! is_numeric( $value ) && ! is_float( $value ) && empty( $value ) ) {
			return null;
		}

		return $value;
	}

	/**
	 * Init stylesheet.
	 *
	 * Initialize CSS file stylesheet by creating a new `Stylesheet` object and register new
	 * breakpoints for the stylesheet.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function init_stylesheet() {
		$this->stylesheet_obj = new Stylesheet();

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint ) {
			$this->stylesheet_obj->add_device( $breakpoint_name, $breakpoint->get_value() );
		}
	}

	/**
	 * Add repeater control style rules.
	 *
	 * Register new style rules for the repeater control.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param Controls_Stack $controls_stack   The control stack.
	 * @param array          $repeater_control The repeater control.
	 * @param array          $repeater_values  Repeater values array.
	 * @param array          $placeholders     Placeholders.
	 * @param array          $replacements     Replacements.
	 */
	protected function add_repeater_control_style_rules( Controls_Stack $controls_stack, array $repeater_control, array $repeater_values, array $placeholders, array $replacements ) {
		$placeholders = array_merge( $placeholders, [ '{{CURRENT_ITEM}}' ] );

		foreach ( $repeater_control['style_fields'] as $index => $item ) {
			$this->add_controls_stack_style_rules(
				$controls_stack,
				$item,
				$repeater_values[ $index ],
				$placeholders,
				array_merge( $replacements, [ '.elementor-repeater-item-' . $repeater_values[ $index ]['_id'] ] ),
				$repeater_control['fields']
			);
		}
	}

	/**
	 * Add dynamic control style rules.
	 *
	 * Register new style rules for the dynamic control.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param array  $control The control.
	 * @param string $value   The value.
	 */
	protected function add_dynamic_control_style_rules( array $control, $value ) {
		Plugin::$instance->dynamic_tags->parse_tags_text( $value, $control, function( $id, $name, $settings ) {
			$tag = Plugin::$instance->dynamic_tags->create_tag( $id, $name, $settings );

			if ( ! $tag instanceof Tag ) {
				return;
			}

			$this->add_controls_stack_style_rules( $tag, $this->get_style_controls( $tag ), $tag->get_active_settings(), [ '{{WRAPPER}}' ], [ '#elementor-tag-' . $id ] );
		} );
	}

	private function get_selector_global_value( $control, $global_key ) {
		$data = Plugin::$instance->data_manager_v2->run( $global_key );

		if ( empty( $data['value'] ) ) {
			return null;
		}

		$global_args = explode( '?id=', $global_key );

		$id = $global_args[1];

		if ( ! empty( $control['groupType'] ) ) {
			$strings_to_replace = [ $control['groupPrefix'] ];

			$active_breakpoint_keys = array_keys( Plugin::$instance->breakpoints->get_active_breakpoints() );

			foreach ( $active_breakpoint_keys as $breakpoint ) {
				$strings_to_replace[] = '_' . $breakpoint;
			}

			$property_name = str_replace( $strings_to_replace, '', $control['name'] );

			// TODO: This check won't retrieve the proper answer for array values (multiple controls).
			if ( empty( $data['value'][ Global_Typography::TYPOGRAPHY_GROUP_PREFIX . $property_name ] ) ) {
				return null;
			}

			$property_name = str_replace( '_', '-', $property_name );

			$value = "var( --e-global-$control[groupType]-$id-$property_name )";

			if ( $control['groupPrefix'] . 'font_family' === $control['name'] ) {
				$default_generic_fonts = Plugin::$instance->kits_manager->get_current_settings( 'default_generic_fonts' );

				if ( $default_generic_fonts ) {
					$value .= ", $default_generic_fonts";
				}
			}
		} else {
			$value = "var( --e-global-$control[type]-$id )";
		}

		return $value;
	}

	final protected function get_active_controls( Controls_Stack $controls_stack, ?array $controls = null, ?array $settings = null ) {
		if ( ! $controls ) {
			$controls = $controls_stack->get_controls();
		}

		if ( ! $settings ) {
			$settings = $controls_stack->get_controls_settings();
		}

		if ( $this->is_global_parsing_supported() ) {
			$settings = $this->parse_global_settings( $settings, $controls );
		}

		$active_controls = array_reduce(
			array_keys( $controls ), function( $active_controls, $control_key ) use ( $controls_stack, $controls, $settings ) {
				$control = $controls[ $control_key ];

				if ( $controls_stack->is_control_visible( $control, $settings, $controls ) ) {
					$active_controls[ $control_key ] = $control;
				}

				return $active_controls;
			}, []
		);

		return $active_controls;
	}

	final public function get_style_controls( Controls_Stack $controls_stack, ?array $controls = null, ?array $settings = null ) {
		$controls = $this->get_active_controls( $controls_stack, $controls, $settings );

		$style_controls = [];

		foreach ( $controls as $control_name => $control ) {
			$control_obj = Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( ! $control_obj instanceof Base_Data_Control ) {
				continue;
			}

			$control = array_merge( $control_obj->get_settings(), $control );

			if ( $control_obj instanceof Control_Repeater ) {
				$style_fields = [];

				foreach ( $controls_stack->get_settings( $control_name ) as $item ) {
					$style_fields[] = $this->get_style_controls( $controls_stack, $control['fields'], $item );
				}

				$control['style_fields'] = $style_fields;
			}

			if ( ! empty( $control['selectors'] ) || ! empty( $control['dynamic'] ) || $this->is_global_control( $controls_stack, $control_name, $controls ) || ! empty( $control['style_fields'] ) ) {
				$style_controls[ $control_name ] = $control;
			}
		}

		return $style_controls;
	}

	private function parse_global_settings( array $settings, array $controls ) {
		foreach ( $controls as $control ) {
			$control_name = $control['name'];
			$control_obj = Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( ! $control_obj instanceof Base_Data_Control ) {
				continue;
			}

			if ( $control_obj instanceof Control_Repeater ) {
				foreach ( $settings[ $control_name ] as & $field ) {
					$field = $this->parse_global_settings( $field, $control['fields'] );
				}

				continue;
			}

			if ( empty( $control['global']['active'] ) ) {
				continue;
			}

			if ( empty( $settings['__globals__'][ $control_name ] ) ) {
				continue;
			}

			$settings[ $control_name ] = 'global';
		}

		return $settings;
	}

	private function is_global_control( Controls_Stack $controls_stack, $control_name, $controls ) {
		$control = $controls[ $control_name ];

		$control_global_key = $control_name;

		if ( ! empty( $control['groupType'] ) ) {
			$control_global_key = $control['groupPrefix'] . $control['groupType'];
		}

		if ( empty( $controls[ $control_global_key ]['global']['active'] ) ) {
			return false;
		}

		$globals = $controls_stack->get_settings( '__globals__' );

		return ! empty( $globals[ $control_global_key ] );
	}

	public function add_font( $font ) {
		if ( ! in_array( $font, $this->fonts, true ) ) {
			$this->fonts[] = $font;
		}
	}
}
