<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WPCF7_Contact_Form_List_Table extends WP_List_Table {

	public static function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'contact-form-7' ),
			'shortcode' => __( 'Shortcode', 'contact-form-7' ),
			'author' => __( 'Author', 'contact-form-7' ),
			'date' => __( 'Date', 'contact-form-7' ),
		);

		return $columns;
	}

	public function __construct() {
		parent::__construct( array(
			'singular' => 'post',
			'plural' => 'posts',
			'ajax' => false,
		) );
	}

	public function prepare_items() {
		$current_screen = get_current_screen();
		$per_page = $this->get_items_per_page( 'wpcf7_contact_forms_per_page' );

		$args = array(
			'posts_per_page' => $per_page,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => ( $this->get_pagenum() - 1 ) * $per_page,
		);

		if ( $search_keyword = wpcf7_superglobal_request( 's' ) ) {
			$args['s'] = $search_keyword;
		}

		if ( $order_by = wpcf7_superglobal_request( 'orderby' ) ) {
			$args['orderby'] = $order_by;
		}

		if (
			$order = wpcf7_superglobal_request( 'order' ) and
			'desc' === strtolower( $order )
		) {
			$args['order'] = 'DESC';
		}

		$this->items = WPCF7_ContactForm::find( $args );

		$total_items = WPCF7_ContactForm::count();
		$total_pages = ceil( $total_items / $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page,
		) );
	}

	public function get_columns() {
		return get_column_headers( get_current_screen() );
	}

	protected function get_sortable_columns() {
		$columns = array(
			'title' => array( 'title', true ),
			'author' => array( 'author', false ),
			'date' => array( 'date', false ),
		);

		return $columns;
	}

	protected function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'contact-form-7' ),
		);

		return $actions;
	}

	protected function column_default( $item, $column_name ) {
		return '';
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id()
		);
	}

	public function column_title( $item ) {
		$edit_link = add_query_arg(
			array(
				'post' => absint( $item->id() ),
				'action' => 'edit',
			),
			menu_page_url( 'wpcf7', false )
		);

		$output = sprintf(
			'<a class="row-title" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $edit_link ),
			esc_attr( sprintf(
				/* translators: %s: title of contact form */
				__( 'Edit &#8220;%s&#8221;', 'contact-form-7' ),
				$item->title()
			) ),
			esc_html( $item->title() )
		);

		$output = sprintf( '<strong>%s</strong>', $output );

		if ( wpcf7_validate_configuration()
		and current_user_can( 'wpcf7_edit_contact_form', $item->id() ) ) {
			$config_validator = new WPCF7_ConfigValidator( $item );
			$config_validator->restore();

			if ( $count_errors = $config_validator->count_errors() ) {
				$error_notice = sprintf(
					/* translators: %s: number of errors detected */
					_n(
						'%s configuration error detected',
						'%s configuration errors detected',
						$count_errors, 'contact-form-7' ),
					number_format_i18n( $count_errors )
				);

				$output .= sprintf(
					'<div class="config-error"><span class="icon-in-circle" aria-hidden="true">!</span> %s</div>',
					$error_notice
				);
			}
		}

		return $output;
	}

	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $column_name !== $primary ) {
			return '';
		}

		$edit_link = add_query_arg(
			array(
				'post' => absint( $item->id() ),
				'action' => 'edit',
			),
			menu_page_url( 'wpcf7', false )
		);

		$actions = array(
			'edit' => wpcf7_link( $edit_link, __( 'Edit', 'contact-form-7' ) ),
		);

		if ( current_user_can( 'wpcf7_edit_contact_form', $item->id() ) ) {
			$copy_link = add_query_arg(
				array(
					'post' => absint( $item->id() ),
					'action' => 'copy',
				),
				menu_page_url( 'wpcf7', false )
			);

			$copy_link = wp_nonce_url(
				$copy_link,
				'wpcf7-copy-contact-form_' . absint( $item->id() )
			);

			$actions = array_merge( $actions, array(
				'copy' => wpcf7_link( $copy_link, __( 'Duplicate', 'contact-form-7' ) ),
			) );
		}

		return $this->row_actions( $actions );
	}

	public function column_author( $item ) {
		$post = get_post( $item->id() );

		if ( ! $post ) {
			return;
		}

		$author = get_userdata( $post->post_author );

		if ( false === $author ) {
			return;
		}

		return esc_html( $author->display_name );
	}

	public function column_shortcode( $item ) {
		$shortcodes = array( $item->shortcode() );

		$output = '';

		foreach ( $shortcodes as $shortcode ) {
			$output .= "\n" . sprintf(
				'<span class="shortcode"><input %s /></span>',
				wpcf7_format_atts( array(
					'type' => 'text',
					'readonly' => true,
					'value' => $shortcode,
					'class' => 'large-text code selectable',
				) )
			);
		}

		return trim( $output );
	}

	public function column_date( $item ) {
		$datetime = get_post_datetime( $item->id() );

		if ( false === $datetime ) {
			return '';
		}

		$t_time = sprintf(
			/* translators: 1: date, 2: time */
			__( '%1$s at %2$s', 'contact-form-7' ),
			/* translators: date format, see https://www.php.net/date */
			$datetime->format( __( 'Y/m/d', 'contact-form-7' ) ),
			/* translators: time format, see https://www.php.net/date */
			$datetime->format( __( 'g:i a', 'contact-form-7' ) )
		);

		return $t_time;
	}
}
