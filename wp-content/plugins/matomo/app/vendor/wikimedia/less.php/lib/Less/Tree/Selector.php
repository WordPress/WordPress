<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Selector extends \Less_Tree
    {
        public $elements;
        public $condition;
        public $extendList = [];
        public $_css;
        public $index;
        public $evaldCondition = \false;
        public $type = 'Selector';
        public $currentFileInfo = [];
        public $isReferenced;
        public $mediaEmpty;
        public $elements_len = 0;
        public $_oelements;
        public $_oelements_assoc;
        public $_oelements_len;
        public $cacheable = \true;
        /**
         * @param bool $isReferenced
         */
        public function __construct($elements, $extendList = [], $condition = null, $index = null, $currentFileInfo = null, $isReferenced = null)
        {
            $this->elements = $elements;
            $this->elements_len = \count($elements);
            $this->extendList = $extendList;
            $this->condition = $condition;
            if ($currentFileInfo) {
                $this->currentFileInfo = $currentFileInfo;
            }
            $this->isReferenced = $isReferenced;
            if (!$condition) {
                $this->evaldCondition = \true;
            }
            $this->CacheElements();
        }
        public function accept($visitor)
        {
            $this->elements = $visitor->visitArray($this->elements);
            $this->extendList = $visitor->visitArray($this->extendList);
            if ($this->condition) {
                $this->condition = $visitor->visitObj($this->condition);
            }
            if ($visitor instanceof \Less_Visitor_extendFinder) {
                $this->CacheElements();
            }
        }
        public function createDerived($elements, $extendList = null, $evaldCondition = null)
        {
            $newSelector = new \Less_Tree_Selector($elements, $extendList ?: $this->extendList, null, $this->index, $this->currentFileInfo, $this->isReferenced);
            $newSelector->evaldCondition = $evaldCondition ?: $this->evaldCondition;
            $newSelector->mediaEmpty = $this->mediaEmpty;
            return $newSelector;
        }
        public function match($other)
        {
            if (!$other->_oelements || $this->elements_len < $other->_oelements_len) {
                return 0;
            }
            for ($i = 0; $i < $other->_oelements_len; $i++) {
                if ($this->elements[$i]->value !== $other->_oelements[$i]) {
                    return 0;
                }
            }
            return $other->_oelements_len;
            // return number of matched elements
        }
        public function CacheElements()
        {
            $this->_oelements = [];
            $this->_oelements_assoc = [];
            $css = '';
            foreach ($this->elements as $v) {
                $css .= $v->combinator;
                if (!$v->value_is_object) {
                    $css .= $v->value;
                    continue;
                }
                if (!\property_exists($v->value, 'value') || !\is_string($v->value->value)) {
                    $this->cacheable = \false;
                    return;
                }
                $css .= $v->value->value;
            }
            $this->_oelements_len = \preg_match_all('/[,&#\\.\\w-](?:[\\w-]|(?:\\\\.))*/', $css, $matches);
            if ($this->_oelements_len) {
                $this->_oelements = $matches[0];
                if ($this->_oelements[0] === '&') {
                    \array_shift($this->_oelements);
                    $this->_oelements_len--;
                }
                $this->_oelements_assoc = \array_fill_keys($this->_oelements, \true);
            }
        }
        public function isJustParentSelector()
        {
            return !$this->mediaEmpty && \count($this->elements) === 1 && $this->elements[0]->value === '&' && ($this->elements[0]->combinator === ' ' || $this->elements[0]->combinator === '');
        }
        public function compile($env)
        {
            $elements = [];
            foreach ($this->elements as $el) {
                $elements[] = $el->compile($env);
            }
            $extendList = [];
            foreach ($this->extendList as $el) {
                $extendList[] = $el->compile($el);
            }
            $evaldCondition = \false;
            if ($this->condition) {
                $evaldCondition = $this->condition->compile($env);
            }
            return $this->createDerived($elements, $extendList, $evaldCondition);
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output, $firstSelector = \true)
        {
            if (!$firstSelector && $this->elements[0]->combinator === "") {
                $output->add(' ', $this->currentFileInfo, $this->index);
            }
            foreach ($this->elements as $element) {
                $element->genCSS($output);
            }
        }
        public function markReferenced()
        {
            $this->isReferenced = \true;
        }
        public function getIsReferenced()
        {
            return !isset($this->currentFileInfo['reference']) || !$this->currentFileInfo['reference'] || $this->isReferenced;
        }
        public function getIsOutput()
        {
            return $this->evaldCondition;
        }
    }
}
