// send html to the post editor
function send_to_editor(h) {
	var win = window.dialogArguments || opener || parent || top;

	tinyMCE = win.tinyMCE;
	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.getInstanceById('content') ) && !ed.isHidden() ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, h);
	} else
		win.edInsertContent(win.edCanvas, h);
}