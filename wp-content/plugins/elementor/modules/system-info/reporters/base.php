<?php
namespace Elementor\Modules\System_Info\Reporters;

use Elementor\Modules\System_Info\Helpers\Model_Helper;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor base reporter.
 *
 * A base abstract class that provides the needed properties and methods to
 * manage and handle reporter in inheriting classes.
 *
 * @since 2.9.0
 * @abstract
 */
abstract class Base {

	/**
	 * Reporter properties.
	 *
	 * Holds the list of all the properties of the report.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array
	 */
	protected $_properties;

	/**
	 * Get report title.
	 *
	 * Retrieve the title of the report.
	 *
	 * @since 2.9.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_title();

	/**
	 * Get report fields.
	 *
	 * Retrieve the required fields for the report.
	 *
	 * @since 2.9.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_fields();

	/**
	 * Is report enabled.
	 *
	 * Whether the report is enabled.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @return bool Whether the report is enabled.
	 */
	public function is_enabled() {
		return true;
	}

	public function print_html() {
		foreach ( $this->get_report( 'html' ) as $field ) {
			$warning_class = ! empty( $field['warning'] ) ? ' class="elementor-warning"' : '';
			$log_label = ! empty( $field['label'] ) ? $field['label'] . ':' : '';
			?>
			<tr<?php Utils::print_unescaped_internal_string( $warning_class ); ?>>
				<td><?php Utils::print_unescaped_internal_string( $log_label ); ?></td>
				<td><?php Utils::print_unescaped_internal_string( $field['value'] ); ?></td>
				<td><?php
				if ( ! empty( $field['recommendation'] ) ) :
					Utils::print_unescaped_internal_string( $field['recommendation'] );
					endif;
				?></td>
			</tr>
			<?php
		}
	}

	public function print_html_label( $label ) {
		Utils::print_unescaped_internal_string( $label );
	}

	public function print_raw( $tabs_count ) {
		$indent = str_repeat( "\t", $tabs_count - 1 );

		$report = $this->get_report( 'raw' );

		echo PHP_EOL . $indent . '== ' . $this->get_title() . ' =='; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo PHP_EOL;

		foreach ( $report as $field_name => $field ) :
			$sub_indent = str_repeat( "\t", $tabs_count );

			$label = $field['label'];

			if ( ! empty( $label ) ) {
				$label .= ': ';
			}
			echo "{$sub_indent}{$label}{$field['value']}" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		endforeach;
	}

	/**
	 * Get report.
	 *
	 * Retrieve the report with all it's containing fields.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @return \WP_Error | array {
	 *    Report fields.
	 *
	 *    @type string $name Field name.
	 *    @type string $label Field label.
	 * }
	 */
	final public function get_report( $format = '' ) {
		$result = [];

		$format = ( empty( $format ) ) ? '' : $format . '_';

		foreach ( $this->get_fields() as $field_name => $field_label ) {
			$method = 'get_' . $format . $field_name;

			if ( ! method_exists( $this, $method ) ) {
				$method = 'get_' . $field_name;
				// fallback:
				if ( ! method_exists( $this, $method ) ) {
					return new \WP_Error( sprintf( "Getter method for the field '%s' wasn't found in %s.", $field_name, get_called_class() ) );
				}
			}

			$reporter_field = [
				'name' => $field_name,
				'label' => $field_label,
			];

			$reporter_field = array_merge( $reporter_field, $this->$method() );
			$result[ $field_name ] = $reporter_field;
		}

		return $result;
	}

	/**
	 * Get properties keys.
	 *
	 * Retrieve the keys of the properties.
	 *
	 * @since 2.9.0
	 * @access public
	 * @static
	 *
	 * @return array {
	 *    Property keys.
	 *
	 *    @type string $name   Property name.
	 *    @type string $fields Property fields.
	 * }
	 */
	public static function get_properties_keys() {
		return [
			'name',
			'format',
			'fields',
		];
	}

	/**
	 * Filter possible properties.
	 *
	 * Retrieve possible properties filtered by property keys.
	 *
	 * @since 2.9.0
	 * @access public
	 * @static
	 *
	 * @param array $properties Properties to filter.
	 *
	 * @return array Possible properties filtered by property keys.
	 */
	final public static function filter_possible_properties( $properties ) {
		return Model_Helper::filter_possible_properties( self::get_properties_keys(), $properties );
	}

	/**
	 * Set properties.
	 *
	 * Add/update properties to the report.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param array $key   Property key.
	 * @param array $value Optional. Property value. Default is `null`.
	 */
	final public function set_properties( $key, $value = null ) {
		if ( is_array( $key ) ) {
			$key = self::filter_possible_properties( $key );

			foreach ( $key as $sub_key => $sub_value ) {
				$this->set_properties( $sub_key, $sub_value );
			}

			return;
		}

		if ( ! in_array( $key, self::get_properties_keys(), true ) ) {
			return;
		}

		$this->_properties[ $key ] = $value;
	}

	/**
	 * Reporter base constructor.
	 *
	 * Initializing the reporter base class.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param array $properties Optional. Properties to filter. Default is `null`.
	 */
	public function __construct( $properties = null ) {
		$this->_properties = array_fill_keys( self::get_properties_keys(), null );

		if ( $properties ) {
			$this->set_properties( $properties, null );
		}
	}
}
