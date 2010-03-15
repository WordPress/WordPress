/**
 * WordPress Administration Custom Navigation
 * Interface JS functions
 *
 * @version 1.1.0
 *
 * @package WordPress
 * @subpackage Administration
 */

function wp_nav_menu_autocomplete( id ) {
	jQuery('#add-'+ id +' .quick-search').autocomplete(jQuery( '#add-'+ id +' .autocomplete' ).val().split('|'));

	jQuery('#add-'+ id +' .quick-search').result(function(event, data, formatted) {
		jQuery('#add-'+ id +' .list-wrap').css( 'display', 'block' );
		jQuery("#add-"+ id +" .list-wrap li:contains('" + data + "')").css( 'display', 'block' );
		jQuery('#add-'+ id +' .show-all').hide();
		jQuery('#add-'+ id +' .hide-all').show();
	});
}

/**
 * Populate the thickbox window with the selected menu items
 *
 * @param int id - the id of the menu li to edit.
 */
function wp_edit_menu_item( id ) {
	var item_type = jQuery('#menu-item-type' + id).val();
	var item_title = jQuery('#menu-item-title' + id).val();
	var item_link = jQuery('#menu-item-url' + id).val();
	var item_attr_title = jQuery('#menu-item-attr-title' + id).val();
	var item_target = jQuery('#menu-item-target' + id).val();
	var item_description = jQuery('#menu-item-description' + id).val();
	var item_classes = jQuery('#menu-item-classes' + id).val();
	var item_xfn = jQuery('#menu-item-xfn' + id).val();
	
	// Only allow custom links to be editable.
	if ( 'custom' != item_type )
		jQuery( '#edit-menu-item-url' ).attr('disabled', 'disabled' );
	
	// Populate the fields for thickbox
	jQuery( '#edit-menu-item-id' ).val(id);
	jQuery( '#edit-menu-item-title' ).val(item_title);
	jQuery( '#edit-menu-item-url' ).val(item_link);
	jQuery( '#edit-menu-item-attr-title' ).val(item_attr_title);
	jQuery( '#edit-menu-item-target' ).val(item_target);
	jQuery( "#edit-menu-item-target option[value='" + item_target  + "']" ).attr('selected', 'selected');
	jQuery( '#edit-menu-item-description' ).val(item_description);
	jQuery( '#edit-menu-item-classes' ).val(item_classes);
	jQuery( '#edit-menu-item-xfn' ).val(item_xfn);
	
	// focus
	jQuery( '#edit-menu-item-title' ).focus();
};

/**
 * Update the values for the menu item being editing
 */
function wp_update_menu_item() {
	var id = jQuery('#edit-menu-item-id').val();
	var item_title = jQuery('#edit-menu-item-title').val();
	var item_link = jQuery('#edit-menu-item-url').val();
	var item_attr_title = jQuery('#edit-menu-item-attr-title').val();
	var item_target = jQuery('#edit-menu-item-target').val();
	var item_description = jQuery('#edit-menu-item-description').val();
	var item_classes = jQuery('#edit-menu-item-classes').val();
	var item_xfn = jQuery('#edit-menu-item-xfn').val();
	
	// update menu item settings
	jQuery('.menu #menu-item' + id).find('span.item-title').html(item_title);
	jQuery('.menu #menu-item-title' + id).val(item_title);
	jQuery('.menu #menu-item-url' + id).val(item_link);
	jQuery('.menu #menu-item-attr-title' + id).val(item_attr_title);
	jQuery('.menu #menu-item-target' + id).val(item_target);
	jQuery('.menu #menu-item-description' + id).val(item_description);
	jQuery('.menu #menu-item-classes' + id).val(item_classes);
	jQuery('.menu #menu-item-xfn' + id).val(item_xfn);
	
	jQuery('.menu #menu-item' + id + ' dt:first').animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { jQuery(this).css( 'backgroundColor', '' ); }});
}

/**
 * Removes a menu item from current menu
 *
 * @param int o - the id of the menu li to remove.
 */
function wp_remove_menu_item( o ) {
	var todelete = document.getElementById('menu-item' + o);
	
	if ( todelete ) {
		// Give some feedback to the user
		jQuery( todelete ).find('dt').each(function(){
			jQuery(this).animate( { backgroundColor: '#FF3333' }, { duration: 'normal', complete: function() { jQuery(this).parent().parent().remove() } } );
		});
	}
};

