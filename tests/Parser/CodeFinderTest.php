<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Parser;

use PHPUnit\Framework\TestCase;

class CodeFinderTest extends TestCase
{
    /** @test */
    function it_finds_files_only_in_the_given_directory()
    {
        $finder = new CodeFinder();

        $finder->addDirectory(CodebaseDirectory::from(__DIR__ . '/../resources/.code/classes'), false);

        $this->assertCount(2, $finder->files());
        $this->assertRegExp('/class plBase/', $finder->files()[0]);
        $this->assertRegExp('/class plPhuml/', $finder->files()[1]);
    }

    /** @test */
    function it_finds_files_recursively()
    {
        $finder = new CodeFinder();

        $finder->addDirectory(CodebaseDirectory::from(__DIR__ . '/../resources/.code/interfaces'));

        $this->assertCount(4, $finder->files());
        $this->assertRegExp('/abstract class plStructureGenerator/', $finder->files()[0]);
        $this->assertRegExp('/abstract class plProcessor/', $finder->files()[1]);
        $this->assertRegExp('/abstract class plExternalCommandProcessor/', $finder->files()[2]);
        $this->assertRegExp('/abstract class plGraphvizProcessorStyle/', $finder->files()[3]);
    }
}
