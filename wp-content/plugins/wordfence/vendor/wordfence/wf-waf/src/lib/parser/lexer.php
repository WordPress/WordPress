<?php

interface wfWAFLexerInterface {

	public function nextToken();

}

class wfWAFRuleLexer implements wfWAFLexerInterface {

	const MATCH_IDENTIFIER = '/[a-zA-Z_][\\w_]*/';
	const MATCH_SINGLE_STRING_LITERAL = '/\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As';
	const MATCH_DOUBLE_STRING_LITERAL = '/"([^#"\\\\]*(?:\\\\.[^#"\\\\]*)*)"/As';
	const MATCH_NUMBER_LITERAL = '/-?\d+(\.\d+)?/';
	const MATCH_DOT = '/\./';
	const MATCH_COMPARISON_OPERATOR = '/\|\||&&/';
	const MATCH_OPEN_PARENTHESIS = '/\(/';
	const MATCH_CLOSE_PARENTHESIS = '/\)/';
	const MATCH_COMMA = '/,/';
	const MATCH_RULE_COMPARISON_END = '/:/';
	const MATCH_ASSIGNMENT = '/=/';
	const MATCH_SINGLE_LINE_COMMENT = '/(?:#|\/\/)[^\n]*/';
	const MATCH_MULTIPLE_LINE_COMMENT = '/\/\*.*?\*\//s';
	const MATCH_OPEN_BRACKET = '/\[/';
	const MATCH_CLOSE_BRACKET = '/\]/';

	const T_RULE_START = 'T_RULE_START';
	const T_IDENTIFIER = 'T_IDENTIFIER';
	const T_SINGLE_STRING_LITERAL = 'T_SINGLE_STRING_LITERAL';
	const T_DOUBLE_STRING_LITERAL = 'T_DOUBLE_STRING_LITERAL';
	const T_NUMBER_LITERAL = 'T_NUMBER_LITERAL';
	const T_DOT = 'T_DOT';
	const T_COMPARISON_OPERATOR = 'T_COMPARISON_OPERATOR';
	const T_OPEN_PARENTHESIS = 'T_OPEN_PARENTHESIS';
	const T_CLOSE_PARENTHESIS = 'T_CLOSE_PARENTHESIS';
	const T_COMMA = 'T_COMMA';
	const T_RULE_COMPARISON_END = 'T_RULE_COMPARISON_END';
	const T_ASSIGNMENT = 'T_ASSIGNMENT';
	const T_SINGLE_LINE_COMMENT = 'T_SINGLE_LINE_COMMENT';
	const T_MULTIPLE_LINE_COMMENT = 'T_MULTIPLE_LINE_COMMENT';
	const T_OPEN_BRACKET = 'T_OPEN_BRACKET';
	const T_CLOSE_BRACKET = 'T_CLOSE_BRACKET';


	/**
	 * @var string
	 */
	private $rules;

	/**
	 * @var wfWAFStringScanner
	 */
	private $scanner;

	/**
	 * wfWAFRuleLexer constructor.
	 * @param $rules
	 */
	public function __construct($rules) {
		$this->setRules($rules);
		$this->scanner = new wfWAFStringScanner($rules);
	}

	/**
	 * @return array
	 * @throws wfWAFParserSyntaxError
	 */
	public function tokenize() {
		$tokens = array();
		while ($token = $this->nextToken()) {
			$tokens[] = $token;
		}
		return $tokens;
	}

