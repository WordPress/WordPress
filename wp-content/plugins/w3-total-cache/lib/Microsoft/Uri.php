<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Microsoft
 * @package   Microsoft_Uri
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Uri.php 18950 2009-11-12 15:37:56Z alexander $
 */

/**
 * Abstract class for all Microsoft_Uri handlers
 *
 * @category  Microsoft
 * @package   Microsoft_Uri
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Microsoft_Uri
{
    /**
     * Scheme of this URI (http, ftp, etc.)
     *
     * @var string
     */
    protected $_scheme = '';

    /**
     * Global configuration array
     *
     * @var array
     */
    static protected $_config = array(
        'allow_unwise' => false
    );

    /**
     * Return a string representation of this URI.
     *
     * @see    getUri()
     * @return string
     */
    public function __toString()
    {
        return $this->getUri();
    }

    /**
     * Convenience function, checks that a $uri string is well-formed
     * by validating it but not returning an object.  Returns TRUE if
     * $uri is a well-formed URI, or FALSE otherwise.
     *
     * @param  string $uri The URI to check
     * @return boolean
     */
    public static function check($uri)
    {
        try {
            $uri = self::factory($uri);
        } catch (Exception $e) {
            return false;
        }

        return $uri->valid();
    }

    /**
     * Create a new Microsoft_Uri object for a URI.  If building a new URI, then $uri should contain
     * only the scheme (http, ftp, etc).  Otherwise, supply $uri with the complete URI.
     *
     * @param  string $uri The URI form which a Microsoft_Uri instance is created
     * @throws Microsoft_Uri_Exception When an empty string was supplied for the scheme
     * @throws Microsoft_Uri_Exception When an illegal scheme is supplied
     * @throws Microsoft_Uri_Exception When the scheme is not supported
     * @return Microsoft_Uri
     * @link   http://www.faqs.org/rfcs/rfc2396.html
     */
    public static function factory($uri = 'http')
    {
        // Separate the scheme from the scheme-specific parts
        $uri            = explode(':', $uri, 2);
        $scheme         = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

        if (strlen($scheme) === 0) {
            require_once 'Microsoft/Uri/Exception.php';
            throw new Microsoft_Uri_Exception('An empty string was supplied for the scheme');
        }

        // Security check: $scheme is used to load a class file, so only alphanumerics are allowed.
        if (ctype_alnum($scheme) === false) {
            require_once 'Microsoft/Uri/Exception.php';
            throw new Microsoft_Uri_Exception('Illegal scheme supplied, only alphanumeric characters are permitted');
        }

        /**
         * Create a new Microsoft_Uri object for the $uri. If a subclass of Microsoft_Uri exists for the
         * scheme, return an instance of that class. Otherwise, a Microsoft_Uri_Exception is thrown.
         */
        switch ($scheme) {
            case 'http':
                // Break intentionally omitted
            case 'https':
                $className = 'Microsoft_Uri_Http';
                break;

            case 'mailto':
                // TODO
            default:
                require_once 'Microsoft/Uri/Exception.php';
                throw new Microsoft_Uri_Exception("Scheme \"$scheme\" is not supported");
                break;
        }

        if (!class_exists($className)) {
            require_once str_replace('_', '/', $className) . '.php';
        }
        $schemeHandler = new $className($scheme, $schemeSpecific);

        return $schemeHandler;
    }

    /**
     * Get the URI's scheme
     *
     * @return string|false Scheme or false if no scheme is set.
     */
    public function getScheme()
    {
        if (empty($this->_scheme) === false) {
            return $this->_scheme;
        } else {
            return false;
        }
    }

    /**
     * Set global configuration options
     *
     * @param Microsoft_Config|array $config
     */
    static public function setConfig($config)
    {
        if ($config instanceof Microsoft_Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            throw new Microsoft_Uri_Exception("Config must be an array or an instance of Microsoft_Config.");
        }

        foreach ($config as $k => $v) {
            self::$_config[$k] = $v;
        }
    }

    /**
     * Microsoft_Uri and its subclasses cannot be instantiated directly.
     * Use Microsoft_Uri::factory() to return a new Microsoft_Uri object.
     *
     * @param string $scheme         The scheme of the URI
     * @param string $schemeSpecific The scheme-specific part of the URI
     */
    abstract protected function __construct($scheme, $schemeSpecific = '');

    /**
     * Return a string representation of this URI.
     *
     * @return string
     */
    abstract public function getUri();

    /**
     * Returns TRUE if this URI is valid, or FALSE otherwise.
     *
     * @return boolean
     */
    abstract public function valid();
}
