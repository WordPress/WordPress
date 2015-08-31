<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>
<p>
<?php _e('Monitoring via New Relic is currently','w3-total-cache') ?> <span class="w3tc-<?php if ($new_relic_enabled): ?>enabled"><?php _e('enabled', 'w3-total-cache')?><?php else: ?>disabled"><?php _e('disabled', 'w3-total-cache')?><?php endif; ?></span>.
</p>
    <div class="metabox-holder">
        <form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
        <?php echo $this->postbox_header(__('Application Settings', 'w3-total-cache'), '', 'application'); ?>
        <?php if ($new_relic_enabled && $application_settings): ?>
        <table class="form-table">
            <tr>
             <th>
                 <label>Application ID:</label>
             </th>
            <td>
                <?php esc_attr_e($application_settings['application-id'])?>
            </td>
            </tr>
            <tr>
                <th>
                    <label>Application name:</label>
                </th>
                <td>
                    <?php esc_attr_e($application_settings['name'])?>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="alerts-enabled">Alerts enabled:</label>
                </th>
                <td>
                    <input name="alerts-enabled]" type="hidden" value="false" />
                    <input id="alerts-enabled" name="application[alerts_enabled]" type="checkbox" value="1" <?php checked($application_settings['alerts-enabled'], 'true') ?> <?php $this->sealing_disabled('newrelic') ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="app-apdex-t">Application ApDex Threshold:</label>
                </th>
                <td>
                    <input id="app-apdex-t" name="application[app_apdex_t]" type="text" value="<?php echo esc_attr($application_settings['app-apdex-t'])?>" <?php $this->sealing_disabled('newrelic') ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="rum-apdex-t"><acronym title="Real User Monitoring">RUM</acronym> ApDex Threshold:</label>
                </th>
                <td>
                    <input id="rum-apdex-t" name="application[rum_apdex_t]" type="text" value="<?php echo esc_attr($application_settings['rum-apdex-t'])?>" <?php $this->sealing_disabled('newrelic') ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="rum-enabled"><acronym title="Real User Monitoring">RUM</acronym> enabled:</label>
                </th>
                <td>
                    <input name="rum-enabled]" type="hidden" value="false" />
                    <input id="rum-enabled" name="application[rum_enabled]" type="checkbox" value="1" <?php checked($application_settings['rum-enabled'], 'true') ?> <?php $this->sealing_disabled('newrelic') ?>/>
                </td>
            </tr>
        </table>
        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_new_relic" class="w3tc-button-save button-primary" value="Save New Relic settings" />
        </p>
        <?php elseif(empty($application_settings)): ?>
        <p><span class="description"><?php echo sprintf(__('Application settings could not be retrieved. New Relic may not be properly configured, <a href="%s">review the settings</a>.', 'w3-total-cache'),network_admin_url('admin.php?page=w3tc_general#monitoring')) ?></span></p>
        <?php else: ?>
        <p><?php _e('Application settings are only visible when New Relic is enabled', 'w3-total-cache', 'w3-total-cache') ?></p>
        <?php endif; ?>
        <?php echo $this->postbox_footer(); ?>
        </form>
        <form action="admin.php?page=<?php echo $this->_page; ?>" method="post">

        <?php echo $this->postbox_header(__('Dashboard Settings', 'w3-total-cache'), '', 'dashboard'); ?>
        <table class="form-table">
            <tr>
                <th><label for="newrelic_cache_time"><?php w3_e_config_label('newrelic.cache_time') ?></label></th>
                <td><input id="newrelic_cache_time" name="newrelic.cache_time" type="text" value="<?php echo esc_attr($this->_config->get_integer('newrelic.cache_time', 5))?>" />
                    <p><span class="description">
                        <?php _e('How many minutes data retrieved from New Relic should be stored. Minimum is 1 minute.', 'w3-total-cache') ?>
                        </span>
                    </p>
                </td>
            </tr>
        </table>
            <p class="submit">
                <?php echo $this->nonce_field('w3tc'); ?>
                <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save settings" />
            </p>
            <?php echo $this->postbox_footer(); ?>
        <?php echo $this->postbox_header(__('Behavior Settings', 'w3-total-cache'), '', 'behavior'); ?>
        <table  class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('newrelic.accept.logged_roles') ?> <?php w3_e_config_label('newrelic.accept.logged_roles') ?></label><br />
                    <span class="description"><?php w3_e_config_label('newrelic.accept.roles') ?></span>

                    <div id="newrelic_accept_roles">
                        <?php $saved_roles = $this->_config->get_array('newrelic.accept.roles'); ?>
                        <input type="hidden" name="newrelic.accept.roles" value="" /><br />
                        <?php foreach( get_editable_roles() as $role_name => $role_data ) : ?>
                        <input type="checkbox" name="newrelic.accept.roles[]" value="<?php echo $role_name ?>"
                            <?php checked( in_array( $role_name, $saved_roles ) ) ?> id="role_<?php echo $role_name ?>" <?php $this->sealing_disabled('newrelic') ?>/>
                        <label for="role_<?php echo $role_name ?>"><?php echo $role_data['name'] ?></label>
                        <?php endforeach; ?>
                    </div>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="newrelic_include_rum"><?php w3_e_config_label('newrelic.include_rum') ?></label>
                </th>
                <td>
                    <input name="newrelic.include_rum" type="hidden" value="0" />
                    <input id="newrelic_include_rum" name="newrelic.include_rum" type="checkbox" value="1" <?php checked($this->_config->get_boolean('newrelic.include_rum')) ?> />
                    <p><span class="description">
                    <?php _e('This enables inclusion of <acronym title="Real User Monitoring">RUM</acronym> when using Page Cache together with Browser Cache gzip or when using Page Cache with Disc: Enhanced', 'w3-total-cache')?>
                    </span>
                    </p>
                </td>
            </tr>
            <?php if(w3_is_network() && w3_get_blog_id() == 0): ?>
            <tr>
                <th><label for="newrelic_appname_prefix"><?php w3_e_config_label('newrelic.appname_prefix') ?></label></th>
                <td><input id="newrelic_appname_prefix" name="newrelic.appname_prefix" type="text" value="<?php echo esc_attr($this->_config->get_string('newrelic.appname_prefix')) ?>" /></td>
            </tr>
            <tr>
                <th><label for="newrelic_merge_with_network"><?php w3_e_config_label('newrelic.merge_with_network') ?></label></th>
                <td>
                    <input name="newrelic.merge_with_network" type="hidden" value="0" />
                    <input id="newrelic_merge_with_network" name="newrelic.merge_with_network" type="checkbox" value="1" <?php checked($this->_config->get_boolean('newrelic.merge_with_network')) ?> />
                <p><span class="description">
                    <?php _e('This means that the data collected for sites in the network will be included in the main network sites data on New Relic.', 'w3-total-cache')?>
                    </span>
                </p>
                </td>
            </tr>
            <?php endif ?>
            <tr>
                <th><label for="newrelic_use_php_function"><?php w3_e_config_label('newrelic.use_php_function') ?></label></th>
                <td>
                    <?php if (w3_is_network()): ?>
                    <input id="newrelic_use_php_function" name="newrelic.use_php_function" type="checkbox" value="1" checked="checked" disabled="disabled" />
                        <p><span class="description">
                            <?php _e('This is required when using New Relic on a network install to set the proper names for sites.', 'w3-total-cache') ?></span></p>
                    <?php else: ?>
                    <input name="newrelic.use_php_function" type="hidden" value="0" />
                    <input id="newrelic_use_php_function" name="newrelic.use_php_function" type="checkbox" value="1" <?php checked($this->_config->get_boolean('newrelic.use_php_function')) ?>/>
                        <p><span class="description">
                           <?php _e('Enable this to dynamically set proper application name. (See New Relic <a href="https://newrelic.com/docs/php/per-directory-settings">Per-directory settings</a> for other methods.', 'w3-total-cache') ?></span>
                        </p>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <th><label for="newrelic_enable_xmit"><?php w3_e_config_label('newrelic.enable_xmit') ?></label></th>
                <td><input name="" type="hidden" value="0" />
                <input id="newrelic_enable_xmit" name="newrelic.enable_xmit" type="checkbox" value="1" <?php checked($this->_config->get_boolean('newrelic.enable_xmit')) ?> <?php $this->sealing_disabled('newrelic') ?>/>
                    <p><span class="description"><?php _e(sprintf('Enable this if you want to record the metric and transaction data (until the name is changed using PHP function), specify a value of true for this argument to make the agent send the transaction to the daemon. There is a slight performance impact as it takes a few milliseconds for the agent to dump its data. <em>From %s</em>',
                        '<a href="https://newrelic.com/docs/php/the-php-api">New Relic PHP API doc</a>')
                        , 'w3-total-cache')?></span></p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>
        </form>
    </div>
    <?php if ($view_metric):?>
    <table>
    <?php foreach($metric_names as $metric):?>
        <tr>
            <th style="text-align: right"><strong><?php echo $metric->name ?></strong></th>
            <td><?php echo implode(', ', $metric->fields) ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php endif; ?>
<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