	/**
	 * @return bool|wfWAFLexerToken
	 * @throws wfWAFParserSyntaxError
	 */
	public function nextToken() {
		if (!$this->scanner->eos()) {
			$this->scanner->skip('/\s+/s');
			if ($this->scanner->eos()) {
				return false;
			}
			if (($match = $this->scanner->scan(self::MATCH_IDENTIFIER)) !== null)
				switch (wfWAFUtils::strtolower($match)) {
					case 'if':
						return $this->createToken(self::T_RULE_START, $match);
					case 'and':
					case 'or':
					case 'xor':
						return $this->createToken(self::T_COMPARISON_OPERATOR, $match);
					default:
						return $this->createToken(self::T_IDENTIFIER, $match);
				}
			else if (($match = $this->scanner->scan(self::MATCH_SINGLE_STRING_LITERAL)) !== null) return $this->createToken(self::T_SINGLE_STRING_LITERAL, $match);
			else if (($match = $this->scanner->scan(self::MATCH_DOUBLE_STRING_LITERAL)) !== null) return $this->createToken(self::T_DOUBLE_STRING_LITERAL, $match);
			else if (($match = $this->scanner->scan(self::MATCH_NUMBER_LITERAL)) !== null) return $this->createToken(self::T_NUMBER_LITERAL, $match);
			else if (($match = $this->scanner->scan(self::MATCH_DOT)) !== null) return $this->createToken(self::T_DOT, $match);
			else if (($match = $this->scanner->scan(self::MATCH_COMPARISON_OPERATOR)) !== null) return $this->createToken(self::T_COMPARISON_OPERATOR, $match);
			else if (($match = $this->scanner->scan(self::MATCH_OPEN_PARENTHESIS)) !== null) return $this->createToken(self::T_OPEN_PARENTHESIS, $match);
			else if (($match = $this->scanner->scan(self::MATCH_CLOSE_PARENTHESIS)) !== null) return $this->createToken(self::T_CLOSE_PARENTHESIS, $match);
			else if (($match = $this->scanner->scan(self::MATCH_COMMA)) !== null) return $this->createToken(self::T_COMMA, $match);
			else if (($match = $this->scanner->scan(self::MATCH_RULE_COMPARISON_END)) !== null) return $this->createToken(self::T_RULE_COMPARISON_END, $match);
			else if (($match = $this->scanner->scan(self::MATCH_ASSIGNMENT)) !== null) return $this->createToken(self::T_ASSIGNMENT, $match);
			else if (($match = $this->scanner->scan(self::MATCH_OPEN_BRACKET)) !== null) return $this->createToken(self::T_OPEN_BRACKET, $match);
			else if (($match = $this->scanner->scan(self::MATCH_CLOSE_BRACKET)) !== null) return $this->createToken(self::T_CLOSE_BRACKET, $match);
			else if (($match = $this->scanner->scan(self::MATCH_SINGLE_LINE_COMMENT)) !== null) return $this->createToken(self::T_SINGLE_LINE_COMMENT, $match);
			else if (($match = $this->scanner->scan(self::MATCH_MULTIPLE_LINE_COMMENT)) !== null) return $this->createToken(self::T_MULTIPLE_LINE_COMMENT, $match);
			else {
				$e = new wfWAFParserSyntaxError(sprintf('Invalid character "%s" found on line %d, column %d',
					$this->scanner->scanChar(), $this->scanner->getLine(), $this->scanner->getColumn()));
				$e->setParseLine($this->scanner->getLine());
				$e->setParseColumn($this->scanner->getColumn());
				throw $e;
			}
		}
		return false;
	}

	/**
	 * @param $type
	 * @param $value
	 * @return wfWAFLexerToken
	 */
	protected function createToken($type, $value) {
		return new wfWAFLexerToken($type, $value, $this->scanner->getLine(), $this->scanner->getColumn());
	}

	/**
	 * @return string
	 */
	public function getRules() {
		return $this->rules;
	}

	/**
	 * @param string $rules
	 */
	public function setRules($rules) {
		$this->rules = rtrim($rules);
	}
}

/**
 *
 */
class wfWAFLexerToken {

	private $type;
	private $value;
	private $line;
	private $column;

	/**
	 * wfWAFRuleToken constructor.
	 *
	 * @param $type
	 * @param $value
	 * @param $line
	 * @param $column
	 */
	public function __construct($type, $value, $line, $column) {
		$this->setType($type);
		$this->setValue($value);
		$this->setLine($line);
		$this->setColumn($column);
	}

	/**
	 * @return string
	 */
	public function getLowerCaseValue() {
		return wfWAFUtils::strtolower($this->getValue());
	}

	/**
	 * @return string
	 */
	public function getUpperCaseValue() {
		return wfWAFUtils::strtoupper($this->getValue());
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	public function getLine() {
		return $this->line;
	}

	/**
	 * @param mixed $line
	 */
	public function setLine($line) {
		$this->line = $line;
	}

	/**
	 * @return mixed
	 */
	public function getColumn() {
		return $this->column;
	}

	/**
	 * @param mixed $column
	 */
	public function setColumn($column) {
		$this->column = $column;
	}
}


class wfWAFParserSyntaxError extends wfWAFException {

	private $parseLine;
	private $parseColumn;
	private $token;

	/**
	 * @return mixed
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * @param mixed $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}

	/**
	 * @return mixed
	 */
	public function getParseLine() {
		return $this->parseLine;
	}

	/**
	 * @param mixed $parseLine
	 */
	public function setParseLine($parseLine) {
		$this->parseLine = $parseLine;
	}

	/**
	 * @return mixed
	 */
	public function getParseColumn() {
		return $this->parseColumn;
	}

