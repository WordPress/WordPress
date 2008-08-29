(function($){
	$.table_hotkeys = function(table, keys, opts) {
		opts = $.extend($.table_hotkeys.defaults, opts);
		var selected_class = opts.class_prefix + opts.selected_suffix;
		var destructive_class = opts.class_prefix + opts.destructive_suffix;	
		var set_current_row = function (tr) {
			if ($.table_hotkeys.current_row) $.table_hotkeys.current_row.removeClass(selected_class);
			tr.addClass(selected_class);
			tr[0].scrollIntoView(false);
			$.table_hotkeys.current_row = tr;
		};
		var next_row = function() {
			var next = get_adjacent_row('next');
			if (!next) return false;
			set_current_row($(next));
			return true;
		};
		var prev_row = function() {
			var prev = get_adjacent_row('prev');
			if (!prev) return false;
			set_current_row($(prev));
			return true;
		};
		var check = function() {
			$(opts.checkbox_expr, $.table_hotkeys.current_row).each(function() {
				this.checked = !this.checked;
			});
		};
		var get_adjacent_row = function(which) {
			if (!$.table_hotkeys.current_row) {
				var start_row_dom = $(opts.cycle_expr, table)[opts.start_row_index];
				$.table_hotkeys.current_row = $(start_row_dom);
				return start_row_dom;
			}
			var method = 'prev' == which? $.fn.prevAll : $.fn.nextAll;
			return method.call($.table_hotkeys.current_row, opts.cycle_expr).filter(':visible')[0];
		}
		var make_key_callback = function(expr) {
			return function() {
				var clickable = $(expr, $.table_hotkeys.current_row).filter(':visible');
				if (!$($(clickable[0]).parent()[0]).is(':visible')) return false;
				if (clickable.is('.'+destructive_class)) next_row() || prev_row();
				clickable.click();
			}
		};
		var make_key_expr = function(elem) {
			if (typeof elem.key == 'string') {
				key = elem.key;
				if (typeof elem.expr == 'string')
					expr = elem.expr;
				else if (typeof elem.suffix == 'string')
					expr = '.'+opts.class_prefix+elem.suffix;
				else
					expr = '.'+opts.class_prefix+elem.key;
			} else {
				key = elem;
				expr = '.'+opts.class_prefix+elem;
			}
			return {key: key, expr: expr};
		};
		if (!$(opts.cycle_expr, table).length) return;
		jQuery.hotkeys.add(opts.next_key, opts.hotkeys_opts, next_row);
		jQuery.hotkeys.add(opts.prev_key, opts.hotkeys_opts, prev_row);
		jQuery.hotkeys.add(opts.mark_key, opts.hotkeys_opts, check);
		jQuery.each(keys, function() {
			var key_expr = make_key_expr(this);
			jQuery.hotkeys.add(key_expr.key, opts.hotkeys_opts, make_key_callback(key_expr.expr));
		});
		
	};
	$.table_hotkeys.current_row = null;
	$.table_hotkeys.defaults = {cycle_expr: 'tr', class_prefix: 'vim-', selected_suffix: 'current',
		destructive_suffix: 'destructive', hotkeys_opts: {disableInInput: true, type: 'keypress'},
		checkbox_expr: ':checkbox', next_key: 'j', prev_key: 'k', mark_key: 'x',
		start_row_index: 1};
})(jQuery);