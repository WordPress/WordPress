<?php

/**
 * JSMinPlus version 1.1
 *
 * Minifies a javascript file using a javascript parser
 *
 * This implements a PHP port of Brendan Eich's Narcissus open source javascript engine (in javascript)
 * References: http://en.wikipedia.org/wiki/Narcissus_(JavaScript_engine)
 * Narcissus sourcecode: http://mxr.mozilla.org/mozilla/source/js/narcissus/
 * JSMinPlus weblog: http://crisp.tweakblogs.net/blog/cat/716
 *
 * Tino Zijdel <crisp@tweakers.net>
 *
 * Usage: $minified = JSMinPlus::minify($script [, $filename])
 *
 * Versionlog (see also changelog.txt):
 * 12-04-2009 - some small bugfixes and performance improvements
 * 09-04-2009 - initial open sourced version 1.0
 *
 * Latest version of this script: http://files.tweakers.net/jsminplus/jsminplus.zip
 *
 */

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is the Narcissus JavaScript engine.
 *
 * The Initial Developer of the Original Code is
 * Brendan Eich <brendan@mozilla.org>.
 * Portions created by the Initial Developer are Copyright (C) 2004
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s): Tino Zijdel <crisp@tweakers.net>
 * PHP port, modifications and minifier routine are (C) 2009
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

define('TOKEN_END', 1);
define('TOKEN_NUMBER', 2);
define('TOKEN_IDENTIFIER', 3);
define('TOKEN_STRING', 4);
define('TOKEN_REGEXP', 5);
define('TOKEN_NEWLINE', 6);
define('TOKEN_CONDCOMMENT_MULTILINE', 7);

define('JS_SCRIPT', 100);
define('JS_BLOCK', 101);
define('JS_LABEL', 102);
define('JS_FOR_IN', 103);
define('JS_CALL', 104);
define('JS_NEW_WITH_ARGS', 105);
define('JS_INDEX', 106);
define('JS_ARRAY_INIT', 107);
define('JS_OBJECT_INIT', 108);
define('JS_PROPERTY_INIT', 109);
define('JS_GETTER', 110);
define('JS_SETTER', 111);
define('JS_GROUP', 112);
define('JS_LIST', 113);

define('DECLARED_FORM', 0);
define('EXPRESSED_FORM', 1);
define('STATEMENT_FORM', 2);

class JSMinPlus
{
	private $parser;
	private $reserved = array(
		'break', 'case', 'catch', 'continue', 'default', 'delete', 'do',
		'else', 'finally', 'for', 'function', 'if', 'in', 'instanceof',
		'new', 'return', 'switch', 'this', 'throw', 'try', 'typeof', 'var',
		'void', 'while', 'with',
		// Words reserved for future use
		'abstract', 'boolean', 'byte', 'char', 'class', 'const', 'debugger',
		'double', 'enum', 'export', 'extends', 'final', 'float', 'goto',
		'implements', 'import', 'int', 'interface', 'long', 'native',
		'package', 'private', 'protected', 'public', 'short', 'static',
		'super', 'synchronized', 'throws', 'transient', 'volatile',
		// These are not reserved, but should be taken into account
		// in isValidIdentifier (See jslint source code)
		'arguments', 'eval', 'true', 'false', 'Infinity', 'NaN', 'null', 'undefined'
	);

	private function __construct()
	{
		$this->parser = new JSParser();
	}

	public static function minify($js, $filename='')
	{
		static $instance;

		// this is a singleton
		if(!$instance)
			$instance = new JSMinPlus();

		return $instance->min($js, $filename);
	}

	private function min($js, $filename)
	{
		try
		{
			$n = $this->parser->parse($js, $filename, 1);
			return $this->parseTree($n);
		}
		catch(Exception $e)
		{
			echo $e->getMessage() . "\n";
		}

		return false;
	}

