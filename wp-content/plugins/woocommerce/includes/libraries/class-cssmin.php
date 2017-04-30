<?php
/**
 * CssMin - A (simple) css minifier with benefits
 *
 * --
 * Copyright (c) 2011 Joe Scylla <joe.scylla@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * --
 *
 * @package		CssMin
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstract definition of a CSS token class.
 *
 * Every token has to extend this class.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssToken
	{
	/**
	 * Returns the token as string.
	 *
	 * @return string
	 */
	abstract public function __toString();
	}

/**
 * Abstract definition of a for a ruleset start token.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssRulesetStartToken extends aCssToken
	{

	}

/**
 * Abstract definition of a for ruleset end token.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssRulesetEndToken extends aCssToken
	{
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "}";
		}
	}

/**
 * Abstract definition of a parser plugin.
 *
 * Every parser plugin have to extend this class. A parser plugin contains the logic to parse one or aspects of a
 * stylesheet.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssParserPlugin
	{
	/**
	 * Plugin configuration.
	 *
	 * @var array
	 */
	protected $configuration = array();
	/**
	 * The CssParser of the plugin.
	 *
	 * @var CssParser
	 */
	protected $parser = null;
	/**
	 * Plugin buffer.
	 *
	 * @var string
	 */
	protected $buffer = "";
	/**
	 * Constructor.
	 *
	 * @param CssParser $parser The CssParser object of this plugin.
	 * @param array $configuration Plugin configuration [optional]
	 * @return void
	 */
	public function __construct(CssParser $parser, array $configuration = null)
		{
		$this->configuration	= $configuration;
		$this->parser			= $parser;
		}
	/**
	 * Returns the array of chars triggering the parser plugin.
	 *
	 * @return array
	 */
	abstract public function getTriggerChars();
	/**
	 * Returns the array of states triggering the parser plugin or FALSE if every state will trigger the parser plugin.
	 *
	 * @return array
	 */
	abstract public function getTriggerStates();
	/**
	 * Parser routine of the plugin.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	abstract public function parse($index, $char, $previousChar, $state);
	}

/**
 * Abstract definition of a minifier plugin class.
 *
 * Minifier plugin process the parsed tokens one by one to apply changes to the token. Every minifier plugin has to
 * extend this class.
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssMinifierPlugin
	{
	/**
	 * Plugin configuration.
	 *
	 * @var array
	 */
	protected $configuration = array();
	/**
	 * The CssMinifier of the plugin.
	 *
	 * @var CssMinifier
	 */
	protected $minifier = null;
	/**
	 * Constructor.
	 *
	 * @param CssMinifier $minifier The CssMinifier object of this plugin.
	 * @param array $configuration Plugin configuration [optional]
	 * @return void
	 */
	public function __construct(CssMinifier $minifier, array $configuration = array())
		{
		$this->configuration	= $configuration;
		$this->minifier			= $minifier;
		}
	/**
	 * Apply the plugin to the token.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	abstract public function apply(aCssToken &$token);
	/**
	 * --
	 *
	 * @return array
	 */
	abstract public function getTriggerTokens();
	}

/**
 * Abstract definition of a minifier filter class.
 *
 * Minifier filters allows a pre-processing of the parsed token to add, edit or delete tokens. Every minifier filter
 * has to extend this class.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssMinifierFilter
	{
	/**
	 * Filter configuration.
	 *
	 * @var array
	 */
	protected $configuration = array();
	/**
	 * The CssMinifier of the filter.
	 *
	 * @var CssMinifier
	 */
	protected $minifier = null;
	/**
	 * Constructor.
	 *
	 * @param CssMinifier $minifier The CssMinifier object of this plugin.
	 * @param array $configuration Filter configuration [optional]
	 * @return void
	 */
	public function __construct(CssMinifier $minifier, array $configuration = array())
		{
		$this->configuration	= $configuration;
		$this->minifier			= $minifier;
		}
	/**
	 * Filter the tokens.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	abstract public function apply(array &$tokens);
	}

/**
 * Abstract formatter definition.
 *
 * Every formatter have to extend this class.
 *
 * @package		CssMin/Formatter
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssFormatter
	{
	/**
	 * Indent string.
	 *
	 * @var string
	 */
	protected $indent = "    ";
	/**
	 * Declaration padding.
	 *
	 * @var integer
	 */
	protected $padding = 0;
	/**
	 * Tokens.
	 *
	 * @var array
	 */
	protected $tokens = array();
	/**
	 * Constructor.
	 *
	 * @param array $tokens Array of CssToken
	 * @param string $indent Indent string [optional]
	 * @param integer $padding Declaration value padding [optional]
	 */
	public function __construct(array $tokens, $indent = null, $padding = null)
		{
		$this->tokens	= $tokens;
		$this->indent	= !is_null($indent) ? $indent : $this->indent;
		$this->padding	= !is_null($padding) ? $padding : $this->padding;
		}
	/**
	 * Returns the array of aCssToken as formatted string.
	 *
	 * @return string
	 */
	abstract public function __toString();
	}

/**
 * Abstract definition of a ruleset declaration token.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssDeclarationToken extends aCssToken
	{
	/**
	 * Is the declaration flagged as important?
	 *
	 * @var boolean
	 */
	public $IsImportant = false;
	/**
	 * Is the declaration flagged as last one of the ruleset?
	 *
	 * @var boolean
	 */
	public $IsLast = false;
	/**
	 * Property name of the declaration.
	 *
	 * @var string
	 */
	public $Property = "";
	/**
	 * Value of the declaration.
	 *
	 * @var string
	 */
	public $Value = "";
	/**
	 * Set the properties of the @font-face declaration.
	 *
	 * @param string $property Property of the declaration
	 * @param string $value Value of the declaration
	 * @param boolean $isImportant Is the !important flag is set?
	 * @param boolean $IsLast Is the declaration the last one of the block?
	 * @return void
	 */
	public function __construct($property, $value, $isImportant = false, $isLast = false)
		{
		$this->Property		= $property;
		$this->Value		= $value;
		$this->IsImportant	= $isImportant;
		$this->IsLast		= $isLast;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return $this->Property . ":" . $this->Value . ($this->IsImportant ? " !important" : "") . ($this->IsLast ? "" : ";");
		}
	}

/**
 * Abstract definition of a for at-rule block start token.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssAtBlockStartToken extends aCssToken
	{

	}

/**
 * Abstract definition of a for at-rule block end token.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
abstract class aCssAtBlockEndToken extends aCssToken
	{
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "}";
		}
	}

/**
 * {@link aCssFromatter Formatter} returning the CSS source in {@link http://goo.gl/etzLs Whitesmiths indent style}.
 *
 * @package		CssMin/Formatter
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssWhitesmithsFormatter extends aCssFormatter
	{
	/**
	 * Implements {@link aCssFormatter::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		$r				= array();
		$level			= 0;
		for ($i = 0, $l = count($this->tokens); $i < $l; $i++)
			{
			$token		= $this->tokens[$i];
			$class		= get_class($token);
			$indent 	= str_repeat($this->indent, $level);
			if ($class === "CssCommentToken")
				{
				$lines = array_map("trim", explode("\n", $token->Comment));
				for ($ii = 0, $ll = count($lines); $ii < $ll; $ii++)
					{
					$r[] = $indent . (substr($lines[$ii], 0, 1) == "*" ? " " : "") . $lines[$ii];
					}
				}
			elseif ($class === "CssAtCharsetToken")
				{
				$r[] = $indent . "@charset " . $token->Charset . ";";
				}
			elseif ($class === "CssAtFontFaceStartToken")
				{
				$r[] = $indent . "@font-face";
				$r[] = $this->indent . $indent . "{";
				$level++;
				}
			elseif ($class === "CssAtImportToken")
				{
				$r[] = $indent . "@import " . $token->Import . " " . implode(", ", $token->MediaTypes) . ";";
				}
			elseif ($class === "CssAtKeyframesStartToken")
				{
				$r[] = $indent . "@keyframes \"" . $token->Name . "\"";
				$r[] = $this->indent . $indent . "{";
				$level++;
				}
			elseif ($class === "CssAtMediaStartToken")
				{
				$r[] = $indent . "@media " . implode(", ", $token->MediaTypes);
				$r[] = $this->indent . $indent . "{";
				$level++;
				}
			elseif ($class === "CssAtPageStartToken")
				{
				$r[] = $indent . "@page";
				$r[] = $this->indent . $indent . "{";
				$level++;
				}
			elseif ($class === "CssAtVariablesStartToken")
				{
				$r[] = $indent . "@variables " . implode(", ", $token->MediaTypes);
				$r[] = $this->indent . $indent . "{";
				$level++;
				}
			elseif ($class === "CssRulesetStartToken" || $class === "CssAtKeyframesRulesetStartToken")
				{
				$r[] = $indent . implode(", ", $token->Selectors);
				$r[] = $this->indent . $indent . "{";
				$level++;
				}
			elseif ($class == "CssAtFontFaceDeclarationToken"
				|| $class === "CssAtKeyframesRulesetDeclarationToken"
				|| $class === "CssAtPageDeclarationToken"
				|| $class == "CssAtVariablesDeclarationToken"
				|| $class === "CssRulesetDeclarationToken"
				)
				{
				$declaration = $indent . $token->Property . ": ";
				if ($this->padding)
					{
					$declaration = str_pad($declaration, $this->padding, " ", STR_PAD_RIGHT);
					}
				$r[] = $declaration . $token->Value . ($token->IsImportant ? " !important" : "") . ";";
				}
			elseif ($class === "CssAtFontFaceEndToken"
				|| $class === "CssAtMediaEndToken"
				|| $class === "CssAtKeyframesEndToken"
				|| $class === "CssAtKeyframesRulesetEndToken"
				|| $class === "CssAtPageEndToken"
				|| $class === "CssAtVariablesEndToken"
				|| $class === "CssRulesetEndToken"
				)
				{
				$r[] = $indent . "}";
				$level--;
				}
			}
		return implode("\n", $r);
		}
	}

/**
 * This {@link aCssMinifierPlugin} will process var-statement and sets the declaration value to the variable value.
 *
 * This plugin only apply the variable values. The variable values itself will get parsed by the
 * {@link CssVariablesMinifierFilter}.
 *
 * Example:
 * <code>
 * @variables
 * 		{
 * 		defaultColor: black;
 * 		}
 * color: var(defaultColor);
 * </code>
 *
 * Will get converted to:
 * <code>
 * color:black;
 * </code>
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssVariablesMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Regular expression matching a value.
	 *
	 * @var string
	 */
	private $reMatch = "/var\((.+)\)/iSU";
	/**
	 * Parsed variables.
	 *
	 * @var array
	 */
	private $variables = null;
	/**
	 * Returns the variables.
	 *
	 * @return array
	 */
	public function getVariables()
		{
		return $this->variables;
		}
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (stripos($token->Value, "var") !== false && preg_match_all($this->reMatch, $token->Value, $m))
			{
			$mediaTypes	= $token->MediaTypes;
			if (!in_array("all", $mediaTypes))
				{
				$mediaTypes[] = "all";
				}
			for ($i = 0, $l = count($m[0]); $i < $l; $i++)
				{
				$variable	= trim($m[1][$i]);
				foreach ($mediaTypes as $mediaType)
					{
					if (isset($this->variables[$mediaType], $this->variables[$mediaType][$variable]))
						{
						// Variable value found => set the declaration value to the variable value and return
						$token->Value = str_replace($m[0][$i], $this->variables[$mediaType][$variable], $token->Value);
						continue 2;
						}
					}
				// If no value was found trigger an error and replace the token with a CssNullToken
				CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": No value found for variable <code>" . $variable . "</code> in media types <code>" . implode(", ", $mediaTypes) . "</code>", (string) $token));
				$token = new CssNullToken();
				return true;
				}
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	/**
	 * Sets the variables.
	 *
	 * @param array $variables Variables to set
	 * @return void
	 */
	public function setVariables(array $variables)
		{
		$this->variables = $variables;
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} will parse the variable declarations out of @variables at-rule
 * blocks. The variables will get store in the {@link CssVariablesMinifierPlugin} that will apply the variables to
 * declaration.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssVariablesMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$variables			= array();
		$defaultMediaTypes	= array("all");
		$mediaTypes			= array();
		$remove				= array();
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			// @variables at-rule block found
			if (get_class($tokens[$i]) === "CssAtVariablesStartToken")
				{
				$remove[] = $i;
				$mediaTypes = (count($tokens[$i]->MediaTypes) == 0 ? $defaultMediaTypes : $tokens[$i]->MediaTypes);
				foreach ($mediaTypes as $mediaType)
					{
					if (!isset($variables[$mediaType]))
						{
						$variables[$mediaType] = array();
						}
					}
				// Read the variable declaration tokens
				for($i = $i; $i < $l; $i++)
					{
					// Found a variable declaration => read the variable values
					if (get_class($tokens[$i]) === "CssAtVariablesDeclarationToken")
						{
						foreach ($mediaTypes as $mediaType)
							{
							$variables[$mediaType][$tokens[$i]->Property] = $tokens[$i]->Value;
							}
						$remove[] = $i;
						}
					// Found the variables end token => break;
					elseif (get_class($tokens[$i]) === "CssAtVariablesEndToken")
						{
						$remove[] = $i;
						break;
						}
					}
				}
			}
		// Variables in @variables at-rule blocks
		foreach($variables as $mediaType => $null)
			{
			foreach($variables[$mediaType] as $variable => $value)
				{
				// If a var() statement in a variable value found...
				if (stripos($value, "var") !== false && preg_match_all("/var\((.+)\)/iSU", $value, $m))
					{
					// ... then replace the var() statement with the variable values.
					for ($i = 0, $l = count($m[0]); $i < $l; $i++)
						{
						$variables[$mediaType][$variable] = str_replace($m[0][$i], (isset($variables[$mediaType][$m[1][$i]]) ? $variables[$mediaType][$m[1][$i]] : ""), $variables[$mediaType][$variable]);
						}
					}
				}
			}
		// Remove the complete @variables at-rule block
		foreach ($remove as $i)
			{
			$tokens[$i] = null;
			}
		if (!($plugin = $this->minifier->getPlugin("CssVariablesMinifierPlugin")))
			{
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": The plugin <code>CssVariablesMinifierPlugin</code> was not found but is required for <code>" . __CLASS__ . "</code>"));
			}
		else
			{
			$plugin->setVariables($variables);
			}
		return count($remove);
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for preserve parsing url() values.
 *
 * This plugin return no {@link aCssToken CssToken} but ensures that url() values will get parsed properly.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssUrlParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("(", ")");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return false;
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of string
		if ($char === "(" && strtolower(substr($this->parser->getSource(), $index - 3, 4)) === "url(" && $state !== "T_URL")
			{
			$this->parser->pushState("T_URL");
			$this->parser->setExclusive(__CLASS__);
			}
		// Escaped LF in url => remove escape backslash and LF
		elseif ($char === "\n" && $previousChar === "\\" && $state === "T_URL")
			{
			$this->parser->setBuffer(substr($this->parser->getBuffer(), 0, -2));
			}
		// Parse error: Unescaped LF in string literal
		elseif ($char === "\n" && $previousChar !== "\\" && $state === "T_URL")
			{
			$line = $this->parser->getBuffer();
			$this->parser->setBuffer(substr($this->parser->getBuffer(), 0, -1) . ")"); // Replace the LF with the url string delimiter
			$this->parser->popState();
			$this->parser->unsetExclusive();
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated string literal", $line . "_"));
			}
		// End of string
		elseif ($char === ")" && $state === "T_URL")
			{
			$this->parser->popState();
			$this->parser->unsetExclusive();
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for preserve parsing string values.
 *
 * This plugin return no {@link aCssToken CssToken} but ensures that string values will get parsed properly.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssStringParserPlugin extends aCssParserPlugin
	{
	/**
	 * Current string delimiter char.
	 *
	 * @var string
	 */
	private $delimiterChar = null;
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("\"", "'", "\n");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return false;
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of string
		if (($char === "\"" || $char === "'") && $state !== "T_STRING")
			{
			$this->delimiterChar = $char;
			$this->parser->pushState("T_STRING");
			$this->parser->setExclusive(__CLASS__);
			}
		// Escaped LF in string => remove escape backslash and LF
		elseif ($char === "\n" && $previousChar === "\\" && $state === "T_STRING")
			{
			$this->parser->setBuffer(substr($this->parser->getBuffer(), 0, -2));
			}
		// Parse error: Unescaped LF in string literal
		elseif ($char === "\n" && $previousChar !== "\\" && $state === "T_STRING")
			{
			$line = $this->parser->getBuffer();
			$this->parser->popState();
			$this->parser->unsetExclusive();
			$this->parser->setBuffer(substr($this->parser->getBuffer(), 0, -1) . $this->delimiterChar); // Replace the LF with the current string char
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated string literal", $line . "_"));
			$this->delimiterChar = null;
			}
		// End of string
		elseif ($char === $this->delimiterChar && $state === "T_STRING")
			{
			// If the Previous char is a escape char count the amount of the previous escape chars. If the amount of
			// escape chars is uneven do not end the string
			if ($previousChar == "\\")
				{
				$source	= $this->parser->getSource();
				$c		= 1;
				$i		= $index - 2;
				while (substr($source, $i, 1) === "\\")
					{
					$c++; $i--;
					}
				if ($c % 2)
					{
					return false;
					}
				}
			$this->parser->popState();
			$this->parser->unsetExclusive();
			$this->delimiterChar = null;
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} sorts the ruleset declarations of a ruleset by name.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Rowan Beentje <http://assanka.net>
 * @copyright	Rowan Beentje <http://assanka.net>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssSortRulesetPropertiesMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value larger than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$r = 0;
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			// Only look for ruleset start rules
			if (get_class($tokens[$i]) !== "CssRulesetStartToken") { continue; }
			// Look for the corresponding ruleset end
			$endIndex = false;
			for ($ii = $i + 1; $ii < $l; $ii++)
				{
				if (get_class($tokens[$ii]) !== "CssRulesetEndToken") { continue; }
				$endIndex = $ii;
				break;
				}
			if (!$endIndex) { break; }
			$startIndex = $i;
			$i = $endIndex;
			// Skip if there's only one token in this ruleset
			if ($endIndex - $startIndex <= 2) { continue; }
			// Ensure that everything between the start and end is a declaration token, for safety
			for ($ii = $startIndex + 1; $ii < $endIndex; $ii++)
				{
				if (get_class($tokens[$ii]) !== "CssRulesetDeclarationToken") { continue(2); }
				}
			$declarations = array_slice($tokens, $startIndex + 1, $endIndex - $startIndex - 1);
			// Check whether a sort is required
			$sortRequired = $lastPropertyName = false;
			foreach ($declarations as $declaration)
				{
				if ($lastPropertyName)
					{
					if (strcmp($lastPropertyName, $declaration->Property) > 0)
						{
						$sortRequired = true;
						break;
						}
					}
				$lastPropertyName = $declaration->Property;
				}
			if (!$sortRequired) { continue; }
			// Arrange the declarations alphabetically by name
			usort($declarations, array(__CLASS__, "userDefinedSort1"));
			// Update "IsLast" property
			for ($ii = 0, $ll = count($declarations) - 1; $ii <= $ll; $ii++)
				{
				if ($ii == $ll)
					{
					$declarations[$ii]->IsLast = true;
					}
				else
					{
					$declarations[$ii]->IsLast = false;
					}
				}
			// Splice back into the array.
			array_splice($tokens, $startIndex + 1, $endIndex - $startIndex - 1, $declarations);
			$r += $endIndex - $startIndex - 1;
			}
		return $r;
		}
	/**
	 * User defined sort function.
	 *
	 * @return integer
	 */
	public static function userDefinedSort1($a, $b)
		{
		return strcmp($a->Property, $b->Property);
		}
	}

