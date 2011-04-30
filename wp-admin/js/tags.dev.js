jQuery(document).ready(function($) {

	$('.delete-tag').live('click', function(e){
		var t = $(this), tr = t.parents('tr'), r = true, data;
		if ( 'undefined' != showNotice )
			r = showNotice.warn();
		if ( r ) {
			data = t.attr('href').replace(/[^?]*\?/, '').replace(/action=delete/, 'action=delete-tag');
			$.post(ajaxurl, data, function(r){
				if ( '1' == r ) {
					$('#ajax-response').empty();
					tr.fadeOut('normal', function(){ tr.remove(); });
					// Remove the term from the parent box and tag cloud
					$('select#parent option[value="' + data.match(/tag_ID=(\d+)/)[1] + '"]').remove();
					$('a.tag-link-' + data.match(/tag_ID=(\d+)/)[1]).remove();
				} else if ( '-1' == r ) {
					$('#ajax-response').empty().append('<div class="error"><p>' + tagsl10n.noPerm + '</p></div>');
					tr.children().css('backgroundColor', '');
				} else {
					$('#ajax-response').empty().append('<div class="error"><p>' + tagsl10n.broken + '</p></div>');
					tr.children().css('backgroundColor', '');
				}
			});
			tr.children().css('backgroundColor', '#f33');
		}
		return false;
	});

	$('#submit').click(function(){
		var form = $(this).parents('form');

		if ( !validateForm( form ) )
			return false;

		$.post(ajaxurl, $('#addtag').serialize(), function(r){
		   $('#ajax-response').empty();
			var res = wpAjax.parseAjaxResponse(r, 'ajax-response');
			if ( ! res )
				return;

			var parent = form.find('select#parent').val();

			if ( parent > 0 && $('#tag-' + parent ).length > 0 ) // If the parent exists on this page, insert it below. Else insert it at the top of the list.
				$('.tags #tag-' + parent).after( res.responses[0].supplemental['noparents'] ); // As the parent exists, Insert the version with - - - prefixed
			else
				$('.tags').prepend( res.responses[0].supplemental['parents'] ); // As the parent is not visible, Insert the version with Parent - Child - ThisTerm

			$('.tags .no-items').remove();

			if ( form.find('select#parent') ) {
				// Parents field exists, Add new term to the list.
				var term = res.responses[1].supplemental;

				// Create an indent for the Parent field
				var indent = '';
				for ( var i = 0; i < res.responses[1].position; i++ )
					indent += '&nbsp;&nbsp;&nbsp;';

				form.find('select#parent option:selected').after('<option value="' + term['term_id'] + '">' + indent + term['name'] + '</option>');
			}

			$('input[type="text"]:visible, textarea:visible', form).val('');
		});

		return false;
	});

});
