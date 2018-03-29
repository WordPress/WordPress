<?php
if (!defined('WORDFENCE_VERSION')) { exit; }

/**
 * Presents the success message for WAF auto prepend installation.
 *
 * Expects $active.
 *
 * @var bool $active True if the WAF's auto_prepend_file is active and not because of a subdirectory install.
 */

if ($active):
?>
<p><?php _e('Nice work! The firewall is now optimized.', 'wordfence'); ?></p>
<?php else: ?>
<p><?php _e('The changes have not yet taken effect. If you are using LiteSpeed or IIS as your web server or CGI/FastCGI interface, you may need to wait a few minutes for the changes to take effect since the configuration files are sometimes cached. You also may need to select a different server configuration in order to complete this step, but wait for a few minutes before trying. You can try refreshing this page.', 'wordfence'); ?></p>
<?php endif; ?>
