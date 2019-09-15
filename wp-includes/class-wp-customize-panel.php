<?php
/**
 * WordPress Customize Panel classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.0.0
 */

/**
 * Customize Panel class.
 *
 * A UI container for sections, managed by the WP_Customize_Manager.
 *
 * @since 4.0.0
 *
 * @see WP_Customize_Manager
 */
class WP_Customize_Panel {

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @since 4.1.0
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @since 4.1.0
	 * @var int
	 */
	public $instance_number;

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 4.0.0
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $id;

	/**
	 * Priority of the panel, defining the display order of panels and sections.
	 *
	 * @since 4.0.0
	 * @var integer
	 */
	public $priority = 160;

	/**
	 * Capability required for the panel.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the panel.
	 *
	 * @since 4.0.0
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 * Title of the panel to show in UI.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Auto-expand a section in a panel when the panel is expanded when the panel only has the one section.
	 *
	 * @since 4.7.4
	 * @var bool
	 */
	public $auto_expand_sole_section = false;

	/**
	 * Customizer sections for this panel.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	public $sections;

	/**
	 * Type of this panel.
	 *
	 * @since 4.1.0
	 * @var string
	 */
	public $type = 'default';

	/**
	 * Active callback.
	 *
	 * @since 4.1.0
	 *
	 * @see WP_Customize_Section::active()
	 *
	 * @var callable Callback is called with one argument, the instance of
	 *               WP_Customize_Section, and returns bool to indicate whether
	 *               the section is active (such as it relates to the URL currently
	 *               being previewed).
	 */
	public $active_callback = '';

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      An specific ID for the panel.
	 * @param array                $args    Panel arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager = $manager;
		$this->id      = $id;
		if ( empty( $this->active_callback ) ) {
			$this->active_callback = array( $this, 'active_callback' );
		}
		self::$instance_count += 1;
		$this->instance_number = self::$instance_count;

		$this->sections = array(); // Users cannot customize the $sections array.
	}

	/**
	 * Check whether panel is active to current Customizer preview.
	 *
	 * @since 4.1.0
	 *
	 * @return bool Whether the panel is active to the current preview.
	 */
	final public function active() {
		$panel  = $this;
		$active = call_user_func( $this->active_callback, $this );

		/**
		 * Filters response of WP_Customize_Panel::active().
		 *
		 * @since 4.1.0
		 *
		 * @param bool               $active Whether the Customizer panel is active.
		 * @param WP_Customize_Panel $panel  WP_Customize_Panel instance.
		 */
		$active = apply_filters( 'customize_panel_active', $active, $panel );

		return $active;
	}

	/**
	 * Default callback used when invoking WP_Customize_Panel::active().
	 *
	 * Subclasses can override this with their specific logic, or they may
	 * provide an 'active_callback' argument to the constructor.
	 *
	 * @since 4.1.0
	 *
	 * @return bool Always true.
	 */
	public function active_callback() {
		return true;
	}

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$array                          = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'type' ) );
		$array['title']                 = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['content']               = $this->get_content();
		$array['active']                = $this->active();
		$array['instanceNumber']        = $this->instance_number;
		$array['autoExpandSoleSection'] = $this->auto_expand_sole_section;
		return $array;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the panel.
	 *
	 * @since 4.0.0
	 *
	 * @return bool False if theme doesn't support the panel or the user doesn't have the capability.
	 */
	final public function check_capabilities() {
		if ( $this->capability && ! current_user_can( $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! current_theme_supports( ... (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the panel's content template for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Content for the panel.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the panel.
	 *
	 * @since 4.0.0
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		/**
		 * Fires before rendering a Customizer panel.
		 *
		 * @since 4.0.0
		 *
		 * @param WP_Customize_Panel $this WP_Customize_Panel instance.
		 */
		do_action( 'customize_render_panel', $this );

		/**
		 * Fires before rendering a specific Customizer panel.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the ID of the specific Customizer panel to be rendered.
		 *
		 * @since 4.0.0
		 */
		do_action( "customize_render_panel_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the panel container, and then its contents (via `this->render_content()`) in a subclass.
	 *
	 * Panel containers are now rendered in JS by default, see WP_Customize_Panel::print_template().
	 *
	 * @since 4.0.0
	 */
	protected function render() {}

	/**
	 * Render the panel UI in a subclass.
	 *
	 * Panel contents are now rendered in JS by default, see WP_Customize_Panel::print_template().
	 *
	 * @since 4.1.0
	 */
	protected function render_content() {}

	/**
	 * Render the panel's JS templates.
	 *
	 * This function is only run for panel types that have been registered with
	 * WP_Customize_Manager::register_panel_type().
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Manager::register_panel_type()
	 */
	public function print_template() {
		?>
		<script type="text/html" id="tmpl-customize-panel-<?php echo esc_attr( $this->type ); ?>-content">
			<?php $this->content_template(); ?>
		</script>
		<script type="text/html" id="tmpl-customize-panel-<?php echo esc_attr( $this->type ); ?>">
			<?php $this->render_template(); ?>
		</script>
		<?php
	}

	/**
	 * An Underscore (JS) template for rendering this panel's container.
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding WP_Customize_Panel::json().
	 *
	 * @see WP_Customize_Panel::print_template()
	 *
	 * @since 4.3.0
	 */
	protected function render_template() {
		?>
		<li id="accordion-panel-{{ data.id }}" class="accordion-section control-section control-panel control-panel-{{ data.type }}">
			<h3 class="accordion-section-title" tabindex="0">
				{{ data.title }}
				<span class="screen-reader-text"><?php _e( 'Press return or enter to open this panel' ); ?></span>
			</h3>
			<ul class="accordion-sub-container control-panel-content"></ul>
		</li>
		<?php
	}

	/**
	 * An Underscore (JS) template for this panel's content (but not its container).
	 *
	 * Class variables for this panel class are available in the `data` JS object;
	 * export custom variables by overriding WP_Customize_Panel::json().
	 *
	 * @see WP_Customize_Panel::print_template()
	 *
	 * @since 4.3.0
	 */
	protected function content_template() {
		?>
		<li class="panel-meta customize-info accordion-section <# if ( ! data.description ) { #> cannot-expand<# } #>">
			<button class="customize-panel-back" tabindex="-1"><span class="screen-reader-text"><?php _e( 'Back' ); ?></span></button>
			<div class="accordion-section-title">
				<span class="preview-notice">
				<?php
					/* translators: %s: The site/panel title in the Customizer. */
					echo sprintf( __( 'You are customizing %s' ), '<strong class="panel-title">{{ data.title }}</strong>' );
				?>
				</span>
				<# if ( data.description ) { #>
					<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php _e( 'Help' ); ?></span></button>
				<# } #>
			</div>
			<# if ( data.description ) { #>
				<div class="description customize-panel-description">
					{{{ data.description }}}
				</div>
			<# } #>

			<div class="customize-control-notifications-container"></div>
		</li>
		<?php
	}
}

/** WP_Customize_Nav_Menus_Panel class */
require_once( ABSPATH . WPINC . '/customize/class-wp-customize-nav-menus-panel.php' );
