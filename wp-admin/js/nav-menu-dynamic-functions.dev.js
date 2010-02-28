/**
 * WordPress Administration Custom Navigation
 * Interface JS functions
 *
 * @version 1.1.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Adds a link to the available links section
 *
 * @param object e - An object recieved via ajax
 */
function wp_update_links_list(e) {
	var link = '<li><dl><dt><label class="item-title"><input type="checkbox" id="link-'+ e.link_id +'" name="'+ e.link_name +'" value="'+ e.link_url +'" />'+ e.link_name +'</label></dt></dl></li>';
		
	// Prepend the link to the available links section
	jQuery('#available-links .list').prepend( link );
	
	// Give feedback to the user
	jQuery('#available-links .list #link-' + e.link_id).parent().animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { jQuery(this).css( 'backgroundColor', '' ); }});
}

/**
 * Populate the thickbox window with the selected menu items
 *
 * @param int o - the id of the menu li to edit.
 */
function wp_edit_menu_item( id ) {
	var itemType = jQuery('#item-type' + id).val();
	var itemTitle = jQuery('#item-title' + id).val();
	var itemURL = jQuery('#item-url' + id).val();
	var itemAttrTitle = jQuery('#item-attr-title' + id).val();
	var itemTarget = jQuery('#item-target' + id).val();
	var itemDesc = jQuery('#item-description' + id).val();
	
	// Disable the ability to edit the url for page and category items
	if ( 'page' == itemType || 'category' == itemType )
		jQuery( '#edit-item-url' ).attr('disabled', 'disabled' );
	
	// Populate the fields for thickbox
	jQuery( '#edit-item-id' ).val(id);
	jQuery( '#edit-item-title' ).val(itemTitle);
	jQuery( '#edit-item-url' ).val(itemURL);
	jQuery( '#edit-item-attr-title' ).val(itemAttrTitle);
	jQuery( '#edit-item-target' ).val(itemTarget);
	jQuery( "#edit-item-target option[value='" + itemTarget  + "']" ).attr('selected', 'selected');
	jQuery( '#edit-item-description' ).val(itemDesc);
};

/**
 * Update the values for the menu item being editing
 */
function wp_update_menu_item() {
	var id = jQuery('#edit-item-id').val();
	var itemTitle = jQuery('#edit-item-title').val();
	var itemURL = jQuery('#edit-item-url').val();
	var itemAttrTitle = jQuery('#edit-item-attr-title').val();
	var itemTarget = jQuery('#edit-item-target').val();
	var itemDesc = jQuery('#edit-item-description').val();
	
	// update menu item settings	
	jQuery('#menu-' + id).find('.item-title:first').html(itemTitle);
	jQuery('#item-title' + id).val(itemTitle);
	jQuery('#item-url' + id).val(itemURL);
	jQuery('#item-attr-title' + id).val(itemAttrTitle);
	jQuery('#item-target' + id).val(itemTarget);
	jQuery('#item-description' + id).val(itemDesc);
}

/**
 * Removes a menu item from current menu
 *
 * @param int o - the id of the menu li to remove.
 */
function wp_remove_menu_item( o ) {
	var todelete = document.getElementById('menu-' + o);
	
	if ( todelete ) {
		// Give some feedback to the user
		jQuery( todelete ).find('dt').each(function(){
			jQuery(this).animate( { backgroundColor: '#FF3333' }, { duration: 'normal', complete: function() { jQuery(this).parent().parent().remove() } } );
		});
		
		wp_update_post_data();
	}
};

/**
 * Prepares menu items for POST
 */
function wp_update_post_data() {
	var i = 0;
	
	 jQuery('.menu li').each(function(i) {
		i = i + 1;
     	var j = jQuery(this).attr('value');

     	jQuery(this).find('#item-position' + j).attr('value', i);
     	jQuery(this).attr('id','menu-' + i);
     	jQuery(this).attr('value', i);

     	jQuery(this).find('#item-dbid' + j).attr('name','item-dbid' + i);
     	jQuery(this).find('#item-dbid' + j).attr('id','item-dbid' + i);

		jQuery(this).find('#item-postmenu' + j).attr('name','item-postmenu' + i);
     	jQuery(this).find('#item-postmenu' + j).attr('id','item-postmenu' + i);

     	var p = jQuery(this).find('#item-parent' + j).parent().parent().parent().attr('value');

		jQuery(this).find('#item-parent' + j).attr('name','item-parent' + i);
		jQuery(this).find('#item-parent' + j).attr('id','item-parent' + i);
		
		if (p) {
			// Do nothing
		} else {
			// reset p to be top level
			p = 0;
		}

		jQuery(this).find('#item-parent' + j).attr('value', p);

		jQuery(this).find('#item-title' + j).attr('name','item-title' + i);
		jQuery(this).find('#item-title' + j).attr('id','item-title' + i);

		jQuery(this).find('#item-url' + j).attr('name','item-url' + i);
		jQuery(this).find('#item-url' + j).attr('id','item-url' + i);

		jQuery(this).find('#item-description' + j).attr('name','item-description' + i);
		jQuery(this).find('#item-description' + j).attr('id','item-description' + i);

		jQuery(this).find('#item-attr-title' + j).attr('name','item-attr-title' + i);
		jQuery(this).find('#item-attr-title' + j).attr('id','item-attr-title' + i);

		jQuery(this).find('#item-target' + j).attr('name','item-target' + i);
		jQuery(this).find('#item-target' + j).attr('id','item-target' + i);

		jQuery(this).find('#item-position' + j).attr('name', 'item-position' + i);
		jQuery(this).find('#item-position' + j).attr('id', 'item-position' + i);

		jQuery(this).find('#item-type' + j).attr('name', 'item-type' + i);
		jQuery(this).find('#item-type' + j).attr('id', 'item-type' + i);

		jQuery('#li-count').attr( 'value', i );
   });
};

