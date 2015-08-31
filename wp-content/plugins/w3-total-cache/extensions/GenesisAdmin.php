<?php
/**
 * W3 GenesisExtension module
 */
if (!defined('W3TC')) {
    die();
}

class W3_GenesisAdmin {

    function run() {
        add_action('admin_init', array($this, 'admin_init'));
        add_filter('w3tc_extensions', array($this, 'extension'), 10, 2);
        add_action('w3tc_extensions_page-genesis.theme', array($this, 'extension_header'));
    }

    /**
     * Display if caching or not.
     */
    function extension_header() {
        $config = w3_instance('W3_Config');
        $settings = w3tc_get_extension_config('genesis.theme');
        $caching = false;
        foreach($settings as $setting => $value) {
            if (strpos($setting, 'reject') === false && $value == '1') {
                $caching = true;
                break;
            }
        }
        echo '<p>';
        printf(__('The Genesis Framework extension is currently %s ', 'w3-total-cache'),
            ($caching ? '<span class="w3tc-enabled">' . __('enabled', 'w3-total-cache') . '</span>' :
                '<span class="w3tc-disabled">' . __('disabled', 'w3-total-cache') . '</span>'));
        if ($caching)
            printf(__('and caching via <strong>%s</strong>', 'w3-total-cahe'),$config->get_string('fragmentcache.engine'));
        echo '.</p>';
    }

    /**
     * Setups sections
     */
    function admin_init() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        // Register our settings field group
        w3tc_add_settings_section(
            'header', // Unique identifier for the settings section
            'Header', // Section title
            '__return_false', // Section callback (we don't want anything)
            'genesis.theme' // extension id, used to uniquely identify the extension;
        );
        w3tc_add_settings_section(
            'content',
            'Content',
            '__return_false',
            'genesis.theme'
        );

        w3tc_add_settings_section(
            'sidebar',
            'Sidebar',
            '__return_false',
            'genesis.theme'
        );


        w3tc_add_settings_section(
            'footer',
            'Footer',
            '__return_false',
            'genesis.theme'
        );

        w3tc_add_settings_section(
            'exclusions',
            'Disable fragment cache',
            '__return_false',
            'genesis.theme'
        );

