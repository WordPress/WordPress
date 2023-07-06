<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_INFO extends WOOBE_EXT {

    protected $slug = 'info'; //unique

    public function __construct() {
        //tabs
        $this->add_tab($this->slug, 'panel', esc_html__('Help', 'woocommerce-bulk-editor'), 'info');
        add_action('woobe_ext_panel_' . $this->slug, array($this, 'woobe_ext_panel'), 1);
    }

    public function woobe_ext_scripts() {
        //wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js');
        //wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css');
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            //lang.<?php echo $this->slug ?>.test = '<?php esc_html_e('test', 'woocommerce-bulk-editor') ?> ...';
        </script>
        <?php
    }

    public function woobe_ext_panel() {
        $data = array();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

}
