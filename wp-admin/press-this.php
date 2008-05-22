<?php
require_once('admin.php');

if ( ! current_user_can('publish_posts') ) wp_die( __( 'Cheatin&#8217; uh?' ));

if ( 'post' == $_REQUEST['action'] ) {
	check_admin_referer('press-this');
	$post_ID = press_it(); ?>
		<script>if(confirm("<?php _e('Your post is saved. Do you want to view the post?') ?>")) {window.opener.location.replace("<?php echo get_permalink($post_ID);?>");}window.close();</script>
<?php 
die;
}

function press_it() {
	$quick['post_status'] = 'publish';
	$quick['post_category'] = $_REQUEST['post_category'];
	$quick['tags_input'] = $_REQUEST['tags_input'];
	$quick['post_title'] = $_REQUEST['post_title'];

	$content = '';
	switch ( $_REQUEST['post_type'] ) {
		case 'regular':
			$content = $_REQUEST['content'];
			if ($_REQUEST['content2'])
				$content .= $_REQUEST['content2']; 
			break;

		case 'quote':
			$content = '<blockquote>' . $_REQUEST['content'];
			if ($_REQUEST['content2']) {
				$content .= '</blockquote>
				';
				$content = $content . $_REQUEST['content2']; 
			}
			break;

		case 'photo':
			if ($_REQUEST['photo_link'])
				$content = '<a href="' . $_REQUEST['photo_link'] . '" target="_new">';

			$content .= '<img src="' . $_REQUEST['photo_src'] . '" alt=""/>';

			if ($_REQUEST['photo_link'])
				$content .= '</a>
				';

			if ($_REQUEST['content'])
				$content = $content . "\n".$_REQUEST['content']; 

			break;
		case "video":
			list($garbage,$video_id) = split("v=", $_REQUEST['content']);
			$content = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' . $video_id . '"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $video_id . '" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
			if ($_REQUEST['content2'])
				$content .= "\n" . $_REQUEST['content2'];
			break;				
	}

	$quick['post_content'] = $content;

	$post_ID = wp_insert_post($quick, true);

	if ( is_wp_error($post_ID) )
		wp_die($wp_error);

	return $post_ID;
}

function tag_div() { ?>
	<p id="jaxtag"><label class="hidden" for="newtag"><?php _e('Tags'); ?></label><input type="text" name="tags_input" class="tags-input" id="tags-input" size="40" tabindex="3" value="<?php echo get_tags_to_edit( $post->ID ); ?>" /></p>
	<div id="tagchecklist"></div>
<?php 
}

function category_div() {
?>
<div id="categories">
	<div class="submitbox" id="submitpost">
		<div id="previewview">	<h2><?php _e('Categories') ?></h2></div>
		<div class="inside">
			<div id="categories-all">
				<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
					<?php wp_category_checklist() ?>
				</ul>
			</div>
		</div>
		<p class="submit">         
		<input type="submit" value="<?php _e('Publish') ?>" onclick="document.getElementById('photo_saving').style.display = '';"/>
		<img src="/images/bookmarklet_loader.gif" alt="" id="photo_saving" style="width:16px; height:16px; vertical-align:-4px; display:none;"/>
		</p>
	</div>	
<?php
}

function get_images_from_uri($uri) {

	$content = wp_remote_fopen($uri);
	if ( false === $content ) return '';

	$pattern = '/<img[^>]+src=[\'"]([^\'" >]+?)[\'" >]/is';
	preg_match_all($pattern, $content, $matches);
	if ( empty($matches[1]) ) return '';
	
	$sources = array();
	foreach ($matches[1] as $src) {
		if ( false !== strpos($src, '&') )
			continue;
		
		$host = parse_url($_GET['u']);
		
		if (strpos($src, 'http://') === false) {
			$src = 'http://'.str_replace('//','/', $host['host'].'/'.$src);
		}
		
		$sources[] = $src;
	}
	return "'" . implode("','", $sources) . "'";
}