	private function parseTree($n, $noBlockGrouping = false)
	{
		$s = '';

		switch ($n->type)
		{
			case KEYWORD_FUNCTION:
				$s .= 'function' . ($n->name ? ' ' . $n->name : '') . '(';
				$params = $n->params;
				for ($i = 0, $j = count($params); $i < $j; $i++)
					$s .= ($i ? ',' : '') . $params[$i];
				$s .= '){' . $this->parseTree($n->body, true) . '}';
			break;

			case JS_SCRIPT:
				// we do nothing with funDecls or varDecls
				$noBlockGrouping = true;
			// fall through
			case JS_BLOCK:
				$childs = $n->treeNodes;
				for ($c = 0, $i = 0, $j = count($childs); $i < $j; $i++)
				{
					$t = $this->parseTree($childs[$i]);
					if (strlen($t))
					{
						if ($c)
						{
							if ($childs[$i]->type == KEYWORD_FUNCTION && $childs[$i]->functionForm == DECLARED_FORM)
								$s .= "\n"; // put declared functions on a new line
							else
								$s .= ';';
						}

						$s .= $t;

						$c++;
					}
				}

				if ($c > 1 && !$noBlockGrouping)
				{
					$s = '{' . $s . '}';
				}
			break;

			case KEYWORD_IF:
				$s = 'if(' . $this->parseTree($n->condition) . ')';
				$thenPart = $this->parseTree($n->thenPart);
				$elsePart = $n->elsePart ? $this->parseTree($n->elsePart) : null;

				// quite a rancid hack to see if we should enclose the thenpart in brackets
				if ($thenPart[0] != '{')
				{
					if (strpos($thenPart, 'if(') !== false)
						$thenPart = '{' . $thenPart . '}';
					elseif ($elsePart)
						$thenPart .= ';';
				}

				$s .= $thenPart;

				if ($elsePart)
				{
					$s .= 'else';

					if ($elsePart[0] != '{')
						$s .= ' ';

					$s .= $elsePart;
				}
			break;

			case KEYWORD_SWITCH:
				$s = 'switch(' . $this->parseTree($n->discriminant) . '){';
				$cases = $n->cases;
				for ($i = 0, $j = count($cases); $i < $j; $i++)
				{
					$case = $cases[$i];
					if ($case->type == KEYWORD_CASE)
						$s .= 'case' . ($case->caseLabel->type != TOKEN_STRING ? ' ' : '') . $this->parseTree($case->caseLabel) . ':';
					else
						$s .= 'default:';

					$statement = $this->parseTree($case->statements);
					if ($statement)
						$s .= $statement . ';';
				}
				$s = rtrim($s, ';') . '}';
			break;

			case KEYWORD_FOR:
				$s = 'for(' . ($n->setup ? $this->parseTree($n->setup) : '')
					. ';' . ($n->condition ? $this->parseTree($n->condition) : '')
					. ';' . ($n->update ? $this->parseTree($n->update) : '') . ')'
					. $this->parseTree($n->body);
			break;

			case KEYWORD_WHILE:
				$s = 'while(' . $this->parseTree($n->condition) . ')' . $this->parseTree($n->body);
			break;

			case JS_FOR_IN:
				$s = 'for(' . ($n->varDecl ? $this->parseTree($n->varDecl) : $this->parseTree($n->iterator)) . ' in ' . $this->parseTree($n->object) . ')' . $this->parseTree($n->body);
			break;

			case KEYWORD_DO:
				$s = 'do{' . $this->parseTree($n->body, true) . '}while(' . $this->parseTree($n->condition) . ')';
			break;

			case KEYWORD_BREAK:
			case KEYWORD_CONTINUE:
				$s = $n->value . ($n->label ? ' ' . $n->label : '');
			break;

			case KEYWORD_TRY:
				$s = 'try{' . $this->parseTree($n->tryBlock, true) . '}';
				$catchClauses = $n->catchClauses;
				for ($i = 0, $j = count($catchClauses); $i < $j; $i++)
				{
					$t = $catchClauses[$i];
					$s .= 'catch(' . $t->varName . ($t->guard ? ' if ' . $this->parseTree($t->guard) : '') . '){' . $this->parseTree($t->block, true) . '}';
				}
				if ($n->finallyBlock)
					$s .= 'finally{' . $this->parseTree($n->finallyBlock, true) . '}';
			break;

			case KEYWORD_THROW:
				$s = 'throw ' . $this->parseTree($n->exception);
			break;

			case KEYWORD_RETURN:
				$s = 'return' . ($n->value ? ' ' . $this->parseTree($n->value) : '');
			break;

			case KEYWORD_WITH:
				$s = 'with(' . $this->parseTree($n->object) . ')' . $this->parseTree($n->body);
			break;

			case KEYWORD_VAR:
			case KEYWORD_CONST:
				$s = $n->value . ' ';
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
				{
					$t = $childs[$i];
					$s .= ($i ? ',' : '') . $t->name;
					$u = $t->initializer;
					if ($u)
						$s .= '=' . $this->parseTree($u);
				}
			break;

			case KEYWORD_DEBUGGER:
				throw new Exception('NOT IMPLEMENTED: DEBUGGER');
			break;

			case TOKEN_CONDCOMMENT_MULTILINE:
				$s = $n->value . ' ';
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
					$s .= $this->parseTree($childs[$i]);
			break;

			case OP_SEMICOLON:
				if ($expression = $n->expression)
					$s = $this->parseTree($expression);
			break;

			case JS_LABEL:
				$s = $n->label . ':' . $this->parseTree($n->statement);
			break;

			case OP_COMMA:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
					$s .= ($i ? ',' : '') . $this->parseTree($childs[$i]);
			break;

			case OP_ASSIGN:
				$s = $this->parseTree($n->treeNodes[0]) . $n->value . $this->parseTree($n->treeNodes[1]);
			break;

			case OP_HOOK:
				$s = $this->parseTree($n->treeNodes[0]) . '?' . $this->parseTree($n->treeNodes[1]) . ':' . $this->parseTree($n->treeNodes[2]);
			break;

			case OP_OR: case OP_AND:
			case OP_BITWISE_OR: case OP_BITWISE_XOR: case OP_BITWISE_AND:
			case OP_EQ: case OP_NE: case OP_STRICT_EQ: case OP_STRICT_NE:
			case OP_LT: case OP_LE: case OP_GE: case OP_GT:
			case OP_LSH: case OP_RSH: case OP_URSH:
			case OP_MUL: case OP_DIV: case OP_MOD:
				$s = $this->parseTree($n->treeNodes[0]) . $n->type . $this->parseTree($n->treeNodes[1]);
			break;

			case OP_PLUS:
			case OP_MINUS:
				$s = $this->parseTree($n->treeNodes[0]) . $n->type;
				$nextTokenType = $n->treeNodes[1]->type;
				if (	$nextTokenType == OP_PLUS || $nextTokenType == OP_MINUS ||
					$nextTokenType == OP_INCREMENT || $nextTokenType == OP_DECREMENT ||
					$nextTokenType == OP_UNARY_PLUS || $nextTokenType == OP_UNARY_MINUS
				)
					$s .= ' ';
				$s .= $this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_IN:
				$s = $this->parseTree($n->treeNodes[0]) . ' in ' . $this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_INSTANCEOF:
				$s = $this->parseTree($n->treeNodes[0]) . ' instanceof ' . $this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_DELETE:
				$s = 'delete ' . $this->parseTree($n->treeNodes[0]);
			break;

			case KEYWORD_VOID:
				$s = 'void(' . $this->parseTree($n->treeNodes[0]) . ')';
			break;

			case KEYWORD_TYPEOF:
				$s = 'typeof ' . $this->parseTree($n->treeNodes[0]);
			break;

			case OP_NOT:
			case OP_BITWISE_NOT:
			case OP_UNARY_PLUS:
			case OP_UNARY_MINUS:
				$s = $n->value . $this->parseTree($n->treeNodes[0]);
			break;

			case OP_INCREMENT:
			case OP_DECREMENT:
				if ($n->postfix)
					$s = $this->parseTree($n->treeNodes[0]) . $n->value;
				else
					$s = $n->value . $this->parseTree($n->treeNodes[0]);
			break;

			case OP_DOT:
				$s = $this->parseTree($n->treeNodes[0]) . '.' . $this->parseTree($n->treeNodes[1]);
			break;

			case JS_INDEX:
				$s = $this->parseTree($n->treeNodes[0]);
				// See if we can replace named index with a dot saving 3 bytes
				if (	$n->treeNodes[0]->type == TOKEN_IDENTIFIER &&
					$n->treeNodes[1]->type == TOKEN_STRING &&
					$this->isValidIdentifier(substr($n->treeNodes[1]->value, 1, -1))
				)
					$s .= '.' . substr($n->treeNodes[1]->value, 1, -1);
				else
					$s .= '[' . $this->parseTree($n->treeNodes[1]) . ']';
			break;

			case JS_LIST:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
					$s .= ($i ? ',' : '') . $this->parseTree($childs[$i]);
			break;

			case JS_CALL:
				$s = $this->parseTree($n->treeNodes[0]) . '(' . $this->parseTree($n->treeNodes[1]) . ')';
			break;

			case KEYWORD_NEW:
			case JS_NEW_WITH_ARGS:
				$s = 'new ' . $this->parseTree($n->treeNodes[0]) . '(' . ($n->type == JS_NEW_WITH_ARGS ? $this->parseTree($n->treeNodes[1]) : '') . ')';
			break;

			case JS_ARRAY_INIT:
				$s = '[';
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
				{
					$s .= ($i ? ',' : '') . $this->parseTree($childs[$i]);
				}
				$s .= ']';
			break;

			case JS_OBJECT_INIT:
				$s = '{';
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
				{
					$t = $childs[$i];
					if ($i)
						$s .= ',';
					if ($t->type == JS_PROPERTY_INIT)
					{
						// Ditch the quotes when the index is a valid identifier
						if (	$t->treeNodes[0]->type == TOKEN_STRING &&
							$this->isValidIdentifier(substr($t->treeNodes[0]->value, 1, -1))
						)
							$s .= substr($t->treeNodes[0]->value, 1, -1);
						else
							$s .= $t->treeNodes[0]->value;

						$s .= ':' . $this->parseTree($t->treeNodes[1]);
					}
					else
					{
						$s .= $t->type == JS_GETTER ? 'get' : 'set';
						$s .= ' ' . $t->name . '(';
						$params = $t->params;
						for ($i = 0, $j = count($params); $i < $j; $i++)
							$s .= ($i ? ',' : '') . $params[$i];
						$s .= '){' . $this->parseTree($t->body, true) . '}';
					}
				}
				$s .= '}';
			break;

			case KEYWORD_NULL: case KEYWORD_THIS: case KEYWORD_TRUE: case KEYWORD_FALSE:
			case TOKEN_IDENTIFIER: case TOKEN_NUMBER: case TOKEN_STRING: case TOKEN_REGEXP:
				$s = $n->value;
			break;

			case JS_GROUP:
				$s = '(' . $this->parseTree($n->treeNodes[0]) . ')';
			break;

			default:
				throw new Exception('UNKNOWN TOKEN TYPE: ' . $n->type);
		}

		return $s;
	}

