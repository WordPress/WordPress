/* global adminCommentsL10n, thousandsSeparator, list_args, QTags, ajaxurl, wpAjax */
var setCommentsList, theList, theExtraList, commentReply;

(function($) {
var getCount, updateCount, updateCountText, updatePending, updateApproved,
	updateHtmlTitle, updateDashboardText, adminTitle = document.title,
	isDashboard = $('#dashboard_right_now').length,
	titleDiv, titleRegEx;

	getCount = function(el) {
		var n = parseInt( el.html().replace(/[^0-9]+/g, ''), 10 );
		if ( isNaN(n) ) {
			return 0;
		}
		return n;
	};

	updateCount = function(el, n) {
		var n1 = '';
		if ( isNaN(n) ) {
			return;
		}
		n = n < 1 ? '0' : n.toString();
		if ( n.length > 3 ) {
			while ( n.length > 3 ) {
				n1 = thousandsSeparator + n.substr(n.length - 3) + n1;
				n = n.substr(0, n.length - 3);
			}
			n = n + n1;
		}
		el.html(n);
	};

	updateApproved = function( diff, commentPostId ) {
		var postSelector = '.post-com-count-' + commentPostId,
			noClass = 'comment-count-no-comments',
			approvedClass = 'comment-count-approved',
			approved,
			noComments;

		updateCountText( 'span.approved-count', diff );

		if ( ! commentPostId ) {
			return;
		}

		// cache selectors to not get dupes
		approved = $( 'span.' + approvedClass, postSelector );
		noComments = $( 'span.' + noClass, postSelector );

		approved.each(function() {
			var a = $(this), n = getCount(a) + diff;
			if ( n < 1 )
				n = 0;

			if ( 0 === n ) {
				a.removeClass( approvedClass ).addClass( noClass );
			} else {
				a.addClass( approvedClass ).removeClass( noClass );
			}
			updateCount( a, n );
		});

		noComments.each(function() {
			var a = $(this);
			if ( diff > 0 ) {
				a.removeClass( noClass ).addClass( approvedClass );
			} else {
				a.addClass( noClass ).removeClass( approvedClass );
			}
			updateCount( a, diff );
		});
	};

	updateCountText = function( selector, diff ) {
		$( selector ).each(function() {
			var a = $(this), n = getCount(a) + diff;
			if ( n < 1 ) {
				n = 0;
			}
			updateCount( a, n );
		});
	};

	updateDashboardText = function ( response ) {
		if ( ! isDashboard || ! response || ! response.i18n_comments_text ) {
			return;
		}

		var rightNow = $( '#dashboard_right_now' );

		$( '.comment-count a', rightNow ).text( response.i18n_comments_text );
		$( '.comment-mod-count a', rightNow ).text( response.i18n_moderation_text )
			.parent()
			[ response.in_moderation > 0 ? 'removeClass' : 'addClass' ]( 'hidden' );
	};

	updateHtmlTitle = function ( diff ) {
		var newTitle, regExMatch, titleCount, commentFrag;

		titleRegEx = titleRegEx || new RegExp( adminCommentsL10n.docTitleCommentsCount.replace( '%s', '\\([0-9' + thousandsSeparator + ']+\\)' ) + '?' );
		// count funcs operate on a $'d element
		titleDiv = titleDiv || $( '<div />' );
		newTitle = adminTitle;

		commentFrag = titleRegEx.exec( document.title );
		if ( commentFrag ) {
			commentFrag = commentFrag[0];
			titleDiv.html( commentFrag );
			titleCount = getCount( titleDiv ) + diff;
		} else {
			titleDiv.html( 0 );
			titleCount = diff;
		}

		if ( titleCount >= 1 ) {
			updateCount( titleDiv, titleCount );
			regExMatch = titleRegEx.exec( document.title );
			if ( regExMatch ) {
				newTitle = document.title.replace( regExMatch[0], adminCommentsL10n.docTitleCommentsCount.replace( '%s', titleDiv.text() ) + ' ' );
			}
		} else {
			regExMatch = titleRegEx.exec( newTitle );
			if ( regExMatch ) {
				newTitle = newTitle.replace( regExMatch[0], adminCommentsL10n.docTitleComments );
			}
		}
		document.title = newTitle;
	};

	updatePending = function( diff, commentPostId ) {
		var postSelector = '.post-com-count-' + commentPostId,
			noClass = 'comment-count-no-pending',
			noParentClass = 'post-com-count-no-pending',
			pendingClass = 'comment-count-pending',
			pending,
			noPending;

		if ( ! isDashboard ) {
			updateHtmlTitle( diff );
		}

		$( 'span.pending-count' ).each(function() {
			var a = $(this), n = getCount(a) + diff;
			if ( n < 1 )
				n = 0;
			a.closest('.awaiting-mod')[ 0 === n ? 'addClass' : 'removeClass' ]('count-0');
			updateCount( a, n );
		});

		if ( ! commentPostId ) {
			return;
		}

		// cache selectors to not get dupes
		pending = $( 'span.' + pendingClass, postSelector );
		noPending = $( 'span.' + noClass, postSelector );

		pending.each(function() {
			var a = $(this), n = getCount(a) + diff;
			if ( n < 1 )
				n = 0;

			if ( 0 === n ) {
				a.parent().addClass( noParentClass );
				a.removeClass( pendingClass ).addClass( noClass );
			} else {
				a.parent().removeClass( noParentClass );
				a.addClass( pendingClass ).removeClass( noClass );
			}
			updateCount( a, n );
		});

		noPending.each(function() {
			var a = $(this);
			if ( diff > 0 ) {
				a.parent().removeClass( noParentClass );
				a.removeClass( noClass ).addClass( pendingClass );
			} else {
				a.parent().addClass( noParentClass );
				a.addClass( noClass ).removeClass( pendingClass );
			}
			updateCount( a, diff );
		});
	};

setCommentsList = function() {
	var totalInput, perPageInput, pageInput, dimAfter, delBefore, updateTotalCount, delAfter, refillTheExtraList, diff,
		lastConfidentTime = 0;

	totalInput = $('input[name="_total"]', '#comments-form');
	perPageInput = $('input[name="_per_page"]', '#comments-form');
	pageInput = $('input[name="_page"]', '#comments-form');

	// Updates the current total (stored in the _total input)
	updateTotalCount = function( total, time, setConfidentTime ) {
		if ( time < lastConfidentTime )
			return;

		if ( setConfidentTime )
			lastConfidentTime = time;

		totalInput.val( total.toString() );
	};

	// this fires when viewing "All"
	dimAfter = function( r, settings ) {
		var editRow, replyID, replyButton, response,
			c = $( '#' + settings.element );

		if ( true !== settings.parsed ) {
			response = settings.parsed.responses[0];
		}

		editRow = $('#replyrow');
		replyID = $('#comment_ID', editRow).val();
		replyButton = $('#replybtn', editRow);

		if ( c.is('.unapproved') ) {
			if ( settings.data.id == replyID )
				replyButton.text(adminCommentsL10n.replyApprove);

			c.find( '.row-actions span.view' ).addClass( 'hidden' ).end()
				.find( 'div.comment_status' ).html( '0' );

		} else {
			if ( settings.data.id == replyID )
				replyButton.text(adminCommentsL10n.reply);

			c.find( '.row-actions span.view' ).removeClass( 'hidden' ).end()
				.find( 'div.comment_status' ).html( '1' );
		}

		diff = $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1;
		if ( response ) {
			updateDashboardText( response.supplemental );
			updatePending( diff, response.supplemental.postId );
			updateApproved( -1 * diff, response.supplemental.postId );
		} else {
			updatePending( diff );
			updateApproved( -1 * diff  );
		}
	};

	// Send current total, page, per_page and url
	delBefore = function( settings, list ) {
		var note, id, el, n, h, a, author,
			action = false,
			wpListsData = $( settings.target ).attr( 'data-wp-lists' );

		settings.data._total = totalInput.val() || 0;
		settings.data._per_page = perPageInput.val() || 0;
		settings.data._page = pageInput.val() || 0;
		settings.data._url = document.location.href;
		settings.data.comment_status = $('input[name="comment_status"]', '#comments-form').val();

		if ( wpListsData.indexOf(':trash=1') != -1 )
			action = 'trash';
		else if ( wpListsData.indexOf(':spam=1') != -1 )
			action = 'spam';

		if ( action ) {
			id = wpListsData.replace(/.*?comment-([0-9]+).*/, '$1');
			el = $('#comment-' + id);
			note = $('#' + action + '-undo-holder').html();

			el.find('.check-column :checkbox').prop('checked', false); // Uncheck the row so as not to be affected by Bulk Edits.

			if ( el.siblings('#replyrow').length && commentReply.cid == id )
				commentReply.close();

			if ( el.is('tr') ) {
				n = el.children(':visible').length;
				author = $('.author strong', el).text();
				h = $('<tr id="undo-' + id + '" class="undo un' + action + '" style="display:none;"><td colspan="' + n + '">' + note + '</td></tr>');
			} else {
				author = $('.comment-author', el).text();
				h = $('<div id="undo-' + id + '" style="display:none;" class="undo un' + action + '">' + note + '</div>');
			}

			el.before(h);

			$('strong', '#undo-' + id).text(author);
			a = $('.undo a', '#undo-' + id);
			a.attr('href', 'comment.php?action=un' + action + 'comment&c=' + id + '&_wpnonce=' + settings.data._ajax_nonce);
			a.attr('data-wp-lists', 'delete:the-comment-list:comment-' + id + '::un' + action + '=1');
			a.attr('class', 'vim-z vim-destructive');
			$('.avatar', el).first().clone().prependTo('#undo-' + id + ' .' + action + '-undo-inside');

			a.click(function( e ){
				e.preventDefault();
				e.stopPropagation(); // ticket #35904
				list.wpList.del(this);
				$('#undo-' + id).css( {backgroundColor:'#ceb'} ).fadeOut(350, function(){
					$(this).remove();
					$('#comment-' + id).css('backgroundColor', '').fadeIn(300, function(){ $(this).show(); });
				});
			});
		}

		return settings;
	};

	// In admin-ajax.php, we send back the unix time stamp instead of 1 on success
	delAfter = function( r, settings ) {
		var total_items_i18n, total, animated, animatedCallback,
			response = true === settings.parsed ? {} : settings.parsed.responses[0],
			commentStatus = true === settings.parsed ? '' : response.supplemental.status,
			commentPostId = true === settings.parsed ? '' : response.supplemental.postId,
			newTotal = true === settings.parsed ? '' : response.supplemental,

			targetParent = $( settings.target ).parent(),
			commentRow = $('#' + settings.element),

			spamDiff, trashDiff, pendingDiff, approvedDiff,

			approved = commentRow.hasClass( 'approved' ),
			unapproved = commentRow.hasClass( 'unapproved' ),
			spammed = commentRow.hasClass( 'spam' ),
			trashed = commentRow.hasClass( 'trash' ),
			undoing = false; // ticket #35904

		updateDashboardText( newTotal );

		// the order of these checks is important
		// .unspam can also have .approve or .unapprove
		// .untrash can also have .approve or .unapprove

		if ( targetParent.is( 'span.undo' ) ) {
			// the comment was spammed
			if ( targetParent.hasClass( 'unspam' ) ) {
				spamDiff = -1;

				if ( 'trash' === commentStatus ) {
					trashDiff = 1;
				} else if ( '1' === commentStatus ) {
					approvedDiff = 1;
				} else if ( '0' === commentStatus ) {
					pendingDiff = 1;
				}

			// the comment was trashed
			} else if ( targetParent.hasClass( 'untrash' ) ) {
				trashDiff = -1;

				if ( 'spam' === commentStatus ) {
					spamDiff = 1;
				} else if ( '1' === commentStatus ) {
					approvedDiff = 1;
				} else if ( '0' === commentStatus ) {
					pendingDiff = 1;
				}
			}

			undoing = true;

		// user clicked "Spam"
		} else if ( targetParent.is( 'span.spam' ) ) {
			// the comment is currently approved
			if ( approved ) {
				approvedDiff = -1;
			// the comment is currently pending
			} else if ( unapproved ) {
				pendingDiff = -1;
			// the comment was in the trash
			} else if ( trashed ) {
				trashDiff = -1;
			}
			// you can't spam an item on the spam screen
			spamDiff = 1;

		// user clicked "Unspam"
		} else if ( targetParent.is( 'span.unspam' ) ) {
			if ( approved ) {
				pendingDiff = 1;
			} else if ( unapproved ) {
				approvedDiff = 1;
			} else if ( trashed ) {
				// the comment was previously approved
				if ( targetParent.hasClass( 'approve' ) ) {
					approvedDiff = 1;
				// the comment was previously pending
				} else if ( targetParent.hasClass( 'unapprove' ) ) {
					pendingDiff = 1;
				}
			} else if ( spammed ) {
				if ( targetParent.hasClass( 'approve' ) ) {
					approvedDiff = 1;

				} else if ( targetParent.hasClass( 'unapprove' ) ) {
					pendingDiff = 1;
				}
			}
			// you can Unspam an item on the spam screen
			spamDiff = -1;

		// user clicked "Trash"
		} else if ( targetParent.is( 'span.trash' ) ) {
			if ( approved ) {
				approvedDiff = -1;
			} else if ( unapproved ) {
				pendingDiff = -1;
			// the comment was in the spam queue
			} else if ( spammed ) {
				spamDiff = -1;
			}
			// you can't trash an item on the trash screen
			trashDiff = 1;

		// user clicked "Restore"
		} else if ( targetParent.is( 'span.untrash' ) ) {
			if ( approved ) {
				pendingDiff = 1;
			} else if ( unapproved ) {
				approvedDiff = 1;
			} else if ( trashed ) {
				if ( targetParent.hasClass( 'approve' ) ) {
					approvedDiff = 1;
				} else if ( targetParent.hasClass( 'unapprove' ) ) {
					pendingDiff = 1;
				}
			}
			// you can't go from trash to spam
			// you can untrash on the trash screen
			trashDiff = -1;

		// User clicked "Approve"
		} else if ( targetParent.is( 'span.approve:not(.unspam):not(.untrash)' ) ) {
			approvedDiff = 1;
			pendingDiff = -1;

		// User clicked "Unapprove"
		} else if ( targetParent.is( 'span.unapprove:not(.unspam):not(.untrash)' ) ) {
			approvedDiff = -1;
			pendingDiff = 1;

		// User clicked "Delete Permanently"
		} else if ( targetParent.is( 'span.delete' ) ) {
			if ( spammed ) {
				spamDiff = -1;
			} else if ( trashed ) {
				trashDiff = -1;
			}
		}

		if ( pendingDiff ) {
			updatePending( pendingDiff, commentPostId );
			updateCountText( 'span.all-count', pendingDiff );
		}

		if ( approvedDiff ) {
			updateApproved( approvedDiff, commentPostId );
			updateCountText( 'span.all-count', approvedDiff );
		}

		if ( spamDiff ) {
			updateCountText( 'span.spam-count', spamDiff );
		}

		if ( trashDiff ) {
			updateCountText( 'span.trash-count', trashDiff );
		}

		if (
			( ( 'trash' === settings.data.comment_status ) && !getCount( $( 'span.trash-count' ) ) ) ||
			( ( 'spam' === settings.data.comment_status ) && !getCount( $( 'span.spam-count' ) ) )
		) {
			$( '#delete_all' ).hide();
		}

		if ( ! isDashboard ) {
			total = totalInput.val() ? parseInt( totalInput.val(), 10 ) : 0;
			if ( $(settings.target).parent().is('span.undo') )
				total++;
			else
				total--;

			if ( total < 0 )
				total = 0;

			if ( 'object' === typeof r ) {
				if ( response.supplemental.total_items_i18n && lastConfidentTime < response.supplemental.time ) {
					total_items_i18n = response.supplemental.total_items_i18n || '';
					if ( total_items_i18n ) {
						$('.displaying-num').text( total_items_i18n );
						$('.total-pages').text( response.supplemental.total_pages_i18n );
						$('.tablenav-pages').find('.next-page, .last-page').toggleClass('disabled', response.supplemental.total_pages == $('.current-page').val());
					}
					updateTotalCount( total, response.supplemental.time, true );
				} else if ( response.supplemental.time ) {
					updateTotalCount( total, response.supplemental.time, false );
				}
			} else {
				updateTotalCount( total, r, false );
			}
		}

		if ( ! theExtraList || theExtraList.length === 0 || theExtraList.children().length === 0 || undoing ) {
			return;
		}

		theList.get(0).wpList.add( theExtraList.children( ':eq(0):not(.no-items)' ).remove().clone() );

		refillTheExtraList();

		animated = $( ':animated', '#the-comment-list' );
		animatedCallback = function () {
			if ( ! $( '#the-comment-list tr:visible' ).length ) {
				theList.get(0).wpList.add( theExtraList.find( '.no-items' ).clone() );
			}
		};

		if ( animated.length ) {
			animated.promise().done( animatedCallback );
		} else {
			animatedCallback();
		}
	};

	refillTheExtraList = function(ev) {
		var args = $.query.get(), total_pages = $('.total-pages').text(), per_page = $('input[name="_per_page"]', '#comments-form').val();

		if (! args.paged)
			args.paged = 1;

		if (args.paged > total_pages) {
			return;
		}

		if (ev) {
			theExtraList.empty();
			args.number = Math.min(8, per_page); // see WP_Comments_List_Table::prepare_items() @ class-wp-comments-list-table.php
		} else {
			args.number = 1;
			args.offset = Math.min(8, per_page) - 1; // fetch only the next item on the extra list
		}

		args.no_placeholder = true;

		args.paged ++;

		// $.query.get() needs some correction to be sent into an ajax request
		if ( true === args.comment_type )
			args.comment_type = '';

		args = $.extend(args, {
			'action': 'fetch-list',
			'list_args': list_args,
			'_ajax_fetch_list_nonce': $('#_ajax_fetch_list_nonce').val()
		});

		$.ajax({
			url: ajaxurl,
			global: false,
			dataType: 'json',
			data: args,
			success: function(response) {
				theExtraList.get(0).wpList.add( response.rows );
			}
		});
	};

	theExtraList = $('#the-extra-comment-list').wpList( { alt: '', delColor: 'none', addColor: 'none' } );
	theList = $('#the-comment-list').wpList( { alt: '', delBefore: delBefore, dimAfter: dimAfter, delAfter: delAfter, addColor: 'none' } )
		.bind('wpListDelEnd', function(e, s){
			var wpListsData = $(s.target).attr('data-wp-lists'), id = s.element.replace(/[^0-9]+/g, '');

			if ( wpListsData.indexOf(':trash=1') != -1 || wpListsData.indexOf(':spam=1') != -1 )
				$('#undo-' + id).fadeIn(300, function(){ $(this).show(); });
		});
};

commentReply = {
	cid : '',
	act : '',
	originalContent : '',

	init : function() {
		var row = $('#replyrow');

		$('a.cancel', row).click(function() { return commentReply.revert(); });
		$('a.save', row).click(function() { return commentReply.send(); });
		$( 'input#author-name, input#author-email, input#author-url', row ).keypress( function( e ) {
			if ( e.which == 13 ) {
				commentReply.send();
				e.preventDefault();
				return false;
			}
		});

		// add events
		$('#the-comment-list .column-comment > p').dblclick(function(){
			commentReply.toggle($(this).parent());
		});

		$('#doaction, #doaction2, #post-query-submit').click(function(){
			if ( $('#the-comment-list #replyrow').length > 0 )
				commentReply.close();
		});

		this.comments_listing = $('#comments-form > input[name="comment_status"]').val() || '';

		/* $(listTable).bind('beforeChangePage', function(){
			commentReply.close();
		}); */
	},

	addEvents : function(r) {
		r.each(function() {
			$(this).find('.column-comment > p').dblclick(function(){
				commentReply.toggle($(this).parent());
			});
		});
	},

	toggle : function(el) {
		if ( 'none' !== $( el ).css( 'display' ) && ( $( '#replyrow' ).parent().is('#com-reply') || window.confirm( adminCommentsL10n.warnQuickEdit ) ) ) {
			$( el ).find( 'a.vim-q' ).click();
		}
	},

	revert : function() {

		if ( $('#the-comment-list #replyrow').length < 1 )
			return false;

		$('#replyrow').fadeOut('fast', function(){
			commentReply.close();
		});

		return false;
	},

	close : function() {
		var commentRow = $(),
			replyRow = $( '#replyrow' );

		// replyrow is not showing?
		if ( replyRow.parent().is( '#com-reply' ) ) {
			return;
		}

		if ( this.cid ) {
			commentRow = $( '#comment-' + this.cid );
		}

		/*
		 * When closing the Quick Edit form, show the comment row and move focus
		 * back to the Quick Edit button.
		 */
		if ( 'edit-comment' === this.act ) {
			commentRow.fadeIn( 300, function() {
				commentRow
					.show()
					.find( '.vim-q' )
						.attr( 'aria-expanded', 'false' )
						.focus();
			} ).css( 'backgroundColor', '' );
		}

		// When closing the Reply form, move focus back to the Reply button.
		if ( 'replyto-comment' === this.act ) {
			commentRow.find( '.vim-r' )
				.attr( 'aria-expanded', 'false' )
				.focus();
		}

		// reset the Quicktags buttons
		if ( typeof QTags != 'undefined' )
			QTags.closeAllTags('replycontent');

		$('#add-new-comment').css('display', '');

		replyRow.hide();
		$( '#com-reply' ).append( replyRow );
		$('#replycontent').css('height', '').val('');
		$('#edithead input').val('');
		$( '.notice-error', replyRow )
			.addClass( 'hidden' )
			.find( '.error' ).empty();
		$( '.spinner', replyRow ).removeClass( 'is-active' );

		this.cid = '';
		this.originalContent = '';
	},

	open : function(comment_id, post_id, action) {
		var editRow, rowData, act, replyButton, editHeight,
			t = this,
			c = $('#comment-' + comment_id),
			h = c.height(),
			colspanVal = 0;

		if ( ! this.discardCommentChanges() ) {
			return false;
		}

		t.close();
		t.cid = comment_id;

		editRow = $('#replyrow');
		rowData = $('#inline-'+comment_id);
		action = action || 'replyto';
		act = 'edit' == action ? 'edit' : 'replyto';
		act = t.act = act + '-comment';
		t.originalContent = $('textarea.comment', rowData).val();
		colspanVal = $( '> th:visible, > td:visible', c ).length;

		// Make sure it's actually a table and there's a `colspan` value to apply.
		if ( editRow.hasClass( 'inline-edit-row' ) && 0 !== colspanVal ) {
			$( 'td', editRow ).attr( 'colspan', colspanVal );
		}

		$('#action', editRow).val(act);
		$('#comment_post_ID', editRow).val(post_id);
		$('#comment_ID', editRow).val(comment_id);

		if ( action == 'edit' ) {
			$( '#author-name', editRow ).val( $( 'div.author', rowData ).text() );
			$('#author-email', editRow).val( $('div.author-email', rowData).text() );
			$('#author-url', editRow).val( $('div.author-url', rowData).text() );
			$('#status', editRow).val( $('div.comment_status', rowData).text() );
			$('#replycontent', editRow).val( $('textarea.comment', rowData).val() );
			$( '#edithead, #editlegend, #savebtn', editRow ).show();
			$('#replyhead, #replybtn, #addhead, #addbtn', editRow).hide();

			if ( h > 120 ) {
				// Limit the maximum height when editing very long comments to make it more manageable.
				// The textarea is resizable in most browsers, so the user can adjust it if needed.
				editHeight = h > 500 ? 500 : h;
				$('#replycontent', editRow).css('height', editHeight + 'px');
			}

			c.after( editRow ).fadeOut('fast', function(){
				$('#replyrow').fadeIn(300, function(){ $(this).show(); });
			});
		} else if ( action == 'add' ) {
			$('#addhead, #addbtn', editRow).show();
			$( '#replyhead, #replybtn, #edithead, #editlegend, #savebtn', editRow ) .hide();
			$('#the-comment-list').prepend(editRow);
			$('#replyrow').fadeIn(300);
		} else {
			replyButton = $('#replybtn', editRow);
			$( '#edithead, #editlegend, #savebtn, #addhead, #addbtn', editRow ).hide();
			$('#replyhead, #replybtn', editRow).show();
			c.after(editRow);

			if ( c.hasClass('unapproved') ) {
				replyButton.text(adminCommentsL10n.replyApprove);
			} else {
				replyButton.text(adminCommentsL10n.reply);
			}

			$('#replyrow').fadeIn(300, function(){ $(this).show(); });
		}

		setTimeout(function() {
			var rtop, rbottom, scrollTop, vp, scrollBottom;

			rtop = $('#replyrow').offset().top;
			rbottom = rtop + $('#replyrow').height();
			scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			vp = document.documentElement.clientHeight || window.innerHeight || 0;
			scrollBottom = scrollTop + vp;

			if ( scrollBottom - 20 < rbottom )
				window.scroll(0, rbottom - vp + 35);
			else if ( rtop - 20 < scrollTop )
				window.scroll(0, rtop - 35);

			$('#replycontent').focus().keyup(function(e){
				if ( e.which == 27 )
					commentReply.revert(); // close on Escape
			});
		}, 600);

		return false;
	},

	send : function() {
		var post = {},
			$errorNotice = $( '#replysubmit .error-notice' );

		$errorNotice.addClass( 'hidden' );
		$( '#replysubmit .spinner' ).addClass( 'is-active' );

		$('#replyrow input').not(':button').each(function() {
			var t = $(this);
			post[ t.attr('name') ] = t.val();
		});

		post.content = $('#replycontent').val();
		post.id = post.comment_post_ID;
		post.comments_listing = this.comments_listing;
		post.p = $('[name="p"]').val();

		if ( $('#comment-' + $('#comment_ID').val()).hasClass('unapproved') )
			post.approve_parent = 1;

		$.ajax({
			type : 'POST',
			url : ajaxurl,
			data : post,
			success : function(x) { commentReply.show(x); },
			error : function(r) { commentReply.error(r); }
		});

		return false;
	},

	show : function(xml) {
		var t = this, r, c, id, bg, pid;

		if ( typeof(xml) == 'string' ) {
			t.error({'responseText': xml});
			return false;
		}

		r = wpAjax.parseAjaxResponse(xml);
		if ( r.errors ) {
			t.error({'responseText': wpAjax.broken});
			return false;
		}

		t.revert();

		r = r.responses[0];
		id = '#comment-' + r.id;

		if ( 'edit-comment' == t.act )
			$(id).remove();

		if ( r.supplemental.parent_approved ) {
			pid = $('#comment-' + r.supplemental.parent_approved);
			updatePending( -1, r.supplemental.parent_post_id );

			if ( this.comments_listing == 'moderated' ) {
				pid.animate( { 'backgroundColor':'#CCEEBB' }, 400, function(){
					pid.fadeOut();
				});
				return;
			}
		}

		if ( r.supplemental.i18n_comments_text ) {
			if ( isDashboard ) {
				updateDashboardText( r.supplemental );
			} else {
				updateApproved( 1, r.supplemental.parent_post_id );
				updateCountText( 'span.all-count', 1 );
			}
		}

		c = $.trim(r.data); // Trim leading whitespaces
		$(c).hide();
		$('#replyrow').after(c);

		id = $(id);
		t.addEvents(id);
		bg = id.hasClass('unapproved') ? '#FFFFE0' : id.closest('.widefat, .postbox').css('backgroundColor');

		id.animate( { 'backgroundColor':'#CCEEBB' }, 300 )
			.animate( { 'backgroundColor': bg }, 300, function() {
				if ( pid && pid.length ) {
					pid.animate( { 'backgroundColor':'#CCEEBB' }, 300 )
						.animate( { 'backgroundColor': bg }, 300 )
						.removeClass('unapproved').addClass('approved')
						.find('div.comment_status').html('1');
				}
			});

	},

	error : function(r) {
		var er = r.statusText,
			$errorNotice = $( '#replysubmit .notice-error' ),
			$error = $errorNotice.find( '.error' );

		$( '#replysubmit .spinner' ).removeClass( 'is-active' );

		if ( r.responseText )
			er = r.responseText.replace( /<.[^<>]*?>/g, '' );

		if ( er ) {
			$errorNotice.removeClass( 'hidden' );
			$error.html( er );
		}
	},

	addcomment: function(post_id) {
		var t = this;

		$('#add-new-comment').fadeOut(200, function(){
			t.open(0, post_id, 'add');
			$('table.comments-box').css('display', '');
			$('#no-comments').remove();
		});
	},

	/**
	 * Alert the user if they have unsaved changes on a comment that will be
	 * lost if they proceed.
	 *
	 * @returns {boolean}
	 */
	discardCommentChanges: function() {
		var editRow = $( '#replyrow' );

		if  ( this.originalContent === $( '#replycontent', editRow ).val() ) {
			return true;
		}

		return window.confirm( adminCommentsL10n.warnCommentChanges );
	}
};

$(document).ready(function(){
	var make_hotkeys_redirect, edit_comment, toggle_all, make_bulk;

	setCommentsList();
	commentReply.init();

	$(document).on( 'click', 'span.delete a.delete', function( e ) {
		e.preventDefault();
	});

	if ( typeof $.table_hotkeys != 'undefined' ) {
		make_hotkeys_redirect = function(which) {
			return function() {
				var first_last, l;

				first_last = 'next' == which? 'first' : 'last';
				l = $('.tablenav-pages .'+which+'-page:not(.disabled)');
				if (l.length)
					window.location = l[0].href.replace(/\&hotkeys_highlight_(first|last)=1/g, '')+'&hotkeys_highlight_'+first_last+'=1';
			};
		};

		edit_comment = function(event, current_row) {
			window.location = $('span.edit a', current_row).attr('href');
		};

		toggle_all = function() {
			$('#cb-select-all-1').data( 'wp-toggle', 1 ).trigger( 'click' ).removeData( 'wp-toggle' );
		};

		make_bulk = function(value) {
			return function() {
				var scope = $('select[name="action"]');
				$('option[value="' + value + '"]', scope).prop('selected', true);
				$('#doaction').click();
			};
		};

		$.table_hotkeys(
			$('table.widefat'),
			[
				'a', 'u', 's', 'd', 'r', 'q', 'z',
				['e', edit_comment],
				['shift+x', toggle_all],
				['shift+a', make_bulk('approve')],
				['shift+s', make_bulk('spam')],
				['shift+d', make_bulk('delete')],
				['shift+t', make_bulk('trash')],
				['shift+z', make_bulk('untrash')],
				['shift+u', make_bulk('unapprove')]
			],
			{
				highlight_first: adminCommentsL10n.hotkeys_highlight_first,
				highlight_last: adminCommentsL10n.hotkeys_highlight_last,
				prev_page_link_cb: make_hotkeys_redirect('prev'),
				next_page_link_cb: make_hotkeys_redirect('next'),
				hotkeys_opts: {
					disableInInput: true,
					type: 'keypress',
					noDisable: '.check-column input[type="checkbox"]'
				},
				cycle_expr: '#the-comment-list tr',
				start_row_index: 0
			}
		);
	}

	// Quick Edit and Reply have an inline comment editor.
	$( '#the-comment-list' ).on( 'click', '.comment-inline', function() {
		var $el = $( this ),
			action = 'replyto';

		if ( 'undefined' !== typeof $el.data( 'action' ) ) {
			action = $el.data( 'action' );
		}

		$( this ).attr( 'aria-expanded', 'true' );
		commentReply.open( $el.data( 'commentId' ), $el.data( 'postId' ), action );
	} );
});

})(jQuery);
