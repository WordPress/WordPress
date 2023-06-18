<?php
/**
 * Base class for the WPCode snippets metabox.
 *
 * @package WPCode
 */

/**
 * WPCode metabox snippets.
 */
abstract class WPCode_Metabox_Snippets {

	/**
	 * Id used for registering the metabox using add_meta_box.
	 *
	 * @var string
	 */
	public $id = 'wpcode-metabox-snippets';

	/**
	 * Title of the metabox.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Tabs for this metabox.
	 *
	 * @var array
	 */
	public $tabs;

	/**
	 * Register the metabox.
	 */
	public function __construct() {
		$this->load_strings();
		$this->hooks();
	}

	/**
	 * Load the translatable strings.
	 *
	 * @return void
	 */
	public function load_strings() {
		$this->title = __( 'WPCode Page Scripts', 'insert-headers-and-footers' );
		$this->tabs  = array(
			'header' => __( 'Header', 'insert-headers-and-footers' ),
			'footer' => __( 'Footer', 'insert-headers-and-footers' ),
		);

		$body_supported = function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' );

		if ( $body_supported ) {
			$this->tabs['body'] = __( 'Body', 'insert-headers-and-footers' );
		}

		$this->tabs['code'] = __( 'Custom Code Snippet', 'insert-headers-and-footers' );

	}

	/**
	 * Add hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'admin_head', array( $this, 'close_metabox_for_current_screen' ) );
	}

	/**
	 * Make sure the metabox is closed by default.
	 *
	 * @return void
	 */
	public function close_metabox_for_current_screen() {
		// Close the metabox by default.
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) ) {
			return;
		}
		if ( apply_filters( 'wpcode_metabox_scripts_force_collapse', true, $screen ) ) {
			add_filter(
				'get_user_option_closedpostboxes_' . $screen->id,
				array(
					$this,
					'add_metabox_to_user_closed',
				)
			);
		}
	}

	/**
	 * Add our metabox id to the array of closed metaboxes when the page loads.
	 *
	 * @param mixed $closed The array of closed metaboxes.
	 *
	 * @return array
	 */
	public function add_metabox_to_user_closed( $closed ) {
		// Make sure it's an array.
		if ( ! is_array( $closed ) ) {
			$closed = array();
		}
		$closed[] = $this->id;

		return $closed;
	}

	/**
	 * Use `add_meta_box` to register the metabox for this class.
	 *
	 * @param string $post_type The post type of the screen where metaboxes are loaded.
	 *
	 * @return void
	 */
	public function register_metabox( $post_type ) {
		// Don't show the metabox to users who aren't allowed to manage snippets.
		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			return;
		}

		if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
			// Don't load the metabox when headers & footers mode is enabled.
			return;
		}

		$post_type_details = get_post_type_object( $post_type );

		// Add metabox only on public post types.
		if ( empty( $post_type_details->public ) ) {
			return;
		}

		add_meta_box(
			$this->id,
			$this->title,
			array(
				$this,
				'output_metabox_content',
			),
			$post_type,
			'normal',
			apply_filters( 'wpcode_post_metabox_priority', 'default' )
		);
	}

	/**
	 * Metabox content output callback.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function output_metabox_content( $post ) {
		$this->metabox_start();
		echo '<div class="wpcode-content">';
		$this->tabs_navigation();
		$this->tabs_content( $post );
		echo '</div>';
		$this->metabox_end();
	}

	/**
	 * Output the menu for switching between tabs.
	 *
	 * @return void
	 */
	public function tabs_navigation() {
		if ( empty( $this->tabs ) ) {
			return;
		}
		?>
		<div class="wpcode-admin-tabs-navigation">
			<ul class="wpcode-admin-tabs">
				<?php
				$class = 'active';
				foreach ( $this->tabs as $tab_id => $tab_name ) {
					?>
					<li>
						<button type="button" data-target="<?php echo esc_attr( $this->get_tab_html_id( $tab_id ) ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $tab_name ); ?></button>
					</li>
					<?php
					$class = '';
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Output the tabs content using tab-specific methods by their ids.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function tabs_content( $post ) {
		$tab_ids = array_keys( $this->tabs );
		$active  = true;
		foreach ( $tab_ids as $tab_id ) {
			$class = 'wpcode-admin-tab-content';

			$class .= $active ? ' active' : '';
			printf(
				'<div class="%1$s" id="%2$s">',
				esc_attr( $class ),
				esc_attr( $this->get_tab_html_id( $tab_id ) )
			);
			if ( method_exists( $this, 'output_tab_' . $tab_id ) ) {
				call_user_func( array( $this, 'output_tab_' . $tab_id ), $post );
			} else {
				$this->output_tab( $tab_id, $post );
			}
			$active = false;
			echo '</div>';
		}
	}

	/**
	 * Generic tab content output method.
	 *
	 * @param string  $tab_id The tab id.
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function output_tab( $tab_id, $post ) {
	}

	/**
	 * Get a standard tab id from the array key.
	 *
	 * @param string $tab_id The tab id.
	 *
	 * @return string
	 */
	public function get_tab_html_id( $tab_id ) {
		return 'wpcode-tab-' . $tab_id;
	}

	/**
	 * Method for running logic at the start of the metabox.
	 *
	 * @return void
	 */
	public function metabox_start() {
	}

	/**
	 * Method for running logic at the end of the metabox, loading scripts, for example.
	 *
	 * @return void
	 */
	public function metabox_end() {
	}
}
