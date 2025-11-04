<?php
/**
 * Scheduled updates list table.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 6.6.0
 */

require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

/**
 * List table for scheduled post updates.
 *
 * @since 6.6.0
 */
class WP_Scheduled_Updates_List_Table extends WP_List_Table {

        /**
         * Post type slug to scope scheduled updates to.
         *
         * @since 6.6.0
         * @var string
         */
        protected $post_type;

        /**
         * Constructor.
         *
         * @since 6.6.0
         *
         * @param array $args Optional. Arguments for the list table. Default empty array.
         */
        public function __construct( $args = array() ) {
                parent::__construct(
                        array(
                                'plural'   => 'scheduled-updates',
                                'singular' => 'scheduled-update',
                                'ajax'     => false,
                        )
                );

                $this->post_type = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
        }

        /**
         * Prepares the list of items for displaying.
         *
         * @since 6.6.0
         */
        public function prepare_items() {
                $per_page     = $this->get_items_per_page( 'scheduled_updates_per_page', 20 );
                $current_page = $this->get_pagenum();
                $offset       = ( $current_page - 1 ) * $per_page;

                $results = wp_get_scheduled_updates(
                        array(
                                'post_type' => $this->post_type,
                                'number'    => $per_page,
                                'offset'    => $offset,
                        )
                );

                $this->items = $results['items'];

                $total_items = $results['total'];
                $total_pages = $per_page ? ceil( $total_items / $per_page ) : 1;

                $this->_column_headers = array( $this->get_columns(), array(), array() );

                $this->set_pagination_args(
                        array(
                                'total_items' => $total_items,
                                'per_page'    => $per_page,
                                'total_pages' => max( 1, $total_pages ),
                        )
                );
        }

        /**
         * Message to display when no items available.
         *
         * @since 6.6.0
         */
        public function no_items() {
                _e( 'No scheduled updates found.' );
        }

        /**
         * Gets the list of columns for the table.
         *
         * @since 6.6.0
         *
         * @return array
         */
        public function get_columns() {
                return array(
                        'title'         => __( 'Post' ),
                        'scheduled_for' => __( 'Scheduled For' ),
                        'author'        => __( 'Author' ),
                        'actions'       => __( 'Actions' ),
                );
        }

        /**
         * Default column handler.
         *
         * @since 6.6.0
         *
         * @param WP_Post $item        Current item.
         * @param string  $column_name Column name.
         * @return string
         */
        public function column_default( $item, $column_name ) {
                switch ( $column_name ) {
                        case 'title':
                                return $this->column_title( $item );
                        case 'scheduled_for':
                                return $this->column_scheduled_for( $item );
                        case 'author':
                                return $this->column_author( $item );
                        case 'actions':
                                return $this->column_actions( $item );
                }

                return '';
        }

        /**
         * Renders the title column.
         *
         * @since 6.6.0
         *
         * @param WP_Post $item Current scheduled update.
         * @return string
         */
        protected function column_title( $item ) {
                $parent = get_post( $item->post_parent );

                if ( ! $parent ) {
                        return esc_html( get_the_title( $item ) );
                }

                $title     = get_the_title( $parent );
                $edit_link = get_edit_post_link( $parent->ID );

                if ( $edit_link ) {
                        $title = sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html( $title ) );
                } else {
                        $title = esc_html( $title );
                }

                return $title;
        }

        /**
         * Renders the scheduled date column.
         *
         * @since 6.6.0
         *
         * @param WP_Post $item Current scheduled update.
         * @return string
         */
        protected function column_scheduled_for( $item ) {
                $timestamp = (int) get_post_meta( $item->ID, '_scheduled_update_timestamp', true );

                if ( ! $timestamp ) {
                        $timestamp = strtotime( $item->post_date_gmt . ' GMT' );
                }

                if ( ! $timestamp ) {
                        return '&mdash;';
                }

                return esc_html(
                        wp_date(
                                get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
                                $timestamp
                        )
                );
        }

        /**
         * Renders the author column.
         *
         * @since 6.6.0
         *
         * @param WP_Post $item Current scheduled update.
         * @return string
         */
        protected function column_author( $item ) {
                $user = get_user_by( 'id', $item->post_author );

                if ( ! $user ) {
                        return '&mdash;';
                }

                return esc_html( $user->display_name );
        }

        /**
         * Renders the actions column.
         *
         * @since 6.6.0
         *
         * @param WP_Post $item Current scheduled update.
         * @return string
         */
        protected function column_actions( $item ) {
                $links = array();

                $links[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url( admin_url( 'revision.php?revision=' . $item->ID ) ),
                        __( 'View diff' )
                );

                if ( current_user_can( 'edit_post', $item->post_parent ) ) {
                        $cancel_url = wp_nonce_url(
                                add_query_arg(
                                        array(
                                                'action'     => 'cancel-scheduled-update',
                                                'revision'   => $item->ID,
                                                'post_type'  => $this->post_type,
                                                'paged'      => $this->get_pagenum(),
                                        ),
                                        admin_url( 'edit.php' )
                                ),
                                'cancel-scheduled-update_' . $item->ID
                        );

                        $links[] = sprintf(
                                '<a href="%s" class="submitdelete">%s</a>',
                                esc_url( $cancel_url ),
                                __( 'Cancel' )
                        );
                }

                return implode( ' | ', $links );
        }

        /**
         * Gets the name of the default primary column.
         *
         * @since 6.6.0
         *
         * @return string
         */
        protected function get_default_primary_column_name() {
                return 'title';
        }
}
