<?php

/*
 * This file is part of the JShrink package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * JShrink
 *
 *
 * @package    JShrink
 * @author     Robert Hafner <tedivm@tedivm.com>
 */
namespace JShrink;

/**
 * Minifier
 *
 * Usage - Minifier::minify($js);
 * Usage - Minifier::minify($js, $options);
 * Usage - Minifier::minify($js, array('flaggedComments' => false));
 *
 * @package JShrink
 * @author Robert Hafner <tedivm@tedivm.com>
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Minifier
{
    /**
     * The input javascript to be minified.
     *
     * @var string
     */
    protected $input;
    /**
     * Length of input javascript.
     *
     * @var int
     */
    protected $len = 0;
    /**
     * The location of the character (in the input string) that is next to be
     * processed.
     *
     * @var int
     */
    protected $index = 0;
    /**
     * The first of the characters currently being looked at.
     *
     * @var string
     */
    protected $a = '';
    /**
     * The next character being looked at (after a);
     *
     * @var string
     */
    protected $b = '';
    /**
     * This character is only active when certain look ahead actions take place.
     *
     *  @var string
     */
    protected $c;
    /**
     * This character is only active when certain look ahead actions take place.
     *
     *  @var string
     */
    protected $last_char;
    /**
     * This character is only active when certain look ahead actions take place.
     *
     *  @var string
     */
    protected $output;
    /**
     * Contains the options for the current minification process.
     *
     * @var array
     */
    protected $options;
    /**
     * These characters are used to define strings.
     */
    protected $stringDelimiters = ['\'' => \true, '"' => \true, '`' => \true];
    /**
     * Contains the default options for minification. This array is merged with
     * the one passed in by the user to create the request specific set of
     * options (stored in the $options attribute).
     *
     * @var array
     */
    protected static $defaultOptions = ['flaggedComments' => \true];
    protected static $keywords = ["delete", "do", "for", "in", "instanceof", "return", "typeof", "yield"];
    protected $max_keyword_len;
    /**
     * Contains lock ids which are used to replace certain code patterns and
     * prevent them from being minified
     *
     * @var array
     */
    protected $locks = [];
    /**
     * Takes a string containing javascript and removes unneeded characters in
     * order to shrink the code without altering it's functionality.
     *
     * @param  string      $js      The raw javascript to be minified
     * @param  array       $options Various runtime options in an associative array
     * @throws \Exception
     * @return bool|string
     */
    public static function minify($js, $options = [])
    {
        try {
            $jshrink = new \JShrink\Minifier();
            $js = $jshrink->lock($js);
            $js = ltrim($jshrink->minifyToString($js, $options));
            $js = $jshrink->unlock($js);
            unset($jshrink);
            return $js;
        } catch (\Exception $e) {
            if (isset($jshrink)) {
                // Since the breakdownScript function probably wasn't finished
                // we clean it out before discarding it.
                $jshrink->clean();
                unset($jshrink);
            }
            throw $e;
        }
    }
    /**
     * Processes a javascript string and outputs only the required characters,
     * stripping out all unneeded characters.
     *
     * @param string $js      The raw javascript to be minified
     * @param array  $options Various runtime options in an associative array
     */
    protected function minifyToString($js, $options)
    {
        $this->initialize($js, $options);
        $this->loop();
        $this->clean();
        return $this->output;
    }
    /**
     *  Initializes internal variables, normalizes new lines,
     *
     * @param string $js      The raw javascript to be minified
     * @param array  $options Various runtime options in an associative array
     */
    protected function initialize($js, $options)
    {
        $this->options = array_merge(static::$defaultOptions, $options);
        $this->input = $js;
        // We add a newline to the end of the script to make it easier to deal
        // with comments at the bottom of the script- this prevents the unclosed
        // comment error that can otherwise occur.
        $this->input .= \PHP_EOL;
        // save input length to skip calculation every time
        $this->len = strlen($this->input);
        // Populate "a" with a new line, "b" with the first character, before
        // entering the loop
        $this->a = "\n";
        $this->b = "\n";
        $this->last_char = "\n";
        $this->output = "";
        $this->max_keyword_len = max(array_map('strlen', static::$keywords));
    }
    /**
     * Characters that can't stand alone preserve the newline.
     *
     * @var array
     */
    protected $noNewLineCharacters = ['(' => \true, '-' => \true, '+' => \true, '[' => \true, '#' => \true, '@' => \true];
    protected function echo($char)
    {
        $this->output .= $char;
        $this->last_char = $char[-1];
    }
    /**
     * The primary action occurs here. This function loops through the input string,
     * outputting anything that's relevant and discarding anything that is not.
     */
    protected function loop()
    {
        while ($this->a !== \false && !is_null($this->a) && $this->a !== '') {
            switch ($this->a) {
                // new lines
                case "\r":
                case "\n":
                    // if the next line is something that can't stand alone preserve the newline
                    if ($this->b !== \false && isset($this->noNewLineCharacters[$this->b])) {
                        $this->echo($this->a);
                        $this->saveString();
                        break;
                    }
                    // if B is a space we skip the rest of the switch block and go down to the
                    // string/regex check below, resetting $this->b with getReal
                    if ($this->b === ' ') {
                        break;
                    }
                // otherwise we treat the newline like a space
                // no break
                case ' ':
                    if (static::isAlphaNumeric($this->b)) {
                        $this->echo($this->a);
                    }
                    $this->saveString();
                    break;
                default:
                    switch ($this->b) {
                        case "\r":
                        case "\n":
                            if (strpos('}])+-"\'', $this->a) !== \false) {
                                $this->echo($this->a);
                                $this->saveString();
                                break;
                            } else {
                                if (static::isAlphaNumeric($this->a)) {
                                    $this->echo($this->a);
                                    $this->saveString();
                                }
                            }
                            break;
                        case ' ':
                            if (!static::isAlphaNumeric($this->a)) {
                                break;
                            }
                        // no break
                        default:
                            // check for some regex that breaks stuff
                            if ($this->a === '/' && ($this->b === '\'' || $this->b === '"')) {
                                $this->saveRegex();
                                continue 3;
                            }
                            $this->echo($this->a);
                            $this->saveString();
                            break;
                    }
            }
            // do reg check of doom
            $this->b = $this->getReal();
            if ($this->b == '/') {
                $valid_tokens = "(,=:[!&|?\n";
                # Find last "real" token, excluding spaces.
                $last_token = $this->a;
                if ($last_token == " ") {
                    $last_token = $this->last_char;
                }
                if (strpos($valid_tokens, $last_token) !== \false) {
                    // Regex can appear unquoted after these symbols
                    $this->saveRegex();
                } else {
                    if ($this->endsInKeyword()) {
                        // This block checks for the "return" token before the slash.
                        $this->saveRegex();
                    }
                }
            }
            // if (($this->b == '/' && strpos('(,=:[!&|?', $this->a) !== false)) {
            //     $this->saveRegex();
            // }
        }
    }
    /**
     * Resets attributes that do not need to be stored between requests so that
     * the next request is ready to go. Another reason for this is to make sure
     * the variables are cleared and are not taking up memory.
     */
    protected function clean()
    {
        unset($this->input);
        $this->len = 0;
        $this->index = 0;
        $this->a = $this->b = '';
        unset($this->c);
        unset($this->options);
    }
    /**
     * Returns the next string for processing based off of the current index.
     *
     * @return string
     */
    protected function getChar()
    {
        // Check to see if we had anything in the look ahead buffer and use that.
        if (isset($this->c)) {
            $char = $this->c;
            unset($this->c);
        } else {
            // Otherwise we start pulling from the input.
            $char = $this->index < $this->len ? $this->input[$this->index] : \false;
            // If the next character doesn't exist return false.
            if (isset($char) && $char === \false) {
                return \false;
            }
            // Otherwise increment the pointer and use this char.
            $this->index++;
        }
        # Convert all line endings to unix standard.
        # `\r\n` converts to `\n\n` and is minified.
        if ($char == "\r") {
            $char = "\n";
        }
        // Normalize all whitespace except for the newline character into a
        // standard space.
        if ($char !== "\n" && $char < " ") {
            return ' ';
        }
        return $char;
    }
    /**
     * This function returns the next character without moving the index forward.
     *
     *
     * @return string            The next character
     * @throws \RuntimeException
     */
    protected function peek()
    {
        if ($this->index >= $this->len) {
            return \false;
        }
        $char = $this->input[$this->index];
        # Convert all line endings to unix standard.
        # `\r\n` converts to `\n\n` and is minified.
        if ($char == "\r") {
            $char = "\n";
        }
        // Normalize all whitespace except for the newline character into a
        // standard space.
        if ($char !== "\n" && $char < " ") {
            return ' ';
        }
        # Return the next character but don't push the index.
        return $char;
    }
    /**
     * This function gets the next "real" character. It is essentially a wrapper
     * around the getChar function that skips comments. This has significant
     * performance benefits as the skipping is done using native functions (ie,
     * c code) rather than in script php.
     *
     *
     * @return string            Next 'real' character to be processed.
     * @throws \RuntimeException
     */
    protected function getReal()
    {
        $startIndex = $this->index;
        $char = $this->getChar();
        // Check to see if we're potentially in a comment
        if ($char !== '/') {
            return $char;
        }
        $this->c = $this->getChar();
        if ($this->c === '/') {
            $this->processOneLineComments($startIndex);
            return $this->getReal();
        } elseif ($this->c === '*') {
            $this->processMultiLineComments($startIndex);
            return $this->getReal();
        }
        return $char;
    }
    /**
     * Removed one line comments, with the exception of some very specific types of
     * conditional comments.
     *
     * @param  int  $startIndex The index point where "getReal" function started
     * @return void
     */
    protected function processOneLineComments($startIndex)
    {
        $thirdCommentString = $this->index < $this->len ? $this->input[$this->index] : \false;
        // kill rest of line
        $this->getNext("\n");
        unset($this->c);
        if ($thirdCommentString == '@') {
            $endPoint = $this->index - $startIndex;
            $this->c = "\n" . substr($this->input, $startIndex, $endPoint);
        }
    }
    /**
     * Skips multiline comments where appropriate, and includes them where needed.
     * Conditional comments and "license" style blocks are preserved.
     *
     * @param  int               $startIndex The index point where "getReal" function started
     * @return void
     * @throws \RuntimeException Unclosed comments will throw an error
     */
    protected function processMultiLineComments($startIndex)
    {
        $this->getChar();
        // current C
        $thirdCommentString = $this->getChar();
        // Detect a completely empty comment, ie `/**/`
        if ($thirdCommentString == "*") {
            $peekChar = $this->peek();
            if ($peekChar == "/") {
                $this->index++;
                return;
            }
        }
        // kill everything up to the next */ if it's there
        if ($this->getNext('*/')) {
            $this->getChar();
            // get *
            $this->getChar();
            // get /
            $char = $this->getChar();
            // get next real character
            // Now we reinsert conditional comments and YUI-style licensing comments
            if ($this->options['flaggedComments'] && $thirdCommentString === '!' || $thirdCommentString === '@') {
                // If conditional comments or flagged comments are not the first thing in the script
                // we need to echo a and fill it with a space before moving on.
                if ($startIndex > 0) {
                    $this->echo($this->a);
                    $this->a = " ";
                    // If the comment started on a new line we let it stay on the new line
                    if ($this->input[$startIndex - 1] === "\n") {
                        $this->echo("\n");
                    }
                }
                $endPoint = $this->index - 1 - $startIndex;
                $this->echo(substr($this->input, $startIndex, $endPoint));
                $this->c = $char;
                return;
            }
        } else {
            $char = \false;
        }
        if ($char === \false) {
            throw new \RuntimeException('Unclosed multiline comment at position: ' . ($this->index - 2));
        }
        // if we're here c is part of the comment and therefore tossed
        $this->c = $char;
    }
    /**
     * Pushes the index ahead to the next instance of the supplied string. If it
     * is found the first character of the string is returned and the index is set
     * to it's position.
     *
     * @param  string       $string
     * @return string|false Returns the first character of the string or false.
     */
    protected function getNext($string)
    {
        // Find the next occurrence of "string" after the current position.
        $pos = strpos($this->input, $string, $this->index);
        // If it's not there return false.
        if ($pos === \false) {
            return \false;
        }
        // Adjust position of index to jump ahead to the asked for string
        $this->index = $pos;
        // Return the first character of that string.
        return $this->index < $this->len ? $this->input[$this->index] : \false;
    }
    /**
     * When a javascript string is detected this function crawls for the end of
     * it and saves the whole string.
     *
     * @throws \RuntimeException Unclosed strings will throw an error
     */
    protected function saveString()
    {
        $startpos = $this->index;
        // saveString is always called after a gets cleared, so we push b into
        // that spot.
        $this->a = $this->b;
        // If this isn't a string we don't need to do anything.
        if (!isset($this->stringDelimiters[$this->a])) {
            return;
        }
        // String type is the quote used, " or '
        $stringType = $this->a;
        // Echo out that starting quote
        $this->echo($this->a);
        // Loop until the string is done
        // Grab the very next character and load it into a
        while (($this->a = $this->getChar()) !== \false) {
            switch ($this->a) {
                // If the string opener (single or double quote) is used
                // output it and break out of the while loop-
                // The string is finished!
                case $stringType:
                    break 2;
                // New lines in strings without line delimiters are bad- actual
                // new lines will be represented by the string \n and not the actual
                // character, so those will be treated just fine using the switch
                // block below.
                case "\n":
                    if ($stringType === '`') {
                        $this->echo($this->a);
                    } else {
                        throw new \RuntimeException('Unclosed string at position: ' . $startpos);
                    }
                    break;
                // Escaped characters get picked up here. If it's an escaped new line it's not really needed
                case '\\':
                    // a is a slash. We want to keep it, and the next character,
                    // unless it's a new line. New lines as actual strings will be
                    // preserved, but escaped new lines should be reduced.
                    $this->b = $this->getChar();
                    // If b is a new line we discard a and b and restart the loop.
                    if ($this->b === "\n") {
                        break;
                    }
                    // echo out the escaped character and restart the loop.
                    $this->echo($this->a . $this->b);
                    break;
                // Since we're not dealing with any special cases we simply
                // output the character and continue our loop.
                default:
                    $this->echo($this->a);
            }
        }
    }
    /**
     * When a regular expression is detected this function crawls for the end of
     * it and saves the whole regex.
     *
     * @throws \RuntimeException Unclosed regex will throw an error
     */
    protected function saveRegex()
    {
        if ($this->a != " ") {
            $this->echo($this->a);
        }
        $this->echo($this->b);
        while (($this->a = $this->getChar()) !== \false) {
            if ($this->a === '/') {
                break;
            }
            if ($this->a === '\\') {
                $this->echo($this->a);
                $this->a = $this->getChar();
            }
            if ($this->a === "\n") {
                throw new \RuntimeException('Unclosed regex pattern at position: ' . $this->index);
            }
            $this->echo($this->a);
        }
        $this->b = $this->getReal();
    }
    /**
     * Checks to see if a character is alphanumeric.
     *
     * @param  string $char Just one character
     * @return bool
     */
    protected static function isAlphaNumeric($char)
    {
        return preg_match('/^[\\w\\$\\pL]$/', $char) === 1 || $char == '/';
    }
    protected function endsInKeyword()
    {
        # When this function is called A is not yet assigned to output.
        # Regular expression only needs to check final part of output for keyword.
        $testOutput = substr($this->output . $this->a, -1 * ($this->max_keyword_len + 10));
        foreach (static::$keywords as $keyword) {
            if (preg_match('/[^\\w]' . $keyword . '[ ]?$/i', $testOutput) === 1) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Replace patterns in the given string and store the replacement
     *
     * @param  string $js The string to lock
     * @return bool
     */
    protected function lock($js)
    {
        /* lock things like <code>"asd" + ++x;</code> */
        $lock = '"LOCK---' . crc32(time()) . '"';
        $matches = [];
        preg_match('/([+-])(\\s+)([+-])/S', $js, $matches);
        if (empty($matches)) {
            return $js;
        }
        $this->locks[$lock] = $matches[2];
        $js = preg_replace('/([+-])\\s+([+-])/S', "\$1{$lock}\$2", $js);
        /* -- */
        return $js;
    }
    /**
     * Replace "locks" with the original characters
     *
     * @param  string $js The string to unlock
     * @return bool
     */
    protected function unlock($js)
    {
        if (empty($this->locks)) {
            return $js;
        }
        foreach ($this->locks as $lock => $replacement) {
            $js = str_replace($lock, $replacement, $js);
        }
        return $js;
    }
}
