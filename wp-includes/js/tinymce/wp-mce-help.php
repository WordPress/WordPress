<?php
/**
 * @package TinyMCE
 * @author Moxiecode
 * @copyright Copyright © 2005-2006, Moxiecode Systems AB, All rights reserved.
 */

/** @ignore */
require_once('../../../wp-load.php');
header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php _e('Rich Editor Help') ?></title>
<script type="text/javascript" src="tiny_mce_popup.js?ver=342"></script>
<?php
wp_admin_css( 'global', true );
wp_admin_css( 'wp-admin', true );
?>
<style type="text/css">
	#wphead {
		font-size: 80%;
		border-top: 0;
		color: #555;
		background-color: #f1f1f1;
	}
	#wphead h1 {
		font-size: 24px;
		color: #555;
		margin: 0;
		padding: 10px;
	}
	#tabs {
		padding: 15px 15px 3px;
		background-color: #f1f1f1;
		border-bottom: 1px solid #dfdfdf;
	}
	#tabs li {
		display: inline;
	}
	#tabs a.current {
		background-color: #fff;
		border-color: #dfdfdf;
		border-bottom-color: #fff;
		color: #d54e21;
	}
	#tabs a {
		color: #2583AD;
		padding: 6px;
		border-width: 1px 1px 0;
		border-style: solid solid none;
		border-color: #f1f1f1;
		text-decoration: none;
	}
	#tabs a:hover {
		color: #d54e21;
	}
	.wrap h2 {
		border-bottom-color: #dfdfdf;
		color: #555;
		margin: 5px 0;
		padding: 0;
		font-size: 18px;
	}
	#user_info {
		right: 5%;
		top: 5px;
	}
	h3 {
		font-size: 1.1em;
		margin-top: 10px;
		margin-bottom: 0px;
	}
	#flipper {
		margin: 0;
		padding: 5px 20px 10px;
		background-color: #fff;
		border-left: 1px solid #dfdfdf;
		border-bottom: 1px solid #dfdfdf;
	}
	* html {
        overflow-x: hidden;
        overflow-y: scroll;
    }
	#flipper div p {
		margin-top: 0.4em;
		margin-bottom: 0.8em;
		text-align: justify;
	}
	th {
		text-align: center;
	}
	.top th {
		text-decoration: underline;
	}
	.top .key {
		text-align: center;
		width: 5em;
	}
	.top .action {
		text-align: left;
	}
	.align {
		border-left: 3px double #333;
		border-right: 3px double #333;
	}
	.keys {
		margin-bottom: 15px;
	}
	.keys p {
		display: inline-block;
		margin: 0px;
		padding: 0px;
	}
	.keys .left { text-align: left; }
	.keys .center { text-align: center; }
	.keys .right { text-align: right; }
	td b {
		font-family: "Times New Roman" Times serif;
	}
	#buttoncontainer {
		text-align: center;
		margin-bottom: 20px;
	}
	#buttoncontainer a, #buttoncontainer a:hover {
		border-bottom: 0px;
	}

	.mac,
	.macos .win {
		display: none;
	}

	.macos span.mac {
		display: inline;
	}

	.macwebkit tr.mac {
		display: table-row;
	}
	
</style>
<?php if ( is_rtl() ) : ?>
<style type="text/css">
	#wphead, #tabs {
		padding-left: auto;
		padding-right: 15px;
	}
	#flipper {
		margin: 5px 0 3px 10px;
	}
	.keys .left, .top, .action { text-align: right; }
	.keys .right { text-align: left; }
	td b { font-family: Tahoma, "Times New Roman", Times, serif }
</style>
<?php endif; ?>
<script type="text/javascript">
	function d(id) { return document.getElementById(id); }

	function flipTab(n) {
		for (i=1;i<=4;i++) {
			c = d('content'+i.toString());
			t = d('tab'+i.toString());
			if ( n == i ) {
				c.className = '';
				t.className = 'current';
			} else {
				c.className = 'hidden';
				t.className = '';
			}
		}
	}

    tinyMCEPopup.onInit.add(function() {
        var win = tinyMCEPopup.getWin();

		document.getElementById('version').innerHTML = tinymce.majorVersion + "." + tinymce.minorVersion;
        document.getElementById('date').innerHTML = tinymce.releaseDate;
 
		if ( win.fullscreen && win.fullscreen.settings.visible ) {
			d('content1').className = 'hidden';
			d('tabs').className = 'hidden';
			d('content3').className = 'dfw';
		}

		if ( tinymce.isMac )
			document.body.className = 'macos';
		
		if ( tinymce.isMac && tinymce.isWebKit )
			document.body.className = 'macos macwebkit';

    });
</script>
</head>
<body>

