<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Ruleset extends \Less_Tree
    {
        protected $lookups;
        public $_variables;
        public $_rulesets;
        public $strictImports;
        public $selectors;
        public $rules;
        public $root;
        public $allowImports;
        public $paths;
        public $firstRoot;
        public $type = 'Ruleset';
        public $multiMedia;
        public $allExtends;
        public $ruleset_id;
        public $originalRuleset;
        public $first_oelements;
        public function SetRulesetIndex()
        {
            $this->ruleset_id = \Less_Parser::$next_id++;
            $this->originalRuleset = $this->ruleset_id;
            if ($this->selectors) {
                foreach ($this->selectors as $sel) {
                    if ($sel->_oelements) {
                        $this->first_oelements[$sel->_oelements[0]] = \true;
                    }
                }
            }
        }
        /**
         * @param null|Less_Tree_Selector[] $selectors
         * @param Less_Tree[] $rules
         * @param null|bool $strictImports
         */
        public function __construct($selectors, $rules, $strictImports = null)
        {
            $this->selectors = $selectors;
            $this->rules = $rules;
            $this->lookups = [];
            $this->strictImports = $strictImports;
            $this->SetRulesetIndex();
        }
        public function accept($visitor)
        {
            if ($this->paths !== null) {
                $paths_len = \count($this->paths);
                for ($i = 0; $i < $paths_len; $i++) {
                    $this->paths[$i] = $visitor->visitArray($this->paths[$i]);
                }
            } elseif ($this->selectors) {
                $this->selectors = $visitor->visitArray($this->selectors);
            }
            if ($this->rules) {
                $this->rules = $visitor->visitArray($this->rules);
            }
        }
        /**
         * @param Less_Environment $env
         * @return Less_Tree_Ruleset
         * @see less-2.5.3.js#Ruleset.prototype.eval
         */
        public function compile($env)
        {
            $ruleset = $this->PrepareRuleset($env);
            // Store the frames around mixin definitions,
            // so they can be evaluated like closures when the time comes.
            $rsRuleCnt = \count($ruleset->rules);
            for ($i = 0; $i < $rsRuleCnt; $i++) {
                // These checks are the equivalent of the rule.evalFirst property in less.js
                if ($ruleset->rules[$i] instanceof \Less_Tree_Mixin_Definition || $ruleset->rules[$i] instanceof \Less_Tree_DetachedRuleset) {
                    $ruleset->rules[$i] = $ruleset->rules[$i]->compile($env);
                }
            }
            $mediaBlockCount = \count($env->mediaBlocks);
            // Evaluate mixin calls.
            $this->EvalMixinCalls($ruleset, $env, $rsRuleCnt);
            // Evaluate everything else
            for ($i = 0; $i < $rsRuleCnt; $i++) {
                if (!($ruleset->rules[$i] instanceof \Less_Tree_Mixin_Definition || $ruleset->rules[$i] instanceof \Less_Tree_DetachedRuleset)) {
                    $ruleset->rules[$i] = $ruleset->rules[$i]->compile($env);
                }
            }
            // Evaluate everything else
            for ($i = 0; $i < $rsRuleCnt; $i++) {
                $rule = $ruleset->rules[$i];
                // for rulesets, check if it is a css guard and can be removed
                if ($rule instanceof \Less_Tree_Ruleset && $rule->selectors && \count($rule->selectors) === 1) {
                    // check if it can be folded in (e.g. & where)
                    if ($rule->selectors[0]->isJustParentSelector()) {
                        \array_splice($ruleset->rules, $i--, 1);
                        $rsRuleCnt--;
                        for ($j = 0; $j < \count($rule->rules); $j++) {
                            $subRule = $rule->rules[$j];
                            if (!$subRule instanceof \Less_Tree_Rule || !$subRule->variable) {
                                \array_splice($ruleset->rules, ++$i, 0, [$subRule]);
                                $rsRuleCnt++;
                            }
                        }
                    }
                }
            }
            // Pop the stack
            $env->shiftFrame();
            if ($mediaBlockCount) {
                $len = \count($env->mediaBlocks);
                for ($i = $mediaBlockCount; $i < $len; $i++) {
                    $env->mediaBlocks[$i]->bubbleSelectors($ruleset->selectors);
                }
            }
            return $ruleset;
        }
        /**
         * Compile Less_Tree_Mixin_Call objects
         *
         * @param Less_Tree_Ruleset $ruleset
         * @param int $rsRuleCnt
         */
        private function EvalMixinCalls($ruleset, $env, &$rsRuleCnt)
        {
            for ($i = 0; $i < $rsRuleCnt; $i++) {
                $rule = $ruleset->rules[$i];
                if ($rule instanceof \Less_Tree_Mixin_Call) {
                    $rule = $rule->compile($env);
                    $temp = [];
                    foreach ($rule as $r) {
                        if ($r instanceof \Less_Tree_Rule && $r->variable) {
                            // do not pollute the scope if the variable is
                            // already there. consider returning false here
                            // but we need a way to "return" variable from mixins
                            if (!$ruleset->variable($r->name)) {
                                $temp[] = $r;
                            }
                        } else {
                            $temp[] = $r;
                        }
                    }
                    $temp_count = \count($temp) - 1;
                    \array_splice($ruleset->rules, $i, 1, $temp);
                    $rsRuleCnt += $temp_count;
                    $i += $temp_count;
                    $ruleset->resetCache();
                } elseif ($rule instanceof \Less_Tree_RulesetCall) {
                    $rule = $rule->compile($env);
                    $rules = [];
                    foreach ($rule->rules as $r) {
                        if ($r instanceof \Less_Tree_Rule && $r->variable) {
                            continue;
                        }
                        $rules[] = $r;
                    }
                    \array_splice($ruleset->rules, $i, 1, $rules);
                    $temp_count = \count($rules);
                    $rsRuleCnt += $temp_count - 1;
                    $i += $temp_count - 1;
                    $ruleset->resetCache();
                }
            }
        }
        /**
         * Compile the selectors and create a new ruleset object for the compile() method
         *
         * @param Less_Environment $env
         * @return Less_Tree_Ruleset
         */
        private function PrepareRuleset($env)
        {
            // NOTE: Preserve distinction between null and empty array when compiling
            // $this->selectors to $selectors
            $thisSelectors = $this->selectors;
            $selectors = null;
            $hasOnePassingSelector = \false;
            if ($thisSelectors) {
                \Less_Tree_DefaultFunc::error("it is currently only allowed in parametric mixin guards,");
                $selectors = [];
                foreach ($thisSelectors as $s) {
                    $selector = $s->compile($env);
                    $selectors[] = $selector;
                    if ($selector->evaldCondition) {
                        $hasOnePassingSelector = \true;
                    }
                }
                \Less_Tree_DefaultFunc::reset();
            } else {
                $hasOnePassingSelector = \true;
            }
            if ($this->rules && $hasOnePassingSelector) {
                // Copy the array (no need for slice in PHP)
                $rules = $this->rules;
            } else {
                $rules = [];
            }
            $ruleset = new \Less_Tree_Ruleset($selectors, $rules, $this->strictImports);
            $ruleset->originalRuleset = $this->ruleset_id;
            $ruleset->root = $this->root;
            $ruleset->firstRoot = $this->firstRoot;
            $ruleset->allowImports = $this->allowImports;
            // push the current ruleset to the frames stack
            $env->unshiftFrame($ruleset);
            // Evaluate imports
            if ($ruleset->root || $ruleset->allowImports || !$ruleset->strictImports) {
                $ruleset->evalImports($env);
            }
            return $ruleset;
        }
        function evalImports($env)
        {
            $rules_len = \count($this->rules);
            for ($i = 0; $i < $rules_len; $i++) {
                $rule = $this->rules[$i];
                if ($rule instanceof \Less_Tree_Import) {
                    $rules = $rule->compile($env);
                    if (\is_array($rules)) {
                        \array_splice($this->rules, $i, 1, $rules);
                        $temp_count = \count($rules) - 1;
                        $i += $temp_count;
                        $rules_len += $temp_count;
                    } else {
                        \array_splice($this->rules, $i, 1, [$rules]);
                    }
                    $this->resetCache();
                }
            }
        }
        function makeImportant()
        {
            $important_rules = [];
            foreach ($this->rules as $rule) {
                if ($rule instanceof \Less_Tree_Rule || $rule instanceof \Less_Tree_Ruleset || $rule instanceof \Less_Tree_NameValue) {
                    $important_rules[] = $rule->makeImportant();
                } else {
                    $important_rules[] = $rule;
                }
            }
            return new \Less_Tree_Ruleset($this->selectors, $important_rules, $this->strictImports);
        }
        public function matchArgs($args, $env = null)
        {
            return !$args;
        }
        // lets you call a css selector with a guard
        public function matchCondition($args, $env)
        {
            $lastSelector = \end($this->selectors);
            if (!$lastSelector->evaldCondition) {
                return \false;
            }
            if ($lastSelector->condition && !$lastSelector->condition->compile($env->copyEvalEnv($env->frames))) {
                return \false;
            }
            return \true;
        }
        function resetCache()
        {
            $this->_rulesets = null;
            $this->_variables = null;
            $this->lookups = [];
        }
        public function variables()
        {
            $this->_variables = [];
            foreach ($this->rules as $r) {
                if ($r instanceof \Less_Tree_Rule && $r->variable === \true) {
                    $this->_variables[$r->name] = $r;
                }
            }
        }
        /**
         * @param string $name
         * @return Less_Tree_Rule|null
         */
        public function variable($name)
        {
            if ($this->_variables === null) {
                $this->variables();
            }
            return $this->_variables[$name] ?? null;
        }
        public function find($selector, $self = null)
        {
            $key = \implode(' ', $selector->_oelements);
            if (!isset($this->lookups[$key])) {
                if (!$self) {
                    $self = $this->ruleset_id;
                }
                $this->lookups[$key] = [];
                $first_oelement = $selector->_oelements[0];
                foreach ($this->rules as $rule) {
                    if ($rule instanceof \Less_Tree_Ruleset && $rule->ruleset_id != $self) {
                        if (isset($rule->first_oelements[$first_oelement])) {
                            foreach ($rule->selectors as $ruleSelector) {
                                $match = $selector->match($ruleSelector);
                                if ($match) {
                                    if ($selector->elements_len > $match) {
                                        $this->lookups[$key] = \array_merge($this->lookups[$key], $rule->find(new \Less_Tree_Selector(\array_slice($selector->elements, $match)), $self));
                                    } else {
                                        $this->lookups[$key][] = $rule;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            return $this->lookups[$key];
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            if (!$this->root) {
                \Less_Environment::$tabLevel++;
            }
            $tabRuleStr = $tabSetStr = '';
            if (!\Less_Parser::$options['compress']) {
                if (\Less_Environment::$tabLevel) {
                    $tabRuleStr = "\n" . \str_repeat(\Less_Parser::$options['indentation'], \Less_Environment::$tabLevel);
                    $tabSetStr = "\n" . \str_repeat(\Less_Parser::$options['indentation'], \Less_Environment::$tabLevel - 1);
                } else {
                    $tabSetStr = $tabRuleStr = "\n";
                }
            }
            $ruleNodes = [];
            $rulesetNodes = [];
            foreach ($this->rules as $rule) {
                $class = \get_class($rule);
                if ($class === 'Less_Tree_Media' || $class === 'Less_Tree_Directive' || $this->root && $class === 'Less_Tree_Comment' || $rule instanceof \Less_Tree_Ruleset && $rule->rules) {
                    $rulesetNodes[] = $rule;
                } else {
                    $ruleNodes[] = $rule;
                }
            }
            // If this is the root node, we don't render
            // a selector, or {}.
            if (!$this->root) {
                $paths_len = \count($this->paths);
                for ($i = 0; $i < $paths_len; $i++) {
                    $path = $this->paths[$i];
                    $firstSelector = \true;
                    foreach ($path as $p) {
                        $p->genCSS($output, $firstSelector);
                        $firstSelector = \false;
                    }
                    if ($i + 1 < $paths_len) {
                        $output->add(',' . $tabSetStr);
                    }
                }
                $output->add((\Less_Parser::$options['compress'] ? '{' : " {") . $tabRuleStr);
            }
            // Compile rules and rulesets
            $ruleNodes_len = \count($ruleNodes);
            $rulesetNodes_len = \count($rulesetNodes);
            for ($i = 0; $i < $ruleNodes_len; $i++) {
                $rule = $ruleNodes[$i];
                // @page{ directive ends up with root elements inside it, a mix of rules and rulesets
                // In this instance we do not know whether it is the last property
                if ($i + 1 === $ruleNodes_len && (!$this->root || $rulesetNodes_len === 0 || $this->firstRoot)) {
                    \Less_Environment::$lastRule = \true;
                }
                $rule->genCSS($output);
                if (!\Less_Environment::$lastRule) {
                    $output->add($tabRuleStr);
                } else {
                    \Less_Environment::$lastRule = \false;
                }
            }
            if (!$this->root) {
                $output->add($tabSetStr . '}');
                \Less_Environment::$tabLevel--;
            }
            $firstRuleset = \true;
            $space = $this->root ? $tabRuleStr : $tabSetStr;
            for ($i = 0; $i < $rulesetNodes_len; $i++) {
                if ($ruleNodes_len && $firstRuleset) {
                    $output->add($space);
                } elseif (!$firstRuleset) {
                    $output->add($space);
                }
                $firstRuleset = \false;
                $rulesetNodes[$i]->genCSS($output);
            }
            if (!\Less_Parser::$options['compress'] && $this->firstRoot) {
                $output->add("\n");
            }
        }
        function markReferenced()
        {
            if (!$this->selectors) {
                return;
            }
            foreach ($this->selectors as $selector) {
                $selector->markReferenced();
            }
        }
        /**
         * @param Less_Tree_Selector[][] $context
         * @param Less_Tree_Selector[]|null $selectors
         * @return Less_Tree_Selector[][]
         */
        public function joinSelectors($context, $selectors)
        {
            $paths = [];
            if ($selectors !== null) {
                foreach ($selectors as $selector) {
                    $this->joinSelector($paths, $context, $selector);
                }
            }
            return $paths;
        }
        public function joinSelector(array &$paths, array $context, \Less_Tree_Selector $selector)
        {
            $newPaths = [];
            $hadParentSelector = $this->replaceParentSelector($newPaths, $context, $selector);
            if (!$hadParentSelector) {
                if ($context) {
                    $newPaths = [];
                    foreach ($context as $path) {
                        $newPaths[] = \array_merge($path, [$selector]);
                    }
                } else {
                    $newPaths = [[$selector]];
                }
            }
            foreach ($newPaths as $newPath) {
                $paths[] = $newPath;
            }
        }
        /**
         * Replace all parent selectors inside $inSelector with $context.
         *
         * @param array &$paths Resulting selectors are appended to $paths.
         * @param mixed $context
         * @param Less_Tree_Selector $inSelector Inner selector from Less_Tree_Paren
         * @return bool True if $inSelector contained at least one parent selector
         */
        private function replaceParentSelector(array &$paths, $context, \Less_Tree_Selector $inSelector)
        {
            $hadParentSelector = \false;
            // The paths are [[Selector]]
            // The first list is a list of comma separated selectors
            // The inner list is a list of inheritance separated selectors
            // e.g.
            // .a, .b {
            //   .c {
            //   }
            // }
            // == [[.a] [.c]] [[.b] [.c]]
            //
            // the elements from the current selector so far
            $currentElements = [];
            // the current list of new selectors to add to the path.
            // We will build it up. We initiate it with one empty selector as we "multiply" the new selectors
            // by the parents
            $newSelectors = [[]];
            foreach ($inSelector->elements as $el) {
                // non-parent reference elements just get added
                if ($el->value !== '&') {
                    $nestedSelector = $this->findNestedSelector($el);
                    if ($nestedSelector !== null) {
                        $this->mergeElementsOnToSelectors($currentElements, $newSelectors);
                        $nestedPaths = [];
                        $replacedNewSelectors = [];
                        $replaced = $this->replaceParentSelector($nestedPaths, $context, $nestedSelector);
                        $hadParentSelector = $hadParentSelector || $replaced;
                        // $nestedPaths is populated by replaceParentSelector()
                        // $nestedPaths should have exactly one TODO, replaceParentSelector does not multiply selectors
                        foreach ($nestedPaths as $nestedPath) {
                            $replacementSelector = $this->createSelector($nestedPath, $el);
                            // join selector path from $newSelectors with every selector path in $addPaths array.
                            // $el contains the element that is being replaced by $addPaths
                            //
                            // @see less-2.5.3.js#Ruleset-addAllReplacementsIntoPath
                            $addPaths = [$replacementSelector];
                            foreach ($newSelectors as $newSelector) {
                                $replacedNewSelectors[] = $this->addReplacementIntoPath($newSelector, $addPaths, $el, $inSelector);
                            }
                        }
                        $newSelectors = $replacedNewSelectors;
                        $currentElements = [];
                    } else {
                        $currentElements[] = $el;
                    }
                } else {
                    $hadParentSelector = \true;
                    // the new list of selectors to add
                    $selectorsMultiplied = [];
                    // merge the current list of non parent selector elements
                    // on to the current list of selectors to add
                    $this->mergeElementsOnToSelectors($currentElements, $newSelectors);
                    foreach ($newSelectors as $sel) {
                        // if we don't have any parent paths, the & might be in a mixin so that it can be used
                        // whether there are parents or not
                        if (!$context) {
                            // the combinator used on el should now be applied to the next element instead so that
                            // it is not lost
                            if ($sel) {
                                $sel[0]->elements[] = new \Less_Tree_Element($el->combinator, '', $el->index, $el->currentFileInfo);
                            }
                            $selectorsMultiplied[] = $sel;
                        } else {
                            // and the parent selectors
                            foreach ($context as $parentSel) {
                                // We need to put the current selectors
                                // then join the last selector's elements on to the parents selectors
                                $newSelectorPath = $this->addReplacementIntoPath($sel, $parentSel, $el, $inSelector);
                                // add that to our new set of selectors
                                $selectorsMultiplied[] = $newSelectorPath;
                            }
                        }
                    }
                    // our new selectors has been multiplied, so reset the state
                    $newSelectors = $selectorsMultiplied;
                    $currentElements = [];
                }
            }
            // if we have any elements left over (e.g. .a& .b == .b)
            // add them on to all the current selectors
            $this->mergeElementsOnToSelectors($currentElements, $newSelectors);
            foreach ($newSelectors as &$sel) {
                $length = \count($sel);
                if ($length) {
                    $paths[] = $sel;
                    $lastSelector = $sel[$length - 1];
                    $sel[$length - 1] = $lastSelector->createDerived($lastSelector->elements, $inSelector->extendList);
                }
            }
            return $hadParentSelector;
        }
        /**
         * @param array $elementsToPak
         * @param Less_Tree_Element $originalElement
         * @return Less_Tree_Selector
         */
        private function createSelector(array $elementsToPak, $originalElement)
        {
            if (!$elementsToPak) {
                // This is an invalid call. Kept to match less.js. Appears unreachable.
                // @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal
                $containedElement = new \Less_Tree_Paren(null);
            } else {
                $insideParent = [];
                foreach ($elementsToPak as $elToPak) {
                    $insideParent[] = new \Less_Tree_Element(null, $elToPak, $originalElement->index, $originalElement->currentFileInfo);
                }
                $containedElement = new \Less_Tree_Paren(new \Less_Tree_Selector($insideParent));
            }
            $element = new \Less_Tree_Element(null, $containedElement, $originalElement->index, $originalElement->currentFileInfo);
            return new \Less_Tree_Selector([$element]);
        }
        /**
         * @param Less_Tree_Element $element
         * @return Less_Tree_Selector|null
         */
        private function findNestedSelector($element)
        {
            $maybeParen = $element->value;
            if (!$maybeParen instanceof \Less_Tree_Paren) {
                return null;
            }
            $maybeSelector = $maybeParen->value;
            if (!$maybeSelector instanceof \Less_Tree_Selector) {
                return null;
            }
            return $maybeSelector;
        }
        /**
         * joins selector path from $beginningPath with selector path in $addPath.
         *
         * $replacedElement contains the element that is being replaced by $addPath
         *
         * @param Less_Tree_Selector[] $beginningPath
         * @param Less_Tree_Selector[] $addPath
         * @param Less_Tree_Element $replacedElement
         * @param Less_Tree_Selector $originalSelector
         * @return Less_Tree_Selector[] Concatenated path
         * @see less-2.5.3.js#Ruleset-addReplacementIntoPath
         */
        private function addReplacementIntoPath(array $beginningPath, array $addPath, $replacedElement, $originalSelector)
        {
            // our new selector path
            $newSelectorPath = [];
            // construct the joined selector - if `&` is the first thing this will be empty,
            // if not newJoinedSelector will be the last set of elements in the selector
            if ($beginningPath) {
                // NOTE: less.js uses Array slice() to copy. In PHP, arrays are naturally copied by value.
                $newSelectorPath = $beginningPath;
                $lastSelector = \array_pop($newSelectorPath);
                $newJoinedSelector = $originalSelector->createDerived($lastSelector->elements);
            } else {
                $newJoinedSelector = $originalSelector->createDerived([]);
            }
            if ($addPath) {
                // if the & does not have a combinator that is "" or " " then
                // and there is a combinator on the parent, then grab that.
                // this also allows `+ a { & .b { .a & { ...`
                $combinator = $replacedElement->combinator;
                $parentEl = $addPath[0]->elements[0];
                if ($replacedElement->combinatorIsEmptyOrWhitespace && !$parentEl->combinatorIsEmptyOrWhitespace) {
                    $combinator = $parentEl->combinator;
                }
                // join the elements so far with the first part of the parent
                $newJoinedSelector->elements[] = new \Less_Tree_Element($combinator, $parentEl->value, $replacedElement->index, $replacedElement->currentFileInfo);
                $newJoinedSelector->elements = \array_merge($newJoinedSelector->elements, \array_slice($addPath[0]->elements, 1));
            }
            // now add the joined selector - but only if it is not empty
            if ($newJoinedSelector->elements) {
                $newSelectorPath[] = $newJoinedSelector;
            }
            // put together the parent selectors after the join (e.g. the rest of the parent)
            if (\count($addPath) > 1) {
                $newSelectorPath = \array_merge($newSelectorPath, \array_slice($addPath, 1));
            }
            return $newSelectorPath;
        }
        function mergeElementsOnToSelectors($elements, &$selectors)
        {
            if (!$elements) {
                return;
            }
            if (!$selectors) {
                $selectors[] = [new \Less_Tree_Selector($elements)];
                return;
            }
            foreach ($selectors as &$sel) {
                // if the previous thing in sel is a parent this needs to join on to it
                if ($sel) {
                    $last = \count($sel) - 1;
                    $sel[$last] = $sel[$last]->createDerived(\array_merge($sel[$last]->elements, $elements));
                } else {
                    $sel[] = new \Less_Tree_Selector($elements);
                }
            }
        }
    }
}
