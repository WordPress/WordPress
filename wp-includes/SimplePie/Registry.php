<?php
/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2012, Ryan Parman, Geoffrey Sneddon, Ryan McCue, and contributors
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
 * @version 1.3.1
 * @copyright 2004-2012 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Handles creating objects and calling methods
 *
 * Access this via {@see SimplePie::get_registry()}
 *
 * @package SimplePie
 */
class SimplePie_Registry
{
	/**
	 * Default class mapping
	 *
	 * Overriding classes *must* subclass these.
	 *
	 * @var array
	 */
	protected $default = array(
		'Cache' => 'SimplePie_Cache',
		'Locator' => 'SimplePie_Locator',
		'Parser' => 'SimplePie_Parser',
		'File' => 'SimplePie_File',
		'Sanitize' => 'SimplePie_Sanitize',
		'Item' => 'SimplePie_Item',
		'Author' => 'SimplePie_Author',
		'Category' => 'SimplePie_Category',
		'Enclosure' => 'SimplePie_Enclosure',
		'Caption' => 'SimplePie_Caption',
		'Copyright' => 'SimplePie_Copyright',
		'Credit' => 'SimplePie_Credit',
		'Rating' => 'SimplePie_Rating',
		'Restriction' => 'SimplePie_Restriction',
		'Content_Type_Sniffer' => 'SimplePie_Content_Type_Sniffer',
		'Source' => 'SimplePie_Source',
		'Misc' => 'SimplePie_Misc',
		'XML_Declaration_Parser' => 'SimplePie_XML_Declaration_Parser',
		'Parse_Date' => 'SimplePie_Parse_Date',
	);

	/**
	 * Class mapping
	 *
	 * @see register()
	 * @var array
	 */
	protected $classes = array();

	/**
	 * Legacy classes
	 *
	 * @see register()
	 * @var array
	 */
	protected $legacy = array();

	/**
	 * Constructor
	 *
	 * No-op
	 */
	public function __construct() { }

	/**
	 * Register a class
	 *
	 * @param string $type See {@see $default} for names
	 * @param string $class Class name, must subclass the corresponding default
	 * @param bool $legacy Whether to enable legacy support for this class
	 * @return bool Successfulness
	 */
	public function register($type, $class, $legacy = false)
	{
		if (!is_subclass_of($class, $this->default[$type]))
		{
			return false;
		}

		$this->classes[$type] = $class;

		if ($legacy)
		{
			$this->legacy[] = $class;
		}

		return true;
	}

	/**
	 * Get the class registered for a type
	 *
	 * Where possible, use {@see create()} or {@see call()} instead
	 *
	 * @param string $type
	 * @return string|null
	 */
	public function get_class($type)
	{
		if (!empty($this->classes[$type]))
		{
			return $this->classes[$type];
		}
		if (!empty($this->default[$type]))
		{
			return $this->default[$type];
		}

		return null;
	}

	/**
	 * Create a new instance of a given type
	 *
	 * @param string $type
	 * @param array $parameters Parameters to pass to the constructor
	 * @return object Instance of class
	 */
	public function &create($type, $parameters = array())
	{
		$class = $this->get_class($type);

		if (in_array($class, $this->legacy))
		{
			switch ($type)
			{
				case 'locator':
					// Legacy: file, timeout, useragent, file_class, max_checked_feeds, content_type_sniffer_class
					// Specified: file, timeout, useragent, max_checked_feeds
					$replacement = array($this->get_class('file'), $parameters[3], $this->get_class('content_type_sniffer'));
					array_splice($parameters, 3, 1, $replacement);
					break;
			}
		}

		if (!method_exists($class, '__construct'))
		{
			$instance = new $class;
		}
		else
		{
			$reflector = new ReflectionClass($class);
			$instance = $reflector->newInstanceArgs($parameters);
		}

		if (method_exists($instance, 'set_registry'))
		{
			$instance->set_registry($this);
		}
		return $instance;
	}

	/**
	 * Call a static method for a type
	 *
	 * @param string $type
	 * @param string $method
	 * @param array $parameters
	 * @return mixed
	 */
	public function &call($type, $method, $parameters = array())
	{
		$class = $this->get_class($type);

		if (in_array($class, $this->legacy))
		{
			switch ($type)
			{
				case 'Cache':
					// For backwards compatibility with old non-static
					// Cache::create() methods
					if ($method === 'get_handler')
					{
						$result = @call_user_func_array(array($class, 'create'), $parameters);
						return $result;
					}
					break;
			}
		}

		$result = call_user_func_array(array($class, $method), $parameters);
		return $result;
	}
}