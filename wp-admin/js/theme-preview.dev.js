
var thickDims, tbWidth, tbHeight;
jQuery(document).ready(function($) {

	thickDims = function() {
		var tbWindow = $('#TB_window'), H = $(window).height(), W = $(window).width(), w, h;

		w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 90;
		h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 60;

		if ( tbWindow.size() ) {
			tbWindow.width(w).height(h);
			$('#TB_iframeContent').width(w).height(h - 27);
			tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
			if ( typeof document.body.style.maxWidth != 'undefined' )
				tbWindow.css({'top':'30px','margin-top':'0'});
		}
	};

	thickDims();
	$(window).resize( function() { thickDims() } );

	$('a.thickbox-preview').click( function() {
		tb_click.call(this);

		var alink = $(this).parents('.available-theme').find('.activatelink'), link = '', href = $(this).attr('href'), url, text;

		if ( tbWidth = href.match(/&tbWidth=[0-9]+/) )
			tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10);
		else
			tbWidth = $(window).width() - 90;

		if ( tbHeight = href.match(/&tbHeight=[0-9]+/) )
			tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
		else
			tbHeight = $(window).height() - 60;

		if ( alink.length ) {
			url = alink.attr('href') || '';
			text = alink.attr('title') || '';
			link = '&nbsp; <a href="' + url + '" target="_top" class="tb-theme-preview-link">' + text + '</a>';
		} else {
			text = $(this).attr('title') || '';
			link = '&nbsp; <span class="tb-theme-preview-link">' + text + '</span>';
		}

		$('#TB_title').css({'background-color':'#222','color':'#dfdfdf'});
		$('#TB_closeAjaxWindow').css({'float':'left'});
		$('#TB_ajaxWindowTitle').css({'float':'right'}).html(link);

		$('#TB_iframeContent').width('100%');
		thickDims();
		
		return false;
	} );

	// Theme details
	$('.theme-detail').click(function () {
		$(this).siblings('.themedetaildiv').toggle();
		return false;
	});

});

