
	function advman_form_update(element){
		//element is the calling element, element.id has the identifier
		//detect calling form element and action accordingly
		switch(element.id){
			/* case 'advman-product':advman_update_product(); break; */
			case 'advman-adformat':advman_update_custom(); break;
			case 'advman-adtype':advman_update_formats(); break;
		}
	}
	
	function advman_select_update(element)
	{
		element.style.color = (element.options[0].selected) ? 'gray' : 'black';
	}
	function advman_update_ad(element,id,what)
	{
		target = document.getElementById(id);
		switch (what) {
			case 'bg':	target.style.background='#' + element.value; break;
			case 'border':	target.style.borderColor='#' + element.value; break;
			case 'font-link':
			case 'font-text':
			case 'font-title':
				target.style.fontFamily=element.value; break;
			default : target.style.color='#' + element.value; break;
		}
	}
	
	function advman_update_formats()
	{
		s = document.getElementById('advman-adtype');
		if (s) {
			n = s.length;
			for (i=0; i<n; i++) {
				v = s.options[i].value;
				r = document.getElementById('advman-form-adformat-'+v);
				if (r) {
					r.style.display = s.options[i].selected ? '' : 'none';
				}
			}
		}
	}

		
	function advman_update_custom()
	{
		if(document.getElementById('advman-adformat') && document.getElementById('advman-settings-custom')) {
			format=document.getElementById('advman-adformat').value;
			if(format=='custom'){on='';} else {on='none';}
			document.getElementById('advman-settings-custom').style.display=on
		}
	}
	
	
//Initialize everything (call the display/hide functions)
jQuery(document).ready( function($) {
	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	if (typeof(postboxes) != 'undefined') {
		postboxes.add_postbox_toggles('advman'); //wp2.7+
	} else {
		add_postbox_toggles('advman'); //wp2.6-
	}
	// Default options
//	$("#control_1, #control_3, #control_4, #control_5").multiSelect();
	
//	// With callback
//	$("#control_6").multiSelect( null, function(el) {
//		$("#callbackResult").show().fadeOut();
//	});
	
	// Options displayed in comma-separated list
	$("#advman-pagetype").multiSelect({
		oneOrMoreSelected: '*',
		allSelected: 'All Pages',
		noneSelected: 'No Pages',
		selectAllText: 'All Pages'
	});
	$("#advman-author").multiSelect({
		oneOrMoreSelected: '*',
		allSelected: 'All Authors',
		noneSelected: 'No Authors',
		selectAllText: 'All Authors'
	});
	$("#advman-category").multiSelect({
		oneOrMoreSelected: '*',
		allSelected: 'All Categories',
		noneSelected: 'No Categories',
		selectAllText: 'All Categories'
	});
	$("#advman-tag").multiSelect({
		oneOrMoreSelected: '*',
		allSelected: 'All Tags',
		noneSelected: 'No Tags',
		selectAllText: 'All Tags'
	});
	
//	// 'Select All' text changed
//	$("#control_8").multiSelect({ selectAllText: 'Pick &lsquo;em all!' });
	
	advman_update_custom();
	advman_update_formats();
});  
//End Initialise
