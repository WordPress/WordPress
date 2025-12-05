<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Url extends \Less_Tree
    {
        public $attrs;
        public $value;
        public $currentFileInfo;
        public $isEvald;
        public $type = 'Url';
        /**
         * @param Less_Tree_Variable|Less_Tree_Quoted|Less_Tree_Anonymous $value
         * @param array|null $currentFileInfo
         * @param bool|null $isEvald
         */
        public function __construct($value, $currentFileInfo = null, $isEvald = null)
        {
            $this->value = $value;
            $this->currentFileInfo = $currentFileInfo;
            $this->isEvald = $isEvald;
        }
        public function accept($visitor)
        {
            $this->value = $visitor->visitObj($this->value);
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add('url(');
            $this->value->genCSS($output);
            $output->add(')');
        }
        /**
         * @param Less_Environment $ctx
         */
        public function compile($ctx)
        {
            $val = $this->value->compile($ctx);
            if (!$this->isEvald) {
                // Add the base path if the URL is relative
                if (\Less_Parser::$options['relativeUrls'] && $this->currentFileInfo && \is_string($val->value) && \Less_Environment::isPathRelative($val->value)) {
                    $rootpath = $this->currentFileInfo['uri_root'];
                    if (!$val->quote) {
                        $rootpath = \preg_replace('/[\\(\\)\'"\\s]/', '\\$1', $rootpath);
                    }
                    $val->value = $rootpath . $val->value;
                }
                $val->value = \Less_Environment::normalizePath($val->value);
            }
            // Add cache buster if enabled
            if (\Less_Parser::$options['urlArgs']) {
                if (!\preg_match('/^\\s*data:/', $val->value)) {
                    $delimiter = \strpos($val->value, '?') === \false ? '?' : '&';
                    $urlArgs = $delimiter . \Less_Parser::$options['urlArgs'];
                    $hash_pos = \strpos($val->value, '#');
                    if ($hash_pos !== \false) {
                        $val->value = \substr_replace($val->value, $urlArgs, $hash_pos, 0);
                    } else {
                        $val->value .= $urlArgs;
                    }
                }
            }
            return new \Less_Tree_URL($val, $this->currentFileInfo, \true);
        }
    }
}
