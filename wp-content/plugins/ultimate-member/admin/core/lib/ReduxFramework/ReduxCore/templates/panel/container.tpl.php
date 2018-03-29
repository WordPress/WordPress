<?php
    /**
     * The template for the main panel container.
     * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
     *
     * @author        Redux Framework
     * @package       ReduxFramework/Templates
     * @version: 3.5.7.8
     */


    $expanded = ( $this->parent->args['open_expanded'] ) ? ' fully-expanded' : '' . ( ! empty( $this->parent->args['class'] ) ? ' ' . esc_attr( $this->parent->args['class'] ) : '' );
    $nonce    = wp_create_nonce( "redux_ajax_nonce" . $this->parent->args['opt_name'] );
?>
<div class="redux-container<?php echo esc_attr( $expanded ); ?>">
    <?php $action = ( $this->parent->args['database'] == "network" && $this->parent->args['network_admin'] && is_network_admin() ? './edit.php?action=redux_' . $this->parent->args['opt_name'] : './options.php' ) ?>
    <form method="post" 
          action="<?php echo esc_attr($action); ?>" 
          data-nonce="<?php echo esc_attr($nonce); ?>" 
          enctype="multipart/form-data"
          id="redux-form-wrapper">
        <?php // $this->parent->args['opt_name'] is sanitized in the Framework class, no need to re-sanitize it. ?>
        <input type="hidden" id="redux-compiler-hook"
            name="<?php echo $this->parent->args['opt_name']; ?>[compiler]"
            value=""/>
        <?php // $this->parent->args['opt_name'] is sanitized in the Framework class, no need to re-sanitize it. ?>
        <input type="hidden" id="currentSection"
            name="<?php echo $this->parent->args['opt_name']; ?>[redux-section]"
            value=""/>
        <?php // $this->parent->args['opt_name'] is sanitized in the Framework class, no need to re-sanitize it. ?>
        <?php if ( ! empty( $this->parent->no_panel ) ) { ?>
            <input type="hidden" 
                name="<?php echo $this->parent->args['opt_name']; ?>[redux-no_panel]"
                value="<?php echo esc_attr(implode( '|', $this->parent->no_panel )); ?>"
            />
        <?php } ?>
        <?php
            // Must run or the page won't redirect properly
            $this->init_settings_fields();

            // Last tab?
            $this->parent->options['last_tab'] = ( isset( $_GET['tab'] ) && ! isset( $this->parent->transients['last_save_mode'] ) ) ? esc_attr( $_GET['tab'] ) : '';
        ?>
        <?php // $this->parent->args['opt_name'] is sanitized in the Framework class, no need to re-sanitize it. ?>
        <input type="hidden" 
               id="last_tab" 
               name="<?php echo $this->parent->args['opt_name']; ?>[last_tab]"
               value="<?php echo esc_attr( $this->parent->options['last_tab'] ); ?>"
        />

        <?php $this->get_template( 'content.tpl.php' ); ?>

    </form>
</div>

<?php if ( isset( $this->parent->args['footer_text'] ) ) { ?>
    <div id="redux-sub-footer"><?php echo wp_kses_post( $this->parent->args['footer_text'] ); ?></div>
<?php } ?>
