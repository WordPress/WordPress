/*
	post-formats.js
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Copyright: (c) 2013 Jermaine Maree, http://jermainemaree.com
*/

jQuery(document).ready(function($) {

	// Hide post format sections
	function hide_statuses() {
		$('#format-audio,#format-aside,#format-chat,#format-gallery,#format-image,#format-link,#format-quote,#format-status,#format-video').hide();
	}

	// Post Formats
	if($("#post-formats-select").length) {
		// Hide post format sections
		hide_statuses();

		// Supported post formats
		var post_formats = ['audio','aside','chat','gallery','image','link','quote','status','video'];

		// Get selected post format
		var selected_post_format = $("input[name='post_format']:checked").val();

		// Show post format meta box
		if(jQuery.inArray(selected_post_format,post_formats) != '-1') {
			$('#format-'+selected_post_format).show();
		}

		// Hide/show post format meta box when option changed
		$("input[name='post_format']:radio").change(function() {
			// Hide post format sections
			hide_statuses();
			// Shoe selected section
			if(jQuery.inArray($(this).val(),post_formats) != '-1') {
				$('#format-'+$(this).val()).show();
			}
		});
	}

});