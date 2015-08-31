<?php
/**
 * Class Minify_CSS_UriRewriter
 * @package Minify
 */

/**
 * Rewrite file-relative URIs as root-relative in CSS files
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_CSS_UriRewriter {

    /**
     * Defines which class to call as part of callbacks, change this
     * if you extend Minify_CSS_UriRewriter
     * @var string
     */
    protected static $className = 'Minify_CSS_UriRewriter';

    /**
     * rewrite() and rewriteRelative() append debugging information here
     * @var string
     */
    public static $debugText = '';

    /**
     * Does CSS rewrite
     *
     * @static
     * @param string $css
     * @param array $options
     * @return string
     */
    public static function rewrite($css, $options) {
        if (isset($options['prependRelativePath']) || isset($options['currentDir'])) {
            $browsercache_id = (isset($options['browserCacheId']) ? $options['browserCacheId'] : 0);
            $browsercache_extensions = (isset($options['browserCacheExtensions']) ? $options['browserCacheExtensions'] : array());

            if (isset($options['currentDir'])) {
                $document_root = (isset($options['docRoot']) ? $options['docRoot'] : $_SERVER['DOCUMENT_ROOT']);
                $symlinks = (isset($options['symlinks']) ? $options['symlinks'] : array());
                $prependAbsolutePath = (isset($options['prependAbsolutePath']) ? $options['prependAbsolutePath'] : '');
                $prependAbsolutePathCallback = (isset($options['prependAbsolutePathCallback']) ? $options['prependAbsolutePathCallback'] : '');

                $css = self::_rewrite(
                    $css,
                    $options['currentDir'],
                    $prependAbsolutePath,
                    $prependAbsolutePathCallback,
                    $document_root,
                    $symlinks,
                    $browsercache_id,
                    $browsercache_extensions
                );
            } elseif (isset($options['prependRelativePath'])) {
                $css = self::_prepend(
                    $css,
                    $options['prependRelativePath'],
                    $browsercache_id,
                    $browsercache_extensions
                );
            }
        }

        return $css;
    }

    /**
     * Rewrite file relative URIs as root relative in CSS files
     *
     * @param string $css
     *
     * @param string $currentDir The directory of the current CSS file.
     *
     * @param string $prependAbsolutePath
     *
     * @param string $prependAbsolutePathCallback
     *
     * @param string $docRoot The document root of the web site in which
     * the CSS file resides (default = $_SERVER['DOCUMENT_ROOT']).
     *
     * @param array $symlinks (default = array()) If the CSS file is stored in
     * a symlink-ed directory, provide an array of link paths to
     * target paths, where the link paths are within the document root. Because
     * paths need to be normalized for this to work, use "//" to substitute
     * the doc root in the link paths (the array keys). E.g.:
     * <code>
     * array('//symlink' => '/real/target/path') // unix
     * array('//static' => 'D:\\staticStorage')  // Windows
     * </code>
     *
     * @param int $browserCacheId
     *
     * @param array $browserCacheExtensions
     *
     * @return string
     */
    private static function _rewrite($css, $currentDir, $prependAbsolutePath = null, $prependAbsolutePathCallback = null, $docRoot = null, $symlinks = array(), $browserCacheId = 0, $browserCacheExtensions = array()) {
        self::$_docRoot = self::_realpath($docRoot ? $docRoot : $_SERVER['DOCUMENT_ROOT']);
        self::$_currentDir = self::_realpath($currentDir);
        self::$_prependAbsolutePath = $prependAbsolutePath;
        self::$_prependAbsolutePathCallback = $prependAbsolutePathCallback;
        self::$_symlinks = array();
        self::$_browserCacheId = $browserCacheId;
        self::$_browserCacheExtensions = $browserCacheExtensions;

        // normalize symlinks
        foreach ($symlinks as $link => $target) {
            $link = ($link === '//') ? self::$_docRoot : str_replace('//', self::$_docRoot . '/', $link);
            $link = strtr($link, '/', DIRECTORY_SEPARATOR);

            self::$_symlinks[$link] = self::_realpath($target);
        }

        self::$debugText .= "docRoot    : " . self::$_docRoot . "\n" . "currentDir : " . self::$_currentDir . "\n";

        if (self::$_symlinks) {
            self::$debugText .= "symlinks : " . implode(', ', self::$_symlinks) . "\n";
        }

        self::$debugText .= "\n";

        $css = self::_trimUrls($css);

        // rewrite
        $css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/', array(self::$className, '_processUriCB'), $css);
        $css = preg_replace_callback('/url\\(\\s*([^\\)\\s]+)\\s*\\)/', array(self::$className, '_processUriCB'), $css);

        return $css;
    }

    /**
     * Prepend a path to relative URIs in CSS files
     *
     * @param string $css
     * @param string $path The path to prepend.
     * @param integer $browserCacheId
     * @param array $browserCacheExtensions
     *
     * @return string
     */
    private static function _prepend($css, $path, $browserCacheId = 0, $browserCacheExtensions = array()) {
        self::$_prependRelativePath = $path;
        self::$_browserCacheId = $browserCacheId;
        self::$_browserCacheExtensions = $browserCacheExtensions;

        $css = self::_trimUrls($css);

        // append
        $css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/', array(self::$className, '_processUriCB'), $css);
        $css = preg_replace_callback('/url\\(\\s*([^\\)\\s]+)\\s*\\)/', array(self::$className, '_processUriCB'), $css);

        return $css;
    }


    /**
     * @var string directory of this stylesheet
     */
    private static $_currentDir = '';

    /**
     * @var string
     */
    private static $_prependAbsolutePath = null;

    /**
     * @var string
     */
    private static $_prependAbsolutePathCallback = null;

    /**
     * @var string DOC_ROOT
     */
    private static $_docRoot = '';

    /**
     * @var array directory replacements to map symlink targets back to their
     * source (within the document root) E.g. '/var/www/symlink' => '/var/realpath'
     */
    private static $_symlinks = array();

    /**
     * @var int
     */
    private static $_browserCacheId = 0;

    /**
     * @var array
     */
    private static $_browserCacheExtensions = array();

    /**
     * @var string path to prepend
     */
    private static $_prependRelativePath = null;

    private static function _trimUrls($css) {
        return preg_replace('/
            url\\(      # url(
            \\s*
            ([^\\)]+?)  # 1 = URI (assuming does not contain ")")
            \\s*
            \\)         # )
        /x', 'url($1)', $css);
    }

    private static function _processUriCB($m) {
        // $m matched either '/@import\\s+([\'"])(.*?)[\'"]/' or '/url\\(\\s*([^\\)\\s]+)\\s*\\)/'
        $isImport = ($m[0][0] === '@');

        // determine URI and the quote character (if any)
        if ($isImport) {
            $quoteChar = $m[1];
            $uri = $m[2];
        } else {
            // $m[1] is either quoted or not
            $quoteChar = ($m[1][0] === "'" || $m[1][0] === '"') ? $m[1][0] : '';
            $uri = ($quoteChar === '') ? $m[1] : substr($m[1], 1, strlen($m[1]) - 2);
        }

        // analyze URI
        if (false === strpos($uri, '//') && 0 !== strpos($uri, 'data:')) {
            // prepend
            if (self::$_prependRelativePath) {
                if (w3_is_url(self::$_prependRelativePath)) {
                    $parse_url = @parse_url(self::$_prependRelativePath);

                    if ($parse_url && isset($parse_url['host'])) {
                        $scheme = $parse_url['scheme'];
                        $host = $parse_url['host'];
                        $port = (isset($parse_url['port']) && $parse_url['port'] != 80 ? ':' . (int) $parse_url['port'] : '');
                        $path = (!empty($parse_url['path']) ? $parse_url['path'] : '/');
                        $dir_css = preg_replace('~[^/]+$~', '', $path);
                        $dir_obj = preg_replace('~[^/]+$~', '', $uri);
                        $dir = (ltrim((strpos($dir_obj, '/') === 0 ? w3_realpath($dir_obj) : w3_realpath($dir_css . $dir_obj)), '/'));
                        $file = basename($uri);

                        $uri = sprintf('%s://%s%s/%s/%s', $scheme, $host, $port, $dir, $file);
                    }
                } else {
                    $uri = self::$_prependRelativePath . $uri;
                }
            } else {
                $uri = self::_rewriteRelative($uri, self::$_currentDir, self::$_docRoot, self::$_symlinks);

                if (self::$_prependAbsolutePath) {
                    $prependAbsolutePath = self::$_prependAbsolutePath;
                } elseif (self::$_prependAbsolutePathCallback) {
                    $prependAbsolutePath = call_user_func(self::$_prependAbsolutePathCallback, $uri);
                } else {
                    $prependAbsolutePath = '';
                }

                if ($prependAbsolutePath) {
                    $uri = rtrim($prependAbsolutePath, '/') . $uri;
                }
            }

            if (self::$_browserCacheId && count(self::$_browserCacheExtensions)) {
                $matches = null;

                if (preg_match('~\.([a-z-_]+)(\?.*)?$~', $uri, $matches)) {
                    $extension = $matches[1];
                    $query = (isset($matches[2]) ? $matches[2] : '');

                    if ($extension && in_array($extension, self::$_browserCacheExtensions)) {
                        $uri = w3_remove_query($uri);
                        $uri .= ($query ? '&' : '?') . self::$_browserCacheId;
                    }
                }
            }
        }

        return $isImport ? "@import {$quoteChar}{$uri}{$quoteChar}" : "url({$quoteChar}{$uri}{$quoteChar})";
    }

    /**
     * Rewrite a file relative URI as root relative
     *
     * <code>
     * Minify_CSS_UriRewriter::rewriteRelative(
     *       '../img/hello.gif'
     *     , '/home/user/www/css'  // path of CSS file
     *     , '/home/user/www'      // doc root
     * );
     * // returns '/img/hello.gif'
     *
     * // example where static files are stored in a symlinked directory
     * Minify_CSS_UriRewriter::rewriteRelative(
     *       'hello.gif'
     *     , '/var/staticFiles/theme'
     *     , '/home/user/www'
     *     , array('/home/user/www/static' => '/var/staticFiles')
     * );
     * // returns '/static/theme/hello.gif'
     * </code>
     *
     * @param string $uri file relative URI
     *
     * @param string $realCurrentDir realpath of the current file's directory.
     *
     * @param string $realDocRoot realpath of the site document root.
     *
     * @param array $symlinks (default = array()) If the file is stored in
     * a symlink-ed directory, provide an array of link paths to
     * real target paths, where the link paths "appear" to be within the document
     * root. E.g.:
     * <code>
     * array('/home/foo/www/not/real/path' => '/real/target/path') // unix
     * array('C:\\htdocs\\not\\real' => 'D:\\real\\target\\path')  // Windows
     * </code>
     *
     * @return string
     */
    private static function _rewriteRelative($uri, $realCurrentDir, $realDocRoot, $symlinks = array()) {
        if ('/' === $uri[0]) { // root-relative
            return $uri;
        }

        // prepend path with current dir separator (OS-independent)
        $path = strtr($realCurrentDir, '/', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . strtr($uri, '/', DIRECTORY_SEPARATOR);

        self::$debugText .= "file-relative URI  : {$uri}\n" . "path prepended     : {$path}\n";

        // "unresolve" a symlink back to doc root
        foreach ($symlinks as $link => $target) {
            if (0 === strpos($path, $target)) {
                // replace $target with $link
                $path = $link . substr($path, strlen($target));

                self::$debugText .= "symlink unresolved : {$path}\n";

                break;
            }
        }

        // strip doc root
        $path = substr($path, strlen($realDocRoot));

        self::$debugText .= "docroot stripped   : {$path}\n";

        // fix to root-relative URI

        $uri = strtr($path, '/\\', '//');

        // remove /./ and /../ where possible
        $uri = str_replace('/./', '/', $uri);

        // inspired by patch from Oleg Cherniy
        do {
            $uri = preg_replace('@/[^/]+/\\.\\./@', '/', $uri, 1, $changed);
        } while ($changed);

        self::$debugText .= "traversals removed : {$uri}\n\n";

        $uri = preg_replace('~^' . w3_preg_quote(w3_get_base_path()) . '~', w3_get_site_path(), $uri);

        return $uri;
    }

    /**
     * Get realpath with any trailing slash removed. If realpath() fails,
     * just remove the trailing slash.
     *
     * @param string $path
     *
     * @return mixed path with no trailing slash
     */
    protected static function _realpath($path) {
        $realPath = realpath($path);

        if ($realPath !== false) {
            $path = $realPath;
        }

        return rtrim($path, '/\\');
    }
}