/**
 * This {@link aCssToken CSS token} represents the start of a ruleset.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRulesetStartToken extends aCssRulesetStartToken
	{
	/**
	 * Array of selectors.
	 *
	 * @var array
	 */
	public $Selectors = array();
	/**
	 * Set the properties of a ruleset token.
	 *
	 * @param array $selectors Selectors of the ruleset
	 * @return void
	 */
	public function __construct(array $selectors = array())
		{
		$this->Selectors = $selectors;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return implode(",", $this->Selectors) . "{";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing ruleset block with including declarations.
 *
 * Found rulesets will add a {@link CssRulesetStartToken} and {@link CssRulesetEndToken} to the
 * parser; including declarations as {@link CssRulesetDeclarationToken}.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRulesetParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array(",", "{", "}", ":", ";");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_MEDIA", "T_RULESET::SELECTORS", "T_RULESET", "T_RULESET_DECLARATION");
		}
	/**
	 * Selectors.
	 *
	 * @var array
	 */
	private $selectors = array();
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of Ruleset and selectors
		if ($char === "," && ($state === "T_DOCUMENT" || $state === "T_AT_MEDIA" || $state === "T_RULESET::SELECTORS"))
			{
			if ($state !== "T_RULESET::SELECTORS")
				{
				$this->parser->pushState("T_RULESET::SELECTORS");
				}
			$this->selectors[] = $this->parser->getAndClearBuffer(",{");
			}
		// End of selectors and start of declarations
		elseif ($char === "{" && ($state === "T_DOCUMENT" || $state === "T_AT_MEDIA" || $state === "T_RULESET::SELECTORS"))
			{
			if ($this->parser->getBuffer() !== "")
				{
				$this->selectors[] = $this->parser->getAndClearBuffer(",{");
				if ($state == "T_RULESET::SELECTORS")
					{
					$this->parser->popState();
					}
				$this->parser->pushState("T_RULESET");
				$this->parser->appendToken(new CssRulesetStartToken($this->selectors));
				$this->selectors = array();
				}
			}
		// Start of declaration
		elseif ($char === ":" && $state === "T_RULESET")
			{
			$this->parser->pushState("T_RULESET_DECLARATION");
			$this->buffer = $this->parser->getAndClearBuffer(":;", true);
			}
		// Unterminated ruleset declaration
		elseif ($char === ":" && $state === "T_RULESET_DECLARATION")
			{
			// Ignore Internet Explorer filter declarations
			if ($this->buffer === "filter")
				{
				return false;
				}
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated declaration", $this->buffer . ":" . $this->parser->getBuffer() . "_"));
			}
		// End of declaration
		elseif (($char === ";" || $char === "}") && $state === "T_RULESET_DECLARATION")
			{
			$value = $this->parser->getAndClearBuffer(";}");
			if (strtolower(substr($value, -10, 10)) === "!important")
				{
				$value = trim(substr($value, 0, -10));
				$isImportant = true;
				}
			else
				{
				$isImportant = false;
				}
			$this->parser->popState();
			$this->parser->appendToken(new CssRulesetDeclarationToken($this->buffer, $value, $this->parser->getMediaTypes(), $isImportant));
			// Declaration ends with a right curly brace; so we have to end the ruleset
			if ($char === "}")
				{
				$this->parser->appendToken(new CssRulesetEndToken());
				$this->parser->popState();
				}
			$this->buffer = "";
			}
		// End of ruleset
		elseif ($char === "}" && $state === "T_RULESET")
			{
			$this->parser->popState();
			$this->parser->clearBuffer();
			$this->parser->appendToken(new CssRulesetEndToken());
			$this->buffer = "";
			$this->selectors = array();
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 *  This {@link aCssToken CSS token} represents the end of a ruleset.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRulesetEndToken extends aCssRulesetEndToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents a ruleset declaration.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRulesetDeclarationToken extends aCssDeclarationToken
	{
	/**
	 * Media types of the declaration.
	 *
	 * @var array
	 */
	public $MediaTypes = array("all");
	/**
	 * Set the properties of a ddocument- or at-rule @media level declaration.
	 *
	 * @param string $property Property of the declaration
	 * @param string $value Value of the declaration
	 * @param mixed $mediaTypes Media types of the declaration
	 * @param boolean $isImportant Is the !important flag is set
	 * @param boolean $isLast Is the declaration the last one of the ruleset
	 * @return void
	 */
	public function __construct($property, $value, $mediaTypes = null, $isImportant = false, $isLast = false)
		{
		parent::__construct($property, $value, $isImportant, $isLast);
		$this->MediaTypes	= $mediaTypes ? $mediaTypes : array("all");
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} sets the IsLast property of any last declaration in a ruleset,
 * @font-face at-rule or @page at-rule block. If the property IsLast is TRUE the decrations will get stringified
 * without tailing semicolon.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRemoveLastDelarationSemiColonMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			$current	= get_class($tokens[$i]);
			$next		= isset($tokens[$i+1]) ? get_class($tokens[$i+1]) : false;
			if (($current === "CssRulesetDeclarationToken" && $next === "CssRulesetEndToken") ||
				($current === "CssAtFontFaceDeclarationToken" && $next === "CssAtFontFaceEndToken") ||
				($current === "CssAtPageDeclarationToken" && $next === "CssAtPageEndToken"))
				{
				$tokens[$i]->IsLast = true;
				}
			}
		return 0;
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} will remove any empty rulesets (including @keyframes at-rule block
 * rulesets).
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRemoveEmptyRulesetsMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$r = 0;
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			$current	= get_class($tokens[$i]);
			$next		= isset($tokens[$i + 1]) ? get_class($tokens[$i + 1]) : false;
			if (($current === "CssRulesetStartToken" && $next === "CssRulesetEndToken") ||
				($current === "CssAtKeyframesRulesetStartToken" && $next === "CssAtKeyframesRulesetEndToken" && !array_intersect(array("from", "0%", "to", "100%"), array_map("strtolower", $tokens[$i]->Selectors)))
				)
				{
				$tokens[$i]		= null;
				$tokens[$i + 1]	= null;
				$i++;
				$r = $r + 2;
				}
			}
		return $r;
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} will remove any empty @font-face, @keyframes, @media and @page
 * at-rule blocks.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRemoveEmptyAtBlocksMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$r = 0;
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			$current	= get_class($tokens[$i]);
			$next		= isset($tokens[$i + 1]) ? get_class($tokens[$i + 1]) : false;
			if (($current === "CssAtFontFaceStartToken" && $next === "CssAtFontFaceEndToken") ||
				($current === "CssAtKeyframesStartToken" && $next === "CssAtKeyframesEndToken") ||
				($current === "CssAtPageStartToken" && $next === "CssAtPageEndToken") ||
				($current === "CssAtMediaStartToken" && $next === "CssAtMediaEndToken"))
				{
				$tokens[$i]		= null;
				$tokens[$i + 1]	= null;
				$i++;
				$r = $r + 2;
				}
			}
		return $r;
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} will remove any comments from the array of parsed tokens.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssRemoveCommentsMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$r = 0;
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if (get_class($tokens[$i]) === "CssCommentToken")
				{
				$tokens[$i] = null;
				$r++;
				}
			}
		return $r;
		}
	}

