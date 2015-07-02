/* global wpSiteIconCropData, jQuery */
(function($) {
	var jcrop_api = {},
		siteIconCrop = {

		updateCoords : function ( coords ) {
			$( '#crop-x' ).val( coords.x );
			$( '#crop-y' ).val( coords.y );
			$( '#crop-width' ).val( coords.w );
			$( '#crop-height' ).val( coords.h );

			siteIconCrop.showPreview( coords );
		},

		showPreview : function( coords ){
			var rx = 64 / coords.w,
				ry = 64 / coords.h,
				preview_rx = 16 / coords.w,
				preview_ry = 16 / coords.h;

			$( '#preview-homeicon' ).css({
				width: Math.round(rx * wpSiteIconCropData.width ) + 'px',
				height: Math.round(ry * wpSiteIconCropData.height ) + 'px',
				marginLeft: '-' + Math.round(rx * coords.x) + 'px',
				marginTop: '-' + Math.round(ry * coords.y) + 'px'
			});

			$( '#preview-favicon' ).css({
				width: Math.round( preview_rx * wpSiteIconCropData.width ) + 'px',
				height: Math.round( preview_ry * wpSiteIconCropData.height ) + 'px',
				marginLeft: '-' + Math.round( preview_rx * coords.x ) + 'px',
				marginTop: '-' + Math.floor( preview_ry* coords.y ) + 'px'
			});
		},

		ready: function() {
			jcrop_api = $.Jcrop( '#crop-image' );
			jcrop_api.setOptions({
				bgColor: 'transparent',
				aspectRatio: 1,
				onSelect: siteIconCrop.updateCoords,
				onChange: siteIconCrop.updateCoords,
				trueSize: [ wpSiteIconCropData.width, wpSiteIconCropData.height ],
				minSize: [ wpSiteIconCropData.min_size, wpSiteIconCropData.min_size ]
			});
			jcrop_api.animateTo([wpSiteIconCropData.init_x, wpSiteIconCropData.init_y, wpSiteIconCropData.init_size, wpSiteIconCropData.init_size]);
		}

	};

	siteIconCrop.ready();

})(jQuery);