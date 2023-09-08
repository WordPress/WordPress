<?php
/**
 * List Table API: WP_Privacy_Data_Removal_Requests_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.9.6
 */

if ( ! class_exists( 'WP_Privacy_Requests_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-requests-table.php';
}

/**
 * WP_Privacy_Data_Removal_Requests_List_Table class.
 *
 * @since 4.9.6
 */
class WP_Privacy_Data_Removal_Requests_List_Table extends WP_Privacy_Requests_Table {
	/**
	 * Action name for the requests this table will work with.
	 *
	 * @since 4.9.6
	 *
	 * @var string $request_type Name of action.
	 */
	protected $request_type = 'remove_personal_data';

	/**
	 * Post type for the requests.
	 *
	 * @since 4.9.6
	 *
	 * @var string $post_type The post type.
	 */
	protected $post_type = 'user_request';

	/**
	 * Outputs the Actions column.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_User_Request $item Item being shown.
	 * @return string Email column markup.
	 */
	public function column_email( $item ) {
		$row_actions = array();

		// Allow the administrator to "force remove" the personal data even if confirmation has not yet been received.
		$status      = $item->status;
		$request_id  = $item->ID;
		$row_actions = array();
		if ( 'request-confirmed' !== $status ) {
			/** This filter is documented in wp-admin/includes/ajax-actions.php */
			$erasers       = apply_filters( 'wp_privacy_personal_data_erasers', array() );
			$erasers_count = count( $erasers );
			$nonce         = wp_create_nonce( 'wp-privacy-erase-personal-data-' . $request_id );

			$remove_data_markup = '<span class="remove-personal-data force-remove-personal-data" ' .
				'data-erasers-count="' . esc_attr( $erasers_count ) . '" ' .
				'data-request-id="' . esc_attr( $request_id ) . '" ' .
				'data-nonce="' . esc_attr( $nonce ) .
				'">';

			$remove_data_markup .= '<span class="remove-personal-data-idle"><button type="button" class="button-link remove-personal-data-handle">' . __( 'Force erase personal data' ) . '</button></span>' .
				'<span class="remove-personal-data-processing hidden">' . __( 'Erasing data...' ) . ' <span class="erasure-progress"></span></span>' .
				'<span class="remove-personal-data-success hidden">' . __( 'Erasure completed.' ) . '</span>' .
				'<span class="remove-personal-data-failed hidden">' . __( 'Force erasure has failed.' ) . ' <button type="button" class="button-link remove-personal-data-handle">' . __( 'Retry' ) . '</button></span>';

			$remove_data_markup .= '</span>';

			$row_actions['remove-data'] = $remove_data_markup;
		}

		if ( 'request-completed' !== $status ) {
			$complete_request_markup  = '<span>';
			$complete_request_markup .= sprintf(
				'<a href="%s" class="complete-request" aria-label="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action'     => 'complete',
								'request_id' => array( $request_id ),
							),
							admin_url( 'erase-personal-data.php' )
						),
						'bulk-privacy_requests'
					)
				),
				esc_attr(
					sprintf(
						/* translators: %s: Request email. */
						__( 'Mark export request for &#8220;%s&#8221; as completed.' ),
						$item->email
					)
				),
				__( 'Complete request' )
			);
			$complete_request_markup .= '</span>';
		}

		if ( ! empty( $complete_request_markup ) ) {
			$row_actions['complete-request'] = $complete_request_markup;
		}

		return sprintf( '<a href="%1$s">%2$s</a> %3$s', esc_url( 'mailto:' . $item->email ), $item->email, $this->row_actions( $row_actions ) );
	}

	/**
	 * Outputs the Next steps column.
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
				$erasers       = apply_filters( 'wp_privacy_personal_data_erasers', array() );
				$erasers_count = count( $erasers );
				$request_id    = $item->ID;
				$nonce         = wp_create_nonce( 'wp-privacy-erase-personal-data-' . $request_id );

				echo '<div class="remove-personal-data" ' .
					'data-force-erase="1" ' .
					'data-erasers-count="' . esc_attr( $erasers_count ) . '" ' .
					'data-request-id="' . esc_attr( $request_id ) . '" ' .
					'data-nonce="' . esc_attr( $nonce ) .
					'">';

				?>
				<span class="remove-personal-data-idle"><button type="button" class="button-link remove-personal-data-handle"><?php _e( 'Erase personal data' ); ?></button></span>
				<span class="remove-personal-data-processing hidden"><?php _e( 'Erasing data...' ); ?> <span class="erasure-progress"></span></span>
				<span class="remove-personal-data-success success-message hidden" ><?php _e( 'Erasure completed.' ); ?></span>
				<span class="remove-personal-data-failed hidden"><?php _e( 'Data erasure has failed.' ); ?> <button type="button" class="button-link remove-personal-data-handle"><?php _e( 'Retry' ); ?></button></span>
				<?php

				echo '</div>';

				break;
			case 'request-failed':
				echo '<button type="submit" class="button-link" name="privacy_action_email_retry[' . $item->ID . ']" id="privacy_action_email_retry[' . $item->ID . ']">' . __( 'Retry' ) . '</button>';
				break;
			case 'request-completed':
				echo '<a href="' . esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action'     => 'delete',
								'request_id' => array( $item->ID ),
							),
							admin_url( 'erase-personal-data.php' )
						),
						'bulk-privacy_requests'
					)
				) . '">' . esc_html__( 'Remove request' ) . '</a>';
				break;
		}
	}
}