	/**
	 * @param mixed $parseColumn
	 */
	public function setParseColumn($parseColumn) {
		$this->parseColumn = $parseColumn;
	}

}

class wfWAFBaseParser {

	protected $tokens;
	protected $index;
	/** @var wfWAFLexerInterface */
	protected $lexer;

	public function __construct($lexer) {
		$this->lexer = $lexer;
	}

	/**
	 * @param wfWAFLexerToken $token
	 * @param mixed $type
	 * @return bool
	 */
	protected function isTokenOfType($token, $type) {
		if (is_array($type)) {
			return $token && in_array($token->getType(), $type);
		}
		return $token && $token->getType() === $type;
	}

	/**
	 * @param wfWAFLexerToken $token
	 * @param int $type
	 * @param string $message
	 * @throws wfWAFParserSyntaxError
	 */
	protected function expectTokenTypeEquals($token, $type, $message = 'Wordfence WAF Syntax Error: Unexpected %s found on line %d, column %d. Expected %s.') {
		if ($token->getType() !== $type) {
			$this->triggerSyntaxError($token, sprintf($message, $token->getType(),
				$token->getLine(), $token->getColumn(), $type));
		}
	}

	/**
	 * @param wfWAFLexerToken $token
	 * @param array $types
	 * @param string $message
	 * @throws wfWAFParserSyntaxError
	 */
	protected function expectTokenTypeInArray($token, $types, $message = 'Wordfence WAF Syntax Error: Unexpected %s found on line %d, column %d') {
		if (!in_array($token->getType(), $types)) {
			$this->triggerSyntaxError($token, sprintf($message, $token->getType(),
				$token->getLine(), $token->getColumn()));
		}
	}

	/**
	 * @param wfWAFLexerToken $token
	 * @param string $message
	 * @throws wfWAFParserSyntaxError
	 */
	protected function triggerSyntaxError($token, $message = 'Wordfence WAF Syntax Error: Unexpected %s %s found on line %d, column %d') {
		$e = new wfWAFParserSyntaxError(sprintf($message, $token->getType(), $token->getValue(),
			$token->getLine(), $token->getColumn()));
		$e->setToken($token);
		$e->setParseLine($token->getLine());
		$e->setParseColumn($token->getColumn());
		throw $e;
	}

	/**
	 * @return wfWAFLexerToken
	 */
	protected function currentToken() {
		return $this->getToken($this->index);
	}

	/**
	 * @return bool|wfWAFLexerToken
	 */
	protected function nextToken() {
		$this->index++;
		return $this->getToken($this->index);
	}

	/**
	 * @param string $message
	 * @return wfWAFLexerToken
	 * @throws wfWAFParserSyntaxError
	 */
	protected function expectNextToken($message = 'Expected statement') {
		$this->index++;
		if ($token = $this->getToken($this->index)) {
			return $token;
		}
		throw new wfWAFParserSyntaxError($message);
	}

	/**
	 * @param int $index
	 * @return mixed
	 */
	protected function getToken($index) {
		if (is_array($this->tokens) && array_key_exists($index, $this->tokens)) {
			return $this->tokens[$index];
		}
		if ($token = $this->getLexer()->nextToken()) {
			$this->tokens[$index] = $token;
			return $this->tokens[$index];
		}
		return false;
	}

	/**
	 * @return wfWAFLexerInterface
	 */
	public function getLexer() {
		return $this->lexer;
	}

	/**
	 * @param wfWAFLexerInterface $lexer
	 */
	public function setLexer($lexer) {
		$this->lexer = $lexer;
	}

	/**
	 * @return mixed
	 */
	public function getTokens() {
		return $this->tokens;
	}

	/**
	 * @param mixed $tokens
	 */
	public function setTokens($tokens) {
		$this->tokens = $tokens;
	}
}

/**
 *
 */
class wfWAFStringScanner {

	private $string;
	private $length;
	private $pointer;
	private $prevPointer;
	private $match;
	private $captures;

	/**
	 * wfWAFStringScanner constructor.
	 * @param $string
	 */
	public function __construct($string = null) {
		if (is_string($string)) {
			$this->setString($string);
		}
	}

	/**
	 * @param $regex
	 * @return mixed
	 */
	public function scan($regex) {
		$remaining = $this->getRemainingString();
		if ($this->regexMatch($regex, $remaining, $matches)) {
			$matchLen = wfWAFUtils::strlen($matches[0]);
			if ($matchLen > 0 && wfWAFUtils::strpos($remaining, $matches[0]) === 0) {
				return $this->setState($matches, $this->getPointer() + $matchLen, $this->getPointer());
			}
		}
		return $this->setState();
	}

