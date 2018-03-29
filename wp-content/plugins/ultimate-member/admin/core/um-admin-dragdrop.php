<?php

class UM_Admin_DragDrop {

	function __construct() {

		add_action('admin_footer', array(&$this, 'load_field_order'), 9);

		add_action('wp_ajax_nopriv_update_order', array(&$this, 'update_order') );
		add_action('wp_ajax_update_order', array(&$this, 'update_order') );

	}

	/***
	***	@update order of fields
	***/
	function update_order(){

		global $ultimatemember;

		if ( !is_user_logged_in() || !current_user_can('manage_options') ) die('Please login as administrator');

		extract($_POST);

		$fields = $ultimatemember->query->get_attr('custom_fields', $form_id );

		$this->row_data = get_option('um_form_rowdata_'. $form_id, array() );
        $this->exist_rows = array();

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $key => $array ) {
				if ( $array['type'] == 'row' ) {
					$this->row_data[$key] = $array;
					unset( $fields[$key] );
				}
			}
		} else {
            $fields = array();
        }

		foreach ( $_POST as $key => $value ) {

			// adding rows
			if ( 0 === strpos( $key, '_um_row_' ) ) {

				$update_args = null;

				$row_id = str_replace( '_um_row_', '', $key );

				$row_array = array(
					'type' => 'row',
					'id' => $value,
					'sub_rows' => $_POST[ '_um_rowsub_'.$row_id .'_rows' ],
					'cols' => $_POST[ '_um_rowcols_'.$row_id .'_cols' ],
					'origin' => $_POST[ '_um_roworigin_'.$row_id . '_val' ],
				);

				$row_args = $row_array;

				if ( isset( $this->row_data[ $row_array['origin'] ] ) ) {
					foreach( $this->row_data[ $row_array['origin'] ] as $k => $v ){
						if ( $k != 'position' && $k != 'metakey' ) {
							$update_args[$k] = $v;
						}
					}
					if ( isset( $update_args ) ) {
					$row_args = array_merge( $update_args, $row_array );
					}
					$this->exist_rows[] = $key;
				}

				$fields[$key] = $row_args;

			}

			// change field position
			if ( 0 === strpos( $key, 'um_position_' ) ) {
				$field_key = str_replace('um_position_','',$key);
				if ( isset( $fields[$field_key] ) ) {
					$fields[$field_key]['position'] = $value;
				}
			}

			// change field master row
			if ( 0 === strpos( $key, 'um_row_' ) ) {
				$field_key = str_replace('um_row_','',$key);
				if ( isset( $fields[$field_key] ) ) {
					$fields[$field_key]['in_row'] = $value;
				}
			}

			// change field sub row
			if ( 0 === strpos( $key, 'um_subrow_' ) ) {
				$field_key = str_replace('um_subrow_','',$key);
				if ( isset( $fields[$field_key] ) ) {
					$fields[$field_key]['in_sub_row'] = $value;
				}
			}

			// change field column
			if ( 0 === strpos( $key, 'um_col_' ) ) {
				$field_key = str_replace('um_col_','',$key);
				if ( isset( $fields[$field_key] ) ) {
					$fields[$field_key]['in_column'] = $value;
				}
			}

			// add field to group
			if ( 0 === strpos( $key, 'um_group_' ) ) {
				$field_key = str_replace('um_group_','',$key);
				if ( isset( $fields[$field_key] ) ) {
					$fields[$field_key]['in_group'] = $value;
				}
			}

		}

		foreach ( $this->row_data as $k => $v ) {
			if ( ! in_array( $k, $this->exist_rows ) )
				unset( $this->row_data[$k] );
		}

		update_option( 'um_existing_rows_' . $form_id, $this->exist_rows );

		update_option( 'um_form_rowdata_' . $form_id , $this->row_data );

		$ultimatemember->query->update_attr( 'custom_fields', $form_id, $fields );

	}

	/***
	***	@load form to maintain form order
	***/
	function load_field_order(){

		global $ultimatemember;

		$screen = get_current_screen();

		if ( !isset( $screen->id ) || $screen->id != 'um_form' ) return;

		?>

		<div class="um-col-demon-settings" data-in_row="" data-in_sub_row="" data-in_column="" data-in_group="" />

		<div class="um-col-demon-row" style="display:none;">

				<div class="um-admin-drag-row-icons">
						<a href="#" class="um-admin-drag-rowsub-add um-admin-tipsy-n" title="<?php _e('Add Row','ultimate-member'); ?>" data-row_action="add_subrow"><i class="um-icon-plus"></i></a>
						<a href="#" class="um-admin-drag-row-edit um-admin-tipsy-n" title="<?php _e('Edit Row','ultimate-member'); ?>" data-modal="UM_edit_row" data-modal-size="normal" data-dynamic-content="um_admin_edit_field_popup" data-arg1="row" data-arg2="<?php echo get_the_ID(); ?>"><i class="um-faicon-pencil"></i></a>
						<span class="um-admin-drag-row-start"><i class="um-icon-arrow-move"></i></span>
						<a href="#" class="um-admin-tipsy-n" title="<?php _e('Delete Row','ultimate-member'); ?>" data-remove_element="um-admin-drag-row"><i class="um-faicon-trash-o"></i></a>
				</div><div class="um-admin-clear"></div>

				<div class="um-admin-drag-rowsubs">
				<div class="um-admin-drag-rowsub">

					<div class="um-admin-drag-ctrls columns">
						<a href="#" class="active" data-cols="1"></a>
						<a href="#" data-cols="2"></a>
						<a href="#" data-cols="3"></a>
					</div>

					<div class="um-admin-drag-rowsub-icons">
						<span class="um-admin-drag-rowsub-start"><i class="um-icon-arrow-move"></i></span>
						<a href="#" class="um-admin-tipsy-n" title="<?php _e('Delete Row','ultimate-member'); ?>" data-remove_element="um-admin-drag-rowsub"><i class="um-faicon-trash-o"></i></a>
					</div><div class="um-admin-clear"></div>

					<div class="um-admin-drag-col">
					</div>

					<div class="um-admin-drag-col-dynamic"></div>

					<div class="um-admin-clear"></div>

				</div>
				</div>

		</div>

		<div class="um-col-demon-subrow" style="display:none;">

			<div class="um-admin-drag-ctrls columns">
				<a href="#" class="active" data-cols="1"></a>
				<a href="#" data-cols="2"></a>
				<a href="#" data-cols="3"></a>
			</div>

			<div class="um-admin-drag-rowsub-icons">
				<span class="um-admin-drag-rowsub-start"><i class="um-icon-arrow-move"></i></span>
				<a href="#" class="um-admin-tipsy-n" title="<?php _e('Delete Row','ultimate-member'); ?>" data-remove_element="um-admin-drag-rowsub"><i class="um-faicon-trash-o"></i></a>
			</div><div class="um-admin-clear"></div>

			<div class="um-admin-drag-col">
			</div>

			<div class="um-admin-drag-col-dynamic"></div>

			<div class="um-admin-clear"></div>

		</div>


		<form action="" method="post" class="um_update_order">

			<input type="hidden" name="action" id="action" value="update_order" />

			<input type="hidden" name="form_id" id="form_id" value="<?php echo get_the_ID(); ?>" />

			<div class="um_update_order_fields">

			</div>

		</form>

		<?php

	}

}
