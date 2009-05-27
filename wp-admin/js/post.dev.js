// return an array with any duplicate, whitespace or values removed
function array_unique_noempty(a) {
	var out = [];
	jQuery.each( a, function(key, val) {
		val = jQuery.trim(val);
		if ( val && jQuery.inArray(val, out) == -1 )
			out.push(val);
		} );
	return out;
}

function new_tag_remove_tag() {
	var id = jQuery( this ).attr( 'id' ), num = id.split('-check-num-')[1], taxbox = jQuery(this).parents('.tagsdiv'), current_tags = taxbox.find( '.the-tags' ).val().split(','), new_tags = [];
	delete current_tags[num];

	jQuery.each( current_tags, function(key, val) {
		val = jQuery.trim(val);
		if ( val ) {
			new_tags.push(val);
		}
	});

	taxbox.find('.the-tags').val( new_tags.join(',').replace(/\s*,+\s*/, ',').replace(/,+/, ',').replace(/,+\s+,+/, ',').replace(/,+\s*$/, '').replace(/^\s*,+/, '') );

	tag_update_quickclicks(taxbox);
	return false;
}

function tag_update_quickclicks(taxbox) {
	if ( jQuery(taxbox).find('.the-tags').length == 0 )
		return;

	var current_tags = jQuery(taxbox).find('.the-tags').val().split(',');
	jQuery(taxbox).find('.tagchecklist').empty();
	shown = false;

	jQuery.each( current_tags, function( key, val ) {
		var txt, button_id;

		val = jQuery.trim(val);
		if ( !val.match(/^\s+$/) && '' != val ) {
			button_id = jQuery(taxbox).attr('id') + '-check-num-' + key;
 			txt = '<span><a id="' + button_id + '" class="ntdelbutton">X</a>&nbsp;' + val + '</span> ';
 			jQuery(taxbox).find('.tagchecklist').append(txt);
 			jQuery( '#' + button_id ).click( new_tag_remove_tag );
		}
	});
	if ( shown )
		jQuery(taxbox).find('.tagchecklist').prepend('<strong>'+postL10n.tagsUsed+'</strong><br />');
}

function tag_flush_to_text(id, a) {
	a = a || false;
	var taxbox, text, tags, newtags;

	taxbox = jQuery('#'+id);
	text = a ? jQuery(a).text() : taxbox.find('input.newtag').val();

	// is the input box empty (i.e. showing the 'Add new tag' tip)?
	if ( taxbox.find('input.newtag').hasClass('form-input-tip') && ! a )
		return false;

	tags = taxbox.find('.the-tags').val();
	newtags = tags ? tags + ',' + text : text;

	// massage
	newtags = newtags.replace(/\s+,+\s*/g, ',').replace(/,+/g, ',').replace(/,+\s+,+/g, ',').replace(/,+\s*$/g, '').replace(/^\s*,+/g, '');
	newtags = array_unique_noempty(newtags.split(',')).join(',');
	taxbox.find('.the-tags').val(newtags);
	tag_update_quickclicks(taxbox);

	if ( ! a )
		taxbox.find('input.newtag').val('').focus();

	return false;
}

function tag_save_on_publish() {
	jQuery('.tagsdiv').each( function(i) {
		if ( !jQuery(this).find('input.newtag').hasClass('form-input-tip') )
        	tag_flush_to_text(jQuery(this).parents('.tagsdiv').attr('id'));
		} );
}

function tag_press_key( e ) {
	if ( 13 == e.which ) {
		tag_flush_to_text(jQuery(e.target).parents('.tagsdiv').attr('id'));
		return false;
	}
};

function tag_init() {

	jQuery('.ajaxtag').show();
    jQuery('.tagsdiv').each( function(i) {
        tag_update_quickclicks(this);
    } );

    // add the quickadd form
    jQuery('.ajaxtag input.tagadd').click(function(){tag_flush_to_text(jQuery(this).parents('.tagsdiv').attr('id'));});
    jQuery('.ajaxtag input.newtag').focus(function() {
        if ( !this.cleared ) {
            this.cleared = true;
            jQuery(this).val( '' ).removeClass( 'form-input-tip' );
        }
    });

    jQuery('.ajaxtag input.newtag').blur(function() {
        if ( this.value == '' ) {
            this.cleared = false;
            jQuery(this).val( postL10n.addTag ).addClass( 'form-input-tip' );
        }
    });

    // auto-save tags on post save/publish
    jQuery('#publish').click( tag_save_on_publish );
    jQuery('#save-post').click( tag_save_on_publish );

    // catch the enter key
    jQuery('.ajaxtag input.newtag').keypress( tag_press_key );
}

