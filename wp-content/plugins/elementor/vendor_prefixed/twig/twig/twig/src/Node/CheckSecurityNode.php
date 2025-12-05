<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node;

use ElementorDeps\Twig\Attribute\YieldReady;
use ElementorDeps\Twig\Compiler;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class CheckSecurityNode extends Node
{
    private $usedFilters;
    private $usedTags;
    private $usedFunctions;
    /**
     * @param array<string, int> $usedFilters
     * @param array<string, int> $usedTags
     * @param array<string, int> $usedFunctions
     */
    public function __construct(array $usedFilters, array $usedTags, array $usedFunctions)
    {
        $this->usedFilters = $usedFilters;
        $this->usedTags = $usedTags;
        $this->usedFunctions = $usedFunctions;
        parent::__construct();
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->write("\n")->write("public function checkSecurity()\n")->write("{\n")->indent()->write('static $tags = ')->repr(\array_filter($this->usedTags))->raw(";\n")->write('static $filters = ')->repr(\array_filter($this->usedFilters))->raw(";\n")->write('static $functions = ')->repr(\array_filter($this->usedFunctions))->raw(";\n\n")->write("try {\n")->indent()->write("\$this->sandbox->checkSecurity(\n")->indent()->write(!$this->usedTags ? "[],\n" : "['" . \implode("', '", \array_keys($this->usedTags)) . "'],\n")->write(!$this->usedFilters ? "[],\n" : "['" . \implode("', '", \array_keys($this->usedFilters)) . "'],\n")->write(!$this->usedFunctions ? "[],\n" : "['" . \implode("', '", \array_keys($this->usedFunctions)) . "'],\n")->write("\$this->source\n")->outdent()->write(");\n")->outdent()->write("} catch (SecurityError \$e) {\n")->indent()->write("\$e->setSourceContext(\$this->source);\n\n")->write("if (\$e instanceof SecurityNotAllowedTagError && isset(\$tags[\$e->getTagName()])) {\n")->indent()->write("\$e->setTemplateLine(\$tags[\$e->getTagName()]);\n")->outdent()->write("} elseif (\$e instanceof SecurityNotAllowedFilterError && isset(\$filters[\$e->getFilterName()])) {\n")->indent()->write("\$e->setTemplateLine(\$filters[\$e->getFilterName()]);\n")->outdent()->write("} elseif (\$e instanceof SecurityNotAllowedFunctionError && isset(\$functions[\$e->getFunctionName()])) {\n")->indent()->write("\$e->setTemplateLine(\$functions[\$e->getFunctionName()]);\n")->outdent()->write("}\n\n")->write("throw \$e;\n")->outdent()->write("}\n\n")->outdent()->write("}\n");
    }
}