        $settings = $this->settings();
        foreach($settings as $setting => $meta) {
            /**
             * @var $label
             * @var $description
             * @var $section
             * @var $type
             */
            extract($meta);
            w3tc_add_settings_field($setting, $label,
                array($this, 'print_setting'), 'genesis.theme', $section,
                array('label_for'=>$setting, 'type'=>$type,
                    'description' => $description));
        }
    }

    /**
     *
     * @param $setting
     * @param $args
     */
    function print_setting($setting, $args) {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        list($name, $id) = w3tc_get_name_and_id('genesis.theme', $setting);
        if ($args['type'] != 'custom')
            w3_ui_element($args['type'], $setting, $name, w3tc_get_extension_config('genesis.theme', $setting), w3_extension_is_sealed('genesis.theme'));
        else {
            if ($setting == 'reject_roles'):
                $saved_roles = w3tc_get_extension_config('genesis.theme', $setting);
                if (!is_array($saved_roles))
                    $saved_roles = array();
                ?>
                <div id="<?php echo esc_attr($id)?>">
                <input type="hidden" name="<?php echo esc_attr($name)?>" value="" />
                <?php foreach( get_editable_roles() as $role_name => $role_data ) : ?>
                    <input <?php disabled(w3_extension_is_sealed('genesis.theme')) ?> type="checkbox" name="<?php echo esc_attr($name)?>[]" value="<?php echo $role_name ?>" <?php checked( in_array( $role_name, $saved_roles ) ) ?> id="role_<?php echo $role_name ?>" />
                    <label for="role_<?php echo $role_name ?>"><?php echo $role_data['name'] ?></label>
                <?php endforeach; ?>
                </div>
            <?php
            else:
                $saved_hooks = w3tc_get_extension_config('genesis.theme', $setting);
                if (!is_array($saved_hooks))
                    $saved_hooks = array();
                $hooks = array('genesis_header' => 'Header', 'genesis_footer' => 'Footer', 'genesis_sidebar' => 'Sidebar', 'genesis_loop' =>'The Loop', 'wp_head' => 'wp_head', 'wp_footer' => 'wp_footer', 'genesis_comments' => 'Comments', 'genesis_pings' => 'Pings', 'genesis_do_nav'=>'Primary navigation', 'genesis_do_subnav' => 'Secondary navigation');?>
                <div id="<?php echo esc_attr($id)?>">
                    <input <?php disabled(w3_extension_is_sealed('genesis.theme')) ?> type="hidden" name="<?php echo esc_attr($name)?>" value="" />
                <?php foreach( $hooks as $hook => $hook_label ) : ?>
                    <input <?php disabled(w3_extension_is_sealed('genesis.theme')) ?> type="checkbox" name="<?php echo esc_attr($name)?>[]" value="<?php echo $hook ?>" <?php checked( in_array( $hook, $saved_hooks ) ) ?> id="role_<?php echo $hook ?>" />
                    <label for="role_<?php echo $hook ?>"><?php echo $hook_label ?></label><br />
                <?php endforeach; ?>
                </div>
            <?php
            endif;
        }
    }

    /**
     * @param $extensions
     * @param W3_Config $config
     * @return mixed
     */
    function extension($extensions, $config) {
        $fc_enabled = ((w3_is_pro($config) || w3_is_enterprise($config)) && 
                $config->get_boolean('fragmentcache.enabled'));

        $activation_enabled = $fc_enabled && defined('PARENT_THEME_NAME') && PARENT_THEME_NAME == 'Genesis' &&
            defined('PARENT_THEME_VERSION') && version_compare(PARENT_THEME_VERSION, '1.9.0') >= 0;
        $message = array();

        if (is_network_admin()) {
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/themes.php');
            $themes = w3tc_get_themes();
            $exists = false;
            foreach ($themes as $theme) {
                if (strtolower($theme->Template) == 'genesis')
                    $exists = true;
            }
            if (!$exists)
                $message[] = 'Genesis Framework';
        } elseif (!(defined('PARENT_THEME_NAME') && PARENT_THEME_NAME == 'Genesis'))
            $message[] = 'Genesis Framework version >= 1.9.0';

        if (!$fc_enabled)
            $message[] = 'Fragment Cache (W3 Total Cache Pro)';

        $extensions['genesis.theme'] = array (
            'name' => 'Genesis Framework',
            'author' => 'W3 EDGE',
            'description' => 'Provides 30-60% improvement in page generation time for the Genesis Framework by Copyblogger Media.',
            'author uri' => 'http://www.w3-edge.com/',
            'extension uri' => 'http://www.w3-edge.com/',
            'extension id' => 'genesis.theme',
            'version' => '0.1',
            'enabled' => $activation_enabled,
            'requirements' => implode(', ', $message),
            'path' => 'w3-total-cache/extensions/Genesis.php'
        );

        return $extensions;
    }

    /**
     * @return array
     */
    function settings() {
        return
            array(
                'wp_head' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'header',
                    'label' => __('Cache wp_head loop:', 'w3-total-cache'),
                    'description' =>__('Cache wp_head. This includes the embedded CSS, JS etc.', 'w3-total-cache')
                ),
                'genesis_header' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'header',
                    'label' => __('Cache header:', 'w3-total-cache'),
                    'description' => __('Cache header loop. This is the area where the logo is located.', 'w3-total-cache')
                ),
                'genesis_do_nav' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'header',
                    'label' => __('Cache primary navigation:', 'w3-total-cache'),
                    'description' => __('Caches the navigation filter; per page.', 'w3-total-cache')
                ),
                'genesis_do_subnav' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'header',
                    'label' => __('Cache secondary navigation:', 'w3-total-cache'),
                    'description' => __('Caches secondary navigation filter; per page.', 'w3-total-cache'),
                ),
                'loop_front_page' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'content',
                    'label' => __('Cache front page post loop:', 'w3-total-cache'),
                    'description' => __('Caches the front page post loop, pagination is supported.', 'w3-total-cache')
                ),
                'loop_terms' =>
                    array(
                        'type' => 'checkbox',
                        'section' => 'content',
                        'label' => __('Cache author/tag/categories/term post loop:', 'w3-total-cache'),
                        'description' => __('Caches the posts listed on tag, categories, author and other term pages, pagination is supported.', 'w3-total-cache')
                 ),
                'flush_terms' =>
                    array(
                        'type' => 'checkbox',
                        'section' => 'content',
                        'label' => __('Flush posts loop:', 'w3-total-cache'),
                        'description' => __('Flushes the posts loop cache on post updates. See setting above for affected loops.', 'w3-total-cache')
                    ),
                'loop_single' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'content',
                    'label' => __('Cache single post / page:', 'w3-total-cache'),
                    'description' => __('Caches the single post / page loop, pagination is supported.', 'w3-total-cache')
                ),
                'loop_single_excluded' =>
                array(
                    'type' => 'textarea',
                    'section' => 'content',
                    'label' => __('Excluded single pages / posts:', 'w3-total-cache'),
                    'description' => __('List of pages / posts that should not have the single post / post loop cached. Specify one page / post per line. This area supports regular expressions.', 'w3-total-cache')
                ),
                'loop_single_genesis_comments' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'content',
                    'label' => __('Cache comments:', 'w3-total-cache'),
                    'description' => __('Caches the comments loop, pagination is supported.', 'w3-total-cache')
                ),
                'loop_single_genesis_pings' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'content',
                    'label' => __('Cache pings:', 'w3-total-cache'),
                    'description' => __('Caches the ping loop, pagination is supported. One per line.', 'w3-total-cache')
                ),
                'sidebar' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'sidebar',
                    'label' => __('Cache sidebar:', 'w3-total-cache'),
                    'description' => __('Caches sidebar loop, the widget area.', 'w3-total-cache')
                ),
                'sidebar_excluded' =>
                array(
                    'type' => 'textarea',
                    'section' => 'sidebar',
                    'label' => __('Exclude pages:', 'w3-total-cache'),
                    'description' => __('List of pages that should not have sidebar cached. Specify one page / post per line. This area supports regular expressions.', 'w3-total-cache')
                ),
                'genesis_footer' =>
                    array(
                        'type' => 'checkbox',
                        'section' => 'footer',
                        'label' => __('Cache footer:', 'w3-total-cache'),
                        'description' => __('Caches footer loop.', 'w3-total-cache')
                ),
                'wp_footer' =>
                array(
                    'type' => 'checkbox',
                    'section' => 'footer',
                    'label' => __('Cache footer:', 'w3-total-cache'),
                    'description' => __('Caches wp_footer loop.', 'w3-total-cache')
                ),
                'reject_logged_roles' =>
                array('type' => 'checkbox',
                    'section' => 'exclusions',
                    'label' => __('Disable fragment cache:', 'w3-total-cache'),
                    'description' => 'Don\'t use fragment cache with the following hooks and for the specified user roles.'
                ),
                'reject_logged_roles_on_actions' =>
                array('type' => 'custom',
                    'section' => 'exclusions',
                    'label' => __('Select hooks:', 'w3-total-cache'),
                    'description' => __('Select hooks from the list that should not be cached if user belongs to any of the roles selected below.', 'w3-total-cache')
                ),
                'reject_roles' =>
                    array('type' => 'custom',
                    'section' => 'exclusions',
                    'label' => __('Select roles:', 'w3-total-cache'),
                    'description' => __('Select user roles that should not use the fragment cache.', 'w3-total-cache')

                )
            );
    }
}

$ext = new W3_GenesisAdmin();
$ext->run();