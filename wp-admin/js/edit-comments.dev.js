var theList, theExtraList, toggleWithKeyboard = false;
(function($) {

setCommentsList = function() {
	var totalInput, perPageInput, pageInput, lastConfidentTime = 0, dimAfter, delBefore, updateTotalCount, delAfter;

	totalInput = $('#comments-form .tablenav :input[name="_total"]');
	perPageInput = $('#comments-form .tablenav :input[name="_per_page"]');
	pageInput = $('#comments-form .tablenav :input[name="_page"]');

	dimAfter = function( r, settings ) {
		var c = $('#' + settings.element);

		if ( c.is('.unapproved') )
			c.find('div.comment_status').html('0')
		else
			c.find('div.comment_status').html('1')

		$('span.pending-count').each( function() {
			var a = $(this), n;
			n = a.html().replace(/[ ,.]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			n = n + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 );
			if ( n < 0 ) { n = 0; }
			a.parents('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			n = n.toString();
			if ( n.length > 3 )
				n = n.substr(0, n.length-3)+' '+n.substr(-3);
			a.html(n);
		});
	};

	// Send current total, page, per_page and url
	delBefore = function( settings ) {
		settings.data._total = totalInput.val();
		settings.data._per_page = perPageInput.val();
		settings.data._page = pageInput.val();
		settings.data._url = document.location.href;

		if ( 'undefined' != showNotice && settings.data.action && settings.data.action == 'delete-comment' && !settings.data.spam )
			return showNotice.warn() ? settings : false;

		return settings;
	};

	/* Updates the current total (as displayed visibly)
	*/
	updateTotalCount = function( total, time, setConfidentTime ) {
		if ( time < lastConfidentTime ) {
			return;
		}
		totalInput.val( total.toString() );
		if ( setConfidentTime ) {
			lastConfidentTime = time;
		}
		$('span.total-type-count').each( function() {
			var a = $(this), n;
			n = totalInput.val().toString();
			if ( n.length > 3 )
				n = n.substr(0, n.length-3)+' '+n.substr(-3);
			a.html(n);
		});

	};

	// In admin-ajax.php, we send back the unix time stamp instead of 1 on success
	delAfter = function( r, settings ) {
		$('span.pending-count').each( function() {
			var a = $(this), n;
			n = a.html().replace(/[ ,.]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			if ( $('#' + settings.element).is('.unapproved') ) { // we deleted a formerly unapproved comment
				n = n - 1;
			} else if ( $(settings.target).parents( 'span.unapprove' ).size() ) { // we "deleted" an approved comment from the approved list by clicking "Unapprove"
				n = n + 1;
			}
			if ( n < 0 ) { n = 0; }
			a.parents('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			n = n.toString();
			if ( n.length > 3 )
				n = n.substr(0, n.length-3)+' '+n.substr(-3);
			a.html(n);
		});

		$('span.spam-count').each( function() {
			var a = $(this), n;
			n = a.html().replace(/[ ,.]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			if ( $(settings.target).parents( 'span.spam' ).size() ) { // we marked a comment as spam
				n = n + 1;
			} else if ( $('#' + settings.element).is('.spam') ) { // we approved or deleted a comment marked as spam
				n = n - 1;
			}
			if ( n < 0 ) { n = 0; }
			n = n.toString();
			if ( n.length > 3 )
				n = n.substr(0, n.length-3)+' '+n.substr(-3);
			a.html(n);
		});


		// XML response
		if ( ( 'object' == typeof r ) && lastConfidentTime < settings.parsed.responses[0].supplemental.time ) {
			// Set the total to the known good value (even if this value is a little old, newer values should only be a few less, and so shouldn't mess up the page links)
			updateTotalCount( settings.parsed.responses[0].supplemental.total, settings.parsed.responses[0].supplemental.time, true );
			if ( $.trim( settings.parsed.responses[0].supplemental.pageLinks ) ) {
				$('.tablenav-pages').find( '.page-numbers' ).remove().end().append( $( settings.parsed.responses[0].supplemental.pageLinks ) );
			} else if ( 'undefined' != typeof settings.parsed.responses[0].supplemental.pageLinks ) {
				$('.tablenav-pages').find( '.page-numbers' ).remove();
			}
		} else {
			// Decrement the total
			var total = parseInt( totalInput.val(), 10 );
			if ( total-- < 0 )
				total = 0;
			updateTotalCount( total, r, false );
		}

		if ( theExtraList.size() == 0 || theExtraList.children().size() == 0 ) {
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
			$('#comments-form thead #cb input:checkbox').click().attr('checked', '');
			toggleWithKeyboard = false;
		}
		make_bulk = function(value) {
			return function(event, _) {
				$('option[value='+value+']').attr('selected', 'selected');
				$('form#comments-form')[0].submit();
			}
		};
		$.table_hotkeys($('table.widefat'),['a', 'u', 's', 'd', 'r', 'q', ['e', edit_comment],
				['shift+a', make_bulk('approve')], ['shift+s', make_bulk('markspam')],
				['shift+d', make_bulk('delete')], ['shift+x', toggle_all],
				['shift+u', make_bulk('unapprove')]],
				{highlight_first: adminCommentsL10n.hotkeys_highlight_first, highlight_last: adminCommentsL10n.hotkeys_highlight_last,
				prev_page_link_cb: make_hotkeys_redirect('prev'), next_page_link_cb: make_hotkeys_redirect('next')}
		);
	}
});

})(jQuery);
