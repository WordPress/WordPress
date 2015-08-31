<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php';?>

<p>
    <?php _e('Remove objects from the CDN by specifying the relative path on individual lines below and clicking the "Purge" button when done. For example:', 'w3-total-cache'); ?>
</p>
<p>
    <?php switch ($this->_config->get_string('cdn.engine')):
        case 'cotendo': ?>
        <ul>
            <li><em><?php echo $path?>/images/headers/</em> &mdash; <?php _e('the directory itself (only when accessed directly without any file).', 'w3-total-cache'); ?></li>
            <li><em><?php echo $path?>/images/headers/*.</em> &mdash; <?php _e('all files in the directory with no extension, with all parameter variations.', 'w3-total-cache'); ?></li>
            <li><em><?php echo $path?>/images/headers/*.jpg</em> &mdash; <?php _e('all files in the directory whose extension is "jpg".', 'w3-total-cache'); ?></li>
            <li><em><?php echo $path?>/images/headers/path</em> &mdash; <?php _e('the specific file (when the file does not have an extension), and without parameters.', 'w3-total-cache'); ?></li>
            <li><em><?php echo $path?>/images/headers/path.jpg</em> &mdash; <?php _e('the specific file with its extension, and without parameters.', 'w3-total-cache'); ?></li>
            <li><em><?php echo $path?>/images/headers/path.jpg?*</em> &mdash; <?php _e('the specific file with its extension, with all variation of parameters.', 'w3-total-cache'); ?></li>
            <li><em><?php echo $path?>/images/headers/path.jpg?key=value</em> &mdash; <?php _e('the specific file with its extension, with the specific parameters.', 'w3-total-cache'); ?></li>
        </ul>
        <?php break;

        default: ?>
        <em><?php echo $path?>/images/headers/path.jpg</em>
        <?php break;
    endswitch; ?>
</p>


<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p><?php _e('Files to purge:', 'w3-total-cache'); ?></p>
    <p>
        <textarea name="files" rows="10" cols="90"></textarea>
    </p>
    <p>
        <?php echo w3_nonce_field('w3tc'); ?>
        <input class="button-primary" type="submit" name="w3tc_cdn_purge_post" value="<?php _e('Purge', 'w3-total-cache'); ?>" />
    </p>
</form>

<div class="log">
    <?php foreach ($results as $result): ?>
        <div class="log-<?php echo ($result['result'] == W3TC_CDN_RESULT_OK ? 'success' : 'error') ?>">
            <?php echo htmlspecialchars($result['remote_path']); ?>
            <strong><?php echo htmlspecialchars($result['error']); ?></strong>
        </div>
    <?php endforeach; ?>
</div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>