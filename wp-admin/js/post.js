/* global postL10n, ajaxurl, wpAjax, setPostThumbnailL10n, postboxes, pagenow, tinymce, alert, deleteUserSetting */
/* global theList:true, theExtraList:true, getUserSetting, setUserSetting, commentReply */

var commentsBox, WPSetThumbnailHTML, WPSetThumbnailID, WPRemoveThumbnail, wptitlehint, makeSlugeditClickable, editPermalink;
// Back-compat: prevent fatal errors
makeSlugeditClickable = editPermalink = function(){};

window.wp = window.wp || {};

( function( $ ) {
	var titleHasFocus = false;

commentsBox = {
	st : 0,

	get : function(total, num) {
		var st = this.st, data;
		if ( ! num )
			num = 20;

		this.st += num;
		this.total = total;
		$( '#commentsdiv .spinner' ).addClass( 'is-active' );

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
				$( '#commentsdiv .spinner' ).removeClass( 'is-active' );

				if ( 'object' == typeof r && r.responses[0] ) {
					$('#the-comment-list').append( r.responses[0].data );

					theList = theExtraList = null;
					$( 'a[className*=\':\']' ).unbind();

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
	},

	load: function(total){
		this.st = jQuery('#the-comment-list tr.comment:visible').length;
		this.get(total);
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
		action: 'set-post-thumbnail', post_id: $( '#post_ID' ).val(), thumbnail_id: -1, _ajax_nonce: nonce, cookie: encodeURIComponent( document.cookie )
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

	send.post_id = post_id;

	if ( lock )
		send.lock = lock;

	data['wp-refresh-post-lock'] = send;

}).on( 'heartbeat-tick.refresh-lock', function( e, data ) {
	// Post locks: update the lock string or show the dialog if somebody has taken over editing
	var received, wrap, avatar;

	if ( data['wp-refresh-post-lock'] ) {
		received = data['wp-refresh-post-lock'];

		if ( received.lock_error ) {
			// show "editing taken over" message
			wrap = $('#post-lock-dialog');

			if ( wrap.length && ! wrap.is(':visible') ) {
				if ( wp.autosave ) {
					// Save the latest changes and disable
					$(document).one( 'heartbeat-tick', function() {
						wp.autosave.server.suspend();
						wrap.removeClass('saving').addClass('saved');
						$(window).off( 'beforeunload.edit-post' );
					});

					wrap.addClass('saving');
					wp.autosave.server.triggerSave();
				}

				if ( received.lock_error.avatar_src ) {
					avatar = $( '<img class="avatar avatar-64 photo" width="64" height="64" alt="" />' ).attr( 'src', received.lock_error.avatar_src.replace( /&amp;/g, '&' ) );
					wrap.find('div.post-locked-avatar').empty().append( avatar );
				}

				wrap.show().find('.currently-editing').text( received.lock_error.text );
				wrap.find('.wp-tab-first').focus();
			}
		} else if ( received.new_lock ) {
			$('#active_post_lock').val( received.new_lock );
		}
	}
}).on( 'before-autosave.update-post-slug', function() {
	titleHasFocus = document.activeElement && document.activeElement.id === 'title';
}).on( 'after-autosave.update-post-slug', function() {
	// Create slug area only if not already there
	// and the title field was not focused (user was not typing a title) when autosave ran
	if ( ! $('#edit-slug-box > *').length && ! titleHasFocus ) {
		$.post( ajaxurl, {
				action: 'sample-permalink',
				post_id: $('#post_ID').val(),
				new_title: $('#title').val(),
				samplepermalinknonce: $('#samplepermalinknonce').val()
			},
			function( data ) {
				if ( data != '-1' ) {
					$('#edit-slug-box').html(data);
				}
			}
		);
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
		var post_id,
			$authCheck = $('#wp-auth-check-wrap');

		if ( check || ( $authCheck.length && ! $authCheck.hasClass( 'hidden' ) ) ) {
			if ( ( post_id = $('#post_ID').val() ) && $('#_wpnonce').val() ) {
				data['wp-refresh-post-nonces'] = {
					post_id: post_id
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
	var stamp, visibility, $submitButtons, updateVisibility, updateText,
		sticky = '',
		$textarea = $('#content'),
		$document = $(document),
		postId = $('#post_ID').val() || 0,
		$submitpost = $('#submitpost'),
		releaseLock = true,
		$postVisibilitySelect = $('#post-visibility-select'),
		$timestampdiv = $('#timestampdiv'),
		$postStatusSelect = $('#post-status-select'),
		isMac = window.navigator.platform ? window.navigator.platform.indexOf( 'Mac' ) !== -1 : false;

	postboxes.add_postbox_toggles(pagenow);

	// Clear the window name. Otherwise if this is a former preview window where the user navigated to edit another post,
	// and the first post is still being edited, clicking Preview there will use this window to show the preview.
	window.name = '';

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

	// Set the heartbeat interval to 15 sec. if post lock dialogs are enabled
	if ( wp.heartbeat && $('#post-lock-dialog').length ) {
		wp.heartbeat.interval( 15 );
	}

	// The form is being submitted by the user
	$submitButtons = $submitpost.find( ':submit, a.submitdelete, #post-preview' ).on( 'click.edit-post', function( event ) {
		var $button = $(this);

		if ( $button.hasClass('disabled') ) {
			event.preventDefault();
			return;
		}

		if ( $button.hasClass('submitdelete') || $button.is( '#post-preview' ) ) {
			return;
		}

		// The form submission can be blocked from JS or by using HTML 5.0 validation on some fields.
		// Run this only on an actual 'submit'.
		$('form#post').off( 'submit.edit-post' ).on( 'submit.edit-post', function( event ) {
			if ( event.isDefaultPrevented() ) {
				return;
			}

			// Stop autosave
			if ( wp.autosave ) {
				wp.autosave.server.suspend();
			}

			if ( typeof commentReply !== 'undefined' ) {
				/*
				 * Close the comment edit/reply form if open to stop the form
				 * action from interfering with the post's form action.
				 */
				commentReply.close();
			}

			releaseLock = false;
			$(window).off( 'beforeunload.edit-post' );

			$submitButtons.addClass( 'disabled' );

			if ( $button.attr('id') === 'publish' ) {
				$submitpost.find( '#major-publishing-actions .spinner' ).addClass( 'is-active' );
			} else {
				$submitpost.find( '#minor-publishing .spinner' ).addClass( 'is-active' );
			}
		});
	});

	// Submit the form saving a draft or an autosave, and show a preview in a new tab
	$('#post-preview').on( 'click.post-preview', function( event ) {
		var $this = $(this),
			$form = $('form#post'),
			$previewField = $('input#wp-preview'),
			target = $this.attr('target') || 'wp-preview',
			ua = navigator.userAgent.toLowerCase();

		event.preventDefault();

		if ( $this.hasClass('disabled') ) {
			return;
		}

		if ( wp.autosave ) {
			wp.autosave.server.tempBlockSave();
		}

		$previewField.val('dopreview');
		$form.attr( 'target', target ).submit().attr( 'target', '' );

		// Workaround for WebKit bug preventing a form submitting twice to the same action.
		// https://bugs.webkit.org/show_bug.cgi?id=28633
		if ( ua.indexOf('safari') !== -1 && ua.indexOf('chrome') === -1 ) {
			$form.attr( 'action', function( index, value ) {
				return value + '?t=' + ( new Date() ).getTime();
			});
		}

		$previewField.val('');
	});

	// This code is meant to allow tabbing from Title to Post content.
	$('#title').on( 'keydown.editor-focus', function( event ) {
		var editor;

		if ( event.keyCode === 9 && ! event.ctrlKey && ! event.altKey && ! event.shiftKey ) {
			editor = typeof tinymce != 'undefined' && tinymce.get('content');

			if ( editor && ! editor.isHidden() ) {
				editor.focus();
			} else if ( $textarea.length ) {
				$textarea.focus();
			} else {
				return;
			}

			event.preventDefault();
		}
	});

	// Autosave new posts after a title is typed
	if ( $( '#auto_draft' ).val() ) {
		$( '#title' ).blur( function() {
			var cancel;

			if ( ! this.value || $('#edit-slug-box > *').length ) {
				return;
			}

			// Cancel the autosave when the blur was triggered by the user submitting the form
			$('form#post').one( 'submit', function() {
				cancel = true;
			});

			window.setTimeout( function() {
				if ( ! cancel && wp.autosave ) {
					wp.autosave.server.triggerSave();
				}
			}, 200 );
		});
	}

	$document.on( 'autosave-disable-buttons.edit-post', function() {
		$submitButtons.addClass( 'disabled' );
	}).on( 'autosave-enable-buttons.edit-post', function() {
		if ( ! wp.heartbeat || ! wp.heartbeat.hasConnectionError() ) {
			$submitButtons.removeClass( 'disabled' );
		}
	}).on( 'before-autosave.edit-post', function() {
		$( '.autosave-message' ).text( postL10n.savingText );
	}).on( 'after-autosave.edit-post', function( event, data ) {
		$( '.autosave-message' ).text( data.message );
	});

	$(window).on( 'beforeunload.edit-post', function() {
		var editor = typeof tinymce !== 'undefined' && tinymce.get('content');

		if ( ( editor && ! editor.isHidden() && editor.isDirty() ) ||
			( wp.autosave && wp.autosave.server.postChanged() ) ) {

			return postL10n.saveAlert;
		}
	}).on( 'unload.edit-post', function( event ) {
		if ( ! releaseLock ) {
			return;
		}

		// Unload is triggered (by hand) on removing the Thickbox iframe.
		// Make sure we process only the main document unload.
		if ( event.target && event.target.nodeName != '#document' ) {
			return;
		}

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			async: false,
			data: {
				action: 'wp-remove-post-lock',
				_wpnonce: $('#_wpnonce').val(),
				post_ID: $('#post_ID').val(),
				active_post_lock: $('#active_post_lock').val()
			}
		});
	});

	// multi-taxonomies
	if ( $('#tagsdiv-post_tag').length ) {
		window.tagBox && window.tagBox.init();
	} else {
		$('.meta-box-sortables').children('div.postbox').each(function(){
			if ( this.id.indexOf('tagsdiv-') === 0 ) {
				window.tagBox && window.tagBox.init();
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
		$('a', '#' + taxonomy + '-tabs').click( function( e ) {
			e.preventDefault();
			var t = $(this).attr('href');
			$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
			$('#' + taxonomy + '-tabs').siblings('.tabs-panel').hide();
			$(t).show();
			if ( '#' + taxonomy + '-all' == t )
				deleteUserSetting( settingName );
			else
				setUserSetting( settingName, 'pop' );
		});

		if ( getUserSetting( settingName ) )
			$('a[href="#' + taxonomy + '-pop"]', '#' + taxonomy + '-tabs').click();

		// Ajax Cat
		$( '#new' + taxonomy ).one( 'focus', function() { $( this ).val( '' ).removeClass( 'form-input-tip' ); } );

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

		$('#' + taxonomy + '-add-toggle').click( function( e ) {
			e.preventDefault();
			$('#' + taxonomy + '-adder').toggleClass( 'wp-hidden-children' );
			$('a[href="#' + taxonomy + '-all"]', '#' + taxonomy + '-tabs').click();
			$('#new'+taxonomy).focus();
		});

		$('#' + taxonomy + 'checklist, #' + taxonomy + 'checklist-pop').on( 'click', 'li.popular-category > label input[type="checkbox"]', function() {
			var t = $(this), c = t.is(':checked'), id = t.val();
			if ( id && t.parents('#taxonomy-'+taxonomy).length )
				$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
		});

	}); // end cats

	// Custom Fields
	if ( $('#postcustom').length ) {
		$( '#the-list' ).wpList( { addAfter: function() {
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

		updateVisibility = function() {
			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'public' ) {
				$('#sticky').prop('checked', false);
				$('#sticky-span').hide();
			} else {
				$('#sticky-span').show();
			}
			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'password' ) {
				$('#password-span').hide();
			} else {
				$('#password-span').show();
			}
		};

		updateText = function() {

			if ( ! $timestampdiv.length )
				return true;

			var attemptedDate, originalDate, currentDate, publishOn, postStatus = $('#post_status'),
				optPublish = $('option[value="publish"]', postStatus), aa = $('#aa').val(),
				mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val();

			attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
			originalDate = new Date( $('#hidden_aa').val(), $('#hidden_mm').val() -1, $('#hidden_jj').val(), $('#hidden_hh').val(), $('#hidden_mn').val() );
			currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val() );

			if ( attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn ) {
				$timestampdiv.find('.timestamp-wrap').addClass('form-invalid');
				return false;
			} else {
				$timestampdiv.find('.timestamp-wrap').removeClass('form-invalid');
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
					'\n' + publishOn + ' <b>' +
					postL10n.dateFormat
						.replace( '%1$s', $( 'option[value="' + mm + '"]', '#mm' ).attr( 'data-text' ) )
						.replace( '%2$s', parseInt( jj, 10 ) )
						.replace( '%3$s', aa )
						.replace( '%4$s', ( '00' + hh ).slice( -2 ) )
						.replace( '%5$s', ( '00' + mn ).slice( -2 ) ) +
						'</b> '
				);
			}

			if ( $postVisibilitySelect.find('input:radio:checked').val() == 'private' ) {
				$('#publish').val( postL10n.update );
				if ( 0 === optPublish.length ) {
					postStatus.append('<option value="publish">' + postL10n.privatelyPublished + '</option>');
				} else {
					optPublish.html( postL10n.privatelyPublished );
				}
				$('option[value="publish"]', postStatus).prop('selected', true);
				$('#misc-publishing-actions .edit-post-status').hide();
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
					$('#misc-publishing-actions .edit-post-status').show();
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
		};

		$( '#visibility .edit-visibility').click( function( e ) {
			e.preventDefault();
			if ( $postVisibilitySelect.is(':hidden') ) {
				updateVisibility();
				$postVisibilitySelect.slideDown( 'fast', function() {
					$postVisibilitySelect.find( 'input[type="radio"]' ).first().focus();
				} );
				$(this).hide();
			}
		});

		$postVisibilitySelect.find('.cancel-post-visibility').click( function( event ) {
			$postVisibilitySelect.slideUp('fast');
			$('#visibility-radio-' + $('#hidden-post-visibility').val()).prop('checked', true);
			$('#post_password').val($('#hidden-post-password').val());
			$('#sticky').prop('checked', $('#hidden-post-sticky').prop('checked'));
			$('#post-visibility-display').html(visibility);
			$('#visibility .edit-visibility').show().focus();
			updateText();
			event.preventDefault();
		});

		$postVisibilitySelect.find('.save-post-visibility').click( function( event ) { // crazyhorse - multiple ok cancels
			$postVisibilitySelect.slideUp('fast');
			$('#visibility .edit-visibility').show().focus();
			updateText();

			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'public' ) {
				$('#sticky').prop('checked', false);
			} // WEAPON LOCKED

			if ( $('#sticky').prop('checked') ) {
				sticky = 'Sticky';
			} else {
				sticky = '';
			}

			$('#post-visibility-display').html(	postL10n[ $postVisibilitySelect.find('input:radio:checked').val() + sticky ]	);
			event.preventDefault();
		});

		$postVisibilitySelect.find('input:radio').change( function() {
			updateVisibility();
		});

		$timestampdiv.siblings('a.edit-timestamp').click( function( event ) {
			if ( $timestampdiv.is( ':hidden' ) ) {
				$timestampdiv.slideDown( 'fast', function() {
					$( 'input, select', $timestampdiv.find( '.timestamp-wrap' ) ).first().focus();
				} );
				$(this).hide();
			}
			event.preventDefault();
		});

		$timestampdiv.find('.cancel-timestamp').click( function( event ) {
			$timestampdiv.slideUp('fast').siblings('a.edit-timestamp').show().focus();
			$('#mm').val($('#hidden_mm').val());
			$('#jj').val($('#hidden_jj').val());
			$('#aa').val($('#hidden_aa').val());
			$('#hh').val($('#hidden_hh').val());
			$('#mn').val($('#hidden_mn').val());
			updateText();
			event.preventDefault();
		});

		$timestampdiv.find('.save-timestamp').click( function( event ) { // crazyhorse - multiple ok cancels
			if ( updateText() ) {
				$timestampdiv.slideUp('fast');
				$timestampdiv.siblings('a.edit-timestamp').show().focus();
			}
			event.preventDefault();
		});

		$('#post').on( 'submit', function( event ) {
			if ( ! updateText() ) {
				event.preventDefault();
				$timestampdiv.show();

				if ( wp.autosave ) {
					wp.autosave.enableButtons();
				}

				$( '#publishing-action .spinner' ).removeClass( 'is-active' );
			}
		});

		$postStatusSelect.siblings('a.edit-post-status').click( function( event ) {
			if ( $postStatusSelect.is( ':hidden' ) ) {
				$postStatusSelect.slideDown( 'fast', function() {
					$postStatusSelect.find('select').focus();
				} );
				$(this).hide();
			}
			event.preventDefault();
		});

		$postStatusSelect.find('.save-post-status').click( function( event ) {
			$postStatusSelect.slideUp( 'fast' ).siblings( 'a.edit-post-status' ).show().focus();
			updateText();
			event.preventDefault();
		});

		$postStatusSelect.find('.cancel-post-status').click( function( event ) {
			$postStatusSelect.slideUp( 'fast' ).siblings( 'a.edit-post-status' ).show().focus();
			$('#post_status').val( $('#hidden_post_status').val() );
			updateText();
			event.preventDefault();
		});
	} // end submitdiv

	// permalink
	function editPermalink() {
		var i, slug_value,
			$el, revert_e,
			c = 0,
			real_slug = $('#post_name'),
			revert_slug = real_slug.val(),
			permalink = $( '#sample-permalink' ),
			permalinkOrig = permalink.html(),
			permalinkInner = $( '#sample-permalink a' ).html(),
			buttons = $('#edit-slug-buttons'),
			buttonsOrig = buttons.html(),
			full = $('#editable-post-name-full');

		// Deal with Twemoji in the post-name
		full.find( 'img' ).replaceWith( function() { return this.alt; } );
		full = full.html();

		permalink.html( permalinkInner );
		$el = $( '#editable-post-name' );
		revert_e = $el.html();

		buttons.html( '<button type="button" class="save button button-small">' + postL10n.ok + '</button> <button type="button" class="cancel button-link">' + postL10n.cancel + '</button>' );
		buttons.children( '.save' ).click( function() {
			var new_slug = $el.children( 'input' ).val();

			if ( new_slug == $('#editable-post-name-full').text() ) {
				buttons.children('.cancel').click();
				return;
			}
			$.post(ajaxurl, {
				action: 'sample-permalink',
				post_id: postId,
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

				buttons.html(buttonsOrig);
				permalink.html(permalinkOrig);
				real_slug.val(new_slug);
				$( '.edit-slug' ).focus();
				wp.a11y.speak( postL10n.permalinkSaved );
			});
		});

		buttons.children( '.cancel' ).click( function() {
			$('#view-post-btn').show();
			$el.html(revert_e);
			buttons.html(buttonsOrig);
			permalink.html(permalinkOrig);
			real_slug.val(revert_slug);
			$( '.edit-slug' ).focus();
		});

		for ( i = 0; i < full.length; ++i ) {
			if ( '%' == full.charAt(i) )
				c++;
		}

		slug_value = ( c > full.length / 4 ) ? '' : full;
		$el.html( '<input type="text" id="new-post-slug" value="' + slug_value + '" autocomplete="off" />' ).children( 'input' ).keydown( function( e ) {
			var key = e.which;
			// On enter, just save the new slug, don't save the post.
			if ( 13 === key ) {
				e.preventDefault();
				buttons.children( '.save' ).click();
			}
			if ( 27 === key ) {
				buttons.children( '.cancel' ).click();
			}
		} ).keyup( function() {
			real_slug.val( this.value );
		}).focus();
	}

	$( '#titlediv' ).on( 'click', '.edit-slug', function() {
		editPermalink();
	});

	wptitlehint = function(id) {
		id = id || 'title';

		var title = $('#' + id), titleprompt = $('#' + id + '-prompt-text');

		if ( '' === title.val() )
			titleprompt.removeClass('screen-reader-text');

		titleprompt.click(function(){
			$(this).addClass('screen-reader-text');
			title.focus();
		});

		title.blur(function(){
			if ( '' === this.value )
				titleprompt.removeClass('screen-reader-text');
		}).focus(function(){
			titleprompt.addClass('screen-reader-text');
		}).keydown(function(e){
			titleprompt.addClass('screen-reader-text');
			$(this).unbind(e);
		});
	};

	wptitlehint();

	// Resize the visual and text editors
	( function() {
		var editor, offset, mce,
			$handle = $('#post-status-info'),
			$postdivrich = $('#postdivrich');

		// No point for touch devices
		if ( ! $textarea.length || 'ontouchstart' in window ) {
			// Hide the resize handle
			$('#content-resize-handle').hide();
			return;
		}

		function dragging( event ) {
			if ( $postdivrich.hasClass( 'wp-editor-expand' ) ) {
				return;
			}

			if ( mce ) {
				editor.theme.resizeTo( null, offset + event.pageY );
			} else {
				$textarea.height( Math.max( 50, offset + event.pageY ) );
			}

			event.preventDefault();
		}

		function endDrag() {
			var height, toolbarHeight;

			if ( $postdivrich.hasClass( 'wp-editor-expand' ) ) {
				return;
			}

			if ( mce ) {
				editor.focus();
				toolbarHeight = parseInt( $( '#wp-content-editor-container .mce-toolbar-grp' ).height(), 10 );

				if ( toolbarHeight < 10 || toolbarHeight > 200 ) {
					toolbarHeight = 30;
				}

				height = parseInt( $('#content_ifr').css('height'), 10 ) + toolbarHeight - 28;
			} else {
				$textarea.focus();
				height = parseInt( $textarea.css('height'), 10 );
			}

			$document.off( '.wp-editor-resize' );

			// sanity check
			if ( height && height > 50 && height < 5000 ) {
				setUserSetting( 'ed_size', height );
			}
		}

		$handle.on( 'mousedown.wp-editor-resize', function( event ) {
			if ( typeof tinymce !== 'undefined' ) {
				editor = tinymce.get('content');
			}

			if ( editor && ! editor.isHidden() ) {
				mce = true;
				offset = $('#content_ifr').height() - event.pageY;
			} else {
				mce = false;
				offset = $textarea.height() - event.pageY;
				$textarea.blur();
			}

			$document.on( 'mousemove.wp-editor-resize', dragging )
				.on( 'mouseup.wp-editor-resize mouseleave.wp-editor-resize', endDrag );

			event.preventDefault();
		}).on( 'mouseup.wp-editor-resize', endDrag );
	})();

	if ( typeof tinymce !== 'undefined' ) {
		// When changing post formats, change the editor body class
		$( '#post-formats-select input.post-format' ).on( 'change.set-editor-class', function() {
			var editor, body, format = this.id;

			if ( format && $( this ).prop( 'checked' ) && ( editor = tinymce.get( 'content' ) ) ) {
				body = editor.getBody();
				body.className = body.className.replace( /\bpost-format-[^ ]+/, '' );
				editor.dom.addClass( body, format == 'post-format-0' ? 'post-format-standard' : format );
				$( document ).trigger( 'editor-classchange' );
			}
		});
	}

	// Save on pressing Ctrl/Command + S in the Text editor
	$textarea.on( 'keydown.wp-autosave', function( event ) {
		if ( event.which === 83 ) {
			if ( event.shiftKey || event.altKey || ( isMac && ( ! event.metaKey || event.ctrlKey ) ) || ( ! isMac && ! event.ctrlKey ) ) {
				return;
			}

			wp.autosave && wp.autosave.server.triggerSave();
			event.preventDefault();
		}
	});

	if ( $( '#original_post_status' ).val() === 'auto-draft' && window.history.replaceState ) {
		var location;

		$( '#publish' ).on( 'click', function() {
			location = window.location.href;
			location += ( location.indexOf( '?' ) !== -1 ) ? '&' : '?';
			location += 'wp-post-new-reload=true';

			window.history.replaceState( null, null, location );
		});
	}
});

( function( $, counter ) {
	$( function() {
		var $content = $( '#content' ),
			$count = $( '#wp-word-count' ).find( '.word-count' ),
			prevCount = 0,
			contentEditor;

		function update() {
			var text, count;

			if ( ! contentEditor || contentEditor.isHidden() ) {
				text = $content.val();
			} else {
				text = contentEditor.getContent( { format: 'raw' } );
			}

			count = counter.count( text );

			if ( count !== prevCount ) {
				$count.text( count );
			}

			prevCount = count;
		}

		$( document ).on( 'tinymce-editor-init', function( event, editor ) {
			if ( editor.id !== 'content' ) {
				return;
			}

			contentEditor = editor;

			editor.on( 'nodechange keyup', _.debounce( update, 1000 ) );
		} );

		$content.on( 'input keyup', _.debounce( update, 1000 ) );

		update();
	} );
} )( jQuery, new wp.utils.WordCounter() );
