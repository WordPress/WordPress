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
 * @version 1.3
 * @copyright 2004-2012 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */


/**
 * Parses the XML Declaration
 *
 * @package SimplePie
 * @subpackage Parsing
 */
class SimplePie_XML_Declaration_Parser
{
	/**
	 * XML Version
	 *
	 * @access public
	 * @var string
	 */
	var $version = '1.0';

	/**
	 * Encoding
	 *
	 * @access public
	 * @var string
	 */
	var $encoding = 'UTF-8';

	/**
	 * Standalone
	 *
	 * @access public
	 * @var bool
	 */
	var $standalone = false;

	/**
	 * Current state of the state machine
	 *
	 * @access private
	 * @var string
	 */
	var $state = 'before_version_name';

	/**
	 * Input data
	 *
	 * @access private
	 * @var string
	 */
	var $data = '';

	/**
	 * Input data length (to avoid calling strlen() everytime this is needed)
	 *
	 * @access private
	 * @var int
	 */
	var $data_length = 0;

	/**
	 * Current position of the pointer
	 *
	 * @var int
	 * @access private
	 */
	var $position = 0;

	/**
	 * Create an instance of the class with the input data
	 *
	 * @access public
	 * @param string $data Input data
	 */
	public function __construct($data)
	{
		$this->data = $data;
		$this->data_length = strlen($this->data);
	}

	/**
	 * Parse the input data
	 *
	 * @access public
	 * @return bool true on success, false on failure
	 */
	public function parse()
	{
		while ($this->state && $this->state !== 'emit' && $this->has_data())
		{
			$state = $this->state;
			$this->$state();
		}
		$this->data = '';
		if ($this->state === 'emit')
		{
			return true;
		}
		else
		{
			$this->version = '';
			$this->encoding = '';
			$this->standalone = '';
			return false;
		}
	}

	/**
	 * Check whether there is data beyond the pointer
	 *
	 * @access private
	 * @return bool true if there is further data, false if not
	 */
	public function has_data()
	{
		return (bool) ($this->position < $this->data_length);
	}

	/**
	 * Advance past any whitespace
	 *
	 * @return int Number of whitespace characters passed
	 */
	public function skip_whitespace()
	{
		$whitespace = strspn($this->data, "\x09\x0A\x0D\x20", $this->position);
		$this->position += $whitespace;
		return $whitespace;
	}

	/**
	 * Read value
	 */
	public function get_value()
	{
		$quote = substr($this->data, $this->position, 1);
		if ($quote === '"' || $quote === "'")
		{
			$this->position++;
			$len = strcspn($this->data, $quote, $this->position);
			if ($this->has_data())
			{
				$value = substr($this->data, $this->position, $len);
				$this->position += $len + 1;
				return $value;
			}
		}
		return false;
	}

	public function before_version_name()
	{
		if ($this->skip_whitespace())
		{
			$this->state = 'version_name';
		}
		else
		{
			$this->state = false;
		}
	}

	public function version_name()
	{
		if (substr($this->data, $this->position, 7) === 'version')
		{
			$this->position += 7;
			$this->skip_whitespace();
			$this->state = 'version_equals';
		}
		else
		{
			$this->state = false;
		}
	}

	public function version_equals()
	{
		if (substr($this->data, $this->position, 1) === '=')
		{
			$this->position++;
			$this->skip_whitespace();
			$this->state = 'version_value';
		}
		else
		{
			$this->state = false;
		}
	}

	public function version_value()
	{
		if ($this->version = $this->get_value())
		{
			$this->skip_whitespace();
			if ($this->has_data())
			{
				$this->state = 'encoding_name';
			}
			else
			{
				$this->state = 'emit';
			}
		}
		else
		{
			$this->state = false;
		}
	}

	public function encoding_name()
	{
		if (substr($this->data, $this->position, 8) === 'encoding')
		{
			$this->position += 8;
			$this->skip_whitespace();
			$this->state = 'encoding_equals';
		}
		else
		{
			$this->state = 'standalone_name';
		}
	}

	public function encoding_equals()
	{
		if (substr($this->data, $this->position, 1) === '=')
		{
			$this->position++;
			$this->skip_whitespace();
			$this->state = 'encoding_value';
		}
		else
		{
			$this->state = false;
		}
	}

	public function encoding_value()
	{
		if ($this->encoding = $this->get_value())
		{
			$this->skip_whitespace();
			if ($this->has_data())
			{
				$this->state = 'standalone_name';
			}
			else
			{
				$this->state = 'emit';
			}
		}
		else
		{
			$this->state = false;
		}
	}

	public function standalone_name()
	{
		if (substr($this->data, $this->position, 10) === 'standalone')
		{
			$this->position += 10;
			$this->skip_whitespace();
			$this->state = 'standalone_equals';
		}
		else
		{
			$this->state = false;
		}
	}

	public function standalone_equals()
	{
		if (substr($this->data, $this->position, 1) === '=')
		{
			$this->position++;
			$this->skip_whitespace();
			$this->state = 'standalone_value';
		}
		else
		{
			$this->state = false;
		}
	}

	public function standalone_value()
	{
		if ($standalone = $this->get_value())
		{
			switch ($standalone)
			{
				case 'yes':
					$this->standalone = true;
					break;

				case 'no':
					$this->standalone = false;
					break;

				default:
					$this->state = false;
					return;
			}

			$this->skip_whitespace();
			if ($this->has_data())
			{
				$this->state = false;
			}
			else
			{
				$this->state = 'emit';
			}
		}
		else
		{
			$this->state = false;
		}
	}
}
