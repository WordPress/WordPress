<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
    var minify_templates = {};
    <?php foreach ($templates as $theme_key => $theme_templates): ?>
    minify_templates['<?php echo addslashes($theme_key); ?>'] = {};
    <?php foreach ($theme_templates as $theme_template_key => $theme_template_name): ?>
    minify_templates['<?php echo addslashes($theme_key); ?>']['<?php echo addslashes($theme_template_key); ?>'] = '<?php echo addslashes($theme_template_name); ?>';
    <?php endforeach; ?>
    <?php endforeach; ?>
/*]]>*/</script>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        <?php echo sprintf(__('Minify via %s is currently %s.', 'w3-total-cache'), w3_get_engine_name($this->_config->get_string('minify.engine')) ,'<span class="w3tc-' . ($minify_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>'); ?>
    </p>
    <p>
		<?php
			echo sprintf( __('To rebuild the minify cache use the %s operation.', 'w3-total-cache'),
				$this->nonce_field('w3tc') . '<input type="submit" name="w3tc_flush_minify" value="' . __('empty cache','w3-total-cache') . '"' . disabled($minify_enabled, false, false) . ' class="button" />');
		?>
        <?php if (!$auto): ?>
            <?php _e('Get minify hints using the', 'w3-total-cache'); ?>
            <input type="button" class="button button-minify-recommendations {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('help', 'w3-total-cache'); ?>" />
            <?php _e('wizard.', 'w3-total-cache'); ?>
        <?php endif; ?>
        <?php echo sprintf( __('%s to make existing file modifications visible to visitors with a primed cache.', 'w3-total-cache'),
            '<input type="submit" name="w3tc_flush_browser_cache" value="'. __('Update media query string', 'w3-total-cache') . '"' . disabled(! ($browsercache_enabled && $browsercache_update_media_qs), true, false) . ' class="button" />');
        ?>
    </p>
</form>

<form id="minify_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('General', 'w3-total-cache'), '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('minify.rewrite', !w3_can_check_rules() || $minify_rewrite_disabled) ?> <?php w3_e_config_label('minify.rewrite') ?></label><br />
                    <span class="description"><?php _e('If disabled, <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> embeddings will use GET variables instead of "fancy" links.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('minify.reject.logged') ?> <?php w3_e_config_label('minify.reject.logged') ?></label><br />
                    <span class="description"><?php _e('Authenticated users will not receive minified pages if this option is enabled.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="minify_error_notification"><?php w3_e_config_label('minify.error.notification') ?></label>
                </th>
                <td>
                    <select id="minify_error_notification" name="minify.error.notification"
                        <?php $this->sealing_disabled('minify') ?>>
                        <?php $value = $this->_config_admin->get_string('minify.error.notification'); ?>
                        <option value=""<?php selected($value, ''); ?>><?php _e('Disabled', 'w3-total-cache'); ?></option>
                        <option value="admin"<?php selected($value, 'admin'); ?>><?php _e('Admin Notification', 'w3-total-cache'); ?></option>
                        <option value="email"<?php selected($value, 'email'); ?>><?php _e('Email Notification', 'w3-total-cache'); ?></option>
                        <option value="admin,email"<?php selected($value, 'admin,email'); ?>><?php _e('Both Admin &amp; Email Notification', 'w3-total-cache'); ?></option>
                    </select>
                    <br /><span class="description"><?php _e('Notify when minify cache creation errors occur.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>', 'w3-total-cache'), '', 'html_xml'); ?>
        <table class="form-table">
            <tr>
                <th><?php _e('<acronym title="Hypertext Markup Language">HTML</acronym> minify settings:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('minify.html.enable') ?> <?php w3_e_config_label('minify.html.enable') ?></label><br />
                    <?php $this->checkbox('minify.html.inline.css', false, 'html_') ?> <?php w3_e_config_label('minify.html.inline.css') ?></label><br />
                    <?php $this->checkbox('minify.html.inline.js', false, 'html_') ?> <?php w3_e_config_label('minify.html.inline.js') ?></label><br />
                    <?php $this->checkbox('minify.html.reject.feed', false, 'html_') ?> <?php w3_e_config_label('minify.html.reject.feed') ?></label><br />
                    <?php
                        $html_engine_file = '';

                        switch ($html_engine) {
                            case 'html':
                            case 'htmltidy':
                                $html_engine_file = W3TC_INC_DIR . '/options/minify/' . $html_engine . '.php';
                                break;
                        }

                        if (file_exists($html_engine_file)) {
                            include $html_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="minify_html_comments_ignore"><?php w3_e_config_label('minify.html.comments.ignore') ?></label></th>
                <td>
                    <textarea id="minify_html_comments_ignore" 
                        <?php $this->sealing_disabled('minify') ?>
                        name="minify.html.comments.ignore" class="html_enabled" cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('minify.html.comments.ignore'))); ?></textarea><br />
                    <span class="description"><?php _e('Do not remove comments that contain these terms.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php
                $html_engine_file2 = '';

                switch ($html_engine_file2) {
                    case 'html':
                    case 'htmltidy':
                        $html_engine_file = W3TC_INC_DIR . '/options/minify/' . $html_engine . '2.php';
                        break;
                }

                if (file_exists($html_engine_file2)) {
                    include $html_engine_file2;
                }
            ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('<acronym title="JavaScript">JS</acronym>', 'w3-total-cache'), '', 'js'); ?>
        <table class="form-table">
            <tr>
                <th><?php _e('<acronym title="JavaScript">JS</acronym> minify settings:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('minify.js.enable') ?> <?php w3_e_config_label('minify.js.enable') ?></label><br />
                    <fieldset><legend><?php _e('Operations in areas:', 'w3-total-cache'); ?></legend>
                        <table id="minify_table">
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="options"><?php w3_e_config_label('minify.js.header.embed_type') ?></td>
                            </tr>
                            <tr>
                                <td class="placement">
                                    <?php _e('Before <span class="html-tag">&lt;/head&gt;', 'w3-total-cache'); ?></span>
                                </td>
                                <td class="options">
                                    <?php $this->radio('minify.js.combine.header', false, false, 'js_') ?> <?php _e('Minify', 'w3-total-cache'); ?> </label> <?php $this->radio('minify.js.combine.header', true, false, 'js_') ?> <?php w3_e_config_label('minify.js.combine.header') ?></label>
                                </td>
                                <td class="options">
                                    <select id="js_use_type_header" name="minify.js.header.embed_type" class="js_enabled">
                                        <option value="blocking" <?php selected('blocking' ,$this->_config->get_string('minify.js.header.embed_type')) ?>><?php _e('Default (blocking)', 'w3-total-cache'); ?></option>
                                        <option value="nb-js" <?php selected('nb-js' ,$this->_config->get_string('minify.js.header.embed_type')) ?>><?php _e('Non-blocking using JS', 'w3-total-cache'); ?></option>
                                        <option value="nb-async" <?php selected('nb-async' ,$this->_config->get_string('minify.js.header.embed_type')) ?>><?php _e('Non-blocking using "async"', 'w3-total-cache'); ?></option>
                                        <option value="nb-defer" <?php selected('nb-defer' ,$this->_config->get_string('minify.js.header.embed_type')) ?>><?php _e('Non-blocking using "defer"', 'w3-total-cache'); ?></option>
                                        <?php if (!$auto): ?>
            								<option value="extsrc" <?php selected('extsrc' ,$this->_config->get_string('minify.js.header.embed_type')) ?>><?php _e('Non-blocking using "extsrc"', 'w3-total-cache'); ?></option>
                                        <option value="asyncsrc" <?php selected('asyncsrc' ,$this->_config->get_string('minify.js.header.embed_type')) ?>><?php _e('Non-blocking using "asyncsrc"', 'w3-total-cache'); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            <?php if (!$auto): ?>
                            <tr>
                                <td class="placement"><?php w3_e_config_label('minify.js.body.embed_type') ?></td>
                                <td class="options">
                                    <?php $this->radio('minify.js.combine.body', false, $auto, 'js_') ?> <?php _e('Minify', 'w3-total-cache'); ?> </label> <?php $this->radio('minify.js.combine.body', true) ?> <?php w3_e_config_label('minify.js.combine.body') ?></label>
                                </td>
                                <td class="options">
                                    <select id="js_use_type_body" name="minify.js.body.embed_type" class="js_enabled">
                                        <option value="blocking" <?php selected('blocking' ,$this->_config->get_string('minify.js.body.embed_type')) ?>><?php _e('Default (blocking)', 'w3-total-cache'); ?></option>
                                        <option value="nb-js" <?php selected('nb-js' ,$this->_config->get_string('minify.js.body.embed_type')) ?>><?php _e('Non-blocking using JS', 'w3-total-cache'); ?></option>
                                        <option value="nb-async" <?php selected('nb-async' ,$this->_config->get_string('minify.js.body.embed_type')) ?>><?php _e('Non-blocking using "async"', 'w3-total-cache'); ?></option>
                                        <option value="nb-defer" <?php selected('nb-defer' ,$this->_config->get_string('minify.js.body.embed_type')) ?>><?php _e('Non-blocking using "defer"', 'w3-total-cache'); ?></option>
                                        <option value="extsrc" <?php selected('extsrc' ,$this->_config->get_string('minify.js.body.embed_type')) ?>><?php _e('Non-blocking using "extsrc"', 'w3-total-cache'); ?></option>
                                        <option value="asyncsrc" <?php selected('asyncsrc' ,$this->_config->get_string('minify.js.body.embed_type')) ?>><?php _e('Non-blocking using "asyncsrc"', 'w3-total-cache'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="placement"><?php w3_e_config_label('minify.js.footer.embed_type') ?></td>
                                <td class="options">
                                    <?php $this->radio('minify.js.combine.footer', false, $auto, 'js_') ?> <?php _e('Minify', 'w3-total-cache'); ?> </label> <?php $this->radio('minify.js.combine.footer', true) ?> <?php w3_e_config_label('minify.js.combine.footer') ?></label>
                                </td>
                                <td class="options">
                                    <select id="js_use_type_footer" name="minify.js.footer.embed_type" class="js_enabled">
                                        <option value="blocking" <?php selected('blocking' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>><?php _e('Default (blocking)', 'w3-total-cache'); ?></option>
                                        <option value="nb-js" <?php selected('nb-js' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>><?php _e('Non-blocking using JS', 'w3-total-cache'); ?></option>
                                        <option value="nb-async" <?php selected('nb-async' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>><?php _e('Non-blocking using "async"', 'w3-total-cache'); ?></option>
                                        <option value="nb-defer" <?php selected('nb-defer' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>><?php _e('Non-blocking using "defer"', 'w3-total-cache'); ?></option>
                                        <option value="extsrc" <?php selected('extsrc' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>><?php _e('Non-blocking using "extsrc"', 'w3-total-cache'); ?></option>
                                        <option value="asyncsrc" <?php selected('asyncsrc' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>><?php _e('Non-blocking using "asyncsrc"', 'w3-total-cache'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </fieldset>
                    <?php
                        $js_engine_file = '';

                        switch ($js_engine) {
                            case 'js':
                            case 'yuijs':
                            case 'ccjs':
                                $js_engine_file = W3TC_INC_DIR . '/options/minify/' . $js_engine . '.php';
                                break;
                        }

                        if (file_exists($js_engine_file)) {
                            include $js_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <?php
                $js_engine_file2 = '';

                switch ($js_engine) {
                    case 'js':
                    case 'yuijs':
                    case 'ccjs':
                        $js_engine_file2 = W3TC_INC_DIR . '/options/minify/' . $js_engine . '2.php';
                        break;
                }

                if (file_exists($js_engine_file2)) {
                    include $js_engine_file2;
                }
            ?>
            <?php if (!$auto): ?>
            <tr>
                <th><?php _e('<acronym title="JavaScript">JS</acronym> file management:', 'w3-total-cache'); ?></th>
                <td>
                    <p>
                        <label>
                            <?php _e('Theme:', 'w3-total-cache'); ?>
                            <select id="js_themes" class="js_enabled" name="js_theme"
                                <?php $this->sealing_disabled('minify') ?>>
                                <?php foreach ($themes as $theme_key => $theme_name): ?>
                                <option value="<?php echo esc_attr($theme_key); ?>"<?php selected($theme_key, $js_theme); ?>><?php echo htmlspecialchars($theme_name); ?><?php if ($theme_key == $js_theme): ?> (active)<?php endif; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <br /><span class="description"><?php _e('Files are minified by template. First select the theme to manage, then add scripts used in all templates to the "All Templates" group. Use the menu above to manage scripts unique to a specific template. If necessary drag &amp; drop to resolve dependency issues (due to incorrect order).', 'w3-total-cache'); ?></span>
                    </p>
                    <ul id="js_files" class="minify-files">
                    <?php foreach ($js_groups as $js_theme => $js_templates): if (isset($templates[$js_theme])): ?>
                        <?php $index = 0; foreach ($js_templates as $js_template => $js_locations): ?>
                            <?php foreach ((array) $js_locations as $js_location => $js_config): ?>
                                <?php if (! empty($js_config['files'])): foreach ((array) $js_config['files'] as $js_file): $index++; ?>
                                <li>
                                    <table>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th><?php _e('File URI:', 'w3-total-cache'); ?></th>
                                            <th><?php _e('Template:', 'w3-total-cache'); ?></th>
                                            <th colspan="3"><?php _e('Embed Location:', 'w3-total-cache'); ?></th>
                                        </tr>
                                        <tr>
                                            <td><?php echo $index; ?>.</td>
                                            <td>
                                                <input class="js_enabled" type="text"
                                                     <?php $this->sealing_disabled('minify') ?>
                                                     name="js_files[<?php echo esc_attr($js_theme); ?>][<?php echo esc_attr($js_template); ?>][<?php echo esc_attr($js_location); ?>][]" value="<?php echo esc_attr($js_file); ?>" size="70" />
                                            </td>
                                            <td>
                                                <select class="js_file_template js_enabled" <?php $this->sealing_disabled('minify') ?>>
                                                    <?php foreach ($templates[$js_theme] as $theme_template_key => $theme_template_name): ?>
                                                    <option value="<?php echo esc_attr($theme_template_key); ?>"<?php selected($theme_template_key, $js_template); ?>><?php echo esc_attr($theme_template_name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="js_file_location js_enabled" <?php $this->sealing_disabled('minify') ?>>
                                                    <option value="include" <?php selected($js_location,'include') ?>><?php _e('Embed in &lt;head&gt;', 'w3-total-cache'); ?></option>
                                                    <option value="include-body" <?php selected($js_location, 'include-body') ?>><?php _e('Embed after &lt;body&gt;', 'w3-total-cache'); ?></option>
                                                    <option value="include-footer" <?php selected($js_location, 'include-footer') ?>><?php _e('Embed before &lt;/body&gt;', 'w3-total-cache'); ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="js_file_delete js_enabled button" type="button" value="<?php _e('Delete', 'w3-total-cache'); ?>" />
                                                <input class="js_file_verify js_enabled button" type="button" value="<?php _e('Verify URI', 'w3-total-cache'); ?>" />
                                            </td>
                                        </tr>
                                    </table>
                                </li>
                                <?php endforeach; endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>
                    </ul>
                    <div id="js_files_empty" class="w3tc-empty" style="display: none;"><?php _e('No <acronym title="JavaScript">JS</acronym> files added', 'w3-total-cache'); ?></div>
                    <input id="js_file_add" class="js_enabled button" type="button" value="<?php _e('Add a script', 'w3-total-cache'); ?>" />
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('<acronym title="Cascading Style Sheet">CSS</acronym>', 'w3-total-cache'), '', 'css'); ?>
        <table class="form-table">
            <tr>
                <th><?php _e('<acronym title="Cascading Style Sheet">CSS</acronym> minify settings:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('minify.css.enable') ?> <?php w3_e_config_label('minify.css.enable') ?></label><br />
                    <?php $this->checkbox('minify.css.combine', false, 'css_') ?> <?php w3_e_config_label('minify.css.combine') ?></label><br />
                    <?php
                        $css_engine_file = '';

                        switch ($css_engine) {
                            case 'css':
                            case 'yuicss':
                            case 'csstidy':
                                $css_engine_file = W3TC_INC_DIR . '/options/minify/' . $css_engine . '.php';
                                break;
                        }

                        if (file_exists($css_engine_file)) {
                            include $css_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="minify_css_import"><?php w3_e_config_label('minify.css.imports') ?></label></th>
                <td>
                    <select id="minify_css_import" class="css_enabled" name="minify.css.imports"
                        <?php $this->sealing_disabled('minify') ?>>
                        <?php foreach ($css_imports_values as $css_imports_key => $css_imports_value): ?>
                        <option value="<?php echo esc_attr($css_imports_key); ?>"<?php selected($css_imports, $css_imports_key); ?>><?php echo $css_imports_value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php
                $css_engine_file2 = '';

                switch ($css_engine) {
                    case 'css':
                    case 'yuicss':
                    case 'csstidy':
                        $css_engine_file2 = W3TC_INC_DIR . '/options/minify/' . $css_engine . '2.php';
                        break;
                }

                if (file_exists($css_engine_file2)) {
                    include $css_engine_file2;
                }
            ?>
            <?php if (!$auto): ?>
            <tr>
                <th><?php _e('<acronym title="Cascading Style Sheet">CSS</acronym> file management:', 'w3-total-cache'); ?></th>
                <td>
                    <p>
                        <label>
                            <?php _e('Theme:', 'w3-total-cache'); ?>
                            <select id="css_themes" class="css_enabled" name="css_theme"
                                <?php $this->sealing_disabled('minify') ?>>
                                <?php foreach ($themes as $theme_key => $theme_name): ?>
                                <option value="<?php echo esc_attr($theme_key); ?>"<?php selected($theme_key, $css_theme); ?>><?php echo htmlspecialchars($theme_name); ?><?php if ($theme_key == $css_theme): ?> (active)<?php endif; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <br /><span class="description"><?php _e('Files are minified by template. First select the theme to manage, then add style sheets used in all templates to the "All Templates" group. Use the menu above to manage style sheets unique to a specific template. If necessary drag &amp; drop to resolve dependency issues (due to incorrect order).', 'w3-total-cache'); ?></span>
                    </p>
                    <ul id="css_files" class="minify-files">
                    <?php foreach ($css_groups as $css_theme => $css_templates): if (isset($templates[$css_theme])): ?>
                        <?php $index = 0; foreach ($css_templates as $css_template => $css_locations): ?>
                            <?php foreach ((array) $css_locations as $css_location => $css_config): ?>
                                <?php if (! empty($css_config['files'])): foreach ((array) $css_config['files'] as $css_file): $index++; ?>
                                <li>
                                    <table>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th><?php _e('File URI:', 'w3-total-cache'); ?></th>
                                            <th colspan="2"><?php _e('Template:', 'w3-total-cache'); ?></th>
                                        </tr>
                                        <tr>
                                            <td><?php echo $index; ?>.</td>
                                            <td>
                                                <input class="css_enabled" type="text"
                                                    <?php $this->sealing_disabled('minify') ?>
                                                    name="css_files[<?php echo esc_attr($css_theme); ?>][<?php echo esc_attr($css_template); ?>][<?php echo esc_attr($css_location); ?>][]" value="<?php echo esc_attr($css_file); ?>" size="70" /><br />
                                            </td>
                                            <td>
                                                <select class="css_file_template css_enabled" <?php $this->sealing_disabled('minify') ?>>
                                                <?php foreach ($templates[$css_theme] as $theme_template_key => $theme_template_name): ?>
                                                    <option value="<?php echo esc_attr($theme_template_key); ?>"<?php selected($theme_template_key, $css_template); ?>><?php echo esc_attr($theme_template_name); ?></option>
                                                <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="css_file_delete css_enabled button" type="button" value="<?php _e('Delete', 'w3-total-cache'); ?>" />
                                                <input class="css_file_verify css_enabled button" type="button" value="<?php _e('Verify URI', 'w3-total-cache'); ?>" />
                                            </td>
                                        </tr>
                                    </table>
                                </li>
                                <?php endforeach; endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>
                    </ul>
                    <div id="css_files_empty" class="w3tc-empty" style="display: none;"><?php _e('No <acronym title="Cascading Style Sheet">CSS</acronym> files added', 'w3-total-cache'); ?></div>
                    <input id="css_file_add" class="css_enabled button" type="button" value="<?php _e('Add a style sheet', 'w3-total-cache'); ?>" />
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Advanced', 'w3-total-cache'), '', 'advanced'); ?>
        <table class="form-table">
        <?php if ($auto):?>
        <tr>
            <th colspan="2"><?php $this->checkbox('minify.auto.disable_filename_length_test')?> <?php w3_e_config_label('minify.auto.disable_filename_length_test') ?></label><br />
                <span class="description"><?php _e('Do not automatically attempt to determine the optimal file name length minify files created in auto mode.', 'w3-total-cache')?></span><br />
                <br />
                <label for="minify_auto_filename_length"><?php w3_e_config_label('minify.auto.filename_length') ?></label>
                <input id="minify_auto_filename_length" name="minify.auto.filename_length" type="text" size="4" <?php disabled($this->_config->get_boolean('minify.auto.disable_filename_length_test'), false) ?> value="<?php echo $this->_config->get_integer('minify.auto.filename_length')?>" />
                <br /><span class="description"><?php _e('Change this value to decrease or determine the number of minified files that are generated.
                The more <acronym title="Cascading Style Sheet">CSS</acronym> / <acronym title="JavaScript">JS</acronym> files you have the more files will be generated because their file names are combined in
                the final <acronym title="Uniform Resource Locator">URL</acronym> of the minified file name. File name length is only applicable when minify is in auto mode and file name length testing is enabled. The maximum file name length value is 246.', 'w3-total-cache') ?>
            </span>
            </th>
        </tr>
        <?php endif ?>
        <?php if ($this->_config->get_string('minify.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers"><?php w3_e_config_label('minify.memcached.servers') ?></label></th>
                <td>
                    <input id="memcached_servers" type="text"
                        <?php $this->sealing_disabled('minify') ?>
                        name="minify.memcached.servers" value="<?php echo esc_attr(implode(',', $this->_config->get_array('minify.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}"
                        type="button" value="<?php _e('Test', 'w3-total-cache'); ?>" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description"><?php _e('Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="minify_lifetime"><?php w3_e_config_label('minify.lifetime') ?></label></th>
                <td>
                    <input id="minify_lifetime" type="text" name="minify.lifetime"
                        <?php $this->sealing_disabled('minify') ?>
                        value="<?php echo esc_attr($this->_config->get_integer('minify.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?><br />
                    <span class="description"><?php _e('Specify the interval between download and update of external files in the minify cache. Hint: 6 hours is 21600 seconds. 12 hours is 43200 seconds. 24 hours is 86400 seconds.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_file_gc"><?php w3_e_config_label('minify.file.gc') ?></label></th>
                <td>
                    <input id="minify_file_gc" type="text" name="minify.file.gc"
                        <?php $this->sealing_disabled('minify') ?>
                        value="<?php echo esc_attr( $this->_config->get_integer('minify.file.gc')); ?>" size="8"<?php if ($this->_config->get_string('minify.engine') != 'file'): ?> disabled="disabled"<?php endif; ?> /> <?php _e('seconds', 'w3-total-cache'); ?>
                    <br /><span class="description"><?php _e('If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_uri"><?php w3_e_config_label('minify.reject.uri') ?></label></th>
                <td>
                    <textarea id="minify_reject_uri" name="minify.reject.uri" 
                        <?php $this->sealing_disabled('minify') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('minify.reject.uri'))); ?></textarea><br />
                    <span class="description"><?php _e('Always ignore the specified pages / directories.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_files_js"><?php w3_e_config_label('minify.reject.files.js') ?></label></th>
                <td>
                    <textarea id="minify_reject_files_js" name="minify.reject.files.js"
                        <?php $this->sealing_disabled('minify') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('minify.reject.files.js'))); ?></textarea><br />
                    <span class="description"><?php _e('Always ignore the specified JS files.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_files_css"><?php w3_e_config_label('minify.reject.files.css') ?></label></th>
                <td>
                    <textarea id="minify_reject_files_css" name="minify.reject.files.css"
                        <?php $this->sealing_disabled('minify') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('minify.reject.files.css'))); ?></textarea><br />
                    <span class="description"><?php _e('Always ignore the specified CSS files.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_ua"><?php w3_e_config_label('minify.reject.ua') ?></label></th>
                <td>
                    <textarea id="minify_reject_ua" name="minify.reject.ua"
                        <?php $this->sealing_disabled('minify') ?>
                        cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('minify.reject.ua'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify user agents that will never receive minified content.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($auto): ?>
            <tr>
                <th><label for="minify_cache_files"><?php w3_e_config_label('minify.cache.files') ?></label></th>
                <td>
                    <textarea id="minify_cache_files" name="minify.cache.files"
                        <?php $this->sealing_disabled('minify') ?>
                              cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('minify.cache.files'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify external files/libraries that should be combined.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
        </table>

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
                        <li><?php _e('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "Cascading Style Sheets &amp; JavaScript" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.', 'w3-total-cache'); ?></li>
                        <li><?php _e('The <acronym title="Time to Live">TTL</acronym> of page cache files is set via the "Expires header lifetime" field in the "Cascading Style Sheets &amp; JavaScript" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.', 'w3-total-cache'); ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>