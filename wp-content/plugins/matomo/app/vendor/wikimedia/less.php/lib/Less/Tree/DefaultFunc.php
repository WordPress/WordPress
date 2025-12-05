<?php

namespace {
    /**
     * @private
     */
    class Less_Tree_DefaultFunc
    {
        static $error_;
        static $value_;
        public static function compile()
        {
            if (self::$error_) {
                throw new \Exception(self::$error_);
            }
            if (self::$value_ !== null) {
                return self::$value_ ? new \Less_Tree_Keyword('true') : new \Less_Tree_Keyword('false');
            }
        }
        public static function value($v)
        {
            self::$value_ = $v;
        }
        public static function error($e)
        {
            self::$error_ = $e;
        }
        public static function reset()
        {
            self::$value_ = self::$error_ = null;
        }
    }
}
