<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Paren extends \Less_Tree
    {
        /** @var Less_Tree $value */
        public $value;
        public $type = 'Paren';
        /**
         * @param Less_Tree $value
         */
        public function __construct($value)
        {
            $this->value = $value;
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
            $output->add('(');
            $this->value->genCSS($output);
            $output->add(')');
        }
        public function compile($env)
        {
            return new \Less_Tree_Paren($this->value->compile($env));
        }
    }
}
