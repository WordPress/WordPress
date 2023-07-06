<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<!------------------ calculator for num textinputs of the products table --------------------------->
<a href="javascript: woobe_draw_calculator();void(0);" class="woobe_calculator_btn"></a>
<div id="woobe_calculator" style="display: none;">
    <table style="width: 100%;">
        <tr>
            <td>
                <select class="woobe_calculator_operation">
                    <option value="+">+</option>
                    <option value="-">-</option>
                    <option class="woobe_calc_rp" value="rp-" style="display: none;">rp-</option>
                    <option class="woobe_calc_sp" value="sp+" style="display: none;">sp+</option>
                </select>
            </td>
            <td style="width: 200px;">
                <input type="number" value="" style="width: 100%;" class="woobe_calculator_value" placeholder="<?php esc_html_e('enter operation value', 'woocommerce-bulk-editor') ?>" />
            </td>
            <td>
                <select class="woobe_calculator_how">
                    <option value="value">n</option>
                    <option value="percent">%</option>
                </select>
            </td>
            <td>
                <?php WOOBE_HELPER::draw_rounding_drop_down() ?>
            </td>
            <td>
                <a href="#" class="woobe_calculator_set button button-primary button-small"></a>&nbsp;<a href="#" class="woobe_calculator_close button button-primary button-small"></a>
            </td>
        </tr>
    </table>

</div>

