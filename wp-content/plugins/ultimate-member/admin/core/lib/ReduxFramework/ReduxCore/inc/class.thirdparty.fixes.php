<?php
    // Fix for the GT3 page builder: http://www.gt3themes.com/wordpress-gt3-page-builder-plugin/
    /** @global string $pagenow */
    if ( has_action( 'ecpt_field_options_' ) ) {
        global $pagenow;
        if ( $pagenow === 'admin.php' ) {

            remove_action( 'admin_init', 'pb_admin_init' );
        }
    }