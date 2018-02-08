#! /bin/bash

if [[ -f wp-login.php && -f wp-admin/load-scripts.php && -f wp-admin/includes/noop.php ]]
then
        sed -i "1 s/^.*$/<?php\ndefine('CONCATENATE_SCRIPTS', false);/" wp-login.php
        sed -i -e "s/^require( ABSPATH . WPINC . '\/script-loader.php' );$/require( ABSPATH . 'wp-admin\/admin.php' );/g" wp-admin/load-scripts.php
        sed -i -e "s/^require( ABSPATH . WPINC . '\/script-loader.php' );$/require( ABSPATH . 'wp-admin\/admin.php' );/g" wp-admin/load-styles.php
        echo """<?php
/**
* Noop functions for load-scripts.php and load-styles.php.
*
* @package WordPress
* @subpackage Administration
* @since 4.4.0
*/

function get_file( \$path ) {
        if ( function_exists('realpath') ) {
                \$path = realpath( \$path );
        }
        if ( ! \$path || ! @is_file( \$path ) ) {
                return '';
        }
        return @file_get_contents( \$path );    
}""" > wp-admin/includes/noop.php
		echo 'Successfuly patched.'
else
        echo 'Please run this file from WordPress root directory.'
fi
