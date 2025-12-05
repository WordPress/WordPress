<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_Color extends \Less_Tree
    {
        public $rgb;
        public $alpha;
        public $isTransparentKeyword;
        public $type = 'Color';
        public function __construct($rgb, $a = 1, $isTransparentKeyword = null)
        {
            if ($isTransparentKeyword) {
                $this->rgb = $rgb;
                $this->alpha = $a;
                $this->isTransparentKeyword = \true;
                return;
            }
            $this->rgb = [];
            if (\is_array($rgb)) {
                $this->rgb = $rgb;
            } elseif (\strlen($rgb) == 6) {
                foreach (\str_split($rgb, 2) as $c) {
                    $this->rgb[] = \hexdec($c);
                }
            } else {
                foreach (\str_split($rgb, 1) as $c) {
                    $this->rgb[] = \hexdec($c . $c);
                }
            }
            $this->alpha = \is_numeric($a) ? $a : 1;
        }
        public function luma()
        {
            $r = $this->rgb[0] / 255;
            $g = $this->rgb[1] / 255;
            $b = $this->rgb[2] / 255;
            $r = $r <= 0.03928 ? $r / 12.92 : \pow(($r + 0.055) / 1.055, 2.4);
            $g = $g <= 0.03928 ? $g / 12.92 : \pow(($g + 0.055) / 1.055, 2.4);
            $b = $b <= 0.03928 ? $b / 12.92 : \pow(($b + 0.055) / 1.055, 2.4);
            return 0.2126 * $r + 0.7151999999999999 * $g + 0.0722 * $b;
        }
        /**
         * @see Less_Tree::genCSS
         */
        public function genCSS($output)
        {
            $output->add($this->toCSS());
        }
        public function toCSS($doNotCompress = \false)
        {
            $compress = \Less_Parser::$options['compress'] && !$doNotCompress;
            $alpha = \Less_Functions::fround($this->alpha);
            //
            // If we have some transparency, the only way to represent it
            // is via `rgba`. Otherwise, we use the hex representation,
            // which has better compatibility with older browsers.
            // Values are capped between `0` and `255`, rounded and zero-padded.
            //
            if ($alpha < 1) {
                if (($alpha === 0 || $alpha === 0.0) && isset($this->isTransparentKeyword) && $this->isTransparentKeyword) {
                    return 'transparent';
                }
                $values = [];
                foreach ($this->rgb as $c) {
                    $values[] = \Less_Functions::clamp(\round($c), 255);
                }
                $values[] = $alpha;
                $glue = $compress ? ',' : ', ';
                return "rgba(" . \implode($glue, $values) . ")";
            } else {
                $color = $this->toRGB();
                if ($compress) {
                    // Convert color to short format
                    if ($color[1] === $color[2] && $color[3] === $color[4] && $color[5] === $color[6]) {
                        $color = '#' . $color[1] . $color[3] . $color[5];
                    }
                }
                return $color;
            }
        }
        //
        // Operations have to be done per-channel, if not,
        // channels will spill onto each other. Once we have
        // our result, in the form of an integer triplet,
        // we create a new Color node to hold the result.
        //
        /**
         * @param string $op
         */
        public function operate($op, $other)
        {
            $rgb = [];
            $alpha = $this->alpha * (1 - $other->alpha) + $other->alpha;
            for ($c = 0; $c < 3; $c++) {
                $rgb[$c] = \Less_Functions::operate($op, $this->rgb[$c], $other->rgb[$c]);
            }
            return new \Less_Tree_Color($rgb, $alpha);
        }
        public function toRGB()
        {
            return $this->toHex($this->rgb);
        }
        public function toHSL()
        {
            $r = $this->rgb[0] / 255;
            $g = $this->rgb[1] / 255;
            $b = $this->rgb[2] / 255;
            $a = $this->alpha;
            $max = \max($r, $g, $b);
            $min = \min($r, $g, $b);
            $l = ($max + $min) / 2;
            $d = $max - $min;
            $h = $s = 0;
            if ($max !== $min) {
                $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
                switch ($max) {
                    case $r:
                        $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                        break;
                    case $g:
                        $h = ($b - $r) / $d + 2;
                        break;
                    case $b:
                        $h = ($r - $g) / $d + 4;
                        break;
                }
                $h /= 6;
            }
            return ['h' => $h * 360, 's' => $s, 'l' => $l, 'a' => $a];
        }
        // Adapted from http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
        public function toHSV()
        {
            $r = $this->rgb[0] / 255;
            $g = $this->rgb[1] / 255;
            $b = $this->rgb[2] / 255;
            $a = $this->alpha;
            $max = \max($r, $g, $b);
            $min = \min($r, $g, $b);
            $v = $max;
            $d = $max - $min;
            if ($max === 0) {
                $s = 0;
            } else {
                $s = $d / $max;
            }
            $h = 0;
            if ($max !== $min) {
                switch ($max) {
                    case $r:
                        $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                        break;
                    case $g:
                        $h = ($b - $r) / $d + 2;
                        break;
                    case $b:
                        $h = ($r - $g) / $d + 4;
                        break;
                }
                $h /= 6;
            }
            return ['h' => $h * 360, 's' => $s, 'v' => $v, 'a' => $a];
        }
        public function toARGB()
        {
            $argb = \array_merge((array) \Less_Parser::round($this->alpha * 255), $this->rgb);
            return $this->toHex($argb);
        }
        public function compare($x)
        {
            if (!\property_exists($x, 'rgb')) {
                return -1;
            }
            return $x->rgb[0] === $this->rgb[0] && $x->rgb[1] === $this->rgb[1] && $x->rgb[2] === $this->rgb[2] && $x->alpha === $this->alpha ? 0 : -1;
        }
        public function toHex($v)
        {
            $ret = '#';
            foreach ($v as $c) {
                $c = \Less_Functions::clamp(\Less_Parser::round($c), 255);
                if ($c < 16) {
                    $ret .= '0';
                }
                $ret .= \dechex($c);
            }
            return $ret;
        }
        /**
         * @param string $keyword
         */
        public static function fromKeyword($keyword)
        {
            $keyword = \strtolower($keyword);
            if (\Less_Colors::hasOwnProperty($keyword)) {
                // detect named color
                return new \Less_Tree_Color(\substr(\Less_Colors::color($keyword), 1));
            }
            if ($keyword === 'transparent') {
                return new \Less_Tree_Color([0, 0, 0], 0, \true);
            }
        }
    }
}
