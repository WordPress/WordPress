<?php
/**
 * Admin page for the snippets library.
 *
 * @package WPCode
 */

/**
 * WPCode_Admin_Page_Library class.
 */
class WPCode_Admin_Page_Library extends WPCode_Admin_Page {

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-library';
	/**
	 * We always show the library on this page.
	 *
	 * @var bool
	 */
	protected $show_library = true;

	/**
	 *  The default view.
	 *
	 * @var string
	 */
	public $view = 'library';

	/**
	 * The object used for loading data on this page.
	 *
	 * @var WPCode_Library
	 */
	protected $data_handler;

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Library', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Setup page-specific views.
	 *
	 * @return void
	 */
	protected function setup_views() {
		$this->views = array(
			'library'      => __( 'Snippets', 'insert-headers-and-footers' ),
			'my_library'   => __( 'My Library', 'insert-headers-and-footers' ),
			'my_favorites' => __( 'My Favorites', 'insert-headers-and-footers' ),
		);
	}

	/**
	 * Add page-specific hooks.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->process_message();
		add_action( 'admin_init', array( $this, 'maybe_add_from_library' ) );
	}

	/**
	 * Handle grabbing snippets from the library.
	 *
	 * @return void
	 */
	public function maybe_add_from_library() {
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'wpcode_add_from_library' ) ) {
			return;
		}
		$library_id = isset( $_GET['snippet_library_id'] ) ? absint( $_GET['snippet_library_id'] ) : 0;

		if ( empty( $library_id ) ) {
			return;
		}

		$snippet = $this->get_data_handler()->create_new_snippet( $library_id );

		if ( $snippet ) {
			$url = add_query_arg(
				array(
					'page'       => 'wpcode-snippet-manager',
					'snippet_id' => $snippet->get_id(),
				),
				admin_url( 'admin.php' )
			);
		} else {
			$url = add_query_arg(
				array(
					'message' => 1,
				),
				remove_query_arg(
					array(
						'_wpnonce',
						'snippet_library_id',
					)
				)
			);
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Markup for the Library page content.
	 *
	 * @return void
	 */
	public function output_content() {

		if ( method_exists( $this, 'output_view_' . $this->view ) ) {
			call_user_func( array( $this, 'output_view_' . $this->view ) );
		}
	}

	/**
	 * Ouptut the library content (default view).
	 *
	 * @return void
	 */
	public function output_view_library() {
		$library_data = $this->get_data_handler()->get_data();
		$categories   = $library_data['categories'];
		$snippets     = $library_data['snippets'];

		$this->get_library_markup( $categories, $snippets );
		$this->library_preview_modal_content();
		$this->library_connect_banner_template();
	}

	/**
	 * For this page we output a menu.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<ul class="wpcode-admin-tabs">
			<?php
			foreach ( $this->views as $slug => $label ) {
				$class = $this->view === $slug ? 'active' : '';
				?>
				<li>
					<a href="<?php echo esc_url( $this->get_view_link( $slug ) ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Process messages specific to this page.
	 *
	 * @return void
	 */
	public function process_message() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! isset( $_GET['message'] ) ) {
			return;
		}

		$messages = array(
			1 => __( 'We encountered an error while trying to load the snippet data. Please try again.', 'insert-headers-and-footers' ),
		);
		$message  = absint( $_GET['message'] );
		// phpcs:enable WordPress.Security.NonceVerification

		if ( ! isset( $messages[ $message ] ) ) {
			return;
		}

		$this->set_error_message( $messages[ $message ] );

	}

	/**
	 * Markup for the "My Library" page.
	 *
	 * @return void
	 */
	public function output_view_my_library() {
		$this->blurred_placeholder_items();
		// Show upsell.
		echo WPCode_Admin_Page::get_upsell_box(
			__( 'My Library is a PRO Feature', 'insert-headers-and-footers' ),
			'<p>' . __( 'Upgrade to WPCode PRO today and save your snippets in the WPCode Library directly from the plugin and import them with 1-click on other sites.', 'insert-headers-and-footers' ) . '</p>',
			array(
				'text' => __( 'Upgrade to PRO and Unlock "My Library"', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'library-page', 'my-library', 'upgrade-and-unlock' ),
			),
			array(
				'text' => esc_html__( 'Learn more about all the features', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'library-page', 'my-library', 'features' ),
			),
			array(
				__( 'Save your snippets to the WPCode Library', 'insert-headers-and-footers' ),
				__( 'Import snippets from the WPCode Library', 'insert-headers-and-footers' ),
				__( 'Set up new websites faster', 'insert-headers-and-footers' ),
				__( 'Easily implement features on multiple sites', 'insert-headers-and-footers' ),
				__( 'Edit snippets in the WPCode Library', 'insert-headers-and-footers' ),
				__( 'Load favorite snippets in the plugin', 'insert-headers-and-footers' ),
			)
		);
	}

	/**
	 * Markup for the "My Library" page.
	 *
	 * @return void
	 */
	public function output_view_my_favorites() {
		$this->blurred_placeholder_items();
		// Show upsell.
		echo WPCode_Admin_Page::get_upsell_box(
			__( 'My Favorites is a PRO Feature', 'insert-headers-and-footers' ),
			'<p>' . __( 'Upgrade to WPCode PRO today and see the snippets you starred in the WPCode Library directly in the plugin.', 'insert-headers-and-footers' ) . '</p>',
			array(
				'text' => __( 'Upgrade to PRO and Unlock "My Favorites"', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'library-page', 'my-favorites', 'upgrade-and-unlock' ),
			),
			array(
				'text' => esc_html__( 'Learn more about all the features', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'library-page', 'my-favorites', 'features' ),
			),
			array(
				__( 'Load favorite snippets in the plugin', 'insert-headers-and-footers' ),
				__( 'Import snippets from the WPCode Library', 'insert-headers-and-footers' ),
				__( 'Save your snippets to the WPCode Library', 'insert-headers-and-footers' ),
				__( 'Set up new websites faster', 'insert-headers-and-footers' ),
				__( 'Easily implement features on multiple sites', 'insert-headers-and-footers' ),
				__( 'Edit snippets in the WPCode Library', 'insert-headers-and-footers' ),
			)
		);
	}

	/**
	 * Get placeholder library items in a blurred box.
	 *
	 * @return void
	 */
	public function blurred_placeholder_items() {
		$snippets = $this->get_placeholder_library_items();
		echo '<div class="wpcode-blur-area">';
		$this->get_library_markup( $snippets['categories'], $snippets['snippets'] );
		echo '</div>';
	}

	/**
	 * Get an array of items for the library blurred background.
	 *
	 * @return array
	 */
	public function get_placeholder_library_items() {
		$categories = array(
			'*'           => 'Most Popular',
			'admin'       => 'Admin',
			'archive'     => 'Archive',
			'attachments' => 'Attachments',
			'comments'    => 'Comments',
			'disable'     => 'Disable',
			'login'       => 'Login',
			'rss-feeds'   => 'RSS Feeds',
			'search'      => 'Search',
		);

		$categories_parsed = array();
		foreach ( $categories as $slug => $name ) {
			$categories_parsed[] = array(
				'slug' => $slug,
				'name' => $name,
			);
		}

		return array(
			'categories' => $categories_parsed,
			'snippets'   => array(
				array(
					'library_id' => 0,
					'title'      => 'Add an Edit Post Link to Archives',
					'code'       => '',
					'note'       => 'Make it easier to edit posts when viewing archives. Or on single pages. If you...',
					'categories' =>
						array(
							0 => 'archive',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Add Featured Images to RSS Feeds',
					'code'       => '',
					'note'       => 'Extend your site\'s RSS feeds by including featured images in the feed.',
					'categories' =>
						array(
							0 => 'rss-feeds',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Add the Page Slug to Body Class',
					'code'       => '',
					'note'       => 'Add the page slug to the body class for better styling.',
					'categories' =>
						array(
							0 => 'archive',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Allow SVG Files Upload',
					'code'       => '',
					'note'       => 'Add support for SVG files to be uploaded in WordPress media.',
					'categories' =>
						array(
							0 => 'most-popular',
							1 => 'attachments',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Automatically Link Featured Images to Posts',
					'code'       => '',
					'note'       => 'Wrap featured images in your theme in links to posts.',
					'categories' =>
						array(
							0 => 'attachments',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Change "Howdy Admin" in Admin Bar',
					'code'       => '',
					'note'       => 'Customize the "Howdy" message in the admin bar.',
					'categories' =>
						array(
							0 => 'admin',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Change Admin Panel Footer Text',
					'code'       => '',
					'note'       => 'Display custom text in the admin panel footer with this snippet.',
					'categories' =>
						array(
							0 => 'admin',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Change Excerpt Length',
					'code'       => '',
					'note'       => 'Update the length of the Excerpts on your website using this snippet.',
					'categories' =>
						array(
							0 => 'archive',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Change Read More Text for Excerpts',
					'code'       => '',
					'note'       => 'Customize the "Read More" text that shows up after excerpts.',
					'categories' =>
						array(
							0 => 'archive',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Completely Disable Comments',
					'code'       => '',
					'note'       => 'Disable comments for all post types, in the admin and the frontend.',
					'categories' =>
						array(
							0 => 'most-popular',
							1 => 'comments',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Delay Posts in RSS Feeds',
					'code'       => '',
					'note'       => 'Add a delay before published posts show up in the RSS feeds.',
					'categories' =>
						array(
							0 => 'rss-feeds',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Disable Attachment Pages',
					'code'       => '',
					'note'       => 'Hide the Attachment/Attachments pages on the frontend from all visitors.',
					'categories' =>
						array(
							0 => 'most-popular',
							1 => 'attachments',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Disable Automatic Updates',
					'code'       => '',
					'note'       => 'Use this snippet to completely disable automatic updates on your website.',
					'categories' =>
						array(
							0 => 'most-popular',
							1 => 'disable',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Disable Automatic Updates Emails',
					'code'       => '',
					'note'       => 'Stop getting emails about automatic updates on your WordPress site.',
					'categories' =>
						array(
							0 => 'most-popular',
							1 => 'disable',
						),
					'code_type'  => 'php',
				),
				array(
					'library_id' => 0,
					'title'      => 'Disable Gutenberg Editor (use Classic Editor)',
					'code'       => '',
					'note'       => 'Switch back to the Classic Editor by disablling the Block Editor.',
					'categories' =>
						array(
							0 => 'most-popular',
							1 => 'admin',
						),
					'code_type'  => 'php',
				),
			),
		);
	}

	/**
	 * Get the data handler for this page.
	 *
	 * @return WPCode_Library
	 */
	public function get_data_handler() {
		if ( ! isset( $this->data_handler ) ) {
			$this->data_handler = wpcode()->library;
		}

		return $this->data_handler;
	}

}
