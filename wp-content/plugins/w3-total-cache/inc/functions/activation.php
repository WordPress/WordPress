<?php

w3_require_once(W3TC_INC_DIR . '/functions/file.php');

/**
 * Deactivate plugin after activation error
 *
 * @return void
 */
function w3_activation_cleanup() {
    $active_plugins = (array) get_option('active_plugins');
    $active_plugins_network = (array) get_site_option('active_sitewide_plugins');

    // workaround for WPMU deactivation bug
    remove_action('deactivate_' . W3TC_FILE, 'deactivate_sitewide_plugin');

    do_action('deactivate_plugin', W3TC_FILE);

    $key = array_search(W3TC_FILE, $active_plugins);

    if ($key !== false) {
        array_splice($active_plugins, $key, 1);
    }

    unset($active_plugins_network[W3TC_FILE]);

    do_action('deactivate_' . W3TC_FILE);
    do_action('deactivated_plugin', W3TC_FILE);

    update_option('active_plugins', $active_plugins);
    update_site_option('active_sitewide_plugins', $active_plugins_network);
}

/**
 * W3 activate error
 *
 * @param string $error
 * @return void
 */
function w3_activate_error($error) {
    w3_activation_cleanup();

    include W3TC_INC_DIR . '/error.php';
    exit();
}

/**
 * Print activation error with repeat button based on exception
 *
 * @param $e
 */
function w3_activation_error_on_exception($e) {
    $reactivate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . W3TC_FILE, 'activate-plugin_' . W3TC_FILE);
    $reactivate_button = sprintf('%1$sre-activate plugin', '<input type="button" value="') .
        sprintf('" onclick="top.location.href = \'%s\'" />', addslashes($reactivate_url));

    w3_activate_error(sprintf(__('%s<br />then %s.', 'w3-total-cache'), $e->getMessage(), $reactivate_button));
}

/**
 * W3 error on read
 *
 * @param string $path
 * @throws FileOperationException
 */
