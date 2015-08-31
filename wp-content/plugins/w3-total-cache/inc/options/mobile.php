<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
var mobile_themes = {};
<?php foreach ($themes as $theme_key => $theme_name): ?>
mobile_themes['<?php echo addslashes($theme_key); ?>'] = '<?php echo addslashes($theme_name); ?>';
<?php endforeach; ?>
/*]]>*/</script>

<p>
    <?php _e('User agent group support is always <span class="w3tc-enabled">enabled</span>.', 'w3-total-cache'); ?>
</p>

<form id="mobile_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('Manage User Agent Groups', 'w3-total-cache'), '', 'manage'); ?>
        <p>
            <input id="mobile_add" type="button" class="button" value="<?php _e('Create a group', 'w3-total-cache'); ?>" /> <?php _e('of user agents by specifying names in the user agents field. Assign a set of user agents to use a specific theme, redirect them to another domain or if an existing mobile plugin is active, create user agent groups to ensure that a unique cache is created for each user agent group. Drag and drop groups into order (if needed) to determine their priority (top -&gt; down).', 'w3-total-cache'); ?>
        </p>

        <ul id="mobile_groups">
            <?php $index = 0; foreach ($groups as $group => $group_config): $index++; ?>
            <li id="mobile_group_<?php echo esc_attr($group); ?>">
                <table class="form-table">
                    <tr>
                        <th>
                            <?php _e('Group name:', 'w3-total-cache'); ?>
                        </th>
                        <td>
                            <span class="mobile_group_number"><?php echo $index; ?>.</span> <span class="mobile_group"><?php echo htmlspecialchars($group); ?></span> <input type="button" class="button mobile_delete" value="Delete group" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="mobile_groups_<?php echo esc_attr($group); ?>_enabled"><?php _e('Enabled:', 'w3-total-cache'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="mobile_groups[<?php echo esc_attr($group); ?>][enabled]" value="0" />
                            <input id="mobile_groups_<?php echo esc_attr($group); ?>_enabled" type="checkbox" name="mobile_groups[<?php echo esc_attr($group); ?>][enabled]" value="1"<?php checked($group_config['enabled'], true); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="mobile_groups_<?php echo esc_attr($group); ?>_theme"><?php _e('Theme:', 'w3-total-cache'); ?></label>
                        </th>
                        <td>
                            <select id="mobile_groups_<?php echo esc_attr($group); ?>_theme" name="mobile_groups[<?php echo esc_attr($group); ?>][theme]">
                                <option value="">-- Pass-through --</option>
                                <?php foreach ($themes as $theme_key => $theme_name): ?>
                                <option value="<?php echo esc_attr($theme_key); ?>"<?php selected($theme_key, $group_config['theme']); ?>><?php echo htmlspecialchars($theme_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br /><span class="description"><?php _e('Assign this group of user agents to a specific theme. Selecting "Pass-through" allows any plugin(s) (e.g. mobile plugins) to properly handle requests for these user agents. If the "redirect users to" field is not empty, this setting is ignored.', 'w3-total-cache'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="mobile_groups_<?php echo esc_attr($group); ?>_redirect"><?php _e('Redirect users to:', 'w3-total-cache') ?></label>
                        </th>
                        <td>
                            <input id="mobile_groups_<?php echo esc_attr($group); ?>_redirect" type="text" name="mobile_groups[<?php echo esc_attr($group); ?>][redirect]" value="<?php echo esc_attr($group_config['redirect']); ?>" size="60" />
                            <br /><span class="description"><?php _e('A 302 redirect is used to send this group of users to another hostname (domain); recommended if a 3rd party service provides a mobile version of your site.', 'w3-total-cache'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="mobile_groups_<?php echo esc_attr($group); ?>_agents"><?php _e('User agents:', 'w3-total-cache'); ?></label>
                        </th>
                        <td>
                            <textarea id="mobile_groups_<?php echo esc_attr($group); ?>_agents" name="mobile_groups[<?php echo esc_attr($group); ?>][agents]" rows="10" cols="50"><?php echo esc_textarea(implode("\r\n", (array) $group_config['agents'])); ?></textarea>
                            <br /><span class="description"><?php _e('Specify the user agents for this group. Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.', 'w3-total-cache'); ?></span>
                        </td>
                    </tr>
                </table>
            </li>
            <?php endforeach; ?>
        </ul>
        <div id="mobile_groups_empty" style="display: none;"><?php _e('No groups added. All user agents recieve the same page and minify cache results.', 'w3-total-cache') ?></div>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Note(s):', 'w3-total-cache'), '', 'notes'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li><?php _e('Enabling even a single user agent group will set a cookie called "w3tc_referrer." It is used to ensure a consistent user experience across page views. Make sure any reverse proxy servers etc respect this cookie for proper operation.', 'w3-total-cache'); ?></li>
                        <li><?php _e('Per the above, make sure that visitors are notified about the cookie as per any regulations in your market.', 'w3-total-cache'); ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
