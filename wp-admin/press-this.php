<?php
/**
 * Press This Display and Handler.
 *
 * @package WordPress
 * @subpackage Press_This
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('publish_posts') ) wp_die( __( 'Cheatin&#8217; uh?' ) );

/**
 * Replace forward slash with backslash and slash.
 *
 * @package WordPress
 * @subpackage Press_This
 * @since 2.6.0
 *
 * @param string $string
 * @return string
 */
function preg_quote2($string) {
	return str_replace('/', '\/', preg_quote($string));
}

/**
 * Convert characters.
 *
 * @package WordPress
 * @subpackage Press_This
 * @since 2.6.0
 *
 * @param string $text
 * @return string
 */
function aposfix($text) {
	$translation_table[chr(34)] = '&quot;';
	$translation_table[chr(38)] = '&';
	$translation_table[chr(39)] = '&apos;';
	return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;" , strtr($text, $translation_table));
}

/**
 * Press It form handler.
 *
 * @package WordPress
 * @subpackage Press_This
 * @since 2.6.0
 *
 * @return int Post ID
 */
function press_it() {
	// define some basic variables
	$quick['post_status'] = 'draft'; // set as draft first
	$quick['post_category'] = $_REQUEST['post_category'];
	$quick['tags_input'] = $_REQUEST['tags_input'];
	$quick['post_title'] = $_REQUEST['title'];
	$quick['post_content'] = '';

	// insert the post with nothing in it, to get an ID
	$post_ID = wp_insert_post($quick, true);
	$content = $_REQUEST['content'];

	if($_REQUEST['photo_src'])
		foreach( (array) $_REQUEST['photo_src'] as $key => $image)
			// see if files exist in content - we don't want to upload non-used selected files.
			if( strpos($_REQUEST['content'], $image) !== false ) {
				$upload = media_sideload_image($image, $post_ID, $_REQUEST['photo_description'][$key]);

				// Replace the POSTED content <img> with correct uploaded ones.
				// escape quote for matching
				$quoted = preg_quote2($image);
				if( !is_wp_error($upload) ) $content = preg_replace('/<img ([^>]*)src=(\"|\')'.$quoted.'(\2)([^>\/]*)\/*>/is', $upload, $content);
			}

	// set the post_content and status
	$quick['post_status'] = isset($_REQUEST['publish']) ? 'publish' : 'draft';
	$quick['post_content'] = $content;
	// error handling for $post
	if ( is_wp_error($post_ID)) {
		wp_die($id);
		wp_delete_post($post_ID);
	// error handling for media_sideload
	} elseif ( is_wp_error($upload)) {
		wp_die($upload);
		wp_delete_post($post_ID);
	} else {
		$quick['ID'] = $post_ID;
		wp_update_post($quick);
	}
	return $post_ID;
}

// For submitted posts.
if ( 'post' == $_REQUEST['action'] ) {
	check_admin_referer('press-this');
	$post_ID = press_it();
	$posted =  $post_ID;
}

// Set Variables
$title = wp_specialchars(aposfix(stripslashes($_GET['t'])));
$selection = trim( aposfix( stripslashes($_GET['s']) ) );
if ( ! empty($selection) ) {
	$selection = preg_replace('/(\r?\n|\r)/', '</p><p>', $selection);
	$selection = '<p>'.str_replace('<p></p>', '', $selection).'</p>';
}
$url = clean_url($_GET['u']);
$image = $_GET['i'];

