<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Parser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use PhUml\Parser\Builders\ClassBuilder;

class ClassVisitor extends NodeVisitorAbstract
{
    /** @var Definitions */
    private $definitions;

    /** @var ClassBuilder */
    private $builder;

    public function __construct(Definitions $definitions, ClassBuilder $builder)
    {
        $this->definitions = $definitions;
        $this->builder = $builder;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_) {
            $this->definitions->add($this->builder->build($node));
        }
    }
}