/**
 * CSS Parser.
 *
 * @package		CssMin/Parser
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssParser
	{
	/**
	 * Parse buffer.
	 *
	 * @var string
	 */
	private $buffer = "";
	/**
	 * {@link aCssParserPlugin Plugins}.
	 *
	 * @var array
	 */
	private $plugins = array();
	/**
	 * Source to parse.
	 *
	 * @var string
	 */
	private $source = "";
	/**
	 * Current state.
	 *
	 * @var integer
	 */
	private $state = "T_DOCUMENT";
	/**
	 * Exclusive state.
	 *
	 * @var string
	 */
	private $stateExclusive = false;
	/**
	 * Media types state.
	 *
	 * @var mixed
	 */
	private $stateMediaTypes = false;
	/**
	 * State stack.
	 *
	 * @var array
	 */
	private $states = array("T_DOCUMENT");
	/**
	 * Parsed tokens.
	 *
	 * @var array
	 */
	private $tokens = array();
	/**
	 * Constructer.
	 *
	 *  Create instances of the used {@link aCssParserPlugin plugins}.
	 *
	 * @param string $source CSS source [optional]
	 * @param array $plugins Plugin configuration [optional]
	 * @return void
	 */
	public function __construct($source = null, array $plugins = null)
		{
		$plugins = array_merge(array
			(
			"Comment"		=> true,
			"String"		=> true,
			"Url"			=> true,
			"Expression"	=> true,
			"Ruleset"		=> true,
			"AtCharset"		=> true,
			"AtFontFace"	=> true,
			"AtImport"		=> true,
			"AtKeyframes"	=> true,
			"AtMedia"		=> true,
			"AtPage"		=> true,
			"AtVariables"	=> true
			), is_array($plugins) ? $plugins : array());
		// Create plugin instances
		foreach ($plugins as $name => $config)
			{
			if ($config !== false)
				{
				$class	= "Css" . $name . "ParserPlugin";
				$config = is_array($config) ? $config : array();
				if (class_exists($class))
					{
					$this->plugins[] = new $class($this, $config);
					}
				else
					{
					CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": The plugin <code>" . $name . "</code> with the class name <code>" . $class . "</code> was not found"));
					}
				}
			}
		if (!is_null($source))
			{
			$this->parse($source);
			}
		}
	/**
	 * Append a token to the array of tokens.
	 *
	 * @param aCssToken $token Token to append
	 * @return void
	 */
	public function appendToken(aCssToken $token)
		{
		$this->tokens[] = $token;
		}
	/**
	 * Clears the current buffer.
	 *
	 * @return void
	 */
	public function clearBuffer()
		{
		$this->buffer = "";
		}
	/**
	 * Returns and clear the current buffer.
	 *
	 * @param string $trim Chars to use to trim the returned buffer
	 * @param boolean $tolower if TRUE the returned buffer will get converted to lower case
	 * @return string
	 */
	public function getAndClearBuffer($trim = "", $tolower = false)
		{
		$r = $this->getBuffer($trim, $tolower);
		$this->buffer = "";
		return $r;
		}
	/**
	 * Returns the current buffer.
	 *
	 * @param string $trim Chars to use to trim the returned buffer
	 * @param boolean $tolower if TRUE the returned buffer will get converted to lower case
	 * @return string
	 */
	public function getBuffer($trim = "", $tolower = false)
		{
		$r = $this->buffer;
		if ($trim)
			{
			$r = trim($r, " \t\n\r\0\x0B" . $trim);
			}
		if ($tolower)
			{
			$r = strtolower($r);
			}
		return $r;
		}
	/**
	 * Returns the current media types state.
	 *
	 * @return array
	 */
	public function getMediaTypes()
		{
		return $this->stateMediaTypes;
		}
	/**
	 * Returns the CSS source.
	 *
	 * @return string
	 */
	public function getSource()
		{
		return $this->source;
		}
	/**
	 * Returns the current state.
	 *
	 * @return integer The current state
	 */
	public function getState()
		{
		return $this->state;
		}
	/**
	 * Returns a plugin by class name.
	 *
	 * @param string $name Class name of the plugin
	 * @return aCssParserPlugin
	 */
	public function getPlugin($class)
		{
		static $index = null;
		if (is_null($index))
			{
			$index = array();
			for ($i = 0, $l = count($this->plugins); $i < $l; $i++)
				{
				$index[get_class($this->plugins[$i])] = $i;
				}
			}
		return isset($index[$class]) ? $this->plugins[$index[$class]] : false;
		}
	/**
	 * Returns the parsed tokens.
	 *
	 * @return array
	 */
	public function getTokens()
		{
		return $this->tokens;
		}
	/**
	 * Returns if the current state equals the passed state.
	 *
	 * @param integer $state State to compare with the current state
	 * @return boolean TRUE is the state equals to the passed state; FALSE if not
	 */
	public function isState($state)
		{
		return ($this->state == $state);
		}
	/**
	 * Parse the CSS source and return a array with parsed tokens.
	 *
	 * @param string $source CSS source
	 * @return array Array with tokens
	 */
	public function parse($source)
		{
		// Reset
		$this->source = "";
		$this->tokens = array();
		// Create a global and plugin lookup table for trigger chars; set array of plugins as local variable and create
		// several helper variables for plugin handling
		$globalTriggerChars		= "";
		$plugins				= $this->plugins;
		$pluginCount			= count($plugins);
		$pluginIndex			= array();
		$pluginTriggerStates	= array();
		$pluginTriggerChars		= array();
		for ($i = 0, $l = count($plugins); $i < $l; $i++)
			{
			$tPluginClassName				= get_class($plugins[$i]);
			$pluginTriggerChars[$i]			= implode("", $plugins[$i]->getTriggerChars());
			$tPluginTriggerStates			= $plugins[$i]->getTriggerStates();
			$pluginTriggerStates[$i]		= $tPluginTriggerStates === false ? false : "|" . implode("|", $tPluginTriggerStates) . "|";
			$pluginIndex[$tPluginClassName]	= $i;
			for ($ii = 0, $ll = strlen($pluginTriggerChars[$i]); $ii < $ll; $ii++)
				{
				$c = substr($pluginTriggerChars[$i], $ii, 1);
				if (strpos($globalTriggerChars, $c) === false)
					{
					$globalTriggerChars .= $c;
					}
				}
			}
		// Normalise line endings
		$source			= str_replace("\r\n", "\n", $source);	// Windows to Unix line endings
		$source			= str_replace("\r", "\n", $source);		// Mac to Unix line endings
		$this->source	= $source;
		// Variables
		$buffer			= &$this->buffer;
		$exclusive		= &$this->stateExclusive;
		$state			= &$this->state;
		$c = $p 		= null;
		// --
		for ($i = 0, $l = strlen($source); $i < $l; $i++)
			{
			// Set the current Char
			$c = $source[$i]; // Is faster than: $c = substr($source, $i, 1);
			// Normalize and filter double whitespace characters
			if ($exclusive === false)
				{
				if ($c === "\n" || $c === "\t")
					{
					$c = " ";
					}
				if ($c === " " && $p === " ")
					{
					continue;
					}
				}
			$buffer .= $c;
			// Extended processing only if the current char is a global trigger char
			if (strpos($globalTriggerChars, $c) !== false)
				{
				// Exclusive state is set; process with the exclusive plugin
				if ($exclusive)
					{
					$tPluginIndex = $pluginIndex[$exclusive];
					if (strpos($pluginTriggerChars[$tPluginIndex], $c) !== false && ($pluginTriggerStates[$tPluginIndex] === false || strpos($pluginTriggerStates[$tPluginIndex], $state) !== false))
						{
						$r = $plugins[$tPluginIndex]->parse($i, $c, $p, $state);
						// Return value is TRUE => continue with next char
						if ($r === true)
							{
							continue;
							}
						// Return value is numeric => set new index and continue with next char
						elseif ($r !== false && $r != $i)
							{
							$i = $r;
							continue;
							}
						}
					}
				// Else iterate through the plugins
				else
					{
					$triggerState = "|" . $state . "|";
					for ($ii = 0, $ll = $pluginCount; $ii < $ll; $ii++)
						{
						// Only process if the current char is one of the plugin trigger chars
						if (strpos($pluginTriggerChars[$ii], $c) !== false && ($pluginTriggerStates[$ii] === false || strpos($pluginTriggerStates[$ii], $triggerState) !== false))
							{
							// Process with the plugin
							$r = $plugins[$ii]->parse($i, $c, $p, $state);
							// Return value is TRUE => break the plugin loop and and continue with next char
							if ($r === true)
								{
								break;
								}
							// Return value is numeric => set new index, break the plugin loop and and continue with next char
							elseif ($r !== false && $r != $i)
								{
								$i = $r;
								break;
								}
							}
						}
					}
				}
			$p = $c; // Set the parent char
			}
		return $this->tokens;
		}
	/**
	 * Remove the last state of the state stack and return the removed stack value.
	 *
	 * @return integer Removed state value
	 */
	public function popState()
		{
		$r = array_pop($this->states);
		$this->state = $this->states[count($this->states) - 1];
		return $r;
		}
	/**
	 * Adds a new state onto the state stack.
	 *
	 * @param integer $state State to add onto the state stack.
	 * @return integer The index of the added state in the state stacks
	 */
	public function pushState($state)
		{
		$r = array_push($this->states, $state);
		$this->state = $this->states[count($this->states) - 1];
		return $r;
		}
	/**
	 * Sets/restores the buffer.
	 *
	 * @param string $buffer Buffer to set
	 * @return void
	 */
	public function setBuffer($buffer)
		{
		$this->buffer = $buffer;
		}
	/**
	 * Set the exclusive state.
	 *
	 * @param string $exclusive Exclusive state
	 * @return void
	 */
	public function setExclusive($exclusive)
		{
		$this->stateExclusive = $exclusive;
		}
	/**
	 * Set the media types state.
	 *
	 * @param array $mediaTypes Media types state
	 * @return void
	 */
	public function setMediaTypes(array $mediaTypes)
		{
		$this->stateMediaTypes = $mediaTypes;
		}
	/**
	 * Sets the current state in the state stack; equals to {@link CssParser::popState()} + {@link CssParser::pushState()}.
	 *
	 * @param integer $state State to set
	 * @return integer
	 */
	public function setState($state)
		{
		$r = array_pop($this->states);
		array_push($this->states, $state);
		$this->state = $this->states[count($this->states) - 1];
		return $r;
		}
	/**
	 * Removes the exclusive state.
	 *
	 * @return void
	 */
	public function unsetExclusive()
		{
		$this->stateExclusive = false;
		}
	/**
	 * Removes the media types state.
	 *
	 * @return void
	 */
	public function unsetMediaTypes()
		{
		$this->stateMediaTypes = false;
		}
	}

/**
 * {@link aCssFromatter Formatter} returning the CSS source in {@link http://goo.gl/j4XdU OTBS indent style} (The One True Brace Style).
 *
 * @package		CssMin/Formatter
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssOtbsFormatter extends aCssFormatter
	{
	/**
	 * Implements {@link aCssFormatter::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		$r				= array();
		$level			= 0;
		for ($i = 0, $l = count($this->tokens); $i < $l; $i++)
			{
			$token		= $this->tokens[$i];
			$class		= get_class($token);
			$indent 	= str_repeat($this->indent, $level);
			if ($class === "CssCommentToken")
				{
				$lines = array_map("trim", explode("\n", $token->Comment));
				for ($ii = 0, $ll = count($lines); $ii < $ll; $ii++)
					{
					$r[] = $indent . (substr($lines[$ii], 0, 1) == "*" ? " " : "") . $lines[$ii];
					}
				}
			elseif ($class === "CssAtCharsetToken")
				{
				$r[] = $indent . "@charset " . $token->Charset . ";";
				}
			elseif ($class === "CssAtFontFaceStartToken")
				{
				$r[] = $indent . "@font-face {";
				$level++;
				}
			elseif ($class === "CssAtImportToken")
				{
				$r[] = $indent . "@import " . $token->Import . " " . implode(", ", $token->MediaTypes) . ";";
				}
			elseif ($class === "CssAtKeyframesStartToken")
				{
				$r[] = $indent . "@keyframes \"" . $token->Name . "\" {";
				$level++;
				}
			elseif ($class === "CssAtMediaStartToken")
				{
				$r[] = $indent . "@media " . implode(", ", $token->MediaTypes) . " {";
				$level++;
				}
			elseif ($class === "CssAtPageStartToken")
				{
				$r[] = $indent . "@page {";
				$level++;
				}
			elseif ($class === "CssAtVariablesStartToken")
				{
				$r[] = $indent . "@variables " . implode(", ", $token->MediaTypes) . " {";
				$level++;
				}
			elseif ($class === "CssRulesetStartToken" || $class === "CssAtKeyframesRulesetStartToken")
				{
				$r[] = $indent . implode(", ", $token->Selectors) . " {";
				$level++;
				}
			elseif ($class == "CssAtFontFaceDeclarationToken"
				|| $class === "CssAtKeyframesRulesetDeclarationToken"
				|| $class === "CssAtPageDeclarationToken"
				|| $class == "CssAtVariablesDeclarationToken"
				|| $class === "CssRulesetDeclarationToken"
				)
				{
				$declaration = $indent . $token->Property . ": ";
				if ($this->padding)
					{
					$declaration = str_pad($declaration, $this->padding, " ", STR_PAD_RIGHT);
					}
				$r[] = $declaration . $token->Value . ($token->IsImportant ? " !important" : "") . ";";
				}
			elseif ($class === "CssAtFontFaceEndToken"
				|| $class === "CssAtMediaEndToken"
				|| $class === "CssAtKeyframesEndToken"
				|| $class === "CssAtKeyframesRulesetEndToken"
				|| $class === "CssAtPageEndToken"
				|| $class === "CssAtVariablesEndToken"
				|| $class === "CssRulesetEndToken"
				)
				{
				$level--;
				$r[] = str_repeat($indent, $level) . "}";
				}
			}
		return implode("\n", $r);
		}
	}

/**
 * This {@link aCssToken CSS token} is a utility token that extends {@link aNullToken} and returns only a empty string.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssNullToken extends aCssToken
	{
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "";
		}
	}

/**
 * CSS Minifier.
 *
 * @package		CssMin/Minifier
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssMinifier
	{
	/**
	 * {@link aCssMinifierFilter Filters}.
	 *
	 * @var array
	 */
	private $filters = array();
	/**
	 * {@link aCssMinifierPlugin Plugins}.
	 *
	 * @var array
	 */
	private $plugins = array();
	/**
	 * Minified source.
	 *
	 * @var string
	 */
	private $minified = "";
	/**
	 * Constructer.
	 *
	 * Creates instances of {@link aCssMinifierFilter filters} and {@link aCssMinifierPlugin plugins}.
	 *
	 * @param string $source CSS source [optional]
	 * @param array $filters Filter configuration [optional]
	 * @param array $plugins Plugin configuration [optional]
	 * @return void
	 */
	public function __construct($source = null, array $filters = null, array $plugins = null)
		{
		$filters = array_merge(array
			(
			"ImportImports"					=> false,
			"RemoveComments"				=> true,
			"RemoveEmptyRulesets"			=> true,
			"RemoveEmptyAtBlocks"			=> true,
			"ConvertLevel3Properties"		=> false,
			"ConvertLevel3AtKeyframes"		=> false,
			"Variables"						=> true,
			"RemoveLastDelarationSemiColon"	=> true
			), is_array($filters) ? $filters : array());
		$plugins = array_merge(array
			(
			"Variables"						=> true,
			"ConvertFontWeight"				=> false,
			"ConvertHslColors"				=> false,
			"ConvertRgbColors"				=> false,
			"ConvertNamedColors"			=> false,
			"CompressColorValues"			=> false,
			"CompressUnitValues"			=> false,
			"CompressExpressionValues"		=> false
			), is_array($plugins) ? $plugins : array());
		// Filters
		foreach ($filters as $name => $config)
			{
			if ($config !== false)
				{
				$class	= "Css" . $name . "MinifierFilter";
				$config = is_array($config) ? $config : array();
				if (class_exists($class))
					{
					$this->filters[] = new $class($this, $config);
					}
				else
					{
					CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": The filter <code>" . $name . "</code> with the class name <code>" . $class . "</code> was not found"));
					}
				}
			}
		// Plugins
		foreach ($plugins as $name => $config)
			{
			if ($config !== false)
				{
				$class	= "Css" . $name . "MinifierPlugin";
				$config = is_array($config) ? $config : array();
				if (class_exists($class))
					{
					$this->plugins[] = new $class($this, $config);
					}
				else
					{
					CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": The plugin <code>" . $name . "</code> with the class name <code>" . $class . "</code> was not found"));
					}
				}
			}
		// --
		if (!is_null($source))
			{
			$this->minify($source);
			}
		}
	/**
	 * Returns the minified Source.
	 *
	 * @return string
	 */
	public function getMinified()
		{
		return $this->minified;
		}
	/**
	 * Returns a plugin by class name.
	 *
	 * @param string $name Class name of the plugin
	 * @return aCssMinifierPlugin
	 */
	public function getPlugin($class)
		{
		static $index = null;
		if (is_null($index))
			{
			$index = array();
			for ($i = 0, $l = count($this->plugins); $i < $l; $i++)
				{
				$index[get_class($this->plugins[$i])] = $i;
				}
			}
		return isset($index[$class]) ? $this->plugins[$index[$class]] : false;
		}
	/**
	 * Minifies the CSS source.
	 *
	 * @param string $source CSS source
	 * @return string
	 */
	public function minify($source)
		{
		// Variables
		$r						= "";
		$parser					= new CssParser($source);
		$tokens					= $parser->getTokens();
		$filters 				= $this->filters;
		$filterCount			= count($this->filters);
		$plugins				= $this->plugins;
		$pluginCount			= count($plugins);
		$pluginIndex			= array();
		$pluginTriggerTokens	= array();
		$globalTriggerTokens	= array();
		for ($i = 0, $l = count($plugins); $i < $l; $i++)
			{
			$tPluginClassName				= get_class($plugins[$i]);
			$pluginTriggerTokens[$i]		= $plugins[$i]->getTriggerTokens();
			foreach ($pluginTriggerTokens[$i] as $v)
				{
				if (!in_array($v, $globalTriggerTokens))
					{
					$globalTriggerTokens[] = $v;
					}
				}
			$pluginTriggerTokens[$i] = "|" . implode("|", $pluginTriggerTokens[$i]) . "|";
			$pluginIndex[$tPluginClassName]	= $i;
			}
		$globalTriggerTokens = "|" . implode("|", $globalTriggerTokens) . "|";
		/*
		 * Apply filters
		 */
		for($i = 0; $i < $filterCount; $i++)
			{
			// Apply the filter; if the return value is larger than 0...
			if ($filters[$i]->apply($tokens) > 0)
				{
				// ...then filter null values and rebuild the token array
				$tokens = array_values(array_filter($tokens));
				}
			}
		$tokenCount = count($tokens);
		/*
		 * Apply plugins
		 */
		for($i = 0; $i < $tokenCount; $i++)
			{
			$triggerToken = "|" . get_class($tokens[$i]) . "|";
			if (strpos($globalTriggerTokens, $triggerToken) !== false)
				{
				for($ii = 0; $ii < $pluginCount; $ii++)
					{
					if (strpos($pluginTriggerTokens[$ii], $triggerToken) !== false || $pluginTriggerTokens[$ii] === false)
						{
						// Apply the plugin; if the return value is TRUE continue to the next token
						if ($plugins[$ii]->apply($tokens[$i]) === true)
							{
							continue 2;
							}
						}
					}
				}
			}
		// Stringify the tokens
		for($i = 0; $i < $tokenCount; $i++)
			{
			$r .= (string) $tokens[$i];
			}
		$this->minified = $r;
		return $r;
		}
	}

