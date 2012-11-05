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
 * Decode HTML Entities
 *
 * This implements HTML5 as of revision 967 (2007-06-28)
 *
 * @deprecated Use DOMDocument instead!
 * @package SimplePie
 */
class SimplePie_Decode_HTML_Entities
{
	/**
	 * Data to be parsed
	 *
	 * @access private
	 * @var string
	 */
	var $data = '';

	/**
	 * Currently consumed bytes
	 *
	 * @access private
	 * @var string
	 */
	var $consumed = '';

	/**
	 * Position of the current byte being parsed
	 *
	 * @access private
	 * @var int
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
	}

	/**
	 * Parse the input data
	 *
	 * @access public
	 * @return string Output data
	 */
	public function parse()
	{
		while (($this->position = strpos($this->data, '&', $this->position)) !== false)
		{
			$this->consume();
			$this->entity();
			$this->consumed = '';
		}
		return $this->data;
	}

	/**
	 * Consume the next byte
	 *
	 * @access private
	 * @return mixed The next byte, or false, if there is no more data
	 */
	public function consume()
	{
		if (isset($this->data[$this->position]))
		{
			$this->consumed .= $this->data[$this->position];
			return $this->data[$this->position++];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Consume a range of characters
	 *
	 * @access private
	 * @param string $chars Characters to consume
	 * @return mixed A series of characters that match the range, or false
	 */
	public function consume_range($chars)
	{
		if ($len = strspn($this->data, $chars, $this->position))
		{
			$data = substr($this->data, $this->position, $len);
			$this->consumed .= $data;
			$this->position += $len;
			return $data;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Unconsume one byte
	 *
	 * @access private
	 */
	public function unconsume()
	{
		$this->consumed = substr($this->consumed, 0, -1);
		$this->position--;
	}

	/**
	 * Decode an entity
	 *
	 * @access private
	 */
	public function entity()
	{
		switch ($this->consume())
		{
			case "\x09":
			case "\x0A":
			case "\x0B":
			case "\x0B":
			case "\x0C":
			case "\x20":
			case "\x3C":
			case "\x26":
			case false:
				break;

			case "\x23":
				switch ($this->consume())
				{
					case "\x78":
					case "\x58":
						$range = '0123456789ABCDEFabcdef';
						$hex = true;
						break;

					default:
						$range = '0123456789';
						$hex = false;
						$this->unconsume();
						break;
				}

				if ($codepoint = $this->consume_range($range))
				{
					static $windows_1252_specials = array(0x0D => "\x0A", 0x80 => "\xE2\x82\xAC", 0x81 => "\xEF\xBF\xBD", 0x82 => "\xE2\x80\x9A", 0x83 => "\xC6\x92", 0x84 => "\xE2\x80\x9E", 0x85 => "\xE2\x80\xA6", 0x86 => "\xE2\x80\xA0", 0x87 => "\xE2\x80\xA1", 0x88 => "\xCB\x86", 0x89 => "\xE2\x80\xB0", 0x8A => "\xC5\xA0", 0x8B => "\xE2\x80\xB9", 0x8C => "\xC5\x92", 0x8D => "\xEF\xBF\xBD", 0x8E => "\xC5\xBD", 0x8F => "\xEF\xBF\xBD", 0x90 => "\xEF\xBF\xBD", 0x91 => "\xE2\x80\x98", 0x92 => "\xE2\x80\x99", 0x93 => "\xE2\x80\x9C", 0x94 => "\xE2\x80\x9D", 0x95 => "\xE2\x80\xA2", 0x96 => "\xE2\x80\x93", 0x97 => "\xE2\x80\x94", 0x98 => "\xCB\x9C", 0x99 => "\xE2\x84\xA2", 0x9A => "\xC5\xA1", 0x9B => "\xE2\x80\xBA", 0x9C => "\xC5\x93", 0x9D => "\xEF\xBF\xBD", 0x9E => "\xC5\xBE", 0x9F => "\xC5\xB8");

					if ($hex)
					{
						$codepoint = hexdec($codepoint);
					}
					else
					{
						$codepoint = intval($codepoint);
					}

					if (isset($windows_1252_specials[$codepoint]))
					{
						$replacement = $windows_1252_specials[$codepoint];
					}
					else
					{
						$replacement = SimplePie_Misc::codepoint_to_utf8($codepoint);
					}

					if (!in_array($this->consume(), array(';', false), true))
					{
						$this->unconsume();
					}

					$consumed_length = strlen($this->consumed);
					$this->data = substr_replace($this->data, $replacement, $this->position - $consumed_length, $consumed_length);
					$this->position += strlen($replacement) - $consumed_length;
				}
				break;

			default:
				static $entities = array(
					'Aacute' => "\xC3\x81",
					'aacute' => "\xC3\xA1",
					'Aacute;' => "\xC3\x81",
					'aacute;' => "\xC3\xA1",
					'Acirc' => "\xC3\x82",
					'acirc' => "\xC3\xA2",
					'Acirc;' => "\xC3\x82",
					'acirc;' => "\xC3\xA2",
					'acute' => "\xC2\xB4",
					'acute;' => "\xC2\xB4",
					'AElig' => "\xC3\x86",
					'aelig' => "\xC3\xA6",
					'AElig;' => "\xC3\x86",
					'aelig;' => "\xC3\xA6",
					'Agrave' => "\xC3\x80",
					'agrave' => "\xC3\xA0",
					'Agrave;' => "\xC3\x80",
					'agrave;' => "\xC3\xA0",
					'alefsym;' => "\xE2\x84\xB5",
					'Alpha;' => "\xCE\x91",
					'alpha;' => "\xCE\xB1",
					'AMP' => "\x26",
					'amp' => "\x26",
					'AMP;' => "\x26",
					'amp;' => "\x26",
					'and;' => "\xE2\x88\xA7",
					'ang;' => "\xE2\x88\xA0",
					'apos;' => "\x27",
					'Aring' => "\xC3\x85",
					'aring' => "\xC3\xA5",
					'Aring;' => "\xC3\x85",
					'aring;' => "\xC3\xA5",
					'asymp;' => "\xE2\x89\x88",
					'Atilde' => "\xC3\x83",
					'atilde' => "\xC3\xA3",
					'Atilde;' => "\xC3\x83",
					'atilde;' => "\xC3\xA3",
					'Auml' => "\xC3\x84",
					'auml' => "\xC3\xA4",
					'Auml;' => "\xC3\x84",
					'auml;' => "\xC3\xA4",
					'bdquo;' => "\xE2\x80\x9E",
					'Beta;' => "\xCE\x92",
					'beta;' => "\xCE\xB2",
					'brvbar' => "\xC2\xA6",
					'brvbar;' => "\xC2\xA6",
					'bull;' => "\xE2\x80\xA2",
					'cap;' => "\xE2\x88\xA9",
					'Ccedil' => "\xC3\x87",
					'ccedil' => "\xC3\xA7",
					'Ccedil;' => "\xC3\x87",
					'ccedil;' => "\xC3\xA7",
					'cedil' => "\xC2\xB8",
					'cedil;' => "\xC2\xB8",
					'cent' => "\xC2\xA2",
					'cent;' => "\xC2\xA2",
					'Chi;' => "\xCE\xA7",
					'chi;' => "\xCF\x87",
					'circ;' => "\xCB\x86",
					'clubs;' => "\xE2\x99\xA3",
					'cong;' => "\xE2\x89\x85",
					'COPY' => "\xC2\xA9",
					'copy' => "\xC2\xA9",
					'COPY;' => "\xC2\xA9",
					'copy;' => "\xC2\xA9",
					'crarr;' => "\xE2\x86\xB5",
					'cup;' => "\xE2\x88\xAA",
					'curren' => "\xC2\xA4",
					'curren;' => "\xC2\xA4",
					'Dagger;' => "\xE2\x80\xA1",
					'dagger;' => "\xE2\x80\xA0",
					'dArr;' => "\xE2\x87\x93",
					'darr;' => "\xE2\x86\x93",
					'deg' => "\xC2\xB0",
					'deg;' => "\xC2\xB0",
					'Delta;' => "\xCE\x94",
					'delta;' => "\xCE\xB4",
					'diams;' => "\xE2\x99\xA6",
					'divide' => "\xC3\xB7",
					'divide;' => "\xC3\xB7",
					'Eacute' => "\xC3\x89",
					'eacute' => "\xC3\xA9",
					'Eacute;' => "\xC3\x89",
					'eacute;' => "\xC3\xA9",
					'Ecirc' => "\xC3\x8A",
					'ecirc' => "\xC3\xAA",
					'Ecirc;' => "\xC3\x8A",
					'ecirc;' => "\xC3\xAA",
					'Egrave' => "\xC3\x88",
					'egrave' => "\xC3\xA8",
					'Egrave;' => "\xC3\x88",
					'egrave;' => "\xC3\xA8",
					'empty;' => "\xE2\x88\x85",
					'emsp;' => "\xE2\x80\x83",
					'ensp;' => "\xE2\x80\x82",
					'Epsilon;' => "\xCE\x95",
					'epsilon;' => "\xCE\xB5",
					'equiv;' => "\xE2\x89\xA1",
					'Eta;' => "\xCE\x97",
					'eta;' => "\xCE\xB7",
					'ETH' => "\xC3\x90",
					'eth' => "\xC3\xB0",
					'ETH;' => "\xC3\x90",
					'eth;' => "\xC3\xB0",
					'Euml' => "\xC3\x8B",
					'euml' => "\xC3\xAB",
					'Euml;' => "\xC3\x8B",
					'euml;' => "\xC3\xAB",
					'euro;' => "\xE2\x82\xAC",
					'exist;' => "\xE2\x88\x83",
					'fnof;' => "\xC6\x92",
					'forall;' => "\xE2\x88\x80",
					'frac12' => "\xC2\xBD",
					'frac12;' => "\xC2\xBD",
					'frac14' => "\xC2\xBC",
					'frac14;' => "\xC2\xBC",
					'frac34' => "\xC2\xBE",
					'frac34;' => "\xC2\xBE",
					'frasl;' => "\xE2\x81\x84",
					'Gamma;' => "\xCE\x93",
					'gamma;' => "\xCE\xB3",
					'ge;' => "\xE2\x89\xA5",
					'GT' => "\x3E",
					'gt' => "\x3E",
					'GT;' => "\x3E",
					'gt;' => "\x3E",
					'hArr;' => "\xE2\x87\x94",
					'harr;' => "\xE2\x86\x94",
					'hearts;' => "\xE2\x99\xA5",
					'hellip;' => "\xE2\x80\xA6",
					'Iacute' => "\xC3\x8D",
					'iacute' => "\xC3\xAD",
					'Iacute;' => "\xC3\x8D",
					'iacute;' => "\xC3\xAD",
					'Icirc' => "\xC3\x8E",
					'icirc' => "\xC3\xAE",
					'Icirc;' => "\xC3\x8E",
					'icirc;' => "\xC3\xAE",
					'iexcl' => "\xC2\xA1",
					'iexcl;' => "\xC2\xA1",
					'Igrave' => "\xC3\x8C",
					'igrave' => "\xC3\xAC",
					'Igrave;' => "\xC3\x8C",
					'igrave;' => "\xC3\xAC",
					'image;' => "\xE2\x84\x91",
					'infin;' => "\xE2\x88\x9E",
					'int;' => "\xE2\x88\xAB",
					'Iota;' => "\xCE\x99",
					'iota;' => "\xCE\xB9",
					'iquest' => "\xC2\xBF",
					'iquest;' => "\xC2\xBF",
					'isin;' => "\xE2\x88\x88",
					'Iuml' => "\xC3\x8F",
					'iuml' => "\xC3\xAF",
					'Iuml;' => "\xC3\x8F",
					'iuml;' => "\xC3\xAF",
					'Kappa;' => "\xCE\x9A",
					'kappa;' => "\xCE\xBA",
					'Lambda;' => "\xCE\x9B",
					'lambda;' => "\xCE\xBB",
					'lang;' => "\xE3\x80\x88",
					'laquo' => "\xC2\xAB",
					'laquo;' => "\xC2\xAB",
					'lArr;' => "\xE2\x87\x90",
					'larr;' => "\xE2\x86\x90",
					'lceil;' => "\xE2\x8C\x88",
					'ldquo;' => "\xE2\x80\x9C",
					'le;' => "\xE2\x89\xA4",
					'lfloor;' => "\xE2\x8C\x8A",
					'lowast;' => "\xE2\x88\x97",
					'loz;' => "\xE2\x97\x8A",
					'lrm;' => "\xE2\x80\x8E",
					'lsaquo;' => "\xE2\x80\xB9",
					'lsquo;' => "\xE2\x80\x98",
					'LT' => "\x3C",
					'lt' => "\x3C",
					'LT;' => "\x3C",
					'lt;' => "\x3C",
					'macr' => "\xC2\xAF",
					'macr;' => "\xC2\xAF",
					'mdash;' => "\xE2\x80\x94",
					'micro' => "\xC2\xB5",
					'micro;' => "\xC2\xB5",
					'middot' => "\xC2\xB7",
					'middot;' => "\xC2\xB7",
					'minus;' => "\xE2\x88\x92",
					'Mu;' => "\xCE\x9C",
					'mu;' => "\xCE\xBC",
					'nabla;' => "\xE2\x88\x87",
					'nbsp' => "\xC2\xA0",
					'nbsp;' => "\xC2\xA0",
					'ndash;' => "\xE2\x80\x93",
					'ne;' => "\xE2\x89\xA0",
					'ni;' => "\xE2\x88\x8B",
					'not' => "\xC2\xAC",
					'not;' => "\xC2\xAC",
					'notin;' => "\xE2\x88\x89",
					'nsub;' => "\xE2\x8A\x84",
					'Ntilde' => "\xC3\x91",
					'ntilde' => "\xC3\xB1",
					'Ntilde;' => "\xC3\x91",
					'ntilde;' => "\xC3\xB1",
					'Nu;' => "\xCE\x9D",
					'nu;' => "\xCE\xBD",
					'Oacute' => "\xC3\x93",
					'oacute' => "\xC3\xB3",
					'Oacute;' => "\xC3\x93",
					'oacute;' => "\xC3\xB3",
					'Ocirc' => "\xC3\x94",
					'ocirc' => "\xC3\xB4",
					'Ocirc;' => "\xC3\x94",
					'ocirc;' => "\xC3\xB4",
					'OElig;' => "\xC5\x92",
					'oelig;' => "\xC5\x93",
					'Ograve' => "\xC3\x92",
					'ograve' => "\xC3\xB2",
					'Ograve;' => "\xC3\x92",
					'ograve;' => "\xC3\xB2",
					'oline;' => "\xE2\x80\xBE",
					'Omega;' => "\xCE\xA9",
					'omega;' => "\xCF\x89",
					'Omicron;' => "\xCE\x9F",
					'omicron;' => "\xCE\xBF",
					'oplus;' => "\xE2\x8A\x95",
					'or;' => "\xE2\x88\xA8",
					'ordf' => "\xC2\xAA",
					'ordf;' => "\xC2\xAA",
					'ordm' => "\xC2\xBA",
					'ordm;' => "\xC2\xBA",
					'Oslash' => "\xC3\x98",
					'oslash' => "\xC3\xB8",
					'Oslash;' => "\xC3\x98",
					'oslash;' => "\xC3\xB8",
					'Otilde' => "\xC3\x95",
					'otilde' => "\xC3\xB5",
					'Otilde;' => "\xC3\x95",
					'otilde;' => "\xC3\xB5",
					'otimes;' => "\xE2\x8A\x97",
					'Ouml' => "\xC3\x96",
					'ouml' => "\xC3\xB6",
					'Ouml;' => "\xC3\x96",
					'ouml;' => "\xC3\xB6",
					'para' => "\xC2\xB6",
					'para;' => "\xC2\xB6",
					'part;' => "\xE2\x88\x82",
					'permil;' => "\xE2\x80\xB0",
					'perp;' => "\xE2\x8A\xA5",
					'Phi;' => "\xCE\xA6",
					'phi;' => "\xCF\x86",
					'Pi;' => "\xCE\xA0",
					'pi;' => "\xCF\x80",
					'piv;' => "\xCF\x96",
					'plusmn' => "\xC2\xB1",
					'plusmn;' => "\xC2\xB1",
					'pound' => "\xC2\xA3",
					'pound;' => "\xC2\xA3",
					'Prime;' => "\xE2\x80\xB3",
					'prime;' => "\xE2\x80\xB2",
					'prod;' => "\xE2\x88\x8F",
					'prop;' => "\xE2\x88\x9D",
					'Psi;' => "\xCE\xA8",
					'psi;' => "\xCF\x88",
					'QUOT' => "\x22",
					'quot' => "\x22",
					'QUOT;' => "\x22",
					'quot;' => "\x22",
					'radic;' => "\xE2\x88\x9A",
					'rang;' => "\xE3\x80\x89",
					'raquo' => "\xC2\xBB",
					'raquo;' => "\xC2\xBB",
					'rArr;' => "\xE2\x87\x92",
					'rarr;' => "\xE2\x86\x92",
					'rceil;' => "\xE2\x8C\x89",
					'rdquo;' => "\xE2\x80\x9D",
					'real;' => "\xE2\x84\x9C",
					'REG' => "\xC2\xAE",
					'reg' => "\xC2\xAE",
					'REG;' => "\xC2\xAE",
					'reg;' => "\xC2\xAE",
					'rfloor;' => "\xE2\x8C\x8B",
					'Rho;' => "\xCE\xA1",
					'rho;' => "\xCF\x81",
					'rlm;' => "\xE2\x80\x8F",
					'rsaquo;' => "\xE2\x80\xBA",
					'rsquo;' => "\xE2\x80\x99",
					'sbquo;' => "\xE2\x80\x9A",
					'Scaron;' => "\xC5\xA0",
					'scaron;' => "\xC5\xA1",
					'sdot;' => "\xE2\x8B\x85",
					'sect' => "\xC2\xA7",
					'sect;' => "\xC2\xA7",
					'shy' => "\xC2\xAD",
					'shy;' => "\xC2\xAD",
					'Sigma;' => "\xCE\xA3",
					'sigma;' => "\xCF\x83",
					'sigmaf;' => "\xCF\x82",
					'sim;' => "\xE2\x88\xBC",
					'spades;' => "\xE2\x99\xA0",
					'sub;' => "\xE2\x8A\x82",
					'sube;' => "\xE2\x8A\x86",
					'sum;' => "\xE2\x88\x91",
					'sup;' => "\xE2\x8A\x83",
					'sup1' => "\xC2\xB9",
					'sup1;' => "\xC2\xB9",
					'sup2' => "\xC2\xB2",
					'sup2;' => "\xC2\xB2",
					'sup3' => "\xC2\xB3",
					'sup3;' => "\xC2\xB3",
					'supe;' => "\xE2\x8A\x87",
					'szlig' => "\xC3\x9F",
					'szlig;' => "\xC3\x9F",
					'Tau;' => "\xCE\xA4",
					'tau;' => "\xCF\x84",
					'there4;' => "\xE2\x88\xB4",
					'Theta;' => "\xCE\x98",
					'theta;' => "\xCE\xB8",
					'thetasym;' => "\xCF\x91",
					'thinsp;' => "\xE2\x80\x89",
					'THORN' => "\xC3\x9E",
					'thorn' => "\xC3\xBE",
					'THORN;' => "\xC3\x9E",
					'thorn;' => "\xC3\xBE",
					'tilde;' => "\xCB\x9C",
					'times' => "\xC3\x97",
					'times;' => "\xC3\x97",
					'TRADE;' => "\xE2\x84\xA2",
					'trade;' => "\xE2\x84\xA2",
					'Uacute' => "\xC3\x9A",
					'uacute' => "\xC3\xBA",
					'Uacute;' => "\xC3\x9A",
					'uacute;' => "\xC3\xBA",
					'uArr;' => "\xE2\x87\x91",
					'uarr;' => "\xE2\x86\x91",
					'Ucirc' => "\xC3\x9B",
					'ucirc' => "\xC3\xBB",
					'Ucirc;' => "\xC3\x9B",
					'ucirc;' => "\xC3\xBB",
					'Ugrave' => "\xC3\x99",
					'ugrave' => "\xC3\xB9",
					'Ugrave;' => "\xC3\x99",
					'ugrave;' => "\xC3\xB9",
					'uml' => "\xC2\xA8",
					'uml;' => "\xC2\xA8",
					'upsih;' => "\xCF\x92",
					'Upsilon;' => "\xCE\xA5",
					'upsilon;' => "\xCF\x85",
					'Uuml' => "\xC3\x9C",
					'uuml' => "\xC3\xBC",
					'Uuml;' => "\xC3\x9C",
					'uuml;' => "\xC3\xBC",
					'weierp;' => "\xE2\x84\x98",
					'Xi;' => "\xCE\x9E",
					'xi;' => "\xCE\xBE",
					'Yacute' => "\xC3\x9D",
					'yacute' => "\xC3\xBD",
					'Yacute;' => "\xC3\x9D",
					'yacute;' => "\xC3\xBD",
					'yen' => "\xC2\xA5",
					'yen;' => "\xC2\xA5",
					'yuml' => "\xC3\xBF",
					'Yuml;' => "\xC5\xB8",
					'yuml;' => "\xC3\xBF",
					'Zeta;' => "\xCE\x96",
					'zeta;' => "\xCE\xB6",
					'zwj;' => "\xE2\x80\x8D",
					'zwnj;' => "\xE2\x80\x8C"
				);

				for ($i = 0, $match = null; $i < 9 && $this->consume() !== false; $i++)
				{
					$consumed = substr($this->consumed, 1);
					if (isset($entities[$consumed]))
					{
						$match = $consumed;
					}
				}

				if ($match !== null)
				{
 					$this->data = substr_replace($this->data, $entities[$match], $this->position - strlen($consumed) - 1, strlen($match) + 1);
					$this->position += strlen($entities[$match]) - strlen($consumed) - 1;
				}
				break;
		}
	}
}

