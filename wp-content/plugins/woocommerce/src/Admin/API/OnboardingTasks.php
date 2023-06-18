<?php
/**
 * REST API Onboarding Tasks Controller
 *
 * Handles requests to complete various onboarding tasks.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingIndustries;
use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\DeprecatedExtendedTask;

defined( 'ABSPATH' ) || exit;

/**
 * Onboarding Tasks Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class OnboardingTasks extends \WC_REST_Data_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-admin';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'onboarding/tasks';

	/**
	 * Duration to milisecond mapping.
	 *
	 * @var string
	 */
	protected $duration_to_ms = array(
		'day'  => DAY_IN_SECONDS * 1000,
		'hour' => HOUR_IN_SECONDS * 1000,
		'week' => WEEK_IN_SECONDS * 1000,
	);

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/import_sample_products',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'import_sample_products' ),
					'permission_callback' => array( $this, 'create_products_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/create_homepage',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_homepage' ),
					'permission_callback' => array( $this, 'create_pages_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/create_product_from_template',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_product_from_template' ),
					'permission_callback' => array( $this, 'create_products_permission_check' ),
					'args'                => array_merge(
						$this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
						array(
							'template_name' => array(
								'required'    => true,
								'type'        => 'string',
								'description' => __( 'Product template name.', 'woocommerce' ),
							),
						)
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_tasks' ),
					'permission_callback' => array( $this, 'get_tasks_permission_check' ),
					'args'                => array(
						'ids' => array(
							'description'       => __( 'Optional parameter to get only specific task lists by id.', 'woocommerce' ),
							'type'              => 'array',
							'sanitize_callback' => 'wp_parse_slug_list',
							'validate_callback' => 'rest_validate_request_arg',
							'items'             => array(
								'enum' => TaskLists::get_list_ids(),
								'type' => 'string',
							),
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_tasks' ),
					'permission_callback' => array( $this, 'get_tasks_permission_check' ),
					'args'                => $this->get_task_list_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_\-]+)/hide',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'hide_task_list' ),
					'permission_callback' => array( $this, 'hide_task_list_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_\-]+)/unhide',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'unhide_task_list' ),
					'permission_callback' => array( $this, 'hide_task_list_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_\-]+)/dismiss',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'dismiss_task' ),
					'permission_callback' => array( $this, 'get_tasks_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_\-]+)/undo_dismiss',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'undo_dismiss_task' ),
					'permission_callback' => array( $this, 'get_tasks_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_-]+)/snooze',
			array(
				'args'   => array(
					'duration'     => array(
						'description'       => __( 'Time period to snooze the task.', 'woocommerce' ),
						'type'              => 'string',
						'validate_callback' => function( $param, $request, $key ) {
							return in_array( $param, array_keys( $this->duration_to_ms ), true );
						},
					),
					'task_list_id' => array(
						'description' => __( 'Optional parameter to query specific task list.', 'woocommerce' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'snooze_task' ),
					'permission_callback' => array( $this, 'snooze_task_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_\-]+)/action',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'action_task' ),
					'permission_callback' => array( $this, 'get_tasks_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[a-z0-9_\-]+)/undo_snooze',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'undo_snooze_task' ),
					'permission_callback' => array( $this, 'snooze_task_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to create a product.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_products_permission_check( $request ) {
		if ( ! wc_rest_check_post_permissions( 'product', 'create' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to create a product.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_pages_permission_check( $request ) {
		if ( ! wc_rest_check_post_permissions( 'page', 'create' ) || ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create new pages.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to manage woocommerce.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_tasks_permission_check( $request ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to retrieve onboarding tasks.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has permission to hide task lists.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function hide_task_list_permission_check( $request ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_update', __( 'Sorry, you are not allowed to hide task lists.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to manage woocommerce.
	 *
	 * @deprecated 7.8.0 snooze task is deprecated.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function snooze_task_permissions_check( $request ) {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.8.0' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to snooze onboarding tasks.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Import sample products from given CSV path.
	 *
	 * @param  string $csv_file CSV file path.
	 * @return WP_Error|WP_REST_Response
	 */
	public static function import_sample_products_from_csv( $csv_file ) {
		include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';

		if ( file_exists( $csv_file ) && class_exists( 'WC_Product_CSV_Importer' ) ) {
			// Override locale so we can return mappings from WooCommerce in English language stores.
			add_filter( 'locale', '__return_false', 9999 );
			$importer_class = apply_filters( 'woocommerce_product_csv_importer_class', 'WC_Product_CSV_Importer' );
			$args           = array(
				'parse'   => true,
				'mapping' => self::get_header_mappings( $csv_file ),
			);
			$args           = apply_filters( 'woocommerce_product_csv_importer_args', $args, $importer_class );

			$importer = new $importer_class( $csv_file, $args );
			$import   = $importer->import();
			return $import;
		} else {
			return new \WP_Error( 'woocommerce_rest_import_error', __( 'Sorry, the sample products data file was not found.', 'woocommerce' ) );
		}
	}

	/**
	 * Import sample products from WooCommerce sample CSV.
	 *
	 * @internal
	 * @return WP_Error|WP_REST_Response
	 */
	public static function import_sample_products() {
		$sample_csv_file = Features::is_enabled( 'experimental-fashion-sample-products' ) ? WC_ABSPATH . 'sample-data/experimental_fashion_sample_9_products.csv' :
		WC_ABSPATH . 'sample-data/experimental_sample_9_products.csv';

		$import = self::import_sample_products_from_csv( $sample_csv_file );
		return rest_ensure_response( $import );
	}

	/**
	 * Creates a product from a template name passed in through the template_name param.
	 *
	 * @internal
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function create_product_from_template( $request ) {
		$template_name = basename( $request->get_param( 'template_name' ) );
		$template_path = __DIR__ . '/Templates/' . $template_name . '_product.csv';
		$template_path = apply_filters( 'woocommerce_product_template_csv_file_path', $template_path, $template_name );

		$import = self::import_sample_products_from_csv( $template_path );

		if ( is_wp_error( $import ) || 0 === count( $import['imported'] ) ) {
			return new \WP_Error(
				'woocommerce_rest_product_creation_error',
				/* translators: %s is template name */
				__( 'Sorry, creating the product with template failed.', 'woocommerce' ),
				array( 'status' => 500 )
			);
		}
		$product = wc_get_product( $import['imported'][0] );
		$product->set_status( 'auto-draft' );
		$product->save();

		return rest_ensure_response(
			array(
				'id' => $product->get_id(),
			)
		);
	}


	/**
	 * Get header mappings from CSV columns.
	 *
	 * @internal
	 * @param string $file File path.
	 * @return array Mapped headers.
	 */
	public static function get_header_mappings( $file ) {
		include_once WC_ABSPATH . 'includes/admin/importers/mappings/mappings.php';

		$importer_class  = apply_filters( 'woocommerce_product_csv_importer_class', 'WC_Product_CSV_Importer' );
		$importer        = new $importer_class( $file, array() );
		$raw_headers     = $importer->get_raw_keys();
		$default_columns = wc_importer_default_english_mappings( array() );
		$special_columns = wc_importer_default_special_english_mappings( array() );

		$headers = array();
		foreach ( $raw_headers as $key => $field ) {
			$index             = $field;
			$headers[ $index ] = $field;

			if ( isset( $default_columns[ $field ] ) ) {
				$headers[ $index ] = $default_columns[ $field ];
			} else {
				foreach ( $special_columns as $regex => $special_key ) {
					if ( preg_match( self::sanitize_special_column_name_regex( $regex ), $field, $matches ) ) {
						$headers[ $index ] = $special_key . $matches[1];
						break;
					}
				}
			}
		}

		return $headers;
	}

	/**
	 * Sanitize special column name regex.
	 *
	 * @internal
	 * @param  string $value Raw special column name.
	 * @return string
	 */
	public static function sanitize_special_column_name_regex( $value ) {
		return '/' . str_replace( array( '%d', '%s' ), '(.*)', trim( quotemeta( $value ) ) ) . '/';
	}

	/**
	 * Returns a valid cover block with an image, if one exists, or background as a fallback.
	 *
	 * @internal
	 * @param  array $image Image to use for the cover block. Should contain a media ID and image URL.
	 * @return string Block content.
	 */
	private static function get_homepage_cover_block( $image ) {
		$shop_url = get_permalink( wc_get_page_id( 'shop' ) );
		if ( ! empty( $image['url'] ) && ! empty( $image['id'] ) ) {
			return '<!-- wp:cover {"url":"' . esc_url( $image['url'] ) . '","id":' . intval( $image['id'] ) . ',"dimRatio":0} -->
			<div class="wp-block-cover" style="background-image:url(' . esc_url( $image['url'] ) . ')"><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"' . __( 'Write title…', 'woocommerce' ) . '","textColor":"white","fontSize":"large"} -->
			<p class="has-text-align-center has-large-font-size">' . __( 'Welcome to the store', 'woocommerce' ) . '</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"align":"center","textColor":"white"} -->
			<p class="has-text-color has-text-align-center">' . __( 'Write a short welcome message here', 'woocommerce' ) . '</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons"><!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link" href="' . esc_url( $shop_url ) . '">' . __( 'Go shopping', 'woocommerce' ) . '</a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons --></div></div>
			<!-- /wp:cover -->';
		}

		return '<!-- wp:cover {"dimRatio":0} -->
		<div class="wp-block-cover"><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"' . __( 'Write title…', 'woocommerce' ) . '","textColor":"white","fontSize":"large"} -->
		<p class="has-text-color has-text-align-center has-large-font-size">' . __( 'Welcome to the store', 'woocommerce' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"align":"center","textColor":"white"} -->
		<p class="has-text-color has-text-align-center">' . __( 'Write a short welcome message here', 'woocommerce' ) . '</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons"><!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link" href="' . esc_url( $shop_url ) . '">' . __( 'Go shopping', 'woocommerce' ) . '</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons --></div></div>
		<!-- /wp:cover -->';
	}

	/**
	 * Returns a valid media block with an image, if one exists, or a uninitialized media block the user can set.
	 *
	 * @internal
	 * @param  array  $image Image to use for the cover block. Should contain a media ID and image URL.
	 * @param  string $align If the image should be aligned to the left or right.
	 * @return string Block content.
	 */
	private static function get_homepage_media_block( $image, $align = 'left' ) {
		$media_position = 'right' === $align ? '"mediaPosition":"right",' : '';
		$css_class      = 'right' === $align ? ' has-media-on-the-right' : '';

		if ( ! empty( $image['url'] ) && ! empty( $image['id'] ) ) {
			return '<!-- wp:media-text {' . $media_position . '"mediaId":' . intval( $image['id'] ) . ',"mediaType":"image"} -->
			<div class="wp-block-media-text alignwide' . $css_class . '""><figure class="wp-block-media-text__media"><img src="' . esc_url( $image['url'] ) . '" alt="" class="wp-image-' . intval( $image['id'] ) . '"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"' . __( 'Content…', 'woocommerce' ) . '","fontSize":"large"} -->
			<p class="has-large-font-size"></p>
			<!-- /wp:paragraph --></div></div>
			<!-- /wp:media-text -->';
		}

		return '<!-- wp:media-text {' . $media_position . '} -->
		<div class="wp-block-media-text alignwide' . $css_class . '"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"' . __( 'Content…', 'woocommerce' ) . '","fontSize":"large"} -->
		<p class="has-large-font-size"></p>
		<!-- /wp:paragraph --></div></div>
		<!-- /wp:media-text -->';
	}

	/**
	 * Returns a homepage template to be inserted into a post. A different template will be used depending on the number of products.
	 *
	 * @internal
	 * @param int $post_id ID of the homepage template.
	 * @return string Template contents.
	 */
	private static function get_homepage_template( $post_id ) {
		$products = wp_count_posts( 'product' );
		if ( $products->publish >= 4 ) {
			$images   = self::sideload_homepage_images( $post_id, 1 );
			$image_1  = ! empty( $images[0] ) ? $images[0] : '';
			$template = self::get_homepage_cover_block( $image_1 ) . '
				<!-- wp:heading {"align":"center"} -->
				<h2 style="text-align:center">' . __( 'Shop by Category', 'woocommerce' ) . '</h2>
				<!-- /wp:heading -->
				<!-- wp:shortcode -->
				[product_categories number="0" parent="0"]
				<!-- /wp:shortcode -->
				<!-- wp:heading {"align":"center"} -->
				<h2 style="text-align:center">' . __( 'New In', 'woocommerce' ) . '</h2>
				<!-- /wp:heading -->
				<!-- wp:woocommerce/product-new {"columns":4} /-->
				<!-- wp:heading {"align":"center"} -->
				<h2 style="text-align:center">' . __( 'Fan Favorites', 'woocommerce' ) . '</h2>
				<!-- /wp:heading -->
				<!-- wp:woocommerce/product-top-rated {"columns":4} /-->
				<!-- wp:heading {"align":"center"} -->
				<h2 style="text-align:center">' . __( 'On Sale', 'woocommerce' ) . '</h2>
				<!-- /wp:heading -->
				<!-- wp:woocommerce/product-on-sale {"columns":4} /-->
				<!-- wp:heading {"align":"center"} -->
				<h2 style="text-align:center">' . __( 'Best Sellers', 'woocommerce' ) . '</h2>
				<!-- /wp:heading -->
				<!-- wp:woocommerce/product-best-sellers {"columns":4} /-->
			';

			/**
			 * Modify the template/content of the default homepage.
			 *
			 * @param string $template The default homepage template.
			 */
			return apply_filters( 'woocommerce_admin_onboarding_homepage_template', $template );
		}

		$images   = self::sideload_homepage_images( $post_id, 3 );
		$image_1  = ! empty( $images[0] ) ? $images[0] : '';
		$image_2  = ! empty( $images[1] ) ? $images[1] : '';
		$image_3  = ! empty( $images[2] ) ? $images[2] : '';
		$template = self::get_homepage_cover_block( $image_1 ) . '
		<!-- wp:heading {"align":"center"} -->
		<h2 style="text-align:center">' . __( 'New Products', 'woocommerce' ) . '</h2>
		<!-- /wp:heading -->

		<!-- wp:woocommerce/product-new /--> ' .

		self::get_homepage_media_block( $image_1, 'right' ) .
		self::get_homepage_media_block( $image_2, 'left' ) .
		self::get_homepage_media_block( $image_3, 'right' ) . '

		<!-- wp:woocommerce/featured-product /-->';

		/** This filter is documented in src/API/OnboardingTasks.php. */
		return apply_filters( 'woocommerce_admin_onboarding_homepage_template', $template );
	}

	/**
	 * Gets the possible industry images from the plugin folder for sideloading. If an image doesn't exist, other.jpg is used a fallback.
	 *
	 * @internal
	 * @return array An array of images by industry.
	 */
	private static function get_available_homepage_images() {
		$industry_images = array();
		$industries      = OnboardingIndustries::get_allowed_industries();
		foreach ( $industries as $industry_slug => $label ) {
			$industry_images[ $industry_slug ] = apply_filters( 'woocommerce_admin_onboarding_industry_image', WC_ADMIN_IMAGES_FOLDER_URL . '/onboarding/other-small.jpg', $industry_slug );
		}
		return $industry_images;
	}

	/**
	 * Uploads a number of images to a homepage template, depending on the selected industry from the profile wizard.
	 *
	 * @internal
	 * @param  int $post_id ID of the homepage template.
	 * @param  int $number_of_images The number of images that should be sideloaded (depending on how many media slots are in the template).
	 * @return array An array of images that have been attached to the post.
	 */
	private static function sideload_homepage_images( $post_id, $number_of_images ) {
		$profile            = get_option( OnboardingProfile::DATA_OPTION, array() );
		$images_to_sideload = array();
		$available_images   = self::get_available_homepage_images();

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		if ( ! empty( $profile['industry'] ) ) {
			foreach ( $profile['industry'] as $selected_industry ) {
				if ( is_string( $selected_industry ) ) {
					$industry_slug = $selected_industry;
				} elseif ( is_array( $selected_industry ) && ! empty( $selected_industry['slug'] ) ) {
					$industry_slug = $selected_industry['slug'];
				} else {
					continue;
				}
				// Capture the first industry for use in our minimum images logic.
				$first_industry       = isset( $first_industry ) ? $first_industry : $industry_slug;
				$images_to_sideload[] = ! empty( $available_images[ $industry_slug ] ) ? $available_images[ $industry_slug ] : $available_images['other'];
			}
		}

		// Make sure we have at least {$number_of_images} images.
		if ( count( $images_to_sideload ) < $number_of_images ) {
			for ( $i = count( $images_to_sideload ); $i < $number_of_images; $i++ ) {
				// Fill up missing image slots with the first selected industry, or other.
				$industry             = isset( $first_industry ) ? $first_industry : 'other';
				$images_to_sideload[] = empty( $available_images[ $industry ] ) ? $available_images['other'] : $available_images[ $industry ];
			}
		}

		$already_sideloaded = array();
		$images_for_post    = array();
		foreach ( $images_to_sideload as $image ) {
			// Avoid uploading two of the same image, if an image is repeated.
			if ( ! empty( $already_sideloaded[ $image ] ) ) {
				$images_for_post[] = $already_sideloaded[ $image ];
				continue;
			}

			$sideload_id = \media_sideload_image( $image, $post_id, null, 'id' );
			if ( ! is_wp_error( $sideload_id ) ) {
				$sideload_url                 = wp_get_attachment_url( $sideload_id );
				$already_sideloaded[ $image ] = array(
					'id'  => $sideload_id,
					'url' => $sideload_url,
				);
				$images_for_post[]            = $already_sideloaded[ $image ];
			}
		}

		return $images_for_post;
	}

	/**
	 * Create a homepage from a template.
	 *
	 * @return WP_Error|array
	 */
	public static function create_homepage() {
		$post_id = wp_insert_post(
			array(
				'post_title'   => __( 'Homepage', 'woocommerce' ),
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '', // Template content is updated below, so images can be attached to the post.
			)
		);

		if ( ! is_wp_error( $post_id ) && 0 < $post_id ) {

			$template = self::get_homepage_template( $post_id );
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $template,
				)
			);

			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $post_id );
			update_option( 'woocommerce_onboarding_homepage_post_id', $post_id );

			// Use the full width template on stores using Storefront.
			if ( 'storefront' === get_stylesheet() ) {
				update_post_meta( $post_id, '_wp_page_template', 'template-fullwidth.php' );
			}

			return array(
				'status'         => 'success',
				'message'        => __( 'Homepage created', 'woocommerce' ),
				'post_id'        => $post_id,
				'edit_post_link' => htmlspecialchars_decode( get_edit_post_link( $post_id ) ),
			);
		} else {
			return $post_id;
		}
	}

	/**
	 * Get the query params for task lists.
	 *
	 * @return array
	 */
	public function get_task_list_params() {
		$params                   = array();
		$params['ids']            = array(
			'description'       => __( 'Optional parameter to get only specific task lists by id.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'enum' => TaskLists::get_list_ids(),
				'type' => 'string',
			),
		);
		$params['extended_tasks'] = array(
			'description'       => __( 'List of extended deprecated tasks from the client side filter.', 'woocommerce' ),
			'type'              => 'array',
			'validate_callback' => function( $param, $request, $key ) {
				$has_valid_keys = true;
				foreach ( $param as $task ) {
					if ( $has_valid_keys ) {
						$has_valid_keys = array_key_exists( 'list_id', $task ) && array_key_exists( 'id', $task );
					}
				}
				return $has_valid_keys;
			},
		);
		return $params;
	}

	/**
	 * Get the onboarding tasks.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_tasks( $request ) {
		$extended_tasks = $request->get_param( 'extended_tasks' );
		$task_list_ids  = $request->get_param( 'ids' );

		TaskLists::maybe_add_extended_tasks( $extended_tasks );

		$lists = is_array( $task_list_ids ) && count( $task_list_ids ) > 0 ? TaskLists::get_lists_by_ids( $task_list_ids ) : TaskLists::get_lists();

		$json = array_map(
			function( $list ) {
				return $list->sort_tasks()->get_json();
			},
			$lists
		);

		return rest_ensure_response( array_values( apply_filters( 'woocommerce_admin_onboarding_tasks', $json ) ) );
	}

	/**
	 * Dismiss a single task.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function dismiss_task( $request ) {
		$id   = $request->get_param( 'id' );
		$task = TaskLists::get_task( $id );

		if ( ! $task && $id ) {
			$task = new DeprecatedExtendedTask(
				array(
					'id'             => $id,
					'is_dismissable' => true,
				)
			);
		}

		if ( ! $task || ! $task->is_dismissable() ) {
			return new \WP_Error(
				'woocommerce_rest_invalid_task',
				__( 'Sorry, no dismissable task with that ID was found.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$task->dismiss();
		return rest_ensure_response( $task->get_json() );
	}

	/**
	 * Undo dismissal of a single task.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function undo_dismiss_task( $request ) {
		$id   = $request->get_param( 'id' );
		$task = TaskLists::get_task( $id );

		if ( ! $task && $id ) {
			$task = new DeprecatedExtendedTask(
				array(
					'id'             => $id,
					'is_dismissable' => true,
				)
			);
		}

		if ( ! $task || ! $task->is_dismissable() ) {
			return new \WP_Error(
				'woocommerce_rest_invalid_task',
				__( 'Sorry, no dismissable task with that ID was found.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$task->undo_dismiss();

		return rest_ensure_response( $task->get_json() );
	}

	/**
	 * Snooze an onboarding task.
	 *
	 * @deprecated 7.8.0 snooze task is deprecated.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function snooze_task( $request ) {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.8.0' );

		$task_id      = $request->get_param( 'id' );
		$task_list_id = $request->get_param( 'task_list_id' );
		$duration     = $request->get_param( 'duration' );

		$task = TaskLists::get_task( $task_id, $task_list_id );

		if ( ! $task && $task_id ) {
			$task = new DeprecatedExtendedTask(
				array(
					'id'            => $task_id,
					'is_snoozeable' => true,
				)
			);
		}

		if ( ! $task || ! $task->is_snoozeable() ) {
			return new \WP_Error(
				'woocommerce_rest_invalid_task',
				__( 'Sorry, no snoozeable task with that ID was found.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$task->snooze( isset( $duration ) ? $duration : 'day' );
		return rest_ensure_response( $task->get_json() );
	}

	/**
	 * Undo snooze of a single task.
	 *
	 * @deprecated 7.8.0 undo snooze task is deprecated.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function undo_snooze_task( $request ) {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.8.0' );

		$id   = $request->get_param( 'id' );
		$task = TaskLists::get_task( $id );

		if ( ! $task && $id ) {
			$task = new DeprecatedExtendedTask(
				array(
					'id'            => $id,
					'is_snoozeable' => true,
				)
			);
		}

		if ( ! $task || ! $task->is_snoozeable() ) {
			return new \WP_Error(
				'woocommerce_rest_invalid_task',
				__( 'Sorry, no snoozeable task with that ID was found.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$task->undo_snooze();
		return rest_ensure_response( $task->get_json() );
	}

	/**
	 * Hide a task list.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function hide_task_list( $request ) {
		$id        = $request->get_param( 'id' );
		$task_list = TaskLists::get_list( $id );

		if ( ! $task_list ) {
			return new \WP_Error(
				'woocommerce_rest_invalid_task_list',
				__( 'Sorry, that task list was not found', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$update = $task_list->hide();
		$json   = $task_list->get_json();

		return rest_ensure_response( $json );
	}

	/**
	 * Unhide a task list.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function unhide_task_list( $request ) {
		$id        = $request->get_param( 'id' );
		$task_list = TaskLists::get_list( $id );

		if ( ! $task_list ) {
			return new \WP_Error(
				'woocommerce_tasks_invalid_task_list',
				__( 'Sorry, that task list was not found', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$update = $task_list->unhide();
		$json   = $task_list->get_json();

		return rest_ensure_response( $json );
	}

	/**
	 * Action a single task.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function action_task( $request ) {
		$id   = $request->get_param( 'id' );
		$task = TaskLists::get_task( $id );

		if ( ! $task && $id ) {
			$task = new DeprecatedExtendedTask(
				array(
					'id' => $id,
				)
			);
		}

		if ( ! $task ) {
			return new \WP_Error(
				'woocommerce_rest_invalid_task',
				__( 'Sorry, no task with that ID was found.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		$task->mark_actioned();
		return rest_ensure_response( $task->get_json() );
	}

}
