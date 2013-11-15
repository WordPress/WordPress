/* global ajaxurl, wpAjax */

var findPosts;
(function($){
	findPosts = {
		open : function(af_name, af_val) {
			var st = document.documentElement.scrollTop || $(document).scrollTop(),
				overlay = $( '.ui-find-overlay' );

			if ( overlay.length === 0 ) {
				$( 'body' ).append( '<div class="ui-find-overlay"></div>' );
				findPosts.overlay();
			}

			overlay.show();

			if ( af_name && af_val ) {
				$('#affected').attr('name', af_name).val(af_val);
			}
			$('#find-posts').show().draggable({
				handle: '#find-posts-head'
			}).css({'top':st + 50 + 'px','left':'50%','marginLeft':'-328px'});

			$('#find-posts-input').focus().keyup(function(e){
				if (e.which == 27) { findPosts.close(); } // close on Escape
			});

			// Pull some results up by default
			findPosts.send();

			return false;
		},

		close : function() {
			$('#find-posts-response').html('');
			$('#find-posts').draggable('destroy').hide();
			$( '.ui-find-overlay' ).hide();
		},

		overlay : function() {
			$( '.ui-find-overlay' ).css(
				{ 'z-index': '999', 'width': $( document ).width() + 'px', 'height': $( document ).height() + 'px' }
			).on('click', function () {
				findPosts.close();
			});
		},

		send : function() {
			var post = {
					ps: $('#find-posts-input').val(),
					action: 'find_posts',
					_ajax_nonce: $('#_ajax_nonce').val()
				},
				spinner = $( '.find-box-search .spinner' );

			spinner.show();

			$.ajax({
				type : 'POST',
				url : ajaxurl,
				data : post,
				success : function(x) { findPosts.show(x); spinner.hide(); },
				error : function(r) { findPosts.error(r); spinner.hide(); }
			});
		},

		show : function(x) {

			if ( typeof(x) == 'string' ) {
				this.error({'responseText': x});
				return;
			}

			var r = wpAjax.parseAjaxResponse(x);

			if ( r.errors ) {
				this.error({'responseText': wpAjax.broken});
			}
			r = r.responses[0];
			$('#find-posts-response').html(r.data);

			// Enable whole row to be clicked
			$( '.found-posts td' ).on( 'click', function () {
				$( this ).parent().find( '.found-radio input' ).prop( 'checked', true );
			});
		},

		error : function(r) {
			var er = r.statusText;

			if ( r.responseText ) {
				er = r.responseText.replace( /<.[^<>]*?>/g, '' );
			}
			if ( er ) {
				$('#find-posts-response').html(er);
			}
		}
	};

	$(document).ready(function() {
		$('#find-posts-submit').click(function(e) {
			if ( '' === $('#find-posts-response').html() )
				e.preventDefault();
		});
		$( '#find-posts .find-box-search :input' ).keypress( function( event ) {
			if ( 13 == event.which ) {
				findPosts.send();
				return false;
			}
		} );
		$( '#find-posts-search' ).click( findPosts.send );
		$( '#find-posts-close' ).click( findPosts.close );
		$('#doaction, #doaction2').click(function(e){
			$('select[name^="action"]').each(function(){
				if ( $(this).val() == 'attach' ) {
					e.preventDefault();
					findPosts.open();
				}
			});
		});
	});
	$(window).resize(function() {
		findPosts.overlay();
	});
})(jQuery);
