var postType   = null;
var postView   = null;
var postsXml   = null;
var inlineRows = null;

jQuery(document).ready(function() {
	postType = window.location.href.indexOf('edit.php') == -1 ? 'page' : 'post';
	postView = window.location.href.indexOf('mode=excerpt') == -1 ? 'list' : 'excerpt';

	// get IDs of all editable rows
	inlineRows = jQuery('table.widefat .check-column :checkbox[name="post[]"]').parents('tr');

	// prepare the edit row
	var blankRow = jQuery('#inline-edit');
	jQuery('ul.categories *', blankRow).removeAttr('id');
	jQuery('ul.categories label', blankRow).removeAttr('for');
	blankRow.attr('title', 'Double-click to cancel')
		.dblclick(function() { toggleRow(this); })
		.keypress(function(event) { if(event.which == 13) return saveRow(this); });
	jQuery('span.cancel a', blankRow).click(function() { return revertRow(this); });
	jQuery('span.save a', blankRow).click(function() { return saveRow(this); });

	// add events and get data
	inlineRows.dblclick(function() { toggleRow(this); });
	addEvents(inlineRows);

	// get data
	getInlineData('all');
});

function toggleRow(el) {
	jQuery('#'+postType+'-'+getRowId(el)).css('display') == 'none' ? revertRow(el) : editRow(el);
}

// add events to links and make rows double-clickable
function addEvents(rows) {
	rows.each(function() {
		var row = jQuery(this);
		jQuery('a.editinline', row).click(function() { editRow(this); return false; });
		row.attr('title', 'Double-click to edit');
	});
}

function getInlineData(id) {	
	if(id == 'all') {
		var editable = [];
		inlineRows.each(function(i) { editable[i] = getRowId(this); });	
		id = editable.join(',');
	}

	if(id == '') 
		return false;
	
	jQuery.post('admin-ajax.php', 
		{ 
			action: 'inline-data', 
			posts: id 
		}, 
		function(xml) {
			if(id.indexOf(',') == -1) {
				var newData = jQuery(xml).find('post[id="'+id+'"]');
				jQuery(postsXml).find('post[id="'+id+'"]').replaceWith(newData);
			} else {
				postsXml = xml;
			}
		}, 'xml'
	);
}

function editRow(id) {
	if(typeof(id) == 'object')
		id = getRowId(id);

	var blankRow = jQuery('#inline-edit');

	var fields = ['post_title', 'post_name', 'post_author', 'post_status', 'jj', 'mm', 'aa', 'hh', 'mn'];
	if(postType == 'page') fields.push('post_parent', 'menu_order', 'page_template', 'post_password');
	if(postType == 'post') fields.push('tags_input');

	// add the new blank row
	var editRow = blankRow.clone(true);
	jQuery(editRow).attr('id', 'edit-'+id).addClass('inline').show();
	if(jQuery('#'+postType+'-'+id).hasClass('alternate'))
		jQuery(editRow).addClass('alternate');
	jQuery('#'+postType+'-'+id).hide().after(editRow);
	
	// populate the data
	var rowData = jQuery(postsXml).find('post[id="'+id+'"]');
	for(var f = 0; f < fields.length; f++) {
		jQuery(':input[name="'+fields[f]+'"]', editRow).val(jQuery(fields[f], rowData).text());
	}
	
	// ping, comments, and privacy
	if(jQuery('comment_status', rowData).text() == 'open')
		jQuery('input[name="comment_status"]', editRow).select();
	if(jQuery('ping_status', rowData).text() == 'open')
		jQuery('input[name="ping_status"]', editRow).select();
	if(jQuery('sticky', rowData).text() == 'sticky')
		jQuery('input[name="sticky"]', editRow).select();
	
	// categories
	var categories = jQuery('post_category', rowData).text().split(',');
	jQuery(categories).each(function() {
		jQuery('ul.categories :checkbox[value="'+this+'"]', editRow).select();
	});
	
	// handle the post status
	var status = jQuery('post_status', rowData).text();
	if(status != 'future') jQuery('select[name="post_status"] option[value="future"]', editRow).remove();
	if(status == 'private') jQuery('input[name="page_private"]', editRow).select();
	
	// enable autocomplete for tags
	if(postType == 'post') {
		jQuery('tr.inline textarea[name="tags_input"]').suggest( 'admin-ajax.php?action=ajax-tag-search', { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );
	}
	
	// remove the current page and children from the parent dropdown
	var pageOpt = jQuery('select[name="post_parent"] option[value="'+id+'"]', editRow);
	if(pageOpt.length > 0) {
		var pageLevel = pageOpt[0].className.split('-')[1];
		var nextPage = pageOpt; var pageLoop = true;
		while(pageLoop) {
			var nextPage  = nextPage.next('option');
			var nextLevel = nextPage[0].className.split('-')[1];
			if(nextLevel <= pageLevel)
				pageLoop = false;
			else {
				nextPage.remove();
				nextPage = pageOpt;
			}
		}
		pageOpt.remove();
	}

	return false;
}

function saveRow(id) {
	if(typeof(id) == 'object')
	  id = getRowId(id);

	jQuery('#edit-'+id+' .check-column').html('<img src="images/loading.gif" alt="Saving..." />');

	var params = {
		action:    'inline-save',
		post_type: postType,
		post_ID:   id,
		edit_date: 'true',
		post_view: postView
	};

	var fields = jQuery('#edit-'+id+' :input').fieldSerialize();
	params = fields + '&' + jQuery.param(params);
    
	// make ajax request
	jQuery.post('admin-ajax.php', params,
		function(html) { 
			var row = jQuery('#'+postType+'-'+id); 
			jQuery('#edit-'+id).hide();
			html = jQuery(html).html();
			row.html(html).show();
			row.animate( { backgroundColor: '#FFFBCC' }, 200)
				 .animate( { backgroundColor: row.css('background-color') }, 500);
			getInlineData(id);
			addEvents(row);
		}
	);

	return false;
}

function revertRow(id) {
	if(typeof(id) == 'object') 
		id = getRowId(id);

	jQuery('#edit-'+id).remove();
	jQuery('#'+postType+'-'+id).show();

	return false;
}

function getRowId(obj) {
	var id = obj.tagName == 'TR' ? obj.id : jQuery(obj).parents('tr').attr('id');
	var parts = id.split('-');
	return parts[parts.length - 1];
}