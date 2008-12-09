
(function($) {
inlineEditTax = {

	init : function() {
		var t = this, row = $('#inline-edit');

		t.type = $('#the-list').attr('className').substr(5);
		t.what = '#'+t.type+'-';

		// get all editable rows
		t.rows = $('tr.iedit');

		// prepare the edit row
		row.keyup(function(e) { if(e.which == 27) return inlineEditTax.revert(); });

		$('a.cancel', row).click(function() { return inlineEditTax.revert(); });
		$('a.save', row).click(function() { return inlineEditTax.save(this); });
		$('input, select', row).keydown(function(e) { if(e.which == 13) return inlineEditTax.save(this); });

		// add events
		t.addEvents(t.rows);

		$('#posts-filter input[type="submit"]').click(function(e){
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
			$(this).find('a.editinline').click(function() { inlineEditTax.edit(this); return false; });
			$(this).find('.hide-if-no-js').removeClass('hide-if-no-js');
		});
	},

	edit : function(id) {
		var t = this;
		t.revert();

		if ( typeof(id) == 'object' )
			id = t.getId(id);

		var editRow = $('#inline-edit').clone(true), rowData = $('#inline_'+id);
		$('td', editRow).attr('colspan', $('.widefat:first thead th:visible').length);

		if ( $(t.what+id).hasClass('alternate') )
			$(editRow).addClass('alternate');

		$(t.what+id).hide().after(editRow);

		$(':input[name="name"]', editRow).val( $('.name', rowData).text() );
		$(':input[name="slug"]', editRow).val( $('.slug', rowData).text() );

		// cat parents
		var cat_parent = $('.cat_parent', rowData).text();
		if ( cat_parent != '0' )
			$('select[name="parent"]', editRow).val(cat_parent);

		// remove the current parent and children from the parent dropdown
		var pageOpt = $('select[name="parent"] option[value="'+id+'"]', editRow);
		if ( pageOpt.length > 0 ) {
			var pageLevel = pageOpt[0].className.split('-')[1], nextPage = pageOpt, pageLoop = true;
			while ( pageLoop ) {
				var nextPage = nextPage.next('option');
				if (nextPage.length == 0) break;
				var nextLevel = nextPage[0].className.split('-')[1];
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
		$('.ptitle', editRow).eq(0).focus();

		return false;
	},

	save : function(id) {
		if( typeof(id) == 'object' )
			id = this.getId(id);

		$('table.widefat .inline-edit-save .waiting').show();

		var params = {
			action: 'inline-save-tax',
			tax_type: this.type,
			tax_ID: id
		};

		var fields = $('#edit-'+id+' :input').fieldSerialize();
		params = fields + '&' + $.param(params);

		// make ajax request
		$.post('admin-ajax.php', params,
			function(r) {

				$('table.widefat .inline-edit-save .waiting').hide();

				if (r) {
					if ( -1 != r.indexOf('<tr') ) {
						$(inlineEditTax.what+id).remove();
						$('#edit-'+id).before(r).remove();

						var row = $(inlineEditTax.what+id);
						row.hide();

						row.find('.hide-if-no-js').removeClass('hide-if-no-js');
						inlineEditTax.addEvents(row);
						row.fadeIn();
					} else
						$('#edit-'+id+' .inline-edit-save .error').html(r).show();
				} else
					$('#edit-'+id+' .inline-edit-save .error').html(inlineEditL10n.error).show();
			}
		);
		return false;
	},

	revert : function() {
		var id = $('table.widefat tr.inline-editor').attr('id');

		if ( id ) {
			$('table.widefat .inline-edit-save .waiting').hide();
			$('#'+id).remove();
			id = id.substr( id.lastIndexOf('-') + 1 );
			$(this.what+id).show();
		}

		return false;
	},

	getId : function(o) {
		var id = o.tagName == 'TR' ? o.id : $(o).parents('tr').attr('id');
		var parts = id.split('-');
		return parts[parts.length - 1];
	}
};

$(document).ready(function(){inlineEditTax.init();});
})(jQuery);
