jQuery(function($) {
	var gallerySortable;
	var gallerySortableInit = function() {
		gallerySortable = $('#media-items').sortable( {
			items: '.media-item',
			placeholder: 'sorthelper',
			axis: 'y',
			distance: 2,
			update: galleryReorder
		} );
	}

	// When an update has occurred, adjust the order for each item
	var galleryReorder = function(e, sort) {
		jQuery.each(sort['element'].sortable('toArray'), function(i, id) {
			jQuery('#' + id + ' .menu_order input')[0].value = (1+i);
		});
	}

	// initialize sortable
	gallerySortableInit();
});

jQuery(document).ready(function($){
	$('.menu_order_input').each(function(){
		if ( this.value == '0' ) this.value = '';
	});
});
