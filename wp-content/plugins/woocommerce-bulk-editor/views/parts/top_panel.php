<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>
<div class="woobe_top_panel_container">

    <div class="woobe_top_panel">
        <div class="woobe_top_panel_wrapper">

            <div id="tabs_f" class="woobe-tabs woobe-tabs-style-shape">

                <nav>
                    <ul><?php do_action('woobe_ext_top_panel_tabs'); //including extensions scripts    ?></ul>
                </nav>

                <div class="content-wrap"><?php do_action('woobe_ext_top_panel_tabs_content'); //including extensions scripts    ?></div>

            </div>

            <a href="#" class="button button-large button-primary woobe_top_panel_btn2" title="<?php esc_html_e('Close the panel', 'woocommerce-bulk-editor') ?>"></a>
        </div>
    </div>

    <div class="woobe_top_panel_slide"><a href="#" class="woobe_top_panel_btn"><?php esc_html_e('Show: Filters/Bulk Edit/Export', 'woocommerce-bulk-editor') ?></a></div>

</div>