var commentsBox, tagCloud;
(function($){

	commentsBox = {
		st : 0,

		get : function(total, num) {
			var st = this.st, data;
			if ( ! num )
				num = 20;

			this.st += num;
			this.total = total;
			$('#commentsdiv img.waiting').show();

			data = {
				'action' : 'get-comments',
				'mode' : 'single',
				'_ajax_nonce' : $('#add_comment_nonce').val(),
				'post_ID' : $('#post_ID').val(),
				'start' : st,
				'num' : num
			};

			$.post(ajaxurl, data,
				function(r) {
					r = wpAjax.parseAjaxResponse(r);
					$('#commentsdiv .widefat').show();
					$('#commentsdiv img.waiting').hide();

					if ( 'object' == typeof r && r.responses[0] ) {
						$('#the-comment-list').append( r.responses[0].data );

						theList = theExtraList = null;
						$("a[className*=':']").unbind();
						setCommentsList();

						if ( commentsBox.st > commentsBox.total )
							$('#show-comments').hide();
						else
							$('#show-comments').html(postL10n.showcomm);
						return;
					} else if ( 1 == r ) {
						$('#show-comments').parent().html(postL10n.endcomm);
						return;
					}

					$('#the-comment-list').append('<tr><td colspan="5">'+wpAjax.broken+'</td></tr>');
				}
			);

			return false;
		}
	};

	tagCloud = {
		init : function() {
			$('.tagcloud-link').click(function(){
				tagCloud.get($(this).attr('id'));
				$(this).unbind().click(function(){
					$(this).siblings('.the-tagcloud').toggle();
					return false;
				});
				return false;
			});
		},

		get : function(id) {
			var tax = id.substr(id.indexOf('-')+1);

			$.post(ajaxurl, {'action':'get-tagcloud','tax':tax}, function(r, stat) {
				if ( 0 == r || 'success' != stat )
					r = wpAjax.broken;

				r = $('<p id="tagcloud-'+tax+'" class="the-tagcloud">'+r+'</p>');
				$('a', r).click(function(){
					var id = $(this).parents('p').attr('id');
					tag_flush_to_text(id.substr(id.indexOf('-')+1), this);
					return false;
				});

				$('#'+id).after(r);
			});
		}
	};

	$(document).ready(function(){tagCloud.init();});
})(jQuery);

