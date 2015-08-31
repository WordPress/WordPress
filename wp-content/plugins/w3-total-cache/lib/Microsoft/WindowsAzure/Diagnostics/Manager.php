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
 * @subpackage Diagnostics
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * @version    $Id: Storage.php 45989 2010-05-03 12:19:10Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Storage_Blob
 */
require_once 'Microsoft/WindowsAzure/Storage/Blob.php';

/**
 * @see Microsoft_WindowsAzure_Diagnostics_Exception
 */
require_once 'Microsoft/WindowsAzure/Diagnostics/Exception.php';

/**
 * @see Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance
 */
require_once 'Microsoft/WindowsAzure/Diagnostics/ConfigurationInstance.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Diagnostics
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_WindowsAzure_Diagnostics_Manager
{
	/**
	 * Blob storage client
	 *
	 * @var Microsoft_WindowsAzure_Storage_Blob
	 */
	protected $_blobStorageClient = null;

	/**
	 * Control container name
	 *
	 * @var string
	 */
	protected $_controlContainer = '';

	/**
	 * Create a new instance of Microsoft_WindowsAzure_Diagnostics_Manager
	 *
	 * @param Microsoft_WindowsAzure_Storage_Blob $blobStorageClient Blob storage client
	 * @param string $controlContainer Control container name
	 */
	public function __construct(Microsoft_WindowsAzure_Storage_Blob $blobStorageClient = null, $controlContainer = 'wad-control-container')
	{
		$this->_blobStorageClient = $blobStorageClient;
		$this->_controlContainer = $controlContainer;

		$this->_ensureStorageInitialized();
	}

	/**
	 * Ensure storage has been initialized
	 */
	protected function _ensureStorageInitialized()
	{
		if (!$this->_blobStorageClient->containerExists($this->_controlContainer)) {
			$this->_blobStorageClient->createContainer($this->_controlContainer);
		}
	}

	/**
	 * Get default configuration values
	 *
	 * @return Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance
	 */
	public function getDefaultConfiguration()
	{
		return new Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance();
	}

	/**
	 * Checks if a configuration for a specific role instance exists.
	 *
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @return boolean
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	public function configurationForRoleInstanceExists($roleInstance = null)
	{
		if (is_null($roleInstance)) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		return $this->_blobStorageClient->blobExists($this->_controlContainer, $roleInstance);
	}

	/**
	 * Checks if a configuration for current role instance exists. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @return boolean
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	public function configurationForCurrentRoleInstanceExists()
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}

		return $this->_blobStorageClient->blobExists($this->_controlContainer, $this->_getCurrentRoleInstanceId());
	}

	/**
	 * Get configuration for current role instance. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @return Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	public function getConfigurationForCurrentRoleInstance()
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		return $this->getConfigurationForRoleInstance($this->_getCurrentRoleInstanceId());
	}

	/**
	 * Get the current role instance ID. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @return string
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	protected function _getCurrentRoleInstanceId()
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}

		if (strpos($_SERVER['RdRoleId'], 'deployment(') === false) {
			return $_SERVER['RdRoleId'];
		} else {
			$roleIdParts = explode('.', $_SERVER['RdRoleId']);
			return $roleIdParts[0] . '/' . $roleIdParts[2] . '/' . $_SERVER['RdRoleId'];
		}
	}

	/**
	 * Set configuration for current role instance. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @param Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance $configuration Configuration to apply
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	public function setConfigurationForCurrentRoleInstance(Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance $configuration)
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}

		$this->setConfigurationForRoleInstance($this->_getCurrentRoleInstanceId(), $configuration);
	}

	/**
	 * Get configuration for a specific role instance
	 *
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @return Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	public function getConfigurationForRoleInstance($roleInstance = null)
	{
		if (is_null($roleInstance)) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		if ($this->_blobStorageClient->blobExists($this->_controlContainer, $roleInstance)) {
			$configurationInstance = new Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance();
			$configurationInstance->loadXml( $this->_blobStorageClient->getBlobData($this->_controlContainer, $roleInstance) );
			return $configurationInstance;
		}

		return new Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance();
	}

	/**
	 * Set configuration for a specific role instance
	 *
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @param Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance $configuration Configuration to apply
	 * @throws Microsoft_WindowsAzure_Diagnostics_Exception
	 */
	public function setConfigurationForRoleInstance($roleInstance = null, Microsoft_WindowsAzure_Diagnostics_ConfigurationInstance $configuration)
	{
		if (is_null($roleInstance)) {
			throw new Microsoft_WindowsAzure_Diagnostics_Exception('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		$this->_blobStorageClient->putBlobData($this->_controlContainer, $roleInstance, $configuration->toXml(), array(), null, array('Content-Type' => 'text/xml'));
	}
}