(function($) {
	wpWordCount = {

		settings : {
			strip : /<[a-zA-Z\/][^<>]*>/g, // strip HTML tags
			clean : /[0-9.(),;:!?%#$¿'"_+=\\/-]+/g, // regexp to remove punctuation, etc.
			count : /\S\s+/g // counting regexp
		},

		settingsEastAsia : {
			count : /[\u3100-\u312F\u31A0-\u31BF\u4E00-\u9FCF\u3400-\u4DBF\uF900-\uFAFF\u2F00-\u2FDF\u2E80-\u2EFF\u31C0-\u31EF\u2FF0-\u2FFF\u1100-\u11FF\uA960-\uA97F\uD780-\uD7FF\u3130-\u318F\uFFA0-\uFFDC\uAC00-\uD7AF\u3040-\u309F\u30A0-\u30FF\u31F0-\u31FF\uFF65-\uFF9F\u3190-\u319F\uA4D0-\uA4FF\uA000-\uA48F\uA490-\uA4CF]/g
		},

		block : 0,

		wc : function(tx) {
			var t = this, w = $('.word-count'), tc = 0;

			if ( t.block )
				return;

			t.block = 1;

			setTimeout( function() {
				if ( tx ) {
					// remove generally useless stuff first
					tx = tx.replace( t.settings.strip, ' ' ).replace( /&nbsp;|&#160;/gi, ' ' );

					// count east asia chars
					tx = tx.replace( t.settingsEastAsia.count, function(){ tc++; return ''; } );

					// count remaining western characters
					tx = tx.replace( t.settings.clean, '' );
					tx.replace( t.settings.count, function(){ tc++; return ''; } );
				}
				w.html(tc.toString());

				setTimeout( function() { t.block = 0; }, 2000 );
			}, 1 );
		}
	}

	$(document).bind( 'wpcountwords', function(e, txt) {
		wpWordCount.wc(txt);
	});
}(jQuery));
