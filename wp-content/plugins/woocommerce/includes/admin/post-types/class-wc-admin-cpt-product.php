<?php
/**
 * Admin functions for the products post type
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Post Types
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Admin_CPT' ) ) {
	include( 'class-wc-admin-cpt.php' );
}

if ( ! class_exists( 'WC_Admin_CPT_Product' ) ) :

/**
 * WC_Admin_CPT_Product Class
 */
class WC_Admin_CPT_Product extends WC_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->type = 'product';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

		// Featured image text
		add_filter( 'gettext', array( $this, 'featured_image_gettext' ) );
		add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );

		// Visibility option
		add_action( 'post_submitbox_misc_actions', array( $this, 'product_data_visibility' ) );

		// Before data updates
		add_action( 'pre_post_update', array( $this, 'pre_post_update' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ) );

		// Admin Columns
		add_filter( 'manage_edit-product_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_product_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'manage_edit-product_sortable_columns', array( $this, 'custom_columns_sort' ) );
		add_filter( 'request', array( $this, 'custom_columns_orderby' ) );

		// Sort link
		add_filter( 'views_edit-product', array( $this, 'default_sorting_link' ) );

		// Prouct filtering
		add_action( 'restrict_manage_posts', array( $this, 'product_filters' ) );
		add_filter( 'parse_query', array( $this, 'product_filters_query' ) );

		// Enhanced search
		add_filter( 'posts_search', array( $this, 'product_search' ) );

		// Maintain hierarchy of terms
		add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );

		// Bulk / quick edit
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'save_post', array( $this, 'bulk_and_quick_edit_save_post' ), 10, 2 );

		// Uploads
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
		add_action( 'media_upload_downloadable_product', array( $this, 'media_upload_downloadable_product' ) );
		add_filter( 'mod_rewrite_rules', array( $this, 'ms_protect_download_rewite_rules' ) );

		// Download permissions
		add_action( 'woocommerce_process_product_file_download_paths', array( $this, 'process_product_file_download_paths' ), 10, 3 );

		// Call WC_Admin_CPT constructor
		parent::__construct();
	}

	/**
	 * Check if we're editing or adding a product
	 * @return boolean
	 */
	private function is_editing_product() {
		if ( ! empty( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) {
			return true;
		}
		if ( ! empty( $_GET['post'] ) && 'product' == get_post_type( $_GET['post'] ) ) {
			return true;
		}
		if ( ! empty( $_REQUEST['post_id'] ) && 'product' == get_post_type( $_REQUEST['post_id'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Replace 'Featured' when editing a product. Adapted from https://gist.github.com/tw2113/c7fd8da782232ce90176
	 * @param  string $string string being translated
	 * @return string after manipulation
	 */
	public function featured_image_gettext( $string = '' ) {
		if ( 'Featured Image' == $string && $this->is_editing_product() ) {
			$string = __( 'Product Image', 'woocommerce' );
		} elseif ( 'Remove featured image' == $string && $this->is_editing_product() ) {
			$string = __( 'Remove product image', 'woocommerce' );
		} elseif ( 'Set featured image' == $string && $this->is_editing_product() ) {
			$string = __( 'Set product image', 'woocommerce' );
		}
		return $string;
	}

	/**
	 * Change "Featured Image" to "Product Image" throughout media modals.
	 * @param  array  $strings Array of strings to translate.
	 * @param  object $post
	 * @return array
	 */
	public function media_view_strings( $strings = array(), $post = null ) {
		if ( is_object( $post ) ) {
			if ( 'product' == $post->post_type ) {
				$strings['setFeaturedImageTitle'] = __( 'Set product image', 'woocommerce' );
				$strings['setFeaturedImage']      = __( 'Set product image', 'woocommerce' );
			}
		}
		return $strings;
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'product' ) {
			return __( 'Product name', 'woocommerce' );
		}

		return $text;
	}

	/**
	 * Output product visibility options.
	 *
	 * @access public
	 * @return void
	 */
	public function product_data_visibility() {
		global $post;

		if ( 'product' != $post->post_type ) {
			return;
		}

		$current_visibility = ( $current_visibility = get_post_meta( $post->ID, '_visibility', true ) ) ? $current_visibility : 'visible';
		$current_featured 	= ( $current_featured = get_post_meta( $post->ID, '_featured', true ) ) ? $current_featured : 'no';

		$visibility_options = apply_filters( 'woocommerce_product_visibility_options', array(
			'visible' => __( 'Catalog/search', 'woocommerce' ),
			'catalog' => __( 'Catalog', 'woocommerce' ),
			'search'  => __( 'Search', 'woocommerce' ),
			'hidden'  => __( 'Hidden', 'woocommerce' )
		) );
		?>
		<div class="misc-pub-section" id="catalog-visibility">
			<?php _e( 'Catalog visibility:', 'woocommerce' ); ?> <strong id="catalog-visibility-display"><?php
				echo isset( $visibility_options[ $current_visibility ]  ) ? esc_html( $visibility_options[ $current_visibility ] ) : esc_html( $current_visibility );

				if ( 'yes' == $current_featured ) {
					echo ', ' . __( 'Featured', 'woocommerce' );
				}
			?></strong>

			<a href="#catalog-visibility" class="edit-catalog-visibility hide-if-no-js"><?php _e( 'Edit', 'woocommerce' ); ?></a>

			<div id="catalog-visibility-select" class="hide-if-js">

				<input type="hidden" name="current_visibility" id="current_visibility" value="<?php echo esc_attr( $current_visibility ); ?>" />
				<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />

				<?php
					echo '<p>' . __( 'Define the loops this product should be visible in. The product will still be accessible directly.', 'woocommerce' ) . '</p>';

					foreach ( $visibility_options as $name => $label ) {
						echo '<input type="radio" name="_visibility" id="_visibility_' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '" ' . checked( $current_visibility, $name, false ) . ' data-label="' . esc_attr( $label ) . '" /> <label for="_visibility_' . esc_attr( $name ) . '" class="selectit">' . esc_html( $label ) . '</label><br />';
					}

					echo '<p>' . __( 'Enable this option to feature this product.', 'woocommerce' ) . '</p>';

					echo '<input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' /> <label for="_featured">' . __( 'Featured Product', 'woocommerce' ) . '</label><br />';
				?>
				<p>
					<a href="#catalog-visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK', 'woocommerce' ); ?></a>
					<a href="#catalog-visibility" class="cancel-post-visibility hide-if-no-js"><?php _e( 'Cancel', 'woocommerce' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Some functions, like the term recount, require the visibility to be set prior. Lets save that here.
	 *
	 * @param int $post_id
	 */
	public function pre_post_update( $post_id ) {
		if ( isset( $_POST['_visibility'] ) ) {
			update_post_meta( $post_id, '_visibility', stripslashes( $_POST['_visibility'] ) );
		}

		if ( isset( $_POST['_stock_status'] ) ) {
			wc_update_product_stock_status( $post_id, wc_clean( $_POST['_stock_status'] ) );
		}
	}

	/**
	 * Forces certain product data based on the product's type, e.g. grouped products cannot have a parent.
	 *
	 * @param array $data
	 * @return array
	 */
	public function wp_insert_post_data( $data ) {
		global $post;

		if ( 'product' == $data['post_type'] && isset( $_POST['product-type'] ) ) {
			$product_type = stripslashes( $_POST['product-type'] );
			switch ( $product_type ) {
				case 'grouped' :
				case 'variable' :
					$data['post_parent'] = 0;
					break;

				default :
					break;
			}
		}

		return $data;
	}

	/**
	 * Change the columns shown in admin.
	 */
	public function edit_columns( $existing_columns ) {

		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns = array();
		$columns['cb'] = '<input type="checkbox" />';
		$columns['thumb'] = '<span class="wc-image tips" data-tip="' . __( 'Image', 'woocommerce' ) . '">' . __( 'Image', 'woocommerce' ) . '</span>';

		$columns['name'] = __( 'Name', 'woocommerce' );

		if ( wc_product_sku_enabled() ) {
			$columns['sku'] = __( 'SKU', 'woocommerce' );
		}

		if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {
			$columns['is_in_stock'] = __( 'Stock', 'woocommerce' );
		}

		$columns['price'] = __( 'Price', 'woocommerce' );

		$columns['product_cat'] = __( 'Categories', 'woocommerce' );
		$columns['product_tag'] = __( 'Tags', 'woocommerce' );
		$columns['featured'] = '<span class="wc-featured tips" data-tip="' . __( 'Featured', 'woocommerce' ) . '">' . __( 'Featured', 'woocommerce' ) . '</span>';
		$columns['product_type'] = '<span class="wc-type tips" data-tip="' . __( 'Type', 'woocommerce' ) . '">' . __( 'Type', 'woocommerce' ) . '</span>';
		$columns['date'] = __( 'Date', 'woocommerce' );

		return array_merge( $columns, $existing_columns );
	}

	/**
	 * Define our custom columns shown in admin.
	 * @param  string $column
	 */
	public function custom_columns( $column ) {
		global $post, $woocommerce, $the_product;

		if ( empty( $the_product ) || $the_product->id != $post->ID ) {
			$the_product = get_product( $post );
		}

		switch ( $column ) {
			case 'thumb' :
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $the_product->get_image() . '</a>';
				break;
			case 'name' :
				$edit_link        = get_edit_post_link( $post->ID );
				$title            = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );
				$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );

				echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) .'">' . $title.'</a>';

				_post_states( $post );

				echo '</strong>';

				if ( $post->post_parent > 0 ) {
					echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link( $post->post_parent ) .'">'. get_the_title( $post->post_parent ) .'</a>';
				}

				// Excerpt view
				if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
					echo apply_filters( 'the_excerpt', $post->post_excerpt );
				}

				// Get actions
				$actions = array();

				$actions['id'] = 'ID: ' . $post->ID;

				if ( $can_edit_post && 'trash' != $post->post_status ) {
					$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item', 'woocommerce' ) ) . '">' . __( 'Edit', 'woocommerce' ) . '</a>';
					$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline', 'woocommerce' ) ) . '">' . __( 'Quick&nbsp;Edit', 'woocommerce' ) . '</a>';
				}
				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
					if ( 'trash' == $post->post_status ) {
						$actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', 'woocommerce' ) ) . '" href="' . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . '">' . __( 'Restore', 'woocommerce' ) . '</a>';
					} elseif ( EMPTY_TRASH_DAYS ) {
						$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this item to the Trash', 'woocommerce' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'woocommerce' ) . '</a>';
					}

					if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS ) {
						$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently', 'woocommerce' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'woocommerce' ) . '</a>';
					}
				}
				if ( $post_type_object->public ) {
					if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
						if ( $can_edit_post )
							$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'woocommerce' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'woocommerce' ) . '</a>';
					} elseif ( 'trash' != $post->post_status ) {
						$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'woocommerce' ), $title ) ) . '" rel="permalink">' . __( 'View', 'woocommerce' ) . '</a>';
					}
				}

				$actions = apply_filters( 'post_row_actions', $actions, $post );

				echo '<div class="row-actions">';

				$i = 0;
				$action_count = sizeof($actions);

				foreach ( $actions as $action => $link ) {
					++$i;
					( $i == $action_count ) ? $sep = '' : $sep = ' | ';
					echo '<span class="' . $action . '">' . $link . $sep . '</span>';
				}
				echo '</div>';

				get_inline_data( $post );

				/* Custom inline data for woocommerce */
				echo '
					<div class="hidden" id="woocommerce_inline_' . $post->ID . '">
						<div class="menu_order">' . $post->menu_order . '</div>
						<div class="sku">' . $the_product->sku . '</div>
						<div class="regular_price">' . $the_product->regular_price . '</div>
						<div class="sale_price">' . $the_product->sale_price . '</div>
						<div class="weight">' . $the_product->weight . '</div>
						<div class="length">' . $the_product->length . '</div>
						<div class="width">' . $the_product->width . '</div>
						<div class="height">' . $the_product->height . '</div>
						<div class="visibility">' . $the_product->visibility . '</div>
						<div class="stock_status">' . $the_product->stock_status . '</div>
						<div class="stock">' . $the_product->stock . '</div>
						<div class="manage_stock">' . $the_product->manage_stock . '</div>
						<div class="featured">' . $the_product->featured . '</div>
						<div class="product_type">' . $the_product->product_type . '</div>
						<div class="product_is_virtual">' . $the_product->virtual . '</div>
						<div class="tax_status">' . $the_product->tax_status . '</div>
						<div class="tax_class">' . $the_product->tax_class . '</div>
						<div class="backorders">' . $the_product->backorders . '</div>
					</div>
				';

			break;
			case 'sku' :
				echo $the_product->get_sku() ? $the_product->get_sku() : '<span class="na">&ndash;</span>';
				break;
			case 'product_type' :
				if ( 'grouped' == $the_product->product_type ) {
					echo '<span class="product-type tips grouped" data-tip="' . __( 'Grouped', 'woocommerce' ) . '"></span>';
				} elseif ( 'external' == $the_product->product_type ) {
					echo '<span class="product-type tips external" data-tip="' . __( 'External/Affiliate', 'woocommerce' ) . '"></span>';
				} elseif ( 'simple' == $the_product->product_type ) {

					if ( $the_product->is_virtual() ) {
						echo '<span class="product-type tips virtual" data-tip="' . __( 'Virtual', 'woocommerce' ) . '"></span>';
					} elseif ( $the_product->is_downloadable() ) {
						echo '<span class="product-type tips downloadable" data-tip="' . __( 'Downloadable', 'woocommerce' ) . '"></span>';
					} else {
						echo '<span class="product-type tips simple" data-tip="' . __( 'Simple', 'woocommerce' ) . '"></span>';
					}

				} elseif ( 'variable' == $the_product->product_type ) {
					echo '<span class="product-type tips variable" data-tip="' . __( 'Variable', 'woocommerce' ) . '"></span>';
				} else {
					// Assuming that we have other types in future
					echo '<span class="product-type tips ' . $the_product->product_type . '" data-tip="' . ucfirst( $the_product->product_type ) . '"></span>';
				}
				break;
			case 'price' :
				echo $the_product->get_price_html() ? $the_product->get_price_html() : '<span class="na">&ndash;</span>';
				break;
			case 'product_cat' :
			case 'product_tag' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=product' ) . ' ">' . $term->name . '</a>';
					}

					echo implode( ', ', $termlist );
				}
				break;
			case 'featured' :
				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_feature_product&product_id=' . $post->ID ), 'woocommerce-feature-product' );
				echo '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle featured', 'woocommerce' ) . '">';
				if ( $the_product->is_featured() ) {
					echo '<span class="wc-featured tips" data-tip="' . __( 'Yes', 'woocommerce' ) . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
				} else {
					echo '<span class="wc-featured not-featured tips" data-tip="' . __( 'No', 'woocommerce' ) . '">' . __( 'No', 'woocommerce' ) . '</span>';
				}
				echo '</a>';
				break;
			case 'is_in_stock' :

				if ( $the_product->is_in_stock() ) {
					echo '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
				} else {
					echo '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
				}

				if ( $the_product->managing_stock() ) {
					echo ' &times; ' . $the_product->get_total_stock();
				}

				break;

			default :
				break;
		}
	}

	/**
	 * Make product columns sortable
	 *
	 * https://gist.github.com/906872
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function custom_columns_sort( $columns ) {
		$custom = array(
			'price'			=> 'price',
			'featured'		=> 'featured',
			'sku'			=> 'sku',
			'name'			=> 'title'
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Product column orderby
	 *
	 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
	 *
	 * @access public
	 * @param mixed $vars
	 * @return array
	 */
	public function custom_columns_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) ) {
			if ( 'price' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' 	=> '_price',
					'orderby' 	=> 'meta_value_num'
				) );
			}
			if ( 'featured' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' 	=> '_featured',
					'orderby' 	=> 'meta_value'
				) );
			}
			if ( 'sku' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' 	=> '_sku',
					'orderby' 	=> 'meta_value'
				) );
			}
		}

		return $vars;
	}

	/**
	 * Product sorting link
	 *
	 * Based on Simple Page Ordering by 10up (http://wordpress.org/extend/plugins/simple-page-ordering/)
	 *
	 * @param array $views
	 * @return array
	 */
	public function default_sorting_link( $views ) {
		global $post_type, $wp_query;

		if ( ! current_user_can('edit_others_pages') ) {
			return $views;
		}

		$class            = ( isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'menu_order title' ) ? 'current' : '';
		$query_string     = remove_query_arg(array( 'orderby', 'order' ));
		$query_string     = add_query_arg( 'orderby', urlencode('menu_order title'), $query_string );
		$query_string     = add_query_arg( 'order', urlencode('ASC'), $query_string );
		$views['byorder'] = '<a href="'. $query_string . '" class="' . esc_attr( $class ) . '">' . __( 'Sort Products', 'woocommerce' ) . '</a>';

		return $views;
	}

	/**
	 * Show a category filter box
	 */
	public function product_filters() {
		global $typenow, $wp_query;

		if ( 'product' != $typenow ) {
			return;
		}

		// Category Filtering
		wc_product_dropdown_categories();

		// Type filtering
		$terms   = get_terms( 'product_type' );
		$output  = '<select name="product_type" id="dropdown_product_type">';
		$output .= '<option value="">' . __( 'Show all product types', 'woocommerce' ) . '</option>';

		foreach ( $terms as $term ) {
			$output .= '<option value="' . sanitize_title( $term->name ) . '" ';

			if ( isset( $wp_query->query['product_type'] ) ) {
				$output .= selected( $term->slug, $wp_query->query['product_type'], false );
			}

			$output .= '>';

			switch ( $term->name ) {
				case 'grouped' :
					$output .= __( 'Grouped product', 'woocommerce' );
					break;
				case 'external' :
					$output .= __( 'External/Affiliate product', 'woocommerce' );
					break;
				case 'variable' :
					$output .= __( 'Variable product', 'woocommerce' );
					break;
				case 'simple' :
					$output .= __( 'Simple product', 'woocommerce' );
					break;
				default :
					// Assuming that we have other types in future
					$output .= ucfirst( $term->name );
					break;
			}

			$output .= " ($term->count)</option>";

			if ( 'simple' == $term->name ) {

				$output .= '<option value="downloadable" ';

				if ( isset( $wp_query->query['product_type'] ) ) {
					$output .= selected( 'downloadable', $wp_query->query['product_type'], false );
				}

				$output .= '> &rarr; ' . __( 'Downloadable', 'woocommerce' ) . '</option>';

				$output .= '<option value="virtual" ';

				if ( isset( $wp_query->query['product_type'] ) ) {
					$output .= selected( 'virtual', $wp_query->query['product_type'], false );
				}

				$output .= '> &rarr;  ' . __( 'Virtual', 'woocommerce' ) . '</option>';
			}
		}

		$output .= '</select>';

		echo apply_filters( 'woocommerce_product_filters', $output );
	}

	/**
	 * Filter the products in admin based on options
	 *
	 * @param mixed $query
	 */
	public function product_filters_query( $query ) {
		global $typenow, $wp_query;

		if ( 'product' == $typenow ) {

			if ( isset( $query->query_vars['product_type'] ) ) {
				// Subtypes
				if ( 'downloadable' == $query->query_vars['product_type'] ) {
					$query->query_vars['product_type']  = '';
					$query->query_vars['meta_value']    = 'yes';
					$query->query_vars['meta_key']      = '_downloadable';
				} elseif ( 'virtual' == $query->query_vars['product_type'] ) {
					$query->query_vars['product_type']  = '';
					$query->query_vars['meta_value']    = 'yes';
					$query->query_vars['meta_key']      = '_virtual';
				}
			}

			// Categories
			if ( isset( $_GET['product_cat'] ) && '0' == $_GET['product_cat'] ) {
				$query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => get_terms( 'product_cat', array( 'fields' => 'ids' ) ),
					'operator' => 'NOT IN'
				);
			}
		}
	}

	/**
	 * Search by SKU or ID for products.
	 * @param string $where
	 * @return string
	 */
	public function product_search( $where ) {
		global $pagenow, $wpdb, $wp;

		if ( 'edit.php' != $pagenow || ! is_search() || ! isset( $wp->query_vars['s'] ) || 'product' != $wp->query_vars['post_type'] ) {
			return $where;
		}

		$search_ids = array();
		$terms      = explode( ',', $wp->query_vars['s'] );

		foreach ( $terms as $term ) {
			if ( is_numeric( $term ) ) {
				$search_ids[] = $term;
			}
			// Attempt to get a SKU
			$sku_to_id = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value LIKE '%%%s%%';", wc_clean( $term ) ) );

			if ( $sku_to_id && sizeof( $sku_to_id ) > 0 ) {
				$search_ids = array_merge( $search_ids, $sku_to_id );
			}
		}

		$search_ids = array_filter( array_map( 'absint', $search_ids ) );

		if ( sizeof( $search_ids ) > 0 ) {
			$where = str_replace( ')))', ") OR ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . "))))", $where );
		}

		return $where;
	}

	/**
	 * Maintain term hierarchy when editing a product.
	 * @param  array $args
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {
		if ( 'product_cat' == $args['taxonomy'] ) {
			$args['checked_ontop'] = false;
		}

		return $args;
	}

	/**
	 * Custom bulk edit - form
	 *
	 * @access public
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function bulk_edit( $column_name, $post_type ) {
		if ( 'price' != $column_name || 'product' != $post_type ) {
			return;
		}

		include( WC()->plugin_path() . '/includes/admin/views/html-bulk-edit-product.php' );
	}

	/**
	 * Custom quick edit - form
	 *
	 * @access public
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function quick_edit( $column_name, $post_type ) {
		if ( 'price' != $column_name || 'product' != $post_type ) {
			return;
		}

		include( WC()->plugin_path() . '/includes/admin/views/html-quick-edit-product.php' );
	}

	/**
	 * Quick and bulk edit saving
	 *
	 * @access public
	 * @param int $post_id
	 * @param WP_Post $post
	 * @return int
	 */
	public function bulk_and_quick_edit_save_post( $post_id, $post ) {
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Check post type is product
		if ( 'product' != $post->post_type ) {
			return $post_id;
		}

		// Check user permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) && ! isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['woocommerce_bulk_edit_nonce'], 'woocommerce_bulk_edit_nonce' ) ) {
			return $post_id;
		}

		// Get the product and save
		$product = get_product( $post );

		if ( ! empty( $_REQUEST['woocommerce_quick_edit'] ) ) {
			$this->quick_edit_save( $post_id, $product );
		} else {
			$this->bulk_edit_save( $post_id, $product );
		}

		// Clear transient
		wc_delete_product_transients( $post_id );

		return $post_id;
	}

	/**
	 * Quick edit
	 */
	private function quick_edit_save( $post_id, $product ) {
		global $wpdb;

		$old_regular_price = $product->regular_price;
		$old_sale_price    = $product->sale_price;

		// Save fields
		if ( isset( $_REQUEST['_sku'] ) ) {
			$sku     = get_post_meta( $post_id, '_sku', true );
			$new_sku = wc_clean( stripslashes( $_REQUEST['_sku'] ) );

			if ( $new_sku !== $sku ) {
				if ( ! empty( $new_sku ) ) {
						$sku_exists = $wpdb->get_var( $wpdb->prepare("
							SELECT $wpdb->posts.ID
							FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
							WHERE $wpdb->posts.post_type = 'product'
							AND $wpdb->posts.post_status = 'publish'
							AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
						 ", $new_sku ) );

					if ( ! $sku_exists ) {
						update_post_meta( $post_id, '_sku', $new_sku );
					}
				} else {
					update_post_meta( $post_id, '_sku', '' );
				}
			}
		}

		if ( isset( $_REQUEST['_weight'] ) ) {
			update_post_meta( $post_id, '_weight', wc_clean( $_REQUEST['_weight'] ) );
		}

		if ( isset( $_REQUEST['_length'] ) ) {
			update_post_meta( $post_id, '_length', wc_clean( $_REQUEST['_length'] ) );
		}

		if ( isset( $_REQUEST['_width'] ) ) {
			update_post_meta( $post_id, '_width', wc_clean( $_REQUEST['_width'] ) );
		}

		if ( isset( $_REQUEST['_height'] ) ) {
			update_post_meta( $post_id, '_height', wc_clean( $_REQUEST['_height'] ) );
		}

		if ( isset( $_REQUEST['_visibility'] ) ) {
			update_post_meta( $post_id, '_visibility', wc_clean( $_REQUEST['_visibility'] ) );
		}

		if ( isset( $_REQUEST['_featured'] ) ) {
			update_post_meta( $post_id, '_featured', 'yes' );
		} else {
			update_post_meta( $post_id, '_featured', 'no' );
		}

		if ( isset( $_REQUEST['_tax_status'] ) ) {
			update_post_meta( $post_id, '_tax_status', wc_clean( $_REQUEST['_tax_status'] ) );
		}

		if ( isset( $_REQUEST['_tax_class'] ) ) {
			update_post_meta( $post_id, '_tax_class', wc_clean( $_REQUEST['_tax_class'] ) );
		}

		if ( $product->is_type('simple') || $product->is_type('external') ) {

			if ( isset( $_REQUEST['_regular_price'] ) ) {
				$new_regular_price = $_REQUEST['_regular_price'] === '' ? '' : wc_format_decimal( $_REQUEST['_regular_price'] );
				update_post_meta( $post_id, '_regular_price', $new_regular_price );
			} else {
				$new_regular_price = null;
			}
			if ( isset( $_REQUEST['_sale_price'] ) ) {
				$new_sale_price = $_REQUEST['_sale_price'] === '' ? '' : wc_format_decimal( $_REQUEST['_sale_price'] );
				update_post_meta( $post_id, '_sale_price', $new_sale_price );
			} else {
				$new_sale_price = null;
			}

			// Handle price - remove dates and set to lowest
			$price_changed = false;

			if ( ! is_null( $new_regular_price ) && $new_regular_price != $old_regular_price ) {
				$price_changed = true;
			} elseif ( ! is_null( $new_sale_price ) && $new_sale_price != $old_sale_price ) {
				$price_changed = true;
			}

			if ( $price_changed ) {
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );

				if ( ! is_null( $new_sale_price ) && $new_sale_price !== '' ) {
					update_post_meta( $post_id, '_price', $new_sale_price );
				} else {
					update_post_meta( $post_id, '_price', $new_regular_price );
				}
			}
		}

		// Handle stock status
		if ( isset( $_REQUEST['_stock_status'] ) ) {
			wc_update_product_stock_status( $post_id, wc_clean( $_REQUEST['_stock_status'] ) );
		}

		// Handle stock
		if ( ! $product->is_type('grouped') ) {
			if ( isset( $_REQUEST['_manage_stock'] ) ) {
				update_post_meta( $post_id, '_manage_stock', 'yes' );
				wc_update_product_stock( $post_id, intval( $_REQUEST['_stock'] ) );
			} else {
				update_post_meta( $post_id, '_manage_stock', 'no' );
				wc_update_product_stock( $post_id, 0 );
			}

			if ( ! empty( $_REQUEST['_backorders'] ) ) {
				update_post_meta( $post_id, '_backorders', wc_clean( $_REQUEST['_backorders'] ) );
			}
		}

		do_action( 'woocommerce_product_quick_edit_save', $product );
	}

	/**
	 * Bulk edit
	 */
	public function bulk_edit_save( $post_id, $product ) {

		$old_regular_price = $product->regular_price;
		$old_sale_price    = $product->sale_price;

		// Save fields
		if ( ! empty( $_REQUEST['change_weight'] ) && isset( $_REQUEST['_weight'] ) ) {
			update_post_meta( $post_id, '_weight', wc_clean( stripslashes( $_REQUEST['_weight'] ) ) );
		}

		if ( ! empty( $_REQUEST['change_dimensions'] ) ) {
			if ( isset( $_REQUEST['_length'] ) ) {
				update_post_meta( $post_id, '_length', wc_clean( stripslashes( $_REQUEST['_length'] ) ) );
			}
			if ( isset( $_REQUEST['_width'] ) ) {
				update_post_meta( $post_id, '_width', wc_clean( stripslashes( $_REQUEST['_width'] ) ) );
			}
			if ( isset( $_REQUEST['_height'] ) ) {
				update_post_meta( $post_id, '_height', wc_clean( stripslashes( $_REQUEST['_height'] ) ) );
			}
		}

		if ( ! empty( $_REQUEST['_tax_status'] ) ) {
			update_post_meta( $post_id, '_tax_status', wc_clean( $_REQUEST['_tax_status'] ) );
		}

		if ( ! empty( $_REQUEST['_tax_class'] ) ) {
			$tax_class = wc_clean( $_REQUEST['_tax_class'] );
			if ( 'standard' == $tax_class ) {
				$tax_class = '';
			}
			update_post_meta( $post_id, '_tax_class', $tax_class );
		}

		if ( ! empty( $_REQUEST['_stock_status'] ) ) {
			wc_update_product_stock_status( $post_id, wc_clean( $_REQUEST['_stock_status'] ) );
		}

		if ( ! empty( $_REQUEST['_visibility'] ) ) {
			update_post_meta( $post_id, '_visibility', stripslashes( $_REQUEST['_visibility'] ) );
		}

		if ( ! empty( $_REQUEST['_featured'] ) ) {
			update_post_meta( $post_id, '_featured', stripslashes( $_REQUEST['_featured'] ) );
		}

		// Handle price - remove dates and set to lowest
		if ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) ) {

			$price_changed = false;

			if ( ! empty( $_REQUEST['change_regular_price'] ) ) {

				$change_regular_price = absint( $_REQUEST['change_regular_price'] );
				$regular_price = esc_attr( stripslashes( $_REQUEST['_regular_price'] ) );

				switch ( $change_regular_price ) {
					case 1 :
						$new_price = $regular_price;
						break;
					case 2 :
						if ( strstr( $regular_price, '%' ) ) {
							$percent = str_replace( '%', '', $regular_price ) / 100;
							$new_price = $old_regular_price + ( round( $old_regular_price * $percent, absint( get_option( 'woocommerce_price_num_decimals' ) ) ) );
						} else {
							$new_price = $old_regular_price + $regular_price;
						}
						break;
					case 3 :
						if ( strstr( $regular_price, '%' ) ) {
							$percent = str_replace( '%', '', $regular_price ) / 100;
							$new_price = $old_regular_price - ( round ( $old_regular_price * $percent, absint( get_option( 'woocommerce_price_num_decimals' ) ) ) );
						} else {
							$new_price = $old_regular_price - $regular_price;
						}
						break;

					default :
						break;
				}

				if ( isset( $new_price ) && $new_price != $old_regular_price ) {
					$price_changed = true;
					$new_price = round( $new_price, absint( get_option( 'woocommerce_price_num_decimals' ) ) );
					update_post_meta( $post_id, '_regular_price', $new_price );
					$product->regular_price = $new_price;
				}
			}

			if ( ! empty( $_REQUEST['change_sale_price'] ) ) {

				$change_sale_price = absint( $_REQUEST['change_sale_price'] );
				$sale_price = esc_attr( stripslashes( $_REQUEST['_sale_price'] ) );

				switch ( $change_sale_price ) {
					case 1 :
						$new_price = $sale_price;
						break;
					case 2 :
						if ( strstr( $sale_price, '%' ) ) {
							$percent = str_replace( '%', '', $sale_price ) / 100;
							$new_price = $old_sale_price + ( $old_sale_price * $percent );
						} else {
							$new_price = $old_sale_price + $sale_price;
						}
						break;
					case 3 :
						if ( strstr( $sale_price, '%' ) ) {
							$percent = str_replace( '%', '', $sale_price ) / 100;
							$new_price = $old_sale_price - ( $old_sale_price * $percent );
						} else {
							$new_price = $old_sale_price - $sale_price;
						}
						break;
					case 4 :
						if ( strstr( $sale_price, '%' ) ) {
							$percent = str_replace( '%', '', $sale_price ) / 100;
							$new_price = $product->regular_price - ( $product->regular_price * $percent );
						} else {
							$new_price = $product->regular_price - $sale_price;
						}
						break;

					default :
						break;
				}

				if ( isset( $new_price ) && $new_price != $old_sale_price ) {
					$price_changed = true;
					$new_price = round( $new_price, absint( get_option( 'woocommerce_price_num_decimals' ) ) );
					update_post_meta( $post_id, '_sale_price', $new_price );
					$product->sale_price = $new_price;
				}
			}

			if ( $price_changed ) {
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );

				if ( $product->regular_price < $product->sale_price ) {
					$product->sale_price = '';
					update_post_meta( $post_id, '_sale_price', '' );
				}

				if ( $product->sale_price ) {
					update_post_meta( $post_id, '_price', $product->sale_price );
				} else {
					update_post_meta( $post_id, '_price', $product->regular_price );
				}
			}
		}

		// Handle stock
		if ( ! $product->is_type( 'grouped' ) ) {

			if ( ! empty( $_REQUEST['change_stock'] ) ) {
				update_post_meta( $post_id, '_manage_stock', 'yes' );
				wc_update_product_stock( $post_id, intval( $_REQUEST['_stock'] ) );
			}

			if ( ! empty( $_REQUEST['_manage_stock'] ) ) {

				if ( $_REQUEST['_manage_stock'] == 'yes' ) {
					update_post_meta( $post_id, '_manage_stock', 'yes' );
				} else {
					update_post_meta( $post_id, '_manage_stock', 'no' );
					wc_update_product_stock( $post_id, 0 );
				}
			}

			if ( ! empty( $_REQUEST['_backorders'] ) ) {
				update_post_meta( $post_id, '_backorders', wc_clean( $_REQUEST['_backorders'] ) );
			}

		}

		do_action( 'woocommerce_product_bulk_edit_save', $product );
	}

	/**
	 * Filter the directory for uploads.
	 *
	 * @param array $pathdata
	 * @return array
	 */
	public function upload_dir( $pathdata ) {
		// Change upload dir for downloadable files
		if ( isset( $_POST['type'] ) && 'downloadable_product' == $_POST['type'] ) {
			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/woocommerce_uploads';
				$pathdata['url']    = $pathdata['url']. '/woocommerce_uploads';
				$pathdata['subdir'] = '/woocommerce_uploads';
			} else {
				$new_subdir = '/woocommerce_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}

		return $pathdata;
	}

	/**
	 * Run a filter when uploading a downloadable product.
	 */
	public function woocommerce_media_upload_downloadable_product() {
		do_action( 'media_upload_file' );
	}

	/**
	 * Protect downloads from ms-files.php in multisite
	 *
	 * @param mixed $rewrite
	 * @return string
	 */
	public function ms_protect_download_rewite_rules( $rewrite ) {
		global $wp_rewrite;

		if ( ! is_multisite() || 'redirect' == get_option( 'woocommerce_file_download_method' ) ) {
			return $rewrite;
		}

		$rule  = "\n# WooCommerce Rules - Protect Files from ms-files.php\n\n";
		$rule .= "<IfModule mod_rewrite.c>\n";
		$rule .= "RewriteEngine On\n";
		$rule .= "RewriteCond %{QUERY_STRING} file=woocommerce_uploads/ [NC]\n";
		$rule .= "RewriteRule /ms-files.php$ - [F]\n";
		$rule .= "</IfModule>\n\n";

		return $rule . $rewrite;
	}

	/**
	 * Grant downloadable file access to any newly added files on any existing
	 * orders for this product that have previously been granted downloadable file access
	 *
	 * @param int $product_id product identifier
	 * @param int $variation_id optional product variation identifier
	 * @param array $downloadable_files newly set files
	 */
	public function process_product_file_download_paths( $product_id, $variation_id, $downloadable_files ) {
		global $wpdb;

		if ( $variation_id ) {
			$product_id = $variation_id;
		}

		$product               = get_product( $product_id );
		$existing_download_ids = array_keys( (array) $product->get_files() );
		$updated_download_ids  = array_keys( (array) $downloadable_files );

		$new_download_ids      = array_filter( array_diff( $updated_download_ids, $existing_download_ids ) );
		$removed_download_ids  = array_filter( array_diff( $existing_download_ids, $updated_download_ids ) );

		if ( $new_download_ids || $removed_download_ids ) {
			// determine whether downloadable file access has been granted via the typical order completion, or via the admin ajax method
			$existing_permissions = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE product_id = %d GROUP BY order_id", $product_id ) );

			foreach ( $existing_permissions as $existing_permission ) {
				$order = new WC_Order( $existing_permission->order_id );

				if ( $order->id ) {
					// Remove permissions
					if ( $removed_download_ids ) {
						foreach ( $removed_download_ids as $download_id ) {
							if ( apply_filters( 'woocommerce_process_product_file_download_paths_remove_access_to_old_file', true, $download_id, $product_id, $order ) ) {
								$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $order->id, $product_id, $download_id ) );
							}
						}
					}
					// Add permissions
					if ( $new_download_ids ) {
						foreach ( $new_download_ids as $download_id ) {
							if ( apply_filters( 'woocommerce_process_product_file_download_paths_grant_access_to_new_file', true, $download_id, $product_id, $order ) ) {
								// grant permission if it doesn't already exist
								if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $order->id, $product_id, $download_id ) ) ) {
									wc_downloadable_file_permission( $download_id, $product_id, $order );
								}
							}
						}
					}
				}
			}
		}
	}
}

endif;

return new WC_Admin_CPT_Product();
