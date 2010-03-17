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
			var item_target = '_self';
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

/*
 * More info at: http://phpjs.org
 *
 * This is version: 3.08
 * php.js is copyright 2010 Kevin van Zonneveld.
 *
 * Portions copyright Brett Zamir (http://brett-zamir.me), Kevin van Zonneveld
 * (http://kevin.vanzonneveld.net), Onno Marsman, Theriault, Michael White
 * (http://getsprink.com), Waldo Malqui Silva, Paulo Ricardo F. Santos, Jack,
 * Jonas Raoni Soares Silva (http://www.jsfromhell.com), Philip Peterson, Ates
 * Goral (http://magnetiq.com), Legaev Andrey, Alex, Ratheous, Martijn
 * Wieringa, Nate, lmeyrick (https://sourceforge.net/projects/bcmath-js/),
 * Enrique Gonzalez, Philippe Baumann, Webtoolkit.info
 * (http://www.webtoolkit.info/), travc, Carlos R. L. Rodrigues
 * (http://www.jsfromhell.com), Jani Hartikainen, Ash Searle
 * (http://hexmen.com/blog/), Ole Vrijenhoek, stag019, d3x, Erkekjetter,
 * GeekFG (http://geekfg.blogspot.com), T.Wild, Johnny Mast
 * (http://www.phpvrouwen.nl), Michael Grier,
 * http://stackoverflow.com/questions/57803/how-to-convert-decimal-to-hex-in-javascript,
 * pilus, marrtins, Andrea Giammarchi (http://webreflection.blogspot.com),
 * WebDevHobo (http://webdevhobo.blogspot.com/), Caio Ariede
 * (http://caioariede.com), Thunder.m, Aman Gupta, Martin
 * (http://www.erlenwiese.de/), Tyler Akins (http://rumkin.com), Lars Fischer,
 * Paul Smith, Alfonso Jimenez (http://www.alfonsojimenez.com), Michael White,
 * mdsjack (http://www.mdsjack.bo.it), Pellentesque Malesuada, gettimeofday,
 * David, Joris, saulius, Robin, Steven Levithan
 * (http://blog.stevenlevithan.com), Public Domain
 * (http://www.json.org/json2.js), Kankrelune (http://www.webfaktory.info/),
 * Tim de Koning (http://www.kingsquare.nl), Arpad Ray (mailto:arpad@php.net),
 * AJ, KELAN, Sakimori, Mailfaker (http://www.weedem.fr/), Oleg Eremeev, Marc
 * Palau, Josh Fraser
 * (http://onlineaspect.com/2007/06/08/auto-detect-a-time-zone-with-javascript/),
 * Karol Kowalski, Chris, Breaking Par Consulting Inc
 * (http://www.breakingpar.com/bkp/home.nsf/0/87256B280015193F87256CFB006C45F7),
 * Mirek Slugen, majak, Felix Geisendoerfer (http://www.debuggable.com/felix),
 * gorthaur, Steve Hilder, LH, Stoyan Kyosev (http://www.svest.org/), Der
 * Simon (http://innerdom.sourceforge.net/), HKM, echo is bad, nord_ua, Ozh,
 * metjay, XoraX (http://www.xorax.info), Eugene Bulkin
 * (http://doubleaw.com/), JB, strcasecmp, strcmp, Taras Bogach, Francesco,
 * Marco, noname, class_exists, madipta, Alan C, mktime, Douglas Crockford
 * (http://javascript.crockford.com), uestla, Frank Forte, David James, Steve
 * Clay, J A R, jpfle, Marc Jansen, Paul, Hyam Singer
 * (http://www.impact-computing.com/), T. Wild, Ole Vrijenhoek
 * (http://www.nervous.nl/), Raphael (Ao RUDLER), kenneth, Brad Touesnard,
 * ChaosNo1, Subhasis Deb, Norman "zEh" Fuchs, 0m3r, Sanjoy Roy, Rob, Gilbert,
 * Bayron Guevara, paulo kuong, Orlando, duncan, sankai, hitwork, Philippe
 * Jausions (http://pear.php.net/user/jausions), Aidan Lister
 * (http://aidanlister.com/), ejsanders, Nick Callen, Brian Tafoya
 * (http://www.premasolutions.com/), johnrembo, sowberry, Yves Sucaet, Denny
 * Wardhana, Ulrich, kilops, dptr1988, john (http://www.jd-tech.net), MeEtc
 * (http://yass.meetcweb.com), Peter-Paul Koch
 * (http://www.quirksmode.org/js/beat.html), T0bsn, Tim Wiel, Bryan Elliott,
 * Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev), JT,
 * Thomas Beaucourt (http://www.webapp.fr), David Randall, DxGx, Soren Hansen,
 * lmeyrick (https://sourceforge.net/projects/bcmath-js/this.), Le Torbi,
 * djmix, Lincoln Ramsay, Linuxworld, Thiago Mata
 * (http://thiagomata.blog.com), Pedro Tainha (http://www.pedrotainha.com),
 * James, Pyerre, Jon Hohle, felix, ger, Russell Walker
 * (http://www.nbill.co.uk/), Garagoth, Andrej Pavlovic, Dino, Jamie Beck
 * (http://www.terabit.ca/), DtTvB
 * (http://dt.in.th/2008-09-16.string-length-in-bytes.html), setcookie, YUI
 * Library: http://developer.yahoo.com/yui/docs/YAHOO.util.DateLocale.html,
 * Blues at http://hacks.bluesmoon.info/strftime/strftime.js, Andreas, rem,
 * meo, Jay Klehr, Kheang Hok Chin (http://www.distantia.ca/), Luke Smith
 * (http://lucassmith.name), Rival, Amir Habibi
 * (http://www.residence-mixte.com/), Cagri Ekin, Greenseed, mk.keck, Leslie
 * Hoare, booeyOH, Ben Bryan, Michael, Christian Doebler, Kirk Strobeck, Brant
 * Messenger (http://www.brantmessenger.com/), Rick Waldron, Mick@el, Martin
 * Pool, Pierre-Luc Paour, Daniel Esteban, Christoph, Saulo Vallory, Kristof
 * Coomans (SCK-CEN Belgian Nucleair Research Centre), rezna, Tomasz
 * Wesolowski, Gabriel Paderni, Marco van Oort, Philipp Lenssen,
 * penutbutterjelly, Simon Willison (http://simonwillison.net), Anton Ongson,
 * Eric Nagel, Bobby Drake, Pul, Blues (http://tech.bluesmoon.info/), Luke
 * Godfrey, Diogo Resende, Howard Yeend, vlado houba, Jalal Berrami, Itsacon
 * (http://www.itsacon.net/), date, Billy, stensi, Cord, fearphage
 * (http://http/my.opera.com/fearphage/), Victor, Matteo, Artur Tchernychev,
 * Francois, nobbler, Fox, marc andreu, Nick Kolosov (http://sammy.ru),
 * Nathan, Arno, Scott Cariss, Slawomir Kaniecki, ReverseSyntax, Jason Wong
 * (http://carrot.org/), Mateusz "loonquawl" Zalega, Manish, Wagner B. Soares,
 * 3D-GRAF, jakes, Yannoo, gabriel paderni, daniel airton wermann
 * (http://wermann.com.br), Atli ?—r, Allan Jensen
 * (http://www.winternet.no), Benjamin Lupton, davook, Maximusya, FGFEmperor,
 * baris ozdil, Luis Salazar (http://www.freaky-media.com/), Tim de Koning,
 * taith, Matt Bradley, FremyCompany, T.J. Leahy, Greg Frazier, Valentina De
 * Rosa, Tod Gentille, Riddler (http://www.frontierwebdev.com/), Alexander M
 * Beedie
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL KEVIN VAN ZONNEVELD BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

function get_html_translation_table (table, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: noname
    // +   bugfixed by: Alex
    // +   bugfixed by: Marco
    // +   bugfixed by: madipta
    // +   improved by: KELAN
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Frank Forte
    // +   bugfixed by: T.Wild
    // +      input by: Ratheous
    // %          note: It has been decided that we're not going to add global
    // %          note: dependencies to php.js, meaning the constants are not
    // %          note: real constants, but strings instead. Integers are also supported if someone
    // %          note: chooses to create the constants themselves.
    // *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
    // *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

    var entities = {}, hash_map = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};

    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: "+useTable+' not supported');
        // return false;
    }

    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }

    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';


    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        hash_map[symbol] = entities[decimal];
    }

    return hash_map;
}


function htmlentities (string, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: nobbler
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // -    depends on: get_html_translation_table
    // *     example 1: htmlentities('Kevin & van Zonneveld');
    // *     returns 1: 'Kevin &amp; van Zonneveld'
    // *     example 2: htmlentities("foo'bar","ENT_QUOTES");
    // *     returns 2: 'foo&#039;bar'

    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();

    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
    hash_map["'"] = '&#039;';
    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(symbol).join(entity);
    }

    return tmp_str;
}