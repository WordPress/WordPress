// send html to the post editor

var wpActiveEditor;

function send_to_editor(h) {
	var ed, mce = typeof(tinymce) != 'undefined', qt = typeof(QTags) != 'undefined';

	if ( !wpActiveEditor ) {
		if ( mce && tinymce.activeEditor ) {
			ed = tinymce.activeEditor;
			wpActiveEditor = ed.id;
		} else if ( !qt ) {
			return false;
		}
	} else if ( mce ) {
		if ( tinymce.activeEditor && (tinymce.activeEditor.id == 'mce_fullscreen' || tinymce.activeEditor.id == 'wp_mce_fullscreen') )
			ed = tinymce.activeEditor;
		else
			ed = tinymce.get(wpActiveEditor);
	}

	if ( ed && !ed.isHidden() ) {
		// restore caret position on IE
		if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
			ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

		if ( h.indexOf('[caption') === 0 ) {
			if ( ed.wpSetImgCaption )
				h = ed.wpSetImgCaption(h);
		} else if ( h.indexOf('[gallery') === 0 ) {
			if ( ed.plugins.wpgallery )
				h = ed.plugins.wpgallery._do_gallery(h);
		} else if ( h.indexOf('[embed') === 0 ) {
			if ( ed.plugins.wordpress )
				h = ed.plugins.wordpress._setEmbed(h);
		}

		ed.execCommand('mceInsertContent', false, h);
	} else if ( qt ) {
		QTags.insertContent(h);
	} else {
		document.getElementById(wpActiveEditor).value += h;
	}

	try{tb_remove();}catch(e){};
}

// thickbox settings
var tb_position;
(function($) {
	tb_position = function() {
		var tbWindow = $('#TB_window'), width = $(window).width(), H = $(window).height(), W = ( 720 < width ) ? 720 : width, adminbar_height = 0;

		if ( $('body.admin-bar').length )
			adminbar_height = 28;

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
			if ( typeof document.body.style.maxWidth != 'undefined' )
				tbWindow.css({'top': 20 + adminbar_height + 'px','margin-top':'0'});
		};

		return $('a.thickbox').each( function() {
			var href = $(this).attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ) );
		});
	};

	$(window).resize(function(){ tb_position(); });

	// store caret position in IE
	$(document).ready(function($){
		$('a.thickbox').click(function(){
			var ed;

			if ( typeof(tinymce) != 'undefined' && tinymce.isIE && ( ed = tinymce.get(wpActiveEditor) ) && !ed.isHidden() ) {
				ed.focus();
				ed.windowManager.insertimagebookmark = ed.selection.getBookmark();
			}
		});
	});

})(jQuery);

// WordPress, TinyMCE, and Media
// -----------------------------
(function($){
	// Stores the editors' `wp.media.controller.Workflow` instaces.
	var workflows = {};

	wp.mce.media = {
		insert: send_to_editor,

		add: function( id, options ) {
			var workflow = this.get( id );

			if ( workflow )
				return workflow;

			workflow = workflows[ id ] = wp.media( _.defaults( options || {}, {
				title:    wp.media.view.l10n.insertMedia,
				multiple: true
			} ) );

			workflow.on( 'update:insert', function( selection ) {
				this.insert( '\n' + selection.map( function( attachment ) {
					return wp.media.string.image( attachment );
				}).join('\n\n') + '\n' );
			}, this );

			workflow.on( 'update:gallery', function( selection ) {
				var view = wp.mce.view.get('gallery'),
					shortcode;

				if ( ! view )
					return;

				shortcode = view.gallery.shortcode( selection );
				this.insert( shortcode.string() );
			}, this );

			return workflow;
		},

		get: function( id ) {
			return workflows[ id ];
		},

		remove: function( id ) {
			delete workflows[ id ];
		},

		init: function() {
			$('.insert-media').on( 'click', function( event ) {
				var editor = $(this).data('editor'),
					workflow;

				event.preventDefault();

				if ( ! editor )
					return;

				workflow = wp.mce.media.get( editor );

				// If the workflow exists, just open it.
				if ( workflow ) {
					workflow.open();
					return;
				}

				// Initialize the editor's workflow if we haven't yet.
				wp.mce.media.add( editor );
			});
		}
	};

	$( wp.mce.media.init );
}(jQuery));