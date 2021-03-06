<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Graphviz\Builders;

use PhUml\Code\ClassDefinition;
use PhUml\Code\Codebase;

/**
 * It discovers associations between classes/interfaces by inspecting
 *
 * 1. The attributes of a class
 * 2. The parameters injected trough the constructor of a class
 *
 * It creates edges between the definitions when appropriate
 */
interface AssociationsBuilder
{
    /** @return \PhUml\Graphviz\Edge[]*/
    public function fromAttributes(ClassDefinition $class, Codebase $codebase): array;

    /** @return \PhUml\Graphviz\Edge[] */
    public function fromConstructor(ClassDefinition $class, Codebase $codebase): array;
}
