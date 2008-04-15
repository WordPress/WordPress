<?php

require('admin.php' );

$post_ID = (int) $_GET['post'];

if ( $post_ID ? !current_user_can( 'edit_post', $post_ID ) : !current_user_can( 'edit_posts' ) )
	die();
	
ob_start();
$popular_ids = wp_popular_terms_checklist('category');
ob_end_clean();

?>


	<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
		<?php dropdown_categories( 0, 0, $popular_ids ); ?>
	</ul>


	<div id="category-add-hidden" style="display: none;">
		<input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" tabindex="3" />
		<?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => __('Parent category'), 'tab_index' => 3 ) ); ?>
		<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php _e( 'Add' ); ?>" tabindex="3" />
		<?php wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
		<span id="category-ajax-response"></span>
	</div>
