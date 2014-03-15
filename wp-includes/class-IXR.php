<?php
/**
 * IXR - The Incutio XML-RPC Library
 *
 * Copyright (c) 2010, Incutio Ltd.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  - Neither the name of Incutio Ltd. nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package IXR
 * @since 1.5.0
 *
 * @copyright  Incutio Ltd 2010 (http://www.incutio.com)
 * @version    1.7.4 7th September 2010
 * @author     Simon Willison
 * @link       http://scripts.incutio.com/xmlrpc/ Site/manual
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD
 */

/**
 * IXR_Value
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Value {
    var $data;
    var $type;

    function IXR_Value($data, $type = false)
    {
        $this->data = $data;
        if (!$type) {
            $type = $this->calculateType();
        }
        $this->type = $type;
        if ($type == 'struct') {
            // Turn all the values in the array in to new IXR_Value objects
            foreach ($this->data as $key => $value) {
                $this->data[$key] = new IXR_Value($value);
            }
        }
        if ($type == 'array') {
            for ($i = 0, $j = count($this->data); $i < $j; $i++) {
                $this->data[$i] = new IXR_Value($this->data[$i]);
            }
        }
    }

    function calculateType()
    {
        if ($this->data === true || $this->data === false) {
            return 'boolean';
        }
        if (is_integer($this->data)) {
            return 'int';
        }
        if (is_double($this->data)) {
            return 'double';
        }

        // Deal with IXR object types base64 and date
        if (is_object($this->data) && is_a($this->data, 'IXR_Date')) {
            return 'date';
        }
        if (is_object($this->data) && is_a($this->data, 'IXR_Base64')) {
            return 'base64';
        }

        // If it is a normal PHP object convert it in to a struct
        if (is_object($this->data)) {
            $this->data = get_object_vars($this->data);
            return 'struct';
        }
        if (!is_array($this->data)) {
            return 'string';
        }

        // We have an array - is it an array or a struct?
        if ($this->isStruct($this->data)) {
            return 'struct';
        } else {
            return 'array';
        }
    }

    function getXml()
    {
        // Return XML for this value
        switch ($this->type) {
            case 'boolean':
                return '<boolean>'.(($this->data) ? '1' : '0').'</boolean>';
                break;
            case 'int':
                return '<int>'.$this->data.'</int>';
                break;
            case 'double':
                return '<double>'.$this->data.'</double>';
                break;
            case 'string':
                return '<string>'.htmlspecialchars($this->data).'</string>';
                break;
            case 'array':
                $return = '<array><data>'."\n";
                foreach ($this->data as $item) {
                    $return .= '  <value>'.$item->getXml()."</value>\n";
                }
                $return .= '</data></array>';
                return $return;
                break;
            case 'struct':
                $return = '<struct>'."\n";
                foreach ($this->data as $name => $value) {
					$name = htmlspecialchars($name);
                    $return .= "  <member><name>$name</name><value>";
                    $return .= $value->getXml()."</value></member>\n";
                }
                $return .= '</struct>';
                return $return;
                break;
            case 'date':
            case 'base64':
                return $this->data->getXml();
                break;
        }
        return false;
    }

    /**
     * Checks whether or not the supplied array is a struct or not
     *
     * @param unknown_type $array
     * @return boolean
     */
    function isStruct($array)
    {
        $expected = 0;
        foreach ($array as $key => $value) {
            if ((string)$key != (string)$expected) {
                return true;
            }
            $expected++;
        }
        return false;
    }
}

/**
 * IXR_MESSAGE
 *
 * @package IXR
 * @since 1.5.0
 *
 */
class IXR_Message
{
    var $message;
    var $messageType;  // methodCall / methodResponse / fault
    var $faultCode;
    var $faultString;
    var $methodName;
    var $params;

    // Current variable stacks
    var $_arraystructs = array();   // The stack used to keep track of the current array/struct
    var $_arraystructstypes = array(); // Stack keeping track of if things are structs or array
    var $_currentStructName = array();  // A stack as well
    var $_param;
    var $_value;
    var $_currentTag;
    var $_currentTagContents;
    // The XML parser
    var $_parser;

