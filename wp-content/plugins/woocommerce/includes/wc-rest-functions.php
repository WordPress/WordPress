<?php
/**
 * WooCommerce REST Functions
 *
 * Functions for REST specific things.
 *
 * @package WooCommerce\Functions
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parses and formats a date for ISO8601/RFC3339.
 *
 * Required WP 4.4 or later.
 * See https://developer.wordpress.org/reference/functions/mysql_to_rfc3339/
 *
 * @since  2.6.0
 * @param  string|null|WC_DateTime $date Date.
 * @param  bool                    $utc  Send false to get local/offset time.
 * @return string|null ISO8601/RFC3339 formatted datetime.
 */
function wc_rest_prepare_date_response( $date, $utc = true ) {
	if ( is_numeric( $date ) ) {
		$date = new WC_DateTime( "@$date", new DateTimeZone( 'UTC' ) );
		$date->setTimezone( new DateTimeZone( wc_timezone_string() ) );
	} elseif ( is_string( $date ) ) {
		$date = new WC_DateTime( $date, new DateTimeZone( 'UTC' ) );
		$date->setTimezone( new DateTimeZone( wc_timezone_string() ) );
	}

	if ( ! is_a( $date, 'WC_DateTime' ) ) {
		return null;
	}

	// Get timestamp before changing timezone to UTC.
	return gmdate( 'Y-m-d\TH:i:s', $utc ? $date->getTimestamp() : $date->getOffsetTimestamp() );
}

/**
 * Returns image mime types users are allowed to upload via the API.
 *
 * @since  2.6.4
 * @return array
 */
function wc_rest_allowed_image_mime_types() {
	return apply_filters(
		'woocommerce_rest_allowed_image_mime_types',
		array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'bmp'          => 'image/bmp',
			'tiff|tif'     => 'image/tiff',
			'ico'          => 'image/x-icon',
			'webp'         => 'image/webp',
		)
	);
}

/**
 * Upload image from URL.
 *
 * @since 2.6.0
 * @param string $image_url Image URL.
 * @return array|WP_Error Attachment data or error message.
 */
function wc_rest_upload_image_from_url( $image_url ) {
	$parsed_url = wp_parse_url( $image_url );

	// Check parsed URL.
	if ( ! $parsed_url || ! is_array( $parsed_url ) ) {
		/* translators: %s: image URL */
		return new WP_Error( 'woocommerce_rest_invalid_image_url', sprintf( __( 'Invalid URL %s.', 'woocommerce' ), $image_url ), array( 'status' => 400 ) );
	}

	// Ensure url is valid.
	$image_url = esc_url_raw( $image_url );

	// download_url function is part of wp-admin.
	if ( ! function_exists( 'download_url' ) ) {
		include_once ABSPATH . 'wp-admin/includes/file.php';
	}

	$file_array         = array();
	$file_array['name'] = basename( current( explode( '?', $image_url ) ) );

	// Download file to temp location.
	$file_array['tmp_name'] = download_url( $image_url );

	// If error storing temporarily, return the error.
	if ( is_wp_error( $file_array['tmp_name'] ) ) {
		return new WP_Error(
			'woocommerce_rest_invalid_remote_image_url',
			/* translators: %s: image URL */
			sprintf( __( 'Error getting remote image %s.', 'woocommerce' ), $image_url ) . ' '
			/* translators: %s: error message */
			. sprintf( __( 'Error: %s', 'woocommerce' ), $file_array['tmp_name']->get_error_message() ),
			array( 'status' => 400 )
		);
	}

	// Do the validation and storage stuff.
	$file = wp_handle_sideload(
		$file_array,
		array(
			'test_form' => false,
			'mimes'     => wc_rest_allowed_image_mime_types(),
		),
		current_time( 'Y/m' )
	);

	if ( isset( $file['error'] ) ) {
		@unlink( $file_array['tmp_name'] ); // @codingStandardsIgnoreLine.

		/* translators: %s: error message */
		return new WP_Error( 'woocommerce_rest_invalid_image', sprintf( __( 'Invalid image: %s', 'woocommerce' ), $file['error'] ), array( 'status' => 400 ) );
	}

	do_action( 'woocommerce_rest_api_uploaded_image_from_url', $file, $image_url );

	return $file;
}

