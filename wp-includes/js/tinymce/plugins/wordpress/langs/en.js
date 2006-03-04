// EN lang variables

if (navigator.userAgent.indexOf('Mac OS') != -1) {
// Mac OS browsers use Ctrl to hit accesskeys
	var metaKey = 'Ctrl';
}
else {
	var metaKey = 'Alt';
}

tinyMCE.addToLang('',{
wordpress_more_button : 'Split post with More tag (' + metaKey + '-t)',
wordpress_page_button : 'Split post with Page tag',
wordpress_more_alt : 'More...',
wordpress_page_alt : '...page...'
});
