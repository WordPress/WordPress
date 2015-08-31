<?php

class NewRelicPresentation {

    /**
     * @param $metric_data
     * @return array
     */
    static function format_metrics_dashboard($metric_data) {
        $formatted = array();
        foreach ($metric_data as $name => $data) {
            switch($name) {
                case 'EndUser':
                    $name = __('Page load time', 'w3-total-cache');
                    break;
                case 'WebTransation':
                    $name = __('Web Transaction', 'w3-total-cache');
                    break;
                case 'Database':
                    $name = __('Database', 'w3-total-cache');
                    break;
                default:
                    break;
            }

            if ($data == 'N/A')
                $formatted[$name] = $data;
            else
                $formatted[$name] = w3_convert_secs_to_time(array_shift($data[0])->average_response_time);

        }
        return $formatted;
    }

    /**
     * Takes metric data in array(page => time in sec) format and returns array(page => formatted time)
     * @param $metric_data
     * @return array
     */
    static function format_slowest_pages($metric_data) {
        $formatted = array();
        foreach($metric_data as $page => $time) {
            $formatted[$page] = w3_convert_secs_to_time($time);
        }
        return $formatted;
    }

    /**
     * Takes metric data in array(transaction => time in sec) format and returns array(transaction => formatted time)
     * @param $metric_slowest_webtransactions
     * @return array
     */
    public static function format_slowest_webtransactions($metric_slowest_webtransactions) {
        return self::format_slowest_pages($metric_slowest_webtransactions);
    }

    /**
     * Takes metric data in array(query => time in sec) format and returns array(query => formatted time)
     * @param $metric_slowest_database
     * @return array
     */
    public static function format_slowest_database($metric_slowest_database) {
        return self::format_slowest_pages($metric_slowest_database);
    }
}
