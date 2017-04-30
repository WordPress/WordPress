<?php
/**
 * Duplicate product functionality
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Duplicate_Product' ) ) :

/**
 * WC_Admin_Duplicate_Product Class
 */
class WC_Admin_Duplicate_Product {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_action_duplicate_product', array( $this, 'duplicate_product_action' ) );
		add_filter( 'post_row_actions', array( $this, 'dupe_link' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'dupe_link' ), 10, 2 );
		add_action( 'post_submitbox_start', array( $this, 'dupe_button' ) );
	}

	/**
	 * Show the "Duplicate" link in admin products list
	 * @param  array   $actions
	 * @param  WP_Post $post Post object
	 * @return array
	 */
	public function dupe_link( $actions, $post ) {
		if ( ! current_user_can( apply_filters( 'woocommerce_duplicate_product_capability', 'manage_woocommerce' ) ) )
			return $actions;

		if ( $post->post_type != 'product' )
			return $actions;

		$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=product&action=duplicate_product&amp;post=' . $post->ID ), 'woocommerce-duplicate-product_' . $post->ID ) . '" title="' . __( 'Make a duplicate from this product', 'woocommerce' )
			. '" rel="permalink">' .  __( 'Duplicate', 'woocommerce' ) . '</a>';

		return $actions;
	}

	/**
	 * Show the dupe product link in admin
	 */
	public function dupe_button() {
		global $post;

		if ( ! current_user_can( apply_filters( 'woocommerce_duplicate_product_capability', 'manage_woocommerce' ) ) )
			return;

		if ( ! is_object( $post ) )
			return;

		if ( $post->post_type != 'product' )
			return;

		if ( isset( $_GET['post'] ) ) {
			$notifyUrl = wp_nonce_url( admin_url( "edit.php?post_type=product&action=duplicate_product&post=" . absint( $_GET['post'] ) ), 'woocommerce-duplicate-product_' . $_GET['post'] );
			?>
			<div id="duplicate-action"><a class="submitduplicate duplication" href="<?php echo esc_url( $notifyUrl ); ?>"><?php _e( 'Copy to a new draft', 'woocommerce' ); ?></a></div>
			<?php
		}
	}

	/**
	 * Duplicate a product action.
	 */
	public function duplicate_product_action() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_die(__( 'No product to duplicate has been supplied!', 'woocommerce' ));
		}

		// Get the original page
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'woocommerce-duplicate-product_' . $id );

		$post = $this->get_product_to_duplicate( $id );

		// Copy the page and insert it
		if ( ! empty( $post ) ) {
			$new_id = $this->duplicate_product( $post );

			// If you have written a plugin which uses non-WP database tables to save
			// information about a page you can hook this action to dupe that data.
			do_action( 'woocommerce_duplicate_product', $new_id, $post );

			// Redirect to the edit screen for the new draft page
			wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
			exit;
		} else {
			wp_die(__( 'Product creation failed, could not find original product:', 'woocommerce' ) . ' ' . $id );
		}
	}

	/**
	 * Function to create the duplicate of the product.
	 *
	 * @access public
	 * @param mixed $post
	 * @param int $parent (default: 0)
	 * @param string $post_status (default: '')
	 * @return int
	 */
	public function duplicate_product( $post, $parent = 0, $post_status = '' ) {
		global $wpdb;

		$new_post_author 	= wp_get_current_user();
		$new_post_date 		= current_time('mysql');
		$new_post_date_gmt 	= get_gmt_from_date($new_post_date);

		if ( $parent > 0 ) {
			$post_parent		= $parent;
			$post_status 		= $post_status ? $post_status : 'publish';
			$suffix 			= '';
		} else {
			$post_parent		= $post->post_parent;
			$post_status 		= $post_status ? $post_status : 'draft';
			$suffix 			= ' ' . __( '(Copy)', 'woocommerce' );
		}

		$new_post_type 			= $post->post_type;
		$post_content    		= str_replace("'", "''", $post->post_content);
		$post_content_filtered 	= str_replace("'", "''", $post->post_content_filtered);
		$post_excerpt    		= str_replace("'", "''", $post->post_excerpt);
		$post_title      		= str_replace("'", "''", $post->post_title).$suffix;
		$post_name       		= str_replace("'", "''", $post->post_name);
		$comment_status  		= str_replace("'", "''", $post->comment_status);
		$ping_status     		= str_replace("'", "''", $post->ping_status);

		// Insert the new template in the post table
		$wpdb->query(
				"INSERT INTO $wpdb->posts
				(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, post_type, comment_status, ping_status, post_password, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_mime_type)
				VALUES
				('$new_post_author->ID', '$new_post_date', '$new_post_date_gmt', '$post_content', '$post_content_filtered', '$post_title', '$post_excerpt', '$post_status', '$new_post_type', '$comment_status', '$ping_status', '$post->post_password', '$post->to_ping', '$post->pinged', '$new_post_date', '$new_post_date_gmt', '$post_parent', '$post->menu_order', '$post->post_mime_type')");

		$new_post_id = $wpdb->insert_id;

		// Copy the taxonomies
		$this->duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

		// Copy the meta information
		$this->duplicate_post_meta( $post->ID, $new_post_id );

		// Copy the children (variations)
		if ( $children_products = get_children( 'post_parent='.$post->ID.'&post_type=product_variation' ) ) {

			if ( $children_products )
				foreach ( $children_products as $child )
					$this->duplicate_product( $this->get_product_to_duplicate( $child->ID ), $new_post_id, $child->post_status );
		}

		return $new_post_id;
	}

	/**
	 * Get a product from the database to duplicate

	 * @access public
	 * @param mixed $id
	 * @return WP_Post|bool
	 * @todo Returning false? Need to check for it in...
	 * @see duplicate_product
	 */
	private function get_product_to_duplicate( $id ) {
		global $wpdb;

		$id = absint( $id );

		if ( ! $id )
			return false;

		$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );

		if ( isset( $post->post_type ) && $post->post_type == "revision" ) {
			$id   = $post->post_parent;
			$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );
		}
		return $post[0];
	}

	/**
	 * Copy the taxonomies of a post to another post
	 *
	 * @access public
	 * @param mixed $id
	 * @param mixed $new_id
	 * @param mixed $post_type
	 * @return void
	 */
	private function duplicate_post_taxonomies( $id, $new_id, $post_type ) {
		global $wpdb;
		$taxonomies = get_object_taxonomies($post_type); //array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($id, $taxonomy);
			$post_terms_count = sizeof( $post_terms );
			for ($i=0; $i<$post_terms_count; $i++) {
				wp_set_object_terms($new_id, $post_terms[$i]->slug, $taxonomy, true);
			}
		}
	}

	/**
	 * Copy the meta information of a post to another post
	 *
	 * @access public
	 * @param mixed $id
	 * @param mixed $new_id
	 * @return void
	 */
	private function duplicate_post_meta( $id, $new_id ) {
		global $wpdb;
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id");

		if (count($post_meta_infos)!=0) {
			$sql_query_sel = array();
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}
	}

}

endif;

return new WC_Admin_Duplicate_Product();
