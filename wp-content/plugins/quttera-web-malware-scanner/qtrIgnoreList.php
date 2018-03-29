<?php
/**
 *       @file  qtrIgnoreList.php
 *      @brief  This module contains implementation of a list of ignored threats
 *
 *     @author  Quttera (qtr), contactus@quttera.com
 *
 *   @internal
 *     Created  01/21/2016
 *    Compiler  gcc/g++
 *     Company  Quttera
 *   Copyright  Copyright (c) 2016, Quttera
 *
 * This source code is released for free distribution under the terms of the
 * GNU General Public License as published by the Free Software Foundation.
 * =====================================================================================
 */


define( 'QTR_IGNORE_LIST','quttera_wp_ignore_list');

class CQtrIgnoreList
{
    protected $_type = QTR_IGNORE_LIST;

    public function __construct( $type = NULL )
    {
        $this->_config      = new CQtrConfig();
        $this->_logger      = new CQtrLogger();
        $this->_report      = array();
        if( $type != NULL ){
            $this->_type = $type;
        }else{
            $type = QTR_IGNORE_LIST;
        }

        $this->_LoadList();
    }

    public function Add($file_sig,$threat_sig)
    {
        $this->_LoadList();
        $key = $this->_BuildKey( $file_sig , $threat_sig );
        if(isset( $this->_report[$key]) ){
            return FALSE;
        }

        $this->_report[$key] = array( $file_sig, $threat_sig );
        $this->_StoreList();
        return TRUE;
    }

    public function GetList(){
        return $this->_report;
    }

    public function Get( $file_sig, $threat_sig )
    {
        $this->_LoadList();

        $key = $this->_BuildKey( $file_sig,$threat_sig);

        if( isset( $this->_report[$key] ) ){
            return $this->_report[$key];
        }

        return NULL;
    }

    public function Remove( $file_sig, $threat_sig )
    {
        $this->_LoadList();
        $key = $this->_BuildKey( $file_sig, $threat_sig );
        if( isset( $this->_report[$key] ) ){
            unset( $this->_report[$key] );
            $this->_StoreList();
        }
    
        return FALSE;
    }


    public function Clean( )
    {
        $this->_report = array();
        $this->_StoreList();
        return TRUE;
    }


    /***************************************************************************
     *
     *      PROTECTED METHODS
     *
     **************************************************************************/
    protected function _BuildKey( $file, $threat )
    {
        return $file . ":" . $threat;
    }

    protected function _LoadList()
    {
        $body   = CQtrOptions::GetOption( $this->_type );

        if( $body )
        {
            $this->_report = CQtrOptions::Unserialize( $body );

            if( !is_array( $this->_report ) ){
                /* 
                 * something gone wrong, reset report
                 */
                $this->_report = array();
            }
        }else{
            /*
             * nothing found
             */
            $this->_report = array();
        }

        return TRUE;
    }

    protected function _StoreList( )
    {

        $body = CQtrOptions::Serialize( $this->_report );

        if ( CQtrOptions::GetOption( $this->_type ) !== false ) 
        {
            $rc = CQtrOptions::UpdateOption( $this->_type , $body );
        }
        else 
        {
            $deprecated = null;
            $autoload   = 'no';
            return CQtrOptions::AddOption( $this->_type, $body ,$deprecated, $autoload );
        }
    }   

}


?>
