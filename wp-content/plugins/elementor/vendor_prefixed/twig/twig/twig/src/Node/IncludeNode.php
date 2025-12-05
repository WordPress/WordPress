<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node;

use ElementorDeps\Twig\Attribute\YieldReady;
use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Node\Expression\AbstractExpression;
/**
 * Represents an include node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class IncludeNode extends Node implements NodeOutputInterface
{
    public function __construct(AbstractExpression $expr, ?AbstractExpression $variables, bool $only, bool $ignoreMissing, int $lineno, ?string $tag = null)
    {
        $nodes = ['expr' => $expr];
        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }
        parent::__construct($nodes, ['only' => $only, 'ignore_missing' => $ignoreMissing], $lineno, $tag);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this);
        if ($this->getAttribute('ignore_missing')) {
            $template = $compiler->getVarName();
            $compiler->write(\sprintf("\$%s = null;\n", $template))->write("try {\n")->indent()->write(\sprintf('$%s = ', $template));
            $this->addGetTemplate($compiler);
            $compiler->raw(";\n")->outdent()->write("} catch (LoaderError \$e) {\n")->indent()->write("// ignore missing template\n")->outdent()->write("}\n")->write(\sprintf("if (\$%s) {\n", $template))->indent()->write(\sprintf('yield from $%s->unwrap()->yield(', $template));
            $this->addTemplateArguments($compiler);
            $compiler->raw(");\n")->outdent()->write("}\n");
        } else {
            $compiler->write('yield from ');
            $this->addGetTemplate($compiler);
            $compiler->raw('->unwrap()->yield(');
            $this->addTemplateArguments($compiler);
            $compiler->raw(");\n");
        }
    }
    protected function addGetTemplate(Compiler $compiler)
    {
        $compiler->write('$this->loadTemplate(')->subcompile($this->getNode('expr'))->raw(', ')->repr($this->getTemplateName())->raw(', ')->repr($this->getTemplateLine())->raw(')');
    }
    protected function addTemplateArguments(Compiler $compiler)
    {
        if (!$this->hasNode('variables')) {
            $compiler->raw(\false === $this->getAttribute('only') ? '$context' : '[]');
        } elseif (\false === $this->getAttribute('only')) {
            $compiler->raw('CoreExtension::merge($context, ')->subcompile($this->getNode('variables'))->raw(')');
        } else {
            $compiler->raw('CoreExtension::toArray(');
            $compiler->subcompile($this->getNode('variables'));
            $compiler->raw(')');
        }
    }
}
