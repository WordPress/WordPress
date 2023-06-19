<?php
/**
 * Render the rules panel.
 */

/**
 * Main function to render the rules panel.
 */
function ao_ccss_render_rules() {
    // Attach required arrays.
    $criticalcss   = autoptimize()->criticalcss();
    $ao_ccss_rules = sanitize_rules( $criticalcss->get_option( 'rules' ) );
    $ao_ccss_types = $criticalcss->get_types();

    if ( empty( $ao_ccss_types ) || ! is_array( $ao_ccss_types ) ) {
        $ao_ccss_types = array( 'No conditionals, check CSS optimization settings.' );
    }
    ?>
    <ul id="rules-panel">
        <li class="itemDetail">
            <h2 class="itemTitle"><?php _e( 'Rules', 'autoptimize' ); ?></h2>

            <!-- BEGIN Rule dialogs -->
            <!-- Unsaved dialog -->
            <div id="unSavedWarning" class="hidden updated settings-error notice notice-warning is-dismissible">
                <p><?php _e( "<strong>Rules or Queue changed!</strong> Don't forget to save your changes!", 'autoptimize' ); ?></p>
            </div>

            <!-- Create/edit rule dialog -->
            <div id="addEditCritCss" class="hidden">
                <table class="form-table rules">
                    <tr id="critcss_addedit_type_wrapper">
                        <th scope="row">
                            <?php _e( 'Rule Type', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <select id="critcss_addedit_type" style="width:100%;">
                                <option value="paths"><?php _e( 'Path', 'autoptimize' ); ?></option>
                                <option value="types"><?php _e( 'Conditional Tag', 'autoptimize' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="critcss_addedit_path_wrapper">
                        <th scope="row">
                            <?php _e( 'String in Path', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <input type="text" id="critcss_addedit_path" placeholder="<?php _e( "Enter a part of the URL that identifies the page(s) you're targetting.", 'autoptimize' ); ?>" style="width:100%;" value="">
                        </td>
                    </tr>
                    <tr id="critcss_addedit_pagetype_wrapper">
                        <th scope="row">
                            <?php _e( 'Conditional Tag, Custom Post Type or Page Template', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <select id="critcss_addedit_pagetype" style="width:100%;">
                                <option value="" disabled selected><?php _e( 'Select from the list below...', 'autoptimize' ); ?></option>
                                <optgroup label="<?php _e( 'Standard Conditional Tags', 'autoptimize' ); ?>">
                                    <?php
                                    // Render grouped simple conditional tags.
                                    foreach ( $ao_ccss_types as $ctag ) {
                                        $optgrp = substr( $ctag, 0, 3 );
                                        if ( substr( $ctag, 0, 3 ) === 'is_' ) {
                                            echo '<option value="' . $ctag . '">' . $ctag . '</option>';
                                        }
                                        $prevgrp = substr( $ctag, 0, 3 );
                                    }

                                    // Render grouped custom post types, templates and specific conditional tags.
                                    foreach ( $ao_ccss_types as $type ) {
                                        $optgrp = substr( $type, 0, 3 );

                                        // Option groups labels.
                                        if ( $optgrp !== $prevgrp && 'is_' !== $optgrp ) {
                                            ?>
                                            </optgroup>
                                            <?php
                                            if ( substr( $type, 0, 12 ) === 'custom_post_' ) {
                                                ?>
                                                <optgroup label="<?php _e( 'Custom Post Types', 'autoptimize' ); ?>">
                                                <?php
                                            } elseif ( substr( $type, 0, 9 ) === 'template_' ) {
                                                ?>
                                                <optgroup label="<?php _e( 'Page Templates', 'autoptimize' ); ?>">
                                                <?php
                                            } elseif ( substr( $type, 0, 4 ) === 'bbp_' ) {
                                                ?>
                                                <optgroup label="<?php _e( 'BBPress Conditional Tags', 'autoptimize' ); ?>">
                                                <?php
                                            } elseif ( substr( $type, 0, 3 ) === 'bp_' ) {
                                                ?>
                                                <optgroup label="<?php _e( 'BuddyPress Conditional Tags', 'autoptimize' ); ?>">
                                                <?php
                                            } elseif ( substr( $type, 0, 4 ) === 'edd_' ) {
                                                ?>
                                                <optgroup label="<?php _e( 'Easy Digital Downloads Conditional Tags', 'autoptimize' ); ?>">
                                                <?php
                                            } elseif ( substr( $type, 0, 4 ) === 'woo_' ) {
                                                ?>
                                                <optgroup label="<?php _e( 'WooCommerce Conditional Tags', 'autoptimize' ); ?>">
                                                <?php
                                            }
                                        }

                                        // Options.
                                        if ( 'is_' !== $optgrp ) {
                                            // Remove prefix from custom post types, templates and some specific conditional tags.
                                            if ( substr( $type, 0, 12 ) === 'custom_post_' ) {
                                                $_type = str_replace( 'custom_post_', '', $type );
                                            } elseif ( substr( $type, 0, 9 ) === 'template_' ) {
                                                $_type = str_replace( 'template_', '', $type );
                                            } elseif ( 'bbp_is_bbpress' === $type ) {
                                                $_type = str_replace( 'bbp_', '', $type );
                                            } elseif ( 'bp_is_buddypress' === $type ) {
                                                $_type = str_replace( 'bp_', '', $type );
                                            } elseif ( substr( $type, 0, 4 ) === 'woo_' ) {
                                                $_type = str_replace( 'woo_', '', $type );
                                            } elseif ( substr( $type, 0, 4 ) === 'edd_' ) {
                                                $_type = str_replace( 'edd_', '', $type );
                                            } else {
                                                $_type = $type;
                                            }

                                            echo '<option value="' . $type . '">' . $_type . '</option>';
                                            $prevgrp = $optgrp;
                                        }
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Custom Critical CSS', 'autoptimize' ); ?>
                        </th>
                        <td>
                            <textarea id="critcss_addedit_css" rows="13" cols="10" style="width:100%;" placeholder="<?php _e( 'Paste your specific critical CSS here and hit submit to save.', 'autoptimize' ); ?>"></textarea>
                            <input type="hidden" id="critcss_addedit_file">
                            <input type="hidden" id="critcss_addedit_id">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Remove dialog -->
            <div id="confirm-rm" title="<?php _e( 'Delete Rule', 'autoptimize' ); ?>" class="hidden">
                <p><?php _e( 'This Critical CSS rule will be deleted immediately and cannot be recovered.<br /><br /><strong>Are you sure?</strong>', 'autoptimize' ); ?></p>
            </div>

            <!-- Remove All dialog -->
            <div id="confirm-rm-all" title="<?php _e( 'Delete all Rules and Jobs', 'autoptimize' ); ?>" class="hidden">
                <p><?php _e( 'All Critical CSS rules will be deleted immediately and cannot be recovered.<br /><br /><strong>Are you sure?</strong>', 'autoptimize' ); ?></p>
            </div>

            <!-- Add/edit default critical CSS dialog -->
            <div id="default_critcss_wrapper" class="hidden">
                <textarea id="dummyDefault" rows="19" cols="10" style="width:100%;" placeholder="<?php _e( 'Paste your minified default critical CSS here and hit submit to save. This is the critical CSS to be used for every page not matching any rule.', 'autoptimize' ); ?>"></textarea>
            </div>

            <!-- Add/edit additional critical CSS dialog -->
            <div id="additional_critcss_wrapper" class="hidden">
                <textarea id="dummyAdditional" rows="19" cols="10" style="width:100%;" placeholder="<?php _e( 'Paste your minified additional critical CSS here and hit submit to save. This is the CSS to be added AT THE END of every critical CSS provided by a matching rule, or the default one.', 'autoptimize' ); ?>"></textarea>
            </div>

            <!-- BEGIN Rules UI -->
            <div class="howto">
                <div class="title-wrap">
                    <h4 class="title"><?php _e( 'How To Use Autoptimize CriticalCSS Rules', 'autoptimize' ); ?></h4>
                    <p class="subtitle"><?php _e( 'Click the side arrow to toggle instructions', 'autoptimize' ); ?></p>
                </div>
                <button type="button" class="toggle-btn">
                    <span class="toggle-indicator dashicons dashicons-arrow-up dashicons-arrow-down"></span>
                </button>
                <div class="howto-wrap hidden">
                <p><?php _e( "TL;DR:<br />Critical CSS files from <span class='badge auto'>AUTO</span> <strong>rules are updated automatically</strong> while from <span class='badge manual'>MANUAL</span> <strong>rules are not.</strong>", 'autoptimize' ); ?></p>
                    <ol>
                        <li><?php _e( 'When a valid <a href="https://criticalcss.com/?aff=1" target="_blank">criticalcss.com</a> API key is in place, Autoptimize starts to operate <strong>automatically</strong>.', 'autoptimize' ); ?></li>
                        <li><?php _e( 'Upon a request to any of the frontend pages made by a <strong>not logged in user</strong>, it will <strong>asynchronously</strong> fetch and update the critical CSS from <a href="https://criticalcss.com/?aff=1" target="_blank">criticalcss.com</a> for conditional tags you have on your site (e.g. is_page, is_single, is_archive etc.)', 'autoptimize' ); ?></li>
                        <li><?php _e( 'These requests also creates an <span class="badge auto">AUTO</span> rule for you. The critical CSS files from <span class="badge auto">AUTO</span> <strong>rules are updated automatically</strong> when a CSS file in your theme or frontend plugins changes.', 'autoptimize' ); ?></li>
                        <li><?php _e( 'If you want to make any fine tunning in the critical CSS file of an <span class="badge auto">AUTO</span> rule, click on "Edit" button of that rule, change what you need, submit and save it. The rule you\'ve just edited becomes a <span class="badge manual">MANUAL</span> rule then.', 'autoptimize' ); ?></li>
                        <li><?php _e( 'You can create <span class="badge manual">MANUAL</span> rules for specific page paths (URL). Longer, more specific paths have higher priority over shorter ones, which in turn have higher priority over <span class="badge auto">AUTO</span> rules. Also, critical CSS files from <span class="badge manual">MANUAL</span> <strong>rules are NEVER updated automatically.</strong>', 'autoptimize' ); ?></li>
                        <li><?php _e( 'At any time you can delete an <span class="badge auto">AUTO</span> or <span class="badge manual">MANUAL</span> rule by cliking on "Remove" button of the desired rule and saving your changes.', 'autoptimize' ); ?></li>
                    </ol>
                </div>
            </div>
            <textarea id="autoptimize_css_defer_inline" name="autoptimize_css_defer_inline" rows="19" cols="10" style="width:100%;"><?php echo autoptimizeStyles::sanitize_css( get_option( 'autoptimize_css_defer_inline', '' ) ); ?></textarea>
            <textarea id="autoptimize_ccss_additional" name="autoptimize_ccss_additional" rows="19" cols="10" style="width:100%;"><?php echo autoptimizeStyles::sanitize_css( get_option( 'autoptimize_ccss_additional', '' ) ); ?></textarea>
            <table class="rules-list" cellspacing="0"><tbody id="rules-list"></tbody></table>
            <input class="hidden" type="text" id="critCssOrigin" name="autoptimize_ccss_rules" value='<?php echo ( json_encode( $ao_ccss_rules, JSON_FORCE_OBJECT ) ); ?>'>
            <!-- Wrapper for in screen notices -->
            <div id="rules-notices"></div>
            <!-- END Rule add/edit dialogs -->
            <div class="submit rules-btn">
                <div class="alignleft">
                    <span id="addCritCssButton" class="button-secondary"><?php _e( 'Add New Rule', 'autoptimize' ); ?></span>
                    <span id="editDefaultButton" class="button-secondary"><?php _e( 'Edit Default Rule CSS', 'autoptimize' ); ?></span>
                    <span id="editAdditionalButton" class="button-secondary"><?php _e( 'Add CSS To All Rules', 'autoptimize' ); ?></span>
                </div>
                <div class="alignright">
                    <span id="removeAllRules" class="button-secondary" style="color:red;"><?php _e( 'Remove all rules', 'autoptimize' ); ?></span>
                </div>
            </div>
            <!-- END Rules UI -->
        </li>
    </ul>
    <?php
}

/**
 * Sanitize rules before rendering.
 *
 * @param array $rules Array with rules to be sanitized.
 */
function sanitize_rules( $rules ) {
    if ( apply_filters( 'autoptimize_filter_ccss_paths_clickable', true ) ) {
        if ( array_key_exists( 'paths', $rules ) ) {
            foreach ( $rules['paths'] as $key => $value ) {
                $newkey = esc_url( $key );
                if ( $newkey !== $key ) {
                    if ( 0 === strpos( $newkey, 'http://' ) && 0 !== strpos( $key, 'http://' ) ) {
                        // esc_url adds "http://" to any string that does not start with either a protocol or a
                        // slash, see https://developer.wordpress.org/reference/functions/esc_url/#more-information
                        // this removes that unneeded protocol again.
                        $newkey = substr_replace( $newkey, '', 0, 7 );
                    }
                    unset( $rules['paths'][ $key ] );
                    $rules['paths'][ $newkey ] = $value;
                }
            }
        }
    }
    
    $rules = autoptimizeUtils::strip_tags_array( $rules );
    return $rules;
}

?>
