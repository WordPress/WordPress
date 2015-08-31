<?php

class W3_Environment_WpLoader {
    /**
     * Verify that WordPress install folder is part of WP_CONTENT_DIR path
     * @return bool
     */
    public function should_create() {
        if (defined('DONOTVERIFY_WP_LOADER') && DONOTVERIFY_WP_LOADER)
            return false;
        if (strpos(WP_PLUGIN_DIR, WP_CONTENT_DIR) === false ||
            strpos(WP_CONTENT_DIR, ABSPATH) === false) {
            return true;
        }
        return false;
    }

    /**
     * @throws FilesystemWriteException
     * @throws FilesystemWriteException
     */
    public function create() {
        $path = trim(w3_get_wp_sitepath() ,"/");
        if ($path)
            $path .= '/';
        $file_data = "
<?php
    if (W3TC_WP_LOADING)
        require_once '" . w3_get_document_root() . '/' . $path . "wp-load.php';
";
        $filename = W3TC_WP_LOADER;
        $data = $file_data;

        w3_require_once(W3TC_INC_DIR . '/functions/rule.php');
        $current_data = @file_get_contents($filename);
        if (strstr(w3_clean_rules($current_data), w3_clean_rules($data)) !== false)
            return;

        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
        w3_wp_write_to_file($filename, $data, '', $_SERVER['REQUEST_URI']);
    }
}
