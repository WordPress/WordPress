<?php

require_once dirname(__FILE__) . '/lexer.php';

class wfWAFRuleParser extends wfWAFBaseParser {

	/**
	 * @var wfWAF
	 */
	private $waf;
	private $parenCount = 0;

	/**
	 * wfWAFRuleParser constructor.
	 * @param $lexer
	 * @param wfWAF $waf
	 */
	public function __construct($lexer, $waf) {
		parent::__construct($lexer);
		$this->setWAF($waf);
	}

	/**
	 * @return array
	 * @throws wfWAFParserSyntaxError
	 * @throws wfWAFRuleParserSyntaxError
	 */
	public function parse() {
		$rules = array();
		$scores = array();
		$blacklistedParams = array();
		$whitelistedParams = array();
		$variables = array();
		$this->index = -1;
		while ($token = $this->nextToken()) {

			// Rule parsing
			if ($token->getType() == wfWAFRuleLexer::T_RULE_START) {
				$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_OPEN_PARENTHESIS);

				$comparisonGroup = $this->parseConditional();
				$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_CLOSE_PARENTHESIS);
				$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_RULE_COMPARISON_END);
				$action = $this->parseAction();

				$rules[] = new wfWAFRule(
					$this->getWAF(),
					$action->getRuleID(),
					$action->getType(),
					$action->getCategory(),
					$action->getScore(),
					$action->getDescription(),
					$action->getWhitelist(),
					$action->getAction(),
					$comparisonGroup
				);
			}

			// Score/config parsing
			if ($token->getType() == wfWAFRuleLexer::T_IDENTIFIER) {
				switch ($token->getValue()) {
					case 'scores':
						$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_DOT);
						$scoreCategoryToken = $this->expectNextToken();
						$this->expectTokenTypeEquals($scoreCategoryToken, wfWAFRuleLexer::T_IDENTIFIER);

						$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_ASSIGNMENT);

						$scoreToken = $this->expectNextToken();
						$this->expectTokenTypeEquals($scoreToken, wfWAFRuleLexer::T_NUMBER_LITERAL);
						$scores[$scoreCategoryToken->getValue()] = $scoreToken->getValue();
						break;

					case 'blacklistParam':
						$blacklistedParams[] = $this->parseURLParams();
						break;

					case 'whitelistParam':
						$whitelistedParams[] = $this->parseURLParams();
						break;

					default:
						$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_ASSIGNMENT);
						$valueToken = $this->expectNextToken();
						$this->expectTokenTypeInArray($valueToken, array(
							wfWAFRuleLexer::T_SINGLE_STRING_LITERAL,
							wfWAFRuleLexer::T_DOUBLE_STRING_LITERAL,
							wfWAFRuleLexer::T_NUMBER_LITERAL,
						));
						if ($valueToken->getType() === wfWAFRuleLexer::T_SINGLE_STRING_LITERAL) {
							$value = wfWAFUtils::substr($valueToken->getValue(), 1, -1);
							$value = str_replace("\\'", "'", $value);
						} else if ($valueToken->getType() === wfWAFRuleLexer::T_DOUBLE_STRING_LITERAL) {
							$value = wfWAFUtils::substr($valueToken->getValue(), 1, -1);
							$value = str_replace('\\"', '"', $value);
						} else {
							$value = $valueToken->getValue();
						}
						$variables[$token->getValue()] = new wfWAFRuleVariable($this->getWAF(), $token->getValue(), $value);
						break;
				}
			}
		}

		return array(
			'scores'            => $scores,
			'blacklistedParams' => $blacklistedParams,
			'whitelistedParams' => $whitelistedParams,
			'variables'         => $variables,
			'rules'             => $rules,
		);
	}

	/**
	 * @param array $vars
	 * @return string
	 */
	public function renderRules($vars) {
		$rules = '';
		if (array_key_exists('scores', $vars)) {
			foreach ($vars['scores'] as $category => $score) {
				// scores.sqli = 100
				$rules .= sprintf("scores.%s = %d\n", $category, $score);
			}
			$rules .= "\n";
		}

		$params = array(
			'blacklistParam' => 'blacklistedParams',
			'whitelistParam' => 'whitelistedParams',
		);
		foreach ($params as $action => $key) {
			if (array_key_exists($key, $vars)) {
				/** @var wfWAFRuleParserURLParam $urlParam */
				foreach ($vars[$key] as $urlParam) {
					$rules .= $urlParam->renderRule($action) . "\n";
				}
				$rules .= "\n";
			}
		}

		if (array_key_exists('variables', $vars)) {
			/** @var wfWAFRuleVariable $variable */
			foreach ($vars['variables'] as $variableName => $variable) {
				$rules .= sprintf("%s = %s\n", $variable->renderRule(), $variable->renderValue());
			}
			$rules .= "\n";
		}

		if (array_key_exists('rules', $vars)) {
			/** @var wfWAFRule $rule */
			foreach ($vars['rules'] as $rule) {
				$rules .= $rule->renderRule() . "\n";
			}
			$rules .= "\n";
		}
		return $rules;
	}

	/**
	 * @param int $index
	 * @return mixed
	 */
	public function getToken($index) {
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
	 * @return wfWAFRuleComparisonGroup
	 */
	private function parseConditional() {
		$comparisonGroup = new wfWAFRuleComparisonGroup();
		while ($token = $this->nextToken()) {
			switch ($token->getType()) {
				case wfWAFRuleLexer::T_IDENTIFIER:
					$comparisonGroup->add($this->parseComparison());
					break;

				case wfWAFRuleLexer::T_COMPARISON_OPERATOR:
					$comparisonGroup->add(new wfWAFRuleLogicalOperator($token->getValue()));
					break;

				case wfWAFRuleLexer::T_OPEN_PARENTHESIS:
					$this->parenCount++;
					$comparisonGroup->add($this->parseConditional());
					break;

				case wfWAFRuleLexer::T_CLOSE_PARENTHESIS:
					if ($this->parenCount === 0) {
						$this->index--;
						return $comparisonGroup;
					}
					$this->parenCount--;
					return $comparisonGroup;
			}

		}
		return $comparisonGroup;
	}

	private function parseComparison() {
		/**
		 * @var wfWAFLexerToken $actionToken
		 * @var wfWAFLexerToken $expectedToken
		 */
		$actionToken = $this->currentToken();
		$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_OPEN_PARENTHESIS);
		$value = $this->expectLiteral();

		$subjects = array();
		while (true) {
			$commaToken = $this->nextToken();
			if (!($commaToken && $commaToken->getType() === wfWAFRuleLexer::T_COMMA)) {
				$this->index--;
				break;
			}
			list($filters, $subject) = $this->parseFilters();
			$subjects[] = new wfWAFRuleComparisonSubject($this->getWAF(), $subject, $filters);
		}
		$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_CLOSE_PARENTHESIS);

		$comparison = new wfWAFRuleComparison($this->getWAF(), $actionToken->getValue(), $value, $subjects);
		return $comparison;
	}

	/**
	 * @return wfWAFRuleParserAction
	 */
	private function parseAction() {
		$action = new wfWAFRuleParserAction();

		$actionToken = $this->expectNextToken();
		$this->expectTokenTypeEquals($actionToken, wfWAFRuleLexer::T_IDENTIFIER);
		$action->setAction($actionToken->getValue());
		$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_OPEN_PARENTHESIS);

		while (true) {
			$token = $this->expectNextToken();
			switch ($token->getType()) {
				case wfWAFRuleLexer::T_IDENTIFIER:
					$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_ASSIGNMENT);
					$valueToken = $this->expectNextToken();
					$this->expectTokenTypeInArray($valueToken, array(
						wfWAFRuleLexer::T_SINGLE_STRING_LITERAL,
						wfWAFRuleLexer::T_DOUBLE_STRING_LITERAL,
						wfWAFRuleLexer::T_NUMBER_LITERAL,
					));
					$action->set($token->getValue(), $valueToken->getValue());
					break;

				case wfWAFRuleLexer::T_COMMA:
					break;

				case wfWAFRuleLexer::T_CLOSE_PARENTHESIS:
					break 2;

				default:
					$this->triggerSyntaxError($token, sprintf('Wordfence WAF Rules Syntax Error: Unexpected %s found on line %d, column %d',
						$token->getType(), $token->getLine(), $token->getColumn()));
			}
		}
		return $action;
	}

	private function parseFilters() {
		$filters = array();
		$subject = null;
		do {
			$globalToken = $this->expectNextToken();
			$this->expectTokenTypeEquals($globalToken, wfWAFRuleLexer::T_IDENTIFIER);
			$parenToken = $this->expectNextToken();
			switch ($parenToken->getType()) {
				case wfWAFRuleLexer::T_DOT:
					$this->index -= 2;
					$subject = $this->parseSubject();
					break 2;

				case wfWAFRuleLexer::T_OPEN_PARENTHESIS:
					$filters[] = $globalToken->getValue();
					break;

				default:
					$this->triggerSyntaxError($parenToken,
						sprintf('Wordfence WAF Rules Syntax Error: Unexpected %s found on line %d, column %d.',
							$parenToken->getType(), $parenToken->getLine(), $parenToken->getColumn()));
			}
		} while (true);
		if ($subject === null) {
			throw new wfWAFParserSyntaxError('No subject supplied to filter');
		}
		for ($i = 0; $i < count($filters); $i++) {
			$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_CLOSE_PARENTHESIS);
		}
		return array($filters, $subject);
	}

	/**
	 * @throws wfWAFParserSyntaxError
	 */
	private function parseSubject() {
		$globalToken = $this->expectNextToken();
		$this->expectTokenTypeEquals($globalToken, wfWAFRuleLexer::T_IDENTIFIER);
		$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_DOT);
		$globalToken2 = $this->expectNextToken();
		$this->expectTokenTypeEquals($globalToken2, wfWAFRuleLexer::T_IDENTIFIER);
		$subject = array(
			$globalToken->getValue() . '.' . $globalToken2->getValue(),
		);
		$savePoint = $this->index;
		while (($property = $this->parsePropertyAccessor()) !== false) {
			$subject[] = $property;
			$savePoint = $this->index;
		}
		$this->index = $savePoint;
		if (count($subject) === 1) {
			list($subject) = $subject;
		}
		return $subject;
	}

	/**
	 * @return bool|mixed|string
	 * @throws wfWAFParserSyntaxError
	 */
	private function parsePropertyAccessor() {
		$savePoint = $this->index;
		$nextToken = $this->nextToken();
		if ($this->isTokenOfType($nextToken, wfWAFRuleLexer::T_DOT)) {
			$property = $this->expectNextToken();
			$this->expectTokenTypeEquals($property, wfWAFRuleLexer::T_IDENTIFIER);
			return $property->getValue();
		} else if ($this->isTokenOfType($nextToken, wfWAFRuleLexer::T_OPEN_BRACKET)) {
			$property = $this->expectLiteral();
			$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_CLOSE_BRACKET);
			return $property;
		}
		$this->index = $savePoint;
		return false;
	}

	/**
	 * @return wfWAFRuleParserURLParam
	 * @throws wfWAFParserSyntaxError
	 */
	private function parseURLParams() {
		$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_OPEN_PARENTHESIS);

		$urlParam = new wfWAFRuleParserURLParam();
		while (true) {
			$token = $this->expectNextToken();
			switch ($token->getType()) {
				case wfWAFRuleLexer::T_IDENTIFIER:
					$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_ASSIGNMENT);
					if ($token->getValue() === 'url') {
						$url = $this->expectLiteral();
						$urlParam->setUrl($url);
					} else if ($token->getValue() === 'param') {
						$subject = $this->parseSubject();
						$urlParam->setParam(wfWAFRuleComparison::getSubjectKey($subject));
					} else if ($token->getValue() === 'rules') {
						$rules = $this->expectLiteral();
						$urlParam->setRules($rules);
					} else if ($token->getValue() === 'conditional') {
						$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_OPEN_PARENTHESIS);
						$conditional = $this->parseConditional();
						$this->expectTokenTypeEquals($this->expectNextToken(), wfWAFRuleLexer::T_CLOSE_PARENTHESIS);
						$urlParam->setConditional($conditional);
					} else if ($token->getValue() === 'minVersion') {
						$minVersion = $this->expectLiteral();
						$urlParam->setMinVersion($minVersion);
					}

					break;

				case wfWAFRuleLexer::T_COMMA:
					break;

				case wfWAFRuleLexer::T_CLOSE_PARENTHESIS:
					break 2;

				default:
					$this->triggerSyntaxError($token, sprintf('Wordfence WAF Rules Syntax Error: Unexpected %s found on line %d, column %d',
						$token->getType(), $token->getLine(), $token->getColumn()));
			}
		}
		return $urlParam;
	}

	/**
	 * @return mixed|string
	 * @throws wfWAFRuleParserSyntaxError
	 */
	private function expectLiteral() {
		$expectedToken = $this->expectNextToken();
		$this->expectTokenTypeInArray($expectedToken, array(
			wfWAFRuleLexer::T_SINGLE_STRING_LITERAL,
			wfWAFRuleLexer::T_DOUBLE_STRING_LITERAL,
			wfWAFRuleLexer::T_IDENTIFIER,
			wfWAFRuleLexer::T_NUMBER_LITERAL,
			wfWAFRuleLexer::T_OPEN_BRACKET,
		));
		if ($expectedToken->getType() === wfWAFRuleLexer::T_SINGLE_STRING_LITERAL) {
			// Remove quotes, strip slashes
			$value = wfWAFUtils::substr($expectedToken->getValue(), 1, -1);
			$value = str_replace("\\'", "'", $value);
		} else if ($expectedToken->getType() === wfWAFRuleLexer::T_DOUBLE_STRING_LITERAL) {
			// Remove quotes, strip slashes
			$value = wfWAFUtils::substr($expectedToken->getValue(), 1, -1);
			$value = str_replace('\\"', '"', $value);
		} else if ($expectedToken->getType() === wfWAFRuleLexer::T_IDENTIFIER) {
			// Remove quotes, strip slashes
			$value = new wfWAFRuleVariable($this->getWAF(), $expectedToken->getValue());
		} else if ($expectedToken->getType() === wfWAFRuleLexer::T_OPEN_BRACKET) {
			$value = array();
			while (true) {
				$nextToken = $this->expectNextToken();
				if ($nextToken->getType() === wfWAFRuleLexer::T_CLOSE_BRACKET) {
					break;
				}
				if ($nextToken->getType() === wfWAFRuleLexer::T_COMMA) {
					continue;
				}
				$this->index--;
				$value[] = $this->expectLiteral();
			}
		} else {
			$value = $expectedToken->getValue();
		}
		return $value;
	}

	/**
	 * @param wfWAFLexerToken $token
	 * @param string|array $value
	 * @return bool
	 */
	private function isIdentifierWithValue($token, $value) {
		return $token && $token->getType() === wfWAFRuleLexer::T_IDENTIFIER &&
		(is_array($value) ? in_array($token->getLowerCaseValue(), array_map('strtolower', $value)) :
			$token->getLowerCaseValue() === strtolower($value));
	}

	/**
	 * @param wfWAFLexerToken $token
	 * @return bool
	 */
	protected function isCommentToken($token) {
		return $token->getType() === wfWAFRuleLexer::T_MULTIPLE_LINE_COMMENT || $token->getType() === wfWAFRuleLexer::T_SINGLE_LINE_COMMENT;
	}

	/**
	 * @return wfWAF
	 */
	public function getWAF() {
		return $this->waf;
	}

	/**
	 * @param wfWAF $waf
	 */
	public function setWAF($waf) {
		$this->waf = $waf;
	}
}

