<?php
require_once('admin.php');

$title = __('Permalink Options');
$parent_file = 'options-general.php';

function add_js() {
?>
<script type="text/javascript">
//<![CDATA[
function GetElementsWithClassName(elementName, className) {
var allElements = document.getElementsByTagName(elementName);
var elemColl = new Array();
for (i = 0; i < allElements.length; i++) {
if (allElements[i].className == className) {
elemColl[elemColl.length] = allElements[i];
}
}
return elemColl;
}

function upit() {
var inputColl = GetElementsWithClassName('input', 'tog');
var structure = document.getElementById('permalink_structure');
var inputs = '';
for (i = 0; i < inputColl.length; i++) {
if ( inputColl[i].checked && inputColl[i].value != '') {
inputs += inputColl[i].value + ' ';
}
}
inputs = inputs.substr(0,inputs.length - 1);
if ( 'custom' != inputs )
structure.value = inputs;
}

function blurry() {
if (!document.getElementById) return;

var structure = document.getElementById('permalink_structure');
structure.onfocus = function () { document.getElementById('custom_selection').checked = 'checked'; }

var aInputs = document.getElementsByTagName('input');

for (var i = 0; i < aInputs.length; i++) {
aInputs[i].onclick = aInputs[i].onkeyup = upit;
}
}

window.onload = blurry;
//]]>
</script>
<?php
}
add_filter('admin_head', 'add_js');

include('admin-header.php');

$home_path = get_home_path();

if ( isset($_POST) ) {
	check_admin_referer();

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

if ( (!file_exists($home_path.'.htaccess') && is_writable($home_path)) || is_writable($home_path.'.htaccess') )
	$writable = true;
else
	$writable = false;

if ($wp_rewrite->using_index_permalinks())
	$usingpi = true;
else
	$usingpi = false;

$wp_rewrite->flush_rules();
?>

<?php if (isset($_POST['submit'])) : ?>
<div id="message" class="updated fade"><p><?php
if ($writable)
	_e('Permalink structure updated.');
else
	_e('You should update your .htaccess now.'); 
?></p></div>
<?php endif; ?>

<div class="wrap"> 
  <h2><?php _e('Customize Permalink Structure') ?></h2> 
  <p><?php _e('By default WordPress uses web URIs which have question marks and lots of numbers in them, however WordPress offers you the ability to create a custom URI structure for your permalinks and archives. This can improve the aesthetics, usability, and forward-compatibility of your links. A <a href="http://codex.wordpress.org/Using_Permalinks">number of tags are available</a>, and here are some examples to get you started.'); ?></p>

<?php
$prefix = '';

if ( ! got_mod_rewrite() )
	$prefix = '/index.php';

$structures = array(
	'',
	$prefix . '/%year%/%monthnum%/%day%/%postname%/',
	$prefix . '/archives/%post_id%'
	);
?>
<form name="form" action="options-permalink.php" method="post"> 
<h3><?php _e('Common options:'); ?></h3>
<p>
	<label>
<input name="selection" type="radio" value="" class="tog" <?php checked('', $permalink_structure); ?> /> 
<?php _e('Default'); ?><br /> <span> &raquo; <code><?php echo get_settings('home'); ?>/?p=123</code></span>
   </label>
</p>
<p>
	<label>
<input name="selection" type="radio" value="<?php echo $structures[1]; ?>" class="tog" <?php checked($structures[1], $permalink_structure); ?> /> 
<?php _e('Date and name based'); ?><br /> <span> &raquo; <code><?php echo get_settings('home') . $prefix . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/sample-post/'; ?></code></span>
   </label>
</p>
<p>
	<label>
<input name="selection" type="radio" value="<?php echo $structures[2]; ?>" class="tog" <?php checked($structures[2], $permalink_structure); ?> /> 
<?php _e('Numeric'); ?><br /> <span> &raquo; <code><?php echo get_settings('home') . $prefix  ; ?>/archives/123</code></span>
   </label>
</p>
<p>
<label>
<input name="selection" id="custom_selection" type="radio" value="custom" class="tog"
<?php if ( !in_array($permalink_structure, $structures) ) { ?>
checked="checked"
<?php } ?>
 /> 
<?php _e('Custom, specify below'); ?>
</label>
<br />
</p>
<p id="customstructure"><?php _e('Custom structure'); ?>: <input name="permalink_structure" id="permalink_structure" type="text" class="code" style="width: 60%;" value="<?php echo $permalink_structure; ?>" size="50" /></p>

<h3><?php _e('Optional'); ?></h3>
<?php if ($is_apache) : ?>
	<p><?php _e('If you like, you may enter a custom prefix for your category URIs here. For example, <code>/taxonomy/tags</code> would make your category links like <code>http://example.org/taxonomy/tags/uncategorized/</code>. If you leave this blank the default will be used.') ?></p>
<?php else : ?>
	<p><?php _e('If you like, you may enter a custom prefix for your category URIs here. For example, <code>/index.php/taxonomy/tags</code> would make your category links like <code>http://example.org/index.php/taxonomy/tags/uncategorized/</code>. If you leave this blank the default will be used.') ?></p>
<?php endif; ?>
	<p> 
  <?php _e('Category base'); ?>: <input name="category_base" type="text" class="code"  value="<?php echo $category_base; ?>" size="30" /> 
     </p> 
    <p class="submit"> 
      <input type="submit" name="submit" value="<?php _e('Update Permalink Structure &raquo;') ?>" /> 
    </p> 
  </form> 
<?php if ( $permalink_structure && !$usingpi && !$writable ) : ?>
  <p><?php _e('If your <code>.htaccess</code> file were <a href="http://codex.wordpress.org/Make_a_Directory_Writable">writable</a>, we could do this automatically, but it isn&#8217;t so these are the mod_rewrite rules you should have in your <code>.htaccess</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all.') ?></p>
<form action="options-permalink.php" method="post">
   <p>
<textarea rows="5" style="width: 98%;" name="rules"><?php echo $wp_rewrite->mod_rewrite_rules(); ?>
</textarea>
    </p>
</form>
<?php endif; ?>

</div>

<?php require('./admin-footer.php'); ?>
