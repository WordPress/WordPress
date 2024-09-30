<?php

/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2022, Ryan Parman, Sam Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @copyright 2004-2016 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace SimplePie;

use SimplePie\Content\Type\Sniffer;
use SimplePie\Parse\Date;
use SimplePie\XML\Declaration\Parser as DeclarationParser;

/**
 * Handles creating objects and calling methods
 *
 * Access this via {@see \SimplePie\SimplePie::get_registry()}
 *
 * @package SimplePie
 */
class Registry
{
    /**
     * Default class mapping
     *
     * Overriding classes *must* subclass these.
     *
     * @var array<class-string, class-string>
     */
    protected $default = [
        Cache::class => Cache::class,
        Locator::class => Locator::class,
        Parser::class => Parser::class,
        File::class => File::class,
        Sanitize::class => Sanitize::class,
        Item::class => Item::class,
        Author::class => Author::class,
        Category::class => Category::class,
        Enclosure::class => Enclosure::class,
        Caption::class => Caption::class,
        Copyright::class => Copyright::class,
        Credit::class => Credit::class,
        Rating::class => Rating::class,
        Restriction::class => Restriction::class,
        Sniffer::class => Sniffer::class,
        Source::class => Source::class,
        Misc::class => Misc::class,
        DeclarationParser::class => DeclarationParser::class,
        Date::class => Date::class,
    ];

    /**
     * Class mapping
     *
     * @see register()
     * @var array
     */
    protected $classes = [];

    /**
     * Legacy classes
     *
     * @see register()
     * @var array<class-string>
     */
    protected $legacy = [];

    /**
     * Legacy types
     *
     * @see register()
     * @var array<string, class-string>
     */
    private $legacyTypes = [
        'Cache' => Cache::class,
        'Locator' => Locator::class,
        'Parser' => Parser::class,
        'File' => File::class,
        'Sanitize' => Sanitize::class,
        'Item' => Item::class,
        'Author' => Author::class,
        'Category' => Category::class,
        'Enclosure' => Enclosure::class,
        'Caption' => Caption::class,
        'Copyright' => Copyright::class,
        'Credit' => Credit::class,
        'Rating' => Rating::class,
        'Restriction' => Restriction::class,
        'Content_Type_Sniffer' => Sniffer::class,
        'Source' => Source::class,
        'Misc' => Misc::class,
        'XML_Declaration_Parser' => DeclarationParser::class,
        'Parse_Date' => Date::class,
    ];

    /**
     * Constructor
     *
     * No-op
     */
    public function __construct()
    {
    }

    /**
     * Register a class
     *
     * @param string $type See {@see $default} for names
     * @param class-string $class Class name, must subclass the corresponding default
     * @param bool $legacy Whether to enable legacy support for this class
     * @return bool Successfulness
     */
    public function register($type, $class, $legacy = false)
    {
        if (array_key_exists($type, $this->legacyTypes)) {
            // trigger_error(sprintf('"%s"(): Using argument #1 ($type) with value "%s" is deprecated since SimplePie 1.8.0, use class-string "%s" instead.', __METHOD__, $type, $this->legacyTypes[$type]), \E_USER_DEPRECATED);

            $type = $this->legacyTypes[$type];
        }

        if (!array_key_exists($type, $this->default)) {
            return false;
        }

        if (!class_exists($class)) {
            return false;
        }

        /** @var string */
        $base_class = $this->default[$type];

        if (!is_subclass_of($class, $base_class)) {
            return false;
        }

        $this->classes[$type] = $class;

        if ($legacy) {
            $this->legacy[] = $class;
        }

        return true;
    }

    /**
     * Get the class registered for a type
     *
     * Where possible, use {@see create()} or {@see call()} instead
     *
     * @template T
     * @param class-string<T> $type
     * @return class-string<T>|null
     */
    public function get_class($type)
    {
        if (array_key_exists($type, $this->legacyTypes)) {
            // trigger_error(sprintf('"%s"(): Using argument #1 ($type) with value "%s" is deprecated since SimplePie 1.8.0, use class-string "%s" instead.', __METHOD__, $type, $this->legacyTypes[$type]), \E_USER_DEPRECATED);

            $type = $this->legacyTypes[$type];
        }

        if (!array_key_exists($type, $this->default)) {
            return null;
        }

        $class = $this->default[$type];

        if (array_key_exists($type, $this->classes)) {
            $class = $this->classes[$type];
        }

        return $class;
    }

    /**
     * Create a new instance of a given type
     *
     * @template T class-string $type
     * @param class-string<T> $type
     * @param array $parameters Parameters to pass to the constructor
     * @return T Instance of class
     */
    public function &create($type, $parameters = [])
    {
        $class = $this->get_class($type);

        if (!method_exists($class, '__construct')) {
            $instance = new $class();
        } else {
            $reflector = new \ReflectionClass($class);
            $instance = $reflector->newInstanceArgs($parameters);
        }

        if ($instance instanceof RegistryAware) {
            $instance->set_registry($this);
        } elseif (method_exists($instance, 'set_registry')) {
            trigger_error(sprintf('Using the method "set_registry()" without implementing "%s" is deprecated since SimplePie 1.8.0, implement "%s" in "%s".', RegistryAware::class, RegistryAware::class, $class), \E_USER_DEPRECATED);
            $instance->set_registry($this);
        }
        return $instance;
    }

    /**
     * Call a static method for a type
     *
     * @param class-string $type
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function &call($type, $method, $parameters = [])
    {
        $class = $this->get_class($type);

        if (in_array($class, $this->legacy)) {
            switch ($type) {
                case Cache::class:
                    // For backwards compatibility with old non-static
                    // Cache::create() methods in PHP < 8.0.
                    // No longer supported as of PHP 8.0.
                    if ($method === 'get_handler') {
                        $result = @call_user_func_array([$class, 'create'], $parameters);
                        return $result;
                    }
                    break;
            }
        }

        $result = call_user_func_array([$class, $method], $parameters);
        return $result;
    }
}

class_alias('SimplePie\Registry', 'SimplePie_Registry');
