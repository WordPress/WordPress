<?php
/**
 * Tools admin page.
 *
 * @package WPCode
 */

/**
 * Class for the Tools admin page.
 */
class WPCode_Admin_Page_Tools extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-tools';

	/**
	 * The action used for the nonce.
	 *
	 * @var string
	 */
	private $action = 'wpcode-tools';

	/**
	 * Default view.
	 *
	 * @var string
	 */
	public $view = 'import';

	/**
	 * The nonce name field.
	 *
	 * @var string
	 */
	private $nonce_name = 'wpcode-tools_nonce';

	/**
	 * Available importers.
	 *
	 * @var WPCode_Importer_Type[]
	 */
	private $importers = array();

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Tools', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Register hook on admin init just for this page.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->process_message();
		add_action( 'admin_init', array( $this, 'submit_listener' ) );
		add_action( 'admin_print_scripts', array( $this, 'importer_templates' ) );
		add_filter( 'wpcode_admin_js_data', array( $this, 'add_tools_data' ) );
		// Listen for log delete requests.
		add_action( 'admin_init', array( $this, 'maybe_delete_log' ) );
		// Localize script data.
		add_filter( 'wpcode_admin_js_data', array( $this, 'add_tools_strings' ) );
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
			1 => sprintf(
			// Translators: Adds a link to the snippets list in the admin.
				__( 'Import was successfully finished. You can go and check %1$syour snippets%2$s.', 'insert-headers-and-footers' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=wpcode' ) ) . '">',
				'</a>'
			),
		);
		$message  = absint( $_GET['message'] );
		// phpcs:enable WordPress.Security.NonceVerification

		if ( ! isset( $messages[ $message ] ) ) {
			return;
		}

		$this->set_success_message( $messages[ $message ] );

	}

	/**
	 * @return WPCode_Importer_Type[]
	 */
	public function get_importers() {
		if ( empty( $this->importers ) ) {
			$this->importers = wpcode()->importers->get_importers();
		}

		return $this->importers;
	}

	/**
	 * The Tools page output.
	 *
	 * @return void
	 */
	public function output_content() {
		if ( method_exists( $this, 'output_view_' . $this->view ) ) {
			call_user_func( array( $this, 'output_view_' . $this->view ) );
		}
	}

	/**
	 * The Import view.
	 *
	 * @return void
	 */
	public function output_view_import() {
		?>
		<div class="wpcode-setting-row wpcode-tools">
			<h3><?php esc_html_e( 'WPCode Snippet Import', 'insert-headers-and-footers' ); ?></h3>
			<p><?php esc_html_e( 'Select a WPCode export file', 'insert-headers-and-footers' ); ?></p>

			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( $this->get_page_action_url() ); ?>">
				<div class="wpcode-file-upload">
					<input type="file" name="file" id="wpcode-tools-snippets-import" class="inputfile" data-multiple-caption="{count} files selected" accept=".json">
					<label for="wpcode-tools-snippets-import">
						<span class="wpcode-file-field"><span class="placeholder"><?php esc_html_e( 'No file chosen', 'insert-headers-and-footers' ); ?></span></span>
						<strong class="wpcode-button wpcode-button-secondary wpcode-button-icon">
							<?php
							wpcode_icon( 'upload', 12, 12 );
							esc_html_e( 'Choose a file&hellip;', 'insert-headers-and-footers' );
							?>
						</strong>
					</label>
				</div>
				<br>
				<input type="hidden" name="action" value="import_snippets">
				<?php wp_nonce_field( $this->action, $this->nonce_name ); ?>
				<button name="submit-import" class="wpcode-button">
					<?php esc_html_e( 'Import', 'insert-headers-and-footers' ); ?>
				</button>
			</form>
		</div>
		<hr/>
		<div class="wpcode-setting-row wpcode-tools">
			<h3><?php esc_html_e( 'Import from Other Code Plugins', 'insert-headers-and-footers' ); ?></h3>
			<p><?php esc_html_e( 'WPCode makes it easy for you to switch by allowing you import your third-party snippet plugins with a single click.', 'insert-headers-and-footers' ); ?></p>
			<form action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
				<select name="provider" id="wpcode-plugins-importer" required>
					<option value=""><?php esc_html_e( 'Select previous Code plugin', 'insert-headers-and-footers' ); ?></option>
					<?php
					foreach ( $this->get_importers() as $importer ) {
						$status = '';

						if ( empty( $importer['installed'] ) ) {
							$status = esc_html__( 'Not Installed', 'insert-headers-and-footers' );
						} elseif ( empty( $importer['active'] ) ) {
							$status = esc_html__( 'Not Active', 'insert-headers-and-footers' );
						}
						printf(
							'<option value="%s" %s>%s %s</option>',
							esc_attr( $importer['slug'] ),
							! empty( $status ) ? 'disabled' : '',
							esc_html( $importer['name'] ),
							! empty( $status ) ? '(' . esc_html( $status ) . ')' : '' //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
					}
					?>
				</select>
				<input type="hidden" name="view" value="importer"/>
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page_slug ); ?>"/>
				<br/>
				<button class="wpcode-button"><?php esc_html_e( 'Import', 'insert-headers-and-footers' ); ?></button>
			</form>
		</div>
		<hr/>
		<?php
	}

	/**
	 * The export view.
	 *
	 * @return void
	 */
	public function output_view_export() {
		?>
		<div class="wpcode-setting-row wpcode-tools">
			<h3><?php esc_html_e( 'Export Code Snippets', 'insert-headers-and-footers' ); ?></h3>
			<form action="<?php echo esc_url( $this->get_page_action_url() ); ?>" method="post">
				<?php
				$this->metabox_row(
					__( 'Status', 'insert-headers-and-footers' ),
					$this->get_status_dropdown(),
					'wpcode_export_status'
				);
				$this->metabox_row(
					__( 'Code type', 'insert-headers-and-footers' ),
					$this->get_code_type_checkboxes(),
					'wpcode_export_code_type'
				);
				$this->metabox_row(
					__( 'Tags', 'insert-headers-and-footers' ),
					$this->get_tags_checkboxes(),
					'wpcode_export_tags'
				);

				wp_nonce_field( $this->action, $this->nonce_name );
				?>
				<button type="submit" name="wpcode-export-snippets" class="wpcode-button"><?php esc_html_e( 'Export Snippets', 'insert-headers-and-footers' ); ?></button>
			</form>
		</div>
		<?php
	}

	/**
	 * The System Info view.
	 *
	 * @return void
	 */
	public function output_view_info() {
		?>
		<div class="wpcode-setting-row">
			<h3><?php esc_html_e( 'System Information', 'insert-headers-and-footers' ); ?></h3>
			<textarea class="info-area" readonly><?php echo esc_textarea( $this->get_system_info() ); ?></textarea>
		</div>
		<hr/>
		<div class="wpcode-setting-row">
			<h3 id="ssl-verify"><?php esc_html_e( 'Test SSL Connections', 'insert-headers-and-footers' ); ?></h3>
			<p><?php esc_html_e( 'Click the button below to verify your web server can perform SSL connections successfully.', 'insert-headers-and-footers' ); ?></p>
			<button type="button" id="wpcode-ssl-verify" class="wpcode-button">
				<?php esc_html_e( 'Test Connection', 'insert-headers-and-footers' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Importer view (from other plugins).
	 *
	 * @return void
	 */
	public function output_view_importer() {
		$provider = ! empty( $_GET['provider'] ) ? sanitize_text_field( wp_unslash( $_GET['provider'] ) ) : '';// phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$importers     = wpcode()->importers->importers;
		$snippets      = array();
		$provider_name = '';
		if ( isset( $importers[ $provider ] ) ) {
			$snippets      = $importers[ $provider ]->get_snippets();
			$provider_name = $importers[ $provider ]->name;
		}

		?>
		<h2><?php esc_html_e( 'Snippets import', 'insert-headers-and-footers' ); ?></h2>
		<hr/>
		<div id="wpcode-importer-snippets">
			<div class="wpcode-setting-row wpcode-tools">
				<p><?php esc_html_e( 'Select the Snippets you would like to import.', 'insert-headers-and-footers' ); ?></p>

				<div class="wpcode-checkbox-multiselect-columns">
					<div class="first-column">
						<h5 class="header"><?php esc_html_e( 'Available Snippets', 'insert-headers-and-footers' ); ?></h5>

						<ul>
							<?php
							if ( empty( $snippets ) ) {
								echo '<li>' . esc_html__( 'No snippets found.', 'insert-headers-and-footers' ) . '</li>';
							} else {
								foreach ( $snippets as $id => $snippet ) {
									printf(
										'<li><label><input type="checkbox" name="snippets[]" value="%s">%s</label></li>',
										esc_attr( $id ),
										esc_attr( sanitize_text_field( $snippet ) )
									);
								}
							}
							?>
						</ul>

						<?php if ( ! empty( $snippets ) ) : ?>
							<a href="#" class="all"><?php esc_html_e( 'Select All', 'insert-headers-and-footers' ); ?></a>
						<?php endif; ?>

					</div>
					<div class="second-column">
						<h5 class="header"><?php esc_html_e( 'Snippets to Import', 'insert-headers-and-footers' ); ?></h5>
						<ul></ul>
					</div>
				</div>
			</div>

			<?php if ( ! empty( $snippets ) ) : ?>
				<p class="submit">
					<input type="hidden" value="<?php echo esc_attr( $provider ); ?>" id="wpcode-importer-provider"/>
					<button class="wpcode-button" id="wpcode-importer-snippets-submit"><?php esc_html_e( 'Import', 'insert-headers-and-footers' ); ?></button>
				</p>
			<?php endif; ?>
		</div>
		<div id="wpcode-importer-process">

			<p class="process-count">
				<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
				<?php
				printf(
					wp_kses(
					// Translators: These add markup to display which snippet out of the total from the provider name.
						__( 'Importing %1$s of %2$s snippets from %3$s.', 'insert-headers-and-footers' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					'<span class="snippet-current">1</span>',
					'<span class="snippet-total">0</span>',
					esc_html( $provider_name )
				);
				?>
			</p>
			<p class="process-completed">
				<?php
				printf(
					wp_kses(
					// Translators: this adds the total snippets count that have been completed.
						__( 'Congrats, the import process has finished! We have successfully imported %s snippets. You can review the results below.', 'insert-headers-and-footers' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					'<span class="snippets-completed"></span>'
				);
				?>
			</p>
			<div class="status"></div>
		</div>
		<?php
	}

	/**
	 * Get a dropdown with the status options.
	 *
	 * @return string
	 */
	public function get_status_dropdown() {
		$options = array(
			'all'     => __( 'All', 'insert-headers-and-footers' ),
			'publish' => __( 'Active', 'insert-headers-and-footers' ),
			'draft'   => __( 'Inactive', 'insert-headers-and-footers' ),
		);

		$select = '<select name="wpcode_export_status" id="wpcode_export_status">';
		foreach ( $options as $value => $label ) {
			$select .= sprintf(
				'<option value="%1$s">%2$s</option>',
				esc_attr( $value ),
				esc_html( $label )
			);
		}
		$select .= '</select>';

		return $select;
	}

	/**
	 * Get all the tags in a checkbox list.
	 *
	 * @return string
	 */
	public function get_code_type_checkboxes() {
		$labels  = wpcode()->execute->get_options();
		$tags    = get_terms(
			array(
				'taxonomy' => 'wpcode_type',
				'count'    => true,
			)
		);
		$options = array();

		if ( empty( $tags ) ) {
			return __( 'No snippets available to export.', 'insert-headers-and-footers' );
		}

		foreach ( $tags as $tag ) {
			$label                    = isset( $labels[ $tag->slug ] ) ? $labels[ $tag->slug ] : $tag->name;
			$options[ $tag->term_id ] = $label . ' (' . $tag->count . ')';
		}

		return $this->get_checkboxes_list( $options, 'wpcode_export_code_type' );
	}

	/**
	 * Get all the tags in a checkbox list.
	 *
	 * @return string
	 */
	public function get_tags_checkboxes() {
		$tags = get_terms(
			array(
				'taxonomy' => 'wpcode_tags',
				'count'    => true,
			)
		);

		if ( empty( $tags ) ) {
			return __( 'No tags available.', 'insert-headers-and-footers' );
		}

		$options = array();
		foreach ( $tags as $tag ) {
			$options[ $tag->term_id ] = $tag->name . ' (' . $tag->count . ')';
		}

		return $this->get_checkboxes_list( $options, 'wpcode_export_tags' );
	}

	/**
	 * Get a list of checkboxes from a key=>value array.
	 *
	 * @param array  $options The options to display as checkboxes.
	 * @param string $name The name used for the input name attribute.
	 *
	 * @return string
	 */
	public function get_checkboxes_list( $options, $name ) {
		$checkboxes = '<div class="wpcode-checkboxes-list">';
		foreach ( $options as $value => $label ) {
			$checkboxes .= sprintf(
				'<label><input type="checkbox" name="%1$s[]" value="%2$s" />%3$s</label>',
				$name,
				$value,
				$label
			);
		}
		$checkboxes .= '</div>';

		return $checkboxes;
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
				if ( 'importer' === $slug ) {
					continue;
				}
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
	 * Setup page-specific views.
	 *
	 * @return void
	 */
	protected function setup_views() {
		$this->views = array(
			'import'   => __( 'Import', 'insert-headers-and-footers' ),
			'export'   => __( 'Export', 'insert-headers-and-footers' ),
			'info'     => __( 'System Info', 'insert-headers-and-footers' ),
			'importer' => __( 'Importer', 'insert-headers-and-footers' ),
			'logs'     => __( 'Logs', 'insert-headers-and-footers' ),
		);
	}

	/**
	 * If the form is submitted attempt to save the values.
	 *
	 * @return void
	 */
	public function submit_listener() {
		if ( ! isset( $_REQUEST[ $this->nonce_name ] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST[ $this->nonce_name ] ), $this->action ) ) {
			// Nonce is missing, so we're not even going to try.
			return;
		}

		if ( isset( $_REQUEST['wpcode-export-snippets'] ) ) {
			$this->handle_export();
		}

		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );
		if ( 'import_snippets' === $action ) {
			$this->handle_import_file();
		}
	}

	/**
	 * Process export form and download a JSON file.
	 *
	 * @return void
	 */
	public function handle_export() {
		// Already verified nonce in parent method @see submit_listener.
		// phpcs:disable WordPress.Security.NonceVerification
		$status     = isset( $_POST['wpcode_export_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wpcode_export_status'] ) ) : 'all';
		$code_types = isset( $_POST['wpcode_export_code_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wpcode_export_code_type'] ) ) : array();
		$tags       = isset( $_POST['wpcode_export_tags'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wpcode_export_tags'] ) ) : array();
		if ( 'all' === $status ) {
			$status = array(
				'publish',
				'draft',
			);
		}
		$tax_query = array();

		if ( ! empty( $code_types ) ) {
			$tax_query[] = array(
				'taxonomy' => 'wpcode_type',
				'terms'    => $code_types,
			);
		}
		if ( ! empty( $tags ) ) {
			$tax_query[] = array(
				'taxonomy' => 'wpcode_tags',
				'terms'    => $tags,
			);
		}

		$query_args = array(
			'post_type'   => 'wpcode',
			'post_status' => $status,
			'nopaging'    => true,
		);
		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}
		$export   = array();
		$snippets = get_posts( $query_args );

		foreach ( $snippets as $snippet ) {
			$snippet                          = new WPCode_Snippet( $snippet );
			$snippet_data                     = $snippet->get_data_for_caching();
			$snippet_data['tags']             = $snippet->get_tags();
			$snippet_data['note']             = $snippet->get_note();
			$snippet_data['cloud_id']         = $snippet->get_cloud_id();
			$snippet_data['custom_shortcode'] = $snippet->get_custom_shortcode();

			$export[] = apply_filters( 'wpcode_export_snippet_data', $snippet_data, $snippet );
		}

		$export = array_reverse( $export );

		ignore_user_abort( true );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=wpcode-snippets-export-' . current_time( 'Y-m-d' ) . '.json' );
		header( 'Expires: 0' );

		echo wp_json_encode( $export );
		exit;
	}

	/**
	 * Process import file.
	 *
	 * @return void
	 */
	public function handle_import_file() {

		$ext = '';

		if ( isset( $_FILES['file']['name'] ) ) {
			$ext = strtolower( pathinfo( sanitize_text_field( wp_unslash( $_FILES['file']['name'] ) ), PATHINFO_EXTENSION ) );
		}

		if ( 'json' !== $ext ) {
			wp_die(
				esc_html__( 'Please upload a valid .json snippets export file.', 'insert-headers-and-footers' ),
				esc_html__( 'Error', 'insert-headers-and-footers' ),
				array(
					'response' => 400,
				)
			);
		}

		$tmp_name = isset( $_FILES['file']['tmp_name'] ) ? sanitize_text_field( $_FILES['file']['tmp_name'] ) : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- wp_unslash() breaks upload on Windows.
		$snippets = json_decode( $this->remove_utf8_bom( file_get_contents( $tmp_name ) ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( empty( $snippets ) || ! is_array( $snippets ) ) {
			wp_die(
				esc_html__( 'Snippets data cannot be imported.', 'insert-headers-and-footers' ),
				esc_html__( 'Error', 'insert-headers-and-footers' ),
				array(
					'response' => 400,
				)
			);
		}

		foreach ( $snippets as $snippet ) {
			if ( empty( $snippet['code_type'] ) ) {
				// Just a minimal check that we have some required fields.
				continue;
			}
			if ( isset( $snippet['id'] ) ) {
				// We don't want to update existing snippets/posts.
				unset( $snippet['id'] );
			}

			$snippet         = apply_filters( 'wpcode_import_snippet_data', $snippet );
			$snippet['code'] = isset( $snippet['code'] ) ? wp_slash( $snippet['code'] ) : '';
			$new_snippet     = new WPCode_Snippet( $snippet );
			$new_snippet->save();

		}

		wp_safe_redirect(
			add_query_arg( 'message', 1 )
		);
		exit;
	}

	/**
	 * Remove UTF-8 BOM signature if it is present.
	 *
	 * @param string $string String to process.
	 *
	 * @return string
	 */
	public function remove_utf8_bom( $string ) {
		if ( strpos( bin2hex( $string ), 'efbbbf' ) === 0 ) {
			$string = substr( $string, 3 );
		}

		return $string;
	}

	/**
	 * Templates used by the importer in JS.
	 *
	 * @return void
	 */
	public function importer_templates() {
		?>
		<script type="text/template" id="wpcode-importer-status-update">
			<div class="item">
				<div class="wpcode-clear">
					<span class="name">
						<?php wpcode_icon( 'check', 16, 13 ); ?>
						<span></span>
					</span>
					<span class="actions">
						<a href="" target="_blank"><?php esc_html_e( 'Edit', 'insert-headers-and-footers' ); ?></a>
					</span>
				</div>
			</div>
		</script>
		<?php
	}

	/**
	 * Add tools-specific localization data.
	 *
	 * @param array $data Localization data.
	 *
	 * @return array
	 */
	public function add_tools_data( $data ) {
		$data['testing'] = esc_html__( 'Testing', 'insert-headers-and-footers' );

		return $data;
	}

	/**
	 * Get system information.
	 *
	 * Based on a function from Easy Digital Downloads by Pippin Williamson.
	 *
	 * @link https://github.com/easydigitaldownloads/easy-digital-downloads/blob/master/includes/admin/tools.php#L470
	 *
	 * @return string
	 */
	public function get_system_info() {

		$data = '### Begin System Info ###' . "\n\n";

		$data .= $this->site_info();
		$data .= $this->wp_info();
		$data .= $this->uploads_info();
		$data .= $this->plugins_info();
		$data .= $this->server_info();

		$data .= "\n" . '### End System Info ###';

		return $data;
	}

	/**
	 * Get Site info.
	 *
	 * @return string
	 */
	private function site_info() {

		$data = "\n" . '-- Site Info' . "\n\n";
		$data .= 'Site URL:                 ' . site_url() . "\n";
		$data .= 'Home URL:                 ' . home_url() . "\n";
		$data .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

		return $data;
	}

	/**
	 * Get WordPress Configuration info.
	 *
	 * @return string
	 */
	private function wp_info() {

		global $wpdb;

		$theme_data = wp_get_theme();
		$theme      = $theme_data->name . ' ' . $theme_data->version;

		$data = "\n" . '-- WordPress Configuration' . "\n\n";
		$data .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$data .= 'Language:                 ' . get_locale() . "\n";
		$data .= 'User Language:            ' . get_user_locale() . "\n";
		$data .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
		$data .= 'Active Theme:             ' . $theme . "\n";
		$data .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";

		// Only show page specs if front page is set to 'page'.
		if ( get_option( 'show_on_front' ) === 'page' ) {
			$front_page_id = get_option( 'page_on_front' );
			$blog_page_id  = get_option( 'page_for_posts' );

			$data .= 'Page On Front:            ' . ( $front_page_id ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
			$data .= 'Page For Posts:           ' . ( $blog_page_id ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
		}
		$data .= 'ABSPATH:                  ' . ABSPATH . "\n";
		$data .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n"; //phpcs:ignore
		$data .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$data .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
		$data .= 'Registered Post Stati:    ' . implode( ', ', get_post_stati() ) . "\n";
		$data .= 'Revisions:                ' . ( WP_POST_REVISIONS ? WP_POST_REVISIONS > 1 ? 'Limited to ' . WP_POST_REVISIONS : 'Enabled' : 'Disabled' ) . "\n";

		return $data;
	}

	/**
	 * Get Uploads/Constants info.
	 *
	 * @return string
	 */
	private function uploads_info() {

		$data = "\n" . '-- WordPress Uploads/Constants' . "\n\n";
		$data .= 'WP_CONTENT_DIR:           ' . ( defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR ? WP_CONTENT_DIR : 'Disabled' : 'Not set' ) . "\n";
		$data .= 'WP_CONTENT_URL:           ' . ( defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL ? WP_CONTENT_URL : 'Disabled' : 'Not set' ) . "\n";
		$data .= 'UPLOADS:                  ' . ( defined( 'UPLOADS' ) ? UPLOADS ? UPLOADS : 'Disabled' : 'Not set' ) . "\n";

		$uploads_dir = wp_upload_dir();

		$data .= 'wp_uploads_dir() path:    ' . $uploads_dir['path'] . "\n";
		$data .= 'wp_uploads_dir() url:     ' . $uploads_dir['url'] . "\n";
		$data .= 'wp_uploads_dir() basedir: ' . $uploads_dir['basedir'] . "\n";
		$data .= 'wp_uploads_dir() baseurl: ' . $uploads_dir['baseurl'] . "\n";

		return $data;
	}

	/**
	 * Get Plugins info.
	 *
	 * @return string
	 */
	private function plugins_info() {

		// Get plugins that have an update.
		$data = $this->mu_plugins();

		$data .= $this->installed_plugins();
		$data .= $this->multisite_plugins();

		return $data;
	}

	/**
	 * Get MU Plugins info.
	 *
	 * @return string
	 */
	private function mu_plugins() {

		$data = '';

		// Must-use plugins.
		// NOTE: MU plugins can't show updates!
		$muplugins = get_mu_plugins();

		if ( ! empty( $muplugins ) && count( $muplugins ) > 0 ) {
			$data = "\n" . '-- Must-Use Plugins' . "\n\n";

			foreach ( $muplugins as $plugin => $plugin_data ) {
				$data .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
			}
		}

		return $data;
	}

	/**
	 * Get Installed Plugins info.
	 *
	 * @return string
	 */
	private function installed_plugins() {

		$updates = get_plugin_updates();

		// WordPress active plugins.
		$data = "\n" . '-- WordPress Active Plugins' . "\n\n";

		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}
			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$data   .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		// WordPress inactive plugins.
		$data .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}
			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$data   .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		return $data;
	}

	/**
	 * Get Multisite Plugins info.
	 *
	 * @return string
	 */
	private function multisite_plugins() {

		$data = '';

		if ( ! is_multisite() ) {
			return $data;
		}

		$updates = get_plugin_updates();

		// WordPress Multisite active plugins.
		$data = "\n" . '-- Network Active Plugins' . "\n\n";

		$plugins        = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach ( $plugins as $plugin_path ) {
			$plugin_base = plugin_basename( $plugin_path );

			if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
				continue;
			}
			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$plugin = get_plugin_data( $plugin_path );
			$data   .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		return $data;
	}

	/**
	 * Get Server info.
	 *
	 * @return string
	 */
	private function server_info() {

		global $wpdb;

		// Server configuration (really just versions).
		$data = "\n" . '-- Webserver Configuration' . "\n\n";
		$data .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$data .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
		$data .= 'Webserver Info:           ' . ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '' ) . "\n";

		// PHP configs... now we're getting to the important stuff.
		$data .= "\n" . '-- PHP Configuration' . "\n\n";
		$data .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$data .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
		$data .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$data .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$data .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$data .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$data .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// PHP extensions and such.
		$data .= "\n" . '-- PHP Extensions' . "\n\n";
		$data .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . "\n";
		$data .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
		$data .= 'SOAP Client:              ' . ( class_exists( 'SoapClient', false ) ? 'Installed' : 'Not Installed' ) . "\n";
		$data .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

		// Session stuff.
		$data .= "\n" . '-- Session Configuration' . "\n\n";
		$data .= 'Session:                  ' . ( isset( $_SESSION ) ? 'Enabled' : 'Disabled' ) . "\n";

		// The rest of this is only relevant if session is enabled.
		if ( isset( $_SESSION ) ) {
			$data .= 'Session Name:             ' . esc_html( ini_get( 'session.name' ) ) . "\n";
			$data .= 'Cookie Path:              ' . esc_html( ini_get( 'session.cookie_path' ) ) . "\n";
			$data .= 'Save Path:                ' . esc_html( ini_get( 'session.save_path' ) ) . "\n";
			$data .= 'Use Cookies:              ' . ( ini_get( 'session.use_cookies' ) ? 'On' : 'Off' ) . "\n";
			$data .= 'Use Only Cookies:         ' . ( ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off' ) . "\n";
		}

		return $data;
	}

	/**
	 * Output the log viewer.
	 *
	 * @return void
	 */
	public function output_view_logs() {

		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			echo '<p>' . esc_html__( 'You do not have sufficient permissions to view logs.', 'insert-headers-and-footers' ) . '</p>';

			return;
		}

		$logs = wpcode()->logger->get_logs();

		if ( empty( $logs ) ) {
			echo '<p>';
			printf(
			// translators: %1$s: opening anchor tag, %2$s: closing anchor tag.
				esc_html__( 'No logs found. You can enable logging from the %1$ssettings panel%2$s.', 'insert-headers-and-footers' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=wpcode-settings' ) ) . '">',
				'</a>'
			);
			echo '</p>';

			return;
		}
		$selected_log      = $logs[0]['path'];
		$selected_log_name = $logs[0]['filename'];

		if ( isset( $_POST['log'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'wpcode_view_log' ) ) {
			$selected_log_name = sanitize_text_field( wp_unslash( $_POST['log'] ) );
			// Find the log file path.
			foreach ( $logs as $log ) {
				if ( $log['filename'] === $selected_log_name ) {
					$selected_log = $log['path'];
					break;
				}
			}
		}
		// Load the selected log.
		$log_content = file_get_contents( $selected_log ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$delete_log_url = wp_nonce_url(
			add_query_arg(
				array(
					'wpcode_action' => 'delete_log',
					'log'           => str_replace( '.log', '', $selected_log_name ),
				),
				$this->get_page_action_url()
			),
			'wpcode_delete_log'
		);
		// Log picker form.
		?>
		<div class="alignleft">
			<h2><?php echo esc_html( $selected_log_name ); ?>
				<a class="wpcode-button wpcode-button-secondary wpcode-delete-log" href="<?php echo esc_url( $delete_log_url ); ?>"><?php esc_html_e( 'Delete log', 'insert-headers-and-footers' ); ?></a>
			</h2>
		</div>
		<div class="alignright">
			<form method="post" action="<?php echo esc_url( $this->get_page_action_url() ); ?>">
				<select name="log">
					<?php foreach ( $logs as $log ) : ?>
						<option value="<?php echo esc_attr( $log['filename'] ); ?>" <?php selected( $selected_log_name, $log['filename'] ); ?>><?php echo esc_html( $log['filename'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="wpcode-button"><?php esc_html_e( 'View', 'insert-headers-and-footers' ); ?></button>
				<?php wp_nonce_field( 'wpcode_view_log' ); ?>
			</form>
		</div>
		<div class="clear"></div>

		<div id="wpcode-log-data">
			<?php if ( empty( $log_content ) ) : ?>
				<p><?php esc_html_e( 'Log is empty.', 'insert-headers-and-footers' ); ?></p>
			<?php endif; ?>
			<pre><?php echo esc_html( $log_content ); ?></pre>
		</div>
		<?php
	}

	/**
	 * Delete a log file.
	 *
	 * @return void
	 */
	public function maybe_delete_log() {

		if ( ! isset( $_GET['wpcode_action'] ) || 'delete_log' !== $_GET['wpcode_action'] || ! isset( $_GET['log'] ) ) {
			return;
		}

		// Check nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'wpcode_delete_log' ) ) {
			wp_die( esc_html__( 'Link expired. Please refresh the page and retry.', 'insert-headers-and-footers' ) );
		}

		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			echo '<p>' . esc_html__( 'You do not have sufficient permissions to delete logs.', 'insert-headers-and-footers' ) . '</p>';

			return;
		}

		wpcode()->logger->delete_log( sanitize_key( wp_unslash( $_GET['log'] ) ) );

		wp_safe_redirect( $this->get_page_action_url() );
		exit;
	}

	/**
	 * Add tools-specific strings to the JS strings object.
	 *
	 * @param array $data The strings object.
	 *
	 * @return array
	 */
	public function add_tools_strings( $data ) {
		$data['confirm_delete_log'] = __( 'Are you sure you want to delete this log?', 'insert-headers-and-footers' );

		return $data;
	}
}
