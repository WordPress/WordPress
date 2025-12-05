<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\TokenParser;

use ElementorDeps\Twig\Node\Expression\TempNameExpression;
use ElementorDeps\Twig\Node\Node;
use ElementorDeps\Twig\Node\PrintNode;
use ElementorDeps\Twig\Node\SetNode;
use ElementorDeps\Twig\Token;
/**
 * Applies filters on a section of a template.
 *
 *   {% apply upper %}
 *      This text becomes uppercase
 *   {% endapply %}
 *
 * @internal
 */
final class ApplyTokenParser extends AbstractTokenParser
{
    public function parse(Token $token) : Node
    {
        $lineno = $token->getLine();
        $name = $this->parser->getVarName();
        $ref = new TempNameExpression($name, $lineno);
        $ref->setAttribute('always_defined', \true);
        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideApplyEnd'], \true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return new Node([new SetNode(\true, $ref, $body, $lineno, $this->getTag()), new PrintNode($filter, $lineno, $this->getTag())]);
    }
    public function decideApplyEnd(Token $token) : bool
    {
        return $token->test('endapply');
    }
    public function getTag() : string
    {
        return 'apply';
    }
}
