<?php

class W3_AdminActions_ActionHandler {
    private $_default = null;
    private $_handlers = array('flush', 'cdn', 'support', 'config', 'new_relic', 'test', 'licensing', 'aws', 'edge_mode', 'extensions', 'default');
    private $_page;
    public function __construct($default_handler = null) {
        $this->_default = $default_handler;
    }

    public function set_default($default_handler) {
        $this->_default = $default_handler;
    }

    public function set_current_page($page) {
        $this->_page = $page;
    }

    public function execute($action) {
        $handler = $this->_get_handler($action);
        $this->_execute($handler, $action);
    }

    public function exists($action) {
        if ($this->_default)
            if (method_exists($this->_default, $action))
                return true;
        $handler =  $this->_get_handler($action);
        return $handler != '';
    }

    private function _get_handler($action) {
        foreach($this->_handlers as $handler) {
            if (strpos($action, "action_$handler") !== false || strpos($action, "action_save_$handler") !== false)
                return $handler;
        }
        if ($action == 'action_save_options')
            return 'default';

        return '';
    }

    private function _execute($handler, $action) {
        if (strpos($action, 'action_') === false)
            throw new Exception(sprintf(__('%s is not an correct action.'), $action));
        if ($handler == '') {
            if (method_exists($this->_default, $action)) {
                call_user_func(array($this->_default, $action));
                return;
            }
        } else {
            $handler = ucfirst($handler);
            $handler_w = explode('_', $handler);
            $handler = '';
            foreach($handler_w as $w)
                $handler .= ucfirst($w);
            $handler_class = "W3_AdminActions_{$handler}ActionsAdmin";
            $handler_object = w3_instance($handler_class);
            if (method_exists($handler_object, $action)) {
                $handler_object->$action();
                return;
            }
        }
        throw new Exception(sprintf(__('action %s does not exist for %s'), $action, $handler));
    }
}
