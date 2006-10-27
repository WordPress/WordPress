<?php
require_once('admin.php');

$title = __('Add Bookmark');
$this_file = 'link-manager.php';
$parent_file = 'link-manager.php';


wp_reset_vars(array('action', 'cat_id', 'linkurl', 'name', 'image',
                       'description', 'visible', 'target', 'category', 'link_id',
                       'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel',
                       'notes', 'linkcheck[]'));

wp_enqueue_script( array('xfn', 'dbx-admin-key?pagenow=link.php') );
if ( current_user_can( 'manage_categories' ) )
	wp_enqueue_script( 'ajaxcat' );
require('admin-header.php');
?>

<?php if ($_GET['added']) : ?>
<div id="message" class="updated fade"><p><?php _e('Link added.'); ?></p></div>
<?php endif; ?>

<?php
	$link = get_default_link_to_edit();
	include('edit-link-form.php');
?>

<div id="wp-link-bookmarklet"  class="wrap">
<h3><?php _e('Add Link Bookmarklet'); ?></h3>
<p><?php _e('Right click on the following link and choose "Bookmark This Link..." to create an add link shortcut. Right now this only works on Mozilla or Netscape, but we’re working on it.'); ?></p>
<?php printf(__('<p><a href="%s" title="Link add bookmarklet">Link This</a></p>'), "javascript:void(linkmanpopup=window.open('" . get_option('siteurl') . "/wp-admin/link-add.php?action=popup&amp;linkurl='+escape(location.href)+'&amp;name='+escape(document.title),'LinkManager','scrollbars=yes,width=750,height=550,left=15,top=15,status=yes,resizable=yes'));linkmanpopup.focus();window.focus();linkmanpopup.focus();") ?>
</div>

<?php
require('admin-footer.php');
?>