class wfWAFRuleParserAction {

	private $ruleID;
	private $type;
	private $category;
	private $score;
	private $description;
	private $whitelist = 1;
	private $action;

	/**
	 * @param string $param
	 * @param mixed $value
	 */
	public function set($param, $value) {
		$propLinkTable = array(
			'id' => 'ruleID',
		);
		if (array_key_exists($param, $propLinkTable)) {
			$param = $propLinkTable[$param];
		}
		if (property_exists($this, $param)) {
			$this->$param = trim($value, '\'"');
		}
	}

	/**
	 * @return mixed
	 */
	public function getRuleID() {
		return $this->ruleID;
	}

	/**
	 * @param mixed $ruleID
	 */
	public function setRuleID($ruleID) {
		$this->ruleID = $ruleID;
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
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param mixed $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * @return mixed
	 */
	public function getScore() {
		return $this->score;
	}

	/**
	 * @param mixed $score
	 */
	public function setScore($score) {
		$this->score = $score;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getWhitelist() {
		return $this->whitelist;
	}

	/**
	 * @param mixed $whitelist
	 */
	public function setWhitelist($whitelist) {
		$this->whitelist = $whitelist;
	}

	/**
	 * @return mixed
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param mixed $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}
}


class wfWAFRuleParserURLParam {
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $param;
	/**
	 * @var null
	 */
	private $rules;
	/**
	 * @var null
	 */
	private $conditional;
	/**
	 * @var float
	 */
	private $minVersion;

	/**
	 * @param string $param
	 * @param mixed $value
	 */
	public function set($param, $value) {
		if (property_exists($this, $param)) {
			$this->$param = trim($value, '\'"');
		}
	}

	/**
	 * @param string $url
	 * @param string $param
	 * @param null $rules
	 */
	public function __construct($url = null, $param = null, $rules = null, $conditional = null, $minVersion = null) {
		$this->url = $url;
		$this->param = $param;
		$this->rules = $rules;
		$this->conditional = $conditional;
		$this->minVersion = $minVersion;
	}

	/**
	 * Return format:
	 * blacklistParam(url='/\/uploadify\.php$/i', param=request.fileNames.Filedata, rules=[3, 14], conditional=(match('1', request.body.field)))
	 *
	 * @param string $action
	 * @return string
	 */
	public function renderRule($action) {
		return sprintf('%s(url=%s, param=%s%s%s)', $action,
			wfWAFRule::exportString($this->getUrl()),
			$this->renderParam($this->getParam()),
			$this->getRules() ? ', rules=[' . join(', ', array_map('intval', $this->getRules())) . ']' : '',
			$this->getConditional() ? ', conditional=(' . $this->getConditional()->renderRule() . ')' : '');
			//minVersion not included in re-rendering
	}

	/**
	 * @param string $param
	 * @return mixed
	 */
	private function renderParam($param) {
		if (preg_match('/([a-zA-Z_][\\w_]*?\\.[a-zA-Z_][\\w_]*)(.*)/', $param, $matches)) {
			list(, $global, $params) = $matches;
			if (strlen($params) > 0) {
				if (preg_match_all('/\\[([^\\]]*?)\\]/', $params, $matches)) {
					$rendered = $global;
					foreach ($matches[1] as $prop) {
						$single = "'" . str_replace(array("'", '\\'), array("\\'", "\\\\"), $prop) . "'";
						$double = '"' . str_replace(array('"', '\\'), array('\\"', "\\\\"), $prop) . '"';
						$rendered .= sprintf('[%s]', strlen($single) <= strlen($double) ? $single : $double);
					}
					return $rendered;
				}
			}
		}
		return $param;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getParam() {
		return $this->param;
	}

	/**
	 * @param string $param
	 */
	public function setParam($param) {
		$this->param = $param;
	}

	/**
	 * @return null
	 */
	public function getRules() {
		return $this->rules;
	}

	/**
	 * @param null $rules
	 */
	public function setRules($rules) {
		$this->rules = $rules;
	}
	
	/**
	 * @return null
	 */
	public function getConditional() {
		return $this->conditional;
	}
	
	/**
	 * @param null $conditional
	 */
	public function setConditional($conditional) {
		$this->conditional = $conditional;
	}
	
	/**
	 * @return float|null
	 */
	public function getMinVersion() {
		return $this->minVersion;
	}
	
	/**
	 * @param float $minVersion
	 */
	public function setMinVersion($minVersion) {
		$this->minVersion = $minVersion;
	}
}

class wfWAFRuleParserSyntaxError extends wfWAFParserSyntaxError {

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
}

class wfWAFRuleVariable {
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var mixed|null
	 */
	private $value;
	/**
	 * @var wfWAF
	 */
	private $waf;


	/**
	 * wfWAFRuleVariable constructor.
	 * @param wfWAF $waf
	 * @param string $name
	 * @param mixed $value
	 */
	public function __construct($waf, $name, $value = null) {
		$this->waf = $waf;
		$this->name = $name;
		$this->value = $value;
	}

	public function render() {
		return sprintf('new %s($this, %s, %s)', get_class($this),
			var_export($this->getName(), true), var_export($this->getValue(), true));
	}

	public function renderRule() {
		return sprintf('%s', $this->getName());
	}

	public function renderValue() {
		return wfWAFRule::exportString($this);
	}

	public function __toString() {
		$value = $this->getValue();
		if (is_string($value)) {
			return $value;
		}
		return (string) $this->getWAF()->getVariable($this->getName());
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return mixed|null
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param mixed|null $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	 * @return wfWAF
	 */
	public function getWAF() {
		return $this->waf;
	}

	/**
	 * @param wfWAF $waf
	 */
	public function setWAF($waf) {
		$this->waf = $waf;
	}
}
