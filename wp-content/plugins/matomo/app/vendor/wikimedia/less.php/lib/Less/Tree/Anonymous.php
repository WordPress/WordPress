<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Anonymous extends \Less_Tree
    {
        public $value;
        public $quote;
        public $index;
        public $mapLines;
        public $currentFileInfo;
        public $type = 'Anonymous';
        /**
         * @param int $index
         * @param bool|null $mapLines
         */
        public function __construct($value, $index = null, $currentFileInfo = null, $mapLines = null)
        {
            $this->value = $value;
            $this->index = $index;
            $this->mapLines = $mapLines;
            $this->currentFileInfo = $currentFileInfo;
        }
        public function compile($env)
        {
            return new \Less_Tree_Anonymous($this->value, $this->index, $this->currentFileInfo, $this->mapLines);
        }
        public function compare($x)
        {
            if (!\is_object($x)) {
                return -1;
            }
            $left = $this->toCSS();
            $right = $x->toCSS();
            if ($left === $right) {
                return 0;
            }
            return $left < $right ? -1 : 1;
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add($this->value, $this->currentFileInfo, $this->index, $this->mapLines);
        }
        public function toCSS()
        {
            return $this->value;
        }
    }
}
