<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Parser\Raw\Visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use PhUml\Code\Codebase;
use PhUml\Parser\Raw\Builders\RawClassBuilder;

/**
 * It extracts a `ClassDefinition` and adds it to the `Codebase`
 */
class ClassVisitor extends NodeVisitorAbstract
{
    /** @var RawClassBuilder */
    private $builder;

    /** @var Codebase */
    private $codebase;

    public function __construct(RawClassBuilder $builder, Codebase $codebase)
    {
        $this->builder = $builder;
        $this->codebase = $codebase;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_) {
            $this->codebase->add($this->builder->build($node));
        }
    }
}
