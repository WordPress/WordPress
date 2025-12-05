<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Matomo\Dependencies\Twig\TokenParser;

use Matomo\Dependencies\Twig\Node\DoNode;
use Matomo\Dependencies\Twig\Node\Node;
use Matomo\Dependencies\Twig\Token;
/**
 * Evaluates an expression, discarding the returned value.
 *
 * @internal
 */
final class DoTokenParser extends AbstractTokenParser
{
    public function parse(Token $token) : Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        return new DoNode($expr, $token->getLine(), $this->getTag());
    }
    public function getTag() : string
    {
        return 'do';
    }
}
