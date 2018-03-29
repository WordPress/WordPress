<?php
/**
 * Diff
 *
 * A comprehensive library for generating differences between two strings
 * in multiple formats (unified, side by side HTML etc)
 *
 * PHP version 5
 *
 * Copyright (c) 2009 Chris Boulton <chris.boulton@interspire.com>
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the Chris Boulton nor the names of its contributors 
 *    may be used to endorse or promote products derived from this software 
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Diff
 * @author Chris Boulton <chris.boulton@interspire.com>
 * @copyright (c) 2009 Chris Boulton
 * @license New BSD License http://www.opensource.org/licenses/bsd-license.php
 * @version 1.1
 * @link http://github.com/chrisboulton/php-diff
 */

class Diff
{
	/**
	 * @var array The "old" sequence to use as the basis for the comparison.
	 */
	private $a = null;

	/**
	 * @var array The "new" sequence to generate the changes for.
	 */
	private $b = null;

	/**
	 * @var array Array containing the generated opcodes for the differences between the two items.
	 */
	private $groupedCodes = null;

	/**
	 * @var array Associative array of the default options available for the diff class and their default value.
	 */
	private $defaultOptions = array(
		'context' => 3,
		'ignoreNewLines' => false,
		'ignoreWhitespace' => false,
		'ignoreCase' => false
	);

	/**
	 * @var array Array of the options that have been applied for generating the diff.
	 */
	private $options = array();

	/**
	 * The constructor.
	 *
	 * @param array $a Array containing the lines of the first string to compare.
	 * @param array $b Array containing the lines for the second string to compare.
	 */
	public function __construct($a, $b, $options=array())
	{
		$this->a = $a;
		$this->b = $b;

		$this->options = array_merge($this->defaultOptions, $options);
	}

	/**
	 * Render a diff using the supplied rendering class and return it.
	 *
	 * @param object $renderer An instance of the rendering object to use for generating the diff.
	 * @return mixed The generated diff. Exact return value depends on the rendered.
	 */
	public function render(Diff_Renderer_Abstract $renderer)
	{
		$renderer->diff = $this;
		return $renderer->render();
	}

	/**
	 * Get a range of lines from $start to $end from the first comparison string
	 * and return them as an array. If no values are supplied, the entire string
	 * is returned. It's also possible to specify just one line to return only
	 * that line.
	 *
	 * @param int $start The starting number.
	 * @param int $end The ending number. If not supplied, only the item in $start will be returned.
	 * @return array Array of all of the lines between the specified range.
	 */
	public function getA($start=0, $end=null)
	{
		if($start == 0 && $end === null) {
			return $this->a;
		}

		if($end === null) {
			$length = 1;
		}
		else {
			$length = $end - $start;
		}

		return array_slice($this->a, $start, $length);

	}

	/**
	 * Get a range of lines from $start to $end from the second comparison string
	 * and return them as an array. If no values are supplied, the entire string
	 * is returned. It's also possible to specify just one line to return only
	 * that line.
	 *
	 * @param int $start The starting number.
	 * @param int $end The ending number. If not supplied, only the item in $start will be returned.
	 * @return array Array of all of the lines between the specified range.
	 */
	public function getB($start=0, $end=null)
	{
		if($start == 0 && $end === null) {
			return $this->b;
		}

		if($end === null) {
			$length = 1;
		}
		else {
			$length = $end - $start;
		}

		return array_slice($this->b, $start, $length);
	}

	/**
	 * Generate a list of the compiled and grouped opcodes for the differences between the
	 * two strings. Generally called by the renderer, this class instantiates the sequence
	 * matcher and performs the actual diff generation and return an array of the opcodes
	 * for it. Once generated, the results are cached in the diff class instance.
	 *
	 * @return array Array of the grouped opcodes for the generated diff.
	 */
	public function getGroupedOpcodes()
	{
		if(!is_null($this->groupedCodes)) {
			return $this->groupedCodes;
		}

		require_once dirname(__FILE__).'/Diff/SequenceMatcher.php';
		$sequenceMatcher = new Diff_SequenceMatcher($this->a, $this->b, null, $this->options);
		$this->groupedCodes = $sequenceMatcher->getGroupedOpcodes();
		return $this->groupedCodes;
	}
}
