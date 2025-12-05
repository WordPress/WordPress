<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class for generating a html select.
 */
class Yoast_Input_Select {

	/**
	 * The id attribute value.
	 *
	 * @var string
	 */
	private $select_id;

	/**
	 * The name attribute value.
	 *
	 * @var string
	 */
	private $select_name;

	/**
	 * Additional select attributes.
	 *
	 * @var array
	 */
	private $select_attributes = [];

	/**
	 * Array with the options to parse.
	 *
	 * @var array
	 */
	private $select_options;

	/**
	 * The current selected option.
	 *
	 * @var string
	 */
	private $selected_option;

	/**
	 * Constructor.
	 *
	 * @param string $select_id       ID for the select.
	 * @param string $select_name     Name for the select.
	 * @param array  $select_options  Array with the options to parse.
	 * @param string $selected_option The current selected option.
	 */
	public function __construct( $select_id, $select_name, array $select_options, $selected_option ) {
		$this->select_id       = $select_id;
		$this->select_name     = $select_name;
		$this->select_options  = $select_options;
		$this->selected_option = $selected_option;
	}

	/**
	 * Print the rendered view.
	 *
	 * @return void
	 */
	public function output_html() {
		// Extract it, because we want each value accessible via a variable instead of accessing it as an array.
		extract( $this->get_select_values() );

		require WPSEO_PATH . 'admin/views/form/select.php';
	}

	/**
	 * Return the rendered view.
	 *
	 * @return string
	 */
	public function get_html() {
		ob_start();

		$this->output_html();

		$rendered_output = ob_get_contents();
		ob_end_clean();

		return $rendered_output;
	}

	/**
	 * Add an attribute to the attributes property.
	 *
	 * @param string $attribute The name of the attribute to add.
	 * @param string $value     The value of the attribute.
	 *
	 * @return void
	 */
	public function add_attribute( $attribute, $value ) {
		$this->select_attributes[ $attribute ] = $value;
	}

	/**
	 * Return the set fields for the select.
	 *
	 * @return array
	 */
	private function get_select_values() {
		return [
			'id'         => $this->select_id,
			'name'       => $this->select_name,
			'attributes' => $this->get_attributes(),
			'options'    => $this->select_options,
			'selected'   => $this->selected_option,
		];
	}

	/**
	 * Return the attribute string, when there are attributes set.
	 *
	 * @return string
	 */
	private function get_attributes() {
		$attributes = $this->select_attributes;

		if ( ! empty( $attributes ) ) {
			array_walk( $attributes, [ $this, 'parse_attribute' ] );

			return implode( ' ', $attributes ) . ' ';
		}

		return '';
	}

	/**
	 * Get an attribute from the attributes.
	 *
	 * @param string $value     The value of the attribute.
	 * @param string $attribute The attribute to look for.
	 *
	 * @return void
	 */
	private function parse_attribute( &$value, $attribute ) {
		$value = sprintf( '%s="%s"', sanitize_key( $attribute ), esc_attr( $value ) );
	}
}
