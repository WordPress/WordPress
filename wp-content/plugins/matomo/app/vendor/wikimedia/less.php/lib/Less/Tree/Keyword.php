<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Keyword extends \Less_Tree
    {
        public $value;
        public $type = 'Keyword';
        /**
         * @param string $value
         */
        public function __construct($value)
        {
            $this->value = $value;
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            if ($this->value === '%') {
                throw new \Less_Exception_Compiler("Invalid % without number");
            }
            $output->add($this->value);
        }
        public function compare($other)
        {
            if ($other instanceof \Less_Tree_Keyword) {
                return $other->value === $this->value ? 0 : 1;
            } else {
                return -1;
            }
        }
    }
}
