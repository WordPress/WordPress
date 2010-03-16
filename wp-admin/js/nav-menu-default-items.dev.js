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
	
	// Delete AYS
	$('#update-nav-menu .deletion').click(function(){
		if ( confirm( navMenuL10n.warnDelete ) ) {
			return true;
		} else {
			return false;
		};
	});
	
	// Handle Save Button Clicks
	$('#save_menu').click(function(){
		return wp_update_post_data();
	});
	
	// close postboxes that should be closed
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	
	// postboxes setup
	postboxes.add_postbox_toggles('menus');
	
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
	$('#menu-container .item-delete').live( 'click', function(e){
		return wp_remove_menu_item( $(this).attr('value') );
	});
	
	// Update menu item settings (thickbox)
	$('#update-menu-item').click(function(){
		wp_update_menu_item();
		return tb_remove();
	});
	
	// Close thickbox
	$('#cancel-save').click(function(){
		return tb_remove();
	});
	
	// Show All Button
	$('.show-all').click(function(e){
		jQuery(e.currentTarget).parent().siblings('.list-wrap').css( 'display', 'block' );
		jQuery(e.currentTarget).parent().siblings('.list-wrap').find('li').css( 'display', 'block' );
		jQuery(e.currentTarget).hide();
		jQuery(e.currentTarget).siblings('.hide-all').show();
	});
	
	// Hide All Button
	$('.hide-all').click(function(e){
		jQuery(e.currentTarget).parent().siblings('.list-wrap').css( 'display', 'none' );
		jQuery(e.currentTarget).parent().siblings('.list-wrap').find('li').css( 'display', 'none' );
		jQuery(e.currentTarget).hide();
		jQuery(e.currentTarget).siblings('.show-all').show();
	});

	// Add menu items into the menu
	$('.add-to-menu').click(function(e){
		return wp_add_checked_items_to_menu(e.currentTarget);
	});

	// Create a new link then add it to the menu
	$('#add-custom-links .add-to-menu a').click(function(e){
		var link_url = jQuery(e.currentTarget).parent().parent().find('#custom-menu-item-url').val();
		var link_name = jQuery(e.currentTarget).parent().parent().find('#custom-menu-item-name').val();
		
		// Add link to menu
		wp_add_item_to_menu( 0, '', 'custom', navMenuL10n.custom, 0, link_name, link_url, '', '', '_self', '', '' );
	});
});