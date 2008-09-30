
(function($) {
inlineEdit = {

	init : function() {
		var t = this, qeRow = $('#inline-edit'), bulkRow = $('#bulk-edit');

		t.type = $('table.widefat').hasClass('page') ? 'page' : 'post';
		t.what = '#'+t.type+'-';

		// get all editable rows
		t.rows = $('tr.iedit');

		// prepare the edit row
		qeRow.dblclick(function() { inlineEdit.toggle(this); })
			.keyup(function(e) { if(e.which == 27) return inlineEdit.revert(); });

		bulkRow.dblclick(function() { inlineEdit.revert(); })
			.keyup(function(e) { if (e.which == 27) return inlineEdit.revert(); });

		$('a.cancel', qeRow).click(function() { return inlineEdit.revert(); });
		$('a.save', qeRow).click(function() { return inlineEdit.save(this); });

		$('a.cancel', bulkRow).click(function() { return inlineEdit.revert(); });
		$('a.save', bulkRow).click(function() { return inlineEdit.saveBulk(); });

		// add events
		t.rows.dblclick(function() { inlineEdit.toggle(this); });
		t.addEvents(t.rows);

		$('#bulk-title-div').after(
			$('#inline-edit div.categories').clone(),
			$('#inline-edit div.tags').clone()
		);

		// categories expandable?
		$('span.catshow').click(function() {
			$('.inline-editor ul.cat-checklist').addClass("cat-hover");
			$('.inline-editor span.cathide').show();
			$(this).hide();
		});

		$('span.cathide').click(function() {
			$('.inline-editor ul.cat-checklist').removeClass("cat-hover");
			$('.inline-editor span.catshow').show();
			$(this).hide();
		});

		$('select[name="_status"] option[value="future"]', bulkRow).remove();

		$('#doaction').click(function(e){
			if ( $('select[name="action"]').val() == 'edit' ) {
				e.preventDefault();
				t.setBulk();
			} else if ( $('form#posts-filter tr.inline-editor').length > 0 ) {
				t.revert();
			}
		});

		$('#doaction2').click(function(e){
			if ( $('select[name="action2"]').val() == 'edit' ) {
				e.preventDefault();
				t.setBulk();
			} else if ( $('form#posts-filter tr.inline-editor').length > 0 ) {
				t.revert();
			}
		});
		
		$('#post-query-submit').click(function(e){
			if ( $('form#posts-filter tr.inline-editor').length > 0 )
				t.revert();
		});
		
	},

	toggle : function(el) {
		var t = this;

		$(t.what+t.getId(el)).css('display') == 'none' ? t.revert() : t.edit(el);
	},

	addEvents : function(r) {
		r.each(function() {
			var row = $(this);
			$('a.editinline', row).click(function() { inlineEdit.edit(this); return false; });
			row.attr('title', inlineEditL10n.edit);
		});
	},

	setBulk : function() {
		var te = '', c = '';
		this.revert();

		$('table.widefat tbody').prepend( $('#bulk-edit') );
		$('#bulk-edit').addClass('inline-editor').show();

		$('tbody th.check-column input[type="checkbox"]').each(function(i){
			if ( $(this).attr('checked') ) {
				var id = $(this).val();
				c = c == '' ? ' class="alternate"' : '';
				te += '<div'+c+' id="ttle'+id+'"><a id="_'+id+'" class="ntdelbutton">X</a>'+$('#inline_'+id+' .post_title').text()+'</div>';
			}
		});

		$('#bulk-titles').html(te);
		$('#bulk-titles a').click(function() {
			var id = $(this).attr('id').substr(1), r = inlineEdit.type+'-'+id;

			$('table.widefat input[value="'+id+'"]').attr('checked', '');
			$('#ttle'+id).remove();
		});

		// enable autocomplete for tags
		if ( this.type == 'post' )
			$('tr.inline-editor textarea[name="tags_input"]').suggest( 'admin-ajax.php?action=ajax-tag-search', { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );
	},

	edit : function(id) {
		var t = this;
		t.revert();

		if ( typeof(id) == 'object' )
			id = t.getId(id);

		var fields = ['post_title', 'post_name', 'post_author', '_status', 'jj', 'mm', 'aa', 'hh', 'mn', 'post_password'];
		if ( t.type == 'page' ) fields.push('post_parent', 'menu_order', 'page_template');
		if ( t.type == 'post' ) fields.push('tags_input');

		// add the new blank row
		var editRow = $('#inline-edit').clone(true);

		if ( $(t.what+id).hasClass('alternate') )
			$(editRow).addClass('alternate');
		$(t.what+id).hide().after(editRow);

		// populate the data
		var rowData = $('#inline_'+id);
		for ( var f = 0; f < fields.length; f++ ) {
			$(':input[name="'+fields[f]+'"]', editRow).val( $('.'+fields[f], rowData).text() );
		}

		if ( $('.comment_status', rowData).text() == 'open' )
			$('input[name="comment_status"]', editRow).attr("checked", "checked");
		if ( $('.ping_status', rowData).text() == 'open' )
			$('input[name="ping_status"]', editRow).attr("checked", "checked");
		if ( $('.sticky', rowData).text() == 'sticky' )
			$('input[name="sticky"]', editRow).attr("checked", "checked");

		// categories
		var cats;
		if ( cats = $('.post_category', rowData).text() )
			$('ul.cat-checklist :checkbox', editRow).val(cats.split(','));

		// handle the post status
		var status = $('._status', rowData).text();
		if ( status != 'future' ) $('select[name="_status"] option[value="future"]', editRow).remove();
		if ( status == 'private' ) $('input[name="keep_private"]', editRow).attr("checked", "checked");

		// remove the current page and children from the parent dropdown
		var pageOpt = $('select[name="post_parent"] option[value="'+id+'"]', editRow);
		if ( pageOpt.length > 0 ) {
			var pageLevel = pageOpt[0].className.split('-')[1], nextPage = pageOpt, pageLoop = true;
			while ( pageLoop ) {
				var nextPage = nextPage.next('option'), nextLevel = nextPage[0].className.split('-')[1];
				if ( nextLevel <= pageLevel ) {
					pageLoop = false;
				} else {
					nextPage.remove();
					nextPage = pageOpt;
				}
			}
			pageOpt.remove();
		}

		$(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
		$('.ptitle', editRow).focus();

		// enable autocomplete for tags
		if ( t.type == 'post' )
			$('tr.inline-editor textarea[name="tags_input"]').suggest( 'admin-ajax.php?action=ajax-tag-search', { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );

		return false;
	},

	save : function(id) {
		if( typeof(id) == 'object' )
			id = this.getId(id);

		$('#edit-'+id+' .check-column').html('<img src="images/loading.gif" alt="" />');

		var params = {
			action: 'inline-save',
			post_type: this.type,
			post_ID: id,
			edit_date: 'true'
		};

		var fields = $('#edit-'+id+' :input').fieldSerialize();
		params = fields + '&' + $.param(params);

		// make ajax request
		$.post('admin-ajax.php', params,
			function(r) {
				var row = $(inlineEdit.what+id);
				$('#edit-'+id).remove();
				row.html($(r).html()).show()
					.animate( { backgroundColor: '#CCEEBB' }, 500)
					.animate( { backgroundColor: '#eefee7' }, 500);
				inlineEdit.addEvents(row);
			}
		);
		return false;
	},

	saveBulk : function() {
		$('form#posts-filter').submit();
	},

	revert : function() {
		var id;

		if ( id = $('table.widefat tr.inline-editor').attr('id') ) {
			if ( 'bulk-edit' == id ) {
				$('table.widefat #bulk-edit').removeClass('inline-editor').hide();
				$('#bulk-titles').html('');
				$('#inlineedit').append( $('#bulk-edit') );
			} else  {
				$('#'+id).remove();
				id = id.substr( id.lastIndexOf('-') + 1 );
				$(this.what+id).show();
			}
		}

		return false;
	},

	getId : function(o) {
		var id = o.tagName == 'TR' ? o.id : $(o).parents('tr').attr('id');
		var parts = id.split('-');
		return parts[parts.length - 1];
	}
};

$(document).ready(function(){inlineEdit.init();});
})(jQuery);
