<?php

    /**
     * ReduxFrameworkInstances Functions
     *
     * @package     Redux_Framework
     * @subpackage  Core
     */
    if ( ! function_exists( 'get_redux_instance' ) ) {

        /**
         * Retreive an instance of ReduxFramework
         *
         * @param  string $opt_name the defined opt_name as passed in $args
         *
         * @return object                ReduxFramework
         */
        function get_redux_instance( $opt_name ) {
            return ReduxFrameworkInstances::get_instance( $opt_name );
        }
    }

    if ( ! function_exists( 'get_all_redux_instances' ) ) {

        /**
         * Retreive all instances of ReduxFramework
         * as an associative array.
         *
         * @return array        format ['opt_name' => $ReduxFramework]
         */
        function get_all_redux_instances() {
            return ReduxFrameworkInstances::get_all_instances();
        }
    }