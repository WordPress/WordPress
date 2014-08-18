jQuery(function ($) {

	is_slide_add_image = false;

	function update_order_numbers() {
		
		$('.slides').each(function(){
			$(this).children('.slide').each(function(i){
				$(this).find('td.slide_order .circle').first().html(i+1);
			});
		});

	}

	function update_slide_order_field() {
		$('#unique_slides_order').val($('.slides').sortable('toArray').toString());
	}

	function current_selected_slide_id() {
		return window.selected_slide_id;
	}

	function update_selected_slide_id( id ) {
		window.selected_slide_id = id;
	}

	function insert_into_editor(html) {
		var b, a = typeof (tinymce) != "undefined",
                f = typeof (QTags) != "undefined";
        if (!wpActiveEditor) {
                if (a && tinymce.activeEditor) {
                        b = tinymce.activeEditor;
                        wpActiveEditor = b.id
                } else {
                        if (!f) {
                                return false
                        }
                }
        } else {
                if (a) {
                        if (tinymce.activeEditor && (tinymce.activeEditor.id == "mce_fullscreen" || tinymce.activeEditor.id == "wp_mce_fullscreen")) {
                                b = tinymce.activeEditor
                        } else {
                                b = tinymce.get(wpActiveEditor)
                        }
                }
        }
        if (b && !b.isHidden()) {
                if (tinymce.isIE && b.windowManager.insertimagebookmark) {
                        b.selection.moveToBookmark(b.windowManager.insertimagebookmark)
                }
                if (html.indexOf("[caption") === 0) {
                        if (b.wpSetImgCaption) {
                                html = b.wpSetImgCaption(c)
                        }
                } else {
                        if (html.indexOf("[gallery") === 0) {
                                if (b.plugins.wpgallery) {
                                        html = b.plugins.wpgallery._do_gallery(c)
                                }
                        } else {
                                if (html.indexOf("[embed") === 0) {
                                        if (b.plugins.wordpress) {
                                                html = b.plugins.wordpress._setEmbed(c)
                                        }
                                }
                        }
                }
                b.execCommand("mceInsertContent", false, html)
        } else {
                if (f) {
                        QTags.insertContent(c)
                } else {
                        document.getElementById(wpActiveEditor).value += c
                }
        }
	}

	$('#submitslider #delete-action a').click(function(e) {

		if( ! confirm( 'Move to Trash. Are you sure ?' ) )
			return false;

	});

	$('.delete_slide').live('click', function(e) {

		if( ! confirm( 'Delete slide. Are you sure ?' ) )
			return false;

		var selector = $('.slide#' + $(this).data('id') );

		selector.fadeOut('slow', function() {
			selector.remove();
			update_order_numbers();
			$('.slide_meta td.slide_order').mouseover();
			update_slide_order_field();
		});

	});

	$('.edit_slide').live('click', function() {

		var edit_form = $('.slide#' + $(this).data('id') + ' .slide_form_mask' );
		
		if ( edit_form.css('display') == 'none' ) {

			$('.slide#' + $(this).data('id')).addClass('form_open');

			edit_form.slideDown();
			update_selected_slide_id($(this).data('id'));

		} else {

			$('.slide#' + $(this).data('id')).removeClass('form_open');

			edit_form.slideUp();
			update_selected_slide_id(null);
		}

	});

	$('#add_slide_button').click(function() {

		$('.no_slides_message').hide();

		var slide_id = $('#next_slide_id').val();
		var label_name_attr = 'slides[' + slide_id + '][label]';
		var image_name_attr = 'slides[' + slide_id + '][image]';
		var type_name_attr = 'slides[' + slide_id + '][type]';
		var attachment_name_attr = 'slides[' + slide_id + '][attachment]';
		var html_name_attr = 'slides[' + slide_id + '][html]';

		$('#new_slide_template .slide').attr('id', slide_id);

		$('#new_slide_template .edit_slide').attr('data-id', slide_id);
		
		$('#new_slide_template .add_image').attr('data-id', slide_id);
		
		$('#new_slide_template .delete_slide').attr('data-id', slide_id);
		
		$('#new_slide_template .slide_order span.circle').attr('data-id', slide_id);
		
		$('#new_slide_template .slide_edit_close .edit_slide').attr('data-id', slide_id);
		
		$('#new_slide_template .slide_label_input').attr('data-id', slide_id);

		$('#new_slide_template .slide_type select').attr('data-id', slide_id);

		$('#new_slide_template .slide_label_input').attr('name', label_name_attr);
	
		$('#new_slide_template .slide_type select').attr('name', type_name_attr);
		
		$('#new_slide_template .slide_attachment').attr('name', attachment_name_attr);
		
		$('#new_slide_template .slide_html textarea').attr('name', html_name_attr);

		var template = $('#new_slide_template').html();

		$('.slides').append(template);

		update_order_numbers();

		update_selected_slide_id(slide_id);


		$('#new_slide_template .slide_label_input').attr('name', '');
		
		$('#new_slide_template .slide_type').attr('name', '');
		$('#new_slide_template .slide_attachment').attr('name', '');
		$('#new_slide_template .slide_type select').attr('name', '');
		$('#new_slide_template .slide_html textarea').attr('name', '');

		$('#next_slide_id').val(Number($('#next_slide_id').val())+1);

	});




	$('.slide_meta td.slide_order').live('mouseover', function() {
			
			var slides = $(this).closest('.slides');
			
			if( slides.hasClass('sortable') ) return false;
			
			slides.addClass('sortable').sortable({
				update: function(event, ui){
					update_order_numbers();
					update_slide_order_field();
				},
				handle: 'td.slide_order',
				cursor: 'move',
				axis: "y",
				revert: true
			});
	});

	$('.slide_label').live('hover', '.slide_label', function() {
		$(this).parent().find('.row_options').show();
	});

	$('.slide_label').live('mouseleave', function() {
		$(this).parent().find('.row_options').hide();
	});

	$('.add_image').live('click', function() {

		var image_input = $(this).parent().find('.slide_image_input');
		var image_preview = $(this).parent().find('.slide_image_preview img');

		if ( image_input.val() !== $(image_preview).attr('src') ) {

			$(image_preview).attr('src', image_input.val());
			return false;
		}

		update_selected_slide_id( $(this).data('id') );

		is_slide_add_image = true;

		tb_show('Add Image', 'media-upload.php?referer=post.php&post_id=0&slider_id=' + $('#post_ID').val() +  '&type=image&TB_iframe=true', false);

		return false;
	});

	window.send_to_editor = function(html) {

		if ( is_slide_add_image ) {

			var image_url = $('img',html).attr('src');

			if ( image_url === undefined  )
				image_url = $(html).attr('src');

			classes = $('img', html).attr('class');

			if ( classes === undefined )
				classes = $(html).attr('class');

			id = classes.replace(/(.*?)wp-image-/, '');

			var image_input = '.slide#' + current_selected_slide_id() + ' .slide_image_input';
			var image_preview = '.slide#' + current_selected_slide_id() + ' .slide_image_preview img';
			var attachment_input = '.slide#' + current_selected_slide_id() + ' .slide_attachment';

			$(image_input).val(image_url);
			$(image_preview).attr('src', image_url);

			$(attachment_input).val(id);

			is_slide_add_image = false;

		} else {
			insert_into_editor(html);
		}

		tb_remove();

	};


	$('.slide_label_input').live('change', function() {

		label = $('.slide#' + $(this).data('id') + ' .slide_label strong a' );

		label.text($(this).val());
	});

	$('.slide_type select').live('change', function() {

		image = $('.slide#' + $(this).data('id') + ' tr.slide_image' );
		html = $('.slide#' + $(this).data('id') + ' tr.slide_html' );

		if ( $(this).val() == 'image' ) {
			image.show();
			html.hide();
			return;
		}

		if ( $(this).val() == 'html' ) {
			html.show();
			image.hide();
		}

		return;

	});

	$('.slides .slide_type select').each(function(index, elem) {
		
		image = $('.slide#' + $(this).data('id') + ' tr.slide_image' );
		html = $('.slide#' + $(this).data('id') + ' tr.slide_html' );

		if ( $(this).val() == 'image' ) {
			image.show();
			html.hide();
			return;
		}

		if ( $(this).val() == 'html' ) {
			html.show();
			image.hide();
		}

		return;
		
	});

	$('#shortcode_text_input').click(function() {
		$(this).select();
	});
	
	if ( pagenow == 'edit-ssp_slider' ) {
		
		$('.add-new-h2').addClass('ssp-button');
		(function($){
			$('#wpbody .wrap').wrapInner('<div id="ssp-col-left" />');
			$('#wpbody .wrap').wrapInner('<div id="ssp-cols" />');
			$('#ssp-col-right').removeClass('hidden').prependTo('#ssp-cols');
			
			$('#ssp-col-left > .icon32').insertBefore('#ssp-cols');
			$('#ssp-col-left > h2').insertBefore('#ssp-cols');
		})(jQuery);

	}

	if ( pagenow == 'ssp_slider' )
		$('.add-new-h2').hide();

});