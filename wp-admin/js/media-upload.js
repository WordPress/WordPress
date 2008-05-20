// send html to the post editor
function send_to_editor(h) {
	var win = window.dialogArguments || opener || parent || top;

	tinyMCE = win.tinyMCE;
	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.getInstanceById('content') ) && !ed.isHidden() ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, h);
	} else
		win.edInsertContent(win.edCanvas, h);
}

// thickbox settings
jQuery(function($) {
	tb_position = function() {
		var tbWindow = $('#TB_window');
		var width = $(window).width();
		var H = $(window).height();
		var W = ( 720 < width ) ? 720 : width;

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px','top':'20px','margin-top':'0'});
			$('#TB_title').css({'background-color':'#222','color':'#cfcfcf'});
			if ( $.browser.msie && $.browser.version.substr(0,1) < 7 ) 
				tbWindow.css({'margin-top':document.documentElement.scrollTop+'px'});
		};

		return $('a.thickbox').each( function() {
			var href = $(this).attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
		});
	};

	$(window).resize( function() { tb_position() } );
	$(document).ready( function() { tb_position() } );
});