<ul id="tabs">
	<li><a id="tab1" href="javascript:flipTab(1)" title="<?php _e('Basics of Rich Editing') ?>" accesskey="1" tabindex="1" class="current"><?php _e('Basics') ?></a></li>
	<li><a id="tab2" href="javascript:flipTab(2)" title="<?php _e('Advanced use of the Rich Editor') ?>" accesskey="2" tabindex="2"><?php _e('Advanced') ?></a></li>
	<li><a id="tab3" href="javascript:flipTab(3)" title="<?php _e('Hotkeys') ?>" accesskey="3" tabindex="3"><?php _e('Hotkeys') ?></a></li>
	<li><a id="tab4" href="javascript:flipTab(4)" title="<?php _e('About the software') ?>" accesskey="4" tabindex="4"><?php _e('About') ?></a></li>
</ul>

<div id="flipper" class="wrap">

<div id="content1">
	<h2><?php _e('Rich Editing Basics') ?></h2>
	<p><?php _e('<em>Rich editing</em>, also called WYSIWYG for What You See Is What You Get, means your text is formatted as you type. The rich editor creates HTML code behind the scenes while you concentrate on writing. Font styles, links and images all appear approximately as they will on the internet.') ?></p>
	<p><?php _e('WordPress includes a rich HTML editor that works well in all major web browsers used today. However editing HTML is not the same as typing text. Each web page has two major components: the structure, which is the actual HTML code and is produced by the editor as you type, and the display, that is applied to it by the currently selected WordPress theme and is defined in style.css. WordPress is producing valid XHTML 1.0 which means that inserting multiple line breaks (BR tags) after a paragraph would not produce white space on the web page. The BR tags will be removed as invalid by the internal HTML correcting functions.') ?></p>
	<p><?php _e('While using the editor, most basic keyboard shortcuts work like in any other text editor. For example: Shift+Enter inserts line break, Ctrl+C = copy, Ctrl+X = cut, Ctrl+Z = undo, Ctrl+Y = redo, Ctrl+A = select all, etc. (on Mac use the Command key instead of Ctrl). See the Hotkeys tab for all available keyboard shortcuts.') ?></p>
    <p><?php _e('If you do not like the way the rich editor works, you may turn it off from Your Profile submenu, under Users in the admin menu.') ?></p>
</div>

<div id="content2" class="hidden">
	<h2><?php _e('Advanced Rich Editing') ?></h2>
	<h3><?php _e('Images and Attachments') ?></h3>
	<p><?php _e('There is a button in the editor toolbar for inserting images that are already hosted somewhere on the internet. If you have a URL for an image, click this button and enter the URL in the box which appears.') ?></p>
	<p><?php _e('If you need to upload an image or another media file from your computer, you can use the Media Library buttons above the editor. The media library will attempt to create a thumbnail-sized copy from each uploaded image. To insert your image into the post, first click on the thumbnail to reveal a menu of options. When you have selected the options you like, click "Send to Editor" and your image or file will appear in the post you are editing. If you are inserting a movie, there are additional options in the "Media" dialog that can be opened from the second toolbar row.') ?></p>
	<h3><?php _e('HTML in the Rich Editor') ?></h3>
	<p><?php _e('Any HTML entered directly into the rich editor will show up as text when the post is viewed. What you see is what you get. When you want to include HTML elements that cannot be generated with the toolbar buttons, you must enter it by hand in the HTML editor. Examples are tables and &lt;code&gt;. To do this, click the HTML tab and edit the code, then switch back to Visual mode. If the code is valid and understood by the editor, you should see it rendered immediately.') ?></p>
	<h3><?php _e('Pasting in the Rich Editor') ?></h3>
	<p><?php _e('When pasting content from another web page the results can be inconsistent and depend on your browser and on the web page you are pasting from. The editor tries to correct any invalid HTML code that was pasted, but for best results try using the HTML tab or one of the paste buttons that are on the second row. Alternatively try pasting paragraph by paragraph. In most browsers to select one paragraph at a time, triple-click on it.') ?></p>
	<p><?php _e('Pasting content from another application, like Word or Excel, is best done with the Paste from Word button on the second row, or in HTML mode.') ?></p>
</div>

