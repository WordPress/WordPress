<?php

namespace {
    /**
     * @private
     */
    class Less_Visitor_joinSelector extends \Less_Visitor
    {
        public $contexts = [[]];
        /**
         * @param Less_Tree_Ruleset $root
         */
        public function run($root)
        {
            return $this->visitObj($root);
        }
        public function visitRule($ruleNode, &$visitDeeper)
        {
            $visitDeeper = \false;
        }
        public function visitMixinDefinition($mixinDefinitionNode, &$visitDeeper)
        {
            $visitDeeper = \false;
        }
        public function visitRuleset($rulesetNode)
        {
            $context = \end($this->contexts);
            $paths = [];
            if (!$rulesetNode->root) {
                $selectors = $rulesetNode->selectors;
                if ($selectors !== null) {
                    $filtered = [];
                    foreach ($selectors as $selector) {
                        if ($selector->getIsOutput()) {
                            $filtered[] = $selector;
                        }
                    }
                    $selectors = $rulesetNode->selectors = $filtered ?: null;
                    if ($selectors) {
                        $paths = $rulesetNode->joinSelectors($context, $selectors);
                    }
                }
                if ($selectors === null) {
                    $rulesetNode->rules = null;
                }
                $rulesetNode->paths = $paths;
            }
            // NOTE: Assigned here instead of at the start like less.js,
            // because PHP arrays aren't by-ref
            $this->contexts[] = $paths;
        }
        public function visitRulesetOut()
        {
            \array_pop($this->contexts);
        }
        public function visitMedia($mediaNode)
        {
            $context = \end($this->contexts);
            if (\count($context) === 0 || \is_object($context[0]) && $context[0]->multiMedia) {
                $mediaNode->rules[0]->root = \true;
            }
        }
    }
}
