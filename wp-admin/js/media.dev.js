
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
			}).resizable({
				handles: 'all',
				minHeight: 150,
				minWidth: 280
			}).css({'top':st + 50 + 'px','left':'50%','marginLeft':'-200px'});

			$('.ui-resizable-handle').css({
				'backgroundColor': '#e5e5e5'
			});

			$('.ui-resizable-se').css({
				'border': '0 none',
				'width': '15px',
				'height': '16px',
				'background': 'transparent url(images/se.png) no-repeat scroll 0 0'
			});

			$('#find-posts-input').focus().keyup(function(e){
				if (e.which == 27) { findPosts.close(); } // close on Escape
			});

			return false;
		},

		close : function() {
			$('#find-posts-response').html('');
			$('#find-posts').draggable('destroy').resizable('destroy').hide();
		},

		send : function() {
			var post = {};

			post['ps'] = $('#find-posts-input').val();
			post['action'] = 'find_posts';
			post['_ajax_nonce'] = $('#_ajax_nonce').val();

			if ( $('#find-posts-pages:checked').val() ) {
				post['pages'] = 1;
			} else {
				post['posts'] = 1;
			}
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
