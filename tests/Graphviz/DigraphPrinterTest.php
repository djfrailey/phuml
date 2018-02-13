<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Graphviz;

use PHPUnit\Framework\TestCase;
use PhUml\Code\Attributes\Attribute;
use PhUml\Code\InterfaceDefinition;
use PhUml\Code\Methods\Method;
use PhUml\Code\Variables\TypeDeclaration;
use PhUml\Code\Variables\Variable;
use PhUml\Fakes\NumericIdClass;
use PhUml\Fakes\NumericIdInterface;
use PhUml\Fakes\ProvidesNumericIds;
use PhUml\Templates\TemplateEngine;
use PhUml\Templates\TemplateFailure;
use PhUml\TestBuilders\A;
use RuntimeException;

class DigraphPrinterTest extends TestCase
{
    use ProvidesNumericIds;

    /** @test */
    function its_dot_language_representation_contains_an_id_and_basic_display_settings()
    {
        $digraph = new Digraph();

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertRegExp('/^digraph "([0-9a-f]){40}"/', $dotLanguage);
        $this->assertContains('splines = true;
overlap = false;
mindist = 0.6;', $dotLanguage);
    }

    /** @test */
    function it_builds_an_html_label_for_a_class_with_attributes()
    {
        $class = A::class('AClass')
            ->withAPublicAttribute('name')
            ->withAPrivateAttribute('age')
            ->withAProtectedAttribute('category', 'string')
            ->build();
        $digraph = new Digraph();
        $digraph->add([new Node($class)]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertContains(
            '<TABLE CELLSPACING="0" BORDER="0" ALIGN="LEFT"><TR><TD BORDER="1" ALIGN="CENTER" BGCOLOR="#fcaf3e"><B><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">AClass</FONT></B></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">+name</FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">-age</FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">#category: string</FONT><BR ALIGN="LEFT"/></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec">&nbsp;</TD></TR></TABLE>',
            $dotLanguage
        );
    }

    /** @test */
    function it_builds_an_html_label_for_a_class_with_constants()
    {
        $class = A::class('AClass')
            ->withAConstant('NUMERIC', 'int')
            ->withAConstant('NO_TYPE')
            ->build();
        $digraph = new Digraph();
        $digraph->add([new Node($class)]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertContains(
            '<TABLE CELLSPACING="0" BORDER="0" ALIGN="LEFT"><TR><TD BORDER="1" ALIGN="CENTER" BGCOLOR="#fcaf3e"><B><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">AClass</FONT></B></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10"><I>+NUMERIC: int</I></FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10"><I>+NO_TYPE</I></FONT><BR ALIGN="LEFT"/></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec">&nbsp;</TD></TR></TABLE>',
            $dotLanguage
        );
    }

    /** @test */
    function it_builds_an_html_label_for_a_class_with_constants_attributes_and_methods()
    {
        $class = A::class('AClass')
            ->withAPrivateAttribute('age')
            ->withAProtectedAttribute('category', 'string')
            ->withAPublicMethod('getAge')
            ->withAProtectedMethod(
                'setCategory',
                A::parameter('category')->withType('string')->build()
            )
            ->withAConstant('NUMERIC', 'int')
            ->build();
        $digraph = new Digraph();
        $digraph->add([new Node($class)]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertContains(
            '<TABLE CELLSPACING="0" BORDER="0" ALIGN="LEFT"><TR><TD BORDER="1" ALIGN="CENTER" BGCOLOR="#fcaf3e"><B><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">AClass</FONT></B></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10"><I>+NUMERIC: int</I></FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">-age</FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">#category: string</FONT><BR ALIGN="LEFT"/></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">+getAge()</FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">#setCategory(category: string)</FONT><BR ALIGN="LEFT"/></TD></TR></TABLE>',
            $dotLanguage
        );
    }

    /** @test */
    function it_builds_an_html_label_for_an_interface()
    {
        $interface = new InterfaceDefinition('AnInterface');
        $digraph = new Digraph();
        $digraph->add([new Node($interface)]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertContains(
            '<TABLE CELLSPACING="0" BORDER="0" ALIGN="LEFT"><TR><TD BORDER="1" ALIGN="CENTER" BGCOLOR="#fcaf3e"><B><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12"><I>AnInterface</I></FONT></B></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec">&nbsp;</TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec">&nbsp;</TD></TR></TABLE>',
            $dotLanguage
        );
    }

    /** @test */
    function it_builds_an_html_label_for_an_interface_with_methods_and_constants()
    {
        $interface = A::interface('AnInterface')
            ->withAPublicMethod('doSomething')
            ->withAPublicMethod('changeValue', A::parameter('$value')->withType('int')->build())
            ->withAConstant('NUMERIC', 'int')
            ->withAConstant('NO_TYPE')
            ->build();
        $digraph = new Digraph();
        $digraph->add([new Node($interface)]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertContains(
            '<TABLE CELLSPACING="0" BORDER="0" ALIGN="LEFT"><TR><TD BORDER="1" ALIGN="CENTER" BGCOLOR="#fcaf3e"><B><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12"><I>AnInterface</I></FONT></B></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10"><I>+NUMERIC: int</I></FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10"><I>+NO_TYPE</I></FONT><BR ALIGN="LEFT"/></TD></TR><TR><TD BORDER="1" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">+doSomething()</FONT><BR ALIGN="LEFT"/><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">+changeValue($value: int)</FONT><BR ALIGN="LEFT"/></TD></TR></TABLE>',
            $dotLanguage
        );
    }

    /** @test */
    function it_represents_a_single_definition_as_dot_language()
    {
        $class = new NumericIdClass('TestClass');
        $digraph = new Digraph();
        $digraph->add([new Node($class)]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertRegExp('/"101" \[label=<(.)+TestClass(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
    }

    /** @test */
    function it_represents_inheritance_as_dot_language()
    {
        $parentClass = new NumericIdClass('ParentClass');
        $class = new NumericIdClass('TestClass', [], [], [], [], $parentClass);
        $digraph = new Digraph();
        $digraph->add([
            new Node($parentClass),
            new Node($class),
            new InheritanceEdge($parentClass, $class),
        ]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertRegExp('/"101" \[label=<(.)+ParentClass(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"102" \[label=<(.)+TestClass(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"101" -> "102" \[dir=back arrowtail=empty style=solid color="#[0-9a-f]{6}"\]/', $dotLanguage);
    }

    /** @test */
    function it_represents_interfaces_implementations_as_dot_language()
    {
        $anInterface = new NumericIdInterface('AnInterface');
        $anotherInterface = new NumericIdInterface('AnotherInterface');
        $class = new NumericIdClass('TestClass', [], [], [], [$anInterface, $anotherInterface]);
        $digraph = new Digraph();
        $digraph->add([
            new Node($class),
            new ImplementationEdge($anInterface, $class),
            new ImplementationEdge($anotherInterface, $class),
            new Node($anInterface),
            new Node($anotherInterface),
        ]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertRegExp('/"101" \[label=<(.)+TestClass(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"1" \[label=<(.)+AnInterface(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"2" \[label=<(.)+AnotherInterface(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"1" -> "101" \[dir=back arrowtail=normal style=dashed color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"2" -> "101" \[dir=back arrowtail=normal style=dashed color="#[0-9a-f]{6}"\]/', $dotLanguage);
    }

    /** @test */
    function it_represents_constructor_dependencies_as_associations_in_dot_language()
    {
        $referenceClass = new NumericIdClass('AReference');
        $testClass = new NumericIdClass('TestClass', [], [], [
            Method::public('__construct', [
                Variable::declaredWith('aReference', TypeDeclaration::from('AReference'))
            ])
        ]);
        $digraph = new Digraph();
        $digraph->add([
            new Node($referenceClass),
            new AssociationEdge($referenceClass, $testClass),
            new Node($testClass),
        ]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertRegExp('/"101" \[label=<(.)+AReference(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"102" \[label=<(.)+TestClass(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"101" -> "102" \[dir=back arrowtail=none style=solid color="#[0-9a-f]{6}"\]/', $dotLanguage);
    }

    /** @test */
    function it_represents_class_attributes_as_associations_in_dot_language()
    {
        $referenceClass = new NumericIdClass('AReference');
        $testClass = new NumericIdClass('TestClass', [], [
            Attribute::private('$aReference', TypeDeclaration::from('AReference'))
        ], []);
        $digraph = new Digraph();
        $digraph->add([
            new Node($referenceClass),
            new AssociationEdge($referenceClass, $testClass),
            new Node($testClass),
        ]);

        $dotLanguage = $this->printer->toDot($digraph);

        $this->assertRegExp('/"101" \[label=<(.)+AReference(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"102" \[label=<(.)+TestClass(.)+> shape=plaintext color="#[0-9a-f]{6}"\]/', $dotLanguage);
        $this->assertRegExp('/"101" -> "102" \[dir=back arrowtail=none style=solid color="#[0-9a-f]{6}"\]/', $dotLanguage);
    }

    /** @test */
    function it_fails_to_build_a_label_if_twig_fails()
    {
        $templateEngine = new class() extends TemplateEngine {
            public function render($name, array $context = []): string {
                throw new TemplateFailure(new RuntimeException('Twig runtime error'));
            }
            public function __construct() {} // Constructor does not needs to be run
        };
        $printer = new DigraphPrinter($templateEngine);

        $this->expectException(TemplateFailure::class);

        $printer->toDot(new Digraph());
    }

    /** @var DigraphPrinter */
    private $printer;

    /** @before */
    function createPrinter()
    {
        $this->printer = new DigraphPrinter();
    }
}