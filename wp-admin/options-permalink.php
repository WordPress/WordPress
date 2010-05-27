<?php
/**
 * Permalink settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Permalink Settings');
$parent_file = 'options-general.php';

add_contextual_help($current_screen, 
	'<p>' . __('This screen provides some common options for your default permalinks URL structure.') . '</p>' .
	'<p>' . __('If you pick an option other than Default, your general URL path with structure tags, terms surrounded by %, will also appear in the custom structure field and your path can be further modified there.') . '</p>' .
	'<p>' . __('When you assign multiple categories or tags to a post, only one can show up in the permalink: the lowest numbered category. This applies if your custom structure includes %category% or %tag%.') . '</p>' .
	'<p>' . __('Note that permalinks beginning with structure tags calling Category, Tag, Author, or Postname require more advanced server resources. Double-check your hosting details to make sure those are in place or start your permalinks with other structure tags.') . '</p>' .
	'<p>' . __('The Optional fields lets you have add a base name that will appear in archive URLs intead of &#8220;category&#8221; or &#8220;tag.&#8221; For example, the page listing all posts in the category &#8220;uncategorized&#8221; could be /topics/uncategorized instead of category/uncategorized.') . '</p>' .
	'<p>' . __('You must click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Settings_Permalinks_SubPanel">Permalinks Settings Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Using_Permalinks">Using Permalinks Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/">Support Forums</a>') . '</p>'
);

/**
 * Display JavaScript on the page.
 *
 * @package WordPress
 * @subpackage Permalink_Settings_Panel
 */
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

include('./admin-header.php');

$home_path = get_home_path();
$iis7_permalinks = iis7_supports_permalinks();

$prefix = $blog_prefix = '';
if ( ! got_mod_rewrite() && ! $iis7_permalinks )
	$prefix = '/index.php';
if ( is_multisite() && !is_subdomain_install() && is_main_site() )
	$blog_prefix = '/blog';

if ( isset($_POST['permalink_structure']) || isset($_POST['category_base']) ) {
	check_admin_referer('update-permalink');

	if ( isset( $_POST['permalink_structure'] ) ) {
		$permalink_structure = $_POST['permalink_structure'];
		if ( ! empty( $permalink_structure ) ) {
			$permalink_structure = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $permalink_structure ) );
			if ( $prefix && $blog_prefix )
				$permalink_structure = $prefix . preg_replace( '#^/?index\.php#', '', $permalink_structure );
			else
				$permalink_structure = $blog_prefix . $permalink_structure;
		}
		$wp_rewrite->set_permalink_structure( $permalink_structure );
	}

	if ( isset( $_POST['category_base'] ) ) {
		$category_base = $_POST['category_base'];
		if ( ! empty( $category_base ) )
			$category_base = $blog_prefix . preg_replace('#/+#', '/', '/' . str_replace( '#', '', $category_base ) );
		$wp_rewrite->set_category_base( $category_base );
	}

	if ( isset( $_POST['tag_base'] ) ) {
		$tag_base = $_POST['tag_base'];
		if ( ! empty( $tag_base ) )
			$tag_base = $blog_prefix . preg_replace('#/+#', '/', '/' . str_replace( '#', '', $tag_base ) );
		$wp_rewrite->set_tag_base( $tag_base );
	}
}

$permalink_structure = get_option('permalink_structure');
$category_base = get_option('category_base');
$tag_base = get_option( 'tag_base' );

if ( $iis7_permalinks ) {
	if ( ( ! file_exists($home_path . 'web.config') && win_is_writable($home_path) ) || win_is_writable($home_path . 'web.config') )
		$writable = true;
	else
		$writable = false;
} else {
	if ( ( ! file_exists($home_path . '.htaccess') && is_writable($home_path) ) || is_writable($home_path . '.htaccess') )
		$writable = true;
	else
		$writable = false;
}

if ( $wp_rewrite->using_index_permalinks() )
	$usingpi = true;
else
	$usingpi = false;

$wp_rewrite->flush_rules();


if (isset($_POST['submit'])) : ?>
<div id="message" class="updated"><p><?php
if ( ! is_multisite() ) {
	if ( $iis7_permalinks ) {
		if ( $permalink_structure && ! $usingpi && ! $writable )
			_e('You should update your web.config now');
		else if ( $permalink_structure && ! $usingpi && $writable )
			_e('Permalink structure updated. Remove write access on web.config file now!');
		else
			_e('Permalink structure updated');
	} else {
		if ( $permalink_structure && ! $usingpi && ! $writable )
			_e('You should update your .htaccess now.');
		else
			_e('Permalink structure updated.');
	}
} else {
	_e('Permalink structure updated.');
}
?>
</p></div>
<?php endif; ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form name="form" action="options-permalink.php" method="post">
<?php wp_nonce_field('update-permalink') ?>

  <p><?php _e('By default WordPress uses web <abbr title="Universal Resource Locator">URL</abbr>s which have question marks and lots of numbers in them, however WordPress offers you the ability to create a custom URL structure for your permalinks and archives. This can improve the aesthetics, usability, and forward-compatibility of your links. A <a href="http://codex.wordpress.org/Using_Permalinks">number of tags are available</a>, and here are some examples to get you started.'); ?></p>

