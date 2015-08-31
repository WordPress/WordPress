<?php
if (!defined('W3TC')) { die(); }

/**
 * Class W3_UI_Settings_SettingsBase
 */
abstract class W3_UI_Settings_SettingsBase {

    /**
     * Gets the label connected with a config key in the provided area
     * @param string $config_key
     * @param string $area get the
     * @return string
     */
    public function get_label($config_key, $area) {
        $strings = $this->strings();
        if (isset($strings[$area][$config_key]))
            return $strings[$area][$config_key];
        else {
            $meta = $this->_get_meta($config_key);
            if ($meta) {
                return $meta['label'];
            }
            return '';
        }
    }

    /**
     * Retrieves meta data concerning a config key, label and connected area
     * @param string $config_key
     * @return string
     */
    public function get_meta($config_key) {
        $strings = $this->strings();
        return $this->_get_meta($config_key);
    }

    /**
     * Constructs and returns the meta data array.
     * @param $config_key
     * @return array|string
     */
    private function _get_meta($config_key) {
        $strings = $this->strings();
        foreach ($strings as $area => $settings) {
            foreach ($settings as $key => $label) {
                if ($key == $config_key)
                    return array('area' => $area, 'label' => $label);
            }
        }
        return '';
    }

    /**
     * Returns the config keys and there related labels for various areas.
     * @return array
     */
    protected abstract function strings();

    public function can_change($config_key, $meta) {
        return true;
    }
}