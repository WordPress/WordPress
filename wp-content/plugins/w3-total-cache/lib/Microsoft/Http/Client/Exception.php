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
 * @subpackage Client_Exception
 * @version    $Id: Exception.php 16872 2009-07-20 11:47:08Z mikaelkael $
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_Http_Exception
 */
require_once 'Microsoft/Http/Exception.php';

/**
 * @category   Microsoft
 * @package    Microsoft_Http
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Microsoft_Http_Client_Exception extends Microsoft_Http_Exception
{}
