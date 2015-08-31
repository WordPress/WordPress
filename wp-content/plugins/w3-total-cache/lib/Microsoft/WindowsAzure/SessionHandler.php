<?php
/**
 * Copyright (c) 2009 - 2010, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Session
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: Storage.php 21617 2009-06-12 10:46:31Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/** Microsoft_WindowsAzure_Storage_Table */
require_once 'Microsoft/WindowsAzure/Storage/Table.php';

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Session
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_SessionHandler
{
    /**
     * Table storage
     *
     * @var Microsoft_WindowsAzure_Storage_Table
     */
    protected $_tableStorage;

    /**
     * Session table name
     *
     * @var string
     */
    protected $_sessionTable;

    /**
     * Session table partition
     *
     * @var string
     */
    protected $_sessionTablePartition;

    /**
     * Creates a new Microsoft_WindowsAzure_SessionHandler instance
     *
     * @param Microsoft_WindowsAzure_Storage_Table $tableStorage Table storage
     * @param string $sessionTable Session table name
     * @param string $sessionTablePartition Session table partition
     */
    public function __construct(Microsoft_WindowsAzure_Storage_Table $tableStorage, $sessionTable = 'phpsessions', $sessionTablePartition = 'sessions')
	{
	    // Set properties
		$this->_tableStorage = $tableStorage;
		$this->_sessionTable = $sessionTable;
		$this->_sessionTablePartition = $sessionTablePartition;
	}

	/**
	 * Registers the current session handler as PHP's session handler
	 *
	 * @return boolean
	 */
	public function register()
	{
        return session_set_save_handler(array($this, 'open'),
                                        array($this, 'close'),
                                        array($this, 'read'),
                                        array($this, 'write'),
                                        array($this, 'destroy'),
                                        array($this, 'gc')
        );
	}

    /**
     * Open the session store
     *
     * @return bool
     */
    public function open()
    {
    	// Make sure table exists
    	$tableExists = $this->_tableStorage->tableExists($this->_sessionTable);
    	if (!$tableExists) {
		    $this->_tableStorage->createTable($this->_sessionTable);
		}

		// Ok!
		return true;
    }

    /**
     * Close the session store
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Read a specific session
     *
     * @param int $id Session Id
     * @return string
     */
    public function read($id)
    {
        try
        {
            $sessionRecord = $this->_tableStorage->retrieveEntityById(
                $this->_sessionTable,
                $this->_sessionTablePartition,
                $id
            );
            return base64_decode($sessionRecord->serializedData);
        }
        catch (Microsoft_WindowsAzure_Exception $ex)
        {
            return '';
        }
    }

    /**
     * Write a specific session
     *
     * @param int $id Session Id
     * @param string $serializedData Serialized PHP object
     */
    public function write($id, $serializedData)
    {
        $sessionRecord = new Microsoft_WindowsAzure_Storage_DynamicTableEntity($this->_sessionTablePartition, $id);
        $sessionRecord->sessionExpires = time();
        $sessionRecord->serializedData = base64_encode($serializedData);

        $sessionRecord->setAzurePropertyType('sessionExpires', 'Edm.Int32');

        try
        {
            $this->_tableStorage->updateEntity($this->_sessionTable, $sessionRecord);
        }
        catch (Microsoft_WindowsAzure_Exception $unknownRecord)
        {
            $this->_tableStorage->insertEntity($this->_sessionTable, $sessionRecord);
        }
    }

    /**
     * Destroy a specific session
     *
     * @param int $id Session Id
     * @return boolean
     */
    public function destroy($id)
    {
        try
        {
            $sessionRecord = $this->_tableStorage->retrieveEntityById(
                $this->_sessionTable,
                $this->_sessionTablePartition,
                $id
            );
            $this->_tableStorage->deleteEntity($this->_sessionTable, $sessionRecord);

            return true;
        }
        catch (Microsoft_WindowsAzure_Exception $ex)
        {
            return false;
        }
    }

    /**
     * Garbage collector
     *
     * @param int $lifeTime Session maximal lifetime
     * @see session.gc_divisor  100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability 1
     * @usage Execution rate 1/100 (session.gc_probability/session.gc_divisor)
     * @return boolean
     */
    public function gc($lifeTime)
    {
        try
        {
            $result = $this->_tableStorage->retrieveEntities($this->_sessionTable, 'PartitionKey eq \'' . $this->_sessionTablePartition . '\' and sessionExpires lt ' . (time() - $lifeTime));
            foreach ($result as $sessionRecord)
            {
                $this->_tableStorage->deleteEntity($this->_sessionTable, $sessionRecord);
            }
            return true;
        }
        catch (Microsoft_WindowsAzure_exception $ex)
        {
            return false;
        }
    }
}
