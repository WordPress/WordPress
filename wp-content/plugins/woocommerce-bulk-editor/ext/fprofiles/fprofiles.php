<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//Profiles of filter sets
final class WOOBE_FPROFILES extends WOOBE_EXT {

    protected $slug = 'fprofiles'; //unique
    protected $fprofiles = null;

    public function __construct() {
        include_once $this->get_ext_path() . 'models/profiles.php';
        $this->fprofiles = new WOOBE_FILTER_PROFILES($this->settings);

        add_action('woobe_ext_scripts', array($this, 'woobe_ext_scripts'), 1);
        add_action('woobe_tools_panel_buttons', array($this, 'woobe_tools_panel_buttons'), 1);
        add_action('woobe_page_end', array($this, 'woobe_page_end'), 1);
    }

    public function woobe_ext_scripts() {
        wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js',array(),WOOBE_VERSION);
        wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css',array(),WOOBE_VERSION);
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            //lang.<?php echo $this->slug ?>.test = '<?php esc_html_e('test', 'woocommerce-bulk-editor') ?>';
        </script>
        <?php
    }

    public function woobe_tools_panel_buttons() {
        ?>
        <a href="#" class="button button-secondary woobe_tools_panel_fprofile_btn" title="<?php esc_html_e('Filters profiles', 'woocommerce-bulk-editor') ?>"></a>
        <?php
    }

    public function woobe_page_end() {
        $data = array();
        $data['fprofiles'] = $this->fprofiles->get();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

}
