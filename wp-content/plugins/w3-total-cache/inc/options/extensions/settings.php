<?php
/**
 * @var W3_UI_ExtensionsAdminView $this
 * @var string $active_tab
 * @var string $extension
 * @var array $meta
 */
?>
<?php do_action("w3tc_extensions_page-{$extension}") ?>
<?php echo $this->postbox_header(htmlspecialchars($meta['author']) . ' - ' . htmlspecialchars($meta['name']), '', $extension) ?>
<p><span class="description"><?php echo esc_html($meta['description']); ?></span></p>
<?php do_action("w3tc_before_do_settings_sections_{$extension}") ?>
<?php w3tc_do_settings_sections("{$extension}")?>
<?php do_action("w3tc_after_do_settings_sections_{$extension}") ?>
<p class="submit">
    <input type="hidden" name="redirect" value="<?php echo w3_admin_url('admin.php?page=w3tc_extensions&extension=' . $extension . '&action=view') ?>" />
    <?php echo $this->nonce_field('w3tc'); ?>
    <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save settings', 'w3-total-cache'); ?>" />
</p>
<?php echo $this->postbox_footer(); ?>