if($_REQUEST['ajax']) {
switch ($_REQUEST['ajax']) {
	case 'video': ?>
		<script type="text/javascript" charset="utf-8">
			jQuery('.select').click(function() {
				append_editor(jQuery('#embed-code').val());
				jQuery('#extra_fields').hide();
				jQuery('#extra_fields').html('');
			});
			jQuery('.close').click(function() {
				jQuery('#extra_fields').hide();
				jQuery('#extra_fields').html('');
			});
		</script>
		<div class="postbox">
		<h2><label for="embed-code"><?php _e('Embed Code') ?></label></h2>
		<div class="inside">
			<textarea name="embed-code" id="embed-code" rows="8" cols="40"><?php echo format_to_edit($selection, true); ?></textarea>
			<p id="options"><a href="#" class="select button"><?php _e('Insert Video'); ?></a> <a href="#" class="close button"><?php _e('Cancel'); ?></a></p>
		</div>
		</div>
		<?php break;

	case 'photo_thickbox': ?>
		<script type="text/javascript" charset="utf-8">
			jQuery('.cancel').click(function() {
				tb_remove();
			});
			jQuery('.select').click(function() {
				image_selector();
			});
		</script>
		<h3 class="tb"><label for="this_photo_description"><?php _e('Description') ?></label></h3>
		<div class="titlediv">
		<div class="titlewrap">
			<input id="this_photo_description" name="photo_description" class="tbtitle text" onkeypress="if(event.keyCode==13) image_selector();" value="<?php echo attribute_escape($title);?>"/>
		</div>
		</div>

		<p class="centered"><input type="hidden" name="this_photo" value="<?php echo attribute_escape($image); ?>" id="this_photo" />
			<a href="#" class="select"><img src="<?php echo clean_url($image); ?>" alt="<?php echo attribute_escape(__('Click to insert.')); ?>" title="<?php echo attribute_escape(__('Click to insert.')); ?>" /></a></p>

		<p id="options"><a href="#" class="select button"><?php _e('Insert Image'); ?></a> <a href="#" class="cancel button"><?php _e('Cancel'); ?></a></p>


		<?php break;

	case 'photo_thickbox_url': ?>
		<script type="text/javascript" charset="utf-8">
			jQuery('.cancel').click(function() {
				tb_remove();
			});

			jQuery('.select').click(function() {
				image_selector();
			});
		</script>
		<h3 class="tb"><label for="this_photo"><?php _e('URL') ?></label></h3>
		<div class="titlediv">
			<div class="titlewrap">
			<input id="this_photo" name="this_photo" class="tbtitle text" onkeypress="if(event.keyCode==13) image_selector();" />
			</div>
		</div>


		<h3 class="tb"><label for="photo_description"><?php _e('Description') ?></label></h3>
		<div id="titlediv">
			<div class="titlewrap">
			<input id="this_photo_description" name="photo_description" class="tbtitle text" onkeypress="if(event.keyCode==13) image_selector();" value="<?php echo attribute_escape($title);?>"/>
			</div>
		</div>

		<p id="options"><a href="#" class="select"><?php _e('Insert Image'); ?></a> | <a href="#" class="cancel"><?php _e('Cancel'); ?></a></p>
		<?php break;
	case 'photo_images':
		/**
		 * Retrieve all image URLs from given URI.
		 *
		 * @package WordPress
		 * @subpackage Press_This
		 * @since 2.6.0
		 *
		 * @param string $uri
		 * @return string
		 */
		function get_images_from_uri($uri) {
			if( preg_match('/\.(jpg|jpe|jpeg|png|gif)$/', $uri) && !strpos($uri,'blogger.com') )
				return "'".$uri."'";
			$content = wp_remote_fopen($uri);
			if ( false === $content )
				return '';
			$host = parse_url($uri);
			$pattern = '/<img ([^>]*)src=(\"|\')([^<>]+?\.(png|jpeg|jpg|jpe|gif))[^<>\'\"]*(\2)([^>\/]*)\/*>/is';
			preg_match_all($pattern, $content, $matches);
			if ( empty($matches[0]) )
				return '';
			$sources = array();
			foreach ($matches[3] as $src) {
				// if no http in url
				if(strpos($src, 'http') === false)
					// if it doesn't have a relative uri
					if( strpos($src, '../') === false && strpos($src, './') === false && strpos($src, '/') === 0)
						$src = 'http://'.str_replace('//','/', $host['host'].'/'.$src);
					else
						$src = 'http://'.str_replace('//','/', $host['host'].'/'.dirname($host['path']).'/'.$src);
				$sources[] = clean_url($src);
			}
			return "'" . implode("','", $sources) . "'";
		}
		$url = urldecode($url);
		$url = str_replace(' ', '%20', $url);
		echo 'new Array('.get_images_from_uri($url).')';

		break;

	case 'photo_js': ?>
		// gather images and load some default JS
		var last = null
		var img, img_tag, aspect, w, h, skip, i, strtoappend = "";
			var my_src = eval(
				jQuery.ajax({
			   		type: "GET",
			   		url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
					cache : false,
					async : false,
			   		data: "ajax=photo_images&u=<?php echo urlencode($url); ?>",
					dataType : "script"
				}).responseText
			);
			if(my_src.length == 0) {
				var my_src = eval(
				jQuery.ajax({
			   		type: "GET",
			   		url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
					cache : false,
					async : false,
			   		data: "ajax=photo_images&u=<?php echo urlencode($url); ?>",
					dataType : "script"
				}).responseText
				);
				if(my_src.length == 0) {
					strtoappend = '<?php _e('Unable to retrieve images or no images on page.'); ?>';
				}
			}

		for (i = 0; i < my_src.length; i++) {
			img = new Image();
			img.src = my_src[i];
			img_attr = 'id="img' + i + '"';
			skip = false;

			maybeappend = '<a href="?ajax=photo_thickbox&amp;i=' + encodeURIComponent(img.src) + '&amp;u=<?php echo urlencode($url); ?>&amp;height=400&amp;width=500" title="" class="thickbox"><img src="' + img.src + '" ' + img_attr + '/></a>';

			if (img.width && img.height) {
				if (img.width >= 30 && img.height >= 30) {
					aspect = img.width / img.height;
					scale = (aspect > 1) ? (71 / img.width) : (71 / img.height);

					w = img.width;
					h = img.height;

					if (scale < 1) {
						w = parseInt(img.width * scale);
						h = parseInt(img.height * scale);
					}
					img_attr += ' style="width: ' + w + 'px; height: ' + h + 'px;"';
					strtoappend += maybeappend;
				}
			} else {
				strtoappend += maybeappend;
			}
		}

		function pick(img, desc) {
			if (img) {
				if('object' == typeof jQuery('.photolist input') && jQuery('.photolist input').length != 0) length = jQuery('.photolist input').length;
				if(length == 0) length = 1;
				jQuery('.photolist').append('<input name="photo_src[' + length + ']" value="' + img +'" type="hidden"/>');
				jQuery('.photolist').append('<input name="photo_description[' + length + ']" value="' + desc +'" type="hidden"/>');
				insert_editor( "\n\n" + encodeURI('<p style="text-align: center;"><a href="<?php echo $url; ?>"><img src="' + img +'" alt="' + desc + '" /></a></p>'));
			}
			return false;
		}

		function image_selector() {
			tb_remove();
			desc = jQuery('#this_photo_description').val();
			src = jQuery('#this_photo').val();
			pick(src, desc);
			jQuery('#extra_fields').hide();
			jQuery('#extra_fields').html('');
			return false;
		}

		jQuery(document).ready(function() {
			jQuery('#extra_fields').html('<div class="postbox"><h2>Photo <small id="photo_directions">(<?php _e("click images to select") ?>)</small></h2><ul id="actions"><li><a href="#" id="photo_add_url" class="thickbox button"><?php _e("Add from URL") ?> +</a></li></ul><div class="inside"><div class="titlewrap"><div id="img_container"></div></div><p id="options"><a href="#" class="close button"><?php _e('Cancel'); ?></a><a href="#" class="refresh button"><?php _e('Refresh'); ?></a></p></div>');
			jQuery('.close').click(function() {
				jQuery('#extra_fields').hide();
				jQuery('#extra_fields').html('');
			});
			jQuery('.refresh').click(function() {
						show('photo');
					});
			jQuery('#img_container').html(strtoappend);
			jQuery('#photo_add_url').attr('href', '?ajax=photo_thickbox_url&height=200&width=500');
			tb_init('#extra_fields .thickbox');


		});
		<?php break;
}
die;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php _e('Press This') ?></title>

<?php
	add_thickbox();
	wp_enqueue_style('press-this');
	wp_enqueue_style('press-this-ie');
	wp_enqueue_style( 'colors' );
	wp_enqueue_script( 'post' );
	wp_enqueue_script('editor');

	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');

	if ( user_can_richedit() ) {
		add_filter( 'teeny_mce_before_init', create_function( '$a', '$a["height"] = "400"; $a["onpageload"] = ""; $a["mode"] = "textareas"; $a["editor_selector"] = "mceEditor"; return $a;' ) );
		wp_tiny_mce( true );
	}
?>
	<script type="text/javascript">
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
	jQuery('#save').click( tag_save_on_publish );
	function insert_plain_editor(text) {
		edCanvas = document.getElementById('content');
		edInsertContent(edCanvas, text);
	}
	function set_editor(text) {
		if ( '' == text || '<p></p>' == text ) text = '<p><br /></p>';
		if ( tinyMCE.activeEditor ) tinyMCE.execCommand('mceSetContent', false, text);
	}
	function insert_editor(text) {
		if ( '' != text && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden()) {
			tinyMCE.execCommand('mceInsertContent', false, '<p>' + decodeURI(tinymce.DOM.decode(text)) + '</p>', {format : 'raw'});
		} else {
			insert_plain_editor(decodeURI(text));
		}
	}
	function append_editor(text) {
		if ( '' != text && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden()) {
			tinyMCE.execCommand('mceSetContent', false, tinyMCE.activeEditor.getContent({format : 'raw'}) + '<p>' + text + '</p>');
			tinyMCE.execCommand('mceCleanup');
		} else {
			insert_plain_editor(text);
		}
	}

	function show(tab_name) {
		jQuery('#extra_fields').html('');
		jQuery('#extra_fields').show();
		switch(tab_name) {
			case 'video' :
				jQuery('#extra_fields').load('<?php echo clean_url($_SERVER['PHP_SELF']); ?>', { ajax: 'video', s: '<?php echo attribute_escape($selection); ?>'}, function() {
					<?php
					$content = '';
					if ( preg_match("/youtube\.com\/watch/i", $url) ) {
						list($domain, $video_id) = split("v=", $url);
						$content = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' . $video_id . '"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $video_id . '" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';

					} elseif ( preg_match("/vimeo\.com\/[0-9]+/i", $url) ) {
						list($domain, $video_id) = split(".com/", $url);
						$content = '<object width="400" height="225"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />	<embed src="http://www.vimeo.com/moogaloop.swf?clip_id=' . $video_id . '&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="400" height="225"></embed></object>';

						if ( trim($selection) == '' )
							$selection = '<p><a href="http://www.vimeo.com/' . $video_id . '?pg=embed&sec=' . $video_id . '">' . $title . '</a> on <a href="http://vimeo.com?pg=embed&sec=' . $video_id . '">Vimeo</a></p>';

					} elseif ( strpos( $selection, '<object' ) !== false ) {
						$content = $selection;
					}
					?>
					jQuery('#embed-code').prepend('<?php echo htmlentities($content); ?>');
				});
				return false;
				break;
			case 'photo' :
				jQuery('#extra_fields').before('<p id="waiting"><img src="images/loading.gif" alt="" /> <?php echo js_escape( __( 'Loading...' ) ); ?></p>');
				jQuery.ajax({
					type: "GET",
					cache : false,
					url: "<?php echo clean_url($_SERVER['PHP_SELF']); ?>",
					data: "ajax=photo_js&u=<?php echo urlencode($url)?>",
					dataType : "script",
					success : function() {
						jQuery('#waiting').remove();
					}
				});
				return false;
				break;
		}
	}
	jQuery(document).ready(function() {
		//resize screen
		window.resizeTo(720,570);
		// set button actions
    	jQuery('#photo_button').click(function() { show('photo'); return false; });
		jQuery('#video_button').click(function() { show('video'); return false; });
		// auto select
		<?php if ( preg_match("/youtube\.com\/watch/i", $url) ) { ?>
			show('video');
		<?php } elseif ( preg_match("/vimeo\.com\/[0-9]+/i", $url) ) { ?>
			show('video');
		<?php  } elseif ( preg_match("/flickr\.com/i", $url) ) { ?>
			show('photo');
		<?php } ?>
		jQuery('#title').unbind();
		jQuery('#publish, #save').click(function() { jQuery('#saving').css('display', 'inline'); });
	});
</script>
</head>
<body class="press-this">
<div id="wphead"></div>
<form action="press-this.php?action=post" method="post">
<div id="poststuff" class="metabox-holder">
	<div id="side-info-column">
		<div class="sleeve">
			<h1 id="viewsite"><a class="button" href="<?php echo get_option('home'); ?>/" target="_blank"><?php bloginfo('name'); ?> &rsaquo; <?php _e('Press This') ?></a></span></h1>

			<?php wp_nonce_field('press-this') ?>
			<input type="hidden" name="post_type" id="post_type" value="text"/>
			<input type="hidden" name="autosave" id="autosave" />
			<input type="hidden" id="original_post_status" name="original_post_status" value="draft" />
			<input type="hidden" id="prev_status" name="prev_status" value="draft" />

			<!-- This div holds the photo metadata -->
			<div class="photolist"></div>

			<div id="categorydiv" class="stuffbox">
				<h2><?php _e('Categories') ?></h2>
				<div class="inside">

					<div id="categories-all" class="ui-tabs-panel">
						<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
							<?php wp_category_checklist($post->ID, false, false, $popular_ids) ?>
						</ul>
					</div>

					<div id="category-adder" class="wp-hidden-children">
						<a id="category-add-toggle" href="#category-add" class="hide-if-no-js" tabindex="3"><?php _e( '+ Add New Category' ); ?></a>
						<p id="category-add" class="wp-hidden-child">
							<label class="hidden" for="newcat"><?php _e( 'Add New Category' ); ?></label><input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" tabindex="3" aria-required="true"/>
							<label class="hidden" for="newcat_parent"><?php _e('Parent category'); ?>:</label><?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => __('Parent category'), 'tab_index' => 3 ) ); ?>
							<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php _e( 'Add' ); ?>" tabindex="3" />
							<?php wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
							<span id="category-ajax-response"></span>
						</p>
					</div>
				</div>
			</div>

			<div class="stuffbox">
				<h2><?php _e('Tags') ?></h2>
				<div class="inside">

					<div id="jaxtag">
						<label class="hidden" for="newtag"><?php _e('Tags'); ?></label>
						<input type="text" name="tags_input" class="tags-input" id="tags-input" size="40" tabindex="3" value="<?php echo get_tags_to_edit( $post->ID ); ?>" />
					</div>
					<div id="tagchecklist"></div>
				</div>
			</div>
			<div id="submitdiv" class="postbox">
				<h2><?php _e('Publish') ?></h2>
				<div class="inside">
					<p>
						<input class="button" type="submit" name="draft" value="<?php _e('Save Draft') ?>" id="save" />
						<input class="button-primary" type="submit" name="publish" value="<?php _e('Publish') ?>" id="publish" />
						<img src="images/loading-publish.gif" alt="" id="saving" style="display:none;"/>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="posting">
		<?php if ( isset($posted) && intval($posted) ) { $post_ID = intval($posted); ?>
		<div id="message" class="updated fade"><p><strong><?php _e('Your post has been saved.'); ?></strong> <a onclick="window.opener.location.replace(this.href); window.close();" href="<?php echo get_permalink( $post_ID); ?>"><?php _e('View post'); ?></a> | <a href="<?php echo get_edit_post_link( $post_ID ); ?>" onclick="window.opener.location.replace(this.href); window.close();"><?php _e('Edit post'); ?></a> | <a href="#" onclick="window.close();"><?php _e('Close Window'); ?></a></p></div>
		<?php } ?>

		<div id="titlediv">
			<div class="titlewrap">
				<input name="title" id="title" class="text" value="<?php echo attribute_escape($title);?>"/>
			</div>
		</div>

		<div id="extra_fields" style="display: none"></div>

		<div class="postdivrich">
			<ul id="actions">
				<li id="photo_button">
					Add: <a title="<?php _e('Insert an Image'); ?>" href="#">
