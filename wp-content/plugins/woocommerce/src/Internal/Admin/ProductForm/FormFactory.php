<?php
/**
 * WooCommerce Product Form Factory
 *
 * @package Woocommerce ProductForm
 */

namespace Automattic\WooCommerce\Internal\Admin\ProductForm;

use WP_Error;

/**
 * Factory that contains logic for the WooCommerce Product Form.
 */
class FormFactory {
	/**
	 * Class instance.
	 *
	 * @var Form instance
	 */
	protected static $instance = null;

	/**
	 * Store form fields.
	 *
	 * @var array
	 */
	protected static $form_fields = array();

	/**
	 * Store form cards.
	 *
	 * @var array
	 */
	protected static $form_subsections = array();

	/**
	 * Store form sections.
	 *
	 * @var array
	 */
	protected static $form_sections = array();

	/**
	 * Store form tabs.
	 *
	 * @var array
	 */
	protected static $form_tabs = array();

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init.
	 */
	public function init() {    }

	/**
	 * Adds a field to the product form.
	 *
	 * @param string $id Field id.
	 * @param string $plugin_id Plugin id.
	 * @param array  $args Array containing the necessary arguments.
	 *     $args = array(
	 *       'type'            => (string) Field type. Required.
	 *       'section'         => (string) Field location. Required.
	 *       'order'           => (int) Field order.
	 *       'properties'      => (array) Field properties.
	 *       'name'            => (string) Field name.
	 *     ).
	 * @return Field|WP_Error New field or WP_Error.
	 */
	public static function add_field( $id, $plugin_id, $args ) {
		$new_field = self::create_item( 'field', 'Field', $id, $plugin_id, $args );
		if ( is_wp_error( $new_field ) ) {
			return $new_field;
		}
		self::$form_fields[ $id ] = $new_field;
		return $new_field;
	}

	/**
	 * Adds a Subsection to the product form.
	 *
	 * @param string $id Subsection id.
	 * @param string $plugin_id Plugin id.
	 * @param array  $args Array containing the necessary arguments.
	 * @return Subsection|WP_Error New subsection or WP_Error.
	 */
	public static function add_subsection( $id, $plugin_id, $args = array() ) {
		$new_subsection = self::create_item( 'subsection', 'Subsection', $id, $plugin_id, $args );
		if ( is_wp_error( $new_subsection ) ) {
			return $new_subsection;
		}
		self::$form_subsections[ $id ] = $new_subsection;
		return $new_subsection;
	}

	/**
	 * Adds a section to the product form.
	 *
	 * @param string $id Card id.
	 * @param string $plugin_id Plugin id.
	 * @param array  $args Array containing the necessary arguments.
	 * @return Section|WP_Error New section or WP_Error.
	 */
	public static function add_section( $id, $plugin_id, $args ) {
		$new_section = self::create_item( 'section', 'Section', $id, $plugin_id, $args );
		if ( is_wp_error( $new_section ) ) {
			return $new_section;
		}
		self::$form_sections[ $id ] = $new_section;
		return $new_section;
	}

	/**
	 * Adds a tab to the product form.
	 *
	 * @param string $id Card id.
	 * @param string $plugin_id Plugin id.
	 * @param array  $args Array containing the necessary arguments.
	 * @return Tab|WP_Error New section or WP_Error.
	 */
	public static function add_tab( $id, $plugin_id, $args ) {
		$new_tab = self::create_item( 'tab', 'Tab', $id, $plugin_id, $args );
		if ( is_wp_error( $new_tab ) ) {
			return $new_tab;
		}
		self::$form_tabs[ $id ] = $new_tab;
		return $new_tab;
	}

	/**
	 * Returns list of registered fields.
	 *
	 * @param array $sort_by key and order to sort by.
	 * @return array list of registered fields.
	 */
	public static function get_fields( $sort_by = array(
		'key'   => 'order',
		'order' => 'asc',
	) ) {
		return self::get_items( 'field', 'Field', $sort_by );
	}

