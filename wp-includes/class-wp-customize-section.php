<?php
/**
 * WordPress Customize Section classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

/**
 * Customize Section class.
 *
 * A UI container for controls, managed by the WP_Customize_Manager class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Manager
 */
#[AllowDynamicProperties]
class WP_Customize_Section {

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
	 * @since 3.4.0
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $id;

	/**
	 * Priority of the section which informs load order of sections.
	 *
	 * @since 3.4.0
	 * @var int
	 */
	public $priority = 160;

	/**
	 * Panel in which to show the section, making it a sub-section.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $panel = '';

	/**
	 * Capability required for the section.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme features required to support the section.
	 *
	 * @since 3.4.0
	 * @var string|string[]
	 */
	public $theme_supports = '';

	/**
	 * Title of the section to show in UI.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Customizer controls for this section.
	 *
	 * @since 3.4.0
	 * @var array
	 */
	public $controls;

	/**
	 * Type of this section.
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
	 * Show the description or hide it behind the help icon.
	 *
	 * @since 4.7.0
	 *
	 * @var bool Indicates whether the Section's description should be
	 *           hidden behind a help icon ("?") in the Section header,
	 *           similar to how help icons are displayed on Panels.
	 */
	public $description_hidden = false;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      A specific ID of the section.
	 * @param array                $args    {
	 *     Optional. Array of properties for the new Section object. Default empty array.
	 *
	 *     @type int             $priority           Priority of the section, defining the display order
	 *                                               of panels and sections. Default 160.
	 *     @type string          $panel              The panel this section belongs to (if any).
	 *                                               Default empty.
	 *     @type string          $capability         Capability required for the section.
	 *                                               Default 'edit_theme_options'
	 *     @type string|string[] $theme_supports     Theme features required to support the section.
	 *     @type string          $title              Title of the section to show in UI.
	 *     @type string          $description        Description to show in the UI.
	 *     @type string          $type               Type of the section.
	 *     @type callable        $active_callback    Active callback.
	 *     @type bool            $description_hidden Hide the description behind a help icon,
	 *                                               instead of inline above the first control.
	 *                                               Default false.
	 * }
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

		$this->controls = array(); // Users cannot customize the $controls array.
	}

	/**
	 * Check whether section is active to current Customizer preview.
	 *
	 * @since 4.1.0
	 *
	 * @return bool Whether the section is active to the current preview.
	 */
	final public function active() {
		$section = $this;
		$active  = call_user_func( $this->active_callback, $this );

		/**
		 * Filters response of WP_Customize_Section::active().
		 *
		 * @since 4.1.0
		 *
		 * @param bool                 $active  Whether the Customizer section is active.
		 * @param WP_Customize_Section $section WP_Customize_Section instance.
		 */
		$active = apply_filters( 'customize_section_active', $active, $section );

		return $active;
	}

	/**
	 * Default callback used when invoking WP_Customize_Section::active().
	 *
	 * Subclasses can override this with their specific logic, or they may provide
	 * an 'active_callback' argument to the constructor.
	 *
	 * @since 4.1.0
	 *
	 * @return true Always true.
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
		$array                   = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'panel', 'type', 'description_hidden' ) );
		$array['title']          = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['content']        = $this->get_content();
		$array['active']         = $this->active();
		$array['instanceNumber'] = $this->instance_number;

		if ( $this->panel ) {
			/* translators: &#9656; is the unicode right-pointing triangle. %s: Section title in the Customizer. */
			$array['customizeAction'] = sprintf( __( 'Customizing &#9656; %s' ), esc_html( $this->manager->get_panel( $this->panel )->title ) );
		} else {
			$array['customizeAction'] = __( 'Customizing' );
		}

		return $array;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the section.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the section or user doesn't have the capability.
	 */
	final public function check_capabilities() {
		if ( $this->capability && ! current_user_can( $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! current_theme_supports( ...(array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the section's content for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Contents of the section.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the section.
	 *
	 * @since 3.4.0
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		/**
		 * Fires before rendering a Customizer section.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Section $section WP_Customize_Section instance.
		 */
		do_action( 'customize_render_section', $this );
		/**
		 * Fires before rendering a specific Customizer section.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to the ID
		 * of the specific Customizer section to be rendered.
		 *
		 * @since 3.4.0
		 */
		do_action( "customize_render_section_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the section UI in a subclass.
	 *
	 * Sections are now rendered in JS by default, see WP_Customize_Section::print_template().
	 *
	 * @since 3.4.0
	 */
	protected function render() {}

	/**
	 * Render the section's JS template.
	 *
	 * This function is only run for section types that have been registered with
	 * WP_Customize_Manager::register_section_type().
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Manager::render_template()
	 */
	public function print_template() {
		?>
		<script type="text/html" id="tmpl-customize-section-<?php echo $this->type; ?>">
			<?php $this->render_template(); ?>
		</script>
		<?php
	}

	/**
	 * An Underscore (JS) template for rendering this section.
	 *
	 * Class variables for this section class are available in the `data` JS object;
	 * export custom variables by overriding WP_Customize_Section::json().
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Section::print_template()
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }}">
			<h3 class="accordion-section-title" tabindex="0">
				{{ data.title }}
				<span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( 'Press return or enter to open this section' );
					?>
				</span>
			</h3>
			<ul class="accordion-section-content">
				<li class="customize-section-description-container section-meta <# if ( data.description_hidden ) { #>customize-info<# } #>">
					<div class="customize-section-title">
						<button class="customize-section-back" tabindex="-1">
							<span class="screen-reader-text">
								<?php
								/* translators: Hidden accessibility text. */
								_e( 'Back' );
								?>
							</span>
						</button>
						<h3>
							<span class="customize-action">
								{{{ data.customizeAction }}}
							</span>
							{{ data.title }}
						</h3>
						<# if ( data.description && data.description_hidden ) { #>
							<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text">
								<?php
								/* translators: Hidden accessibility text. */
								_e( 'Help' );
								?>
							</span></button>
							<div class="description customize-section-description">
								{{{ data.description }}}
							</div>
						<# } #>

						<div class="customize-control-notifications-container"></div>
					</div>

					<# if ( data.description && ! data.description_hidden ) { #>
						<div class="description customize-section-description">
							{{{ data.description }}}
						</div>
					<# } #>
				</li>
			</ul>
		</li>
		<?php
	}
}

/** WP_Customize_Themes_Section class */
require_once ABSPATH . WPINC . '/customize/class-wp-customize-themes-section.php';

/** WP_Customize_Sidebar_Section class */
require_once ABSPATH . WPINC . '/customize/class-wp-customize-sidebar-section.php';

/** WP_Customize_Nav_Menu_Section class */
require_once ABSPATH . WPINC . '/customize/class-wp-customize-nav-menu-section.php';
