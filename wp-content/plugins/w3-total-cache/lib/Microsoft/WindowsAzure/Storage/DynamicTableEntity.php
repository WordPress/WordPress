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
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: BlobInstance.php 14561 2009-05-07 08:05:12Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';

/**
 * @see Microsoft_WindowsAzure_Storage_TableEntity
 */
require_once 'Microsoft/WindowsAzure/Storage/TableEntity.php';


/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_Storage_DynamicTableEntity extends Microsoft_WindowsAzure_Storage_TableEntity
{
    /**
     * Dynamic properties
     *
     * @var array
     */
    protected $_dynamicProperties = array();

    /**
     * Magic overload for setting properties
     *
     * @param string $name     Name of the property
     * @param string $value    Value to set
     */
    public function __set($name, $value) {
        $this->setAzureProperty($name, $value, null);
    }

    /**
     * Magic overload for getting properties
     *
     * @param string $name     Name of the property
     */
    public function __get($name) {
        return $this->getAzureProperty($name);
    }

    /**
     * Set an Azure property
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @param string $type Property type (Edm.xxxx)
     * @return Microsoft_WindowsAzure_Storage_DynamicTableEntity
     */
    public function setAzureProperty($name, $value = '', $type = null)
    {
        if (strtolower($name) == 'partitionkey') {
            $this->setPartitionKey($value);
        } else if (strtolower($name) == 'rowkey') {
            $this->setRowKey($value);
        } else if (strtolower($name) == 'etag') {
            $this->setEtag($value);
        } else {
            if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
                // Determine type?
                if (is_null($type)) {
                    $type = 'Edm.String';
                    if (is_int($value)) {
                        $type = 'Edm.Int32';
                    } else if (is_float($value)) {
                        $type = 'Edm.Double';
                    } else if (is_bool($value)) {
                        $type = 'Edm.Boolean';
                    }
                }

                // Set dynamic property
                $this->_dynamicProperties[strtolower($name)] = (object)array(
                        'Name'  => $name,
                    	'Type'  => $type,
                    	'Value' => $value,
                    );
            }

            $this->_dynamicProperties[strtolower($name)]->Value = $value;
        }
        return $this;
    }

    /**
     * Set an Azure property type
     *
     * @param string $name Property name
     * @param string $type Property type (Edm.xxxx)
     * @return Microsoft_WindowsAzure_Storage_DynamicTableEntity
     */
    public function setAzurePropertyType($name, $type = 'Edm.String')
    {
        if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
            $this->setAzureProperty($name, '', $type);
        } else {
            $this->_dynamicProperties[strtolower($name)]->Type = $type;
        }
        return $this;
    }

    /**
     * Get an Azure property
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @param string $type Property type (Edm.xxxx)
     * @return Microsoft_WindowsAzure_Storage_DynamicTableEntity
     */
    public function getAzureProperty($name)
    {
        if (strtolower($name) == 'partitionkey') {
            return $this->getPartitionKey();
        }
        if (strtolower($name) == 'rowkey') {
            return $this->getRowKey();
        }
        if (strtolower($name) == 'etag') {
            return $this->getEtag();
        }

        if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
            $this->setAzureProperty($name);
        }

        return $this->_dynamicProperties[strtolower($name)]->Value;
    }

    /**
     * Get an Azure property type
     *
     * @param string $name Property name
     * @return string Property type (Edm.xxxx)
     */
    public function getAzurePropertyType($name)
    {
        if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
            $this->setAzureProperty($name, '', $type);
        }

        return $this->_dynamicProperties[strtolower($name)]->Type;
    }

    /**
     * Get Azure values
     *
     * @return array
     */
    public function getAzureValues()
    {
        return array_merge(array_values($this->_dynamicProperties), parent::getAzureValues());
    }

    /**
     * Set Azure values
     *
     * @param array $values
     * @param boolean $throwOnError Throw Microsoft_WindowsAzure_Exception when a property is not specified in $values?
     * @throws Microsoft_WindowsAzure_Exception
     */
    public function setAzureValues($values = array(), $throwOnError = false)
    {
        // Set parent values
        parent::setAzureValues($values, false);

        // Set current values
        foreach ($values as $key => $value)
        {
            $this->$key = $value;
        }
    }
}
