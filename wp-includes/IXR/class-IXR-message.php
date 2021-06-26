<?php

/**
 * IXR_MESSAGE
 *
 * @package IXR
 * @since 1.5.0
 *
 */
class IXR_Message
{
    var $message     = false;
    var $messageType = false;  // methodCall / methodResponse / fault
    var $faultCode   = false;
    var $faultString = false;
    var $methodName  = '';
    var $params      = array();

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

	/**
	 * PHP5 constructor.
	 */
    function __construct( $message )
    {
        $this->message =& $message;
    }

	/**
	 * PHP4 constructor.
	 */
	public function IXR_Message( $message ) {
		self::__construct( $message );
	}

    function parse()
    {
        if ( ! function_exists( 'xml_parser_create' ) ) {
            trigger_error( __( "PHP's XML extension is not available. Please contact your hosting provider to enable PHP's XML extension." ) );
            return false;
        }

        // first remove the XML declaration
        // merged from WP #10698 - this method avoids the RAM usage of preg_replace on very large messages
        $header = preg_replace( '/<\?xml.*?\?'.'>/s', '', substr( $this->message, 0, 100 ), 1 );
        $this->message = trim( substr_replace( $this->message, $header, 0, 100 ) );
        if ( '' == $this->message ) {
            return false;
        }

        // Then remove the DOCTYPE
        $header = preg_replace( '/^<!DOCTYPE[^>]*+>/i', '', substr( $this->message, 0, 200 ), 1 );
        $this->message = trim( substr_replace( $this->message, $header, 0, 200 ) );
        if ( '' == $this->message ) {
            return false;
        }

        // Check that the root tag is valid
        $root_tag = substr( $this->message, 0, strcspn( substr( $this->message, 0, 20 ), "> \t\r\n" ) );
        if ( '<!DOCTYPE' === strtoupper( $root_tag ) ) {
            return false;
        }
        if ( ! in_array( $root_tag, array( '<methodCall', '<methodResponse', '<fault' ) ) ) {
            return false;
        }

        // Bail if there are too many elements to parse
        $element_limit = 30000;
        if ( function_exists( 'apply_filters' ) ) {
            /**
             * Filters the number of elements to parse in an XML-RPC response.
             *
             * @since 4.0.0
             *
             * @param int $element_limit Default elements limit.
             */
            $element_limit = apply_filters( 'xmlrpc_element_limit', $element_limit );
        }
        if ( $element_limit && 2 * $element_limit < substr_count( $this->message, '<' ) ) {
            return false;
        }

        $this->_parser = xml_parser_create();
        // Set XML parser to take the case of tags in to account
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
        // Set XML parser callback functions
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($this->_parser, 'cdata');

        // 256Kb, parse in chunks to avoid the RAM usage on very large messages
        $chunk_size = 262144;

        /**
         * Filters the chunk size that can be used to parse an XML-RPC response message.
         *
         * @since 4.4.0
         *
         * @param int $chunk_size Chunk size to parse in bytes.
         */
        $chunk_size = apply_filters( 'xmlrpc_chunk_parsing_size', $chunk_size );

        $final = false;

        do {
            if (strlen($this->message) <= $chunk_size) {
                $final = true;
            }

            $part = substr($this->message, 0, $chunk_size);
            $this->message = substr($this->message, $chunk_size);

            if (!xml_parse($this->_parser, $part, $final)) {
                xml_parser_free($this->_parser);
                unset($this->_parser);
                return false;
            }

            if ($final) {
                break;
            }
        } while (true);

        xml_parser_free($this->_parser);
        unset($this->_parser);

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
                // Just add as a parameter
                $this->params[] = $value;
            }
        }
        $this->_currentTagContents = '';
    }
}
