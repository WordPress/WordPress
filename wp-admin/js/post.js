var tagBox, commentsBox, editPermalink, makeSlugeditClickable, WPSetThumbnailHTML, WPSetThumbnailID, WPRemoveThumbnail, wptitlehint;

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

(function($){

tagBox = {
	clean : function(tags) {
		var comma = postL10n.comma;
		if ( ',' !== comma )
			tags = tags.replace(new RegExp(comma, 'g'), ',');
		tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');
		if ( ',' !== comma )
			tags = tags.replace(/,/g, comma);
		return tags;
	},

	parseTags : function(el) {
		var id = el.id, num = id.split('-check-num-')[1], taxbox = $(el).closest('.tagsdiv'),
			thetags = taxbox.find('.the-tags'), comma = postL10n.comma,
			current_tags = thetags.val().split(comma), new_tags = [];
		delete current_tags[num];

		$.each( current_tags, function(key, val) {
			val = $.trim(val);
			if ( val ) {
				new_tags.push(val);
			}
		});

		thetags.val( this.clean( new_tags.join(comma) ) );

		this.quickClicks(taxbox);
		return false;
	},

	quickClicks : function(el) {
		var thetags = $('.the-tags', el),
			tagchecklist = $('.tagchecklist', el),
			id = $(el).attr('id'),
			current_tags, disabled;

		if ( !thetags.length )
			return;

		disabled = thetags.prop('disabled');

		current_tags = thetags.val().split(postL10n.comma);
		tagchecklist.empty();

		$.each( current_tags, function( key, val ) {
			var span, xbutton;

			val = $.trim( val );

			if ( ! val )
				return;

			// Create a new span, and ensure the text is properly escaped.
			span = $('<span />').text( val );

			// If tags editing isn't disabled, create the X button.
			if ( ! disabled ) {
				xbutton = $( '<a id="' + id + '-check-num-' + key + '" class="ntdelbutton">X</a>' );
				xbutton.click( function(){ tagBox.parseTags(this); });
				span.prepend('&nbsp;').prepend( xbutton );
			}

			// Append the span to the tag list.
			tagchecklist.append( span );
		});
	},

	flushTags : function(el, a, f) {
		a = a || false;
		var tags = $('.the-tags', el),
			newtag = $('input.newtag', el),
			comma = postL10n.comma,
			newtags, text;

		text = a ? $(a).text() : newtag.val();
		tagsval = tags.val();
		newtags = tagsval ? tagsval + comma + text : text;

		newtags = this.clean( newtags );
		newtags = array_unique_noempty( newtags.split(comma) ).join(comma);
		tags.val(newtags);
		this.quickClicks(el);

		if ( !a )
			newtag.val('');
		if ( 'undefined' == typeof(f) )
			newtag.focus();

		return false;
	},

	get : function(id) {
		var tax = id.substr(id.indexOf('-')+1);

		$.post(ajaxurl, {'action':'get-tagcloud', 'tax':tax}, function(r, stat) {
			if ( 0 == r || 'success' != stat )
				r = wpAjax.broken;

			r = $('<p id="tagcloud-'+tax+'" class="the-tagcloud">'+r+'</p>');
			$('a', r).click(function(){
				tagBox.flushTags( $(this).closest('.inside').children('.tagsdiv'), this);
				return false;
			});

			$('#'+id).after(r);
		});
	},

	init : function() {
		var t = this, ajaxtag = $('div.ajaxtag');

		$('.tagsdiv').each( function() {
			tagBox.quickClicks(this);
		});

		$('input.tagadd', ajaxtag).click(function(){
			t.flushTags( $(this).closest('.tagsdiv') );
		});

		$('div.taghint', ajaxtag).click(function(){
			$(this).css('visibility', 'hidden').parent().siblings('.newtag').focus();
		});

		$('input.newtag', ajaxtag).blur(function() {
			if ( this.value == '' )
				$(this).parent().siblings('.taghint').css('visibility', '');
		}).focus(function(){
			$(this).parent().siblings('.taghint').css('visibility', 'hidden');
		}).keyup(function(e){
			if ( 13 == e.which ) {
				tagBox.flushTags( $(this).closest('.tagsdiv') );
				return false;
			}
		}).keypress(function(e){
			if ( 13 == e.which ) {
				e.preventDefault();
				return false;
			}
		}).each(function(){
			var tax = $(this).closest('div.tagsdiv').attr('id');
			$(this).suggest( ajaxurl + '?action=ajax-tag-search&tax=' + tax, { delay: 500, minchars: 2, multiple: true, multipleSep: postL10n.comma + ' ' } );
		});

		// save tags on post save/publish
		$('#post').submit(function(){
			$('div.tagsdiv').each( function() {
				tagBox.flushTags(this, false, 1);
			});
		});

		// tag cloud
		$('a.tagcloud-link').click(function(){
			tagBox.get( $(this).attr('id') );
			$(this).unbind().click(function(){
				$(this).siblings('.the-tagcloud').toggle();
				return false;
			});
			return false;
		});
	}
};

commentsBox = {
	st : 0,

	get : function(total, num) {
		var st = this.st, data;
		if ( ! num )
			num = 20;

		this.st += num;
		this.total = total;
		$('#commentsdiv .spinner').show();

		data = {
			'action' : 'get-comments',
			'mode' : 'single',
			'_ajax_nonce' : $('#add_comment_nonce').val(),
			'p' : $('#post_ID').val(),
			'start' : st,
			'number' : num
		};

		$.post(ajaxurl, data,
			function(r) {
				r = wpAjax.parseAjaxResponse(r);
				$('#commentsdiv .widefat').show();
				$('#commentsdiv .spinner').hide();

				if ( 'object' == typeof r && r.responses[0] ) {
					$('#the-comment-list').append( r.responses[0].data );

					theList = theExtraList = null;
					$("a[className*=':']").unbind();

					if ( commentsBox.st > commentsBox.total )
						$('#show-comments').hide();
					else
						$('#show-comments').show().children('a').html(postL10n.showcomm);

					return;
				} else if ( 1 == r ) {
					$('#show-comments').html(postL10n.endcomm);
					return;
				}

				$('#the-comment-list').append('<tr><td colspan="2">'+wpAjax.broken+'</td></tr>');
			}
		);

		return false;
	}
};

WPSetThumbnailHTML = function(html){
	$('.inside', '#postimagediv').html(html);
};

WPSetThumbnailID = function(id){
	var field = $('input[value="_thumbnail_id"]', '#list-table');
	if ( field.size() > 0 ) {
		$('#meta\\[' + field.attr('id').match(/[0-9]+/) + '\\]\\[value\\]').text(id);
	}
};

WPRemoveThumbnail = function(nonce){
	$.post(ajaxurl, {
		action:"set-post-thumbnail", post_id: $('#post_ID').val(), thumbnail_id: -1, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
	}, function(str){
		if ( str == '0' ) {
			alert( setPostThumbnailL10n.error );
		} else {
			WPSetThumbnailHTML(str);
		}
	}
	);
};

$(document).on( 'heartbeat-send.refresh-lock', function( e, data ) {
	var lock = $('#active_post_lock').val(),
		post_id = $('#post_ID').val(),
		send = {};

	if ( ! post_id || ! $('#post-lock-dialog').length )
		return;

	send['post_id'] = post_id;

	if ( lock )
		send['lock'] = lock;

	data['wp-refresh-post-lock'] = send;
});

// Post locks: update the lock string or show the dialog if somebody has taken over editing
$(document).on( 'heartbeat-tick.refresh-lock', function( e, data ) {
	var received, wrap, avatar;

	if ( data['wp-refresh-post-lock'] ) {
		received = data['wp-refresh-post-lock'];

		if ( received.lock_error ) {
			// show "editing taken over" message
			wrap = $('#post-lock-dialog');

			if ( wrap.length && ! wrap.is(':visible') ) {
				if ( typeof autosave == 'function' ) {
					$(document).on('autosave-disable-buttons.post-lock', function() {
						wrap.addClass('saving');
					}).on('autosave-enable-buttons.post-lock', function() {
						wrap.removeClass('saving').addClass('saved');
						window.onbeforeunload = null;
					});

					// Save the latest changes and disable
					if ( ! autosave() )
						window.onbeforeunload = null;

					autosave = function(){};
				}

				if ( received.lock_error.avatar_src ) {
					avatar = $('<img class="avatar avatar-64 photo" width="64" height="64" />').attr( 'src', received.lock_error.avatar_src.replace(/&amp;/g, '&') );
					wrap.find('div.post-locked-avatar').empty().append( avatar );
				}

				wrap.show().find('.currently-editing').text( received.lock_error.text );
				wrap.find('.wp-tab-first').focus();
			}
		} else if ( received.new_lock ) {
			$('#active_post_lock').val( received.new_lock );
		}
	}
});

}(jQuery));