/**
 * CssMin - A (simple) css minifier with benefits
 *
 * --
 * Copyright (c) 2011 Joe Scylla <joe.scylla@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * --
 *
 * @package		CssMin
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssMin
	{
	/**
	 * Index of classes
	 *
	 * @var array
	 */
	private static $classIndex = array();
	/**
	 * Parse/minify errors
	 *
	 * @var array
	 */
	private static $errors = array();
	/**
	 * Verbose output.
	 *
	 * @var boolean
	 */
	private static $isVerbose = false;
	/**
	 * {@link http://goo.gl/JrW54 Autoload} function of CssMin.
	 *
	 * @param string $class Name of the class
	 * @return void
	 */
	public static function autoload($class)
		{
		if (isset(self::$classIndex[$class]))
			{
			require(self::$classIndex[$class]);
			}
		}
	/**
	 * Return errors
	 *
	 * @return array of {CssError}.
	 */
	public static function getErrors()
		{
		return self::$errors;
		}
	/**
	 * Returns if there were errors.
	 *
	 * @return boolean
	 */
	public static function hasErrors()
		{
		return count(self::$errors) > 0;
		}
	/**
	 * Initialises CssMin.
	 *
	 * @return void
	 */
	public static function initialise()
		{
		// Create the class index for autoloading or including
		$paths = array(dirname(__FILE__));
		while (list($i, $path) = each($paths))
			{
			$subDirectorys = glob($path . "*", GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
			if (is_array($subDirectorys))
				{
				foreach ($subDirectorys as $subDirectory)
					{
					$paths[] = $subDirectory;
					}
				}
			$files = glob($path . "*.php", 0);
			if (is_array($files))
				{
				foreach ($files as $file)
					{
					$class = substr(basename($file), 0, -4);
					self::$classIndex[$class] = $file;
					}
				}
			}
		krsort(self::$classIndex);
		// Only use autoloading if spl_autoload_register() is available and no __autoload() is defined (because
		// __autoload() breaks if spl_autoload_register() is used.
		if (function_exists("spl_autoload_register") && !is_callable("__autoload"))
			{
			spl_autoload_register(array(__CLASS__, "autoload"));
			}
		// Otherwise include all class files
		else
			{
			foreach (self::$classIndex as $class => $file)
				{
				if (!class_exists($class))
					{
					require_once($file);
					}
				}
			}
		}
	/**
	 * Minifies CSS source.
	 *
	 * @param string $source CSS source
	 * @param array $filters Filter configuration [optional]
	 * @param array $plugins Plugin configuration [optional]
	 * @return string Minified CSS
	 */
	public static function minify($source, array $filters = null, array $plugins = null)
		{
		self::$errors = array();
		$minifier = new CssMinifier($source, $filters, $plugins);
		return $minifier->getMinified();
		}
	/**
	 * Parse the CSS source.
	 *
	 * @param string $source CSS source
	 * @param array $plugins Plugin configuration [optional]
	 * @return array Array of aCssToken
	 */
	public static function parse($source, array $plugins = null)
		{
		self::$errors = array();
		$parser = new CssParser($source, $plugins);
		return $parser->getTokens();
		}
	/**
	 * --
	 *
	 * @param boolean $to
	 * @return boolean
	 */
	public static function setVerbose($to)
		{
		self::$isVerbose = (boolean) $to;
		return self::$isVerbose;
		}
	/**
	 * --
	 *
	 * @param CssError $error
	 * @return void
	 */
	public static function triggerError(CssError $error)
		{
		self::$errors[] = $error;
		if (self::$isVerbose)
			{
			trigger_error((string) $error, E_USER_WARNING);
			}
		}
	}
// Initialises CssMin
CssMin::initialise();

