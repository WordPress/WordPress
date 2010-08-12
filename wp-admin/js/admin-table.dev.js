jQuery(document).ready(function($) {
	$('form').each(function() {
		this.reset();
	});

	if ( '' == $.query.GET('paged') )
		$.query.SET('paged', 1);

	var total_pages;
	var set_total_pages = function() {
		total_pages = parseInt($('.total-pages').eq(0).text());
	}

	set_total_pages();

	var loading = false,
		$tbody = $('#the-list, #the-comment-list'),
		$overlay = $('<div id="loading-items>')
			.html(adminTableL10n.loading)
			.hide()
			.prependTo($('body'));

	var show_overlay = function() {
		loading = true;

		$('.error.ajax').remove();

		$overlay
			.css({
				width: $tbody.width() + 'px',
				height: $tbody.height() - 20 + 'px'
			})
			.css($tbody.offset())
			.show();
	}

	var hide_overlay = function() {
		loading = false;
		$overlay.hide();
	}

	var handle_error = function() {
		hide_overlay();

		$('h2').after('<div class="error ajax below-h2"><p>' + adminTableL10n.error + '</p></div>');
	}

	var update_rows = function(args, reset_paging, callback) {
		if ( loading )
			return false;

		var different = false;

		$.each(args, function(key, val) {
			if ( val != $.query.GET(key) ) {
				$.query.SET(key, val);
				different = true;
			}
		});
		
		if ( !different )
			return false;

		show_overlay();

		if ( reset_paging )
			$.query.SET('paged', 1);

		var data = $.query.get();

		data['action'] = 'fetch-list';
		data['list_args'] = list_args;

		$.ajax({
			url: ajaxurl,
			global: false,
			dataType: 'json',
			data: data,
			success: function(response) {
				if ( 'object' != typeof response ) {
					handle_error();
				} else {
					hide_overlay();

					$tbody.html(response.rows);

					$('.displaying-num').html(response.total_items);

					$('.total-pages').html(response.total_pages);
					set_total_pages();

					$('.current-page').val($.query.GET('paged'));

					if ( callback )
						callback();
				}
			},
			error: handle_error
		});

		return true;
	}

	// paging
	var change_page = function(paged) {
		if ( paged < 1 || paged > total_pages )
			return false;

		update_rows({'paged': paged});
	}

	$('.tablenav-pages a').click(function() {
		var paged = $.query.GET('paged');

		switch ( $(this).attr('class') ) {
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
				paged = total_pages;
				break;
		}

		change_page(paged);

		return false;
	});

	$('.current-page').keypress(function(e) {
		if ( 13 != e.keyCode )
			return;

		change_page(parseInt($(this).val()));

		return false;
	});

	// sorting
	$('th a').click(function() {
		var orderby = $.query.GET('orderby'),
			order = $.query.GET('order'),
			$th = $(this).parent('th');

		if ( $th.hasClass('sortable') ) {
			orderby = $.query.load($(this).attr('href')).get('orderby');
			order = 'asc';

			$('th.sorted-desc, th.sorted-asc')
				.removeClass('sorted-asc')
				.removeClass('sorted-desc')
				.addClass('sortable');

			$th.removeClass('sortable').addClass('sorted-asc');
		}
		else if ( $th.hasClass('sorted-asc') ) {
			order = 'desc';
			$th.removeClass('sorted-asc').addClass('sorted-desc');
		}
		else if ( $th.hasClass('sorted-desc') ) {
			order = 'asc';
			$th.removeClass('sorted-desc').addClass('sorted-asc');
		}

		update_rows({'orderby': orderby, 'order': order}, true);

		return false;
	});

	// searching
	var htmlencode = function(value) {
		return $('<div/>').text(value).html();
	}

	var change_search = function(s) {
		update_rows({'s': s}, true, function() {
			$('h2 .subtitle').remove();

			if ( s )
				$('h2').eq(0).append($('<span class="subtitle">').html(adminTableL10n.search.replace('%s', htmlencode(s))));
		});
	}

	$('.search-box :submit').click(function() {
		change_search($(this).parent('.search-box').find(':text').val());

		return false;
	});

	$('.search-box :text').keypress(function(e) {
		if ( 13 != e.keyCode )
			return;

		change_search($(this).val());

		return false;
	});

	// tablenav dropdowns
	$('#post-query-submit').click(function() {
		var $this = $(this), key, val, args = {};

		$this.parents('.actions').find('select[name!="action"]').each(function() {
			args[$this.attr('name')] = $this.val();
		});

		update_rows(args, true);

		return false;
	});

	// view switch
	$('.view-switch a').click(function() {
		var $this = $(this);

		update_rows({'mode': $.query.load($this.attr('href')).get('mode')}, false, function() {
			$('.view-switch .current').removeClass('current');
			$this.addClass('current');
		});

		return false;
	});

/*
	// problem: when switching from one to the other, columns are not always the same
	$('.subsubsub a').click(function() {
		var $this = $(this);

		update_rows($.query.load($this.attr('href')).get(), true, function() {
			$('.subsubsub .current').removeClass('current');
			$this.addClass('current');
		});

		return false;
	});
/**/
});