/**
 * Set uploaded image as attachment.
 *
 * @since 2.6.0
 * @param array $upload Upload information from wp_upload_bits.
 * @param int   $id Post ID. Default to 0.
 * @return int Attachment ID
 */
function wc_rest_set_uploaded_image_as_attachment( $upload, $id = 0 ) {
	$info    = wp_check_filetype( $upload['file'] );
	$title   = '';
	$content = '';

	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		include_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$image_meta = @wp_read_image_metadata( $upload['file'] );
	if ( $image_meta ) {
		if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
			$title = wc_clean( $image_meta['title'] );
		}
		if ( trim( $image_meta['caption'] ) ) {
			$content = wc_clean( $image_meta['caption'] );
		}
	}

	$attachment = array(
		'post_mime_type' => $info['type'],
		'guid'           => $upload['url'],
		'post_parent'    => $id,
		'post_title'     => $title ? $title : basename( $upload['file'] ),
		'post_content'   => $content,
	);

	$attachment_id = wp_insert_attachment( $attachment, $upload['file'], $id );
	if ( ! is_wp_error( $attachment_id ) ) {
		@wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	}

	return $attachment_id;
}

/**
 * Validate reports request arguments.
 *
 * @since 2.6.0
 * @param mixed           $value   Value to validate.
 * @param WP_REST_Request $request Request instance.
 * @param string          $param   Param to validate.
 * @return WP_Error|boolean
 */
function wc_rest_validate_reports_request_arg( $value, $request, $param ) {

	$attributes = $request->get_attributes();
	if ( ! isset( $attributes['args'][ $param ] ) || ! is_array( $attributes['args'][ $param ] ) ) {
		return true;
	}
	$args = $attributes['args'][ $param ];

	if ( 'string' === $args['type'] && ! is_string( $value ) ) {
		/* translators: 1: param 2: type */
		return new WP_Error( 'woocommerce_rest_invalid_param', sprintf( __( '%1$s is not of type %2$s', 'woocommerce' ), $param, 'string' ) );
	}

	if ( 'date' === $args['format'] ) {
		$regex = '#^\d{4}-\d{2}-\d{2}$#';

		if ( ! preg_match( $regex, $value, $matches ) ) {
			return new WP_Error( 'woocommerce_rest_invalid_date', __( 'The date you provided is invalid.', 'woocommerce' ) );
		}
	}

	return true;
}

/**
 * Encodes a value according to RFC 3986.
 * Supports multidimensional arrays.
 *
 * @since 2.6.0
 * @param string|array $value The value to encode.
 * @return string|array       Encoded values.
 */
function wc_rest_urlencode_rfc3986( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'wc_rest_urlencode_rfc3986', $value );
	}

	return str_replace( array( '+', '%7E' ), array( ' ', '~' ), rawurlencode( $value ) );
}

/**
 * Check permissions of posts on REST API.
 *
 * @since 2.6.0
 * @param string $post_type Post type.
 * @param string $context   Request context.
 * @param int    $object_id Post ID.
 * @return bool
 */
function wc_rest_check_post_permissions( $post_type, $context = 'read', $object_id = 0 ) {
	$contexts = array(
		'read'   => 'read_private_posts',
		'create' => 'publish_posts',
		'edit'   => 'edit_post',
		'delete' => 'delete_post',
		'batch'  => 'edit_others_posts',
	);

	if ( 'revision' === $post_type ) {
		$permission = false;
	} else {
		$cap              = $contexts[ $context ];
		$post_type_object = get_post_type_object( $post_type );
		$permission       = current_user_can( $post_type_object->cap->$cap, $object_id );
	}

	return apply_filters( 'woocommerce_rest_check_permissions', $permission, $context, $object_id, $post_type );
}

/**
 * Check permissions of users on REST API.
 *
 * @since 2.6.0
 * @param string $context   Request context.
 * @param int    $object_id Post ID.
 * @return bool
 */
