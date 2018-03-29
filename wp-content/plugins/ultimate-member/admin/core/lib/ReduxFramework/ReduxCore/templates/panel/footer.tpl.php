<?php
    /**
     * The template for the panel footer area.
     * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
     *
     * @author        Redux Framework
     * @package       ReduxFramework/Templates
     * @version:      3.5.8.3
     */
?>
<div id="redux-sticky-padder" style="display: none;">&nbsp;</div>
<div id="redux-footer-sticky">
    <div id="redux-footer">
<?php 
        if ( isset( $this->parent->args['share_icons'] )) { 

            $skip_icons = false;
            if (!$this->parent->args['dev_mode'] && $this->parent->omit_share_icons ) {
                $skip_icons = true;
            }
?>
            <div id="redux-share">
<?php 
                foreach ( $this->parent->args['share_icons'] as $link ) {
                    if ($skip_icons) {
                        continue;
                    }
                    
                    // SHIM, use URL now
                    if ( isset( $link['link'] ) && ! empty( $link['link'] ) ) {
                        $link['url'] = $link['link'];
                        unset( $link['link'] );
                    }
?>
                    <a href="<?php echo esc_url( $link['url'] ) ?>" title="<?php echo esc_attr( $link['title'] ); ?>" target="_blank">
                        <?php if ( isset( $link['icon'] ) && ! empty( $link['icon'] ) ) : ?>
                            <i class="<?php
                                if ( strpos( $link['icon'], 'el-icon' ) !== false && strpos( $link['icon'], 'el ' ) === false ) {
                                    $link['icon'] = 'el ' . $link['icon'];
                                }
                                echo esc_attr( $link['icon'] );
                            ?>"></i>
                        <?php else : ?>
                            <img src="<?php echo esc_url( $link['img'] ); ?>"/>
                        <?php endif; ?>

                    </a>
                <?php } ?>

            </div>
        <?php } ?>

        <div class="redux-action_bar">
            <span class="spinner"></span>
<?php 
            if ( false === $this->parent->args['hide_save'] ) {
                submit_button( __( 'Save Changes', 'redux-framework' ), 'primary', 'redux_save', false );
            }

            if ( false === $this->parent->args['hide_reset'] ) {
                submit_button( __( 'Reset Section', 'redux-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false, array( 'id' => 'redux-defaults-section' ) );
                submit_button( __( 'Reset All', 'redux-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false, array( 'id' => 'redux-defaults' ) );
            } 
?>
        </div>

        <div class="redux-ajax-loading" alt="<?php _e( 'Working...', 'redux-framework' ) ?>">&nbsp;</div>
        <div class="clear"></div>

    </div>
</div>