	private function isValidIdentifier($string)
	{
		return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $string) && !in_array($string, $this->reserved);
	}
}

class JSParser
{
	private $t;

	private $opPrecedence = array(
		';' => 0,
		',' => 1,
		'=' => 2, '?' => 2, ':' => 2,
		// The above all have to have the same precedence, see bug 330975.
		'||' => 4,
		'&&' => 5,
		'|' => 6,
		'^' => 7,
		'&' => 8,
		'==' => 9, '!=' => 9, '===' => 9, '!==' => 9,
		'<' => 10, '<=' => 10, '>=' => 10, '>' => 10, 'in' => 10, 'instanceof' => 10,
		'<<' => 11, '>>' => 11, '>>>' => 11,
		'+' => 12, '-' => 12,
		'*' => 13, '/' => 13, '%' => 13,
		'delete' => 14, 'void' => 14, 'typeof' => 14,
		'!' => 14, '~' => 14, 'U+' => 14, 'U-' => 14,
		'++' => 15, '--' => 15,
		'new' => 16,
		'.' => 17,
		JS_NEW_WITH_ARGS => 0, JS_INDEX => 0, JS_CALL => 0,
		JS_ARRAY_INIT => 0, JS_OBJECT_INIT => 0, JS_GROUP => 0
	);

	private $opArity = array(
		',' => -2,
		'=' => 2,
		'?' => 3,
		'||' => 2,
		'&&' => 2,
		'|' => 2,
		'^' => 2,
		'&' => 2,
		'==' => 2, '!=' => 2, '===' => 2, '!==' => 2,
		'<' => 2, '<=' => 2, '>=' => 2, '>' => 2, 'in' => 2, 'instanceof' => 2,
		'<<' => 2, '>>' => 2, '>>>' => 2,
		'+' => 2, '-' => 2,
		'*' => 2, '/' => 2, '%' => 2,
		'delete' => 1, 'void' => 1, 'typeof' => 1,
		'!' => 1, '~' => 1, 'U+' => 1, 'U-' => 1,
		'++' => 1, '--' => 1,
		'new' => 1,
		'.' => 2,
		JS_NEW_WITH_ARGS => 2, JS_INDEX => 2, JS_CALL => 2,
		JS_ARRAY_INIT => 1, JS_OBJECT_INIT => 1, JS_GROUP => 1,
		TOKEN_CONDCOMMENT_MULTILINE => 1
	);

	public function __construct()
	{
		$this->t = new JSTokenizer();
	}

	public function parse($s, $f, $l)
	{
		// initialize tokenizer
		$this->t->init($s, $f, $l);

		$x = new JSCompilerContext(false);
		$n = $this->Script($x);
		if (!$this->t->isDone())
			throw $this->t->newSyntaxError('Syntax error');

		return $n;
	}

	private function Script($x)
	{
		$n = $this->Statements($x);
		$n->type = JS_SCRIPT;
		$n->funDecls = $x->funDecls;
		$n->varDecls = $x->varDecls;

		return $n;
	}

	private function Statements($x)
	{
		$n = new JSNode($this->t, JS_BLOCK);
		array_push($x->stmtStack, $n);

		while (!$this->t->isDone() && $this->t->peek() != OP_RIGHT_CURLY)
			$n->addNode($this->Statement($x));

		array_pop($x->stmtStack);

		return $n;
	}

	private function Block($x)
	{
		$this->t->mustMatch(OP_LEFT_CURLY);
		$n = $this->Statements($x);
		$this->t->mustMatch(OP_RIGHT_CURLY);

		return $n;
	}

