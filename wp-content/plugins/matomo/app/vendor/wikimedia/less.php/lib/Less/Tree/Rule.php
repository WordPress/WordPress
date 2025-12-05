<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Rule extends \Less_Tree
    {
        public $name;
        /** @var Less_Tree $value */
        public $value;
        public $important;
        public $merge;
        public $index;
        public $inline;
        public $variable;
        public $currentFileInfo;
        public $type = 'Rule';
        /**
         * @param string $important
         */
        public function __construct($name, $value = null, $important = null, $merge = null, $index = null, $currentFileInfo = null, $inline = \false)
        {
            $this->name = $name;
            $this->value = $value instanceof \Less_Tree ? $value : new \Less_Tree_Value([$value]);
            $this->important = $important ? ' ' . \trim($important) : '';
            $this->merge = $merge;
            $this->index = $index;
            $this->currentFileInfo = $currentFileInfo;
            $this->inline = $inline;
            $this->variable = \is_string($name) && $name[0] === '@';
        }
        public function accept($visitor)
        {
            $this->value = $visitor->visitObj($this->value);
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add($this->name . \Less_Environment::$_outputMap[': '], $this->currentFileInfo, $this->index);
            try {
                $this->value->genCSS($output);
            } catch (\Less_Exception_Parser $e) {
                $e->index = $this->index;
                $e->currentFile = $this->currentFileInfo;
                throw $e;
            }
            $output->add($this->important . ($this->inline || \Less_Environment::$lastRule && \Less_Parser::$options['compress'] ? "" : ";"), $this->currentFileInfo, $this->index);
        }
        /**
         * @param Less_Environment $env
         * @return Less_Tree_Rule
         */
        public function compile($env)
        {
            $name = $this->name;
            if (\is_array($name)) {
                // expand 'primitive' name directly to get
                // things faster (~10% for benchmark.less):
                if (\count($name) === 1 && $name[0] instanceof \Less_Tree_Keyword) {
                    $name = $name[0]->value;
                } else {
                    $name = $this->CompileName($env, $name);
                }
            }
            $strictMathBypass = \Less_Parser::$options['strictMath'];
            if ($name === "font" && !\Less_Parser::$options['strictMath']) {
                \Less_Parser::$options['strictMath'] = \true;
            }
            try {
                $evaldValue = $this->value->compile($env);
                if (!$this->variable && $evaldValue->type === "DetachedRuleset") {
                    throw new \Less_Exception_Compiler("Rulesets cannot be evaluated on a property.", null, $this->index, $this->currentFileInfo);
                }
                if (\Less_Environment::$mixin_stack) {
                    $return = new \Less_Tree_Rule($name, $evaldValue, $this->important, $this->merge, $this->index, $this->currentFileInfo, $this->inline);
                } else {
                    $this->name = $name;
                    $this->value = $evaldValue;
                    $return = $this;
                }
            } catch (\Less_Exception_Parser $e) {
                if (!\is_numeric($e->index)) {
                    $e->index = $this->index;
                    $e->currentFile = $this->currentFileInfo;
                }
                throw $e;
            }
            \Less_Parser::$options['strictMath'] = $strictMathBypass;
            return $return;
        }
        public function CompileName($env, $name)
        {
            $output = new \Less_Output();
            foreach ($name as $n) {
                $n->compile($env)->genCSS($output);
            }
            return $output->toString();
        }
        public function makeImportant()
        {
            return new \Less_Tree_Rule($this->name, $this->value, '!important', $this->merge, $this->index, $this->currentFileInfo, $this->inline);
        }
    }
}