	/**
	 * @param $regex
	 * @return int|null
	 */
	public function skip($regex) {
		return $this->scan($regex) ? wfWAFUtils::strlen($this->getMatch()) : null;
	}

	/**
	 * @return mixed
	 */
	public function scanChar() {
		return $this->scan('/./s');
	}

	/**
	 * @param string $regex
	 * @return mixed
	 */
	public function check($regex) {
		$remaining = $this->getRemainingString();
		if ($this->regexMatch($regex, $remaining, $matches)) {
			$matchLen = wfWAFUtils::strlen($matches[0]);
			if ($matchLen > 0 && wfWAFUtils::strpos($remaining, $matches[0]) === 0) {
				return $this->setState($matches);
			}
		}
		return $this->setState();
	}

	/**
	 * @param string $regex
	 * @param string $remaining
	 * @param $matches
	 * @return int
	 */
	public function regexMatch($regex, $remaining, &$matches) {
//		$startTime = microtime(true);
		$result = preg_match($regex, $remaining, $matches);
//		printf("%s took %f seconds\n", $regex, microtime(true) - $startTime);
		return $result;
	}

	/**
	 * @return bool
	 */
	public function eos() {
		return $this->getPointer() === $this->getLength();
	}

	/**
	 * @return string
	 */
	public function getRemainingString() {
		return wfWAFUtils::substr($this->getString(), $this->getPointer());
	}

	/**
	 * @return $this
	 */
	public function reset() {
		$this->setState(array(), 0, 0);
		return $this;
	}

	/**
	 * The current line of the scanned string.
	 *
	 * @return int
	 */
	public function getLine() {
		if ($this->getPointer() + 1 > $this->getLength()) {
			return wfWAFUtils::substr_count($this->getString(), "\n") + 1;
		}
		return wfWAFUtils::substr_count($this->getString(), "\n", 0, $this->getPointer() + 1) + 1;
	}

	/**
	 * The current column of the line of the scanned string.
	 *
	 * @return int
	 */
	public function getColumn() {
		return $this->getPointer() - ((int) wfWAFUtils::strrpos(wfWAFUtils::substr($this->getString(), 0, $this->getPointer() + 1), "\n")) + 1;
	}

	/**
	 * @param array $matches
	 * @param int|null $pointer
	 * @param int|null $prevPointer
	 * @return mixed
	 */
	protected function setState($matches = array(), $pointer = null, $prevPointer = null) {
		if ($pointer !== null) {
			$this->setPointer($pointer);
		}
		if ($prevPointer !== null) {
			$this->setPrevPointer($prevPointer);
		}
		if (is_array($matches)) {
			$this->setCaptures(array_slice($matches, 1));
			if (count($matches) > 0) {
				$this->setMatch($matches[0]);
			} else {
				$this->setMatch(null);
			}
		} else {
			$this->setMatch(null);
		}
		return $this->getMatch();
	}

	/**
	 * @return string
	 */
	public function getString() {
		return $this->string;
	}

	/**
	 * @param string $string
	 * @throws InvalidArgumentException
	 */
	public function setString($string) {
		if (!is_string($string)) {
			throw new InvalidArgumentException(sprintf('String expected, got [%s]', gettype($string)));
		}
		$this->setLength(wfWAFUtils::strlen($string));
		$this->string = $string;
		$this->reset();
	}

	/**
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * @param int $length
	 */
	protected function setLength($length) {
		$this->length = $length;
	}

	/**
	 * @param int $length
	 */
	public function advancePointer($length) {
		$this->setPointer($this->getPointer() + $length);
	}

	/**
	 * @return int
	 */
	public function getPointer() {
		return $this->pointer;
	}

	/**
	 * @param int $pointer
	 */
	protected function setPointer($pointer) {
		$this->pointer = $pointer;
	}

	/**
	 * @return int
	 */
	public function getPrevPointer() {
		return $this->prevPointer;
	}

	/**
	 * @param int $prevPointer
	 */
	protected function setPrevPointer($prevPointer) {
		$this->prevPointer = $prevPointer;
	}

	/**
	 * @return mixed
	 */
	public function getMatch() {
		return $this->match;
	}

	/**
	 * @param mixed $match
	 */
	protected function setMatch($match) {
		$this->match = $match;
	}

	/**
	 * @param null $index
	 * @return mixed
	 */
	public function getCaptures($index = null) {
		if (is_numeric($index)) {
			return isset($this->captures[$index]) ? $this->captures[$index] : null;
		}
		return $this->captures;
	}

	/**
	 * @param mixed $captures
	 */
	protected function setCaptures($captures) {
		$this->captures = $captures;
	}
}


