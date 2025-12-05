<?php

namespace {
    /**
     * Builtin functions
     * @see https://lesscss.org/functions/
     */
    class Less_Functions
    {
        public $env;
        public $currentFileInfo;
        function __construct($env, array $currentFileInfo = null)
        {
            $this->env = $env;
            $this->currentFileInfo = $currentFileInfo;
        }
        /**
         * @param string $op
         * @param float $a
         * @param float $b
         */
        public static function operate($op, $a, $b)
        {
            switch ($op) {
                case '+':
                    return $a + $b;
                case '-':
                    return $a - $b;
                case '*':
                    return $a * $b;
                case '/':
                    return $a / $b;
            }
        }
        public static function clamp($val, $max = 1)
        {
            return \min(\max($val, 0), $max);
        }
        public static function fround($value)
        {
            if ($value === 0) {
                return $value;
            }
            if (\Less_Parser::$options['numPrecision']) {
                $p = \pow(10, \Less_Parser::$options['numPrecision']);
                return \round($value * $p) / $p;
            }
            return $value;
        }
        public static function number($n)
        {
            if ($n instanceof \Less_Tree_Dimension) {
                return \floatval($n->unit->is('%') ? $n->value / 100 : $n->value);
            } elseif (\is_numeric($n)) {
                return $n;
            } else {
                throw new \Less_Exception_Compiler("color functions take numbers as parameters");
            }
        }
        public static function scaled($n, $size = 255)
        {
            if ($n instanceof \Less_Tree_Dimension && $n->unit->is('%')) {
                return (float) $n->value * $size / 100;
            } else {
                return self::number($n);
            }
        }
        public function rgb($r = null, $g = null, $b = null)
        {
            if ($r === null || $g === null || $b === null) {
                throw new \Less_Exception_Compiler("rgb expects three parameters");
            }
            return $this->rgba($r, $g, $b, 1.0);
        }
        public function rgba($r = null, $g = null, $b = null, $a = null)
        {
            $rgb = [$r, $g, $b];
            $rgb = \array_map(['Less_Functions', 'scaled'], $rgb);
            $a = self::number($a);
            return new \Less_Tree_Color($rgb, $a);
        }
        public function hsl($h, $s, $l)
        {
            return $this->hsla($h, $s, $l, 1.0);
        }
        public function hsla($h, $s, $l, $a)
        {
            $h = \fmod(self::number($h), 360) / 360;
            // Classic % operator will change float to int
            $s = self::clamp(self::number($s));
            $l = self::clamp(self::number($l));
            $a = self::clamp(self::number($a));
            $m2 = $l <= 0.5 ? $l * ($s + 1) : $l + $s - $l * $s;
            $m1 = $l * 2 - $m2;
            return $this->rgba(self::hsla_hue($h + 1 / 3, $m1, $m2) * 255, self::hsla_hue($h, $m1, $m2) * 255, self::hsla_hue($h - 1 / 3, $m1, $m2) * 255, $a);
        }
        /**
         * @param float $h
         * @param float $m1
         * @param float $m2
         */
        public function hsla_hue($h, $m1, $m2)
        {
            $h = $h < 0 ? $h + 1 : ($h > 1 ? $h - 1 : $h);
            if ($h * 6 < 1) {
                return $m1 + ($m2 - $m1) * $h * 6;
            } elseif ($h * 2 < 1) {
                return $m2;
            } elseif ($h * 3 < 2) {
                return $m1 + ($m2 - $m1) * (2 / 3 - $h) * 6;
            } else {
                return $m1;
            }
        }
        public function hsv($h, $s, $v)
        {
            return $this->hsva($h, $s, $v, 1.0);
        }
        /**
         * @param Less_Tree|float $h
         * @param Less_Tree|float $s
         * @param Less_Tree|float $v
         * @param float $a
         */
        public function hsva($h, $s, $v, $a)
        {
            $h = self::number($h) % 360 / 360 * 360;
            $s = self::number($s);
            $v = self::number($v);
            $a = self::number($a);
            $i = \floor((int) ($h / 60) % 6);
            $f = $h / 60 - $i;
            $vs = [$v, $v * (1 - $s), $v * (1 - $f * $s), $v * (1 - (1 - $f) * $s)];
            $perm = [[0, 3, 1], [2, 0, 1], [1, 0, 3], [1, 2, 0], [3, 1, 0], [0, 1, 2]];
            return $this->rgba($vs[$perm[$i][0]] * 255, $vs[$perm[$i][1]] * 255, $vs[$perm[$i][2]] * 255, $a);
        }
        public function hue($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to hue must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $c = $color->toHSL();
            return new \Less_Tree_Dimension(\Less_Parser::round($c['h']));
        }
        public function saturation($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to saturation must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $c = $color->toHSL();
            return new \Less_Tree_Dimension(\Less_Parser::round($c['s'] * 100), '%');
        }
        public function lightness($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to lightness must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $c = $color->toHSL();
            return new \Less_Tree_Dimension(\Less_Parser::round($c['l'] * 100), '%');
        }
        public function hsvhue($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to hsvhue must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsv = $color->toHSV();
            return new \Less_Tree_Dimension(\Less_Parser::round($hsv['h']));
        }
        public function hsvsaturation($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to hsvsaturation must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsv = $color->toHSV();
            return new \Less_Tree_Dimension(\Less_Parser::round($hsv['s'] * 100), '%');
        }
        public function hsvvalue($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to hsvvalue must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsv = $color->toHSV();
            return new \Less_Tree_Dimension(\Less_Parser::round($hsv['v'] * 100), '%');
        }
        public function red($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to red must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return new \Less_Tree_Dimension($color->rgb[0]);
        }
        public function green($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to green must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return new \Less_Tree_Dimension($color->rgb[1]);
        }
        public function blue($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to blue must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return new \Less_Tree_Dimension($color->rgb[2]);
        }
        public function alpha($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to alpha must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $c = $color->toHSL();
            return new \Less_Tree_Dimension($c['a']);
        }
        public function luma($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to luma must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return new \Less_Tree_Dimension(\Less_Parser::round($color->luma() * $color->alpha * 100), '%');
        }
        public function luminance($color = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to luminance must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $luminance = 0.2126 * $color->rgb[0] / 255 + 0.7151999999999999 * $color->rgb[1] / 255 + 0.0722 * $color->rgb[2] / 255;
            return new \Less_Tree_Dimension(\Less_Parser::round($luminance * $color->alpha * 100), '%');
        }
        public function saturate($color = null, $amount = null)
        {
            // filter: saturate(3.2);
            // should be kept as is, so check for color
            if ($color instanceof \Less_Tree_Dimension) {
                return null;
            }
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to saturate must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to saturate must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['s'] += $amount->value / 100;
            $hsl['s'] = self::clamp($hsl['s']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        /**
         * @param Less_Tree_Color|null $color
         * @param Less_Tree_Dimension|null $amount
         */
        public function desaturate($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to desaturate must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to desaturate must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['s'] -= $amount->value / 100;
            $hsl['s'] = self::clamp($hsl['s']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        public function lighten($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to lighten must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to lighten must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['l'] += $amount->value / 100;
            $hsl['l'] = self::clamp($hsl['l']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        public function darken($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to darken must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to darken must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['l'] -= $amount->value / 100;
            $hsl['l'] = self::clamp($hsl['l']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        public function fadein($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to fadein must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to fadein must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['a'] += $amount->value / 100;
            $hsl['a'] = self::clamp($hsl['a']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        public function fadeout($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to fadeout must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to fadeout must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['a'] -= $amount->value / 100;
            $hsl['a'] = self::clamp($hsl['a']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        public function fade($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to fade must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to fade must be a percentage' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hsl['a'] = $amount->value / 100;
            $hsl['a'] = self::clamp($hsl['a']);
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        public function spin($color = null, $amount = null)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to spin must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$amount instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The second argument to spin must be a number' . ($amount instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $hsl = $color->toHSL();
            $hue = \fmod($hsl['h'] + $amount->value, 360);
            $hsl['h'] = $hue < 0 ? 360 + $hue : $hue;
            return $this->hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
        }
        //
        // Copyright (c) 2006-2009 Hampton Catlin, Nathan Weizenbaum, and Chris Eppstein
        // https://sass-lang.com/
        //
        /**
         * @param Less_Tree|null $color1
         * @param Less_Tree|null $color2
         * @param Less_Tree|null $weight
         */
        public function mix($color1 = null, $color2 = null, $weight = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to mix must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to mix must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$weight) {
                $weight = new \Less_Tree_Dimension('50', '%');
            }
            if (!$weight instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The third argument to contrast must be a percentage' . ($weight instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            $p = $weight->value / 100.0;
            $w = $p * 2 - 1;
            $hsl1 = $color1->toHSL();
            $hsl2 = $color2->toHSL();
            $a = $hsl1['a'] - $hsl2['a'];
            $w1 = (($w * $a == -1 ? $w : ($w + $a) / (1 + $w * $a)) + 1) / 2;
            $w2 = 1 - $w1;
            $rgb = [$color1->rgb[0] * $w1 + $color2->rgb[0] * $w2, $color1->rgb[1] * $w1 + $color2->rgb[1] * $w2, $color1->rgb[2] * $w1 + $color2->rgb[2] * $w2];
            $alpha = $color1->alpha * $p + $color2->alpha * (1 - $p);
            return new \Less_Tree_Color($rgb, $alpha);
        }
        public function greyscale($color)
        {
            return $this->desaturate($color, new \Less_Tree_Dimension(100, '%'));
        }
        public function contrast($color, $dark = null, $light = null, $threshold = null)
        {
            // filter: contrast(3.2);
            // should be kept as is, so check for color
            if (!$color instanceof \Less_Tree_Color) {
                return null;
            }
            if (!$light) {
                $light = $this->rgba(255, 255, 255, 1.0);
            }
            if (!$dark) {
                $dark = $this->rgba(0, 0, 0, 1.0);
            }
            if (!$dark instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to contrast must be a color' . ($dark instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$light instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The third argument to contrast must be a color' . ($light instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            // Figure out which is actually light and dark!
            if ($dark->luma() > $light->luma()) {
                $t = $light;
                $light = $dark;
                $dark = $t;
            }
            if (!$threshold) {
                $threshold = 0.43;
            } else {
                $threshold = self::number($threshold);
            }
            if ($color->luma() < $threshold) {
                return $light;
            } else {
                return $dark;
            }
        }
        public function e($str)
        {
            if (\is_string($str)) {
                return new \Less_Tree_Anonymous($str);
            }
            return new \Less_Tree_Anonymous($str instanceof \Less_Tree_JavaScript ? $str->expression : $str->value);
        }
        public function escape($str)
        {
            $revert = ['%21' => '!', '%2A' => '*', '%27' => "'", '%3F' => '?', '%26' => '&', '%2C' => ',', '%2F' => '/', '%40' => '@', '%2B' => '+', '%24' => '$'];
            return new \Less_Tree_Anonymous(\strtr(\rawurlencode($str->value), $revert));
        }
        /**
         * todo: This function will need some additional work to make it work the same as less.js
         *
         */
        public function replace($string, $pattern, $replacement, $flags = null)
        {
            $result = $string->value;
            $expr = '/' . \str_replace('/', '\\/', $pattern->value) . '/';
            if ($flags && $flags->value) {
                $expr .= self::replace_flags($flags->value);
            }
            $result = \preg_replace($expr, $replacement->value, $result);
            if (\property_exists($string, 'quote')) {
                return new \Less_Tree_Quoted($string->quote, $result, $string->escaped);
            }
            return new \Less_Tree_Quoted('', $result);
        }
        public static function replace_flags($flags)
        {
            $flags = \str_split($flags, 1);
            $new_flags = '';
            foreach ($flags as $flag) {
                switch ($flag) {
                    case 'e':
                    case 'g':
                        break;
                    default:
                        $new_flags .= $flag;
                        break;
                }
            }
            return $new_flags;
        }
        public function _percent()
        {
            $string = \func_get_arg(0);
            $args = \func_get_args();
            \array_shift($args);
            $result = $string->value;
            foreach ($args as $arg) {
                if (\preg_match('/%[sda]/i', $result, $token)) {
                    $token = $token[0];
                    $value = \stristr($token, 's') ? $arg->value : $arg->toCSS();
                    $value = \preg_match('/[A-Z]$/', $token) ? \urlencode($value) : $value;
                    $result = \preg_replace('/%[sda]/i', $value, $result, 1);
                }
            }
            $result = \str_replace('%%', '%', $result);
            return new \Less_Tree_Quoted($string->quote, $result, $string->escaped);
        }
        public function unit($val, $unit = null)
        {
            if (!$val instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('The first argument to unit must be a number' . ($val instanceof \Less_Tree_Operation ? '. Have you forgotten parenthesis?' : '.'));
            }
            if ($unit) {
                if ($unit instanceof \Less_Tree_Keyword) {
                    $unit = $unit->value;
                } else {
                    $unit = $unit->toCSS();
                }
            } else {
                $unit = "";
            }
            return new \Less_Tree_Dimension($val->value, $unit);
        }
        public function convert($val, $unit)
        {
            return $val->convertTo($unit->value);
        }
        public function round($n, $f = \false)
        {
            $fraction = 0;
            if ($f !== \false) {
                $fraction = $f->value;
            }
            return $this->_math('Less_Parser::round', null, $n, $fraction);
        }
        public function pi()
        {
            return new \Less_Tree_Dimension(\M_PI);
        }
        public function mod($a, $b)
        {
            return new \Less_Tree_Dimension($a->value % $b->value, $a->unit);
        }
        public function pow($x, $y)
        {
            if (\is_numeric($x) && \is_numeric($y)) {
                $x = new \Less_Tree_Dimension($x);
                $y = new \Less_Tree_Dimension($y);
            } elseif (!$x instanceof \Less_Tree_Dimension || !$y instanceof \Less_Tree_Dimension) {
                throw new \Less_Exception_Compiler('Arguments must be numbers');
            }
            return new \Less_Tree_Dimension(\pow($x->value, $y->value), $x->unit);
        }
        // var mathFunctions = [{name:"ce ...
        public function ceil($n)
        {
            return $this->_math('ceil', null, $n);
        }
        public function floor($n)
        {
            return $this->_math('floor', null, $n);
        }
        public function sqrt($n)
        {
            return $this->_math('sqrt', null, $n);
        }
        public function abs($n)
        {
            return $this->_math('abs', null, $n);
        }
        public function tan($n)
        {
            return $this->_math('tan', '', $n);
        }
        public function sin($n)
        {
            return $this->_math('sin', '', $n);
        }
        public function cos($n)
        {
            return $this->_math('cos', '', $n);
        }
        public function atan($n)
        {
            return $this->_math('atan', 'rad', $n);
        }
        public function asin($n)
        {
            return $this->_math('asin', 'rad', $n);
        }
        public function acos($n)
        {
            return $this->_math('acos', 'rad', $n);
        }
        private function _math()
        {
            $args = \func_get_args();
            $fn = \array_shift($args);
            $unit = \array_shift($args);
            if ($args[0] instanceof \Less_Tree_Dimension) {
                if ($unit === null) {
                    $unit = $args[0]->unit;
                } else {
                    $args[0] = $args[0]->unify();
                }
                $args[0] = (float) $args[0]->value;
                return new \Less_Tree_Dimension(\call_user_func_array($fn, $args), $unit);
            } elseif (\is_numeric($args[0])) {
                return \call_user_func_array($fn, $args);
            } else {
                throw new \Less_Exception_Compiler("math functions take numbers as parameters");
            }
        }
        /**
         * @param bool $isMin
         * @param array<Less_Tree> $args
         */
        private function _minmax($isMin, $args)
        {
            $arg_count = \count($args);
            if ($arg_count < 1) {
                throw new \Less_Exception_Compiler('one or more arguments required');
            }
            $j = null;
            $unitClone = null;
            $unitStatic = null;
            // elems only contains original argument values.
            $order = [];
            // key is the unit.toString() for unified tree.Dimension values,
            // value is the index into the order array.
            $values = [];
            for ($i = 0; $i < $arg_count; $i++) {
                $current = $args[$i];
                if (!$current instanceof \Less_Tree_Dimension) {
                    // @phan-suppress-next-line PhanUndeclaredProperty Checked Less_Tree->value
                    if (\property_exists($args[$i], 'value') && \is_array($args[$i]->value)) {
                        // @phan-suppress-next-line PhanUndeclaredProperty Checked Less_Tree->value
                        $args[] = $args[$i]->value;
                    }
                    continue;
                }
                // PhanTypeInvalidDimOffset -- False positive, safe after continue or non-first iterations
                '@phan-var non-empty-list<Less_Tree_Dimension> $order';
                if ($current->unit->toString() === '' && !$unitClone) {
                    $temp = new \Less_Tree_Dimension($current->value, $unitClone);
                    $currentUnified = $temp->unify();
                } else {
                    $currentUnified = $current->unify();
                }
                if ($currentUnified->unit->toString() === "" && !$unitStatic) {
                    $unit = $unitStatic;
                } else {
                    $unit = $currentUnified->unit->toString();
                }
                if ($unit !== '' && !$unitStatic || $unit !== '' && $order[0]->unify()->unit->toString() === "") {
                    $unitStatic = $unit;
                }
                if ($unit != '' && !$unitClone) {
                    $unitClone = $current->unit->toString();
                }
                if (isset($values['']) && $unit !== '' && $unit === $unitStatic) {
                    $j = $values[''];
                } elseif (isset($values[$unit])) {
                    $j = $values[$unit];
                } else {
                    if ($unitStatic && $unit !== $unitStatic) {
                        throw new \Less_Exception_Compiler('incompatible types');
                    }
                    $values[$unit] = \count($order);
                    $order[] = $current;
                    continue;
                }
                if ($order[$j]->unit->toString() === "" && $unitClone) {
                    $temp = new \Less_Tree_Dimension($order[$j]->value, $unitClone);
                    $referenceUnified = $temp->unify();
                } else {
                    $referenceUnified = $order[$j]->unify();
                }
                if ($isMin && $currentUnified->value < $referenceUnified->value || !$isMin && $currentUnified->value > $referenceUnified->value) {
                    $order[$j] = $current;
                }
            }
            if (\count($order) == 1) {
                return $order[0];
            }
            $args = [];
            foreach ($order as $a) {
                $args[] = $a->toCSS();
            }
            return new \Less_Tree_Anonymous(($isMin ? 'min(' : 'max(') . \implode(\Less_Environment::$_outputMap[','], $args) . ')');
        }
        public function min()
        {
            $args = \func_get_args();
            return $this->_minmax(\true, $args);
        }
        public function max()
        {
            $args = \func_get_args();
            return $this->_minmax(\false, $args);
        }
        public function getunit($n)
        {
            return new \Less_Tree_Anonymous($n->unit);
        }
        public function argb($color)
        {
            if (!$color instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to argb must be a color' . ($color instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return new \Less_Tree_Anonymous($color->toARGB());
        }
        public function percentage($n)
        {
            return new \Less_Tree_Dimension($n->value * 100, '%');
        }
        public function color($n)
        {
            if ($n instanceof \Less_Tree_Quoted) {
                $colorCandidate = $n->value;
                $returnColor = \Less_Tree_Color::fromKeyword($colorCandidate);
                if ($returnColor) {
                    return $returnColor;
                }
                if (\preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})/', $colorCandidate)) {
                    return new \Less_Tree_Color(\substr($colorCandidate, 1));
                }
                throw new \Less_Exception_Compiler("argument must be a color keyword or 3/6 digit hex e.g. #FFF");
            } else {
                throw new \Less_Exception_Compiler("argument must be a string");
            }
        }
        public function iscolor($n)
        {
            return $this->_isa($n, 'Less_Tree_Color');
        }
        public function isnumber($n)
        {
            return $this->_isa($n, 'Less_Tree_Dimension');
        }
        public function isstring($n)
        {
            return $this->_isa($n, 'Less_Tree_Quoted');
        }
        public function iskeyword($n)
        {
            return $this->_isa($n, 'Less_Tree_Keyword');
        }
        public function isurl($n)
        {
            return $this->_isa($n, 'Less_Tree_Url');
        }
        public function ispixel($n)
        {
            return $this->isunit($n, 'px');
        }
        public function ispercentage($n)
        {
            return $this->isunit($n, '%');
        }
        public function isem($n)
        {
            return $this->isunit($n, 'em');
        }
        /**
         * @param Less_Tree $n
         * @param Less_Tree|string $unit
         */
        public function isunit($n, $unit)
        {
            if (\is_object($unit) && \property_exists($unit, 'value')) {
                // @phan-suppress-next-line PhanUndeclaredProperty Checked Less_Tree->value
                $unit = $unit->value;
            }
            return $n instanceof \Less_Tree_Dimension && $n->unit->is($unit) ? new \Less_Tree_Keyword('true') : new \Less_Tree_Keyword('false');
        }
        /**
         * @param Less_Tree $n
         * @param string $type
         */
        private function _isa($n, $type)
        {
            return \is_a($n, $type) ? new \Less_Tree_Keyword('true') : new \Less_Tree_Keyword('false');
        }
        public function tint($color, $amount = null)
        {
            return $this->mix($this->rgb(255, 255, 255), $color, $amount);
        }
        public function shade($color, $amount = null)
        {
            return $this->mix($this->rgb(0, 0, 0), $color, $amount);
        }
        public function extract($values, $index)
        {
            $index = (int) $index->value - 1;
            // (1-based index)
            // handle non-array values as an array of length 1
            // return 'undefined' if index is invalid
            if (\property_exists($values, 'value') && \is_array($values->value)) {
                if (isset($values->value[$index])) {
                    return $values->value[$index];
                }
                return null;
            } elseif ((int) $index === 0) {
                return $values;
            }
            return null;
        }
        public function length($values)
        {
            $n = \property_exists($values, 'value') && \is_array($values->value) ? \count($values->value) : 1;
            return new \Less_Tree_Dimension($n);
        }
        public function datauri($mimetypeNode, $filePathNode = null)
        {
            $filePath = $filePathNode ? $filePathNode->value : null;
            $mimetype = $mimetypeNode->value;
            $args = 2;
            if (!$filePath) {
                $filePath = $mimetype;
                $args = 1;
            }
            $filePath = \str_replace('\\', '/', $filePath);
            if (\Less_Environment::isPathRelative($filePath)) {
                $currentFileInfo = $this->currentFileInfo;
                '@phan-var array $currentFileInfo';
                if (\Less_Parser::$options['relativeUrls']) {
                    $temp = $currentFileInfo['currentDirectory'];
                } else {
                    $temp = $currentFileInfo['entryPath'];
                }
                if (!empty($temp)) {
                    $filePath = \Less_Environment::normalizePath(\rtrim($temp, '/') . '/' . $filePath);
                }
            }
            // detect the mimetype if not given
            if ($args < 2) {
                /* incomplete
                			$mime = require('mime');
                			mimetype = mime.lookup(path);
                
                			// use base 64 unless it's an ASCII or UTF-8 format
                			var charset = mime.charsets.lookup(mimetype);
                			useBase64 = ['US-ASCII', 'UTF-8'].indexOf(charset) < 0;
                			if (useBase64) mimetype += ';base64';
                			*/
                $mimetype = \Less_Mime::lookup($filePath);
                $charset = \Less_Mime::charsets_lookup($mimetype);
                $useBase64 = !\in_array($charset, ['US-ASCII', 'UTF-8']);
                if ($useBase64) {
                    $mimetype .= ';base64';
                }
            } else {
                $useBase64 = \preg_match('/;base64$/', $mimetype);
            }
            if (\file_exists($filePath)) {
                $buf = @\file_get_contents($filePath);
            } else {
                $buf = \false;
            }
            // IE8 cannot handle a data-uri larger than 32KB. If this is exceeded
            // and the --ieCompat flag is enabled, return a normal url() instead.
            $DATA_URI_MAX_KB = 32;
            $fileSizeInKB = \round(\strlen($buf) / 1024);
            if ($fileSizeInKB >= $DATA_URI_MAX_KB) {
                $url = new \Less_Tree_Url($filePathNode ?: $mimetypeNode, $this->currentFileInfo);
                return $url->compile($this->env);
            }
            if ($buf) {
                $buf = $useBase64 ? \base64_encode($buf) : \rawurlencode($buf);
                $filePath = '"data:' . $mimetype . ',' . $buf . '"';
            }
            return new \Less_Tree_Url(new \Less_Tree_Anonymous($filePath));
        }
        // svg-gradient
        public function svggradient($direction)
        {
            $throw_message = 'svg-gradient expects direction, start_color [start_position], [color position,]..., end_color [end_position]';
            $arguments = \func_get_args();
            if (\count($arguments) < 3) {
                throw new \Less_Exception_Compiler($throw_message);
            }
            $stops = \array_slice($arguments, 1);
            $gradientType = 'linear';
            $rectangleDimension = 'x="0" y="0" width="1" height="1"';
            $useBase64 = \true;
            $directionValue = $direction->toCSS();
            switch ($directionValue) {
                case "to bottom":
                    $gradientDirectionSvg = 'x1="0%" y1="0%" x2="0%" y2="100%"';
                    break;
                case "to right":
                    $gradientDirectionSvg = 'x1="0%" y1="0%" x2="100%" y2="0%"';
                    break;
                case "to bottom right":
                    $gradientDirectionSvg = 'x1="0%" y1="0%" x2="100%" y2="100%"';
                    break;
                case "to top right":
                    $gradientDirectionSvg = 'x1="0%" y1="100%" x2="100%" y2="0%"';
                    break;
                case "ellipse":
                case "ellipse at center":
                    $gradientType = "radial";
                    $gradientDirectionSvg = 'cx="50%" cy="50%" r="75%"';
                    $rectangleDimension = 'x="-50" y="-50" width="101" height="101"';
                    break;
                default:
                    throw new \Less_Exception_Compiler("svg-gradient direction must be 'to bottom', 'to right', 'to bottom right', 'to top right' or 'ellipse at center'");
            }
            $returner = '<?xml version="1.0" ?>' . '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100%" viewBox="0 0 1 1" preserveAspectRatio="none">' . '<' . $gradientType . 'Gradient id="gradient" gradientUnits="userSpaceOnUse" ' . $gradientDirectionSvg . '>';
            for ($i = 0; $i < \count($stops); $i++) {
                if (\is_object($stops[$i]) && \property_exists($stops[$i], 'value')) {
                    $color = $stops[$i]->value[0];
                    $position = $stops[$i]->value[1];
                } else {
                    $color = $stops[$i];
                    $position = null;
                }
                if (!$color instanceof \Less_Tree_Color || !(($i === 0 || $i + 1 === \count($stops)) && $position === null) && !$position instanceof \Less_Tree_Dimension) {
                    throw new \Less_Exception_Compiler($throw_message);
                }
                if ($position) {
                    $positionValue = $position->toCSS();
                } elseif ($i === 0) {
                    $positionValue = '0%';
                } else {
                    $positionValue = '100%';
                }
                $alpha = $color->alpha;
                $returner .= '<stop offset="' . $positionValue . '" stop-color="' . $color->toRGB() . '"' . ($alpha < 1 ? ' stop-opacity="' . $alpha . '"' : '') . '/>';
            }
            $returner .= '</' . $gradientType . 'Gradient><rect ' . $rectangleDimension . ' fill="url(#gradient)" /></svg>';
            if ($useBase64) {
                $returner = "'data:image/svg+xml;base64," . \base64_encode($returner) . "'";
            } else {
                $returner = "'data:image/svg+xml," . $returner . "'";
            }
            return new \Less_Tree_URL(new \Less_Tree_Anonymous($returner));
        }
        /**
         * Php version of javascript's `encodeURIComponent` function
         *
         * @param string $string The string to encode
         * @return string The encoded string
         */
        public static function encodeURIComponent($string)
        {
            $revert = ['%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'];
            return \strtr(\rawurlencode($string), $revert);
        }
        // Color Blending
        // ref: https://www.w3.org/TR/compositing-1/
        public function colorBlend($mode, $color1, $color2)
        {
            // backdrop
            $ab = $color1->alpha;
            // source
            $as = $color2->alpha;
            $result = [];
            $ar = $as + $ab * (1 - $as);
            for ($i = 0; $i < 3; $i++) {
                $cb = $color1->rgb[$i] / 255;
                $cs = $color2->rgb[$i] / 255;
                $cr = \call_user_func($mode, $cb, $cs);
                if ($ar) {
                    $cr = ($as * $cs + $ab * ($cb - $as * ($cb + $cs - $cr))) / $ar;
                }
                $result[$i] = $cr * 255;
            }
            return new \Less_Tree_Color($result, $ar);
        }
        public function multiply($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to multiply must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to multiply must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendMultiply'], $color1, $color2);
        }
        private function colorBlendMultiply($cb, $cs)
        {
            return $cb * $cs;
        }
        public function screen($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to screen must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to screen must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendScreen'], $color1, $color2);
        }
        private function colorBlendScreen($cb, $cs)
        {
            return $cb + $cs - $cb * $cs;
        }
        public function overlay($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to overlay must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to overlay must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendOverlay'], $color1, $color2);
        }
        private function colorBlendOverlay($cb, $cs)
        {
            $cb *= 2;
            return $cb <= 1 ? $this->colorBlendMultiply($cb, $cs) : $this->colorBlendScreen($cb - 1, $cs);
        }
        public function softlight($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to softlight must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to softlight must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendSoftlight'], $color1, $color2);
        }
        private function colorBlendSoftlight($cb, $cs)
        {
            $d = 1;
            $e = $cb;
            if ($cs > 0.5) {
                $e = 1;
                $d = $cb > 0.25 ? \sqrt($cb) : ((16 * $cb - 12) * $cb + 4) * $cb;
            }
            return $cb - (1 - 2 * $cs) * $e * ($d - $cb);
        }
        public function hardlight($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to hardlight must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to hardlight must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendHardlight'], $color1, $color2);
        }
        private function colorBlendHardlight($cb, $cs)
        {
            return $this->colorBlendOverlay($cs, $cb);
        }
        public function difference($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to difference must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to difference must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendDifference'], $color1, $color2);
        }
        private function colorBlendDifference($cb, $cs)
        {
            return \abs($cb - $cs);
        }
        public function exclusion($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to exclusion must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to exclusion must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendExclusion'], $color1, $color2);
        }
        private function colorBlendExclusion($cb, $cs)
        {
            return $cb + $cs - 2 * $cb * $cs;
        }
        public function average($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to average must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to average must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendAverage'], $color1, $color2);
        }
        // non-w3c functions:
        public function colorBlendAverage($cb, $cs)
        {
            return ($cb + $cs) / 2;
        }
        public function negation($color1 = null, $color2 = null)
        {
            if (!$color1 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The first argument to negation must be a color' . ($color1 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            if (!$color2 instanceof \Less_Tree_Color) {
                throw new \Less_Exception_Compiler('The second argument to negation must be a color' . ($color2 instanceof \Less_Tree_Expression ? ' (did you forgot commas?)' : ''));
            }
            return $this->colorBlend([$this, 'colorBlendNegation'], $color1, $color2);
        }
        public function colorBlendNegation($cb, $cs)
        {
            return 1 - \abs($cb + $cs - 1);
        }
        // ~ End of Color Blending
    }
}
