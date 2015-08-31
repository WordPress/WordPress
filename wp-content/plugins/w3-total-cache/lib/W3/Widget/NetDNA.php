<?php
/**
 * W3 NetDNA Widget
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
w3_require_once(W3TC_INC_DIR . '/functions/widgets.php');

/**
 * Class W3_Widget_NetDNA
 */
class W3_Widget_NetDNA extends W3_Plugin {
    private $authorized;
    private $have_zone;
    private $_sealed;

    /**
     * @var NetDNA
     */
    private $api;
    function run() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        if(w3tc_get_current_wp_page() == 'w3tc_dashboard')
            add_action('admin_enqueue_scripts', array($this,'enqueue'));
        add_action('w3tc_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        add_action('w3tc_network_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));

        // Configure authorize and have_zone
        $this->_setup($this->_config);
        /**
         * Retry setup with main blog
         */
        if (w3_is_network() && is_network_admin() && !$this->authorized) {
            $this->_config = new W3_Config(false, 1);
            $this->_setup($this->_config);
        }

        if (w3_is_network()) {
            $conig_admin = w3_instance('W3_ConfigAdmin');
            $this->_sealed = $conig_admin->get_boolean('cdn.configuration_sealed');
        };

        if ($this->have_zone && $this->authorized && isset($_GET['page']) && strpos($_GET['page'], 'w3tc_dashboard') !== false) {
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNAPresentation.php');
            $authorization_key = $this->_config->get_string('cdn.netdna.authorization_key');
            $alias = $consumerkey = $consumersecret = '';

            $keys = explode('+', $authorization_key);
            if (sizeof($keys) == 3)
                list($alias, $consumerkey, $consumersecret) =  $keys;

            $this->api = new NetDNA($alias, $consumerkey, $consumersecret);
            add_action('admin_head', array(&$this, 'admin_head'));
        }
    }

    function admin_head() {
        $zone_id = $this->_config->get_string('cdn.netdna.zone_id');
        try {
            $zone_info = $this->api->get_pull_zone($zone_id);

            if (!$zone_info)
                return;
            $filetypes = $this->api->get_list_of_file_types_per_zone($zone_id);

            if (!isset($filetypes['filetypes']))
                return;
        } catch(Exception $ex) {
            return;
        }

        $filetypes = $filetypes['filetypes'];
        $group_hits = NetDNAPresentation::group_hits_per_filetype_group($filetypes);

        $list = array();
        $colors = array();
        foreach ($group_hits as $group => $hits) {
            $list[] = sprintf("['%s', %d]", $group, $hits);
            $colors[] = '\'' . NetDNAPresentation::get_file_group_color($group) . '\'';
        }
    ?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Filetype', 'Hits'],<?php
echo "                ", implode(',', $list);
                ?>
            ]);
            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            var options = {colors: [<?php echo implode(',', $colors) ?>]};
            chart.draw(data, options);
        }
    </script>
<?php
    }

    /**
     * Dashboard setup action
     *
     * @return void
     */
    function wp_dashboard_setup() {
            $view = '<span> </span>';
        w3tc_add_dashboard_widget('w3tc_netdna', $view, array(
            &$this,
            'widget_netdna'
        ), null, 'normal');
    }

    /**
     * Loads and configures NetDNA widget to be used in WP Dashboards.
     * @param $widget_id
     * @param array $form_inputs
     */
    function widget_netdna($widget_id, $form_inputs = array()) {
        $authorized = $this->authorized;
        $have_zone = $this->have_zone;
        $is_sealed = $this->_sealed;
        $error = '';
        $pull_zones = array();
        $zone_info = false;
        if ($authorized && $have_zone) {
            $zone_id = $this->_config->get_integer('cdn.netdna.zone_id');
            try{
                $zone_info = $this->api->get_pull_zone($zone_id);
            } catch(Exception $ex) {
                $error = $ex->getMessage();
                $zone_info = false;
            }
            if ($zone_info) {
                $content_zone = $zone_info['name'];
                try{
                    $summary = $this->api->get_stats_per_zone($zone_id);
                    $filetypes = $this->api->get_list_of_file_types_per_zone($zone_id);
                    $popular_files = $this->api->get_list_of_popularfiles_per_zone($zone_id);
                    $popular_files = NetDNAPresentation::format_popular($popular_files);
                    $popular_files = array_slice($popular_files, 0 , 5);
                    $account = $this->api->get_account();
                    $account_status = NetDNAPresentation::get_account_status($account['status']);
                    include W3TC_INC_WIDGET_DIR . '/netdna.php';
                } catch(Exception $ex) {
                    try {
                        $pull_zones = $this->api->get_zones_by_url(home_url());
                    } catch(Exception $ex) {}
                    $error = $ex->getMessage();
                    include W3TC_INC_WIDGET_DIR . '/netdna_signup.php';
                }
            } else {
                try {
                    $pull_zones = $this->api->get_zones_by_url(home_url());
                } catch(Exception $ex) {}
                include W3TC_INC_WIDGET_DIR . '/netdna_signup.php';
            }
        } else {
            include W3TC_INC_WIDGET_DIR . '/netdna_signup.php';
        }
    }

    /**
     * @param W3_Config $config
     */
    private function _setup($config) {
        $this->authorized = $config->get_string('cdn.netdna.authorization_key') != '' &&
            $config->get_string('cdn.engine') == 'netdna';
        $keys = explode('+', $config->get_string('cdn.netdna.authorization_key'));
        $this->authorized = $this->authorized  && sizeof($keys) == 3;

        $this->have_zone = $config->get_string('cdn.netdna.zone_id') != 0;
    }

    public function enqueue() {
        wp_enqueue_style('w3tc-widget');
        wp_enqueue_script('w3tc-metadata');
        wp_enqueue_script('w3tc-widget');
    }
}
