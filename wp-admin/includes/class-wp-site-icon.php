<?php

/**
 * Class WP_Site_Icon.
 *
 * @since 4.3.0
 */
class WP_Site_Icon {

	/**
	 * The minimum size of the site icon.
	 *
	 * @since 4.3.0
	 *
	 * @var int
	 */
	public $min_size  = 512;

	/**
	 * The size to which to crop the image so that we can display it in the UI nicely.
	 *
	 * @since 4.3.0
	 *
	 * @var int
	 */
	public $page_crop = 512;

	/**
	 *
	 * @since 4.3.0
	 *
	 * @var array
	 */
	public $site_icon_sizes = array(
		/**
		 * Square, medium sized tiles for IE11+.
		 *
		 * @link https://msdn.microsoft.com/library/dn455106(v=vs.85).aspx
		 */
		270,

		/**
		 * App icons up to iPhone 6 Plus.
		 *
		 * @link https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Conceptual/MobileHIG/IconMatrix.html
		 */
		180,

		// Our regular Favicon.
		32,
	);

	/**
	 * Register our actions and filters.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {

		// Add the favicon to the backend.
		add_action( 'admin_menu', array( $this, 'admin_menu_upload_site_icon' ) );

		add_action( 'admin_action_set_site_icon',    array( $this, 'set_site_icon'    ) );
		add_action( 'admin_action_remove_site_icon', array( $this, 'remove_site_icon' ) );

		add_action( 'delete_attachment', array( $this, 'delete_attachment_data' ), 10, 1 );
		add_filter( 'get_post_metadata', array( $this, 'get_post_metadata' ), 10, 4 );
	}

	/**
	 * Add a hidden upload page.
	 *
	 * There is no need to access it directly.
	 *
	 * @since 4.3.0
	 */
	public function admin_menu_upload_site_icon() {
		$hook = add_submenu_page( null, __( 'Site Icon' ), __( 'Site Icon' ), 'manage_options', 'site-icon', array( $this, 'upload_site_icon_page' ) );

		add_action( "load-$hook", array( $this, 'add_upload_settings' ) );
		add_action( "admin_print_scripts-$hook", array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add scripts to admin settings pages.
	 *
	 * @since 4.3.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'jcrop' );
		wp_enqueue_script( 'site-icon-crop' );
	}

	/**
	 * Load on when the admin is initialized.
	 *
	 * @since 4.3.0
	 */
	public function add_upload_settings() {
		add_settings_section( 'site-icon-upload', false, false, 'site-icon-upload' );
		add_settings_field( 'site-icon-upload', __( 'Upload Image' ), array( $this, 'upload_field' ), 'site-icon-upload', 'site-icon-upload', array(
			'label_for' => 'site-icon-upload',
		) );
	}

	/**
	 * Removes site icon.
	 *
	 * @since 4.3.0
	 */
	public function remove_site_icon() {
		check_admin_referer( 'remove-site-icon' );

		$this->delete_site_icon();

		add_settings_error( 'site-icon', 'icon-removed', __( 'Site Icon removed.' ), 'updated' );
	}

	/**
	 * Uploading a site_icon is a 3 step process
	 *
	 * 1. Select the file to upload
	 * 2. Crop the file
	 * 3. Confirmation
	 *
	 * @since 4.3.0
	 */
	public function upload_site_icon_page() {
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'select_site_icon';

		switch ( $action ) {
			case 'select_site_icon':
				$this->select_page();
				break;

			case 'crop_site_icon':
				$this->crop_page();
				break;

			default:
				wp_safe_redirect( admin_url( 'options-general.php#site-icon' ) );
				exit;
		}
	}

