<?php
require_once('admin.php');

if ( ! current_user_can('publish_posts') ) wp_die( __( 'Cheatin&#8217; uh?' )); ?>

<?php 
function press_it() {
		$quick['post_status'] = 'publish';
		$quick['post_category'] = $_REQUEST['post_category'];
		$quick['tags_input'] = $_REQUEST['tags_input'];
		$quick['post_title'] = $_REQUEST['post_title'];
		$quick['post_content'] = '';
		
		// insert the post with nothing in it, to get an ID
		$post_ID = wp_insert_post($quick, true);
		
		$content = '';
		switch ( $_REQUEST['post_type'] ) {
			case 'text':
				$content .= $_REQUEST['content'];

			case 'quote':
				$content .= $_REQUEST['content'];
				break;

			case 'photo':
				if ($_REQUEST['photo_link'])
					$content .= '<a href="' . $_REQUEST['photo_link'] . '">';
					
					$content .= media_sideload_image($_REQUEST['photo_src'], $post_ID);

				if ($_REQUEST['photo_link'])
					$content .= '</a>';

				if ($_REQUEST['content'])
					$content .= $content . "\n\n".$_REQUEST['content']; 

				break;
			case "video":
				if($_REQUEST['embed_code']) 
					$content .= $_REQUEST['embed_code']."\n\n";
				$content .= $_REQUEST['content'];
				break;	
		}

	$quick['post_content'] = $content;

	if ( is_wp_error($post_ID) ) {
		wp_die($id);
		wp_delete_post($post_ID);
	} else {
		$quick['ID'] = $post_ID;
		wp_update_post($quick);
	}
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
			<img src="images/loading.gif" alt="" id="photo_saving" style="width:16px; height:16px; vertical-align:-4px; display:none;"/>
			</p>
		</div>	
	<?php
	}

// For posts submitted
if ( 'post' == $_REQUEST['action'] ) { 
	check_admin_referer('press-this'); $post_ID = press_it(); ?>
		<script>if(confirm("<?php _e('Your post is saved. Do you want to view the post?') ?>")){window.opener.location.replace("<?php echo get_permalink($post_ID);?>");}window.close();</script>
	<?php die;
}

// Ajax Requests
$url = $_REQUEST['url'];
$selection = $_REQUEST['selection'];

if($_REQUEST['ajax'] == 'video') { ?>
	<h2 id="embededcode"><label for="embed_code"><?php _e('Embed Code') ?></label></h2>
	<div class="titlewrap" >
		<textarea name="embed_code" id="embed_code" rows="8" cols="40"><?php echo $selection; ?></textarea>
	</div>
<?php die;
}

if($_REQUEST['ajax'] == 'photo_images') {
	function get_images_from_uri($uri) {
		$content = wp_remote_fopen($uri);
		$host = parse_url($uri);
		if ( false === $content ) return '';

		$pattern = '/<img[^>]+src=[\'"]([^\'" >]+?)[\'" >]/is';
		preg_match_all($pattern, $content, $matches);
		if ( empty($matches[1]) ) return '';
    
		$sources = array();

		foreach ($matches[1] as $src) {
			if ( false !== strpos($src, '&') ) continue;
				if(strpos($src, 'http') === false) {
					if(strpos($src, '../') === false && strpos($src, './') === false) {
						$src = 'http://'.str_replace('//','/', $host['host'].'/'.$src);
					} else {
						$src = 'http://'.str_replace('//','/', $host['host'].'/'.$host['path'].'/'.$src);
					}
				}
				$sources[] = $src;
		}
		return "'" . implode("','", $sources) . "'";
	}  
		
	echo 'new Array('.get_images_from_uri($url).')'; 
die;		
}

