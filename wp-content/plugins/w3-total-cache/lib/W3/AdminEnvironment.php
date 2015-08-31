<?php

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

/**
 * Class W3_Environment
 */
class W3_AdminEnvironment {
    /*
     * Fixes environment
     * @param W3_Config $config
     * @throws SelfTestExceptions
     **/
    function fix_in_wpadmin($config, $force_all_checks = false) {
        $exs = new SelfTestExceptions();
        $fix_on_event = false;
        if (w3_is_multisite() && w3_get_blog_id() != 0) {
            if (get_transient('w3tc_config_changes') != ($md5_string = $config->get_md5() )) {
                $fix_on_event = true;
                set_transient('w3tc_config_changes', $md5_string, 3600);
            }
        }
        // call plugin-related handlers
        foreach ($this->get_handlers($config) as $h) {
            try {
                $h->fix_on_wpadmin_request($config, $force_all_checks);
                if ($fix_on_event) {
                    $this->fix_on_event($config, 'admin_request');
                }
            } catch (SelfTestExceptions $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        $exs = new SelfTestExceptions();

        // call plugin-related handlers
        foreach ($this->get_handlers($config) as $h) {
            try {
                $h->fix_on_event($config, $event);
            } catch (SelfTestExceptions $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment after plugin deactivation
     * @param W3_Config $config
     * @throws SelfTestExceptions
     */
    public function fix_after_deactivation($config) {
        $exs = new SelfTestExceptions();

        // call plugin-related handlers
        foreach ($this->get_handlers($config) as $h) {
            try {
                $h->fix_after_deactivation();
            } catch (SelfTestExceptions $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns an array[filename]=rules of rules for .htaccess or nginx files
     * @param W3_Config $config
     * @return array
     */
    public function get_required_rules($config) {
        $rewrite_rules_descriptors = array();
        $rewrite_rules_descriptors_last = array();

        foreach ($this->get_handlers($config) as $h) {
            $required_rules = $h->get_required_rules($config);

            if (!is_null($required_rules)) {
                foreach ($required_rules as $descriptor) {
                    $filename = $descriptor['filename'];
                    $last = isset($descriptor['last']) && $descriptor['last'];
                    if ($last) {
                        $content = isset($rewrite_rules_descriptors_last[$filename]) ?
                            $rewrite_rules_descriptors_last[$filename]['content'] : '';

                        $rewrite_rules_descriptors_last[$filename] = array(
                            'filename' => $filename,
                            'content' => $content . $descriptor['content']
                        );
                    } else {
                        $content = isset($rewrite_rules_descriptors[$filename]) ?
                            $rewrite_rules_descriptors[$filename]['content'] : '';

                        $rewrite_rules_descriptors[$filename] = array(
                            'filename' => $filename,
                            'content' => $content . $descriptor['content']
                        );
                    }
                }
            }
        }
        $rewrite_rules_descriptors_temp = array();
        foreach($rewrite_rules_descriptors as $filename => $descriptor) {
            if (isset($rewrite_rules_descriptors_last[$filename]['content'])) {
                $rewrite_rules_descriptors_temp[$filename] = array(
                    'filename' => $filename,
                    'content' => $descriptor['content'] . $rewrite_rules_descriptors_last[$filename]['content']
                );
            } else {
                $rewrite_rules_descriptors_temp[$filename] = array(
                    'filename' => $filename,
                    'content' => $descriptor['content']
                );
            }
        }
        ksort($rewrite_rules_descriptors_temp);
        return $rewrite_rules_descriptors_temp;
    }

    /**
     * Returns plugin-related environment handlers
     * @param W3_Config $config
     * @return array
     */
    private function get_handlers($config) {
        $a = array(
            w3_instance('W3_GenericAdminEnvironment'),
            w3_instance('W3_MinifyAdminEnvironment'),
            w3_instance('W3_PgCacheAdminEnvironment'),
            w3_instance('W3_BrowserCacheAdminEnvironment'),
            w3_instance('W3_ObjectCacheAdminEnvironment'),
            w3_instance('W3_DbCacheAdminEnvironment'),
            w3_instance('W3_CdnAdminEnvironment'),
            w3_instance('W3_NewRelicAdminEnvironment')
        );

        if (w3_is_pro($config) || w3_is_enterprise($config))
            array_push($a,
                w3_instance('W3_Pro_FragmentCacheAdminEnvironment'));
        
        return $a;
    }

    public function get_other_instructions($config) {
        $instructions_descriptors = array();

        foreach ($this->get_handlers($config) as $h) {
            if (method_exists($h, 'get_instructions')) {
                $instructions = $h->get_instructions($config);
                if (!is_null($instructions)) {
                    foreach ($instructions as $descriptor) {
                        $area = $descriptor['area'];
                        $instructions_descriptors[$area][] = array(
                            'title' => $descriptor['title'],
                            'content' => $descriptor['content']
                        );
                    }
                }
            }

        }
        return $instructions_descriptors;
    }
}
