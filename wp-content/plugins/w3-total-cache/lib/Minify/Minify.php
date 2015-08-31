<?php
/**
 * Class Minify
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

/**
 * Minify_Source
 */
w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Source.php');

/**
 * Minify - Combines, minifies, and caches JavaScript and CSS files on demand.
 *
 * See README for usage instructions (for now).
 *
 * This library was inspired by {@link mailto:flashkot@mail.ru jscsscomp by Maxim Martynyuk}
 * and by the article {@link http://www.hunlock.com/blogs/Supercharged_Javascript "Supercharged JavaScript" by Patrick Hunlock}.
 *
 * Requires PHP 5.1.0.
 * Tested on PHP 5.1.6.
 *
 * @package Minify
 * @author Ryan Grove <ryan@wonko.com>
 * @author Stephen Clay <steve@mrclay.org>
 * @copyright 2008 Ryan Grove, Stephen Clay. All rights reserved.
 * @license http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link http://code.google.com/p/minify/
 */
class Minify {

    const VERSION = '2.1.3';
    const TYPE_CSS = 'text/css';
    const TYPE_HTML = 'text/html';
    // there is some debate over the ideal JS Content-Type, but this is the
    // Apache default and what Yahoo! uses..
    const TYPE_JS = 'application/x-javascript';

    /**
     * How many hours behind are the file modification times of uploaded files?
     *
     * If you upload files from Windows to a non-Windows server, Windows may report
     * incorrect mtimes for the files. Immediately after modifying and uploading a
     * file, use the touch command to update the mtime on the server. If the mtime
     * jumps ahead by a number of hours, set this variable to that number. If the mtime
     * moves back, this should not be needed.
     *
     * @var int $uploaderHoursBehind
     */
    public static $uploaderHoursBehind = 0;

    /**
     * If this string is not empty AND the serve() option 'bubbleCssImports' is
     * NOT set, then serve() will check CSS files for @import declarations that
     * appear too late in the combined stylesheet. If found, serve() will prepend
     * the output with this warning.
     *
     * @var string $importWarning
     */
    public static $importWarning = "/* See http://code.google.com/p/minify/wiki/CommonProblems#@imports_can_appear_in_invalid_locations_in_combined_CSS_files */\n";

    /**
     * Cache ID
     * @var string
     */
    protected static $_cacheId = null;

