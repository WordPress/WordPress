<?php
w3_require_once(W3TC_INC_DIR . '/functions/file.php');

/**
 * Removes old legacy folders. User gets a note about manual removal if the function can't delete all in 20 seconds.
 */
function w3_remove_old_folders() {
    $config_admin = w3_instance('W3_ConfigAdmin');
    $old_folders = w3_find_old_folders();
    if ($old_folders) {
        $config_admin->set('notes.remove_w3tc',true);
        $config_admin->save();

        @set_time_limit(20);
        foreach ($old_folders as $old_folder)
            w3_rmdir($old_folder);

        $config_admin->set('notes.remove_w3tc',false);
        $config_admin->save();
    }
}

/**
 * Finds and returns a list of old legacy folders.
 * @return array
 */
function w3_find_old_folders(){
    $dir = @opendir(WP_CONTENT_DIR);
    $include = 'w3tc*';
    $exclude = 'w3tc-config';
    $folders = array();
    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            if (fnmatch($exclude, basename($entry))) {
                    continue;
            }
            if (fnmatch($include, basename($entry))) {
                @closedir($dir);
                $folders[] = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $entry;
            }
        }

        @closedir($dir);
    }
    return $folders;
}
