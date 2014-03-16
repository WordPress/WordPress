/* global plugininstallL10n, tb_click, confirm */

/* Plugin Browser Thickbox related JS*/
var tb_position;
jQuery( document ).ready( function( $ ) {
	tb_position = function() {
		var tbWindow = $( '#TB_window' ),
			width = $( window ).width(),
			H = $( window ).height() - ( ( 850 < width ) ? 60 : 20 ),
			W = ( 850 < width ) ? 830 : width - 20;

		if ( tbWindow.size() ) {
			tbWindow.width( W ).height( H );
			$( '#TB_iframeContent' ).width( W ).height( H );
			tbWindow.css({
				'margin-left': '-' + parseInt( ( W / 2 ), 10 ) + 'px'
			});
			if ( typeof document.body.style.maxWidth !== 'undefined' ) {
				tbWindow.css({
					'top': ( ( 850 < width ) ? 30 : 10 ) + 'px',
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

	$('.plugins').on( 'click', 'a.thickbox', function() {
		tb_click.call(this);

		$('#TB_title').css({'background-color':'#222','color':'#cfcfcf'});
		$('#TB_ajaxWindowTitle').html('<strong>' + plugininstallL10n.plugin_information + '</strong>&nbsp;' + $(this).attr('title') );
		return false;
	});

	/* Plugin install related JS*/
	$( '#plugin-information-tabs a' ).click( function( event ) {
		var tab = $( this ).attr( 'name' );
		event.preventDefault();
		//Flip the tab
		$( '#plugin-information-tabs a.current' ).removeClass( 'current' );
		$( this ).addClass( 'current' );
		//Flip the content.
		$( '#section-holder div.section' ).hide(); //Hide 'em all
		$( '#section-' + tab ).show();
	});

	$( 'a.install-now' ).click( function() {
		return confirm( plugininstallL10n.ays );
	});
});
