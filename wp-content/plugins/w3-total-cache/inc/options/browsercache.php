<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p> 
        <?php echo sprintf(__('Browser caching is currently %s.', 'w3-total-cache'), '<span class="w3tc-' . ($browsercache_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>'); ?>
    </p>
    <p>
        <?php echo $this->nonce_field('w3tc'); ?>
        	
        	<?php echo sprintf(
        				__('%sUpdate media query string%s to make existing file modifications visible to visitors with a primed cache', 'w3-total-cache'),
        				'<input type="submit" name="w3tc_flush_browser_cache" value="',
        				'" ' . disabled(!($browsercache_enabled && $browsercache_update_media_qs), true, false) . ' class="button" />');
        	?>    
    </p>
</form>
<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('General', 'w3-total-cache'), '', 'general'); ?>
        <p><?php _e('Specify global browser cache policy.', 'w3-total-cache') ?></p>
        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <label>
                    <input id="browsercache_last_modified" type="checkbox" name="expires"
                        <?php $this->sealing_disabled('browsercache') ?>
                           value="1"<?php checked($browsercache_last_modified, true); ?> /> <?php _e('Set Last-Modified header', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Set the Last-Modified header to enable 304 Not Modified response.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <label>
                        <input id="browsercache_expires" type="checkbox" name="expires"
                            <?php $this->sealing_disabled('browsercache') ?>
                            value="1"<?php checked($browsercache_expires && $this->_config->get_string('cdn.engine') != 'cf2', true); ?> <?php disabled($this->_config->get_string('cdn.engine') == 'cf2' ) ?> /> <?php _e('Set expires header', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Set the expires header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_cache_control" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?> name="cache_control" value="1"<?php checked($browsercache_cache_control, true); ?> /> <?php _e('Set cache control header', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Set pragma and cache-control headers to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_etag" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="etag" value="1"<?php checked($browsercache_etag, true); ?> /> <?php _e('Set entity tag (eTag)', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Set the Etag header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_w3tc" type="checkbox" name="w3tc"
                        <?php $this->sealing_disabled('browsercache') ?> value="1" <?php checked($browsercache_w3tc, true); ?> /> <?php _e('Set W3 Total Cache header', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Set this header to assist in identifying optimized files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_compression" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="compression"<?php checked($browsercache_compression, true); ?> value="1" /> <?php _e('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Reduce the download time for text-based files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_replace" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="replace" value="1"<?php checked($browsercache_replace, true); ?> /> <?php _e('Prevent caching of objects after settings change', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Whenever settings are changed, a new query string will be generated and appended to objects allowing the new policy to be applied.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th><label for="browsercache_replace_exceptions"><?php w3_e_config_label('browsercache.replace.exceptions') ?></label></th>
                <td>
                    <textarea id="browsercache_replace_exceptions"
                        <?php $this->sealing_disabled('browsercache') ?>
                              name="browsercache.replace.exceptions" cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('browsercache.replace.exceptions'))); ?></textarea><br />
                    <span class="description"><?php _e('Do not add the prevent caching query string to the specified files. Supports regular expressions.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_nocookies" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="nocookies" value="1"<?php checked($browsercache_nocookies, true); ?> /> <?php _e("Don't set cookies for static files", 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Removes Set-Cookie header for responses.'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.no404wp', !w3_can_check_rules()) ?> <?php w3_e_config_label('browsercache.no404wp') ?></label>
                    <br /><span class="description"><?php _e('Reduce server load by allowing the web server to handle 404 (not found) errors for static files (images etc).', 'w3-total-cache'); ?></span>
                    <br /><span class="description"><?php _e('If enabled - tou may get 404 File Not Found response for some files generated on-the-fly by WordPress plugins. You may add those file URIs to 404 error exception list below to avoid that.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th><label for="browsercache_no404wp_exceptions"><?php w3_e_config_label('browsercache.no404wp.exceptions') ?></label></th>
                <td>
                    <textarea id="browsercache_no404wp_exceptions"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="browsercache.no404wp.exceptions" cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('browsercache.no404wp.exceptions'))); ?></textarea><br />
                    <span class="description"><?php _e('Never process 404 (not found) events for the specified files.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('<acronym title="Cascading Style Sheet">CSS</acronym> &amp; <acronym title="JavaScript">JS</acronym>', 'w3-total-cache'), '', 'css_js'); ?>
        <p><?php _e('Specify browser cache policy for Cascading Style Sheets and JavaScript files.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.last_modified') ?> <?php w3_e_config_label('browsercache.cssjs.last_modified') ?></label>
                    <br /><span class="description"><?php _e('Set the Last-Modified header to enable 304 Not Modified response.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.expires', $this->_config->get_string('cdn.engine') == 'cf2') ?> <?php w3_e_config_label('browsercache.cssjs.expires') ?></label>
                    <br /><span class="description"><?php _e('Set the expires header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_cssjs_lifetime"><?php w3_e_config_label('browsercache.cssjs.lifetime') ?></label>
                </th>
                <td>
                    <input id="browsercache_cssjs_lifetime" type="text"
                       <?php $this->sealing_disabled('browsercache') ?>
                       name="browsercache.cssjs.lifetime" value="<?php echo esc_attr($this->_config->get_integer('browsercache.cssjs.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.cache.control') ?> <?php w3_e_config_label('browsercache.cssjs.cache.control') ?></label>
                    <br /><span class="description"><?php _e('Set pragma and cache-control headers to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_cssjs_cache_policy"><?php w3_e_config_label('browsercache.cssjs.cache.policy') ?></label>
                </th>
                <td>
                    <select id="browsercache_cssjs_cache_policy"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="browsercache.cssjs.cache.policy">
                        <?php $value = $this->_config->get_string('browsercache.cssjs.cache.policy'); ?>
                        <option value="cache"<?php selected($value, 'cache'); ?>>cache ("public")</option>
                        <option value="cache_public_maxage"<?php selected($value, 'cache_public_maxage'); ?>><?php _e('cache with max-age ("public, max-age=EXPIRES_SECONDS")', 'w3-total-cache'); ?></option>
                        <option value="cache_validation"<?php selected($value, 'cache_validation'); ?>><?php _e('cache with validation ("public, must-revalidate, proxy-revalidate"', 'w3-total-cache'); ?></option>
                        <option value="cache_maxage"<?php selected($value, 'cache_maxage'); ?>><?php _e('cache with max-age and validation ("max-age=EXPIRES_SECONDS, public, must-revalidate, proxy-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="cache_noproxy"<?php selected($value, 'cache_noproxy'); ?>><?php _e('cache without proxy ("private, must-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="no_cache"<?php selected($value, 'no_cache'); ?>><?php _e('no-cache ("max-age=0, private, no-store, no-cache, must-revalidate"', 'w3-total-cache'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.etag') ?> <?php w3_e_config_label('browsercache.cssjs.etag') ?></label>
                    <br /><span class="description"><?php _e('Set the Etag header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.w3tc') ?> <?php w3_e_config_label('browsercache.cssjs.w3tc') ?></label>
                    <br /><span class="description"><?php _e('Set this header to assist in identifying optimized files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.compression') ?> <?php w3_e_config_label('browsercache.cssjs.compression') ?>  </label>
                    <br /><span class="description"><?php _e('Reduce the download time for text-based files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.replace') ?> <?php w3_e_config_label('browsercache.cssjs.replace') ?></label>
                    <br /><span class="description"><?php _e('Whenever settings are changed, a new query string will be generated and appended to objects allowing the new policy to be applied.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.nocookies') ?> <?php w3_e_config_label('browsercache.cssjs.nocookies') ?></label>
                    <br /><span class="description"><?php _e('Removes Set-Cookie header for responses.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="Extensible Markup Language">XML</acronym>', 'w3-total-cache'), '', 'html_xml'); ?>
        <p><?php _e('Specify browser cache policy for posts, pages, feeds and text-based files.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.last_modified') ?> <?php w3_e_config_label('browsercache.html.last_modified') ?></label>
                    <br /><span class="description"><?php _e('Set the Last-Modified header to enable 304 Not Modified response.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.expires', $this->_config->get_string('cdn.engine') == 'cf2') ?> <?php w3_e_config_label('browsercache.html.expires') ?></label>
                    <br /><span class="description"><?php _e('Set the expires header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th style="width: 250px;">
                    <label for="browsercache_html_lifetime"><?php w3_e_config_label('browsercache.html.lifetime') ?></label>
                </th>
                <td>
                    <input id="browsercache_html_lifetime" type="text" 
                       name="browsercache.html.lifetime"
                       <?php $this->sealing_disabled('browsercache') ?>
                       value="<?php echo esc_attr($this->_config->get_integer('browsercache.html.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.cache.control') ?> <?php w3_e_config_label('browsercache.html.cache.control') ?></label>
                    <br /><span class="description"><?php _e('Set pragma and cache-control headers to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_html_cache_policy"><?php w3_e_config_label('browsercache.html.cache.policy') ?></label>
                </th>
                <td>
                    <select id="browsercache_html_cache_policy" name="browsercache.html.cache.policy"
                        <?php $this->sealing_disabled('browsercache') ?>>
                        <?php $value = $this->_config->get_string('browsercache.html.cache.policy'); ?>
                        <option value="cache"<?php selected($value, 'cache'); ?>>cache ("public")</option>
                        <option value="cache_public_maxage"<?php selected($value, 'cache_public_maxage'); ?>><?php _e('cache with max-age ("public, max-age=EXPIRES_SECONDS")', 'w3-total-cache'); ?></option>
                        <option value="cache_validation"<?php selected($value, 'cache_validation'); ?>><?php _e('cache with validation ("public, must-revalidate, proxy-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="cache_maxage"<?php selected($value, 'cache_maxage'); ?>><?php _e('cache with max-age and validation ("max-age=EXPIRES_SECONDS, public, must-revalidate, proxy-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="cache_noproxy"<?php selected($value, 'cache_noproxy'); ?>><?php _e('cache without proxy ("private, must-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="no_cache"<?php selected($value, 'no_cache'); ?>><?php _e('no-cache ("max-age=0, private, no-store, no-cache, must-revalidate")', 'w3-total-cache'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.etag') ?> <?php w3_e_config_label('browsercache.html.etag') ?></label>
                    <br /><span class="description"><?php _e('Set the Etag header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.w3tc') ?> <?php w3_e_config_label('browsercache.html.w3tc') ?></label>
                    <br /><span class="description"><?php _e('Set this header to assist in identifying optimized files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.compression') ?> <?php w3_e_config_label('browsercache.html.compression') ?></label>
                    <br /><span class="description"><?php _e('Reduce the download time for text-based files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Media &amp; Other Files', 'w3-total-cache'), '', 'media'); ?>
        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.last_modified') ?> <?php w3_e_config_label('browsercache.other.last_modified') ?></label>
                    <br /><span class="description"><?php _e('Set the Last-Modified header to enable 304 Not Modified response.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.expires', $this->_config->get_string('cdn.engine') == 'cf2') ?> <?php w3_e_config_label('browsercache.other.expires') ?></label>
                    <br /><span class="description"><?php _e('Set the expires header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th style="width: 250px;">
                    <label for="browsercache_other_lifetime"><?php w3_e_config_label('browsercache.other.lifetime') ?></label>
                </th>
                <td>
                    <input id="browsercache_other_lifetime" type="text"
                       <?php $this->sealing_disabled('browsercache') ?>
                       name="browsercache.other.lifetime" value="<?php echo esc_attr($this->_config->get_integer('browsercache.other.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.cache.control') ?> <?php w3_e_config_label('browsercache.other.cache.control') ?></label>
                    <br /><span class="description"><?php _e('Set pragma and cache-control headers to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_other_cache_policy"><?php w3_e_config_label('browsercache.other.cache.policy') ?></label>
                </th>
                <td>
                    <select id="browsercache_other_cache_policy" 
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="browsercache.other.cache.policy">
                        <?php $value = $this->_config->get_string('browsercache.other.cache.policy'); ?>
                        <option value="cache"<?php selected($value, 'cache'); ?>><?php _e('cache ("public")'); ?></option>
                        <option value="cache_public_maxage"<?php selected($value, 'cache_public_maxage'); ?>><?php _e('cache with max-age ("public, max-age=EXPIRES_SECONDS")', 'w3-total-cache'); ?></option>
                        <option value="cache_validation"<?php selected($value, 'cache_validation'); ?>><?php _e('cache with validation ("public, must-revalidate, proxy-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="cache_maxage"<?php selected($value, 'cache_maxage'); ?>><?php _e('cache with max-age and validation ("max-age=EXPIRES_SECONDS, public, must-revalidate, proxy-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="cache_noproxy"<?php selected($value, 'cache_noproxy'); ?>><?php _e('cache without proxy ("private, must-revalidate")', 'w3-total-cache'); ?></option>
                        <option value="no_cache"<?php selected($value, 'no_cache'); ?>><?php _e('no-cache ("max-age=0, private, no-store, no-cache, must-revalidate")', 'w3-total-cache'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.etag') ?> <?php w3_e_config_label('browsercache.other.etag') ?></label>
                    <br /><span class="description"><?php _e('Set the Etag header to encourage browser caching of files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.w3tc') ?> <?php w3_e_config_label('browsercache.other.w3tc') ?></label>
                    <br /><span class="description"><?php _e('Set this header to assist in identifying optimized files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.compression') ?> <?php w3_e_config_label('browsercache.other.compression') ?>
                    <br /><span class="description"><?php _e('Reduce the download time for text-based files.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.replace') ?> <?php w3_e_config_label('browsercache.other.replace') ?></label>
                    <br /><span class="description"><?php _e('Whenever settings are changed, a new query string will be generated and appended to objects allowing the new policy to be applied.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.nocookies') ?> <?php w3_e_config_label('browsercache.other.nocookies') ?></label>
                    <br /><span class="description"><?php _e('Removes Set-Cookie header for responses.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