/**
 * Adds the item to the menu
 *
 * @param string item_db_id - The menu item's db id.
 * @param string item_object_id - The menu item's object id.
 * @param string item_type - The menu item's object type.
 * @param string item_append - The menu item's nice name.
 * @param string item_parent_id - The menu item's parent id.
 * @param string item_title - The menu item title.
 * @param string item_url - The menu item url
 * @param string item_description - The menu item description.
 * @param string item_attr_title - The title attribute.
 * @param string item_target - The target attribute.
 * @param string item_classes - Optional. Additional CSS classes for the menu item
 * @param string item_xfn - Optional. The rel attribute.
 */
function wp_add_item_to_menu( item_db_id, item_object_id, item_type, item_append, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn ) {
	var randomnumber = wp_get_unique_menu_id();
	var hidden = wp_get_hidden_inputs( randomnumber, item_db_id, item_object_id, item_type, item_append, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn );
	
	// Adds the item in the queue
	jQuery('.menu').append('<li id="menu-item' + randomnumber + '" value="' + randomnumber + '"><div class="dropzone ui-droppable"></div><dl class="ui-droppable"><dt><span class="item-title">' + item_title + '</span><span class="item-controls"><span class="item-type">' + item_append + '</span><a class="item-edit thickbox" id="edit' + randomnumber + '" value="' + randomnumber +'" onClick="wp_edit_menu_item('+ randomnumber +')" title="' + navMenuL10n.thickbox + '" href="#TB_inline?height=540&width=300&inlineId=menu-item-settings">' + navMenuL10n.edit + '</a> | <a class="item-delete" id="delete' + randomnumber + '" value="' + randomnumber +'">Delete</a></span></dt></dl>' + hidden + '</li>');
	
	// Give some feedback to the user
	jQuery( '.menu #menu-item' + randomnumber + ' dt:first' ).animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { jQuery(this).css( 'backgroundColor', '' ); }});
	
	// Enable drag-n-drop
	wp_drag_and_drop();
	
	// Reload thickbox
	tb_init('a.thickbox, area.thickbox, input.thickbox');
};

/**
 * Grabs items from the queue and adds them to the menu.
 *
 * @param string button - a reference to the button that was clicked
 */
function wp_add_checked_items_to_menu( button ) {
	// Grab checked items
	var items = jQuery(button).siblings('.list-wrap').find(':checked');
	
	// If nothing was checked, cancel
	if ( 0 == items.length )
		return false;
	
	// Loop through each item, grab it's hidden data and add it to the menu.
	jQuery(items).each(function(){
		var item_type = jQuery(this).parent().siblings('.menu-item-type').val();
		
		if ( 'custom' == item_type ) {
			var item_attr_title = jQuery(this).parent().siblings('.menu-item-attr-title').val();
			var item_target = jQuery(this).parent().siblings('.menu-item-target').val();
			var item_classes = jQuery(this).parent().siblings('.menu-item-classes').val();
			var item_xfn = jQuery(this).parent().siblings('.menu-item-xfn').val();
		} else {
			var item_attr_title = '';
			var item_target = '_none';
			var item_classes = '';
			var item_xfn = '';
		};
		
		var item_db_id = jQuery(this).parent().siblings('.menu-item-db-id').val();
		var item_object_id = jQuery(this).parent().siblings('.menu-item-object-id').val();
		var item_append = jQuery(this).parent().siblings('.menu-item-append').val();
		var item_parent_id = jQuery(this).parent().siblings('.menu-item-parent-id').val();
		var item_title = jQuery(this).parent().siblings('.menu-item-title').val();
		var item_url = jQuery(this).parent().siblings('.menu-item-url').val();
		var item_description = jQuery(this).parent().siblings('.menu-item-description').val();
		
		if ( undefined == item_description ) {
			item_description = '';
		};
		
		// Add the menu item to the menu
		wp_add_item_to_menu( item_db_id, item_object_id, item_type, item_append, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn );
		
		// uncheck the menu item in the list
		jQuery(this).attr( 'checked', false );
	});
};

/**
 * Makes the menu items drag and droppable.
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
		zIndex: 100
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
 * Prepares menu items for POST.
 */
