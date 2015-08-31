<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Microsoft
 * @package    Microsoft_Http
 * @subpackage Client_Adapter
 * @version    $Id: Interface.php 16214 2009-06-21 19:34:03Z thomas $
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
if (!defined('W3TC')) {
    die();
}

/**
 * An interface description for Microsoft_Http_Client_Adapter_Stream classes.
 *
 * This interface decribes Microsoft_Http_Client_Adapter which supports streaming.
 *
 * @category   Microsoft
 * @package    Microsoft_Http
 * @subpackage Client_Adapter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Microsoft_Http_Client_Adapter_Stream
{
    /**
     * Set output stream
     *
     * This function sets output stream where the result will be stored.
     *
     * @param resource $stream Stream to write the output to
     *
     */
    function setOutputStream($stream);
}
