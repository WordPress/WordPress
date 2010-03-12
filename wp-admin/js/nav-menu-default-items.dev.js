/**
 * WordPress Administration Custom Navigation
 * Interface $ functions
 *
 * @version 2.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Init Functions
 */
jQuery(document).ready(function($){
	
	wp_drag_and_drop();
	
	wp_update_post_data();
	
	// Handle Save Button Clicks
	$('#save_menu').click(function(){
		return wp_update_post_data();
	});
		
	// Clear the quick search textbox
	$('.quick-search').click(function(){
		$(this).attr( 'value', '' );
	});
	
	// Quick Search submit
	$('.quick-search-submit').click(function(){
		$(this).siblings('.quick-search').search();
	});
	
	// Edit menu item
	$('#menu-container .item-edit').click(function(){
		return wp_edit_menu_item( $(this).attr('value') );
	});
	
	// Delete menu item
	$('#menu-container .item-delete').live( 'click', function(){
		return wp_remove_menu_item( $(this).attr('value') );
	});
	
	// Update menu item settings (thickbox)
	$('#update-menu-item').click(function(){
		wp_update_menu_item();
		tb_remove();
		
		// Give feedback to the user
		var id = $('#edit-item-id').val();
		$('#menu-' + id + ' dt:first').animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { jQuery(this).css( 'backgroundColor', '' ); }});
	});
	
	// Close thickbox
	$('#cancel-save').click(function(){
		return tb_remove();
	});
	
	// Show All Button
	$('.show-all').click(function(){
		$(this).offsetParent().find('#add-buttons-actions').attr( 'style','margin-bottom: 10px;' );
		$(this).offsetParent().find('.list-wrap').css( 'display','block' );
		$(this).siblings('.quick-search').attr( 'value', '' );		
		$(this).offsetParent().find('.list-wrap li').css( 'display', 'block' );
		$(this).hide();
		$(this).siblings('.hide-all').show();
	});
	
	// Hide All Button
	$('.hide-all').click(function(){
		$(this).offsetParent().find('#add-buttons-actions').attr( 'style','margin-bottom: 0px;' );
		$(this).offsetParent().find('.list-wrap').css( 'display','none' );
		$(this).siblings('.quick-search').attr( 'value', 'Search' );
		$(this).offsetParent().find('.list-wrap li').css( 'display', 'none' );
		$(this).hide();
		$(this).siblings('.show-all').show();
	});

	// Add menu item to queue
	$('.list input').click(function(){

		var item_type = jQuery(this).parent().siblings('.item-type').val();
		var item_title = jQuery(this).parent().siblings('.item-title').val();
		var item_url = jQuery(this).parent().siblings('.item-url').val();
		var item_id = jQuery(this).parent().siblings('.item-dbid').val();
		var item_parent_id = jQuery(this).parent().siblings('.item-parent').val();
		var item_description = jQuery(this).parent().siblings('.item-description').val();
		
		return wp_update_queue( $(this), item_type, item_title, item_url, item_id, item_parent_id, item_description );
	});

	// Add queued menu items into the menu
	$('.enqueue a').click(function(){
		return wp_add_queued_items_to_menu(this);
	});

	// Create the link, add it to the menu + available links section
	$('#add-custom-link .add-to-menu a').click(function(){
		var link_url = $(this).offsetParent().find('#menu-item-url').val();
		var link_name = $(this).offsetParent().find('#menu-item-name').val();
		
		wp_add_queued_items_to_menu( this );
				
		// Don't save the link if it was left with it's default settings
		if ( 'http://' == link_url || 'Menu Item' == link_name )
			return;

		// and update the Menu with the new link
		wp_add_item_to_menu( 'custom', link_name, link_url, 0, 0, '' );
	});
});