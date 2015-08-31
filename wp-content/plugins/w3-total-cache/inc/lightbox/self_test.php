<?php 

if (!defined('W3TC')) 
    die();

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');
 
?>
<h3><?php _e('Compatibility Test', 'w3-total-cache'); ?></h3>

<fieldset>
    <legend><?php _e('Legend', 'w3-total-cache'); ?></legend>

    <p>
        <?php _e('<code>Installed</code>: Functionality will work properly.', 'w3-total-cache'); ?><br />
        <?php _e('<code>Not detected</code>: May be installed, but cannot be automatically confirmed.', 'w3-total-cache'); ?><br />
        <?php _e('<code>Ok</code>: Current value is acceptable.', 'w3-total-cache'); ?><br />
        <?php _e('<code>Yes / No</code>: The value was successful detected.', 'w3-total-cache'); ?>
    </p>
</fieldset>

<div id="w3tc-self-test">
    <h4 style="margin-top: 0;"><?php _e('Server Modules &amp; Resources:', 'w3-total-cache'); ?></h4>

    <ul>
        <li>
            <?php _e('Plugin Version:', 'w3-total-cache'); ?> <code><?php echo W3TC_VERSION; ?></code>
        </li>

        <li>
            <?php _e('PHP Version:', 'w3-total-cache'); ?>
            <code><?php echo PHP_VERSION; ?></code>;
        </li>

        <li>
            Web Server:
            <?php if (stristr($_SERVER['SERVER_SOFTWARE'], 'apache') !== false): ?>
            <code>Apache</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false): ?>
            <code>Lite Speed</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false): ?>
            <code>nginx</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false): ?>
            <code>lighttpd</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'iis') !== false): ?>
            <code>Microsoft IIS</code>
            <?php else: ?>
            <code>Not detected</code>
            <?php endif; ?>
        </li>

        <li>
            FTP functions:
            <?php if (function_exists('ftp_connect')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for Self-hosted (<acronym title="File Transfer Protocol">FTP</acronym>) <acronym title="Content Delivery Network">CDN</acronym> support)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            <?php _e('Multibyte String support:', 'w3-total-cache'); ?>
            <?php if (function_exists('mb_substr')): ?>
            <code><?php _e('Installed', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for Rackspace Cloud Files support)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            <?php _e('cURL extension:', 'w3-total-cache'); ?>
            <?php if (function_exists('curl_init')): ?>
            <code><?php _e('Installed', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for Amazon S3, Amazon CloudFront, Rackspace CloudFiles support)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            zlib extension:
            <?php if (function_exists('gzencode')): ?>
            <code><?php _e('Installed', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for compression support)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            Opcode cache:
            <?php if (function_exists('apc_store')): ?>
            <code><?php _e('Installed (APC)', 'w3-total-cache'); ?></code>
            <?php elseif (function_exists('eaccelerator_put')): ?>
            <code><?php _e('Installed (eAccelerator)', 'w3-total-cache'); ?></code>
            <?php elseif (function_exists('xcache_set')): ?>
            <code><?php _e('Installed (XCache)', 'w3-total-cache'); ?></code>
            <?php elseif (PHP_VERSION >= 6): ?>
            <code><?php _e('PHP6', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('Memcache extension:', 'w3-total-cache'); ?>
            <?php if (class_exists('Memcache')): ?>
            <code><?php _e('Installed', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('HTML Tidy extension:', 'w3-total-cache'); ?>
            <?php if (class_exists('tidy')): ?>
            <code><?php _e('Installed', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for HTML Tidy minifier suppport)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            <?php _e('Mime type detection:', 'w3-total-cache'); ?>
            <?php if (function_exists('finfo_open')): ?>
            <code><?php _e('Installed (Fileinfo)', 'w3-total-cache'); ?></code>
            <?php elseif (function_exists('mime_content_type')): ?>
            <code><?php _e('Installed (mime_content_type)', 'w3-total-cache'); ?></code>
            <?php else:  ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for <acronym title="Content Delivery Network">CDN</acronym> support)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            <?php _e('Hash function:', 'w3-total-cache'); ?>
            <?php if (function_exists('hash')): ?>
            <code><?php _e('Installed (hash)', 'w3-total-cache'); ?></code>
            <?php elseif (function_exists('mhash')): ?>
            <code><?php _e('Installed (mhash)', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint"><?php _e('(required for NetDNA / MaxCDN <acronym title="Content Delivery Network">CDN</acronym> purge support)', 'w3-total-cache'); ?></span>
        </li>

        <li>
            <?php _e('Safe mode:', 'w3-total-cache'); ?>
            <?php if (w3_to_boolean(ini_get('safe_mode'))): ?>
            <code><?php _e('On', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Off', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('Open basedir:', 'w3-total-cache'); ?>
            <?php $open_basedir = ini_get('open_basedir'); if ($open_basedir): ?>
            <code><?php _e('On:', 'w3-total-cache'); ?> <?php echo htmlspecialchars($open_basedir); ?></code>
            <?php else: ?>
            <code><?php _e('Off', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('zlib output compression:', 'w3-total-cache'); ?>
            <?php if (w3_to_boolean(ini_get('zlib.output_compression'))): ?>
            <code><?php _e('On', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Off', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('set_time_limit:', 'w3-total-cache'); ?>
            <?php if (function_exists('set_time_limit')): ?>
            <code><?php _e('Available', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not available', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <?php
        if (w3_is_apache()):
            $apache_modules = (function_exists('apache_get_modules') ? apache_get_modules() : false);

            $modules = array(
                'mod_deflate',
                'mod_env',
                'mod_expires',
                'mod_headers',
                'mod_mime',
                'mod_rewrite',
                'mod_setenvif'
            );
        ?>
            <?php foreach ($modules as $module): ?>
                <li>
                    <?php echo $module; ?>:
                    <?php if ($apache_modules): ?>
                        <?php if (in_array($module, $apache_modules)): ?>
                        <code><?php _e('Installed', 'w3-total-cache'); ?></code>
                        <?php else: ?>
                        <code><?php _e('Not installed', 'w3-total-cache'); ?></code>
                        <?php endif; ?>
                    <?php else: ?>
                    <code><?php _e('Not detected', 'w3-total-cache'); ?></code>
                    <?php endif; ?>
                    <span class="w3tc-self-test-hint"><?php _e('(required for disk enhanced Page Cache and Browser Cache)', 'w3-total-cache'); ?></span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <?php $additional_checks = apply_filters('w3tc_compatibility_test', __return_empty_array());
    if ($additional_checks):?>
    <h4><?php _e('Additional Server Modules','w3-total-cache')?></h4>
    <ul>
    <?php
    foreach($additional_checks as $check)
        echo '<li>', $check, '</li>';
    ?>
    </ul>
    <?php
    endif;
    ?>

    <h4><?php _e('WordPress Resources', 'w3-total-cache'); ?></h4>

    <ul>
        <?php
        $paths = array_unique(array(
            w3_get_pgcache_rules_core_path(),
            w3_get_browsercache_rules_cache_path(),
            w3_get_browsercache_rules_no404wp_path()
        ));
        ?>
        <?php foreach ($paths as $path): if ($path): ?>
        <li>
            <?php echo htmlspecialchars($path); ?>:
            <?php if (file_exists($path)): ?>
                <?php if (w3_is_writable($path)): ?>
                <code><?php _e('OK', 'w3-total-cache'); ?></code>
                <?php else: ?>
                <code><?php _e('Not write-able', 'w3-total-cache'); ?></code>
                <?php endif; ?>
            <?php else: ?>
                <?php if (w3_is_writable_dir(dirname($path))): ?>
                <code><?php _e('Write-able', 'w3-total-cache'); ?></code>
                <?php else: ?>
                <code><?php _e('Not write-able', 'w3-total-cache'); ?></code>
                <?php endif; ?>
            <?php endif; ?>
        </li>
        <?php endif; endforeach; ?>

        <li>
            <?php echo w3_path(WP_CONTENT_DIR); ?>:
            <?php if (w3_is_writable_dir(WP_CONTENT_DIR)): ?>
            <code><?php _e('OK', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Not write-able', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php $uploads_dir = @wp_upload_dir(); ?>
            <?php echo htmlspecialchars($uploads_dir['path']); ?>:
            <?php if (!empty($uploads_dir['error'])): ?>
            <code><?php _e('Error:', 'w3-total-cache'); ?> <?php echo htmlspecialchars($uploads_dir['error']); ?></code>
            <?php elseif (!w3_is_writable_dir($uploads_dir['path'])): ?>
            <code><?php _e('Not write-able', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('OK', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('Fancy permalinks:', 'w3-total-cache'); ?>
            <?php $permalink_structure = get_option('permalink_structure'); if ($permalink_structure): ?>
            <code><?php echo htmlspecialchars($permalink_structure); ?></code>
            <?php else: ?>
            <code><?php _e('Disabled', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('WP_CACHE define:', 'w3-total-cache'); ?>
            <?php if (defined('WP_CACHE')): ?>
            <code><?php _e('Defined', 'w3-total-cache'); ?> (<?php echo (WP_CACHE ? 'true' : 'false'); ?>)</code>
            <?php else: ?>
            <code><?php _e('Not defined', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('URL rewrite:', 'w3-total-cache'); ?>
            <?php if (w3_can_check_rules()): ?>
            <code><?php _e('Enabled', 'w3-total-cache'); ?></code>
            <?php else: ?>
            <code><?php _e('Disabled', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>

        <li>
            <?php _e('Network mode:', 'w3-total-cache'); ?>
            <?php if (w3_is_network()): ?>
            <code><?php _e('Yes', 'w3-total-cache'); ?> (<?php echo (w3_is_subdomain_install() ? 'subdomain' : 'subdir'); ?>)</code>
            <?php else: ?>
            <code><?php _e('No', 'w3-total-cache'); ?></code>
            <?php endif; ?>
        </li>
    </ul>
</div>

<div id="w3tc-self-test-bottom">
    <input class="button-primary" type="button" value="<?php _e('Close', 'w3-total-cache'); ?>" />
</div>