    function IXR_Message($message)
    {
        $this->message =& $message;
    }

    function parse()
    {
        // first remove the XML declaration
        // merged from WP #10698 - this method avoids the RAM usage of preg_replace on very large messages
        $header = preg_replace( '/<\?xml.*?\?'.'>/', '', substr($this->message, 0, 100), 1);
        $this->message = substr_replace($this->message, $header, 0, 100);
        if (trim($this->message) == '') {
            return false;
        }
        $this->_parser = xml_parser_create();
        // Set XML parser to take the case of tags in to account
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
        // Set XML parser callback functions
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($this->_parser, 'cdata');
        $chunk_size = 262144; // 256Kb, parse in chunks to avoid the RAM usage on very large messages
        $final = false;
        do {
            if (strlen($this->message) <= $chunk_size) {
                $final = true;
            }
            $part = substr($this->message, 0, $chunk_size);
            $this->message = substr($this->message, $chunk_size);
            if (!xml_parse($this->_parser, $part, $final)) {
                return false;
            }
            if ($final) {
                break;
            }
        } while (true);
        xml_parser_free($this->_parser);

        // Grab the error messages, if any
        if ($this->messageType == 'fault') {
            $this->faultCode = $this->params[0]['faultCode'];
            $this->faultString = $this->params[0]['faultString'];
        }
        return true;
    }

    function tag_open($parser, $tag, $attr)
    {
        $this->_currentTagContents = '';
        $this->currentTag = $tag;
        switch($tag) {
            case 'methodCall':
            case 'methodResponse':
            case 'fault':
                $this->messageType = $tag;
                break;
                /* Deal with stacks of arrays and structs */
            case 'data':    // data is to all intents and puposes more interesting than array
                $this->_arraystructstypes[] = 'array';
                $this->_arraystructs[] = array();
                break;
            case 'struct':
                $this->_arraystructstypes[] = 'struct';
                $this->_arraystructs[] = array();
                break;
        }
    }

    function cdata($parser, $cdata)
    {
        $this->_currentTagContents .= $cdata;
    }

    function tag_close($parser, $tag)
    {
        $valueFlag = false;
        switch($tag) {
            case 'int':
            case 'i4':
                $value = (int)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'double':
                $value = (double)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'string':
                $value = (string)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'dateTime.iso8601':
                $value = new IXR_Date(trim($this->_currentTagContents));
                $valueFlag = true;
                break;
            case 'value':
                // "If no type is indicated, the type is string."
                if (trim($this->_currentTagContents) != '') {
                    $value = (string)$this->_currentTagContents;
                    $valueFlag = true;
                }
                break;
            case 'boolean':
                $value = (boolean)trim($this->_currentTagContents);
                $valueFlag = true;
                break;
            case 'base64':
                $value = base64_decode($this->_currentTagContents);
                $valueFlag = true;
                break;
                /* Deal with stacks of arrays and structs */
            case 'data':
            case 'struct':
                $value = array_pop($this->_arraystructs);
                array_pop($this->_arraystructstypes);
                $valueFlag = true;
                break;
            case 'member':
                array_pop($this->_currentStructName);
                break;
            case 'name':
                $this->_currentStructName[] = trim($this->_currentTagContents);
                break;
            case 'methodName':
                $this->methodName = trim($this->_currentTagContents);
                break;
        }

        if ($valueFlag) {
            if (count($this->_arraystructs) > 0) {
                // Add value to struct or array
                if ($this->_arraystructstypes[count($this->_arraystructstypes)-1] == 'struct') {
                    // Add to struct
                    $this->_arraystructs[count($this->_arraystructs)-1][$this->_currentStructName[count($this->_currentStructName)-1]] = $value;
                } else {
                    // Add to array
                    $this->_arraystructs[count($this->_arraystructs)-1][] = $value;
                }
            } else {
                // Just add as a paramater
                $this->params[] = $value;
            }
        }
        $this->_currentTagContents = '';
    }
}

