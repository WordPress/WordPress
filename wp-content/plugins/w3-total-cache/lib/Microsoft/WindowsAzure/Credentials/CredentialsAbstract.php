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
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: SharedKeyCredentials.php 14561 2009-05-07 08:05:12Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_Http_Client
 */
require_once 'Microsoft/Http/Client.php';

/**
 * @see Microsoft_WindowsAzure_Credentials_Exception
 */
require_once 'Microsoft/WindowsAzure/Credentials/Exception.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
abstract class Microsoft_WindowsAzure_Credentials_CredentialsAbstract
{
	/**
	 * Development storage account and key
	 */
	const DEVSTORE_ACCOUNT       = "devstoreaccount1";
	const DEVSTORE_KEY           = "Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==";

	/**
	 * HTTP header prefixes
	 */
	const PREFIX_PROPERTIES      = "x-ms-prop-";
	const PREFIX_METADATA        = "x-ms-meta-";
	const PREFIX_STORAGE_HEADER  = "x-ms-";

	/**
	 * Permissions
	 */
	const PERMISSION_READ        = "r";
	const PERMISSION_WRITE       = "w";
	const PERMISSION_DELETE      = "d";
	const PERMISSION_LIST        = "l";

	/**
	 * Account name for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountName = '';

	/**
	 * Account key for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountKey = '';

	/**
	 * Use path-style URI's
	 *
	 * @var boolean
	 */
	protected $_usePathStyleUri = false;

	/**
	 * Creates a new Microsoft_WindowsAzure_Credentials_CredentialsAbstract instance
	 *
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 */
	public function __construct(
		$accountName = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT,
		$accountKey  = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY,
		$usePathStyleUri = false
	) {
		$this->_accountName = $accountName;
		$this->_accountKey = base64_decode($accountKey);
		$this->_usePathStyleUri = $usePathStyleUri;
	}

	/**
	 * Set account name for Windows Azure
	 *
	 * @param  string $value
	 * @return Microsoft_WindowsAzure_Credentials_CredentialsAbstract
	 */
	public function setAccountName($value = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_ACCOUNT)
	{
		$this->_accountName = $value;
		return $this;
	}

	/**
	 * Set account key for Windows Azure
	 *
	 * @param  string $value
	 * @return Microsoft_WindowsAzure_Credentials_CredentialsAbstract
	 */
	public function setAccountkey($value = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::DEVSTORE_KEY)
	{
		$this->_accountKey = base64_decode($value);
		return $this;
	}

	/**
	 * Set use path-style URI's
	 *
	 * @param  boolean $value
	 * @return Microsoft_WindowsAzure_Credentials_CredentialsAbstract
	 */
	public function setUsePathStyleUri($value = false)
	{
		$this->_usePathStyleUri = $value;
		return $this;
	}

	/**
	 * Sign request URL with credentials
	 *
	 * @param string $requestUrl Request URL
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return string Signed request URL
	 */
	abstract public function signRequestUrl(
		$requestUrl = '',
		$resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN,
		$requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ
	);

	/**
	 * Sign request headers with credentials
	 *
	 * @param string $httpVerb HTTP verb the request will use
	 * @param string $path Path for the request
	 * @param string $queryString Query string for the request
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @param mixed  $rawData Raw post data
	 * @return array Array of headers
	 */
	abstract public function signRequestHeaders(
		$httpVerb = Microsoft_Http_Client::GET,
		$path = '/',
		$queryString = '',
		$headers = null,
		$forTableStorage = false,
		$resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN,
		$requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ,
		$rawData = null
	);


	/**
	 * Prepare query string for signing
	 *
	 * @param  string $value Original query string
	 * @return string        Query string for signing
	 */
	protected function _prepareQueryStringForSigning($value)
	{
	    // Return value
	    $returnValue = array();

	    // Prepare query string
	    $queryParts = $this->_makeArrayOfQueryString($value);
	    foreach ($queryParts as $key => $value) {
	    	$returnValue[] = $key . '=' . $value;
	    }

	    // Return
	    if (count($returnValue) > 0) {
	    	return '?' . implode('&', $returnValue);
	    } else {
	    	return '';
	    }
	}

	/**
	 * Make array of query string
	 *
	 * @param  string $value Query string
	 * @return array         Array of key/value pairs
	 */
	protected function _makeArrayOfQueryString($value)
	{
		// Returnvalue
		$returnValue = array();

	    // Remove front ?
   		if (strlen($value) > 0 && strpos($value, '?') === 0) {
    		$value = substr($value, 1);
    	}

    	// Split parts
    	$queryParts = explode('&', $value);
    	foreach ($queryParts as $queryPart) {
    		$queryPart = explode('=', $queryPart, 2);

    		if ($queryPart[0] != '') {
    			$returnValue[ $queryPart[0] ] = isset($queryPart[1]) ? $queryPart[1] : '';
    		}
    	}

    	// Sort
    	ksort($returnValue);

    	// Return
		return $returnValue;
	}

	/**
	 * Returns an array value if the key is set, otherwide returns $valueIfNotSet
	 *
	 * @param array $array
	 * @param mixed $key
	 * @param mixed $valueIfNotSet
	 * @return mixed
	 */
	protected function _issetOr($array, $key, $valueIfNotSet)
	{
		return isset($array[$key]) ? $array[$key] : $valueIfNotSet;
	}
}
