<?php
/**
 * "Inline" diff renderer.
 *
 * Copyright 2004-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-license.php.
 *
 * @author  Ciprian Popovici
 * @package Text_Diff
 */

/** Text_Diff_Renderer */

// WP #7391
require_once dirname(dirname(__FILE__)) . '/Renderer.php';

/**
 * "Inline" diff renderer.
 *
 * This class renders diffs in the Wiki-style "inline" format.
 *
 * @author  Ciprian Popovici
 * @package Text_Diff
 */
class Text_Diff_Renderer_inline extends Text_Diff_Renderer {

    /**
     * Number of leading context "lines" to preserve.
     *
     * @var integer
     */
    var $_leading_context_lines = 10000;

    /**
     * Number of trailing context "lines" to preserve.
     *
     * @var integer
     */
    var $_trailing_context_lines = 10000;

    /**
     * Prefix for inserted text.
     *
     * @var string
     */
    var $_ins_prefix = '<ins>';

    /**
     * Suffix for inserted text.
     *
     * @var string
     */
    var $_ins_suffix = '</ins>';

    /**
     * Prefix for deleted text.
     *
     * @var string
     */
    var $_del_prefix = '<del>';

    /**
     * Suffix for deleted text.
     *
     * @var string
     */
    var $_del_suffix = '</del>';

    /**
     * Header for each change block.
     *
     * @var string
     */
    var $_block_header = '';

    /**
     * Whether to split down to character-level.
     *
     * @var boolean
     */
    var $_split_characters = false;

    /**
     * What are we currently splitting on? Used to recurse to show word-level
     * or character-level changes.
     *
     * @var string
     */
    var $_split_level = 'lines';

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        return $this->_block_header;
    }

    function _startBlock($header)
    {
        return $header;
    }

    function _lines($lines, $prefix = ' ', $encode = true)
    {
        if ($encode) {
            array_walk($lines, array(&$this, '_encode'));
        }

        if ($this->_split_level == 'lines') {
            return implode("\n", $lines) . "\n";
        } else {
            return implode('', $lines);
        }
    }

    function _added($lines)
    {
        array_walk($lines, array(&$this, '_encode'));
        $lines[0] = $this->_ins_prefix . $lines[0];
        $lines[count($lines) - 1] .= $this->_ins_suffix;
        return $this->_lines($lines, ' ', false);
    }

    function _deleted($lines, $words = false)
    {
        array_walk($lines, array(&$this, '_encode'));
        $lines[0] = $this->_del_prefix . $lines[0];
        $lines[count($lines) - 1] .= $this->_del_suffix;
        return $this->_lines($lines, ' ', false);
    }

    function _changed($orig, $final)
    {
        /* If we've already split on characters, just display. */
        if ($this->_split_level == 'characters') {
            return $this->_deleted($orig)
                . $this->_added($final);
        }

        /* If we've already split on words, just display. */
        if ($this->_split_level == 'words') {
            $prefix = '';
            while ($orig[0] !== false && $final[0] !== false &&
                   substr($orig[0], 0, 1) == ' ' &&
                   substr($final[0], 0, 1) == ' ') {
                $prefix .= substr($orig[0], 0, 1);
                $orig[0] = substr($orig[0], 1);
                $final[0] = substr($final[0], 1);
            }
            return $prefix . $this->_deleted($orig) . $this->_added($final);
        }

        $text1 = implode("\n", $orig);
        $text2 = implode("\n", $final);

        /* Non-printing newline marker. */
        $nl = "\0";

        if ($this->_split_characters) {
            $diff = new Text_Diff('native',
                                  array(preg_split('//', $text1),
                                        preg_split('//', $text2)));
        } else {
            /* We want to split on word boundaries, but we need to preserve
             * whitespace as well. Therefore we split on words, but include
             * all blocks of whitespace in the wordlist. */
            $diff = new Text_Diff('native',
                                  array($this->_splitOnWords($text1, $nl),
                                        $this->_splitOnWords($text2, $nl)));
        }

        /* Get the diff in inline format. */
        $renderer = new Text_Diff_Renderer_inline
            (array_merge($this->getParams(),
                         array('split_level' => $this->_split_characters ? 'characters' : 'words')));

        /* Run the diff and get the output. */
        return str_replace($nl, "\n", $renderer->render($diff)) . "\n";
    }

    function _splitOnWords($string, $newlineEscape = "\n")
    {
        // Ignore \0; otherwise the while loop will never finish.
        $string = str_replace("\0", '', $string);

        $words = array();
        $length = strlen($string);
        $pos = 0;

        while ($pos < $length) {
            // Eat a word with any preceding whitespace.
            $spaces = strspn(substr($string, $pos), " \n");
            $nextpos = strcspn(substr($string, $pos + $spaces), " \n");
            $words[] = str_replace("\n", $newlineEscape, substr($string, $pos, $spaces + $nextpos));
            $pos += $spaces + $nextpos;
        }

        return $words;
    }

    function _encode(&$string)
    {
        $string = htmlspecialchars($string);
    }

}
