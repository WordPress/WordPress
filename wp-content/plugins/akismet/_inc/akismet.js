jQuery( function ( $ ) {
	$( 'a.activate-option' ).click( function(){
		var link = $( this );
		if ( link.hasClass( 'clicked' ) ) {
			link.removeClass( 'clicked' );
		}
		else {
			link.addClass( 'clicked' );
		}
		$( '.toggle-have-key' ).slideToggle( 'slow', function() {});
		return false;
	});
	$('.akismet-status').each(function () {
		var thisId = $(this).attr('commentid');
		$(this).prependTo('#comment-' + thisId + ' .column-comment');
	});
	$('.akismet-user-comment-count').each(function () {
		var thisId = $(this).attr('commentid');
		$(this).insertAfter('#comment-' + thisId + ' .author strong:first').show();
	});
	$('#the-comment-list').find('tr.comment, tr[id ^= "comment-"]').find('.column-author a[title ^= "http://"]').each(function () {
		var thisTitle = $(this).attr('title');
			thisCommentId = $(this).parents('tr:first').attr('id').split("-");

		$(this).attr("id", "author_comment_url_"+ thisCommentId[1]);

		if (thisTitle) {
			$(this).after(
				$( '<a href="#" class="remove_url">x</a>' )
					.attr( 'commentid', thisCommentId[1] )
					.attr( 'title', WPAkismet.strings['Remove this URL'] )
			);
		}
	});
	$('.remove_url').live('click', function () {
		var thisId = $(this).attr('commentid');
		var data = {
			action: 'comment_author_deurl',
			_wpnonce: WPAkismet.comment_author_url_nonce,
			id: thisId
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				// Removes "x" link
				$("a[commentid='"+ thisId +"']").hide();
				// Show temp status
				$("#author_comment_url_"+ thisId).html( $( '<span/>' ).text( WPAkismet.strings['Removing...'] ) );
			},
			success: function (response) {
				if (response) {
					// Show status/undo link
					$("#author_comment_url_"+ thisId)
						.attr('cid', thisId)
						.addClass('akismet_undo_link_removal')
						.html(
							$( '<span/>' ).text( WPAkismet.strings['URL removed'] )
						)
						.append( ' ' )
						.append(
							$( '<span/>' )
								.text( WPAkismet.strings['(undo)'] )
								.addClass( 'akismet-span-link' )
						);
				}
			}
		});

		return false;
	});
	$('.akismet_undo_link_removal').live('click', function () {
		var thisId = $(this).attr('cid');
		var thisUrl = $(this).attr('href').replace("http://www.", "").replace("http://", "");
		var data = {
			action: 'comment_author_reurl',
			_wpnonce: WPAkismet.comment_author_url_nonce,
			id: thisId,
			url: thisUrl
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				// Show temp status
				$("#author_comment_url_"+ thisId).html( $( '<span/>' ).text( WPAkismet.strings['Re-adding...'] ) );
			},
			success: function (response) {
				if (response) {
					// Add "x" link
					$("a[commentid='"+ thisId +"']").show();
					// Show link
					$("#author_comment_url_"+ thisId).removeClass('akismet_undo_link_removal').html(thisUrl);
				}
			}
		});

		return false;
	});
	$('a[id^="author_comment_url"], tr.pingback td.column-author a:first-of-type').mouseover(function () {
		var wpcomProtocol = ( 'https:' === location.protocol ) ? 'https://' : 'http://';
		// Need to determine size of author column
		var thisParentWidth = $(this).parent().width();
		// It changes based on if there is a gravatar present
		thisParentWidth = ($(this).parent().find('.grav-hijack').length) ? thisParentWidth - 42 + 'px' : thisParentWidth + 'px';
		if ($(this).find('.mShot').length == 0 && !$(this).hasClass('akismet_undo_link_removal')) {
			var self = $( this );
			$('.widefat td').css('overflow', 'visible');
			$(this).css('position', 'relative');
			var thisHref = $.URLEncode( $(this).attr('href') );
			$(this).append('<div class="mShot mshot-container" style="left: '+thisParentWidth+'"><div class="mshot-arrow"></div><img src="//s0.wordpress.com/mshots/v1/'+thisHref+'?w=450" width="450" class="mshot-image" style="margin: 0;" /></div>');
			setTimeout(function () {
				self.find( '.mshot-image' ).attr('src', '//s0.wordpress.com/mshots/v1/'+thisHref+'?w=450&r=2');
			}, 6000);
			setTimeout(function () {
				self.find( '.mshot-image' ).attr('src', '//s0.wordpress.com/mshots/v1/'+thisHref+'?w=450&r=3');
			}, 12000);
		} else {
			$(this).find('.mShot').css('left', thisParentWidth).show();
		}
	}).mouseout(function () {
		$(this).find('.mShot').hide();
	});
	$('.checkforspam:not(.button-disabled)').click( function(e) {
		$('.checkforspam:not(.button-disabled)').addClass('button-disabled');
		$('.checkforspam-spinner').addClass( 'spinner' );
		akismet_check_for_spam(0, 100);
		e.preventDefault();
	});

	function akismet_check_for_spam(offset, limit) {
		$.post(
			ajaxurl,
			{
				'action': 'akismet_recheck_queue',
				'offset': offset,
				'limit': limit
			},
			function(result) {
				if (result.processed < limit) {
					window.location.reload();
				}
				else {
					akismet_check_for_spam(offset + limit, limit);
				}
			}
		);
	}
});
// URL encode plugin
jQuery.extend({URLEncode:function(c){var o='';var x=0;c=c.toString();var r=/(^[a-zA-Z0-9_.]*)/;
  while(x<c.length){var m=r.exec(c.substr(x));
    if(m!=null && m.length>1 && m[1]!=''){o+=m[1];x+=m[1].length;
    }else{if(c[x]==' ')o+='+';else{var d=c.charCodeAt(x);var h=d.toString(16);
    o+='%'+(h.length<2?'0':'')+h.toUpperCase();}x++;}}return o;}
});
