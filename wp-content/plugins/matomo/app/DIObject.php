<?php

namespace Matomo\Dependencies\DI;

/**
 * This file aims to circumvent problems when updating to Matomo 4.
 * Matomo 4 includes a newer version of PHP-DI, which does not include \DI\object() any longer
 * To not run into any problems with plugins still using that we forward this method to \DI\autowire
 */
if (!function_exists("Matomo\\Dependencies\\DI\\object")) {
    function object(...$params)
    {
        return \Piwik\DI::autowire(...$params);
    }
}
if (!function_exists("Matomo\\Dependencies\\DI\\link")) {
    function link(...$params)
    {
        return \Piwik\DI::get(...$params);
    }
}
