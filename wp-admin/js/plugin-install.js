/* Plugin Browser Thickbox related JS*/
var tb_position;
jQuery(document).ready(function($) {
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
			if ( ! href )
				return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ) );
		});
	};

	$(window).resize(function(){ tb_position(); });

	$('#dashboard_plugins, .plugins').on( 'click', 'a.thickbox', function() {
		tb_click.call(this);

		$('#TB_title').css({'background-color':'#222','color':'#cfcfcf'});
		$('#TB_ajaxWindowTitle').html('<strong>' + plugininstallL10n.plugin_information + '</strong>&nbsp;' + $(this).attr('title') );
		return false;
	});

	/* Plugin install related JS*/
	$('#plugin-information #sidemenu a').click( function() {
		var tab = $(this).attr('name');
		//Flip the tab
		$('#plugin-information-header a.current').removeClass('current');
		$(this).addClass('current');
		//Flip the content.
		$('#section-holder div.section').hide(); //Hide 'em all
		$('#section-' + tab).show();
		return false;
	});

	$('a.install-now').click( function() {
		return confirm( plugininstallL10n.ays );
	});
});