function press_this_js_init() { ?>
    <?php if ( user_can_richedit() ) { 
		$language = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) );
		// Add TinyMCE languages
		@include_once( dirname(__FILE__).'/../wp-includes/js/tinymce/langs/wp-langs.php' );
		if ( isset($strings) ) echo $strings; ?>
			(function() {
				var base = tinymce.baseURL, sl = tinymce.ScriptLoader, ln = "<?php echo $language; ?>";

				sl.markDone(base + '/langs/' + ln + '.js');
				sl.markDone(base + '/themes/advanced/langs/' + ln + '.js');
				sl.markDone(base + '/themes/advanced/langs/' + ln + '_dlg.js');
			})();
			
			tinyMCE.init({
				mode: "textareas",
				editor_selector: "mceEditor",
				language : "<?php echo $language; ?>",
				width: "100%",
				theme : "advanced",
				theme_advanced_buttons1 : "bold,italic,underline,blockquote,separator,strikethrough,bullist,numlist,undo,redo,link,unlink",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				theme_advanced_resize_horizontal : false,
				skin : "wp_theme",
				dialog_type : "modal",
				relative_urls : false,
				remove_script_host : false,
				convert_urls : false,
				apply_source_formatting : false,
				remove_linebreaks : true,
				accessibility_focus : false,
				tab_focus : ":next",
				plugins : "safari,inlinepopups",
				entity_encoding : "raw"
			});
    <?php } ?>

    	jQuery('#tags-input').hide();

		tag_update_quickclicks();

		// add the quickadd form
		jQuery('#jaxtag').prepend('<span id="ajaxtag"><input type="text" name="newtag" id="newtag" class="form-input-tip" size="16" autocomplete="off" value="'+postL10n.addTag+'" /><input type="submit" class="button" id="tagadd" value="' + postL10n.add + '" tabindex="3" onclick="return false;" /><input type="hidden"/><input type="hidden"/><span class="howto">'+postL10n.separate+'</span></span>');
		
		jQuery('#tagadd').click( tag_flush_to_text );
		jQuery('#newtag').focus(function() {
			if ( this.value == postL10n.addTag )
				jQuery(this).val( '' ).removeClass( 'form-input-tip' );
		});
		jQuery('#newtag').blur(function() {
			if ( this.value == '' )
				jQuery(this).val( postL10n.addTag ).addClass( 'form-input-tip' );
		});

		// auto-save tags on post save/publish
		jQuery('#publish').click( tag_save_on_publish );
		jQuery('#save-post').click( tag_save_on_publish );
<?php
}

// Clean up the data being passed in
$title = wp_specialchars(stripslashes($_GET['t']));
$selection = wp_specialchars(stripslashes($_GET['s']));
$url = clean_url($_GET['u']);                          

