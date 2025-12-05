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
namespace ElementorDeps\Twig\Node\Expression;

use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Extension\SandboxExtension;
use ElementorDeps\Twig\Template;
class GetAttrExpression extends AbstractExpression
{
    public function __construct(AbstractExpression $node, AbstractExpression $attribute, ?AbstractExpression $arguments, string $type, int $lineno)
    {
        $nodes = ['node' => $node, 'attribute' => $attribute];
        if (null !== $arguments) {
            $nodes['arguments'] = $arguments;
        }
        parent::__construct($nodes, ['type' => $type, 'is_defined_test' => \false, 'ignore_strict_check' => \false, 'optimizable' => \true], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $env = $compiler->getEnvironment();
        $arrayAccessSandbox = \false;
        // optimize array calls
        if ($this->getAttribute('optimizable') && (!$env->isStrictVariables() || $this->getAttribute('ignore_strict_check')) && !$this->getAttribute('is_defined_test') && Template::ARRAY_CALL === $this->getAttribute('type')) {
            $var = '$' . $compiler->getVarName();
            $compiler->raw('((' . $var . ' = ')->subcompile($this->getNode('node'))->raw(') && is_array(')->raw($var);
            if (!$env->hasExtension(SandboxExtension::class)) {
                $compiler->raw(') || ')->raw($var)->raw(' instanceof ArrayAccess ? (')->raw($var)->raw('[')->subcompile($this->getNode('attribute'))->raw('] ?? null) : null)');
                return;
            }
            $arrayAccessSandbox = \true;
            $compiler->raw(') || ')->raw($var)->raw(' instanceof ArrayAccess && in_array(')->raw('get_class(' . $var . ')')->raw(', CoreExtension::ARRAY_LIKE_CLASSES, true) ? (')->raw($var)->raw('[')->subcompile($this->getNode('attribute'))->raw('] ?? null) : ');
        }
        $compiler->raw('CoreExtension::getAttribute($this->env, $this->source, ');
        if ($this->getAttribute('ignore_strict_check')) {
            $this->getNode('node')->setAttribute('ignore_strict_check', \true);
        }
        $compiler->subcompile($this->getNode('node'))->raw(', ')->subcompile($this->getNode('attribute'));
        if ($this->hasNode('arguments')) {
            $compiler->raw(', ')->subcompile($this->getNode('arguments'));
        } else {
            $compiler->raw(', []');
        }
        $compiler->raw(', ')->repr($this->getAttribute('type'))->raw(', ')->repr($this->getAttribute('is_defined_test'))->raw(', ')->repr($this->getAttribute('ignore_strict_check'))->raw(', ')->repr($env->hasExtension(SandboxExtension::class))->raw(', ')->repr($this->getNode('node')->getTemplateLine())->raw(')');
        if ($arrayAccessSandbox) {
            $compiler->raw(')');
        }
    }
}
