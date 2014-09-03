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
 * Handles `<media:text>` captions as defined in Media RSS.
 *
 * Used by {@see SimplePie_Enclosure::get_caption()} and {@see SimplePie_Enclosure::get_captions()}
 *
 * This class can be overloaded with {@see SimplePie::set_caption_class()}
 *
 * @package SimplePie
 * @subpackage API
 */
class SimplePie_Caption
{
	/**
	 * Content type
	 *
	 * @var string
	 * @see get_type()
	 */
	var $type;

	/**
	 * Language
	 *
	 * @var string
	 * @see get_language()
	 */
	var $lang;

	/**
	 * Start time
	 *
	 * @var string
	 * @see get_starttime()
	 */
	var $startTime;

	/**
	 * End time
	 *
	 * @var string
	 * @see get_endtime()
	 */
	var $endTime;

	/**
	 * Caption text
	 *
	 * @var string
	 * @see get_text()
	 */
	var $text;

	/**
	 * Constructor, used to input the data
	 *
	 * For documentation on all the parameters, see the corresponding
	 * properties and their accessors
	 */
	public function __construct($type = null, $lang = null, $startTime = null, $endTime = null, $text = null)
	{
		$this->type = $type;
		$this->lang = $lang;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->text = $text;
	}

	/**
	 * String-ified version
	 *
	 * @return string
	 */
	public function __toString()
	{
		// There is no $this->data here
		return md5(serialize($this));
	}

	/**
	 * Get the end time
	 *
	 * @return string|null Time in the format 'hh:mm:ss.SSS'
	 */
	public function get_endtime()
	{
		if ($this->endTime !== null)
		{
			return $this->endTime;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the language
	 *
	 * @link http://tools.ietf.org/html/rfc3066
	 * @return string|null Language code as per RFC 3066
	 */
	public function get_language()
	{
		if ($this->lang !== null)
		{
			return $this->lang;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the start time
	 *
	 * @return string|null Time in the format 'hh:mm:ss.SSS'
	 */
	public function get_starttime()
	{
		if ($this->startTime !== null)
		{
			return $this->startTime;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the text of the caption
	 *
	 * @return string|null
	 */
	public function get_text()
	{
		if ($this->text !== null)
		{
			return $this->text;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the content type (not MIME type)
	 *
	 * @return string|null Either 'text' or 'html'
	 */
	public function get_type()
	{
		if ($this->type !== null)
		{
			return $this->type;
		}
		else
		{
			return null;
		}
	}
}