	/**
	 * Displays the site_icon form to upload the image.
	 *
	 * @since 4.3.0
	 */
	public function select_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Add Site Icon' ); ?></h2>
			<?php settings_errors( 'site-icon' ); ?>
			<?php do_settings_sections( 'site-icon-upload' ); ?>
		</div>
	<?php
	}

	/**
	 * Settings field for file upload.
	 *
	 * @since 4.3.0
	 */
	public function upload_field() {
		wp_enqueue_media();
		wp_enqueue_script( 'site-icon' );
		wp_dequeue_script( 'site-icon-crop' );

		$update_url = esc_url( add_query_arg( array(
			'page' => 'site-icon',
			'action' => 'crop_site_icon',
		), wp_nonce_url( admin_url( 'options-general.php' ), 'crop-site-icon' ) ) );
		?>
		<p class="hide-if-no-js">
			<label class="screen-reader-text" for="choose-from-library-link"><?php _e( 'Choose an image from your media library:' ); ?></label>
			<button type="button" id="choose-from-library-link" class="button" data-update-link="<?php echo esc_attr( $update_url ); ?>" data-choose="<?php esc_attr_e( 'Choose a Site Icon' ); ?>" data-update="<?php esc_attr_e( 'Set as Site Icon' ); ?>"><?php _e( 'Choose Image' ); ?></button>
		</p>
		<form class="hide-if-js" action="<?php echo esc_url( admin_url( 'options-general.php?page=site-icon' ) ); ?>" method="post" enctype="multipart/form-data">
			<input name="action" value="crop_site_icon" type="hidden" />
			<input name="site-icon" type="file" />
			<input name="submit" value="<?php esc_attr_e( 'Upload Image' ); ?>" type="submit" class="button button-primary" />
			<p class="description">
				<?php printf( __( 'The image is recommended to be a square image of at least %spx in both width and height.' ), "<strong>$this->min_size</strong>" ); ?>
			</p>
			<?php wp_nonce_field( 'crop-site-icon' ); ?>
		</form>
	<?php
	}

	/**
	 * Crop a the image admin view.
	 *
	 * @since 4.3.0
	 */
	public function crop_page() {
		check_admin_referer( 'crop-site-icon' );

		if ( isset( $_GET['file'] ) ) {
			$attachment_id = absint( $_GET['file'] );
			$file          = get_attached_file( $attachment_id, true );
			$url           = wp_get_attachment_image_src( $attachment_id, 'full' );
			$url           = $url[0];
		} else {
			$upload        = $this->handle_upload();
			$attachment_id = $upload['attachment_id'];
			$file          = $upload['file'];
			$url           = $upload['url'];
		}

		$image_size = getimagesize( $file );

		if ( $image_size[0] < $this->min_size ) {
			add_settings_error( 'site-icon', 'too-small', sprintf( __( 'The selected image is smaller than %upx in width.' ), $this->min_size ) );

			// back to step one
			$this->select_page();

			return;
		}

		if ( $image_size[1] < $this->min_size ) {
			add_settings_error( 'site-icon', 'too-small', sprintf( __( 'The selected image is smaller than %upx in height.' ), $this->min_size ) );

			// Back to step one.
			$this->select_page();

			return;
		}

		// Let's resize the image so that the user can easier crop a image that in the admin view.
		$crop_height = absint( $this->page_crop * $image_size[1] / $image_size[0] );
		$cropped = wp_crop_image( $attachment_id, 0, 0, 0, 0, $this->page_crop, $crop_height );
		if ( ! $cropped || is_wp_error( $cropped ) ) {
			wp_die( __( 'Image could not be processed. Please go back and try again.' ), __( 'Image Processing Error' ) );
		}
		$cropped_size = getimagesize( $cropped );

		// set default values (in case of no JS)
		$crop_ratio = $image_size[0] / $cropped_size[0];
		if ( $cropped_size[0] < $cropped_size[1] ) {
			$crop_x    = 0;
			$crop_y    = absint( ( $cropped_size[1] - $cropped_size[0] ) / 2 );
			$crop_size = $cropped_size[0];
		} elseif ( $cropped_size[0] > $cropped_size[1] ) {
			$crop_x    = absint( ( $cropped_size[0] - $cropped_size[1] ) / 2 );
			$crop_y    = 0;
			$crop_size = $cropped_size[1];
		} else {
			$crop_x    = 0;
			$crop_y    = 0;
			$crop_size = $cropped_size[0];
		}

		wp_delete_file( $cropped );

		wp_localize_script( 'site-icon-crop', 'wpSiteIconCropData', $this->initial_crop_data( $crop_ratio, $cropped_size ) );
		?>

		<div class="wrap">
			<h2 class="site-icon-title"><?php _e( 'Site Icon' ); ?></h2>
			<?php settings_errors( 'site-icon' ); ?>

			<div class="site-icon-crop-shell">
				<form action="options-general.php" method="post" enctype="multipart/form-data">
					<p class="hide-if-no-js description"><?php _e('Choose the part of the image you want to use as your site icon.'); ?></p>
					<p class="hide-if-js description"><strong><?php _e( 'You need Javascript to choose a part of the image.'); ?></strong></p>

					<div class="site-icon-crop-preview-shell hide-if-no-js">
						<h3><?php _e( 'Preview' ); ?></h3>
						<strong><?php _e( 'As your favicon' ); ?></strong>
						<div class="site-icon-crop-favicon-preview-shell">
							<img src="images/browser.png" class="site-icon-browser-preview" width="182" height="" alt="<?php esc_attr_e( 'Browser Chrome' ); ?>"/>

							<div class="site-icon-crop-preview-favicon">
								<img src="<?php echo esc_url( $url ); ?>" id="preview-favicon" alt="<?php esc_attr_e( 'Preview Favicon' ); ?>"/>
							</div>
							<span class="site-icon-browser-title"><?php bloginfo( 'name' ); ?></span>
						</div>

						<strong><?php _e( 'As a mobile icon' ); ?></strong>
						<div class="site-icon-crop-preview-homeicon">
							<img src="<?php echo esc_url( $url ); ?>" id="preview-homeicon" alt="<?php esc_attr_e( 'Preview Home Icon' ); ?>"/>
						</div>
					</div>
					<img src="<?php echo esc_url( $url ); ?>" id="crop-image" class="site-icon-crop-image" width="<?php echo esc_attr( $cropped_size[0] ); ?>" height="<?php echo esc_attr( $cropped_size[1] ); ?>" alt="<?php esc_attr_e( 'Image to be cropped' ); ?>"/>

					<input type="hidden" id="crop-x" name="crop-x" value="<?php echo esc_attr( $crop_x ); ?>" />
					<input type="hidden" id="crop-y" name="crop-y" value="<?php echo esc_attr( $crop_y ); ?>" />
					<input type="hidden" id="crop-width" name="crop-w" value="<?php echo esc_attr( $crop_size ); ?>" />
					<input type="hidden" id="crop-height" name="crop-h" value="<?php echo esc_attr( $crop_size ); ?>" />

					<input type="hidden" name="action" value="set_site_icon" />
					<input type="hidden" name="attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" />
					<input type="hidden" name="crop_ratio" value="<?php echo esc_attr( $crop_ratio ); ?>" />
					<?php if ( empty( $_POST ) && isset( $_GET['file'] ) ) : ?>
						<input type="hidden" name="create-new-attachment" value="true" />
					<?php endif; ?>
					<?php wp_nonce_field( 'set-site-icon' ); ?>

					<p class="submit">
						<?php submit_button( __( 'Crop and Publish' ), 'primary hide-if-no-js', 'submit', false ); ?>
						<?php submit_button( __( 'Publish' ), 'primary hide-if-js', 'submit', false ); ?>
						<a class="button secondary" href="options-general.php"><?php _e( 'Cancel' ); ?></a>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Saves a new Site Icon.
	 *
	 * @since 4.3.0
	 */
	public function set_site_icon() {
		check_admin_referer( 'set-site-icon' );

		// Delete any existing site icon images.
		$this->delete_site_icon();

		$attachment_id = absint( $_POST['attachment_id'] );

		// TODO
		if ( empty( $_POST['skip-cropping'] ) ) {
			$crop_ratio = (float) $_POST['crop_ratio'];
			$crop_data = $this->convert_coordinates_from_resized_to_full( $_POST['crop-x'], $_POST['crop-y'], $_POST['crop-w'], $_POST['crop-h'], $crop_ratio );
			$cropped = wp_crop_image( $attachment_id, $crop_data['crop_x'], $crop_data['crop_y'], $crop_data['crop_width'], $crop_data['crop_height'], $this->min_size, $this->min_size );
		} elseif ( ! empty( $_POST['create-new-attachment'] ) ) {
			$cropped = _copy_image_file( $attachment_id );
		} else {
			$cropped = get_attached_file( $attachment_id );
		}

		if ( ! $cropped || is_wp_error( $cropped ) ) {
			wp_die( __( 'Image could not be processed. Please go back and try again.' ), __( 'Image Processing Error' ) );
		}

		$object = $this->create_attachment_object( $cropped, $attachment_id );

		if ( ! empty( $_POST['create-new-attachment'] ) ) {
			unset( $object['ID'] );
		}

		// Update the attachment
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'additional_sizes' ) );
		$attachment_id = $this->insert_attachment( $object, $cropped );
		remove_filter( 'intermediate_image_sizes_advanced', array( $this, 'additional_sizes' ) );

		// Save the site_icon data into option
		update_option( 'site_icon', $attachment_id );

		add_settings_error( 'site-icon', 'icon-updated', __( 'Site Icon updated.' ), 'updated' );
	}

	/**
	 * This function is used to pass data to the localize script
	 * so that we can center the cropper and also set the minimum
	 * cropper.
	 *
	 * @since 4.3.0
	 *
	 * @param float $ratio
	 * @param array $cropped_size
	 * @return array
	 */
	public function initial_crop_data( $ratio, $cropped_size ) {
		$init_x = $init_y = $init_size = 0;

		$min_crop_size  = ( $this->min_size / $ratio );
		$resized_width  = $cropped_size[0];
		$resized_height = $cropped_size[1];

		// Landscape format ( width > height )
		if ( $resized_width > $resized_height ) {
			$init_x    = ( $this->page_crop - $resized_height ) / 2;
			$init_size = $resized_height;
		}

		// Portrait format ( height > width )
		if ( $resized_width < $resized_height ) {
			$init_y    = ( $this->page_crop - $resized_width ) / 2;
			$init_size = $resized_height;
		}

		// Square height == width
		if ( $resized_width == $resized_height ) {
			$init_size = $resized_height;
		}

		return array(
			'init_x'    => $init_x,
			'init_y'    => $init_y,
			'init_size' => $init_size,
			'min_size'  => $min_crop_size,
		);
	}

	/**
	 * Converts the coordinates from the downsized image to the original image for accurate cropping.
	 *
	 * @since 4.3.0
	 *
	 * @param int   $crop_x
	 * @param int   $crop_y
	 * @param int   $crop_width
	 * @param int   $crop_height
	 * @param float $ratio
	 * @return array
	 */
	public function convert_coordinates_from_resized_to_full( $crop_x, $crop_y, $crop_width, $crop_height, $ratio ) {
		return array(
			'crop_x'      => floor( $crop_x * $ratio ),
			'crop_y'      => floor( $crop_y * $ratio ),
			'crop_width'  => floor( $crop_width * $ratio ),
			'crop_height' => floor( $crop_height * $ratio ),
		);
	}

	/**
	 * Upload the file to be cropped in the second step.
	 *
	 * @since 4.3.0
	 */
	public function handle_upload() {
		$uploaded_file = $_FILES['site-icon'];
		$file_type     = wp_check_filetype_and_ext( $uploaded_file['tmp_name'], $uploaded_file['name'] );
		if ( ! wp_match_mime_types( 'image', $file_type['type'] ) ) {
			wp_die( __( 'The uploaded file is not a valid image. Please try again.' ) );
		}

		$file = wp_handle_upload( $uploaded_file, array( 'test_form' => false ) );

		if ( isset( $file['error'] ) ) {
			wp_die( $file['error'], __( 'Image Upload Error' ) );
		}

		$url      = $file['url'];
		$type     = $file['type'];
		$file     = $file['file'];
		$filename = basename( $file );

		// Construct the object array
		$object = array(
			'post_title'     => $filename,
			'post_content'   => $url,
			'post_mime_type' => $type,
			'guid'           => $url,
			'context'        => 'site-icon',
		);

		// Save the data
		$attachment_id = wp_insert_attachment( $object, $file );

		return compact( 'attachment_id', 'file', 'filename', 'url', 'type' );
	}

	/**
	 * Create an attachment 'object'.
	 *
	 * @since 4.3.0
	 *
	 * @param string $cropped              Cropped image URL.
	 * @param int    $parent_attachment_id Attachment ID of parent image.
	 * @return array Attachment object.
	 */
	public function create_attachment_object( $cropped, $parent_attachment_id ) {
		$parent     = get_post( $parent_attachment_id );
		$parent_url = $parent->guid;
		$url        = str_replace( basename( $parent_url ), basename( $cropped ), $parent_url );

		$size       = @getimagesize( $cropped );
		$image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

		$object = array(
			'ID'             => $parent_attachment_id,
			'post_title'     => basename( $cropped ),
			'post_content'   => $url,
			'post_mime_type' => $image_type,
			'guid'           => $url,
			'context'        => 'site-icon'
		);

		return $object;
	}

	/**
	 * Insert an attachment and its metadata.
	 *
	 * @since 4.3.0
	 *
	 * @param array  $object  Attachment object.
	 * @param string $cropped Cropped image URL.
	 * @return int Attachment ID.
	 */
	public function insert_attachment( $object, $cropped ) {
		$attachment_id = wp_insert_attachment( $object, $cropped );
		$metadata      = wp_generate_attachment_metadata( $attachment_id, $cropped );

		/**
		 * Filter the site icon attachment metadata.
		 *
		 * @since 4.3.0
		 *
		 * @see wp_generate_attachment_metadata()
		 *
		 * @param array $metadata Attachment metadata.
		 */
		$metadata = apply_filters( 'site_icon_attachment_metadata', $metadata );
		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $attachment_id;
	}

	/**
	 * Add additional sizes to be made when creating the site_icon images.
	 *
	 * @since 4.3.0
	 *
	 * @param array $sizes
	 * @return array
	 */
	public function additional_sizes( $sizes = array() ) {
		$only_crop_sizes = array();

		/**
		 * Filter the different dimensions that a site icon is saved in.
		 *
		 * @since 4.3.0
		 *
		 * @param array $site_icon_sizes Sizes available for the Site Icon.
		 */
		$this->site_icon_sizes = apply_filters( 'site_icon_image_sizes', $this->site_icon_sizes );

		// Use a natural sort of numbers.
		natsort( $this->site_icon_sizes );
		$this->site_icon_sizes = array_reverse( $this->site_icon_sizes );

		// ensure that we only resize the image into
		foreach ( $sizes as $name => $size_array ) {
			if ( $size_array['crop'] ) {
				$only_crop_sizes[ $name ] = $size_array;
			}
		}

		foreach ( $this->site_icon_sizes as $size ) {
			if ( $size < $this->min_size ) {
				$only_crop_sizes[ 'site_icon-' . $size ] = array(
					'width ' => $size,
					'height' => $size,
					'crop'   => true,
				);
			}
		}

		return $only_crop_sizes;
	}

	/**
	 * Add Site Icon sizes to the array of image sizes on demand.
	 *
	 * @since 4.3.0
	 *
	 * @param array $sizes
	 * @return array
	 */
	public function intermediate_image_sizes( $sizes = array() ) {
		/** This filter is documented in wp-admin/includes/site-icon.php */
		$this->site_icon_sizes = apply_filters( 'site_icon_image_sizes', $this->site_icon_sizes );
		foreach ( $this->site_icon_sizes as $size ) {
			$sizes[] = 'site_icon-' . $size;
		}

		return $sizes;
	}

	/**
	 * Deletes all additional image sizes, used for site icons.
	 *
	 * @since 4.3.0
	 *
	 * @return bool
	 */
	public function delete_site_icon() {
		// We add the filter to make sure that we also delete all the added images.
		add_filter( 'intermediate_image_sizes', array( $this, 'intermediate_image_sizes' ) );
		wp_delete_attachment( get_option( 'site_icon' ), true );
		remove_filter( 'intermediate_image_sizes', array( $this, 'intermediate_image_sizes' ) );

		return delete_option( 'site_icon' );
	}

	/**
	 * Deletes the Site Icon when the image file is deleted.
	 *
	 * @since 4.3.0
	 *
	 * @param int $post_id Attachment ID.
	 */
	public function delete_attachment_data( $post_id ) {
		$site_icon_id = get_option( 'site_icon' );

		if ( $site_icon_id && $post_id == $site_icon_id ) {
			delete_option( 'site_icon' );
		}
	}

	/**
	 * Adds custom image sizes when meta data for an image is requested, that happens to be used as Site Icon.
	 *
	 * @since 4.3.0
	 *
	 * @param null|array|string $value    The value get_metadata() should
	 *                                    return - a single metadata value,
	 *                                    or an array of values.
	 * @param int               $post_id  Post ID.
	 * @param string            $meta_key Meta key.
	 * @param string|array      $single   Meta value, or an array of values.
	 * @return array|null|string
	 */
	public function get_post_metadata( $value, $post_id, $meta_key, $single ) {
		$site_icon_id = get_option( 'site_icon' );

		if ( $post_id == $site_icon_id && '_wp_attachment_backup_sizes' == $meta_key && $single ) {
			add_filter( 'intermediate_image_sizes', array( $this, 'intermediate_image_sizes' ) );
		}

		return $value;
	}
}

/**
 * @global WP_Site_Icon $wp_site_icon
 */
$GLOBALS['wp_site_icon'] = new WP_Site_Icon;
