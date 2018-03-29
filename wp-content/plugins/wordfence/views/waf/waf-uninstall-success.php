<?php
if (!defined('WORDFENCE_VERSION')) { exit; }

/**
 * Presents the success message for WAF auto prepend uninstallation.
 *
 * Expects $active and $subdirectory.
 *
 * @var bool $active True if the WAF's auto_prepend_file is active and not because of a subdirectory install.
 * @var bool $subdirectory True if the WAF's auto_prepend_file is active because of a subdirectory install.
 */

if (!$active && !$subdirectory):
?>
	<p><?php _e('Uninstallation was successful!', 'wordfence'); ?></p>
<?php elseif (!$active): ?>
	<p><?php _e('Uninstallation from this site was successful! The Wordfence Firewall is still active because it is installed in another WordPress installation.', 'wordfence'); ?></p>
<?php else: ?>
	<p><?php _e('The changes have not yet taken effect. If you are using LiteSpeed or IIS as your web server or CGI/FastCGI interface, you may need to wait a few minutes for the changes to take effect since the configuration files are sometimes cached. You also may need to select a different server configuration in order to complete this step, but wait for a few minutes before trying. You can try refreshing this page.', 'wordfence'); ?></p>
<?php endif; ?>
