jQuery(document).ready(function($) {

window.listTable = {

	init: function() {
		this.loading = false;

		this.reset( '.tablenav, .search-box, .wp-list-table' );

		if ( '' == $.query.GET('paged') )
			$.query.SET('paged', 1);
		this.set_total_pages();

		this.$tbody = $('#the-list, #the-comment-list');
	},

	/**
	 * Simulates form.reset() for all input, select, and textarea elements
	 * within a provided context.
	 */
	reset: function( context ) {
		context = $(context);

		$('input', context).each( function(){
			this.value = this.defaultValue;
			this.checked = this.defaultChecked;
		});

		$('select', context).each( function(){
			var options = $('option', this),
				anySelected = false;

			options.each( function(){
				this.selected = this.defaultSelected;
				anySelected = anySelected || this.defaultSelected;
			});

			// If no options are selected within a single-select dropdown,
			// select the first element by default.
			if ( ! this.multiple && ! anySelected )
				options[0].selected = true;
		});

		$('textarea', context).each( function(){
			this.value = this.defaultValue;
		});
	},

	// paging
	set_total_pages: function(num) {
		var last_page_url = $('.last-page').attr('href');

		if ( last_page_url )
			this.total_pages = num || $.query.load( last_page_url ).get('paged');
	},

	get_total_pages: function() {
		return this.total_pages;
	},

	htmlencode: function(value) {
		return $('<div/>').text(value).html();
	},

	update_rows: function(args, reset_paging, callback) {
		if ( this.loading )
			return false;

		var different = false, data = {};

		$.each(args, function(key, val) {
			if ( val != $.query.GET(key) ) {
				$.query.SET(key, val);
				different = true;
			}
		});

		if ( !different )
			return false;

		this.start_loading();

		if ( reset_paging )
			$.query.SET('paged', 1);

		$.each( $.query.get(), function(key, value) {
			if ( true === value )
				data[key] = '';
			else
				data[key] = value;
		});

		this._callback = callback;

		this.fetch_list(
			data,
			$.proxy(this, 'handle_success'),
			$.proxy(this, 'handle_error')
		);

		return true;
	},

	fetch_list: function(data, success_callback, error_callback) {
		data = $.extend(data, {
			'action': 'fetch-list',
			'list_args': list_args,
			'_ajax_fetch_list_nonce': $('#_ajax_fetch_list_nonce').val()
		});

		$.ajax({
			url: ajaxurl,
			global: false,
			dataType: 'json',
			data: data,
			success: success_callback,
			error: error_callback
		});
	},

	handle_success: function(response) {
		if ( 'object' != typeof response ) {
			this.handle_error();
		} else {
			var tablenav = $('.tablenav-pages');

			this.stop_loading();

			$('div.updated, div.error').not('.persistent, .inline').remove();

			this.$tbody.html(response.rows);

			$('.displaying-num').html(response.total_items_i18n);
			$('.total-pages').html(response.total_pages_i18n);

			this.set_total_pages(response.total_pages);

			if ( response.total_pages > 1 )
				tablenav.removeClass('one-page');

			$('.current-page').val($.query.GET('paged'));

			// Disable buttons that should noop.
			tablenav.find('.first-page, .prev-page').toggleClass('disabled', 1 == $.query.GET('paged'));
			tablenav.find('.next-page, .last-page').toggleClass('disabled', response.total_pages == $.query.GET('paged'));

			$('th.column-cb :input').attr('checked', false);

			if ( history.replaceState ) {
				history.replaceState({}, '', location.pathname + $.query);
			}

			if ( this._callback )
				this._callback();
		}
	},

	handle_error: function() {
		this.stop_loading();

		$('h2').after('<div class="error ajax below-h2"><p>' + listTableL10n.error + '</p></div>');
	},

	start_loading: function() {
		this.loading = true;

		$('.error.ajax').remove();

		$('.list-ajax-loading').css('visibility', 'visible');
	},

	stop_loading: function() {
		this.loading = false;

		$('.list-ajax-loading').css('visibility', 'hidden');
	}
}

listTable.init();

// Ajaxify various UI elements

	function change_page(paged, $el) {
		if ( paged < 1 )
			paged = 1;

		if ( paged > listTable.get_total_pages() )
			paged = listTable.get_total_pages();

		$(listTable).trigger('beforeChangePage');
		listTable.update_rows({'paged': paged}, false, function() {
			if ( $el.parents('.tablenav.bottom').length )
				scrollTo(0, 0);

			$(listTable).trigger('changePage');
		});
	}

	// pagination
	$('.tablenav-pages a').click(function() {
		var $el = $(this),
			paged = $.query.GET('paged');

		switch ( $el.attr('class') ) {
			case 'first-page':
				paged = 1;
				break;
			case 'prev-page':
				paged -= 1;
				break;
			case 'next-page':
				paged += 1;
				break;
			case 'last-page':
				paged = listTable.get_total_pages();
				break;
		}

		change_page(paged, $el);

		return false;
	});

	$('.current-page').keypress(function(e) {
		if ( 13 != e.keyCode )
			return;

		var $el = $(this);

		change_page(parseInt($el.val()) || 1, $el);

		return false;
	});

	// sortable columns
	$('th.sortable a, th.sorted a').click(function() {

		function get_initial_order($el) {
			return $.query.load( $el.find('a').attr('href') ).get('order');
		}

		var $link = $(this),
			$th = $link.parent('th'),
			thIndex = $th.index(),
			orderby = $.query.load( $link.attr('href') ).get('orderby'),
			order;

		// th should include both headers in thead and tfoot
		$th = $th.closest('table').find('thead th:eq(' + thIndex + '), tfoot th:eq(' + thIndex + ')');

		if ( orderby == $.query.get('orderby') ) {
			// changing the direction
			order = ( 'asc' == $.query.get('order') ) ? 'desc' : 'asc';
		} else {
			// changing the parameter
			order = get_initial_order($th);

			var $old_th = $('th.sorted');
			if ( $old_th.length ) {
				$old_th.removeClass('sorted').addClass('sortable');
				$old_th.removeClass('desc').removeClass('asc').addClass(
					'asc' == get_initial_order( $old_th ) ? 'desc' : 'asc'
				);
			}

			$th.removeClass('sortable').addClass('sorted');
		}

		$th.removeClass('desc').removeClass('asc').addClass(order);

		listTable.update_rows({'orderby': orderby, 'order': order}, true);

		return false;
	});

	// searchbox
	function change_search(ev) {
		if ( 'keypress' == ev.type && 13 != ev.keyCode )
			return;

		ev.preventDefault();
		ev.stopImmediatePropagation();

		var data = $(this).parent('.search-box').find(':input').serializeObject();

		listTable.update_rows(data, true, function() {
			if ( $('h2.nav-tab-wrapper').length )
				return;

			if ( 'site-users-network' == pagenow || 'site-themes-network' == pagenow ) {
				$('h4.search-text').remove();

				if ( data.s )
					$('ul.subsubsub').after($('<h4 class="clear search-text">').html(
						listTableL10n.search.replace('%s', this.htmlencode(data.s))
					));
			} else {
				$('h2 .subtitle').remove();

				if ( data.s )
					$('h2').append($('<span class="subtitle">').html(
						listTableL10n.search.replace('%s', this.htmlencode(data.s))
					));
			}
		});
	}
	$('.search-box :submit').click(change_search);
	$('.search-box :text').keypress(change_search);

	// tablenav dropdowns
	$('#post-query-submit').click(function() {
		var args = {};

		$(this).parents('.actions').find('select[name!="action"]').each(function() {
			var $el = $(this);

			args[$el.attr('name')] = $el.val();
		});

		listTable.update_rows(args, true);

		return false;
	});

	// view switch
	$('.view-switch a').click(function() {
		var $this = $(this);

		listTable.update_rows({'mode': $.query.load($this.attr('href')).get('mode')}, false, function() {
			$('.view-switch .current').removeClass('current');
			$this.addClass('current');
		});

		return false;
	});
});

