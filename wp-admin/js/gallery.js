jQuery(function($) {
	var gallerySortable;
	var gallerySortableInit = function() {
		gallerySortable = $('#media-items').sortable( {
			items: '.media-item',
			placeholder: 'sorthelper',
			update: galleryReorder
		} );
	}

	// When an update has occurred, adjust the order for each item
	var galleryReorder = function(e, sort) {
		jQuery.each(sort['instance'].toArray(), function(i, id) {
			jQuery('#' + id + ' .menu_order input')[0].value = i;
		});
	}

	// initialize sortable
	gallerySortableInit();
});
