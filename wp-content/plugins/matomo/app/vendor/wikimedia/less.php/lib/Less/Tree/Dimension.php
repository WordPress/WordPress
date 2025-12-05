<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Dimension extends \Less_Tree
    {
        public $value;
        public $unit;
        public $type = 'Dimension';
        public function __construct($value, $unit = null)
        {
            $this->value = \floatval($value);
            if ($unit && $unit instanceof \Less_Tree_Unit) {
                $this->unit = $unit;
            } elseif ($unit) {
                $this->unit = new \Less_Tree_Unit([$unit]);
            } else {
                $this->unit = new \Less_Tree_Unit();
            }
        }
        public function accept($visitor)
        {
            $this->unit = $visitor->visitObj($this->unit);
        }
        public function toColor()
        {
            return new \Less_Tree_Color([$this->value, $this->value, $this->value]);
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            if (\Less_Parser::$options['strictUnits'] && !$this->unit->isSingular()) {
                throw new \Less_Exception_Compiler("Multiple units in dimension. Correct the units or use the unit function. Bad unit: " . $this->unit->toString());
            }
            $value = \Less_Functions::fround($this->value);
            $strValue = (string) $value;
            if ($value !== 0 && $value < 1.0E-6 && $value > -1.0E-6) {
                // would be output 1e-6 etc.
                $strValue = \number_format((float) $strValue, 10);
                $strValue = \preg_replace('/\\.?0+$/', '', $strValue);
            }
            if (\Less_Parser::$options['compress']) {
                // Zero values doesn't need a unit
                if ($value === 0 && $this->unit->isLength()) {
                    $output->add($strValue);
                    return;
                }
                // Float values doesn't need a leading zero
                if ($value > 0 && $value < 1 && $strValue[0] === '0') {
                    $strValue = \substr($strValue, 1);
                }
            }
            $output->add($strValue);
            $this->unit->genCSS($output);
        }
        public function __toString()
        {
            return $this->toCSS();
        }
        // In an operation between two Dimensions,
        // we default to the first Dimension's unit,
        // so `1px + 2em` will yield `3px`.
        /**
         * @param string $op
         */
        public function operate($op, $other)
        {
            $value = \Less_Functions::operate($op, $this->value, $other->value);
            $unit = clone $this->unit;
            if ($op === '+' || $op === '-') {
                if (!$unit->numerator && !$unit->denominator) {
                    $unit->numerator = $other->unit->numerator;
                    $unit->denominator = $other->unit->denominator;
                } elseif (!$other->unit->numerator && !$other->unit->denominator) {
                    // do nothing
                } else {
                    $other = $other->convertTo($this->unit->usedUnits());
                    if (\Less_Parser::$options['strictUnits'] && $other->unit->toString() !== $unit->toCSS()) {
                        throw new \Less_Exception_Compiler("Incompatible units. Change the units or use the unit function. Bad units: '" . $unit->toString() . "' and " . $other->unit->toString() . "'.");
                    }
                    $value = \Less_Functions::operate($op, $this->value, $other->value);
                }
            } elseif ($op === '*') {
                $unit->numerator = \array_merge($unit->numerator, $other->unit->numerator);
                $unit->denominator = \array_merge($unit->denominator, $other->unit->denominator);
                \sort($unit->numerator);
                \sort($unit->denominator);
                $unit->cancel();
            } elseif ($op === '/') {
                $unit->numerator = \array_merge($unit->numerator, $other->unit->denominator);
                $unit->denominator = \array_merge($unit->denominator, $other->unit->numerator);
                \sort($unit->numerator);
                \sort($unit->denominator);
                $unit->cancel();
            }
            return new \Less_Tree_Dimension($value, $unit);
        }
        public function compare($other)
        {
            if ($other instanceof \Less_Tree_Dimension) {
                if ($this->unit->isEmpty() || $other->unit->isEmpty()) {
                    $a = $this;
                    $b = $other;
                } else {
                    $a = $this->unify();
                    $b = $other->unify();
                    if ($a->unit->compare($b->unit) !== 0) {
                        return -1;
                    }
                }
                $aValue = $a->value;
                $bValue = $b->value;
                if ($bValue > $aValue) {
                    return -1;
                } elseif ($bValue < $aValue) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
        }
        public function unify()
        {
            return $this->convertTo(['length' => 'px', 'duration' => 's', 'angle' => 'rad']);
        }
        public function convertTo($conversions)
        {
            $value = $this->value;
            $unit = clone $this->unit;
            if (\is_string($conversions)) {
                $derivedConversions = [];
                foreach (\Less_Tree_UnitConversions::$groups as $i) {
                    if (isset(\Less_Tree_UnitConversions::${$i}[$conversions])) {
                        $derivedConversions = [$i => $conversions];
                    }
                }
                $conversions = $derivedConversions;
            }
            foreach ($conversions as $groupName => $targetUnit) {
                $group = \Less_Tree_UnitConversions::${$groupName};
                // numerator
                foreach ($unit->numerator as $i => $atomicUnit) {
                    $atomicUnit = $unit->numerator[$i];
                    if (!isset($group[$atomicUnit])) {
                        continue;
                    }
                    $value = $value * ($group[$atomicUnit] / $group[$targetUnit]);
                    $unit->numerator[$i] = $targetUnit;
                }
                // denominator
                foreach ($unit->denominator as $i => $atomicUnit) {
                    $atomicUnit = $unit->denominator[$i];
                    if (!isset($group[$atomicUnit])) {
                        continue;
                    }
                    $value = $value / ($group[$atomicUnit] / $group[$targetUnit]);
                    $unit->denominator[$i] = $targetUnit;
                }
            }
            $unit->cancel();
            return new \Less_Tree_Dimension($value, $unit);
        }
    }
}