<img alt="<?php _e('Insert an Image'); ?>" src="images/media-button-image.gif"/></a>
				</li>
				<li id="video_button">
					<a title="<?php _e('Embed a Video'); ?>" href="#"><img alt="<?php _e('Embed a Video'); ?>" src="images/media-button-video.gif"/></a>
				</li>
				<?php if( user_can_richedit() ) { ?>
				<li id="switcher">
					<?php wp_print_scripts( 'quicktags' ); ?>
					<?php add_filter('the_editor_content', 'wp_richedit_pre'); ?>
					<a id="edButtonHTML" onclick="switchEditors.go('<?php echo $id; ?>', 'html');"><?php _e('HTML'); ?></a>
					<a id="edButtonPreview" class="active" onclick="switchEditors.go('<?php echo $id; ?>', 'tinymce');"><?php _e('Visual'); ?></a>
					<div class="zerosize"><input accesskey="e" type="button" onclick="switchEditors.go('<?php echo $id; ?>')" /></div>
				</li>
				<?php } ?>
			</ul>
			<div id="quicktags"></div>
			<div class="editor-container">
				<textarea name="content" id="content" style="width:100%;" class="mceEditor" rows="15">
					<?php if ($selection) echo wp_richedit_pre(htmlspecialchars_decode($selection)); ?>
					<?php if ($url) { echo '<p>'; if($selection) _e('via '); echo "<a href='$url'>$title</a>."; echo '</p>'; } ?>
				</textarea>
			</div>
		</div>
	</div>
</div>
</form>
</body>
</html>
