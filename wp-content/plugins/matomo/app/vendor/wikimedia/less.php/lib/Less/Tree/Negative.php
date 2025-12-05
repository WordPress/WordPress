<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Negative extends \Less_Tree
    {
        public $value;
        public $type = 'Negative';
        public function __construct($node)
        {
            $this->value = $node;
        }
        // function accept($visitor) {
        //	$this->value = $visitor->visit($this->value);
        //}
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add('-');
            $this->value->genCSS($output);
        }
        public function compile($env)
        {
            if (\Less_Environment::isMathOn()) {
                $ret = new \Less_Tree_Operation('*', [new \Less_Tree_Dimension(-1), $this->value]);
                return $ret->compile($env);
            }
            return new \Less_Tree_Negative($this->value->compile($env));
        }
    }
}