	private function Statement($x)
	{
		$tt = $this->t->get();
		$n2 = null;

		// Cases for statements ending in a right curly return early, avoiding the
		// common semicolon insertion magic after this switch.
		switch ($tt)
		{
			case KEYWORD_FUNCTION:
				return $this->FunctionDefinition(
					$x,
					true,
					count($x->stmtStack) > 1 ? STATEMENT_FORM : DECLARED_FORM
				);
			break;

			case OP_LEFT_CURLY:
				$n = $this->Statements($x);
				$this->t->mustMatch(OP_RIGHT_CURLY);
			return $n;

			case KEYWORD_IF:
				$n = new JSNode($this->t);
				$n->condition = $this->ParenExpression($x);
				array_push($x->stmtStack, $n);
				$n->thenPart = $this->Statement($x);
				$n->elsePart = $this->t->match(KEYWORD_ELSE) ? $this->Statement($x) : null;
				array_pop($x->stmtStack);
			return $n;

			case KEYWORD_SWITCH:
				$n = new JSNode($this->t);
				$this->t->mustMatch(OP_LEFT_PAREN);
				$n->discriminant = $this->Expression($x);
				$this->t->mustMatch(OP_RIGHT_PAREN);
				$n->cases = array();
				$n->defaultIndex = -1;

				array_push($x->stmtStack, $n);

				$this->t->mustMatch(OP_LEFT_CURLY);

				while (($tt = $this->t->get()) != OP_RIGHT_CURLY)
				{
					switch ($tt)
					{
						case KEYWORD_DEFAULT:
							if ($n->defaultIndex >= 0)
								throw $this->t->newSyntaxError('More than one switch default');
							// FALL THROUGH
						case KEYWORD_CASE:
							$n2 = new JSNode($this->t);
							if ($tt == KEYWORD_DEFAULT)
								$n->defaultIndex = count($n->cases);
							else
								$n2->caseLabel = $this->Expression($x, OP_COLON);
								break;
						default:
							throw $this->t->newSyntaxError('Invalid switch case');
					}

					$this->t->mustMatch(OP_COLON);
					$n2->statements = new JSNode($this->t, JS_BLOCK);
					while (($tt = $this->t->peek()) != KEYWORD_CASE && $tt != KEYWORD_DEFAULT && $tt != OP_RIGHT_CURLY)
						$n2->statements->addNode($this->Statement($x));

					array_push($n->cases, $n2);
				}

				array_pop($x->stmtStack);
			return $n;

			case KEYWORD_FOR:
				$n = new JSNode($this->t);
				$n->isLoop = true;
				$this->t->mustMatch(OP_LEFT_PAREN);

				if (($tt = $this->t->peek()) != OP_SEMICOLON)
				{
					$x->inForLoopInit = true;
					if ($tt == KEYWORD_VAR || $tt == KEYWORD_CONST)
					{
						$this->t->get();
						$n2 = $this->Variables($x);
					}
					else
					{
						$n2 = $this->Expression($x);
					}
					$x->inForLoopInit = false;
				}

				if ($n2 && $this->t->match(KEYWORD_IN))
				{
					$n->type = JS_FOR_IN;
					if ($n2->type == KEYWORD_VAR)
					{
						if (count($n2->treeNodes) != 1)
						{
							throw $this->t->SyntaxError(
								'Invalid for..in left-hand side',
								$this->t->filename,
								$n2->lineno
							);
						}

						// NB: n2[0].type == IDENTIFIER and n2[0].value == n2[0].name.
						$n->iterator = $n2->treeNodes[0];
						$n->varDecl = $n2;
					}
					else
					{
						$n->iterator = $n2;
						$n->varDecl = null;
					}

					$n->object = $this->Expression($x);
				}
				else
				{
					$n->setup = $n2 ? $n2 : null;
					$this->t->mustMatch(OP_SEMICOLON);
					$n->condition = $this->t->peek() == OP_SEMICOLON ? null : $this->Expression($x);
					$this->t->mustMatch(OP_SEMICOLON);
					$n->update = $this->t->peek() == OP_RIGHT_PAREN ? null : $this->Expression($x);
				}

				$this->t->mustMatch(OP_RIGHT_PAREN);
				$n->body = $this->nest($x, $n);
			return $n;

			case KEYWORD_WHILE:
			        $n = new JSNode($this->t);
			        $n->isLoop = true;
			        $n->condition = $this->ParenExpression($x);
			        $n->body = $this->nest($x, $n);
			return $n;

			case KEYWORD_DO:
				$n = new JSNode($this->t);
				$n->isLoop = true;
				$n->body = $this->nest($x, $n, KEYWORD_WHILE);
				$n->condition = $this->ParenExpression($x);
				if (!$x->ecmaStrictMode)
				{
					// <script language="JavaScript"> (without version hints) may need
					// automatic semicolon insertion without a newline after do-while.
					// See http://bugzilla.mozilla.org/show_bug.cgi?id=238945.
					$this->t->match(OP_SEMICOLON);
					return $n;
				}
			break;

			case KEYWORD_BREAK:
			case KEYWORD_CONTINUE:
				$n = new JSNode($this->t);

				if ($this->t->peekOnSameLine() == TOKEN_IDENTIFIER)
				{
					$this->t->get();
					$n->label = $this->t->currentToken()->value;
				}

				$ss = $x->stmtStack;
				$i = count($ss);
				$label = $n->label;
				if ($label)
				{
					do
					{
						if (--$i < 0)
							throw $this->t->newSyntaxError('Label not found');
					}
					while ($ss[$i]->label != $label);
				}
				else
				{
					do
					{
						if (--$i < 0)
							throw $this->t->newSyntaxError('Invalid ' . $tt);
					}
					while (!$ss[$i]->isLoop && ($tt != KEYWORD_BREAK || $ss[$i]->type != KEYWORD_SWITCH));
				}

				$n->target = $ss[$i];
			break;

			case KEYWORD_TRY:
				$n = new JSNode($this->t);
				$n->tryBlock = $this->Block($x);
				$n->catchClauses = array();

				while ($this->t->match(KEYWORD_CATCH))
				{
					$n2 = new JSNode($this->t);
					$this->t->mustMatch(OP_LEFT_PAREN);
					$n2->varName = $this->t->mustMatch(TOKEN_IDENTIFIER)->value;

					if ($this->t->match(KEYWORD_IF))
					{
						if ($x->ecmaStrictMode)
							throw $this->t->newSyntaxError('Illegal catch guard');

						if (count($n->catchClauses) && !end($n->catchClauses)->guard)
							throw $this->t->newSyntaxError('Guarded catch after unguarded');

						$n2->guard = $this->Expression($x);
					}
					else
					{
						$n2->guard = null;
					}

					$this->t->mustMatch(OP_RIGHT_PAREN);
					$n2->block = $this->Block($x);
					array_push($n->catchClauses, $n2);
				}

				if ($this->t->match(KEYWORD_FINALLY))
					$n->finallyBlock = $this->Block($x);

				if (!count($n->catchClauses) && !$n->finallyBlock)
					throw $this->t->newSyntaxError('Invalid try statement');
			return $n;

			case KEYWORD_CATCH:
			case KEYWORD_FINALLY:
				throw $this->t->newSyntaxError($tt + ' without preceding try');

			case KEYWORD_THROW:
				$n = new JSNode($this->t);
				$n->exception = $this->Expression($x);
			break;

			case KEYWORD_RETURN:
				if (!$x->inFunction)
					throw $this->t->newSyntaxError('Invalid return');

				$n = new JSNode($this->t);
				$tt = $this->t->peekOnSameLine();
				if ($tt != TOKEN_END && $tt != TOKEN_NEWLINE && $tt != OP_SEMICOLON && $tt != OP_RIGHT_CURLY)
					$n->value = $this->Expression($x);
				else
					$n->value = null;
			break;

			case KEYWORD_WITH:
				$n = new JSNode($this->t);
				$n->object = $this->ParenExpression($x);
				$n->body = $this->nest($x, $n);
			return $n;

			case KEYWORD_VAR:
			case KEYWORD_CONST:
			        $n = $this->Variables($x);
			break;

			case TOKEN_CONDCOMMENT_MULTILINE:
				$n = new JSNode($this->t);
			return $n;

			case KEYWORD_DEBUGGER:
				$n = new JSNode($this->t);
			break;

			case TOKEN_NEWLINE:
			case OP_SEMICOLON:
				$n = new JSNode($this->t, OP_SEMICOLON);
				$n->expression = null;
			return $n;

			default:
				if ($tt == TOKEN_IDENTIFIER)
				{
					$this->t->scanOperand = false;
					$tt = $this->t->peek();
					$this->t->scanOperand = true;
					if ($tt == OP_COLON)
					{
						$label = $this->t->currentToken()->value;
						$ss = $x->stmtStack;
						for ($i = count($ss) - 1; $i >= 0; --$i)
						{
							if ($ss[$i]->label == $label)
								throw $this->t->newSyntaxError('Duplicate label');
						}

						$this->t->get();
						$n = new JSNode($this->t, JS_LABEL);
						$n->label = $label;
						$n->statement = $this->nest($x, $n);

						return $n;
					}
				}

				$n = new JSNode($this->t, OP_SEMICOLON);
				$this->t->unget();
				$n->expression = $this->Expression($x);
				$n->end = $n->expression->end;
			break;
		}

		if ($this->t->lineno == $this->t->currentToken()->lineno)
		{
			$tt = $this->t->peekOnSameLine();
			if ($tt != TOKEN_END && $tt != TOKEN_NEWLINE && $tt != OP_SEMICOLON && $tt != OP_RIGHT_CURLY)
				throw $this->t->newSyntaxError('Missing ; before statement');
		}

		$this->t->match(OP_SEMICOLON);

		return $n;
	}

