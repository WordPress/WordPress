<?php
/**
 * Customize API: WP_Customize_Partial class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.5.0
 */

/**
 * Core Customizer class for implementing selective refresh partials.
 *
 * Representation of a rendered region in the previewed page that gets
 * selectively refreshed when an associated setting is changed.
 * This class is analogous of WP_Customize_Control.
 *
 * @since 4.5.0
 */
#[AllowDynamicProperties]
class WP_Customize_Partial {

	/**
	 * Component.
	 *
	 * @since 4.5.0
	 * @var WP_Customize_Selective_Refresh
	 */
	public $component;

	/**
	 * Unique identifier for the partial.
	 *
	 * If the partial is used to display a single setting, this would generally
	 * be the same as the associated setting's ID.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $id;

	/**
	 * Parsed ID.
	 *
	 * @since 4.5.0
	 * @var array {
	 *     @type string $base ID base.
	 *     @type array  $keys Keys for multidimensional.
	 * }
	 */
	protected $id_data = array();

	/**
	 * Type of this partial.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $type = 'default';

	/**
	 * The jQuery selector to find the container element for the partial.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $selector;

	/**
	 * IDs for settings tied to the partial.
	 *
	 * @since 4.5.0
	 * @var string[]
	 */
	public $settings;

	/**
	 * The ID for the setting that this partial is primarily responsible for rendering.
	 *
	 * If not supplied, it will default to the ID of the first setting.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $primary_setting;

	/**
	 * Capability required to edit this partial.
	 *
	 * Normally this is empty and the capability is derived from the capabilities
	 * of the associated `$settings`.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $capability;

	/**
	 * Render callback.
	 *
	 * @since 4.5.0
	 *
	 * @see WP_Customize_Partial::render()
	 * @var callable Callback is called with one argument, the instance of
	 *               WP_Customize_Partial. The callback can either echo the
	 *               partial or return the partial as a string, or return false if error.
	 */
	public $render_callback;

	/**
	 * Whether the container element is included in the partial, or if only the contents are rendered.
	 *
	 * @since 4.5.0
	 * @var bool
	 */
	public $container_inclusive = false;

	/**
	 * Whether to refresh the entire preview in case a partial cannot be refreshed.
	 *
	 * A partial render is considered a failure if the render_callback returns false.
	 *
	 * @since 4.5.0
	 * @var bool
	 */
	public $fallback_refresh = true;

	/**
	 * Constructor.
	 *
	 * Supplied `$args` override class property defaults.
	 *
	 * If `$args['settings']` is not defined, use the $id as the setting ID.
	 *
	 * @since 4.5.0
	 *
	 * @param WP_Customize_Selective_Refresh $component Customize Partial Refresh plugin instance.
	 * @param string                         $id        Control ID.
	 * @param array                          $args {
	 *     Optional. Array of properties for the new Partials object. Default empty array.
	 *
	 *     @type string   $type                  Type of the partial to be created.
	 *     @type string   $selector              The jQuery selector to find the container element for the partial, that is,
	 *                                           a partial's placement.
	 *     @type string[] $settings              IDs for settings tied to the partial. If undefined, `$id` will be used.
	 *     @type string   $primary_setting       The ID for the setting that this partial is primarily responsible for
	 *                                           rendering. If not supplied, it will default to the ID of the first setting.
	 *     @type string   $capability            Capability required to edit this partial.
	 *                                           Normally this is empty and the capability is derived from the capabilities
	 *                                           of the associated `$settings`.
	 *     @type callable $render_callback       Render callback.
	 *                                           Callback is called with one argument, the instance of WP_Customize_Partial.
	 *                                           The callback can either echo the partial or return the partial as a string,
	 *                                           or return false if error.
	 *     @type bool     $container_inclusive   Whether the container element is included in the partial, or if only
	 *                                           the contents are rendered.
	 *     @type bool     $fallback_refresh      Whether to refresh the entire preview in case a partial cannot be refreshed.
	 *                                           A partial render is considered a failure if the render_callback returns
	 *                                           false.
	 * }
	 */
	public function __construct( WP_Customize_Selective_Refresh $component, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->component       = $component;
		$this->id              = $id;
		$this->id_data['keys'] = preg_split( '/\[/', str_replace( ']', '', $this->id ) );
		$this->id_data['base'] = array_shift( $this->id_data['keys'] );

		if ( empty( $this->render_callback ) ) {
			$this->render_callback = array( $this, 'render_callback' );
		}

		// Process settings.
		if ( ! isset( $this->settings ) ) {
			$this->settings = array( $id );
		} elseif ( is_string( $this->settings ) ) {
			$this->settings = array( $this->settings );
		}

		if ( empty( $this->primary_setting ) ) {
			$this->primary_setting = current( $this->settings );
		}
	}

