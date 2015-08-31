<?php
/**
 * @var W3_UI_ExtensionsAdminView $this
 * @var string $extension_status
 * @var int $page
 * @var array $extensions list of extensions for current $extension_status
 * @var array $extensions_all list of all extensions
 * @var array $extensions_active list of all active extensions
 * @var array $extensions_inactive list of all inactive extensions
 * @var array $extensions_core list of all core extensions
 */
?>
<ul class="subsubsub">
    <li class="all"><a href="?page=w3tc_extensions&extension_status=all"<?php echo $extension_status == 'all' ? ' class="current"':''?>>All <span class="count">(<?php echo sizeof($extensions_all) ?>)</span></a> |</li>
    <li class="active"><a href="?page=w3tc_extensions&extension_status=active"<?php echo $extension_status == 'active' ? ' class="current"':''?>>Active <span class="count">(<?php echo sizeof($extensions_active) ?>)</span></a> |</li>
    <li class="inactive"><a href="?page=w3tc_extensions&extension_status=inactive"<?php echo $extension_status == 'inactive' ? ' class="current"':''?>>Inactive <span class="count">(<?php echo sizeof($extensions_inactive) ?>)</span></a></li>
</ul>

<div class="tablenav top">

    <div class="alignleft actions">
        <select name="action">
            <option value="-1" selected="selected"><?php _e('Bulk Actions', 'w3-total-cache')?></option>
            <option value="activate-selected"><?php _e('Activate', 'w3-total-cache')?></option>
            <option value="deactivate-selected"><?php _e('Deactivate', 'w3-total-cache')?></option>
        </select>
        <input type="submit" name="" id="doaction" class="w3tc-button-save button action" value="<?php esc_attr_e('Apply')?>">
    </div>
    <div class="tablenav-pages one-page">
        <span class="displaying-num"><?php printf( translate_nooped_plural( _n_noop('%s extension', '%s extensions'), sizeof($extensions), 'w3-total-cache' ), sizeof($extensions) ); ?></span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat plugins extensions" cellspacing="0">
    <thead>
        <tr>
            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All', 'w3-total-cache')?></label><input id="cb-select-all-1" type="checkbox"></th><th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Extension', 'w3-total-cache')?></th><th scope="col" id="description" class="manage-column column-description" style=""><?php _e('Description', 'w3-total-cache')?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All', 'w3-total-cache')?></label><input id="cb-select-all-2" type="checkbox"></th><th scope="col" class="manage-column column-name" style=""><?php _e('Extension', 'w3-total-cache')?></th><th scope="col" class="manage-column column-description" style=""><?php _e('Description', 'w3-total-cache')?></th>
        </tr>
    </tfoot>
    <tbody id="the-list">
    <?php
        $cb_id = 0;
        foreach($extensions as $extension => $meta):
            $meta = $this->default_meta($meta);
            $cb_id++;
        ?>
            <?php do_action("w3tc_extension_before_row-{$extension}") ?>
            <tr id="<?php echo esc_attr($extension)?>" class="<?php echo w3_is_extension_active($extension) ? 'active' : 'inactive'?>">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="checkbox_<?php echo $cb_id?>"><?php printf(__('Select %s'), $meta['name']) ?></label>
                <input type="checkbox" name="checked[]" value="<?php echo esc_attr($extension) ?>" id="checkbox_<?php echo $cb_id?>" <?php disabled(!$meta['enabled'])?>>
            </th>
            <td class="plugin-title">
                <strong><?php esc_html_e($meta['name']) ?></strong>
                <div class="row-actions-visible">
                    <?php if (w3_is_extension_active($extension)):
                        $activate = '<a class="edit" href="' . esc_attr(w3_admin_url(sprintf('admin.php?page=w3tc_extensions&extension=%s', $extension))).'&action=view">'. __('Settings').'</a>';
                        $extra_links = apply_filters("w3tc_extension_plugin_links-{$extension}", array($activate));
                        $links =  implode(' | ', $extra_links);
                        if ($links) {
                            echo $links, ' | ';
                        }
                        ?>
                    <span class="0">
                    </span>
                    <span class="deactivate">
                        <a href="<?php esc_attr_e(wp_nonce_url(w3_admin_url(sprintf('admin.php?page=w3tc_extensions&action=deactivate&extension=%s&amp;extension_status=%s&amp;paged=%d', $extension, $extension_status, $page)), 'w3tc')) ?>" title="<?php esc_attr_e('Deactivate this extension', 'w3-total-cache') ?> ">
                            <?php _e('Deactivate') ?>
                        </a>
                    </span>
                    <?php else: ?>
                        <span class="activate">
                        <?php if ($meta['enabled']): ?>
                        <a href="<?php esc_attr_e(wp_nonce_url(w3_admin_url(sprintf('admin.php?page=w3tc_extensions&action=activate&extension=%s&amp;extension_status=%s&amp;paged=%d', $extension, $extension_status, $page)), 'w3tc')) ?>" title="<?php esc_attr_e('Activate this extension', 'w3-total-cache') ?> ">
                            <?php _e('Activate') ?>
                        </a>
                        <?php else: ?>
                            <?php _e('Disabled: Unsupported', 'w3-total-cache') ?>
                        <?php endif; ?>
                    </span>
                    <?php endif ?>
                </div>
            </td>
            <td class="column-description desc">
                <div class="plugin-description">
                    <p><?php _e($meta['description']) ?>
                    <?php if (!$meta['enabled']): ?>
                        <br />
                        <span class="description"><?php printf(__('Requirements: %s', 'w3-total-cache'), apply_filters("w3tc_extension_requirements-{$extension}", $meta['requirements'])) ?></span>
                        <?php do_action("w3tc_extension_requirements-{$extension}") ?>
                    <?php endif ?>
                    </p>
                </div>
                <div class="<?php echo w3_is_extension_active($extension) ? 'active' : 'inactive'?> second plugin-version-author-uri">
                    <?php printf(__('Version %s', 'w3-total-cache'), $meta['version'])?> | <?php printf(__('By %s', 'w3-total-cache'), sprintf('<a href="%s" title="' . __('Visit author homepage') . '">%s</a>', $meta['author uri'], $meta['author'])) ?>  | <a href="<?php esc_attr_e($meta['extension uri']) ?>" title="<?php esc_attr_e('Visit extension site', 'w3-total-cache') ?>"><?php _e('Visit extension site', 'w3-total-cache')?></a>
                </div>
            </td>
        </tr>
        <?php do_action("w3tc_extension_after_row", $extension) ?>
        <?php do_action("w3tc_extension_after_row-{$extension}") ?>
        <?php if (is_network_admin() && !w3_force_master()): ?>
        <tr>
            <th></th>
            <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox_admin_extensions("extensions.configuration_sealed", $extension); ?> <?php printf(__('Apply the %s settings to the entire network.', 'w3-total-cache'), $meta['name']); ?></label>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach ?>
    </tbody>
</table>
<div class="tablenav bottom">

    <div class="alignleft actions">
        <select name="action">
            <option value="-1" selected="selected"><?php _e('Bulk Actions', 'w3-total-cache')?></option>
            <option value="activate-selected"><?php _e('Activate', 'w3-total-cache')?></option>
            <option value="deactivate-selected"><?php _e('Deactivate', 'w3-total-cache')?></option>
        </select>
        <input type="submit" name="" id="doaction" class="w3tc-button-save button action" value="<?php esc_attr_e('Apply')?>">
    </div>
    <div class="tablenav-pages one-page">
        <span class="displaying-num"><?php printf( translate_nooped_plural( _n_noop('%s extension', '%s extensions'), sizeof($extensions), 'w3-total-cache' ), sizeof($extensions) ); ?></span>
    </div>
    <br class="clear">
</div>

<?php if (is_network_admin()): ?>
    <p class="submit">
    <?php echo $this->nonce_field('w3tc'); ?>
    <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save network settings', 'w3-total-cache'); ?>" />
</p>
<?php endif ?>
