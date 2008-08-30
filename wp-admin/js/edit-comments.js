var theList; var theExtraList;
(function($) {

setCommentsList = function() {
	var dimAfter = function( r, settings ) {
		$('li span.comment-count').each( function() {
			var a = $(this);
			var n = parseInt(a.html(),10);
			n = n + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 );
			if ( n < 0 ) { n = 0; }
			a.html( n.toString() );
			$('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
		});
		$('.post-com-count span.comment-count').each( function() {
			var a = $(this);
			var n = parseInt(a.html(),10);
			var t = parseInt(a.parent().attr('title'), 10);
			if ( $('#' + settings.element).is('.unapproved') ) { // we unapproved a formerly approved comment
				n = n - 1;
				t = t + 1;
			} else { // we approved a formerly unapproved comment
				n = n + 1;
				t = t - 1;
			}
			if ( n < 0 ) { n = 0; }
			if ( t < 0 ) { t = 0; }
			if ( t >= 0 ) { a.parent().attr('title', adminCommentsL10n.pending.replace( /%i%/, t.toString() ) ); }
			if ( 0 === t ) { a.parents('strong:first').replaceWith( a.parents('strong:first').html() ); }
			a.html( n.toString() );
		});
	};
	
	var delAfter = function( r, settings ) {
		$('li span.comment-count').each( function() {
			var a = $(this);
			var n = parseInt(a.html(),10);
			if ( $('#' + settings.element).is('.unapproved') ) { // we deleted a formerly unapproved comment
				n = n - 1;
			} else if ( $(settings.target).parents( 'span.unapprove' ).size() ) { // we "deleted" an approved comment from the approved list by clicking "Unapprove"
				n = n + 1;
			}
			if ( n < 0 ) { n = 0; }
			a.html( n.toString() );
			$('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
		});
		$('.post-com-count span.comment-count').each( function() {
			var a = $(this);
			if ( $('#' + settings.element).is('.unapproved') ) { // we deleted a formerly unapproved comment
				var t = parseInt(a.parent().attr('title'), 10);
				if ( t < 1 ) { return; }
				t = t - 1;
				a.parent().attr('title', adminCommentsL10n.pending.replace( /%i%/, t.toString() ) );
				if ( 0 === t ) { a.parents('strong:first').replaceWith( a.parents('strong:first').html() ); }
				return;
			}
			var n = parseInt(a.html(),10) - 1;
			a.html( n.toString() );
		});
		$('li span.spam-comment-count' ).each( function() {
			var a = $(this);
			var n = parseInt(a.html(),10);
			if ( $(settings.target).parents( 'span.spam' ).size() ) { // we marked a comment as spam
				n = n + 1;
			} else if ( $('#' + settings.element).is('.spam') ) { // we approved or deleted a comment marked as spam
				n = n - 1;
			}
			if ( n < 0 ) { n = 0; }
			a.html( n.toString() );
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

})(jQuery);

(function($){

commentReply = {

	open : function(c, p) {
		var d = $('#comment-'+c).offset(), H = $('#replydiv').height(), top = 200, left = 100, h = 120;

		if ( d && H ) {
			top = (d.top - H) < 10 ? 10 : d.top - H - 5;
			left = d.left;
		}

		$('#replydiv #comment_post_ID').val(p);
		$('#replydiv #comment_ID').val(c);

		$('#replydiv').draggable({
			handle : '#replyhandle',
			containment : '#wpwrap'
		}).resizable({
			handles : 'se',
			minHeight : 200,
			minWidth : 400,
			containment : '#wpwrap',
			resize : function(e,o) {
				h = o.size.height - 80 - $('#ed_reply_qtags').height();
				$('#replycontainer').height(h);
			},
			stop : function(e,o) {
				if ( $.browser.msie )
					$('#replycontent').height(h);
			}
		});

		$('.ui-resizable-se').css({
			border: '0 none',
			width: '11px',
			height: '12px',
			background: 'transparent url(images/se.png) no-repeat scroll 0 0'
		});

		$('#replydiv').css({
			'position' : 'absolute',
			'top' : top,
			'left' : left
		}).show();

		$('#replycontent').focus().keyup(function(e){
			if (e.which == 27) commentReply.close(); // close on Escape
		});
		
		// emulate the Safari/Opera scrollIntoView
		var to = $('#replydiv').offset();
		var scr = document.documentElement.scrollTop ? document.documentElement.scrollTop : 0;

		if ( scr - 20 > to.top )
			window.scroll(0, to.top - 100);
	},

	close : function() {
		$('#replycontent').val('');
		$('#replyerror').hide();

		$('#replydiv').draggable('destroy').resizable('destroy').css('position','relative');
		$('#replydiv').hide();
		return false;
	},

	send : function() {
		var post = {};

		$('#replyform input').each(function() {
			post[ $(this).attr('name') ] = $(this).val();
		});

		post.comment = $('#replycontent').val();
		post.id = post.comment_post_ID;

		$.ajax({
			type : 'POST',
			url : wpListL10n.url,
			data : post,
			success : function(x) { commentReply.show(x); },
			error : function(r) { commentReply.error(r); }
		});
	},

	show : function(xml) {

		if ( typeof(xml) == 'string' ) {
			this.error({'responseText': xml});
			return;
		}

		var r = wpAjax.parseAjaxResponse(xml);
		if ( r.errors )
			this.error({'responseText': wpAjax.broken});

		r = r.responses[0];
		this.close();
//		var scr1 = $('#the-comment-list').offset(), scr2 = $('#the-comment-list').height();

		if ( r.position == -1 ) {
//			window.scroll(0, scr1.top - 100); // Scroll to the new comment? Seems annoing..
			$('#the-comment-list').prepend(r.data);
		} else {
//			window.scroll(0, scr1.top + scr2 + 200);
			$('#the-comment-list').append(r.data);
		}

		$('#comment-'+r.id)
			.animate( { backgroundColor:"#CFEBF7" }, 600 )
			.animate( { backgroundColor:"transparent" }, 600 );
		
		setCommentsList();
	},

	error : function(r) {
		var er = r.statusText;

		if ( r.responseText )
			er = r.responseText.replace( /<.[^<>]*?>/g, '' );

		if ( er ) {
			var o = $('#replydiv').offset();
			$('#replydiv').hide();

			$('#replyerror').css({
				'top' : o.top + 60 + 'px',
				'left' : o.left + 'px'
			}).show().draggable();

			$('#replyerrtext').html(er)
			$('#close-button').css('outline','none').focus().keyup(function(e) {
				if (e.which == 27) commentReply.close(); // close on Escape
			});
		}
	},
	
	back : function() {
		if ( $('#replydiv').is(':hidden') && $('#replyerror').is(':visible') ) {
			$('#replyerror').hide();
			$('#replydiv').show();
		}
	}
};

$(document).ready(function(){
	if ( typeof QTags != 'undefined' )
		ed_reply = new QTags('ed_reply', 'replycontent', 'replycontainer', 'more');
	if ( typeof $.table_hotkeys != 'undefined' )
		$.table_hotkeys($('table.widefat'), ['a', 'u', 's', 'd', 'r']);
});

})(jQuery);