function w3_throw_on_read_error($path) {
    w3_require_once(W3TC_INC_DIR . '/functions/file.php');

    if (w3_check_open_basedir($path)) {
        $error = sprintf(__('<strong>%s</strong> could not be read, please run following command:<br />
        <strong style="color: #f00;">chmod 777 %s</strong>', 'w3-total-cache'), $path,
            (file_exists($path) ? $path : dirname($path)));
    } else {
        $error = sprintf(__('<strong>%s</strong> could not be read, <strong>open_basedir</strong> restriction in effect,
        please check your php.ini settings:<br /><strong style="color: #f00;">open_basedir = "%s"</strong>',
                'w3-total-cache'), $path,
            ini_get('open_basedir'));
    }

    throw new FileOperationException($error, 'read', 'file', $path);
}

/**
 * W3 writable error
 *
 * @param string $path
 * @param string[] $chmod_dirs Directories that should be chmod 777 inorder to write
 * @throws FileOperationException
 */
function w3_throw_on_write_error($path, $chmod_dirs = array()) {
    w3_require_once(W3TC_INC_DIR . '/functions/file.php');
    $chmods = '';
    if ($chmod_dirs) {
        $chmods = '<ul>';
        foreach($chmod_dirs as $dir) {
            $chmods .= sprintf(__('<li><strong style="color: #f00;">chmod 777 %s</strong></li>', 'w3-total-cache'), $dir);
        }
    } else {
        $chmods = sprintf('<strong style="color: #f00;">chmod 777 %s</strong>',
                         (file_exists($path) ? $path : dirname($path)));
    }
    if (w3_check_open_basedir($path)) {
        $error = sprintf(__('<strong>%s</strong> could not be created, please run following command:<br />%s',
                'w3-total-cache'), $path,
            $chmods);
    } else {
        $error = sprintf(__('<strong>%s</strong> could not be created, <strong>open_basedir
                    </strong> restriction in effect, please check your php.ini settings:<br />
                    <strong style="color: #f00;">open_basedir = "%s"</strong>', 'w3-total-cache'), $path,
            ini_get('open_basedir'));
    }

    throw new FileOperationException($error, 'create', 'file', $path);
}

/**
 * Tries to write file content
 *
 * @param string $filename path to file
 * @param string $content data to write
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder in which to write file
 * @throws FilesystemWriteException
 * @return void
 */
function w3_wp_write_to_file($filename, $content) {
    if (@file_put_contents($filename, $content))
        return;

    try {
        w3_wp_request_filesystem_credentials();
    } catch (FilesystemOperationException $ex) {
        throw new FilesystemWriteException($ex->getMessage(), 
            $ex->credentials_form(), $filename, $content);
    }

    global $wp_filesystem;
    if (!$wp_filesystem->put_contents($filename, $content)) {
        throw new FilesystemWriteException(
            'FTP credentials don\'t allow to write to file <strong>' . 
            $filename . '</strong>', w3_get_filesystem_credentials_form(),
            $filename, $content);
    }
}

/**
 * Tries to read file content
 * @param string $filename path to file
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to read from
 * @return mixed
 * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_read_from_file($filename) {
    $content = @file_get_contents($filename);
    if ($content)
        return $content;

    w3_wp_request_filesystem_credentials();

    global $wp_filesystem;
    if (!($content = $wp_filesystem->get_contents($filename))) {
        throw new FileOperationException('Could not read file: ' . $filename, 'write', 'file', $filename);
    }
    return $content;
}

/**
 * Copy file using WordPress filesystem functions.
 * @param $source_filename
 * @param $destination_filename
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to copy files too
 * @throws FilesystemCopyException
 */
function w3_wp_copy_file($source_filename, $destination_filename) {
    $contents = @file_get_contents($source_filename);
    if ($contents) {
        @file_put_contents($destination_filename, $contents);
    }
    if (@file_exists($destination_filename)) {
        if (@file_get_contents($destination_filename) == $contents)
            return;
    }

    try {
        w3_wp_request_filesystem_credentials();
    } catch (FilesystemOperationException $ex) {
        throw new FilesystemCopyException($ex->getMessage(), 
            $ex->credentials_form(), 
            $source_filename, $destination_filename);
    }

    global $wp_filesystem;
    if (!$wp_filesystem->put_contents($destination_filename, $contents, 
            FS_CHMOD_FILE)) {
        throw new FilesystemCopyException(
            'FTP credentials don\'t allow to copy to file <strong>' . 
                $destination_filename . '</strong>', 
            w3_get_filesystem_credentials_form(),
            $source_filename, $destination_filename);
    }
}

/**
 * @param $folder
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to create folder in
 * @throws FilesystemMkdirException
 */
function w3_wp_create_folder($folder, $from_folder) {
    if (@is_dir($folder))
        return;

     if (w3_mkdir_from($folder, $from_folder))
        return;

    try {
        w3_wp_request_filesystem_credentials();
    } catch (FilesystemOperationException $ex) {
        throw new FilesystemMkdirException($ex->getMessage(), 
            $ex->credentials_form(), $folder);
    }

    global $wp_filesystem;
    if (!$wp_filesystem->mkdir($folder, FS_CHMOD_DIR)) {
        throw new FilesystemMkdirException(
            'FTP credentials don\'t allow to create folder <strong>' . 
                $folder . '</strong>',
            w3_get_filesystem_credentials_form(),
            $folder);
    }
}

/**
 * @param $folder
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context folder to create folder in
 * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
 * @throws FileOperationException
 */
function w3_wp_create_writeable_folder($folder, $from_folder) {
    w3_wp_create_folder($folder, $from_folder);

    $permissions = array(0755, 0775, 0777);
    
    for ($set_index = 0; $set_index < count($permissions); $set_index++) {
        if (is_writable($folder))
            break;

        w3_wp_chmod($folder, $permissions[$set_index]);
    }
}

/**
 * @param $folder
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context path folder where delete folders resides
 * @throws FilesystemRmdirException
 */
function w3_wp_delete_folder($folder) {
    if (!@is_dir($folder))
        return;

    w3_rmdir($folder);
    if (!@is_dir($folder))
        return;

    try {
        w3_wp_request_filesystem_credentials();
    } catch (FilesystemOperationException $ex) {
        throw new FilesystemRmdirException($ex->getMessage(), 
            $ex->credentials_form(), $folder);
    }

    global $wp_filesystem;
    if (!$wp_filesystem->rmdir($folder)) {
        throw new FilesystemRmdirException(
            __('FTP credentials don\'t allow to delete folder ', 'w3-total-cache') .
            '<strong>' . $folder . '</strong>', 
            w3_get_filesystem_credentials_form(),
            $folder);
    }
}

/**
 * @param string $filename
 * @param int $permission
 * @return void
 * @throws FilesystemChmodException
 */
function w3_wp_chmod($filename, $permission) {
    if (@chmod($filename, $permission))
        return;

    
    try {
        w3_wp_request_filesystem_credentials();
    } catch (FilesystemOperationException $ex) {
        throw new FilesystemChmodException($ex->getMessage(), 
            $ex->credentials_form(), $filename, $permission);
    }

    global $wp_filesystem;
    if (!$wp_filesystem->chmod($filename, $permission, true)) {
        throw new FilesystemChmodException(
            __('FTP credentials don\'t allow to chmod ', 'w3-total-cache') .
            '<strong>' . $filename . '</strong>', 
            w3_get_filesystem_credentials_form(),
            $filename, $permission);
    }

    return true;
}

/**
 * @param $file
 * @param string $method
 * @param string $url
 * @param bool|string $context folder where file to be deleted resides
 * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
 */
function w3_wp_delete_file($filename) {
    if (!@file_exists($filename))
        return;
    if (@unlink($filename))
        return;

    try {
        w3_wp_request_filesystem_credentials();
    } catch (FilesystemOperationException $ex) {
        throw new FilesystemRmException($ex->getMessage(), 
            $ex->credentials_form(), $filename);
    }

    global $wp_filesystem;
    if (!$wp_filesystem->delete($filename)) {
        throw new FilesystemRmException(
            __('FTP credentials don\'t allow to delete ', 'w3-total-cache') .
            '<strong>' . $filename . '</strong>', 
            w3_get_filesystem_credentials_form(),
            $filename);
    }
}

/**
 * Get WordPress filesystems credentials. Required for WP filesystem usage.
 * @param string $method Which method to use when creating
 * @param string $url Where to redirect after creation
 * @param bool|string $context path to folder that should be have filesystem credentials.
 * If false WP_CONTENT_DIR is assumed
 * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
 */
function w3_wp_request_filesystem_credentials($method = '', $url = '', $context = false) {
    if (strlen($url) <= 0)
        $url = $_SERVER['REQUEST_URI'];
    $url = preg_replace("/&w3tc_note=([^&]+)/", '', $url);


    $success = true;
    ob_start();
    if (false === ($creds = request_filesystem_credentials($url, $method, false, $context, array()))) {
        $success =  false;
    }
    $form = ob_get_contents();
    ob_end_clean();

    ob_start();
    // If first check failed try again and show error message
    if (!WP_Filesystem($creds) && $success) {
        request_filesystem_credentials($url, $method, true, false, array());
        $success =  false;
        $form = ob_get_contents();
    }
    ob_end_clean();

    $error = '';
    if (preg_match("/<div([^c]+)class=\"error\">(.+)<\/div>/", $form, $matches)) {
        $error = $matches[2];
        $form = str_replace($matches[0], '', $form);
    }

    if (!$success) {
        throw new FilesystemOperationException($error, $form);
    }
}

/**
 * @param string $method
 * @param string $url
 * @param bool|string $context
 * @return FilesystemOperationException with S/FTP form
 */
function w3_get_filesystem_credentials_form($method = '', $url = '', 
        $context = false) {
    ob_start();
    // If first check failed try again and show error message
    request_filesystem_credentials($url, $method, true, false, array());
    $success =  false;
    $form = ob_get_contents();

    ob_end_clean();

    $error = '';
    if (preg_match("/<div([^c]+)class=\"error\">(.+)<\/div>/", $form, $matches)) {
        $form = str_replace($matches[0], '', $form);
    }

    return $form;
}

/**
 * Creates maintenance mode file
 * @param $time
 */
function w3_enable_maintenance_mode($time = null) {
    if (is_null($time))
        $time = 'time()';
    w3_wp_write_to_file(w3_get_site_root() . '/.maintenance', "<?php \$upgrading = $time; ?>");
}

/**
 * Deletes maintenance mode file
 */
function w3_disable_maintenance_mode() {
    w3_wp_delete_file(w3_get_site_root() . '/.maintenance');
}

/**
 * Used to display SelfTestExceptions in UI
 * @param SelfTestExceptions $exs
 * @return array(before_errors = [], required_changes =>, later_errors => [])
 **/
function w3_parse_selftest_exceptions($exs) {
    $exceptions = $exs->exceptions();

    $commands = '';
    $required_changes = '';
    $before_errors = array();
    $later_errors = array();
    $operation_error_already_shown = false;

    foreach ($exceptions as $ex) {
        if ($ex instanceof FilesystemOperationException) {
            if (!$operation_error_already_shown) {
                $m = $ex->getMessage();
                if (strlen($m) > 0) {
                    $before_errors[] = $m;
                    // if multiple operations failed when
                    // they tried to fix environment - show only first
                    // otherwise can duplication information about
                    // absense of permissions
                    $operation_error_already_shown = true;
                }
                if ($ex instanceof FilesystemWriteException) {
                    $required_changes .=
                        sprintf(__('Create the <strong>%s</strong> file and paste the following text into it:
                    <textarea>%s</textarea> <br />', 'w3-total-cache'), $ex->filename(), esc_textarea($ex->file_contents()));
                } else if ($ex instanceof FilesystemModifyException) {
                    $modification_content = $ex->file_contents();
                    if (strlen($modification_content) > 0)
                        $modification_content =
                            '<textarea style="height: 100px; width: 100%;">' .
                            esc_textarea($modification_content) . '</textarea>';
                    $required_changes .=
                        $ex->modification_description() .
                        $modification_content .
                        '<br />';
                } else if ($ex instanceof FilesystemCopyException) {
                    $commands .= 'cp ' . $ex->source_filename() . ' ' .
                        $ex->destination_filename() . '<br />';
                } else if ($ex instanceof FilesystemMkdirException) {
                    $commands .= 'mkdir ' . $ex->folder() . '<br />';
                    $commands .= 'chmod 777 ' . $ex->folder() . '<br />';
                } else if ($ex instanceof FilesystemRmException) {
                    $commands .= 'rm ' . $ex->filename() . '<br />';
                } else if ($ex instanceof FilesystemRmdirException) {
                    $commands .= 'rm -rf ' . $ex->folder() . '<br />';
                } else if ($ex instanceof FilesystemChmodException) {
                    $commands .= 'chmod 777 ' . $ex->filename() . '<br />';
                }
            }
        } else if ($ex instanceof SelfTestFailedException) {
            $t = $ex->technical_message();
            if (strlen($t) > 0) {
                $t = '<br />' .
                    '<a class="w3tc_read_technical_info" href="#">' .
                    __('Technical info', 'w3-total-cache').'</a>' .
                    '<div class="w3tc_technical_info" style="display: none">' .
                    $t . '</div>';
            }

            $later_errors[] = $ex->getMessage() . $t;
        } else {
            // unknown command
            $later_errors[] = $ex->getMessage();
        }
    }

    if (strlen($commands) > 0) {
        $required_changes .= __('Execute next commands in a shell:', 'w3-total-cache') .
            '<br><strong>' . $commands . '</strong>';
    }

    return array(
        'before_errors' => $before_errors,
        'required_changes' => $required_changes,
        'later_errors' => $later_errors
    );
}

/**
 * Thrown when the plugin fails to get correct filesystem rights when it tries to modify manipulate filesystem.
 */
class FilesystemOperationException extends Exception {
    private $credentials_form;

    public function __construct($message, $credentials_form = null) {
        parent::__construct($message);
        $this->credentials_form = $credentials_form;
    }

    public function credentials_form() {
        return $this->credentials_form;

    }
}

class FilesystemWriteException extends FilesystemOperationException {
    private $filename;
    private $file_contents;

    public function __construct($message, $credentials_form, $filename, 
            $file_contents) {
        parent::__construct($message, $credentials_form);

        $this->filename = $filename;
        $this->file_contents = $file_contents;
    }

    public function filename() {
        return $this->filename;
    }

    public function file_contents() {
        return $this->file_contents;
    }
}

class FilesystemModifyException extends FilesystemOperationException {
    private $modification_description; 
    private $filename;
    private $file_contents;

    public function __construct($message, $credentials_form,
            $modification_description, $filename, $file_contents = '') {
        parent::__construct($message, $credentials_form);

        $this->modification_description = $modification_description;
        $this->filename = $filename;
        $this->file_contents = $file_contents;
    }

    function modification_description() {
        return $this->modification_description;
    }

    public function filename() {
        return $this->filename;
    }

    public function file_contents() {
        return $this->file_contents;
    }
}

class FilesystemCopyException extends FilesystemOperationException {
    private $source_filename;
    private $destination_filename;

    public function __construct($message, $credentials_form,
            $source_filename, $destination_filename) {
        parent::__construct($message, $credentials_form);

        $this->source_filename = $source_filename;
        $this->destination_filename = $destination_filename;
    }

    public function source_filename() {
        return $this->source_filename;
    }

    public function destination_filename() {
        return $this->destination_filename;
    }
}

class FilesystemMkdirException extends FilesystemOperationException {
    private $folder;

    public function __construct($message, $credentials_form, $folder) {
        parent::__construct($message, $credentials_form);

        $this->folder = $folder;
    }

    public function folder() {
        return $this->folder;
    }
}

class FilesystemChmodException extends FilesystemOperationException {
    private $filename;
    private $permission;

    public function __construct($message, $credentials_form, $filename, $permission) {
        parent::__construct($message, $credentials_form);

        $this->filename = $filename;
        $this->permission = $permission;
    }

    public function filename() {
        return $this->filename;
    }

    public function permission() {
        return $this->permission;
    }
}

class FilesystemRmException extends FilesystemOperationException {
    private $filename;

    public function __construct($message, $credentials_form, $filename) {
        parent::__construct($message, $credentials_form);

        $this->filename = $filename;
    }

    public function filename() {
        return $this->filename;
    }
}

class FilesystemRmdirException extends FilesystemOperationException {
    private $folder;

    public function __construct($message, $credentials_form, $folder) {
        parent::__construct($message, $credentials_form);

        $this->folder = $folder;
    }

    public function folder() {
        return $this->folder;
    }
}

class SelfTestFailedException extends Exception {
    private $technical_message;

    public function __construct($message, $technical_message = '') {
        parent::__construct($message);
        $this->technical_message = $technical_message;
    }

    public function technical_message() {
        return $this->technical_message;
    }
}



class SelfTestExceptions extends Exception {
    private $exceptions;
    private $credentials_form;

    public function __construct() {
        parent::__construct();

        $this->exceptions = array();
    }

    public function push($ex) {
        if ($ex instanceof SelfTestExceptions) {
            foreach ($ex->exceptions() as $ex2)
                $this->push($ex2);
        } else {
            if ($this->credentials_form == null &&
                    $ex instanceof FilesystemOperationException &&
                    $ex->credentials_form() != null)
                $this->credentials_form = $ex->credentials_form();
            $this->exceptions[] = $ex;
        }
    }

    /**
     * @return Exception[]
     */
    public function exceptions() {
        return $this->exceptions;
    }

    public function credentials_form() {
        return $this->credentials_form;
    }
}

/**
 * Thrown when the plugin fails to read, create, delete files and folders.
 */
class FileOperationException extends Exception {
    private $operation;
    private $file_type;
    private $filename;
    public function __construct($message, $operation = '', $file_type = '', $filename = '') {
        parent::__construct($message);
        $this->operation = $operation;
        $this->file_type = $file_type;
        $this->filename = $filename;
    }

    public function getFileType()
    {
        return $this->file_type;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getOperation()
    {
        return $this->operation;
    }
}

/**
 * @return string[] error messages
 */
function w3_deactivate_plugin() {
    $errors = array();
    try {
        $environment = w3_instance('W3_AdminEnvironment');
        $environment->fix_after_deactivation();
        deactivate_plugins(plugin_basename(W3TC_FILE));
    } catch (SelfTestExceptions $exs) {
        $r = w3_parse_selftest_exceptions($exs);

        foreach ($r['before_errors'] as $e)
            $errors[] = $e;

        if (strlen($r['required_changes']) > 0) {
            $changes_style = 'border: 1px solid black; ' .
                'background: white; ' .
                'margin: 10px 30px 10px 30px; ' .
                'padding: 10px; display: none';
            $ftp_style = 'border: 1px solid black; background: white; ' .
                'margin: 10px 30px 10px 30px; ' .
                'padding: 10px; display: none';
            $ftp_form = str_replace('class="wrap"', '',
                $exs->credentials_form());
            $ftp_form = str_replace('<fieldset>', '', $ftp_form);
            $ftp_form = str_replace('</fieldset>', '', $ftp_form);
            $ftp_form = str_replace('id="upgrade" class="button"',
                'id="upgrade" class="button w3tc-button-save"', $ftp_form);
            $error = sprintf( __('<strong>W3 Total Cache Error:</strong>
		                    Files and directories could not be automatically
		                    deleted.
		                    <table>
		                    <tr>
		                    <td>Please execute commands manually</td>
		                    <td>
								%s
		                    </td>
		                    </tr>
		                    <tr>
		                    <td>or use FTP form to allow
		                    <strong>W3 Total Cache</strong> make it automatically.
		                    </td>
		                    <td>
								%s
		                    </td>
		                    </tr></table>', 'w3-total-cache'),
                    w3_button(__('View required changes', 'w3-total-cache'), '', 'w3tc-show-required-changes'),
                    w3_button(__('Update via FTP', 'w3-total-cache'), '', 'w3tc-show-ftp-form')
                ) . '<div class="w3tc-required-changes" style="' .
                $changes_style . '">' . $r['required_changes'] . '</div>' .
                '<div class="w3tc-ftp-form" style="' . $ftp_style . '">' .
                $ftp_form . '</div>';

            $errors[] = $error;
        }
        return $errors;
    }
}