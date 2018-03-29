jQuery(document).ready(function(){
	if(typeof(performance) !== 'undefined'){
		var timing = {
			fetchStart: false,
			domainLookupStart: false,
			domainLookupEnd: false,
			connectStart: false,
			connectEnd: false,
			requestStart: false,
			responseStart: false,
			responseEnd: false
		};
		for(var k in timing){
			timing[k] = performance.timing[k];
		}
		timing['domReady'] = new Date().getTime();
		jQuery(window).load(function(){
			timing['URL'] = document.URL;
			timing['loaded'] = new Date().getTime();
			var fields = ['fetchStart', 'domainLookupStart', 'domainLookupEnd', 'connectStart', 'connectEnd', 'requestStart', 'responseStart', 'responseEnd', 'domReady', 'loaded'];
			for(var i = fields.length - 1; i >= 1; i--){
				timing[fields[i]] -= timing[fields[i - 1]];
			}
			timing['fetchStart'] = 0;
			timing['action'] = 'wordfence_perfLog';
			jQuery.ajax({
				type: 'POST',
				url: wordfenceAjaxURL,
				dataType: 'json',
				data: timing,
				success: function(json){},
				error: function(){}
				});
		});
	}
});
