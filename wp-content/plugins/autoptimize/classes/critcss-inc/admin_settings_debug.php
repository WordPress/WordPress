<?php
/**
 * Debug panel.
 */

// Attach wpdb() object.
global $wpdb;

// Query AO's options.
$ao_options = $wpdb->get_results(
    '
    SELECT option_name  AS name,
    option_value AS value
    FROM ' . $wpdb->options . '
    WHERE option_name LIKE "autoptimize_%%"
    ORDER BY name
    ',
    ARRAY_A
);

// Query AO's transients.
$ao_trans = $wpdb->get_results(
    '
    SELECT option_name  AS name,
    option_value AS value
    FROM ' . $wpdb->options . '
    WHERE option_name LIKE "_transient_autoptimize_%%"
    OR option_name LIKE "_transient_timeout_autoptimize_%%"
    ',
    ARRAY_A
);

// Render debug panel if there's something to show.
if ( $ao_options || $ao_trans ) {
    ?>
<!-- BEGIN: Settings Debug -->
<ul>
    <li class="itemDetail">
        <h2 class="itemTitle"><?php _e( 'Debug Information', 'autoptimize' ); ?></h2>

        <?php
        // Render options.
        if ( $ao_options ) {
            ?>
            <h4><?php _e( 'Options', 'autoptimize' ); ?>:</h4>
            <table class="form-table debug">
            <?php
            foreach ( $ao_options as $option ) {
                ?>
                <tr>
                    <th scope="row">
                        <?php echo wp_strip_all_tags( $option['name'] ); ?>
                    </th>
                    <td>
                        <?php
                        if ( 'autoptimize_ccss_queue' == $option['name'] || 'autoptimize_ccss_rules' == $option['name'] ) {
                            $value = print_r( json_decode( wp_strip_all_tags( $option['value'] ), true ), true );
                            if ( $value ) {
                                echo "Raw JSON:\n<pre>" . wp_strip_all_tags( $option['value'] ) . "</pre>\n\nDecoded JSON:\n<pre>" . wp_strip_all_tags( $value ) . '</pre>';
                            } else {
                                echo 'Empty';
                            }
                        } else {
                            echo wp_strip_all_tags( $option['value'] );
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </table>
            <hr />
            <?php
        }
        // Render WP-Cron intervals and scheduled events.
        ?>
        <h4><?php _e( 'WP-Cron Intervals', 'autoptimize' ); ?>:</h4>
        <pre><?php print_r( wp_get_schedules() ); ?></pre>
        <hr />
        <h4><?php _e( 'WP-Cron Scheduled Events', 'autoptimize' ); ?>:</h4>
        <pre><?php print_r( _get_cron_array() ); ?></pre>
    </li>
</ul>
<!-- END: Settings Debug -->
    <?php
}
