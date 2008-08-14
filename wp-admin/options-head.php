<?php
/**
 * WordPress Options Header.
 *
 * Resets variables: 'action', 'standalone', and 'option_group_id'. Displays
 * updated message, if updated variable is part of the URL query.
 *
 * @package WordPress
 * @subpackage Administration
 */

wp_reset_vars(array('action', 'standalone', 'option_group_id'));
?>

<?php if (isset($_GET['updated'])) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
<?php endif; ?>