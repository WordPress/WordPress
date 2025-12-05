<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Unit extends \Less_Tree
    {
        var $numerator = [];
        var $denominator = [];
        public $backupUnit;
        public $type = 'Unit';
        public function __construct($numerator = [], $denominator = [], $backupUnit = null)
        {
            $this->numerator = $numerator;
            $this->denominator = $denominator;
            $this->backupUnit = $backupUnit;
        }
        public function __clone()
        {
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            if ($this->numerator) {
                $output->add($this->numerator[0]);
            } elseif ($this->denominator) {
                $output->add($this->denominator[0]);
            } elseif (!\Less_Parser::$options['strictUnits'] && $this->backupUnit) {
                $output->add($this->backupUnit);
                return;
            }
        }
        public function toString()
        {
            $returnStr = \implode('*', $this->numerator);
            foreach ($this->denominator as $d) {
                $returnStr .= '/' . $d;
            }
            return $returnStr;
        }
        public function __toString()
        {
            return $this->toString();
        }
        /**
         * @param Less_Tree_Unit $other
         */
        public function compare($other)
        {
            return $this->is($other->toString()) ? 0 : -1;
        }
        public function is($unitString)
        {
            return $this->toString() === $unitString;
        }
        public function isLength()
        {
            $css = $this->toCSS();
            return (bool) \preg_match('/px|em|%|in|cm|mm|pc|pt|ex/', $css);
        }
        public function isAngle()
        {
            return isset(\Less_Tree_UnitConversions::$angle[$this->toCSS()]);
        }
        public function isEmpty()
        {
            return !$this->numerator && !$this->denominator;
        }
        public function isSingular()
        {
            return \count($this->numerator) <= 1 && !$this->denominator;
        }
        public function usedUnits()
        {
            $result = [];
            foreach (\Less_Tree_UnitConversions::$groups as $groupName) {
                $group = \Less_Tree_UnitConversions::${$groupName};
                foreach ($this->numerator as $atomicUnit) {
                    if (isset($group[$atomicUnit]) && !isset($result[$groupName])) {
                        $result[$groupName] = $atomicUnit;
                    }
                }
                foreach ($this->denominator as $atomicUnit) {
                    if (isset($group[$atomicUnit]) && !isset($result[$groupName])) {
                        $result[$groupName] = $atomicUnit;
                    }
                }
            }
            return $result;
        }
        public function cancel()
        {
            $counter = [];
            $backup = null;
            foreach ($this->numerator as $atomicUnit) {
                if (!$backup) {
                    $backup = $atomicUnit;
                }
                $counter[$atomicUnit] = ($counter[$atomicUnit] ?? 0) + 1;
            }
            foreach ($this->denominator as $atomicUnit) {
                if (!$backup) {
                    $backup = $atomicUnit;
                }
                $counter[$atomicUnit] = ($counter[$atomicUnit] ?? 0) - 1;
            }
            $this->numerator = [];
            $this->denominator = [];
            foreach ($counter as $atomicUnit => $count) {
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $this->numerator[] = $atomicUnit;
                    }
                } elseif ($count < 0) {
                    for ($i = 0; $i < -$count; $i++) {
                        $this->denominator[] = $atomicUnit;
                    }
                }
            }
            if (!$this->numerator && !$this->denominator && $backup) {
                $this->backupUnit = $backup;
            }
            \sort($this->numerator);
            \sort($this->denominator);
        }
    }
}