/**
 * This {@link aCssMinifierFilter minifier filter} import external css files defined with the @import at-rule into the
 * current stylesheet.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssImportImportsMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Array with already imported external stylesheets.
	 *
	 * @var array
	 */
	private $imported = array();
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		if (!isset($this->configuration["BasePath"]) || !is_dir($this->configuration["BasePath"]))
			{
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Base path <code>" . ($this->configuration["BasePath"] ? $this->configuration["BasePath"] : "null"). "</code> is not a directory"));
			return 0;
			}
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if (get_class($tokens[$i]) === "CssAtImportToken")
				{
				$import = $this->configuration["BasePath"] . "/" . $tokens[$i]->Import;
				// Import file was not found/is not a file
				if (!is_file($import))
					{
					CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Import file <code>" . $import. "</code> was not found.", (string) $tokens[$i]));
					}
				// Import file already imported; remove this @import at-rule to prevent recursions
				elseif (in_array($import, $this->imported))
					{
					CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Import file <code>" . $import. "</code> was already imported.", (string) $tokens[$i]));
					$tokens[$i] = null;
					}
				else
					{
					$this->imported[] = $import;
					$parser = new CssParser(file_get_contents($import));
					$import = $parser->getTokens();
					// The @import at-rule has media types defined requiring special handling
					if (count($tokens[$i]->MediaTypes) > 0 && !(count($tokens[$i]->MediaTypes) == 1 && $tokens[$i]->MediaTypes[0] == "all"))
						{
						$blocks = array();
						/*
						 * Filter or set media types of @import at-rule or remove the @import at-rule if no media type is matching the parent @import at-rule
						 */
						for($ii = 0, $ll = count($import); $ii < $ll; $ii++)
							{
							if (get_class($import[$ii]) === "CssAtImportToken")
								{
								// @import at-rule defines no media type or only the "all" media type; set the media types to the one defined in the parent @import at-rule
								if (count($import[$ii]->MediaTypes) == 0 || (count($import[$ii]->MediaTypes) == 1 && $import[$ii]->MediaTypes[0] == "all"))
									{
									$import[$ii]->MediaTypes = $tokens[$i]->MediaTypes;
									}
								// @import at-rule defineds one or more media types; filter out media types not matching with the  parent @import at-rule
								elseif (count($import[$ii]->MediaTypes) > 0)
									{
									foreach ($import[$ii]->MediaTypes as $index => $mediaType)
										{
										if (!in_array($mediaType, $tokens[$i]->MediaTypes))
											{
											unset($import[$ii]->MediaTypes[$index]);
											}
										}
									$import[$ii]->MediaTypes = array_values($import[$ii]->MediaTypes);
									// If there are no media types left in the @import at-rule remove the @import at-rule
									if (count($import[$ii]->MediaTypes) == 0)
										{
										$import[$ii] = null;
										}
									}
								}
							}
						/*
						 * Remove media types of @media at-rule block not defined in the @import at-rule
						 */
						for($ii = 0, $ll = count($import); $ii < $ll; $ii++)
							{
							if (get_class($import[$ii]) === "CssAtMediaStartToken")
								{
								foreach ($import[$ii]->MediaTypes as $index => $mediaType)
									{
									if (!in_array($mediaType, $tokens[$i]->MediaTypes))
										{
										unset($import[$ii]->MediaTypes[$index]);
										}
									$import[$ii]->MediaTypes = array_values($import[$ii]->MediaTypes);
									}
								}
							}
						/*
						 * If no media types left of the @media at-rule block remove the complete block
						 */
						for($ii = 0, $ll = count($import); $ii < $ll; $ii++)
							{
							if (get_class($import[$ii]) === "CssAtMediaStartToken")
								{
								if (count($import[$ii]->MediaTypes) === 0)
									{
									for ($iii = $ii; $iii < $ll; $iii++)
										{
										if (get_class($import[$iii]) === "CssAtMediaEndToken")
											{
											break;
											}
										}
									if (get_class($import[$iii]) === "CssAtMediaEndToken")
										{
										array_splice($import, $ii, $iii - $ii + 1, array());
										$ll = count($import);
										}
									}
								}
							}
						/*
						 * If the media types of the @media at-rule equals the media types defined in the @import
						 * at-rule remove the CssAtMediaStartToken and CssAtMediaEndToken token
						 */
						for($ii = 0, $ll = count($import); $ii < $ll; $ii++)
							{
							if (get_class($import[$ii]) === "CssAtMediaStartToken" && count(array_diff($tokens[$i]->MediaTypes, $import[$ii]->MediaTypes)) === 0)
								{
								for ($iii = $ii; $iii < $ll; $iii++)
									{
									if (get_class($import[$iii]) == "CssAtMediaEndToken")
										{
										break;
										}
									}
								if (get_class($import[$iii]) == "CssAtMediaEndToken")
									{
									unset($import[$ii]);
									unset($import[$iii]);
									$import = array_values($import);
									$ll = count($import);
									}
								}
							}
						/**
						 * Extract CssAtImportToken and CssAtCharsetToken tokens
						 */
						for($ii = 0, $ll = count($import); $ii < $ll; $ii++)
							{
							$class = get_class($import[$ii]);
							if ($class === "CssAtImportToken" || $class === "CssAtCharsetToken")
								{
								$blocks = array_merge($blocks, array_splice($import, $ii, 1, array()));
								$ll = count($import);
								}
							}
						/*
						 * Extract the @font-face, @media and @page at-rule block
						 */
						for($ii = 0, $ll = count($import); $ii < $ll; $ii++)
							{
							$class = get_class($import[$ii]);
							if ($class === "CssAtFontFaceStartToken" || $class === "CssAtMediaStartToken" || $class === "CssAtPageStartToken" || $class === "CssAtVariablesStartToken")
								{
								for ($iii = $ii; $iii < $ll; $iii++)
									{
									$class = get_class($import[$iii]);
									if ($class === "CssAtFontFaceEndToken" || $class === "CssAtMediaEndToken" || $class === "CssAtPageEndToken" || $class === "CssAtVariablesEndToken")
										{
										break;
										}
									}
								$class = get_class($import[$iii]);
								if (isset($import[$iii]) && ($class === "CssAtFontFaceEndToken" || $class === "CssAtMediaEndToken" || $class === "CssAtPageEndToken" || $class === "CssAtVariablesEndToken"))
									{
									$blocks = array_merge($blocks, array_splice($import, $ii, $iii - $ii + 1, array()));
									$ll = count($import);
									}
								}
							}
						// Create the import array with extracted tokens and the rulesets wrapped into a @media at-rule block
						$import = array_merge($blocks, array(new CssAtMediaStartToken($tokens[$i]->MediaTypes)), $import, array(new CssAtMediaEndToken()));
						}
					// Insert the imported tokens
					array_splice($tokens, $i, 1, $import);
					// Modify parameters of the for-loop
					$i--;
					$l = count($tokens);
					}
				}
			}
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for preserve parsing expression() declaration values.
 *
 * This plugin return no {@link aCssToken CssToken} but ensures that expression() declaration values will get parsed
 * properly.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssExpressionParserPlugin extends aCssParserPlugin
	{
	/**
	 * Count of left braces.
	 *
	 * @var integer
	 */
	private $leftBraces = 0;
	/**
	 * Count of right braces.
	 *
	 * @var integer
	 */
	private $rightBraces = 0;
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("(", ")", ";", "}");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return false;
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of expression
		if ($char === "(" && strtolower(substr($this->parser->getSource(), $index - 10, 11)) === "expression(" && $state !== "T_EXPRESSION")
			{
			$this->parser->pushState("T_EXPRESSION");
			$this->leftBraces++;
			}
		// Count left braces
		elseif ($char === "(" && $state === "T_EXPRESSION")
			{
			$this->leftBraces++;
			}
		// Count right braces
		elseif ($char === ")" && $state === "T_EXPRESSION")
			{
			$this->rightBraces++;
			}
		// Possible end of expression; if left and right braces are equal the expressen ends
		elseif (($char === ";" || $char === "}") && $state === "T_EXPRESSION" && $this->leftBraces === $this->rightBraces)
			{
			$this->leftBraces = $this->rightBraces = 0;
			$this->parser->popState();
			return $index - 1;
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * CSS Error.
 *
 * @package		CssMin
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssError
	{
	/**
	 * File.
	 *
	 * @var string
	 */
	public $File = "";
	/**
	 * Line.
	 *
	 * @var integer
	 */
	public $Line = 0;
	/**
	 * Error message.
	 *
	 * @var string
	 */
	public $Message = "";
	/**
	 * Source.
	 *
	 * @var string
	 */
	public $Source = "";
	/**
	 * Constructor triggering the error.
	 *
	 * @param string $message Error message
	 * @param string $source Corresponding line [optional]
	 * @return void
	 */
	public function __construct($file, $line, $message, $source = "")
		{
		$this->File		= $file;
		$this->Line		= $line;
		$this->Message	= $message;
		$this->Source	= $source;
		}
	/**
	 * Returns the error as formatted string.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return $this->Message . ($this->Source ? ": <br /><code>" . $this->Source . "</code>": "") . "<br />in file " . $this->File . " at line " . $this->Line;
		}
	}

/**
 * This {@link aCssMinifierPlugin} will convert a color value in rgb notation to hexadecimal notation.
 *
 * Example:
 * <code>
 * color: rgb(200,60%,5);
 * </code>
 *
 * Will get converted to:
 * <code>
 * color:#c89905;
 * </code>
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssConvertRgbColorsMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Regular expression matching the value.
	 *
	 * @var string
	 */
	private $reMatch = "/rgb\s*\(\s*([0-9%]+)\s*,\s*([0-9%]+)\s*,\s*([0-9%]+)\s*\)/iS";
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (stripos($token->Value, "rgb") !== false && preg_match($this->reMatch, $token->Value, $m))
			{
			for ($i = 1, $l = count($m); $i < $l; $i++)
				{
				if (strpos("%", $m[$i]) !== false)
					{
					$m[$i] = substr($m[$i], 0, -1);
					$m[$i] = (int) (256 * ($m[$i] / 100));
					}
				$m[$i] = str_pad(dechex($m[$i]),  2, "0", STR_PAD_LEFT);
				}
			$token->Value = str_replace($m[0], "#" . $m[1] . $m[2] . $m[3], $token->Value);
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	}

/**
 * This {@link aCssMinifierPlugin} will convert named color values to hexadecimal notation.
 *
 * Example:
 * <code>
 * color: black;
 * border: 1px solid indigo;
 * </code>
 *
 * Will get converted to:
 * <code>
 * color:#000;
 * border:1px solid #4b0082;
 * </code>
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssConvertNamedColorsMinifierPlugin extends aCssMinifierPlugin
	{

	/**
	 * Regular expression matching the value.
	 *
	 * @var string
	 */
	private $reMatch = null;
	/**
	 * Regular expression replacing the value.
	 *
	 * @var string
	 */
	private $reReplace = "\"\${1}\" . \$this->transformation[strtolower(\"\${2}\")] . \"\${3}\"";
	/**
	 * Transformation table used by the {@link CssConvertNamedColorsMinifierPlugin::$reReplace replace regular expression}.
	 *
	 * @var array
	 */
	private $transformation = array
		(
		"aliceblue"						=> "#f0f8ff",
		"antiquewhite"					=> "#faebd7",
		"aqua"							=> "#0ff",
		"aquamarine"					=> "#7fffd4",
		"azure"							=> "#f0ffff",
		"beige"							=> "#f5f5dc",
		"black"							=> "#000",
		"blue"							=> "#00f",
		"blueviolet"					=> "#8a2be2",
		"brown"							=> "#a52a2a",
		"burlywood"						=> "#deb887",
		"cadetblue"						=> "#5f9ea0",
		"chartreuse"					=> "#7fff00",
		"chocolate"						=> "#d2691e",
		"coral"							=> "#ff7f50",
		"cornflowerblue"				=> "#6495ed",
		"cornsilk"						=> "#fff8dc",
		"crimson"						=> "#dc143c",
		"darkblue"						=> "#00008b",
		"darkcyan"						=> "#008b8b",
		"darkgoldenrod"					=> "#b8860b",
		"darkgray"						=> "#a9a9a9",
		"darkgreen"						=> "#006400",
		"darkkhaki"						=> "#bdb76b",
		"darkmagenta"					=> "#8b008b",
		"darkolivegreen"				=> "#556b2f",
		"darkorange"					=> "#ff8c00",
		"darkorchid"					=> "#9932cc",
		"darkred"						=> "#8b0000",
		"darksalmon"					=> "#e9967a",
		"darkseagreen"					=> "#8fbc8f",
		"darkslateblue"					=> "#483d8b",
		"darkslategray"					=> "#2f4f4f",
		"darkturquoise"					=> "#00ced1",
		"darkviolet"					=> "#9400d3",
		"deeppink"						=> "#ff1493",
		"deepskyblue"					=> "#00bfff",
		"dimgray"						=> "#696969",
		"dodgerblue"					=> "#1e90ff",
		"firebrick"						=> "#b22222",
		"floralwhite"					=> "#fffaf0",
		"forestgreen"					=> "#228b22",
		"fuchsia"						=> "#f0f",
		"gainsboro"						=> "#dcdcdc",
		"ghostwhite"					=> "#f8f8ff",
		"gold"							=> "#ffd700",
		"goldenrod"						=> "#daa520",
		"gray"							=> "#808080",
		"green"							=> "#008000",
		"greenyellow"					=> "#adff2f",
		"honeydew"						=> "#f0fff0",
		"hotpink"						=> "#ff69b4",
		"indianred"						=> "#cd5c5c",
		"indigo"						=> "#4b0082",
		"ivory"							=> "#fffff0",
		"khaki"							=> "#f0e68c",
		"lavender"						=> "#e6e6fa",
		"lavenderblush"					=> "#fff0f5",
		"lawngreen"						=> "#7cfc00",
		"lemonchiffon"					=> "#fffacd",
		"lightblue"						=> "#add8e6",
		"lightcoral"					=> "#f08080",
		"lightcyan"						=> "#e0ffff",
		"lightgoldenrodyellow"			=> "#fafad2",
		"lightgreen"					=> "#90ee90",
		"lightgrey"						=> "#d3d3d3",
		"lightpink"						=> "#ffb6c1",
		"lightsalmon"					=> "#ffa07a",
		"lightseagreen"					=> "#20b2aa",
		"lightskyblue"					=> "#87cefa",
		"lightslategray"				=> "#789",
		"lightsteelblue"				=> "#b0c4de",
		"lightyellow"					=> "#ffffe0",
		"lime"							=> "#0f0",
		"limegreen"						=> "#32cd32",
		"linen"							=> "#faf0e6",
		"maroon"						=> "#800000",
		"mediumaquamarine"				=> "#66cdaa",
		"mediumblue"					=> "#0000cd",
		"mediumorchid"					=> "#ba55d3",
		"mediumpurple"					=> "#9370db",
		"mediumseagreen"				=> "#3cb371",
		"mediumslateblue"				=> "#7b68ee",
		"mediumspringgreen"				=> "#00fa9a",
		"mediumturquoise"				=> "#48d1cc",
		"mediumvioletred"				=> "#c71585",
		"midnightblue"					=> "#191970",
		"mintcream"						=> "#f5fffa",
		"mistyrose"						=> "#ffe4e1",
		"moccasin"						=> "#ffe4b5",
		"navajowhite"					=> "#ffdead",
		"navy"							=> "#000080",
		"oldlace"						=> "#fdf5e6",
		"olive"							=> "#808000",
		"olivedrab"						=> "#6b8e23",
		"orange"						=> "#ffa500",
		"orangered"						=> "#ff4500",
		"orchid"						=> "#da70d6",
		"palegoldenrod"					=> "#eee8aa",
		"palegreen"						=> "#98fb98",
		"paleturquoise"					=> "#afeeee",
		"palevioletred"					=> "#db7093",
		"papayawhip"					=> "#ffefd5",
		"peachpuff"						=> "#ffdab9",
		"peru"							=> "#cd853f",
		"pink"							=> "#ffc0cb",
		"plum"							=> "#dda0dd",
		"powderblue"					=> "#b0e0e6",
		"purple"						=> "#800080",
		"red"							=> "#f00",
		"rosybrown"						=> "#bc8f8f",
		"royalblue"						=> "#4169e1",
		"saddlebrown"					=> "#8b4513",
		"salmon"						=> "#fa8072",
		"sandybrown"					=> "#f4a460",
		"seagreen"						=> "#2e8b57",
		"seashell"						=> "#fff5ee",
		"sienna"						=> "#a0522d",
		"silver"						=> "#c0c0c0",
		"skyblue"						=> "#87ceeb",
		"slateblue"						=> "#6a5acd",
		"slategray"						=> "#708090",
		"snow"							=> "#fffafa",
		"springgreen"					=> "#00ff7f",
		"steelblue"						=> "#4682b4",
		"tan"							=> "#d2b48c",
		"teal"							=> "#008080",
		"thistle"						=> "#d8bfd8",
		"tomato"						=> "#ff6347",
		"turquoise"						=> "#40e0d0",
		"violet"						=> "#ee82ee",
		"wheat"							=> "#f5deb3",
		"white"							=> "#fff",
		"whitesmoke"					=> "#f5f5f5",
		"yellow"						=> "#ff0",
		"yellowgreen"					=> "#9acd32"
		);
	/**
	 * Overwrites {@link aCssMinifierPlugin::__construct()}.
	 *
	 * The constructor will create the {@link CssConvertNamedColorsMinifierPlugin::$reReplace replace regular expression}
	 * based on the {@link CssConvertNamedColorsMinifierPlugin::$transformation transformation table}.
	 *
	 * @param CssMinifier $minifier The CssMinifier object of this plugin.
	 * @param array $configuration Plugin configuration [optional]
	 * @return void
	 */
	public function __construct(CssMinifier $minifier, array $configuration = array())
		{
		$this->reMatch = "/(^|\s)+(" . implode("|", array_keys($this->transformation)) . ")(\s|$)+/eiS";
		parent::__construct($minifier, $configuration);
		}
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		$lcValue = strtolower($token->Value);
		// Declaration value equals a value in the transformation table => simple replace
		if (isset($this->transformation[$lcValue]))
			{
			$token->Value = $this->transformation[$lcValue];
			}
		// Declaration value contains a value in the transformation table => regular expression replace
		elseif (preg_match($this->reMatch, $token->Value))
			{
			$token->Value = preg_replace($this->reMatch, $this->reReplace, $token->Value);
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} triggers on CSS Level 3 properties and will add declaration tokens
 * with browser-specific properties.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssConvertLevel3PropertiesMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Css property transformations table. Used to convert CSS3 and proprietary properties to the browser-specific
	 * counterparts.
	 *
	 * @var array
	 */
	private $transformations = array
		(
		// Property						Array(Mozilla, Webkit, Opera, Internet Explorer); NULL values are placeholders and will get ignored
		"animation"						=> array(null, "-webkit-animation", null, null),
		"animation-delay"				=> array(null, "-webkit-animation-delay", null, null),
		"animation-direction" 			=> array(null, "-webkit-animation-direction", null, null),
		"animation-duration"			=> array(null, "-webkit-animation-duration", null, null),
		"animation-fill-mode"			=> array(null, "-webkit-animation-fill-mode", null, null),
		"animation-iteration-count"		=> array(null, "-webkit-animation-iteration-count", null, null),
		"animation-name"				=> array(null, "-webkit-animation-name", null, null),
		"animation-play-state"			=> array(null, "-webkit-animation-play-state", null, null),
		"animation-timing-function"		=> array(null, "-webkit-animation-timing-function", null, null),
		"appearance"					=> array("-moz-appearance", "-webkit-appearance", null, null),
		"backface-visibility"			=> array(null, "-webkit-backface-visibility", null, null),
		"background-clip"				=> array(null, "-webkit-background-clip", null, null),
		"background-composite"			=> array(null, "-webkit-background-composite", null, null),
		"background-inline-policy"		=> array("-moz-background-inline-policy", null, null, null),
		"background-origin"				=> array(null, "-webkit-background-origin", null, null),
		"background-position-x"			=> array(null, null, null, "-ms-background-position-x"),
		"background-position-y"			=> array(null, null, null, "-ms-background-position-y"),
		"background-size"				=> array(null, "-webkit-background-size", null, null),
		"behavior"						=> array(null, null, null, "-ms-behavior"),
		"binding"						=> array("-moz-binding", null, null, null),
		"border-after"					=> array(null, "-webkit-border-after", null, null),
		"border-after-color"			=> array(null, "-webkit-border-after-color", null, null),
		"border-after-style"			=> array(null, "-webkit-border-after-style", null, null),
		"border-after-width"			=> array(null, "-webkit-border-after-width", null, null),
		"border-before"					=> array(null, "-webkit-border-before", null, null),
		"border-before-color"			=> array(null, "-webkit-border-before-color", null, null),
		"border-before-style"			=> array(null, "-webkit-border-before-style", null, null),
		"border-before-width"			=> array(null, "-webkit-border-before-width", null, null),
		"border-border-bottom-colors"	=> array("-moz-border-bottom-colors", null, null, null),
		"border-bottom-left-radius"		=> array("-moz-border-radius-bottomleft", "-webkit-border-bottom-left-radius", null, null),
		"border-bottom-right-radius"	=> array("-moz-border-radius-bottomright", "-webkit-border-bottom-right-radius", null, null),
		"border-end"					=> array("-moz-border-end", "-webkit-border-end", null, null),
		"border-end-color"				=> array("-moz-border-end-color", "-webkit-border-end-color", null, null),
		"border-end-style"				=> array("-moz-border-end-style", "-webkit-border-end-style", null, null),
		"border-end-width"				=> array("-moz-border-end-width", "-webkit-border-end-width", null, null),
		"border-fit"					=> array(null, "-webkit-border-fit", null, null),
		"border-horizontal-spacing"		=> array(null, "-webkit-border-horizontal-spacing", null, null),
		"border-image"					=> array("-moz-border-image", "-webkit-border-image", null, null),
		"border-left-colors"			=> array("-moz-border-left-colors", null, null, null),
		"border-radius"					=> array("-moz-border-radius", "-webkit-border-radius", null, null),
		"border-border-right-colors"	=> array("-moz-border-right-colors", null, null, null),
		"border-start"					=> array("-moz-border-start", "-webkit-border-start", null, null),
		"border-start-color"			=> array("-moz-border-start-color", "-webkit-border-start-color", null, null),
		"border-start-style"			=> array("-moz-border-start-style", "-webkit-border-start-style", null, null),
		"border-start-width"			=> array("-moz-border-start-width", "-webkit-border-start-width", null, null),
		"border-top-colors"				=> array("-moz-border-top-colors", null, null, null),
		"border-top-left-radius"		=> array("-moz-border-radius-topleft", "-webkit-border-top-left-radius", null, null),
		"border-top-right-radius"		=> array("-moz-border-radius-topright", "-webkit-border-top-right-radius", null, null),
		"border-vertical-spacing"		=> array(null, "-webkit-border-vertical-spacing", null, null),
		"box-align"						=> array("-moz-box-align", "-webkit-box-align", null, null),
		"box-direction"					=> array("-moz-box-direction", "-webkit-box-direction", null, null),
		"box-flex"						=> array("-moz-box-flex", "-webkit-box-flex", null, null),
		"box-flex-group"				=> array(null, "-webkit-box-flex-group", null, null),
		"box-flex-lines"				=> array(null, "-webkit-box-flex-lines", null, null),
		"box-ordinal-group"				=> array("-moz-box-ordinal-group", "-webkit-box-ordinal-group", null, null),
		"box-orient"					=> array("-moz-box-orient", "-webkit-box-orient", null, null),
		"box-pack"						=> array("-moz-box-pack", "-webkit-box-pack", null, null),
		"box-reflect"					=> array(null, "-webkit-box-reflect", null, null),
		"box-shadow"					=> array("-moz-box-shadow", "-webkit-box-shadow", null, null),
		"box-sizing"					=> array("-moz-box-sizing", null, null, null),
		"color-correction"				=> array(null, "-webkit-color-correction", null, null),
		"column-break-after"			=> array(null, "-webkit-column-break-after", null, null),
		"column-break-before"			=> array(null, "-webkit-column-break-before", null, null),
		"column-break-inside"			=> array(null, "-webkit-column-break-inside", null, null),
		"column-count"					=> array("-moz-column-count", "-webkit-column-count", null, null),
		"column-gap"					=> array("-moz-column-gap", "-webkit-column-gap", null, null),
		"column-rule"					=> array("-moz-column-rule", "-webkit-column-rule", null, null),
		"column-rule-color"				=> array("-moz-column-rule-color", "-webkit-column-rule-color", null, null),
		"column-rule-style"				=> array("-moz-column-rule-style", "-webkit-column-rule-style", null, null),
		"column-rule-width"				=> array("-moz-column-rule-width", "-webkit-column-rule-width", null, null),
		"column-span"					=> array(null, "-webkit-column-span", null, null),
		"column-width"					=> array("-moz-column-width", "-webkit-column-width", null, null),
		"columns"						=> array(null, "-webkit-columns", null, null),
		"filter"						=> array(__CLASS__, "filter"),
		"float-edge"					=> array("-moz-float-edge", null, null, null),
		"font-feature-settings"			=> array("-moz-font-feature-settings", null, null, null),
		"font-language-override"		=> array("-moz-font-language-override", null, null, null),
		"font-size-delta"				=> array(null, "-webkit-font-size-delta", null, null),
		"font-smoothing"				=> array(null, "-webkit-font-smoothing", null, null),
		"force-broken-image-icon"		=> array("-moz-force-broken-image-icon", null, null, null),
		"highlight"						=> array(null, "-webkit-highlight", null, null),
		"hyphenate-character"			=> array(null, "-webkit-hyphenate-character", null, null),
		"hyphenate-locale"				=> array(null, "-webkit-hyphenate-locale", null, null),
		"hyphens"						=> array(null, "-webkit-hyphens", null, null),
		"force-broken-image-icon"		=> array("-moz-image-region", null, null, null),
		"ime-mode"						=> array(null, null, null, "-ms-ime-mode"),
		"interpolation-mode"			=> array(null, null, null, "-ms-interpolation-mode"),
		"layout-flow"					=> array(null, null, null, "-ms-layout-flow"),
		"layout-grid"					=> array(null, null, null, "-ms-layout-grid"),
		"layout-grid-char"				=> array(null, null, null, "-ms-layout-grid-char"),
		"layout-grid-line"				=> array(null, null, null, "-ms-layout-grid-line"),
		"layout-grid-mode"				=> array(null, null, null, "-ms-layout-grid-mode"),
		"layout-grid-type"				=> array(null, null, null, "-ms-layout-grid-type"),
		"line-break"					=> array(null, "-webkit-line-break", null, "-ms-line-break"),
		"line-clamp"					=> array(null, "-webkit-line-clamp", null, null),
		"line-grid-mode"				=> array(null, null, null, "-ms-line-grid-mode"),
		"logical-height"				=> array(null, "-webkit-logical-height", null, null),
		"logical-width"					=> array(null, "-webkit-logical-width", null, null),
		"margin-after"					=> array(null, "-webkit-margin-after", null, null),
		"margin-after-collapse"			=> array(null, "-webkit-margin-after-collapse", null, null),
		"margin-before"					=> array(null, "-webkit-margin-before", null, null),
		"margin-before-collapse"		=> array(null, "-webkit-margin-before-collapse", null, null),
		"margin-bottom-collapse"		=> array(null, "-webkit-margin-bottom-collapse", null, null),
		"margin-collapse"				=> array(null, "-webkit-margin-collapse", null, null),
		"margin-end"					=> array("-moz-margin-end", "-webkit-margin-end", null, null),
		"margin-start"					=> array("-moz-margin-start", "-webkit-margin-start", null, null),
		"margin-top-collapse"			=> array(null, "-webkit-margin-top-collapse", null, null),
		"marquee "						=> array(null, "-webkit-marquee", null, null),
		"marquee-direction"				=> array(null, "-webkit-marquee-direction", null, null),
		"marquee-increment"				=> array(null, "-webkit-marquee-increment", null, null),
		"marquee-repetition"			=> array(null, "-webkit-marquee-repetition", null, null),
		"marquee-speed"					=> array(null, "-webkit-marquee-speed", null, null),
		"marquee-style"					=> array(null, "-webkit-marquee-style", null, null),
		"mask"							=> array(null, "-webkit-mask", null, null),
		"mask-attachment"				=> array(null, "-webkit-mask-attachment", null, null),
		"mask-box-image"				=> array(null, "-webkit-mask-box-image", null, null),
		"mask-clip"						=> array(null, "-webkit-mask-clip", null, null),
		"mask-composite"				=> array(null, "-webkit-mask-composite", null, null),
		"mask-image"					=> array(null, "-webkit-mask-image", null, null),
		"mask-origin"					=> array(null, "-webkit-mask-origin", null, null),
		"mask-position"					=> array(null, "-webkit-mask-position", null, null),
		"mask-position-x"				=> array(null, "-webkit-mask-position-x", null, null),
		"mask-position-y"				=> array(null, "-webkit-mask-position-y", null, null),
		"mask-repeat"					=> array(null, "-webkit-mask-repeat", null, null),
		"mask-repeat-x"					=> array(null, "-webkit-mask-repeat-x", null, null),
		"mask-repeat-y"					=> array(null, "-webkit-mask-repeat-y", null, null),
		"mask-size"						=> array(null, "-webkit-mask-size", null, null),
		"match-nearest-mail-blockquote-color" => array(null, "-webkit-match-nearest-mail-blockquote-color", null, null),
		"max-logical-height"			=> array(null, "-webkit-max-logical-height", null, null),
		"max-logical-width"				=> array(null, "-webkit-max-logical-width", null, null),
		"min-logical-height"			=> array(null, "-webkit-min-logical-height", null, null),
		"min-logical-width"				=> array(null, "-webkit-min-logical-width", null, null),
		"object-fit"					=> array(null, null, "-o-object-fit", null),
		"object-position"				=> array(null, null, "-o-object-position", null),
		"opacity"						=> array(__CLASS__, "opacity"),
		"outline-radius"				=> array("-moz-outline-radius", null, null, null),
		"outline-bottom-left-radius"	=> array("-moz-outline-radius-bottomleft", null, null, null),
		"outline-bottom-right-radius"	=> array("-moz-outline-radius-bottomright", null, null, null),
		"outline-top-left-radius"		=> array("-moz-outline-radius-topleft", null, null, null),
		"outline-top-right-radius"		=> array("-moz-outline-radius-topright", null, null, null),
		"padding-after"					=> array(null, "-webkit-padding-after", null, null),
		"padding-before"				=> array(null, "-webkit-padding-before", null, null),
		"padding-end"					=> array("-moz-padding-end", "-webkit-padding-end", null, null),
		"padding-start"					=> array("-moz-padding-start", "-webkit-padding-start", null, null),
		"perspective"					=> array(null, "-webkit-perspective", null, null),
		"perspective-origin"			=> array(null, "-webkit-perspective-origin", null, null),
		"perspective-origin-x"			=> array(null, "-webkit-perspective-origin-x", null, null),
		"perspective-origin-y"			=> array(null, "-webkit-perspective-origin-y", null, null),
		"rtl-ordering"					=> array(null, "-webkit-rtl-ordering", null, null),
		"scrollbar-3dlight-color"		=> array(null, null, null, "-ms-scrollbar-3dlight-color"),
		"scrollbar-arrow-color"			=> array(null, null, null, "-ms-scrollbar-arrow-color"),
		"scrollbar-base-color"			=> array(null, null, null, "-ms-scrollbar-base-color"),
		"scrollbar-darkshadow-color"	=> array(null, null, null, "-ms-scrollbar-darkshadow-color"),
		"scrollbar-face-color"			=> array(null, null, null, "-ms-scrollbar-face-color"),
		"scrollbar-highlight-color"		=> array(null, null, null, "-ms-scrollbar-highlight-color"),
		"scrollbar-shadow-color"		=> array(null, null, null, "-ms-scrollbar-shadow-color"),
		"scrollbar-track-color"			=> array(null, null, null, "-ms-scrollbar-track-color"),
		"stack-sizing"					=> array("-moz-stack-sizing", null, null, null),
		"svg-shadow"					=> array(null, "-webkit-svg-shadow", null, null),
		"tab-size"						=> array("-moz-tab-size", null, "-o-tab-size", null),
		"table-baseline"				=> array(null, null, "-o-table-baseline", null),
		"text-align-last"				=> array(null, null, null, "-ms-text-align-last"),
		"text-autospace"				=> array(null, null, null, "-ms-text-autospace"),
		"text-combine"					=> array(null, "-webkit-text-combine", null, null),
		"text-decorations-in-effect"	=> array(null, "-webkit-text-decorations-in-effect", null, null),
		"text-emphasis"					=> array(null, "-webkit-text-emphasis", null, null),
		"text-emphasis-color"			=> array(null, "-webkit-text-emphasis-color", null, null),
		"text-emphasis-position"		=> array(null, "-webkit-text-emphasis-position", null, null),
		"text-emphasis-style"			=> array(null, "-webkit-text-emphasis-style", null, null),
		"text-fill-color"				=> array(null, "-webkit-text-fill-color", null, null),
		"text-justify"					=> array(null, null, null, "-ms-text-justify"),
		"text-kashida-space"			=> array(null, null, null, "-ms-text-kashida-space"),
		"text-overflow"					=> array(null, null, "-o-text-overflow", "-ms-text-overflow"),
		"text-security"					=> array(null, "-webkit-text-security", null, null),
		"text-size-adjust"				=> array(null, "-webkit-text-size-adjust", null, "-ms-text-size-adjust"),
		"text-stroke"					=> array(null, "-webkit-text-stroke", null, null),
		"text-stroke-color"				=> array(null, "-webkit-text-stroke-color", null, null),
		"text-stroke-width"				=> array(null, "-webkit-text-stroke-width", null, null),
		"text-underline-position"		=> array(null, null, null, "-ms-text-underline-position"),
		"transform"						=> array("-moz-transform", "-webkit-transform", "-o-transform", null),
		"transform-origin"				=> array("-moz-transform-origin", "-webkit-transform-origin", "-o-transform-origin", null),
		"transform-origin-x"			=> array(null, "-webkit-transform-origin-x", null, null),
		"transform-origin-y"			=> array(null, "-webkit-transform-origin-y", null, null),
		"transform-origin-z"			=> array(null, "-webkit-transform-origin-z", null, null),
		"transform-style"				=> array(null, "-webkit-transform-style", null, null),
		"transition"					=> array("-moz-transition", "-webkit-transition", "-o-transition", null),
		"transition-delay"				=> array("-moz-transition-delay", "-webkit-transition-delay", "-o-transition-delay", null),
		"transition-duration"			=> array("-moz-transition-duration", "-webkit-transition-duration", "-o-transition-duration", null),
		"transition-property"			=> array("-moz-transition-property", "-webkit-transition-property", "-o-transition-property", null),
		"transition-timing-function"	=> array("-moz-transition-timing-function", "-webkit-transition-timing-function", "-o-transition-timing-function", null),
		"user-drag"						=> array(null, "-webkit-user-drag", null, null),
		"user-focus"					=> array("-moz-user-focus", null, null, null),
		"user-input"					=> array("-moz-user-input", null, null, null),
		"user-modify"					=> array("-moz-user-modify", "-webkit-user-modify", null, null),
		"user-select"					=> array("-moz-user-select", "-webkit-user-select", null, null),
		"white-space"					=> array(__CLASS__, "whiteSpace"),
		"window-shadow"					=> array("-moz-window-shadow", null, null, null),
		"word-break"					=> array(null, null, null, "-ms-word-break"),
		"word-wrap"						=> array(null, null, null, "-ms-word-wrap"),
		"writing-mode"					=> array(null, "-webkit-writing-mode", null, "-ms-writing-mode"),
		"zoom"							=> array(null, null, null, "-ms-zoom")
		);
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value large than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$r = 0;
		$transformations = &$this->transformations;
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if (get_class($tokens[$i]) === "CssRulesetDeclarationToken")
				{
				$tProperty = $tokens[$i]->Property;
				if (isset($transformations[$tProperty]))
					{
					$result = array();
					if (is_callable($transformations[$tProperty]))
						{
						$result = call_user_func_array($transformations[$tProperty], array($tokens[$i]));
						if (!is_array($result) && is_object($result))
							{
							$result = array($result);
							}
						}
					else
						{
						$tValue			= $tokens[$i]->Value;
						$tMediaTypes	= $tokens[$i]->MediaTypes;
						foreach ($transformations[$tProperty] as $property)
							{
							if ($property !== null)
								{
								$result[] = new CssRulesetDeclarationToken($property, $tValue, $tMediaTypes);
								}
							}
						}
					if (count($result) > 0)
						{
						array_splice($tokens, $i + 1, 0, $result);
						$i += count($result);
						$l += count($result);
						}
					}
				}
			}
		return $r;
		}
	/**
	 * Transforms the Internet Explorer specific declaration property "filter" to Internet Explorer 8+ compatible
	 * declaratiopn property "-ms-filter".
	 *
	 * @param aCssToken $token
	 * @return array
	 */
	private static function filter($token)
		{
		$r = array
			(
			new CssRulesetDeclarationToken("-ms-filter", "\"" . $token->Value . "\"", $token->MediaTypes),
			);
		return $r;
		}
	/**
	 * Transforms "opacity: {value}" into browser specific counterparts.
	 *
	 * @param aCssToken $token
	 * @return array
	 */
	private static function opacity($token)
		{
		// Calculate the value for Internet Explorer filter statement
		$ieValue = (int) ((float) $token->Value * 100);
		$r = array
			(
			// Internet Explorer >= 8
			new CssRulesetDeclarationToken("-ms-filter", "\"alpha(opacity=" . $ieValue . ")\"", $token->MediaTypes),
			// Internet Explorer >= 4 <= 7
			new CssRulesetDeclarationToken("filter", "alpha(opacity=" . $ieValue . ")", $token->MediaTypes),
			new CssRulesetDeclarationToken("zoom", "1", $token->MediaTypes)
			);
		return $r;
		}
	/**
	 * Transforms "white-space: pre-wrap" into browser specific counterparts.
	 *
	 * @param aCssToken $token
	 * @return array
	 */
	private static function whiteSpace($token)
		{
		if (strtolower($token->Value) === "pre-wrap")
			{
			$r = array
				(
				// Firefox < 3
				new CssRulesetDeclarationToken("white-space", "-moz-pre-wrap", $token->MediaTypes),
				// Webkit
				new CssRulesetDeclarationToken("white-space", "-webkit-pre-wrap", $token->MediaTypes),
				// Opera >= 4 <= 6
				new CssRulesetDeclarationToken("white-space", "-pre-wrap", $token->MediaTypes),
				// Opera >= 7
				new CssRulesetDeclarationToken("white-space", "-o-pre-wrap", $token->MediaTypes),
				// Internet Explorer >= 5.5
				new CssRulesetDeclarationToken("word-wrap", "break-word", $token->MediaTypes)
				);
			return $r;
			}
		else
			{
			return array();
			}
		}
	}

