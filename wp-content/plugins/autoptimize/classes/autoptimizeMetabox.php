<?php
/**
 * Handles meta box to disable optimizations.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeMetabox
{
    public function __construct()
    {
        $this->run();
    }

    public function run()
    {
        add_action( 'add_meta_boxes', array( $this, 'ao_metabox_add_box' ) );
        add_action( 'save_post', array( $this, 'ao_metabox_save' ) );
        add_action( 'wp_ajax_ao_metabox_ccss_addjob', array( $this, 'ao_metabox_generateccss_callback' ) );
    }

    public function ao_metabox_add_box()
    {
        $screens = array(
            'post',
            'page',
            // add extra types e.g. product or ... ?
        );

        $screens = apply_filters( 'autoptimize_filter_metabox_screens', $screens );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'ao_metabox',
                __( 'Autoptimize this page', 'autoptimize' ),
                array( $this, 'ao_metabox_content' ),
                $screen,
                'side'
            );
        }
    }

    /**
     * Prints the box content.
     *
     * @param WP_Post $post The object for the current post/page.
     */
    function ao_metabox_content( $post )
    {
        // phpcs:disable Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace

        wp_nonce_field( 'ao_metabox', 'ao_metabox_nonce' );

        $ao_opt_value = $this->check_ao_opt_sanity( get_post_meta( $post->ID, 'ao_post_optimize', true ) );

        $_ao_meta_sub_opacity = '';
        if ( 'on' !== $ao_opt_value['ao_post_optimize'] ) {
            $_ao_meta_sub_opacity = 'opacity:.33;';
        }
        ?>
        <p >
            <input type="checkbox" id="autoptimize_post_optimize" class="ao_meta_main" name="ao_post_optimize" <?php echo 'on' !== $ao_opt_value['ao_post_optimize'] ? '' : 'checked="checked" '; ?> />
            <label for="autoptimize_post_optimize">
                 <?php _e( 'Optimize this page?', 'autoptimize' ); ?>
            </label>
        </p>
        <?php
        $_ao_meta_js_style = '';
        if ( 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_js', false ) ) {
            $_ao_meta_js_style = 'display:none;';
        }
        echo '<p class="ao_meta_sub" style="' . $_ao_meta_sub_opacity . $_ao_meta_js_style . '">';
        ?>
        <input type="checkbox" id="autoptimize_post_optimize_js" name="ao_post_js_optimize" <?php echo 'on' !== $ao_opt_value['ao_post_js_optimize'] ? '' : 'checked="checked" '; ?> />
            <label for="autoptimize_post_optimize_js">
                 <?php _e( 'Optimize JS?', 'autoptimize' ); ?>
            </label>
        </p>
        <?php
        $_ao_meta_css_style = '';
        if ( 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_css', false ) ) {
            $_ao_meta_css_style = 'display:none;';
        }
        echo '<p class="ao_meta_sub" style="' . $_ao_meta_sub_opacity . $_ao_meta_css_style . '">';
        ?>
        <input type="checkbox" id="autoptimize_post_optimize_css" name="ao_post_css_optimize" <?php echo 'on' !== $ao_opt_value['ao_post_css_optimize'] ? '' : 'checked="checked" '; ?> />
            <label for="autoptimize_post_optimize_css">
                 <?php _e( 'Optimize CSS?', 'autoptimize' ); ?>
            </label>
        </p>
        <?php
        $_ao_meta_ccss_style = '';
        if ( 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_css_defer', false ) || 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_css', false ) ) {
            $_ao_meta_ccss_style = 'display:none;';
        }
        if ( 'on' !== $ao_opt_value['ao_post_css_optimize'] ) {
            $_ao_meta_ccss_style .= 'opacity:.33;';
        }
        echo '<p class="ao_meta_sub ao_meta_sub_css" style="' . $_ao_meta_sub_opacity . $_ao_meta_ccss_style . '">';
        ?>
            <input type="checkbox" id="autoptimize_post_ccss" name="ao_post_ccss" <?php echo 'on' !== $ao_opt_value['ao_post_ccss'] ? '' : 'checked="checked" '; ?> />
            <label for="autoptimize_post_ccss">
                 <?php _e( 'Inline critical CSS?', 'autoptimize' ); ?>
            </label>
        </p>
        <?php
        $_ao_meta_lazyload_style = '';
        if ( false === autoptimizeImages::should_lazyload_wrapper( true ) ) {
            $_ao_meta_lazyload_style = 'display:none;';
        }
        echo '<p class="ao_meta_sub" style="' . $_ao_meta_sub_opacity . $_ao_meta_lazyload_style . '">';
        ?>
            <input type="checkbox" id="autoptimize_post_lazyload" name="ao_post_lazyload" <?php echo 'on' !== $ao_opt_value['ao_post_lazyload'] ? '' : 'checked="checked" '; ?> />
            <label for="autoptimize_post_lazyload">
                 <?php _e( 'Lazyload images?', 'autoptimize' ); ?>
            </label>
        </p>
        <?php
        $_ao_meta_preload_style = '';
        if ( false === autoptimizeImages::should_lazyload_wrapper() && false === autoptimizeImages::imgopt_active() ) {
            // img preload requires imgopt and/ or lazyload to be active.
            $_ao_meta_preload_style = 'opacity:.33;';
        }
        ?>
        <p class="ao_meta_sub ao_meta_preload" style="<?php echo $_ao_meta_sub_opacity . $_ao_meta_preload_style; ?>">
            <label for="autoptimize_post_preload">
                 <?php _e( 'LCP Image to preload', 'autoptimize' ); ?>
            </label>
            <?php
            if ( is_array( $ao_opt_value ) && array_key_exists( 'ao_post_preload', $ao_opt_value ) ) {
                $_preload_img = esc_attr( $ao_opt_value['ao_post_preload'] );
            } else {
                $_preload_img = '';
            }
            ?>
            <input type="text" id="autoptimize_post_preload" name="ao_post_preload" value="<?php echo $_preload_img; ?>">
        </p>
        <?php
            echo apply_filters( 'autoptimize_filter_metabox_extra_ui', '');
        ?>
        <p>&nbsp;</p>
        <p>
            <?php
            // Get path + check if button should be enabled or disabled.
            $_generate_disabled = true;
            $_slug              = false;
            $_type              = 'is_single';

            // harvest post ID from URL, get permalink from that and extract path from that.
            if ( array_key_exists( 'post', $_GET ) ) {
                $_slug = str_replace( AUTOPTIMIZE_WP_SITE_URL, '', get_permalink( $_GET['post'] ) );
            }

            // override the default 'is_single' if post.
            global $post;
            if ( 'page' === $post->post_type ) {
                $_type = 'is_page';
            }

            // if CSS opt and inline & defer are on and if we have a slug, the button can be active.
            if ( false !== $_slug && 'on' === autoptimizeOptionWrapper::get_option( 'autoptimize_css', false ) && 'on' === autoptimizeOptionWrapper::get_option( 'autoptimize_css_defer', false ) && ! empty( apply_filters( 'autoptimize_filter_ccss_key', autoptimizeOptionWrapper::get_option( 'autoptimize_ccss_key', false ) ) ) && '2' === autoptimizeOptionWrapper::get_option( 'autoptimize_ccss_keyst', false ) ) {
                $_generate_disabled = false;
            }
            ?>
            <button class="button ao_meta_sub ao_meta_sub_css" id="generateccss" style="<?php echo $_ao_meta_sub_opacity . $_ao_meta_ccss_style; ?>" <?php if ( true === $_generate_disabled ) { echo 'disabled'; } ?>><?php _e( 'Generate Critical CSS', 'autoptimize' ); ?></button>
        </p>
        <script>
            jQuery(document).ready(function() {
                jQuery( "#autoptimize_post_optimize" ).change(function() {
                    if (this.checked) {
                        jQuery(".ao_meta_sub:visible").fadeTo("fast",1);
                    } else {
                        jQuery(".ao_meta_sub:visible").fadeTo("fast",.33);
                    }
                });
                jQuery( "#autoptimize_post_optimize_css" ).change(function() {
                    if (this.checked) {
                        jQuery(".ao_meta_sub_css:visible").fadeTo("fast",1);
                    } else {
                        jQuery(".ao_meta_sub_css:visible").fadeTo("fast",.33);
                    }
                });
                jQuery( "#autoptimize_post_ccss" ).change(function() {
                    if (this.checked) {
                        jQuery("#generateccss:visible").fadeTo("fast",1);
                    } else {
                        jQuery("#generateccss:visible").fadeTo("fast",.33);
                    }
                });
                <?php
                if ( true === autoptimizeImages::should_lazyload_wrapper() && false === autoptimizeImages::imgopt_active() ) {
                ?>
                    jQuery( "#autoptimize_post_lazyload" ).change(function() {
                        if (this.checked) {
                            jQuery(".ao_meta_preload:visible").fadeTo("fast",1);
                        } else {
                            jQuery(".ao_meta_preload:visible").fadeTo("fast",.33);
                        }                    
                    });
                <?php
                }
                ?>
                jQuery("#generateccss").click(function(e){
                    e.preventDefault();
                    // disable button to avoid it being clicked several times.
                    jQuery("#generateccss").prop('disabled', true);
                    var data = {
                        'action': 'ao_metabox_ccss_addjob',
                        'path'  : '<?php echo $_slug; ?>',
                        'type'  : '<?php echo $_type; ?>',
                        'ao_ccss_addjob_nonce': '<?php echo wp_create_nonce( 'ao_ccss_addjob_nonce' ); ?>',
                    };

                    jQuery.post(ajaxurl, data, function(response) {
                        response_array=JSON.parse(response);
                        if (response_array['code'] == 200) {
                            setCritCSSbutton("<?php _e( 'Added to CCSS job queue.', 'autoptimize' ); ?>", "green");
                        } else {
                            setCritCSSbutton("<?php _e( 'Could not add to CCSS job queue.', 'autoptimize' ); ?>", "orange");
                        }
                    }).fail(function() {
                        setCritCSSbutton("<?php _e( 'Sorry, something went wrong.', 'autoptimize' ); ?>", "orange");
                    });
                });
            });

            function setCritCSSbutton( message, color) {
                jQuery("#generateccss").html(message);
                jQuery("#generateccss").prop("style","border-color:" + color + "!important; color:" + color + "!important");
            }
        </script>
        <?php
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function ao_metabox_save( $post_id )
    {
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // check if from our own data.
        if ( ! isset( $_POST['ao_metabox_nonce'] ) ) {
            return $post_id;
        }

        // Check if our nonce is set and verify if valid.
        $nonce = $_POST['ao_metabox_nonce'];
        if ( ! wp_verify_nonce( $nonce, 'ao_metabox' ) ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        // OK, we can have a look at the actual data now.
        // Sanitize user input.
        foreach ( apply_filters( 'autoptimize_filter_meta_valid_optims', array( 'ao_post_optimize', 'ao_post_js_optimize', 'ao_post_css_optimize', 'ao_post_ccss', 'ao_post_lazyload', 'ao_post_preload' ) ) as $opti_type ) {
            if ( in_array( $opti_type, apply_filters( 'autoptimize_filter_meta_optim_nonbool', array( 'ao_post_preload' ) ) ) ) {
                if ( isset( $_POST[ $opti_type ] ) ) {
                    $ao_meta_result[ $opti_type ] = $_POST[ $opti_type ];
                } else {
                    $ao_meta_result[ $opti_type ] = false;
                }
            } else if ( ! isset( $_POST[ $opti_type ] ) ) {
                $ao_meta_result[ $opti_type ] = '';
            } else if ( 'on' === $_POST[ $opti_type ] ) {
                $ao_meta_result[ $opti_type ] = 'on';
            } 
        }

        // Update the meta field in the database.
        update_post_meta( $post_id, 'ao_post_optimize', $ao_meta_result );
    }

    public function ao_metabox_generateccss_callback()
    {
        check_ajax_referer( 'ao_ccss_addjob_nonce', 'ao_ccss_addjob_nonce' );

        if ( current_user_can( 'manage_options' ) && array_key_exists( 'path', $_POST ) && ! empty( $_POST['path'] ) ) {
            if ( array_key_exists( 'type', $_POST ) && 'is_page' === $_POST['type'] ) {
                $type = 'is_page';
            } else {
                $type = 'is_single';
            }

            $path = wp_strip_all_tags( $_POST['path'] );
            $criticalcss = autoptimize()->criticalcss();
            $_result = $criticalcss->enqueue( '', $path, $type );

            if ( $_result ) {
                $response['code']   = '200';
                $response['string'] = $path . ' added to job queue.';
            } else {
                $response['code']   = '404';
                $response['string'] = 'could not add ' . $path . ' to job queue.';
            }
        } else {
            $response['code']   = '500';
            $response['string'] = 'nok';
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function get_metabox_default_values()
    {
        $ao_metabox_defaults = array(
            'ao_post_optimize'     => 'on',
            'ao_post_js_optimize'  => 'on',
            'ao_post_css_optimize' => 'on',
            'ao_post_ccss'         => 'on',
            'ao_post_lazyload'     => 'on',
            'ao_post_preload'      => '',
        );
        return $ao_metabox_defaults;
    }

    public function check_ao_opt_sanity( $ao_opt_val ) {
        if ( empty( $ao_opt_val ) ) {
            $ao_opt_val = $this->get_metabox_default_values();
        } else {
            foreach ( array( 'ao_post_optimize', 'ao_post_js_optimize', 'ao_post_css_optimize', 'ao_post_ccss', 'ao_post_lazyload' ) as $key ) {
                if ( ! array_key_exists( $key, $ao_opt_val ) ) {
                    $ao_opt_val[ $key ] = 'off';
                }
            }
        }

        return $ao_opt_val;
    }
}
