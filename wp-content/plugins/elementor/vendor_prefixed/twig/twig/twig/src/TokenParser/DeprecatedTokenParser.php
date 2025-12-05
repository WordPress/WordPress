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

use ElementorDeps\Twig\Error\SyntaxError;
use ElementorDeps\Twig\Node\DeprecatedNode;
use ElementorDeps\Twig\Node\Node;
use ElementorDeps\Twig\Token;
/**
 * Deprecates a section of a template.
 *
 *    {% deprecated 'The "base.twig" template is deprecated, use "layout.twig" instead.' %}
 *    {% extends 'layout.html.twig' %}
 *
 *    {% deprecated 'The "base.twig" template is deprecated, use "layout.twig" instead.' package="foo/bar" version="1.1" %}
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @internal
 */
final class DeprecatedTokenParser extends AbstractTokenParser
{
    public function parse(Token $token) : Node
    {
        $stream = $this->parser->getStream();
        $expressionParser = $this->parser->getExpressionParser();
        $expr = $expressionParser->parseExpression();
        $node = new DeprecatedNode($expr, $token->getLine(), $this->getTag());
        while ($stream->test(Token::NAME_TYPE)) {
            $k = $stream->getCurrent()->getValue();
            $stream->next();
            $stream->expect(Token::OPERATOR_TYPE, '=');
            switch ($k) {
                case 'package':
                    $node->setNode('package', $expressionParser->parseExpression());
                    break;
                case 'version':
                    $node->setNode('version', $expressionParser->parseExpression());
                    break;
                default:
                    throw new SyntaxError(\sprintf('Unknown "%s" option.', $k), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }
        $stream->expect(Token::BLOCK_END_TYPE);
        return $node;
    }
    public function getTag() : string
    {
        return 'deprecated';
    }
}
