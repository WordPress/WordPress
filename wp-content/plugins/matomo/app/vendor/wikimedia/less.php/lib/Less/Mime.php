<?php

namespace {
    /**
     * Mime lookup
     *
     * @private
     */
    class Less_Mime
    {
        // this map is intentionally incomplete
        // if you want more, install 'mime' dep
        private static $types = ['.htm' => 'text/html', '.html' => 'text/html', '.gif' => 'image/gif', '.jpg' => 'image/jpeg', '.jpeg' => 'image/jpeg', '.png' => 'image/png', '.ttf' => 'application/x-font-ttf', '.otf' => 'application/x-font-otf', '.eot' => 'application/vnd.ms-fontobject', '.woff' => 'application/x-font-woff', '.svg' => 'image/svg+xml'];
        public static function lookup($filepath)
        {
            $parts = \explode('.', $filepath);
            $ext = '.' . \strtolower(\array_pop($parts));
            return self::$types[$ext] ?? null;
        }
        public static function charsets_lookup($type = null)
        {
            // assumes all text types are UTF-8
            return $type && \preg_match('/^text\\//', $type) ? 'UTF-8' : '';
        }
    }
}
