// EN lang variables

if (navigator.userAgent.indexOf('Mac OS') != -1) {
// Mac OS browsers use Ctrl to hit accesskeys
	var metaKey = 'Ctrl';
}
else {
	var metaKey = 'Alt';
}

tinyMCE.addToLang('',{
wordpress_more_button : 'Split post with More tag (' + metaKey + '+t)',
wordpress_page_button : 'Split post with Page tag',
wordpress_adv_button : 'Show/Hide Advanced Toolbar (' + metaKey + '+b)',
wordpress_more_alt : 'More...',
wordpress_page_alt : '...page...',
help_button_title : 'Help (' + metaKey + '+h)',
bold_desc : 'Bold (Ctrl+B)',
italic_desc : 'Italic (Ctrl+I)',
underline_desc : 'Underline (Ctrl+U)',
link_desc : 'Insert/edit link (' + metaKey + '+a)',
unlink_desc : 'Unlink (' + metaKey + '+s)',
image_desc : 'Insert/edit image (' + metaKey + '+m)',
striketrough_desc : 'Strikethrough (' + metaKey + '+k)',
justifyleft_desc : 'Align left (' + metaKey + '+f)',
justifycenter_desc : 'Align center (' + metaKey + '+c)',
justifyright_desc : 'Align right (' + metaKey + '+r)',
justifyfull_desc : 'Align full (' + metaKey + '+j)',
bullist_desc : 'Unordered list (' + metaKey + '+l)',
numlist_desc : 'Ordered list (' + metaKey + '+o)',
outdent_desc : 'Outdent (' + metaKey + '+w)',
indent_desc : 'Indent List/Blockquote (' + metaKey + '+q)'
});
