<?php
/**
 *       @file  qtrConfig.php
 *      @brief  This module contains scanner configuration parameters
 *
 *     @author  Quttera (qtr), contactus@quttera.com
 *
 *   @internal
 *     Created  01/13/2016
 *    Compiler  gcc/g++
 *     Company  Quttera
 *   Copyright  Copyright (c) 2016, Quttera
 *
 * This source code is released for free distribution under the terms of the
 * GNU General Public License as published by the Free Software Foundation.
 * =====================================================================================
 */

class CQtrConfig
{
    
    public static  $LOG_SEVERITY = array( "ERROR" => 0, "WARNING" => 1, "INFO" => 2, "OFF" => 3 );
    public static  $LOG_SEVERITY_NAMES = array( "ERROR","WARNING","INFO","OFF");

    
    /**
     * @brief   current log severity to be used
     * @return  appropriate log severity 
     */
    public function LogSeverity(){
        return self::$LOG_SEVERITY["INFO"];
    }


    /**
     * @brief       converts log severity to appropriate name (string)
     * @param[in]   val - severity value
     * @return      appropriate string mapping
     */
    public function LogSeverityStr( $val ){
        if( $val < 0 ){
            return self::$LOG_SEVERITY_NAMES[0];
        }
        else if( $val >= count( self::$LOG_SEVERITY_NAMES ) ){
            return self::$LOG_SEVERITY_NAMES[3];
        }
        else{
            return self::$LOG_SEVERITY_NAMES[$val];
        }
    }

    public function PatternsDbName(){
        return "patterns.db";
    }

}

?>
