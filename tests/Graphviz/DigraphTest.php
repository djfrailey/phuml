<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace PhUml\Graphviz;

use PHPUnit\Framework\TestCase;
use PhUml\Code\Method;
use PhUml\Code\Variable;
use plClassNameLabelBuilder;
use plNumericIdClass;
use plNumericIdInterface;

class DigraphTest extends TestCase
{
    /** @test */
    function it_can_be_represented_as_dot_language_from_a_structure_with_one_definition()
    {
        $labelBuilder = new plClassNameLabelBuilder();
        $interfaceElements = new InterfaceGraphElements($labelBuilder);
        $classElements = new ClassGraphElements(true, $labelBuilder);
        $digraph = new Digraph($interfaceElements, $classElements);

        $digraph->fromCodeStructure([new plNumericIdClass('TestClass')]);

        $dotLanguage = $digraph->toDotLanguage();

        $this->assertRegExp('/^digraph "([0-9a-f]){40}"/', $dotLanguage);
        $this->assertStringEndsWith(' {
splines = true;
overlap = false;
mindist = 0.6;
"101" [label=<<table><tr><td>TestClass</td></tr></table>> shape=plaintext]
}', $dotLanguage);
    }

    /** @test */
    function it_can_be_represented_as_dot_language_from_a_structure_with_several_definitions()
    {
        $labelBuilder = new plClassNameLabelBuilder();
        $interfaceElements = new InterfaceGraphElements($labelBuilder);
        $classElements = new ClassGraphElements(true, $labelBuilder);
        $digraph = new Digraph($interfaceElements, $classElements);

        $parentInterface = new plNumericIdInterface('ParentInterface');
        $childInterface = new plNumericIdInterface('ChildInterface', [], $parentInterface);
        $anotherInterface = new plNumericIdInterface('AnotherInterface');
        $parentClass = new plNumericIdClass('ParentClass');
        $referenceClass = new plNumericIdClass('AReference');
        $testClass = new plNumericIdClass('TestClass', [], [
            new Method('__construct', 'public', [
                new Variable('aReference', 'AReference')
            ])
        ], [$childInterface, $anotherInterface], $parentClass);
        $digraph->fromCodeStructure([
            $referenceClass->name => $referenceClass,
            $parentInterface->name => $parentInterface,
            $childInterface->name => $childInterface,
            $anotherInterface->name => $anotherInterface,
            $parentClass->name => $parentClass,
            $testClass->name => $testClass,
        ]);

        $dotLanguage = $digraph->toDotLanguage();
        $this->assertRegExp('/^digraph "([0-9a-f]){40}"/', $dotLanguage);
        $this->assertStringEndsWith(' {
splines = true;
overlap = false;
mindist = 0.6;
"103" [label=<<table><tr><td>AReference</td></tr></table>> shape=plaintext]
"1" [label=<<table><tr><td>ParentInterface</td></tr></table>> shape=plaintext]
"2" [label=<<table><tr><td>ChildInterface</td></tr></table>> shape=plaintext]
"1" -> "2" [dir=back arrowtail=empty style=solid]
"3" [label=<<table><tr><td>AnotherInterface</td></tr></table>> shape=plaintext]
"102" [label=<<table><tr><td>ParentClass</td></tr></table>> shape=plaintext]
"103" -> "104" [dir=back arrowtail=none style=dashed]
"104" [label=<<table><tr><td>TestClass</td></tr></table>> shape=plaintext]
"102" -> "104" [dir=back arrowtail=empty style=solid]
"2" -> "104" [dir=back arrowtail=normal style=dashed]
"3" -> "104" [dir=back arrowtail=normal style=dashed]
}', $dotLanguage);
    }
}