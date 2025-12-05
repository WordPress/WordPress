<?php

namespace {
    /**
     * A simple CSS name-value pair, e.g. `width: 100px;`
     *
     * In bootstrap, there are about 600-1000 simple name-value pairs (depending on
     * how forgiving the match is) -vs- 6,020 dynamic rules (Less_Tree_Rule).
     *
     * Using the name-value object can speed up bootstrap compilation slightly, but
     * it breaks color keyword interpretation: `color: red` -> `color: #FF0000`.
     *
     * @private
     */
    class Less_Tree_NameValue extends \Less_Tree
    {
        public $name;
        public $value;
        public $index;
        public $currentFileInfo;
        public $type = 'NameValue';
        public $important = '';
        public function __construct($name, $value = null, $index = null, $currentFileInfo = null)
        {
            $this->name = $name;
            $this->value = $value;
            $this->index = $index;
            $this->currentFileInfo = $currentFileInfo;
        }
        public function genCSS($output)
        {
            $output->add($this->name . \Less_Environment::$_outputMap[': '] . $this->value . $this->important . (\Less_Environment::$lastRule && \Less_Parser::$options['compress'] ? "" : ";"), $this->currentFileInfo, $this->index);
        }
        public function compile($env)
        {
            return $this;
        }
        public function makeImportant()
        {
            $new = new \Less_Tree_NameValue($this->name, $this->value, $this->index, $this->currentFileInfo);
            $new->important = ' !important';
            return $new;
        }
    }
}
