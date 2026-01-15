<?php
/**
 * General API for generating and formatting diffs - the differences between
 * two sequences of strings.
 *
 * The original PHP version of this code was written by Geoffrey T. Dairiki
 * <dairiki@dairiki.org>, and is used/adapted with his permission.
 *
 * Copyright 2004 Geoffrey T. Dairiki <dairiki@dairiki.org>
 * Copyright 2004-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see https://opensource.org/license/lgpl-2-1/.
 *
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 */
class Text_Diff {

    /**
     * Array of changes.
     *
     * @var array
     */
    var $_edits;

    /**
     * Computes diffs between sequences of strings.
     *
     * @param string $engine     Name of the diffing engine to use.  'auto'
     *                           will automatically select the best.
     * @param array $params      Parameters to pass to the diffing engine.
     *                           Normally an array of two arrays, each
     *                           containing the lines from a file.
     */
    function __construct( $engine, $params )
    {
        // Backward compatibility workaround.
        if (!is_string($engine)) {
            $params = array($engine, $params);
            $engine = 'auto';
        }

        if ($engine == 'auto') {
            $engine = extension_loaded('xdiff') ? 'xdiff' : 'native';
        } else {
            $engine = basename($engine);
        }

        // WP #7391
        require_once dirname(__FILE__).'/Diff/Engine/' . $engine . '.php';
        $class = 'Text_Diff_Engine_' . $engine;
        $diff_engine = new $class();

        $this->_edits = call_user_func_array(array($diff_engine, 'diff'), $params);
    }

	/**
	 * PHP4 constructor.
	 */
	public function Text_Diff( $engine, $params ) {
		self::__construct( $engine, $params );
	}

    /**
     * Returns the array of differences.
     */
    function getDiff()
    {
        return $this->_edits;
    }

    /**
     * returns the number of new (added) lines in a given diff.
     *
     * @since Text_Diff 1.1.0
     *
     * @return int The number of new lines
     */
    function countAddedLines()
    {
        $count = 0;
        foreach ($this->_edits as $edit) {
            if (is_a($edit, 'Text_Diff_Op_add') ||
                is_a($edit, 'Text_Diff_Op_change')) {
                $count += $edit->nfinal();
            }
        }
        return $count;
    }

    /**
     * Returns the number of deleted (removed) lines in a given diff.
     *
     * @since Text_Diff 1.1.0
     *
     * @return int The number of deleted lines
     */
    function countDeletedLines()
    {
        $count = 0;
        foreach ($this->_edits as $edit) {
            if (is_a($edit, 'Text_Diff_Op_delete') ||
                is_a($edit, 'Text_Diff_Op_change')) {
                $count += $edit->norig();
            }
        }
        return $count;
    }

    /**
     * Computes a reversed diff.
     *
     * Example:
     * <code>
     * $diff = new Text_Diff($lines1, $lines2);
     * $rev = $diff->reverse();
     * </code>
     *
     * @return Text_Diff  A Diff object representing the inverse of the
     *                    original diff.  Note that we purposely don't return a
     *                    reference here, since this essentially is a clone()
     *                    method.
     */
    function reverse()
    {
        if (version_compare(zend_version(), '2', '>')) {
            $rev = clone($this);
        } else {
            $rev = $this;
        }
        $rev->_edits = array();
        foreach ($this->_edits as $edit) {
            $rev->_edits[] = $edit->reverse();
        }
        return $rev;
    }

