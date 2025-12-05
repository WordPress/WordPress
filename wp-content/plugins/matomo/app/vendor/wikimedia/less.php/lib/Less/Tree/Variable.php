<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Variable extends \Less_Tree
    {
        public $name;
        public $index;
        public $currentFileInfo;
        public $evaluating = \false;
        public $type = 'Variable';
        /**
         * @param string $name
         */
        public function __construct($name, $index = null, $currentFileInfo = null)
        {
            $this->name = $name;
            $this->index = $index;
            $this->currentFileInfo = $currentFileInfo;
        }
        /**
         * @param Less_Environment $env
         * @return Less_Tree
         * @see less-2.5.3.js#Ruleset.prototype.eval
         */
        public function compile($env)
        {
            if ($this->name[1] === '@') {
                $v = new \Less_Tree_Variable(\substr($this->name, 1), $this->index + 1, $this->currentFileInfo);
                // While some Less_Tree nodes have no 'value', we know these can't ocurr after a variable
                // assignment (would have been a ParseError).
                // TODO: Solve better (https://phabricator.wikimedia.org/T327082).
                // @phan-suppress-next-line PhanUndeclaredProperty
                $name = '@' . $v->compile($env)->value;
            } else {
                $name = $this->name;
            }
            if ($this->evaluating) {
                throw new \Less_Exception_Compiler("Recursive variable definition for " . $name, null, $this->index, $this->currentFileInfo);
            }
            $this->evaluating = \true;
            foreach ($env->frames as $frame) {
                if ($v = $frame->variable($name)) {
                    $r = $v->value->compile($env);
                    $this->evaluating = \false;
                    return $r;
                }
            }
            throw new \Less_Exception_Compiler("variable " . $name . " is undefined in file " . $this->currentFileInfo["filename"], null, $this->index, $this->currentFileInfo);
        }
    }
}
