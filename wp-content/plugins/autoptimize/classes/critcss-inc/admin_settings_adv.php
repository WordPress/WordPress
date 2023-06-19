<?php
/**
 * Contains the function to render the advanced panel.
 */

/**
 * Function to render the advanced panel.
 */
function ao_ccss_render_adv() {
    $criticalcss = autoptimize()->criticalcss();

    $ao_ccss_debug       = esc_attr( $criticalcss->get_option( 'debug' ) );
    $ao_ccss_finclude    = esc_textarea( $criticalcss->get_option( 'finclude' ) );
    $ao_ccss_rtimelimit  = esc_attr( $criticalcss->get_option( 'rtimelimit' ) );
    $ao_ccss_noptimize   = esc_attr( $criticalcss->get_option( 'noptimize' ) );
    $ao_ccss_loggedin    = esc_attr( $criticalcss->get_option( 'loggedin' ) );
    $ao_ccss_forcepath   = esc_attr( $criticalcss->get_option( 'forcepath' ) );
    $ao_ccss_deferjquery = esc_attr( $criticalcss->get_option( 'deferjquery' ) );
    $ao_ccss_domain      = esc_attr( $criticalcss->get_option( 'domain' ) );
    $ao_ccss_unloadccss  = esc_attr( $criticalcss->get_option( 'unloadccss' ) );

    // In case domain is not set yet (done in cron.php).
    if ( empty( $ao_ccss_domain ) ) {
        $ao_ccss_domain = get_site_url();
    }

    // Get viewport size.
    $viewport = $criticalcss->viewport();
    ?>
    <ul id="adv-panel">
        <li class="itemDetail">
            <h2 class="itemTitle fleft"><?php _e( 'Advanced Settings', 'autoptimize' ); ?></h2>
            <button type="button" class="toggle-btn">
                <span class="toggle-indicator dashicons dashicons-arrow-up dashicons-arrow-down"></span>
            </button>
            <div class="collapsible hidden">
                <table id="key" class="form-table">
                    <tr>
                        <th scope="row">
                            <?php _e( 'Viewport Size', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <label for="autoptimize_ccss_vw"><?php _e( 'Width', 'autoptimize' ); ?>:</label> <input type="number" id="autoptimize_ccss_vw" name="autoptimize_ccss_viewport[w]" min="800" max="4096" placeholder="1400" value="<?php echo $viewport['w']; ?>" />&nbsp;&nbsp;
                            <label for="autoptimize_ccss_vh"><?php _e( 'Height', 'autoptimize' ); ?>:</label> <input type="number" id="autoptimize_ccss_vh" name="autoptimize_ccss_viewport[h]" min="600" max="2160" placeholder="1080" value="<?php echo $viewport['h']; ?>" />
                            <p class="notes">
                                <?php _e( '<a href="https://criticalcss.com/account/api-keys?aff=1" target="_blank">criticalcss.com</a> default viewport size is 1400x1080 pixels (width x height). You can change this size by typing a desired width and height values above. Allowed value ranges are from 800 to 4096 for width and from 600 to 2160 for height.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Force Include CSS selectors', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <textarea id="autoptimize_ccss_finclude" name="autoptimize_ccss_finclude" rows='3' maxlenght='500' style="width:100%;" placeholder="<?php _e( '.button-special,//#footer', 'autoptimize' ); ?>"><?php echo trim( esc_textarea( $ao_ccss_finclude ) ); ?></textarea>
                            <p class="notes">
                                <?php _e( 'Force include CSS selectors can be used to style dynamic content that is not part of the HTML that is seen during the Critical CSS generation. To use this feature, add comma separated values with both simple strings and/or regular expressions to match the desired selectors. Regular expressions must be preceeded by two forward slashes. For instance: <code>.button-special,//#footer</code>. In this example <code>.button-special</code> will match <code>.button-special</code> selector only, while <code>//#footer</code> will match <code>#footer</code>, <code>#footer-address</code> and <code>#footer-phone</code> selectors in case they exist.<br />Do take into account that changing this setting will only affect new/ updated rules, so you might want to remove old rules and clear your page cache to expedite the forceIncludes becoming used.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Queue processing time limit', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="number" id="autoptimize_ccss_rtimelimit" name="autoptimize_ccss_rtimelimit" min="0" max="240" placeholder="0" value="<?php echo $ao_ccss_rtimelimit; ?>" />
                            <p class="notes">
                                <?php _e( 'The cronned queue processing is an asynchronous process triggerd by (WordPress) cron. To avoid this process from running too long and potentially getting killed, you can set the number of seconds here, 0 means no limit.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Fetch Original CSS', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="checkbox" id="autoptimize_ccss_noptimize" name="autoptimize_ccss_noptimize" value="1" <?php checked( 1 == $ao_ccss_noptimize ); ?>>
                            <p class="notes">
                                <?php _e( 'In some (rare) cases the generation of critical CSS works better with the original CSS instead of the Autoptimized one, this option enables that behavior.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Add CCSS for logged in users?', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="checkbox" id="autoptimize_ccss_loggedin" name="autoptimize_ccss_loggedin" value="1" <?php checked( 1 == $ao_ccss_loggedin ); ?>>
                            <p class="notes">
                                <?php _e( 'Critical CSS is generated by criticalcss.com from your pages as seen by an "anonymous visitor". Disable this option if you don\'t want the "visitor" critical CSS to be used for logged in users.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Force path-based rules to be generated for pages?', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="checkbox" id="autoptimize_ccss_forcepath" name="autoptimize_ccss_forcepath" value="1" <?php checked( 1 == $ao_ccss_forcepath ); ?>>
                            <p class="notes">
                                <?php _e( 'By default for each page a separate rule is generated. If your pages have (semi-)identical above the fold look and feel and you want to keep the rules lean, you can disable that so one rule is created to all pages.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <?php if ( 1 == $ao_ccss_deferjquery ) { ?>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Defer jQuery and other non-aggregated JS-files? (deprecated)', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="checkbox" id="autoptimize_ccss_deferjquery" name="autoptimize_ccss_deferjquery" value="1" <?php checked( 1 == $ao_ccss_deferjquery ); ?>>
                            <p class="notes">
                                <?php _e( 'Defer all non-aggregated JS, including jQuery and inline JS to fix remaining render-blocking issues. Make sure to test your site thoroughly when activating this option!', 'autoptimize' ); ?>
                                <?php _e( '<b>This functionality will be removed in a next major version of Autoptimize</b>, being replaced by the combination of the "do not aggregate but defer JS" + "defer inline JS" options on the main settings page.', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Unload critical CSS after page load?', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="checkbox" id="autoptimize_ccss_unloadccss" name="autoptimize_ccss_unloadccss" value="1" <?php checked( 1 == $ao_ccss_unloadccss ); ?>>
                            <p class="notes">
                                <?php _e( 'In rare cases the critical CSS needs to be removed once the full CSS loads, this option makes it so!', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Bound domain', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="text" id="autoptimize_ccss_domain" name="autoptimize_ccss_domain" style="width:100%;" placeholder="<?php _e( 'Don\'t leave this empty, put e.g. https://example.net/ or simply \'none\' to disable domain binding.', 'autoptimize' ); ?>" value="<?php echo trim( esc_attr( $ao_ccss_domain ) ); ?>">
                            <p class="notes">
                                <?php _e( 'Only requests from this domain will be sent for Critical CSS generation (pricing is per domain/ month).', 'autoptimize' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Debug Mode', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="checkbox" id="autoptimize_ccss_debug" name="autoptimize_ccss_debug" value="1" <?php checked( 1 == $ao_ccss_debug ); ?>>
                            <p class="notes">
                                <?php
                                _e( '<strong>CAUTION! Only use debug mode on production/live environments for ad-hoc troubleshooting and remember to turn it back off after</strong>, as this generates a lot of log-data.<br />Check the box above to enable Autoptimize CriticalCSS Power-Up debug mode. It provides debug facilities in this screen, to the browser console and to this file: ', 'autoptimize' );
                                echo '<code>' . AO_CCSS_LOG . '</code>';
                                ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </li>
    </ul>
    <?php
}
?>