	private function FunctionDefinition($x, $requireName, $functionForm)
	{
		$f = new JSNode($this->t);

		if ($f->type != KEYWORD_FUNCTION)
			$f->type = ($f->value == 'get') ? JS_GETTER : JS_SETTER;

		if ($this->t->match(TOKEN_IDENTIFIER))
			$f->name = $this->t->currentToken()->value;
		elseif ($requireName)
			throw $this->t->newSyntaxError('Missing function identifier');

		$this->t->mustMatch(OP_LEFT_PAREN);
			$f->params = array();

		while (($tt = $this->t->get()) != OP_RIGHT_PAREN)
		{
			if ($tt != TOKEN_IDENTIFIER)
				throw $this->t->newSyntaxError('Missing formal parameter');

			array_push($f->params, $this->t->currentToken()->value);

			if ($this->t->peek() != OP_RIGHT_PAREN)
				$this->t->mustMatch(OP_COMMA);
		}

		$this->t->mustMatch(OP_LEFT_CURLY);

		$x2 = new JSCompilerContext(true);
		$f->body = $this->Script($x2);

		$this->t->mustMatch(OP_RIGHT_CURLY);
		$f->end = $this->t->currentToken()->end;

		$f->functionForm = $functionForm;
		if ($functionForm == DECLARED_FORM)
			array_push($x->funDecls, $f);

		return $f;
	}

	private function Variables($x)
	{
		$n = new JSNode($this->t);

		do
		{
			$this->t->mustMatch(TOKEN_IDENTIFIER);

			$n2 = new JSNode($this->t);
			$n2->name = $n2->value;

			if ($this->t->match(OP_ASSIGN))
			{
				if ($this->t->currentToken()->assignOp)
					throw $this->t->newSyntaxError('Invalid variable initialization');

				$n2->initializer = $this->Expression($x, OP_COMMA);
			}

			$n2->readOnly = $n->type == KEYWORD_CONST;

			$n->addNode($n2);
			array_push($x->varDecls, $n2);
		}
		while ($this->t->match(OP_COMMA));

		return $n;
	}

