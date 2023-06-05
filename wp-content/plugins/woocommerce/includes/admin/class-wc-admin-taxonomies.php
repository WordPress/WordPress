<?php
/**
 * Handles taxonomies in admin
 *
 * @class    WC_Admin_Taxonomies
 * @version  2.3.10
 * @package  WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Automattic\WooCommerce\Internal\AssignDefaultCategory;

/**
 * WC_Admin_Taxonomies class.
 */
class WC_Admin_Taxonomies {

	/**
	 * Class instance.
	 *
	 * @var WC_Admin_Taxonomies instance
	 */
	protected static $instance = false;

	/**
	 * Default category ID.
	 *
	 * @var int
	 */
	private $default_cat_id = 0;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Default category ID.
		$this->default_cat_id = get_option( 'default_product_cat', 0 );

		// Category/term ordering.
		add_action( 'create_term', array( $this, 'create_term' ), 5, 3 );
		add_action(
			'delete_product_cat',
			function() {
				wc_get_container()->get( AssignDefaultCategory::class )->schedule_action();
			}
		);

		// Add form.
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		// Add columns.
		add_filter( 'manage_edit-product_cat_columns', array( $this, 'product_cat_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'product_cat_column' ), 10, 3 );

		// Add row actions.
		add_filter( 'product_cat_row_actions', array( $this, 'product_cat_row_actions' ), 10, 2 );
		add_filter( 'admin_init', array( $this, 'handle_product_cat_row_actions' ) );

		// Taxonomy page descriptions.
		add_action( 'product_cat_pre_add_form', array( $this, 'product_cat_description' ) );
		add_action( 'after-product_cat-table', array( $this, 'product_cat_notes' ) );

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				add_action( 'pa_' . $attribute->attribute_name . '_pre_add_form', array( $this, 'product_attribute_description' ) );
			}
		}