/**
 * IXR_Server
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Server
{
    var $data;
    var $callbacks = array();
    var $message;
    var $capabilities;

    function IXR_Server($callbacks = false, $data = false, $wait = false)
    {
        $this->setCapabilities();
        if ($callbacks) {
            $this->callbacks = $callbacks;
        }
        $this->setCallbacks();
        if (!$wait) {
            $this->serve($data);
        }
    }

    function serve($data = false)
    {
        if (!$data) {
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            	header('Content-Type: text/plain'); // merged from WP #9093
                die('XML-RPC server accepts POST requests only.');
            }

            global $HTTP_RAW_POST_DATA;
            if (empty($HTTP_RAW_POST_DATA)) {
                // workaround for a bug in PHP 5.2.2 - http://bugs.php.net/bug.php?id=41293
                $data = file_get_contents('php://input');
            } else {
                $data =& $HTTP_RAW_POST_DATA;
            }
        }
        $this->message = new IXR_Message($data);
        if (!$this->message->parse()) {
            $this->error(-32700, 'parse error. not well formed');
        }
        if ($this->message->messageType != 'methodCall') {
            $this->error(-32600, 'server error. invalid xml-rpc. not conforming to spec. Request must be a methodCall');
        }
        $result = $this->call($this->message->methodName, $this->message->params);

        // Is the result an error?
        if (is_a($result, 'IXR_Error')) {
            $this->error($result);
        }

        // Encode the result
        $r = new IXR_Value($result);
        $resultxml = $r->getXml();

        // Create the XML
        $xml = <<<EOD
<methodResponse>
  <params>
    <param>
      <value>
      $resultxml
      </value>
    </param>
  </params>
</methodResponse>

EOD;
      // Send it
      $this->output($xml);
    }

    function call($methodname, $args)
    {
        if (!$this->hasMethod($methodname)) {
            return new IXR_Error(-32601, 'server error. requested method '.$methodname.' does not exist.');
        }
        $method = $this->callbacks[$methodname];

        // Perform the callback and send the response
        if (count($args) == 1) {
            // If only one paramater just send that instead of the whole array
            $args = $args[0];
        }

        // Are we dealing with a function or a method?
        if (is_string($method) && substr($method, 0, 5) == 'this:') {
            // It's a class method - check it exists
            $method = substr($method, 5);
            if (!method_exists($this, $method)) {
                return new IXR_Error(-32601, 'server error. requested class method "'.$method.'" does not exist.');
            }

            //Call the method
            $result = $this->$method($args);
        } else {
            // It's a function - does it exist?
            if (is_array($method)) {
                if (!is_callable(array($method[0], $method[1]))) {
                    return new IXR_Error(-32601, 'server error. requested object method "'.$method[1].'" does not exist.');
                }
            } else if (!function_exists($method)) {
                return new IXR_Error(-32601, 'server error. requested function "'.$method.'" does not exist.');
            }

            // Call the function
            $result = call_user_func($method, $args);
        }
        return $result;
    }

    function error($error, $message = false)
    {
        // Accepts either an error object or an error code and message
        if ($message && !is_object($error)) {
            $error = new IXR_Error($error, $message);
        }
        $this->output($error->getXml());
    }

    function output($xml)
    {
        $charset = function_exists('get_option') ? get_option('blog_charset') : '';
        if ($charset)
            $xml = '<?xml version="1.0" encoding="'.$charset.'"?>'."\n".$xml;
        else
            $xml = '<?xml version="1.0"?>'."\n".$xml;
        $length = strlen($xml);
        header('Connection: close');
        header('Content-Length: '.$length);
        if ($charset)
            header('Content-Type: text/xml; charset='.$charset);
        else
            header('Content-Type: text/xml');
        header('Date: '.date('r'));
        echo $xml;
        exit;
    }

    function hasMethod($method)
    {
        return in_array($method, array_keys($this->callbacks));
    }

    function setCapabilities()
    {
        // Initialises capabilities array
        $this->capabilities = array(
            'xmlrpc' => array(
                'specUrl' => 'http://www.xmlrpc.com/spec',
                'specVersion' => 1
        ),
            'faults_interop' => array(
                'specUrl' => 'http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php',
                'specVersion' => 20010516
        ),
            'system.multicall' => array(
                'specUrl' => 'http://www.xmlrpc.com/discuss/msgReader$1208',
                'specVersion' => 1
        ),
        );
    }

    function getCapabilities($args)
    {
        return $this->capabilities;
    }

    function setCallbacks()
    {
        $this->callbacks['system.getCapabilities'] = 'this:getCapabilities';
        $this->callbacks['system.listMethods'] = 'this:listMethods';
        $this->callbacks['system.multicall'] = 'this:multiCall';
    }

    function listMethods($args)
    {
        // Returns a list of methods - uses array_reverse to ensure user defined
        // methods are listed before server defined methods
        return array_reverse(array_keys($this->callbacks));
    }

    function multiCall($methodcalls)
    {
        // See http://www.xmlrpc.com/discuss/msgReader$1208
        $return = array();
        foreach ($methodcalls as $call) {
            $method = $call['methodName'];
            $params = $call['params'];
            if ($method == 'system.multicall') {
                $result = new IXR_Error(-32600, 'Recursive calls to system.multicall are forbidden');
            } else {
                $result = $this->call($method, $params);
            }
            if (is_a($result, 'IXR_Error')) {
                $return[] = array(
                    'faultCode' => $result->code,
                    'faultString' => $result->message
                );
            } else {
                $return[] = array($result);
            }
        }
        return $return;
    }
}

/**
 * IXR_Request
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Request
{
    var $method;
    var $args;
    var $xml;

    function IXR_Request($method, $args)
    {
        $this->method = $method;
        $this->args = $args;
        $this->xml = <<<EOD
<?xml version="1.0"?>
<methodCall>
<methodName>{$this->method}</methodName>
<params>

EOD;
        foreach ($this->args as $arg) {
            $this->xml .= '<param><value>';
            $v = new IXR_Value($arg);
            $this->xml .= $v->getXml();
            $this->xml .= "</value></param>\n";
        }
        $this->xml .= '</params></methodCall>';
    }

    function getLength()
    {
        return strlen($this->xml);
    }

    function getXml()
    {
        return $this->xml;
    }
}

/**
 * IXR_Client
 *
 * @package IXR
 * @since 1.5.0
 *
 */
