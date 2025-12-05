<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node\Expression;

use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Extension\CoreExtension;
use ElementorDeps\Twig\Node\Node;
class FunctionExpression extends CallExpression
{
    public function __construct(string $name, Node $arguments, int $lineno)
    {
        parent::__construct(['arguments' => $arguments], ['name' => $name, 'type' => 'function', 'is_defined_test' => \false], $lineno);
    }
    public function compile(Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $function = $compiler->getEnvironment()->getFunction($name);
        $this->setAttribute('needs_charset', $function->needsCharset());
        $this->setAttribute('needs_environment', $function->needsEnvironment());
        $this->setAttribute('needs_context', $function->needsContext());
        $this->setAttribute('arguments', $function->getArguments());
        $callable = $function->getCallable();
        if ('constant' === $name && $this->getAttribute('is_defined_test')) {
            $callable = [CoreExtension::class, 'constantIsDefined'];
        }
        $this->setAttribute('callable', $callable);
        $this->setAttribute('is_variadic', $function->isVariadic());
        $this->compileCallable($compiler);
    }
}
