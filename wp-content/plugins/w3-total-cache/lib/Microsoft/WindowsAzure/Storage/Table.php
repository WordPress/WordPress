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
 * @version    $Id: Blob.php 14561 2009-05-07 08:05:12Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Credentials_CredentialsAbstract
 */
require_once 'Microsoft/WindowsAzure/Credentials/CredentialsAbstract.php';

/**
 * @see Microsoft_WindowsAzure_Credentials_SharedKey
 */
require_once 'Microsoft/WindowsAzure/Credentials/SharedKey.php';

/**
 * @see Microsoft_WindowsAzure_Credentials_SharedKeyLite
 */
require_once 'Microsoft/WindowsAzure/Credentials/SharedKeyLite.php';

/**
 * @see Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract
 */
require_once 'Microsoft/WindowsAzure/RetryPolicy/RetryPolicyAbstract.php';

/**
 * @see Microsoft_Http_Client
 */
require_once 'Microsoft/Http/Client.php';

/**
 * @see Microsoft_Http_Response
 */
require_once 'Microsoft/Http/Response.php';

/**
 * @see Microsoft_WindowsAzure_Storage
 */
require_once 'Microsoft/WindowsAzure/Storage.php';

/**
 * @see Microsoft_WindowsAzure_Storage_BatchStorageAbstract
 */
require_once 'Microsoft/WindowsAzure/Storage/BatchStorageAbstract.php';

/**
 * @see Microsoft_WindowsAzure_Storage_TableInstance
 */
require_once 'Microsoft/WindowsAzure/Storage/TableInstance.php';

/**
 * @see Microsoft_WindowsAzure_Storage_TableEntity
 */
require_once 'Microsoft/WindowsAzure/Storage/TableEntity.php';

/**
 * @see Microsoft_WindowsAzure_Storage_DynamicTableEntity
 */
require_once 'Microsoft/WindowsAzure/Storage/DynamicTableEntity.php';

/**
 * @see Microsoft_WindowsAzure_Storage_TableEntityQuery
 */
require_once 'Microsoft/WindowsAzure/Storage/TableEntityQuery.php';

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';


