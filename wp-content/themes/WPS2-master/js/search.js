/* -----------------------------------------
   SEARCH - /js/search.js
----------------------------------------- */

$(document).ready(function(){
	var serpLocation = "/serp.html"; // Location of your search engine results page (SERP)
	
	var prepareSearchForm = {
		init: function() {
			elemSearchForm = document.getElementById("local_form");
			if (elemSearchForm) {
				elemSearchForm.action = serpLocation;
				elemSearchForm.cof.value = "FORID:10";
			}
	
			if ((navigator.appVersion.indexOf("MSIE 7.") != -1)||(navigator.appVersion.indexOf("MSIE 8.") != -1)) { /* Fix Google Autocomplete and IE7/8 issue where default text doesn't clear */
				document.getElementById("search_local_textfield").value="";
			}
	
		}
	}
	
	prepareSearchForm.init();
});