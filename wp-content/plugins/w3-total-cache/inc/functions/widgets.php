<?php

/**
 * Registers dashboard widgets.
 *
 * Handles POST data, sets up filters.
 *
 * @since 0.9.2.6
 */
function w3tc_dashboard_setup() {
    global $w3tc_registered_widgets, $w3tc_registered_widget_controls, $w3tc_dashboard_control_callbacks;
    $w3tc_dashboard_control_callbacks = array();
    $screen = get_current_screen();

    $update = false;
    $widget_options = get_option( 'w3tc_dashboard_widget_options' );
    if ( !$widget_options || !is_array($widget_options) )
        $widget_options = array();

    // Hook to register new widgets
    // Filter widget order
    if ( is_network_admin() ) {
        do_action( 'w3tc_network_dashboard_setup' );
        $dashboard_widgets = apply_filters( 'w3tc_network_dashboard_widgets', array() );
    } else {
        do_action( 'w3tc_dashboard_setup' );
        $dashboard_widgets = apply_filters( 'w3tc_dashboard_widgets', array() );
    }

    foreach ( $dashboard_widgets as $widget_id ) {
        $name = empty( $w3tc_registered_widgets[$widget_id]['all_link'] ) ? $w3tc_registered_widgets[$widget_id]['name'] : $w3tc_registered_widgets[$widget_id]['name'] . " <a href='{$w3tc_registered_widgets[$widget_id]['all_link']}' class='edit-box open-box'>" . __('View all') . '</a>';
        w3tc_add_dashboard_widget( $widget_id, $name, $w3tc_registered_widgets[$widget_id]['callback'], $w3tc_registered_widget_controls[$widget_id]['callback'] );
    }

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) ) {
        check_admin_referer( 'edit-dashboard-widget_' . $_POST['widget_id'], 'dashboard-widget-nonce' );
        ob_start(); // hack - but the same hack wp-admin/widgets.php uses
        w3tc_dashboard_trigger_widget_control( $_POST['widget_id'] );
        ob_end_clean();
    }

    if ( $update )
        update_option( 'w3tc_dashboard_widget_options', $widget_options );

    do_action('do_meta_boxes', $screen->id, 'normal', '');
    do_action('do_meta_boxes', $screen->id, 'side', '');
}

function w3tc_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null,$location = 'normal' , $title_image = '' ) {
    $screen = get_current_screen();
    global $w3tc_dashboard_control_callbacks;
    if ($title_image && $title_image != 'div')
        $widget_name = '<img class="' . $widget_id . '" src="' . esc_attr($title_image) . '" />'.$widget_name;
    else if ($title_image == 'div')
        $widget_name = '<div class="w3_widget_logo"></div>' . $widget_name;

    if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
        $w3tc_dashboard_control_callbacks[$widget_id] = $control_callback;
        if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
            list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
            $widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
            $callback = '_w3tc_dashboard_control_callback';
        } else {
            list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
            $widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
        }
    }

    $side_widgets = array();

    $priority = 'core';

    add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority );
}


/* Dashboard Widgets Controls */
function _w3tc_dashboard_control_callback( $dashboard, $meta_box ) {
    echo '<form action="" method="post" class="dashboard-widget-control-form">';
    w3tc_dashboard_trigger_widget_control( $meta_box['id'] );
    wp_nonce_field( 'edit-dashboard-widget_' . $meta_box['id'], 'dashboard-widget-nonce' );
    echo '<input type="hidden" name="widget_id" value="' . esc_attr($meta_box['id']) . '" />';
    submit_button( __('Submit') );
    echo '</form>';
}

/**
 * Calls widget control callback.
 *
 * @since 0.9.2.6
 *
 * @param int|bool $widget_control_id Registered Widget ID.
 */
function w3tc_dashboard_trigger_widget_control( $widget_control_id = false ) {
    global $w3tc_dashboard_control_callbacks;

    if ( is_scalar($widget_control_id) && $widget_control_id && isset($w3tc_dashboard_control_callbacks[$widget_control_id]) && is_callable($w3tc_dashboard_control_callbacks[$widget_control_id]) ) {
        call_user_func( $w3tc_dashboard_control_callbacks[$widget_control_id], '', array( 'id' => $widget_control_id, 'callback' => $w3tc_dashboard_control_callbacks[$widget_control_id] ) );
    }
}