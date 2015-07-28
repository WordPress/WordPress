/* global plugininstallL10n, tb_click */

/* Plugin Browser Thickbox related JS*/
var tb_position;
jQuery( document ).ready( function( $ ) {
	tb_position = function() {
		var tbWindow = $( '#TB_window' ),
			width = $( window ).width(),
			H = $( window ).height() - ( ( 792 < width ) ? 60 : 20 ),
			W = ( 792 < width ) ? 772 : width - 20;

		if ( tbWindow.size() ) {
			tbWindow.width( W ).height( H );
			$( '#TB_iframeContent' ).width( W ).height( H );
			tbWindow.css({
				'margin-left': '-' + parseInt( ( W / 2 ), 10 ) + 'px'
			});
			if ( typeof document.body.style.maxWidth !== 'undefined' ) {
				tbWindow.css({
					'top': '30px',
					'margin-top': '0'
				});
			}
		}

		return $( 'a.thickbox' ).each( function() {
			var href = $( this ).attr( 'href' );
			if ( ! href ) {
				return;
			}
			href = href.replace( /&width=[0-9]+/g, '' );
			href = href.replace( /&height=[0-9]+/g, '' );
			$(this).attr( 'href', href + '&width=' + W + '&height=' + ( H ) );
		});
	};

	$( window ).resize( function() {
		tb_position();
	});

	$( '.plugin-card, .plugins .plugin-version-author-uri' ).on( 'click', 'a.thickbox', function() {
		tb_click.call(this);

		$('#TB_title').css({'background-color':'#23282d','color':'#cfcfcf'});
		$('#TB_ajaxWindowTitle').html( '<strong>' + plugininstallL10n.plugin_information + '</strong>&nbsp;' + $(this).data( 'title' ) );
		$('#TB_iframeContent').attr( 'title', plugininstallL10n.plugin_information + ' ' + $(this).data( 'title' ) );
		$('#TB_closeWindowButton').focus();

		return false;
	});

	/* Plugin install related JS */
	$( '#plugin-information-tabs a' ).click( function( event ) {
		var tab = $( this ).attr( 'name' );
		event.preventDefault();

		// Flip the tab
		$( '#plugin-information-tabs a.current' ).removeClass( 'current' );
		$( this ).addClass( 'current' );

		// Only show the fyi box in the description section, on smaller screen, where it's otherwise always displayed at the top.
		if ( 'description' !== tab && $( window ).width() < 772 ) {
			$( '#plugin-information-content' ).find( '.fyi' ).hide();
		} else {
			$( '#plugin-information-content' ).find( '.fyi' ).show();
		}

		// Flip the content.
		$( '#section-holder div.section' ).hide(); // Hide 'em all.
		$( '#section-' + tab ).show();
	});
});