	private function Expression($x, $stop=false)
	{
		$operators = array();
		$operands = array();
		$n = false;

		$bl = $x->bracketLevel;
		$cl = $x->curlyLevel;
		$pl = $x->parenLevel;
		$hl = $x->hookLevel;

		while (($tt = $this->t->get()) != TOKEN_END)
		{
			if ($tt == $stop &&
				$x->bracketLevel == $bl &&
				$x->curlyLevel == $cl &&
				$x->parenLevel == $pl &&
				$x->hookLevel == $hl
			)
			{
				// Stop only if tt matches the optional stop parameter, and that
				// token is not quoted by some kind of bracket.
				break;
			}

			switch ($tt)
			{
				case OP_SEMICOLON:
					// NB: cannot be empty, Statement handled that.
					break 2;

				case OP_ASSIGN:
				case OP_HOOK:
				case OP_COLON:
					if ($this->t->scanOperand)
						break 2;

					// Use >, not >=, for right-associative ASSIGN and HOOK/COLON.
					while (	!empty($operators) &&
						(	$this->opPrecedence[end($operators)->type] > $this->opPrecedence[$tt] ||
							($tt == OP_COLON && end($operators)->type == OP_ASSIGN)
						)
					)
						$this->reduce($operators, $operands);

					if ($tt == OP_COLON)
					{
						$n = end($operators);
						if ($n->type != OP_HOOK)
							throw $this->t->newSyntaxError('Invalid label');

						--$x->hookLevel;
					}
					else
					{
						array_push($operators, new JSNode($this->t));
						if ($tt == OP_ASSIGN)
							end($operands)->assignOp = $this->t->currentToken()->assignOp;
						else
							++$x->hookLevel;
					}

					$this->t->scanOperand = true;
				break;

				case KEYWORD_IN:
					// An in operator should not be parsed if we're parsing the head of
					// a for (...) loop, unless it is in the then part of a conditional
					// expression, or parenthesized somehow.
					if ($x->inForLoopInit && !$x->hookLevel &&
						!$x->bracketLevel && !$x->curlyLevel &&
						!$x->parenLevel
					)
					{
						break 2;
					}
				// FALL THROUGH
				case OP_COMMA:
					// Treat comma as left-associative so reduce can fold left-heavy
					// COMMA trees into a single array.
					// FALL THROUGH
				case OP_OR:
				case OP_AND:
				case OP_BITWISE_OR:
				case OP_BITWISE_XOR:
				case OP_BITWISE_AND:
				case OP_EQ: case OP_NE: case OP_STRICT_EQ: case OP_STRICT_NE:
				case OP_LT: case OP_LE: case OP_GE: case OP_GT:
				case KEYWORD_INSTANCEOF:
				case OP_LSH: case OP_RSH: case OP_URSH:
				case OP_PLUS: case OP_MINUS:
				case OP_MUL: case OP_DIV: case OP_MOD:
				case OP_DOT:
					if ($this->t->scanOperand)
						break 2;

					while (	!empty($operators) &&
						$this->opPrecedence[end($operators)->type] >= $this->opPrecedence[$tt]
					)
						$this->reduce($operators, $operands);

					if ($tt == OP_DOT)
					{
						$this->t->mustMatch(TOKEN_IDENTIFIER);
						array_push($operands, new JSNode($this->t, OP_DOT, array_pop($operands), new JSNode($this->t)));
					}
					else
					{
						array_push($operators, new JSNode($this->t));
						$this->t->scanOperand = true;
					}
				break;

				case KEYWORD_DELETE: case KEYWORD_VOID: case KEYWORD_TYPEOF:
				case OP_NOT: case OP_BITWISE_NOT: case OP_UNARY_PLUS: case OP_UNARY_MINUS:
				case KEYWORD_NEW:
					if (!$this->t->scanOperand)
						break 2;

					array_push($operators, new JSNode($this->t));
				break;

				case OP_INCREMENT: case OP_DECREMENT:
					if ($this->t->scanOperand)
					{
						array_push($operators, new JSNode($this->t));  // prefix increment or decrement
					}
					else
					{
						// Don't cross a line boundary for postfix {in,de}crement.
						$t = $this->t->tokens[($this->t->tokenIndex + $this->t->lookahead - 1) & 3];
						if ($t && $t->lineno != $this->t->lineno)
							break 2;

						if (!empty($operators))
						{
							// Use >, not >=, so postfix has higher precedence than prefix.
							while ($this->opPrecedence[end($operators)->type] > $this->opPrecedence[$tt])
								$this->reduce($operators, $operands);
						}

						$n = new JSNode($this->t, $tt, array_pop($operands));
						$n->postfix = true;
						array_push($operands, $n);
					}
				break;

				case KEYWORD_FUNCTION:
					if (!$this->t->scanOperand)
						break 2;

					array_push($operands, $this->FunctionDefinition($x, false, EXPRESSED_FORM));
					$this->t->scanOperand = false;
				break;

				case KEYWORD_NULL: case KEYWORD_THIS: case KEYWORD_TRUE: case KEYWORD_FALSE:
				case TOKEN_IDENTIFIER: case TOKEN_NUMBER: case TOKEN_STRING: case TOKEN_REGEXP:
					if (!$this->t->scanOperand)
						break 2;

					array_push($operands, new JSNode($this->t));
					$this->t->scanOperand = false;
				break;

				case TOKEN_CONDCOMMENT_MULTILINE:
					if ($this->t->scanOperand)
						array_push($operators, new JSNode($this->t));
					else
						array_push($operands, new JSNode($this->t));
				break;

				case OP_LEFT_BRACKET:
					if ($this->t->scanOperand)
					{
						// Array initialiser.  Parse using recursive descent, as the
						// sub-grammar here is not an operator grammar.
						$n = new JSNode($this->t, JS_ARRAY_INIT);
						while (($tt = $this->t->peek()) != OP_RIGHT_BRACKET)
						{
							if ($tt == OP_COMMA)
							{
								$this->t->get();
								$n->addNode(null);
								continue;
							}

							$n->addNode($this->Expression($x, OP_COMMA));
							if (!$this->t->match(OP_COMMA))
								break;
						}

						$this->t->mustMatch(OP_RIGHT_BRACKET);
						array_push($operands, $n);
						$this->t->scanOperand = false;
					}
					else
					{
						// Property indexing operator.
						array_push($operators, new JSNode($this->t, JS_INDEX));
						$this->t->scanOperand = true;
						++$x->bracketLevel;
					}
				break;

				case OP_RIGHT_BRACKET:
					if ($this->t->scanOperand || $x->bracketLevel == $bl)
						break 2;

					while ($this->reduce($operators, $operands)->type != JS_INDEX)
						continue;

					--$x->bracketLevel;
				break;

				case OP_LEFT_CURLY:
					if (!$this->t->scanOperand)
						break 2;

					// Object initialiser.  As for array initialisers (see above),
					// parse using recursive descent.
					++$x->curlyLevel;
					$n = new JSNode($this->t, JS_OBJECT_INIT);
					while (!$this->t->match(OP_RIGHT_CURLY))
					{
						do
						{
							$tt = $this->t->get();
							$tv = $this->t->currentToken()->value;
							if (($tv == 'get' || $tv == 'set') && $this->t->peek() == TOKEN_IDENTIFIER)
							{
								if ($x->ecmaStrictMode)
									throw $this->t->newSyntaxError('Illegal property accessor');

								$n->addNode($this->FunctionDefinition($x, true, EXPRESSED_FORM));
							}
							else
							{
								switch ($tt)
								{
									case TOKEN_IDENTIFIER:
									case TOKEN_NUMBER:
									case TOKEN_STRING:
										$id = new JSNode($this->t);
									break;

									case OP_RIGHT_CURLY:
										if ($x->ecmaStrictMode)
											throw $this->t->newSyntaxError('Illegal trailing ,');
									break 3;

									default:
										throw $this->t->newSyntaxError('Invalid property name');
								}

								$this->t->mustMatch(OP_COLON);
								$n->addNode(new JSNode($this->t, JS_PROPERTY_INIT, $id, $this->Expression($x, OP_COMMA)));
							}
						}
						while ($this->t->match(OP_COMMA));

						$this->t->mustMatch(OP_RIGHT_CURLY);
						break;
					}

					array_push($operands, $n);
					$this->t->scanOperand = false;
					--$x->curlyLevel;
				break;

				case OP_RIGHT_CURLY:
					if (!$this->t->scanOperand && $x->curlyLevel != $cl)
						throw new Exception('PANIC: right curly botch');
				break 2;

				case OP_LEFT_PAREN:
					if ($this->t->scanOperand)
					{
						array_push($operators, new JSNode($this->t, JS_GROUP));
					}
					else
					{
						while (	!empty($operators) &&
							$this->opPrecedence[end($operators)->type] > $this->opPrecedence[KEYWORD_NEW]
						)
							$this->reduce($operators, $operands);

						// Handle () now, to regularize the n-ary case for n > 0.
						// We must set scanOperand in case there are arguments and
						// the first one is a regexp or unary+/-.
						$n = end($operators);
						$this->t->scanOperand = true;
						if ($this->t->match(OP_RIGHT_PAREN))
						{
							if ($n && $n->type == KEYWORD_NEW)
							{
								array_pop($operators);
								$n->addNode(array_pop($operands));
							}
							else
							{
								$n = new JSNode($this->t, JS_CALL, array_pop($operands), new JSNode($this->t, JS_LIST));
							}

							array_push($operands, $n);
							$this->t->scanOperand = false;
							break;
						}

						if ($n && $n->type == KEYWORD_NEW)
							$n->type = JS_NEW_WITH_ARGS;
						else
							array_push($operators, new JSNode($this->t, JS_CALL));
					}

					++$x->parenLevel;
				break;

				case OP_RIGHT_PAREN:
					if ($this->t->scanOperand || $x->parenLevel == $pl)
						break 2;

					while (($tt = $this->reduce($operators, $operands)->type) != JS_GROUP &&
						$tt != JS_CALL && $tt != JS_NEW_WITH_ARGS
					)
					{
						continue;
					}

					if ($tt != JS_GROUP)
					{
						$n = end($operands);
						if ($n->treeNodes[1]->type != OP_COMMA)
							$n->treeNodes[1] = new JSNode($this->t, JS_LIST, $n->treeNodes[1]);
						else
							$n->treeNodes[1]->type = JS_LIST;
					}

					--$x->parenLevel;
				break;

				// Automatic semicolon insertion means we may scan across a newline
				// and into the beginning of another statement.  If so, break out of
				// the while loop and let the t.scanOperand logic handle errors.
				default:
					break 2;
			}
		}

		if ($x->hookLevel != $hl)
			throw $this->t->newSyntaxError('Missing : after ?');

		if ($x->parenLevel != $pl)
			throw $this->t->newSyntaxError('Missing ) in parenthetical');

		if ($x->bracketLevel != $bl)
			throw $this->t->newSyntaxError('Missing ] in index expression');

		if ($this->t->scanOperand)
			throw $this->t->newSyntaxError('Missing operand');

		// Resume default mode, scanning for operands, not operators.
		$this->t->scanOperand = true;
		$this->t->unget();

		while (count($operators))
			$this->reduce($operators, $operands);

		return array_pop($operands);
	}