function wp_update_post_data() {
	var i = 0;
	
	 jQuery('.menu li').each(function(i) {
		i = i + 1;
     	var j = jQuery(this).attr('value');

     	jQuery(this).find('#menu-item-position' + j).attr('value', i);
     	jQuery(this).attr('id','menu-item' + i);
     	jQuery(this).attr('value', i);
		
     	jQuery(this).find('#menu-item-db-id' + j).attr('id','menu-item-db-id' + i);
     	jQuery(this).find('#menu-item-object-id' + j).attr('id','menu-item-object-id' + i);
		jQuery(this).find('#menu-item-append' + j).attr('id', 'menu-item-append' + i);
		jQuery(this).find('#menu-item-type' + j).attr('id', 'menu-item-type' + i);
		jQuery(this).find('#menu-item-position' + j).attr('id', 'menu-item-position' + i);

     	var p = jQuery(this).find('#menu-item-parent-id' + j).parent().parent().parent().attr('value');
		jQuery(this).find('#menu-item-parent-id' + j).attr('id','menu-item-parent-id' + i);
		if (p) {
			// Do nothing
		} else {
			// reset p to be top level
			p = 0;
		}
		jQuery(this).find('#menu-item-parent-id' + j).attr('value', p);
		
		jQuery(this).find('#menu-item-title' + j).attr('id','menu-item-title' + i);
		jQuery(this).find('#menu-item-url' + j).attr('id','menu-item-url' + i);
		jQuery(this).find('#menu-item-description' + j).attr('id','menu-item-description' + i);
		jQuery(this).find('#menu-item-classes' + j).attr('id','menu-item-classes' + i);
		jQuery(this).find('#menu-item-xfn' + j).attr('id','menu-item-xfn' + i);
		jQuery(this).find('#menu-item-description' + j).attr('id','menu-item-description' + i);
		jQuery(this).find('#menu-item-attr-title' + j).attr('id','menu-item-attr-title' + i);
		jQuery(this).find('#menu-item-target' + j).attr('id','menu-item-target' + i);
		
		jQuery('#li-count').attr( 'value', i );
   });
};

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

/**
 * Returns all the nessecary hidden inputs for each menu item.
 * 
 * @param string item_db_id - The menu item's db id.
 * @param string item_object_id - The menu item's object id.
 * @param string item_type - The menu item's object type.
 * @param string item_append - The menu item's nice name.
 * @param string item_parent_id - The menu item's parent id.
 * @param string item_title - The menu item title.
 * @param string item_url - The menu item url
 * @param string item_description - The menu item description.
 * @param string item_attr_title - The title attribute.
 * @param string item_target - The target attribute.
 * @param string item_classes - Optional. Additional CSS classes for the menu item
 * @param string item_xfn - Optional. The rel attribute.
 */
function wp_get_hidden_inputs( randomnumber, item_db_id, item_object_id, item_type, item_append, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn ) {
	var hidden = '';
	
	hidden += '<input type="hidden" name="menu-item-db-id[]" id="menu-item-db-id' + randomnumber + '" value="' + item_db_id + '" />';
	hidden += '<input type="hidden" name="menu-item-object-id[]" id="menu-item-object-id' + randomnumber + '" value="' + item_object_id + '" />';
	hidden += '<input type="hidden" name="menu-item-type[]" id="menu-item-type' + randomnumber + '" value="' + item_type + '" />';
	hidden += '<input type="hidden" name="menu-item-append[]" id="menu-item-append' + randomnumber + '" value="' + item_append + '" />';
	hidden += '<input type="hidden" name="menu-item-parent-id[]" id="menu-item-parent-id' + randomnumber + '" value="' + item_parent_id + '" />';
	hidden += '<input type="hidden" name="menu-item-position[]" id="menu-item-position' + randomnumber + '" value="' + randomnumber + '" />';
	hidden += '<input type="hidden" name="menu-item-title[]" id="menu-item-title' + randomnumber + '" value="' + item_title + '" />';
	hidden += '<input type="hidden" name="menu-item-attr-title[]" id="menu-item-attr-title' + randomnumber + '" value="' + item_attr_title + '" />';
	hidden += '<input type="hidden" name="menu-item-url[]" id="menu-item-url' + randomnumber + '" value="' + item_url + '" />';
	hidden += '<input type="hidden" name="menu-item-target[]" id="menu-item-target' + randomnumber + '" value="' + item_target + '" />';
	hidden += '<input type="hidden" name="menu-item-description[]" id="menu-item-description' + randomnumber + '" value="' + item_description + '" />';
	hidden += '<input type="hidden" name="menu-item-classes[]" id="menu-item-classes' + randomnumber + '" value="' + item_classes + '" />';
	hidden += '<input type="hidden" name="menu-item-xfn[]" id="menu-item-xfn' + randomnumber + '" value="' + item_xfn + '" />';
	
	return hidden;
}