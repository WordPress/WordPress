<?php
/**
 * Class used internally by Diff to actually compute the diffs.
 *
 * This class uses the Unix `diff` program via shell_exec to compute the
 * differences between the two input arrays.
 *
 * $Horde: framework/Text_Diff/Diff/Engine/shell.php,v 1.8 2008/01/04 10:07:50 jan Exp $
 *
 * Copyright 2007-2008 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-license.php.
 *
 * @author  Milian Wolff <mail@milianw.de>
 * @package Text_Diff
 * @since   0.3.0
 */
class Text_Diff_Engine_shell {

    /**
     * Path to the diff executable
     *
     * @var string
     */
    var $_diffCommand = 'diff';

    /**
     * Returns the array of differences.
     *
     * @param array $from_lines lines of text from old file
     * @param array $to_lines   lines of text from new file
     *
     * @return array all changes made (array with Text_Diff_Op_* objects)
     */
    function diff($from_lines, $to_lines)
    {
        array_walk($from_lines, array('Text_Diff', 'trimNewlines'));
        array_walk($to_lines, array('Text_Diff', 'trimNewlines'));

        $temp_dir = Text_Diff::_getTempDir();

        // Execute gnu diff or similar to get a standard diff file.
        $from_file = tempnam($temp_dir, 'Text_Diff');
        $to_file = tempnam($temp_dir, 'Text_Diff');
        $fp = fopen($from_file, 'w');
        fwrite($fp, implode("\n", $from_lines));
        fclose($fp);
        $fp = fopen($to_file, 'w');
        fwrite($fp, implode("\n", $to_lines));
        fclose($fp);
        $diff = shell_exec($this->_diffCommand . ' ' . $from_file . ' ' . $to_file);
        unlink($from_file);
        unlink($to_file);

        if (is_null($diff)) {
            // No changes were made
            return array(new Text_Diff_Op_copy($from_lines));
        }

        $from_line_no = 1;
        $to_line_no = 1;
        $edits = array();

        // Get changed lines by parsing something like:
        // 0a1,2
        // 1,2c4,6
        // 1,5d6
        preg_match_all('#^(\d+)(?:,(\d+))?([adc])(\d+)(?:,(\d+))?$#m', $diff,
            $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (!isset($match[5])) {
                // This paren is not set every time (see regex).
                $match[5] = false;
            }

            if ($match[3] == 'a') {
                $from_line_no--;
            }

            if ($match[3] == 'd') {
                $to_line_no--;
            }

            if ($from_line_no < $match[1] || $to_line_no < $match[4]) {
                // copied lines
                assert('$match[1] - $from_line_no == $match[4] - $to_line_no');
                array_push($edits,
                    new Text_Diff_Op_copy(
                        $this->_getLines($from_lines, $from_line_no, $match[1] - 1),
                        $this->_getLines($to_lines, $to_line_no, $match[4] - 1)));
            }

            switch ($match[3]) {
            case 'd':
                // deleted lines
                array_push($edits,
                    new Text_Diff_Op_delete(
                        $this->_getLines($from_lines, $from_line_no, $match[2])));
                $to_line_no++;
                break;

            case 'c':
                // changed lines
                array_push($edits,
                    new Text_Diff_Op_change(
                        $this->_getLines($from_lines, $from_line_no, $match[2]),
                        $this->_getLines($to_lines, $to_line_no, $match[5])));
                break;

            case 'a':
                // added lines
                array_push($edits,
                    new Text_Diff_Op_add(
                        $this->_getLines($to_lines, $to_line_no, $match[5])));
                $from_line_no++;
                break;
            }
        }

        if (!empty($from_lines)) {
            // Some lines might still be pending. Add them as copied
            array_push($edits,
                new Text_Diff_Op_copy(
                    $this->_getLines($from_lines, $from_line_no,
                                     $from_line_no + count($from_lines) - 1),
                    $this->_getLines($to_lines, $to_line_no,
                                     $to_line_no + count($to_lines) - 1)));
        }

        return $edits;
    }

    /**
     * Get lines from either the old or new text
     *
     * @access private
     *
     * @param array &$text_lines Either $from_lines or $to_lines
     * @param int   &$line_no    Current line number
     * @param int   $end         Optional end line, when we want to chop more
     *                           than one line.
     *
     * @return array The chopped lines
     */
    function _getLines(&$text_lines, &$line_no, $end = false)
    {
        if (!empty($end)) {
            $lines = array();
            // We can shift even more
            while ($line_no <= $end) {
                array_push($lines, array_shift($text_lines));
                $line_no++;
            }
        } else {
            $lines = array(array_shift($text_lines));
            $line_no++;
        }

        return $lines;
    }

}
