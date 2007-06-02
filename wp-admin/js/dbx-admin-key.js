addLoadEvent( function() {var manager = new dbxManager( dbxL10n.manager );} );

addLoadEvent( function()
{
	//create new docking boxes group
	var meta = new dbxGroup(
		'grabit', 		// container ID [/-_a-zA-Z0-9/]
		'vertical', 		// orientation ['vertical'|'horizontal']
		'10', 			// drag threshold ['n' pixels]
		'no',			// restrict drag movement to container axis ['yes'|'no']
		'10', 			// animate re-ordering [frames per transition, or '0' for no effect]
		'yes', 			// include open/close toggle buttons ['yes'|'no']
		'closed', 		// default state ['open'|'closed']
		dbxL10n.open, 		// word for "open", as in "open this box"
		dbxL10n.close, 		// word for "close", as in "close this box"
		dbxL10n.moveMouse,	// sentence for "move this box" by mouse
		dbxL10n.toggleMouse,	// pattern-match sentence for "(open|close) this box" by mouse
		dbxL10n.moveKey,	// sentence for "move this box" by keyboard
		dbxL10n.toggleKey,	// pattern-match sentence-fragment for "(open|close) this box" by keyboard
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
		);

	// Boxes are closed by default. Open the Category box if the cookie isn't already set.
	var catdiv = document.getElementById('categorydiv');
	if ( catdiv ) {
		var button = catdiv.getElementsByTagName('A')[0];
		if ( dbx.cookiestate == null && /dbx\-toggle\-closed/.test(button.className) )
			meta.toggleBoxState(button, true);
	}

	var advanced = new dbxGroup(
		'advancedstuff',
		'vertical',
		'10',
		'yes',			// restrict drag movement to container axis ['yes'|'no']
		'10',
		'yes',
		'closed',
		dbxL10n.open,
		dbxL10n.close,
		dbxL10n.moveMouse,
		dbxL10n.toggleMouse,
		dbxL10n.moveKey,
		dbxL10n.toggleKey,
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
		);
});