/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_Storage_Table
    extends Microsoft_WindowsAzure_Storage_BatchStorageAbstract
{
	/**
	 * Throw Microsoft_WindowsAzure_Exception when a property is not specified in Windows Azure?
	 * Defaults to true, making behaviour similar to Windows Azure StorageClient in .NET.
	 *
	 * @var boolean
	 */
	protected $_throwExceptionOnMissingData = true;

	/**
	 * Throw Microsoft_WindowsAzure_Exception when a property is not specified in Windows Azure?
	 * Defaults to true, making behaviour similar to Windows Azure StorageClient in .NET.
	 *
	 * @param boolean $value
	 */
	public function setThrowExceptionOnMissingData($value = true)
	{
		$this->_throwExceptionOnMissingData = $value;
	}

	/**
	 * Throw Microsoft_WindowsAzure_Exception when a property is not specified in Windows Azure?
	 */
	public function getThrowExceptionOnMissingData()
	{
		return $this->_throwExceptionOnMissingData;
	}

	/**
	 * Creates a new Microsoft_WindowsAzure_Storage_Table instance
	 *
	 * @param string $host Storage host name
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 * @param Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract $retryPolicy Retry policy to use when making requests
	 */
	public function __construct($host = Microsoft_WindowsAzure_Storage::URL_DEV_TABLE, $accountName = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT, $accountKey = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY, $usePathStyleUri = false, Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract $retryPolicy = null)
	{
		parent::__construct($host, $accountName, $accountKey, $usePathStyleUri, $retryPolicy);

	    // Always use SharedKeyLite authentication
	    $this->_credentials = new Microsoft_WindowsAzure_Credentials_SharedKeyLite($accountName, $accountKey, $this->_usePathStyleUri);

	    // API version
		$this->_apiVersion = '2009-09-19';
	}

	/**
	 * Check if a table exists
	 *
	 * @param string $tableName Table name
	 * @return boolean
	 */
	public function tableExists($tableName = '')
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}

		// List tables
        $tables = $this->listTables(); // 2009-09-19 does not support $this->listTables($tableName); all of a sudden...
        foreach ($tables as $table) {
            if ($table->Name == $tableName) {
                return true;
            }
        }

        return false;
	}

	/**
	 * List tables
	 *
	 * @param  string $nextTableName Next table name, used for listing tables when total amount of tables is > 1000.
	 * @return array
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function listTables($nextTableName = '')
	{
	    // Build query string
		$queryString = array();
	    if ($nextTableName != '') {
	        $queryString[] = 'NextTableName=' . $nextTableName;
	    }
	    $queryString = self::createQueryStringFromArray($queryString);

		// Perform request
		$response = $this->_performRequest('Tables', $queryString, Microsoft_Http_Client::GET, null, true);
		if ($response->isSuccessful()) {
		    // Parse result
		    $result = $this->_parseResponse($response);

		    if (!$result || !$result->entry) {
		        return array();
		    }

		    $entries = null;
		    if (count($result->entry) > 1) {
		        $entries = $result->entry;
		    } else {
		        $entries = array($result->entry);
		    }

		    // Create return value
		    $returnValue = array();
		    foreach ($entries as $entry) {
		        $tableName = $entry->xpath('.//m:properties/d:TableName');
		        $tableName = (string)$tableName[0];

		        $returnValue[] = new Microsoft_WindowsAzure_Storage_TableInstance(
		            (string)$entry->id,
		            $tableName,
		            (string)$entry->link['href'],
		            (string)$entry->updated
		        );
		    }

			// More tables?
		    if (!is_null($response->getHeader('x-ms-continuation-NextTableName'))) {
		        $returnValue = array_merge($returnValue, $this->listTables($response->getHeader('x-ms-continuation-NextTableName')));
		    }

		    return $returnValue;
		} else {
			throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Create table
	 *
	 * @param string $tableName Table name
	 * @return Microsoft_WindowsAzure_Storage_TableInstance
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function createTable($tableName = '')
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}

		// Generate request body
		$requestBody = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                        <entry
                        	xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices"
                        	xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata"
                        	xmlns="http://www.w3.org/2005/Atom">
                          <title />
                          <updated>{tpl:Updated}</updated>
                          <author>
                            <name />
                          </author>
                          <id />
                          <content type="application/xml">
                            <m:properties>
                              <d:TableName>{tpl:TableName}</d:TableName>
                            </m:properties>
                          </content>
                        </entry>';

        $requestBody = $this->_fillTemplate($requestBody, array(
            'BaseUrl' => $this->getBaseUrl(),
            'TableName' => htmlspecialchars($tableName),
        	'Updated' => $this->isoDate(),
            'AccountName' => $this->_accountName
        ));

        // Add header information
        $headers = array();
        $headers['Content-Type'] = 'application/atom+xml';
        $headers['DataServiceVersion'] = '1.0;NetFx';
        $headers['MaxDataServiceVersion'] = '1.0;NetFx';

		// Perform request
		$response = $this->_performRequest('Tables', '', Microsoft_Http_Client::POST, $headers, true, $requestBody);
		if ($response->isSuccessful()) {
		    // Parse response
		    $entry = $this->_parseResponse($response);

		    $tableName = $entry->xpath('.//m:properties/d:TableName');
		    $tableName = (string)$tableName[0];

		    return new Microsoft_WindowsAzure_Storage_TableInstance(
		        (string)$entry->id,
		        $tableName,
		        (string)$entry->link['href'],
		        (string)$entry->updated
		    );
		} else {
			throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Create table if it does not exist
	 *
	 * @param string $tableName Table name
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function createTableIfNotExists($tableName = '')
	{
		if (!$this->tableExists($tableName)) {
			$this->createTable($tableName);
		}
	}

	/**
	 * Delete table
	 *
	 * @param string $tableName Table name
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function deleteTable($tableName = '')
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}

        // Add header information
        $headers = array();
        $headers['Content-Type'] = 'application/atom+xml';

		// Perform request
		$response = $this->_performRequest('Tables(\'' . $tableName . '\')', '', Microsoft_Http_Client::DELETE, $headers, true, null);
		if (!$response->isSuccessful()) {
			throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Insert entity into table
	 *
	 * @param string                              $tableName   Table name
	 * @param Microsoft_WindowsAzure_Storage_TableEntity $entity      Entity to insert
	 * @return Microsoft_WindowsAzure_Storage_TableEntity
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function insertEntity($tableName = '', Microsoft_WindowsAzure_Storage_TableEntity $entity = null)
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}
		if (is_null($entity)) {
			throw new Microsoft_WindowsAzure_Exception('Entity is not specified.');
		}

		// Generate request body
		$requestBody = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                        <entry xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices" xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" xmlns="http://www.w3.org/2005/Atom">
                          <title />
                          <updated>{tpl:Updated}</updated>
                          <author>
                            <name />
                          </author>
                          <id />
                          <content type="application/xml">
                            <m:properties>
                              {tpl:Properties}
                            </m:properties>
                          </content>
                        </entry>';

        $requestBody = $this->_fillTemplate($requestBody, array(
        	'Updated'    => $this->isoDate(),
            'Properties' => $this->_generateAzureRepresentation($entity)
        ));

        // Add header information
        $headers = array();
        $headers['Content-Type'] = 'application/atom+xml';

		// Perform request
	    $response = null;
	    if ($this->isInBatch()) {
		    $this->getCurrentBatch()->enlistOperation($tableName, '', Microsoft_Http_Client::POST, $headers, true, $requestBody);
		    return null;
		} else {
		    $response = $this->_performRequest($tableName, '', Microsoft_Http_Client::POST, $headers, true, $requestBody);
		}
		if ($response->isSuccessful()) {
		    // Parse result
		    $result = $this->_parseResponse($response);

		    $timestamp = $result->xpath('//m:properties/d:Timestamp');
		    $timestamp = (string)$timestamp[0];

		    $etag      = $result->attributes('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
		    $etag      = (string)$etag['etag'];

		    // Update properties
		    $entity->setTimestamp($timestamp);
		    $entity->setEtag($etag);

		    return $entity;
		} else {
			throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Delete entity from table
	 *
	 * @param string                              $tableName   Table name
	 * @param Microsoft_WindowsAzure_Storage_TableEntity $entity      Entity to delete
	 * @param boolean                             $verifyEtag  Verify etag of the entity (used for concurrency)
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function deleteEntity($tableName = '', Microsoft_WindowsAzure_Storage_TableEntity $entity = null, $verifyEtag = false)
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}
		if (is_null($entity)) {
			throw new Microsoft_WindowsAzure_Exception('Entity is not specified.');
		}

        // Add header information
        $headers = array();
        if (!$this->isInBatch()) {
        	// http://social.msdn.microsoft.com/Forums/en-US/windowsazure/thread/9e255447-4dc7-458a-99d3-bdc04bdc5474/
            $headers['Content-Type']   = 'application/atom+xml';
        }
        $headers['Content-Length'] = 0;
        if (!$verifyEtag) {
            $headers['If-Match']       = '*';
        } else {
            $headers['If-Match']       = $entity->getEtag();
        }

		// Perform request
	    $response = null;
	    if ($this->isInBatch()) {
		    $this->getCurrentBatch()->enlistOperation($tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() . '\')', '', Microsoft_Http_Client::DELETE, $headers, true, null);
		    return null;
		} else {
		    $response = $this->_performRequest($tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() . '\')', '', Microsoft_Http_Client::DELETE, $headers, true, null);
		}
		if (!$response->isSuccessful()) {
		    throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Retrieve entity from table, by id
	 *
	 * @param string $tableName    Table name
	 * @param string $partitionKey Partition key
	 * @param string $rowKey       Row key
	 * @param string $entityClass  Entity class name*
	 * @return Microsoft_WindowsAzure_Storage_TableEntity
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function retrieveEntityById($tableName = '', $partitionKey = '', $rowKey = '', $entityClass = 'Microsoft_WindowsAzure_Storage_DynamicTableEntity')
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}
		if ($partitionKey === '') {
			throw new Microsoft_WindowsAzure_Exception('Partition key is not specified.');
		}
		if ($rowKey === '') {
			throw new Microsoft_WindowsAzure_Exception('Row key is not specified.');
		}
		if ($entityClass === '') {
			throw new Microsoft_WindowsAzure_Exception('Entity class is not specified.');
		}


		// Check for combined size of partition key and row key
		// http://msdn.microsoft.com/en-us/library/dd179421.aspx
		if (strlen($partitionKey . $rowKey) >= 256) {
		    // Start a batch if possible
		    if ($this->isInBatch()) {
		        throw new Microsoft_WindowsAzure_Exception('Entity cannot be retrieved. A transaction is required to retrieve the entity, but another transaction is already active.');
		    }

		    $this->startBatch();
		}

		// Fetch entities from Azure
        $result = $this->retrieveEntities(
            $this->select()
                 ->from($tableName)
                 ->wherePartitionKey($partitionKey)
                 ->whereRowKey($rowKey),
            '',
            $entityClass
        );

        // Return
        if (count($result) == 1) {
            return $result[0];
        }

        return null;
	}

	/**
	 * Create a new Microsoft_WindowsAzure_Storage_TableEntityQuery
	 *
	 * @return Microsoft_WindowsAzure_Storage_TableEntityQuery
	 */
	public function select()
	{
	    return new Microsoft_WindowsAzure_Storage_TableEntityQuery();
	}

	/**
	 * Retrieve entities from table
	 *
	 * @param string $tableName|Microsoft_WindowsAzure_Storage_TableEntityQuery    Table name -or- Microsoft_WindowsAzure_Storage_TableEntityQuery instance
	 * @param string $filter                                                Filter condition (not applied when $tableName is a Microsoft_WindowsAzure_Storage_TableEntityQuery instance)
	 * @param string $entityClass                                           Entity class name
	 * @param string $nextPartitionKey                                      Next partition key, used for listing entities when total amount of entities is > 1000.
	 * @param string $nextRowKey                                            Next row key, used for listing entities when total amount of entities is > 1000.
	 * @return array Array of Microsoft_WindowsAzure_Storage_TableEntity
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function retrieveEntities($tableName = '', $filter = '', $entityClass = 'Microsoft_WindowsAzure_Storage_DynamicTableEntity', $nextPartitionKey = null, $nextRowKey = null)
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}
		if ($entityClass === '') {
			throw new Microsoft_WindowsAzure_Exception('Entity class is not specified.');
		}

		// Convenience...
		if (class_exists($filter)) {
		    $entityClass = $filter;
		    $filter = '';
		}

		// Query string
		$queryString = '';

		// Determine query
		if (is_string($tableName)) {
		    // Option 1: $tableName is a string

		    // Append parentheses
		    $tableName .= '()';

    	    // Build query
    	    $query = array();

    		// Filter?
    		if ($filter !== '') {
    		    $query[] = '$filter=' . Microsoft_WindowsAzure_Storage_TableEntityQuery::encodeQuery($filter);
    		}

    	    // Build queryString
    	    if (count($query) > 0)  {
    	        $queryString = '?' . implode('&', $query);
    	    }
		} else if (get_class($tableName) == 'Microsoft_WindowsAzure_Storage_TableEntityQuery') {
		    // Option 2: $tableName is a Microsoft_WindowsAzure_Storage_TableEntityQuery instance

		    // Build queryString
		    $queryString = $tableName->assembleQueryString(true);

		    // Change $tableName
		    $tableName = $tableName->assembleFrom(true);
		} else {
		    throw new Microsoft_WindowsAzure_Exception('Invalid argument: $tableName');
		}

		// Add continuation querystring parameters?
		if (!is_null($nextPartitionKey) && !is_null($nextRowKey)) {
		    if ($queryString !== '') {
		        $queryString .= '&';
		    }

		    $queryString .= '&NextPartitionKey=' . rawurlencode($nextPartitionKey) . '&NextRowKey=' . rawurlencode($nextRowKey);
		}

		// Perform request
	    $response = null;
	    if ($this->isInBatch() && $this->getCurrentBatch()->getOperationCount() == 0) {
		    $this->getCurrentBatch()->enlistOperation($tableName, $queryString, Microsoft_Http_Client::GET, array(), true, null);
		    $response = $this->getCurrentBatch()->commit();

		    // Get inner response (multipart)
		    $innerResponse = $response->getBody();
		    $innerResponse = substr($innerResponse, strpos($innerResponse, 'HTTP/1.1 200 OK'));
		    $innerResponse = substr($innerResponse, 0, strpos($innerResponse, '--batchresponse'));
		    $response = Microsoft_Http_Response::fromString($innerResponse);
		} else {
		    $response = $this->_performRequest($tableName, $queryString, Microsoft_Http_Client::GET, array(), true, null);
		}

		if ($response->isSuccessful()) {
		    // Parse result
		    $result = $this->_parseResponse($response);
		    if (!$result) {
		        return array();
		    }

		    $entries = null;
		    if ($result->entry) {
    		    if (count($result->entry) > 1) {
    		        $entries = $result->entry;
    		    } else {
    		        $entries = array($result->entry);
    		    }
		    } else {
		        // This one is tricky... If we have properties defined, we have an entity.
		        $properties = $result->xpath('//m:properties');
		        if ($properties) {
		            $entries = array($result);
		        } else {
		            return array();
		        }
		    }

		    // Create return value
		    $returnValue = array();
		    foreach ($entries as $entry) {
    		    // Parse properties
    		    $properties = $entry->xpath('.//m:properties');
    		    $properties = $properties[0]->children('http://schemas.microsoft.com/ado/2007/08/dataservices');

    		    // Create entity
    		    $entity = new $entityClass('', '');
    		    $entity->setAzureValues((array)$properties, $this->_throwExceptionOnMissingData);

    		    // If we have a Microsoft_WindowsAzure_Storage_DynamicTableEntity, make sure all property types are OK
    		    if ($entity instanceof Microsoft_WindowsAzure_Storage_DynamicTableEntity) {
    		        foreach ($properties as $key => $value) {
    		            $attributes = $value->attributes('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
    		            $type = (string)$attributes['type'];
    		            if ($type !== '') {
    		                $entity->setAzurePropertyType($key, $type);
    		            }
    		        }
    		    }

    		    // Update etag
    		    $etag      = $entry->attributes('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
    		    $etag      = (string)$etag['etag'];
    		    $entity->setEtag($etag);

    		    // Add to result
    		    $returnValue[] = $entity;
		    }

			// More entities?
		    if (!is_null($response->getHeader('x-ms-continuation-NextPartitionKey')) && !is_null($response->getHeader('x-ms-continuation-NextRowKey'))) {
		        if (strpos($queryString, '$top') === false) {
		            $returnValue = array_merge($returnValue, $this->retrieveEntities($tableName, $filter, $entityClass, $response->getHeader('x-ms-continuation-NextPartitionKey'), $response->getHeader('x-ms-continuation-NextRowKey')));
		        }
		    }

		    // Return
		    return $returnValue;
		} else {
		    throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Update entity by replacing it
	 *
	 * @param string                              $tableName   Table name
	 * @param Microsoft_WindowsAzure_Storage_TableEntity $entity      Entity to update
	 * @param boolean                             $verifyEtag  Verify etag of the entity (used for concurrency)
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function updateEntity($tableName = '', Microsoft_WindowsAzure_Storage_TableEntity $entity = null, $verifyEtag = false)
	{
	    return $this->_changeEntity(Microsoft_Http_Client::PUT, $tableName, $entity, $verifyEtag);
	}

	/**
	 * Update entity by adding or updating properties
	 *
	 * @param string                              $tableName   Table name
	 * @param Microsoft_WindowsAzure_Storage_TableEntity $entity      Entity to update
	 * @param boolean                             $verifyEtag  Verify etag of the entity (used for concurrency)
	 * @param array                               $properties  Properties to merge. All properties will be used when omitted.
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function mergeEntity($tableName = '', Microsoft_WindowsAzure_Storage_TableEntity $entity = null, $verifyEtag = false, $properties = array())
	{
		$mergeEntity = null;
		if (is_array($properties) && count($properties) > 0) {
			// Build a new object
			$mergeEntity = new Microsoft_WindowsAzure_Storage_DynamicTableEntity($entity->getPartitionKey(), $entity->getRowKey());

			// Keep only values mentioned in $properties
			$azureValues = $entity->getAzureValues();
			foreach ($azureValues as $key => $value) {
				if (in_array($value->Name, $properties)) {
					$mergeEntity->setAzureProperty($value->Name, $value->Value, $value->Type);
				}
			}
		} else {
			$mergeEntity = $entity;
		}

		// Ensure entity timestamp matches updated timestamp
        $entity->setTimestamp($this->isoDate());

	    return $this->_changeEntity(Microsoft_Http_Client::MERGE, $tableName, $mergeEntity, $verifyEtag);
	}

	/**
	 * Get error message from Microsoft_Http_Response
	 *
	 * @param Microsoft_Http_Response $response Repsonse
	 * @param string $alternativeError Alternative error message
	 * @return string
	 */
	protected function _getErrorMessage(Microsoft_Http_Response $response, $alternativeError = 'Unknown error.')
	{
		$response = $this->_parseResponse($response);
		if ($response && $response->message) {
		    return (string)$response->message;
		} else {
		    return $alternativeError;
		}
	}

	/**
	 * Update entity / merge entity
	 *
	 * @param string                              $httpVerb    HTTP verb to use (PUT = update, MERGE = merge)
	 * @param string                              $tableName   Table name
	 * @param Microsoft_WindowsAzure_Storage_TableEntity $entity      Entity to update
	 * @param boolean                             $verifyEtag  Verify etag of the entity (used for concurrency)
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	protected function _changeEntity($httpVerb = Microsoft_Http_Client::PUT, $tableName = '', Microsoft_WindowsAzure_Storage_TableEntity $entity = null, $verifyEtag = false)
	{
		if ($tableName === '') {
			throw new Microsoft_WindowsAzure_Exception('Table name is not specified.');
		}
		if (is_null($entity)) {
			throw new Microsoft_WindowsAzure_Exception('Entity is not specified.');
		}

        // Add header information
        $headers = array();
        $headers['Content-Type']   = 'application/atom+xml';
        $headers['Content-Length'] = 0;
        if (!$verifyEtag) {
            $headers['If-Match']       = '*';
        } else {
            $headers['If-Match']       = $entity->getEtag();
        }

	    // Generate request body
		$requestBody = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                        <entry xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices" xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" xmlns="http://www.w3.org/2005/Atom">
                          <title />
                          <updated>{tpl:Updated}</updated>
                          <author>
                            <name />
                          </author>
                          <id />
                          <content type="application/xml">
                            <m:properties>
                              {tpl:Properties}
                            </m:properties>
                          </content>
                        </entry>';

		// Attempt to get timestamp from entity
        $timestamp = $entity->getTimestamp();
        if ($timestamp == Microsoft_WindowsAzure_Storage_TableEntity::DEFAULT_TIMESTAMP) {
            $timestamp = $this->isoDate();
        }

        $requestBody = $this->_fillTemplate($requestBody, array(
        	'Updated'    => $timestamp,
            'Properties' => $this->_generateAzureRepresentation($entity)
        ));

        // Add header information
        $headers = array();
        $headers['Content-Type'] = 'application/atom+xml';
	    if (!$verifyEtag) {
            $headers['If-Match']       = '*';
        } else {
            $headers['If-Match']       = $entity->getEtag();
        }

		// Perform request
		$response = null;
	    if ($this->isInBatch()) {
		    $this->getCurrentBatch()->enlistOperation($tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() . '\')', '', $httpVerb, $headers, true, $requestBody);
		    return null;
		} else {
		    $response = $this->_performRequest($tableName . '(PartitionKey=\'' . $entity->getPartitionKey() . '\', RowKey=\'' . $entity->getRowKey() . '\')', '', $httpVerb, $headers, true, $requestBody);
		}
		if ($response->isSuccessful()) {
		    // Update properties
			$entity->setEtag($response->getHeader('Etag'));
			$entity->setTimestamp($response->getHeader('Last-modified'));

		    return $entity;
		} else {
			throw new Microsoft_WindowsAzure_Exception($this->_getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}

	/**
	 * Generate RFC 1123 compliant date string
	 *
	 * @return string
	 */
	protected function _rfcDate()
	{
	    return gmdate('D, d M Y H:i:s', time()) . ' GMT'; // RFC 1123
	}

	/**
	 * Fill text template with variables from key/value array
	 *
	 * @param string $templateText Template text
	 * @param array $variables Array containing key/value pairs
	 * @return string
	 */
	protected function _fillTemplate($templateText, $variables = array())
	{
	    foreach ($variables as $key => $value) {
	        $templateText = str_replace('{tpl:' . $key . '}', $value, $templateText);
	    }
	    return $templateText;
	}

	/**
	 * Generate Azure representation from entity (creates atompub markup from properties)
	 *
	 * @param Microsoft_WindowsAzure_Storage_TableEntity $entity
	 * @return string
	 */
	protected function _generateAzureRepresentation(Microsoft_WindowsAzure_Storage_TableEntity $entity = null)
	{
		// Generate Azure representation from entity
		$azureRepresentation = array();
		$azureValues         = $entity->getAzureValues();
		foreach ($azureValues as $azureValue) {
		    $value = array();
		    $value[] = '<d:' . $azureValue->Name;
		    if ($azureValue->Type != '') {
		        $value[] = ' m:type="' . $azureValue->Type . '"';
		    }
		    if (is_null($azureValue->Value)) {
		        $value[] = ' m:null="true"';
		    }
		    $value[] = '>';

		    if (!is_null($azureValue->Value)) {
		        if (strtolower($azureValue->Type) == 'edm.boolean') {
		            $value[] = ($azureValue->Value == true ? '1' : '0');
		        } else {
		            $value[] = htmlspecialchars($azureValue->Value);
		        }
		    }

		    $value[] = '</d:' . $azureValue->Name . '>';
		    $azureRepresentation[] = implode('', $value);
		}

		return implode('', $azureRepresentation);
	}

		/**
	 * Perform request using Microsoft_Http_Client channel
	 *
	 * @param string $path Path
	 * @param string $queryString Query string
	 * @param string $httpVerb HTTP verb the request will use
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param mixed $rawData Optional RAW HTTP data to be sent over the wire
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return Microsoft_Http_Response
	 */
	protected function _performRequest(
		$path = '/',
		$queryString = '',
		$httpVerb = Microsoft_Http_Client::GET,
		$headers = array(),
		$forTableStorage = false,
		$rawData = null,
		$resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN,
		$requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ
	) {
		// Add headers
		$headers['DataServiceVersion'] = '1.0;NetFx';
		$headers['MaxDataServiceVersion'] = '1.0;NetFx';

		// Perform request
		return parent::_performRequest(
			$path,
			$queryString,
			$httpVerb,
			$headers,
			$forTableStorage,
			$rawData,
			$resourceType,
			$requiredPermission
		);
	}
}
