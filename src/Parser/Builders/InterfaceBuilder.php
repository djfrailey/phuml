<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Parser\Builders;

use PhpParser\Node\Stmt\Interface_;

class InterfaceBuilder
{
    /** @var MethodsBuilder */
    private $methodsBuilder;

    public function __construct(MethodsBuilder $methodsBuilder = null)
    {
        $this->methodsBuilder = $methodsBuilder ?? new MethodsBuilder();
    }

    public function build(Interface_ $interface): array
    {
        return [
            'interface' => $interface->name,
            'functions' => $this->methodsBuilder->build($interface),
            'extends' => !empty($interface->extends) ? end($interface->extends)->getLast() : null,
        ];
    }
}
