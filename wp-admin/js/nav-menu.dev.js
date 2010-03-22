/**
 * WordPress Administration Navigation Menu
 * Interface JS functions
 *
 * @version 2.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

var wpNavMenu;

(function($) {
	
	wpNavMenu = {
		
		// Functions that run on init.
		init : function() {
			
			wpNavMenu.initial_meta_boxes();
			
			wpNavMenu.drag_and_drop();
			
			// Delete AYS
			$('#update-nav-menu .deletion').click(function(){
				if ( confirm( navMenuL10n.warnDelete ) ) {
					return true;
				} else {
					return false;
				};
			});

			// Handle Save Button Clicks
			$('#update-nav-menu').submit(function(){
				wpNavMenu.update_post_data();
			});

			// Handle some return keypresses
			$('#create-menu-name').keypress(function(e){
				if ( 13 == e.keyCode ) {
					$('#create-menu-button').click();
					return false;
				}
			});

			$('#custom-menu-item-url, #custom-menu-item-name').keypress(function(e){
				if ( 13 == e.keyCode ) {
					$('#add-custom-links a.button').click();
					return false;
				}
			}).focus(function(){
				if ( $(this).val() == $(this).attr('defaultValue') && $(this).attr('id') != 'custom-menu-item-url' ) {
					$(this).val('');
				}
			}).blur(function(){
				if ( $(this).val() == '' ) {
					$(this).val($(this).attr('defaultValue'));
				}
			});

			$('#create-menu-name').focus(function(){
				if ( $(this).val() == $(this).attr('defaultValue') ) {
					$(this).val('');
				}
			}).blur(function(){
				if ( $(this).val() == '' ) {
					$(this).val($(this).attr('defaultValue'));
				}
			});

			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

			// postboxes setup
			postboxes.add_postbox_toggles('nav-menus');

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
				wpNavMenu.edit_menu_item( $(this).attr('value') );
			});

			// Delete menu item
			$('#menu-container .item-delete').click(function(){
				wpNavMenu.remove_menu_item( $(this).attr('value') );
			});

			// Update menu item settings (thickbox)
			$('#update-menu-item').click(function(){
				wpNavMenu.update_menu_item();
				tb_remove();
			});

			// Close thickbox
			$('#cancel-save').click(function(){
				tb_remove();
			});

			// Show All Button
			$('.show-all').click(function(e){
				$(e.currentTarget).parent().parent().siblings('.list-wrap').css( 'display', 'block' );
				$(e.currentTarget).parent().parent().siblings('.list-wrap').find('li').css( 'display', 'block' );
				$(e.currentTarget).hide();
				$(e.currentTarget).siblings('.hide-all').show();
			});

			// Hide All Button
			$('.hide-all').click(function(e){
				$(e.currentTarget).parent().parent().siblings('.list-wrap').css( 'display', 'none' );
				$(e.currentTarget).parent().parent().siblings('.list-wrap').find('li').css( 'display', 'none' );
				$(e.currentTarget).hide();
				$(e.currentTarget).siblings('.show-all').show();
			});

			// Add menu items into the menu
			$('.add-to-menu').click(function(e){
				wpNavMenu.add_checked_items_to_menu(e.currentTarget);
			});

			// Create a new link then add it to the menu
			$('#add-custom-links .add-to-menu a').click(function(e){
				// Add link to menu
				if ( $('#custom-menu-item-url').val() == $('#custom-menu-item-url').attr('defaultValue') )
					return; // Do not allow "http://" submissions to go through

				wpNavMenu.add_custom_link( $('#custom-menu-item-name').val(), $('#custom-menu-item-url').val() );
				
				// Reset the fields back to their defaults
				$('#custom-menu-item-name').val($('#custom-menu-item-name').attr('defaultValue'));
				$('#custom-menu-item-url' ).val($('#custom-menu-item-url' ).attr('defaultValue')).focus();
			});
		},
		
		add_custom_link : function( link_name, link_url ) {
			var params = {
				action: 'save-custom-link',
				link_name: link_name,
				link_url: link_url
			}
			
			$.post( ajaxurl, params, function(link_id) {
				if ( '-1' == link_id )
					return;

				wpNavMenu.add_to_menu( link_id, link_id, 'custom', 'custom', navMenuL10n.custom, 0, link_name, link_url, '', '', '_self', '', '' );
			}, 'json');
		},
		
		/**
		 * In combination with the php function wp_initial_nav_menu_meta_boxes(),
		 * this function limits the metaboxes for first time users to just links, pages and cats.
		 */
		initial_meta_boxes : function() {
			var hidden = $('#hidden-metaboxes').val().split( ',' );

			if ( '' != hidden ) {
				for ( var i = 0; i < hidden.length; i++ ) {
					$( '#' + hidden[i] ).attr( 'style', 'display: none;' );
					$( '#' + hidden[i] + '-hide' ).attr( 'checked', false );
				};
			};
		},
		
		// Makes the menu items drag and droppable.
		drag_and_drop : function() {
			// Make sure all li's have dropzones
			$('.menu li').each(function(){
				if ( !$(this).children('.dropzone').attr('class') ) {
					$(this).prepend('<div class="dropzone"></div>');
				};
			});

			// make menu item draggable
			$('.menu li').draggable({
				handle: ' > dl',
				opacity: .8,
				addClasses: false,
				helper: 'clone',
				zIndex: 100
			});

			// make menu item droppable
			$('.menu li dl, .menu li .dropzone').droppable({
				accept: '.menu li',
				tolerance: 'pointer',
				drop: function(e, ui) {
					var li = $(this).parent();
					var child = !$(this).hasClass('dropzone');

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
					$(this).parent().find('dt').removeAttr('style');
					$(this).parent().find('div:first').removeAttr('style');

				},
				over: function(e) {
			    	// Add child
					if ( $(this).attr('class') == 'dropzone ui-droppable' ) {
		    			$(this).parent().find('div:first').css({ background: '#f5f5f5', border: '1px dashed #bbb', margin: '10px 0px', height: '40px' });
		    		}
					// Add above
		    		else if ( $(this).attr('class') == 'ui-droppable' ) {
						$(this).parent().find('dt:first').css('background', '#d8d8d8');
		    		} else {
						// Do nothing
		    		}
		       	},
			    out: function() {
		        	$(this).parent().find('dt').removeAttr('style');
		        	$(this).parent().find('div:first').removeAttr('style');
		        	$(this).filter('.dropzone').css({ borderColor: '' });
		    	}
			});
		},
	
		// Prepares menu items for POST.
		update_post_data : function() {
			var i = 0; // counter

			$('.menu li').each(function(){
				i = i + 1; // the menu order for each item

				var j = $(this).attr('value'); // reference to the current menu item (e.g. li#menu-item + j)

				// Grab the menu item id
				var id = $(this).children('input[name=menu-item-db-id[]]').val();

				// Update the li value to equal the menu order
				$(this).attr('value', i);

				// Update the position
				$(this).children('input[name=menu-item-position[]]').attr( 'value', i );

				// Update the parent id
				var pid = $(this).parent('.sub-menu').siblings('input[name=menu-item-object-id[]]').val();
				
				if ( undefined == pid ) {
					pid = 0;
				};

				$(this).children('input[name=menu-item-parent-id[]]').attr( 'value', pid );

				// Update the menu item count
				$('#li-count').attr( 'value', i );
			});
		},
		
		/**
		 * Enables autocomplete for nav menu types.
		 *
		 * @param int id - the id of the menu item type.
		 */
		autocomplete : function( id ) {
			$('#add-'+ id +' .quick-search').autocomplete( $( '#add-'+ id +' .autocomplete' ).val().split('|') );

			$('#add-'+ id +' .quick-search').result(function( event, data, formatted ) {
				$('#add-'+ id +' .list-wrap').css( 'display', 'block' );
				$("#add-"+ id +" .list-wrap li:contains('" + data + "')").css( 'display', 'block' );
				$('#add-'+ id +' .show-all').hide();
				$('#add-'+ id +' .hide-all').show();
			});
		},
		
		/**
		 * Populate the thickbox window with the selected menu items
		 *
		 * @param int id - the id of the menu item to edit.
		 */
		edit_menu_item : function( id ) {
			var item_type = $('#menu-item-' + id).children('input[name=menu-item-type[]]').val();
			var item_title = $('#menu-item-' + id).children('input[name=menu-item-title[]]').val();
			var item_link = $('#menu-item-' + id).children('input[name=menu-item-url[]]').val();
			var item_attr_title = $('#menu-item-' + id).children('input[name=menu-item-attr-title[]]').val();
			var item_target = $('#menu-item-' + id).children('input[name=menu-item-target[]]').val();
			var item_description = $('#menu-item-' + id).children('input[name=menu-item-description[]]').val();
			var item_classes = $('#menu-item-' + id).children('input[name=menu-item-classes[]]').val();
			var item_xfn = $('#menu-item-' + id).children('input[name=menu-item-xfn[]]').val();

			// Only allow custom links to be editable.
			if ( 'custom' != item_type )
				$( '#edit-menu-item-url' ).attr('disabled', 'disabled' );

			// Populate the fields for thickbox
			$( '#edit-menu-item-id' ).val(id);
			$( '#edit-menu-item-title' ).val(item_title);
			$( '#edit-menu-item-url' ).val(item_link);
			$( '#edit-menu-item-attr-title' ).val(item_attr_title);
			$( '#edit-menu-item-target' ).val(item_target);
			$( "#edit-menu-item-target option[value='" + item_target  + "']" ).attr('selected', 'selected');
			$( '#edit-menu-item-description' ).val(item_description);
			$( '#edit-menu-item-classes' ).val(item_classes);
			$( '#edit-menu-item-xfn' ).val(item_xfn);

			// @todo: focus on #edit-menu-item-title
		},
		
		/**
		 * Update the values for the menu item being editing
		 */
		update_menu_item : function() {
			var id = $('#edit-menu-item-id').val();
			var item_title = $('#edit-menu-item-title').val();
			var item_link = $('#edit-menu-item-url').val();
			var item_attr_title = $('#edit-menu-item-attr-title').val();
			var item_target = $('#edit-menu-item-target').val();
			var item_description = $('#edit-menu-item-description').val();
			var item_classes = $('#edit-menu-item-classes').val();
			var item_xfn = $('#edit-menu-item-xfn').val();

			// update menu item settings
			$('.menu #menu-item-' + id).find('span.item-title:first').html(item_title);

			$('#menu-item-' + id).children('input[name=menu-item-title[]]').val(item_title);
			$('#menu-item-' + id).children('input[name=menu-item-url[]]').val(item_link);
			$('#menu-item-' + id).children('input[name=menu-item-attr-title[]]').val(item_attr_title);
			$('#menu-item-' + id).children('input[name=menu-item-target[]]').val(item_target);
			$('#menu-item-' + id).children('input[name=menu-item-description[]]').val(item_description);
			$('#menu-item-' + id).children('input[name=menu-item-classes[]]').val(item_classes);
			$('#menu-item-' + id).children('input[name=menu-item-xfn[]]').val(item_xfn);

			$('.menu #menu-item-' + id + ' dt:first').animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { $(this).css( 'backgroundColor', '' ); }});
		},
		
		/**
		 * Removes a menu item from current menu
		 *
		 * @param int id - the id of the menu item to remove.
		 */
		remove_menu_item : function( id ) {
			var todelete = $('#menu-item-' + id);

			if ( todelete ) {
				// Give some feedback to the user
				$( todelete ).find('dt').each(function(){
					$(this).animate( { backgroundColor: '#FF3333' }, { duration: 'normal', complete: function() { $(this).parent().parent().remove() } } );
				});
			}
		},
		
		/**
		 * Adds the item to the menu
		 *
		 * @param string item_db_id - The menu item's db id.
		 * @param string item_object_id - The menu item's object id.
		 * @param string item_object - The menu item's object name.
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
		add_to_menu : function( item_db_id, item_object_id, item_object, item_type, item_append, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn ) {
			var randomnumber = $('.menu li').length + 1;
			var hidden = wpNavMenu.get_hidden_inputs( randomnumber, item_db_id, item_object_id, item_object, item_type, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn );
			
			// Adds the item to the menu
			$('.menu').append('<li id="menu-item-' + randomnumber + '" value="' + randomnumber + '"><div class="dropzone ui-droppable"></div><dl class="ui-droppable"><dt><span class="item-title">' + item_title + '</span><span class="item-controls"><span class="item-type">' + item_append + '</span><a class="item-edit thickbox" id="edit' + randomnumber + '" value="' + randomnumber +'" onclick="wpNavMenu.edit_menu_item('+ randomnumber +');" title="' + navMenuL10n.thickbox + '" href="#TB_inline?height=540&width=300&inlineId=menu-item-settings">' + navMenuL10n.edit + '</a> | <a class="item-delete" id="delete' + randomnumber + '" value="' + randomnumber +'" onclick="wpNavMenu.remove_menu_item('+ randomnumber +');">Delete</a></span></dt></dl>' + hidden + '</li>');

			// Give some feedback to the user
			$( '.menu #menu-item-' + randomnumber + ' dt:first' ).animate( { backgroundColor: '#FFFF33' }, { duration: 'normal', complete: function() { $(this).css( 'backgroundColor', '' ); }});

			// Enable drag-n-drop
			wpNavMenu.drag_and_drop();

			// Reload thickbox
			tb_init('a.thickbox, area.thickbox, input.thickbox');
		},
		
		/**
		 * Grabs items from the queue and adds them to the menu.
		 *
		 * @param string button - a reference to the button that was clicked
		 */
		add_checked_items_to_menu : function( button ) {
			// Grab checked items
			var items = $(button).parent().siblings('.list-wrap').find(':checked');

			// If nothing was checked, cancel
			if ( 0 == items.length )
				return false;

			// Loop through each item, grab it's hidden data and add it to the menu.
			$(items).each(function(){
				var item_type = $(this).parent().siblings('.menu-item-type').val();

				if ( 'custom' == item_type ) {
					var item_attr_title = $(this).parent().siblings('.menu-item-attr-title').val();
					var item_target = $(this).parent().siblings('.menu-item-target').val();
					var item_classes = $(this).parent().siblings('.menu-item-classes').val();
					var item_xfn = $(this).parent().siblings('.menu-item-xfn').val();
				} else {
					var item_attr_title = '';
					var item_target = '_self';
					var item_classes = '';
					var item_xfn = '';
				};

				var item_db_id = $(this).parent().siblings('.menu-item-db-id').val();
				var item_object_id = $(this).parent().siblings('.menu-item-object-id').val();
				var item_object = $(this).parent().siblings('.menu-item-object').val();
				var item_append = $(this).parent().siblings('.menu-item-append').val();
				var item_parent_id = $(this).parent().siblings('.menu-item-parent-id').val();
				var item_title = $(this).parent().siblings('.menu-item-title').val();
				var item_url = $(this).parent().siblings('.menu-item-url').val();
				var item_description = $(this).parent().siblings('.menu-item-description').val();

				if ( undefined == item_description ) {
					item_description = '';
				};

				if ( undefined == item_attr_title ) {
					item_attr_title = '';
				};

				// Add the menu item to the menu
				wpNavMenu.add_to_menu( item_db_id, item_object_id, item_object, item_type, item_append, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn );

				// uncheck the menu item in the list
				$(this).attr( 'checked', false );
			});
		},
		
		/**
		 * Returns all the nessecary hidden inputs for each menu item.
		 *
		 * @param string item_db_id - The menu item's db id.
		 * @param string item_object_id - The menu item's object id.
		 * @param string item_object - The menu item's object name.
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
		get_hidden_inputs : function( randomnumber, item_db_id, item_object_id, item_object, item_type, item_parent_id, item_title, item_url, item_description, item_attr_title, item_target, item_classes, item_xfn ) {
			var hidden = '';

			hidden += '<input type="hidden" name="menu-item-db-id[]" value="' + item_db_id + '" />';
			hidden += '<input type="hidden" name="menu-item-object-id[]" value="' + item_object_id + '" />';
			hidden += '<input type="hidden" name="menu-item-object[]" value="' + item_object + '" />';
			hidden += '<input type="hidden" name="menu-item-type[]" value="' + item_type + '" />';
			hidden += '<input type="hidden" name="menu-item-parent-id[]" value="' + item_parent_id + '" />';
			hidden += '<input type="hidden" name="menu-item-position[]" value="' + randomnumber + '" />';
			hidden += '<input type="hidden" name="menu-item-title[]" value="' + item_title + '" />';
			hidden += '<input type="hidden" name="menu-item-attr-title[]" value="' + item_attr_title + '" />';
			hidden += '<input type="hidden" name="menu-item-url[]" value="' + item_url + '" />';
			hidden += '<input type="hidden" name="menu-item-target[]" value="' + item_target + '" />';
			hidden += '<input type="hidden" name="menu-item-description[]" value="' + item_description + '" />';
			hidden += '<input type="hidden" name="menu-item-classes[]" value="' + item_classes + '" />';
			hidden += '<input type="hidden" name="menu-item-xfn[]" value="' + item_xfn + '" />';

			return hidden;
		}
	}
	
	$(document).ready(function($){ wpNavMenu.init(); });
})(jQuery);