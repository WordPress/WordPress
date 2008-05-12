<?php
require_once('admin.php');

if ( ! current_user_can('publish_posts') )
 	wp_die( __( 'Cheatin&#8217; uh?' ));

function quick_post() {
	$quick['post_status'] = 'publish';
	$quick['post_category'] = $_REQUEST['post_category'];
	$quick['tags_input'] = $_REQUEST['tags_input'];
	$quick['post_title'] = $_REQUEST['post_title'];

	$content = '';
	switch ( $_REQUEST['post_type'] ) {
		case 'regular':
			$content = $_REQUEST['content'];
			if ($_REQUEST['content2'])
				$content .= '<p>' . $_REQUEST['content2']; 
			break;

		case 'quote':
			$content = '<blockquote>' . $_REQUEST['content'];
			if ($_REQUEST['content2']) {
					$content = $content . '</blockquote>';
				$content = $content . '<p>' . $_REQUEST['content2']; 
			}
			break;

		case 'photo':
			if ($_REQUEST['photo_link'])
				$content = '<a href="' . $_REQUEST['photo_link'] . '" target="_new">';

			$content .= '<img src="' . $_REQUEST['photo_src'] . '\ style="float:left;padding:5px;">';

			if ($_REQUEST['photo_link'])
				$content .= '</a>';

			if ($_REQUEST['content'])
				$content = $content . "<br clear=\"all\">" . $_REQUEST['content']; 

			break;
		case "video":
			list($garbage,$video_id) = split("v=", $_REQUEST['content']);
			$content = "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/" .$video_id ."\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/" .$video_id ."\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>";
			if ($_REQUEST['content2'])
				$content = $content ."</br><p>" .$_REQUEST['content2'] ."</p>";
			break;				
	}

	$quick['post_content'] = $content;

	$post_ID = wp_insert_post($quick, true);

	if ( is_wp_error($post_ID) )
		wp_die($wp_error);

	return $post_ID;
}

