// send html to the post editor
function send_to_editor(h) {
	var win = window.opener ? window.opener : window.dialogArguments;
	if ( !win )
		win = top;
	tinyMCE = win.tinyMCE;
	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.getInstanceById('content') ) && !ed.isHidden() ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, h);
	} else
		win.edInsertContent(win.edCanvas, h);
}