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
 * @subpackage RetryPolicy
 * @version    $Id: RetryPolicy.php 28585 2009-09-07 12:12:56Z unknown $
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
if (!defined('W3TC')) {
    die();
}

/**
 * @see Microsoft_WindowsAzure_Exception
 */
require_once 'Microsoft/WindowsAzure/Exception.php';

/**
 * @see Microsoft_WindowsAzure_RetryPolicy_NoRetry
 */
require_once 'Microsoft/WindowsAzure/RetryPolicy/NoRetry.php';

/**
 * @see Microsoft_WindowsAzure_RetryPolicy_RetryN
 */
require_once 'Microsoft/WindowsAzure/RetryPolicy/RetryN.php';

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage RetryPolicy
 * @copyright  Copyright (c) 2009 - 2010, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
abstract class Microsoft_WindowsAzure_RetryPolicy_RetryPolicyAbstract
{
    /**
     * Execute function under retry policy
     *
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @return mixed
     */
    public abstract function execute($function, $parameters = array());

    /**
     * Create a Microsoft_WindowsAzure_RetryPolicy_NoRetry instance
     *
     * @return Microsoft_WindowsAzure_RetryPolicy_NoRetry
     */
    public static function noRetry()
    {
        return new Microsoft_WindowsAzure_RetryPolicy_NoRetry();
    }

    /**
     * Create a Microsoft_WindowsAzure_RetryPolicy_RetryN instance
     *
     * @param int $count                    Number of retries
     * @param int $intervalBetweenRetries   Interval between retries (in milliseconds)
     * @return Microsoft_WindowsAzure_RetryPolicy_RetryN
     */
    public static function retryN($count = 1, $intervalBetweenRetries = 0)
    {
        return new Microsoft_WindowsAzure_RetryPolicy_RetryN($count, $intervalBetweenRetries);
    }
}