<?php
if ( is_multisite() && !is_subdomain_install() && is_main_site() ) {
	$permalink_structure = preg_replace( '|^/?blog|', '', $permalink_structure );
	$category_base = preg_replace( '|^/?blog|', '', $category_base );
	$tag_base = preg_replace( '|^/?blog|', '', $tag_base );
}

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
		<th><label><input name="selection" type="radio" value="<?php echo esc_attr($structures[1]); ?>" class="tog" <?php checked($structures[1], $permalink_structure); ?> /> <?php _e('Day and name'); ?></label></th>
		<td><code><?php echo get_option('home') . $blog_prefix . $prefix . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/sample-post/'; ?></code></td>
	</tr>
	<tr>
		<th><label><input name="selection" type="radio" value="<?php echo esc_attr($structures[2]); ?>" class="tog" <?php checked($structures[2], $permalink_structure); ?> /> <?php _e('Month and name'); ?></label></th>
		<td><code><?php echo get_option('home') . $blog_prefix . $prefix . '/' . date('Y') . '/' . date('m') . '/sample-post/'; ?></code></td>
	</tr>
	<tr>
		<th><label><input name="selection" type="radio" value="<?php echo esc_attr($structures[3]); ?>" class="tog" <?php checked($structures[3], $permalink_structure); ?> /> <?php _e('Numeric'); ?></label></th>
		<td><code><?php echo get_option('home') . $blog_prefix . $prefix; ?>/archives/123</code></td>
	</tr>
	<tr>
		<th>
			<label><input name="selection" id="custom_selection" type="radio" value="custom" class="tog" <?php checked( !in_array($permalink_structure, $structures) ); ?> />
			<?php _e('Custom Structure'); ?>
			</label>
		</th>
		<td>
			<?php echo $blog_prefix; ?>
			<input name="permalink_structure" id="permalink_structure" type="text" value="<?php echo esc_attr($permalink_structure); ?>" class="regular-text code" />
		</td>
	</tr>
</table>

<h3><?php _e('Optional'); ?></h3>
<?php if ( $is_apache || $iis7_permalinks ) : ?>
	<p><?php _e('If you like, you may enter custom structures for your category and tag <abbr title="Universal Resource Locator">URL</abbr>s here. For example, using <kbd>topics</kbd> as your category base would make your category links like <code>http://example.org/topics/uncategorized/</code>. If you leave these blank the defaults will be used.') ?></p>
<?php else : ?>
	<p><?php _e('If you like, you may enter custom structures for your category and tag <abbr title="Universal Resource Locator">URL</abbr>s here. For example, using <code>topics</code> as your category base would make your category links like <code>http://example.org/index.php/topics/uncategorized/</code>. If you leave these blank the defaults will be used.') ?></p>
<?php endif; ?>

<table class="form-table">
	<tr>
		<th><label for="category_base"><?php /* translators: prefix for category permalinks */ _e('Category base'); ?></label></th>
		<td><?php echo $blog_prefix; ?> <input name="category_base" id="category_base" type="text" value="<?php echo esc_attr( $category_base ); ?>" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="tag_base"><?php _e('Tag base'); ?></label></th>
		<td><?php echo $blog_prefix; ?> <input name="tag_base" id="tag_base" type="text" value="<?php echo esc_attr($tag_base); ?>" class="regular-text code" /></td>
	</tr>
	<?php do_settings_fields('permalink', 'optional'); ?>
</table>

<?php do_settings_sections('permalink'); ?>

<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
  </form>
<?php if ( !is_multisite() ) { ?>
<?php if ( $iis7_permalinks ) :
	if ( isset($_POST['submit']) && $permalink_structure && ! $usingpi && ! $writable ) :
		if ( file_exists($home_path . 'web.config') ) : ?>
<p><?php _e('If your <code>web.config</code> file were <a href="http://codex.wordpress.org/Changing_File_Permissions">writable</a>, we could do this automatically, but it isn&#8217;t so this is the url rewrite rule you should have in your <code>web.config</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all. Then insert this rule inside of the <code>/&lt;configuration&gt;/&lt;system.webServer&gt;/&lt;rewrite&gt;/&lt;rules&gt;</code> element in <code>web.config</code> file.') ?></p>
<form action="options-permalink.php" method="post">
<?php wp_nonce_field('update-permalink') ?>
	<p><textarea rows="9" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_html($wp_rewrite->iis7_url_rewrite_rules()); ?></textarea></p>
</form>
<p><?php _e('If you temporarily make your <code>web.config</code> file writable for us to generate rewrite rules automatically, do not forget to revert the permissions after rule has been saved.')  ?></p>
		<?php else : ?>
<p><?php _e('If the root directory of your site were <a href="http://codex.wordpress.org/Changing_File_Permissions">writable</a>, we could do this automatically, but it isn&#8217;t so this is the url rewrite rule you should have in your <code>web.config</code> file. Create a new file, called <code>web.config</code> in the root directory of your site. Click in the field and press <kbd>CTRL + a</kbd> to select all. Then insert this code into the <code>web.config</code> file.') ?></p>
<form action="options-permalink.php" method="post">
<?php wp_nonce_field('update-permalink') ?>
	<p><textarea rows="18" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_html($wp_rewrite->iis7_url_rewrite_rules(true)); ?></textarea></p>
</form>
<p><?php _e('If you temporarily make your site&#8217;s root directory writable for us to generate the <code>web.config</code> file automatically, do not forget to revert the permissions after the file has been created.')  ?></p>
		<?php endif; ?>
	<?php endif; ?>
<?php else :
	if ( $permalink_structure && ! $usingpi && ! $writable ) : ?>
<p><?php _e('If your <code>.htaccess</code> file were <a href="http://codex.wordpress.org/Changing_File_Permissions">writable</a>, we could do this automatically, but it isn&#8217;t so these are the mod_rewrite rules you should have in your <code>.htaccess</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all.') ?></p>
<form action="options-permalink.php" method="post">
<?php wp_nonce_field('update-permalink') ?>
	<p><textarea rows="6" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_html($wp_rewrite->mod_rewrite_rules()); ?></textarea></p>
</form>
	<?php endif; ?>
<?php endif; ?>
<?php } // multisite ?>

</div>

<?php require('./admin-footer.php'); ?>
