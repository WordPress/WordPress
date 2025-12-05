<?php
namespace Elementor;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor settings page.
 *
 * An abstract class that provides the needed properties and methods to handle
 * WordPress dashboard settings pages in inheriting classes.
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Settings_Page {

	/**
	 * Settings page ID.
	 */
	const PAGE_ID = '';

	/**
	 * Tabs.
	 *
	 * Holds the settings page tabs, sections and fields.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $tabs;

	/**
	 * Create tabs.
	 *
	 * Return the settings page tabs, sections and fields.
	 *
	 * @since 1.5.0
	 * @access protected
	 * @abstract
	 */
	abstract protected function create_tabs();

	/**
	 * Get settings page title.
	 *
	 * Retrieve the title for the settings page.
	 *
	 * @since 1.5.0
	 * @access protected
	 * @abstract
	 */
	abstract protected function get_page_title();

	/**
	 * Get settings page URL.
	 *
	 * Retrieve the URL of the settings page.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 *
	 * @return string Settings page URL.
	 */
	final public static function get_url() {
		return admin_url( 'admin.php?page=' . static::PAGE_ID );
	}

	/**
	 * Get settings tab URL.
	 *
	 * Retrieve the URL of a specific tab in the settings page.
	 *
	 * @since 3.23.0
	 * @access public
	 * @static
	 *
	 * @param string $tab_id The ID of the settings tab.
	 *
	 * @return string Settings tab URL.
	 */
	final public static function get_settings_tab_url( $tab_id ): string {
		$settings_page_id = Plugin::$instance->experiments->is_feature_active( 'home_screen' )
			? 'elementor-settings'
			: 'elementor';

		return admin_url( "admin.php?page=$settings_page_id#tab-$tab_id" );
	}

	/**
	 * Settings page constructor.
	 *
	 * Initializing Elementor settings page.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function __construct() {
		// PHPCS - The user data is not used.
		if ( ! empty( $_POST['option_page'] ) && static::PAGE_ID === $_POST['option_page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			add_action( 'admin_init', [ $this, 'register_settings_fields' ] );
		}
	}

	/**
	 * Get tabs.
	 *
	 * Retrieve the settings page tabs, sections and fields.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Settings page tabs, sections and fields.
	 */
	final public function get_tabs() {
		$this->ensure_tabs();

		return $this->tabs;
	}

	/**
	 * Add tab.
	 *
	 * Register a new tab to a settings page.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $tab_id   Tab ID.
	 * @param array  $tab_args Optional. Tab arguments. Default is an empty array.
	 */
	final public function add_tab( $tab_id, array $tab_args = [] ) {
		$this->ensure_tabs();

		if ( isset( $this->tabs[ $tab_id ] ) ) {
			// Don't override an existing tab
			return;
		}

		if ( ! isset( $tab_args['sections'] ) ) {
			$tab_args['sections'] = [];
		}

		$this->tabs[ $tab_id ] = $tab_args;
	}

	/**
	 * Add section.
	 *
	 * Register a new section to a tab.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $tab_id       Tab ID.
	 * @param string $section_id   Section ID.
	 * @param array  $section_args Optional. Section arguments. Default is an
	 *                             empty array.
	 */
	final public function add_section( $tab_id, $section_id, array $section_args = [] ) {
		$this->ensure_tabs();

		if ( ! isset( $this->tabs[ $tab_id ] ) ) {
			// If the requested tab doesn't exists, use the first tab
			$tab_id = key( $this->tabs );
		}

		if ( isset( $this->tabs[ $tab_id ]['sections'][ $section_id ] ) ) {
			// Don't override an existing section
			return;
		}

		if ( ! isset( $section_args['fields'] ) ) {
			$section_args['fields'] = [];
		}

		$this->tabs[ $tab_id ]['sections'][ $section_id ] = $section_args;
	}

	/**
	 * Add field.
	 *
	 * Register a new field to a section.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $tab_id     Tab ID.
	 * @param string $section_id Section ID.
	 * @param string $field_id   Field ID.
	 * @param array  $field_args Field arguments.
	 */
	final public function add_field( $tab_id, $section_id, $field_id, array $field_args ) {
		$this->ensure_tabs();

		if ( ! isset( $this->tabs[ $tab_id ] ) ) {
			// If the requested tab doesn't exists, use the first tab
			$tab_id = key( $this->tabs );
		}

		if ( ! isset( $this->tabs[ $tab_id ]['sections'][ $section_id ] ) ) {
			// If the requested section doesn't exists, use the first section
			$section_id = key( $this->tabs[ $tab_id ]['sections'] );
		}

		if ( isset( $this->tabs[ $tab_id ]['sections'][ $section_id ]['fields'][ $field_id ] ) ) {
			// Don't override an existing field
			return;
		}

		$this->tabs[ $tab_id ]['sections'][ $section_id ]['fields'][ $field_id ] = $field_args;
	}

	/**
	 * Add fields.
	 *
	 * Register multiple fields to a section.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $tab_id     Tab ID.
	 * @param string $section_id Section ID.
	 * @param array  $fields     {
	 *    An array of fields.
	 *
	 *    @type string $field_id   Field ID.
	 *    @type array  $field_args Field arguments.
	 * }
	 */
	final public function add_fields( $tab_id, $section_id, array $fields ) {
		foreach ( $fields as $field_id => $field_args ) {
			$this->add_field( $tab_id, $section_id, $field_id, $field_args );
		}
	}

	/**
	 * Register settings fields.
	 *
	 * In each tab register his inner sections, and in each section register his
	 * inner fields.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	final public function register_settings_fields() {
		$controls_class_name = __NAMESPACE__ . '\Settings_Controls';

		$tabs = $this->get_tabs();

		foreach ( $tabs as $tab_id => $tab ) {

			foreach ( $tab['sections'] as $section_id => $section ) {
				$full_section_id = 'elementor_' . $section_id . '_section';

				$label = isset( $section['label'] ) ? $section['label'] : '';

				$section_callback = isset( $section['callback'] ) ? $section['callback'] : '__return_empty_string';

				add_settings_section( $full_section_id, $label, $section_callback, static::PAGE_ID );

				foreach ( $section['fields'] as $field_id => $field ) {
					$full_field_id = ! empty( $field['full_field_id'] ) ? $field['full_field_id'] : 'elementor_' . $field_id;

					$field['field_args']['id'] = $full_field_id;

					$field_classes = [ $full_field_id ];

					if ( ! empty( $field['class'] ) ) {
						$field_classes[] = $field['field_args']['class'];
					}

					$field['field_args']['class'] = implode( ' ', $field_classes );

					if ( ! isset( $field['render'] ) ) {
						$field['render'] = [ $controls_class_name, 'render' ];
					}

					add_settings_field(
						$full_field_id,
						isset( $field['label'] ) ? $field['label'] : '',
						$field['render'],
						static::PAGE_ID,
						$full_section_id,
						$field['field_args']
					);

					$setting_args = [];

					if ( ! empty( $field['setting_args'] ) ) {
						$setting_args = $field['setting_args'];
					}

					register_setting( static::PAGE_ID, $full_field_id, $setting_args );
				}
			}
		}
	}

	/**
	 * Display settings page.
	 *
	 * Output the content for the settings page.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function display_settings_page() {
		$this->register_settings_fields();

		$tabs = $this->get_tabs();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( $this->get_page_title() ); ?></h1>
			<div id="elementor-settings-tabs-wrapper" class="nav-tab-wrapper">
				<?php
				foreach ( $tabs as $tab_id => $tab ) {
					if ( ! $this->should_render_tab( $tab ) ) {
						continue;
					}

					$active_class = '';

					if ( 'general' === $tab_id ) {
						$active_class = ' nav-tab-active';
					}

					$sanitized_tab_id = esc_attr( $tab_id );
					$sanitized_tab_label = esc_html( $tab['label'] );

					// PHPCS - Escaped the relevant strings above.
					echo "<a id='elementor-settings-tab-{$sanitized_tab_id}' class='nav-tab{$active_class}' href='#tab-{$sanitized_tab_id}'>{$sanitized_tab_label}</a>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<form id="elementor-settings-form" method="post" action="options.php">
				<?php
				settings_fields( static::PAGE_ID );

				foreach ( $tabs as $tab_id => $tab ) {
					if ( ! $this->should_render_tab( $tab ) ) {
						continue;
					}

					$active_class = '';

					if ( 'general' === $tab_id ) {
						$active_class = ' elementor-active';
					}

					$sanitized_tab_id = esc_attr( $tab_id );

					// PHPCS - $active_class is a non-dynamic string and $sanitized_tab_id is escaped above.
					echo "<div id='tab-{$sanitized_tab_id}' class='elementor-settings-form-page{$active_class}'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					foreach ( $tab['sections'] as $section_id => $section ) {
						if ( ! $this->should_render_section( $section ) ) {
							continue;
						}

						$full_section_id = 'elementor_' . $section_id . '_section';

						if ( ! empty( $section['label'] ) ) {
							echo '<h2>' . esc_html( $section['label'] ) . '</h2>';
						}

						if ( ! empty( $section['callback'] ) ) {
							$section['callback']();
						}

						echo '<table class="form-table">';

						do_settings_fields( static::PAGE_ID, $full_section_id );

						echo '</table>';
					}

					echo '</div>';
				}

				submit_button( __( 'Save Changes', 'elementor' ), 'primary', 'submit', true, [ 'data-id' => 'elementor-settings-button-save-changes' ] );
				?>
			</form>
		</div><!-- /.wrap -->
		<?php
	}

	public function get_usage_fields() {
		return [
			'allow_tracking' => [
				'label' => esc_html__( 'Data Sharing', 'elementor' ),
				'field_args' => [
					'type' => 'checkbox',
					'value' => 'yes',
					'default' => '',
					'sub_desc' => sprintf(
						'%1$s <a href="https://go.elementor.com/usage-data-tracking/" target="_blank">%2$s</a>',
						esc_html__( 'Become a super contributor by helping us understand how you use our service to enhance your experience and improve our product.', 'elementor' ),
						esc_html__( 'Learn more', 'elementor' )
					),
				],
				'setting_args' => [ __NAMESPACE__ . '\Tracker', 'check_for_settings_optin' ],
			],
		];
	}

	/**
	 * Ensure tabs.
	 *
	 * Make sure the settings page has tabs before inserting any new sections or
	 * fields.
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function ensure_tabs() {
		if ( null === $this->tabs ) {
			$this->tabs = $this->create_tabs();

			$page_id = static::PAGE_ID;

			/**
			 * After create settings.
			 *
			 * Fires after the settings are created in Elementor admin page.
			 *
			 * The dynamic portion of the hook name, `$page_id`, refers to the current page ID.
			 *
			 * @since 1.0.0
			 *
			 * @param Settings_Page $this The settings page.
			 */
			do_action( "elementor/admin/after_create_settings/{$page_id}", $this );
		}
	}

	/**
	 * Should it render the settings tab
	 *
	 * @param array $tab
	 *
	 * @return bool
	 */
	private function should_render_tab( $tab ) {
		// BC - When 'show_if' prop is not exists, it actually should render the tab.
		return ! empty( $tab['sections'] ) && ( ! isset( $tab['show_if'] ) || $tab['show_if'] );
	}

	/**
	 * Should it render the settings section
	 *
	 * @param array $section
	 *
	 * Since 3.19.0
	 *
	 * @return bool
	 */
	private function should_render_section( $section ) {
		// BC - When 'show_if' prop is not exists, it actually should render the section.
		return ! isset( $section['show_if'] ) || $section['show_if'];
	}
}
