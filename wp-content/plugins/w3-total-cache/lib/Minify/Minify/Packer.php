<?php
/**
 * Class Minify_Packer
 *
 * To use this class you must first download the PHP port of Packer
 * and place the file "class.JavaScriptPacker.php" in /lib (or your
 * include_path).
 * @link http://joliclic.free.fr/php/javascript-packer/en/
 *
 * Be aware that, as long as HTTP encoding is used, scripts minified with JSMin
 * will provide better client-side performance, as they need not be unpacked in
 * client-side code.
 *
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

if (false === (@include W3TC_LIB_MINIFY_DIR . '/class.JavaScriptPacker.php')) {
    trigger_error(
        'The script "class.JavaScriptPacker.php" is required. Please see: http:'
        .'//code.google.com/p/minify/source/browse/trunk/min/lib/Minify/Packer.php'
        ,E_USER_ERROR
    );
}

/**
 * Minify Javascript using Dean Edward's Packer
 *
 * @package Minify
 */
class Minify_Packer {
    public static function minify($code, $options = array())
    {
        // @todo: set encoding options based on $options :)
        $packer = new JavascriptPacker($code, 'Normal', true, false);
        return trim($packer->pack());
    }
}
