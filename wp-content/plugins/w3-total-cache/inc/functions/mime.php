<?php

/**
 * Returns file mime type
 *
 * @param string $file
 * @return string
 */
function w3_get_mime_type($file) {
    static $cache = array();

    if (!isset($cache[$file])) {
        $mime_type = false;

        /**
         * Try to detect by extension (fast)
         */
        $mime_types = include W3TC_INC_DIR . '/mime/all.php';

        foreach ($mime_types as $extension => $type) {
            if (preg_match('~\.(' . $extension . ')$~i', $file)) {
                if (is_array($type))
                    $mime_type = array_pop($type);
                else
                    $mime_type = $type;
                break;
            }
        }

        /**
         * Try to detect using file info function
         */
        if (!$mime_type && function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);

            if (!$finfo) {
                $finfo = @finfo_open(FILEINFO_MIME);
            }

            if ($finfo) {
                $mime_type = @finfo_file($finfo, $file);

                if ($mime_type) {
                    $extra_mime_type_info = strpos($mime_type, "; ");

                    if ($extra_mime_type_info) {
                        $mime_type = substr($mime_type, 0, $extra_mime_type_info);
                    }

                    if ($mime_type == 'application/octet-stream') {
                        $mime_type = false;
                    }
                }

                @finfo_close($finfo);
            }
        }

        /**
         * Try to detect using mime type function
         */
        if (!$mime_type && function_exists('mime_content_type')) {
            $mime_type = @mime_content_type($file);
        }

        /**
         * If detection failed use default mime type
         */
        if (!$mime_type) {
            $mime_type = 'application/octet-stream';
        }

        $cache[$file] = $mime_type;
    }

    return $cache[$file];
}
