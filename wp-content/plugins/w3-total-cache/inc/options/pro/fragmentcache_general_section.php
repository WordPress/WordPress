<?php echo $this->postbox_header('Fragment Cache', '', 'fragment_cache'); ?>
<p>Enable fragment caching reduce execution time for common operations.</p>

<table class="form-table">
    <tr>
        <th><?php w3_e_config_label('fragmentcache.enabled', 'general') ?></th>
        <td>
            <?php $this->checkbox('fragmentcache.enabled') ?>&nbsp;<strong>Enable</strong></label>
            <br /><span class="description">Fragment caching greatly increases performance for highly themes and plugins that use the <a href="http://codex.wordpress.org/Transients_API" target="_blank">Transient <acronym title="Application Programming Interface">API</acronym></a>.</span>
        </td>
    </tr>
    <tr>
        <th><?php w3_e_config_label('fragmentcache.engine', 'general') ?></th>
        <td>
            <select name="fragmentcache.engine" <?php $this->sealing_disabled('fragmentcache'); ?>>
                <optgroup label="Shared Server:">
                    <option value="file" <?php selected($this->_config->get_string('fragmentcache.engine'), 'file'); ?>>Disk</option>
                </optgroup>
                <optgroup label="Dedicated / Virtual Server:">
                    <option value="apc"<?php selected($this->_config->get_string('fragmentcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>>Opcode: Alternative PHP Cache (APC)</option>
                    <option value="eaccelerator"<?php selected($this->_config->get_string('fragmentcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>>Opcode: eAccelerator</option>
                    <option value="xcache"<?php selected($this->_config->get_string('fragmentcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>>Opcode: XCache</option>
                    <option value="wincache"<?php selected($this->_config->get_string('fragmentcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>>Opcode: WinCache</option>
                </optgroup>
                <optgroup label="Multiple Servers:">
                    <option value="memcached"<?php selected($this->_config->get_string('fragmentcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>>Memcached</option>
                </optgroup>
            </select>
        </td>
    </tr>
    <?php if (is_network_admin() && !w3_force_master()): ?>
    <tr>
        <th>Network policy:</th>
        <td>
            <?php $this->checkbox_admin('fragmentcache.configuration_sealed'); ?> Apply the settings above to the entire network.</label>
        </td>
    </tr>
    <?php endif; ?>
</table>

<p class="submit">
    <?php echo $this->nonce_field('w3tc'); ?>
    <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
    <input type="submit" name="w3tc_flush_fragmentcache" value="Empty cache"<?php if (! $fragmentcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
</p>
<?php echo $this->postbox_footer(); ?>