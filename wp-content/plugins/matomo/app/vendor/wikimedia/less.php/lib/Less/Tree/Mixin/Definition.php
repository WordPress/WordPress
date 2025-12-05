<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Mixin_Definition extends \Less_Tree_Ruleset
    {
        public $name;
        public $selectors;
        public $params;
        public $arity = 0;
        public $rules;
        public $lookups = [];
        public $required = 0;
        public $frames = [];
        public $condition;
        public $variadic;
        public $type = 'MixinDefinition';
        // less.js : /lib/less/tree/mixin.js : tree.mixin.Definition
        public function __construct($name, $params, $rules, $condition, $variadic = \false, $frames = [])
        {
            $this->name = $name;
            $this->selectors = [new \Less_Tree_Selector([new \Less_Tree_Element(null, $name)])];
            $this->params = $params;
            $this->condition = $condition;
            $this->variadic = $variadic;
            $this->rules = $rules;
            if ($params) {
                $this->arity = \count($params);
                foreach ($params as $p) {
                    if (!isset($p['name']) || $p['name'] && !isset($p['value'])) {
                        $this->required++;
                    }
                }
            }
            $this->frames = $frames;
            $this->SetRulesetIndex();
        }
        // function accept( $visitor ){
        //	$this->params = $visitor->visit($this->params);
        //	$this->rules = $visitor->visit($this->rules);
        //	$this->condition = $visitor->visit($this->condition);
        //}
        public function toCSS()
        {
            return '';
        }
        // less.js : /lib/less/tree/mixin.js : tree.mixin.Definition.evalParams
        public function compileParams($env, $mixinFrames, $args = [], &$evaldArguments = [])
        {
            $frame = new \Less_Tree_Ruleset(null, []);
            $params = $this->params;
            $mixinEnv = null;
            $argsLength = 0;
            if ($args) {
                $argsLength = \count($args);
                for ($i = 0; $i < $argsLength; $i++) {
                    $arg = $args[$i];
                    if ($arg && $arg['name']) {
                        $isNamedFound = \false;
                        foreach ($params as $j => $param) {
                            if (!isset($evaldArguments[$j]) && $arg['name'] === $params[$j]['name']) {
                                $evaldArguments[$j] = $arg['value']->compile($env);
                                \array_unshift($frame->rules, new \Less_Tree_Rule($arg['name'], $arg['value']->compile($env)));
                                $isNamedFound = \true;
                                break;
                            }
                        }
                        if ($isNamedFound) {
                            \array_splice($args, $i, 1);
                            $i--;
                            $argsLength--;
                            continue;
                        } else {
                            throw new \Less_Exception_Compiler("Named argument for " . $this->name . ' ' . $args[$i]['name'] . ' not found');
                        }
                    }
                }
            }
            $argIndex = 0;
            foreach ($params as $i => $param) {
                if (isset($evaldArguments[$i])) {
                    continue;
                }
                $arg = null;
                if (isset($args[$argIndex])) {
                    $arg = $args[$argIndex];
                }
                if (isset($param['name']) && $param['name']) {
                    if (isset($param['variadic'])) {
                        $varargs = [];
                        for ($j = $argIndex; $j < $argsLength; $j++) {
                            $varargs[] = $args[$j]['value']->compile($env);
                        }
                        $expression = new \Less_Tree_Expression($varargs);
                        \array_unshift($frame->rules, new \Less_Tree_Rule($param['name'], $expression->compile($env)));
                    } else {
                        $val = $arg && $arg['value'] ? $arg['value'] : \false;
                        if ($val) {
                            $val = $val->compile($env);
                        } elseif (isset($param['value'])) {
                            if (!$mixinEnv) {
                                $mixinEnv = new \Less_Environment();
                                $mixinEnv->frames = \array_merge([$frame], $mixinFrames);
                            }
                            $val = $param['value']->compile($mixinEnv);
                            $frame->resetCache();
                        } else {
                            throw new \Less_Exception_Compiler("Wrong number of arguments for " . $this->name . " (" . $argsLength . ' for ' . $this->arity . ")");
                        }
                        \array_unshift($frame->rules, new \Less_Tree_Rule($param['name'], $val));
                        $evaldArguments[$i] = $val;
                    }
                }
                if (isset($param['variadic']) && $args) {
                    for ($j = $argIndex; $j < $argsLength; $j++) {
                        $evaldArguments[$j] = $args[$j]['value']->compile($env);
                    }
                }
                $argIndex++;
            }
            \ksort($evaldArguments);
            $evaldArguments = \array_values($evaldArguments);
            return $frame;
        }
        public function compile($env)
        {
            if ($this->frames) {
                return new \Less_Tree_Mixin_Definition($this->name, $this->params, $this->rules, $this->condition, $this->variadic, $this->frames);
            }
            return new \Less_Tree_Mixin_Definition($this->name, $this->params, $this->rules, $this->condition, $this->variadic, $env->frames);
        }
        public function evalCall($env, $args = null, $important = null)
        {
            \Less_Environment::$mixin_stack++;
            $_arguments = [];
            if ($this->frames) {
                $mixinFrames = \array_merge($this->frames, $env->frames);
            } else {
                $mixinFrames = $env->frames;
            }
            $frame = $this->compileParams($env, $mixinFrames, $args, $_arguments);
            $ex = new \Less_Tree_Expression($_arguments);
            \array_unshift($frame->rules, new \Less_Tree_Rule('@arguments', $ex->compile($env)));
            $ruleset = new \Less_Tree_Ruleset(null, $this->rules);
            $ruleset->originalRuleset = $this->ruleset_id;
            $ruleSetEnv = new \Less_Environment();
            $ruleSetEnv->frames = \array_merge([$this, $frame], $mixinFrames);
            $ruleset = $ruleset->compile($ruleSetEnv);
            if ($important) {
                $ruleset = $ruleset->makeImportant();
            }
            \Less_Environment::$mixin_stack--;
            return $ruleset;
        }
        /** @return bool */
        public function matchCondition($args, $env)
        {
            if (!$this->condition) {
                return \true;
            }
            // set array to prevent error on array_merge
            if (!\is_array($this->frames)) {
                $this->frames = [];
            }
            $frame = $this->compileParams($env, \array_merge($this->frames, $env->frames), $args);
            $compile_env = new \Less_Environment();
            $compile_env->frames = \array_merge(
                [$frame],
                // the parameter variables
                $this->frames,
                // the parent namespace/mixin frames
                $env->frames
            );
            $compile_env->functions = $env->functions;
            return (bool) $this->condition->compile($compile_env);
        }
        public function matchArgs($args, $env = null)
        {
            $argsLength = \count($args);
            if (!$this->variadic) {
                if ($argsLength < $this->required) {
                    return \false;
                }
                if ($argsLength > \count($this->params)) {
                    return \false;
                }
            } else {
                if ($argsLength < $this->required - 1) {
                    return \false;
                }
            }
            $len = \min($argsLength, $this->arity);
            for ($i = 0; $i < $len; $i++) {
                if (!isset($this->params[$i]['name']) && !isset($this->params[$i]['variadic'])) {
                    if ($args[$i]['value']->compile($env)->toCSS() != $this->params[$i]['value']->compile($env)->toCSS()) {
                        return \false;
                    }
                }
            }
            return \true;
        }
    }
}
