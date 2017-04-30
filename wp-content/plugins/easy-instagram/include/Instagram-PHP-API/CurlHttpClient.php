<?php
class MC_CurlHttpClient {
    /**
     * Default User-Agent
     * @var string
     */
    const DEFAULT_USER_AGENT = 'Instagram PHP Implementation http://mauriciocuenca.com/';

    /**
     * Used HTTP request methods
     */
    const GET = 'GET';
    const POST = 'POST';
    const DELETE = 'DELETE';

    /**
     * cURL handler
     * @var resource
     */
    private $handler;

	private $curl_loops = 0;
	private $curl_max_loops = 20;

    /**
     * Store the POST fields
     */
    private $postParams = array();

    /**
     * Initiate a cURL session
     * @param string $url
     */
    public function __construct($uri) {
        $this->handler = curl_init($uri);
        $this->_setOptions();
    }

	private function _can_follow_redirects() {
		if ( ini_get( 'open_basedir' ) != '' ) {
			return FALSE;
		}
		
		$safe_mode = ini_get( 'safe_mode' );
		if ( $safe_mode && ( 'on' == strtolower( $safe_mode ) ) ) {
			return FALSE;
		}
		
		return TRUE;
	}

    protected function _setOptions() {
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handler, CURLOPT_USERAGENT, self::DEFAULT_USER_AGENT);
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, false); // On some servers the call failed on https requests
		
		if ( $this->_can_follow_redirects() ) {
	        curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);		
		}
    }

    /**
     * Set the URI
     * @param $uri
     */
    public function setUri($uri) {
        $this->handler = curl_init($uri);
        $this->_setOptions();
    }

    /**
     * Receive the response with full headers
     * @param boolean $value
     */
    public function setHeaders($value = true) {
        curl_setopt($this->handler, CURLOPT_HEADER, $value);
    }

    /**
     * Set the HTTP request method
     * @param string $method
     */
    public function setMethod($method = self::GET) {
        switch ($method) {
            case self::GET :
                curl_setopt($this->handler, CURLOPT_HTTPGET, true);
                break;
            case self::POST :
                curl_setopt($this->handler, CURLOPT_POST, true);
                break;
            case self::DELETE :
                curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, self::DELETE);
                break;
            default:
                throw new MC_CurlHttpClientException('Method not supported');
        }
    }

    /**
     * Add a new post param to the set
     * @param string $name
     * @param mixed $value
     */
    public function setPostParam($name, $value) {
        $this->postParams[$name] = $value;
        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->postParams);
    }

    /**
     * Get the response
     * @return string
     */
    public function getResponse() {
		if ( $this->_can_follow_redirects() ) {
	        $response = curl_exec($this->handler);
		}
		else {	
			$response = $this->curl_redir_exec();
		}
		
        curl_close($this->handler);

        return $response;
    }

    /**
     * Workaround for CURL not following redirects on some PHP configs 
     * @return string
     */
	public function curl_redir_exec() { 
		if ($this->curl_loops++ >= $this->curl_max_loops) {
			$this->curl_loops = 0;
			return FALSE;
		}
 
		curl_setopt( $this->handler, CURLOPT_HEADER, true );
 
		curl_setopt( $this->handler, CURLOPT_RETURNTRANSFER, true );
 
		$data = curl_exec( $this->handler );
		//print_r($data);
		list( $header, $data ) = explode( "\r\n", $data, 2 );
		$http_code = curl_getinfo( $this->handler, CURLINFO_HTTP_CODE );
		if ( $http_code == 301 || $http_code == 302 ) {
			$matches = array();
			preg_match( '/Location:(.*?)\n/', $header, $matches );
 
			$url = @parse_url( trim( array_pop( $matches ) ) );
			if ( ! $url ) {
				//couldn't process the url to redirect to
				$this->curl_loops = 0;
				return $data;
			}
 
			$last_url = parse_url( curl_getinfo( $this->handler, CURLINFO_EFFECTIVE_URL ) );
 
			if ( ! $url['scheme'] ) {
				$url['scheme'] = $last_url['scheme'];
			}
			
			if ( ! $url['host'] ) {
				$url['host'] = $last_url['host'];
			}
			
			if ( ! $url['path'] ) {
				$url['path'] = $last_url['path'];
			}
			
			$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] 
				. ( $url['query'] ? '?' . $url['query'] : '' );
 
			curl_setopt( $this->handler, CURLOPT_URL, $new_url );
 
			return $this->curl_redir_exec();
		} 
		else {
			$this->curl_loops = 0;
			return $data;
		}
	}

    /**
     * Extract the headers from a response string
     *
     * @param string $response
     * @return mixed[]
     */
    protected function extractHeaders($response) {
        $headers = array();

        // First, split body and headers
        $parts = preg_split('|(?:\r?\n){2}|m', $response_str, 2);
        if (!$parts[0]) return $headers;

        // Split headers part to lines
        $lines = explode("\n", $parts[0]);
        unset($parts);
        $last_header = null;

        foreach($lines as $line) {
            $line = trim($line, "\r\n");
            if ($line == "") break;

            // Locate headers like 'Location: ...' and 'Location:...' (note the missing space)
            if (preg_match("|^([\w-]+):\s*(.+)|", $line, $m)) {
                unset($last_header);
                $h_name = strtolower($m[1]);
                $h_value = $m[2];

                if (isset($headers[$h_name])) {
                    if (! is_array($headers[$h_name])) {
                        $headers[$h_name] = array($headers[$h_name]);
                    }

                    $headers[$h_name][] = $h_value;
                } else {
                    $headers[$h_name] = $h_value;
                }
                $last_header = $h_name;
            } else if (preg_match("|^\s+(.+)$|", $line, $m) && $last_header !== null) {
                if (is_array($headers[$last_header])) {
                    end($headers[$last_header]);
                    $last_header_key = key($headers[$last_header]);
                    $headers[$last_header][$last_header_key] .= $m[1];
                } else {
                    $headers[$last_header] .= $m[1];
                }
            }
        }

        return $headers;
    }
}

class MC_CurlHttpClientException extends Exception {}