	private function ParenExpression($x)
	{
		$this->t->mustMatch(OP_LEFT_PAREN);
		$n = $this->Expression($x);
		$this->t->mustMatch(OP_RIGHT_PAREN);

		return $n;
	}

	// Statement stack and nested statement handler.
	private function nest($x, $node, $end = false)
	{
		array_push($x->stmtStack, $node);
		$n = $this->statement($x);
		array_pop($x->stmtStack);

		if ($end)
			$this->t->mustMatch($end);

		return $n;
	}

	private function reduce(&$operators, &$operands)
	{
		$n = array_pop($operators);
		$op = $n->type;
		$arity = $this->opArity[$op];
		$c = count($operands);
		if ($arity == -2)
		{
			// Flatten left-associative trees
			if ($c >= 2)
			{
				$left = $operands[$c - 2];
				if ($left->type == $op)
				{
					$right = array_pop($operands);
					$left->addNode($right);
					return $left;
				}
			}
			$arity = 2;
		}

		// Always use push to add operands to n, to update start and end
		$a = array_splice($operands, $c - $arity);
		for ($i = 0; $i < $arity; $i++)
			$n->addNode($a[$i]);

		// Include closing bracket or postfix operator in [start,end]
		$te = $this->t->currentToken()->end;
		if ($n->end < $te)
			$n->end = $te;

		array_push($operands, $n);

		return $n;
	}
}

class JSCompilerContext
{
	public $inFunction = false;
	public $inForLoopInit = false;
	public $ecmaStrictMode = false;
	public $bracketLevel = 0;
	public $curlyLevel = 0;
	public $parenLevel = 0;
	public $hookLevel = 0;

	public $stmtStack = array();
	public $funDecls = array();
	public $varDecls = array();

	public function __construct($inFunction)
	{
		$this->inFunction = $inFunction;
	}
}

class JSNode
{
	private $type;
	private $value;
	private $lineno;
	private $start;
	private $end;

	public $treeNodes = array();
	public $funDecls = array();
	public $varDecls = array();

	public function __construct($t, $type=0)
	{
		if ($token = $t->currentToken())
		{
			$this->type = $type ? $type : $token->type;
			$this->value = $token->value;
			$this->lineno = $token->lineno;
			$this->start = $token->start;
			$this->end = $token->end;
		}
		else
		{
			$this->type = $type;
			$this->lineno = $t->lineno;
		}

		if (($numargs = func_num_args()) > 2)
		{
			$args = func_get_args();;
			for ($i = 2; $i < $numargs; $i++)
				$this->addNode($args[$i]);
		}
	}

	// we don't want to bloat our object with all kind of specific properties, so we use overloading
	public function __set($name, $value)
	{
		$this->$name = $value;
	}

	public function __get($name)
	{
		if (isset($this->$name))
			return $this->$name;

		return null;
	}

	public function addNode($node)
	{
		$this->treeNodes[] = $node;
	}
}

class JSTokenizer
{
	private $cursor = 0;
	private $source;

	public $tokens = array();
	public $tokenIndex = 0;
	public $lookahead = 0;
	public $scanNewlines = false;
	public $scanOperand = true;

	public $filename;
	public $lineno;

	private $keywords = array(
		'break',
		'case', 'catch', 'const', 'continue',
		'debugger', 'default', 'delete', 'do',
		'else', 'enum',
		'false', 'finally', 'for', 'function',
		'if', 'in', 'instanceof',
		'new', 'null',
		'return',
		'switch',
		'this', 'throw', 'true', 'try', 'typeof',
		'var', 'void',
		'while', 'with'
	);

	private $opTypeNames = array(
		';'	=> 'SEMICOLON',
		','	=> 'COMMA',
		'?'	=> 'HOOK',
		':'	=> 'COLON',
		'||'	=> 'OR',
		'&&'	=> 'AND',
		'|'	=> 'BITWISE_OR',
		'^'	=> 'BITWISE_XOR',
		'&'	=> 'BITWISE_AND',
		'==='	=> 'STRICT_EQ',
		'=='	=> 'EQ',
		'='	=> 'ASSIGN',
		'!=='	=> 'STRICT_NE',
		'!='	=> 'NE',
		'<<'	=> 'LSH',
		'<='	=> 'LE',
		'<'	=> 'LT',
		'>>>'	=> 'URSH',
		'>>'	=> 'RSH',
		'>='	=> 'GE',
		'>'	=> 'GT',
		'++'	=> 'INCREMENT',
		'--'	=> 'DECREMENT',
		'+'	=> 'PLUS',
		'-'	=> 'MINUS',
		'*'	=> 'MUL',
		'/'	=> 'DIV',
		'%'	=> 'MOD',
		'!'	=> 'NOT',
		'~'	=> 'BITWISE_NOT',
		'.'	=> 'DOT',
		'['	=> 'LEFT_BRACKET',
		']'	=> 'RIGHT_BRACKET',
		'{'	=> 'LEFT_CURLY',
		'}'	=> 'RIGHT_CURLY',
		'('	=> 'LEFT_PAREN',
		')'	=> 'RIGHT_PAREN',
		'@*/'	=> 'CONDCOMMENT_END'
	);

	private $assignOps = array('|', '^', '&', '<<', '>>', '>>>', '+', '-', '*', '/', '%');
	private $opRegExp;

	public function __construct()
	{
		$this->opRegExp = '#^(' . implode('|', array_map('preg_quote', array_keys($this->opTypeNames))) . ')#';

		// this is quite a hidden yet convenient place to create the defines for operators and keywords
		foreach ($this->opTypeNames as $operand => $name)
			define('OP_' . $name, $operand);

		define('OP_UNARY_PLUS', 'U+');
		define('OP_UNARY_MINUS', 'U-');

		foreach ($this->keywords as $keyword)
			define('KEYWORD_' . strtoupper($keyword), $keyword);
	}

	public function init($source, $filename = '', $lineno = 1)
	{
		$this->source = $source;
		$this->filename = $filename ? $filename : '[inline]';
		$this->lineno = $lineno;

		$this->cursor = 0;
		$this->tokens = array();
		$this->tokenIndex = 0;
		$this->lookahead = 0;
		$this->scanNewlines = false;
		$this->scanOperand = true;
	}

	public function getInput($chunksize)
	{
		if ($chunksize)
			return substr($this->source, $this->cursor, $chunksize);

		return substr($this->source, $this->cursor);
	}

	public function isDone()
	{
		return $this->peek() == TOKEN_END;
	}

	public function match($tt)
	{
		return $this->get() == $tt || $this->unget();
	}

	public function mustMatch($tt)
	{
	        if (!$this->match($tt))
			throw $this->newSyntaxError('Unexpected token; token ' . $tt . ' expected');

		return $this->currentToken();
	}

