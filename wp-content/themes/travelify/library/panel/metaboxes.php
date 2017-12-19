<?php
/**
 * Travelify Meta Boxes
 *
 */

 add_action( 'add_meta_boxes', 'travelify_add_custom_box' );
/**
 * Add Meta Boxes.
 *
 * Add Meta box in page and post post types.
 */
function travelify_add_custom_box() {
	add_meta_box(
		'siderbar-layout',							  										//Unique ID
		__( 'Select layout for this specific Page only ( Note: This setting only reflects if page Template is set as Default Template and Blog Type Templates.)', 'travelify' ),   	//Title
		'travelify_sidebar_layout',                   							//Callback function
		'page'                                          							//show metabox in pages
	);
	add_meta_box(
		'siderbar-layout',							  										//Unique ID
		__( 'Select layout for this specific Post only', 'travelify' ),   	//Title
		'travelify_sidebar_layout',                   							//Callback function
		'post'                                          							//show metabox in posts
	);
}

/****************************************************************************************/

global $sidebar_layout;
$sidebar_layout = array(
							'default-sidebar' 		=> array(
															'id'        => 'travelify_sidebarlayout',
															'value'     => 'default',
															'label'     => __( 'Default Layout Set in', 'travelify' ).' '.'<a href="'.esc_url( home_url( '/' ) ).'/wp-admin/themes.php?page=theme_options" target="_blank">'.__( 'Theme Settings', 'travelify' ).'</a>',
															'thumbnail' => ' '
															),
							'no-sidebar'         => array(
															'id'        => 'travelify_sidebarlayout',
															'value'     => 'no-sidebar',
															'label'     => __( 'No sidebar', 'travelify' ),
															'thumbnail' => get_template_directory_uri() . '/library/panel/images/no-sidebar.png'
															),
							'no-sidebar-full-width' => array(
															'id'        => 'travelify_sidebarlayout',
															'value'     => 'no-sidebar-full-width',
															'label'     => __( 'No sidebar, Full Width', 'travelify' ),
															'thumbnail' => get_template_directory_uri() . '/library/panel/images/no-sidebar-fullwidth.png'
															),
							'no-sidebar-one-column' => array(
															'id'        => 'travelify_sidebarlayout',
															'value'     => 'no-sidebar-one-column',
															'label'     => __( 'No Sidebar, One Column', 'travelify' ),
															'thumbnail' => get_template_directory_uri() . '/library/panel/images/one-column.png'
															),
							'left-sidebar' => array(
															'id'        => 'travelify_sidebarlayout',
															'value'     => 'left-sidebar',
															'label'     => __( 'Left sidebar', 'travelify' ),
															'thumbnail' => get_template_directory_uri() . '/library/panel/images/left-sidebar.png'
															),
							'right-sidebar' => array(
															'id'        => 'travelify_sidebarlayout',
															'value'     => 'right-sidebar',
															'label'     => __( 'Right sidebar', 'travelify' ),
															'thumbnail' => get_template_directory_uri() . '/library/panel/images/right-sidebar.png'
															)
						);

/****************************************************************************************/

/**
 * Displays metabox to for sidebar layout
 */
function travelify_sidebar_layout() {
	global $sidebar_layout, $post;
	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'custom_meta_box_nonce' );

	// Begin the field table and loop  ?>
	<table id="sidebar-metabox" class="form-table" width="100%">
		<tbody>
			<tr>
				<?php
				foreach ($sidebar_layout as $field) {
					$meta = get_post_meta( $post->ID, $field['id'], true );
					if(empty( $meta ) ){
						$meta='default';
					}
					if( ' ' == $field['thumbnail'] ): ?>
						<label class="description">
						<input type="radio" name="<?php echo $field['id']; ?>" value="<?php echo $field['value']; ?>" <?php checked( $field['value'], $meta ); ?>/>&nbsp;&nbsp;<?php echo $field['label']; ?>
						</label>
					<?php else: ?>
						<td>
							<label class="description">
							<span><img src="<?php echo esc_url( $field['thumbnail'] ); ?>" width="136" height="122" alt="" /></span></br>
							<input type="radio" name="<?php echo $field['id']; ?>" value="<?php echo $field['value']; ?>" <?php checked( $field['value'], $meta ); ?>/>&nbsp;&nbsp;<?php echo $field['label']; ?>
							</label>
						</td>
					<?php endif;
				} // end foreach
				?>
			</tr>
		</tbody>
	</table>
	<?php
}

/****************************************************************************************/


add_action('save_post', 'travelify_save_custom_meta');
/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function travelify_save_custom_meta( $post_id ) {
	global $sidebar_layout, $post;

	// Verify the nonce before proceeding.
    if ( !isset( $_POST[ 'custom_meta_box_nonce' ] ) || !wp_verify_nonce( $_POST[ 'custom_meta_box_nonce' ], basename( __FILE__ ) ) )
      return;

	// Stop WP from clearing custom fields on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
      return;

	if ('page' == $_POST['post_type']) {
      if (!current_user_can( 'edit_page', $post_id ) )
         return $post_id;
   }
   elseif (!current_user_can( 'edit_post', $post_id ) ) {
      return $post_id;
   }

	foreach ($sidebar_layout as $field) {
		//Execute this saving function
		$old = get_post_meta( $post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	} // end foreach
}