function tag_input() {
	$s = '<div id="tagdiv">
		<h2>' . __('Tags') . '</h2>
		<input type="text" name="tags_input" class="text" id="tags-input" size="30" tabindex="3" value="" /><br/>' .
		__('Comma separated (e.g. Wordpress, Plugins)') .
		'</div>';
	
	return $s;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php _e('Quick Post') ?></title>

	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
		
	<?php wp_enqueue_script('jquery-ui-tabs'); ?>
	<?php wp_enqueue_script('thickbox'); ?>
	<?php do_action('admin_print_scripts'); do_action('admin_head'); ?>
	<?php wp_admin_css('css/quick-post'); ?>

	<script type="text/javascript">
    <? if ( user_can_richedit() ) { ?>
			tinyMCE.init({
				mode: "textareas",
				editor_selector: "mceEditor",
				width: "100%",
				theme : "advanced",
				theme_advanced_buttons1 : "bold,italic,underline,indent,separator,strikethrough,bullist,numlist,undo,redo,link,unlink",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_path_location : "bottom",
				extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
			});
    <? } ?>

	jQuery(document).ready(function() {
    <?php if ( preg_match("/youtube\.com\/watch/i", $_GET['u']) ) { ?>
		jQuery('#container > ul').tabs({ selected: 4 })({ fx: { height: 'toggle', opacity: 'toggle', fxSpeed: 'fast' } });
	<?php } elseif ( preg_match("/flickr\.com/i", $_GET['u']) ) { ?>
		jQuery('#container > ul').tabs({ selected: 2 })({ fx: { height: 'toggle', opacity: 'toggle', fxSpeed: 'fast' } });
	<?php } else { ?>
		jQuery('#container > ul').tabs({ fx: { height: 'toggle', opacity: 'toggle', fxSpeed: 'fast' } });
	<?php } ?>
	});
			
	</script>
</head>
<body>

<?php
if ( 'post' == $_REQUEST['action'] ) {
	check_admin_referer('quick-post');
	$post_ID = quick_post();
?>
	<script>if(confirm("<?php _e('Your post is saved. Do you want to view the post?') ?>")) {window.opener.location.replace("<?php echo get_permalink($post_ID);?>");}window.close();</script>
	</body></html>
<?php
	die;
}

// Clean up the data being passed in
$title = stripslashes($_GET['t']);

?>
	<div id="container">
	
		<ul>
			<li><a href="#section-1"><span><?php _e('Text/Link') ?></span></a></li>
		 	<li><a href="#section-2"><span><?php _e('Photo') ?></span></a></li>
			<li><a href="#section-3"><span><?php _e('Quote') ?></span></a></li>
			<li><a href="#section-4"><span><?php _e('Video') ?></span></a></li>
		</ul>

		<!-- Regular -->
		<div id="section-1">
		  <form action="quick-post.php?action=post" method="post" id="regular_form">
		  		<?php wp_nonce_field('quick-post') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="regular"/>
				<div id="posting">
					<h2><?php _e('Post Title') ?></h2>
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>

				  	<h2><?php _e('Post') ?></h2>
					<div>
						<textarea name="content" id="regular_post_two" style="height:170px;width:100%;" class="mceEditor"><?php echo stripslashes($_GET['s']);?><br>&lt;a href="<?php echo $_GET['u'];?>"&gt;<?php echo $title;?>&lt;/a&gt;</textarea>
					</div>        

					<?php tag_input(); ?>
       
					<div>         
						<input type="submit" value="<?php _e('Create Post') ?>" style="margin-top:15px;" onclick="document.getElementById('regular_saving').style.display = '';"/>&nbsp;&nbsp;
						<a href="#" onclick="if (confirm('<?php _e('Are you sure?') ?>')) { self.close(); } else { return false; }" style="color:#007BFF;"><?php _e('Cancel') ?></a>&nbsp;&nbsp;
						<img src="/images/bookmarklet_loader.gif" alt="" id="regular_saving" style="width:16px; height:16px; vertical-align:-4px; display:none;"/>
					</div>
				</div>
				<div id="categories">
					<h2><?php _e('Categories') ?></h2>
					<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
					<?php wp_category_checklist($post_ID) ?>
					</ul>
				</div>
			 </form>
		</div>

		<!-- Photo -->
		<div id="section-2">
			<form action="quick-post.php?action=post" method="post" id="photo_form">
				<?php wp_nonce_field('quick-post') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="photo"/>
				<div id="posting">
					<h2><?php _e('Post Title') ?></h2>
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>

					<h2><?php _e('Caption') ?></h2>
					<div>
						<textarea name="content" id="photo_post_two" style="height:130px;width:100%;" class="mceEditor"><?php echo "" .stripslashes($_GET['s']);?>
						<br>&lt;a href="<?php echo $_GET['u'];?>"&gt;<?php echo $title;?>&lt;/a&gt;</textarea>
					</div>

					<h2><?php _e('Photo URL') ?></h2>
					<input name="photo_src" id="photo_src" class="text" onkeydown="pick(0);"/>

					<style type="text/css">
						#img_container img {
					    	width:          75px;
					        height:         75px;
					        padding:        2px;
					        background-color: #f4f4f4;
					        margin-right:   7px; 
					        margin-bottom:  7px; 
					        cursor:         pointer;
					    }
					</style>

					<div id="img_container" style="border:solid 1px #ccc; background-color:#f4f4f4; padding:5px; width:370px; margin-top:10px; overflow:auto; height:100px;">
						<script type="text/javascript">
							var img, img_tag, aspect, w, h, skip, i, strtoappend = "";
							var my_src = ['<?php echo str_replace(",", "','", rtrim($_GET['imagez'], ","));?>'];
							var last = null;

							function pick(img) {
								if (last) last.style.backgroundColor = '#f4f4f4';
								if (img) {
									document.getElementById('photo_src').value = img.src;
									img.style.backgroundColor = '#44f';
								}
								last = img;
								return false;
							}
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
							if (jQuery.browser.safari) {
 								document.getElementById('img_container').innerHTML = strtoappend;  
 							} else {
								document.write(strtoappend);                
							}
						</script>
					</div>

					<h2><?php _e('Link Photo to following URL') ?></h2><?php _e('(leave blank to leave the photo unlinked)') ?>
					<input name="photo_link" id="photo_link" class="text" value="<?php echo attribute_escape($_GET['u']);?>"/>

					<?php tag_input(); ?>
      
					<div>         
						<input type="submit" value="<?php _e('Create Photo') ?>" style="margin-top:15px;"	onclick="document.getElementById('photo_saving').style.display = '';"/>&nbsp;&nbsp;

						<a href="#" onclick="if (confirm('Are you sure?')) { self.close(); } else { return false; }" style="color:#007BFF;">Cancel</a>&nbsp;&nbsp;
						<img src="/images/bookmarklet_loader.gif" alt="" id="photo_saving" style="width:16px; height:16px; vertical-align:-4px; display:none;"/>
					</div>
				</div>
				<div id="categories">
					<h2><?php _e('Categories') ?></h2>
					<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
					<?php wp_category_checklist($post_ID) ?>
					</ul>
				</div>
			</form>
		</div>

		<!-- Quote -->
		<div id="section-3">
			<form action="quick-post.php?action=post" method="post" id="quote_form">
				<?php wp_nonce_field('quick-post') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="quote"/>
				<div id="posting">
					<h2><?php _e('Post Title') ?></h2>
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape(sprintf(__('Quote by %s'), $title)); ?>"/>

					<h2><?php _e('Quote') ?></h2>
					<div>
						<textarea name="content" id="quote_post_one" style="height:130px;width:100%;" class="mceEditor"><?php echo stripslashes($_GET['s']);?></textarea>
					</div>

					<h2><?php _e('Source <span class="optional">(optional)</span>') ?></h2>
					<div>
						<textarea name="content2" id="quote_post_two" style="height:130px;width:100%;" class="mceEditor"><br>&lt;a href="<?php echo clean_url($_GET['u']);?>"&gt;<?php echo $title;?>&lt;/a&gt;</textarea>
					</div>

					<?php tag_input(); ?>

					<div>         
						<input type="submit" value="Create Quote" style="margin-top:15px;" onclick="document.getElementById('quote_saving').style.display = '';"/>&nbsp;&nbsp;
						<a href="#" onclick="if (confirm('Are you sure?')) { self.close(); } else { return false; }" style="color:#007BFF;"><?php _e('Cancel') ?></a>&nbsp;&nbsp;
						<img src="/images/bookmarklet_loader.gif" alt="" id="quote_saving" style="width:16px; height:16px; vertical-align:-4px; display:none;"/>
					</div>
				</div>
				<div id="categories">
					<h2><?php _e('Categories') ?></h2>
					<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
					<?php wp_category_checklist($post_ID) ?>
					</ul>
				</div>
			</form>
		</div>

		<!-- Video -->
		<div id="section-4">
			<form action="quick-post.php?action=post" method="post" id="video_form">
				<?php wp_nonce_field('quick-post') ?>
				<input type="hidden" name="source" value="bookmarklet"/>
				<input type="hidden" name="post_type" value="video"/>
				<div id="posting">
					<h2><?php _e('Post Title') ?></h2>
					<input name="post_title" id="post_title" class="text" value="<?php echo attribute_escape($title);?>"/>

					<?php 
					if ( preg_match("/youtube\.com\/watch/i", $_GET['u']) ) { 
						list($domain, $video_id) = split("v=", $_GET['u']);
					?>
					<input type="hidden" name="content" value="<?php echo $_GET['u']; ?>" />
					<img src="http://img.youtube.com/vi/<?php echo $video_id; ?>/default.jpg" align="right" style="border:solid 1px #aaa;" width="130" height="97"/><br clear="all" />
					<?php } else { ?>
					<h2><?php _e('Embed Code') ?></h2>
					<textarea name="content" id="video_post_one" style="height:80px;width:100%;"></textarea>
					<?php } ?>

					<h2><?php _e('Caption <span class="optional">(optional)</span>') ?></h2>

					<div>
						<textarea name="content2" id="video_post_two" style="height:130px;width:100%;" class="mceEditor"><?php echo stripslashes($_GET['s']);?><br>&lt;a href="<?php echo $_GET['u'];?>"&gt;<?php echo $title;?>&lt;/a&gt;</textarea>
					</div>

					<?php tag_input(); ?>

					<div>               
						<input type="submit" value="<?php _e('Create Video') ?>" style="margin-top:15px;" onclick="document.getElementById('video_saving').style.display = '';"/>&nbsp;&nbsp;
						<a href="#" onclick="if (confirm('Are you sure?')) { self.close(); } else { return false; }" style="color:#007BFF;"><?php _e('Cancel'); ?></a>&nbsp;&nbsp;
						<img src="/images/bookmarklet_loader.gif" alt="" id="video_saving" style="width:16px; height:16px; vertical-align:-4px; display:none;"/>
					</div>
				</div>
				<div id="categories">
					<h2><?php _e('Categories') ?></h2>
					<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
					<?php wp_category_checklist($post_ID) ?>
					</ul>
				</div>
			</form>
		</div>

	</div>

</body>
</html>
