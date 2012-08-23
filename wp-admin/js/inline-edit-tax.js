
(function($) {
inlineEditTax = {

	init : function() {
		var t = this, row = $('#inline-edit');

		t.type = $('#the-list').attr('class').substr(5);
		t.what = '#'+t.type+'-';

		$('.editinline').live('click', function(){
			inlineEditTax.edit(this);
			return false;
		});

		// prepare the edit row
		row.keyup(function(e) { if(e.which == 27) return inlineEditTax.revert(); });

		$('a.cancel', row).click(function() { return inlineEditTax.revert(); });
		$('a.save', row).click(function() { return inlineEditTax.save(this); });
		$('input, select', row).keydown(function(e) { if(e.which == 13) return inlineEditTax.save(this); });

		$('#posts-filter input[type="submit"]').mousedown(function(e){
			t.revert();
		});
	},

	toggle : function(el) {
		var t = this;
		$(t.what+t.getId(el)).css('display') == 'none' ? t.revert() : t.edit(el);
	},

	edit : function(id) {
		var t = this, editRow;
		t.revert();

		if ( typeof(id) == 'object' )
			id = t.getId(id);

		editRow = $('#inline-edit').clone(true), rowData = $('#inline_'+id);
		$('td', editRow).attr('colspan', $('.widefat:first thead th:visible').length);

		if ( $(t.what+id).hasClass('alternate') )
			$(editRow).addClass('alternate');

		$(t.what+id).hide().after(editRow);

		$(':input[name="name"]', editRow).val( $('.name', rowData).text() );
		$(':input[name="slug"]', editRow).val( $('.slug', rowData).text() );

		$(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
		$('.ptitle', editRow).eq(0).focus();

		return false;
	},

	save : function(id) {
		var params, fields, tax = $('input[name="taxonomy"]').val() || '';

		if( typeof(id) == 'object' )
			id = this.getId(id);

		$('table.widefat .inline-edit-save .waiting').show();

		params = {
			action: 'inline-save-tax',
			tax_type: this.type,
			tax_ID: id,
			taxonomy: tax
		};

		fields = $('#edit-'+id+' :input').serialize();
		params = fields + '&' + $.param(params);

		// make ajax request
		$.post( ajaxurl, params,
			function(r) {
				var row, new_id;
				$('table.widefat .inline-edit-save .waiting').hide();

				if (r) {
					if ( -1 != r.indexOf('<tr') ) {
						$(inlineEditTax.what+id).remove();
						new_id = $(r).attr('id');

						$('#edit-'+id).before(r).remove();
						row = new_id ? $('#'+new_id) : $(inlineEditTax.what+id);
						row.hide().fadeIn();
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
		var id = o.tagName == 'TR' ? o.id : $(o).parents('tr').attr('id'), parts = id.split('-');
		return parts[parts.length - 1];
	}
};

$(document).ready(function(){inlineEditTax.init();});
})(jQuery);