	public function peek()
	{
		if ($this->lookahead)
		{
			$next = $this->tokens[($this->tokenIndex + $this->lookahead) & 3];
			if ($this->scanNewlines && $next->lineno != $this->lineno)
				$tt = TOKEN_NEWLINE;
			else
				$tt = $next->type;
		}
		else
		{
			$tt = $this->get();
			$this->unget();
		}

		return $tt;
	}

	public function peekOnSameLine()
	{
		$this->scanNewlines = true;
		$tt = $this->peek();
		$this->scanNewlines = false;

		return $tt;
	}

	public function currentToken()
	{
		if (!empty($this->tokens))
			return $this->tokens[$this->tokenIndex];
	}

	public function get($chunksize = 1000)
	{
		while($this->lookahead)
		{
			$this->lookahead--;
			$this->tokenIndex = ($this->tokenIndex + 1) & 3;
			$token = $this->tokens[$this->tokenIndex];
			if ($token->type != TOKEN_NEWLINE || $this->scanNewlines)
				return $token->type;
		}

		$conditional_comment = false;

		// strip whitespace and comments
		while(true)
		{
			$input = $this->getInput($chunksize);

			// whitespace handling; gobble up \r as well (effectively we don't have support for MAC newlines!)
			$re = $this->scanNewlines ? '/^[ \r\t]+/' : '/^\s+/';
			if (preg_match($re, $input, $match))
			{
				$spaces = $match[0];
				$spacelen = strlen($spaces);
				$this->cursor += $spacelen;
				if (!$this->scanNewlines)
					$this->lineno += substr_count($spaces, "\n");

				if ($spacelen == $chunksize)
					continue; // complete chunk contained whitespace

				$input = $this->getInput($chunksize);
				if ($input == '' || $input[0] != '/')
					break;
			}

			// Comments
			if (!preg_match('/^\/(?:\*(@(?:cc_on|if|elif|else|end))?(?:.|\n)*?\*\/|\/.*)/', $input, $match))
			{
				if (!$chunksize)
					break;

				// retry with a full chunk fetch; this also prevents breakage of long regular expressions (which will never match a comment)
				$chunksize = null;
				continue;
			}

			// check if this is a conditional (JScript) comment
			if (!empty($match[1]))
			{
				//$match[0] = '/*' . $match[1];
				$conditional_comment = true;
				break;
			}
			else
			{
				$this->cursor += strlen($match[0]);
				$this->lineno += substr_count($match[0], "\n");
			}
		}

		if ($input == '')
		{
			$tt = TOKEN_END;
			$match = array('');
		}
		elseif ($conditional_comment)
		{
			$tt = TOKEN_CONDCOMMENT_MULTILINE;
		}
		else
		{
			switch ($input[0])
			{
				case '0': case '1': case '2': case '3': case '4':
				case '5': case '6': case '7': case '8': case '9':
					if (preg_match('/^\d+\.\d*(?:[eE][-+]?\d+)?|^\d+(?:\.\d*)?[eE][-+]?\d+/', $input, $match))
					{
						$tt = TOKEN_NUMBER;
					}
					elseif (preg_match('/^0[xX][\da-fA-F]+|^0[0-7]*|^\d+/', $input, $match))
					{
						// this should always match because of \d+
						$tt = TOKEN_NUMBER;
					}
				break;

				case '"':
				case "'":
					if (preg_match('/^"(?:\\\\(?:.|\r?\n)|[^\\\\"\r\n])*"|^\'(?:\\\\(?:.|\r?\n)|[^\\\\\'\r\n])*\'/', $input, $match))
					{
						$tt = TOKEN_STRING;
					}
					else
					{
						if ($chunksize)
							return $this->get(null); // retry with a full chunk fetch

						throw $this->newSyntaxError('Unterminated string literal');
					}
				break;

				case '/':
					if ($this->scanOperand && preg_match('/^\/((?:\\\\.|\[(?:\\\\.|[^\]])*\]|[^\/])+)\/([gimy]*)/', $input, $match))
					{
						$tt = TOKEN_REGEXP;
						break;
					}
				// fall through

				case '|':
				case '^':
				case '&':
				case '<':
				case '>':
				case '+':
				case '-':
				case '*':
				case '%':
				case '=':
				case '!':
					// should always match
					preg_match($this->opRegExp, $input, $match);
					$op = $match[0];
					if (in_array($op, $this->assignOps) && $input[strlen($op)] == '=')
					{
						$tt = OP_ASSIGN;
						$match[0] .= '=';
					}
					else
					{
						$tt = $op;
						if ($this->scanOperand)
						{
							if ($op == OP_PLUS)
								$tt = OP_UNARY_PLUS;
							elseif ($op == OP_MINUS)
								$tt = OP_UNARY_MINUS;
						}
						$op = null;
					}
				break;

				case '.':
					if (preg_match('/^\.\d+(?:[eE][-+]?\d+)?/', $input, $match))
					{
						$tt = TOKEN_NUMBER;
						break;
					}
				// fall through

				case ';':
				case ',':
				case '?':
				case ':':
				case '~':
				case '[':
				case ']':
				case '{':
				case '}':
				case '(':
				case ')':
					// these are all single
					$match = array($input[0]);
					$tt = $input[0];
				break;

				case '@':
					throw $this->newSyntaxError('Illegal token');
				break;

				case "\n":
					if ($this->scanNewlines)
					{
						$match = array("\n");
						$tt = TOKEN_NEWLINE;
					}
					else
						throw $this->newSyntaxError('Illegal token');
				break;

				default:
					// FIXME: add support for unicode and unicode escape sequence \uHHHH
					if (preg_match('/^[$\w]+/', $input, $match))
					{
						$tt = in_array($match[0], $this->keywords) ? $match[0] : TOKEN_IDENTIFIER;
					}
					else
						throw $this->newSyntaxError('Illegal token');
			}
		}

		$this->tokenIndex = ($this->tokenIndex + 1) & 3;

		if (!isset($this->tokens[$this->tokenIndex]))
			$this->tokens[$this->tokenIndex] = new JSToken();

		$token = $this->tokens[$this->tokenIndex];
		$token->type = $tt;

		if ($tt == OP_ASSIGN)
			$token->assignOp = $op;

		$token->start = $this->cursor;

		$token->value = $match[0];
		$this->cursor += strlen($match[0]);

		$token->end = $this->cursor;
		$token->lineno = $this->lineno;

		return $tt;
	}

	public function unget()
	{
		if (++$this->lookahead == 4)
			throw $this->newSyntaxError('PANIC: too much lookahead!');

		$this->tokenIndex = ($this->tokenIndex - 1) & 3;
	}

	public function newSyntaxError($m)
	{
		return new Exception('Parse error: ' . $m . ' in file \'' . $this->filename . '\' on line ' . $this->lineno);
	}
}

class JSToken
{
	public $type;
	public $value;
	public $start;
	public $end;
	public $lineno;
	public $assignOp;
}

?>