/**
 * Adds the item to the menu
 *
 * @param string id - The menu item's id
 * @param string additemtype - Page, Category, or Custom.
 * @param string itemtext - menu text.
 * @param string itemurl - url of the menu.
 * @param int itemid - menu id.
 * @param int itemparentid - default 0.
 * @param string itemdescription - the description of the menu item.
 */
function wp_add_item_to_menu( item_type, item_title, item_url, item_id, item_parent_id, item_description ) {
	var randomnumber = wp_get_unique_menu_id();
	
	// Adds the item in the queue
	jQuery('.menu').append('<li id="menu-' + randomnumber + '" value="' + randomnumber + '"><div class="dropzone ui-droppable"></div><dl class="ui-droppable"><dt><span class="item-title">' + item_title + '</span><span class="item-controls"><span class="item-type">' + item_type + '</span><a class="item-edit thickbox" id="edit' + randomnumber + '" value="' + randomnumber +'" onClick="wp_edit_menu_item('+ randomnumber +')" title="' + navMenuL10n.thickbox + '" href="#TB_inline?height=380&width=300&inlineId=menu-item-settings">' + navMenuL10n.edit + '</a> | <a class="item-delete" id="delete' + randomnumber + '" value="' + randomnumber +'">Delete</a></span></dt></dl><input type="hidden" name="item-postmenu' + randomnumber + '" id="item-postmenu' + randomnumber + '" value="' + item_id + '" /><input type="hidden" name="item-parent' + randomnumber + '" id="item-parent' + randomnumber + '" value="' + item_parent_id + '" /><input type="hidden" name="item-title' + randomnumber + '" id="item-title' + randomnumber + '" value="' + item_title + '" /><input type="hidden" name="item-url' + randomnumber + '" id="item-url' + randomnumber + '" value="' + item_url + '" /><input type="hidden" name="item-description' + randomnumber + '" id="item-description' + randomnumber + '" value="' + item_description + '" /><input type="hidden" name="item-position' + randomnumber + '" id="item-position' + randomnumber + '" value="' + randomnumber + '" /><input type="hidden" name="item-type' + randomnumber + '" id="item-type' + randomnumber + '" value="' + item_type + '" /><input type="hidden" name="item-attr-title' + randomnumber + '" id="item-attr-title' + randomnumber + '" value="' + item_title + '" /><input type="hidden" name="item-target' + randomnumber + '" id="item-target' + randomnumber + '" value="0" /></li>');
	
	// Give some feedback to the user
	jQuery( '.menu #menu-' + randomnumber + ' dt:first' ).animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { jQuery(this).css( 'backgroundColor', '' ); }});
	
	// Enable drag-n-drop
	wp_drag_and_drop();
	
	wp_update_post_data();
	
	// Reload thickbox
	tb_init('a.thickbox, area.thickbox, input.thickbox');
};

