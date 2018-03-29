jQuery(document).ready(function() {
	
	jQuery(document).on('click', '.um-popup-overlay', function(){

		remove_Modal();
	});
	
	jQuery(document).on('click', '.um-modal-overlay, a[data-action="um_remove_modal"]', function(){

		um_remove_modal();
	});

	jQuery(document).on('click', 'a[data-modal^="um_"], span[data-modal^="um_"], .um-modal a', function(e){
		e.preventDefault();
		return false;
	});
	
	jQuery(document).on('click', '.um-modal .um-single-file-preview a.cancel', function(e){
		e.preventDefault();
		
		var parent = jQuery(this).parents('.um-modal-body');
		var src = jQuery(this).parents('.um-modal-body').find('.um-single-fileinfo a').attr('href');
		
		parent.find('.um-single-file-preview').hide();
		
		parent.find('.ajax-upload-dragdrop').show();
		
		parent.find('.um-modal-btn.um-finish-upload').addClass('disabled');
		
		um_modal_responsive();
		
		jQuery.ajax({
			url: um_scripts.ajaxurl,
			type: 'post',
			data: {
				action: 'ultimatemember_remove_file',
				src: src
			}
		});
		
		return false;
	});
	
	jQuery(document).on('click', '.um-modal .um-single-image-preview a.cancel', function(e){
		e.preventDefault();
		
		var parent = jQuery(this).parents('.um-modal-body');
		var src = jQuery(this).parents('.um-modal-body').find('.um-single-image-preview img').attr('src');
		
		jQuery('img.cropper-hidden').cropper('destroy');
		
		parent.find('.um-single-image-preview img').attr('src', '');
		
		parent.find('.um-single-image-preview').hide();
		
		parent.find('.ajax-upload-dragdrop').show();
		
		parent.find('.um-modal-btn.um-finish-upload').addClass('disabled');
		
		um_modal_responsive();
		
		jQuery.ajax({
			url: um_scripts.ajaxurl,
			type: 'post',
			data: {
				action: 'ultimatemember_remove_file',
				src: src
			}
		});
		
		return false;
	});
	
	jQuery(document).on('click', '.um-finish-upload.file', function(){
		
		var key = jQuery(this).attr('data-key');
		var preview = jQuery(this).parents('.um-modal-body').find('.um-single-file-preview').html();
		
		um_remove_modal();
		
		jQuery('.um-single-file-preview[data-key='+key+']').fadeIn().html( preview );
		
		jQuery('.um-single-file-preview[data-key='+key+']').parents('.um-field').find('.um-btn-auto-width').html( jQuery(this).attr('data-change') );
		
		jQuery('.um-single-file-preview[data-key='+key+']').parents('.um-field').find('input[type=hidden]').val( jQuery('.um-single-file-preview[data-key='+key+']').parents('.um-field').find('.um-single-fileinfo a').attr('href') );
	});

	jQuery(document).on('click', '.um-finish-upload.image', function(){
		
		var elem = jQuery(this);
		var key = jQuery(this).attr('data-key');
		var img_c = jQuery(this).parents('.um-modal-body').find('.um-single-image-preview');
		var src = img_c.find('img').attr('src');
		var coord = img_c.attr('data-coord');

		if ( jQuery(this).parents('#um_upload_single').attr('data-user_id')  ) {
			var user_id = jQuery(this).parents('#um_upload_single').attr('data-user_id');
		} else {
			var user_id = 0;
		}
		
		if ( coord ) {
		
			jQuery(this).html( jQuery(this).attr('data-processing') ).addClass('disabled');

			jQuery.ajax({
				url: um_scripts.ajaxurl,
				type: 'POST',
				data: {
					action: 'ultimatemember_resize_image',
					src : src,
					coord : coord,
					user_id : user_id,
					key: key
				},
				success: function(data){
				
					d = new Date();
					
					if ( key == 'profile_photo') {
						jQuery('.um-profile-photo-img img').attr('src', data + "?"+d.getTime());
					}
					
					if ( key == 'cover_photo') {
						jQuery('.um-cover-e').empty().html('<img src="' + data + "?"+d.getTime() + '" alt="" />');
						if ( jQuery('.um').hasClass('um-editing') ) {
							jQuery('.um-cover-overlay').show();
						}
					}
					
					jQuery('.um-single-image-preview[data-key='+key+']').fadeIn().find('img').attr('src', data + "?"+d.getTime());
					um_remove_modal();
					jQuery('.um-single-image-preview[data-key='+key+']').parents('.um-field').find('.um-btn-auto-width').html( elem.attr('data-change') );
					
					jQuery('.um-single-image-preview[data-key='+key+']').parents('.um-field').find('input[type=hidden]').val( data );

				}
			});
		
		} else {

					d = new Date();
					jQuery('.um-single-image-preview[data-key='+key+']').fadeIn().find('img').attr('src', src + "?"+d.getTime());
					um_remove_modal();
					jQuery('.um-single-image-preview[data-key='+key+']').parents('.um-field').find('.um-btn-auto-width').html( elem.attr('data-change') );
					
					jQuery('.um-single-image-preview[data-key='+key+']').parents('.um-field').find('input[type=hidden]').val( src );

			
		}
	});
	
	jQuery(document).on('click', 'a[data-modal^="um_"], span[data-modal^="um_"]', function(e){

		var modal_id = jQuery(this).attr('data-modal');
		
		if ( jQuery(this).data('modal-size')  ) {
			var size = jQuery(this).data('modal-size');
		} else {
			var size = 'normal';
		}
		
		if ( jQuery(this).data('modal-copy') ) {
		
			jQuery('#' + modal_id).html( jQuery(this).parents('.um-field').find('.um-modal-hidden-content').html() );
			
			if ( jQuery(this).parents('.um-profile-photo').attr('data-user_id') ) {
				jQuery('#' + modal_id).attr('data-user_id', jQuery(this).parents('.um-profile-photo').attr('data-user_id') );
			}
			
			if ( jQuery(this).parents('.um-cover').attr('data-ratio') ) {
				jQuery('#' + modal_id).attr('data-ratio',  jQuery(this).parents('.um-cover').attr('data-ratio')  );
			}
			
			if ( jQuery(this).parents('.um-cover').attr('data-user_id') ) {
				jQuery('#' + modal_id).attr('data-user_id',  jQuery(this).parents('.um-cover').attr('data-user_id')  );
			}
			um_new_modal( modal_id, size );
			
		} else {

			um_new_modal( modal_id, size );
			
		}
	});

});