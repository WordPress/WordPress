<?php
/**
 * WooCommerce Import Tracking
 *
 * @package WooCommerce\Tracks
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Products.
 */
class WC_Products_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'load-edit.php', array( $this, 'track_products_view' ), 10 );
		add_action( 'load-edit-tags.php', array( $this, 'track_categories_and_tags_view' ), 10, 2 );
		add_action( 'edit_post', array( $this, 'track_product_updated' ), 10, 2 );
		add_action( 'wp_after_insert_post', array( $this, 'track_product_published' ), 10, 4 );
		add_action( 'created_product_cat', array( $this, 'track_product_category_created' ) );
		add_action( 'edited_product_cat', array( $this, 'track_product_category_updated' ) );
		add_action( 'add_meta_boxes_product', array( $this, 'track_product_updated_client_side' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_product_tracking_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_product_import_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_attribute_tracking_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_tag_tracking_scripts' ) );
	}

	/**
	 * Send a Tracks event when the Products page is viewed.
	 */
	public function track_products_view() {
		// We only record Tracks event when no `_wp_http_referer` query arg is set, since
		// when searching, the request gets sent from the browser twice,
		// once with the `_wp_http_referer` and once without it.
		//
		// Otherwise, we would double-record the view and search events.

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
		if (
			isset( $_GET['post_type'] )
			&& 'product' === wp_unslash( $_GET['post_type'] )
			&& ! isset( $_GET['_wp_http_referer'] )
		) {
			// phpcs:enable

			WC_Tracks::record_event( 'products_view' );

			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
			if (
				isset( $_GET['s'] )
				&& 0 < strlen( sanitize_text_field( wp_unslash( $_GET['s'] ) ) )
			) {
				// phpcs:enable

				WC_Tracks::record_event( 'products_search' );
			}
		}
	}

	/**
	 * Send a Tracks event when the Products Categories and Tags page is viewed.
	 */
	public function track_categories_and_tags_view() {
		// We only record Tracks event when no `_wp_http_referer` query arg is set, since
		// when searching, the request gets sent from the browser twice,
		// once with the `_wp_http_referer` and once without it.
		//
		// Otherwise, we would double-record the view and search events.

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
		if (
			isset( $_GET['post_type'] )
			&& 'product' === wp_unslash( $_GET['post_type'] )
			&& isset( $_GET['taxonomy'] )
			&& ! isset( $_GET['_wp_http_referer'] )
		) {
			$taxonomy = wp_unslash( $_GET['taxonomy'] );
			// phpcs:enable

			if ( 'product_cat' === $taxonomy ) {
				WC_Tracks::record_event( 'categories_view' );
			} elseif ( 'product_tag' === $taxonomy ) {
				WC_Tracks::record_event( 'tags_view' );
			}

			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
			if (
				isset( $_GET['s'] )
				&& 0 < strlen( sanitize_text_field( wp_unslash( $_GET['s'] ) ) )
			) {
				// phpcs:enable

				if ( 'product_cat' === $taxonomy ) {
					WC_Tracks::record_event( 'categories_search' );
				} elseif ( 'product_tag' === $taxonomy ) {
					WC_Tracks::record_event( 'tags_search' );
				}
			}
		}
	}

	/**
	 * Send a Tracks event when a product is updated.
	 *
	 * @param int    $product_id Product id.
	 * @param object $post       WordPress post.
	 */
	public function track_product_updated( $product_id, $post ) {
		if ( 'product' !== $post->post_type ) {
			return;
		}

		$properties = array(
			'product_id' => $product_id,
		);

		WC_Tracks::record_event( 'product_edit', $properties );
	}

	/**
	 * Track the Update button being clicked on the client side.
	 * This is needed because `track_product_updated` (using the `edit_post`
	 * hook) is called in response to a number of other triggers.
	 *
	 * @param WP_Post $post The post, not used.
	 */
	public function track_product_updated_client_side( $post ) {
		wc_enqueue_js(
			"
			if ( $( 'h1.wp-heading-inline' ).text().trim() === '" . __( 'Edit product', 'woocommerce' ) . "') {
				var initialStockValue = $( '#_stock' ).val();
				var isBlockEditor = false;
				var child_element = '#publish';

				if ( $( '.block-editor' ).length !== 0 && $( '.block-editor' )[0] ) {
	    			isBlockEditor = true;
				}

				if ( isBlockEditor ) {
					child_element = '.editor-post-publish-button';
				}

				$( '#wpwrap' ).on( 'click', child_element, function() {
					var description_value  = '';
					var tagsText = '';
					var currentStockValue = $( '#_stock' ).val();

					function getProductTypeOptions() {
						const productTypeOptionsCheckboxes = $( 'input[type=\"checkbox\"][data-product-type-option-id]' );
						const productTypeOptions = productTypeOptionsCheckboxes.map( function() {
							return {
								id: $( this ).data( 'product-type-option-id' ),
								isEnabled: $( this ).is( ':checked' ),
							};
						} ).get();
						return productTypeOptions;
					}

					function getProductTypeOptionsString( productTypeOptions ) {
						return productTypeOptions
							.filter( productTypeOption => productTypeOption.isEnabled )
							.map( productTypeOption => productTypeOption.id )
							.join( ', ' );
					}

					const productTypeOptions = getProductTypeOptions();
					const productTypeOptionsString = getProductTypeOptionsString( productTypeOptions );

					if ( ! isBlockEditor ) {
						tagsText          = $( '[name=\"tax_input[product_tag]\"]' ).val();
						if ( $( '#content' ).is( ':visible' ) ) {
							description_value = $( '#content' ).val();
						} else if ( typeof tinymce === 'object' && tinymce.get( 'content' ) ) {
							description_value = tinymce.get( 'content' ).getContent();
						}
					} else {
						description_value  = $( '.block-editor-rich-text__editable' ).text();
					}

					// We can't just check the number of '.woocommerce_attribute' elements because
					// there might be empty ones, which get stripped out when saved. So, we'll check
					// whether the name and values have been filled out.
					var numberOfAttributes = $( '.woocommerce_attribute' ).filter( function () {
						var attributeElement = $( this );
						var attributeName = attributeElement.find( 'input.attribute_name' ).val();
						var attributeValues = attributeElement.find( 'textarea[name^=\"attribute_values\"]' ).val();

						return attributeName !== '' && attributeValues !== '';
					} ).length;

					var properties = {
						attributes:				     numberOfAttributes,
						categories:				     $( '[name=\"tax_input[product_cat][]\"]:checked' ).length,
						cross_sells:			     $( '#crosssell_ids option' ).length ? 'Yes' : 'No',
						description:			     description_value.trim() !== '' ? 'Yes' : 'No',
						enable_reviews:			     $( '#comment_status' ).is( ':checked' ) ? 'Yes' : 'No',
						is_virtual:				     $( '#_virtual' ).is( ':checked' ) ? 'Yes' : 'No',
						is_block_editor:		     isBlockEditor,
						is_downloadable:		     $( '#_downloadable' ).is( ':checked' ) ? 'Yes' : 'No',
						manage_stock:			     $( '#_manage_stock' ).is( ':checked' ) ? 'Yes' : 'No',
						menu_order:				     parseInt( $( '#menu_order' ).val(), 10 ) !== 0 ? 'Yes' : 'No',
						product_gallery:		     $( '#product_images_container .product_images > li' ).length,
						product_image:			     $( '#_thumbnail_id' ).val() > 0 ? 'Yes' : 'No',
						product_type:			     $( '#product-type' ).val(),
						product_type_options_string: productTypeOptionsString,
						purchase_note:			     $( '#_purchase_note' ).val().length ? 'yes' : 'no',
						sale_price:				     $( '#_sale_price' ).val() ? 'yes' : 'no',
						short_description:		     $( '#excerpt' ).val().length ? 'yes' : 'no',
						stock_quantity_update:	     ( initialStockValue != currentStockValue ) ? 'Yes' : 'No',
						tags:					     tagsText.length > 0 ? tagsText.split( ',' ).length : 0,
						upsells:				     $( '#upsell_ids option' ).length ? 'Yes' : 'No',
						weight:					     $( '#_weight' ).val() ? 'Yes' : 'No',
					};
					window.wcTracks.recordEvent( 'product_update', properties );
				} );
			}
			"
		);
	}

	/**
	 * Get the IDs of the possible product type options.
	 *
	 * @return array
	 */
	private static function get_possible_product_type_options_ids() {
		$product_type_options_ids = array_merge(
			array_values(
				array_map(
					function ( $product_type_option ) {
						return $product_type_option['id'];
					},
					/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
					apply_filters( 'product_type_options', array() )
				)
			),
			array( '_downloadable', '_virtual' )
		);

		return $product_type_options_ids;
	}

	/**
	 * Get the product type options for a product.
	 *
	 * @param int $post_id The ID of the product.
	 *
	 * @return array
	 */
	private static function get_product_type_options( $post_id ) {
		$possible_product_type_options_ids = self::get_possible_product_type_options_ids();
		$post_meta                         = get_post_meta( $post_id );
		$product_type_options              = array();

		foreach ( $possible_product_type_options_ids as $product_type_option_id ) {
			$product_type_options[ $product_type_option_id ] = isset( $post_meta[ $product_type_option_id ] ) ? $post_meta[ $product_type_option_id ][0] : 'no';
		}

		return $product_type_options;
	}

	/**
	 * Get a comma-separated string of the product type options that are enabled.
	 *
	 * @param array $product_type_options The product type options.
	 *
	 * @return string
	 */
	private static function get_product_type_options_string( $product_type_options ) {
		return implode(
			', ',
			array_keys(
				array_filter(
					$product_type_options,
					function ( $is_enabled ) {
						return 'yes' === $is_enabled;
					}
				)
			)
		);
	}

	/**
	 * Send a Tracks event when a product is published.
	 *
	 * @param int          $post_id     Post ID.
	 * @param WP_Post      $post        Post object.
	 * @param bool         $update      Whether this is an existing post being updated.
	 * @param null|WP_Post $post_before Null for new posts, the WP_Post object prior
	 *                                  to the update for updated posts.
	 */
	public function track_product_published( $post_id, $post, $update, $post_before ) {
		if (
			'product' !== $post->post_type ||
			'publish' !== $post->post_status ||
			( $post_before && 'publish' === $post_before->post_status )
		) {
			return;
		}

		$product = wc_get_product( $post_id );

		$product_type_options        = self::get_product_type_options( $post_id );
		$product_type_options_string = self::get_product_type_options_string( $product_type_options );

		$properties = array(
			'attributes'           => count( $product->get_attributes() ),
			'categories'           => count( $product->get_category_ids() ),
			'cross_sells'          => ! empty( $product->get_cross_sell_ids() ) ? 'yes' : 'no',
			'description'          => $product->get_description() ? 'yes' : 'no',
			'dimensions'           => wc_format_dimensions( $product->get_dimensions( false ) ) !== 'N/A' ? 'yes' : 'no',
			'enable_reviews'       => $product->get_reviews_allowed() ? 'yes' : 'no',
			'is_downloadable'      => $product->is_downloadable() ? 'yes' : 'no',
			'is_virtual'           => $product->is_virtual() ? 'yes' : 'no',
			'manage_stock'         => $product->get_manage_stock() ? 'yes' : 'no',
			'menu_order'           => $product->get_menu_order() ? 'yes' : 'no',
			'product_id'           => $post_id,
			'product_gallery'      => count( $product->get_gallery_image_ids() ),
			'product_image'        => $product->get_image_id() ? 'yes' : 'no',
			'product_type'         => $product->get_type(),
			'product_type_options' => $product_type_options_string,
			'purchase_note'        => $product->get_purchase_note() ? 'yes' : 'no',
			'sale_price'           => $product->get_sale_price() ? 'yes' : 'no',
			'short_description'    => $product->get_short_description() ? 'yes' : 'no',
			'tags'                 => count( $product->get_tag_ids() ),
			'upsells'              => ! empty( $product->get_upsell_ids() ) ? 'yes' : 'no',
			'weight'               => $product->get_weight() ? 'yes' : 'no',
		);

		WC_Tracks::record_event( 'product_add_publish', $properties );
	}

	/**
	 * Send a Tracks event when a product category is created.
	 *
	 * @param int $category_id Category ID.
	 */
	public function track_product_category_created( $category_id ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		// Only track category creation from the edit product screen or the
		// category management screen (which both occur via AJAX).
		if (
			! Constants::is_defined( 'DOING_AJAX' ) ||
			empty( $_POST['action'] ) ||
			(
				// Product Categories screen.
				'add-tag' !== $_POST['action'] &&
				// Edit Product screen.
				'add-product_cat' !== $_POST['action']
			)
		) {
			return;
		}

		$category        = get_term( $category_id, 'product_cat' );
		$parent_category = $category->parent > 0 ? 'Other' : 'None';
		if ( $category->parent > 0 ) {
			$parent = get_term( $category_id, 'product_cat' );
			if ( 'uncategorized' === $parent->name ) {
				$parent_category = 'Uncategorized';
			}
		}
		$properties = array(
			'category_id'     => $category_id,
			'parent_id'       => $category->parent,
			'parent_category' => $parent_category,
			'page'            => ( 'add-tag' === $_POST['action'] ) ? 'categories' : 'product',
			'display_type'    => isset( $_POST['display_type'] ) ? wp_unslash( $_POST['display_type'] ) : '',
			'image'           => isset( $_POST['product_cat_thumbnail_id'] ) && '' !== $_POST['product_cat_thumbnail_id'] ? 'Yes' : 'No',
		);
		// phpcs:enable

		WC_Tracks::record_event( 'product_category_add', $properties );
	}

	/**
	 * Send a Tracks event when a product category is updated.
	 *
	 * @param int $category_id Category ID.
	 */
	public function track_product_category_updated( $category_id ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// Only track category creation from the edit product screen or the
		// category management screen (which both occur via AJAX).
		if (
			empty( $_POST['action'] ) ||
			( 'editedtag' !== $_POST['action'] && 'inline-save-tax' !== $_POST['action'] )
		) {
			return;
		}
		// phpcs:enable

		WC_Tracks::record_event( 'product_category_update' );
	}

	/**
	 * Adds the tracking scripts for product filtering actions.
	 *
	 * @param string $hook Hook of the current page.
	 * @return string|boolean
	 */
	protected function get_product_screen( $hook ) {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
		if (
			'edit.php' === $hook &&
			isset( $_GET['post_type'] ) &&
			'product' === wp_unslash( $_GET['post_type'] )
		) {
			return 'list';
		}

		if (
			'post-new.php' === $hook &&
			isset( $_GET['post_type'] ) &&
			'product' === wp_unslash( $_GET['post_type'] )
		) {
			return 'new';
		}

		if (
			'post.php' === $hook &&
			isset( $_GET['post'] ) &&
			'product' === get_post_type( intval( $_GET['post'] ) )
		) {
			return 'edit';
		}

		if ( 'product_page_product_importer' === $hook ) {
			return 'import';
		}

		// phpcs:enable

		return false;
	}

	/**
	 * Adds the tracking scripts for product filtering actions.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_product_tracking_scripts( $hook ) {
		$product_screen = $this->get_product_screen( $hook );
		if ( ! $product_screen ) {
			return;
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'product-tracking', false );
		wp_localize_script(
			'wc-admin-product-tracking',
			'productScreen',
			array(
				'name' => $product_screen,
			)
		);
	}

	/**
	 * Adds the tracking scripts for product setting pages.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_product_import_scripts( $hook ) {
		$product_screen = $this->get_product_screen( $hook );

		if ( 'import' !== $product_screen ) {
			return;
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'product-import-tracking', false );

	}

	/**
	 * Adds the tracking scripts for product attributes filtering actions.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_attribute_tracking_scripts( $hook ) {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
		if (
			'product_page_product_attributes' !== $hook ||
			! isset( $_GET['page'] ) ||
			'product_attributes' !== wp_unslash( $_GET['page'] )
		) {
			return;
		}
		// phpcs:enable

		WCAdminAssets::register_script( 'wp-admin-scripts', 'attributes-tracking', false );
	}

	/**
	 * Adds the tracking scripts for tags and categories filtering actions.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_tag_tracking_scripts( $hook ) {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
		if (
			'edit-tags.php' !== $hook ||
			! isset( $_GET['post_type'] ) ||
			'product' !== wp_unslash( $_GET['post_type'] )
		) {
			return;
		}
		// phpcs:enable

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if (
			isset( $_GET['taxonomy'] ) &&
			'product_tag' === wp_unslash( $_GET['taxonomy'] )
		) {
			WCAdminAssets::register_script( 'wp-admin-scripts', 'tags-tracking', false );
			return;
		}

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if (
			isset( $_GET['taxonomy'] ) &&
			'product_cat' === wp_unslash( $_GET['taxonomy'] )
		) {
			WCAdminAssets::register_script( 'wp-admin-scripts', 'category-tracking', false );
			return;
		}
		WCAdminAssets::register_script( 'wp-admin-scripts', 'add-term-tracking', false );
	}
}
