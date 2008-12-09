var theList; var theExtraList;
(function($) {

setCommentsList = function() {
	var dimAfter = function( r, settings ) {
		var c = $('#' + settings.element);

		if ( c.is('.unapproved') )
			c.find('div.comment_status').html('0')
		else
			c.find('div.comment_status').html('1')

		$('span.pending-count').each( function() {
			var a = $(this);
			var n = a.html().replace(/[ ,.]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			n = n + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 );
			if ( n < 0 ) { n = 0; }
			$('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			n = n.toString();
			if ( n.length > 3 )
				n = n.substr(0, n.length-3)+' '+n.substr(-3);
			a.html(n);
		});
	};

	var delAfter = function( r, settings ) {
		$('span.pending-count').each( function() {
			var a = $(this);
			var n = a.html().replace(/[ ,.]+/g, '');
			n = parseInt(n,10);
			if ( isNaN(n) ) return;
			if ( $('#' + settings.element).is('.unapproved') ) { // we deleted a formerly unapproved comment
				n = n - 1;
			} else if ( $(settings.target).parents( 'span.unapprove' ).size() ) { // we "deleted" an approved comment from the approved list by clicking "Unapprove"
				n = n + 1;
			}
			if ( n < 0 ) { n = 0; }
			$('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
			n = n.toString();
			if ( n.length > 3 )
				n = n.substr(0, n.length-3)+' '+n.substr(-3);
			a.html(n);
		});

		$('span.spam-count' ).each( function() {
			var a = $(this);
			var n = a.html().replace(/[ ,.]+/g, '');
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

		if ( theExtraList.size() == 0 || theExtraList.children().size() == 0 ) {
			return;
		}

		theList.get(0).wpList.add( theExtraList.children(':eq(0)').remove().clone() );
		$('#get-extra-comments').submit();
	};

	theExtraList = $('#the-extra-comment-list').wpList( { alt: '', delColor: 'none', addColor: 'none' } );
	theList = $('#the-comment-list').wpList( { alt: '', dimAfter: dimAfter, delAfter: delAfter, addColor: 'none' } );

};

$(document).ready(function(){
	setCommentsList();
});

commentReply = {

	init : function() {
		var row = $('#replyrow');

		$('a.cancel', row).click(function() { return commentReply.revert(); });
		$('a.save', row).click(function() { return commentReply.send(this); });

		// add events
		$('#the-comment-list .column-comment > p').dblclick(function(){
				commentReply.toggle($(this).parent());
			});

		$('#doaction, #doaction2, #post-query-submit').click(function(e){
			if ( $('#the-comment-list #replyrow').length > 0 )
				t.close();
		});

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
		var t = this;
		t.close();
		t.o = '#comment-'+id;

		$('#replyrow td').attr('colspan', $('.widefat thead th:visible').length);
		var editRow = $('#replyrow'), rowData = $('#inline-'+id);
		var act = t.act = (a == 'edit') ? 'edit-comment' : 'replyto-comment';

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

			var h = $(t.o).height();
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
			var rtop = $('#replyrow').offset().top;
			var rbottom = rtop + $('#replyrow').height();
			var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			var vp = document.documentElement.clientHeight || self.innerHeight || 0;
			var scrollBottom = scrollTop + vp;

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

		if ( typeof(xml) == 'string' ) {
			this.error({'responseText': xml});
			return false;
		}

		var r = wpAjax.parseAjaxResponse(xml);
		if ( r.errors ) {
			this.error({'responseText': wpAjax.broken});
			return false;
		}

		if ( 'edit-comment' == this.act )
			$(this.o).remove();

		r = r.responses[0];
		var c = r.data;

		$(c).hide()
		$('#replyrow').after(c);
		this.o = id = '#comment-'+r.id;
		$(id+' .hide-if-no-js').removeClass('hide-if-no-js');
		this.revert();
		this.addEvents($(id));
		var bg = $(id).hasClass('unapproved') ? '#ffffe0' : '#fff';

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
toggleWithKeyboard = false;
$(document).ready(function(){
	columns.init('edit-comments');
	commentReply.init();

	if ( typeof QTags != 'undefined' )
		ed_reply = new QTags('ed_reply', 'replycontent', 'replycontainer', 'more');

	if ( typeof $.table_hotkeys != 'undefined' ) {
		var make_hotkeys_redirect = function(which) {
			return function() {
				var first_last = 'next' == which? 'first' : 'last';
				var l=$('.'+which+'.page-numbers');
				if (l.length)
					window.location = l[0].href.replace(/\&hotkeys_highlight_(first|last)=1/g, '')+'&hotkeys_highlight_'+first_last+'=1';
			}
		};
		var edit_comment = function(event, current_row) {
			window.location = $('span.edit a', current_row).attr('href');
		};
		var toggle_all = function() {
			toggleWithKeyboard = true;
			var master_checkbox = $('form#comments-form .check-column :checkbox:first');
			master_checkbox.click().attr('checked', '');
			toggleWithKeyboard = false;
		}
		var make_bulk = function(value) {
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