if ( empty($_GET['tab']) ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php _e('Press This') ?></title>

	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
<?php
	wp_enqueue_script('jquery-ui-tabs');
	add_thickbox();
	wp_admin_css('press-this');
	wp_admin_css( 'colors' );
	wp_enqueue_script('post');

	do_action('admin_print_scripts'); 
	do_action('admin_print_styles'); 
	do_action('admin_head');
?>
	<script type="text/javascript">

	jQuery(document).ready(function() {
    <?php if ( preg_match("/youtube\.com\/watch/i", $url) ) { ?>
		jQuery('#container > ul').tabs({ selected: 3 });
	<?php } elseif ( preg_match("/flickr\.com/i", $url) ) { ?>
		jQuery('#container > ul').tabs({ selected: 1 });
	<?php } else { ?>
		jQuery('#container > ul').tabs();
	<?php } ?>
	});

	</script>
	

</head>
<body>
<div id="wphead">
<h1><span id="viewsite"><a href="<?php echo get_option('home'); ?>/"><?php _e('Visit:') ?> <?php bloginfo('name'); ?></a></span></h1>
</div>
	<div id="container">
		<ul>
			<li><a href="<?php echo clean_url(add_query_arg('tab', 'text', stripslashes($_SERVER['REQUEST_URI']))) ?>"><?php _e('Text') ?></a></li>
		 	<li><a href="<?php echo clean_url(add_query_arg('tab', 'photo', stripslashes($_SERVER['REQUEST_URI']))) ?>"><?php _e('Photo') ?></a></li>
			<li><a href="<?php echo clean_url(add_query_arg('tab', 'quote', stripslashes($_SERVER['REQUEST_URI']))) ?>"><?php _e('Quote') ?></a></li>
			<li><a href="<?php echo clean_url(add_query_arg('tab', 'video', stripslashes($_SERVER['REQUEST_URI']))) ?>"><?php _e('Video') ?></a></li>
		</ul>
		
	</div>
</body>
</html>
<?php exit; } elseif ( 'photo' == $_GET['tab'] ) { ?>
	<script type="text/javascript">
		<?php press_this_js_init(); ?>
			var last = null;
			function pick(img) {
				if (last) last.style.backgroundColor = '#f4f4f4';
				if (img) {
					document.getElementById('photo_src').value = img.src;
					img.style.backgroundColor = '#44f';
				}
				last = img;
				
				/*jQuery('.photolist').append('<h2><?php _e("Photo URL") ?></h2>' +
				'<div class="titlewrap">' + 
				'<a href="#" class="remove">remove <input name="photo_src" id="photo_src[]" value ="'+ img.src +'" class="text" onkeydown="pick(0);"/></a>' +
				'</div>');*/
				return false;
			}

			jQuery('.remove').click(function() {
				jQuery(this).remove;
				
			});
			jQuery(document).ready(function() {
				var img, img_tag, aspect, w, h, skip, i, strtoappend = "";
				var my_src = [<?php echo get_images_from_uri($url); ?>];

				for (i = 0; i < my_src.length; i++) {
 					img = new Image();
 					img.src = my_src[i];
 					img_attr = 'id="img' + i + '" onclick="pick(this);"';
 					skip = false;
					if (img.width && img.height) {
						if (img.width * img.height < 2500) skip = true;
						aspect = img.width / img.height;
						if (aspect > 1) {
							// Image is wide
							scale = 75 / img.width;
						} else {
							// Image is tall or square
							scale = 75 / img.height;
						}
						if (scale < 1) {
							w = parseInt(img.width * scale);
							h = parseInt(img.height * scale);
						} else {
							w = img.width;
							h = img.height;
						}
						img_attr += ' style="width: ' + w + 'px; height: ' + h + 'px;"';
					}
					if (!skip) {
						strtoappend += '<a href="' + img.src + '" title="" class="thickbox"><img src="' + img.src + '" ' + img_attr + '/></a>'
                	}
				}
				
				jQuery('#img_container').html(strtoappend);

				tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
			});
	</script>
			<form action="press-this.php?action=post" method="post" id="photo_form">
				<?php wp_nonce_field('press-this') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="photo"/>
				<div id="posting">
					
					<h2><?php _e('Title') ?></h2>
					<div class="titlewrap">
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>
					</div>
					
					<h2><?php _e('Photo URL') ?></h2>
					<div class="titlewrap">
					<input name="photo_src" id="photo_src" class="text" onkeydown="pick(0);"/>
					</div>
					
					<div class="photolist"></div>
					
					<h2><?php _e('Link Photo to following URL') ?></h2><?php _e('(leave blank to leave the photo unlinked)') ?>
					<div class="titlewrap">
					<input name="photo_link" id="photo_link" class="text" value="<?php echo attribute_escape($url);?>"/>
					</div>
					
					<small><?php _e('Click images to select:') ?></small>
					<div class="titlewrap">
					<div id="img_container">
					</div>
					</div>
					
					
					<h2><?php _e('Caption') ?></h2>
					<div class="editor-container">
						<textarea name="content" id="photo_post_two" style="" class="mceEditor"><?php echo $selection;?>
						&lt;a href="<?php echo attribute_escape($url);?>"&gt;<?php echo $title;?>&lt;/a&gt; </textarea>
					</div>

					<?php tag_div(); ?>
					
				</div>
					<?php category_div() ?>
			</form>
<?php
exit;
} elseif ( 'text' == $_GET['tab'] ) {
?>
		<script type="text/javascript">
		<?php press_this_js_init(); ?>
		</script>

		  <form action="press-this.php?action=post" method="post" id="regular_form">
		  		<?php wp_nonce_field('press-this') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="regular"/>
				<div id="posting">
					<h2><?php _e('Title') ?></h2>
					<div class="titlewrap">
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>
					</div>
					
				  	<h2><?php _e('Post') ?></h2>
					<div class="editor-container">
						<textarea name="content" id="regular_post_two" style="height:170px;width:100%;" class="mceEditor"><?php echo $selection;?><a href="<?php echo $url;?>"><?php echo $title;?></a></textarea>
					</div>        

					<?php tag_div(); ?>
       
				</div>
				<?php category_div() ?>
			 </form>
<?php
exit;
} elseif ( 'quote' == $_GET['tab'] ) {
?>
		<script type="text/javascript">
		<?php press_this_js_init(); ?>
		</script>

			<form action="press-this.php?action=post" method="post" id="quote_form">
				<?php wp_nonce_field('press-this') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="quote"/>
				<div id="posting">
					<h2><?php _e('Title') ?></h2>
					<div class="titlewrap">
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape(sprintf(__('Quote by %s'), $title)); ?>"/>
					</div>
					
					<h2><?php _e('Quote') ?></h2>
					<div class="editor-container">
						<textarea name="content" id="quote_post_one" style="height:130px;width:100%;" class="mceEditor"><?php echo $selection;?></textarea>
					</div>

					<h2><?php _e('Source <span class="optional">(optional)</span>') ?></h2>
					<div class="editor-container">
						<textarea name="content2" id="quote_post_two" style="height:130px;width:100%;" class="mceEditor"><cite>&lt;a href="<?php echo $url;?>"&gt;<?php echo $title;?>&lt;/a&gt;</cite></textarea>
					</div>

					<?php tag_div(); ?>
				</div>
				<?php category_div() ?>
			</form>
<?php
exit;
} elseif ( 'video' == $_GET['tab'] ) {
?>
		<script type="text/javascript">
		<?php press_this_js_init(); ?>
		</script>

			<form action="press-this.php?action=post" method="post" id="video_form">
				<?php wp_nonce_field('press-this') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="video"/>
				<div id="posting">
				
					<h2><?php _e('Video Title') ?></h2>
				<div class="titlewrap">	
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>
				</div>
					<?php 
					if ( preg_match("/youtube\.com\/watch/i", $url) ) {
						list($domain, $video_id) = split("v=", $url);
					?>
					<input type="hidden" name="content" value="<?php echo attribute_escape($url); ?>" />
					<img src="http://img.youtube.com/vi/<?php echo $video_id; ?>/default.jpg" align="right" style="border:solid 1px #aaa;" width="130" height="97"/>
					<?php } else { ?>
					
					<h2><?php _e('Embed Code') ?></h2>
					<textarea name="content" id="video_post_one" style="height:80px;width:100%;"></textarea>
					<?php } ?>

					<h2><?php _e('Caption <span class="optional">(optional)</span>') ?></h2>

					<div class="editor-container">
						<textarea name="content2" id="video_post_two" style="height:130px;width:100%;" class="mceEditor"><?php echo $selection; ?> &lt;a href="<?php echo $url; ?>"&gt;<?php echo $title;?>&lt;/a&gt;</textarea>
					</div>

					<?php tag_div(); ?>
				</div>
				<?php category_div() ?>
			</form>
<?php
exit;
}
?>			