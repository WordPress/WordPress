<?php
/**
 * Press This Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Press This');

require_once('admin-header.php');
?>

<div class="wrap">
<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?></p>
<p><a href="<?php echo get_shortcut_link(); ?>" title="<?php echo attribute_escape(__('Press This')) ?>"><?php _e('Press This') ?></a></p>
</div>

<?php include('admin-footer.php'); ?>