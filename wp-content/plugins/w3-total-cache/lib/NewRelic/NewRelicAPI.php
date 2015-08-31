<?php
define('NEWRELIC_API_BASE', 'https://api.newrelic.com');

/**
 * Interacts with the New Relic Connect API
 * @link newrelic.github.com/newrelic_api/
 */
class NewRelicAPI {
    private $_api_key;
    private $_account_id;

    /**
     * @param string $api_key New Relic API Key
     * @param string $account_id optional
     */
    function __construct($api_key, $account_id = '') {
        $this->_api_key = $api_key;
        $this->_account_id = $account_id;
    }

    /**
     * @param string $api_call_url url path with query string used to define what to get from the NR API
     * @throws Exception
     * @return bool
     */
    private function _get($api_call_url) {
        $defaults = array(
            'headers'=>'x-api-key:'.$this->_api_key
        );
        $url = NEWRELIC_API_BASE . $api_call_url;

        $response = wp_remote_get($url, $defaults);

        if (is_wp_error($response)) {
            throw new Exception('Could not get data');
        } elseif ($response['response']['code'] == 200) {
            $return = $response['body'];
        } else {
            switch ($response['response']['code']) {
                case '403':
                    $message = __('Invalid API key or Account ID', 'w3-total-cache');
                    break;
                default:
                    $message = $response['response']['message'];
            }

            throw new Exception($message, $response['response']['code']);
        }
        return $return;
    }

    /**
     * @param string $api_call_url url path with query string used to define what to get from the NR API
     * @param array $params key value array.
     * @throws Exception
     * @return bool
     */
    private function _put($api_call_url, $params) {
        $defaults = array(
            'method' => 'PUT',
            'headers'=>'x-api-key:'.$this->_api_key,
            'body' => $params
        );
        $url = NEWRELIC_API_BASE . $api_call_url;
        $response = wp_remote_request($url, $defaults);

        if (is_wp_error($response)) {
            throw new Exception('Could not put data');
        } elseif ($response['response']['code'] == 200) {
            $return = true;
        } else {
            throw new Exception($response['response']['message'], $response['response']['code']);
        }
        return $return;
    }

    /**
     * Get the Dashboard HTML fragment for provided application or for all applications connected with the API key
     * @param int $application_id
     * @return bool
     */
    function get_dashboard($application_id = 0) {
        if ($application_id)
            $dashboard = $this->_get("/application_dashboard?application_id=$application_id");
        else
            $dashboard = $this->_get('/application_dashboard');
        return $dashboard;
    }

    /**
     * Get applications connected with the API key and provided account id.
     * @param int $account_id
     * @return array
     */
    function get_applications($account_id = 0) {
        if ($account_id == 0)
            $account_id = $this->_account_id;
        $applications = array();
        if ($xml_string  = $this->_get("/api/v1/accounts/{$account_id}/applications.xml")) {
            $xml = simplexml_load_string($xml_string);
            foreach($xml->application as $application) {
                $applications[(int)$application->id] = (string)$application->name;
            }
        }
        return $applications;
    }

    /**
     * Get the application summary data for the provided application
     * @param $application_id
     * @return array array(metric name => metric value)
     */
    function get_application_summary($application_id) {
        $summary = array();

        if ($xml_string = $this->_get("/api/v1/accounts/{$this->_account_id}/applications/{$application_id}/threshold_values.xml")) {
            $xml = simplexml_load_string($xml_string);
            foreach($xml->{'threshold_value'} as $value) {
                $summary[(string)$value['name']] = (string)$value['formatted_metric_value'];
            }
        }
        return $summary;
    }

    /**
     * Return key value array with information connected to account.
     * @return array|mixed|null
     */
    function get_account() {
        $account = null;
        if ($xml_string = $this->_get('/api/v1/accounts.xml')) {
            $xml = simplexml_load_string($xml_string);
            foreach($xml->account as $account_values) {
                $account=json_decode(json_encode((array) $account_values), 1);
                break;
            }
        }
        return $account;
    }

    /**
     * Get key value array with application settings
     * @param $application_id
     * @return array|mixed
     */
    function get_application_settings($application_id) {
        $settings = array();
        if ($xml_string = $this->_get("/api/v1/accounts/:account_id/application_settings/{$application_id}.xml")) {
            $xml = simplexml_load_string($xml_string);
            $settings=json_decode(json_encode((array) $xml), 1);
        }
        return $settings;
    }

    /**
     * Update application settings. verifies the keys in provided settings array is acceptable
     * @param $application_id
     * @param $settings
     * @return bool
     */
    function update_application_settings($application_id, $settings) {
        $supported = array('alerts_enabled', 'app_apdex_t','rum_apdex_t','rum_enabled');
        $call = "/api/v1/accounts/{$this->_account_id}/application_settings/{$application_id}.xml";
        $params = array();
        foreach($settings as $key => $value)  {
            if (in_array($key, $supported))
                $params[$key] = $value;
        }

        return $this->_put($call, $params);
    }

    /**
     * Returns the available metric names for provided application
     * @param $application_id
     * @param string $regex
     * @param string $limit
     * @return array|mixed
     */
    function get_metric_names($application_id, $regex = '', $limit = '') {
        $call = "/api/v1/agents/{$application_id}/metrics.json";
        $callQS = '';
        $qs = array();
        if ($regex)
            $qs[] = 're=' . urlencode($regex);
        if ($limit)
            $qs[] = 'limit=' . $limit;
        if ($qs)
            $callQS = '?' . implode('&', $qs);
        $json = $this->_get($call . $callQS);
        $metric_names=json_decode($json);
        return $metric_names;
    }

    /**
     * Gets the metric data for the provided metric names.
     * @param string $account_id
     * @param string $application_id
     * @param string $begin XML date in GMT
     * @param string $to XML date in GMT
     * @param array $metrics
     * @param string $field
     * @param bool $summary if values should be merged or overtime
     * @return array|mixed
     */
    function get_metric_data($account_id, $application_id, $begin, $to, $metrics, $field, $summary = true) {
        $metricParamArray = array();
        foreach($metrics as $metric) {
            $metricParamArray[] = 'metrics[]=' . $metric;
        }
        $metricQS = implode('&', $metricParamArray);
        $fieldsQS = 'field=' . $field;
        $agentQS =  'agent_id=' . $application_id;
        $beginQS = 'begin=' . $begin;
        $toQS = 'end=' . $to;
        $summaryQS = $summary ? 'summary=1' : 'summary=0';
        $command = $beginQS . '&' . $toQS . '&' . $metricQS . '&' . $fieldsQS . '&' . $agentQS . '&' . $summaryQS;

        $json = $this->_get("/api/v1/accounts/{$account_id}/metrics/data.json?{$command}");
        $metric_data=json_decode($json);

        return $metric_data;
    }
}
