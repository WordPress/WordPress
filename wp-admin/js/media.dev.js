
var findPosts;
(function($){
	findPosts = {
		open : function(af_name, af_val) {
			var st = document.documentElement.scrollTop || $(document).scrollTop();

			if ( af_name && af_val ) {
				$('#affected').attr('name', af_name).val(af_val);
			}
			$('#find-posts').show().draggable({
				handle: '#find-posts-head'
			}).css({'top':st + 50 + 'px','left':'50%','marginLeft':'-250px'});

			$('#find-posts-input').focus().keyup(function(e){
				if (e.which == 27) { findPosts.close(); } // close on Escape
			});

			return false;
		},

		close : function() {
			$('#find-posts-response').html('');
			$('#find-posts').draggable('destroy').hide();
		},

		send : function() {
			var post = {
				ps: $('#find-posts-input').val(),
				action: 'find_posts',
				_ajax_nonce: $('#_ajax_nonce').val()
			};

			var selectedItem;
			$("input[@name='itemSelect[]']:checked").each(function() { selectedItem = $(this).val() });
			post['post_type'] = selectedItem;

			$.ajax({
				type : 'POST',
				url : ajaxurl,
				data : post,
				success : function(x) { findPosts.show(x); },
				error : function(r) { findPosts.error(r); }
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
			if ( '' == $('#find-posts-response').html() )
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
})(jQuery);
