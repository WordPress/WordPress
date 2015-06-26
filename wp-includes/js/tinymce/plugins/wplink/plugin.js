/* global tinymce */
tinymce.PluginManager.add( 'wplink', function( editor ) {
	var toolbar;

	editor.addCommand( 'WP_Link', function() {
		window.wpLink && window.wpLink.open( editor.id );
	});

	// WP default shortcut
	editor.addShortcut( 'Alt+Shift+A', '', 'WP_Link' );
	// The "de-facto standard" shortcut, see #27305
	editor.addShortcut( 'Meta+K', '', 'WP_Link' );

	editor.addButton( 'link', {
		icon: 'link',
		tooltip: 'Insert/edit link',
		cmd: 'WP_Link',
		stateSelector: 'a[href]'
	});

	editor.addButton( 'unlink', {
		icon: 'unlink',
		tooltip: 'Remove link',
		cmd: 'unlink'
	});

	editor.addMenuItem( 'link', {
		icon: 'link',
		text: 'Insert/edit link',
		cmd: 'WP_Link',
		stateSelector: 'a[href]',
		context: 'insert',
		prependToContext: true
	});

	editor.on( 'pastepreprocess', function( event ) {
		var pastedStr = event.content,
			regExp = /^(?:https?:)?\/\/\S+$/i;

		if ( ! editor.selection.isCollapsed() && ! regExp.test( editor.selection.getContent() ) ) {
			pastedStr = pastedStr.replace( /<[^>]+>/g, '' );
			pastedStr = tinymce.trim( pastedStr );

			if ( regExp.test( pastedStr ) ) {
				editor.execCommand( 'mceInsertLink', false, {
					href: editor.dom.decode( pastedStr )
				} );

				event.preventDefault();
			}
		}
	} );

	tinymce.ui.WPLinkPreview = tinymce.ui.Control.extend( {
		url: '#',
		renderHtml: function() {
			return (
				'<div id="' + this._id + '" class="wp-link-preview">' +
					'<a href="' + this.url + '" target="_blank" tabindex="-1">' + this.url + '</a>' +
				'</div>'
			);
		},
		setURL: function( url ) {
			var index, lastIndex;

			if ( this.url !== url ) {
				this.url = url;

				url = window.decodeURIComponent( url );

				url = url.replace( /^(?:https?:)?\/\/(?:www\.)?/, '' );

				if ( ( index = url.indexOf( '?' ) ) !== -1 ) {
					url = url.slice( 0, index );
				}

				if ( ( index = url.indexOf( '#' ) ) !== -1 ) {
					url = url.slice( 0, index );
				}

				url = url.replace( /(?:index)?\.html$/, '' );

				if ( url.charAt( url.length - 1 ) === '/' ) {
					url = url.slice( 0, -1 );
				}

				// If the URL is longer that 40 chars, concatenate the beginning (after the domain) and ending with ...
				if ( url.length > 40 && ( index = url.indexOf( '/' ) ) !== -1 && ( lastIndex = url.lastIndexOf( '/' ) ) !== -1 && lastIndex !== index ) {
					// If the beginning + ending are shorter that 40 chars, show more of the ending
					if ( index + url.length - lastIndex < 40 ) {
						lastIndex =  -( 40 - ( index + 1 ) );
					}

					url = url.slice( 0, index + 1 ) + '\u2026' + url.slice( lastIndex );
				}

				tinymce.$( this.getEl().firstChild ).attr( 'href', this.url ).text( url );
			}
		},
		postRender: function() {
			var self = this;

			editor.on( 'wptoolbar', function( event ) {
				var anchor = editor.dom.getParent( event.element, 'a' ),
					$ = editor.$,
					href;

				if ( anchor && ! $( anchor ).find( 'img' ).length &&
					( href = $( anchor ).attr( 'href' ) ) ) {

					self.setURL( href );
					event.element = anchor;
					event.toolbar = toolbar;
				}
			} );
		}
	} );

	editor.addButton( 'wp_link_edit', {
		tooltip: 'Edit ', // trailing space is needed, used for context
		icon: 'dashicon dashicons-edit',
		cmd: 'WP_Link'
	} );

	editor.addButton( 'wp_link_remove', {
		tooltip: 'Remove',
		icon: 'dashicon dashicons-no',
		cmd: 'unlink'
	} );

	editor.on( 'preinit', function() {
		toolbar = editor.wp._createToolbar( [
			'WPLinkPreview',
			'wp_link_edit',
			'wp_link_remove'
		], true );
	} );
});
