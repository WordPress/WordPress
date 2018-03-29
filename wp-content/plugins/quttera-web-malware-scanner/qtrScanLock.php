<?php
/**
 *       @file  qtrScanLock.php
 *      @brief  This module contains implelemtation of a scan lock to prevent concurrent execution of multiple scanners
 *
 *     @author  Quttera (qtr), contactus@quttera.com
 *
 *   @internal
 *     Created  01/20/2016
 *    Compiler  gcc/g++
 *     Company  Quttera
 *   Copyright  Copyright (c) 2016, Quttera
 *
 * This source code is released for free distribution under the terms of the
 * GNU General Public License as published by the Free Software Foundation.
 * =====================================================================================
 */

class CQtrScanLock
{

    public static function Acquire()
    {
        if( self::IsLocked() == TRUE ){
            /* 
             * Lock already taken
             */
            return FALSE;
        }

        return self::Lock();
    }


    public static function TryAcquire()
    {
        return self::Acquire();
    }


    public static function IsLocked()
    {
        if(!is_file( self::LockName() )){
            /*
             * lock file is missing
             */
            return FALSE;
        }

        $handle = @fopen(self::LockName(),"r");
        if( !$handle ){
            /*
             * Failed to open lock
             */
            return FALSE;
        }

        $contents = fread($handle, filesize(self::LockName()));
        fclose($handle);

        $pid = intval($contents);
        if( $pid == getmypid() ){
            /*
             * this PID locked the scan
             */
            return TRUE;
        }

        /*
         * if process running posix_kill return true otherwise false
         */
        /*
        $rc = posix_kill($pid,0);
        if( !$rc ){ 
            # process locked scan is not running anymore
            # remove lock file from system
            self::Release();
        }*/
        $rc = self::_IsProcessRunning($pid);
        if( !$rc ){
            self::Release();
        }
        return $rc;
    }

    public static function Lock()
    {
        $lock = @fopen(self::LockName(),"w");
        if( !$lock ){
            /*
             * Failed to open lock
             */
            return FALSE;
        }
        @fwrite($lock,sprintf("%d",getmypid()));
        @fflush($lock);
        @fclose($lock);
        return self::IsLocked();
    }

    public static function Release()
    {
        @unlink( self::LockName() );
        return TRUE;
    }

    public static function LockName()
    {
        $tmp_path       = NULL;

        if( function_exists("get_temp_dir") ){
            /*
             * WordPress internal implementation 
             */
            $tmp_path = get_temp_dir();
        }else{
            $tmp_path = @sys_get_temp_dir();
        }

        $site_name = self::_GetCurrentSite();
        $lock_file_name = "__QTR_LOCK__" . $site_name;
        return $tmp_path . DIRECTORY_SEPARATOR . $lock_file_name;
    }

    public static function ForceUnlock(){
        return self::Release();
    }

    protected static function _GetCurrentSite()
    {
        if(!function_exists('get_site_url') ){
            /*
             * running outside of WP
             */
            return "localhost";
        }

        $site = get_site_url();
        $site = str_replace(":","",$site);
        $site = str_replace("/","",$site);
        return $site;
    }

    protected static function _IsProcessRunning( $pid )
    {
        /*
        * if process running posix_kill return true otherwise false
        */
        $isRunning = false;
        if(strncasecmp(PHP_OS, "win", 3) == 0)
        {
            $out = array();
            exec("TASKLIST /FO LIST /FI \"PID eq $pid\"", $out);
            if(count($out) > 1) {
                $isRunning = true;
            }
        }
        elseif(posix_kill($pid, 0))
        {
            $isRunning = true;
        }
        return $isRunning;
    }

}

?>
