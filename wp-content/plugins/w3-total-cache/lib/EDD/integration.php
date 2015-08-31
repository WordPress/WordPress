<?php

/**
 * Activates a license
 *
 * @param $license
 * @param $version
 * @return array|bool|mixed
 */
function edd_w3edge_w3tc_activate_license($license, $version) {

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> $license,
            'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ), // the name of our product in EDD
            'r' => rand(),
            'version' => $version
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        return $license_data;
}

/**
 * Deactivates a license
 *
 * @param string $license
 * @return bool
 */
function edd_w3edge_w3tc_deactivate_license($license) {
        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'license' 	=> $license,
            'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ), // the name of our product in EDD,
            'r' => rand()
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "deactivated" or "failed"
        return $license_data->license == 'deactivated';
}

/**
 * Checks if a license key is still valid
 *
 * @param $license
 * @param $version
 * @return array|bool|mixed
 */
function edd_w3edge_w3tc_check_license($license, $version) {

    global $wp_version;

    $api_params = array(
        'edd_action' => 'check_license',
        'license' => $license,
        'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ),
        'r' => rand(),
        'version' => $version
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    if ( is_wp_error( $response ) )
        return false;
    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
    return $license_data;
}

/**
 * Verifies a license key
 *
 * @param $license
 * @param $version
 * @return array|bool|mixed
 */
function edd_w3edge_w3tc_verify_license($license, $version) {

    global $wp_version;

    $api_params = array(
        'edd_action' => 'verify_license',
        'license' => $license,
        'item_name' => urlencode( EDD_W3EDGE_W3TC_NAME ),
        'r' => rand(),
        'version' => $version
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, EDD_W3EDGE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    if ( is_wp_error( $response ) )
        return false;
    $body = wp_remote_retrieve_body( $response );
    $license_data = json_decode( $body );
    return $license_data;
}

/**
 * Return product type
 * @param bool $license
 * @param $version
 * @return mixed
 */
function edd_w3edge_w3tc_check_license_product($license, $version) {
    $license = edd_w3edge_w3tc_check_license($license, $version);
    return $license->type;
}
