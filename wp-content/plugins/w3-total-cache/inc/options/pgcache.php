<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
    	<?php echo 
    		sprintf( __('Page caching via %1$s is currently %2$s', 'w3-total-cache'), 
    		'<strong>'.w3_get_engine_name($this->_config->get_string('pgcache.engine')).'</strong>', 
    		'<span class="w3tc-'.($pgcache_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>.'
    		); 
    	?>
    </p>
    <p>
		<?php
			echo sprintf( __('To rebuild the page cache use the %s operation', 'w3-total-cache'), 
			$this->nonce_field('w3tc') . '<input type="submit" name="w3tc_flush_pgcache" value="empty cache"' . disabled($pgcache_enabled, false, false) . ' class="button" />'
			);
		?>
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('General', 'w3-total-cache'), '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.home'); ?> <?php w3_e_config_label('pgcache.cache.home') ?></label><br />
                    <span class="description"><?php _e('For many blogs this is your most visited page, it is recommended that you cache it.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php if (get_option( 'show_on_front') != 'posts'): ?>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.reject.front_page'); ?> <?php w3_e_config_label('pgcache.reject.front_page') ?></label><br />
                    <span class="description"><?php _e('By default the front page is cached when using static front page in reading settings.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.feed') ?> <?php w3_e_config_label('pgcache.cache.feed') ?></label><br />
                    <span class="description"><?php _e('Even if using a feed proxy service (like <a href="http://en.wikipedia.org/wiki/FeedBurner" target="_blank">FeedBurner</a>), enabling this option is still recommended.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.ssl') ?> <?php w3_e_config_label('pgcache.cache.ssl') ?></label><br />
                    <span class="description"><?php _e('Cache <acronym title="Secure Socket Layer">SSL</acronym> requests (uniquely) for improved performance.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.query', ($this->_config->get_string('pgcache.engine') == 'file_generic')) ?> <?php w3_e_config_label('pgcache.cache.query', 'settings') ?></label><br />
                    <span class="description"><?php _e('Search result (and similar) pages will be cached if enabled.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.404') ?> <?php w3_e_config_label('pgcache.cache.404'); ?></label><br />
                    <span class="description"><?php _e('Reduce server load by caching 404 pages. If the disk enhanced method of disk caching is used, 404 pages will be returned with a 200 response code. Use at your own risk.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.check.domain', $disable_check_domain) ?> <?php w3_e_config_label('pgcache.check.domain') ?></label><br />
                    <span class="description"><?php _e('Cache only requests with the same <acronym title="Uniform Resource Indicator">URL</acronym> as the site\'s <a href="options-general.php">site address</a>.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.reject.logged') ?> <?php w3_e_config_label('pgcache.reject.logged') ?></label><br />
                    <span class="description"><?php _e('Unauthenticated users may view a cached version of the last authenticated user\'s view of a given page. Disabling this option is not recommended.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.reject.logged_roles') ?> <?php w3_e_config_label('pgcache.reject.logged_roles') ?></label><br />
                    <span class="description"><?php _e('Select user roles that should not receive cached pages:', 'w3-total-cache'); ?></span>
                    
                    <div id="pgcache_reject_roles">
                        <?php $saved_roles = $this->_config->get_array('pgcache.reject.roles'); ?>
                        <input type="hidden" name="pgcache.reject.roles" value="" /><br />
                        <?php foreach( get_editable_roles() as $role_name => $role_data ) : ?>
                            <input type="checkbox" name="pgcache.reject.roles[]" value="<?php echo $role_name ?>" <?php checked( in_array( $role_name, $saved_roles ) ) ?> id="role_<?php echo $role_name ?>" />
                            <label for="role_<?php echo $role_name ?>"><?php echo $role_data['name'] ?></label>
                        <?php endforeach; ?>
                    </div>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Cache Preload', 'w3-total-cache'), '', 'cache_preload'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('pgcache.prime.enabled') ?> <?php w3_e_config_label('pgcache.prime.enabled') ?></label><br />
                </th>
            </tr>
            <tr>
                <th><label for="pgcache_prime_interval"><?php w3_e_config_label('pgcache.prime.interval') ?></label></th>
                <td>
                    <input id="pgcache_prime_interval" type="text" name="pgcache.prime.interval" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo esc_attr($this->_config->get_integer('pgcache.prime.interval')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?><br />
                    <span class="description"><?php _e('The number of seconds to wait before creating another set of cached pages.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_prime_limit"><?php w3_e_config_label('pgcache.prime.limit') ?></label></th>
                <td>
                    <input id="pgcache_prime_limit" type="text" name="pgcache.prime.limit" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo esc_attr( $this->_config->get_integer('pgcache.prime.limit')); ?>" size="8" /><br />
                    <span class="description"><?php _e('Limit the number of pages to create per batch. Fewer pages may be better for under-powered servers.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_prime_sitemap"><?php w3_e_config_label('pgcache.prime.sitemap') ?></label></th>
                <td>
                    <input id="pgcache_prime_sitemap" type="text" name="pgcache.prime.sitemap" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo esc_attr($this->_config->get_string('pgcache.prime.sitemap')); ?>" size="100" /><br />
                    <span class="description"><?php _e('A <a href="http://www.xml-sitemaps.com/validate-xml-sitemap.html" target="_blank">compliant</a> sitemap can be used to specify the pages to maintain in the primed cache. Pages will be cached according to the priorities specified in the <acronym title="Extensible Markup Language">XML</acronym> file. Due to its completeness and integrations, <a href="http://wordpress.org/extend/plugins/wordpress-seo/" target="_blank">WordPress SEO</a> is recommended for use with this feature.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('pgcache.prime.post.enabled') ?> <?php w3_e_config_label('pgcache.prime.post.enabled') ?></label><br />                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php
            $modules = array();
            if ($pgcache_enabled) $modules[] = 'Page Cache';
            if ($varnish_enabled) $modules [] = 'Varnish';
            if ($cdn_mirror_purge_enabled) $modules[] = 'CDN';
        echo $this->postbox_header(__('Purge Policy: ', 'w3-total-cache') . implode(', ', $modules), '', 'purge_policy'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php _e('Specify the pages and feeds to purge when posts are created, edited, or comments posted. The defaults are recommended because additional options may reduce server performance:', 'w3-total-cache' ) ?>

                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <th style="padding-left: 0;">
                                <?php if (get_option('show_on_front') != 'posts'): ?>
                                <?php $this->checkbox('pgcache.purge.front_page') ?> <?php w3_e_config_label('pgcache.purge.front_page') ?></label><br />
                                <?php endif; ?>
                                <?php $this->checkbox('pgcache.purge.home') ?>  <?php w3_e_config_label('pgcache.purge.home') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.post') ?> <?php w3_e_config_label('pgcache.purge.post') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.feed.blog') ?> <?php w3_e_config_label('pgcache.purge.feed.blog') ?></label><br />
                            </th>
                            <th>
                                <?php $this->checkbox('pgcache.purge.comments') ?> <?php w3_e_config_label('pgcache.purge.comments') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.author') ?> <?php w3_e_config_label('pgcache.purge.author') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.terms') ?> <?php w3_e_config_label('pgcache.purge.terms') ?></label><br />
                            </th>
                            <th>
                                <?php $this->checkbox('pgcache.purge.feed.comments') ?> <?php w3_e_config_label('pgcache.purge.feed.comments') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.feed.author') ?> <?php w3_e_config_label('pgcache.purge.feed.author') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.feed.terms') ?> <?php w3_e_config_label('pgcache.purge.feed.terms') ?></label>
                            </th>
                            <th>
                                <?php $this->checkbox('pgcache.purge.archive.daily') ?> <?php w3_e_config_label('pgcache.purge.archive.daily') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.archive.monthly') ?> <?php w3_e_config_label('pgcache.purge.archive.monthly') ?></label><br />
                                <?php $this->checkbox('pgcache.purge.archive.yearly') ?> <?php w3_e_config_label('pgcache.purge.archive.yearly') ?></label><br />
                            </th>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php w3_e_config_label('pgcache.purge.feed.types') ?><br />
                    <input type="hidden" name="pgcache.purge.feed.types" value="" />
                    <?php foreach($feeds as $feed): ?>
                        <label>
                            <input type="checkbox" name="pgcache.purge.feed.types[]" 
                                value="<?php echo $feed; ?>"
                                <?php checked(in_array($feed, $this->_config->get_array('pgcache.purge.feed.types')), true); ?> 
                                <?php $this->sealing_disabled('pgcache') ?>
                                />
                        <?php echo $feed; ?>
                        <?php if ($feed == $default_feed): ?>(default)<?php endif; ?></label><br />
                    <?php endforeach; ?>
                </th>
            </tr>
            <tr>
                <th><label for="pgcache_purge_postpages_limit"><?php w3_e_config_label('pgcache.purge.postpages_limit') ?></label></th>
                <td>
                    <input id="pgcache_purge_postpages_limit" name="pgcache.purge.postpages_limit" <?php $this->sealing_disabled('pgcache') ?> type="text" value="<?php echo esc_attr($this->_config->get_integer('pgcache.purge.postpages_limit')); ?>" /><br />
                    <span class="description"><?php _e('Specify number of pages that lists posts (archive etc) that should be purged on post updates etc, i.e example.com/ ... example.com/page/5. <br />0 means all pages that lists posts are purged, i.e example.com/page/2 ... .', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_purge_pages"><?php w3_e_config_label('pgcache.purge.pages') ?></label></th>
                <td>
                    <textarea id="pgcache_purge_pages" name="pgcache.purge.pages"
                        <?php $this->sealing_disabled('pgcache') ?>
                              cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.purge.pages'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify additional pages to purge. Including parent page in path. Ex: parent/posts.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_purge_sitemap_regex"><?php w3_e_config_label('pgcache.purge.sitemap_regex') ?></label></th>
                <td>
                    <input id="pgcache_purge_sitemap_regex" name="pgcache.purge.sitemap_regex" <?php $this->sealing_disabled('pgcache') ?> value="<?php echo esc_attr($this->_config->get_string('pgcache.purge.sitemap_regex')) ?>" type="text" /><br />
                    <span class="description"><?php _e('Specify a regular expression that matches your sitemaps.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Advanced', 'w3-total-cache'), '', 'advanced'); ?>
        <table class="form-table">
            <tr>
                <th><label for="pgcache_late_init"><?php _e('Late initialization:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input type="hidden" name="pgcache.late_init" value="0" />
                    <label><input id="pgcache_late_init" type="checkbox" name="pgcache.late_init" value="1"<?php checked($this->_config->get_string('pgcache.engine') != 'file_generic' && $this->_config->get_boolean('pgcache.late_init')); ?> <?php disabled($this->_config->get_string('pgcache.engine'), 'file_generic') ?> /> <?php _e('Enable', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Enables support for WordPress functionality in fragment caching for the page caching engine. Use of this feature may increase response times.', 'w3-total-cache')?></span>
                </td>
            </tr>
            <?php if ($this->_config->get_string('pgcache.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers"><?php w3_e_config_label('pgcache.memcached.servers') ?></label></th>
                <td>
                    <input id="memcached_servers" type="text" 
                        name="pgcache.memcached.servers" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo esc_attr(implode(',', $this->_config->get_array('pgcache.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        type="button" value="<?php esc_attr_e('Test', 'w3-total-cache'); ?>" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description"><?php _e('Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($this->_config->get_string('pgcache.engine') == 'file_generic'): ?>
            <tr>
                <th><label><?php _e('Compatibility mode', 'w3-total-cache'); ?></label></th>
                <td>
                    <?php $this->checkbox('pgcache.compatibility') ?> <?php w3_e_config_label('pgcache.compatibility') ?></label><br />
                    <span class="description"><?php _e('Decreases performance by ~20% at scale in exchange for increasing interoperability with more hosting environments and WordPress idiosyncrasies. This option should be enabled for most sites', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if (!w3_is_nginx()): ?>
                <tr>
                    <th><label><?php _e('Charset:', 'w3-total-cache')?></label></th>
                    <td>
                        <?php $this->checkbox('pgcache.remove_charset') ?> <?php w3_e_config_label('pgcache.remove_charset') ?></label><br />
                        <span class="description"><?php _e('Resolve issues incorrect odd character encoding that may appear in cached pages.', 'w3-total-cache')?></span>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <th><label for="pgcache_reject_request_head"><?php _e('Reject HEAD requests:', 'w3-total-cache'); ?></label></th>
                <td>
                    <?php if ($this->_config->get_string('pgcache.engine') == 'file_generic'):?>
                    <input id="pgcache_reject_request_head" type="checkbox" name="pgcache.reject.request_head" value="1" disabled="disabled" /> <?php w3_e_config_label('pgcache.reject.request_head') ?><br />
                    <?php else: ?>
                    <?php $this->checkbox('pgcache.reject.request_head', false,'', false) ?><?php w3_e_config_label('pgcache.reject.request_head') ?><br />
                    <?php endif; ?>
                    <span class="description"><?php _e('If disabled, HEAD requests can often be cached resulting in "empty pages" being returned for subsequent requests for a <acronym title="Uniform Resource Indicator">URL</acronym>.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($this->_config->get_string('pgcache.engine') != 'file' && $this->_config->get_string('pgcache.engine') != 'file_generic'): ?>
            <tr>
                <th><label for="pgcache_lifetime"><?php w3_e_config_label('pgcache.lifetime') ?></label></th>
                <td>
                    <input id="pgcache_lifetime" type="text" name="pgcache.lifetime"
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo esc_attr($this->_config->get_integer('pgcache.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                    <br /><span class="description"><?php _e('Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="pgcache_file_gc"><?php w3_e_config_label('pgcache.file.gc') ?></label></th>
                <td>
                    <input id="pgcache_file_gc" type="text" name="pgcache.file.gc" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo esc_attr($this->_config->get_integer('pgcache.file.gc')); ?>" size="8"<?php if ($this->_config->get_string('pgcache.engine') != 'file' && $this->_config->get_string('pgcache.engine') != 'file_generic'): ?> disabled="disabled"<?php endif; ?> /> <?php _e('seconds', 'w3-total-cache') ?>
                    <br /><span class="description"><?php _e('If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_comment_cookie_ttl"><?php w3_e_config_label('pgcache.comment_cookie_ttl') ?></label></th>
                <td>
                        <input id="pgcache_comment_cookie_ttl" type="text" name="pgcache.comment_cookie_ttl" value="<?php echo esc_attr($this->_config->get_integer('pgcache.comment_cookie_ttl')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                        <br /><span class="description"><?php _e('Significantly reduce the default <acronym title="Time to Live">TTL</acronym> for comment cookies to reduce the number of authenticated user traffic. Enter -1 to revert to default <acronym title="Time to Live">TTL</acronym>.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_accept_qs"><?php w3_e_config_label('pgcache.accept.qs') ?></label></th>
                <td>
                    <textarea id="pgcache_accept_qs" name="pgcache.accept.qs"
                        <?php $this->sealing_disabled('pgcache') ?>
                              cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.accept.qs'))); ?></textarea><br />
                    <span class="description"><?php _e('Always cache URLs with these query strings.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_reject_ua"><?php w3_e_config_label('pgcache.reject.ua') ?></label></th>
                <td>
                    <textarea id="pgcache_reject_ua" name="pgcache.reject.ua" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.reject.ua'))); ?></textarea><br />
                    <span class="description"><?php _e('Never send cache pages for these user agents.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_reject_cookie"><?php w3_e_config_label('pgcache.reject.cookie') ?></label></th>
                <td>
                    <textarea id="pgcache_reject_cookie" name="pgcache.reject.cookie" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.reject.cookie'))); ?></textarea><br />
                    <span class="description"><?php _e('Never cache pages that use the specified cookies.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_reject_uri"><?php w3_e_config_label('pgcache.reject.uri') ?></label></th>
                <td>
                    <textarea id="pgcache_reject_uri" name="pgcache.reject.uri" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.reject.uri'))); ?></textarea><br />
                    <span class="description">
						<?php 
							echo sprintf( 
								__( 'Always ignore the specified pages / directories. Supports regular expressions (See <a href="%s">FAQ</a>)', 'w3-total-cache'),   								network_admin_url('admin.php?page=w3tc_faq#q82')
							); ?>
					</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_accept_files"><?php w3_e_config_label('pgcache.accept.files') ?></label></th>
                <td>
                    <textarea id="pgcache_accept_files" name="pgcache.accept.files" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.accept.files'))); ?></textarea><br />
                    <span class="description"><?php echo sprintf( __('Cache the specified pages / directories even if listed in the "never cache the following pages" field. Supports regular expression (See <a href="%s">FAQ</a>)', 'w3-total-cache'), network_admin_url('admin.php?page=w3tc_faq#q82') ); ?></span>
                </td>
            </tr>
            <?php if (substr($permalink_structure, -1) == '/'): ?>
            <tr>
                <th><label for="pgcache_accept_uri"><?php w3_e_config_label('pgcache.accept.uri') ?></label></th>
                <td>
                    <textarea id="pgcache_accept_uri" name="pgcache.accept.uri" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.accept.uri'))); ?></textarea><br />
                    <span class="description"><?php _e('Cache the specified pages even if they don\'t have tailing slash.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="pgcache_cache_headers"><?php w3_e_config_label('pgcache.cache.headers') ?></label></th>
                <td>
                    <textarea id="pgcache_cache_headers" name="pgcache.cache.headers" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"<?php if ($this->_config->get_string('pgcache.engine') == 'file_generic'): ?> disabled="disabled"<?php endif; ?>><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('pgcache.cache.headers'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify additional page headers to cache.', 'w3-total-cache')?></span>
                </td>
            </tr>
            <?php if (w3_is_nginx() && $this->_config->get_string('pgcache.engine') == 'file_generic'): ?>
            <tr>
                <th><label><?php w3_e_config_label('pgcache.cache.nginx_handle_xml') ?></label></th>
                <td>
                    <?php $this->checkbox('pgcache.cache.nginx_handle_xml', true) ?> <?php w3_e_config_label('pgcache.cache.nginx_handle_xml') ?></label><br />
                    <span class="description"><?php _e('Return correct Content-Type header for XML files. Slows down cache engine.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Note(s)', 'w3-total-cache'), '', 'notes'); ?>
        <table class="form-table">
            <tr>
                <th>
                    <ul>
                        <li><?php _e('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "<acronym title="Hypertext Markup Language">HTML</acronym>" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.', 'w3-total-cache'); ?></li>
                        <li><?php _e('The <acronym title="Time to Live">TTL</acronym> of page cache files is set via the "Expires header lifetime" field in the "<acronym title="Hypertext Markup Language">HTML</acronym>" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.', 'w3-total-cache'); ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
