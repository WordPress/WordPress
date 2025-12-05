<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Expression extends \Less_Tree
    {
        /** @var array */
        public $value = [];
        public $parens = \false;
        public $type = 'Expression';
        public function __construct($value, $parens = null)
        {
            $this->value = $value;
            $this->parens = $parens;
        }
        public function accept($visitor)
        {
            $this->value = $visitor->visitArray($this->value);
        }
        public function compile($env)
        {
            $doubleParen = \false;
            if ($this->parens && !$this->parensInOp) {
                \Less_Environment::$parensStack++;
            }
            $returnValue = null;
            if ($this->value) {
                $count = \count($this->value);
                if ($count > 1) {
                    $ret = [];
                    foreach ($this->value as $e) {
                        $ret[] = $e->compile($env);
                    }
                    $returnValue = new \Less_Tree_Expression($ret);
                } else {
                    if ($this->value[0] instanceof \Less_Tree_Expression && $this->value[0]->parens && !$this->value[0]->parensInOp) {
                        $doubleParen = \true;
                    }
                    $returnValue = $this->value[0]->compile($env);
                }
            } else {
                $returnValue = $this;
            }
            if ($this->parens) {
                if (!$this->parensInOp) {
                    \Less_Environment::$parensStack--;
                } elseif (!\Less_Environment::isMathOn() && !$doubleParen) {
                    $returnValue = new \Less_Tree_Paren($returnValue);
                }
            }
            return $returnValue;
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $val_len = \count($this->value);
            for ($i = 0; $i < $val_len; $i++) {
                $this->value[$i]->genCSS($output);
                if ($i + 1 < $val_len) {
                    $output->add(' ');
                }
            }
        }
        public function throwAwayComments()
        {
            if (\is_array($this->value)) {
                $new_value = [];
                foreach ($this->value as $v) {
                    if ($v instanceof \Less_Tree_Comment) {
                        continue;
                    }
                    $new_value[] = $v;
                }
                $this->value = $new_value;
            }
        }
    }
}