class IXR_Client
{
    var $server;
    var $port;
    var $path;
    var $useragent;
    var $response;
    var $message = false;
    var $debug = false;
    var $timeout;
    var $headers = array();

    // Storage place for an error message
    var $error = false;

    function IXR_Client($server, $path = false, $port = 80, $timeout = 15)
    {
        if (!$path) {
            // Assume we have been given a URL instead
            $bits = parse_url($server);
            $this->server = $bits['host'];
            $this->port = isset($bits['port']) ? $bits['port'] : 80;
            $this->path = isset($bits['path']) ? $bits['path'] : '/';

            // Make absolutely sure we have a path
            if (!$this->path) {
                $this->path = '/';
            }

            if ( ! empty( $bits['query'] ) ) {
                $this->path .= '?' . $bits['query'];
            }
        } else {
            $this->server = $server;
            $this->path = $path;
            $this->port = $port;
        }
        $this->useragent = 'The Incutio XML-RPC PHP Library';
        $this->timeout = $timeout;
    }

    function query()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $request = new IXR_Request($method, $args);
        $length = $request->getLength();
        $xml = $request->getXml();
        $r = "\r\n";
        $request  = "POST {$this->path} HTTP/1.0$r";

        // Merged from WP #8145 - allow custom headers
        $this->headers['Host']          = $this->server;
        $this->headers['Content-Type']  = 'text/xml';
        $this->headers['User-Agent']    = $this->useragent;
        $this->headers['Content-Length']= $length;

        foreach( $this->headers as $header => $value ) {
            $request .= "{$header}: {$value}{$r}";
        }
        $request .= $r;

        $request .= $xml;

        // Now send the request
        if ($this->debug) {
            echo '<pre class="ixr_request">'.htmlspecialchars($request)."\n</pre>\n\n";
        }