function wc_rest_check_user_permissions( $context = 'read', $object_id = 0 ) {
	$contexts = array(
		'read'   => 'list_users',
		'create' => 'promote_users', // Check if current user can create users, shop managers are not allowed to create users.
		'edit'   => 'edit_users',
		'delete' => 'delete_users',
		'batch'  => 'promote_users',
	);

	// Check to allow shop_managers to manage only customers.
	if ( in_array( $context, array( 'edit', 'delete' ), true ) && wc_current_user_has_role( 'shop_manager' ) ) {
		$permission                  = false;
		$user_data                   = get_userdata( $object_id );
		$shop_manager_editable_roles = apply_filters( 'woocommerce_shop_manager_editable_roles', array( 'customer' ) );

		if ( isset( $user_data->roles ) ) {
			$can_manage_users = array_intersect( $user_data->roles, array_unique( $shop_manager_editable_roles ) );

			// Check if Shop Manager can edit customer or with the is same shop manager.
			if ( 0 < count( $can_manage_users ) || intval( $object_id ) === intval( get_current_user_id() ) ) {
				$permission = current_user_can( $contexts[ $context ], $object_id );
			}
		}
	} else {
		$permission = current_user_can( $contexts[ $context ], $object_id );
	}

	return apply_filters( 'woocommerce_rest_check_permissions', $permission, $context, $object_id, 'user' );
}

/**
 * Check permissions of product terms on REST API.
 *
 * @since 2.6.0
 * @param string $taxonomy  Taxonomy.
 * @param string $context   Request context.
 * @param int    $object_id Post ID.
 * @return bool
 */
function wc_rest_check_product_term_permissions( $taxonomy, $context = 'read', $object_id = 0 ) {
	$contexts = array(
		'read'   => 'manage_terms',
		'create' => 'edit_terms',
		'edit'   => 'edit_terms',
		'delete' => 'delete_terms',
		'batch'  => 'edit_terms',
	);

	$cap             = $contexts[ $context ];
	$taxonomy_object = get_taxonomy( $taxonomy );
	$permission      = current_user_can( $taxonomy_object->cap->$cap, $object_id );

	return apply_filters( 'woocommerce_rest_check_permissions', $permission, $context, $object_id, $taxonomy );
}

/**
 * Check manager permissions on REST API.
 *
 * @since 2.6.0
 * @param string $object  Object.
 * @param string $context Request context.
 * @return bool
 */
function wc_rest_check_manager_permissions( $object, $context = 'read' ) {
	$objects = array(
		'reports'          => 'view_woocommerce_reports',
		'settings'         => 'manage_woocommerce',
		'system_status'    => 'manage_woocommerce',
		'attributes'       => 'manage_product_terms',
		'shipping_methods' => 'manage_woocommerce',
		'payment_gateways' => 'manage_woocommerce',
		'webhooks'         => 'manage_woocommerce',
	);

	$permission = current_user_can( $objects[ $object ] );

	return apply_filters( 'woocommerce_rest_check_permissions', $permission, $context, 0, $object );
}

/**
 * Check product reviews permissions on REST API.
 *
 * @since 3.5.0
 * @param string $context   Request context.
 * @param string $object_id Object ID.
 * @return bool
 */
function wc_rest_check_product_reviews_permissions( $context = 'read', $object_id = 0 ) {
	$permission = false;
	$contexts   = array(
		'read'   => 'moderate_comments',
		'create' => 'edit_products',
		'edit'   => 'edit_products',
		'delete' => 'edit_products',
		'batch'  => 'edit_products',
	);

	if ( $object_id > 0 ) {
		$object = get_comment( $object_id );

		if ( ! is_a( $object, 'WP_Comment' ) || get_comment_type( $object ) !== 'review' ) {
			return false;
		}
	}

	if ( isset( $contexts[ $context ] ) ) {
		$permission = current_user_can( $contexts[ $context ], $object_id );
	}

	return apply_filters( 'woocommerce_rest_check_permissions', $permission, $context, $object_id, 'product_review' );
}