    /**
     * Specify a cache object (with identical interface as Minify_Cache_File) or
     * a path to use with Minify_Cache_File.
     *
     * If not called, Minify will not use a cache and, for each 200 response, will
     * need to recombine files, minify and encode the output.
     *
     * @param mixed $cache object with identical interface as Minify_Cache_File or
     * a directory path, or null to disable caching. (default = '')
     *
     * @return null
     */
    public static function setCache($cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Serve a request for a minified file.
     *
     * Here are the available options and defaults in the base controller:
     *
     * 'isPublic' : send "public" instead of "private" in Cache-Control
     * headers, allowing shared caches to cache the output. (default true)
     *
     * 'quiet' : set to true to have serve() return an array rather than sending
     * any headers/output (default false)
     *
     * 'encodeOutput' : set to false to disable content encoding, and not send
     * the Vary header (default true)
     *
     * 'encodeMethod' : generally you should let this be determined by
     * HTTP_Encoder (leave null), but you can force a particular encoding
     * to be returned, by setting this to 'gzip' or '' (no encoding)
     *
     * 'encodeLevel' : level of encoding compression (0 to 9, default 9)
     *
     * 'contentTypeCharset' : appended to the Content-Type header sent. Set to a falsey
     * value to remove. (default 'utf-8')
     *
     * 'maxAge' : set this to the number of seconds the client should use its cache
     * before revalidating with the server. This sets Cache-Control: max-age and the
     * Expires header. Unlike the old 'setExpires' setting, this setting will NOT
     * prevent conditional GETs. Note this has nothing to do with server-side caching.
     *
     * 'rewriteCssUris' : If true, serve() will automatically set the 'currentDir'
     * minifier option to enable URI rewriting in CSS files (default true)
     *
     * 'bubbleCssImports' : If true, all @import declarations in combined CSS
     * files will be move to the top. Note this may alter effective CSS values
     * due to a change in order. (default false)
     *
     * 'debug' : set to true to minify all sources with the 'Lines' controller, which
     * eases the debugging of combined files. This also prevents 304 responses.
     * @see Minify_Lines::minify()
     *
     * 'minifiers' : to override Minify's default choice of minifier function for
     * a particular content-type, specify your callback under the key of the
     * content-type:
     * <code>
     * // call customCssMinifier($css) for all CSS minification
     * $options['minifiers'][Minify::TYPE_CSS] = 'customCssMinifier';
     *
     * // don't minify Javascript at all
     * $options['minifiers'][Minify::TYPE_JS] = '';
     * </code>
     *
     * 'minifierOptions' : to send options to the minifier function, specify your options
     * under the key of the content-type. E.g. To send the CSS minifier an option:
     * <code>
     * // give CSS minifier array('optionName' => 'optionValue') as 2nd argument
     * $options['minifierOptions'][Minify::TYPE_CSS]['optionName'] = 'optionValue';
     * </code>
     *
     * 'contentType' : (optional) this is only needed if your file extension is not
     * js/css/html. The given content-type will be sent regardless of source file
     * extension, so this should not be used in a Groups config with other
     * Javascript/CSS files.
     *
     * Any controller options are documented in that controller's setupSources() method.
     *
     * @param mixed instance of subclass of Minify_Controller_Base or string name of
     * controller. E.g. 'Files'
     *
     * @param array $options controller/serve options
     *
     * @return mixed null, or, if the 'quiet' option is set to true, an array
     * with keys "success" (bool), "statusCode" (int), "content" (string), and
     * "headers" (array).
     */
    public static function serve($controller, $options = array())
    {
        if (is_string($controller)) {
            // make $controller into object
            $class = 'Minify_Controller_' . $controller;
            if (! class_exists($class, false)) {
                w3_require_once(W3TC_LIB_MINIFY_DIR . "/Minify/Controller/"
                    . str_replace('_', '/', $controller) . ".php");
            }
            $controller = new $class();
        }

        // set up controller sources and mix remaining options with
        // controller defaults
        $options = $controller->setupSources($options);
        $options = $controller->analyzeSources($options);
        self::$_options = $controller->mixInDefaultOptions($options);

        // check request validity
        if (! $controller->sources) {
            // invalid request!
            if (! self::$_options['quiet']) {
                header(self::$_options['badRequestHeader']);
                echo self::$_options['badRequestHeader'];
                return;
            } else {
                list(,$statusCode) = explode(' ', self::$_options['badRequestHeader']);
                return array(
                    'success' => false
                    ,'statusCode' => (int)$statusCode
                    ,'content' => ''
                    ,'headers' => array()
                );
            }
        }

        self::$_controller = $controller;

        if (self::$_options['debug']) {
            self::_setupDebug($controller->sources);
            self::$_options['maxAge'] = 0;
        }

        // determine encoding
        if (self::$_options['encodeOutput']) {
            if (self::$_options['encodeMethod'] !== null) {
                // controller specifically requested this
                $contentEncoding = self::$_options['encodeMethod'];
            } else {
                // sniff request header
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/HTTP/Encoder.php');
                // depending on what the client accepts, $contentEncoding may be
                // 'x-gzip' while our internal encodeMethod is 'gzip'. Calling
                // getAcceptedEncoding(false, false) leaves out compress and deflate as options.
                list(self::$_options['encodeMethod'], $contentEncoding) = HTTP_Encoder::getAcceptedEncoding(self::$_options['encodeOutput']);
            }
        } else {
            self::$_options['encodeMethod'] = ''; // identity (no encoding)
        }

        // check client cache
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/HTTP/ConditionalGet.php');
        $cgOptions = array(
            'cacheHeaders' => self::$_options['cacheHeaders']
            ,'lastModifiedTime' => self::$_options['lastModifiedTime']
            ,'isPublic' => self::$_options['isPublic']
            ,'encoding' => self::$_options['encodeMethod']
        );
        if (self::$_options['maxAge'] > 0) {
            $cgOptions['maxAge'] = self::$_options['maxAge'];
        }
        $cg = new HTTP_ConditionalGet($cgOptions);
        if ($cg->cacheIsValid) {
            // client's cache is valid
            if (! self::$_options['quiet']) {
                $cg->sendHeaders();
                return;
            } else {
                return array(
                    'success' => true
                    ,'statusCode' => 304
                    ,'content' => ''
                    ,'headers' => $cg->getHeaders()
                );
            }
        } else {
            // client will need output
            $headers = $cg->getHeaders();
            unset($cg);
        }

        if (self::$_options['contentType'] === self::TYPE_CSS) {
            reset($controller->sources);
            while (list($key, $source) = each($controller->sources)) {
                if (self::$_options['rewriteCssUris']
                    && $source->filepath
                    && !isset($source->minifyOptions['currentDir'])
                    && !isset($source->minifyOptions['prependRelativePath'])
                ) {
                    $source->minifyOptions['currentDir'] = dirname($source->filepath);
                }

                $source->minifyOptions['processCssImports'] = self::$_options['processCssImports'];
            }
        }

        // check server cache
        if (null !== self::$_cache) {
            // using cache
            // the goal is to use only the cache methods to sniff the length and
            // output the content, as they do not require ever loading the file into
            // memory.
            $cacheId = self::_getCacheId();
            $fullCacheId = (self::$_options['encodeMethod'])
                ? $cacheId . '.' . self::$_options['encodeMethod']
                : $cacheId;
            // check cache for valid entry
            $cacheIsReady = self::$_cache->isValid($fullCacheId, self::$_options['lastModifiedTime']);
            if ($cacheIsReady) {
                $cacheContentLength = self::$_cache->getSize($fullCacheId);
            } else {
                // generate & cache content
                $content = self::_combineMinify();
                self::$_cache->store($cacheId, $content);

                if (self::$_options['encodeOutput'] && function_exists('gzencode')) {
                    self::$_cache->store($cacheId . '.' . self::$_options['encodeMethod'], gzencode($content, self::$_options['encodeLevel']));
                }
            }
        } else {
            // no cache
            $cacheIsReady = false;
            $content = self::_combineMinify();
        }
        if (! $cacheIsReady) {
            switch (self::$_options['encodeMethod']) {
                case 'gzip':
                    $content = gzencode($content, self::$_options['encodeLevel']);
                    break;

                case 'deflate':
                    $content = gzdeflate($content, self::$_options['encodeLevel']);
                    break;
            }
            // still need to encode
        }

        // add headers
        $headers['Content-Length'] = $cacheIsReady
            ? $cacheContentLength
            : strlen($content);
        $headers['Content-Type'] = self::$_options['contentTypeCharset']
            ? self::$_options['contentType'] . '; charset=' . self::$_options['contentTypeCharset']
            : self::$_options['contentType'];
        if (self::$_options['encodeMethod'] !== '') {
            $headers['Content-Encoding'] = $contentEncoding;
        }
        if (self::$_options['encodeOutput']) {
            $headers['Vary'] = 'Accept-Encoding';
        }

        if (! self::$_options['quiet']) {
            // output headers & content
            foreach ($headers as $name => $val) {
                header($name . ': ' . $val);
            }
            if ($cacheIsReady) {
                self::$_cache->display($fullCacheId);
            } else {
                echo $content;
            }
        } else {
            return array(
                'success' => true
                ,'statusCode' => 200
                ,'content' => $cacheIsReady
                    ? self::$_cache->fetch($fullCacheId)
                    : $content
                ,'headers' => $headers
            );
        }
    }

    /**
     * Return combined minified content for a set of sources
     *
     * No internal caching will be used and the content will not be HTTP encoded.
     *
     * @param array $sources array of filepaths and/or Minify_Source objects
     *
     * @param array $options (optional) array of options for serve. By default
     * these are already set: quiet = true, encodeMethod = '', lastModifiedTime = 0.
     *
     * @return string
     */
    public static function combine($sources, $options = array())
    {
        $cache = self::$_cache;
        self::$_cache = null;
        $options = array_merge(array(
            'files' => (array)$sources
            ,'quiet' => true
            ,'encodeMethod' => ''
            ,'lastModifiedTime' => 0
        ), $options);
        $out = self::serve('Files', $options);
        self::$_cache = $cache;
        return $out['content'];
    }

    /**
     * On IIS, create $_SERVER['DOCUMENT_ROOT']
     *
     * @param bool $unsetPathInfo (default false) if true, $_SERVER['PATH_INFO']
     * will be unset (it is inconsistent with Apache's setting)
     *
     * @return null
     */
    public static function setDocRoot($unsetPathInfo = false)
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])
            && 0 === strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/')
        ) {
            $_SERVER['PATH_TRANSLATED']= preg_replace('~[/\\\]+~', '/', $_SERVER['PATH_TRANSLATED']);

            $_SERVER['DOCUMENT_ROOT'] = rtrim(substr(
                $_SERVER['PATH_TRANSLATED']
                ,0
                ,strlen($_SERVER['PATH_TRANSLATED']) - strlen($_SERVER['SCRIPT_NAME'])
            ), '\\');
            if ($unsetPathInfo) {
                unset($_SERVER['PATH_INFO']);
            }
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Logger.php');
            Minify_Logger::log("setDocRoot() set DOCUMENT_ROOT to \"{$_SERVER['DOCUMENT_ROOT']}\"");
        }
    }

    /**
     * @var mixed Minify_Cache_* object or null (i.e. no server cache is used)
     */
    private static $_cache = null;

    /**
     * @var Minify_Controller active controller for current request
     */
    protected static $_controller = null;

    /**
     * @var array options for current request
     */
    protected static $_options = null;

    /**
     * Set up sources to use Minify_Lines
     *
     * @param array $sources Minify_Source instances
     *
     * @return null
     */
    protected static function _setupDebug($sources)
    {
        foreach ($sources as $source) {
            $source->minifier = array('Minify_Lines', 'minify');
            $id = $source->getId();
            $source->minifyOptions = array_merge((array) $source->minifyOptions, array(
            	'id' => (is_file($id) ? basename($id) : $id))
            );
        }
    }

    /**
     * Combines sources and minifies the result.
     *
     * @return string
     */
    protected static function _combineMinify()
    {
        $type = self::$_options['contentType']; // ease readability

        // when combining scripts, make sure all statements separated and
        // trailing single line comment is terminated
        $implodeSeparator = ($type === self::TYPE_JS)
            ? "\n;"
            : '';
        // allow the user to pass a particular array of options to each
        // minifier (designated by type). source objects may still override
        // these
        $defaultOptions = isset(self::$_options['minifierOptions'][$type])
            ? self::$_options['minifierOptions'][$type]
            : array();
        // if minifier not set, default is no minification. source objects
        // may still override this
        $defaultMinifier = isset(self::$_options['minifiers'][$type])
            ? self::$_options['minifiers'][$type]
            : false;

        if (Minify_Source::haveNoMinifyPrefs(self::$_controller->sources)) {
            // all source have same options/minifier, better performance
            // to combine, then minify once
            foreach (self::$_controller->sources as $source) {
                $pieces[] = $source->getContent();
            }
            $content = implode($implodeSeparator, $pieces);
            if ($defaultMinifier) {
                self::$_controller->loadMinifier($defaultMinifier);
                $content = call_user_func($defaultMinifier, $content, $defaultOptions);
            }
        } else {
            // minify each source with its own options and minifier, then combine
            foreach (self::$_controller->sources as $source) {
                // allow the source to override our minifier and options
                $minifier = (null !== $source->minifier)
                    ? $source->minifier
                    : $defaultMinifier;
                $options = (null !== $source->minifyOptions)
                    ? array_merge($defaultOptions, $source->minifyOptions)
                    : $defaultOptions;
                if ($minifier) {
                    self::$_controller->loadMinifier($minifier);
                    // get source content and minify it
                    $pieces[] = call_user_func($minifier, $source->getContent(), $options);
                } else {
                    $pieces[] = $source->getContent();
                }
            }
            $content = implode($implodeSeparator, $pieces);
        }

        if ($type === self::TYPE_CSS && false !== strpos($content, '@import')) {
            $content = self::_handleCssImports($content);
        }

        // do any post-processing (esp. for editing build URIs)
        if (self::$_options['postprocessorRequire']) {
            require_once self::$_options['postprocessorRequire'];
        }
        if (self::$_options['postprocessor']) {
            $content = call_user_func(self::$_options['postprocessor'], $content, $type);
        }
        return $content;
    }

    /**
     * Sets cache ID
     * @param string $cacheId
     */
    public static function setCacheId($cacheId = null)
    {
        self::$_cacheId = $cacheId;
    }

    /**
     * Returns cache ID
     */
    public static function getCacheId()
    {
        return self::$_cacheId;
    }

    /**
     * Make a unique cache id for for this request.
     *
     * Any settings that could affect output are taken into consideration
     *
     * @return string
     */
    protected static function _getCacheId()
    {
        return (self::$_cacheId ? self::$_cacheId : md5(serialize(array(
            Minify_Source::getDigest(self::$_controller->sources)
            ,self::$_options['minifiers']
            ,self::$_options['minifierOptions']
            ,self::$_options['postprocessor']
            ,self::$_options['bubbleCssImports']
            ,self::$_options['processCssImports']
        ))));
    }

    /**
     * Bubble CSS @imports to the top or prepend a warning if an
     * @import is detected not at the top.
     */
    protected static function _handleCssImports($css)
    {
        if (self::$_options['bubbleCssImports']) {
            // bubble CSS imports
            preg_match_all('/@import.*?;/', $css, $imports);
            $css = implode('', $imports[0]) . preg_replace('/@import.*?;/', '', $css);
        } else if ('' !== self::$importWarning) {
            // remove comments so we don't mistake { in a comment as a block
            $noCommentCss = preg_replace('@/\\*[\\s\\S]*?\\*/@', '', $css);
            $lastImportPos = strrpos($noCommentCss, '@import');
            $firstBlockPos = strpos($noCommentCss, '{');
            if (false !== $lastImportPos
                && false !== $firstBlockPos
                && $firstBlockPos < $lastImportPos
            ) {
                // { appears before @import : prepend warning
                $css = self::$importWarning . $css;
            }
        }
        return $css;
    }
}
