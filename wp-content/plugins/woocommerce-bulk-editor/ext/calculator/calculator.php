<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//calculator for numerical textinputs
final class WOOBE_CALCULATOR extends WOOBE_EXT {

    protected $slug = 'calculator'; //unique

    public function __construct() {
        add_action('woobe_ext_scripts', array($this, 'woobe_ext_scripts'), 1);
        add_action('woobe_page_end', array($this, 'woobe_page_end'), 1);
    }

    public function woobe_ext_scripts() {
        wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js',array(),WOOBE_VERSION);
        wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css',array(),WOOBE_VERSION);
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            //lang.<?php echo $this->slug ?>.xxx = 'xxx';
        </script>
        <?php
    }

    public function woobe_page_end() {
        $data = array();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

}
