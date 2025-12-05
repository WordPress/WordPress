<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Value extends \Less_Tree
    {
        public $type = 'Value';
        public $value;
        /**
         * @param array<Less_Tree> $value
         */
        public function __construct($value)
        {
            $this->value = $value;
        }
        public function accept($visitor)
        {
            $this->value = $visitor->visitArray($this->value);
        }
        public function compile($env)
        {
            $ret = [];
            $i = 0;
            foreach ($this->value as $i => $v) {
                $ret[] = $v->compile($env);
            }
            if ($i > 0) {
                return new \Less_Tree_Value($ret);
            }
            return $ret[0];
        }
        /**
         * @see Less_Tree::genCSS
         */
        function genCSS($output)
        {
            $len = \count($this->value);
            for ($i = 0; $i < $len; $i++) {
                $this->value[$i]->genCSS($output);
                if ($i + 1 < $len) {
                    $output->add(\Less_Environment::$_outputMap[',']);
                }
            }
        }
    }
}
