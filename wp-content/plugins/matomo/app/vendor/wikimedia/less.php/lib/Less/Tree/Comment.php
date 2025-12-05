<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Comment extends \Less_Tree
    {
        public $value;
        public $silent;
        public $isReferenced;
        public $currentFileInfo;
        public $type = 'Comment';
        public function __construct($value, $silent, $index = null, $currentFileInfo = null)
        {
            $this->value = $value;
            $this->silent = (bool) $silent;
            $this->currentFileInfo = $currentFileInfo;
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            // if( $this->debugInfo ){
            //$output->add( tree.debugInfo($env, $this), $this->currentFileInfo, $this->index);
            //}
            $output->add(\trim($this->value));
            // TODO shouldn't need to trim, we shouldn't grab the \n
        }
        public function toCSS()
        {
            return \Less_Parser::$options['compress'] ? '' : $this->value;
        }
        public function isSilent()
        {
            $isReference = $this->currentFileInfo && isset($this->currentFileInfo['reference']) && (!isset($this->isReferenced) || !$this->isReferenced);
            $isCompressed = \Less_Parser::$options['compress'] && !\preg_match('/^\\/\\*!/', $this->value);
            return $this->silent || $isReference || $isCompressed;
        }
        public function markReferenced()
        {
            $this->isReferenced = \true;
        }
    }
}
