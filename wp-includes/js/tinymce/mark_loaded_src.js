(function(){
	if ( 'undefined' == tinyMCEPreInit )
		return;

	var baseurl = tinyMCEPreInit.base, sl = tinymce.ScriptLoader, lang = tinyMCEPreInit.ref.language,
		theme = tinyMCEPreInit.ref.theme, plugins = tinyMCEPreInit.ref.plugins, suffix = tinyMCEPreInit.suffix;

	sl.markDone( baseurl+'/langs/'+lang+'.js' );
	sl.markDone( baseurl+'/themes/'+theme+'/editor_template'+suffix+'.js' );
	sl.markDone( baseurl+'/themes/'+theme+'/langs/'+lang+'.js' );
	sl.markDone( baseurl+'/themes/'+theme+'/langs/'+lang+'_dlg.js' );

	tinymce.each( plugins.split(','), function(plugin){
		if ( plugin && plugin.charAt(0) != '-' ) {
			sl.markDone( baseurl+'/plugins/'+plugin+'/editor_plugin'+suffix+'.js' );
			sl.markDone( baseurl+'/plugins/'+plugin+'/langs/'+lang+'.js' );
			sl.markDone( baseurl+'/plugins/'+plugin+'/langs/'+lang+'_dlg.js' )
		}
	});
})();