if($_REQUEST['ajax'] == 'photo_js') { ?>
	
			var last = null
			function pick(img) {

				if (last) last.style.backgroundColor = '#f4f4f4';
				if (img) {
					jQuery('#photo_src').val(img.src);
					img.style.backgroundColor = '#44f';
				}
				last = img;
				
				/*noel's code to select more than one image....
				jQuery('.photolist').append('<h2><?php _e("Photo URL") ?></h2>' +
				'<div class="titlewrap">' + 
				'<a href="#" class="remove">remove <input name="photo_src" id="photo_src[]" value ="'+ img.src +'" class="text" onkeydown="pick(0);"/></a>' +
				'</div>');*/

				return false;
			}

			jQuery('.remove').click(function() {
				jQuery(this).remove;
			});


			var my_src, img, img_tag, aspect, w, h, skip, i, strtoappend = "";
			
			var my_src =eval(
			jQuery.ajax({
			   	type: "GET",
			   	url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
				cache : false,
				async : false,
			   	data: "ajax=photo_images&url=<?php echo $url?>",
			dataType : "script"
			}).responseText);

			for (i = 0; i < my_src.length; i++) {
				img = new Image();
				img.src = my_src[i];
				img_attr = 'id="img' + i + '" onclick="pick(this);"';
				skip = false;

				if (img.width && img.height) {
					if (img.width * img.height < 2500) skip = true;
					aspect = img.width / img.height;
					if (aspect > 1) { // Image is wide
						scale = 75 / img.width;
					} else { // Image is tall or square
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

				if (!skip) strtoappend += '<a href="' + img.src + '" title="" class="thickbox"><img src="' + img.src + '" ' + img_attr + '/></a>';

			}

			jQuery('#img_container').html(strtoappend);

			tb_init('a.thickbox, area.thickbox, input.thickbox'); //pass where to apply thickbox

<?php die; }

if($_REQUEST['ajax'] == 'photo') { ?>
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
			<div id="img_container">Loading Images...</div>
		</div>
<?php die; }

// Clean up the data being passed in
$title = wp_specialchars(stripslashes($_GET['t']));
$selection = trim(wp_specialchars(str_replace("\n", ' ',stripslashes($_GET['s']))));
$url = clean_url($_GET['u']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php _e('Press This') ?></title>

	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
<?php
	add_thickbox();
	wp_enqueue_style('press-this');
	wp_enqueue_style( 'colors' );
	wp_enqueue_script('post');

	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
?>
	<script type="text/javascript">
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
				extended_valid_elements : "object[width|height],param[name|value],embed[src|type|wmode|width|height], a[name|href|target|title|onclick], img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name], hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
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
				entities : "38,amp,60,lt,62,gt"
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
		
	function set_menu(type) {
		jQuery('#text_button').removeClass('ui-tabs-selected');
		jQuery('#menu li').removeClass('ui-tabs-selected');
		jQuery('#' + type + '_button').addClass('ui-tabs-selected');
		jQuery("#post_type").val(type);
	}
	function set_editor(text) {
		if(tinyMCE.activeEditor) tinyMCE.activeEditor.setContent('');
		if(tinyMCE.activeEditor) tinyMCE.execCommand('mceInsertContent' ,false, text);
	}
	function set_title(title) { jQuery("#content_type").text(title); }
	function reset_height() {
		tinyMCE.height = '170px';
	}
	function show(tab_name) {
		jQuery('body').removeClass('video_split');
		jQuery('#extra_fields').hide();
		switch(tab_name) {
			case 'text' :
				reset_height();
				jQuery('.editor-container').show();
				jQuery('#content_type').show();
				set_menu('text');
				set_title('<?php _e('Text') ?>');
				set_editor('<?php echo $selection; ?>');
				return false;
			break;
			case 'quote' :
				reset_height();
				jQuery('.editor-container').show();
				jQuery('#content_type').show();
				set_menu('quote');
				set_title('<?php _e('Quote') ?>');
				set_editor('<blockquote><p><?php echo $selection; ?> </p><p><cite><a href="<?php echo $url; ?>"><?php echo $title; ?></a></cite> </p></blockquote>');

				return false;
			break;
			case 'video' :
				reset_height();
				jQuery('.editor-container').show();
				jQuery('#content_type').show();
				set_menu('video');
				set_title('<?php _e('Caption') ?>');
				
				jQuery('#extra_fields').show();
				jQuery('body').addClass('video_split');
			
				
				jQuery('#extra_fields').load('<?php echo clean_url($_SERVER['PHP_SELF']); ?>', { ajax: 'video', selection: '<?php echo attribute_escape($selection); ?>'}, function() {
					
					<?php 
					if ( preg_match("/youtube\.com\/watch/i", $url) ) {
					list($domain, $video_id) = split("v=", $url);
					$content = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' . $video_id . '"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $video_id . '" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>'; ?>
					
					<?php } elseif ( preg_match("/vimeo\.com\/[0-9]+/i", $url) ) {
					
					list($domain, $video_id) = split(".com/", $url);
					$content = '<object width="400" height="225"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />	<embed src="http://www.vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="400" height="225"></embed></object>';
					
					if(trim($selection) == '') $selection = '<a href="http://www.vimeo.com/' . $video_id . '?pg=embed&sec=' . $video_id . '">' . $title . '</a> on <a href="http://vimeo.com?pg=embed&sec=' . $video_id . '">Vimeo</a>';
					}else {
						$content = $selection;
					} ?>
					jQuery('#embed_code').prepend('<?php echo htmlentities($content); ?>');
					set_editor('<?php echo $title; ?>');
					
				});

				
				return false;
			break;
			
			case 'photo' :
				reset_height();
				set_menu('photo');
				set_title('Caption');
				set_editor('<a href="<?php echo $url; ?>"><?php echo $title; ?></a>');
				
				jQuery('#extra_fields').show();
				jQuery('#extra_fields').load('<?php echo clean_url($_SERVER['PHP_SELF']).'/?ajax=photo&url='.attribute_escape($url); ?>');
				jQuery('#extra_fields').prepend('<h2><img src="images/loading.gif" alt="" /> Loading...</h2>');
				jQuery.ajax({
					type: "GET",
					cache : false,
					url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
					data: "ajax=photo_js&url=<?php echo $url?>",
					dataType : "script",
					success : function() {
					}
				});
				return false;
			break;

		}
	
	}
	
	jQuery(document).ready(function() {
    	jQuery('#menu li').click(function (){ 
			tab_name = this.id.split('_');
			tab_name = tab_name[0];
			show(tab_name);
		});
		
		<?php if ( preg_match("/youtube\.com\/watch/i", $url) ) { ?>
			show('video');
		<?php } elseif ( preg_match("/vimeo\.com\/[0-9]+/i", $url) ) { ?>
			show('video');
			<?php  } elseif ( preg_match("/flickr\.com/i", $url) ) { ?>
			show('photo');
		<?php } ?>
	});
</script>
</head>
<body class="press-this">
<div id="wphead">
<h1><span id="viewsite"><a href="<?php echo get_option('home'); ?>/"><?php _e('Visit:') ?> <?php bloginfo('name'); ?></a></span></h1>
</div>

		<ul id="menu" class="ui-tabs-nav">
			<li id="text_button" class="ui-tabs-selected"><a href="#"><?php _e('Text') ?></a></li>
		 	<li id="photo_button"><a href="#"><?php _e('Photo') ?></a></li>
			<li id="quote_button"><a href="#"><?php _e('Quote') ?></a></li>
			<li id="video_button"><a href="#"><?php _e('Video') ?></a></li>
		</ul>

			<form action="press-this.php?action=post" method="post">

				<?php wp_nonce_field('press-this') ?>
				<input type="hidden" name="post_type" id="post_type" value="text"/>
				<div id="posting">
					
					<h2 id="title"><label for="post_title"><?php _e('Title') ?></label></h2>
					<div class="titlewrap">
						<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>
					</div>

					<div id="extra_fields" style="display: none"></div>
					<div class="editor_area">
					<h2 id="content_type"><label for="content"><?php _e('Post') ?></label></h2>
					<div class="editor-container">
						<textarea name="content" id="content" style="width:100%;" class="mceEditor">
						<?php echo $selection; ?>
						</textarea>
					</div>
					</div>
					<?php tag_div(); ?>
				</div>
				<?php category_div(); ?>
			</form>		
					
</body>
</html>