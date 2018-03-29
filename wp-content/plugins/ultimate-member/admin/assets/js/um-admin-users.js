jQuery(document).ready(function() {

	function getParameterByName(name, url) {
	    if (!url) url = window.location.href;
	    name = name.replace(/[\[\]]/g, "\\$&");
	    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
	        results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, " "));
	}

	jQuery("#_um_synced_role").on("change",function(){
		$sync_button = jQuery("#_um_button_sync_update_button");
		var url = $sync_button.attr("href");
		
		if ( ! getParameterByName('wp_role', url) ) {
			console.log("wp_role is not set");
		}

		var um_role = getParameterByName('um_role', url);
		var wp_role = jQuery(this).val();
		$sync_button.attr("href", window.location.href+'&um_adm_action=mass_role_sync&um_role='+um_role+'&wp_role='+wp_role );

	});



});