	/**
	 * Returns list of registered cards.
	 *
	 * @param array $sort_by key and order to sort by.
	 * @return array list of registered cards.
	 */
	public static function get_subsections( $sort_by = array(
		'key'   => 'order',
		'order' => 'asc',
	) ) {
		return self::get_items( 'subsection', 'Subsection', $sort_by );
	}

	/**
	 * Returns list of registered sections.
	 *
	 * @param array $sort_by key and order to sort by.
	 * @return array list of registered sections.
	 */
	public static function get_sections( $sort_by = array(
		'key'   => 'order',
		'order' => 'asc',
	) ) {
		return self::get_items( 'section', 'Section', $sort_by );
	}

	/**
	 * Returns list of registered tabs.
	 *
	 * @param array $sort_by key and order to sort by.
	 * @return array list of registered tabs.
	 */
	public static function get_tabs( $sort_by = array(
		'key'   => 'order',
		'order' => 'asc',
	) ) {
		return self::get_items( 'tab', 'Tab', $sort_by );
	}

	/**
	 * Returns list of registered items.
	 *
	 * @param string $type Form component type.
	 * @return array List of registered items.
	 */
	private static function get_item_list( $type ) {
		$mapping = array(
			'field'      => self::$form_fields,
			'subsection' => self::$form_subsections,
			'section'    => self::$form_sections,
			'tab'        => self::$form_tabs,
		);
		if ( array_key_exists( $type, $mapping ) ) {
			return $mapping[ $type ];
		}
		return array();
	}

	/**
	 * Returns list of registered items.
	 *
	 * @param string       $type Form component type.
	 * @param class-string $class_name Class of component type.
	 * @param array        $sort_by key and order to sort by.
	 * @return array       list of registered items.
	 */
	private static function get_items( $type, $class_name, $sort_by = array(
		'key'   => 'order',
		'order' => 'asc',
	) ) {
		$item_list = self::get_item_list( $type );
		$class     = 'Automattic\\WooCommerce\\Internal\\Admin\\ProductForm\\' . $class_name;
		$items     = array_values( $item_list );
		if ( class_exists( $class ) && method_exists( $class, 'sort' ) ) {
			usort(
				$items,
				function ( $a, $b ) use ( $sort_by, $class ) {
					return $class::sort( $a, $b, $sort_by );
				}
			);
		}
		return $items;
	}

	/**
	 * Creates a new item.
	 *
	 * @param string       $type Form component type.
	 * @param class-string $class_name Class of component type.
	 * @param string       $id Item id.
	 * @param string       $plugin_id Plugin id.
	 * @param array        $args additional arguments for item.
	 * @return Field|Card|Section|Tab|WP_Error New product form item or WP_Error.
	 */
	private static function create_item( $type, $class_name, $id, $plugin_id, $args ) {
		$item_list = self::get_item_list( $type );
		$class     = 'Automattic\\WooCommerce\\Internal\\Admin\\ProductForm\\' . $class_name;
		if ( ! class_exists( $class ) ) {
			return new WP_Error(
				'wc_product_form_' . $type . '_missing_form_class',
				sprintf(
				/* translators: 1: missing class name. */
					esc_html__( '%1$s class does not exist.', 'woocommerce' ),
					$class
				)
			);
		}
		if ( isset( $item_list[ $id ] ) ) {
			return new WP_Error(
				'wc_product_form_' . $type . '_duplicate_field_id',
				sprintf(
				/* translators: 1: Item type 2: Duplicate registered item id. */
					esc_html__( 'You have attempted to register a duplicate form %1$s with WooCommerce Form: %2$s', 'woocommerce' ),
					$type,
					'`' . $id . '`'
				)
			);
		}

		$defaults = array(
			'order' => 20,
		);

		$item_arguments = wp_parse_args( $args, $defaults );

		try {
			return new $class( $id, $plugin_id, $item_arguments );
		} catch ( \Exception $e ) {
			return new WP_Error(
				'wc_product_form_' . $type . '_class_creation',
				$e->getMessage()
			);
		}
	}
}

