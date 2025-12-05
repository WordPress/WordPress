<?php
namespace Elementor\Modules\Ai;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Element_Base;
use Elementor\Modules\Ai\Feature_Intro\Product_Image_Unification_Intro;
use Elementor\Plugin;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\Ai\Connect\Ai;
use Elementor\User;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	const HISTORY_TYPE_ALL = 'all';
	const HISTORY_TYPE_TEXT = 'text';
	const HISTORY_TYPE_CODE = 'code';
	const HISTORY_TYPE_IMAGE = 'images';
	const HISTORY_TYPE_BLOCK = 'blocks';
	const VALID_HISTORY_TYPES = [
		self::HISTORY_TYPE_ALL,
		self::HISTORY_TYPE_TEXT,
		self::HISTORY_TYPE_CODE,
		self::HISTORY_TYPE_IMAGE,
		self::HISTORY_TYPE_BLOCK,
	];
	const MIN_PAGES_FOR_CREATE_WITH_AI_BANNER = 10;

	public function get_name() {
		return 'ai';
	}

	public function __construct() {
		parent::__construct();

		( new SitePlannerConnect\Module() );

		if ( is_admin() ) {
			( new Preferences() )->register();
			add_action( 'elementor/import-export/import-kit/runner/after-run', [ $this, 'handle_kit_install' ] );
		}

		if ( ! $this->is_ai_enabled() ) {
			return;
		}

		add_filter( 'elementor/core/admin/homescreen', [ $this, 'add_create_with_ai_banner_to_homescreen' ] );

		add_action( 'elementor/connect/apps/register', function ( ConnectModule $connect_module ) {
			$connect_module->register_app( 'ai', Ai::get_class_name() );
		} );

		add_action( 'elementor/ajax/register_actions', function( $ajax ) {
			$handlers = [
				'ai_get_user_information' => [ $this, 'ajax_ai_get_user_information' ],
				'ai_get_remote_config' => [ $this, 'ajax_ai_get_remote_config' ],
				'ai_get_remote_frontend_config' => [ $this, 'ajax_ai_get_remote_frontend_config' ],
				'ai_get_completion_text' => [ $this, 'ajax_ai_get_completion_text' ],
				'ai_get_excerpt' => [ $this, 'ajax_ai_get_excerpt' ],
				'ai_get_featured_image' => [ $this, 'ajax_ai_get_featured_image' ],
				'ai_get_edit_text' => [ $this, 'ajax_ai_get_edit_text' ],
				'ai_get_custom_code' => [ $this, 'ajax_ai_get_custom_code' ],
				'ai_get_custom_css' => [ $this, 'ajax_ai_get_custom_css' ],
				'ai_set_get_started' => [ $this, 'ajax_ai_set_get_started' ],
				'ai_set_status_feedback' => [ $this, 'ajax_ai_set_status_feedback' ],
				'ai_get_image_prompt_enhancer' => [ $this, 'ajax_ai_get_image_prompt_enhancer' ],
				'ai_get_text_to_image' => [ $this, 'ajax_ai_get_text_to_image' ],
				'ai_get_image_to_image' => [ $this, 'ajax_ai_get_image_to_image' ],
				'ai_get_image_to_image_mask' => [ $this, 'ajax_ai_get_image_to_image_mask' ],
				'ai_get_image_to_image_mask_cleanup' => [ $this, 'ajax_ai_get_image_to_image_mask_cleanup' ],
				'ai_get_image_to_image_outpainting' => [ $this, 'ajax_ai_get_image_to_image_outpainting' ],
				'ai_get_image_to_image_upscale' => [ $this, 'ajax_ai_get_image_to_image_upscale' ],
				'ai_get_image_to_image_remove_background' => [ $this, 'ajax_ai_get_image_to_image_remove_background' ],
				'ai_get_image_to_image_replace_background' => [ $this, 'ajax_ai_get_image_to_image_replace_background' ],
				'ai_upload_image' => [ $this, 'ajax_ai_upload_image' ],
				'ai_generate_layout' => [ $this, 'ajax_ai_generate_layout' ],
				'ai_get_layout_prompt_enhancer' => [ $this, 'ajax_ai_get_layout_prompt_enhancer' ],
				'ai_get_history' => [ $this, 'ajax_ai_get_history' ],
				'ai_delete_history_item' => [ $this, 'ajax_ai_delete_history_item' ],
				'ai_toggle_favorite_history_item' => [ $this, 'ajax_ai_toggle_favorite_history_item' ],
				'ai_get_product_image_unification' => [ $this, 'ajax_ai_get_product_image_unification' ],
				'ai_get_animation' => [ $this, 'ajax_ai_get_animation' ],
				'ai_get_image_to_image_isolate_objects' => [ $this, 'ajax_ai_get_product_image_unification' ],
			];

			foreach ( $handlers as $tag => $callback ) {
				$ajax->register_ajax_action( $tag, $callback );
			}
		} );

		add_action( 'elementor/editor/before_enqueue_scripts', function() {
			$this->enqueue_main_script();
			$this->enqueue_layout_script();
		} );

		add_action( 'elementor/editor/after_enqueue_styles', function() {
			wp_enqueue_style(
				'elementor-ai-editor',
				$this->get_css_assets_url( 'modules/ai/editor' ),
				[],
				ELEMENTOR_VERSION
			);
		} );

		add_action( 'elementor/preview/enqueue_styles', function() {
			wp_enqueue_style(
				'elementor-ai-layout-preview',
				$this->get_css_assets_url( 'modules/ai/layout-preview' ),
				[],
				ELEMENTOR_VERSION
			);
		} );

		if ( is_admin() ) {
			add_action( 'wp_enqueue_media', [ $this, 'enqueue_ai_media_library' ] );
			add_action( 'admin_head', [ $this, 'enqueue_ai_media_library_upload_screen' ] );

			if ( current_user_can( 'edit_products' ) || current_user_can( 'publish_products' ) ) {
				add_action( 'admin_init', [ $this, 'enqueue_ai_products_page_scripts' ] );
				add_action( 'current_screen', [ $this, 'enqueue_ai_single_product_page_scripts' ] );
				add_action( 'wp_ajax_elementor-ai-get-product-images', [ $this, 'get_product_images_ajax' ] );
				add_action( 'wp_ajax_elementor-ai-set-product-images', [ $this, 'set_product_images_ajax' ] );
				Product_Image_Unification_Intro::add_hooks();
			}
		}

		add_action( 'enqueue_block_editor_assets', function() {
			wp_enqueue_script( 'elementor-ai-gutenberg',
				$this->get_js_assets_url( 'ai-gutenberg' ),
				[
					'jquery',
					'elementor-v2-ui',
					'elementor-v2-icons',
					'wp-blocks',
					'wp-element',
					'wp-editor',
					'wp-data',
					'wp-components',
					'wp-compose',
					'wp-i18n',
					'wp-hooks',
					'elementor-ai-media-library',
				],
			ELEMENTOR_VERSION, true );

			wp_localize_script(
				'elementor-ai-gutenberg',
				'ElementorAiConfig',
				[
					'is_get_started' => User::get_introduction_meta( 'ai_get_started' ),
					'connect_url' => $this->get_ai_connect_url(),
				]
			);

			wp_set_script_translations( 'elementor-ai-gutenberg', 'elementor' );
		});

		add_filter( 'elementor/document/save/data', function ( $data ) {
			return $this->remove_temporary_containers( $data );
		} );

		add_action( 'elementor/element/common/section_effects/after_section_start', [ $this, 'register_ai_motion_effect_control' ], 10, 1 );
		add_action( 'elementor/element/container/section_effects/after_section_start', [ $this, 'register_ai_motion_effect_control' ], 10, 1 );
		add_action( 'elementor/element/common/_section_transform/after_section_end', [ $this, 'register_ai_hover_effect_control' ], 10, 1 );
		add_action( 'elementor/element/container/_section_transform/after_section_end', [ $this, 'register_ai_hover_effect_control' ], 10, 1 );
	}

	public function is_ai_enabled() {
		if ( ! Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			return false;
		}

		return Preferences::is_ai_enabled( get_current_user_id() );
	}

	public function handle_kit_install( $imported_data ) {
		if ( ! $this->is_ai_enabled() ) {
			return;
		}

		if ( ! isset( $imported_data['status'] ) || 'success' !== $imported_data['status'] ) {
			return;
		}

		if ( ! isset( $imported_data['runner'] ) || 'site-settings' !== $imported_data['runner'] ) {
			return;
		}

		if ( ! isset( $imported_data['configData']['lastImportedSession']['instance_data']['site_settings']['settings']['ai'] ) ) {
			return;
		}

		$is_connected = $this->get_ai_app()->is_connected() && User::get_introduction_meta( 'ai_get_started' );

		if ( ! $is_connected ) {
			return;
		}

		$last_imported_session = $imported_data['configData']['lastImportedSession'];
		$imported_ai_data = $last_imported_session['instance_data']['site_settings']['settings']['ai'];

		$this->get_ai_app()->send_event( [
			'name' => 'kit_installed',
			'data' => $imported_ai_data,
			'client' => [
				'name' => 'elementor',
				'version' => ELEMENTOR_VERSION,
				'session_id' => $last_imported_session['session_id'],
			],
		] );
	}

	public function register_ai_hover_effect_control( Element_Base $element ) {
		if ( ! $element->get_controls( 'ai_hover_animation' ) ) {
			$element->add_control(
				'ai_hover_animation',
				[
					'tabs_wrapper' => '_tabs_positioning',
					'inner_tab' => '_tab_positioning_hover',
					'label' => esc_html__( 'Animate With AI', 'elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '
<style>
  .elementor-control-ai_hover_animation .elementor-control-content {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  .elementor-control-ai_hover_animation .elementor-control-raw-html {
  	display: none;
  }
</style>',
					'render_type' => 'none',
					'ai' => [
						'active' => true,
						'type' => 'hover_animation',
					],
				],
				[
					'position' => [
						'of' => '_transform_rotate_popover_hover',
						'type' => 'control',
						'at' => 'before',
					],
				]
			);
		}
	}
	public function register_ai_motion_effect_control( $element ) {
		if ( Utils::has_pro() && ! $element->get_controls( 'ai_animation' ) ) {
			$element->add_control(
				'ai_animation',
				[
					'label' => esc_html__( 'Animate With AI', 'elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '
	<style>
	.elementor-control-ai_animation .elementor-control-content {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: center;
	}
	.elementor-control-ai_animation .elementor-control-raw-html {
		display: none;
	}
	</style>',
					'render_type' => 'none',
					'ai' => [
						'active' => true,
						'type' => 'animation',
					],
				]
			);
		}
	}

	private function get_current_screen() {
		$is_wc = class_exists( 'WooCommerce' ) && post_type_exists( 'product' );
		if ( ! $is_wc ) {
			return 'other';
		}

		$is_products_page = isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'];
		if ( $is_products_page ) {
			return 'wc-products';
		}

		$screen = get_current_screen();
		$is_single_product_page = isset( $screen->post_type ) && ( 'product' === $screen->post_type && 'post' === $screen->base );
		if ( $is_single_product_page ) {
			return 'wc-single-product';
		}

		return 'other';
	}

	public function enqueue_ai_products_page_scripts() {
		if ( 'wc-products' !== $this->get_current_screen() ) {
			return;
		}

		$this->add_wc_scripts();
	}

	public function enqueue_ai_single_product_page_scripts() {
		if ( 'wc-single-product' !== $this->get_current_screen() ) {
			return;
		}

		$this->add_wc_scripts();
	}

	private function add_products_bulk_action( $bulk_actions ) {
		$bulk_actions['elementor-ai-unify-product-images'] = __( 'Unify with Elementor AI', 'elementor' );
		return $bulk_actions;
	}

	public function get_product_images_ajax() {
		check_ajax_referer( 'elementor-ai-unify-product-images_nonce', 'nonce' );

		$post_ids = isset( $_POST['post_ids'] ) ? array_map( 'intval', $_POST['post_ids'] ) : [];
		$is_galley_only = isset( $_POST['is_galley_only'] ) && sanitize_text_field( wp_unslash( $_POST['is_galley_only'] ) );

		$image_ids = [];

		foreach ( $post_ids as $post_id ) {
			if ( $is_galley_only ) {
				$product = wc_get_product( $post_id );
				$gallery_image_ids = $product->get_gallery_image_ids();
				foreach ( $gallery_image_ids as $image_id ) {
					$image_ids[] = [
						'productId' => $post_id,
						'id'   => $image_id,
						'image_url' => wp_get_attachment_url( $image_id ),
					];
				}
				continue;
			}

			$image_id = get_post_thumbnail_id( $post_id );

			if ( ! $image_id ) {
				$product = wc_get_product( $post_id );
				$gallery_image_ids = $product->get_gallery_image_ids();
				if ( ! empty( $gallery_image_ids ) ) {
					$image_id = $gallery_image_ids[0];
				}
			}

			$image_ids[] = [
				'productId' => $post_id,
				'id' => $image_id ? $image_id : 'No Image',
				'image_url' => $image_id ? wp_get_attachment_url( $image_id ) : 'No Image',
			];
		}

		wp_send_json_success( [ 'product_images' => array_slice( $image_ids, 0, 10 ) ] );

		wp_die();
	}

	private function get_attachment_id_by_url( $url ) {
		$attachments = get_posts( [
			'post_type'  => 'attachment',
			'meta_query' => [
				[
					'key'   => '_wp_attached_file',
					'value' => basename( $url ),
					'compare' => 'LIKE',
				],
			],
			'fields'     => 'ids',
			'numberposts' => 1,
		] );

		return ! empty( $attachments ) ? $attachments[0] : null;
	}

	public function set_product_images_ajax() {
		check_ajax_referer( 'elementor-ai-unify-product-images_nonce', 'nonce' );

		$product_id = isset( $_POST['productId'] ) ? sanitize_text_field( wp_unslash( $_POST['productId'] ) ) : '';
		$image_url = isset( $_POST['image_url'] ) ? sanitize_text_field( wp_unslash( $_POST['image_url'] ) ) : '';
		$image_to_add = isset( $_POST['image_to_add'] ) ? intval( wp_unslash( $_POST['image_to_add'] ) ) : null;
		$image_to_remove = isset( $_POST['image_to_remove'] ) ? intval( wp_unslash( $_POST['image_to_remove'] ) ) : null;
		$is_product_gallery = isset( $_POST['is_product_gallery'] ) && sanitize_text_field( wp_unslash( $_POST['is_product_gallery'] ) ) === 'true';

		if ( ! $product_id || ! $image_url ) {
			throw new \Exception( 'Product ID and Image URL are required' );
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			throw new \Exception( 'Product not found' );
		}

		$attachment_id = $this->get_attachment_id_by_url( $image_url );
		if ( is_wp_error( $attachment_id ) ) {
			throw new \Exception( 'Image upload failed' );
		}

		if ( $is_product_gallery ) {
			$this->update_product_gallery( $product, $image_to_remove, $image_to_add );
		} else {
			$product->set_image_id( $attachment_id );
			$product->save();
		}

		wp_send_json_success( [
			'message' => __( 'Image added successfully', 'elementor' ),
		] );
	}

	public function enqueue_ai_media_library_upload_screen() {
		$screen = get_current_screen();
		if ( ! $screen || 'upload' !== $screen->id ) {
			return;
		}

		$this->enqueue_ai_media_library();
	}

	public function enqueue_ai_media_library() {
		wp_enqueue_script( 'elementor-ai-media-library',
			$this->get_js_assets_url( 'ai-media-library' ),
			[
				'jquery',
				'elementor-v2-ui',
				'elementor-v2-icons',
				'media-grid',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_localize_script(
			'elementor-ai-media-library',
			'ElementorAiConfig',
			[
				'is_get_started' => User::get_introduction_meta( 'ai_get_started' ),
				'connect_url' => $this->get_ai_connect_url(),
			]
		);

		wp_set_script_translations( 'elementor-ai-media-library', 'elementor' );
	}

	private function enqueue_main_script() {
		wp_enqueue_script(
			'elementor-ai',
			$this->get_js_assets_url( 'ai' ),
			[
				'react',
				'react-dom',
				'backbone-marionette',
				'elementor-web-cli',
				'wp-date',
				'elementor-common',
				'elementor-editor-modules',
				'elementor-editor-document',
				'elementor-v2-ui',
				'elementor-v2-icons',
			],
			ELEMENTOR_VERSION,
			true
		);

		$config = [
			'is_get_started' => User::get_introduction_meta( 'ai_get_started' ),
			'connect_url' => $this->get_ai_connect_url(),
		];

		wp_localize_script(
			'elementor-ai',
			'ElementorAiConfig',
			$config
		);

		wp_set_script_translations( 'elementor-ai', 'elementor' );
	}

	private function enqueue_layout_script() {
		wp_enqueue_script(
			'elementor-ai-layout',
			$this->get_js_assets_url( 'ai-layout' ),
			[
				'react',
				'react-dom',
				'backbone-marionette',
				'elementor-common',
				'elementor-web-cli',
				'elementor-editor-modules',
				'elementor-ai',
				'elementor-v2-ui',
				'elementor-v2-icons',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'elementor-ai-layout', 'elementor' );
	}

	private function remove_temporary_containers( $data ) {
		if ( empty( $data['elements'] ) || ! is_array( $data['elements'] ) ) {
			return $data;
		}

		// If for some reason the document has been saved during an AI Layout session,
		// ensure that the temporary containers are removed from the data.
		$data['elements'] = array_filter( $data['elements'], function( $element ) {
			$is_preview_container = strpos( $element['id'], 'e-ai-preview-container' ) === 0;
			$is_screenshot_container = strpos( $element['id'], 'e-ai-screenshot-container' ) === 0;

			return ! $is_preview_container && ! $is_screenshot_container;
		} );

		return $data;
	}

	private function get_ai_connect_url() {
		$app = $this->get_ai_app();

		return $app->get_admin_url( 'authorize', [
			'utm_source' => 'ai-popup',
			'utm_campaign' => 'connect-account',
			'utm_medium' => 'wp-dash',
			'source' => 'generic',
		] );
	}

	public function ajax_ai_get_user_information( $data ) {
		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			return [
				'is_connected' => false,
				'connect_url' => $this->get_ai_connect_url(),
			];
		}

		$user_usage = wp_parse_args( $app->get_usage(), [
			'hasAiSubscription' => false,
			'usedQuota' => 0,
			'quota' => 100,
		] );

		return [
			'is_connected' => true,
			'is_get_started' => User::get_introduction_meta( 'ai_get_started' ),
			'usage' => $user_usage,
		];
	}

	public function ajax_ai_get_remote_config() {
		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			return [];
		}

		return $app->get_remote_config();
	}

	public function ajax_ai_get_remote_frontend_config( $data ) {
		$callback = function () use ( $data ) {
			return $this->get_ai_app()->get_remote_frontend_config( $data );
		};

		return Utils::get_cached_callback( $callback, 'ai_remote_frontend_config-' . get_current_user_id(), HOUR_IN_SECONDS );
	}

	public function verify_upload_permissions( $data ) {
		$referer = wp_get_referer();

		if ( str_contains( $referer, 'wp-admin/upload.php' ) && current_user_can( 'upload_files' ) ) {
			return;
		}
		$this->verify_permissions( $data['editor_post_id'] );
	}

	private function verify_permissions( $editor_post_id ) {
		$document = Plugin::$instance->documents->get( $editor_post_id );

		if ( ! $document ) {
			throw new \Exception( 'Document not found' );
		}

		if ( $document->is_built_with_elementor() ) {
			if ( ! $document->is_editable_by_current_user() ) {
				throw new \Exception( 'Access denied' );
			}
		} elseif ( ! current_user_can( 'edit_post', $editor_post_id ) ) {
				throw new \Exception( 'Access denied' );
		}
	}

	public function ajax_ai_get_image_prompt_enhancer( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_image_prompt_enhanced( $data['prompt'], [], $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_completion_text( $data ) {
		$this->verify_permissions( $data['editor_post_id'] );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_completion_text( $data['payload']['prompt'], $context, $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}


	public function ajax_ai_get_excerpt( $data ): array {
		$app = $this->get_ai_app();

		if ( empty( $data['payload']['content'] ) ) {
			throw new \Exception( 'Missing content' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'Not connected' );
		}

		$context = $this->get_request_context( $data );

		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_excerpt( $data['payload']['content'], $context, $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_featured_image( $data ): array {
		$this->verify_upload_permissions( $data );

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_featured_image( $data, $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	private function get_ai_app(): Ai {
		return Plugin::$instance->common->get_component( 'connect' )->get_app( 'ai' );
	}

	private function get_request_context( $data ) {
		if ( empty( $data['context'] ) ) {
			return [];
		}

		return $data['context'];
	}

	private function get_request_ids( $data ) {
		if ( empty( $data['requestIds'] ) ) {
			return new \stdClass();
		}

		return $data['requestIds'];
	}

	public function ajax_ai_get_edit_text( $data ) {
		$this->verify_permissions( $data['editor_post_id'] );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['input'] ) ) {
			throw new \Exception( 'Missing input' );
		}

		if ( empty( $data['payload']['instruction'] ) ) {
			throw new \Exception( 'Missing instruction' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_edit_text( $data, $context, $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_custom_code( $data ) {
		$app = $this->get_ai_app();

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( empty( $data['payload']['language'] ) ) {
			throw new \Exception( 'Missing language' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_custom_code( $data, $context, $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_custom_css( $data ) {
		$this->verify_permissions( $data['editor_post_id'] );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( empty( $data['payload']['html_markup'] ) ) {
			$data['html_markup'] = '';
		}

		if ( empty( $data['payload']['element_id'] ) ) {
			throw new \Exception( 'Missing element_id' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_custom_css( $data, $context, $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_set_get_started( $data ) {
		$app = $this->get_ai_app();

		User::set_introduction_viewed( [
			'introductionKey' => 'ai_get_started',
		] );

		return $app->set_get_started();
	}

	public function ajax_ai_set_status_feedback( $data ) {
		if ( empty( $data['response_id'] ) ) {
			throw new \Exception( 'Missing response_id' );
		}

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$app->set_status_feedback( $data['response_id'] );

		return [];
	}

	public function ajax_ai_get_text_to_image( $data ) {
		$this->verify_upload_permissions( $data );

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_text_to_image( $data, $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_image_to_image( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( empty( $data['payload']['settings'] ) ) {
			throw new \Exception( 'Missing prompt settings' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_image_to_image( [
			'prompt' => $data['payload']['prompt'],
			'promptSettings' => $data['payload']['settings'],
			'attachment_id' => $data['payload']['image']['id'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_image_to_image_upscale( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( empty( $data['payload']['promptSettings'] ) ) {
			throw new \Exception( 'Missing prompt settings' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_image_to_image_upscale( [
			'promptSettings' => $data['payload']['promptSettings'],
			'attachment_id' => $data['payload']['image']['id'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_image_to_image_replace_background( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Prompt Missing' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_image_to_image_replace_background( [
			'attachment_id' => $data['payload']['image']['id'],
			'prompt' => $data['payload']['prompt'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_image_to_image_remove_background( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );
		$result = $app->get_image_to_image_remove_background( [
			'attachment_id' => $data['payload']['image']['id'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_image_to_image_mask( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( empty( $data['payload']['settings'] ) ) {
			throw new \Exception( 'Missing prompt settings' );
		}

		if ( empty( $data['payload']['mask'] ) ) {
			throw new \Exception( 'Missing Mask' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_image_to_image_mask( [
			'prompt' => $data['payload']['prompt'],
			'attachment_id' => $data['payload']['image']['id'],
			'mask' => $data['payload']['mask'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}
	public function ajax_ai_get_image_to_image_mask_cleanup( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( empty( $data['payload']['settings'] ) ) {
			throw new \Exception( 'Missing prompt settings' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		if ( empty( $data['payload']['mask'] ) ) {
			throw new \Exception( 'Missing Mask' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_image_to_image_mask_cleanup( [
			'attachment_id' => $data['payload']['image']['id'],
			'mask' => $data['payload']['mask'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_image_to_image_outpainting( $data ) {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		if ( empty( $data['payload']['mask'] ) ) {
			throw new \Exception( 'Missing Expended Image' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );
		$result = $app->get_image_to_image_out_painting( [
			'size' => $data['payload']['size'],
			'position' => $data['payload']['position'],
			'mask' => $data['payload']['mask'],
			'image_base64' => $data['payload']['image_base64'],
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_upload_image( $data ) {
		if ( empty( $data['image'] ) ) {
			throw new \Exception( 'Missing image data' );
		}

		$image = $data['image'];

		if ( empty( $image['image_url'] ) ) {
			throw new \Exception( 'Missing image_url' );
		}

		$image_data = $this->upload_image( $image['image_url'], $data['prompt'], $data['editor_post_id'] );

		if ( is_wp_error( $image_data ) ) {
			throw new \Exception( esc_html( $image_data->get_error_message() ) );
		}

		if ( ! empty( $image['use_gallery_image'] ) && ! empty( $image['id'] ) ) {
			$app = $this->get_ai_app();
			$app->set_used_gallery_image( $image['id'] );
		}

		return [
			'image' => array_merge( $image_data, $data ),
		];
	}

	public function ajax_ai_generate_layout( $data ) {
		$this->verify_permissions( $data['editor_post_id'] );

		$app = $this->get_ai_app();

		if ( empty( $data['prompt'] ) && empty( $data['attachments'] ) ) {
			throw new \Exception( 'Missing prompt / attachments' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$result = $app->generate_layout(
			$data,
			$this->prepare_generate_layout_context( $data )
		);

		if ( is_wp_error( $result ) ) {
			$message = $result->get_error_message();

			if ( is_array( $message ) ) {
				$message = implode( ', ', $message );
				throw new \Exception( esc_html( $message ) );
			}

			$this->throw_on_error( $result );
		}

		$elements = $result['text']['elements'] ?? [];
		$base_template_id = $result['baseTemplateId'] ?? null;
		$template_type = $result['templateType'] ?? null;

		if ( empty( $elements ) || ! is_array( $elements ) ) {
			throw new \Exception( 'unknown_error' );
		}

		if ( 1 === count( $elements ) ) {
			$template = $elements[0];
		} else {
			$template = [
				'elType' => 'container',
				'elements' => $elements,
				'settings' => [
					'content_width' => 'full',
					'flex_gap' => [
						'column' => '0',
						'row' => '0',
						'unit' => 'px',
					],
					'padding' => [
						'unit' => 'px',
						'top' => '0',
						'right' => '0',
						'bottom' => '0',
						'left' => '0',
						'isLinked' => true,
					],
				],
			];
		}

		return [
			'all' => [],
			'text' => $template,
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
			'base_template_id' => $base_template_id,
			'template_type' => $template_type,
		];
	}

	public function ajax_ai_get_layout_prompt_enhancer( $data ) {
		$this->verify_permissions( $data['editor_post_id'] );

		$app = $this->get_ai_app();

		if ( empty( $data['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$result = $app->get_layout_prompt_enhanced(
			$data['prompt'],
			$data['enhance_type'],
			$this->prepare_generate_layout_context( $data )
		);

		$this->throw_on_error( $result );

		return [
			'text' => $result['text'] ?? $data['prompt'],
			'response_id' => $result['responseId'] ?? '',
			'usage' => $result['usage'] ?? '',
		];
	}

	private function prepare_generate_layout_context( $data ) {
		$request_context = $this->get_request_context( $data );
		$kit = Plugin::$instance->kits_manager->get_active_kit();

		if ( ! $kit ) {
			return $request_context;
		}

		$kits_data = Collection::make( $kit->get_data()['settings'] ?? [] );

		$colors = $kits_data
			->filter( function ( $_, $key ) {
				return in_array( $key, [ 'system_colors', 'custom_colors' ], true );
			} )
			->flatten()
			->filter( function ( $val ) {
				return ! empty( $val['_id'] );
			} )
			->map( function ( $val ) {
				return [
					'id' => $val['_id'],
					'label' => $val['title'] ?? null,
					'value' => $val['color'] ?? null,
				];
			} );

		$typography = $kits_data
			->filter( function ( $_, $key ) {
				return in_array( $key, [ 'system_typography', 'custom_typography' ], true );
			} )
			->flatten()
			->filter( function ( $val ) {
				return ! empty( $val['_id'] );
			} )
			->map( function ( $val ) {
				$font_size = null;

				if ( isset(
					$val['typography_font_size']['unit'],
					$val['typography_font_size']['size']
				) ) {
					$prop = $val['typography_font_size'];

					$font_size = 'custom' === $prop['unit']
						? $prop['size']
						: $prop['size'] . $prop['unit'];
				}

				return [
					'id' => $val['_id'],
					'label' => $val['title'] ?? null,
					'value' => [
						'family' => $val['typography_font_family'] ?? null,
						'weight' => $val['typography_font_weight'] ?? null,
						'style' => $val['typography_font_style'] ?? null,
						'size' => $font_size,
					],
				];
			} );

		$request_context['globals'] = [
			'colors' => $colors->all(),
			'typography' => $typography->all(),
		];

		return $request_context;
	}

	private function upload_image( $image_url, $image_title, $parent_post_id = 0 ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			throw new \Exception( 'Not Allowed to Upload images' );
		}

		$uploads_manager = new \Elementor\Core\Files\Uploads_Manager();
		if ( $uploads_manager::are_unfiltered_uploads_enabled() ) {
			Plugin::$instance->uploads_manager->set_elementor_upload_state( true );
			add_filter( 'wp_handle_sideload_prefilter', [ Plugin::$instance->uploads_manager, 'handle_elementor_upload' ] );
			add_filter( 'image_sideload_extensions', function( $extensions ) {
				$extensions[] = 'svg';
				return $extensions;
			});
		}

		$attachment_id = media_sideload_image( $image_url, $parent_post_id, $image_title, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return new \WP_Error( 'upload_error', $attachment_id->get_error_message() );
		}

		if ( ! empty( $attachment_id['error'] ) ) {
			return new \WP_Error( 'upload_error', $attachment_id['error'] );
		}

		return [
			'id' => $attachment_id,
			'url' => esc_url( wp_get_attachment_image_url( $attachment_id, 'full' ) ),
			'alt' => esc_attr( $image_title ),
			'source' => 'library',
		];
	}

	public function ajax_ai_get_history( $data ): array {
		$type = $data['type'] ?? self::HISTORY_TYPE_ALL;

		if ( ! in_array( $type, self::VALID_HISTORY_TYPES, true ) ) {
			throw new \Exception( 'Invalid history type' );
		}

		$page = sanitize_text_field( $data['page'] ?? 1 );
		$limit = sanitize_text_field( $data['limit'] ?? 10 );

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$result = $app->get_history_by_type( $type, $page, $limit, $context );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( esc_html( $result->get_error_message() ) );
		}

		return $result;
	}

	public function ajax_ai_delete_history_item( $data ): array {
		if ( empty( $data['id'] ) || ! wp_is_uuid( $data['id'] ) ) {
			throw new \Exception( 'Missing id parameter' );
		}

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$result = $app->delete_history_item( $data['id'], $context );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( esc_html( $result->get_error_message() ) );
		}

		return [];
	}

	public function ajax_ai_toggle_favorite_history_item( $data ): array {
		if ( empty( $data['id'] ) || ! wp_is_uuid( $data['id'] ) ) {
			throw new \Exception( 'Missing id parameter' );
		}

		$app = $this->get_ai_app();

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$result = $app->toggle_favorite_history_item( $data['id'], $context );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( esc_html( $result->get_error_message() ) );
		}

		return [];
	}

	public function ajax_ai_get_product_image_unification( $data ): array {
		if ( ! empty( $data['payload']['postId'] ) ) {
			$data['editor_post_id'] = $data['payload']['postId'];
		}
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['image'] ) || empty( $data['payload']['image']['id'] ) ) {
			throw new \Exception( 'Missing Image' );
		}

		if ( empty( $data['payload']['settings'] ) ) {
			throw new \Exception( 'Missing prompt settings' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );
		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_unify_product_images( [
			'promptSettings' => $data['payload']['settings'],
			'attachment_id' => $data['payload']['image']['id'],
			'featureIdentifier' => $data['payload']['featureIdentifier'] ?? '',
		], $context, $request_ids );

		$this->throw_on_error( $result );

		return [
			'images' => $result['images'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	public function ajax_ai_get_animation( $data ): array {
		$this->verify_upload_permissions( $data );

		$app = $this->get_ai_app();

		if ( empty( $data['payload']['prompt'] ) ) {
			throw new \Exception( 'Missing prompt' );
		}

		if ( empty( $data['payload']['motionEffectType'] ) ) {
			throw new \Exception( 'Missing animation type' );
		}

		if ( ! $app->is_connected() ) {
			throw new \Exception( 'not_connected' );
		}

		$context = $this->get_request_context( $data );

		$request_ids = $this->get_request_ids( $data['payload'] );

		$result = $app->get_animation( $data, $context, $request_ids );
		$this->throw_on_error( $result );

		return [
			'text' => $result['text'],
			'response_id' => $result['responseId'],
			'usage' => $result['usage'],
		];
	}

	/**
	 * @param mixed $result
	 */
	private function throw_on_error( $result ): void {
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [
				'message' => esc_html( $result->get_error_message() ),
				'extra_data' => $result->get_error_data(),
			] );
		}
	}

	/**
	 * @return void
	 */
	public function add_wc_scripts(): void {
		wp_enqueue_script( 'elementor-ai-unify-product-images',
			$this->get_js_assets_url( 'ai-unify-product-images' ),
			[
				'jquery',
				'elementor-v2-ui',
				'elementor-v2-icons',
				'wp-components',
				'elementor-common',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_localize_script(
			'elementor-ai-unify-product-images',
			'UnifyProductImagesConfig',
			[
				'get_product_images_url' => admin_url( 'admin-ajax.php' ),
				'set_product_images_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'elementor-ai-unify-product-images_nonce' ),
				'placeholder' => ELEMENTOR_ASSETS_URL . 'images/app/ai/product-image-unification-example.gif?' . ELEMENTOR_VERSION,
				'is_get_started' => User::get_introduction_meta( 'ai_get_started' ),
				'connect_url' => $this->get_ai_connect_url(),
			]
		);

		add_filter( 'bulk_actions-edit-product', function ( $data ) {
			return $this->add_products_bulk_action( $data );
		});

		wp_set_script_translations( 'elementor-ai-unify-product-images', 'elementor' );
	}

	/**
	 * @param $product
	 * @param int|null $image_to_remove
	 * @param int|null $image_to_add
	 * @return void
	 */
	private function update_product_gallery( $product, ?int $image_to_remove, ?int $image_to_add ): void {
		$gallery_image_ids = $product->get_gallery_image_ids();

		$index = array_search( $image_to_remove, $gallery_image_ids, true );
		if ( false !== $index ) {
			unset( $gallery_image_ids[ $index ] );
		}

		if ( ! in_array( $image_to_add, $gallery_image_ids, true ) ) {
			$gallery_image_ids[] = $image_to_add;
		}

		$product->set_gallery_image_ids( $gallery_image_ids );
		$product->save();
	}

	private function should_display_create_with_ai_banner() {
		$elementor_pages = new \WP_Query( [
			'post_type' => 'page',
			'post_status' => 'publish',
			'fields' => 'ids',
			'posts_per_page' => self::MIN_PAGES_FOR_CREATE_WITH_AI_BANNER + 1,
		] );

		if ( $elementor_pages->post_count > self::MIN_PAGES_FOR_CREATE_WITH_AI_BANNER ) {
			return false;
		}

		if ( Utils::is_custom_kit_applied() ) {
			return false;
		}

		return true;
	}

	private function get_create_with_ai_banner_data() {
		return [
			'title' => 'Create and launch your site faster with AI',
			'description' => 'Share your vision with our AI Chat and watch as it becomes a brief, sitemap, and wireframes in minutes:',
			'input_placeholder' => 'Start describing the site you want to create...',
			'button_title' => 'Create with AI',
			'button_cta_url' => 'http://planner.elementor.com/chat.html',
			'background_image' => ELEMENTOR_ASSETS_URL . 'images/app/ai/ai-site-creator-homepage-bg.svg',
			'utm_source' => 'editor-home',
			'utm_medium' => 'wp-dash',
			'utm_campaign' => 'generate-with-ai',
		];
	}

	public function add_create_with_ai_banner_to_homescreen( $home_screen_data ) {
		if ( $this->should_display_create_with_ai_banner() ) {
			$home_screen_data['create_with_ai'] = $this->get_create_with_ai_banner_data();
		} else {
			$home_screen_data['create_with_ai'] = null;
		}

		return $home_screen_data;
	}
}