        if ($this->timeout) {
            $fp = @fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        } else {
            $fp = @fsockopen($this->server, $this->port, $errno, $errstr);
        }
        if (!$fp) {
            $this->error = new IXR_Error(-32300, 'transport error - could not open socket');
            return false;
        }
        fputs($fp, $request);
        $contents = '';
        $debugContents = '';
        $gotFirstLine = false;
        $gettingHeaders = true;
        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            if (!$gotFirstLine) {
                // Check line for '200'
                if (strstr($line, '200') === false) {
                    $this->error = new IXR_Error(-32300, 'transport error - HTTP status code was not 200');
                    return false;
                }
                $gotFirstLine = true;
            }
            if (trim($line) == '') {
                $gettingHeaders = false;
            }
            if (!$gettingHeaders) {
            	// merged from WP #12559 - remove trim
                $contents .= $line;
            }
            if ($this->debug) {
            	$debugContents .= $line;
            }
        }
        if ($this->debug) {
            echo '<pre class="ixr_response">'.htmlspecialchars($debugContents)."\n</pre>\n\n";
        }

        // Now parse what we've got back
        $this->message = new IXR_Message($contents);
        if (!$this->message->parse()) {
            // XML error
            $this->error = new IXR_Error(-32700, 'parse error. not well formed');
            return false;
        }

        // Is the message a fault?
        if ($this->message->messageType == 'fault') {
            $this->error = new IXR_Error($this->message->faultCode, $this->message->faultString);
            return false;
        }

        // Message must be OK
        return true;
    }

    function getResponse()
    {
        // methodResponses can only have one param - return that
        return $this->message->params[0];
    }

    function isError()
    {
        return (is_object($this->error));
    }

    function getErrorCode()
    {
        return $this->error->code;
    }

    function getErrorMessage()
    {
        return $this->error->message;
    }
}