	/**
	 * Retrieves parsed ID data for multidimensional setting.
	 *
	 * @since 4.5.0
	 *
	 * @return array {
	 *     ID data for multidimensional partial.
	 *
	 *     @type string $base ID base.
	 *     @type array  $keys Keys for multidimensional array.
	 * }
	 */
	final public function id_data() {
		return $this->id_data;
	}

	/**
	 * Renders the template partial involving the associated settings.
	 *
	 * @since 4.5.0
	 *
	 * @param array $container_context Optional. Array of context data associated with the target container (placement).
	 *                                 Default empty array.
	 * @return string|array|false The rendered partial as a string, raw data array (for client-side JS template),
	 *                            or false if no render applied.
	 */
	final public function render( $container_context = array() ) {
		$partial  = $this;
		$rendered = false;

		if ( ! empty( $this->render_callback ) ) {
			ob_start();
			$return_render = call_user_func( $this->render_callback, $this, $container_context );
			$ob_render     = ob_get_clean();

			if ( null !== $return_render && '' !== $ob_render ) {
				_doing_it_wrong( __FUNCTION__, __( 'Partial render must echo the content or return the content string (or array), but not both.' ), '4.5.0' );
			}

			/*
			 * Note that the string return takes precedence because the $ob_render may just\
			 * include PHP warnings or notices.
			 */
			$rendered = null !== $return_render ? $return_render : $ob_render;
		}

		/**
		 * Filters partial rendering.
		 *
		 * @since 4.5.0
		 *
		 * @param string|array|false   $rendered          The partial value. Default false.
		 * @param WP_Customize_Partial $partial           WP_Customize_Setting instance.
		 * @param array                $container_context Optional array of context data associated with
		 *                                                the target container.
		 */
		$rendered = apply_filters( 'customize_partial_render', $rendered, $partial, $container_context );

		/**
		 * Filters partial rendering for a specific partial.
		 *
		 * The dynamic portion of the hook name, `$partial->ID` refers to the partial ID.
		 *
		 * @since 4.5.0
		 *
		 * @param string|array|false   $rendered          The partial value. Default false.
		 * @param WP_Customize_Partial $partial           WP_Customize_Setting instance.
		 * @param array                $container_context Optional array of context data associated with
		 *                                                the target container.
		 */
		$rendered = apply_filters( "customize_partial_render_{$partial->id}", $rendered, $partial, $container_context );

		return $rendered;
	}

	/**
	 * Default callback used when invoking WP_Customize_Control::render().
	 *
	 * Note that this method may echo the partial *or* return the partial as
	 * a string or array, but not both. Output buffering is performed when this
	 * is called. Subclasses can override this with their specific logic, or they
	 * may provide an 'render_callback' argument to the constructor.
	 *
	 * This method may return an HTML string for straight DOM injection, or it
	 * may return an array for supporting Partial JS subclasses to render by
	 * applying to client-side templating.
	 *
	 * @since 4.5.0
	 *
	 * @param WP_Customize_Partial $partial Partial.
	 * @param array                $context Context.
	 * @return string|array|false
	 */
	public function render_callback( WP_Customize_Partial $partial, $context = array() ) {
		unset( $partial, $context );
		return false;
	}

	/**
	 * Retrieves the data to export to the client via JSON.
	 *
	 * @since 4.5.0
	 *
	 * @return array Array of parameters passed to the JavaScript.
	 */
	public function json() {
		$exports = array(
			'settings'           => $this->settings,
			'primarySetting'     => $this->primary_setting,
			'selector'           => $this->selector,
			'type'               => $this->type,
			'fallbackRefresh'    => $this->fallback_refresh,
			'containerInclusive' => $this->container_inclusive,
		);
		return $exports;
	}

	/**
	 * Checks if the user can refresh this partial.
	 *
	 * Returns false if the user cannot manipulate one of the associated settings,
	 * or if one of the associated settings does not exist.
	 *
	 * @since 4.5.0
	 *
	 * @return bool False if user can't edit one of the related settings,
	 *                    or if one of the associated settings does not exist.
	 */
	final public function check_capabilities() {
		if ( ! empty( $this->capability ) && ! current_user_can( $this->capability ) ) {
			return false;
		}
		foreach ( $this->settings as $setting_id ) {
			$setting = $this->component->manager->get_setting( $setting_id );
			if ( ! $setting || ! $setting->check_capabilities() ) {
				return false;
			}
		}
		return true;
	}
}
