<?php

namespace {
    /**
     * @private
     */
    class Less_Environment
    {
        /**
         * Information about the current file - for error reporting and importing and making urls relative etc.
         *
         * - rootpath: rootpath to append to URLs
         *
         * @var array|null $currentFileInfo;
         */
        public $currentFileInfo;
        /* Whether we are currently importing multiple copies */
        public $importMultiple = \false;
        /**
         * @var array
         */
        public $frames = [];
        /**
         * @var array
         */
        public $mediaBlocks = [];
        /**
         * @var array
         */
        public $mediaPath = [];
        public static $parensStack = 0;
        public static $tabLevel = 0;
        public static $lastRule = \false;
        public static $_outputMap;
        public static $mixin_stack = 0;
        /**
         * @var array
         */
        public $functions = [];
        public function Init()
        {
            self::$parensStack = 0;
            self::$tabLevel = 0;
            self::$lastRule = \false;
            self::$mixin_stack = 0;
            if (\Less_Parser::$options['compress']) {
                self::$_outputMap = [',' => ',', ': ' => ':', '' => '', ' ' => ' ', ':' => ' :', '+' => '+', '~' => '~', '>' => '>', '|' => '|', '^' => '^', '^^' => '^^'];
            } else {
                self::$_outputMap = [',' => ', ', ': ' => ': ', '' => '', ' ' => ' ', ':' => ' :', '+' => ' + ', '~' => ' ~ ', '>' => ' > ', '|' => '|', '^' => ' ^ ', '^^' => ' ^^ '];
            }
        }
        public function copyEvalEnv($frames = [])
        {
            $new_env = new \Less_Environment();
            $new_env->frames = $frames;
            return $new_env;
        }
        public static function isMathOn()
        {
            return !\Less_Parser::$options['strictMath'] || self::$parensStack;
        }
        public static function isPathRelative($path)
        {
            return !\preg_match('/^(?:[a-z-]+:|\\/)/', $path);
        }
        /**
         * Canonicalize a path by resolving references to '/./', '/../'
         * Does not remove leading "../"
         * @param string $path or url
         * @return string Canonicalized path
         */
        public static function normalizePath($path)
        {
            $segments = \explode('/', $path);
            $segments = \array_reverse($segments);
            $path = [];
            $path_len = 0;
            while ($segments) {
                $segment = \array_pop($segments);
                switch ($segment) {
                    case '.':
                        break;
                    case '..':
                        // @phan-suppress-next-line PhanTypeInvalidDimOffset False positive
                        if (!$path_len || $path[$path_len - 1] === '..') {
                            $path[] = $segment;
                            $path_len++;
                        } else {
                            \array_pop($path);
                            $path_len--;
                        }
                        break;
                    default:
                        $path[] = $segment;
                        $path_len++;
                        break;
                }
            }
            return \implode('/', $path);
        }
        public function unshiftFrame($frame)
        {
            \array_unshift($this->frames, $frame);
        }
        public function shiftFrame()
        {
            return \array_shift($this->frames);
        }
    }
}
