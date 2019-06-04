<?php
/**
 * List Table API: WP_Privacy_Data_Export_Requests_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.9.6
 */

if ( ! class_exists( 'WP_Privacy_Requests_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-privacy-requests-table.php' );
}

/**
 * WP_Privacy_Data_Export_Requests_Table class.
 *
 * @since 4.9.6
 */
class WP_Privacy_Data_Export_Requests_List_Table extends WP_Privacy_Requests_Table {
	/**
	 * Action name for the requests this table will work with.
	 *
	 * @since 4.9.6
	 *
	 * @var string $request_type Name of action.
	 */
	protected $request_type = 'export_personal_data';

	/**
	 * Post type for the requests.
	 *
	 * @since 4.9.6
	 *
	 * @var string $post_type The post type.
	 */
	protected $post_type = 'user_request';

	/**
	 * Actions column.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 * @return string Email column markup.
	 */
	public function column_email( $item ) {
		/** This filter is documented in wp-admin/includes/ajax-actions.php */
		$exporters       = apply_filters( 'wp_privacy_personal_data_exporters', array() );
		$exporters_count = count( $exporters );
		$request_id      = $item->ID;
		$nonce           = wp_create_nonce( 'wp-privacy-export-personal-data-' . $request_id );

		$download_data_markup = '<div class="export-personal-data" ' .
			'data-exporters-count="' . esc_attr( $exporters_count ) . '" ' .
			'data-request-id="' . esc_attr( $request_id ) . '" ' .
			'data-nonce="' . esc_attr( $nonce ) .
			'">';

		$download_data_markup .= '<span class="export-personal-data-idle"><button type="button" class="button-link export-personal-data-handle">' . __( 'Download Personal Data' ) . '</button></span>' .
			'<span class="export-personal-data-processing hidden">' . __( 'Downloading Data...' ) . '</span>' .
			'<span class="export-personal-data-success hidden"><button type="button" class="button-link export-personal-data-handle">' . __( 'Download Personal Data Again' ) . '</button></span>' .
			'<span class="export-personal-data-failed hidden">' . __( 'Download failed.' ) . ' <button type="button" class="button-link">' . __( 'Retry' ) . '</button></span>';

		$download_data_markup .= '</div>';

		$row_actions = array(
			'download-data' => $download_data_markup,
		);

		return sprintf( '<a href="%1$s">%2$s</a> %3$s', esc_url( 'mailto:' . $item->email ), $item->email, $this->row_actions( $row_actions ) );
	}

	/**
	 * Displays the next steps column.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 */
	public function column_next_steps( $item ) {
		$status = $item->status;

		switch ( $status ) {
			case 'request-pending':
				esc_html_e( 'Waiting for confirmation' );
				break;
			case 'request-confirmed':
				/** This filter is documented in wp-admin/includes/ajax-actions.php */
				$exporters       = apply_filters( 'wp_privacy_personal_data_exporters', array() );
				$exporters_count = count( $exporters );
				$request_id      = $item->ID;
				$nonce           = wp_create_nonce( 'wp-privacy-export-personal-data-' . $request_id );

				echo '<div class="export-personal-data" ' .
					'data-send-as-email="1" ' .
					'data-exporters-count="' . esc_attr( $exporters_count ) . '" ' .
					'data-request-id="' . esc_attr( $request_id ) . '" ' .
					'data-nonce="' . esc_attr( $nonce ) .
					'">';

				?>
				<span class="export-personal-data-idle"><button type="button" class="button export-personal-data-handle"><?php _e( 'Send Export Link' ); ?></button></span>
				<span class="export-personal-data-processing button updating-message hidden"><?php _e( 'Sending Email...' ); ?></span>
				<span class="export-personal-data-success success-message hidden"><?php _e( 'Email sent.' ); ?></span>
				<span class="export-personal-data-failed hidden"><?php _e( 'Email could not be sent.' ); ?> <button type="button" class="button export-personal-data-handle"><?php _e( 'Retry' ); ?></button></span>
				<?php

				echo '</div>';
				break;
			case 'request-failed':
				submit_button( __( 'Retry' ), 'secondary', 'privacy_action_email_retry[' . $item->ID . ']', false );
				break;
			case 'request-completed':
				echo '<a href="' . esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action'     => 'delete',
								'request_id' => array( $item->ID ),
							),
							admin_url( 'export-personal-data.php' )
						),
						'bulk-privacy_requests'
					)
				) . '" class="button">' . esc_html__( 'Remove request' ) . '</a>';
				break;
		}
	}
}