    /**
     * Checks for an empty diff.
     *
     * @return bool True if two sequences were identical.
     */
    function isEmpty()
    {
        foreach ($this->_edits as $edit) {
            if (!is_a($edit, 'Text_Diff_Op_copy')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Computes the length of the Longest Common Subsequence (LCS).
     *
     * This is mostly for diagnostic purposes.
     *
     * @return int The length of the LCS.
     */
    function lcs()
    {
        $lcs = 0;
        foreach ($this->_edits as $edit) {
            if (is_a($edit, 'Text_Diff_Op_copy')) {
                $lcs += count($edit->orig);
            }
        }
        return $lcs;
    }

    /**
     * Gets the original set of lines.
     *
     * This reconstructs the $from_lines parameter passed to the constructor.
     *
     * @return array  The original sequence of strings.
     */
    function getOriginal()
    {
        $lines = array();
        foreach ($this->_edits as $edit) {
            if ($edit->orig) {
                array_splice($lines, count($lines), 0, $edit->orig);
            }
        }
        return $lines;
    }

    /**
     * Gets the final set of lines.
     *
     * This reconstructs the $to_lines parameter passed to the constructor.
     *
     * @return array  The sequence of strings.
     */
    function getFinal()
    {
        $lines = array();
        foreach ($this->_edits as $edit) {
            if ($edit->final) {
                array_splice($lines, count($lines), 0, $edit->final);
            }
        }
        return $lines;
    }

    /**
     * Removes trailing newlines from a line of text. This is meant to be used
     * with array_walk().
     *
     * @param string $line  The line to trim.
     * @param int    $key   The index of the line in the array. Not used.
     */
    static function trimNewlines(&$line, $key)
    {
        $line = str_replace(array("\n", "\r"), '', $line);
    }

    /**
     * Determines the location of the system temporary directory.
     *
     * @access protected
     *
     * @return string  A directory name which can be used for temp files.
     */
    static function _getTempDir()
    {
        return get_temp_dir();
    }

    /**
     * Checks a diff for validity.
     *
     * This is here only for debugging purposes.
     */
    function _check($from_lines, $to_lines)
    {
        if (serialize($from_lines) != serialize($this->getOriginal())) {
            throw new Text_Exception("Reconstructed original does not match");
        }
        if (serialize($to_lines) != serialize($this->getFinal())) {
            throw new Text_Exception("Reconstructed final does not match");
        }

        $rev = $this->reverse();
        if (serialize($to_lines) != serialize($rev->getOriginal())) {
            throw new Text_Exception("Reversed original does not match");
        }
        if (serialize($from_lines) != serialize($rev->getFinal())) {
            throw new Text_Exception("Reversed final does not match");
        }

        $prevtype = null;
        foreach ($this->_edits as $edit) {
            if ($prevtype !== null && $edit instanceof $prevtype) {
                throw new Text_Exception("Edit sequence is non-optimal");
            }
            $prevtype = get_class($edit);
        }

        return true;
    }

}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 */
class Text_MappedDiff extends Text_Diff {

    /**
     * Computes a diff between sequences of strings.
     *
     * This can be used to compute things like case-insensitive diffs, or diffs
     * which ignore changes in white-space.
     *
     * @param array $from_lines         An array of strings.
     * @param array $to_lines           An array of strings.
     * @param array $mapped_from_lines  This array should have the same size
     *                                  number of elements as $from_lines.  The
     *                                  elements in $mapped_from_lines and
     *                                  $mapped_to_lines are what is actually
     *                                  compared when computing the diff.
     * @param array $mapped_to_lines    This array should have the same number
     *                                  of elements as $to_lines.
     */
    function __construct($from_lines, $to_lines,
                             $mapped_from_lines, $mapped_to_lines)
    {
        assert(count($from_lines) == count($mapped_from_lines));
        assert(count($to_lines) == count($mapped_to_lines));

        parent::Text_Diff($mapped_from_lines, $mapped_to_lines);

        $xi = $yi = 0;
        for ($i = 0; $i < count($this->_edits); $i++) {
            $orig = &$this->_edits[$i]->orig;
            if (is_array($orig)) {
                $orig = array_slice($from_lines, $xi, count($orig));
                $xi += count($orig);
            }

            $final = &$this->_edits[$i]->final;
            if (is_array($final)) {
                $final = array_slice($to_lines, $yi, count($final));
                $yi += count($final);
            }
        }
    }

	/**
	 * PHP4 constructor.
	 */
	public function Text_MappedDiff( $from_lines, $to_lines,
                             $mapped_from_lines, $mapped_to_lines ) {
		self::__construct( $from_lines, $to_lines,
                             $mapped_from_lines, $mapped_to_lines );
	}

}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
abstract class Text_Diff_Op {

    var $orig;
    var $final;

    abstract function &reverse();

    function norig()
    {
        return $this->orig ? count($this->orig) : 0;
    }

    function nfinal()
    {
        return $this->final ? count($this->final) : 0;
    }

}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_copy extends Text_Diff_Op {

	/**
	 * PHP5 constructor.
	 */
    function __construct( $orig, $final = false )
    {
        if (!is_array($final)) {
            $final = $orig;
        }
        $this->orig = $orig;
        $this->final = $final;
    }

	/**
	 * PHP4 constructor.
	 */
	public function Text_Diff_Op_copy( $orig, $final = false ) {
		self::__construct( $orig, $final );
	}

    function &reverse()
    {
        $reverse = new Text_Diff_Op_copy($this->final, $this->orig);
        return $reverse;
    }

}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_delete extends Text_Diff_Op {

	/**
	 * PHP5 constructor.
	 */
	function __construct( $lines )
    {
        $this->orig = $lines;
        $this->final = false;
    }

	/**
	 * PHP4 constructor.
	 */
	public function Text_Diff_Op_delete( $lines ) {
		self::__construct( $lines );
	}

    function &reverse()
    {
        $reverse = new Text_Diff_Op_add($this->orig);
        return $reverse;
    }

}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_add extends Text_Diff_Op {

	/**
	 * PHP5 constructor.
	 */
    function __construct( $lines )
    {
        $this->final = $lines;
        $this->orig = false;
    }

	/**
	 * PHP4 constructor.
	 */
	public function Text_Diff_Op_add( $lines ) {
		self::__construct( $lines );
	}

    function &reverse()
    {
        $reverse = new Text_Diff_Op_delete($this->final);
        return $reverse;
    }

}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_change extends Text_Diff_Op {

	/**
	 * PHP5 constructor.
	 */
    function __construct( $orig, $final )
    {
        $this->orig = $orig;
        $this->final = $final;
    }

	/**
	 * PHP4 constructor.
	 */
	public function Text_Diff_Op_change( $orig, $final ) {
		self::__construct( $orig, $final );
	}

    function &reverse()
    {
        $reverse = new Text_Diff_Op_change($this->final, $this->orig);
        return $reverse;
    }

}
