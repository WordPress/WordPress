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
	
	// Handle Save Button Clicks
	$('#save_menu').click(function(){
		wp_update_post_data();
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
		wp_edit_menu_item( $(this).attr('value') );
	});
	
	// Delete menu item
	$('#menu-container .item-delete').live( 'click', function(){
		wp_remove_menu_item( $(this).attr('value') );
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
		tb_remove();
	});
	
	// Show All Button
	$('.show-all').click(function(){
		$(this).offsetParent().find('#add-buttons-actions').attr( 'style','margin-bottom: 10px;' );
		$(this).offsetParent().find('.list-wrap').css( 'display','block' );
		$(this).siblings('.quick-search').attr( 'value', '' );		
		$(this).offsetParent().find('.list-wrap dt').css( 'display', 'block' );
		$(this).hide();
		$(this).siblings('.hide-all').show();
	});
	
	// Hide All Button
	$('.hide-all').click(function(){
		$(this).offsetParent().find('#add-buttons-actions').attr( 'style','margin-bottom: 0px;' );
		$(this).offsetParent().find('.list-wrap').css( 'display','none' );
		$(this).siblings('.quick-search').attr( 'value', 'Search' );
		$(this).offsetParent().find('.list-wrap dt').css( 'display', 'none' );
		$(this).hide();
		$(this).siblings('.show-all').show();
	});
	
	// Add queued menu items into the menu
	$('.enqueue a').click(function(){
		wp_add_queued_items_to_menu(this);
	});
	
	// Create the link, add it to the menu + available links section
	$('#add-custom-link .add-to-menu a').click(function(){
		var link_url = $(this).offsetParent().find('#menu-item-url').val();
		var link_name = $(this).offsetParent().find('#menu-item-name').val();
		var links = $(this).offsetParent().find('#available-links input:checked');
		
		// If links are checked in the available links section, add them to the menu
		if ( links ) {
			for (var i = 0; i < links.length; i++) {
				wp_add_item_to_menu( 'Custom', links[i].name, links[i].value, links[i].id.substring( 5, links[i].id.length ), 0, '' );
			};
			
			// Reset the checkboxes;
			$(links).attr('checked', false);
		};
				
		// Don't save the link if it was left with it's default settings
		if ( 'http://' == link_url || 'Menu Item' == link_name )
			return;
		
		// Parameters to send off
		params = {
			action: 'add-menu-link',
			link_url: link_url,
			link_name: link_name,
		};
		
		// save the link
		$.post( ajaxurl, params, function(response) {
			if ( null == response || '-1' == response )
				return false;
			
			params.link_id = response;
			
			// add it to the available links section
			// wp_update_links_list( params );
			
			// and update the Menu with the new link
			wp_add_item_to_menu( 'Custom', params.link_name, params.link_url, params.link_id, 0, '' );
		}, 'json');
	});
	
	// Add dropzones
    $('#menu li').prepend('<div class="dropzone"></div>');

	// Make menu items draggable
	$('#menu li').draggable({
		    handle: ' > dl',
		    opacity: .8,
		    addClasses: false,
		    helper: 'clone',
		    zIndex: 100
	});

	// Make items droppable
	$('#menu dl, #menu .dropzone').droppable({
		accept: '#menu li',
		tolerance: 'pointer',
		
		drop: function(e, ui) {
			var li = $(this).parent();
			var child = !$(this).hasClass('dropzone');
			
			// Add UL to first child
        	if ( child && li.children('ul').length == 0 ) {
            	li.append('<ul id="sub-menu" />');
        	}
        	// Make it draggable
        	if ( child ) {
            	li.children('ul').append( ui.draggable );
        	} else {
            	li.before( ui.draggable );
        	}

        	li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });

			var draggablevalue = ui.draggable.attr('value');
			var droppablevalue = li.attr('value');
			
        	li.find('#menu-' + draggablevalue).find('#parent' + draggablevalue).val(droppablevalue);
			$(this).parent().find('dt').removeAttr('style');
			
			$(this).parent().find('div:first').removeAttr('style');
    	},
    	
		over: function() {
    		
    		if ( 'dropzone ui-droppable' == $(this).attr('class') ) {
				// Add child
    			$(this).parent().find('dt:first').css('background', 'none').css('height', '50px');
				
    		} else if ($(this).attr('class') == 'ui-droppable') {
				// Add above
    			$(this).parent().find('dt:first').css('background', '#d8d8d8');
				
    		} else {
				// do nothing
    		}
    		var parentid = $(this).parent().attr('id');
       	},
    	out: function() {
        	$(this).parent().find('dt').removeAttr('style');
        	$(this).parent().find('div:first').removeAttr('style');
        	$(this).filter('.dropzone').css({ borderColor: '' });
    	},
    	deactivate: function() {
			//
    	}
	});
});