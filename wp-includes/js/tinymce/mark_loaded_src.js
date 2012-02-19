(function(undefined){
	if ( undefined === tinyMCEPreInit )
		return;

	var t = tinyMCEPreInit, baseurl = t.base, markDone = tinymce.ScriptLoader.markDone, lang = t.ref.language,
		theme = t.ref.theme, plugins = t.ref.plugins, suffix = t.suffix;

	markDone( baseurl+'/langs/'+lang+'.js' );
	markDone( baseurl+'/themes/'+theme+'/editor_template'+suffix+'.js' );
	markDone( baseurl+'/themes/'+theme+'/langs/'+lang+'.js' );
	markDone( baseurl+'/themes/'+theme+'/langs/'+lang+'_dlg.js' );

	tinymce.each( plugins.split(','), function(plugin){
		if ( plugin && plugin.charAt(0) != '-' ) {
			markDone( baseurl+'/plugins/'+plugin+'/editor_plugin'+suffix+'.js' );
			markDone( baseurl+'/plugins/'+plugin+'/langs/'+lang+'.js' );
			markDone( baseurl+'/plugins/'+plugin+'/langs/'+lang+'_dlg.js' )
		}
	});
})();
