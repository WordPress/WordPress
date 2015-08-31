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
 * @version    $Id: BlobInstance.php 53615 2010-11-16 20:45:11Z unknown $
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';

/**
 * @see Microsoft_WindowsAzure_Storage_StorageEntityAbstract
 */
require_once 'Microsoft/WindowsAzure/Storage/StorageEntityAbstract.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 *
 * @property string  $Container       The name of the blob container in which the blob is stored.
 * @property string  $Name            The name of the blob.
 * @property string  $SnapshotId      The blob snapshot ID if it is a snapshot blob (= a backup copy of a blob).
 * @property string  $Etag            The entity tag, used for versioning and concurrency.
 * @property string  $LastModified    Timestamp when the blob was last modified.
 * @property string  $Url             The full URL where the blob can be downloaded.
 * @property int     $Size            The blob size in bytes.
 * @property string  $ContentType     The blob content type header.
 * @property string  $ContentMd5      The blob content MD5 header.
 * @property string  $ContentEncoding The blob content encoding header.
 * @property string  $ContentLanguage The blob content language header.
 * @property string  $CacheControl    The blob cache control header.
 * @property string  $BlobType        The blob type (block blob / page blob).
 * @property string  $LeaseStatus     The blob lease status.
 * @property boolean $IsPrefix        Is it a blob or a directory prefix?
 * @property array   $Metadata        Key/value pairs of meta data
 */
class Microsoft_WindowsAzure_Storage_BlobInstance
{
    /**
     * Data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Constructor
     *
     * @param string  $containerName   Container name
     * @param string  $name            Name
     * @param string  $snapshotId      Snapshot id
     * @param string  $etag            Etag
     * @param string  $lastModified    Last modified date
     * @param string  $url             Url
     * @param int     $size            Size
     * @param string  $contentType     Content Type
     * @param string  $contentMd5      Content MD5
     * @param string  $contentEncoding Content Encoding
     * @param string  $contentLanguage Content Language
     * @param string  $cacheControl    Cache control
     * @param string  $blobType        Blob type
     * @param string  $leaseStatus     Lease status
     * @param boolean $isPrefix        Is Prefix?
     * @param array   $metadata        Key/value pairs of meta data
     */
    public function __construct($containerName, $name, $snapshotId, $etag, $lastModified, $url = '', $size = 0, $contentType = '', $contentMd5 = '', $contentEncoding = '', $contentLanguage = '', $cacheControl = '', $blobType = '', $leaseStatus = '', $isPrefix = false, $metadata = array())
    {
        $this->_data = array(
            'container'        => $containerName,
            'name'             => $name,
        	'snapshotid'	   => $snapshotId,
            'etag'             => $etag,
            'lastmodified'     => $lastModified,
            'url'              => $url,
            'size'             => $size,
            'contenttype'      => $contentType,
            'contentmd5'       => $contentMd5,
            'contentencoding'  => $contentEncoding,
            'contentlanguage'  => $contentLanguage,
            'cachecontrol'     => $cacheControl,
            'blobtype'         => $blobType,
            'leasestatus'      => $leaseStatus,
            'isprefix'         => $isPrefix,
            'metadata'         => $metadata
        );
    }

    /**
     * Magic overload for setting properties
     *
     * @param string $name     Name of the property
     * @param string $value    Value to set
     */
    public function __set($name, $value) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new Exception("Unknown property: " . $name);
    }

    /**
     * Magic overload for getting properties
     *
     * @param string $name     Name of the property
     */
    public function __get($name) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new Exception("Unknown property: " . $name);
    }
}
