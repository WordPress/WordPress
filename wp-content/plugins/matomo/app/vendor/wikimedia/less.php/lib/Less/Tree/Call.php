<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Call extends \Less_Tree
    {
        public $value;
        public $name;
        public $args;
        public $index;
        public $currentFileInfo;
        public $type = 'Call';
        public function __construct($name, $args, $index, $currentFileInfo = null)
        {
            $this->name = $name;
            $this->args = $args;
            $this->index = $index;
            $this->currentFileInfo = $currentFileInfo;
        }
        public function accept($visitor)
        {
            $this->args = $visitor->visitArray($this->args);
        }
        //
        // When evaluating a function call,
        // we either find the function in `tree.functions` [1],
        // in which case we call it, passing the  evaluated arguments,
        // or we simply print it out as it appeared originally [2].
        //
        // The *functions.js* file contains the built-in functions.
        //
        // The reason why we evaluate the arguments, is in the case where
        // we try to pass a variable to a function, like: `saturate(@color)`.
        // The function should receive the value, not the variable.
        //
        public function compile($env = null)
        {
            $args = [];
            foreach ($this->args as $a) {
                $args[] = $a->compile($env);
            }
            $nameLC = \strtolower($this->name);
            switch ($nameLC) {
                case '%':
                    $nameLC = '_percent';
                    break;
                case 'get-unit':
                    $nameLC = 'getunit';
                    break;
                case 'data-uri':
                    $nameLC = 'datauri';
                    break;
                case 'svg-gradient':
                    $nameLC = 'svggradient';
                    break;
            }
            $result = null;
            if ($nameLC === 'default') {
                $result = \Less_Tree_DefaultFunc::compile();
            } else {
                $func = null;
                if (\method_exists('Less_Functions', $nameLC)) {
                    $functions = new \Less_Functions($env, $this->currentFileInfo);
                    $func = [$functions, $nameLC];
                } elseif (isset($env->functions[$nameLC]) && \is_callable($env->functions[$nameLC])) {
                    $func = $env->functions[$nameLC];
                }
                // If the function name isn't known to LESS, output it unchanged as CSS.
                if ($func) {
                    try {
                        $result = \call_user_func_array($func, $args);
                    } catch (\Exception $e) {
                        // Preserve original trace, especially from custom functions.
                        // https://github.com/wikimedia/less.php/issues/38
                        throw new \Less_Exception_Compiler('error evaluating function `' . $this->name . '` ' . $e->getMessage() . ' index: ' . $this->index, $e);
                    }
                }
            }
            if ($result !== null) {
                return $result;
            }
            return new \Less_Tree_Call($this->name, $args, $this->index, $this->currentFileInfo);
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add($this->name . '(', $this->currentFileInfo, $this->index);
            $args_len = \count($this->args);
            for ($i = 0; $i < $args_len; $i++) {
                $this->args[$i]->genCSS($output);
                if ($i + 1 < $args_len) {
                    $output->add(', ');
                }
            }
            $output->add(')');
        }
        // public function toCSS(){
        //    return $this->compile()->toCSS();
        //}
    }
}
