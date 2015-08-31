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
 * @version    $Id: Storage.php 21617 2009-06-12 10:46:31Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';

/**
 * @see Microsoft_WindowsAzure_Storage_BatchStorageAbstract
 */
require_once 'Microsoft/WindowsAzure/Storage/BatchStorageAbstract.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_Storage_Batch
{
    /**
     * Storage client the batch is defined on
     *
     * @var Microsoft_WindowsAzure_Storage_BatchStorageAbstract
     */
    protected $_storageClient = null;

    /**
     * For table storage?
     *
     * @var boolean
     */
    protected $_forTableStorage = false;

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Pending operations
     *
     * @var unknown_type
     */
    protected $_operations = array();

    /**
     * Does the batch contain a single select?
     *
     * @var boolean
     */
    protected $_isSingleSelect = false;

    /**
     * Creates a new Microsoft_WindowsAzure_Storage_Batch
     *
     * @param Microsoft_WindowsAzure_Storage_BatchStorageAbstract $storageClient Storage client the batch is defined on
     */
    public function __construct(Microsoft_WindowsAzure_Storage_BatchStorageAbstract $storageClient = null, $baseUrl = '')
    {
        $this->_storageClient = $storageClient;
        $this->_baseUrl       = $baseUrl;
        $this->_beginBatch();
    }

	/**
	 * Get base URL for creating requests
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->_baseUrl;
	}

    /**
     * Starts a new batch operation set
     *
     * @throws Microsoft_WindowsAzure_Exception
     */
    protected function _beginBatch()
    {
        $this->_storageClient->setCurrentBatch($this);
    }

    /**
     * Cleanup current batch
     */
    protected function _clean()
    {
        unset($this->_operations);
        $this->_storageClient->setCurrentBatch(null);
        $this->_storageClient = null;
        unset($this);
    }

	/**
	 * Enlist operation in current batch
	 *
	 * @param string $path Path
	 * @param string $queryString Query string
	 * @param string $httpVerb HTTP verb the request will use
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param mixed $rawData Optional RAW HTTP data to be sent over the wire
	 * @throws Microsoft_WindowsAzure_Exception
	 */
	public function enlistOperation($path = '/', $queryString = '', $httpVerb = Microsoft_Http_Client::GET, $headers = array(), $forTableStorage = false, $rawData = null)
	{
	    // Set _forTableStorage
	    if ($forTableStorage) {
	        $this->_forTableStorage = true;
	    }

	    // Set _isSingleSelect
	    if ($httpVerb == Microsoft_Http_Client::GET) {
	        if (count($this->_operations) > 0) {
	            throw new Microsoft_WindowsAzure_Exception("Select operations can only be performed in an empty batch transaction.");
	        }
	        $this->_isSingleSelect = true;
	    }

	    // Clean path
		if (strpos($path, '/') !== 0) {
			$path = '/' . $path;
		}

		// Clean headers
		if (is_null($headers)) {
		    $headers = array();
		}

		// URL encoding
		$path           = Microsoft_WindowsAzure_Storage::urlencode($path);
		$queryString    = Microsoft_WindowsAzure_Storage::urlencode($queryString);

		// Generate URL
		$requestUrl     = $this->getBaseUrl() . $path . $queryString;

		// Generate $rawData
		if (is_null($rawData)) {
		    $rawData = '';
		}

		// Add headers
		if ($httpVerb != Microsoft_Http_Client::GET) {
    		$headers['Content-ID'] = count($this->_operations) + 1;
    		if ($httpVerb != Microsoft_Http_Client::DELETE) {
    		    $headers['Content-Type'] = 'application/atom+xml;type=entry';
    		}
    		$headers['Content-Length'] = strlen($rawData);
		}

		// Generate $operation
		$operation = '';
		$operation .= $httpVerb . ' ' . $requestUrl . ' HTTP/1.1' . "\n";
		foreach ($headers as $key => $value)
		{
		    $operation .= $key . ': ' . $value . "\n";
		}
		$operation .= "\n";

		// Add data
		$operation .= $rawData;

		// Store operation
		$this->_operations[] = $operation;
	}

    /**
     * Commit current batch
     *
     * @return Microsoft_Http_Response
     * @throws Microsoft_WindowsAzure_Exception
     */
    public function commit()
    {
        // Perform batch
        $response = $this->_storageClient->performBatch($this->_operations, $this->_forTableStorage, $this->_isSingleSelect);

        // Dispose
        $this->_clean();

        // Parse response
        $errors = null;
        preg_match_all('/<message (.*)>(.*)<\/message>/', $response->getBody(), $errors);

        // Error?
        if (count($errors[2]) > 0) {
            throw new Microsoft_WindowsAzure_Exception('An error has occured while committing a batch: ' . $errors[2][0]);
        }

        // Return
        return $response;
    }

    /**
     * Rollback current batch
     */
    public function rollback()
    {
        // Dispose
        $this->_clean();
    }

    /**
     * Get operation count
     *
     * @return integer
     */
    public function getOperationCount()
    {
        return count($this->_operations);
    }

    /**
     * Is single select?
     *
     * @return boolean
     */
    public function isSingleSelect()
    {
        return $this->_isSingleSelect;
    }
}
