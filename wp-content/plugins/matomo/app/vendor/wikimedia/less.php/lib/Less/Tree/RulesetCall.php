<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_RulesetCall extends \Less_Tree
    {
        public $variable;
        public $type = "RulesetCall";
        /**
         * @param string $variable
         */
        public function __construct($variable)
        {
            $this->variable = $variable;
        }
        public function accept($visitor)
        {
        }
        public function compile($env)
        {
            $variable = new \Less_Tree_Variable($this->variable);
            $detachedRuleset = $variable->compile($env);
            '@phan-var Less_Tree_DetachedRuleset $detachedRuleset';
            return $detachedRuleset->callEval($env);
        }
    }
}
