<?php

namespace {
    /**
     * @private
     */
    class Less_Visitor_processExtends extends \Less_Visitor
    {
        public $allExtendsStack;
        /**
         * @param Less_Tree_Ruleset $root
         */
        public function run($root)
        {
            $extendFinder = new \Less_Visitor_extendFinder();
            $extendFinder->run($root);
            if (!$extendFinder->foundExtends) {
                return $root;
            }
            $root->allExtends = $this->doExtendChaining($root->allExtends, $root->allExtends);
            $this->allExtendsStack = [];
            $this->allExtendsStack[] =& $root->allExtends;
            return $this->visitObj($root);
        }
        private function doExtendChaining($extendsList, $extendsListTarget, $iterationCount = 0)
        {
            //
            // chaining is different from normal extension.. if we extend an extend then we are not just copying, altering and pasting
            // the selector we would do normally, but we are also adding an extend with the same target selector
            // this means this new extend can then go and alter other extends
            //
            // this method deals with all the chaining work - without it, extend is flat and doesn't work on other extend selectors
            // this is also the most expensive.. and a match on one selector can cause an extension of a selector we had already processed if
            // we look at each selector at a time, as is done in visitRuleset
            $extendsToAdd = [];
            // loop through comparing every extend with every target extend.
            // a target extend is the one on the ruleset we are looking at copy/edit/pasting in place
            // e.g. .a:extend(.b) {} and .b:extend(.c) {} then the first extend extends the second one
            // and the second is the target.
            // the separation into two lists allows us to process a subset of chains with a bigger set, as is the
            // case when processing media queries
            for ($extendIndex = 0, $extendsList_len = \count($extendsList); $extendIndex < $extendsList_len; $extendIndex++) {
                for ($targetExtendIndex = 0; $targetExtendIndex < \count($extendsListTarget); $targetExtendIndex++) {
                    $extend = $extendsList[$extendIndex];
                    $targetExtend = $extendsListTarget[$targetExtendIndex];
                    // Optimisation: Explicit reference, <https://github.com/wikimedia/less.php/pull/14>
                    if (\array_key_exists($targetExtend->object_id, $extend->parent_ids)) {
                        // ignore circular references
                        continue;
                    }
                    // find a match in the target extends self selector (the bit before :extend)
                    $selectorPath = [$targetExtend->selfSelectors[0]];
                    $matches = $this->findMatch($extend, $selectorPath);
                    if ($matches) {
                        // we found a match, so for each self selector..
                        foreach ($extend->selfSelectors as $selfSelector) {
                            // process the extend as usual
                            $newSelector = $this->extendSelector($matches, $selectorPath, $selfSelector);
                            // but now we create a new extend from it
                            $newExtend = new \Less_Tree_Extend($targetExtend->selector, $targetExtend->option, 0);
                            $newExtend->selfSelectors = $newSelector;
                            // add the extend onto the list of extends for that selector
                            \end($newSelector)->extendList = [$newExtend];
                            // $newSelector[ count($newSelector)-1]->extendList = array($newExtend);
                            // record that we need to add it.
                            $extendsToAdd[] = $newExtend;
                            $newExtend->ruleset = $targetExtend->ruleset;
                            // remember its parents for circular references
                            $newExtend->parent_ids = \array_merge($newExtend->parent_ids, $targetExtend->parent_ids, $extend->parent_ids);
                            // only process the selector once.. if we have :extend(.a,.b) then multiple
                            // extends will look at the same selector path, so when extending
                            // we know that any others will be duplicates in terms of what is added to the css
                            if ($targetExtend->firstExtendOnThisSelectorPath) {
                                $newExtend->firstExtendOnThisSelectorPath = \true;
                                $targetExtend->ruleset->paths[] = $newSelector;
                            }
                        }
                    }
                }
            }
            if ($extendsToAdd) {
                // try to detect circular references to stop a stack overflow.
                // may no longer be needed.			$this->extendChainCount++;
                if ($iterationCount > 100) {
                    try {
                        $selectorOne = $extendsToAdd[0]->selfSelectors[0]->toCSS();
                        $selectorTwo = $extendsToAdd[0]->selector->toCSS();
                    } catch (\Exception $e) {
                        $selectorOne = "{unable to calculate}";
                        $selectorTwo = "{unable to calculate}";
                    }
                    throw new \Less_Exception_Parser("extend circular reference detected. One of the circular extends is currently:" . $selectorOne . ":extend(" . $selectorTwo . ")");
                }
                // now process the new extends on the existing rules so that we can handle a extending b extending c ectending d extending e...
                $extendsToAdd = $this->doExtendChaining($extendsToAdd, $extendsListTarget, $iterationCount + 1);
            }
            return \array_merge($extendsList, $extendsToAdd);
        }
        protected function visitRule($ruleNode, &$visitDeeper)
        {
            $visitDeeper = \false;
        }
        protected function visitMixinDefinition($mixinDefinitionNode, &$visitDeeper)
        {
            $visitDeeper = \false;
        }
        protected function visitSelector($selectorNode, &$visitDeeper)
        {
            $visitDeeper = \false;
        }
        protected function visitRuleset($rulesetNode)
        {
            if ($rulesetNode->root) {
                return;
            }
            $allExtends = \end($this->allExtendsStack);
            $paths_len = \count($rulesetNode->paths);
            // look at each selector path in the ruleset, find any extend matches and then copy, find and replace
            foreach ($allExtends as $allExtend) {
                for ($pathIndex = 0; $pathIndex < $paths_len; $pathIndex++) {
                    // extending extends happens initially, before the main pass
                    if (isset($rulesetNode->extendOnEveryPath) && $rulesetNode->extendOnEveryPath) {
                        continue;
                    }
                    $selectorPath = $rulesetNode->paths[$pathIndex];
                    if (\end($selectorPath)->extendList) {
                        continue;
                    }
                    $this->ExtendMatch($rulesetNode, $allExtend, $selectorPath);
                }
            }
        }
        private function ExtendMatch($rulesetNode, $extend, $selectorPath)
        {
            $matches = $this->findMatch($extend, $selectorPath);
            if ($matches) {
                foreach ($extend->selfSelectors as $selfSelector) {
                    $rulesetNode->paths[] = $this->extendSelector($matches, $selectorPath, $selfSelector);
                }
            }
        }
        /**
         * @param Less_Tree_Extend $extend
         * @param Less_Tree_Selector[] $haystackSelectorPath
         * @return false|array<array{index:int,initialCombinator:string}>
         */
        private function findMatch($extend, $haystackSelectorPath)
        {
            if (!$this->HasMatches($extend, $haystackSelectorPath)) {
                return \false;
            }
            //
            // look through the haystack selector path to try and find the needle - extend.selector
            // returns an array of selector matches that can then be replaced
            //
            $needleElements = $extend->selector->elements;
            $potentialMatches = [];
            $potentialMatches_len = 0;
            $potentialMatch = null;
            $matches = [];
            // loop through the haystack elements
            $haystack_path_len = \count($haystackSelectorPath);
            for ($haystackSelectorIndex = 0; $haystackSelectorIndex < $haystack_path_len; $haystackSelectorIndex++) {
                $hackstackSelector = $haystackSelectorPath[$haystackSelectorIndex];
                $haystack_elements_len = \count($hackstackSelector->elements);
                for ($hackstackElementIndex = 0; $hackstackElementIndex < $haystack_elements_len; $hackstackElementIndex++) {
                    $haystackElement = $hackstackSelector->elements[$hackstackElementIndex];
                    // if we allow elements before our match we can add a potential match every time. otherwise only at the first element.
                    if ($extend->allowBefore || $haystackSelectorIndex === 0 && $hackstackElementIndex === 0) {
                        $potentialMatches[] = ['pathIndex' => $haystackSelectorIndex, 'index' => $hackstackElementIndex, 'matched' => 0, 'initialCombinator' => $haystackElement->combinator];
                        $potentialMatches_len++;
                    }
                    for ($i = 0; $i < $potentialMatches_len; $i++) {
                        $potentialMatch =& $potentialMatches[$i];
                        $potentialMatch = $this->PotentialMatch($potentialMatch, $needleElements, $haystackElement, $hackstackElementIndex);
                        // if we are still valid and have finished, test whether we have elements after and whether these are allowed
                        if ($potentialMatch && $potentialMatch['matched'] === $extend->selector->elements_len) {
                            $potentialMatch['finished'] = \true;
                            if (!$extend->allowAfter && ($hackstackElementIndex + 1 < $haystack_elements_len || $haystackSelectorIndex + 1 < $haystack_path_len)) {
                                $potentialMatch = null;
                            }
                        }
                        // if null we remove, if not, we are still valid, so either push as a valid match or continue
                        if ($potentialMatch) {
                            if ($potentialMatch['finished']) {
                                $potentialMatch['length'] = $extend->selector->elements_len;
                                $potentialMatch['endPathIndex'] = $haystackSelectorIndex;
                                $potentialMatch['endPathElementIndex'] = $hackstackElementIndex + 1;
                                // index after end of match
                                $potentialMatches = [];
                                // we don't allow matches to overlap, so start matching again
                                $potentialMatches_len = 0;
                                $matches[] = $potentialMatch;
                            }
                            continue;
                        }
                        \array_splice($potentialMatches, $i, 1);
                        $potentialMatches_len--;
                        $i--;
                    }
                }
            }
            return $matches;
        }
        // Before going through all the nested loops, lets check to see if a match is possible
        // Reduces Bootstrap 3.1 compile time from ~6.5s to ~5.6s
        private function HasMatches($extend, $haystackSelectorPath)
        {
            if (!$extend->selector->cacheable) {
                return \true;
            }
            $first_el = $extend->selector->_oelements[0];
            foreach ($haystackSelectorPath as $hackstackSelector) {
                if (!$hackstackSelector->cacheable) {
                    return \true;
                }
                // Optimisation: Explicit reference, <https://github.com/wikimedia/less.php/pull/14>
                if (\array_key_exists($first_el, $hackstackSelector->_oelements_assoc)) {
                    return \true;
                }
            }
            return \false;
        }
        /**
         * @param int $hackstackElementIndex
         */
        private function PotentialMatch($potentialMatch, $needleElements, $haystackElement, $hackstackElementIndex)
        {
            if ($potentialMatch['matched'] > 0) {
                // selectors add " " onto the first element. When we use & it joins the selectors together, but if we don't
                // then each selector in haystackSelectorPath has a space before it added in the toCSS phase. so we need to work out
                // what the resulting combinator will be
                $targetCombinator = $haystackElement->combinator;
                if ($targetCombinator === '' && $hackstackElementIndex === 0) {
                    $targetCombinator = ' ';
                }
                if ($needleElements[$potentialMatch['matched']]->combinator !== $targetCombinator) {
                    return null;
                }
            }
            // if we don't match, null our match to indicate failure
            if (!$this->isElementValuesEqual($needleElements[$potentialMatch['matched']]->value, $haystackElement->value)) {
                return null;
            }
            $potentialMatch['finished'] = \false;
            $potentialMatch['matched']++;
            return $potentialMatch;
        }
        /**
         * @param string|Less_Tree_Attribute|Less_Tree_Dimension|Less_Tree_Keyword $elementValue1
         * @param string|Less_Tree_Attribute|Less_Tree_Dimension|Less_Tree_Keyword $elementValue2
         * @return bool
         */
        private function isElementValuesEqual($elementValue1, $elementValue2)
        {
            if ($elementValue1 === $elementValue2) {
                return \true;
            }
            if (\is_string($elementValue1) || \is_string($elementValue2)) {
                return \false;
            }
            if ($elementValue1 instanceof \Less_Tree_Attribute) {
                return $this->isAttributeValuesEqual($elementValue1, $elementValue2);
            }
            $elementValue1 = $elementValue1->value;
            if ($elementValue1 instanceof \Less_Tree_Selector) {
                return $this->isSelectorValuesEqual($elementValue1, $elementValue2);
            }
            return \false;
        }
        /**
         * @param Less_Tree_Selector $elementValue1
         */
        private function isSelectorValuesEqual($elementValue1, $elementValue2)
        {
            $elementValue2 = $elementValue2->value;
            if (!$elementValue2 instanceof \Less_Tree_Selector || $elementValue1->elements_len !== $elementValue2->elements_len) {
                return \false;
            }
            for ($i = 0; $i < $elementValue1->elements_len; $i++) {
                if ($elementValue1->elements[$i]->combinator !== $elementValue2->elements[$i]->combinator) {
                    if ($i !== 0 || ($elementValue1->elements[$i]->combinator || ' ') !== ($elementValue2->elements[$i]->combinator || ' ')) {
                        return \false;
                    }
                }
                if (!$this->isElementValuesEqual($elementValue1->elements[$i]->value, $elementValue2->elements[$i]->value)) {
                    return \false;
                }
            }
            return \true;
        }
        /**
         * @param Less_Tree_Attribute $elementValue1
         */
        private function isAttributeValuesEqual($elementValue1, $elementValue2)
        {
            if ($elementValue1->op !== $elementValue2->op || $elementValue1->key !== $elementValue2->key) {
                return \false;
            }
            if (!$elementValue1->value || !$elementValue2->value) {
                if ($elementValue1->value || $elementValue2->value) {
                    return \false;
                }
                return \true;
            }
            $elementValue1 = $elementValue1->value->value ?: $elementValue1->value;
            $elementValue2 = $elementValue2->value->value ?: $elementValue2->value;
            return $elementValue1 === $elementValue2;
        }
        private function extendSelector($matches, $selectorPath, $replacementSelector)
        {
            // for a set of matches, replace each match with the replacement selector
            $currentSelectorPathIndex = 0;
            $currentSelectorPathElementIndex = 0;
            $path = [];
            $selectorPath_len = \count($selectorPath);
            for ($matchIndex = 0, $matches_len = \count($matches); $matchIndex < $matches_len; $matchIndex++) {
                $match = $matches[$matchIndex];
                $selector = $selectorPath[$match['pathIndex']];
                $firstElement = new \Less_Tree_Element($match['initialCombinator'], $replacementSelector->elements[0]->value, $replacementSelector->elements[0]->index, $replacementSelector->elements[0]->currentFileInfo);
                if ($match['pathIndex'] > $currentSelectorPathIndex && $currentSelectorPathElementIndex > 0) {
                    $last_path = \end($path);
                    $last_path->elements = \array_merge($last_path->elements, \array_slice($selectorPath[$currentSelectorPathIndex]->elements, $currentSelectorPathElementIndex));
                    $currentSelectorPathElementIndex = 0;
                    $currentSelectorPathIndex++;
                }
                $newElements = \array_merge(
                    \array_slice($selector->elements, $currentSelectorPathElementIndex, $match['index'] - $currentSelectorPathElementIndex),
                    // last parameter of array_slice is different than the last parameter of javascript's slice
                    [$firstElement],
                    \array_slice($replacementSelector->elements, 1)
                );
                if ($currentSelectorPathIndex === $match['pathIndex'] && $matchIndex > 0) {
                    $last_key = \count($path) - 1;
                    $path[$last_key]->elements = \array_merge($path[$last_key]->elements, $newElements);
                } else {
                    $path = \array_merge($path, \array_slice($selectorPath, $currentSelectorPathIndex, $match['pathIndex']));
                    $path[] = new \Less_Tree_Selector($newElements);
                }
                $currentSelectorPathIndex = $match['endPathIndex'];
                $currentSelectorPathElementIndex = $match['endPathElementIndex'];
                if ($currentSelectorPathElementIndex >= \count($selectorPath[$currentSelectorPathIndex]->elements)) {
                    $currentSelectorPathElementIndex = 0;
                    $currentSelectorPathIndex++;
                }
            }
            if ($currentSelectorPathIndex < $selectorPath_len && $currentSelectorPathElementIndex > 0) {
                $last_path = \end($path);
                $last_path->elements = \array_merge($last_path->elements, \array_slice($selectorPath[$currentSelectorPathIndex]->elements, $currentSelectorPathElementIndex));
                $currentSelectorPathIndex++;
            }
            $slice_len = $selectorPath_len - $currentSelectorPathIndex;
            $path = \array_merge($path, \array_slice($selectorPath, $currentSelectorPathIndex, $slice_len));
            return $path;
        }
        protected function visitMedia($mediaNode)
        {
            $newAllExtends = \array_merge($mediaNode->allExtends, \end($this->allExtendsStack));
            $this->allExtendsStack[] = $this->doExtendChaining($newAllExtends, $mediaNode->allExtends);
        }
        protected function visitMediaOut()
        {
            \array_pop($this->allExtendsStack);
        }
        protected function visitDirective($directiveNode)
        {
            $newAllExtends = \array_merge($directiveNode->allExtends, \end($this->allExtendsStack));
            $this->allExtendsStack[] = $this->doExtendChaining($newAllExtends, $directiveNode->allExtends);
        }
        protected function visitDirectiveOut()
        {
            \array_pop($this->allExtendsStack);
        }
    }
}
