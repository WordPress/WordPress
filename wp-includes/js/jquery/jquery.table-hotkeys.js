(function($){
	$.fn.filter_visible = function(depth) {
		depth = depth || 3;
		var is_visible = function() {
			var p = $(this);
			for(i=0; i<depth-1; ++i) {
				if (!p.is(':visible')) return false;
				p = p.parent();
			}
			return true;
		}
		return this.filter(is_visible);
	};
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
		var adjacent_row_callback = function(which) {
			if (!adjacent_row(which) && $.isFunction(opts[which+'_page_link_cb'])) {
				opts[which+'_page_link_cb']();
			}
		};
		var get_adjacent_row = function(which) {
			if (!$.table_hotkeys.current_row) {
				var first_row = get_first_row();
				$.table_hotkeys.current_row = first_row;
				return first_row[0];
			}
			var method = 'prev' == which? $.fn.prevAll : $.fn.nextAll;
			return method.call($.table_hotkeys.current_row, opts.cycle_expr).filter_visible()[0];
		};
		var adjacent_row = function(which) {
			var adj = get_adjacent_row(which);
			if (!adj) return false;
			set_current_row($(adj));
			return true;
		};
		var prev_row = function() { return adjacent_row('prev'); };
		var next_row = function() { return adjacent_row('next'); };
		var check = function() {
			$(opts.checkbox_expr, $.table_hotkeys.current_row).each(function() {
				this.checked = !this.checked;
			});
		};
		var get_first_row = function() {
			return $(opts.cycle_expr, table).filter_visible().eq(opts.start_row_index);
		};
		var get_last_row = function() {
			var rows = $(opts.cycle_expr, table).filter_visible();
			return rows.eq(rows.length-1);
		};
		var make_key_callback = function(expr) {
			return function() {
				if ( null == $.table_hotkeys.current_row ) return false;
				var clickable = $(expr, $.table_hotkeys.current_row);
				if (!clickable.length) return false;
				if (clickable.is('.'+destructive_class)) next_row() || prev_row();
				clickable.click();
			}
		};
		var first_row = get_first_row();
		if (!first_row.length) return;
		if (opts.highlight_first)
			set_current_row(first_row);
		else if (opts.highlight_last)
			set_current_row(get_last_row());
		jQuery.hotkeys.add(opts.prev_key, opts.hotkeys_opts, function() {return adjacent_row_callback('prev')});
		jQuery.hotkeys.add(opts.next_key, opts.hotkeys_opts, function() {return adjacent_row_callback('next')});
		jQuery.hotkeys.add(opts.mark_key, opts.hotkeys_opts, check);
		jQuery.each(keys, function() {
			if ($.isFunction(this[1])) {
				var callback = this[1];
				var key = this[0];
				jQuery.hotkeys.add(key, opts.hotkeys_opts, function(event) { return callback(event, $.table_hotkeys.current_row); });
			} else {
				var key = this;
				jQuery.hotkeys.add(key, opts.hotkeys_opts, make_key_callback('.'+opts.class_prefix+key));
			}
		});

	};
	$.table_hotkeys.current_row = null;
	$.table_hotkeys.defaults = {cycle_expr: 'tr', class_prefix: 'vim-', selected_suffix: 'current',
		destructive_suffix: 'destructive', hotkeys_opts: {disableInInput: true, type: 'keypress'},
		checkbox_expr: ':checkbox', next_key: 'j', prev_key: 'k', mark_key: 'x',
		start_row_index: 2, highlight_first: false, highlight_last: false, next_page_link_cb: false, prev_page_link_cb: false};
})(jQuery);
