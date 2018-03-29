<?php
/**
 *       @file  qtrExternalScanner.php
 *      @brief  This module contains API to query external scanner
 *
 *     @author  Quttera (qtr), contactus@quttera.com
 *
 *   @internal
 *     Created  01/17/2016
 *    Compiler  gcc/g++
 *     Company  Quttera
 *   Copyright  Copyright (c) 2016, Quttera
 *
 * This source code is released for free distribution under the terms of the
 * GNU General Public License as published by the Free Software Foundation.
 * =====================================================================================
 */


class CQtrExternalScanner
{

    /**
    * @brief       sends request to remote scanner 
    * @param[in]   remote_url - URL query 
    * @return      on success returns retireved Json, on failure empty string
    */
    public static function SendQuery ( $remote_url )
    {
        if( function_exists('curl_init') )
        {
        /* curl library loaded */ 
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $remote_url );
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        }else{
            return file_get_contents( $remote_url );
        }
    }

}


?>
