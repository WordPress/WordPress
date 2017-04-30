jQuery(document).ready(function() {
	jQuery('.easy-instagram-thumbnail-wrapper').each(function() {
		var f = jQuery(this).children('form').first();
		var elem_id = jQuery(this).attr('id');

		jQuery.ajax({
			url: Easy_Instagram_Settings.ajaxurl,
			data: f.serialize(),
			success: function(data) {
				var obj = jQuery.parseJSON(data);
				if( "SUCCESS" == obj.status ) {
					jQuery('#'+elem_id).html(obj.output);
					if('' != Easy_Instagram_Settings.after_ajax_content_load) {
						eval(Easy_Instagram_Settings.after_ajax_content_load);
					}
				}
			}
		});
	});
});