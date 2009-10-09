var theList, theExtraList, toggleWithKeyboard = false;
(function($) {

setCommentsList = function() {
	var totalInput, perPageInput, pageInput, lastConfidentTime = 0, dimAfter, delBefore, updateTotalCount, delAfter;

	totalInput = $('.tablenav input[name="_total"]', '#comments-form');
	perPageInput = $('.tablenav input[name="_per_page"]', '#comments-form');
	pageInput = $('.tablenav input[name="_page"]', '#comments-form');

	dimAfter = function( r, settings ) {
		var c = $('#' + settings.element);

		if ( c.is('.unapproved') )
			c.find('div.comment_status').html('0')
		else
			c.find('div.comment_status').html('1')

		$('span.pending-count').each( function() {
			var a = $(this), n, dif;
			n = a.html().replace(/[^0-9]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			dif = $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1;
			n = n + dif;
			if ( n < 0 ) { n = 0; }
			a.closest('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			updateCount(a, n);
			dashboardTotals();
		});
	};

	// Send current total, page, per_page and url
	delBefore = function( settings, list ) {
		var cl = $(settings.target).attr('className'), id, el, n, h, a, to, author;

		settings.data._total = totalInput.val() || 0;
		settings.data._per_page = perPageInput.val() || 0;
		settings.data._page = pageInput.val() || 0;
		settings.data._url = document.location.href;

		if ( cl.indexOf(':trash=1') != -1 ) {
			id = cl.replace(/.*?comment-([0-9]+).*/, '$1');
			el = $('#comment-' + id);
			note = $('#undo-holder').html();

			if ( el.is('tr') ) {
				n = el.children(':visible').length;
				author = $('.author strong', el).html();
				h = $('<tr id="trashundo-' + id + '" style="display:none;"><td class="trash-undo" colspan="' + n + '">' + note + '</td></tr>');
			} else {
				author = $('.comment-author', el).html();
				h = $('<div id="trashundo-' + id + '" style="display:none;" class="trash-undo">' + note + '</div>');
			}

			el.before(h);
			h.fadeIn(400);

			$('strong', '#trashundo-' + id).html(author);
			a = $('a.undo-trash', '#trashundo-' + id);
			a.attr('href', 'comment.php?action=untrashcomment&c=' + id + '&_ajax_nonce=' + settings.data._ajax_nonce);
			a.attr('className', 'delete:the-comment-list:comment-' + id + '::untrash=1 vim-z vim-destructive');

			a.click(function(){
				list.wpList.del(this);
				$('#trashundo-' + id).fadeOut(250, function(){
					$(this).remove();
					$('#comment-' + id).css('backgroundColor', '').fadeIn(400);
				});
				return false;
			});

			to = window.setTimeout( function(){
				$('#trashundo-' + id).fadeOut('slow', function(){ $(this).remove(); });
			}, 200000 );
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

	function dashboardTotals(n) {
		var dash = $('#dashboard_right_now'), total, appr, totalN, apprN;

		n = n || 0;
		if ( isNaN(n) || !dash.length )
			return;

		total = $('span.total-count', dash);
		appr = $('span.approved-count', dash);
		totalN = getCount(total);
		apprN = getCount(appr);

		if ( totalN ) {
			totalN = totalN + n;
			apprN = totalN - getCount( $('span.pending-count', dash) );
			updateCount(total, totalN);
			updateCount(appr, apprN);
		}
	}

	function getCount(el) {
		var n = parseInt( el.html().replace(/[^0-9]+/g, ''), 10 );
		if ( isNaN(n) )
			return 0;
		return n;
	}

	function updateCount(el, n) {
		if ( isNaN(n) )
			return;
		n = n < 1 ? '0' : n.toString();
		if ( n.length > 3 )
			n = n.substr(0, n.length-3) + thousandsSeparator + n.substr(-3);
		el.html(n);
	}

	// In admin-ajax.php, we send back the unix time stamp instead of 1 on success
	delAfter = function( r, settings ) {
		var total, pageLinks, N, untrash = $(settings.target).parent().is('span.untrash'), spam, trash;

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

		$('span.pending-count').each( function() {
			var a = $(this), n = getCount(a), unapproved = $('#' + settings.element).is('.unapproved');

			if ( $(settings.target).parent().is('span.unapprove') || ( untrash && unapproved ) ) { // we "deleted" an approved comment from the approved list by clicking "Unapprove"
				n = n + 1;
			} else if ( unapproved ) { // we deleted a formerly unapproved comment
				n = n - 1;
			}
			if ( n < 0 ) { n = 0; }
			a.closest('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			updateCount(a, n);
			dashboardTotals();
		});

		$('span.spam-count').each( function() {
			var a = $(this), n = getCount(a) + spam;
			updateCount(a, n);
		});

		$('span.trash-count').each( function() {
			var a = $(this), n = getCount(a) + trash;
			updateCount(a, n);
		});

		if ( $('#dashboard_right_now').length ) {
			N = spam || trash || 0;
			if ( N > 0 )
				dashboardTotals(-1);
			else if ( N < 0 )
				dashboardTotals(1);
		} else {
			// XML response
			if ( ( 'object' == typeof r ) && lastConfidentTime < settings.parsed.responses[0].supplemental.time ) {
				pageLinks = settings.parsed.responses[0].supplemental.pageLinks || '';
				if ( $.trim( pageLinks ) )
					$('.tablenav-pages').find( '.page-numbers' ).remove().end().append( $( pageLinks ) );
				else
					$('.tablenav-pages').find( '.page-numbers' ).remove();
			}

			total = totalInput.val() ? parseInt( totalInput.val(), 10 ) : 0;
			total = total - spam - trash;
			if ( total < 0 )
				total = 0;

			updateTotalCount( total, r, false );
		}

		if ( theExtraList.size() == 0 || theExtraList.children().size() == 0 || untrash ) {
			return;
		}

		theList.get(0).wpList.add( theExtraList.children(':eq(0)').remove().clone() );
		$('#get-extra-comments').submit();
	};

	theExtraList = $('#the-extra-comment-list').wpList( { alt: '', delColor: 'none', addColor: 'none' } );
	theList = $('#the-comment-list').wpList( { alt: '', delBefore: delBefore, dimAfter: dimAfter, delAfter: delAfter, addColor: 'none' } );

};

commentReply = {

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
		$(this.o).fadeIn('fast').css('backgroundColor', '');
		$('#com-reply').append( $('#replyrow') );
		$('#replycontent').val('');
		$('#edithead input').val('');
		$('#replysubmit .error').html('').hide();
		$('#replysubmit .waiting').hide();
		if ( $.browser.msie )
			$('#replycontainer, #replycontent').css('height', '120px');
		else
			$('#replycontainer').resizable('destroy').css('height', '120px');
	},

	open : function(id, p, a) {
		var t = this, editRow, act, h;
		t.close();
		t.o = '#comment-'+id;

		$('#replyrow td').attr('colspan', $('.widefat thead th:visible').length);
		editRow = $('#replyrow'), rowData = $('#inline-'+id);
		act = t.act = (a == 'edit') ? 'edit-comment' : 'replyto-comment';

		$('#action', editRow).val(act);
		$('#comment_post_ID', editRow).val(p);
		$('#comment_ID', editRow).val(id);

		if ( a == 'edit' ) {
			$('#author', editRow).val( $('div.author', rowData).text() );
			$('#author-email', editRow).val( $('div.author-email', rowData).text() );
			$('#author-url', editRow).val( $('div.author-url', rowData).text() );
			$('#status', editRow).val( $('div.comment_status', rowData).text() );
			$('#replycontent', editRow).val( $('textarea.comment', rowData).val() );
			$('#edithead, #savebtn', editRow).show();
			$('#replyhead, #replybtn', editRow).hide();

			h = $(t.o).height();
			if ( h > 220 )
				if ( $.browser.msie )
					$('#replycontainer, #replycontent', editRow).height(h-105);
				else
					$('#replycontainer', editRow).height(h-105);

			$(t.o).after(editRow.hide()).fadeOut('fast', function(){
				$('#replyrow').fadeIn('fast');
			});
		} else {
			$('#edithead, #savebtn', editRow).hide();
			$('#replyhead, #replybtn', editRow).show();
			$(t.o).after(editRow);
			$('#replyrow').hide().fadeIn('fast');
		}

		if ( ! $.browser.msie )
			$('#replycontainer').resizable({
				handles : 's',
				axis : 'y',
				minHeight : 80,
				stop : function() {
					$('#replycontainer').width('auto');
				}
			});

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
				if (e.which == 27) commentReply.revert(); // close on Escape
			});
		}, 600);

		return false;
	},

	send : function() {
		var post = {};

		$('#replysubmit .waiting').show();

		$('#replyrow input').each(function() {
			post[ $(this).attr('name') ] = $(this).val();
		});

		post.content = $('#replycontent').val();
		post.id = post.comment_post_ID;
		post.comments_listing = this.comments_listing;

		$.ajax({
			type : 'POST',
			url : wpListL10n.url,
			data : post,
			success : function(x) { commentReply.show(x); },
			error : function(r) { commentReply.error(r); }
		});

		return false;
	},

	show : function(xml) {
		var r, c, id, bg;

		if ( typeof(xml) == 'string' ) {
			this.error({'responseText': xml});
			return false;
		}

		r = wpAjax.parseAjaxResponse(xml);
		if ( r.errors ) {
			this.error({'responseText': wpAjax.broken});
			return false;
		}

		if ( 'edit-comment' == this.act )
			$(this.o).remove();

		r = r.responses[0];
		c = r.data;

		$(c).hide()
		$('#replyrow').after(c);

		this.o = id = '#comment-'+r.id;
		this.revert();
		this.addEvents($(id));
		bg = $(id).hasClass('unapproved') ? '#ffffe0' : '#fff';

		$(id)
			.animate( { 'backgroundColor':'#CCEEBB' }, 600 )
			.animate( { 'backgroundColor': bg }, 600 );

		$.fn.wpList.process($(id))
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
	$('span.delete a.delete').click(function(){return false;});

	if ( typeof QTags != 'undefined' )
		ed_reply = new QTags('ed_reply', 'replycontent', 'replycontainer', 'more');

	if ( typeof $.table_hotkeys != 'undefined' ) {
		make_hotkeys_redirect = function(which) {
			return function() {
				var first_last, l;

				first_last = 'next' == which? 'first' : 'last';
				l = $('.'+which+'.page-numbers');
				if (l.length)
					window.location = l[0].href.replace(/\&hotkeys_highlight_(first|last)=1/g, '')+'&hotkeys_highlight_'+first_last+'=1';
			}
		};

		edit_comment = function(event, current_row) {
			window.location = $('span.edit a', current_row).attr('href');
		};

		toggle_all = function() {
			toggleWithKeyboard = true;
			$('input:checkbox', '#cb').click().attr('checked', '');
			toggleWithKeyboard = false;
		};

		make_bulk = function(value) {
			return function() {
				var scope = $('select[name="action"]');
				$('option[value='+value+']', scope).attr('selected', 'selected');
				$('#comments-form').submit();
			}
		};

		$.table_hotkeys(
			$('table.widefat'),
			['a', 'u', 's', 'd', 'r', 'q', 'z', ['e', edit_comment], ['shift+x', toggle_all],
			['shift+a', make_bulk('approve')], ['shift+s', make_bulk('markspam')],
			['shift+d', make_bulk('delete')], ['shift+t', make_bulk('trash')],
			['shift+z', make_bulk('untrash')], ['shift+u', make_bulk('unapprove')]],
			{ highlight_first: adminCommentsL10n.hotkeys_highlight_first, highlight_last: adminCommentsL10n.hotkeys_highlight_last,
			prev_page_link_cb: make_hotkeys_redirect('prev'), next_page_link_cb: make_hotkeys_redirect('next') }
		);
	}
});

})(jQuery);