		// Maintain hierarchy of terms.
		add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );

		// Admin footer scripts for this product categories admin screen.
		add_action( 'admin_footer', array( $this, 'scripts_at_product_cat_screen_footer' ) );
	}

	/**
	 * Order term when created (put in position 0).
	 *
	 * @param mixed  $term_id Term ID.
	 * @param mixed  $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'product_cat' !== $taxonomy && ! taxonomy_is_product_attribute( $taxonomy ) ) {
			return;
		}

		update_term_meta( $term_id, 'order', 0 );
	}

	/**
	 * When a term is deleted, delete its meta.
	 *
	 * @deprecated 3.6.0 No longer needed.
	 * @param mixed $term_id Term ID.
	 */
	public function delete_term( $term_id ) {
		wc_deprecated_function( 'delete_term', '3.6' );
	}

	/**
	 * Category thumbnail fields.
	 */
	public function add_category_fields() {
		?>
		<div class="form-field term-display-type-wrap">
			<label for="display_type"><?php esc_html_e( 'Display type', 'woocommerce' ); ?></label>
			<select id="display_type" name="display_type" class="postform">
				<option value=""><?php esc_html_e( 'Default', 'woocommerce' ); ?></option>
				<option value="products"><?php esc_html_e( 'Products', 'woocommerce' ); ?></option>
				<option value="subcategories"><?php esc_html_e( 'Subcategories', 'woocommerce' ); ?></option>
				<option value="both"><?php esc_html_e( 'Both', 'woocommerce' ); ?></option>
			</select>
		</div>
		<div class="form-field term-thumbnail-wrap">
			<label><?php esc_html_e( 'Thumbnail', 'woocommerce' ); ?></label>
			<div id="product_cat_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
				<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
			</div>
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#product_cat_thumbnail_id' ).val() ) {
					jQuery( '.remove_image_button' ).hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
						var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

						jQuery( '#product_cat_thumbnail_id' ).val( attachment.id );
						jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
						jQuery( '.remove_image_button' ).show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.remove_image_button', function() {
					jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
					jQuery( '#product_cat_thumbnail_id' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});

				jQuery( document ).ajaxComplete( function( event, request, options ) {
					if ( request && 4 === request.readyState && 200 === request.status
						&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

						var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
						if ( ! res || res.errors ) {
							return;
						}
						// Clear Thumbnail fields on submit
						jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#product_cat_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						// Clear Display type field on submit
						jQuery( '#display_type' ).val( '' );
						return;
					}
				} );

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited.
	 */
	public function edit_category_fields( $term ) {

		$display_type = get_term_meta( $term->term_id, 'display_type', true );
		$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = wc_placeholder_img_src();
		}
		?>
		<tr class="form-field term-display-type-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Display type', 'woocommerce' ); ?></label></th>
			<td>
				<select id="display_type" name="display_type" class="postform">
					<option value="" <?php selected( '', $display_type ); ?>><?php esc_html_e( 'Default', 'woocommerce' ); ?></option>
					<option value="products" <?php selected( 'products', $display_type ); ?>><?php esc_html_e( 'Products', 'woocommerce' ); ?></option>
					<option value="subcategories" <?php selected( 'subcategories', $display_type ); ?>><?php esc_html_e( 'Subcategories', 'woocommerce' ); ?></option>
					<option value="both" <?php selected( 'both', $display_type ); ?>><?php esc_html_e( 'Both', 'woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field term-thumbnail-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail', 'woocommerce' ); ?></label></th>
			<td>
				<div id="product_cat_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
					<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
					<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
				</div>
				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ( '0' === jQuery( '#product_cat_thumbnail_id' ).val() ) {
						jQuery( '.remove_image_button' ).hide();
					}

					// Uploading files
					var file_frame;

					jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
							button: {
								text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
							var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

							jQuery( '#product_cat_thumbnail_id' ).val( attachment.id );
							jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
							jQuery( '.remove_image_button' ).show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery( document ).on( 'click', '.remove_image_button', function() {
						jQuery( '#product_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#product_cat_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save category fields
	 *
	 * @param mixed  $term_id Term ID being saved.
	 * @param mixed  $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['display_type'] ) && 'product_cat' === $taxonomy ) { // WPCS: CSRF ok, input var ok.
			update_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) ); // WPCS: CSRF ok, sanitization ok, input var ok.
		}
		if ( isset( $_POST['product_cat_thumbnail_id'] ) && 'product_cat' === $taxonomy ) { // WPCS: CSRF ok, input var ok.
			update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_cat_thumbnail_id'] ) ); // WPCS: CSRF ok, input var ok.
		}
	}

	/**
	 * Description for product_cat page to aid users.
	 */
	public function product_cat_description() {
		echo wp_kses(
			wpautop( __( 'Product categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top-right of this page.', 'woocommerce' ) ),
			array( 'p' => array() )
		);
	}

	/**
	 * Add some notes to describe the behavior of the default category.
	 */
	public function product_cat_notes() {
		$category_id   = get_option( 'default_product_cat', 0 );
		$category      = get_term( $category_id, 'product_cat' );
		$category_name = ( ! $category || is_wp_error( $category ) ) ? _x( 'Uncategorized', 'Default category slug', 'woocommerce' ) : $category->name;
		?>
		<div class="form-wrap edit-term-notes">
			<p>
				<strong><?php esc_html_e( 'Note:', 'woocommerce' ); ?></strong><br>
				<?php
					printf(
						/* translators: %s: default category */
						esc_html__( 'Deleting a category does not delete the products in that category. Instead, products that were only assigned to the deleted category are set to the category %s.', 'woocommerce' ),
						'<strong>' . esc_html( $category_name ) . '</strong>'
					);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Description for shipping class page to aid users.
	 */
	public function product_attribute_description() {
		echo wp_kses(
			wpautop( __( 'Attribute terms can be assigned to products and variations.<br/><br/><b>Note</b>: Deleting a term will remove it from all products and variations to which it has been assigned. Recreating a term will not automatically assign it back to products.', 'woocommerce' ) ),
			array( 'p' => array() )
		);
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @param mixed $columns Columns array.
	 * @return array
	 */
	public function product_cat_columns( $columns ) {
		$new_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['thumb'] = __( 'Image', 'woocommerce' );

		$columns           = array_merge( $new_columns, $columns );
		$columns['handle'] = '';

		return $columns;
	}

	/**
	 * Adjust row actions.
	 *
	 * @param array  $actions Array of actions.
	 * @param object $term Term object.
	 * @return array
	 */
	public function product_cat_row_actions( $actions, $term ) {
		$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

		if ( $default_category_id !== $term->term_id && current_user_can( 'edit_term', $term->term_id ) ) {
			$actions['make_default'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				wp_nonce_url( 'edit-tags.php?action=make_default&amp;taxonomy=product_cat&amp;post_type=product&amp;tag_ID=' . absint( $term->term_id ), 'make_default_' . absint( $term->term_id ) ),
				/* translators: %s: taxonomy term name */
				esc_attr( sprintf( __( 'Make &#8220;%s&#8221; the default category', 'woocommerce' ), $term->name ) ),
				__( 'Make default', 'woocommerce' )
			);
		}

		return $actions;
	}

	/**
	 * Handle custom row actions.
	 */
	public function handle_product_cat_row_actions() {
		if ( isset( $_GET['action'], $_GET['tag_ID'], $_GET['_wpnonce'] ) && 'make_default' === $_GET['action'] ) { // WPCS: CSRF ok, input var ok.
			$make_default_id = absint( $_GET['tag_ID'] ); // WPCS: Input var ok.

			if ( wp_verify_nonce( $_GET['_wpnonce'], 'make_default_' . $make_default_id ) && current_user_can( 'edit_term', $make_default_id ) ) { // WPCS: Sanitization ok, input var ok, CSRF ok.
				update_option( 'default_product_cat', $make_default_id );
			}
		}
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param string $columns Column HTML output.
	 * @param string $column Column name.
	 * @param int    $id Product ID.
	 *
	 * @return string
	 */
	public function product_cat_column( $columns, $column, $id ) {
		if ( 'thumb' === $column ) {
			// Prepend tooltip for default category.
			$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

			if ( $default_category_id === $id ) {
				$columns .= wc_help_tip( __( 'This is the default category and it cannot be deleted. It will be automatically assigned to products with no category.', 'woocommerce' ) );
			}

			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wc_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605 .
			$image    = str_replace( ' ', '%20', $image );
			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'woocommerce' ) . '" class="wp-post-image" height="48" width="48" />';
		}
		if ( 'handle' === $column ) {
			$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
		}
		return $columns;
	}

	/**
	 * Maintain term hierarchy when editing a product.
	 *
	 * @param  array $args Term checklist args.
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {
		if ( ! empty( $args['taxonomy'] ) && 'product_cat' === $args['taxonomy'] ) {
			$args['checked_ontop'] = false;
		}
		return $args;
	}

	/**
	 * Admin footer scripts for the product categories admin screen
	 *
	 * @return void
	 */
	public function scripts_at_product_cat_screen_footer() {
		if ( ! isset( $_GET['taxonomy'] ) || 'product_cat' !== $_GET['taxonomy'] ) { // WPCS: CSRF ok, input var ok.
			return;
		}
		// Ensure the tooltip is displayed when the image column is disabled on product categories.
		wc_enqueue_js(
			"(function( $ ) {
				'use strict';
				var product_cat = $( 'tr#tag-" . absint( $this->default_cat_id ) . "' );
				product_cat.find( 'th' ).empty();
				product_cat.find( 'td.thumb span' ).detach( 'span' ).appendTo( product_cat.find( 'th' ) );
			})( jQuery );"
		);
	}
}

$wc_admin_taxonomies = WC_Admin_Taxonomies::get_instance();