/*
 * Queues items in perperation for appendToList
 *
 * @param string type - Page, Category, or Custom.
 * @param string link - menu text.
 * @param string url - url of the menu.
 * @param int id - menu id.
 * @param int parent_id - default 0.
 * @param string description - the description of the menu item.
*/
function wp_update_queue( e, item_type, item_title, item_url, item_id, item_parent_id, item_description ) {
	var randomnumber = wp_get_unique_menu_id();
	var menu_item_id = jQuery(e).attr('id');
	
	if ( jQuery(e).attr('checked') ) {
		// Add menu item to the queue
		jQuery('#queue').append('<li id="menu-' + randomnumber + '" value="' + randomnumber + '"><div class="dropzone ui-droppable"></div><dl class="ui-droppable"><dt><span class="item-title">' + item_title + '</span><span class="item-controls"><span class="item-type">' + item_type + '</span><a class="item-edit thickbox" id="edit' + randomnumber + '" value="' + randomnumber +'" onClick="wp_edit_menu_item('+ randomnumber +')" title="' + navMenuL10n.thickbox + '" href="#TB_inline?height=380&width=300&inlineId=menu-item-settings">' + navMenuL10n.edit + '</a> | <a class="item-delete" id="delete' + randomnumber + '" value="' + randomnumber +'">Delete</a></span></dt></dl><input type="hidden" name="item-postmenu' + randomnumber + '" id="item-postmenu' + randomnumber + '" value="' + item_id + '" /><input type="hidden" name="item-parent' + randomnumber + '" id="item-parent' + randomnumber + '" value="' + item_parent_id + '" /><input type="hidden" name="item-title' + randomnumber + '" id="item-title' + randomnumber + '" value="' + item_title + '" /><input type="hidden" name="item-url' + randomnumber + '" id="item-url' + randomnumber + '" value="' + item_url + '" /><input type="hidden" name="item-description' + randomnumber + '" id="item-description' + randomnumber + '" value="' + item_description + '" /><input type="hidden" name="item-position' + randomnumber + '" id="item-position' + randomnumber + '" value="' + randomnumber + '" /><input type="hidden" name="item-type' + randomnumber + '" id="item-type' + randomnumber + '" value="' + item_type + '" /><input type="hidden" name="item-attr-title' + randomnumber + '" id="item-attr-title' + randomnumber + '" value="' + item_title + '" /><input type="hidden" name="item-target' + randomnumber + '" id="item-target' + randomnumber + '" value="0" /><input type="hidden" name="item-id' + randomnumber + '" id="item-id' + randomnumber + '" value="'+ menu_item_id +'" /></li>');
		
	} else {
		// Remove the item from the queue
		if ( menu_item_id == jQuery('#queue li input[name^="item-id"]' ).val() ) {
			jQuery('#queue li input[name^="item-id"]' ).parent().remove()
		};
	};
};

/**
 * Grabs items from the queue and adds them to the menu.
 *
 * @param string button - a reference of the button that was clicked
 */
function wp_add_queued_items_to_menu( button ) {	
	// Grab items in queue
	var items = jQuery('#queue').children();
	
	// Empty Queue
	jQuery('#queue').empty();
		
	// Appends HTML to the menu
	jQuery('.menu').append( items );

	// Give some feedback to the user
	jQuery(items).each(function(){
		jQuery(this).find('dt').animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { jQuery(this).css( 'backgroundColor', '' ); }});
	});
	
	// Uncheck the checkboxes in the list
	jQuery(button).offsetParent().find('.list-container input').attr('checked', false);
	
	wp_update_post_data();
	
	// Enable drag-n-drop
	wp_drag_and_drop();
	
	// Reload thickbox
	tb_init('a.thickbox, area.thickbox, input.thickbox');
};

/**
 * Allow the items in the Menu to be dragged and dropped.
 */
function wp_drag_and_drop() {
	// Make sure all li's have dropzones
	jQuery('.menu li').each(function(){
		if ( !jQuery(this).children('.dropzone').attr('class') ) {
			jQuery(this).prepend('<div class="dropzone"></div>');
		};
	});

	// make menu item draggable
	jQuery('.menu li').draggable({
		handle: ' > dl',
		opacity: .8,
		addClasses: false,
		helper: 'clone',
		zIndex: 100,
	});

	// make menu item droppable
	jQuery('.menu li dl, .menu li .dropzone').droppable({
		accept: '.menu li',
		tolerance: 'pointer',
		drop: function(e, ui) {
			var li = jQuery(this).parent();
			var child = !jQuery(this).hasClass('dropzone');
			
			// Append UL to first child
			if ( child && li.children('ul').length == 0 ) {
				li.append( '<ul class="sub-menu" />' );
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
			jQuery(this).parent().find('dt').removeAttr('style');
			jQuery(this).parent().find('div:first').removeAttr('style');

		},
		over: function() {
	    		// Add child
	    		if ( jQuery(this).attr('class') == 'dropzone ui-droppable' ) {
	    			jQuery(this).parent().find('div:first').css('background', 'none').css('height', '50px');
	    		}
	    		// Add above
	    		else if ( jQuery(this).attr('class') == 'ui-droppable' ) {
	    			jQuery(this).parent().find('dt:first').css('background', '#d8d8d8');
	    		} else {
					// do nothing
	    		}
	    		var parentid = jQuery(this).parent().attr('id');

	       	},
	    	out: function() {
	        	jQuery(this).parent().find('dt').removeAttr('style');
	        	jQuery(this).parent().find('div:first').removeAttr('style');
	        	jQuery(this).filter('.dropzone').css({ borderColor: '' });
	    	}
		}
	);
}

/**
 * Gets a unique number based on how many items are in the menu
 */
function wp_get_unique_menu_id() {
	var count = jQuery('.menu li').length + 1;
	var randomnumber = count;
	var validatetest = 0;

	try {
		var test = document.getElementById( 'menu-' + randomnumber.toString() ).value;
	}
	catch ( err ) {
		validatetest = 1;
	}

	while ( validatetest == 0 ) {
		randomnumber = randomnumber + 1;
		try {
			var test2 = document.getElementById( 'menu-' + randomnumber.toString() ).value;
		}
		catch ( err ) {
			validatetest = 1;
		}
	}
	return randomnumber;
}