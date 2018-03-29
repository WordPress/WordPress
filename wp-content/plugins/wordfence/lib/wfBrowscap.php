<?php
class wfBrowscap {
	protected $_cacheLoaded = false;
	protected $_userAgents = array();
	protected $_browsers = array();
	protected $_patterns = array();
	protected $_properties = array();
	protected $resultCache = array();
	const COMPRESSION_PATTERN_START = '@';
	const COMPRESSION_PATTERN_DELIMITER = '|';
	const REGEX_DELIMITER = '@';
	
	public static function shared() {
		static $_browscap = null;
		if ($_browscap === null) {
			$_browscap = new wfBrowscap();
		}
		return $_browscap;
	}

    public function getBrowser($user_agent){
        if (!$this->_cacheLoaded) {
                if (!$this->_loadCache('wfBrowscapCache.php')) {
                    throw new Exception('Cannot load this cache version - the cache format is not compatible.');
                }
            }

        $browser = array();
        foreach ($this->_patterns as $pattern => $pattern_data) {
            if (preg_match($pattern . 'i', $user_agent, $matches)) {
                if (1 == count($matches)) {
                    $key = $pattern_data;

                    $simple_match = true;
                } else {
                    $pattern_data = unserialize($pattern_data);

                    array_shift($matches);

                    $match_string = self::COMPRESSION_PATTERN_START
                        . implode(self::COMPRESSION_PATTERN_DELIMITER, $matches);

                    if (!isset($pattern_data[$match_string])) {
                        continue;
                    }

                    $key = $pattern_data[$match_string];

                    $simple_match = false;
                }

                $browser = array(
                    $user_agent,
                    trim(strtolower($pattern), self::REGEX_DELIMITER),
                    $this->_pregUnQuote($pattern, $simple_match ? false : $matches)
                );

                $browser = $value = $browser + unserialize($this->_browsers[$key]);

                while (array_key_exists(3, $value)) {
                    $value = unserialize($this->_browsers[$value[3]]);
                    $browser += $value;
                }

                if (!empty($browser[3])) {
                    $browser[3] = $this->_userAgents[$browser[3]];
                }

                break;
            }
        }

        $array = array();
        foreach ($browser as $key => $value) {
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }
            $array[$this->_properties[$key]] = $value;
        }

        return $array;
    }
    protected function _loadCache($cache_file){
        $cache_version  = null;
        $source_version = null;
        $browsers       = array();
        $userAgents     = array();
        $patterns       = array();
        $properties     = array();

        $this->_cacheLoaded = false;

        require $cache_file;

        $this->_source_version = $source_version;
        $this->_browsers       = $browsers;
        $this->_userAgents     = $userAgents;
        $this->_patterns       = $patterns;
        $this->_properties     = $properties;

        $this->_cacheLoaded = true;

        return true;
    }
    protected function _pregUnQuote($pattern, $matches){
        $search  = array(
            '\\' . self::REGEX_DELIMITER, '\\.', '\\\\', '\\+', '\\[', '\\^', '\\]', '\\$', '\\(', '\\)', '\\{', '\\}',
            '\\=', '\\!', '\\<', '\\>', '\\|', '\\:', '\\-', '.*', '.', '\\?'
        );
        $replace = array(
            self::REGEX_DELIMITER, '\\?', '\\', '+', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|',
            ':', '-', '*', '?', '.'
        );

        $result = substr(str_replace($search, $replace, $pattern), 2, -2);

        if ($matches) {
            foreach ($matches as $one_match) {
                $num_pos = strpos($result, '(\d)');
                $result  = substr_replace($result, $one_match, $num_pos, 4);
            }
        }

        return $result;
    }
}
