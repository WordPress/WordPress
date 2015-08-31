<?php

/**
 * Google Page Speed API
 */
define('W3TC_PAGESPEED_API_URL', 'https://www.googleapis.com/pagespeedonline/v1/runPagespeed');

/**
 * Class W3_PageSpeed
 */
class W3_PageSpeed {
    /**
     * API Key
     *
     * @var string
     */
    var $key = '';

    /**
     * PHP5-style constructor
     */
    function __construct() {
        $config = w3_instance('W3_Config');

        $this->key = $config->get_string('widget.pagespeed.key');
    }

    /**
     * Analyze URL
     *
     * @param string $url
     * @param bool $force
     * @return array|bool|mixed|null
     */
    function analyze($url, $force = false) {
        $results = null;

        if (!$force) {
            $results = $this->_load($url);
        }

        if ($results === null) {
            $json = $this->_request($url);

            if ($json) {
                $results = $this->_parse($json);

                if ($results) {
                    $this->_sort($results);
                }
            }

            $this->_store($url, $results);
        }

        return $results;
    }

    /**
     * Make API request
     *
     * @param string $url
     * @return string
     */
    function _request($url) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');
        w3_require_once(W3TC_INC_DIR . '/functions/url.php');

        $request_url = w3_url_format(W3TC_PAGESPEED_API_URL, array(
            'url' => $url,
            'key' => $this->key,
        ));

        $response = w3_http_get($request_url);

        if (!is_wp_error($response) && $response['response']['code'] == 200) {
            return $response['body'];
        }

        return false;
    }

    /**
     * Parse response
     *
     * @param string $json
     * @return array|bool
     */
    function _parse($json) {
        $data = json_decode($json);
        $results = false;

        if (isset($data->formattedResults)) {
            $results = array(
                'url' => $data->id,
                'code' => $data->responseCode,
                'title' => $data->title,
                'score' => $data->score,
                'rules' => array()
            );

            foreach ((array) $data->formattedResults->ruleResults as $i => $rule_result) {
                $results['rules'][$i] = array(
                    'name' => $rule_result->localizedRuleName,
                    'score' => $rule_result->ruleScore,
                    'impact' => $rule_result->ruleImpact,
                    'priority' => $this->_get_priority($rule_result->ruleImpact),
                    'resolution' => $this->_get_resolution($rule_result->localizedRuleName),
                    'blocks' => array()
                );

                if (isset($rule_result->urlBlocks)) {
                    foreach ((array) $rule_result->urlBlocks as $j => $url_block) {
                        $args = isset( $url_block->header->args ) ? $url_block->header->args : array();
                        $results['rules'][$i]['blocks'][$j] = array(
                            'header' => $this->_format_string( $url_block->header->format, $args ),
                            'urls' => array()
                        );

                        if (isset($url_block->urls)) {
                            foreach ((array) $url_block->urls as $k => $url) {
                                $args = isset( $url->result->args ) ? $url->result->args : array();
                                $results['rules'][$i]['blocks'][$j]['urls'][$k] = array(
                                    'result' => $this->_format_string( $url->result->format, $args )
                                );
                            }
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Returns rule priority by impact
     *
     * @param float $impact
     * @return string
     */
    function _get_priority($impact) {
        if ($impact < 3) {
            $priority = 'low';
        } elseif ($impact >= 3 && $impact <= 10) {
            $priority = 'medium';
        } else {
            $priority = 'high';
        }

        return $priority;
    }

    /**
     * Returns resolution
     *
     * @param string $code
     * @return array
     */
    function _get_resolution($code) {
        switch ($code) {
            case 'MinifyHTML':
                return array(
                    'header' => 'Enable HTML Minify',
                    'tab' => 'minify'
                );

            case 'MinifyJavaScript':
            case 'DeferParsingJavaScript':
                return array(
                    'header' => 'Enable JavaScript Minify',
                    'tab' => 'minify'
                );

            case 'MinifyCss':
            case 'PutCssInTheDocumentHead':
                return array(
                    'header' => 'Enable CSS Minify',
                    'tab' => 'minify'
                );

            case 'AvoidCssImport':
                return array(
                    'header' => 'Enable CSS Minify and @import processing',
                    'tab' => 'minify'
                );

            case 'OptimizeTheOrderOfStylesAndScripts':
                return array(
                    'header' => 'Enable JavaScript and CSS Minify',
                    'tab' => 'minify'
                );

            case 'PreferAsyncResources':
                return array(
                    'header' => 'Switch to non-blocking JavaScript embedding',
                    'tab' => 'minify'
                );

            case 'RemoveQueryStringsFromStaticResources':
                return array(
                    'header' => 'Disable the "Prevent caching of objects after settings change" feature',
                    'tab' => 'browsercache'
                );

            case 'LeverageBrowserCaching':
                return array(
                    'header' => 'Enable the expires header on the Browser Cache Settings tab',
                    'tab' => 'browsercache'
                );

            case 'SpecifyACacheValidator':
                return array(
                    'header' => 'Enable the ETag header on the Browser Cache Settings tab',
                    'tab' => 'browsercache'
                );
        }

        return array();
    }

    /**
     * Loads results from cache
     *
     * @param string $url
     * @return mixed|null
     */
    function _load($url) {
        $file = $this->_get_cache_file($url);

        if (is_readable($file)) {
            $data = @file_get_contents($file);

            if ($data) {
                return @unserialize($data);
            }
        }

        return null;
    }

    /**
     * Save results to cache
     *
     * @param string $url
     * @param array $results
     * @return bool|int
     */
    function _store($url, $results) {
        $file = $this->_get_cache_file($url);
        $data = serialize($results);

        return @file_put_contents($file, $data);
    }

    /**
     * Returns cache file
     *
     * @param string $url
     * @return string
     */
    function _get_cache_file($url) {
        return W3TC_CACHE_TMP_DIR . '/pagespeed_' . md5($url);
    }

    var $_format_string_args = array();

    /**
     * Formats string
     *
     * @param string $format
     * @param array $args
     * @return mixed
     */
    function _format_string($format, $args) {
        $result = $format;
        if ( !empty( $args ) ) {
            $this->_format_string_args = $args;

            $result = preg_replace_callback( '~\$([0-9]+)~', array(
                                                                  &$this,
                                                                  '_format_string_callback'
                                                             ), $format );
        }

        return $result;
    }

    /**
     * Format string callback
     *
     * @param array $matches
     * @return string
     */
    function _format_string_callback($matches) {
        $index = (int) $matches[1] - 1;

        if (isset($this->_format_string_args[$index]->value)) {
            switch ($this->_format_string_args[$index]->type) {
                case 'URL':
                    return sprintf('<a href="%s">%s</a>', $this->_format_string_args[$index]->value, $this->_format_string_args[$index]->value);

                default:
                    return $this->_format_string_args[$index]->value;
            }
        }

        return $matches[0];
    }

    /**
     * Sort results
     *
     * @param array $results
     * @return void
     */
    function _sort(&$results) {
        if (isset($results['rules'])) {
            usort($results['rules'], array(
                &$this,
                '_sort_cmp'
            ));
        }
    }

    /**
     * Compare function
     *
     * @param array $rule_result1
     * @param array $rule_result2
     * @return int
     */
    function _sort_cmp(&$rule_result1, &$rule_result2) {
        if ($rule_result1['impact'] == $rule_result2['impact']) {
            return 0;
        }

        return ($rule_result1['impact'] > $rule_result2['impact']) ? -1 : 1;
    }
}
