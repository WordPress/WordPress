<?php /*

**************************************************************************

Plugin Name:  Regenerate Thumbnails
Description:  Regenerate the thumbnails for one or more of your image uploads. Useful when changing their sizes or your theme.
Plugin URI:   https://alex.blog/wordpress-plugins/regenerate-thumbnails/
Version:      3.0.1
Author:       Alex Mills (Viper007Bond)
Author URI:   https://alex.blog/
Text Domain:  regenerate-thumbnails
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

**************************************************************************

Regenerate Thumbnails is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Regenerate Thumbnails is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Regenerate Thumbnails. If not, see https://www.gnu.org/licenses/gpl-2.0.html.

**************************************************************************/

require( dirname( __FILE__ ) . '/includes/class-regeneratethumbnails-regenerator.php' );
require( dirname( __FILE__ ) . '/includes/class-regeneratethumbnails-rest-controller.php' );

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class RegenerateThumbnails {
	/**
	 * This plugin's version number. Used for busting caches.
	 *
	 * @var string
	 */
	public $version = '3.0.1';

	/**
	 * The menu ID of this plugin, as returned by add_management_page().
	 *
	 * @var string
	 */
	public $menu_id;

	/**
	 * The capability required to use this plugin.
	 * Please don't change this directly. Use the "regenerate_thumbs_cap" filter instead.
	 *
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * The instance of the REST API controller class used to extend the REST API.
	 *
	 * @var RegenerateThumbnails_REST_Controller
	 */
	public $rest_api;

	/**
	 * The single instance of this plugin.
	 *
	 * @see    RegenerateThumbnails()
	 *
	 * @access private
	 * @var    RegenerateThumbnails
	 */
	private static $instance;

	/**
	 * Constructor. Doesn't actually do anything as instance() creates the class instance.
	 */
	private function __construct() {}

	/**
	 * Prevents the class from being cloned.
	 */
	public function __clone() {
		wp_die( "Please don't clone RegenerateThumbnails" );
	}

	/**
	 * Prints the class from being unserialized and woken up.
	 */
	public function __wakeup() {
		wp_die( "Please don't unserialize/wakeup RegenerateThumbnails" );
	}

	/**
	 * Creates a new instance of this class if one hasn't already been made
	 * and then returns the single instance of this class.
	 *
	 * @return RegenerateThumbnails
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new RegenerateThumbnails;
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Register all of the needed hooks and actions.
	 */
	public function setup() {
		// Allow people to change what capability is required to use this plugin.
		$this->capability = apply_filters( 'regenerate_thumbs_cap', $this->capability );

		// Initialize the REST API routes.
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		// Add a new item to the Tools menu in the admin menu.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Load the required JavaScript and CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueues' ) );

		// For the bulk action dropdowns.
		add_action( 'admin_head-upload.php', array( $this, 'add_bulk_actions_via_javascript' ) );
		add_action( 'admin_action_bulk_regenerate_thumbnails', array( $this, 'bulk_action_handler' ) ); // Top drowndown.
		add_action( 'admin_action_-1', array( $this, 'bulk_action_handler' ) ); // Bottom dropdown.

		// Add a regenerate button to the non-modal edit media page.
		add_action( 'attachment_submitbox_misc_actions', array( $this, 'add_button_to_media_edit_page' ), 99 );

		// Add a regenerate button to the list of fields in the edit media modal.
		// Ideally this would with the action links but I'm not good enough with JavaScript to do it.
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_button_to_edit_media_modal_fields_area' ), 99, 2 );

		// Add a regenerate link to actions list in the media list view.
		add_filter( 'media_row_actions', array( $this, 'add_regenerate_link_to_media_list_view' ), 10, 2 );
	}

	/**
	 * Initialize the REST API routes.
	 */
	public function rest_api_init() {
		$this->rest_api = new RegenerateThumbnails_REST_Controller();
		$this->rest_api->register_routes();
		$this->rest_api->register_filters();
	}

	/**
	 * Adds a the new item to the admin menu.
	 */
	public function add_admin_menu() {
		$this->menu_id = add_management_page( __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ), __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ), $this->capability, 'regenerate-thumbnails', array( $this, 'regenerate_interface' ) );

		add_action( 'admin_head-' . $this->menu_id, array( $this, 'add_admin_notice_if_resizing_not_supported' ) );
	}

	/**
	 * Enqueues the requires JavaScript file and stylesheet on the plugin's admin page.
	 *
	 * @param string $hook_suffix The current page's hook suffix as provided by admin-header.php.
	 */
	public function admin_enqueues( $hook_suffix ) {
		if ( $hook_suffix != $this->menu_id ) {
			return;
		}

		// Pre-4.9 compatibility.
		if ( ! wp_script_is( 'wp-api-request', 'registered' ) ) {
			wp_register_script(
				'wp-api-request',
				plugins_url( 'js/api-request.min.js', __FILE__ ),
				array( 'jquery' ),
				'4.9',
				true
			);

			wp_localize_script( 'wp-api-request', 'wpApiSettings', array(
				'root'          => esc_url_raw( get_rest_url() ),
				'nonce'         => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
				'versionString' => 'wp/v2/',
			) );
		}

		wp_enqueue_script(
			'regenerate-thumbnails',
			plugins_url( 'dist/build.js', __FILE__ ),
			array( 'wp-api-request' ),
			( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? filemtime( dirname( __FILE__ ) . '/dist/build.js' ) : $this->version,
			true
		);

		$script_data = array(
			'data'    => array(
				'thumbnailSizes' => $this->get_thumbnail_sizes(),
				'genericEditURL' => admin_url( 'post.php?action=edit&post=' ),
			),
			'options' => array(
				'onlyMissingThumbnails' => apply_filters( 'regenerate_thumbnails_options_onlymissingthumbnails', true ),
				'updatePostContents'    => apply_filters( 'regenerate_thumbnails_options_updatepostcontents', false ),
				'deleteOldThumbnails'   => apply_filters( 'regenerate_thumbnails_options_deleteoldthumbnails', false ),
			),
			'l10n'    => array(
				'common'             => array(
					'regenerateThumbnails'                      => __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ),
					'loading'                                   => __( 'Loading…', 'regenerate-thumbnails' ),
					'onlyRegenerateMissingThumbnails'           => __( 'Skip regenerating existing correctly sized thumbnails (faster).', 'regenerate-thumbnails' ),
					'deleteOldThumbnails'                       => __( "Delete thumbnail files for old unregistered sizes in order to free up server space. You risk broken images if you do this so it's strongly recommended that you update the content of posts to reduce the risk.", 'regenerate-thumbnails' ),
					'thumbnailSizeItemWithCropMethodNoFilename' => __( '<strong>{label}:</strong> {width}×{height} pixels ({cropMethod})', 'regenerate-thumbnails' ),
					'thumbnailSizeItemWithCropMethod'           => __( '<strong>{label}:</strong> {width}×{height} pixels ({cropMethod}) <code>{filename}</code>', 'regenerate-thumbnails' ),
					'thumbnailSizeItemWithoutCropMethod'        => __( '<strong>{label}:</strong> {width}×{height} pixels <code>{filename}</code>', 'regenerate-thumbnails' ),
					'thumbnailSizeBiggerThanOriginal'           => __( '<strong>{label}:</strong> {width}×{height} pixels (thumbnail would be larger than original)', 'regenerate-thumbnails' ),
					'thumbnailSizeItemIsCropped'                => __( 'cropped to fit', 'regenerate-thumbnails' ),
					'thumbnailSizeItemIsProportional'           => __( 'proportionally resized to fit inside dimensions', 'regenerate-thumbnails' ),
				),
				'Home'               => array(
					'intro1'                                     => sprintf(
						__( 'When you change WordPress themes or change the sizes of your thumbnails at <a href="%s">Settings → Media</a>, images that you have previously uploaded to you media library will be missing thumbnail files for those new image sizes. This tool will allow you to create those missing thumbnail files for all images.', 'regenerate-thumbnails' ),
						esc_url( admin_url( 'options-media.php' ) )
					),
					'intro2'                                     => sprintf(
						__( 'To process a specific image, visit your media library and click the &quot;Regenerate Thumbnails&quot; link or button. To process multiple specific images, make sure you\'re in the <a href="%s">list view</a> and then use the Bulk Actions dropdown after selecting one or more images.', 'regenerate-thumbnails' ),
						esc_url( admin_url( 'upload.php?mode=list' ) )
					),
					'updatePostContents'                         => __( 'Update the content of posts to use the new sizes.', 'regenerate-thumbnails' ),
					'RegenerateThumbnailsForAllAttachments'      => __( 'Regenerate Thumbnails For All Attachments', 'regenerate-thumbnails' ),
					'RegenerateThumbnailsForAllXAttachments'     => __( 'Regenerate Thumbnails For All {attachmentCount} Attachments', 'regenerate-thumbnails' ),
					'RegenerateThumbnailsForFeaturedImagesOnly'  => __( 'Regenerate Thumbnails For Featured Images Only', 'regenerate-thumbnails' ),
					'RegenerateThumbnailsForXFeaturedImagesOnly' => __( 'Regenerate Thumbnails For The {attachmentCount} Featured Images Only', 'regenerate-thumbnails' ),
					'thumbnailSizes'                             => __( 'Thumbnail Sizes', 'regenerate-thumbnails' ),
					'thumbnailSizesDescription'                  => __( 'These are all of the thumbnail sizes that are currently registered:', 'regenerate-thumbnails' ),
					'alternatives'                               => __( 'Alternatives', 'regenerate-thumbnails' ),
					'alternativesText1'                          => __( 'If you have <a href="{url-cli}">command-line</a> access to your site\'s server, consider using <a href="{url-wpcli}">WP-CLI</a> instead of this tool. It has a built-in <a href="{url-wpcli-regenerate}">regenerate command</a> that works similarly to this tool but should be significantly faster since it has the advantage of being a command-line tool.', 'regenerate-thumbnails' ),
					'alternativesText2'                          => __( 'Another alternative is to use the <a href="{url-photon}">Photon</a> functionality that comes with the <a href="{url-jetpack}">Jetpack</a> plugin. It generates thumbnails on-demand using WordPress.com\'s infrastructure. <em>Disclaimer: The author of this plugin, Regenerate Thumbnails, is an employee of the company behind WordPress.com and Jetpack but I would recommend it even if I wasn\'t.', 'regenerate-thumbnails' ),
				),
				'RegenerateSingle'   => array(
					/* translators: Admin screen title. */
					'title'                    => __( 'Regenerate Thumbnails: {name} — WordPress', 'regenerate-thumbnails' ),
					'errorWithMessage'         => __( '<strong>ERROR:</strong> {error}', 'regenerate-thumbnails' ),
					'filenameAndDimensions'    => __( '<code>{filename}</code> {width}×{height} pixels', 'regenerate-thumbnails' ),
					'preview'                  => __( 'Preview', 'regenerate-thumbnails' ),
					'updatePostContents'       => __( 'Update the content of posts that use this attachment to use the new sizes.', 'regenerate-thumbnails' ),
					'regenerating'             => __( 'Regenerating…', 'regenerate-thumbnails' ),
					'done'                     => __( 'Done! Click here to go back.', 'regenerate-thumbnails' ),
					'errorRegenerating'        => __( 'Error Regenerating', 'regenerate-thumbnails' ),
					'errorRegeneratingMessage' => __( 'There was an error regenerating this attachment. The error was: <em>{message}</em>', 'regenerate-thumbnails' ),
					'registeredSizes'          => __( 'These are the currently registered thumbnail sizes, whether they exist for this attachment, and their filenames:', 'regenerate-thumbnails' ),
					'unregisteredSizes'        => __( 'The attachment says it also has these thumbnail sizes but they are no longer in use by WordPress. You can probably safely have this plugin delete them, especially if you have this plugin update any posts that make use of this attachment.', 'regenerate-thumbnails' ),
				),
				'RegenerateMultiple' => array(
					'errorsEncountered'    => __( 'Errors Encountered', 'regenerate-thumbnails' ),
					'regenerationLog'      => __( 'Regeneration Log', 'regenerate-thumbnails' ),
					'pause'                => __( 'Pause', 'regenerate-thumbnails' ),
					'resume'               => __( 'Resume', 'regenerate-thumbnails' ),
					'logRegeneratedItem'   => __( 'Regenerated {name}', 'regenerate-thumbnails' ),
					'logSkippedItem'       => __( 'Skipped Attachment ID {id} ({name}): {reason}', 'regenerate-thumbnails' ),
					'logSkippedItemNoName' => __( 'Skipped Attachment ID {id}: {reason}', 'regenerate-thumbnails' ),
					'duration'             => __( 'All done in {duration}.', 'regenerate-thumbnails' ),
					'hours'                => __( '{count} hours', 'regenerate-thumbnails' ),
					'minutes'              => __( '{count} minutes', 'regenerate-thumbnails' ),
					'seconds'              => __( '{count} seconds', 'regenerate-thumbnails' ),
					'error'                => __( "Unable to fetch a list of attachment IDs to process from the WordPress REST API. You can check your browser's console for details.", 'regenerate-thumbnails' ),
				),
			),
		);

		// Bulk regeneration.
		if ( ! empty( $_GET['ids'] ) ) {
			$script_data['data']['thumbnailIDs'] = array_map( 'intval', explode( ',', $_GET['ids'] ) );

			$script_data['l10n']['Home']['RegenerateThumbnailsForXAttachments'] = sprintf(
				__( 'Regenerate Thumbnails For The %d Selected Attachments', 'regenerate-thumbnails' ),
				count( $script_data['data']['thumbnailIDs'] )
			);
		}

		wp_localize_script( 'regenerate-thumbnails', 'regenerateThumbnails', $script_data );

		wp_enqueue_style(
			'regenerate-thumbnails-progressbar',
			plugins_url( 'css/progressbar.css', __FILE__ ),
			array(),
			( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? filemtime( dirname( __FILE__ ) . '/css/progressbar.css' ) : $this->version
		);
	}

	/**
	 * The main Regenerate Thumbnails interface, as displayed at Tools → Regenerate Thumbnails.
	 */
	public function regenerate_interface() {
		global $wp_version;

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Regenerate Thumbnails', 'regenerate-thumbnails' ) . '</h1>';

		if ( version_compare( $wp_version, '4.7', '<' ) ) {
			echo '<p>' . sprintf(
					__( 'This plugin requires WordPress 4.7 or newer. You are on version %1$s. Please <a href="%2$s">upgrade</a>.', 'regenerate-thumbnails' ),
					esc_html( $wp_version ),
					esc_url( admin_url( 'update-core.php' ) )
				) . '</p>';
		} else {

			?>

			<div id="regenerate-thumbnails-app">
				<div class="notice notice-error hide-if-js">
					<p><strong><?php esc_html_e( 'This tool requires that JavaScript be enabled to work.', 'regenerate-thumbnails' ); ?></strong></p>
				</div>

				<router-view><p class="hide-if-no-js"><?php esc_html_e( 'Loading…', 'regenerate-thumbnails' ); ?></p></router-view>
			</div>

			<?php

		} // version_compare()

		echo '</div>';
	}

	/**
	 * If the image editor doesn't support image resizing (thumbnailing), then add an admin notice
	 * warning the user of this.
	 */
	public function add_admin_notice_if_resizing_not_supported() {
		if ( ! wp_image_editor_supports( array( 'methods' => array( 'resize' ) ) ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices_resizing_not_supported' ) );
		}
	}

	/**
	 * Outputs an admin notice stating that image resizing (thumbnailing) is not supported.
	 */
	public function admin_notices_resizing_not_supported() {
		?>
		<div class="notice notice-error">
			<p><strong><?php esc_html_e( "This tool won't be able to do anything because your server doesn't support image editing which means that WordPress can't create thumbnail images. Please ask your host to install the Imagick or GD PHP extensions.", 'regenerate-thumbnails' ); ?></strong></p>
		</div>
		<?php
	}

	/**
	 * Helper function to create a URL to regenerate a single image.
	 *
	 * @param int $id The attachment ID that should be regenerated.
	 *
	 * @return string The URL to the admin page.
	 */
	public function create_page_url( $id ) {
		return add_query_arg( 'page', 'regenerate-thumbnails', admin_url( 'tools.php' ) ) . '#/regenerate/' . $id;
	}

	/**
	 * Determines whether an attachment can have its thumbnails regenerated.
	 *
	 * This includes checking to see if non-images, such as PDFs, are supported
	 * by the current image editor.
	 *
	 * @param WP_Post $post An attachment's post object.
	 *
	 * @return bool Whether the given attachment can have its thumbnails regenerated.
	 */
	public function is_regeneratable( $post ) {
		if ( 'site-icon' === get_post_meta( $post->ID, '_wp_attachment_context', true ) ) {
			return false;
		}

		if ( wp_attachment_is_image( $post ) ) {
			return true;
		}

		$fullsize = get_attached_file( $post->ID );

		if ( ! $fullsize || ! file_exists( $fullsize ) ) {
			return false;
		}

		$editor = wp_get_image_editor( $fullsize );

		if ( is_wp_error( $editor ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds "Regenerate Thumbnails" below each image in the media library list view.
	 *
	 * @param array   $actions An array of current actions.
	 * @param WP_Post $post    The current attachment's post object.
	 *
	 * @return array The new list of actions.
	 */
	public function add_regenerate_link_to_media_list_view( $actions, $post ) {
		if ( ! current_user_can( $this->capability ) || ! $this->is_regeneratable( $post ) ) {
			return $actions;
		}

		$actions['regenerate_thumbnails'] = '<a href="' . esc_url( $this->create_page_url( $post->ID ) ) . '" title="' . esc_attr( __( 'Regenerate the thumbnails for this single image', 'regenerate-thumbnails' ) ) . '">' . __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ) . '</a>';

		return $actions;
	}

	/**
	 * Add a "Regenerate Thumbnails" button to the submit box on the non-modal "Edit Media" screen for an image attachment.
	 */
	public function add_button_to_media_edit_page() {
		global $post;

		if ( ! current_user_can( $this->capability ) || ! $this->is_regeneratable( $post ) ) {
			return;
		}

		echo '<div class="misc-pub-section misc-pub-regenerate-thumbnails">';
		echo '<a href="' . esc_url( $this->create_page_url( $post->ID ) ) . '" class="button-secondary button-large" title="' . esc_attr( __( 'Regenerate the thumbnails for this single image', 'regenerate-thumbnails' ) ) . '">' . __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ) . '</a>';
		echo '</div>';
	}

	/**
	 * Adds a "Regenerate Thumbnails" button to the edit media modal view.
	 *
	 * Ideally it would be down with the actions but I'm not good enough at JavaScript
	 * in order to be able to do it, so instead I'm adding it to the bottom of the list
	 * of media fields. Pull requests to improve this are welcome!
	 *
	 * @param array   $form_fields An array of existing form fields.
	 * @param WP_Post $post        The current media item, as a post object.
	 *
	 * @return array The new array of form fields.
	 */
	public function add_button_to_edit_media_modal_fields_area( $form_fields, $post ) {
		if ( ! current_user_can( $this->capability ) || ! $this->is_regeneratable( $post ) ) {
			return $form_fields;
		}

		$form_fields['regenerate_thumbnails'] = array(
			'label'         => '',
			'input'         => 'html',
			'html'          => '<a href="' . esc_url( $this->create_page_url( $post->ID ) ) . '" class="button-secondary button-large" title="' . esc_attr( __( 'Regenerate the thumbnails for this single image', 'regenerate-thumbnails' ) ) . '">' . __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ) . '</a>',
			'show_in_modal' => true,
			'show_in_edit'  => false,
		);

		return $form_fields;
	}

	/**
	 * Add "Regenerate Thumbnails" to the bulk actions dropdown on the media list using Javascript.
	 */
	public function add_bulk_actions_via_javascript() {
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('select[name^="action"] option:last-child').before(
					$('<option/>')
						.attr('value', 'bulk_regenerate_thumbnails')
						.text('<?php echo esc_js( __( 'Regenerate Thumbnails', 'regenerate-thumbnails' ) ); ?>')
				);
			});
		</script>
		<?php
	}

	/**
	 * Handles the submission of the new bulk actions entry and redirects to the admin page with the selected attachment IDs.
	 */
	public function bulk_action_handler() {
		if (
			empty( $_REQUEST['action'] ) ||
			empty( $_REQUEST['action2'] ) ||
			( 'bulk_regenerate_thumbnails' != $_REQUEST['action'] && 'bulk_regenerate_thumbnails' != $_REQUEST['action2'] ) ||
			empty( $_REQUEST['media'] ) ||
			! is_array( $_REQUEST['media'] )
		) {
			return;
		}

		check_admin_referer( 'bulk-media' );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => 'regenerate-thumbnails',
					'ids'  => rawurlencode( implode( ',', array_map( 'intval', $_REQUEST['media'] ) ) ),
				),
				admin_url( 'tools.php' )
			)
		);

		exit();
	}

	/**
	 * Returns an array of all thumbnail sizes, including their label, size, and crop setting.
	 *
	 * @return array An array, with the thumbnail label as the key and an array of thumbnail properties (width, height, crop).
	 */
	public function get_thumbnail_sizes() {
		global $_wp_additional_image_sizes;

		$thumbnail_sizes = array();

		foreach ( get_intermediate_image_sizes() as $size ) {
			$thumbnail_sizes[ $size ]['label'] = $size;
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$thumbnail_sizes[ $size ]['width']  = (int) get_option( $size . '_size_w' );
				$thumbnail_sizes[ $size ]['height'] = (int) get_option( $size . '_size_h' );
				$thumbnail_sizes[ $size ]['crop']   = ( 'thumbnail' == $size ) ? (bool) get_option( 'thumbnail_crop' ) : false;
			} elseif ( ! empty( $_wp_additional_image_sizes ) && ! empty( $_wp_additional_image_sizes[ $size ] ) ) {
				$thumbnail_sizes[ $size ]['width']  = (int) $_wp_additional_image_sizes[ $size ]['width'];
				$thumbnail_sizes[ $size ]['height'] = (int) $_wp_additional_image_sizes[ $size ]['height'];
				$thumbnail_sizes[ $size ]['crop']   = (bool) $_wp_additional_image_sizes[ $size ]['crop'];
			}
		}

		return $thumbnail_sizes;
	}
}

/**
 * Returns the single instance of this plugin, creating one if needed.
 *
 * @return RegenerateThumbnails
 */
function RegenerateThumbnails() {
	return RegenerateThumbnails::instance();
}

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action( 'init', 'RegenerateThumbnails' );
