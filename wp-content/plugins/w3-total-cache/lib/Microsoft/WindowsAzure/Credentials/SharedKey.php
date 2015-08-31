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
 * @version    $Id$
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Credentials_CredentialsAbstract
 */
require_once 'Microsoft/WindowsAzure/Credentials/CredentialsAbstract.php';

/**
 * @see Microsoft_WindowsAzure_Storage
 */
require_once 'Microsoft/WindowsAzure/Storage.php';

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
class Microsoft_WindowsAzure_Credentials_SharedKey
    extends Microsoft_WindowsAzure_Credentials_CredentialsAbstract
{
    /**
	 * Sign request URL with credentials
	 *
	 * @param string $requestUrl Request URL
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return string Signed request URL
	 */
	public function signRequestUrl(
		$requestUrl = '',
		$resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN,
		$requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ
	) {
	    return $requestUrl;
	}

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
	public function signRequestHeaders(
		$httpVerb = Microsoft_Http_Client::GET,
		$path = '/',
		$queryString = '',
		$headers = null,
		$forTableStorage = false,
		$resourceType = Microsoft_WindowsAzure_Storage::RESOURCE_UNKNOWN,
		$requiredPermission = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ,
		$rawData = null
	) {
		// http://github.com/sriramk/winazurestorage/blob/214010a2f8931bac9c96dfeb337d56fe084ca63b/winazurestorage.py

		// Table storage?
		if ($forTableStorage) {
			throw new Microsoft_WindowsAzure_Credentials_Exception('The Windows Azure SDK for PHP does not support SharedKey authentication on table storage. Use SharedKeyLite authentication instead.');
		}

		// Determine path
		if ($this->_usePathStyleUri) {
			$path = substr($path, strpos($path, '/'));
		}

		// Determine query
		$queryString = $this->_prepareQueryStringForSigning($queryString);

		// Canonicalized headers
		$canonicalizedHeaders = array();

		// Request date
		$requestDate = '';
		if (isset($headers[Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date'])) {
		    $requestDate = $headers[Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date'];
		} else {
		    $requestDate = gmdate('D, d M Y H:i:s', time()) . ' GMT'; // RFC 1123
		    $canonicalizedHeaders[] = Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date:' . $requestDate;
		}

		// Build canonicalized headers
		if (!is_null($headers)) {
			foreach ($headers as $header => $value) {
				if (is_bool($value)) {
					$value = $value === true ? 'True' : 'False';
				}

				$headers[$header] = $value;
				if (substr($header, 0, strlen(Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER)) == Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER) {
				    $canonicalizedHeaders[] = strtolower($header) . ':' . $value;
				}
			}
		}
		sort($canonicalizedHeaders);

		// Build canonicalized resource string
		$canonicalizedResource  = '/' . $this->_accountName;
		if ($this->_usePathStyleUri) {
			$canonicalizedResource .= '/' . $this->_accountName;
		}
		$canonicalizedResource .= $path;
		if ($queryString !== '') {
		    $queryStringItems = $this->_makeArrayOfQueryString($queryString);
		    foreach ($queryStringItems as $key => $value) {
		    	$canonicalizedResource .= "\n" . strtolower($key) . ':' . $value;
		    }
		}

		// Content-Length header
		$contentLength = '';
		if (strtoupper($httpVerb) != Microsoft_Http_Client::GET
			 && strtoupper($httpVerb) != Microsoft_Http_Client::DELETE
			 && strtoupper($httpVerb) != Microsoft_Http_Client::HEAD) {
			$contentLength = 0;

			if (!is_null($rawData)) {
				$contentLength = strlen($rawData);
			}
		}

		// Create string to sign
		$stringToSign   = array();
		$stringToSign[] = strtoupper($httpVerb); 									// VERB
    	$stringToSign[] = $this->_issetOr($headers, 'Content-Encoding', '');		// Content-Encoding
    	$stringToSign[] = $this->_issetOr($headers, 'Content-Language', '');		// Content-Language
    	$stringToSign[] = $contentLength; 											// Content-Length
    	$stringToSign[] = $this->_issetOr($headers, 'Content-MD5', '');				// Content-MD5
    	$stringToSign[] = $this->_issetOr($headers, 'Content-Type', '');			// Content-Type
    	$stringToSign[] = "";														// Date
    	$stringToSign[] = $this->_issetOr($headers, 'If-Modified-Since', '');		// If-Modified-Since
    	$stringToSign[] = $this->_issetOr($headers, 'If-Match', '');				// If-Match
    	$stringToSign[] = $this->_issetOr($headers, 'If-None-Match', '');			// If-None-Match
    	$stringToSign[] = $this->_issetOr($headers, 'If-Unmodified-Since', '');		// If-Unmodified-Since
    	$stringToSign[] = $this->_issetOr($headers, 'Range', '');					// Range

    	if (!$forTableStorage && count($canonicalizedHeaders) > 0) {
    		$stringToSign[] = implode("\n", $canonicalizedHeaders); // Canonicalized headers
    	}

    	$stringToSign[] = $canonicalizedResource;		 			// Canonicalized resource
    	$stringToSign   = implode("\n", $stringToSign);
    	$signString     = base64_encode(hash_hmac('sha256', $stringToSign, $this->_accountKey, true));

    	// Sign request
    	$headers[Microsoft_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date'] = $requestDate;
    	$headers['Authorization'] = 'SharedKey ' . $this->_accountName . ':' . $signString;

    	// Return headers
    	return $headers;
	}
}
