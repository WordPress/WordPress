
(function($) {
inlineEdit = {
	type : '',
	rows : '',

	init : function() {
		var t = this, blankRow = $('#inline-edit');

		t.type = $('table.widefat').hasClass('page') ? 'page' : 'post';

		// get all editable rows
		t.rows = $('tr.iedit');

		// prepare the edit row
		blankRow.dblclick(function() { inlineEdit.toggle(this); })
			.keyup(function(e) { if(e.which == 27) return inlineEdit.revert(this); });

		$('a.cancel', blankRow).click(function() { return inlineEdit.revert(this); });
		$('a.save', blankRow).click(function() { return inlineEdit.save(this); });

		// add events
		t.rows.dblclick(function() { inlineEdit.toggle(this); });
		t.addEvents(t.rows);
	},

	toggle : function(el) {
		var t = this;

		$('#'+t.type+'-'+t.getId(el)).css('display') == 'none' ? t.revert(el) : t.edit(el);
	},

	addEvents : function(r) {
		r.each(function() {
			var row = $(this);
			$('a.editinline', row).click(function() { inlineEdit.edit(this); return false; });
			row.attr('title', inlineEditL10n.edit);
		});
	},

	edit : function(id) {
		var t = this, type = t.type, old = $('tr.inline-editor').attr('id');

		if( typeof(id) == 'object' )
			id = t.getId(id);

		if ( old ) {
			old = old.split('-')[1];
			t.revert(old);
		}

		var fields = ['post_title', 'post_name', 'post_author', 'post_status', 'jj', 'mm', 'aa', 'hh', 'mn', 'post_password'];
		if ( type == 'page' ) fields.push('post_parent', 'menu_order', 'page_template');
		if ( type == 'post' ) fields.push('tags_input');

		// add the new blank row
		var editRow = $('#inline-edit').clone(true);

		if ( $('#'+type+'-'+id).hasClass('alternate') )
			$(editRow).addClass('alternate');
		$('#'+type+'-'+id).hide().after(editRow);

		// populate the data
		var rowData = $('#inline_'+id);
		for ( var f = 0; f < fields.length; f++ ) {
			$(':input[name="'+fields[f]+'"]', editRow).val( $('.'+fields[f], rowData).val() );
		}

		if ( $('.comment_status', rowData).val() == 'open' )
			$('input[name="comment_status"]', editRow).attr("checked", "checked");
		if ( $('.ping_status', rowData).val() == 'open' )
			$('input[name="ping_status"]', editRow).attr("checked", "checked");
		if ( $('.sticky', rowData).val() == 'sticky' )
			$('input[name="sticky"]', editRow).attr("checked", "checked");

		// categories
		var cats;
		if ( cats = $('.post_category', rowData).val() )
			$('ul.cat-checklist :checkbox').val(cats.split(','));

		// handle the post status
		var status = $('.post_status', rowData).val();
		if ( status != 'future' ) $('select[name="post_status"] option[value="future"]', editRow).remove();
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

		// categories expandable?
		$('span.catshow', editRow).click(function() {
			$('ul.cat-checklist', editRow).addClass("cat-hover");
			$('span.cathide', editRow).show();
			$(this).hide();
		});

		$('span.cathide', editRow).click(function() {
			$('ul.cat-checklist', editRow).removeClass("cat-hover");
			$('span.catshow', editRow).show();
			$(this).hide();
		});

		$(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
		$('.ptitle', editRow).focus();

		// enable autocomplete for tags
		if ( type == 'post' )
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
			function(html) {
				var row = $('#'+inlineEdit.type+'-'+id);
				$('#edit-'+id).hide();
				html = $(html).html();
				row.html(html).show();
				row.animate( { backgroundColor: '#FFFBCC' }, 200)
					 .animate( { backgroundColor: row.css('background-color') }, 500);
				inlineEdit.addEvents(row);
			}
		);
		return false;
	},

	revert : function(id) {
		if ( typeof(id) == 'object' )
			id = this.getId(id);

		$('#edit-'+id).remove();
		$('#'+this.type+'-'+id).show();

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
