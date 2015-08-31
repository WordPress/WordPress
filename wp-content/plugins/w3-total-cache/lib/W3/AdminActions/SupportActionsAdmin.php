<?php
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_SupportActionsAdmin extends W3_UI_PluginView {

    /**
     * Array of request types
     *
     * @var array
     */
    var $_request_types;

    /**
     * Array of request groups
     *
     * @var array
     */
    var $_request_groups = array(
        'Free Support' => array(
            'bug_report',
            'new_feature'
        ),
        'Premium Services (per site pricing)' => array(
            'email_support',
            'phone_support',
            'plugin_config',
            'theme_config',
            'linux_config'
        )
    );

    /**
     * Request price list
     *
     * @var array
     */
    var $_request_prices = array(
        'email_support' => 75,
        'phone_support' => 150,
        'plugin_config' => 100,
        'theme_config' => 150,
        'linux_config' => 200
    );

    /**
     * @var W3_Config
     */
    public $_config;

    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_support';

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_request_types = array(
            'bug_report' => __('Submit a Bug Report', 'w3-total-cache'),
            'new_feature' => __('Suggest a New Feature', 'w3-total-cache'),
            'email_support' => __('Less than 15 Minute Email Support Response (M-F 9AM - 5PM EDT): $75 USD', 'w3-total-cache'),
            'phone_support' => __('Less than 15 Minute Phone Support Response (M-F 9AM - 5PM EDT): $150 USD', 'w3-total-cache'),
            'plugin_config' => __('Professional Plugin Configuration: Starting @ $100 USD', 'w3-total-cache'),
            'theme_config' => __('Theme Performance Optimization & Plugin Configuration: Starting @ $150 USD', 'w3-total-cache'),
            'linux_config' => __('Linux Server Optimization & Plugin Configuration: Starting @ $200 USD', 'w3-total-cache')
        );
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        $this->_page = w3tc_get_current_page();
    }

    /**
     * Support select action
     *
     * @return void
     */
    function action_support_select() {
        include W3TC_INC_DIR . '/options/support/select.php';
    }

    /**
     * Support payment action
     *
     * @return void
     */
    function action_support_payment() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $request_type = W3_Request::get_string('request_type');

        if (!isset($this->_request_types[$request_type])) {
            $request_type = 'bug_report';
        }

        $request_id = date('YmdHi');
        $return_url = admin_url('admin.php?page=w3tc_support&request_type=' . $request_type . '&payment=1&request_id=' . $request_id);
        $cancel_url = admin_url('admin.php?page=w3tc_dashboard');

        include W3TC_INC_DIR . '/options/support/payment.php';
    }

    /**
     * Support form action
     *
     * @return void
     */
    function action_support_form() {
        global $current_user;

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $name = '';
        $email = '';
        $request_type = W3_Request::get_string('request_type');

        if (!isset($this->_request_types[$request_type])) {
            $request_type = 'bug_report';
        }

        if (is_a($current_user, 'WP_User')) {
            if ($current_user->first_name) {
                $name = $current_user->first_name;
            }

            if ($current_user->last_name) {
                $name .= ($name != '' ? ' ' : '') . $current_user->last_name;
            }

            if ($name == 'admin') {
                $name = '';
            }

            if ($current_user->user_email) {
                $email = $current_user->user_email;
            }
        }

        $theme = w3tc_get_current_theme();
        $template_files = (isset($theme['Template Files']) ? (array)$theme['Template Files'] : array());

        $ajax = W3_Request::get_boolean('ajax');
        $request_id = W3_Request::get_string('request_id', date('YmdHi'));
        $payment = W3_Request::get_boolean('payment');
        $url = W3_Request::get_string('url', w3_get_domain_url());
        $name = W3_Request::get_string('name', $name);
        $email = W3_Request::get_string('email', $email);
        $twitter = W3_Request::get_string('twitter');
        $phone = W3_Request::get_string('phone');
        $subject = W3_Request::get_string('subject');
        $description = W3_Request::get_string('description');
        $templates = W3_Request::get_array('templates');
        $forum_url = W3_Request::get_string('forum_url');
        $wp_login = W3_Request::get_string('wp_login');
        $wp_password = W3_Request::get_string('wp_password');
        $ftp_host = W3_Request::get_string('ftp_host');
        $ftp_login = W3_Request::get_string('ftp_login');
        $ftp_password = W3_Request::get_string('ftp_password');
        $subscribe_releases = W3_Request::get_string('subscribe_releases');
        $subscribe_customer = W3_Request::get_string('subscribe_customer');

        include W3TC_INC_DIR . '/options/support/form.php';
    }

    /**
     * Send support request action
     *
     * @return void
     */
    function action_support_request() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $request_type = W3_Request::get_string('request_type');
        $payment = W3_Request::get_boolean('payment');
        $request_id = W3_Request::get_string('request_id');
        $url = W3_Request::get_string('url');
        $name = W3_Request::get_string('name');
        $email = W3_Request::get_string('email');
        $twitter = W3_Request::get_string('twitter');
        $phone = W3_Request::get_string('phone');
        $subject = W3_Request::get_string('subject');
        $description = W3_Request::get_string('description');
        $templates = W3_Request::get_array('templates');
        $forum_url = W3_Request::get_string('forum_url');
        $wp_login = W3_Request::get_string('wp_login');
        $wp_password = W3_Request::get_string('wp_password');
        $ftp_host = W3_Request::get_string('ftp_host');
        $ftp_login = W3_Request::get_string('ftp_login');
        $ftp_password = W3_Request::get_string('ftp_password');
        $subscribe_releases = W3_Request::get_string('subscribe_releases');
        $subscribe_customer = W3_Request::get_string('subscribe_customer');

        $params = array(
            'request_type' => $request_type,
            'payment' => $payment,
            'url' => $url,
            'name' => $name,
            'email' => $email,
            'twitter' => $twitter,
            'phone' => $phone,
            'subject' => $subject,
            'description' => $description,
            'forum_url' => $forum_url,
            'wp_login' => $wp_login,
            'wp_password' => $wp_password,
            'ftp_host' => $ftp_host,
            'ftp_login' => $ftp_login,
            'ftp_password' => $ftp_password,
            'subscribe_releases' => $subscribe_releases,
            'subscribe_customer' => $subscribe_customer
        );

        $post = $params;
        foreach ($templates as $template_index => $template) {
            $template_key = sprintf('templates[%d]', $template_index);
            $params[$template_key] = $template;
        }

        if (!isset($this->_request_types[$request_type])) {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_type'
            )), false);
        }

        $required = array(
            'bug_report' => 'url,name,email,subject,description',
            'new_feature' => 'url,name,email,subject,description',
            'email_support' => 'url,name,email,subject,description',
            'phone_support' => 'url,name,email,subject,description,phone',
            'plugin_config' => 'url,name,email,subject,description,wp_login,wp_password',
            'theme_config' => 'url,name,email,subject,description,wp_login,wp_password,ftp_host,ftp_login,ftp_password',
            'linux_config' => 'url,name,email,subject,description,wp_login,wp_password,ftp_host,ftp_login,ftp_password'
        );

        if (strstr($required[$request_type], 'url') !== false && $url == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_url'
            )), false);
        }

        if (strstr($required[$request_type], 'name') !== false && $name == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_name'
            )), false);
        }

        if (strstr($required[$request_type], 'email') !== false && !preg_match('~^[a-z0-9_\-\.]+@[a-z0-9-\.]+\.[a-z]{2,5}$~', $email)) {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_email'
            )), false);
        }

        if (strstr($required[$request_type], 'phone') !== false && !preg_match('~^[0-9\-\.\ \(\)\+]+$~', $phone)) {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_phone'
            )), false);
        }

        if (strstr($required[$request_type], 'subject') !== false && $subject == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_subject'
            )), false);
        }

        if (strstr($required[$request_type], 'description') !== false && $description == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_description'
            )), false);
        }

        if (strstr($required[$request_type], 'wp_login') !== false && $wp_login == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_wp_login'
            )), false);
        }

        if (strstr($required[$request_type], 'wp_password') !== false && $wp_password == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_wp_password'
            )), false);
        }

        if (strstr($required[$request_type], 'ftp_host') !== false && $ftp_host == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_ftp_host'
            )), false);
        }

        if (strstr($required[$request_type], 'ftp_login') !== false && $ftp_login == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_ftp_login'
            )), false);
        }

        if (strstr($required[$request_type], 'ftp_password') !== false && $ftp_password == '') {
            w3_admin_redirect(array_merge($params, array(
                'w3tc_error' => 'support_request_ftp_password'
            )), false);
        }

        /**
         * Add attachments
         */
        $attachments = array();

        $attach_files = array(
            /**
             * Attach WP config file
             */
            w3_get_wp_config_path(),

            /**
             * Attach minify file
             */
            w3_cache_blog_dir('log') . '/minify.log',

            /**
             * Attach .htaccess files
             */
            w3_get_pgcache_rules_core_path(),
            w3_get_pgcache_rules_cache_path(),
            w3_get_browsercache_rules_cache_path(),
            w3_get_browsercache_rules_no404wp_path(),
            w3_get_minify_rules_core_path(),
            w3_get_minify_rules_cache_path()
        );

        /**
         * Attach config files
         */
        if ($handle = opendir(W3TC_CONFIG_DIR)) {
            while (($entry = @readdir($handle)) !== false) {
                if ($entry == '.' || $entry == '..' || $entry == 'index.html')
                    continue;

                $attachments[] = W3TC_CONFIG_DIR . '/' . $entry;
            }
            closedir($handle);
        }


        foreach ($attach_files as $attach_file) {
            if ($attach_file && file_exists($attach_file) && !in_array($attach_file, $attachments)) {
                $attachments[] = $attach_file;
            }
        }

        /**
         * Attach server info
         */
        $server_info = print_r($this->get_server_info(), true);
        $server_info = str_replace("\n", "\r\n", $server_info);

        $server_info_path = W3TC_CACHE_TMP_DIR . '/server_info.txt';

        if (@file_put_contents($server_info_path, $server_info)) {
            $attachments[] = $server_info_path;
        }

        /**
         * Attach phpinfo
         */
        ob_start();
        phpinfo();
        $php_info = ob_get_contents();
        ob_end_clean();

        $php_info_path = W3TC_CACHE_TMP_DIR . '/php_info.html';

        if (@file_put_contents($php_info_path, $php_info)) {
            $attachments[] = $php_info_path;
        }

        /**
         * Attach self-test
         */
        ob_start();
        $this->action_self_test();
        $self_test = ob_get_contents();
        ob_end_clean();

        $self_test_path = W3TC_CACHE_TMP_DIR . '/self_test.html';

        if (@file_put_contents($self_test_path, $self_test)) {
            $attachments[] = $self_test_path;
        }

        /**
         * Attach templates
         */
        foreach ($templates as $template) {
            if (!empty($template)) {
                $attachments[] = $template;
            }
        }

        /**
         * Attach other files
         */
        if (!empty($_FILES['files'])) {
            $files = (array)$_FILES['files'];
            for ($i = 0, $l = count($files); $i < $l; $i++) {
                if (isset($files['tmp_name'][$i]) && isset($files['name'][$i]) && isset($files['error'][$i]) && $files['error'][$i] == UPLOAD_ERR_OK) {
                    $path = W3TC_CACHE_TMP_DIR . '/' . $files['name'][$i];
                    if (@move_uploaded_file($files['tmp_name'][$i], $path)) {
                        $attachments[] = $path;
                    }
                }
            }
        }

        $data = array();

        if (!empty($wp_login) && !empty($wp_password)) {
            $data['WP Admin login'] = $wp_login;
            $data['WP Admin password'] = $wp_password;
        }

        if (!empty($ftp_host) && !empty($ftp_login) && !empty($ftp_password)) {
            $data['SSH / FTP host'] = $ftp_host;
            $data['SSH / FTP login'] = $ftp_login;
            $data['SSH / FTP password'] = $ftp_password;
        }

        /**
         * Store request data for future access
         */
        if (count($data)) {
            $hash = md5(microtime());
            $request_data = get_option('w3tc_request_data', array());
            $request_data[$hash] = $data;

            update_option('w3tc_request_data', $request_data);

            $request_data_url = sprintf('%s/w3tc_request_data/%s', w3_get_home_url(), $hash);
        } else {
            $request_data_url = '';
        }

        $nonce = wp_create_nonce('w3tc_support_request');
        if (is_network_admin()) {
            update_site_option('w3tc_support_request', $nonce);
        } else {
            update_option('w3tc_support_request', $nonce);
        }
        $file_access = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/files.php';
        if (w3_get_domain(w3_get_home_url()) != w3_get_domain(w3_get_site_url())) {
            $file_access = str_replace(w3_get_domain(w3_get_home_url()), w3_get_domain(w3_get_site_url()), $file_access);
        }

        $post['file_access'] =  $file_access;
        $post['nonce'] = $nonce;
        $post['request_data_url'] = $request_data_url;
        $post['ip'] = $_SERVER['REMOTE_ADDR'];
        $post['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $post['version'] = W3TC_VERSION;
        $post['plugin'] = 'W3 Total Cache';
        $post['request_id'] = $request_id;
        $license_level = 'community';
        if (w3_is_pro($this->_config))
            $license_level = 'pro';
        elseif (w3_is_enterprise($this->_config))
            $license_level = 'enterprise';

        $post['license_level'] = $license_level;

        $unset = array('wp_login', 'wp_password', 'ftp_host', 'ftp_login', 'ftp_password');

        foreach ($unset as $key)
            unset($post[$key]);

        foreach ($attachments as $attachment) {
            if (is_network_admin())
                update_site_option('attachment_' . md5($attachment), $attachment);
            else
                update_option('attachment_' . md5($attachment), $attachment);
        }
        $post = array_merge($post, array('files' => $attachments));

        if (defined('W3_SUPPORT_DEBUG') && W3_SUPPORT_DEBUG) {
            $data = sprintf("[%s] Post support request\n", date('r'));
            foreach ($post as $key => $value)
                $data .= sprintf("%s => %s\n", $key, is_array($value) ? implode(',', $value) : $value);
            $filename = w3_cache_blog_dir('log') . '/support.log';
            if (!is_dir(dirname($filename)))
                w3_mkdir_from(dirname($filename), W3TC_CACHE_DIR);

            @file_put_contents($filename, $data, FILE_APPEND);
        }

        $response = wp_remote_post(W3TC_SUPPORT_REQUEST_URL, array('body' => $post, 'timeout' => $this->_config->get_integer('timelimit.email_send')));

        if (defined('W3_SUPPORT_DEBUG') && W3_SUPPORT_DEBUG) {
            $filename = w3_cache_blog_dir('log') . '/support.log';
            $data = sprintf("[%s] Post response \n%s\n", date('r'), print_r($response, true));
            @file_put_contents($filename, $data, FILE_APPEND);
        }

        if (!is_wp_error($response))
            $result = $response['response']['code'] == 200 && $response['body'] == 'Ok';
        else  {
            $result = false;
        }
        /**
         * Remove temporary files
         */
        foreach ($attachments as $attachment) {
            if (strstr($attachment, W3TC_CACHE_TMP_DIR) !== false) {
                @unlink($attachment);
            }
            if (is_network_admin())
                delete_site_option('attachment_' . md5($attachment));
            else
                delete_option('attachment_' . md5($attachment));
        }

        if (is_network_admin())
            delete_site_option('w3tc_support_request');
        else
            delete_option('w3tc_support_request');

        if ($result) {
            w3_admin_redirect(array(
                'tab' => 'general',
                'w3tc_note' => 'support_request'
            ), false);
        } else {
            w3_admin_redirect(array_merge($params, array(
                'request_type' => $request_type,
                'w3tc_error' => 'support_request'
            )), false);
        }
    }

    /**
     * Self test action
     */
    function action_self_test() {
        include W3TC_INC_DIR . '/lightbox/self_test.php';
    }

    /**
     * PHPMailer init function
     *
     * @param PHPMailer $phpmailer
     * @return void
     */
    function phpmailer_init(&$phpmailer) {
        $phpmailer->Sender = $this->_phpmailer_sender;
    }

    /**
     * Returns server info
     *
     * @return array
     */
    function get_server_info() {
        global $wp_version, $wp_db_version, $wpdb;

        $wordpress_plugins = get_plugins();
        $wordpress_plugins_active = array();

        foreach ($wordpress_plugins as $wordpress_plugin_file => $wordpress_plugin) {
            if (is_plugin_active($wordpress_plugin_file)) {
                $wordpress_plugins_active[$wordpress_plugin_file] = $wordpress_plugin;
            }
        }

        $mysql_version = $wpdb->get_var('SELECT VERSION()');
        $mysql_variables_result = (array) $wpdb->get_results('SHOW VARIABLES', ARRAY_N);
        $mysql_variables = array();

        foreach ($mysql_variables_result as $mysql_variables_row) {
            $mysql_variables[$mysql_variables_row[0]] = $mysql_variables_row[1];
        }

        $server_info = array(
            'w3tc' => array(
                'version' => W3TC_VERSION,
                'server' => (!empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown'),
                'dir' => W3TC_DIR,
                'cache_dir' => W3TC_CACHE_DIR,
                'blog_id' => w3_get_blog_id(),
                'document_root' => w3_get_document_root(),
                'home_root' => w3_get_home_root(),
                'site_root' => w3_get_site_root(),
                'base_path' => w3_get_base_path(),
                'home_path' => w3_get_home_path(),
                'site_path' => w3_get_site_path()
            ),
            'wp' => array(
                'version' => $wp_version,
                'db_version' => $wp_db_version,
                'abspath' => ABSPATH,
                'home' => get_option('home'),
                'siteurl' => get_option('siteurl'),
                'email' => get_option('admin_email'),
                'upload_info' => (array) w3_upload_info(),
                'theme' => w3tc_get_current_theme(),
                'wp_cache' => ((defined('WP_CACHE') && WP_CACHE) ? 'true' : 'false'),
                'plugins' => $wordpress_plugins_active
            ),
            'mysql' => array(
                'version' => $mysql_version,
                'variables' => $mysql_variables
            )
        );

        return $server_info;
    }

    /**
     * not used
     */
    protected function view(){}


    /**
     * Returns list of support types
     *
     * @return array
     */
    function get_supports() {
        $supports = array(
            'footer' => 'page footer'
        );

        $link_categories = get_terms('link_category', array(
            'hide_empty' => 0
        ));

        foreach ($link_categories as $link_category) {
            $supports['link_category_' . $link_category->term_id] = strtolower($link_category->name);
        }

        return $supports;
    }


    /**
     * Support Us action
     *
     * @return void
     */
    function action_support_us() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        $supports = $this->get_supports();
        global $current_user;
        get_currentuserinfo();
        $email = $current_user->user_email;
        include W3TC_INC_DIR . '/lightbox/support_us.php';
    }
}
