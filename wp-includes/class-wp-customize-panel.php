<?php
/**
 * Customize Panel Class.
 *
 * A UI container for sections, managed by the WP_Customize_Manager.
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.0.0
 */
class WP_Customize_Panel {

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * Priority of the panel, defining the display order of panels and sections.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var integer
	 */
	public $priority = 160;

	/**
	 * Capability required for the panel.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the panel.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 * Title of the panel to show in UI.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * Customizer sections for this panel.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var array
	 */
	public $sections;

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
		$this->id = $id;

		$this->sections = array(); // Users cannot customize the $sections array.

		return $this;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the panel.
	 *
	 * @since 4.0.0
	 *
	 * @return bool False if theme doesn't support the panel or the user doesn't have the capability.
	 */
	public final function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check capabilities and render the panel.
	 *
	 * @since 4.0.0
	 */
	public final function maybe_render() {
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
		 * The dynamic portion of the hook name, $this->id, refers to the ID
		 * of the specific Customizer panel to be rendered.
		 *
		 * @since 4.0.0
		 */
		do_action( "customize_render_panel_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the panel, and the sections that have been added to it.
	 *
	 * @since 4.0.0
	 * @access protected
	 */
	protected function render() {
		?>
		<li id="accordion-panel-<?php echo esc_attr( $this->id ); ?>" class="control-section control-panel accordion-section">
			<h3 class="accordion-section-title" tabindex="0">
				<?php echo esc_html( $this->title ); ?>
				<span class="screen-reader-text"><?php _e( 'Press return or enter to open this panel' ); ?></span>
			</h3>
			<ul class="accordion-sub-container control-panel-content">
				<li class="accordion-section control-section<?php if ( empty( $this->description ) ) echo ' cannot-expand'; ?>">
					<div class="accordion-section-title" tabindex="0">
						<span class="preview-notice"><?php
							/* translators: %s is the site/panel title in the Customizer */
							echo sprintf( __( 'You are customizing %s' ), '<strong class="panel-title">' . esc_html( $this->title ) . '</strong>' );
						?></span>
					</div>
					<?php if ( ! empty( $this->description ) ) : ?>
						<div class="accordion-section-content description">
							<?php echo $this->description; ?>
						</div>
					<?php endif; ?>
				</li>
				<?php
				foreach ( $this->sections as $section ) {
					$section->maybe_render();
				}
				?>
			</ul>
		</li>
		<?php
	}
}