(function($) {
	var check, timeout;

	function schedule() {
		check = false;
		window.clearTimeout( timeout );
		timeout = window.setTimeout( function(){ check = true; }, 300000 );
	}

	$(document).on( 'heartbeat-send.wp-refresh-nonces', function( e, data ) {
		var nonce, post_id;

		if ( check ) {
			if ( ( post_id = $('#post_ID').val() ) && ( nonce = $('#_wpnonce').val() ) ) {
				data['wp-refresh-post-nonces'] = {
					post_id: post_id,
					post_nonce: nonce
				};
			}
		}
	}).on( 'heartbeat-tick.wp-refresh-nonces', function( e, data ) {
		var nonces = data['wp-refresh-post-nonces'];

		if ( nonces ) {
			schedule();

			if ( nonces.replace ) {
				$.each( nonces.replace, function( selector, value ) {
					$( '#' + selector ).val( value );
				});
			}

			if ( nonces.heartbeatNonce )
				window.heartbeatSettings.nonce = nonces.heartbeatNonce;
		}
	}).ready( function() {
		schedule();
	});
}(jQuery));

jQuery(document).ready( function($) {
	var stamp, visibility, sticky = '', last = 0, co = $('#content');

	postboxes.add_postbox_toggles(pagenow);

	// Post locks: contain focus inside the dialog. If the dialog is shown, focus the first item.
	$('#post-lock-dialog .notification-dialog').on( 'keydown', function(e) {
		if ( e.which != 9 )
			return;

		var target = $(e.target);

		if ( target.hasClass('wp-tab-first') && e.shiftKey ) {
			$(this).find('.wp-tab-last').focus();
			e.preventDefault();
		} else if ( target.hasClass('wp-tab-last') && ! e.shiftKey ) {
			$(this).find('.wp-tab-first').focus();
			e.preventDefault();
		}
	}).filter(':visible').find('.wp-tab-first').focus();

	// multi-taxonomies
	if ( $('#tagsdiv-post_tag').length ) {
		tagBox.init();
	} else {
		$('#side-sortables, #normal-sortables, #advanced-sortables').children('div.postbox').each(function(){
			if ( this.id.indexOf('tagsdiv-') === 0 ) {
				tagBox.init();
				return false;
			}
		});
	}

	// categories
	$('.categorydiv').each( function(){
		var this_id = $(this).attr('id'), catAddBefore, catAddAfter, taxonomyParts, taxonomy, settingName;

		taxonomyParts = this_id.split('-');
		taxonomyParts.shift();
		taxonomy = taxonomyParts.join('-');
		settingName = taxonomy + '_tab';
		if ( taxonomy == 'category' )
			settingName = 'cats';

		// TODO: move to jQuery 1.3+, support for multiple hierarchical taxonomies, see wp-lists.js
		$('a', '#' + taxonomy + '-tabs').click( function(){
			var t = $(this).attr('href');
			$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
			$('#' + taxonomy + '-tabs').siblings('.tabs-panel').hide();
			$(t).show();
			if ( '#' + taxonomy + '-all' == t )
				deleteUserSetting(settingName);
			else
				setUserSetting(settingName, 'pop');
			return false;
		});

		if ( getUserSetting(settingName) )
			$('a[href="#' + taxonomy + '-pop"]', '#' + taxonomy + '-tabs').click();

		// Ajax Cat
		$('#new' + taxonomy).one( 'focus', function() { $(this).val( '' ).removeClass( 'form-input-tip' ) } );

		$('#new' + taxonomy).keypress( function(event){
			if( 13 === event.keyCode ) {
				event.preventDefault();
				$('#' + taxonomy + '-add-submit').click();
			}
		});
		$('#' + taxonomy + '-add-submit').click( function(){ $('#new' + taxonomy).focus(); });

		catAddBefore = function( s ) {
			if ( !$('#new'+taxonomy).val() )
				return false;
			s.data += '&' + $( ':checked', '#'+taxonomy+'checklist' ).serialize();
			$( '#' + taxonomy + '-add-submit' ).prop( 'disabled', true );
			return s;
		};

		catAddAfter = function( r, s ) {
			var sup, drop = $('#new'+taxonomy+'_parent');

			$( '#' + taxonomy + '-add-submit' ).prop( 'disabled', false );
			if ( 'undefined' != s.parsed.responses[0] && (sup = s.parsed.responses[0].supplemental.newcat_parent) ) {
				drop.before(sup);
				drop.remove();
			}
		};

		$('#' + taxonomy + 'checklist').wpList({
			alt: '',
			response: taxonomy + '-ajax-response',
			addBefore: catAddBefore,
			addAfter: catAddAfter
		});

		$('#' + taxonomy + '-add-toggle').click( function() {
			$('#' + taxonomy + '-adder').toggleClass( 'wp-hidden-children' );
			$('a[href="#' + taxonomy + '-all"]', '#' + taxonomy + '-tabs').click();
			$('#new'+taxonomy).focus();
			return false;
		});

		$('#' + taxonomy + 'checklist, #' + taxonomy + 'checklist-pop').on( 'click', 'li.popular-category > label input[type="checkbox"]', function() {
			var t = $(this), c = t.is(':checked'), id = t.val();
			if ( id && t.parents('#taxonomy-'+taxonomy).length )
				$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
		});

	}); // end cats

	// Custom Fields
	if ( $('#postcustom').length ) {
		$('#the-list').wpList( { addAfter: function( xml, s ) {
			$('table#list-table').show();
		}, addBefore: function( s ) {
			s.data += '&post_id=' + $('#post_ID').val();
			return s;
		}
		});
	}

	// submitdiv
	if ( $('#submitdiv').length ) {
		stamp = $('#timestamp').html();
		visibility = $('#post-visibility-display').html();

		function updateVisibility() {
			var pvSelect = $('#post-visibility-select');
			if ( $('input:radio:checked', pvSelect).val() != 'public' ) {
				$('#sticky').prop('checked', false);
				$('#sticky-span').hide();
			} else {
				$('#sticky-span').show();
			}
			if ( $('input:radio:checked', pvSelect).val() != 'password' ) {
				$('#password-span').hide();
			} else {
				$('#password-span').show();
			}
		}

		function updateText() {

			if ( ! $('#timestampdiv').length )
				return true;

			var attemptedDate, originalDate, currentDate, publishOn, postStatus = $('#post_status'),
				optPublish = $('option[value="publish"]', postStatus), aa = $('#aa').val(),
				mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val();

			attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
			originalDate = new Date( $('#hidden_aa').val(), $('#hidden_mm').val() -1, $('#hidden_jj').val(), $('#hidden_hh').val(), $('#hidden_mn').val() );
			currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val() );

			if ( attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn ) {
				$('.timestamp-wrap', '#timestampdiv').addClass('form-invalid');
				return false;
			} else {
				$('.timestamp-wrap', '#timestampdiv').removeClass('form-invalid');
			}

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
					postL10n.dateFormat.replace( '%1$s', $('option[value="' + $('#mm').val() + '"]', '#mm').text() )
						.replace( '%2$s', jj )
						.replace( '%3$s', aa )
						.replace( '%4$s', hh )
						.replace( '%5$s', mn )
					+ '</b> '
				);
			}

			if ( $('input:radio:checked', '#post-visibility-select').val() == 'private' ) {
				$('#publish').val( postL10n.update );
				if ( optPublish.length == 0 ) {
					postStatus.append('<option value="publish">' + postL10n.privatelyPublished + '</option>');
				} else {
					optPublish.html( postL10n.privatelyPublished );
				}
				$('option[value="publish"]', postStatus).prop('selected', true);
				$('.edit-post-status', '#misc-publishing-actions').hide();
			} else {
				if ( $('#original_post_status').val() == 'future' || $('#original_post_status').val() == 'draft' ) {
					if ( optPublish.length ) {
						optPublish.remove();
						postStatus.val($('#hidden_post_status').val());
					}
				} else {
					optPublish.html( postL10n.published );
				}
				if ( postStatus.is(':hidden') )
					$('.edit-post-status', '#misc-publishing-actions').show();
			}
			$('#post-status-display').html($('option:selected', postStatus).text());
			if ( $('option:selected', postStatus).val() == 'private' || $('option:selected', postStatus).val() == 'publish' ) {
				$('#save-post').hide();
			} else {
				$('#save-post').show();
				if ( $('option:selected', postStatus).val() == 'pending' ) {
					$('#save-post').show().val( postL10n.savePending );
				} else {
					$('#save-post').show().val( postL10n.saveDraft );
				}
			}
			return true;
		}

		$('.edit-visibility', '#visibility').click(function () {
			if ($('#post-visibility-select').is(":hidden")) {
				updateVisibility();
				$('#post-visibility-select').slideDown('fast');
				$(this).hide();
			}
			return false;
		});

		$('.cancel-post-visibility', '#post-visibility-select').click(function () {
			$('#post-visibility-select').slideUp('fast');
			$('#visibility-radio-' + $('#hidden-post-visibility').val()).prop('checked', true);
			$('#post_password').val($('#hidden-post-password').val());
			$('#sticky').prop('checked', $('#hidden-post-sticky').prop('checked'));
			$('#post-visibility-display').html(visibility);
			$('.edit-visibility', '#visibility').show();
			updateText();
			return false;
		});

		$('.save-post-visibility', '#post-visibility-select').click(function () { // crazyhorse - multiple ok cancels
			var pvSelect = $('#post-visibility-select');

			pvSelect.slideUp('fast');
			$('.edit-visibility', '#visibility').show();
			updateText();

			if ( $('input:radio:checked', pvSelect).val() != 'public' ) {
				$('#sticky').prop('checked', false);
			} // WEAPON LOCKED

			if ( true == $('#sticky').prop('checked') ) {
				sticky = 'Sticky';
			} else {
				sticky = '';
			}

			$('#post-visibility-display').html(	postL10n[$('input:radio:checked', pvSelect).val() + sticky]	);
			return false;
		});

		$('input:radio', '#post-visibility-select').change(function() {
			updateVisibility();
		});

		$('#timestampdiv').siblings('a.edit-timestamp').click(function() {
			if ($('#timestampdiv').is(":hidden")) {
				$('#timestampdiv').slideDown('fast');
				$('#mm').focus();
				$(this).hide();
			}
			return false;
		});

		$('.cancel-timestamp', '#timestampdiv').click(function() {
			$('#timestampdiv').slideUp('fast');
			$('#mm').val($('#hidden_mm').val());
			$('#jj').val($('#hidden_jj').val());
			$('#aa').val($('#hidden_aa').val());
			$('#hh').val($('#hidden_hh').val());
			$('#mn').val($('#hidden_mn').val());
			$('#timestampdiv').siblings('a.edit-timestamp').show();
			updateText();
			return false;
		});

		$('.save-timestamp', '#timestampdiv').click(function () { // crazyhorse - multiple ok cancels
			if ( updateText() ) {
				$('#timestampdiv').slideUp('fast');
				$('#timestampdiv').siblings('a.edit-timestamp').show();
			}
			return false;
		});

		$('#post').on( 'submit', function(e){
			if ( ! updateText() ) {
				e.preventDefault();
				$('#timestampdiv').show();
				$('#publishing-action .spinner').hide();
				$('#publish').prop('disabled', false).removeClass('button-primary-disabled');
				return false;
			}
		});

		$('#post-status-select').siblings('a.edit-post-status').click(function() {
			if ($('#post-status-select').is(":hidden")) {
				$('#post-status-select').slideDown('fast');
				$(this).hide();
			}
			return false;
		});

		$('.save-post-status', '#post-status-select').click(function() {
			$('#post-status-select').slideUp('fast');
			$('#post-status-select').siblings('a.edit-post-status').show();
			updateText();
			return false;
		});

		$('.cancel-post-status', '#post-status-select').click(function() {
			$('#post-status-select').slideUp('fast');
			$('#post_status').val($('#hidden_post_status').val());
			$('#post-status-select').siblings('a.edit-post-status').show();
			updateText();
			return false;
		});
	} // end submitdiv

	// permalink
	if ( $('#edit-slug-box').length ) {
		editPermalink = function(post_id) {
			var i, c = 0, e = $('#editable-post-name'), revert_e = e.html(), real_slug = $('#post_name'), revert_slug = real_slug.val(), b = $('#edit-slug-buttons'), revert_b = b.html(), full = $('#editable-post-name-full').html();

			$('#view-post-btn').hide();
			b.html('<a href="#" class="save button button-small">'+postL10n.ok+'</a> <a class="cancel" href="#">'+postL10n.cancel+'</a>');
			b.children('.save').click(function() {
				var new_slug = e.children('input').val();
				if ( new_slug == $('#editable-post-name-full').text() ) {
					return $('.cancel', '#edit-slug-buttons').click();
				}
				$.post(ajaxurl, {
					action: 'sample-permalink',
					post_id: post_id,
					new_slug: new_slug,
					new_title: $('#title').val(),
					samplepermalinknonce: $('#samplepermalinknonce').val()
				}, function(data) {
					var box = $('#edit-slug-box');
					box.html(data);
					if (box.hasClass('hidden')) {
						box.fadeIn('fast', function () {
							box.removeClass('hidden');
						});
					}
					b.html(revert_b);
					real_slug.val(new_slug);
					makeSlugeditClickable();
					$('#view-post-btn').show();
				});
				return false;
			});

			$('.cancel', '#edit-slug-buttons').click(function() {
				$('#view-post-btn').show();
				e.html(revert_e);
				b.html(revert_b);
				real_slug.val(revert_slug);
				return false;
			});

			for ( i = 0; i < full.length; ++i ) {
				if ( '%' == full.charAt(i) )
					c++;
			}

			slug_value = ( c > full.length / 4 ) ? '' : full;
			e.html('<input type="text" id="new-post-slug" value="'+slug_value+'" />').children('input').keypress(function(e) {
				var key = e.keyCode || 0;
				// on enter, just save the new slug, don't save the post
				if ( 13 == key ) {
					b.children('.save').click();
					return false;
				}
				if ( 27 == key ) {
					b.children('.cancel').click();
					return false;
				}
			}).keyup(function(e) {
				real_slug.val(this.value);
			}).focus();
		}

		makeSlugeditClickable = function() {
			$('#editable-post-name').click(function() {
				$('#edit-slug-buttons').children('.edit-slug').click();
			});
		}
		makeSlugeditClickable();
	}

	// word count
	if ( typeof(wpWordCount) != 'undefined' ) {
		$(document).triggerHandler('wpcountwords', [ co.val() ]);

		co.keyup( function(e) {
			var k = e.keyCode || e.charCode;

			if ( k == last )
				return true;

			if ( 13 == k || 8 == last || 46 == last )
				$(document).triggerHandler('wpcountwords', [ co.val() ]);

			last = k;
			return true;
		});
	}

	wptitlehint = function(id) {
		id = id || 'title';

		var title = $('#' + id), titleprompt = $('#' + id + '-prompt-text');

		if ( title.val() == '' )
			titleprompt.removeClass('screen-reader-text');

		titleprompt.click(function(){
			$(this).addClass('screen-reader-text');
			title.focus();
		});

		title.blur(function(){
			if ( this.value == '' )
				titleprompt.removeClass('screen-reader-text');
		}).focus(function(){
			titleprompt.addClass('screen-reader-text');
		}).keydown(function(e){
			titleprompt.addClass('screen-reader-text');
			$(this).unbind(e);
		});
	}

	wptitlehint();

	// resizable textarea#content
	(function() {
		var textarea = $('textarea#content'), offset = null, el;
		// No point for touch devices
		if ( !textarea.length || 'ontouchstart' in window )
			return;

		function dragging(e) {
			textarea.height( Math.max(50, offset + e.pageY) + 'px' );
			return false;
		}

		function endDrag(e) {
			var height;

			textarea.focus();
			$(document).unbind('mousemove', dragging).unbind('mouseup', endDrag);

			height = parseInt( textarea.css('height'), 10 );

			// sanity check
			if ( height && height > 50 && height < 5000 )
				setUserSetting( 'ed_size', height );
		}

		textarea.css('resize', 'none');
		el = $('<div id="content-resize-handle"><br></div>');
		$('#wp-content-wrap').append(el);
		el.on('mousedown', function(e) {
			offset = textarea.height() - e.pageY;
			textarea.blur();
			$(document).mousemove(dragging).mouseup(endDrag);
			return false;
		});
	})();

	if ( typeof(tinymce) != 'undefined' ) {
		tinymce.onAddEditor.add(function(mce, ed){
			// iOS expands the iframe to full height and the user cannot adjust it.
			if ( ed.id != 'content' || tinymce.isIOS5 )
				return;

			function getHeight() {
				var height, node = document.getElementById('content_ifr'),
					ifr_height = node ? parseInt( node.style.height, 10 ) : 0,
					tb_height = $('#content_tbl tr.mceFirst').height();

				if ( !ifr_height || !tb_height )
					return false;

				// total height including toolbar and statusbar
				height = ifr_height + tb_height + 21;
				// textarea height = total height - 33px toolbar
				height -= 33;

				return height;
			}

			// resize TinyMCE to match the textarea height when switching Text -> Visual
			ed.onLoadContent.add( function(ed, o) {
				var ifr_height, node = document.getElementById('content'),
					height = node ? parseInt( node.style.height, 10 ) : 0,
					tb_height = $('#content_tbl tr.mceFirst').height() || 33;

				// height cannot be under 50 or over 5000
				if ( !height || height < 50 || height > 5000 )
					height = 360; // default height for the main editor

				if ( getUserSetting( 'ed_size' ) > 5000  )
					setUserSetting( 'ed_size', 360 );

				// compensate for padding and toolbars
				ifr_height = ( height - tb_height ) + 12;

				// sanity check
				if ( ifr_height > 50 && ifr_height < 5000 ) {
					$('#content_tbl').css('height', '' );
					$('#content_ifr').css('height', ifr_height + 'px' );
				}
			});

			// resize the textarea to match TinyMCE's height when switching Visual -> Text
			ed.onSaveContent.add( function(ed, o) {
				var height = getHeight();

				if ( !height || height < 50 || height > 5000 )
					return;

				$('textarea#content').css( 'height', height + 'px' );
			});

			// save on resizing TinyMCE
			ed.onPostRender.add(function() {
				$('#content_resize').on('mousedown.wp-mce-resize', function(e){
					$(document).on('mouseup.wp-mce-resize', function(e){
						var height;

						$(document).off('mouseup.wp-mce-resize');

						height = getHeight();
						// sanity check
						if ( height && height > 50 && height < 5000 )
							setUserSetting( 'ed_size', height );
					});
				});
			});
		});

		// When changing post formats, change the editor body class
		$('#post-formats-select input.post-format').on( 'change.set-editor-class', function( event ) {
			var editor, body, format = this.id;

			if ( format && $( this ).prop('checked') ) {
				editor = tinymce.get( 'content' );

				if ( editor ) {
					body = editor.getBody();
					body.className = body.className.replace( /\bpost-format-[^ ]+/, '' );
					editor.dom.addClass( body, format == 'post-format-0' ? 'post-format-standard' : format );
				}
			}
		});
	}
});