/**
 * IXR_Error
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Error
{
    var $code;
    var $message;

    function IXR_Error($code, $message)
    {
        $this->code = $code;
        $this->message = htmlspecialchars($message);
    }

    function getXml()
    {
        $xml = <<<EOD
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>{$this->code}</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>{$this->message}</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>

EOD;
        return $xml;
    }
}

/**
 * IXR_Date
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Date {
    var $year;
    var $month;
    var $day;
    var $hour;
    var $minute;
    var $second;
    var $timezone;

    function IXR_Date($time)
    {
        // $time can be a PHP timestamp or an ISO one
        if (is_numeric($time)) {
            $this->parseTimestamp($time);
        } else {
            $this->parseIso($time);
        }
    }

    function parseTimestamp($timestamp)
    {
        $this->year = date('Y', $timestamp);
        $this->month = date('m', $timestamp);
        $this->day = date('d', $timestamp);
        $this->hour = date('H', $timestamp);
        $this->minute = date('i', $timestamp);
        $this->second = date('s', $timestamp);
        $this->timezone = '';
    }

    function parseIso($iso)
    {
        $this->year = substr($iso, 0, 4);
        $this->month = substr($iso, 4, 2);
        $this->day = substr($iso, 6, 2);
        $this->hour = substr($iso, 9, 2);
        $this->minute = substr($iso, 12, 2);
        $this->second = substr($iso, 15, 2);
        $this->timezone = substr($iso, 17);
    }

    function getIso()
    {
        return $this->year.$this->month.$this->day.'T'.$this->hour.':'.$this->minute.':'.$this->second.$this->timezone;
    }

    function getXml()
    {
        return '<dateTime.iso8601>'.$this->getIso().'</dateTime.iso8601>';
    }

    function getTimestamp()
    {
        return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
    }
}

/**
 * IXR_Base64
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Base64
{
    var $data;

    function IXR_Base64($data)
    {
        $this->data = $data;
    }

    function getXml()
    {
        return '<base64>'.base64_encode($this->data).'</base64>';
    }
}

/**
 * IXR_IntrospectionServer
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_IntrospectionServer extends IXR_Server
{
    var $signatures;
    var $help;

    function IXR_IntrospectionServer()
    {
        $this->setCallbacks();
        $this->setCapabilities();
        $this->capabilities['introspection'] = array(
            'specUrl' => 'http://xmlrpc.usefulinc.com/doc/reserved.html',
            'specVersion' => 1
        );
        $this->addCallback(
            'system.methodSignature',
            'this:methodSignature',
            array('array', 'string'),
            'Returns an array describing the return type and required parameters of a method'
        );
        $this->addCallback(
            'system.getCapabilities',
            'this:getCapabilities',
            array('struct'),
            'Returns a struct describing the XML-RPC specifications supported by this server'
        );
        $this->addCallback(
            'system.listMethods',
            'this:listMethods',
            array('array'),
            'Returns an array of available methods on this server'
        );
        $this->addCallback(
            'system.methodHelp',
            'this:methodHelp',
            array('string', 'string'),
            'Returns a documentation string for the specified method'
        );
    }

    function addCallback($method, $callback, $args, $help)
    {
        $this->callbacks[$method] = $callback;
        $this->signatures[$method] = $args;
        $this->help[$method] = $help;
    }

    function call($methodname, $args)
    {
        // Make sure it's in an array
        if ($args && !is_array($args)) {
            $args = array($args);
        }

        // Over-rides default call method, adds signature check
        if (!$this->hasMethod($methodname)) {
            return new IXR_Error(-32601, 'server error. requested method "'.$this->message->methodName.'" not specified.');
        }
        $method = $this->callbacks[$methodname];
        $signature = $this->signatures[$methodname];
        $returnType = array_shift($signature);

        // Check the number of arguments
        if (count($args) != count($signature)) {
            return new IXR_Error(-32602, 'server error. wrong number of method parameters');
        }

        // Check the argument types
        $ok = true;
        $argsbackup = $args;
        for ($i = 0, $j = count($args); $i < $j; $i++) {
            $arg = array_shift($args);
            $type = array_shift($signature);
            switch ($type) {
                case 'int':
                case 'i4':
                    if (is_array($arg) || !is_int($arg)) {
                        $ok = false;
                    }
                    break;
                case 'base64':
                case 'string':
                    if (!is_string($arg)) {
                        $ok = false;
                    }
                    break;
                case 'boolean':
                    if ($arg !== false && $arg !== true) {
                        $ok = false;
                    }
                    break;
                case 'float':
                case 'double':
                    if (!is_float($arg)) {
                        $ok = false;
                    }
                    break;
                case 'date':
                case 'dateTime.iso8601':
                    if (!is_a($arg, 'IXR_Date')) {
                        $ok = false;
                    }
                    break;
            }
            if (!$ok) {
                return new IXR_Error(-32602, 'server error. invalid method parameters');
            }
        }
        // It passed the test - run the "real" method call
        return parent::call($methodname, $argsbackup);
    }

    function methodSignature($method)
    {
        if (!$this->hasMethod($method)) {
            return new IXR_Error(-32601, 'server error. requested method "'.$method.'" not specified.');
        }
        // We should be returning an array of types
        $types = $this->signatures[$method];
        $return = array();
        foreach ($types as $type) {
            switch ($type) {
                case 'string':
                    $return[] = 'string';
                    break;
                case 'int':
                case 'i4':
                    $return[] = 42;
                    break;
                case 'double':
                    $return[] = 3.1415;
                    break;
                case 'dateTime.iso8601':
                    $return[] = new IXR_Date(time());
                    break;
                case 'boolean':
                    $return[] = true;
                    break;
                case 'base64':
                    $return[] = new IXR_Base64('base64');
                    break;
                case 'array':
                    $return[] = array('array');
                    break;
                case 'struct':
                    $return[] = array('struct' => 'struct');
                    break;
            }
        }
        return $return;
    }

    function methodHelp($method)
    {
        return $this->help[$method];
    }
}

/**
 * IXR_ClientMulticall
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_ClientMulticall extends IXR_Client
{
    var $calls = array();

    function IXR_ClientMulticall($server, $path = false, $port = 80)
    {
        parent::IXR_Client($server, $path, $port);
        $this->useragent = 'The Incutio XML-RPC PHP Library (multicall client)';
    }

    function addCall()
    {
        $args = func_get_args();
        $methodName = array_shift($args);
        $struct = array(
            'methodName' => $methodName,
            'params' => $args
        );
        $this->calls[] = $struct;
    }

    function query()
    {
        // Prepare multicall, then call the parent::query() method
        return parent::query('system.multicall', $this->calls);
    }
}
