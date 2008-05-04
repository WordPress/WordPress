<?php
require_once('admin.php');

$title = __('Permalink Settings');
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

if ( isset($_POST['permalink_structure']) || isset($_POST['category_base']) ) {
	check_admin_referer('update-permalink');

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

	if ( isset($_POST['tag_base']) ) {
		$tag_base = $_POST['tag_base'];
		if (! empty($tag_base) )
			$tag_base = preg_replace('#/+#', '/', '/' . $_POST['tag_base']);
		$wp_rewrite->set_tag_base($tag_base);
	}
}

$permalink_structure = get_option('permalink_structure');
$category_base = get_option('category_base');
$tag_base = get_option( 'tag_base' );

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
if ( $permalink_structure && !$usingpi && !$writable )
	_e('You should update your .htaccess now.');
else
	_e('Permalink structure updated.');
?></p></div>
<?php endif; ?>

<div class="wrap">
  <h2><?php _e('Customize Permalink Structure') ?></h2>
<form name="form" action="options-permalink.php" method="post">
<?php wp_nonce_field('update-permalink') ?>
  <p><?php _e('By default WordPress uses web <abbr title="Universal Resource Locator">URL</abbr>s which have question marks and lots of numbers in them, however WordPress offers you the ability to create a custom URL structure for your permalinks and archives. This can improve the aesthetics, usability, and forward-compatibility of your links. A <a href="http://codex.wordpress.org/Using_Permalinks">number of tags are available</a>, and here are some examples to get you started.'); ?></p>

<?php
$prefix = '';

if ( ! got_mod_rewrite() )
	$prefix = '/index.php';

$structures = array(
	'',
	$prefix . '/%year%/%monthnum%/%day%/%postname%/',
	$prefix . '/%year%/%monthnum%/%postname%/',
	$prefix . '/archives/%post_id%'
	);
?>
<h3><?php _e('Common settings'); ?></h3>
<table class="form-table">
	<tr>
		<th><label><input name="selection" type="radio" value="" class="tog" <?php checked('', $permalink_structure); ?> /> <?php _e('Default'); ?></label></th>
		<td><code><?php echo get_option('home'); ?>/?p=123</code></td>
	</tr>
	<tr>
		<th><label><input name="selection" type="radio" value="<?php echo $structures[1]; ?>" class="tog" <?php checked($structures[1], $permalink_structure); ?> /> <?php _e('Day and name'); ?></label></th>
		<td><code><?php echo get_option('home') . $prefix . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/sample-post/'; ?></code></td>
	</tr>
	<tr>
		<th><label><input name="selection" type="radio" value="<?php echo $structures[2]; ?>" class="tog" <?php checked($structures[2], $permalink_structure); ?> /> <?php _e('Month and name'); ?></label></th>
		<td><code><?php echo get_option('home') . $prefix . '/' . date('Y') . '/' . date('m') . '/sample-post/'; ?></code></td>
	</tr>
	<tr>
		<th><label><input name="selection" type="radio" value="<?php echo $structures[3]; ?>" class="tog" <?php checked($structures[3], $permalink_structure); ?> /> <?php _e('Numeric'); ?></label></th>
		<td><code><?php echo get_option('home') . $prefix  ; ?>/archives/123</code></td>
	</tr>
	<tr>
		<th>
			<label><input name="selection" id="custom_selection" type="radio" value="custom" class="tog"
			<?php if ( !in_array($permalink_structure, $structures) ) { ?>
			checked="checked"
			<?php } ?>
			 />
			<?php _e('Custom Structure'); ?>
			</label>
		</th>
		<td>
			<input name="permalink_structure" id="permalink_structure" type="text" class="code" style="width: 60%;" value="<?php echo attribute_escape($permalink_structure); ?>" size="50" />
		</td>
	</tr>
</table>

<h3><?php _e('Optional'); ?></h3>
<?php if ($is_apache) : ?>
	<p><?php _e('If you like, you may enter custom structures for your category and tag <abbr title="Universal Resource Locator">URL</abbr>s here. For example, using <code>/topics/</code> as your category base would make your category links like <code>http://example.org/topics/uncategorized/</code>. If you leave these blank the defaults will be used.') ?></p>
<?php else : ?>
	<p><?php _e('If you like, you may enter custom structures for your category and tag <abbr title="Universal Resource Locator">URL</abbr>s here. For example, using <code>/topics/</code> as your category base would make your category links like <code>http://example.org/index.php/topics/uncategorized/</code>. If you leave these blank the defaults will be used.') ?></p>
<?php endif; ?>

<table class="form-table">
	<tr>
		<th><label for="category_base"><?php _e('Category base'); ?></label></th>
		<td><input name="category_base" id="category_base" type="text" class="code"  value="<?php echo attribute_escape($category_base); ?>" size="30" /></td>
	</tr>
	<tr>
		<th><label for="tag_base"><?php _e('Tag base'); ?></label></th>
		<td><input name="tag_base" id="tag_base" type="text" class="code"  value="<?php echo attribute_escape($tag_base); ?>" size="30" /></td>
	</tr>
</table>
<p class="submit"><input type="submit" name="submit" class="button" value="<?php _e('Save Changes') ?>" /></p>
  </form>
<?php if ( $permalink_structure && !$usingpi && !$writable ) : ?>
  <p><?php _e('If your <code>.htaccess</code> file were <a href="http://codex.wordpress.org/Changing_File_Permissions">writable</a>, we could do this automatically, but it isn&#8217;t so these are the mod_rewrite rules you should have in your <code>.htaccess</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all.') ?></p>
<form action="options-permalink.php" method="post">
<?php wp_nonce_field('update-permalink') ?>
	<p><textarea rows="5" style="width: 98%;" name="rules" id="rules"><?php echo wp_specialchars($wp_rewrite->mod_rewrite_rules()); ?></textarea></p>
</form>
<?php endif; ?>

</div>

<?php require('./admin-footer.php'); ?>
