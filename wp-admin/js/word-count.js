// Word count
(function(JQ) {
	wpWordCount = {

		init : function() {
			var t = this, last = 0, co = JQ('#content');

			JQ('#wp-word-count').html( wordCountL10n.count.replace( /%d/, '<span id="word-count">0</span>' ) );
			t.block = 0;
			t.wc(co.val());
			co.keyup( function(e) {
				if ( e.keyCode == last ) return true;
				if ( 13 == e.keyCode || 8 == last || 46 == last ) t.wc(co.val());
				last = e.keyCode;
				return true;
			});
		},

		wc : function(tx) {
			var t = this, w = JQ('#word-count'), tc = 0;

			if ( t.block ) return;
			t.block = 1;

			setTimeout( function() {
				if ( tx ) {
					tx = tx.replace( /<.[^<>]*?>/g, ' ' ).replace( /&nbsp;/gi, ' ' );
					tx = tx.replace( /[0-9.(),;:!?%#$¿'"_+=\\/-]*/g, '' );
					tx.replace( /\S\s+/g, function(){tc++;} );
				}
				w.html(tc.toString());

				setTimeout( function() { t.block = 0; }, 2000 );
			}, 1 );
		}
	}
}(jQuery));

jQuery(document).ready( function(){ wpWordCount.init(); } );