jQuery(document).ready( function($) {
	var noSyncChecks = false, syncChecks, catAddAfter, stamp = $('#timestamp').html(), visibility = $('#post-visibility-display').html(), sticky = '';

	// for Press This
	if ( typeof autosave != 'function' )
		autosave = function(){};

	// postboxes
	postboxes.add_postbox_toggles('post');

	// Editable slugs
	make_slugedit_clickable();

	// prepare the tag UI
	tag_init();

	$('#title').blur( function() { if ( ($("#post_ID").val() > 0) || ($("#title").val().length == 0) ) return; autosave(); } );

	// auto-suggest stuff
	$('.newtag').each(function(){
		var tax = $(this).parents('div.tagsdiv').attr('id');
		$(this).suggest( 'admin-ajax.php?action=ajax-tag-search&tax='+tax, { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );
	});

	// category tabs
	$('#category-tabs a').click(function(){
		var t = $(this).attr('href');
		$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
		$('.tabs-panel').hide();
		$(t).show();
		if ( '#categories-all' == t )
			deleteUserSetting('cats');
		else
			setUserSetting('cats','pop');
		return false;
	});
	if ( getUserSetting('cats') )
		$('#category-tabs a[href="#categories-pop"]').click();

	// Ajax Cat
	$('#newcat').one( 'focus', function() { $(this).val( '' ).removeClass( 'form-input-tip' ) } );
	$('#category-add-sumbit').click(function(){$('#newcat').focus();});

	syncChecks = function() {
		if ( noSyncChecks )
			return;
		noSyncChecks = true;
		var th = jQuery(this), c = th.is(':checked'), id = th.val().toString();
		$('#in-category-' + id + ', #in-popular-category-' + id).attr( 'checked', c );
		noSyncChecks = false;
	};

	popularCats = $('#categorychecklist-pop :checkbox').map( function() { return parseInt(jQuery(this).val(), 10); } ).get().join(',');
	catAddBefore = function( s ) {
		if ( !$('#newcat').val() )
			return false;
		s.data += '&popular_ids=' + popularCats + '&' + jQuery( '#categorychecklist :checked' ).serialize();
		return s;
	};

	catAddAfter = function( r, s ) {
		var newCatParent = jQuery('#newcat_parent'), newCatParentOption = newCatParent.find( 'option[value="-1"]' );
		$(s.what + ' response_data', r).each( function() {
			var t = $($(this).text());
			t.find( 'label' ).each( function() {
				var th = $(this), val = th.find('input').val(), id = th.find('input')[0].id, name, o;
				$('#' + id).change( syncChecks ).change();
				if ( newCatParent.find( 'option[value="' + val + '"]' ).size() )
					return;
				name = $.trim( th.text() );
				o = $( '<option value="' +  parseInt( val, 10 ) + '"></option>' ).text( name );
				newCatParent.prepend( o );
			} );
			newCatParentOption.attr( 'selected', 'selected' );
		} );
	};

	$('#categorychecklist').wpList( {
		alt: '',
		response: 'category-ajax-response',
		addBefore: catAddBefore,
		addAfter: catAddAfter
	} );

	$('#category-add-toggle').click( function() {
		$('#category-adder').toggleClass( 'wp-hidden-children' );
		$('#category-tabs a[href="#categories-all"]').click();
		return false;
	} );

	$('.categorychecklist .popular-category :checkbox').change( syncChecks ).filter( ':checked' ).change(), sticky = '';

	function updateVisibility() {
		if ( $('#post-visibility-select input:radio:checked').val() != 'public' ) {
			$('#sticky').attr('checked', false);
			$('#sticky-span').hide();
		} else {
			$('#sticky-span').show();
		}
		if ( $('#post-visibility-select input:radio:checked').val() != 'password' ) {
			$('#password-span').hide();
		} else {
			$('#password-span').show();
		}
	}

	function updateText() {
		var attemptedDate, originalDate, currentDate, publishOn;

		attemptedDate = new Date( $('#aa').val(), $('#mm').val() -1, $('#jj').val(), $('#hh').val(), $('#mn').val());
		originalDate = new Date( $('#hidden_aa').val(), $('#hidden_mm').val() -1, $('#hidden_jj').val(), $('#hidden_hh').val(), $('#hidden_mn').val());
		currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val());
		if ( attemptedDate > currentDate && $('#original_post_status').val() != 'future' ) {
			publishOn = postL10n.publishOnFuture;
			$('#publish').val( postL10n.schedule );
		} else if ( attemptedDate <= currentDate && $('#original_post_status').val() != 'publish' ) {
			publishOn = postL10n.publishOn;
			$('#publish').val( postL10n.publish );
		} else {
			publishOn = postL10n.publishOnPast;
			$('#publish').val( postL10n.update );
		}
		if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) { //hack
			$('#timestamp').html(stamp);
		} else {
			$('#timestamp').html(
				publishOn + ' <b>' +
				$( '#mm option[value=' + $('#mm').val() + ']' ).text() + ' ' +
				$('#jj').val() + ', ' +
				$('#aa').val() + ' @ ' +
				$('#hh').val() + ':' +
				$('#mn').val() + '</b> '
			);
		}

		if ( $('#post-visibility-select input:radio:checked').val() == 'private' ) {
			$('#publish').val( postL10n.update );
			if ( $('#post_status option[value=publish]').length == 0 ) {
				$('#post_status').append('<option value="publish">' + postL10n.privatelyPublished + '</option>');
			}
			$('#post_status option[value=publish]').html( postL10n.privatelyPublished );
			$('#post_status option[value=publish]').attr('selected', true);
			$('.edit-post-status').hide();
		} else {
			if ( $('#original_post_status').val() == 'future' || $('#original_post_status').val() == 'draft' ) {
				if ( $('#post_status option[value=publish]').length != 0 ) {
					$('#post_status option[value=publish]').remove();
					$('#post_status').val($('#hidden_post_status').val());
				}
			} else {
				$('#post_status option[value=publish]').html( postL10n.published );
			}
			$('.edit-post-status').show();
		}
		$('#post-status-display').html($('#post_status :selected').text());
		if ( $('#post_status :selected').val() == 'private' || $('#post_status :selected').val() == 'publish' ) {
			$('#save-post').hide();
		} else {
			$('#save-post').show();
			if ( $('#post_status :selected').val() == 'pending' ) {
				$('#save-post').show().val( postL10n.savePending );
			} else {
				$('#save-post').show().val( postL10n.saveDraft );
			}
		}
	}

	$('.edit-visibility').click(function () {
		if ($('#post-visibility-select').is(":hidden")) {
			updateVisibility();
			$('#post-visibility-select').slideDown("normal");
			$('.edit-visibility').hide();
		}
		return false;
	});

	$('.cancel-post-visibility').click(function () {
		$('#post-visibility-select').slideUp("normal");
		$('#visibility-radio-' + $('#hidden-post-visibility').val()).attr('checked', true);
		$('#post_password').val($('#hidden_post_password').val());
		$('#sticky').attr('checked', $('#hidden-post-sticky').attr('checked'));
		$('#post-visibility-display').html(visibility);
		$('.edit-visibility').show();
		updateText();
		return false;
	});

	$('.save-post-visibility').click(function () { // crazyhorse - multiple ok cancels
		$('#post-visibility-select').slideUp("normal");
		$('.edit-visibility').show();
		updateText();
		if ( $('#post-visibility-select input:radio:checked').val() != 'public' ) {
			$('#sticky').attr('checked', false);
		}

		if ( true == $('#sticky').attr('checked') ) {
			sticky = 'Sticky';
		} else {
			sticky = '';
		}

		$('#post-visibility-display').html(
			postL10n[$('#post-visibility-select input:radio:checked').val() + sticky]
		);

		return false;
	});

	$('#post-visibility-select input:radio').change(function() {
		updateVisibility();
	});

	$('.edit-timestamp').click(function () {
		if ($('#timestampdiv').is(":hidden")) {
			$('#timestampdiv').slideDown("normal");
			$('.edit-timestamp').hide();
		}

		return false;
	});

	$('.cancel-timestamp').click(function() {
		$('#timestampdiv').slideUp("normal");
		$('#mm').val($('#hidden_mm').val());
		$('#jj').val($('#hidden_jj').val());
		$('#aa').val($('#hidden_aa').val());
		$('#hh').val($('#hidden_hh').val());
		$('#mn').val($('#hidden_mn').val());
		$('.edit-timestamp').show();
		updateText();
		return false;
	});

	$('.save-timestamp').click(function () { // crazyhorse - multiple ok cancels
		$('#timestampdiv').slideUp("normal");
		$('.edit-timestamp').show();
		updateText();

		return false;
	});

	$('.edit-post-status').click(function() {
		if ($('#post-status-select').is(":hidden")) {
			$('#post-status-select').slideDown("normal");
			$(this).hide();
		}

		return false;
	});

	$('.save-post-status').click(function() {
		$('#post-status-select').slideUp("normal");
		$('.edit-post-status').show();
		updateText();
		return false;
	});

	$('.cancel-post-status').click(function() {
		$('#post-status-select').slideUp("normal");
		$('#post_status').val($('#hidden_post_status').val());
		$('.edit-post-status').show();
		updateText();
		return false;
	});

	// Custom Fields
	$('#the-list').wpList( { addAfter: function( xml, s ) {
		$('table#list-table').show();
		if ( $.isFunction( autosave_update_post_ID ) ) {
			autosave_update_post_ID(s.parsed.responses[0].supplemental.postid);
		}
	}, addBefore: function( s ) {
		s.data += '&post_id=' + $('#post_ID').val();
		return s;
	}
	});
});
