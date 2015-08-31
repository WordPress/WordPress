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
 * @see Microsoft_WindowsAzure_Storage
 */
require_once 'Microsoft/WindowsAzure/Storage.php';

/**
 * @see Microsoft_WindowsAzure_Credentials_CredentialsAbstract
 */
require_once 'Microsoft/WindowsAzure/Credentials/CredentialsAbstract.php';

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';

/**
 * @see Microsoft_WindowsAzure_Storage_Batch
 */
require_once 'Microsoft/WindowsAzure/Storage/Batch.php';

/**
 * @see Microsoft_Http_Client
 */
require_once 'Microsoft/Http/Client.php';

/**
 * @see Microsoft_Http_Response
 */
require_once 'Microsoft/Http/Response.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
abstract class Microsoft_WindowsAzure_Storage_BatchStorageAbstract
    extends Microsoft_WindowsAzure_Storage
{
    /**
     * Current batch
     *
     * @var Microsoft_WindowsAzure_Storage_Batch
     */
    protected $_currentBatch = null;

    /**
     * Set current batch
     *
     * @param Microsoft_WindowsAzure_Storage_Batch $batch Current batch
     * @throws Microsoft_WindowsAzure_Exception
     */
    public function setCurrentBatch(Microsoft_WindowsAzure_Storage_Batch $batch = null)
    {
        if (!is_null($batch) && $this->isInBatch()) {
            throw new Microsoft_WindowsAzure_Exception('Only one batch can be active at a time.');
        }
        $this->_currentBatch = $batch;
    }

    /**
     * Get current batch
     *
     * @return Microsoft_WindowsAzure_Storage_Batch
     */
    public function getCurrentBatch()
    {
        return $this->_currentBatch;
    }

    /**
     * Is there a current batch?
     *
     * @return boolean
     */
    public function isInBatch()
    {
        return !is_null($this->_currentBatch);
    }

    /**
     * Starts a new batch operation set
     *
     * @return Microsoft_WindowsAzure_Storage_Batch
     * @throws Microsoft_WindowsAzure_Exception
     */
    public function startBatch()
    {
        return new Microsoft_WindowsAzure_Storage_Batch($this, $this->getBaseUrl());
    }

	/**
	 * Perform batch using Microsoft_Http_Client channel, combining all batch operations into one request
	 *
	 * @param array $operations Operations in batch
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param boolean $isSingleSelect Is the request a single select statement?
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return Microsoft_Http_Response
	 */
	public function performBatch($operations = array(), $forTableStorage = false, $isSingleSelect = false, $resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN, $requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ)
	{
	    // Generate boundaries
	    $batchBoundary = 'batch_' . md5(time() . microtime());
	    $changesetBoundary = 'changeset_' . md5(time() . microtime());

	    // Set headers
	    $headers = array();

		// Add version header
		$headers['x-ms-version'] = $this->_apiVersion;

		// Add dataservice headers
		$headers['DataServiceVersion'] = '1.0;NetFx';
		$headers['MaxDataServiceVersion'] = '1.0;NetFx';

		// Add content-type header
		$headers['Content-Type'] = 'multipart/mixed; boundary=' . $batchBoundary;

		// Set path and query string
		$path           = '/$batch';
		$queryString    = '';

		// Set verb
		$httpVerb = Microsoft_Http_Client::POST;

		// Generate raw data
    	$rawData = '';

		// Single select?
		if ($isSingleSelect) {
		    $operation = $operations[0];
		    $rawData .= '--' . $batchBoundary . "\n";
            $rawData .= 'Content-Type: application/http' . "\n";
            $rawData .= 'Content-Transfer-Encoding: binary' . "\n\n";
            $rawData .= $operation;
            $rawData .= '--' . $batchBoundary . '--';
		} else {
    		$rawData .= '--' . $batchBoundary . "\n";
    		$rawData .= 'Content-Type: multipart/mixed; boundary=' . $changesetBoundary . "\n\n";

        		// Add operations
        		foreach ($operations as $operation)
        		{
                    $rawData .= '--' . $changesetBoundary . "\n";
                	$rawData .= 'Content-Type: application/http' . "\n";
                	$rawData .= 'Content-Transfer-Encoding: binary' . "\n\n";
                	$rawData .= $operation;
        		}
        		$rawData .= '--' . $changesetBoundary . '--' . "\n";

    		$rawData .= '--' . $batchBoundary . '--';
		}

		// Generate URL and sign request
		$requestUrl     = $this->_credentials->signRequestUrl($this->getBaseUrl() . $path . $queryString, $resourceType, $requiredPermission);
		$requestHeaders = $this->_credentials->signRequestHeaders($httpVerb, $path, $queryString, $headers, $forTableStorage, $resourceType, $requiredPermission);

		// Prepare request
		$this->_httpClientChannel->resetParameters(true);
		$this->_httpClientChannel->setUri($requestUrl);
		$this->_httpClientChannel->setHeaders($requestHeaders);
		$this->_httpClientChannel->setRawData($rawData);

		// Execute request
		$response = $this->_retryPolicy->execute(
		    array($this->_httpClientChannel, 'request'),
		    array($httpVerb)
		);

		return $response;
	}
}