/**
 * This {@link aCssMinifierFilter minifier filter} will convert @keyframes at-rule block to browser specific counterparts.
 *
 * @package		CssMin/Minifier/Filters
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssConvertLevel3AtKeyframesMinifierFilter extends aCssMinifierFilter
	{
	/**
	 * Implements {@link aCssMinifierFilter::filter()}.
	 *
	 * @param array $tokens Array of objects of type aCssToken
	 * @return integer Count of added, changed or removed tokens; a return value larger than 0 will rebuild the array
	 */
	public function apply(array &$tokens)
		{
		$r = 0;
		$transformations = array("-moz-keyframes", "-webkit-keyframes");
		for ($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if (get_class($tokens[$i]) === "CssAtKeyframesStartToken")
				{
				for ($ii = $i; $ii < $l; $ii++)
					{
					if (get_class($tokens[$ii]) === "CssAtKeyframesEndToken")
						{
						break;
						}
					}
				if (get_class($tokens[$ii]) === "CssAtKeyframesEndToken")
					{
					$add	= array();
					$source	= array();
					for ($iii = $i; $iii <= $ii; $iii++)
						{
						$source[] = clone($tokens[$iii]);
						}
					foreach ($transformations as $transformation)
						{
						$t = array();
						foreach ($source as $token)
							{
							$t[] = clone($token);
							}
						$t[0]->AtRuleName = $transformation;
						$add = array_merge($add, $t);
						}
					if (isset($this->configuration["RemoveSource"]) && $this->configuration["RemoveSource"] === true)
						{
						array_splice($tokens, $i, $ii - $i + 1, $add);
						}
					else
						{
						array_splice($tokens, $ii + 1, 0, $add);
						}
					$l = count($tokens);
					$i = $ii + count($add);
					$r += count($add);
					}
				}
			}
		return $r;
		}
	}