<div id="content3" class="hidden">
	<h2><?php _e('Writing at Full Speed') ?></h2>
    <p><?php _e('Rather than reaching for your mouse to click on the toolbar, use these access keys. Windows and Linux use Ctrl + letter. Macintosh uses Command + letter.') ?></p>
	<p class="dfw-extra"><?php

	printf( __('In Distraction free writing mode use %1$s to make the editor wider, %2$s to make it narrower and %3$s to reset it to the original theme width.'),
		'<span class="win">Alt +</span><span class="mac">Ctrl +</span>',
		'<span class="win">Alt -</span><span class="mac">Ctrl -</span>',
		'<span class="win">Alt 0</span><span class="mac">Ctrl 0</span>'
	);

	?></p>
	<table class="keys" width="100%" style="border: 0 none;">
		<tr class="top"><th class="key center"><?php _e('Letter') ?></th><th class="left"><?php _e('Action') ?></th><th class="key center"><?php _e('Letter') ?></th><th class="left"><?php _e('Action') ?></th></tr>
		<tr><th>c</th><td><?php _e('Copy') ?></td><th>v</th><td><?php _e('Paste') ?></td></tr>
		<tr><th>a</th><td><?php _e('Select all') ?></td><th>x</th><td><?php _e('Cut') ?></td></tr>
		<tr><th>z</th><td><?php _e('Undo') ?></td><th>y</th><td><?php _e('Redo') ?></td></tr>
		
		<tr class="win"><th>b</th><td><?php _e('Bold') ?></td><th>i</th><td><?php _e('Italic') ?></td></tr>
		<tr class="win"><th>u</th><td><?php _e('Underline') ?></td><th>1</th><td><?php _e('Heading 1') ?></td></tr>
		<tr class="win"><th>2</th><td><?php _e('Heading 2') ?></td><th>3</th><td><?php _e('Heading 3') ?></td></tr>
		<tr class="win"><th>4</th><td><?php _e('Heading 4') ?></td><th>5</th><td><?php _e('Heading 5') ?></td></tr>
		<tr class="win"><th>6</th><td><?php _e('Heading 6') ?></td><th>9</th><td><?php _e('Address') ?></td></tr>
	</table>

	<p><?php _e('The following shortcuts use different access keys: Alt + Shift + letter.') ?></p>
	<table class="keys" width="100%" style="border: 0 none;">
		<tr class="top"><th class="key center"><?php _e('Letter') ?></th><th class="left"><?php _e('Action') ?></th><th class="key center"><?php _e('Letter') ?></th><th class="left"><?php _e('Action') ?></th></tr>
		<tr class="mac"><th>b</th><td><?php _e('Bold') ?></td><th>i</th><td><?php _e('Italic') ?></td></tr>

		<tr><th>n</th><td><?php _e('Check Spelling') ?></td><th>l</th><td><?php _e('Align Left') ?></td></tr>
		<tr><th>j</th><td><?php _e('Justify Text') ?></td><th>c</th><td><?php _e('Align Center') ?></td></tr>
		<tr><th>d</th><td><span style="text-decoration: line-through;"><?php _e('Strikethrough') ?></span></td><th>r</th><td><?php _e('Align Right') ?></td></tr>
		<tr><th>u</th><td><strong>&bull;</strong> <?php _e('List') ?></td><th>a</th><td><?php _e('Insert link') ?></td></tr>
		<tr><th>o</th><td>1. <?php _e('List') ?></td><th>s</th><td><?php _e('Remove link') ?></td></tr>
		<tr><th>q</th><td><?php _e('Quote') ?></td><th>m</th><td><?php _e('Insert Image') ?></td></tr>
		<tr><th>g</th><td><?php _e('Full Screen') ?></td><th>t</th><td><?php _e('Insert More Tag') ?></td></tr>
		<tr><th>p</th><td><?php _e('Insert Page Break tag') ?></td><th>h</th><td><?php _e('Help') ?></td></tr>
		<tr><th>e</th><td colspan="3"><?php _e('Switch to HTML mode') ?></td></tr>
	</table>
</div>

<div id="content4" class="hidden">
	<h2><?php _e('About TinyMCE'); ?></h2>

    <p><?php _e('Version:'); ?> <span id="version"></span> (<span id="date"></span>)</p>
	<p><?php printf(__('TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor released as Open Source under %sLGPL</a>	by Moxiecode Systems AB. It has the ability to convert HTML TEXTAREA fields or other HTML elements to editor instances.'), '<a href="'.home_url('/wp-includes/js/tinymce/license.txt').'" target="_blank" title="'.esc_attr__('GNU Library General Public Licence').'">') ?></p>
	<p><?php _e('Copyright &copy; 2003-2011, <a href="http://www.moxiecode.com" target="_blank">Moxiecode Systems AB</a>, All rights reserved.') ?></p>
	<p><?php _e('For more information about this software visit the <a href="http://tinymce.moxiecode.com" target="_blank">TinyMCE website</a>.') ?></p>

	<div id="buttoncontainer">
		<a href="http://www.moxiecode.com" target="_blank"><img src="themes/advanced/img/gotmoxie.png" alt="<?php _e('Got Moxie?') ?>" style="border: 0" /></a>
	</div>

</div>
</div>

<div class="mceActionPanel">
	<div style="margin: 8px auto; text-align: center;padding-bottom: 10px;">
		<input type="button" id="cancel" name="cancel" value="<?php _e('Close'); ?>" title="<?php _e('Close'); ?>" onclick="tinyMCEPopup.close();" />
	</div>
</div>

</body>
</html>
