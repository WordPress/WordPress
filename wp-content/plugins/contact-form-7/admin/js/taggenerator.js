(function($) {

	$.fn.tagGenerator = function(title, options) {
		var menu = $('<div class="tag-generator"></div>');

		var selector = $('<span>' + title + '</span>');

		selector.css({
			border: '1px solid #ddd',
			padding: '2px 4px',
			background: '#fff url(' + options.fadebuttImageUrl + ') repeat-x 0 0',
			'-moz-border-radius': '3px',
			'-khtml-border-radius': '3px',
			'-webkit-border-radius': '3px',
			'border-radius': '3px'
		});

		selector.mouseover(function() {
			$(this).css({ 'border-color': '#bbb' });
		});
		selector.mouseout(function() {
			$(this).css({ 'border-color': '#ddd' });
		});
		selector.mousedown(function() {
			$(this).css({ background: '#ddd' });
		});
		selector.mouseup(function() {
			$(this).css({
				background: '#fff url(' + options.fadebuttImageUrl + ') repeat-x 0 0'
			});
		});
		selector.click(function() {
			dropdown.slideDown('fast');
			return false;
		});
		$('body').click(function() {
			dropdown.hide();
		});

		if (options.dropdownIconUrl) {
			var dropdown_icon = $('<img src="' + options.dropdownIconUrl + '" />');
			dropdown_icon.css({ 'vertical-align': 'bottom' });
			selector.append(dropdown_icon);
		}

		menu.append(selector);

		var pane = $('<div class="tg-pane"></div>');
		pane.hide();
		menu.append(pane);

		var dropdown = $('<div class="tg-dropdown"></div>');
		dropdown.hide();
		menu.append(dropdown);

		$.each($.tgPanes, function(i, n) {
			var submenu = $('<div>' + $.tgPanes[i].title + '</div>');
			submenu.css({
				margin: 0,
				padding: '0 4px',
				'line-height': '180%',
				background: '#fff'
			});
			submenu.mouseover(function() {
				$(this).css({ background: '#d4f2f2' });
			});
			submenu.mouseout(function() {
				$(this).css({ background: '#fff' });
			});
			submenu.click(function() {
				dropdown.hide();
				pane.hide();
				pane.empty();
				$.tgPane(pane, i);
				pane.slideDown('fast');
				return false;
			});
			dropdown.append(submenu);
		});

		this.append(menu);
	};

	$.tgPane = function(pane, tagType) {
		var closeButtonDiv = $('<div></div>');
		closeButtonDiv.css({ float: 'right' });

		var closeButton = $('<span class="tg-closebutton">&#215;</span>');
		closeButton.click(function() {
			pane.slideUp('fast').empty();
		});
		closeButtonDiv.append(closeButton);

		pane.append(closeButtonDiv);

		var paneTitle = $('<div class="tg-panetitle">' + $.tgPanes[tagType].title + '</div>');
		pane.append(paneTitle);

		pane.append($('#' + $.tgPanes[tagType].content).clone().contents());

		pane.find(':checkbox.exclusive').change(function() {
			if ($(this).is(':checked'))
				$(this).siblings(':checkbox.exclusive').removeAttr('checked');
		});

		if ($.isFunction($.tgPanes[tagType].change))
			$.tgPanes[tagType].change(pane, tagType);
		else
			$.tgCreateTag(pane, tagType);

		pane.find(':input').change(function() {
			if ($.isFunction($.tgPanes[tagType].change))
				$.tgPanes[tagType].change(pane, tagType);
			else
				$.tgCreateTag(pane, tagType);
		});
	}

	$.tgCreateTag = function(pane, tagType) {
		pane.find('input[name="name"]').each(function(i) {
			var val = $(this).val();
			val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
			if ('' == val) {
				var rand = Math.floor(Math.random() * 1000);
				val = tagType + '-' + rand;
			}
			$(this).val(val);
		});

		pane.find(':input.numeric').each(function(i) {
			var val = $(this).val();
			val = val.replace(/[^0-9.-]/g, '');
			$(this).val(val);
		});

		pane.find(':input.idvalue').each(function(i) {
			var val = $(this).val();
			val = val.replace(/[^-0-9a-zA-Z_]/g, '');
			$(this).val(val);
		});

		pane.find(':input.classvalue').each(function(i) {
			var val = $(this).val();
			val = $.map(val.split(' '), function(n) {
				return n.replace(/[^-0-9a-zA-Z_]/g, '');
			}).join(' ');
			val = $.trim(val.replace(/\s+/g, ' '));
			$(this).val(val);
		});

		pane.find(':input.color').each(function(i) {
			var val = $(this).val();
			val = val.replace(/[^0-9a-fA-F]/g, '');
			$(this).val(val);
		});

		pane.find(':input.filesize').each(function(i) {
			var val = $(this).val();
			val = val.replace(/[^0-9kKmMbB]/g, '');
			$(this).val(val);
		});

		pane.find(':input.filetype').each(function(i) {
			var val = $(this).val();
			val = val.replace(/[^0-9a-zA-Z.,|\s]/g, '');
			$(this).val(val);
		});

		pane.find(':input.date').each(function(i) {
			var val = $(this).val();
			if (! val.match(/^\d{4}-\d{2}-\d{2}$/)) // 'yyyy-mm-dd' ISO 8601 format
				$(this).val('');
		});

		pane.find(':input[name="values"]').each(function(i) {
			var val = $(this).val();
			val = $.trim(val);
			$(this).val(val);
		});

		pane.find('input.tag').each(function(i) {
			var type = $(this).attr('name');

			var scope = pane.find('.scope.' + type);
			if (! scope.length)
				scope = pane;

			if (pane.find(':input[name="required"]').is(':checked'))
				type += '*';

			var name = pane.find(':input[name="name"]').val();

			var options = [];

			var size = scope.find(':input[name="size"]').val() || '';
			var maxlength = scope.find(':input[name="maxlength"]').val() || '';
			var cols = scope.find(':input[name="cols"]').val() || '';
			var rows = scope.find(':input[name="rows"]').val() || '';

			if ((cols || rows) && maxlength)
				options.push(cols + 'x' + rows + '/' + maxlength);
			else if (cols || rows)
				options.push(cols + 'x' + rows);
			else if (size || maxlength)
				options.push(size + '/' + maxlength);

			scope.find('input.option').not(':checkbox,:radio').each(function(i) {
				var excluded = ['size', 'maxlength', 'cols', 'rows'];

				if (-1 < $.inArray($(this).attr('name'), excluded))
					return;

				var val = $(this).val();

				if (! val)
					return;

				if ($(this).hasClass('filetype'))
					val = val.split(/[,|\s]+/).join('|');

				if ($(this).hasClass('color'))
					val = '#' + val;

				if ('class' == $(this).attr('name')) {
					$.each(val.split(' '), function(i, n) { options.push('class:' + n) });
				} else {
					options.push($(this).attr('name') + ':' + val);
				}
			});

			scope.find('input:checkbox.option').each(function(i) {
				if ($(this).is(':checked'))
					options.push($(this).attr('name'));
			});

			options = (options.length > 0) ? ' ' + options.join(' ') : '';

			var value = '';

			if (scope.find(':input[name="values"]').val()) {
				$.each(scope.find(':input[name="values"]').val().split("\n"), function(i, n) {
					value += ' "' + n.replace(/["]/g, '&quot;') + '"';
				});
			}

			if ($.tgPanes[tagType].nameless)
				var tag = '[' + type + options + value + ']';
			else
				var tag = name ? '[' + type + ' ' + name + options + value + ']' : '';

			$(this).val(tag);
		});

		pane.find('input.mail-tag').each(function(i) {
			var name = pane.find(':input[name="name"]').val();

			var tag = name ? '[' + name + ']' : '';

			$(this).val(tag);
		});

	}

	$.tgPanes = {};

})(jQuery);