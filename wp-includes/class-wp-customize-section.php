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
class WP_Customize_Section {

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var int
	 */
	public $instance_number;

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * Priority of the section which informs load order of sections.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var integer
	 */
	public $priority = 160;

	/**
	 * Panel in which to show the section, making it a sub-section.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $panel = '';

	/**
	 * Capability required for the section.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the section.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 * Title of the section to show in UI.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * Customizer controls for this section.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var array
	 */
	public $controls;

	/**
	 * Type of this section.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $type = 'default';

	/**
	 * Active callback.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @see WP_Customize_Section::active()
	 *
	 * @var callable Callback is called with one argument, the instance of
	 *               {@see WP_Customize_Section}, and returns bool to indicate
	 *               whether the section is active (such as it relates to the URL
	 *               currently being previewed).
	 */
	public $active_callback = '';

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      An specific ID of the section.
	 * @param array                $args    Section arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager = $manager;
		$this->id = $id;
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
	 * @access public
	 *
	 * @return bool Whether the section is active to the current preview.
	 */
	final public function active() {
		$section = $this;
		$active = call_user_func( $this->active_callback, $this );

		/**
		 * Filter response of {@see WP_Customize_Section::active()}.
		 *
		 * @since 4.1.0
		 *
		 * @param bool                 $active  Whether the Customizer section is active.
		 * @param WP_Customize_Section $section {@see WP_Customize_Section} instance.
		 */
		$active = apply_filters( 'customize_section_active', $active, $section );

		return $active;
	}

	/**
	 * Default callback used when invoking {@see WP_Customize_Section::active()}.
	 *
	 * Subclasses can override this with their specific logic, or they may provide
	 * an 'active_callback' argument to the constructor.
	 *
	 * @since 4.1.0
	 * @access public
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
		$array = wp_array_slice_assoc( (array) $this, array( 'title', 'description', 'priority', 'panel', 'type' ) );
		$array['content'] = $this->get_content();
		$array['active'] = $this->active();
		$array['instanceNumber'] = $this->instance_number;
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
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the section's content template for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Contents of the section.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		$template = trim( ob_get_contents() );
		ob_end_clean();
		return $template;
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
		 * @param WP_Customize_Section $this WP_Customize_Section instance.
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
	 * Render the section, and the controls that have been added to it.
	 *
	 * @since 3.4.0
	 */
	protected function render() {
		$classes = 'accordion-section control-section control-section-' . $this->type;
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
			<h3 class="accordion-section-title" tabindex="0">
				<?php echo esc_html( $this->title ); ?>
				<span class="screen-reader-text"><?php _e( 'Press return or enter to expand' ); ?></span>
			</h3>
			<ul class="accordion-section-content">
				<?php if ( ! empty( $this->description ) ) : ?>
					<li class="customize-section-description-container">
						<p class="description customize-section-description"><?php echo $this->description; ?></p>
					</li>
				<?php endif; ?>
			</ul>
		</li>
		<?php
	}
}

/**
 * Customize Themes Section class.
 *
 * A UI container for theme controls, which behaves like a backwards Panel.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Section
 */
class WP_Customize_Themes_Section extends WP_Customize_Section {

	public $type = 'themes';

	/**
	 * Render the themes section, which behaves like a panel.
	 *
	 * @since 4.2.0
	 */
	protected function render() {
		$classes = 'accordion-section control-section control-section-' . $this->type;
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
			<h3 class="accordion-section-title" tabindex="0">
				<?php echo esc_html( $this->title ); ?>
				<span class="screen-reader-text"><?php _e( 'Press return or enter to expand' ); ?></span>
			</h3>
			<span class="control-panel-back themes-panel-back" tabindex="-1"><span class="screen-reader-text"><?php _e( 'Back' ); ?></span></span>
			<div class="customize-themes-panel control-panel-content themes-php">
				<h2><?php esc_html_e( 'Themes' ); ?>
					<span class="title-count theme-count"><?php echo count( $this->controls ) - 1; ?></span>
				<?php if ( ! is_multisite() && current_user_can( 'install_themes' ) ) : ?>
					<a href="<?php echo admin_url( 'theme-install.php' ); ?>" target="_top" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'Add new theme' ); ?></a>
				<?php endif; ?>
				</h2>
				<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( 'Theme details' ); ?>"></div>
				<div id="customize-container"></div>
				<?php if ( 6 < count( $this->controls ) ) : ?>
					<p><label for="themes-filter">
						<span class="screen-reader-text"><?php _e( 'Search installed themes...' ); ?></span>
						<input type="search" id="themes-filter" placeholder="<?php esc_attr_e( 'Search installed themes...' ); ?>" />
					</label></p>
				<?php endif; ?>
				<div class="theme-browser rendered">
					<ul class="themes accordion-section-content">
					</ul>
				</div>
			</div>
		</li>
<?php }
}

/**
 * Customizer section representing widget area (sidebar).
 *
 * @since 4.1.0
 *
 * @see WP_Customize_Section
 */
class WP_Customize_Sidebar_Section extends WP_Customize_Section {

	/**
	 * Type of this section.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $type = 'sidebar';

	/**
	 * Unique identifier.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $sidebar_id;

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$json = parent::json();
		$json['sidebarId'] = $this->sidebar_id;
		return $json;
	}

	/**
	 * Whether the current sidebar is rendered on the page.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @return bool Whether sidebar is rendered.
	 */
	public function active_callback() {
		return $this->manager->widgets->is_sidebar_rendered( $this->sidebar_id );
	}
}
