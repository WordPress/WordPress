<?php
require_once('admin.php');

$title = __('Permalink Options');
$parent_file = 'options-general.php';

include('admin-header.php');

$home_path = get_home_path();

if ( isset($_POST) ) {
	if ( isset($_POST['permalink_structure']) ) {
		$permalink_structure = $_POST['permalink_structure'];
		if (! empty($permalink_structure) )
			$permalink_structure = preg_replace('#/+#', '/', '/' . $_POST['permalink_structure']);
		$wp_rewrite->set_permalink_structure($permalink_structure);
	}
	
	if ( isset($_POST['category_base']) ) {
		$category_base = $_POST['category_base'];
		if (! empty($category_base) )
			$category_base = preg_replace('#/+#', '/', '/' . $_POST['category_base']);
		$wp_rewrite->set_category_base($category_base);
	}
}
	
$permalink_structure = get_settings('permalink_structure');
$category_base = get_settings('category_base');

generate_page_rewrite_rules();

if ( (!file_exists($home_path.'.htaccess') && is_writable($home_path)) || is_writable($home_path.'.htaccess') )
	$writable = true;
else
	$writable = false;

if ($wp_rewrite->using_index_permalinks())
	$usingpi = true;
else
	$usingpi = false;

save_mod_rewrite_rules();
?>

<?php if (isset($_POST['submit'])) : ?>
<div class="updated"><p><?php _e('Permalink structure updated.'); ?></p></div>
<?php endif; ?>

<div class="wrap"> 
  <h2><?php _e('Edit Permalink Structure') ?></h2> 
  <p><?php _e('By default WordPress uses web URIs which have question marks and lots of numbers in them, however WordPress offers you the ability to create a custom URI structure for your permalinks and archives. This can improve the aesthetics, usability, and longevity of your links. A <a href="http://codex.wordpress.org/Using_Permalinks">number of tags are available</a>, and here are some examples to get you started.'); ?></p>

<?php if ($is_apache) : ?>
<dl>
<dt><?php _e('Structure'); ?>: <code>/%year%/%monthnum%/%day%/%postname%/</code></dt>
	<strong>
	<dd><?php _e('Result'); ?>: <code><?php echo get_settings('home') . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/sample-post/'; ?></code></dd>
	</strong>
	<dt><?php _e('Structure'); ?>: <code>/archives/%post_id%</code></dt>
	<strong>
	<dd><?php _e('Result'); ?>: <code><?php echo get_settings('home'); ?>/archives/123</code></dd>
	</strong>
	<dt></dt>
</dl>

<p><?php _e('For the above to work you must have something called <code>mod_rewrite</code> installed on your server. (Ask your host.) If that isn&#8217;t available, you can prefix the structure with <code>/index.php/</code> . This is the recommend method if you are on any web server but Apache.'); ?></p>

<?php else : ?>
<dl>
<dt><?php _e('Structure'); ?>: <code>/index.php/%year%/%monthnum%/%day%/%postname%/</code></dt>
	<strong>
	<dd><?php _e('Result'); ?>: <code><?php echo get_settings('home') . '/index.php/' . date('Y') . '/' . date('m') . '/' . date('d') . '/sample-post/'; ?></code></dd>
	</strong>
	<dt><?php _e('Structure'); ?>: <code>/index.php/archives/%post_id%</code></dt>
	<strong>
	<dd><?php _e('Result'); ?>: <code><?php echo get_settings('home'); ?>/index.php/archives/123</code></dd>
	</strong>
	<dt></dt>
</dl>
<?php endif; ?>

  <form name="form" action="options-permalink.php" method="post"> 
    <p><?php _e('Use the template tags above to create a virtual site structure:') ?></p>
    <p> 
      <?php _e('Structure'); ?>: <input name="permalink_structure" type="text" class="code" style="width: 60%;" value="<?php echo $permalink_structure; ?>" size="50" /> 
    </p> 
	<p><?php _e('If you like, you may enter a custom prefix for your category URIs here. For example, <code>/taxonomy/categorias</code> would make your category links like <code>http://example.org/taxonomy/categorias/uncategorized/</code>. If you leave this blank the default will be used.') ?></p>
	<p> 
  <?php _e('Category base'); ?>: <input name="category_base" type="text" class="code"  value="<?php echo $category_base; ?>" size="30" /> 
     </p> 
    <p class="submit"> 
      <input type="submit" name="submit" value="<?php _e('Update Permalink Structure &raquo;') ?>" /> 
    </p> 
  </form> 
<?php if ( $permalink_structure && !$usingpi && !$writable ) : ?>
  <p><?php _e('If your <code>.htaccess</code> was <a href="http://codex.wordpress.org/Make_a_Directory_Writable">writable</a> we could do this automatically, but it isn&#8217;t so these are the mod_rewrite rules you should have in your <code>.htaccess</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all.') ?></p>
<form action="options-permalink.php" method="post">
   <p>
<textarea rows="5" style="width: 98%;" name="rules"><?php echo $wp_rewrite->mod_rewrite_rules(); ?>
</textarea>
    </p>
</form>
<?php endif; ?>

</div>

<?php require('./admin-footer.php'); ?>