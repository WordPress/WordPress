var theList, theExtraList, toggleWithKeyboard = false, getCount, updateCount, updatePending, dashboardTotals;
(function($) {

setCommentsList = function() {
	var totalInput, perPageInput, pageInput, lastConfidentTime = 0, dimAfter, delBefore, updateTotalCount, delAfter, refillTheExtraList;

	totalInput = $('input[name="_total"]', '#comments-form');
	perPageInput = $('input[name="_per_page"]', '#comments-form');
	pageInput = $('input[name="_page"]', '#comments-form');

	dimAfter = function( r, settings ) {
		var c = $('#' + settings.element), editRow, replyID, replyButton;

		editRow = $('#replyrow');
		replyID = $('#comment_ID', editRow).val();
		replyButton = $('#replybtn', editRow);

		if ( c.is('.unapproved') ) {
			if ( settings.data.id == replyID )
				replyButton.text(adminCommentsL10n.replyApprove);

			c.find('div.comment_status').html('0');
		} else {
			if ( settings.data.id == replyID )
				replyButton.text(adminCommentsL10n.reply);

			c.find('div.comment_status').html('1');
		}

		$('span.pending-count').each( function() {
			var a = $(this), n, dif;
			n = a.html().replace(/[^0-9]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			dif = $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1;
			n = n + dif;
			if ( n < 0 ) { n = 0; }
			a.closest('.awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			updateCount(a, n);
			dashboardTotals();
		});
	};

	// Send current total, page, per_page and url
	delBefore = function( settings, list ) {
		var cl = $(settings.target).attr('class'), id, el, n, h, a, author, action = false;

		settings.data._total = totalInput.val() || 0;
		settings.data._per_page = perPageInput.val() || 0;
		settings.data._page = pageInput.val() || 0;
		settings.data._url = document.location.href;
		settings.data.comment_status = $('input[name="comment_status"]', '#comments-form').val();

		if ( cl.indexOf(':trash=1') != -1 )
			action = 'trash';
		else if ( cl.indexOf(':spam=1') != -1 )
			action = 'spam';

		if ( action ) {
			id = cl.replace(/.*?comment-([0-9]+).*/, '$1');
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

			$('strong', '#undo-' + id).text(author + ' ');
			a = $('.undo a', '#undo-' + id);
			a.attr('href', 'comment.php?action=un' + action + 'comment&c=' + id + '&_wpnonce=' + settings.data._ajax_nonce);
			a.attr('class', 'delete:the-comment-list:comment-' + id + '::un' + action + '=1 vim-z vim-destructive');
			$('.avatar', el).clone().prependTo('#undo-' + id + ' .' + action + '-undo-inside');

			a.click(function(){
				list.wpList.del(this);
				$('#undo-' + id).css( {backgroundColor:'#ceb'} ).fadeOut(350, function(){
					$(this).remove();
					$('#comment-' + id).css('backgroundColor', '').fadeIn(300, function(){ $(this).show() });
				});
				return false;
			});
		}

		return settings;
	};

	// Updates the current total (as displayed visibly)
	updateTotalCount = function( total, time, setConfidentTime ) {
		if ( time < lastConfidentTime )
			return;

		if ( setConfidentTime )
			lastConfidentTime = time;

		totalInput.val( total.toString() );
		$('span.total-type-count').each( function() {
			updateCount( $(this), total );
		});
	};

	dashboardTotals = function(n) {
		var dash = $('#dashboard_right_now'), total, appr, totalN, apprN;

		n = n || 0;
		if ( isNaN(n) || !dash.length )
			return;

		total = $('span.total-count', dash);
		appr = $('span.approved-count', dash);
		totalN = getCount(total);

		totalN = totalN + n;
		apprN = totalN - getCount( $('span.pending-count', dash) ) - getCount( $('span.spam-count', dash) );
		updateCount(total, totalN);
		updateCount(appr, apprN);

	};

	getCount = function(el) {
		var n = parseInt( el.html().replace(/[^0-9]+/g, ''), 10 );
		if ( isNaN(n) )
			return 0;
		return n;
	};

	updateCount = function(el, n) {
		var n1 = '';
		if ( isNaN(n) )
			return;
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

	updatePending = function(n) {
		$('span.pending-count').each( function() {
			var a = $(this);

			if ( n < 0 )
				n = 0;

			a.closest('.awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			updateCount(a, n);
			dashboardTotals();
		});
	};

	// In admin-ajax.php, we send back the unix time stamp instead of 1 on success
	delAfter = function( r, settings ) {
		var total, N, untrash = $(settings.target).parent().is('span.untrash'),
			unspam = $(settings.target).parent().is('span.unspam'), spam, trash, pending,
			unapproved = $('#' + settings.element).is('.unapproved');

		function getUpdate(s) {
			if ( $(settings.target).parent().is('span.' + s) )
				return 1;
			else if ( $('#' + settings.element).is('.' + s) )
				return -1;

			return 0;
		}

		spam = getUpdate('spam');
		trash = getUpdate('trash');

		if ( untrash )
			trash = -1;
		if ( unspam )
			spam = -1;

		pending = getCount( $('span.pending-count').eq(0) );

		if ( $(settings.target).parent().is('span.unapprove') || ( ( untrash || unspam ) && unapproved ) ) { // we "deleted" an approved comment from the approved list by clicking "Unapprove"
			pending = pending + 1;
		} else if ( unapproved ) { // we deleted a formerly unapproved comment
			pending = pending - 1;
		}

		updatePending(pending);

		$('span.spam-count').each( function() {
			var a = $(this), n = getCount(a) + spam;
			updateCount(a, n);
		});

		$('span.trash-count').each( function() {
			var a = $(this), n = getCount(a) + trash;
			updateCount(a, n);
		});

		if ( $('#dashboard_right_now').length ) {
			N = trash ? -1 * trash : 0;
			dashboardTotals(N);
		} else {
			total = totalInput.val() ? parseInt( totalInput.val(), 10 ) : 0;
			total = total - spam - trash;
			if ( total < 0 )
				total = 0;

			if ( ( 'object' == typeof r ) && lastConfidentTime < settings.parsed.responses[0].supplemental.time ) {
				total_items_i18n = settings.parsed.responses[0].supplemental.total_items_i18n || '';
				if ( total_items_i18n ) {
					$('.displaying-num').text( total_items_i18n );
					$('.total-pages').text( settings.parsed.responses[0].supplemental.total_pages_i18n );
					$('.tablenav-pages').find('.next-page, .last-page').toggleClass('disabled', settings.parsed.responses[0].supplemental.total_pages == $('.current-page').val());
				}
				updateTotalCount( total, settings.parsed.responses[0].supplemental.time, true );
			} else {
				updateTotalCount( total, r, false );
			}
		}


		if ( ! theExtraList || theExtraList.size() == 0 || theExtraList.children().size() == 0 || untrash || unspam ) {
			return;
		}

		theList.get(0).wpList.add( theExtraList.children(':eq(0)').remove().clone() );

		refillTheExtraList();
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
			var id = s.element.replace(/[^0-9]+/g, '');

			if ( s.target.className.indexOf(':trash=1') != -1 || s.target.className.indexOf(':spam=1') != -1 )
				$('#undo-' + id).fadeIn(300, function(){ $(this).show() });
		});
};

commentReply = {
	cid : '',
	act : '',

	init : function() {
		var row = $('#replyrow');

		$('a.cancel', row).click(function() { return commentReply.revert(); });
		$('a.save', row).click(function() { return commentReply.send(); });
		$('input#author, input#author-email, input#author-url', row).keypress(function(e){
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

		$('#doaction, #doaction2, #post-query-submit').click(function(e){
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
		if ( $(el).css('display') != 'none' )
			$(el).find('a.vim-q').click();
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
		var c;

		if ( this.cid ) {
			c = $('#comment-' + this.cid);

			if ( typeof QTags != 'undefined' )
				QTags.closeAllTags('replycontent');

			if ( this.act == 'edit-comment' )
				c.fadeIn(300, function(){ c.show() }).css('backgroundColor', '');

			$('#replyrow').hide();
			$('#com-reply').append( $('#replyrow') );
			$('#replycontent').val('');
			$('input', '#edithead').val('');
			$('.error', '#replysubmit').html('').hide();
			$('.waiting', '#replysubmit').hide();
			$('#replycontainer, #replycontent').height(120);

			this.cid = '';
		}
	},

	open : function(id, p, a) {
		var t = this, editRow, rowData, act, c = $('#comment-' + id), h = c.height(), replyButton;

		t.close();
		t.cid = id;

		editRow = $('#replyrow');
		rowData = $('#inline-'+id);
		act = t.act = (a == 'edit') ? 'edit-comment' : 'replyto-comment';

		$('#action', editRow).val(act);
		$('#comment_post_ID', editRow).val(p);
		$('#comment_ID', editRow).val(id);

		if ( h > 220 )
			$('#replycontainer, #replycontent', editRow).height(h-104);

		if ( a == 'edit' ) {
			$('#author', editRow).val( $('div.author', rowData).text() );
			$('#author-email', editRow).val( $('div.author-email', rowData).text() );
			$('#author-url', editRow).val( $('div.author-url', rowData).text() );
			$('#status', editRow).val( $('div.comment_status', rowData).text() );
			$('#replycontent', editRow).val( $('textarea.comment', rowData).val() );
			$('#edithead, #savebtn', editRow).show();
			$('#replyhead, #replybtn', editRow).hide();

			c.after( editRow ).fadeOut('fast', function(){
				$('#replyrow').fadeIn(300, function(){ $(this).show() });
			});
		} else {
			replyButton = $('#replybtn', editRow);
			$('#edithead, #savebtn', editRow).hide();
			$('#replyhead, #replybtn', editRow).show();
			c.after(editRow);

			if ( c.hasClass('unapproved') ) {
				replyButton.text(adminCommentsL10n.replyApprove);
			} else {
				replyButton.text(adminCommentsL10n.reply);
			}

			$('#replyrow').fadeIn(300, function(){ $(this).show() });
		}

		setTimeout(function() {
			var rtop, rbottom, scrollTop, vp, scrollBottom;

			rtop = $('#replyrow').offset().top;
			rbottom = rtop + $('#replyrow').height();
			scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			vp = document.documentElement.clientHeight || self.innerHeight || 0;
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
		var post = {};

		$('#replysubmit .error').hide();
		$('#replysubmit .waiting').show();

		$('#replyrow input').not(':button').each(function() {
			post[ $(this).attr('name') ] = $(this).val();
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

		t.revert();

		if ( typeof(xml) == 'string' ) {
			t.error({'responseText': xml});
			return false;
		}

		r = wpAjax.parseAjaxResponse(xml);
		if ( r.errors ) {
			t.error({'responseText': wpAjax.broken});
			return false;
		}

		r = r.responses[0];
		c = r.data;
		id = '#comment-' + r.id;
		if ( 'edit-comment' == t.act )
			$(id).remove();

		if ( r.supplemental.parent_approved ) {
			pid = $('#comment-' + r.supplemental.parent_approved);
			updatePending( getCount( $('span.pending-count').eq(0) ) - 1 );

			if ( this.comments_listing == 'moderated' ) {
				pid.animate( { 'backgroundColor':'#CCEEBB' }, 400, function(){
					pid.fadeOut();
				});
				return;
			}
		}

		$(c).hide()
		$('#replyrow').after(c);
		id = $(id);
		t.addEvents(id);
		bg = id.hasClass('unapproved') ? '#FFFFE0' : id.closest('.widefat').css('backgroundColor');

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
		var er = r.statusText;

		$('#replysubmit .waiting').hide();

		if ( r.responseText )
			er = r.responseText.replace( /<.[^<>]*?>/g, '' );

		if ( er )
			$('#replysubmit .error').html(er).show();

	}
};

$(document).ready(function(){
	var make_hotkeys_redirect, edit_comment, toggle_all, make_bulk;

	setCommentsList();
	commentReply.init();
	$(document).delegate('span.delete a.delete', 'click', function(){return false;});

	if ( typeof QTags != 'undefined' )
		quicktags({id: 'replycontent', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close'});

	if ( typeof $.table_hotkeys != 'undefined' ) {
		make_hotkeys_redirect = function(which) {
			return function() {
				var first_last, l;

				first_last = 'next' == which? 'first' : 'last';
				l = $('.tablenav-pages .'+which+'-page:not(.disabled)');
				if (l.length)
					window.location = l[0].href.replace(/\&hotkeys_highlight_(first|last)=1/g, '')+'&hotkeys_highlight_'+first_last+'=1';
			}
		};

		edit_comment = function(event, current_row) {
			window.location = $('span.edit a', current_row).attr('href');
		};

		toggle_all = function() {
			toggleWithKeyboard = true;
			$('input:checkbox', '#cb').click().prop('checked', false);
			toggleWithKeyboard = false;
		};

		make_bulk = function(value) {
			return function() {
				var scope = $('select[name="action"]');
				$('option[value="' + value + '"]', scope).prop('selected', true);
				$('#doaction').click();
			}
		};

		$.table_hotkeys(
			$('table.widefat'),
			['a', 'u', 's', 'd', 'r', 'q', 'z', ['e', edit_comment], ['shift+x', toggle_all],
			['shift+a', make_bulk('approve')], ['shift+s', make_bulk('spam')],
			['shift+d', make_bulk('delete')], ['shift+t', make_bulk('trash')],
			['shift+z', make_bulk('untrash')], ['shift+u', make_bulk('unapprove')]],
			{ highlight_first: adminCommentsL10n.hotkeys_highlight_first, highlight_last: adminCommentsL10n.hotkeys_highlight_last,
			prev_page_link_cb: make_hotkeys_redirect('prev'), next_page_link_cb: make_hotkeys_redirect('next') }
		);
	}
});

})(jQuery);