/**
 * This {@link aCssMinifierPlugin} will convert a color value in hsl notation to hexadecimal notation.
 *
 * Example:
 * <code>
 * color: hsl(232,36%,48%);
 * </code>
 *
 * Will get converted to:
 * <code>
 * color:#4e5aa7;
 * </code>
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssConvertHslColorsMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Regular expression matching the value.
	 *
	 * @var string
	 */
	private $reMatch = "/^hsl\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*%\s*,\s*([0-9]+)\s*%\s*\)/iS";
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (stripos($token->Value, "hsl") !== false && preg_match($this->reMatch, $token->Value, $m))
			{
			$token->Value = str_replace($m[0], $this->hsl2hex($m[1], $m[2], $m[3]), $token->Value);
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	/**
	 * Convert a HSL value to hexadecimal notation.
	 *
	 * Based on: {@link http://www.easyrgb.com/index.php?X=MATH&H=19#text19}.
	 *
	 * @param integer $hue Hue
	 * @param integer $saturation Saturation
	 * @param integer $lightness Lightnesss
	 * @return string
	 */
	private function hsl2hex($hue, $saturation, $lightness)
		{
		$hue		= $hue / 360;
		$saturation	= $saturation / 100;
		$lightness	= $lightness / 100;
		if ($saturation == 0)
			{
			$red	= $lightness * 255;
			$green	= $lightness * 255;
			$blue	= $lightness * 255;
			}
		else
			{
			if ($lightness < 0.5 )
				{
				$v2 = $lightness * (1 + $saturation);
				}
			else
				{
				$v2 = ($lightness + $saturation) - ($saturation * $lightness);
				}
			$v1		= 2 * $lightness - $v2;
			$red	= 255 * self::hue2rgb($v1, $v2, $hue + (1 / 3));
			$green	= 255 * self::hue2rgb($v1, $v2, $hue);
			$blue	= 255 * self::hue2rgb($v1, $v2, $hue - (1 / 3));
			}
		return "#" . str_pad(dechex(round($red)), 2, "0", STR_PAD_LEFT) . str_pad(dechex(round($green)), 2, "0", STR_PAD_LEFT) . str_pad(dechex(round($blue)), 2, "0", STR_PAD_LEFT);
		}
	/**
	 * Apply hue to a rgb color value.
	 *
	 * @param integer $v1 Value 1
	 * @param integer $v2 Value 2
	 * @param integer $hue Hue
	 * @return integer
	 */
	private function hue2rgb($v1, $v2, $hue)
		{
		if ($hue < 0)
			{
			$hue += 1;
			}
		if ($hue > 1)
			{
			$hue -= 1;
			}
		if ((6 * $hue) < 1)
			{
			return ($v1 + ($v2 - $v1) * 6 * $hue);
			}
		if ((2 * $hue) < 1)
			{
			return ($v2);
			}
		if ((3 * $hue) < 2)
			{
			return ($v1 + ($v2 - $v1) * (( 2 / 3) - $hue) * 6);
			}
		return $v1;
		}
	}

/**
 * This {@link aCssMinifierPlugin} will convert the font-weight values normal and bold to their numeric notation.
 *
 * Example:
 * <code>
 * font-weight: normal;
 * font: bold 11px monospace;
 * </code>
 *
 * Will get converted to:
 * <code>
 * font-weight:400;
 * font:700 11px monospace;
 * </code>
 *
 * @package		CssMin/Minifier/Pluginsn
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssConvertFontWeightMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Array of included declaration properties this plugin will process; others declaration properties will get
	 * ignored.
	 *
	 * @var array
	 */
	private $include = array
		(
		"font",
		"font-weight"
		);
	/**
	 * Regular expression matching the value.
	 *
	 * @var string
	 */
	private $reMatch = null;
	/**
	 * Regular expression replace the value.
	 *
	 * @var string
	 */
	private $reReplace = "\"\${1}\" . \$this->transformation[\"\${2}\"] . \"\${3}\"";
	/**
	 * Transformation table used by the {@link CssConvertFontWeightMinifierPlugin::$reReplace replace regular expression}.
	 *
	 * @var array
	 */
	private $transformation = array
		(
		"normal"	=> "400",
 		"bold"		=> "700"
		);
	/**
	 * Overwrites {@link aCssMinifierPlugin::__construct()}.
	 *
	 * The constructor will create the {@link CssConvertFontWeightMinifierPlugin::$reReplace replace regular expression}
	 * based on the {@link CssConvertFontWeightMinifierPlugin::$transformation transformation table}.
	 *
	 * @param CssMinifier $minifier The CssMinifier object of this plugin.
	 * @return void
	 */
	public function __construct(CssMinifier $minifier)
		{
		$this->reMatch = "/(^|\s)+(" . implode("|", array_keys($this->transformation)). ")(\s|$)+/eiS";
		parent::__construct($minifier);
		}
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (in_array($token->Property, $this->include) && preg_match($this->reMatch, $token->Value, $m))
			{
			$token->Value = preg_replace($this->reMatch, $this->reReplace, $token->Value);
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	}

/**
 * This {@link aCssMinifierPlugin} will compress several unit values to their short notations. Examples:
 *
 * <code>
 * padding: 0.5em;
 * border: 0px;
 * margin: 0 0 0 0;
 * </code>
 *
 * Will get compressed to:
 *
 * <code>
 * padding:.5px;
 * border:0;
 * margin:0;
 * </code>
 *
 * --
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssCompressUnitValuesMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Regular expression used for matching and replacing unit values.
	 *
	 * @var array
	 */
	private $re = array
		(
		"/(^| |-)0\.([0-9]+?)(0+)?(%|em|ex|px|in|cm|mm|pt|pc)/iS" => "\${1}.\${2}\${4}",
		"/(^| )-?(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/iS" => "\${1}0",
		"/(^0\s0\s0\s0)|(^0\s0\s0$)|(^0\s0$)/iS" => "0"
		);
	/**
	 * Regular expression matching the value.
	 *
	 * @var string
	 */
	private $reMatch = "/(^| |-)0\.([0-9]+?)(0+)?(%|em|ex|px|in|cm|mm|pt|pc)|(^| )-?(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)|(^0\s0\s0\s0$)|(^0\s0\s0$)|(^0\s0$)/iS";
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (preg_match($this->reMatch, $token->Value))
			{
			foreach ($this->re as $reMatch => $reReplace)
				{
				$token->Value = preg_replace($reMatch, $reReplace, $token->Value);
				}
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	}

/**
 * This {@link aCssMinifierPlugin} compress the content of expresssion() declaration values.
 *
 * For compression of expressions {@link https://github.com/rgrove/jsmin-php/ JSMin} will get used. JSMin have to be
 * already included or loadable via {@link http://goo.gl/JrW54 PHP autoloading}.
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssCompressExpressionValuesMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (class_exists("JSMin") && stripos($token->Value, "expression(") !== false)
			{
			$value	= $token->Value;
			$value	= substr($token->Value, stripos($token->Value, "expression(") + 10);
			$value	= trim(JSMin::minify($value));
			$token->Value = "expression(" . $value . ")";
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	}

/**
 * This {@link aCssMinifierPlugin} will convert hexadecimal color value with 6 chars to their 3 char hexadecimal
 * notation (if possible).
 *
 * Example:
 * <code>
 * color: #aabbcc;
 * </code>
 *
 * Will get converted to:
 * <code>
 * color:#abc;
 * </code>
 *
 * @package		CssMin/Minifier/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssCompressColorValuesMinifierPlugin extends aCssMinifierPlugin
	{
	/**
	 * Regular expression matching 6 char hexadecimal color values.
	 *
	 * @var string
	 */
	private $reMatch = "/\#([0-9a-f]{6})/iS";
	/**
	 * Implements {@link aCssMinifierPlugin::minify()}.
	 *
	 * @param aCssToken $token Token to process
	 * @return boolean Return TRUE to break the processing of this token; FALSE to continue
	 */
	public function apply(aCssToken &$token)
		{
		if (strpos($token->Value, "#") !== false && preg_match($this->reMatch, $token->Value, $m))
			{
			$value = strtolower($m[1]);
			if ($value[0] == $value[1] && $value[2] == $value[3] && $value[4] == $value[5])
				{
				$token->Value = str_replace($m[0], "#" . $value[0] . $value[2] . $value[4], $token->Value);
				}
			}
		return false;
		}
	/**
	 * Implements {@link aMinifierPlugin::getTriggerTokens()}
	 *
	 * @return array
	 */
	public function getTriggerTokens()
		{
		return array
			(
			"CssAtFontFaceDeclarationToken",
			"CssAtPageDeclarationToken",
			"CssRulesetDeclarationToken"
			);
		}
	}

/**
 * This {@link aCssToken CSS token} represents a CSS comment.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssCommentToken extends aCssToken
	{
	/**
	 * Comment as Text.
	 *
	 * @var string
	 */
	public $Comment = "";
	/**
	 * Set the properties of a comment token.
	 *
	 * @param string $comment Comment including comment delimiters
	 * @return void
	 */
	public function __construct($comment)
		{
		$this->Comment = $comment;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return $this->Comment;
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing comments.
 *
 * Adds a {@link CssCommentToken} to the parser if a comment was found.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssCommentParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("*", "/");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return false;
		}
	/**
	 * Stored buffer for restore.
	 *
	 * @var string
	 */
	private $restoreBuffer = "";
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		if ($char === "*" && $previousChar === "/" && $state !== "T_COMMENT")
			{
			$this->parser->pushState("T_COMMENT");
			$this->parser->setExclusive(__CLASS__);
			$this->restoreBuffer = substr($this->parser->getAndClearBuffer(), 0, -2);
			}
		elseif ($char === "/" && $previousChar === "*" && $state === "T_COMMENT")
			{
			$this->parser->popState();
			$this->parser->unsetExclusive();
			$this->parser->appendToken(new CssCommentToken("/*" . $this->parser->getAndClearBuffer()));
			$this->parser->setBuffer($this->restoreBuffer);
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the start of a @variables at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtVariablesStartToken extends aCssAtBlockStartToken
	{
	/**
	 * Media types of the @variables at-rule block.
	 *
	 * @var array
	 */
	public $MediaTypes = array();
	/**
	 * Set the properties of a @variables at-rule token.
	 *
	 * @param array $mediaTypes Media types
	 * @return void
	 */
	public function __construct($mediaTypes = null)
		{
		$this->MediaTypes = $mediaTypes ? $mediaTypes : array("all");
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @variables at-rule block with including declarations.
 *
 * Found @variables at-rule blocks will add a {@link CssAtVariablesStartToken} and {@link CssAtVariablesEndToken} to the
 * parser; including declarations as {@link CssAtVariablesDeclarationToken}.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtVariablesParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", "{", "}", ":", ";");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_VARIABLES::PREPARE", "T_AT_VARIABLES", "T_AT_VARIABLES_DECLARATION");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of @variables at-rule block
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 10)) === "@variables")
			{
			$this->parser->pushState("T_AT_VARIABLES::PREPARE");
			$this->parser->clearBuffer();
			return $index + 10;
			}
		// Start of @variables declarations
		elseif ($char === "{" && $state === "T_AT_VARIABLES::PREPARE")
			{
			$this->parser->setState("T_AT_VARIABLES");
			$mediaTypes = array_filter(array_map("trim", explode(",", $this->parser->getAndClearBuffer("{"))));
			$this->parser->appendToken(new CssAtVariablesStartToken($mediaTypes));
			}
		// Start of @variables declaration
		if ($char === ":" && $state === "T_AT_VARIABLES")
			{
			$this->buffer = $this->parser->getAndClearBuffer(":");
			$this->parser->pushState("T_AT_VARIABLES_DECLARATION");
			}
		// Unterminated @variables declaration
		elseif ($char === ":" && $state === "T_AT_VARIABLES_DECLARATION")
			{
			// Ignore Internet Explorer filter declarations
			if ($this->buffer === "filter")
				{
				return false;
				}
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated @variables declaration", $this->buffer . ":" . $this->parser->getBuffer() . "_"));
			}
		// End of @variables declaration
		elseif (($char === ";" || $char === "}") && $state === "T_AT_VARIABLES_DECLARATION")
			{
			$value = $this->parser->getAndClearBuffer(";}");
			if (strtolower(substr($value, -10, 10)) === "!important")
				{
				$value = trim(substr($value, 0, -10));
				$isImportant = true;
				}
			else
				{
				$isImportant = false;
				}
			$this->parser->popState();
			$this->parser->appendToken(new CssAtVariablesDeclarationToken($this->buffer, $value, $isImportant));
			$this->buffer = "";
			}
		// End of @variables at-rule block
		elseif ($char === "}" && $state === "T_AT_VARIABLES")
			{
			$this->parser->popState();
			$this->parser->clearBuffer();
			$this->parser->appendToken(new CssAtVariablesEndToken());
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the end of a @variables at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtVariablesEndToken extends aCssAtBlockEndToken
	{
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "";
		}
	}

/**
 * This {@link aCssToken CSS token} represents a declaration of a @variables at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtVariablesDeclarationToken extends aCssDeclarationToken
	{
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "";
		}
	}

/**
* This {@link aCssToken CSS token} represents the start of a @page at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtPageStartToken extends aCssAtBlockStartToken
	{
	/**
	 * Selector.
	 *
	 * @var string
	 */
	public $Selector = "";
	/**
	 * Sets the properties of the @page at-rule.
	 *
	 * @param string $selector Selector
	 * @return void
	 */
	public function __construct($selector = "")
		{
		$this->Selector = $selector;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "@page" . ($this->Selector ? " " . $this->Selector : "") . "{";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @page at-rule block with including declarations.
 *
 * Found @page at-rule blocks will add a {@link CssAtPageStartToken} and {@link CssAtPageEndToken} to the
 * parser; including declarations as {@link CssAtPageDeclarationToken}.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtPageParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", "{", "}", ":", ";");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_PAGE::SELECTOR", "T_AT_PAGE", "T_AT_PAGE_DECLARATION");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of @page at-rule block
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 5)) === "@page")
			{
			$this->parser->pushState("T_AT_PAGE::SELECTOR");
			$this->parser->clearBuffer();
			return $index + 5;
			}
		// Start of @page declarations
		elseif ($char === "{" && $state === "T_AT_PAGE::SELECTOR")
			{
			$selector = $this->parser->getAndClearBuffer("{");
			$this->parser->setState("T_AT_PAGE");
			$this->parser->clearBuffer();
			$this->parser->appendToken(new CssAtPageStartToken($selector));
			}
		// Start of @page declaration
		elseif ($char === ":" && $state === "T_AT_PAGE")
			{
			$this->parser->pushState("T_AT_PAGE_DECLARATION");
			$this->buffer = $this->parser->getAndClearBuffer(":", true);
			}
		// Unterminated @font-face declaration
		elseif ($char === ":" && $state === "T_AT_PAGE_DECLARATION")
			{
			// Ignore Internet Explorer filter declarations
			if ($this->buffer === "filter")
				{
				return false;
				}
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated @page declaration", $this->buffer . ":" . $this->parser->getBuffer() . "_"));
			}
		// End of @page declaration
		elseif (($char === ";" || $char === "}") && $state == "T_AT_PAGE_DECLARATION")
			{
			$value = $this->parser->getAndClearBuffer(";}");
			if (strtolower(substr($value, -10, 10)) == "!important")
				{
				$value = trim(substr($value, 0, -10));
				$isImportant = true;
				}
			else
				{
				$isImportant = false;
				}
			$this->parser->popState();
			$this->parser->appendToken(new CssAtPageDeclarationToken($this->buffer, $value, $isImportant));
			// --
			if ($char === "}")
				{
				$this->parser->popState();
				$this->parser->appendToken(new CssAtPageEndToken());
				}
			$this->buffer = "";
			}
		// End of @page at-rule block
		elseif ($char === "}" && $state === "T_AT_PAGE")
			{
			$this->parser->popState();
			$this->parser->clearBuffer();
			$this->parser->appendToken(new CssAtPageEndToken());
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the end of a @page at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtPageEndToken extends aCssAtBlockEndToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents a declaration of a @page at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtPageDeclarationToken extends aCssDeclarationToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents the start of a @media at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtMediaStartToken extends aCssAtBlockStartToken
	{
	/**
	 * Sets the properties of the @media at-rule.
	 *
	 * @param array $mediaTypes Media types
	 * @return void
	 */
	public function __construct(array $mediaTypes = array())
		{
		$this->MediaTypes = $mediaTypes;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "@media " . implode(",", $this->MediaTypes) . "{";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @media at-rule block.
 *
 * Found @media at-rule blocks will add a {@link CssAtMediaStartToken} and {@link CssAtMediaEndToken} to the parser.
 * This plugin will also set the the current media types using {@link CssParser::setMediaTypes()} and
 * {@link CssParser::unsetMediaTypes()}.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtMediaParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", "{", "}");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_MEDIA::PREPARE", "T_AT_MEDIA");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 6)) === "@media")
			{
			$this->parser->pushState("T_AT_MEDIA::PREPARE");
			$this->parser->clearBuffer();
			return $index + 6;
			}
		elseif ($char === "{" && $state === "T_AT_MEDIA::PREPARE")
			{
			$mediaTypes = array_filter(array_map("trim", explode(",", $this->parser->getAndClearBuffer("{"))));
			$this->parser->setMediaTypes($mediaTypes);
			$this->parser->setState("T_AT_MEDIA");
			$this->parser->appendToken(new CssAtMediaStartToken($mediaTypes));
			}
		elseif ($char === "}" && $state === "T_AT_MEDIA")
			{
			$this->parser->appendToken(new CssAtMediaEndToken());
			$this->parser->clearBuffer();
			$this->parser->unsetMediaTypes();
			$this->parser->popState();
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the end of a @media at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtMediaEndToken extends aCssAtBlockEndToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents the start of a @keyframes at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtKeyframesStartToken extends aCssAtBlockStartToken
	{
	/**
	 * Name of the at-rule.
	 *
	 * @var string
	 */
	public $AtRuleName = "keyframes";
	/**
	 * Name
	 *
	 * @var string
	 */
	public $Name = "";
	/**
	 * Sets the properties of the @page at-rule.
	 *
	 * @param string $selector Selector
	 * @return void
	 */
	public function __construct($name, $atRuleName = null)
		{
		$this->Name = $name;
		if (!is_null($atRuleName))
			{
			$this->AtRuleName = $atRuleName;
			}
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "@" . $this->AtRuleName . " \"" . $this->Name . "\"{";
		}
	}

/**
 * This {@link aCssToken CSS token} represents the start of a ruleset of a @keyframes at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtKeyframesRulesetStartToken extends aCssRulesetStartToken
	{
	/**
	 * Array of selectors.
	 *
	 * @var array
	 */
	public $Selectors = array();
	/**
	 * Set the properties of a ruleset token.
	 *
	 * @param array $selectors Selectors of the ruleset
	 * @return void
	 */
	public function __construct(array $selectors = array())
		{
		$this->Selectors = $selectors;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return implode(",", $this->Selectors) . "{";
		}
	}

