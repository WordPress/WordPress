<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Assignment extends \Less_Tree
    {
        public $key;
        public $value;
        public $type = 'Assignment';
        public function __construct($key, $val)
        {
            $this->key = $key;
            $this->value = $val;
        }
        public function accept($visitor)
        {
            $this->value = $visitor->visitObj($this->value);
        }
        public function compile($env)
        {
            return new \Less_Tree_Assignment($this->key, $this->value->compile($env));
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add($this->key . '=');
            $this->value->genCSS($output);
        }
        public function toCss()
        {
            return $this->key . '=' . $this->value->toCSS();
        }
    }
}
