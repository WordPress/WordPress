<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('Database Cluster Configuration File', 'w3-total-cache')); ?>
        <table class="form-table">
            <tr>
                <th>
                    <textarea cols="70" rows="25" style="width: 100%" 
                        name="newcontent" id="newcontent" 
                        tabindex="1"><?php echo esc_textarea($content); ?></textarea><br />
                    <br />
                    <span class="description">
                        <?php _e('Note: Changes will have immediate effect on your database configuration. If the application stops working creating the settings file, edit or remove this configuration file manually at <strong>/wp-content/db-cluster-config.php</strong>.', 'w3-total-cache'); ?>
                    </span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_config_dbcluster_config_save" class="w3tc-button-save button-primary" value="<?php _e('Save configuration file', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>