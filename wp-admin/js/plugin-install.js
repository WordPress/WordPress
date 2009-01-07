/* Plugin Browser Thickbox related JS*/
jQuery(function($) {
	var thickDims = function() {
		var tbWindow = $('#TB_window');
		var width = $(window).width();
		var H = $(window).height();
		var W = ( 720 < width ) ? 720 : width;

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
			if ( ! ( $.browser.msie && $.browser.version.substr(0,1) < 7 ) )
				tbWindow.css({'top':'20px','margin-top':'0'});
		};

		return $('a.thickbox').each( function() {
			var href = $(this).attr('href');
			if ( ! href )
				return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
		});
	};

	thickDims().click( function() {
		$('#TB_title').css({'background-color':'#222','color':'#cfcfcf'});
		$('#TB_ajaxWindowTitle').html('<strong>' + plugininstallL10n.plugin_information + '</strong>&nbsp;' + $(this).attr('title') );
		return false;
	});
});

/* Plugin install related JS*/
jQuery(function($) {
	$('#install-plugins tbody.plugins tr').click( function() {
		$(this).find('.action-links a.onclick').click();
		return false;
	});

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
});