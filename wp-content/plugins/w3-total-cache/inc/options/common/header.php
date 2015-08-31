<?php if (!defined('W3TC')) die(); ?>

<?php
/**
 * @var array $custom_areas Filter that sets it is located in GeneralAdminVIew
 */
$licensing_visible = ((!w3_is_multisite() || is_network_admin()) && 
            !ini_get('w3tc.license_key') && 
            get_transient('w3tc_license_status') != 'host_valid');
?>

<?php do_action('w3tc-dashboard-head') ?>
<div class="wrap" id="w3tc">
    <h2 class="logo"><?php _e('W3 Total Cache <span>by W3 EDGE <sup>&reg;</sup></span>', 'w3-total-cache'); ?></h2>
<?php if (!(w3_is_pro($this->_config) || w3_is_enterprise($this->_config))): ?>
    <?php include W3TC_INC_OPTIONS_DIR . '/edd/buy.php' ?>
<?php endif ?>
    <?php foreach ($this->_errors as $error): ?>
    <div class="error">
        <p><?php echo $error; ?></p>
    </div>
    <?php endforeach; ?>

    <?php if (!$this->_disable_cache_write_notification && $this->_rule_errors_autoinstall != ''): ?>
    <div class="error">
        <p>
            <?php _e('The following configuration changes are needed to ensure optimal performance:', 'w3-total-cache'); ?><br />
        </p>
            <ul style="padding-left: 20px">
                <?php foreach ($this->_rule_errors as $error): ?>
                    <li><?php echo $error[0]; ?></li>
                <?php endforeach; ?>
            </ul>

        <p>
            <?php _e('If permission allow this can be done automatically, by clicking here:', 'w3-total-cache'); ?>
            <?php echo $this->_rule_errors_autoinstall ?>.
            <?php echo $this->_rule_errors_hide ?>
        </p>
    </div>
    <?php endif; ?>

    <?php if (!$this->_disable_file_operation_notification && $this->_rule_errors_root): ?>
    <div class="error">
        <p>
            <?php _e('The following configuration changes are needed to ensure optimal performance:', 'w3-total-cache'); ?><br />
        </p>
        <ul style="padding-left: 20px">
            <?php foreach ($this->_rule_errors_root as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>

    <?php if (isset($this->_ftp_form) && ($this->_use_ftp_form || $this->_rule_errors_root)): ?>
        <p>
            <?php _e('If permission allow this can be done using the <a href="#ftp_upload_form">FTP form</a> below.', 'w3-total-cache'); ?> <?php echo $this->_rule_errors_root_hide; ?>
        </p>
    <?php endif ?>
    </div>
    <?php endif ?>

    <?php if (isset($this->_ftp_form) && ($this->_use_ftp_form || $this->_rule_errors_root)): ?>
    <div id="ftp_upload_form">
        <?php echo $this->_ftp_form ?>
    </div>
    <?php endif; ?>

    <?php foreach ($this->_notes as $note): ?>
    <div class="updated fade">
        <p><?php echo $note; ?></p>
    </div>
    <?php endforeach; ?>

    <?php if (!$this->_config_admin->get_boolean('common.visible_by_master_only') || (is_super_admin() &&
    (!w3_force_master() || is_network_admin()))): ?>
    
    <?php
        switch ($this->_page){
            case 'w3tc_general':
                $anchors = array(
                array('id' => 'general', 'text' => __('General', 'w3-total-cache')),
                array('id' => 'page_cache', 'text' => __('Page Cache', 'w3-total-cache')),
                array('id' => 'minify', 'text' => 'Minify'),
                array('id' => 'database_cache', 'text' => __('Database Cache', 'w3-total-cache')),
                array('id' => 'object_cache', 'text' => __('Object Cache', 'w3-total-cache')));
                if (w3_is_pro($this->_config) || w3_is_enterprise($this->_config))
                    $anchors[] = array('id' => 'fragment_cache', 'text' => __('Fragment Cache', 'w3-total-cache'));

                $anchors = array_merge($anchors, array(
                array('id' => 'browser_cache', 'text' => __('Browser Cache', 'w3-total-cache')),
                array('id' => 'cdn', 'text' => __('<abbr title="Content Delivery Network">CDN</abbr>', 'w3-total-cache')),
                array('id' => 'varnish', 'text' => __('Varnish', 'w3-total-cache'))));
                if (w3_is_enterprise())
                    $anchors[] = array('id' => 'amazon_sns', 'text' => __('Amazon <abbr title="Simple Notification Service">SNS</abbr>', 'w3-total-cache'));
                $anchors[] = array('id' => 'monitoring', 'text' => __('Monitoring', 'w3-total-cache'));
                if ($licensing_visible)
                    array('id' => 'licensing', 'text' => __('Licensing', 'w3-total-cache'));
                $link_attrs = array_merge($anchors, $custom_areas, array(
                    array('id' => 'miscellaneous', 'text' => __('Miscellaneous', 'w3-total-cache')),
                    array('id' => 'debug', 'text' => __('Debug', 'w3-total-cache')),
                    array('id' => 'settings', 'text' => __('Import / Export Settings', 'w3-total-cache'))
                ));

                $links = array();
                foreach($link_attrs as $link) {
                    $links[] = "<a href=\"#{$link['id']}\">{$link['text']}</a>";
                }

    ?>
                <p id="w3tc-options-menu">
                    <?php echo implode(' | ', $links); ?>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_pgcache':
    ?>
                <p id="w3tc-options-menu">
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a> |
                    <a href="#cache_preload"><?php _e('Cache Preload', 'w3-total-cache'); ?></a> |
                    <a href="#purge_policy"><?php _e('Purge Policy', 'w3-total-cache'); ?></a> |
                    <a href="#notes"><?php _e('Note(s)', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_minify':
    ?>
                <p id="w3tc-options-menu">
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#html_xml"><?php _e('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#js"><?php _e('<acronym title="JavaScript">JS</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#css"><?php _e('<acronym title="Cascading Style Sheet">CSS</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a> |
                    <a href="#notes"><?php _e('Note(s)', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_dbcache':
    ?>
                <p id="w3tc-options-menu">
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_objectcache':
    ?>
                <p id="w3tc-options-menu">
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_browsercache':
    ?>
                <p id="w3tc-options-menu">
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#css_js"><?php _e('<acronym title="Cascading Style Sheet">CSS</acronym> &amp; <acronym title="JavaScript">JS</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#html_xml"><?php _e('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#media"><?php _e('Media', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_mobile':
    ?>
                <p id="w3tc-options-menu">
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#manage"><?php _e('Manage User Agent Groups', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_referrer':
    ?>
                <p id="w3tc-options-menu">
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#manage"><?php _e('Manage Referrer Groups', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>
    <?php
            case 'w3tc_cdn':
    ?>
                <p id="w3tc-options-menu">    
                    <?php _e('Jump to:', 'w3-total-cache'); ?> 
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#configuration"><?php _e('Configuration', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a> |
                    <a href="#notes"><?php _e('Note(s)', 'w3-total-cache'); ?></a>
                </p>
    <?php
                break;
    ?>

    <?php
        }            
    ?>
<?php endif ?>
