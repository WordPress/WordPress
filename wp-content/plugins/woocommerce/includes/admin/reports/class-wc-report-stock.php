<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * WC_Report_Stock
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin/Reports
 * @version     2.1.0
 */
class WC_Report_Stock extends WP_List_Table {

    /**
     * __construct function.
     *
     * @access public
     */
    public function __construct(){
        parent::__construct( array(
            'singular'  => __( 'Stock', 'woocommerce' ),
            'plural'    => __( 'Stock', 'woocommerce' ),
            'ajax'      => false
        ) );
    }

    /**
     * No items found text
     */
    public function no_items() {
        _e( 'No products found.', 'woocommerce' );
    }

    /**
     * Don't need this
     */
    public function display_tablenav( $position ) {
        if ( $position != 'top' )
            parent::display_tablenav( $position );
    }

	/**
	 * Output the report
	 */
	public function output_report() {
		$this->prepare_items();
		echo '<div id="poststuff" class="woocommerce-reports-wide">';
		$this->display();
		echo '</div>';
	}

    /**
     * column_default function.
     *
     * @access public
     * @param mixed $item
     * @param mixed $column_name
     */
    function column_default( $item, $column_name ) {
    	global $woocommerce, $wpdb, $product;

    	if ( ! $product || $product->id !== $item->id )
    		$product = get_product( $item->id );

        switch( $column_name ) {
        	case 'product' :
                if ( $sku = $product->get_sku() )
                    echo $sku . ' - ';

        		echo $product->get_title();

                // Get variation data
                if ( $product->is_type( 'variation' ) ) {
                    $list_attributes = array();
                    $attributes = $product->get_variation_attributes();

                    foreach ( $attributes as $name => $attribute ) {
                        $list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': <strong>' . $attribute . '</strong>';
                    }

                   echo '<div class="description">' . implode( ', ', $list_attributes ) . '</div>';
                }
        	break;
            case 'parent' :
                if ( $item->parent )
                    echo get_the_title( $item->parent );
                else
                    echo '-';
            break;
            case 'stock_status' :
                if ( $product->is_in_stock() ) {
                    echo '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
                } else {
                    echo '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
                }
            break;
        	case 'stock_level' :
        		echo $product->get_stock_quantity();
        	break;
            case 'wc_actions' :
                ?><p>
                    <?php
                        $actions = array();
                        $action_id = $product->is_type( 'variation' ) ? $item->parent : $item->id;

                        $actions['edit'] = array(
                            'url'       => admin_url( 'post.php?post=' . $action_id . '&action=edit' ),
                            'name'      => __( 'Edit', 'woocommerce' ),
                            'action'    => "edit"
                        );

                        if ( $product->is_visible() )
                            $actions['view'] = array(
                                'url'       => get_permalink( $action_id ),
                                'name'      => __( 'View', 'woocommerce' ),
                                'action'    => "view"
                            );

                        $actions = apply_filters( 'woocommerce_admin_stock_report_product_actions', $actions, $product );

                        foreach ( $actions as $action ) {
                            printf( '<a class="button tips %s" href="%s" data-tip="%s ' . __( 'product', 'woocommerce' ) . '">%s</a>', $action['action'], esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
                        }
                    ?>
                </p><?php
            break;
        }
	}

    /**
     * get_columns function.
     *
     * @access public
     */
    function get_columns(){
        $columns = array(
            'product'      => __( 'Product', 'woocommerce' ),
            'parent'       => __( 'Parent', 'woocommerce' ),
            'stock_level'  => __( 'Units in stock', 'woocommerce' ),
            'stock_status' => __( 'Stock status', 'woocommerce' ),
            'wc_actions'   => __( 'Actions', 'woocommerce' ),
        );

        return $columns;
    }

    /**
     * prepare_items function.
     *
     * @access public
     */
    public function prepare_items() {
        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
        $current_page          = absint( $this->get_pagenum() );
        $per_page              = 20;

        $this->get_items( $current_page, $per_page );

        /**
         * Pagination
         */
        $this->set_pagination_args( array(
            'total_items' => $this->max_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $this->max_items / $per_page )
        ) );
    }
}