/**
 * This {@link aCssToken CSS token} represents the end of a ruleset of a @keyframes at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtKeyframesRulesetEndToken extends aCssRulesetEndToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents a ruleset declaration of a @keyframes at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtKeyframesRulesetDeclarationToken extends aCssDeclarationToken
	{

	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @keyframes at-rule blocks, rulesets and declarations.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtKeyframesParserPlugin extends aCssParserPlugin
	{
	/**
	 * @var string Keyword
	 */
	private $atRuleName = "";
	/**
	 * Selectors.
	 *
	 * @var array
	 */
	private $selectors = array();
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", "{", "}", ":", ",", ";");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_KEYFRAMES::NAME", "T_AT_KEYFRAMES", "T_AT_KEYFRAMES_RULESETS", "T_AT_KEYFRAMES_RULESET", "T_AT_KEYFRAMES_RULESET_DECLARATION");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of @keyframes at-rule block
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 10)) === "@keyframes")
			{
			$this->atRuleName = "keyframes";
			$this->parser->pushState("T_AT_KEYFRAMES::NAME");
			$this->parser->clearBuffer();
			return $index + 10;
			}
		// Start of @keyframes at-rule block (@-moz-keyframes)
		elseif ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 15)) === "@-moz-keyframes")
			{
			$this->atRuleName = "-moz-keyframes";
			$this->parser->pushState("T_AT_KEYFRAMES::NAME");
			$this->parser->clearBuffer();
			return $index + 15;
			}
		// Start of @keyframes at-rule block (@-webkit-keyframes)
		elseif ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 18)) === "@-webkit-keyframes")
			{
			$this->atRuleName = "-webkit-keyframes";
			$this->parser->pushState("T_AT_KEYFRAMES::NAME");
			$this->parser->clearBuffer();
			return $index + 18;
			}
		// Start of @keyframes rulesets
		elseif ($char === "{" && $state === "T_AT_KEYFRAMES::NAME")
			{
			$name = $this->parser->getAndClearBuffer("{\"'");
			$this->parser->setState("T_AT_KEYFRAMES_RULESETS");
			$this->parser->clearBuffer();
			$this->parser->appendToken(new CssAtKeyframesStartToken($name, $this->atRuleName));
			}
		// Start of @keyframe ruleset and selectors
		if ($char === "," && $state === "T_AT_KEYFRAMES_RULESETS")
			{
			$this->selectors[] = $this->parser->getAndClearBuffer(",{");
			}
		// Start of a @keyframes ruleset
		elseif ($char === "{" && $state === "T_AT_KEYFRAMES_RULESETS")
			{
			if ($this->parser->getBuffer() !== "")
				{
				$this->selectors[] = $this->parser->getAndClearBuffer(",{");
				$this->parser->pushState("T_AT_KEYFRAMES_RULESET");
				$this->parser->appendToken(new CssAtKeyframesRulesetStartToken($this->selectors));
				$this->selectors = array();
				}
			}
		// Start of @keyframes ruleset declaration
		elseif ($char === ":" && $state === "T_AT_KEYFRAMES_RULESET")
			{
			$this->parser->pushState("T_AT_KEYFRAMES_RULESET_DECLARATION");
			$this->buffer = $this->parser->getAndClearBuffer(":;", true);
			}
		// Unterminated @keyframes ruleset declaration
		elseif ($char === ":" && $state === "T_AT_KEYFRAMES_RULESET_DECLARATION")
			{
			// Ignore Internet Explorer filter declarations
			if ($this->buffer === "filter")
				{
				return false;
				}
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated @keyframes ruleset declaration", $this->buffer . ":" . $this->parser->getBuffer() . "_"));
			}
		// End of declaration
		elseif (($char === ";" || $char === "}") && $state === "T_AT_KEYFRAMES_RULESET_DECLARATION")
			{
			$value = $this->parser->getAndClearBuffer(";}");
			if (strtolower(substr($value, -10, 10)) === "!important")
				{
				$value = trim(substr($value, 0, -10));
				$isImportant = true;
				}
			else
				{
				$isImportant = false;
				}
			$this->parser->popState();
			$this->parser->appendToken(new CssAtKeyframesRulesetDeclarationToken($this->buffer, $value, $isImportant));
			// Declaration ends with a right curly brace; so we have to end the ruleset
			if ($char === "}")
				{
				$this->parser->appendToken(new CssAtKeyframesRulesetEndToken());
				$this->parser->popState();
				}
			$this->buffer = "";
			}
		// End of @keyframes ruleset
		elseif ($char === "}" && $state === "T_AT_KEYFRAMES_RULESET")
			{
			$this->parser->clearBuffer();

			$this->parser->popState();
			$this->parser->appendToken(new CssAtKeyframesRulesetEndToken());
			}
		// End of @keyframes rulesets
		elseif ($char === "}" && $state === "T_AT_KEYFRAMES_RULESETS")
			{
			$this->parser->clearBuffer();
			$this->parser->popState();
			$this->parser->appendToken(new CssAtKeyframesEndToken());
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the end of a @keyframes at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtKeyframesEndToken extends aCssAtBlockEndToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents a @import at-rule.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1.b1 (2001-02-22)
 */
class CssAtImportToken extends aCssToken
	{
	/**
	 * Import path of the @import at-rule.
	 *
	 * @var string
	 */
	public $Import = "";
	/**
	 * Media types of the @import at-rule.
	 *
	 * @var array
	 */
	public $MediaTypes = array();
	/**
	 * Set the properties of a @import at-rule token.
	 *
	 * @param string $import Import path
	 * @param array $mediaTypes Media types
	 * @return void
	 */
	public function __construct($import, $mediaTypes)
		{
		$this->Import		= $import;
		$this->MediaTypes	= $mediaTypes ? $mediaTypes : array();
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "@import \"" . $this->Import . "\"" . (count($this->MediaTypes) > 0 ? " "  . implode(",", $this->MediaTypes) : ""). ";";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @import at-rule.
 *
 * If a @import at-rule was found this plugin will add a {@link CssAtImportToken} to the parser.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtImportParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", ";", ",", "\n");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_IMPORT");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 7)) === "@import")
			{
			$this->parser->pushState("T_AT_IMPORT");
			$this->parser->clearBuffer();
			return $index + 7;
			}
		elseif (($char === ";" || $char === "\n") && $state === "T_AT_IMPORT")
			{
			$this->buffer = $this->parser->getAndClearBuffer(";");
			$pos = false;
			foreach (array(")", "\"", "'") as $needle)
				{
				if (($pos = strrpos($this->buffer, $needle)) !== false)
					{
					break;
					}
				}
			$import = substr($this->buffer, 0, $pos + 1);
			if (stripos($import, "url(") === 0)
				{
				$import = substr($import, 4, -1);
				}
			$import = trim($import, " \t\n\r\0\x0B'\"");
			$mediaTypes = array_filter(array_map("trim", explode(",", trim(substr($this->buffer, $pos + 1), " \t\n\r\0\x0B{"))));
			if ($pos)
				{
				$this->parser->appendToken(new CssAtImportToken($import, $mediaTypes));
				}
			else
				{
				CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Invalid @import at-rule syntax", $this->parser->buffer));
				}
			$this->parser->popState();
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the start of a @font-face at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtFontFaceStartToken extends aCssAtBlockStartToken
	{
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "@font-face{";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @font-face at-rule block with including declarations.
 *
 * Found @font-face at-rule blocks will add a {@link CssAtFontFaceStartToken} and {@link CssAtFontFaceEndToken} to the
 * parser; including declarations as {@link CssAtFontFaceDeclarationToken}.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtFontFaceParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", "{", "}", ":", ";");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_FONT_FACE::PREPARE", "T_AT_FONT_FACE", "T_AT_FONT_FACE_DECLARATION");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		// Start of @font-face at-rule block
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 10)) === "@font-face")
			{
			$this->parser->pushState("T_AT_FONT_FACE::PREPARE");
			$this->parser->clearBuffer();
			return $index + 10;
			}
		// Start of @font-face declarations
		elseif ($char === "{" && $state === "T_AT_FONT_FACE::PREPARE")
			{
			$this->parser->setState("T_AT_FONT_FACE");
			$this->parser->clearBuffer();
			$this->parser->appendToken(new CssAtFontFaceStartToken());
			}
		// Start of @font-face declaration
		elseif ($char === ":" && $state === "T_AT_FONT_FACE")
			{
			$this->parser->pushState("T_AT_FONT_FACE_DECLARATION");
			$this->buffer = $this->parser->getAndClearBuffer(":", true);
			}
		// Unterminated @font-face declaration
		elseif ($char === ":" && $state === "T_AT_FONT_FACE_DECLARATION")
			{
			// Ignore Internet Explorer filter declarations
			if ($this->buffer === "filter")
				{
				return false;
				}
			CssMin::triggerError(new CssError(__FILE__, __LINE__, __METHOD__ . ": Unterminated @font-face declaration", $this->buffer . ":" . $this->parser->getBuffer() . "_"));
			}
		// End of @font-face declaration
		elseif (($char === ";" || $char === "}") && $state === "T_AT_FONT_FACE_DECLARATION")
			{
			$value = $this->parser->getAndClearBuffer(";}");
			if (strtolower(substr($value, -10, 10)) === "!important")
				{
				$value = trim(substr($value, 0, -10));
				$isImportant = true;
				}
			else
				{
				$isImportant = false;
				}
			$this->parser->popState();
			$this->parser->appendToken(new CssAtFontFaceDeclarationToken($this->buffer, $value, $isImportant));
			$this->buffer = "";
			// --
			if ($char === "}")
				{
				$this->parser->appendToken(new CssAtFontFaceEndToken());
				$this->parser->popState();
				}
			}
		// End of @font-face at-rule block
		elseif ($char === "}" && $state === "T_AT_FONT_FACE")
			{
			$this->parser->appendToken(new CssAtFontFaceEndToken());
			$this->parser->clearBuffer();
			$this->parser->popState();
			}
		else
			{
			return false;
			}
		return true;
		}
	}

/**
 * This {@link aCssToken CSS token} represents the end of a @font-face at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtFontFaceEndToken extends aCssAtBlockEndToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents a declaration of a @font-face at-rule block.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtFontFaceDeclarationToken extends aCssDeclarationToken
	{

	}

/**
 * This {@link aCssToken CSS token} represents a @charset at-rule.
 *
 * @package		CssMin/Tokens
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtCharsetToken extends aCssToken
	{
	/**
	 * Charset of the @charset at-rule.
	 *
	 * @var string
	 */
	public $Charset = "";
	/**
	 * Set the properties of @charset at-rule token.
	 *
	 * @param string $charset Charset of the @charset at-rule token
	 * @return void
	 */
	public function __construct($charset)
		{
		$this->Charset = $charset;
		}
	/**
	 * Implements {@link aCssToken::__toString()}.
	 *
	 * @return string
	 */
	public function __toString()
		{
		return "@charset " . $this->Charset . ";";
		}
	}

/**
 * {@link aCssParserPlugin Parser plugin} for parsing @charset at-rule.
 *
 * If a @charset at-rule was found this plugin will add a {@link CssAtCharsetToken} to the parser.
 *
 * @package		CssMin/Parser/Plugins
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		3.0.1
 */
class CssAtCharsetParserPlugin extends aCssParserPlugin
	{
	/**
	 * Implements {@link aCssParserPlugin::getTriggerChars()}.
	 *
	 * @return array
	 */
	public function getTriggerChars()
		{
		return array("@", ";", "\n");
		}
	/**
	 * Implements {@link aCssParserPlugin::getTriggerStates()}.
	 *
	 * @return array
	 */
	public function getTriggerStates()
		{
		return array("T_DOCUMENT", "T_AT_CHARSET");
		}
	/**
	 * Implements {@link aCssParserPlugin::parse()}.
	 *
	 * @param integer $index Current index
	 * @param string $char Current char
	 * @param string $previousChar Previous char
	 * @return mixed TRUE will break the processing; FALSE continue with the next plugin; integer set a new index and break the processing
	 */
	public function parse($index, $char, $previousChar, $state)
		{
		if ($char === "@" && $state === "T_DOCUMENT" && strtolower(substr($this->parser->getSource(), $index, 8)) === "@charset")
			{
			$this->parser->pushState("T_AT_CHARSET");
			$this->parser->clearBuffer();
			return $index + 8;
			}
		elseif (($char === ";" || $char === "\n") && $state === "T_AT_CHARSET")
			{
			$charset = $this->parser->getAndClearBuffer(";");
			$this->parser->popState();
			$this->parser->appendToken(new CssAtCharsetToken($charset));
			}
		else
			{
			return false;
			}
		return true;
		}
	}

?>